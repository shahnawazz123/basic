<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use himiklab\sortablegrid\SortableGridView;
use yii\widgets\ActiveForm;
use yii\helpers\BaseUrl;
use app\helpers\AppHelper;
use app\helpers\PermissionHelper;

$allowUpdate = $allowView = $allowDelete = $allowStock = true;
/* $allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'admin');
  $allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'admin');
  $allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'admin');
  $allowStock = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'add-product-stock', Yii::$app->user->identity->admin_id, 'admin');
  //
  if (\Yii::$app->session['_eyadatAuth'] == 2) {
  //$btnStr = '{stock} {view}';
  $allowDelete = false;
  $allowUpdate = false;
  $allowStock = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'add-product-stock', Yii::$app->user->identity->shop_id, 'S');
  $allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->shop_id, 'S');
  }
  if (\Yii::$app->session['_eyadatAuth'] == 4) {
  //$btnStr = '{stock} {view}';
  $allowDelete = false;
  $allowUpdate = false;
  $allowStock = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'add-product-stock', Yii::$app->user->identity->shop_admin_id, 'S');
  $allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->shop_admin_id, 'S');
  } */

$permissionStr = '';
if ($allowStock) {
    // $permissionStr .= '{stock}';
}
if ($allowView) {
    $permissionStr .= '{view}';
}
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
if ($allowDelete) {
    $permissionStr .= '{delete}';
}
\app\assets\SelectAsset::register($this);
$this->registerJsFile(BaseUrl::home() . 'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model app\models\Product */

$this->title = $model->name_en;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$associatedDataProvider = \app\helpers\AppHelper::getAssociatedProductsDataProvider($model->product_id);

$productAttributeValues = $model->productAttributeValues;

//debugPrint($productAttributeValues); exit;
$attributeCanvasStr = '';
if (!empty($productAttributeValues)) {
    foreach ($productAttributeValues as $row) {
        $attributeCanvasStr .= $row->attributeValue->attribute0->name_en . ' : ' . $row->attributeValue->value_en . '  ';
    }
}
$attributeCanvasStr = trim($attributeCanvasStr);
?>
<style>
    .panel-primary {
        border-color: #d4d4d4;
        border: none;
    }

    .panel-primary>.panel-heading {
        color: #333;
        background-color: #d4d4d4;
        border-color: #d4d4d4;
    }

    .panel-primary .table {
        margin-bottom: 0px;
    }

    .ui-sortable-handle {
        cursor: move;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                ?>
                    <p class="pull pull-right">
                        <?= ($allowUpdate) ? Html::a('Update', ['update', 'id' => $model->product_id], ['class' => 'btn btn-primary']) : "" ?>
                        <?=
                        ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->product_id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]) : ""
                        ?>
                    </p>
                    <br clear='all' />
                <?php
                }
                ?>
                <?php
                $barcodeImage = Yii::getAlias('@webroot') . '/uploads/barcode-' . $model->barcode . '.jpg';
                if (!empty($model->barcode) && file_exists($barcodeImage)) {

                    $rawImageBytes = \app\helpers\AppHelper::generateBarCodeTemplate($model);
                    if (!empty($rawImageBytes)) {
                        $file_name = cleanBarcodeName($model->name_en) . '-' . $model->barcode . '.jpg';
                        $path = Yii::getAlias('@webroot') . '/uploads/' . $file_name;
                        //echo $this->render('barcode-template', ['model'=>$model]);
                ?>
                        <img style="border: 1px solid #000;" src="data:image/jpeg;base64,<?php echo base64_encode($rawImageBytes); ?>" /> <br>
                        <a href="<?php echo \yii\helpers\Url::to(['barcode-templates/' . $file_name], true) ?>" download>Download image</a>
                        <br> <br>
                <?php
                    }
                }
                ?>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Part Info: <?php echo $model->name_en ?></h3>
                    </div>
                    <div class="">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute' => 'attribute_set_id',
                                    'value' => (!empty($model->attributeSet) ? $model->attributeSet->name_en : ""),
                                ],
                                //'admin_id',
                                'name_en',
                                'name_ar',
                                [
                                    'attribute' => 'short_description_en',
                                    'format' => 'raw',
                                    'visible' => ($model->short_description_en != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'short_description_ar',
                                    'format' => 'raw',
                                    'visible' => ($model->short_description_ar != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'description_en',
                                    'format' => 'raw',
                                    'visible' => ($model->description_en != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'description_ar',
                                    'format' => 'raw',
                                    'visible' => ($model->description_ar != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'specification_en',
                                    'format' => 'raw',
                                    'visible' => ($model->specification_en != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'specification_ar',
                                    'format' => 'raw',
                                    'visible' => ($model->specification_ar != null) ? true : false,
                                ],
                                'SKU',
                                'barcode',
                                'supplier_barcode',
                                'regular_price',
                                'final_price',
                                //'cost_price',
                                [
                                    'attribute' => 'base_currency_id',
                                    'value' => $model->baseCurrency->code_en,
                                ],
                                'remaining_quantity',
                                [
                                    'attribute' => 'posted_date',
                                    'format' => 'dateTime',

                                ],

                                [
                                    'attribute' => 'updated_date',
                                    'visible' => ($model->updated_date != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'is_featured',
                                    'value' => ($model->is_featured == 0) ? 'No' : 'Yes',
                                ],
                                [
                                    'attribute' => 'is_active',
                                    'value' => ($model->is_active == 1) ? 'Yes' : 'No',
                                ],
                                [
                                    'attribute' => 'views',
                                    'visible' => ($model->views != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'meta_title_en',
                                    'format' => 'raw',
                                    'visible' => ($model->meta_title_en != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'meta_title_ar',
                                    'format' => 'raw',
                                    'visible' => ($model->meta_title_ar != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'meta_keywords_en',
                                    'format' => 'raw',
                                    'visible' => ($model->meta_keywords_en != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'meta_keywords_ar',
                                    'format' => 'raw',
                                    'visible' => ($model->meta_keywords_ar != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'meta_description_en',
                                    'format' => 'raw',
                                    'visible' => ($model->meta_description_en != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'meta_description_ar',
                                    'format' => 'raw',
                                    'visible' => ($model->meta_description_ar != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'brand_id',
                                    'value' => (!empty($model->brand) ? $model->brand->name_en : ""),
                                    'visible' => !empty($model->brand) ? true : false,
                                ],
                                [
                                    'attribute' => 'manufacturer_id',
                                    'value' => (!empty($model->manufacturer) ? $model->manufacturer->name_en : ""),
                                    'visible' => !empty($model->manufacturer) ? true : false,
                                ],
                                [
                                    'attribute' => 'is_new',
                                    'value' => ($model->is_new == 1) ? 'Yes' : 'No',
                                ],
                                [
                                    'attribute' => 'start_date',
                                    'visible' => ($model->start_date != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'end_date',
                                    'visible' => ($model->end_date != null) ? true : false,
                                ],
                                [
                                    'attribute' => 'show_as_individual',
                                    'value' => ($model->show_as_individual == 0) ? 'No' : 'Yes',
                                ],
                                [
                                    'attribute' => 'is_best_seller',
                                    'value' => ($model->is_best_seller == 0) ? 'No' : 'Yes',
                                ],
                                [
                                    'attribute' => 'pharmacy_id',
                                    'value' => (!empty($model->pharmacy)) ? $model->pharmacy->name_en : '',
                                ],
                                'deeplink_url',
                            ],
                        ])
                        ?>
                    </div>
                </div>
                <?php
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                ?>
                    <br clear="all" />
                    <div class="text-right">
                        <a tabindex="0" class="btn btn-md btn-warning" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" title="" data-content="Drag & Drop to change the order"><i class="fa fa-info-circle"></i></a>
                    </div>
                <?php
                }
                ?>
                <span class="clearfix">&nbsp;</span>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Product Images</h3>
                    </div>
                    <div class="">
                        <?php
                        $dataProvider = new ActiveDataProvider([
                            'query' => $model->getAllImages($model->product_id),
                            'pagination' => [
                                'pageSize' => 20,
                            ],
                            'sort' => ['defaultOrder' => ['sort_order' => SORT_ASC]],
                        ]);

                        if (\Yii::$app->session['_eyadatAuth'] == 1) {
                            echo SortableGridView::widget([
                                'dataProvider' => $dataProvider,
                                //'filterModel' => $searchModel,
                                'sortableAction' => ['product-image/sort'],
                                'summary' => '',
                                'columns' => [
                                    //['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'attribute' => 'image',
                                        'value' => function ($image) {
                                            return AppHelper::getUploadUrl() . $image->image;
                                        },
                                        'format' => ['image', ['width' => '96']],
                                        'filter' => false,
                                    ],
                                ],
                            ]);
                        } else {
                            echo GridView::widget([
                                'dataProvider' => $dataProvider,
                                //'filterModel' => $searchModel,
                                'summary' => '',
                                'columns' => [
                                    //['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'attribute' => 'image',
                                        'value' => function ($image) {
                                            return AppHelper::getUploadUrl() . $image->image;
                                        },
                                        'format' => ['image', ['width' => '96']],
                                        'filter' => false,
                                    ],
                                ],
                            ]);
                        }
                        ?>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Product Category</h3>
                            </div>
                            <div class="">
                                <?php
                                $dataProvider1 = new ActiveDataProvider([
                                    'query' => $model->getProductCategories(),
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                ]);
                                echo GridView::widget([
                                    'dataProvider' => $dataProvider1,
                                    //'filterModel' => $searchModel,
                                    'summary' => '',
                                    'columns' => [
                                        //['class' => 'yii\grid\SerialColumn'],
                                        'category.name_en',
                                    ],
                                    'pjax' => true,
                                    'pjaxSettings' => [
                                        'options' => [
                                            'id' => 'category-list-pjax'
                                        ]
                                    ]
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Related Product</h3>
                            </div>
                            <div class="">
                                <?php
                                $dataProvider2 = new ActiveDataProvider([
                                    'query' => $model->getRelatedProducts(),
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                ]);

                                echo GridView::widget([
                                    'dataProvider' => $dataProvider2,
                                    //'filterModel' => $searchModel,
                                    'summary' => '',
                                    'columns' => [
                                        //['class' => 'yii\grid\SerialColumn'],
                                        'related.name_en',
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <br clear="all" />
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Product Attribute</h3>
                            </div>
                            <div class="">
                                <?php
                                $dataProvider3 = new ActiveDataProvider([
                                    'query' => $model->getProductAttributeValues(),
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                ]);

                                echo GridView::widget([
                                    'dataProvider' => $dataProvider3,
                                    //'filterModel' => $searchModel,
                                    'summary' => '',
                                    'columns' => [
                                        //['class' => 'yii\grid\SerialColumn'],
                                        'attributeValue.value_en',
                                        'attributeValue.attribute0.code',
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($model->type == "G") {
                    ?>
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Associated Product</h3>
                                </div>
                                <div class="">
                                    <?php
                                    $dataProvider4 = new ActiveDataProvider([
                                        'query' => $associatedDataProvider,
                                        'pagination' => [
                                            'pageSize' => 20,
                                        ],
                                    ]);

                                    echo GridView::widget([
                                        'dataProvider' => $dataProvider4,
                                        //'filterModel' => $searchModel,
                                        'summary' => '',
                                        'columns' => [
                                            //['class' => 'yii\grid\SerialColumn'],

                                            [
                                                'label' => 'Image',
                                                'value' => function ($model) {
                                                    $image = $model->getProductImage($model->product_id);
                                                    return $image;
                                                },
                                                'format' => ['image', ['height' => '100']],
                                                'filter' => false,
                                            ],
                                            [
                                                'value' => function ($model) {
                                                    return $model->SKU;
                                                },
                                                'attribute' => 'SKU',
                                                'format' => 'raw',
                                            ],
                                            [
                                                'attribute' => 'name',
                                                'label' => 'Name',
                                                'format' => 'raw',
                                                'value' => function ($model) {
                                                    $attrs = \app\models\ProductAttributeValues::find()->where(['product_id' => $model->product_id])->all();
                                                    $html = '<p>' . $model->name_en . '</p>';
                                                    if (!empty($attrs)) {
                                                        $html .= '<ul>';
                                                        foreach ($attrs as $row) {
                                                            $html .= '<li>' . $row->attributeValue->value_en . '</li>';
                                                        }
                                                        $html .= '</ul>';
                                                    }

                                                    return $html;
                                                },
                                            ],
                                            [
                                                'label' => 'Regular Price',
                                                'value' => function ($model) {
                                                    return $model->regular_price . ' ' . $model->baseCurrency->code_en;
                                                },
                                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                                'attribute' => 'regular_price',
                                                'format' => 'raw',
                                            ],
                                            [
                                                'label' => 'Final Price',
                                                'value' => function ($model) {
                                                    return $model->final_price . ' ' . $model->baseCurrency->code_en;
                                                },
                                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                                'attribute' => 'final_price',
                                                'format' => 'raw',
                                            ],
                                            [
                                                'label' => 'Price',
                                                'value' => function ($model) {
                                                    return $model->final_price . ' ' . $model->baseCurrency->code_en;
                                                },
                                                'attribute' => 'final_price',
                                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 2),
                                            ],
                                            [
                                                'attribute' => 'remaining_quantity',
                                                'label' => 'Stock',
                                                'value' => function ($model) {
                                                    return $model->remaining_quantity;
                                                },
                                            ],
                                            [
                                                'label' => 'Publish Status',
                                                'attribute' => 'is_active',
                                                'format' => 'raw',
                                                'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                                                'value' => function ($model) {
                                                    return '<div class="onoffswitch">'
                                                        . Html::checkbox('onoffswitch', $model->is_active, [
                                                            'class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->product_id,
                                                            'onclick' => 'product.changeProductStatus("product/publish",this,' . $model->product_id . ')'
                                                        ])
                                                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->product_id . '"></label></div>';
                                                }
                                            ],
                                            [
                                                'class' => 'yii\grid\ActionColumn',
                                                'template' => $permissionStr,
                                                'buttons' => [
                                                    'stock' => function ($url, $model) {
                                                        return Html::a('<i title="Add stock" class="glyphicon glyphicon-download"></i> ', "javascript:;", [
                                                            'title' => Yii::t('yii', 'Add stock'),
                                                            'data-container' => 'body',
                                                            'data-toggle' => 'popover',
                                                            'data-placement' => 'left',
                                                            'data-html' => "true",
                                                            'data-content' => '<input placeholder="Enter stock" id="stkPop_' . $model->product_id . '" type="text" name="stock_' . $model->product_id . '" value="" class="form-control"/><div id="error_' . $model->product_id . '">&nbsp;</div><button onclick="product.addMoreStock(' . $model->product_id . ')" type="button" class="btn btn-sm btn-info"><i class="fa fa-check"></i>Save</button>'
                                                        ]);
                                                    },
                                                ]
                                            ]
                                        ],
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <br clear="all" />
                    <?php
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>

<?php
/*if(!empty($model->barcode)) {
    $barcodeTitle = (count($model->name_en)>30) ? substr($model->name_en,0,27).'...' : $model->name_en;
    $this->registerJs("
        var canvas = document.getElementById('barcode-canvas'),
        ctx = canvas.getContext('2d');
       
        
        function doCanvas() {
            var heightIndex = 0;
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            heightIndex = 10;
            ctx.fillStyle = '#333';
            ctx.font = 'bold  16px sans-serif';
            ctx.textAlign=\"center\"; 
            ctx.textBaseline = \"middle\";
            ctx.fillText(\"" . $barcodeTitle . "\", 150, heightIndex);
            
            heightIndex += 20;
            ctx.fillStyle = '#333';
            ctx.font = '12px sans-serif';
            ctx.fillText('KD " . number_format($model->final_price, 3) . "', 150, heightIndex);
            
            heightIndex += 20;
            ctx.fillStyle = '#333';
            ctx.font = '12px sans-serif';
            ctx.fillText('" . $attributeCanvasStr . "', 150, heightIndex);
            
            heightIndex += 15;
            var x = 10;
            var y = heightIndex;
            
            var imageObj = new Image();
            imageObj.src = '" . \yii\helpers\Url::to(['uploads/barcode-' . $model->barcode . '.png'], true) . "';
           
            imageObj.onload = function() {
                var originalWidth = this.width;
                var originalHeight = this.height;
                var width = canvas.width-20;
                //var height = (originalHeight/originalWidth)*width;
                var height = 41.58;
                ctx.drawImage(imageObj, x, y, width, height);
            };
           

            ctx.width = window.innerWidth;
            ctx.fillStyle = '#555';
            ctx.font = 'bold 12px sans-serif';
            ctx.fillText('" . $model->barcode . "', 150, canvas.height -10);
        }
        
        function downloadCanvas(link, canvasId, filename) {
            link.href = document.getElementById(canvasId).toDataURL();
            link.download = filename;
        }
        

        document.getElementById('download-barcode').addEventListener('click', function() {
            downloadCanvas(this, 'barcode-canvas', \"".str_replace(' ','-',$model->name_en).'-'.$model->barcode.".png\");
        }, false);
        
        doCanvas();
    ", \yii\web\View::POS_END);
}*/
