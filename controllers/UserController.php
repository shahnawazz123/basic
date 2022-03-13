<?php

namespace app\controllers;

use app\models\Orders;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;
use app\models\Users;
use app\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * UserController implements the CRUD actions for Users model.
 */
class UserController extends Controller {

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
                'only' => ['index', 'view', 'delete', 'abandoned-cart', 'update-shipping-address'],
                'rules' => [
                    [
                        'actions' => \app\helpers\PermissionHelper::getUserPermissibleAction(1),
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
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) 
        {
            $request = Yii::$app->request->bodyParams;

            $doctor_report_request_id = $request['doctor_report_request_id'];

            $RequestCheck = \app\models\DoctorReportRequest::find()
                    ->where(['doctor_report_request_id' => $doctor_report_request_id])
                    ->one();

            $reports = $request['Users']['reports'];
            if(!empty($reports))
            {
                foreach($reports as $report_id)
                {
                    $modelRequest = new \app\models\DoctorAssignedReportRequest();
                    $modelRequest->doctor_appointment_id = $request['Users']['req_doctor_appointment_id'];
                    $modelRequest->report_id = $report_id;
                    $modelRequest->is_approved = $request['Users']['is_approved'];
                    $modelRequest->save(false);
                }
            }
            Yii::$app->session->setFlash('success', 'Doctors successfully added');
            return $this->redirect(['view?id='.$id]);
            
        }
        return $this->render('view', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();

        Yii::$app->session->setFlash('success', 'User successfully deleted');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Users::find()->where(['user_id' => $id, 'is_deleted' => 0])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAbandonedCart() {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->abandonedCartUsers(Yii::$app->request->queryParams);

        return $this->render('abandoned-cart', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSendPush($id, $msg) {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {
            //checking the product validity
            $user = Users::find()
                    ->where(['is_deleted' => 0, 'user_id' => $id])
                    ->one();

            if (empty($user)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'User does exist'
                ]);
            } else {

                Yii::$app->mailer->compose('@app/mail/abandoned-cart', [
                            "name" => $user->first_name . ' ' . $user->last_name,
                            "message" => $msg
                        ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo($user->email)
                        ->setSubject("You forgot something in your cart!")
                        ->send();

                if (!empty($user->device_token)) {
                    $devicesList = [];

                    $devicesList[] = $user->device_token;
                    //sending push to device
                    \app\helpers\AppHelper::sendPushwoosh($msg, $devicesList, 'CA', '');

                    return json_encode([
                        'success' => '1',
                        'msg' => 'Push successfully sent'
                    ]);
                } else {
                    return json_encode([
                        'success' => '0',
                        'msg' => 'User device token not found.'
                    ]);
                }
            }
        }
    }

    public function actionExport() {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 1);

        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->getProperties()->setCreator("Sayarti")
                ->setTitle('Sheet1')
                ->setKeywords("phpExcel");
        $objPHPExcel->setActiveSheetIndex(0);
        //excel columns
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Name');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Email');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Phone');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Is Social Register');
        $n = 2;
        foreach ($dataProvider as $model) {

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $n, $model->first_name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $n, $model->email);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $n, $model->phone);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $n, ($model->is_social_register ? 'Yes' : 'No'));
            $n++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="user-' . date('YmdHis') . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xls");
        $objWriter->save('php://output');

    }

    public function actionUpdateShippingAddress($id) {
        $model = \app\models\ShippingAddresses::find()
                ->where(['is_deleted' => 0, 'shipping_address_id' => $id])
                ->one();
        if (empty($model))
            throw new NotFoundHttpException('The requested page does not exist.');

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            //debugPrint($request);exit;
            if($request['ShippingAddresses']['is_default']==1)
            {
                \app\models\ShippingAddresses::updateAll(['is_default' => 0],'user_id = '.$model->user_id);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Shipping address successfully updated');
                return $this->redirect(['update-shipping-address', 'id' => $model->shipping_address_id]);
            } else {
                return $this->render('update-shipping-address', [
                            'model' => $model,
                ]);
            }
        } else {
            return $this->render('update-shipping-address', [
                        'model' => $model,
            ]);
        }
    }

}
