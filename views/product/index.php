<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\components\EditableColumn;
use app\helpers\PermissionHelper;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile(BaseUrl::home() . 'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(BaseUrl::home() . 'css/jquery.fancybox.min.css');

$method = $this->context->action->id;
if ($method == 'stock-products')
    $this->title = 'Out of Stock Products';
elseif (!empty($_GET['ProductSearch']['type']) && $_GET['ProductSearch']['type'] == 'G')
    $this->title = 'Grouped Products';
elseif (!empty($_GET['ProductSearch']['type']) && $_GET['ProductSearch']['type'] == 'S')
    $this->title = 'Simple Products';
else
    $this->title = 'Products';

$this->params['breadcrumbs'][] = $this->title;

$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
//$btnStr = '{push} {stock} {view} {update} {delete} {reviews}';
$btnStr = '';
$allowCreate = $allowPush = $allowStock = $allowView = $allowUpdate = $allowDelete = true;
/*
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowPush = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'send-push', Yii::$app->user->identity->admin_id, 'A');
$allowStock = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'add-product-stock', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');

if (\Yii::$app->session['_eyadatAuth'] == 2) {
    //$btnStr = '{stock} {view}';
    $allowStock = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'add-product-stock', Yii::$app->user->identity->admin_id, 'S');
    $allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'S');
}
if (\Yii::$app->session['_eyadatAuth'] == 4) {
    //$btnStr = '{stock} {view}';
    $allowStock = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'add-product-stock', Yii::$app->user->identity->shop_admin_id, 'S');
    $allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->shop_admin_id, 'S');
}*/

if ($allowPush) {
    $btnStr .= '{push} ';
}
if ($allowStock) {
    $btnStr .= '{stock} ';
}
if ($allowView) {
    $btnStr .= '{view} ';
}
if ($allowUpdate) {
    $btnStr .= '{update} ';
}
if ($allowDelete) {
    $btnStr .= '{delete} ';
}
?>
<style>
    .grid-view {
        overflow: auto;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php // echo $this->render('_search', ['model' => $searchModel]);     
                ?>

                <p class="pull pull-right">
                    <?php
                    if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    ?>
                        <?= ($allowCreate) ? Html::a('Create product', ['create'], ['class' => 'btn btn-success']) : "" ?>
                    <?php
                    }
                    ?>
                    <?php
                    if (\Yii::$app->session['_eyadatAuth'] == 1) {
                        echo Html::a('Export to excel', ['export' . $urlQuery], ['class' => 'btn btn-info']);
                    }
                    ?>
                </p>
                <?php
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                ?>
                    <p class="pull pull-left">
                        <?php
                        $url = yii\helpers\BaseUrl::home() . 'product/bulk-delete';
                        if ($allowDelete) {
                        ?>
                            <a onclick="product.bulkDelete('<?php echo $url ?>', 'product(s)')" href="javascript:;" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</a>
                        <?php
                        }
                        ?>
                    </p>
                    <br clear="all" />
                <?php
                }
                ?>
                <div class="pull pull-left">
                    <?php
                    $queryStr = '';
                    if (isset($_GET['ProductSearch'])) {
                        unset($_GET['ProductSearch']['page_size']);
                        foreach ($_GET['ProductSearch'] as $k => $ps) {
                            $queryStr .= 'ProductSearch[' . $k . ']=' . $ps . '&';
                        }
                    }
                    echo Html::activeTextInput($searchModel, 'page_size', [
                        'class' => 'form-control',
                        'placeholder' => 'Page Size',
                        'onchange' => "window.location.href=baseUrl+'product/index?" . $queryStr . "ProductSearch[page_size]='+this.value"
                    ]);
                    ?>
                </div>
                <?php
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                ?>
                    <div class="text-right">
                        <a tabindex="0" class="btn btn-md btn-warning" role="button" data-toggle="popover" data-placement="left" data-trigger="focus" title="" data-content="Drag & Drop to change the order"><i class="fa fa-info-circle"></i></a>
                    </div>
                <?php
                }
                ?>
                <br clear="all" />
                <?php
                if (\Yii::$app->session['_eyadatAuth'] == 1) {
                    $gridViewClassName = \himiklab\sortablegrid\SortableGridView::className();
                } else {
                    $gridViewClassName = GridView::className();
                }

                echo $gridViewClassName::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'headerOptions' => [
                                'class' => 'i-checks'
                            ],
                            'cssClass' => 'i-checks'
                        ],
                        [
                            'label' => 'Image',
                            'value' => function ($model) {
                                $image = $model->getProductImage($model->product_id);
                                if (!empty($image)) {
                                    return '<a class="fancybox"  href="' . $image . '">
                                                <img  src="' . $image . '" style="max-height: 100px;" />
                                            </a>';
                                } else {
                                    $image = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                                    return '<a class="fancybox"  href="' . $image . '">
                                                <img  src="' . $image . '" style="max-height: 100px;" />
                                            </a>';
                                }
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],
                        [
                            'value' => function ($model) {
                                return $model->SKU;
                            },
                            'attribute' => 'SKU',
                        ],
                        [
                            'value' => function ($model) {
                                return (!empty($model->barcode)) ? $model->barcode : '';
                            },
                            'attribute' => 'barcode',
                        ],
                        [
                            'value' => function ($model) {
                                return (!empty($model->supplier_barcode)) ? $model->supplier_barcode : '';
                            },
                            'attribute' => 'supplier_barcode',
                        ],
                        [
                            'attribute' => 'name',
                            'label' => 'Name',
                            'value' => function ($model) {
                                return $model->name_en;
                            },
                        ],
                        [
                            'label' => 'Final Price',
                            'value' => function ($model) {
                                return $model->final_price . ' ' . $model->baseCurrency->code_en;
                            },
                            'attribute' => 'final_price',
                            'format' => 'raw',
                            'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                        ],
                        [
                            'label' => 'Sales Price',
                            'value' => function ($model) {
                                return $model->final_price . ' ' . $model->baseCurrency->code_en;
                            },
                            'attribute' => 'final_price',
                            'visible' => (\Yii::$app->session['_eyadatAuth'] == 2),
                        ],
                        /*[
                            'label' => 'Cost Price',
                            'value' => function ($model) {
                                return $model->cost_price . ' ' . $model->baseCurrency->code_en;
                            },
                            'attribute' => 'cost_price',
                            'visible' => (\Yii::$app->session['_eyadatAuth'] == 2),
                        ],
                        [
                            'label' => 'Product Margin',
                            'value' => function ($model) {
                                if ($model->product_margin == 0) {
                                    if (!empty($model->brand)) {
                                        if ($model->brand->commission_percentage == 0) {
                                            if (isset($model->shop->commission_percentage)) {
                                                if (!empty($model->shop->commission_percentage)) {
                                                    return 0;
                                                } else {
                                                    return ($model->final_price * $model->shop->commission_percentage) / 100;
                                                }
                                            } else {
                                                return 0;
                                            }
                                        } else {
                                            return ($model->final_price * $model->brand->commission_percentage) / 100;
                                        }
                                    } else {
                                        return "";
                                    }
                                } else {
                                    return $model->product_margin;
                                }
                            }
                        ],*/
                        [
                            'label' => 'Category',
                            'value' => function ($model) {
                                $categoryArr = [];
                                foreach ($model->productCategories as $productCategory) {
                                    if (!empty($productCategory->category)) {
                                        $categoryPath = [];
                                        $parent = $productCategory->category->parents()->asArray()->all();
                                        if (!empty($parent)) {
                                            foreach ($parent as $p) {
                                                $categoryPath[] = $p['name_en'];
                                            }
                                        }
                                        $categoryPath[] = $productCategory->category->name_en;
                                        if (!empty($categoryPath)) {
                                            $categoryArr[] = implode(" -> ", $categoryPath);
                                        }
                                    }
                                }
                                return Html::ul($categoryArr, ['class' => 'category-ul']);
                            },
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'category_id', app\helpers\BannerHelper::getRecursiveCategory(), ['class' => 'form-control select2', 'prompt' => 'Filter Category', 'style' => 'width: 200px;']),
                        ],
                        [
                            'label' => 'Attribute List',
                            'value' => function ($model) {
                                $attributeList = app\helpers\ProductHelper::getAttributesByProduct($model);
                                $html = '<ul>';
                                if (!empty($attributeList)) {
                                    foreach ($attributeList as $attlst) {
                                        $html .= '<li style="width:100px;">' . $attlst . '</li>';
                                    }
                                }
                                $html .= '</ul>';
                                return $html;
                            },
                            'format' => 'raw',
                            'filter' => Html::activeTextInput($searchModel, 'attribute_value_search', ['class' => 'form-control']),
                        ],
                        [
                            'attribute' => 'remaining_quantity',
                            'label' => 'Quantity',
                            'value' => function ($model) {
                                return $model->remaining_quantity;
                            },
                        ],
                        [
                            'label' => 'Quantity Sold',
                            'value' => function ($model) {
                                $stocks = app\models\OrderItems::find()
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
                                    return $stocks['total'];
                                } else {
                                    return 0;
                                }
                            },
                        ],
                        [
                            'attribute' => 'brand_id',
                            'value' => function ($model) {
                                return (!empty($model->brand) ? $model->brand->name_en : "");
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'brand_id', app\helpers\ProductHelper::getBrandList(), ['class' => 'form-control select2', 'prompt' => 'Filter']),
                            'attribute' => 'brand_id',
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'pharmacy_id',
                            'value' => function ($model) {
                                return (!empty($model->pharmacy) ? $model->pharmacy->name_en : "");
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'pharmacy_id', app\helpers\ProductHelper::getPharmacyList(), ['class' => 'form-control select2', 'prompt' => 'Filter']),
                            'format' => 'raw',
                            'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                        ],
                        [
                            'attribute' => 'manufacturer_id',
                            'value' => function ($model) {
                                return (!empty($model->manufacturer) ? $model->manufacturer->name_en : "");
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'manufacturer_id', app\helpers\ProductHelper::getManufacturerList(), ['class' => 'form-control select2', 'prompt' => 'Filter']),
                            'format' => 'raw',
                            'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
                        ],
                        [
                            'label' => 'Featured',
                            'attribute' => 'is_featured',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<div class="onoffswitch">'
                                    . Html::checkbox('onoffswitch', $model->is_featured, [
                                        'class' => "onoffswitch-checkbox", 'id' => "featuredButton" . $model->product_id,
                                        'onclick' => 'common.changeStatus("product/featured",this,' . $model->product_id . ')'
                                    ])
                                    . '<label class="onoffswitch-label" for="featuredButton' . $model->product_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_featured', [1 => 'Featured', 0 => 'Non-Featured'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                        ],
                        [
                            'label' => 'Trending',
                            'attribute' => 'is_trending',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<div class="onoffswitch">'
                                    . Html::checkbox('onoffswitch', $model->is_trending, [
                                        'class' => "onoffswitch-checkbox", 'id' => "trendingButton" . $model->product_id,
                                        'onclick' => 'common.changeStatus("product/trend",this,' . $model->product_id . ')'
                                    ])
                                    . '<label class="onoffswitch-label" for="trendingButton' . $model->product_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_trending', [1 => 'Trending', 0 => 'Non-Trending'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                        ],

                        [
                            'attribute' => 'type',
                            'value' => function ($model) {
                                return ($model->type == 'S') ? "Simple" : "Grouped";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'type', ['S' => 'Simple', 'G' => 'Grouped', 'A' => 'All Products'], ['class' => 'form-control select2', 'prompt' => 'Filter']),
                        ],
                        [
                            'attribute' => 'attribute_set_id',
                            'value' => function ($model) {
                                return (!empty($model->attributeSet)) ? $model->attributeSet->name_en : '';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'attribute_set_id', \app\helpers\ProductHelper::getAttributeSetList(), ['class' => 'form-control select2', 'prompt' => 'Attribute Set']),
                            'format' => 'raw',
                            'visible' => (\Yii::$app->session['_eyadatAuth'] == 1),
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
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $btnStr,
                            'buttons' => [
                                'stock' => function ($url, $model) {
                                    return Html::a('<i title="Add stock" class="glyphicon glyphicon-download"></i> ', "javascript:;", [
                                        'title' => Yii::t('yii', 'Add stock'),
                                        //'data-container' => 'body',
                                        'data-toggle' => 'popover',
                                        'data-placement' => 'left',
                                        'data-html' => 'true',
                                        'data-sanitize' => 'false',
                                        'data-content' => '<input placeholder="Enter stock" id="stkPop_' . $model->product_id . '" type="text" name="stock_' . $model->product_id . '" value="" class="form-control"/><div id="error_' . $model->product_id . '">&nbsp;</div><button onclick="product.addMoreStock(' . $model->product_id . ')" type="button" class="btn btn-sm btn-info"><i class="fa fa-check"></i>Save</button>'
                                    ]);
                                },
                                'push' => function ($url, $model) {
                                    if ($model->is_active == 1) {
                                        return Html::a('<i class="glyphicon glyphicon-export"></i> ', "javascript:;", [
                                            'title' => Yii::t('yii', 'Send push'),
                                            'onclick' => 'product.openPushPopup(' . $model->product_id . ',"' . $model->name_en . '")',
                                        ]);
                                    } else {
                                        return '';
                                    }
                                },
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>

        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="pushModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Push notification</h4>
            </div>
            <div class="modal-body">
                <div id="pushResult"></div>
                <input id="pushItem" type="hidden" name="push_item_id" value="" />
                <input id="pushTitle" name="txtMessage" class="form-control" placeholder="Title" />
                <span class="clearfix">&nbsp;</span>
                <textarea id="pushMsg" name="txtMessage" class="form-control" placeholder="Message"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="product.sendTargetedPush('product/send-push')" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusHistoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="statusHistoryModalLabel">Status History</h4>
            </div>
            <div class="modal-body">
                <div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Status</th>
                                <th>Status Date</th>
                                <th>Comment</th>
                                <th>Notify Customer</th>
                            </tr>
                        </thead>
                        <tbody id="statusHistoryResult">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJsFile(BaseUrl::home() . 'js/jquery.fancybox.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
$this->registerJs('$(function () {
            $(\'[data-toggle="popover"]\').popover({
                html: true,
                sanitize: false,
            });
            $(\'body\').on(\'click\', function (e) {
                  $(\'[data-toggle="popover"]\').each(function () {
                  //the \'is\' for buttons that trigger popups
                  //the \'has\' for icons within a button that triggers a popup
                      if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $(\'.popover\').has(e.target).length === 0) {
                          $(this).popover(\'hide\');
                      }
                  });
            });
            $(\'body\').on(\'hidden.bs.popover\', function (e) {
              $(e.target).data("bs.popover").inState = { click: false, hover: false, focus: false }
            });
          })', \yii\web\View::POS_END);
$this->registerJs('
            var triggeredByChild = false;

             $(\'.select-on-check-all\').on(\'ifChecked\', function (event) {
                 $(\'.grid-view tbody .i-checks\').iCheck(\'check\');
                 triggeredByChild = false;
             });

             $(\'.select-on-check-all\').on(\'ifUnchecked\', function (event) {
                 if (!triggeredByChild) {
                     $(\'.grid-view tbody .i-checks\').iCheck(\'uncheck\');
                 }
                 triggeredByChild = false;
             });
             // Removed the checked state from "All" if any checkbox is unchecked
             $(\'.grid-view tbody .i-checks\').on(\'ifUnchecked\', function (event) {
                 triggeredByChild = true;
                 $(\'.select-on-check-all\').iCheck(\'uncheck\');
             });

             $(\'.i-checks\').on(\'ifChecked\', function (event) {
                 if ($(\'.grid-view tbody .i-checks\').filter(\':checked\').length == $(\'.grid-view tbody .i-checks\').length) {
                     $(\'.select-on-check-all\').iCheck(\'check\');
                 }
             });
         ', \yii\web\View::POS_END, 'check');

$this->registerJs('$("a.fancybox").fancybox();', \yii\web\View::POS_READY);
?>