<?php

namespace app\controllers;

use app\helpers\AppHelper;
use app\helpers\ProductHelper;
use app\helpers\RestHelper;
use app\models\AssociatedProducts;
use app\models\AttributeSetGroups;
use app\models\AttributeSets;
use app\models\AttributeValues;
use app\models\Brands;
use app\models\Manufacturers;
use app\models\Category;
use app\models\ExcelUpload;
use app\models\OrderItems;
use app\models\ProductAttributeValues;
use app\models\ProductCategories;
use app\models\ProductImages;
use app\models\Pharmacies;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Yii;
use app\models\Product;
use app\models\ProductSearch;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;
use himiklab\sortablegrid\SortableGridAction;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => [
                    'index', 'view', 'create', 'update', 'delete', 'add-product-stock', 'stock-products', 'stock'
                ],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-product-stock', 'stock-products', 'stock'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-product-stock', 'stock-products', 'stock'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_PHARMACY
                        ]
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'sort' => [
                'class' => SortableGridAction::className(),
                'modelName' => \app\models\Product::className(),
            ],

        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'image-delete') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $pageSize = Yii::$app->request->get('per-page', 20);
        $searchModel->page_size = $pageSize;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionFeatured($id)
    {
        $model = $this->findModel($id);
        if ($model->is_featured == 0) {
            $model->is_featured = '1';
        } else {
            $model->is_featured = '0';
        }
        if ($model->save(false)) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }
    public function actionTrend($id)
    {
        $model = $this->findModel($id);
        if ($model->is_trending == 0) {
            $model->is_trending = '1';
        } else {
            $model->is_trending = '0';
        }
        if ($model->save()) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }
    public function actionStockProducts()
    {
        $searchModel = new ProductSearch();
        $searchModel->no_stock = 1;
        if (Yii::$app->request->get('active')) {
            $searchModel->no_stock_active = Yii::$app->request->get('active');
        }
        if (Yii::$app->request->get('inactive')) {
            $searchModel->no_stock_inactive = Yii::$app->request->get('inactive');
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('out-of-stock', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->session['_eyadatAuth'] == 5 && (Yii::$app->user->identity->pharmacy_id != $model->pharmacy_id)) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        }
        /*if ( (Yii::$app->user->identity->pharmacy_id != $model->pharmacy_id)) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        }*/
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
        $model->scenario = 'create';
        $model->base_currency_id = 82;
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                $model->admin_id = Yii::$app->user->identity->admin_id;
            }
            $model->posted_date = date('Y-m-d H:i:s');
            if (isset($request['Product']['start_date']) && isset($request['Product']['end_date'])) {
                $model->is_new = 1;
                $model->start_date = $request['Product']['start_date'];
                $model->end_date = $request['Product']['end_date'];
            }
            $model->show_as_individual = 1;
            if (empty($model->barcode)) {
                $model->barcode = ProductHelper::generateBarcodeProduct();
            }
            if (!empty($model->barcode)) {
                $newBarcode = $model->barcode;
                $generator = new BarcodeGeneratorJPG();
                $barcode = $generator->getBarcode($newBarcode, $generator::TYPE_CODE_128, 4, 45);
                $barcodeName = 'barcode-' . $newBarcode . '.jpg';
                file_put_contents(Yii::getAlias('@webroot') . '/uploads/' . $barcodeName, $barcode);
                $model->barcode = $newBarcode;
            }
            if ($model->save()) {
                if (!empty($model->barcode)) {
                    \app\helpers\AppHelper::generateBarCodeTemplate($model);
                }
                //product status
                $productStatusHistory = new \app\models\ProductStatusHistory();
                $productStatusHistory->product_id = $model->product_id;
                $productStatusHistory->product_status_id = 2;
                $productStatusHistory->status_date = date('Y-m-d H:i:s');
                $productStatusHistory->notify_customer = 0;
                $productStatusHistory->save();
                //product stocks
                if (!empty($request['Product']['images'])) {
                    $size = sizeof($request['Product']['images']);
                    for ($i = 0; $i < $size; $i++) {
                        $productImage = new \app\models\ProductImages();
                        $productImage->product_id = $model->product_id;
                        $productImage->image = $request['Product']['images'][$i];
                        $productImage->save();
                    }
                }
                if (isset($request['kv-node-selected']) && $request['kv-node-selected'] != "") {
                    $categories = $request['kv-node-selected'];
                    $catArray = explode(',', $categories);
                    foreach ($catArray as $key => $value) {
                        $productCategory = new \app\models\ProductCategories();
                        $productCategory->category_id = $value;
                        $productCategory->product_id = $model->product_id;
                        $productCategory->save();
                    }
                }
                if (!empty($request['Product']['attribute_values'])) {
                    foreach ($request['Product']['attribute_values'] as $row) {
                        $attValue = new \app\models\ProductAttributeValues();
                        $attValue->attribute_value_id = $row;
                        $attValue->product_id = $model->product_id;
                        $attValue->save();
                    }
                }
                if (!empty($request['rp_id'])) {
                    foreach ($request['rp_id'] as $row) {
                        $relatedProduct = new \app\models\RelatedProducts();
                        $relatedProduct->related_id = $row;
                        $relatedProduct->product_id = $model->product_id;
                        $relatedProduct->save();
                    }
                }
                if (!empty($request['ap_id'])) {
                    foreach ($request['ap_id'] as $row) {
                        $associatedProduct = new \app\models\AssociatedProducts();
                        $associatedProduct->child_id = $row;
                        $associatedProduct->parent_id = $model->product_id;
                        $associatedProduct->save();
                        //
                        if (!empty($model->productCategories)) {
                            foreach ($model->productCategories as $ps) {
                                $productChildCategory = new \app\models\ProductCategories();
                                $productChildCategory->category_id = $ps->category_id;
                                $productChildCategory->product_id = $associatedProduct->child_id;
                                $productChildCategory->save();
                            }
                        }
                    }
                }
                if (!empty($request['Product']['store_id'])) {
                    //foreach ($request['Product']['store_id'] as $strId) {
                    $storeProduct = new \app\models\StoreProducts();
                    $storeProduct->store_id = $request['Product']['store_id']; //$strId;
                    $storeProduct->product_id = $model->product_id;
                    $storeProduct->save();
                    //}
                }
                $deepLinkUrl = null;
                $model->deeplink_url = $deepLinkUrl;
                $model->save(false);
                Yii::$app->session->setFlash('success', 'Product successfully added');

                return $this->redirect(['index']);
            } else {
                json_encode($model->errors);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $youtubeId = $model->youtube_id;
        $old_barcode = $model->barcode;
        $oldImagesModel = $model->getProductImages()->select(['image'])->asArray()->all();
        $oldImages = array_map(function ($ar) {
            return $ar['image'];
        }, $oldImagesModel);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (isset($request['Product']['start_date']) && isset($request['Product']['end_date'])) {
                $model->is_new = 1;
                $model->start_date = $request['Product']['start_date'];
                $model->end_date = $request['Product']['end_date'];
            }
            $dir = Yii::getAlias('@webroot') . '/uploads/barcode-';
            if ((!empty($model->barcode) && $old_barcode != $model->barcode) || (!empty($model->barcode) && !file_exists($dir . $model->barcode . '.jpg'))) {
                $newBarcode = $model->barcode;
                $oldBarcodeFile = Yii::getAlias('@webroot') . '/uploads/barcode-' . $old_barcode . '.jpg';
                if (file_exists($oldBarcodeFile)) {
                    unlink($oldBarcodeFile);
                }
                $generator = new BarcodeGeneratorJPG();
                $barcode = $generator->getBarcode($newBarcode, $generator::TYPE_CODE_128, 4, 45);
                $barcodeName = 'barcode-' . $newBarcode . '.jpg';
                file_put_contents(Yii::getAlias('@webroot') . '/uploads/' . $barcodeName, $barcode);
                $model->barcode = $newBarcode;
            }
            if ($model->save()) {
                if (!empty($model->barcode)) {
                    \app\helpers\AppHelper::generateBarCodeTemplate($model);
                }
                $product_image = ProductImages::find()
                    ->where(['product_id' => $model->product_id])
                    ->all();
                if (!empty($request['Product']['images'])) {
                    $image_list = array();
                    foreach ($product_image as $pi) {
                        $image_list[] = $pi['image'];
                        if (!in_array($pi['image'], $request['Product']['images'])) {
                            $url = Yii::$app->basePath . '/web/uploads/' . $pi['image'];
                            if (file_exists($url)) {
                                unlink($url);
                            }
                            $productImages = ProductImages::find()
                                ->where(['image' => $pi['image'], 'product_id' => $model->product_id])
                                ->one();
                            if (!empty($productImages)) {
                                $productImages->delete();
                            }
                        }
                    }
                    foreach ($request['Product']['images'] as $img) {
                        if (!in_array($img, $image_list)) {
                            $productImage = new ProductImages();
                            $productImage->image = $img;
                            $productImage->product_id = $model->product_id;
                            $productImage->save();
                        }
                    }
                } else {
                    if (!empty($product_image)) {
                        foreach ($product_image as $pi) {
                            $url = Yii::$app->basePath . '/web/uploads/' . $pi['image'];
                            if (file_exists($url)) {
                                unlink($url);
                            }
                        }
                    }
                    ProductImages::deleteAll(['product_id' => $model->product_id]);
                }

                if (isset($request['kv-node-selected']) && $request['kv-node-selected'] != "") {
                    \app\models\ProductCategories::deleteAll('product_id = ' . $model->product_id);
                    $categories = $request['kv-node-selected'];
                    $catArray = explode(',', $categories);
                    foreach ($catArray as $key => $value) {
                        $productCategory = new \app\models\ProductCategories();
                        $productCategory->category_id = $value;
                        $productCategory->product_id = $model->product_id;
                        $productCategory->save();
                    }
                }

                \app\models\ProductAttributeValues::deleteAll('product_id = ' . $model->product_id);
                if (!empty($request['Product']['attribute_values'])) {
                    foreach ($request['Product']['attribute_values'] as $row) {
                        $attValue = new \app\models\ProductAttributeValues();
                        $attValue->attribute_value_id = $row;
                        $attValue->product_id = $model->product_id;
                        $attValue->save();
                    }
                }
                \app\models\RelatedProducts::deleteAll('product_id = ' . $model->product_id);
                if (!empty($request['rp_id'])) {
                    foreach ($request['rp_id'] as $row) {
                        $relatedProduct = new \app\models\RelatedProducts();
                        $relatedProduct->related_id = $row;
                        $relatedProduct->product_id = $model->product_id;
                        $relatedProduct->save();
                    }
                }

                \app\models\AssociatedProducts::deleteAll('parent_id = ' . $model->product_id);
                if (!empty($request['ap_id'])) {
                    foreach ($request['ap_id'] as $row) {
                        $childModel = Product::findOne($row);
                        $childModel->show_as_individual = 0;
                        $childModel->save(false);
                        $associatedProduct = new \app\models\AssociatedProducts();
                        $associatedProduct->child_id = $row;
                        $associatedProduct->parent_id = $model->product_id;
                        $associatedProduct->save();
                        //
                        ProductCategories::deleteAll('product_id = ' . $associatedProduct->child_id);
                        if (!empty($model->productCategories)) {
                            foreach ($model->productCategories as $ps) {
                                $productChildCategory = new \app\models\ProductCategories();
                                $productChildCategory->category_id = $ps->category_id;
                                $productChildCategory->product_id = $associatedProduct->child_id;
                                $productChildCategory->save();
                            }
                        }
                    }
                }
                if (!empty($request['Product']['store_id'])) {
                    \app\models\StoreProducts::deleteAll('product_id = ' . $model->product_id);
                    //foreach ($request['Product']['store_id'] as $strId) {
                    $storeProduct = new \app\models\StoreProducts();
                    $storeProduct->store_id = $request['Product']['store_id']; //$strId;
                    $storeProduct->product_id = $model->product_id;
                    $storeProduct->save();
                    //}
                }

                Yii::$app->session->setFlash('success', 'Product successfully updated');

                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionGenerateBarcode()
    {
        $model = Product::find()->where(['IS NOT', 'barcode', null])->andWhere(['is_deleted' => 0])->all();
        $i = 0;
        $barcodeName = '';
        $success = [];
        $failed = [];
        //try {
        if (!empty($model)) {
            foreach ($model as $row) {
                if (!empty($row->barcode)) {
                    try {
                        $newBarcode = $row->barcode;
                        $pathOld = Yii::getAlias('@webroot') . '/uploads/' . $row->barcode;
                        if (file_exists($pathOld)) {
                            unlink($pathOld);
                        }
                        $generator = new BarcodeGeneratorJPG();
                        $barcode = $generator->getBarcode($newBarcode, $generator::TYPE_CODE_128, 4, 45);
                        $barcodeName = 'barcode-' . $newBarcode . '.jpg';
                        $path = Yii::getAlias('@webroot') . '/uploads/' . $barcodeName;
                        if (file_exists($path)) {
                            unlink($path);
                        }
                        file_put_contents(Yii::getAlias('@webroot') . '/uploads/' . $barcodeName, $barcode);
                        $row->barcode = $newBarcode;
                        $row->save(false);
                        $i++;
                        $success[] = $row->barcode;
                    } catch (\Exception $e) {
                        $failed[] = $row->barcode;
                    }
                }
            }
        }
        echo 'failed : <br>';
        debugPrint($failed);
        echo 'success : <br>';
        debugPrint($success);
        echo "<br> success: " . $i;
    }

    public function actionGenerateBarcodeTemplate()
    {
        $query = Product::find()
            ->where(['!=', 'barcode', ""])
            ->andWhere(['is_deleted' => 0]);
        $model = $query->all();
        $i = 0;
        if (!empty($model)) {
            foreach ($model as $row) {
                if (!empty($row->barcode)) {
                    $file_name = cleanBarcodeName($row->name_en) . '-' . $row->barcode . '.jpg';
                    $path = Yii::getAlias('@webroot') . '/barcode-templates/' . $file_name;
                    //echo $path;
                    if (!file_exists($path)) {
                        $generator = new BarcodeGeneratorPNG();
                        $barcode = $generator->getBarcode($row->barcode, $generator::TYPE_CODE_128);
                        file_put_contents(Yii::getAlias('@webroot') . '/barcode-templates/' . $file_name, $barcode);
                        \app\helpers\AppHelper::generateBarCodeTemplate($row);
                        $i++;
                    } else {
                        \app\helpers\AppHelper::generateBarCodeTemplate($row);
                        $i++;
                    }
                }
            }
        }
        echo "{$i} barcodes generated successfully!!!";
    }

    public function actionDownloadZip()
    {
        $dir = Yii::getAlias('@webroot') . '/barcode-templates/';
        $path = dirname(__FILE__);
        $path = str_replace('\\', '/', $path);
        $path = dirname($path);
        $zip_file = $path . "/web/barcode-templates/barcode-zip-" . time() . ".zip";
        // Get real path for our folder
        //$rootPath = realpath($dir);
        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dir));
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing object
        @$zip->close();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($zip_file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zip_file));
        readfile($zip_file);
        unlink($zip_file);
    }

    public function actionGetAttribute($attset)
    {
        if (!empty($attset)) {
            $sql = 'SET SESSION group_concat_max_len = 1000000;';
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $command->execute();
            $attributes = \app\models\Attributes::find()
                ->select(['attributes.attribute_id', 'attributes.name_en', new \yii\db\Expression("GROUP_CONCAT(CONCAT(`attribute_values`.`attribute_value_id`, '~~', `attribute_values`.`value_en`) ORDER BY attribute_values.sort_order SEPARATOR '##') AS attribute_values")])
                ->join('LEFT JOIN', 'attribute_set_groups', 'attribute_set_groups.attribute_id = attributes.attribute_id')
                ->join('LEFT JOIN', 'attribute_values', 'attributes.attribute_id = attribute_values.attribute_id')
                ->where(['attribute_set_groups.attribute_set_id' => $attset])
                ->groupBy(['attributes.attribute_id'])
                ->orderBy(['attributes.sort_order' => SORT_ASC])
                ->asArray()->all();
            $result = "";
            if (!empty($attributes)) {
                foreach ($attributes as $row) {
                    $result .= "<div class='col-md-6'><div class='form-group field-product-attribute_values'><label class='control-label' for='product-attribute_" . $row['attribute_id'] . "'>" . $row['name_en'] . "</label>";
                    $result .= "<select id='product-attribute_" . $row['attribute_id'] . "' class='select5 form-control' name='Product[attribute_values][]'>";
                    $result .= '<option value="">Please select</option>';
                    $attributesValues = explode("##", $row['attribute_values']);
                    foreach ($attributesValues as $value) {
                        $tmp = explode("~~", $value);
                        $result .= "<option value='" . $tmp[0] . "'>" . $tmp[1] . "</option>";
                    }
                    $result .= "</select><div class=\"help-block\"></div></div></div><script>common.addvalidation('w0', 'product-attribute_" . $row['attribute_id'] . "','Product[attribute_values][]', '.field-product-attribute_values', 'Please select the value.');</script>";
                }
            }
            return $result;
        }
    }

    public function actionLoadDatatable($category_id = NULL)
    {
        $productCategory = \app\models\ProductCategories::find()
            ->where(['category_id' => $category_id])
            ->all();

        return $this->renderAjax('load-datatable', [
            'productCategory' => $productCategory
        ]);
    }

    public function actionGetList($type = null, $exclude = null, $att_set = null)
    {
        $requestData = Yii::$app->request->queryParams;
        $columns = array(
            0 => 'product_id',
            1 => 'name_en',
            2 => 'SKU',
            3 => 'final_price'
        );
        if (!empty($exclude)) {
            $associated_product = AssociatedProducts::find()->where('parent_id !=' . $exclude)->all();
        } else {
            $associated_product = AssociatedProducts::find()->all();
        }
        $associat_array = [];
        $query = \app\models\Product::find()
            ->select(['product.product_id', 'product.name_en', 'SKU', 'regular_price', 'final_price', 'base_currency_id', 'remaining_quantity', 'image', 'associated_products.associated_product_id', '(select count(*) from associated_products where child_id = `product`.`product_id`) as count_used_as_child'])
            ->join('LEFT JOIN', 'product_images', 'product.product_id = product_images.product_id')
            ->join('left join', 'associated_products', 'product.product_id = associated_products.parent_id')
            ->where(['product.is_deleted' => 0]);
        $query->join('LEFT JOIN', 'product_attribute_values', 'product.product_id = product_attribute_values.product_id');
        $query->join('LEFT JOIN', 'attribute_values', 'product_attribute_values.attribute_value_id = attribute_values.attribute_value_id');
        $query->join('LEFT JOIN', 'attributes', 'attribute_values.attribute_id = attributes.attribute_id');
        $query->join('LEFT JOIN', 'attribute_set_groups', 'attributes.attribute_id = attribute_set_groups.attribute_id');
        if ($exclude != null) {
            $query->andWhere(['!=', 'product.product_id', $exclude]);
        }

        if (\Yii::$app->session['_eyadatAuth'] == 5) {
            $query->andFilterWhere(['=', 'product.pharmacy_id', Yii::$app->user->identity->pharmacy_id]);
        }
        if ($type == 'R') {
            $query->andWhere([
                'OR',
                [
                    'AND',
                    ['=', 'show_as_individual', 1],
                    ['=', 'product.type', 'S']
                ],
                ['=', 'product.type', 'G'],
            ]);
            $query->having(['count_used_as_child' => 0]);
        } elseif ($type == 'A') {
            //$query->andWhere(['show_as_individual' => 0, 'product.type' => 'S']);
            $query->andWhere(['show_as_individual' => [0, 1], 'product.type' => 'S']);
            if (!empty($product_associate)) {
                $query->andWhere('product.product_id NOT IN(' . $product_associate . ')');
            }
        }
        if ($att_set != null) {
            $query->andWhere(['=', 'attribute_set_groups.attribute_set_id', $att_set]);
        }
        $query->groupBy(['product.product_id']);
        $query->orderBy(['count_used_as_child' => SORT_DESC, 'product.product_id' => SORT_DESC]);
        $data = $query->all();
        $totalData = count($data);
        $totalFiltered = $totalData;
        if (!empty($requestData['search']['value'])) {
            $query->andWhere([
                'AND',
                [
                    'OR',
                    ['LIKE', 'product.name_en', $requestData['search']['value']],
                    ['LIKE', 'SKU', $requestData['search']['value']],
                    ['LIKE', 'final_price', $requestData['search']['value']]
                ]
            ]);
        }
        $data = $query->all();
        $totalFiltered = count($data);
        $query->limit($requestData['length']);
        $query->offset($requestData['start']);
        // echo $query->createCommand()->rawSql; exit;
        $result = $query->all();
        $data1 = array();
        $data2 = array();
        $i = 1;
        foreach ($result as $key => $row) {
            $isAssociated = 0;
            if (isset($exclude) && $exclude != "") {
                if ($type == 'A') {
                    $checkAssociated = \app\models\AssociatedProducts::find()
                        ->where(['parent_id' => $exclude, 'child_id' => $row["product_id"]])
                        ->one();
                } else {
                    $checkAssociated = \app\models\RelatedProducts::find()
                        ->where(['product_id' => $exclude, 'related_id' => $row["product_id"]])
                        ->one();
                }
                if (!empty($checkAssociated)) {
                    $isAssociated = 1;
                }
            }
            $nestedData = array();
            $nestedData[] = $row["product_id"];
            $nestedData[] = $row["name_en"];
            $nestedData[] = $row["SKU"];
            $nestedData[] = $row["final_price"];
            if ($isAssociated == 1) {
                $data1[] = $nestedData;
            } else {
                $data2[] = $nestedData;
            }
            $i++;
        }

        $finalData = array_merge($data1, $data2);
        $json_data = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $finalData   // total data array
        );
        echo json_encode($json_data);
    }

    public function actionGetProductList($cid)
    {
        $requestData = Yii::$app->request->queryParams;
        $columns = array(
            0 => 'product_id',
            1 => 'name_en',
            2 => 'SKU',
            3 => 'final_price'
        );
        $sql = "select product_categories.product_id,product_categories.category_id,product.name_en,product.SKU,product.final_price
                from product_categories 
                left join product ON product.product_id = product_categories.product_id
                WHERE product.is_deleted = 0 AND product_categories.category_id = '" . $cid . "'
                GROUP BY product_categories.product_id";

        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $totalData = count($data);
        $totalFiltered = $totalData;
        // $sql.="WHERE 1=1";
        if (!empty($requestData['search']['value'])) {
            $sql .= " AND (name_en LIKE '" . $requestData['search']['value'] . "%' ";
            $sql .= " OR SKU LIKE '" . $requestData['search']['value'] . "%'";
            $sql .= " OR final_price LIKE '" . $requestData['search']['value'] . "%')";
        }
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $totalFiltered = count($data);
        $sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," .
            $requestData['length'] . "   ";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        $data = array();
        $i = 1;
        foreach ($result as $key => $row) {
            $nestedData = array();
            $nestedData[] = $row["product_id"];
            $nestedData[] = $row["name_en"];
            $nestedData[] = $row["SKU"];
            $nestedData[] = $row["final_price"];
            $data[] = $nestedData;
            $i++;
        }
        $json_data = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data   // total data array
        );
        echo json_encode($json_data);
    }

    public function actionRemoveCategoryProduct()
    {
        $request = Yii::$app->request->queryParams;
        if (!empty($request['id'])) {
            foreach ($request['id'] as $id) {
                $model = \app\models\ProductCategories::find()
                    ->where(['product_id' => $id, 'category_id' => $request['cid']])
                    ->one();
                $model->delete();
            }
        }
    }

    public function actionImageDelete()
    {
        $model = \app\models\ProductImages::findOne($_POST['key']);
        $oldimg = Yii::$app->basePath . '/web/uploads/' . $model->image;
        if (file_exists($oldimg)) {
            unlink($oldimg);
        }
        if ($model) {
            $model->delete();
        }
        echo json_encode(array());
    }

    public function actionApproved()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->approvedSearch(Yii::$app->request->queryParams);
        return $this->render('approved', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        //
        if ($model->type == 'S') {
            $child = \app\models\AssociatedProducts::find()
                ->where(['child_id' => $model->product_id])
                ->one();
            if (!empty($child)) {
                $child->delete();
            }
        } else {
            \app\models\AssociatedProducts::deleteAll('parent_id = ' . $model->product_id);
        }
        //
        \app\models\RelatedProducts::deleteAll('product_id = ' . $model->product_id);
        \app\models\RelatedProducts::deleteAll('related_id = ' . $model->product_id);
        Yii::$app->session->setFlash('success', 'Product successfully deleted');
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionApproveProduct($id)
    {
        if (isset($id) && $id != "") {
            $productIds = explode(',', $id);
            $processed = [];
            foreach ($productIds as $r) {
                $model = Product::find()
                    ->where(['product_id' => $r, 'is_deleted' => 0])
                    ->one();
                if (!empty($model)) {
                    $productStatusHistory = new \app\models\ProductStatusHistory();
                    $productStatusHistory->product_id = $model->product_id;
                    $productStatusHistory->product_status_id = 2;
                    $productStatusHistory->status_date = date('Y-m-d H:i:s');
                    $productStatusHistory->notify_customer = 0;
                    if ($productStatusHistory->save()) {
                        array_push($processed, $model->product_id);
                    }
                }
            }
            if (!empty($processed)) {
                echo json_encode([
                    'success' => '1',
                    'data' => $processed,
                    'msg' => 'Product successfully approved'
                ]);
            } else {
                echo json_encode([
                    'success' => '0',
                    'data' => "",
                    'msg' => 'Something went wrong try again'
                ]);
            }
        }
    }

    public function actionAddProductStock($id, $stock)
    {
        //throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to perform this action.'));
        if (isset($id) && isset($stock)) {
            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                $productModel = Product::find()
                    ->where(['product_id' => $id, 'is_deleted' => 0])
                    ->one();
            } elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
                $productModel = Product::find()
                    ->where(['product_id' => $id, 'is_deleted' => 0])
                    ->one();
            }
            if (!empty($productModel)) {
                $remainingQty = $productModel->remaining_quantity;
                $newQty = $remainingQty + $stock;
                if ($newQty < 0) {
                    return 'Stock cannot be reduced more than the existing quantity.';
                }
                $productModel->updateCounters(['remaining_quantity' => $stock]);

                AppHelper::adjustStock($id, $stock, "Adding stock from listing. Remaining quantity is {$productModel->remaining_quantity}.");

                $notifyFlag = \app\models\RemainingQuantityNotifyFlag::find()
                    ->where(['product_id' => $productModel->product_id])
                    ->one();
                if (!empty($notifyFlag)) {
                    $notifyFlag->delete();
                }
                return '1';
            } else {
                return 'Product does not exist';
            }
        }
    }

    public function actionBulkDelete($id)
    {
        if (isset($id) && $id != "") {
            $productIds = explode(',', $id);
            $processed = [];
            foreach ($productIds as $r) {
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    $model = Product::find()
                        ->where(['product_id' => $r, 'is_deleted' => 0])
                        ->one();
                } elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
                    $model = Product::find()
                        ->where(['product_id' => $r, 'is_deleted' => 0])
                        ->one();
                }
                if (!empty($model)) {
                    $model->is_deleted = 1;
                    if ($model->save()) {
                        array_push($processed, $model->product_id);
                    }
                }
            }
            if (!empty($processed)) {
                echo json_encode([
                    'success' => '1',
                    'data' => $processed,
                    'msg' => 'Product(s) successfully deleted'
                ]);
            } else {
                echo json_encode([
                    'success' => '0',
                    'data' => "",
                    'msg' => 'Something went wrong try again'
                ]);
            }
        }
    }

    public function actionEditPrice()
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->final_price = $request['value'];
            if ($request['value'] > $model->regular_price) {
                return json_encode([
                    'status' => false,
                    'msg' => "Final price can't be more then reguler price",
                ]);
            }
            if ($model->save()) {
                return json_encode([
                    'status' => true,
                    'data' => $model->final_price . ' ' . $model->baseCurrency->code
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors['regular_price'][0],
                ]);
            }
        }
    }

    public function actionEditCostPrice()
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            $model->cost_price = $request['value'];
            if ($model->save()) {
                return json_encode([
                    'status' => true,
                    'data' => $model->cost_price . ' ' . $model->baseCurrency->code
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors['regular_price'][0],
                ]);
            }
        }
    }

    public function actionPublish($id)
    {
        $model = $this->findModel($id);
        if ($model->is_active == 0) {
            $model->is_active = '1';
        } else {
            $model->is_active = '0';
        }
        if ($model->save(false)) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }

    public function actionGetApprovedReviewedList($type = null, $exclude = null, $category = null)
    {
        ini_set('memory_limit', '-1');
        $requestData = Yii::$app->request->queryParams;
        $columns = array(
            0 => 'product_id',
            1 => 'name_en',
            2 => 'SKU',
            3 => 'final_price'
        );
        $con = '';
        if ($exclude != null) {
            $con = ' AND product_id != "' . $exclude . '"';
        }

        if (!empty($requestData['search']['value'])) {
            $con .= " AND (name_en LIKE '" . $requestData['search']['value'] . "%' ";
            $con .= " OR SKU LIKE '" . $requestData['search']['value'] . "%'";
            $con .= " OR final_price LIKE '" . $requestData['search']['value'] . "%')";
        }

        $join = $order = '';
        if (!empty($category)) {
            $join = " LEFT JOIN product_categories ON product_categories.product_id = product.product_id AND category_id = '" . $category . "'";
            $order = " ORDER BY (CASE WHEN product_categories.product_category_id IS NOT NULL THEN 1 ELSE 0 END) DESC";
        }

        if ($type == null) {
            $sql = "SELECT  product.* from product " . $join . " where is_active = 1 AND is_deleted = 0 " . $con . $order;
        } else {
            $sql = "SELECT  product.* from product " . $join . " where is_active = 1 AND is_deleted = 0 and type = 'S' " . $con . $order;
        }
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $totalData = count($data);
        $totalFiltered = $totalData;
        // $sql.="WHERE 1=1";

        /* $data = Yii::$app->db->createCommand($sql)->queryAll();
          $totalFiltered = count($data); */

        if (empty($order)) {
            $order = " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'];
            $sql .= $order;
        }
        $sql .= "  LIMIT " . $requestData['start'] . " ," .
            $requestData['length'] . "   ";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        $data = array();
        $i = 1;
        foreach ($result as $key => $row) {
            $nestedData = array();
            $nestedData[] = $row["product_id"];
            $nestedData[] = $row["name_en"];
            $nestedData[] = $row["SKU"];
            $nestedData[] = $row["final_price"];
            $data[] = $nestedData;
            $i++;
        }
        $json_data = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data   // total data array
        );
        echo json_encode($json_data);
    }

    public function actionReviews($id)
    {
        $model = $this->findModel($id);
        return $this->render('reviews', [
            'model' => $model,
        ]);
    }

    public function actionPendingReviews()
    {
        $searchModel = new \app\models\ProductReviewsSearch();
        $searchModel->is_approved = [0, 2];
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('pending-reviews', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => 'Pending Reviews'
        ]);
    }

    public function actionApprovedReviews()
    {
        $searchModel = new \app\models\ProductReviewsSearch();
        $searchModel->is_approved = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('pending-reviews', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => 'Approved Reviews'
        ]);
    }

    public function actionReviewStatus()
    {
        $request = Yii::$app->request->bodyParams;
        $model = \app\models\ProductReviews::find()
            ->where(['product_review_id' => $request['pk']])
            ->one();
        if (!empty($model)) {
            $model->is_approved = $request['value'];
            if ($model->save()) {
                return 'Product review successfully updated';
            } else {
                return json_encode($model->errors);
            }
        }
    }

    public function actionDeleteReview($id)
    {
        $model = \app\models\ProductReviews::find()
            ->where(['product_review_id' => $id])
            ->one();
        if (!empty($model)) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Review successfully deleted');
            return $this->redirect(['reviews?id=' . $model->product_id]);
        }
    }

    public function actionDeletePendingReview($id)
    {
        $model = \app\models\ProductReviews::find()
            ->where(['product_review_id' => $id])
            ->one();
        if (!empty($model)) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Review successfully deleted');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionApproveReview($id)
    {
        $model = \app\models\ProductReviews::find()
            ->where(['product_review_id' => $id])
            ->one();
        if (!empty($model)) {
            $model->is_approved = 1;
            $model->save();
            Yii::$app->session->setFlash('success', 'Review successfully approved');
            return $this->redirect(['pending-reviews']);
        }
    }

    public function actionGetHistory($id)
    {
        if (isset($id) && $id != "") {
            if (\Yii::$app->session['_eyadatAuth'] == 5) {
                $model = Product::find()
                    ->where(['product_id' => $id, 'is_deleted' => 0])
                    ->one();
            } elseif (\Yii::$app->session['_eyadatAuth'] == 1) {
                $model = Product::find()
                    ->where(['product_id' => $id, 'is_deleted' => 0])
                    ->one();
            }
            $data = [];
            if (!empty($model)) {
                $productStatusHistory = \app\models\ProductStatusHistory::find()
                    ->where([
                        'product_id' => $model->product_id
                    ])
                    ->orderBy(['product_status_history_id' => SORT_DESC])
                    ->all();
                if (!empty($productStatusHistory)) {
                    foreach ($productStatusHistory as $row) {
                        $d = [
                            'status' => $row->productStatus->status_name_en,
                            'date' => date("M d, Y", strtotime($row->status_date)),
                            'comment' => ($row->comment != "") ? $row->comment : "",
                            'notify' => ($row->notify_customer == 0) ? "No" : "Yes",
                        ];
                        array_push($data, $d);
                    }
                }
            }
            return json_encode($data);
        }
    }

    public function actionAddStatus()
    {
        $request = Yii::$app->request->bodyParams;
        if (\Yii::$app->session['_eyadatAuth'] == 1) {
            $productHistory = new \app\models\ProductStatusHistory();
            $productHistory->product_id = $request['id'];
            $productHistory->product_status_id = $request['ProductStatusHistory']['product_status_id'];
            $productHistory->status_date = date('Y-m-d H:i:s');
            $productHistory->comment = $request['ProductStatusHistory']['comment'];
            $productHistory->notify_customer = $request['ProductStatusHistory']['notify_customer'];
            if ($productHistory->save()) {
                return json_encode(['status' => 200, 'msg' => 'Product status successfully updated.']);
            } else {
                return json_encode($productHistory->errors);
            }
        }
    }

    public function actionQuickProductForm($attset, $product_id = null, $brand_id = null, $manufacturer_id = null, $pharmacy_id = null)
    {
        $model = new Product();
        $model->scenario = 'quick-product';
        $attributes = \app\models\Attributes::find()
            ->select(['attributes.attribute_id', 'attributes.name_en', new \yii\db\Expression("GROUP_CONCAT(CONCAT(`attribute_values`.`attribute_value_id`, '~~', `attribute_values`.`value_en`) ORDER BY attribute_values.sort_order SEPARATOR '##') AS attribute_values")])
            ->join('LEFT JOIN', 'attribute_set_groups', 'attribute_set_groups.attribute_id = attributes.attribute_id')
            ->join('LEFT JOIN', 'attribute_values', 'attributes.attribute_id = attribute_values.attribute_id')
            ->where(['attribute_set_groups.attribute_set_id' => $attset])
            ->groupBy(['attributes.attribute_id'])
            ->asArray()->all();
        $result = "";
        if (!empty($attributes)) {
            foreach ($attributes as $row) {
                $result .= "<div class=\"col-md-6\"><div class='form-group field-product-attribute_values'><label class='control-label' for='product-attribute_" . $row['attribute_id'] . "'>" . $row['name_en'] . "</label>";
                $result .= "<select id='product-attribute_" . $row['attribute_id'] . "' class='select6 form-control' name='Product[attribute_values][]'>";
                $attributesValues = explode("##", $row['attribute_values']);
                $result .= '<option value="">Please select</option>';
                // debugPrint($attributesValues); exit;
                foreach ($attributesValues as $value) {
                    $tmp = explode("~~", $value);
                    if (isset($tmp[0]) && isset($tmp[1])) {
                        $result .= "<option value='" . $tmp[0] . "'>" . $tmp[1] . "</option>";
                    }
                }
                $result .= "</select></div></div>";
            }
        }
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                $request = Yii::$app->request->bodyParams;
                $sku = $request['Product']['qf-sku'];
                if (!empty($post['Product']['attribute_values'])) {
                    $filtered_array = array_filter($request['Product']['attribute_values']);
                    $attIds = implode(',', $filtered_array);
                    if (isset($attIds) && !empty($attIds) && $attIds != ",") {
                        $attributeValues = \app\models\AttributeValues::find()
                            ->select("attribute_values.value_en,attribute_values.value_ar")
                            ->where('attribute_value_id IN(' . $attIds . ')')
                            ->asArray()
                            ->all();
                    }
                }
                $attName = '';
                $productNameEn = "";
                if (isset($request['Product']['qf-name_en']) && $request['Product']['qf-name_en'] != "") {
                    $productNameEn .= $request['Product']['qf-name_en'] . '-';
                }
                $productNameAr = "";
                if (isset($request['Product']['qf-name_ar']) && $request['Product']['qf-name_ar'] != "") {
                    $productNameAr .= $request['Product']['qf-name_ar'] . '-';
                }
                if (!empty($attributeValues)) {
                    foreach ($attributeValues as $av) {
                        $attName .= strtolower(str_replace(' ', '', $av['value_en'])) . '-';
                        $productNameEn .= $av['value_en'] . '-';
                        $productNameAr .= $av['value_en'] . '-';
                    }
                }
                $productNameEn = substr($productNameEn, 0, strlen($productNameEn) - 1);
                $productNameAr = substr($productNameAr, 0, strlen($productNameAr) - 1);

                $model->name_en = $productNameEn;
                $model->name_ar = $productNameAr;

                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    $model->admin_id = Yii::$app->user->identity->admin_id;
                }
                $model->type = 'S';
                $model->posted_date = date('Y-m-d H:i:s');
                $model->remaining_quantity = 0;
                $model->base_currency_id = 82;
                $attributeSetId = $request['Product']['attribute_set_id'];
                $model->attribute_set_id = $attributeSetId;
                $model->SKU = $request['Product']['SKU'];
                $model->barcode = $request['Product']['barcode'];
                $model->supplier_barcode = $request['Product']['supplier_barcode'];
                $model->cost_price = $request['Product']['qf_cost_price'];
                $model->regular_price = $request['Product']['qf_regular_price'];
                $model->final_price = $request['Product']['qf_final_price'];
                $model->product_margin = $request['Product']['qf_product_margin'];
                $model->is_active = 1;
                $model->show_as_individual = 0;

                if (empty($model->barcode)) {
                    $model->barcode = \app\helpers\ProductHelper::generateBarcodeProduct();
                }
                if (!empty($model->barcode)) {
                    $newBarcode = $model->barcode;
                    $generator = new BarcodeGeneratorJPG();
                    //$barcode = $generator->getBarcode($newBarcode, $generator::TYPE_EAN_13, 4, 45);
                    $barcode = $generator->getBarcode($newBarcode, $generator::TYPE_CODE_128, 4, 45);
                    $barcodeName = 'barcode-' . $newBarcode . '.jpg';
                    file_put_contents(Yii::getAlias('@webroot') . '/uploads/' . $barcodeName, $barcode);
                    $model->barcode = $newBarcode;
                }

                if ($model->save(false)) {
                    if (!empty($model->barcode))
                        \app\helpers\AppHelper::generateBarCodeTemplate($model);
                    //product status
                    $productStatusHistory = new \app\models\ProductStatusHistory();
                    $productStatusHistory->product_id = $model->product_id;
                    $productStatusHistory->product_status_id = 2;
                    $productStatusHistory->status_date = date('Y-m-d H:i:s');
                    $productStatusHistory->notify_customer = 0;
                    $productStatusHistory->save();
                    //product attributes
                    if (!empty($request['Product']['attribute_values'])) {
                        foreach ($request['Product']['attribute_values'] as $row) {
                            $attValue = new \app\models\ProductAttributeValues();
                            $attValue->attribute_value_id = $row;
                            $attValue->product_id = $model->product_id;
                            $attValue->save();
                        }
                    }
                    //product image
                    if (isset($request['Product']['quick_product_image'])) {
                        $count = \app\models\ProductImages::find()
                            ->count();
                        $productImage = new \app\models\ProductImages();
                        $productImage->product_id = $model->product_id;
                        $productImage->image = $request['Product']['quick_product_image'];
                        $productImage->sort_order = $count + 1;
                        $productImage->save();
                    }
                    //associated product
                    if ($product_id != null) {
                        $associatedProduct = new \app\models\AssociatedProducts();
                        $associatedProduct->child_id = $model->product_id;
                        $associatedProduct->parent_id = $product_id;
                        $associatedProduct->save();
                    }
                    //category
                    if ($product_id != null) {
                        $categories = \app\models\ProductCategories::find()->where(['product_id' => $product_id])->all();
                        if (!empty($categories)) {
                            foreach ($categories as $row) {
                                $productChildCategory = new \app\models\ProductCategories();
                                $productChildCategory->product_id = $model->product_id;
                                $productChildCategory->category_id = $row->category_id;
                                $productChildCategory->save();
                            }
                        }
                    }
                    //echo $request['Product']['store_id'];die;
                    if (!empty($request['Product']['store_id'])) {
                        foreach ($request['Product']['store_id'] as $strId) {
                            $storeProduct = new \app\models\StoreProducts();
                            $storeProduct->store_id = $strId;
                            $storeProduct->product_id = $model->product_id;
                            $storeProduct->save();
                        }
                    }

                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                    $message = 'Product successfully added';
                    return [
                        'success' => 1,
                        'msg' => $message,
                        'child_id' => $model->product_id,
                    ];
                } else {
                    $error = \yii\widgets\ActiveForm::validate($model);
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $error;
                }
            }
        }

        return $this->renderAjax('quick-poduct-form', [
            'model' => $model,
            'result' => $result,
            'sku' => isset($sku) ? $sku : "",
            'attset' => $attset,
            'brand_id' => $brand_id,
            'manufacturer_id' => $manufacturer_id,
            'pharmacy_id' => $pharmacy_id,
        ]);
    }

    public function actionValidate()
    {
        $model = new Product();
        //$model->scenario = 'quick-product';
        $request = \Yii::$app->getRequest();
        $model->posted_date = date('Y-m-d H:i:s');
        //debugPrint($request);exit;
        if ($request->isPost && $model->load($request->post())) {
            $post = Yii::$app->request->bodyParams;
            if (!empty($post['Product']['attribute_values'])) {
                $filtered_array = array_filter($post['Product']['attribute_values']);
                $attIds = implode(',', $filtered_array);
                if (isset($attIds) && !empty($attIds) && $attIds != ",") {
                    $attributeValues = \app\models\AttributeValues::find()
                        ->select("attribute_values.value_en,attribute_values.value_ar")
                        ->where('attribute_value_id IN(' . $attIds . ')')
                        ->asArray()
                        ->all();
                }
            }
            $attName = '';
            $productNameEn = '-';
            $productNameAr = '-';
            if (!empty($attributeValues)) {
                foreach ($attributeValues as $av) {
                    $attName .= strtolower(str_replace(' ', '', $av['value_en'])) . '-';
                    $productNameEn .= $av['value_en'] . '-';
                    $productNameAr .= $av['value_en'] . '-';
                }
            }
            $productNameEn = substr($productNameEn, 0, strlen($productNameEn) - 1);
            $productNameAr = substr($productNameAr, 0, strlen($productNameAr) - 1);

            // $sku = \app\helpers\ProductHelper::generateSkuProduct();
            $model->name_en = ($productNameEn != '') ? $productNameEn : '-';
            $model->name_ar = ($productNameAr != '') ? $productNameAr : '-';
            // $model->SKU = $sku;
            $model->base_currency_id = 82;
            $model->final_price = $post['Product']['qf_final_price'];
            $model->regular_price = $post['Product']['qf_regular_price'];
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
    }

    public function actionFeature($id)
    {
        $model = $this->findModel($id);

        if ($model->is_featured == 0) {
            $model->is_featured = '1';
        } else {
            $model->is_featured = '0';
        }
        if ($model->validate() && $model->save()) {
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }

    public function actionGetAssociatedList($type = null, $exclude = null)
    {
        $requestData = Yii::$app->request->queryParams;
        $columns = array(
            0 => 'product_id',
            1 => 'name_en',
            2 => 'SKU',
            3 => 'final_price'
        );

        $query = \app\models\Product::find()
            ->select(['product.product_id', 'name_en', 'SKU', 'regular_price', 'final_price', 'base_currency_id', 'remaining_quantity', 'image'])
            ->join('LEFT JOIN', 'product_images', 'product.product_id = product_images.product_id')
            //->join('LEFT JOIN', 'associated_products', 'associated_products.parent_id = product.product_id')
            ->where(['product.is_active' => 1, 'product.is_deleted' => 0]);

        $query->join('LEFT JOIN', '(SELECT t1.*
                                    FROM product_status_history AS t1
                                    LEFT OUTER JOIN product_status_history AS t2 ON t1.product_id = t2.product_id 
                                          AND (t1.status_date < t2.status_date 
                                           OR (t1.status_date = t2.status_date AND t1.product_status_history_id < t2.product_status_history_id))
                                    WHERE t2.product_id IS NULL) as temp', 'temp.product_id = product.product_id');

        $query->andWhere(['temp.product_status_id' => 2]);
        $data = $query->groupBy(['product.product_id'])->all();
        $totalData = count($data);
        $totalFiltered = $totalData;
        // $sql.="WHERE 1=1";
        if (!empty($requestData['search']['value'])) {
            $query->andWhere([
                'AND',
                [
                    'OR',
                    ['LIKE', 'name_en', $requestData['search']['value']],
                    ['LIKE', 'SKU', $requestData['search']['value']],
                    ['LIKE', 'final_price', $requestData['search']['value']]
                ]
            ]);
        }

        $data = $query->all();
        $totalFiltered = count($data);

        $query->orderBy([$columns[$requestData['order'][0]['column']] => $requestData['order'][0]['dir']]);
        $query->limit($requestData['length']);
        $query->offset($requestData['start']);

        $result = $query->all();
        $data = array();
        $i = 1;
        foreach ($result as $key => $row) {
            $nestedData = array();
            $nestedData[] = $row["product_id"];
            $nestedData[] = $row["name_en"];
            $nestedData[] = $row["SKU"];
            $nestedData[] = $row["final_price"];
            $data[] = $nestedData;
            $i++;
        }
        $json_data = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data   // total data array
        );
        echo json_encode($json_data);
    }

    public function actionAddMoreCar($n)
    {
        $n = $n + 1;
        return $this->renderAjax('_add-more-car', [
            'n' => $n
        ]);
    }

    public function actionRemoveMoreCar($eid)
    {
        $model = \app\models\ProductEngines::find()
            ->where(['product_engine_id' => $eid])
            ->one();

        if (!empty($model)) {
            $model->delete();
            return '1';
        }
    }

    public function actionImageUpload()
    {
        $imageFile = UploadedFile::getInstanceByName('Product[img]');
        $directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR;
        //debugPrint($imageFile);exit;;
        if ($imageFile) {
            $filetype = mime_content_type($imageFile->tempName);
            $allowed = array('image/png', 'image/jpeg', 'image/gif');
            if (!in_array(strtolower($filetype), $allowed)) {
                return json_encode([
                    'files' => [
                        'error' => "File type not supported",
                    ]
                ]);
            } else {
                $uid = uniqid(time(), true);
                $fileName = $uid . '.' . $imageFile->extension;
                $filePath = $directory . $fileName;
                if ($imageFile->saveAs($filePath)) {

                    $destNameWithExt = $fileName;
                    $destNameJpg = $uid . ".jpg";

                    AppHelper::resize('uploads/' . $destNameWithExt, 'uploads/' . $destNameJpg, 1000, 1000, 100);

                    if ($destNameWithExt != $destNameJpg) {
                        @unlink('uploads/' . $destNameWithExt);
                    }
                    $path = \yii\helpers\BaseUrl::home() . 'uploads/' . $destNameJpg;

                    return json_encode([
                        'files' => [
                            'name' => $destNameJpg,
                            'size' => $imageFile->size,
                            "url" => $path,
                            "thumbnailUrl" => $path,
                            "deleteUrl" => 'image-delete?name=' . $destNameJpg,
                            "deleteType" => "POST",
                            'error' => ""
                        ]
                    ]);
                }
            }
        }
        return '';
    }

    public function actionDeleteImage($src, $id = null)
    {
        if (isset($id) && $id != null) {
            $model = \app\models\ProductImages::find()
                ->where(['product_image_id' => $id])
                ->one();
            if (!empty($model)) {
                $model->delete();
            }
        }
        $src = basename($src);
        $file = 'uploads/' . $src;
        @unlink($file);
        echo '1';
    }

    public function actionMetaDataExport($lang = 'en')
    {
        set_time_limit(0);
        //ini_set('max_execution_time', '0');
        ini_set('memory_limit', -1);

        $queryParam = Yii::$app->request->queryParams;

        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->export($queryParam, 1);
        // debugPrint(count($dataProvider)); exit;

        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $objPHPExcel->getProperties()->setCreator("Wishlist")
            ->setTitle('Meta Data')
            ->setKeywords("phpExcel");
        $objPHPExcel->setActiveSheetIndex(0);
        //excel columns
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'id');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'title');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'ios_url');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'ios_app_store_id');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'ios_app_name');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'android_url');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'android_package');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'android_app_name');
        // $objPHPExcel->getActiveSheet()->setCellValue('A1', 'product:retailer_item_id');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'description');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'image_link');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'availability');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'price');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'sale_price');
        $objPHPExcel->getActiveSheet()->setCellValue('N1', 'brand');
        $objPHPExcel->getActiveSheet()->setCellValue('O1', 'condition');
        $objPHPExcel->getActiveSheet()->setCellValue('P1', 'link');
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'override');
        $objPHPExcel->getActiveSheet()->setCellValue('R1', 'google_product_category');
        // $objPHPExcel->getActiveSheet()->setCellValue('G1', 'product:price:currency');

        $n = 2;
        foreach ($dataProvider as $model) {
            $brand = (!empty($model->brand) ? $model->brand->{'name_' . $lang} : "TWL");
            $image = $model->getProductImage($model->product_id);
            if (empty($image))
                $image = Yii::$app->urlManager->createAbsoluteUrl('images/placeholderImg.jpg');
            $availability = ($model->remaining_quantity > 0) ? 'in stock' : 'out of stock';

            $title = preg_replace_callback('/([.!?])\s*(\w)/', function ($matches) {
                return strtoupper($matches[1] . ' ' . $matches[2]);
            }, ucfirst(strtolower($model->{'name_' . $lang})));

            $description = '';
            $description_lang = trim($model->{'description_' . $lang});
            if (!empty($description_lang)) {
                $description = preg_replace_callback('/([.!?])\s*(\w)/', function ($matches) {
                    return strtoupper($matches[1] . ' ' . $matches[2]);
                }, ucfirst(strtolower($description_lang)));
            } else {
                $description = $title;
            }

            $categories = [];
            // Category in format Men > Accessories > Watches
            foreach ($model->productCategories as $productCategory) {
                $categoryModel = $productCategory->category;
                if (!empty($categoryModel)) {
                    $level = $categoryModel->lvl;
                    $categoryName = $categoryModel->{'name_' . $lang};
                    /* if ($level > 0) {
                      for ($lvl = 1; $lvl <= $level; $lvl++){
                      $parent = $categoryModel->parents($lvl)->one();
                      $categoryName = $parent->{'name_'.$lang}." > ".$categoryName;
                      }
                      } */
                    $categories[] = $categoryName;
                    break;
                }
            }

            $categoryStr = !empty($categories) ? implode("\n", $categories) : '';
            // $categoryStr = '';
            $deeplink_url = (!empty($model->deeplink_url)) ? $model->deeplink_url : 'https://app.shop-twl.com';

            // $objPHPExcel->getActiveSheet()->setCellValue('A' . $n, $model->SKU);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $n, $model->product_id);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $n, $title);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $n, $deeplink_url);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $n, "1462076096");
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $n, "THE WISH LIST");
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $n, $deeplink_url);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $n, "com.leza.wishlist");
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $n, "THE WISH LIST");
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $n, $description);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $n, $image);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $n, $availability);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $n, 'KWD' . number_format($model->regular_price, 2));
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $n, 'KWD' . number_format($model->final_price, 2));
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $n, $brand);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $n, 'new');
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $n, "https://www.shop-twl.com//product/detail/" . $model->product_id);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $n, '');
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $n, $categoryStr);
            // debugPrint($categoryStr); exit;
            $objPHPExcel->getActiveSheet()->getStyle('R' . $n)->getAlignment()->setWrapText(true);

            $n++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Meta Data');
        header('Content-Type: text/csv;charset=UTF-8');
        header('Content-Disposition: attachment;filename="ProductList-' . date('YmdHis') . '.csv"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        header('Cache-Control: max-age=0');

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Csv");
        $objWriter->save('php://output');
        exit;
    }

    public function actionSendPush($id, $msg, $title = "")
    {
        if (isset($id) && $id != "" && isset($msg) && $msg != "") {

            $model = $this->findModel($id);

            if (empty($model)) {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Product does not exist'
                ]);
            } else {
                date_default_timezone_set(Yii::$app->params['timezone']);
                $notification = new \app\models\Notifications();
                $notification->title    = $title;
                $notification->message  = $msg;
                $notification->user_id  = "";
                $notification->target   = "P";
                $notification->target_id = $model->product_id;
                $notification->posted_date = date('Y-m-d H:i:s');
                $notification->save(false);
                \app\helpers\AppHelper::sendPushwoosh($msg, '', "P", $model->product_id, $title, '', $model->name_en, $model->name_ar);

                return json_encode([
                    'success' => '1',
                    'msg' => 'Push successfully sent'
                ]);
            }
        }
    }

    public function actionEditAttributeSet()
    {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request)) {
            $model = $this->findModel($request['pk']);
            if (\Yii::$app->session['_boutiqatAuth'] == 2 || \Yii::$app->session['_boutiqatAuth'] == 4) {
                if (Yii::$app->user->identity->boutique_id != $model->boutique_id) {
                    return json_encode([
                        'msg' => 'You are not authorized to view this page.'
                    ]);
                }
            }

            $model->attribute_set_id = $request['value'];
            if ($model->save()) {
                \app\models\ProductAttributeValues::deleteAll('product_id = ' . $model->product_id);
                foreach ($model->associatedProducts as $p) {
                    $child = $this->findModel($p->child_id);
                    $child->attribute_set_id = $request['value'];
                    $child->save(false);
                    //
                    \app\models\ProductAttributeValues::deleteAll('product_id = ' . $child->product_id);
                }
                return json_encode([
                    'status' => true,
                    'data' => $model->attributeSet->name_en
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'msg' => $model->errors['regular_price'][0],
                ]);
            }
        }
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionStock()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy(['remaining_quantity' => SORT_ASC]);

        return $this->render('stock', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStockDifference()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->stockDifference(Yii::$app->request->queryParams);

        return $this->render('stock-difference', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionStockMovement($id)
    {
        $model = \app\models\ProductStocks::find()
            ->where(['product_id' => $id])
            ->orderBy(['product_stock_id' => SORT_DESC])
            ->all();

        return $this->render('movement', ['model' => $model]);
    }

    public function actionAddRemoveStock($id, $stock, $radio, $message = "NULL message")
    {
        //print_r($radio);exit();
        if (isset($id) && isset($stock) && isset($radio)) {
            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                $productModel = Product::find()
                    ->where(['product_id' => $id, 'is_deleted' => 0])
                    ->one();
            } elseif (\Yii::$app->session['_eyadatAuth'] == 5) {
                $productModel = Product::find()
                    ->where(['product_id' => $id, 'is_deleted' => 0, 'shop_id' => Yii::$app->user->identity->shop_id])
                    ->one();
            }
            if (!empty($productModel)) {
                if ($radio == "add" && $stock > 0) {
                    $productModel->updateCounters(['remaining_quantity' => $stock]);

                    if (empty($message)) {
                        $message = "Remaining quantity is {$productModel->remaining_quantity}. :product/add-remove-stock";
                    } else {
                        $message .= ". Remaining quantity is {$productModel->remaining_quantity}. :product/add-remove-stock";
                    }

                    AppHelper::adjustStock($id, $stock, $message);

                    $notifyFlag = \app\models\RemainingQuantityNotifyFlag::find()
                        ->where(['product_id' => $productModel->product_id])
                        ->one();
                    if (!empty($notifyFlag)) {
                        $notifyFlag->delete();
                    }

                    return json_encode([
                        'success' => '1',
                        'msg' => 'Stock Successfully Added'
                    ]);
                } else {
                    if ($productModel->remaining_quantity > $stock) {
                        $productModel->updateCounters(['remaining_quantity' => -$stock]);

                        if (empty($message)) {
                            $message = "Remaining quantity is {$productModel->remaining_quantity}. :product/add-remove-stock";
                        } else {
                            $message .= ". Remaining quantity is {$productModel->remaining_quantity}. :product/add-remove-stock";
                        }

                        AppHelper::adjustStock($id, -$stock, $message);

                        $notifyFlag = \app\models\RemainingQuantityNotifyFlag::find()
                            ->where(['product_id' => $productModel->product_id])
                            ->one();
                        if (!empty($notifyFlag)) {
                            $notifyFlag->delete();
                        }

                        return json_encode([
                            'success' => '1',
                            'msg' => 'Stock Successfully Removed'
                        ]);
                    } else {
                        return json_encode([
                            'success' => '0',
                            'msg' => 'Remaining stock quantity should be greater than removed quantity'
                        ]);
                    }
                }
            } else {
                return json_encode([
                    'success' => '0',
                    'msg' => 'Product does not exist'
                ]);
            }
        }
    }

    public function actionSingle()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->Singlesearch(Yii::$app->request->queryParams);

        return $this->render('single', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGroup()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->Groupedsearch(Yii::$app->request->queryParams);

        return $this->render('single', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSearchProduct($term)
    {
        $query = \app\models\Product::find()
            ->select([
                'product.product_id',
                'product.name_en',
                'SKU',
                '(select count(*) from associated_products where child_id = `product`.`product_id`) as count_used_as_child'
            ])
            ->where([
                'OR',
                ['like', 'name_en', '%' . $term . '%', false,],
                ['like', 'SKU', '%' . $term . '%', false,],
            ])
            ->andWhere(['product.is_deleted' => 0]);
        $query->andWhere([
            'OR',
            [
                'AND',
                ['=', 'show_as_individual', 1],
                ['=', 'product.type', 'S']
            ],
            ['=', 'product.type', 'G'],
        ]);
        $query->having(['count_used_as_child' => 0]);
        $query->orderBy(['name_en' => SORT_ASC]);
        $query->limit(100);
        //echo $query->createCommand()->rawSql;
        $models = $query->all();
        $data = [];
        foreach ($models as $row) {
            $name = $row->name_en . '(' . $row->SKU . ')';
            $d = [
                'id' => $row->product_id,
                'completeName' => $name,
                'slug' => $row->name_en . '(' . $row->SKU . ')',
            ];
            array_push($data, $d);
        }
        return json_encode($data);
    }

    public function actionBulkUpload()
    {
        $model = new Product();
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->post();
            $main_arr = [];
            $errorArr = [];
            $errorNo = 1;
            $successNo = 0;
            if (!empty($request['Product']['images'])) {
                $uniqueSku = array_unique($request['Product']['SKU']);
                $size = sizeof($request['Product']['images']);
                foreach ($uniqueSku as $pSku) {
                    $mainArr = [];
                    for ($i = 0; $i < $size; $i++) {
                        if ($pSku == $request['Product']['SKU'][$i]) {
                            $sku = $request['Product']['SKU'][$i];
                            $productImage = $request['Product']['images'][$i];
                            $position = $request['Product']['original_name'][$i];
                            $positionArr = explode("_", $position);
                            if (!isset($positionArr[1])) {
                                $originalName = $positionArr[0];
                                $originalName = '<b>' . $originalName . '</b>';
                                array_push($errorArr, $errorNo . '. ' . $originalName . ' file does not contain valid SKU.');
                                $errorMessages = '';
                                foreach ($errorArr as $err) {
                                    $errorMessages .= $err . '<br>';
                                }
                                if (!empty($errorMessages)) {
                                    \Yii::$app->session->setFlash('error', $errorMessages);
                                }
                                $model = new Product();
                                return $this->render('bulk-upload', [
                                    'model' => $model,
                                ]);
                            }
                            $truePosition = (int) $positionArr[1];
                            $mainArr[$truePosition][] = $sku;
                            $mainArr[$truePosition][] = $productImage;
                            $mainArr[$truePosition][] = $request['Product']['original_name'][$i];
                        }
                    }
                    ksort($mainArr);
                    foreach ($mainArr as $pImage) {
                        $sku = $pImage[0];
                        $productImageName = $pImage[1];
                        $productImgageOriginal = $pImage[2];
                        $model = Product::find()->where(['TRIM(SKU)' => trim($sku), 'is_deleted' => 0])->one();
                        if (!empty($model)) {
                            $productImage = new \app\models\ProductImages();
                            $productImage->product_id = $model->product_id;
                            $productImage->image = $productImageName;
                            $productImage->save();
                            $successNo++;
                        } else {
                            $fileName = $productImageName;
                            $filepath = Yii::getAlias('@webroot') . '/upload/' . $fileName;
                            if (file_exists($filepath)) {
                                unlink($filepath);
                            }
                            $originalName = $productImgageOriginal;
                            $originalName = '<b>' . $originalName . '</b>';
                            array_push($errorArr, $errorNo . '. ' . $originalName . ' file does not contain valid SKU .');
                            $errorNo++;
                        }
                    }
                }
                if ($successNo > 0) {
                    Yii::$app->session->setFlash('success', $successNo . ' product image(s) successfully added');
                    return $this->redirect(['bulk-upload']);
                }
                $errorMessages = '';
                foreach ($errorArr as $err) {
                    $errorMessages .= $err . '<br>';
                }
                if (!empty($errorMessages)) {
                    \Yii::$app->session->setFlash('error', $errorMessages);
                }
                $model = new Product();
                return $this->render('bulk-upload', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('bulk-upload', [
                'model' => $model,
            ]);
        }
    }

    public function actionBulkImageUpload()
    {
        $imageFile = UploadedFile::getInstanceByName('Product[img]');
        $directory = \Yii::getAlias('@app/web/uploads') . DIRECTORY_SEPARATOR;
        if (isset($imageFile)) {
            $filetype = mime_content_type($imageFile->tempName);
            $allowed = array('image/png', 'image/jpeg', 'image/gif', 'image/png');
            if (!in_array(strtolower($filetype), $allowed)) {
                return json_encode([
                    'files' => [
                        'error' => "File type not supported",
                    ]
                ]);
            } else {
                $imageName = $imageFile->name;
                $nameExplodedDot = explode('.', $imageName);
                $nameExploded = explode('_', $nameExplodedDot[0]);
                $sku = $nameExploded[0];
                $uid = uniqid(time(), true);
                $fileName = $uid . '.' . $imageFile->extension;
                $filePath = $directory . $fileName;
                if ($imageFile->saveAs($filePath)) {
                    AppHelper::resize('uploads/' . $fileName, 'uploads/' . $fileName, 1000, 1000, 100);
                    $path = \yii\helpers\BaseUrl::home() . 'uploads/' . $fileName;
                    return json_encode([
                        'files' => [
                            'name' => $fileName,
                            'size' => $imageFile->size,
                            "url" => $path,
                            "thumbnailUrl" => $path,
                            "deleteUrl" => 'image-delete?name=' . $fileName,
                            "deleteType" => "POST",
                            'error' => "",
                            'original_name' => $imageName,
                            'sku' => $sku,
                        ]
                    ]);
                }
            }
        }
        return '';
    }

    public function actionExport()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->export(Yii::$app->request->queryParams);
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $objPHPExcel->getProperties()->setCreator("Eyadat")
            ->setTitle('Sheet1')
            ->setKeywords("phpExcel");
        $objPHPExcel->setActiveSheetIndex(0);
        //excel columns
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '_sku');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '_product_name_en');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '_product_name_ar');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '_short_description_en');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '_short_description_ar');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '_specification_en');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '_specification_ar');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', '_barcode');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', '_type');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', '_category');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', '_brand');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', '_manufacturer');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', '_description_en');
        $objPHPExcel->getActiveSheet()->setCellValue('N1', '_description_ar');
        $objPHPExcel->getActiveSheet()->setCellValue('O1', '_attribute_set_code');
        $objPHPExcel->getActiveSheet()->setCellValue('P1', '_status');
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', '_qty');
        $objPHPExcel->getActiveSheet()->setCellValue('R1', '_currency');
        $objPHPExcel->getActiveSheet()->setCellValue('S1', '_regular_price');
        $objPHPExcel->getActiveSheet()->setCellValue('T1', '_final_price');
        $objPHPExcel->getActiveSheet()->setCellValue('U1', '_cost_price');
        $objPHPExcel->getActiveSheet()->setCellValue('V1', '_discount');
        $objPHPExcel->getActiveSheet()->setCellValue('W1', '_discounted_unit_price');
        $objPHPExcel->getActiveSheet()->setCellValue('X1', '_pharmacy');
        $objPHPExcel->getActiveSheet()->setCellValue('Y1', '_supplier_barcode');
        $objPHPExcel->getActiveSheet()->setCellValue('Z1', '_qty_sold');

        $n = 2;
        foreach ($dataProvider as $model) {
            $brand = (!empty($model->brand) ? $model->brand->name_en : "");
            $manufacturer = (!empty($model->manufacturer) ? $model->brand->name_en : "");
            $type = ($model->type == 'G' || $model->show_as_individual == 0) ? 'Grouped' : 'Simple';
            $attribute_set_code = (!empty($model->attributeSet)) ? $model->attributeSet->attribute_set_code : '';

            $categories = [];
            $quantity_sold = 0;
            $stocks = OrderItems::find()
                ->select(['SUM(quantity) as total'])
                ->join('left join', 'pharmacy_orders', 'order_items.pharmacy_order_id = pharmacy_orders.pharmacy_order_id')
                ->join('left join', 'orders', 'pharmacy_orders.order_id = orders.order_id')
                ->join('LEFT JOIN', '(SELECT t1.*
                                            FROM order_status AS t1
                                            LEFT OUTER JOIN order_status AS t2 ON t1.order_id = t2.order_id
                                                  AND (t1.status_date < t2.status_date
                                                   OR (t1.status_date = t2.status_date AND t1.order_status_id < t2.order_status_id))
                                            WHERE t2.order_id IS NULL) as temp', 'temp.order_id = orders.order_id')
                ->where(['product_id' => $model->product_id, 'orders.is_processed' => 1])
                ->andWhere(['!=', 'temp.status_id', 6])
                ->asArray()
                ->one();
            if (!empty($stocks) && isset($stocks['total'])) {
                $quantity_sold = $stocks['total'];
            } else {
                $quantity_sold = 0;
            }

            foreach ($model->productCategories as $productCategory) {
                if (!empty($productCategory->category)) {
                    $inner_child_arr = [];
                    $pCategory = $productCategory->category;
                    $parentList = $pCategory->parents()->all(); /* Getting all Parents of this particular element in loop */
                    if (!empty($parentList)) {
                        foreach ($parentList as $parent) {
                            $inner_child_arr[] = trim($parent->name_en);
                        }
                    }
                    $inner_child_arr[] = trim($productCategory->category->name_en);
                    if (!empty($inner_child_arr)) {
                        $categories[] = implode(",", $inner_child_arr);
                    }
                }
            }
            $categoryStr = !empty($categories) ? implode('#', $categories) : '';
            $productAttrbute = ProductAttributeValues::find()
                ->select(['product_attribute_values.*', 'attribute_values.value_en', 'attribute_values.value_ar', 'attributes.code', 'attributes.name_en'])
                ->join('left join', 'attribute_values', 'product_attribute_values.attribute_value_id = attribute_values.attribute_value_id')
                ->join('left join', 'attributes', 'attribute_values.attribute_id = attributes.attribute_id')
                ->where(['product_id' => $model->product_id])
                ->asArray()
                ->all();

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $n, $model->SKU);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $n, $model->name_en);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $n, $model->name_ar);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $n, $model->short_description_en);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $n, $model->short_description_ar);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $n, $model->specification_en);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $n, $model->specification_ar);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $n, $model->barcode);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $n, $type);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $n, $categoryStr); //categories
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $n, $brand);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $n, $manufacturer);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $n, $model->description_en);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $n, $model->description_ar);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $n, $attribute_set_code);
            $status = ($model->is_active) ? 'Active' : 'Inactive';
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $n, $status);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $n, $model->remaining_quantity);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $n, 'KWD');
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $n, $model->regular_price);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $n, $model->final_price);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $n, $model->cost_price);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $n, ((($model->regular_price - $model->final_price) / $model->regular_price) * 100));
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $n, $model->final_price);
            $pharmaModel = $model->pharmacy;
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $n, ((!empty($pharmaModel)) ? $pharmaModel->name_en : ''));
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $n, $model->supplier_barcode);
            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $n, $quantity_sold);
            $n++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ProductList-' . date('YmdHis') . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xls');
        $objWriter->save('php://output');
        exit;
    }

    public function actionExcelImport()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $model = new ExcelUpload();
        if ($model->load(Yii::$app->request->post())) {
            $excel = UploadedFile::getInstance($model, 'file');
            if ($excel) {
                $model->file = 'products-import-' . time() . '.' . $excel->extension;
                $upload_path = Yii::$app->basePath . '/web/uploads/';
                $path = $upload_path . $model->file;
                $excel->saveAs($path);
                $objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                //debugPrint($sheetData); exit;
                $errorCount = 0;
                $errorArr = [];
                $successCount = 0;
                $successProductArr = [];
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    for ($i = 2; $i <= count($sheetData); $i++) {
                        if (!empty($sheetData[$i]['C'])) {
                            $type = 'S';

                            $sku = trim($sheetData[$i]['A']);
                            $parent_sku = trim($sheetData[$i]['B']);
                            $product_name_en = trim($sheetData[$i]['C']);
                            $product_name_ar = trim($sheetData[$i]['D']);
                            $barcode = trim($sheetData[$i]['E']);
                            $supplier_barcode = trim($sheetData[$i]['F']);
                            $pharmacy = trim(strtolower($sheetData[$i]['H']));
                            $manufacturer = trim(strtolower($sheetData[$i]['I']));
                            $brand = trim(strtolower($sheetData[$i]['J']));
                            $short_desc_en = trim(strtolower($sheetData[$i]['K']));
                            $_short_desc_en = trim(strtolower($sheetData[$i]['L']));
                            $_description_en = trim(strtolower($sheetData[$i]['M']));
                            $_description_ar = trim(strtolower($sheetData[$i]['N']));
                            $_description_en = trim(strtolower($sheetData[$i]['O']));
                            $_description_ar = trim(strtolower($sheetData[$i]['P']));
                            $_attribute_set_code = trim(strtolower($sheetData[$i]['Q']));
                            $_attribute_value_1 = trim(strtolower($sheetData[$i]['R']));
                            $_attribute_value_2 = trim(strtolower($sheetData[$i]['S']));
                            $regular_price = trim($sheetData[$i]['T']);
                            $final_price = trim($sheetData[$i]['U']);
                            if (empty($sku) || empty($barcode) || empty($pharmacy) || empty($brand) || empty($sheetData[$i]['C']) || empty($regular_price) || empty($final_price)) {
                                Yii::$app->session->setFlash('error', "Error occured at row number {$i} while importing products. <br>Please check <strong>_sku, _product_name_en, _product_name_ar, _barcode, _regular_price, _final_price</strong> cannot be blank.");
                                return $this->refresh();
                            }
                            if (!is_numeric($regular_price) || !is_numeric($final_price)) {
                                Yii::$app->session->setFlash('error', "Error occured at row number {$i} while importing products. <br>Invalid <strong>_regular_price and _final_price</strong>.");
                                return $this->refresh();
                            }
                            $attributeValueArr = [];
                            $attributeSetModel = AttributeSets::find()
                                ->where(['TRIM(attribute_set_code)' => trim($sheetData[$i]['Q'])])
                                ->one();
                            if (!empty($attributeSetModel)) {
                                $attributeInputs = [];
                                if (!empty($sheetData[$i]['R']) || $sheetData[$i]['R'] == 0) {
                                    $attributeInputs[] = trim($sheetData[$i]['R']);
                                }
                                if (!empty($sheetData[$i]['S']) || $sheetData[$i]['S'] == 0) {
                                    $attributeInputs[] = trim($sheetData[$i]['S']);
                                }

                                $attributeSetGroups = AttributeSetGroups::find()
                                    ->join('inner join', 'attributes', 'attributes.attribute_id = attribute_set_groups.attribute_id')
                                    ->where(['attribute_set_groups.attribute_set_id' => $attributeSetModel->attribute_set_id])
                                    ->orderBy(['attributes.sort_order' => SORT_ASC])
                                    ->all();
                                if (!empty($attributeInputs) && !empty($attributeSetGroups)) {
                                    foreach ($attributeSetGroups as $index => $attributeSetGroup) {
                                        $attributeValue = AttributeValues::find()
                                            ->select(['attribute_value_id'])
                                            ->where(['value_en' => $attributeInputs[$index], 'attribute_values.attribute_id' => $attributeSetGroup->attribute_id])
                                            ->asArray()
                                            ->one();
                                        if (!empty($attributeValue)) {
                                            $attributeValueArr[] = $attributeValue['attribute_value_id'];
                                        }
                                    }
                                }
                            }
                            $allCategories = explode('#', trim($sheetData[$i]['G']));
                            $categoryArr = [];
                            if (!empty($allCategories)) {
                                foreach ($allCategories as $category) {
                                    $categories = explode(',', trim($category));
                                    $root = 0;
                                    $left = $right = 0;
                                    if (!empty($categories)) {
                                        foreach ($categories as $k => $c) {
                                            $nextIndex = $k + 1;
                                            $rootCategoryModel = Category::find()
                                                ->where(['TRIM(LOWER(name_en))' => trim(strtolower($c)), 'lvl' => $k]);
                                            if (!empty($root)) {
                                                $rootCategoryModel->andWhere(['root' => $root]);
                                                if ($left != 0 && $right != 0) {
                                                    $rootCategoryModel->andWhere(['>', 'lft', $left])
                                                        ->andWhere(['<', 'rgt', $right]);
                                                }
                                            }
                                            $rootCategoryModel = $rootCategoryModel->one();
                                            if (!empty($rootCategoryModel)) {
                                                $left = $rootCategoryModel->lft;
                                                $right = $rootCategoryModel->rgt;
                                                if ($k == 0) {
                                                    $root = $rootCategoryModel->category_id;
                                                }
                                                $categoryArr[] = $rootCategoryModel->category_id;
                                                $subCategoryModel = $rootCategoryModel->children(1);
                                                if (!empty($categories[$nextIndex])) {
                                                    $subCategoryModel->andWhere(['TRIM(LOWER(name_en))' => trim(strtolower($categories[$nextIndex])), 'root' => $root]);
                                                }
                                                $subCategoryModel = $subCategoryModel->asArray()->one();
                                                if (!empty($subCategoryModel)) {
                                                    $categoryArr[] = $subCategoryModel['category_id'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($categoryArr)) {
                                $categoryArr = array_unique($categoryArr);
                            }
                            $brandModel = Brands::find()
                                ->where(['TRIM(LOWER(name_en))' => $brand, 'is_deleted' => 0])
                                ->one();
                            $pharmaModel = Pharmacies::find()
                                ->where(['TRIM(LOWER(name_en))' => $pharmacy, 'is_deleted' => 0])
                                ->one();
                            $manufactureModel = Manufacturers::find()
                                ->where(['TRIM(LOWER(name_en))' => $manufacturer, 'is_deleted' => 0])
                                ->one();
                            if (!empty($sku) && !empty($barcode)) {
                                $oldProductModel = Product::find()
                                    ->where(['is_deleted' => 0])
                                    ->andWhere(['OR', ['SKU' => $sku], ['barcode' => $barcode]])
                                    ->one();
                                if (!empty($oldProductModel)) {
                                    $transaction->rollBack();
                                    Yii::$app->session->setFlash('error', "Error occured at row number {$i} while importing products.<br> <strong>SKU or Barcode</strong> already exist in the system.");
                                    return $this->refresh();
                                }
                            }
                            $productModel = new Product();
                            $productModel->SKU = $sku;
                            $productModel->barcode = $barcode;
                            $productModel->supplier_barcode = $supplier_barcode;
                            $productModel->posted_date = date('Y-m-d H:i:s');
                            $productModel->remaining_quantity = 0;
                            if (\Yii::$app->session['_eyadatAuth'] == 1) {
                                $productModel->admin_id = Yii::$app->user->identity->admin_id;
                            }
                            $productModel->attribute_set_id = (!empty($attributeSetModel)) ? $attributeSetModel->attribute_set_id : null;
                            $productModel->name_en = trim($sheetData[$i]['C']);
                            $productModel->name_ar = (!empty($sheetData[$i]['D'])) ? $sheetData[$i]['D'] : $sheetData[$i]['C'];
                            if (!empty($parent_sku) && $parent_sku == $sku) {
                                $type = 'G';
                                $productModel->type = $type;
                                $productModel->show_as_individual = 1;
                            } else {
                                $type = 'S';
                                $productModel->type = $type;
                                $productModel->show_as_individual = (empty($parent_sku)) ? 1 : 0;
                            }
                            if (!empty($brandModel)) {
                                $productModel->brand_id = $brandModel->brand_id;
                            }
                            if (!empty($pharmaModel)) {
                                $productModel->pharmacy_id = $pharmaModel->pharmacy_id;
                            }
                            if (!empty($manufactureModel)) {
                                $productModel->manufacturer_id = $manufactureModel->manufacturer_id;
                            }
                            $productModel->short_description_en = $sheetData[$i]['K'];
                            $productModel->short_description_ar = (!empty($sheetData[$i]['L'])) ? $sheetData[$i]['L'] : $sheetData[$i]['L'];
                            $productModel->description_en = $sheetData[$i]['M'];
                            $productModel->description_ar = (!empty($sheetData[$i]['N'])) ? $sheetData[$i]['N'] : $sheetData[$i]['N'];

                            $productModel->specification_en = $sheetData[$i]['O'];
                            $productModel->specification_ar = $sheetData[$i]['P'];
                            $productModel->base_currency_id = 82;
                            $productModel->cost_price = (float) 0;
                            $productModel->regular_price = (float) $regular_price;
                            $productModel->final_price = (float) $final_price;
                            $productModel->product_margin = 0; //trim($sheetData[$i]['X']);
                            $productModel->is_active = 0;
                            if ($productModel->save(false)) {
                                $successProductArr[] = $productModel->product_id;
                                if (!empty($attributeValueArr)) {
                                    foreach ($attributeValueArr as $row) {
                                        $attValue = new \app\models\ProductAttributeValues();
                                        $attValue->attribute_value_id = $row;
                                        $attValue->product_id = $productModel->product_id;
                                        $attValue->save(false);
                                    }
                                }
                                if (!empty($parent_sku) && $parent_sku != $sku) {
                                    $parentProduct = Product::find()
                                        ->where(['SKU' => $parent_sku, 'is_deleted' => 0])
                                        ->one();
                                    if (!empty($parentProduct)) {
                                        $associatedProduct = new \app\models\AssociatedProducts();
                                        $associatedProduct->child_id = $productModel->product_id;
                                        $associatedProduct->parent_id = $parentProduct->product_id;
                                        $associatedProduct->save(false);
                                        $parentProduct->type = 'G';
                                        $parentProduct->save(false);
                                    }
                                }
                                if (!empty($categoryArr)) {
                                    foreach ($categoryArr as $row) {
                                        $productCategory = new \app\models\ProductCategories();
                                        $productCategory->category_id = $row;
                                        $productCategory->product_id = $productModel->product_id;
                                        $productCategory->save(false);
                                    }
                                }
                                $successCount++;
                            } else {
                                debugPrint($productModel->errors);
                                exit;
                            }
                        }
                    }
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
                if ($errorCount == 0) {
                    Yii::$app->session->setFlash('success', "{$successCount} Products imported successfully.");
                } elseif ($successCount == 0) {
                    Yii::$app->session->setFlash('error', "Products import failed.");
                } else {
                    Yii::$app->session->setFlash('error', "Error occured while adding products for the following rows.<br/> <p>" . implode(', ', $errorArr) . ". <br/>Please fix the errors and try again</p>");
                }
                unlink($path); // remove file from uploads directory
                return $this->refresh();
            }
        }
        return $this->render('excel-import', [
            'model' => $model,
        ]);
    }

    public function actionGetMedicineList($pharmacy_id = null)
    {
        $requestData = Yii::$app->request->queryParams;
        $columns = array(
            0 => 'product_id',
            1 => 'final_price'
        );

        $query = Product::find()
            ->where(['product.is_deleted' => 0, 'product.pharmacy_id' => $pharmacy_id]);

        //echo $query->createCommand()->rawSql;exit;

        $data = $query->all();

        $totalData = count($data);
        if (!empty($requestData['search']['value'])) {
            $query->andWhere([
                'AND',
                [
                    'OR',
                    ['LIKE', 'product.name_en', $requestData['search']['value']],
                    //['LIKE', 'SKU', $requestData['search']['value']],
                    //['LIKE', 'final_price', $requestData['search']['value']]
                ]
            ]);
        }
        $data = $query->all();
        $totalFiltered = count($data);
        $query->limit($requestData['length']);
        $query->offset($requestData['start']);
        $result = $query->all();
        $data1 = array();
        $data2 = array();
        $i = 1;
        foreach ($result as $key => $row) {
            $isAssociated = 0;
            if (isset($exclude) && $exclude != "") {
                if ($type == 'A') {
                    $checkAssociated = \app\models\AssociatedProducts::find()
                        ->where(['parent_id' => $exclude, 'child_id' => $row["product_id"]])
                        ->one();
                } else {
                    $checkAssociated = \app\models\RelatedProducts::find()
                        ->where(['product_id' => $exclude, 'related_id' => $row["product_id"]])
                        ->one();
                }
                if (!empty($checkAssociated)) {
                    $isAssociated = 1;
                }
            }
            $nestedData = array();
            $nestedData[] = $row["product_id"];
            $nestedData[] = $row["name_en"];
            $nestedData[] = $row["final_price"];
            $nestedData[] = '<input type="text" name="qty_' . $row['product_id'] . '" class="form-control">';
            $nestedData[] = '<input type="text" name="instruction_' . $row['product_id'] . '" class="form-control">';
            if ($isAssociated == 1) {
                $data1[] = $nestedData;
            } else {
                $data2[] = $nestedData;
            }
            $i++;
        }
        ob_start();
        $finalData = array_merge($data1, $data2);
        $json_data = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $finalData   // total data array
        );
        echo json_encode($json_data);
    }
}
