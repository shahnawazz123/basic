<?php

namespace app\controllers;

use Yii;
use app\models\Currencies;
use app\models\CurrenciesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * CurrenyController implements the CRUD actions for Currencies model.
 */
class CurrencyController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'update', 'refresh'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(6),
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Currencies models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CurrenciesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRefresh() {
        $ratesJson = file_get_contents('http://openexchangerates.org/api/latest.json?app_id=179d47f3454c43f3adc2636d9f068711');
        $rates = json_decode($ratesJson);

        $model = Currencies::find()->where(['code_en' => $rates->base])->one();

        if (!empty($model)) {
            Currencies::updateAll(['is_base_currency' => 0]);

            $model->is_base_currency = 1;
            $model->save();
        }

        foreach ($rates->rates as $code => $rate) {
            $model = \app\models\Currencies::find()->where(['code_en' => $code])->one();

            if (!empty($model)) {
                $model->currency_rate = $rate;
                //$model->code_ar = $code;
                $model->save();
            }
        }
        Yii::$app->session->setFlash('success', 'Currencies successfully updated');
        return $this->redirect(['index']);
    }

    /**
     * Displays a single Currencies model.
     * @param integer $id
     * @return mixed
     */
    /* public function actionView($id)
      {
      return $this->render('view', [
      'model' => $this->findModel($id),
      ]);
      } */

    /**
     * Creates a new Currencies model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /* public function actionCreate()
      {
      $model = new Currencies();

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
      return $this->redirect(['view', 'id' => $model->currency_id]);
      } else {
      return $this->render('create', [
      'model' => $model,
      ]);
      }
      } */

    /**
     * Updates an existing Currencies model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Currencies model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    /* public function actionDelete($id)
      {
      $this->findModel($id)->delete();

      return $this->redirect(['index']);
      } */

    /**
     * Finds the Currencies model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Currencies the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Currencies::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
