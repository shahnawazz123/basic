<?php

use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;

//use himiklab\sortablegrid\SortableGridView;

\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\BrandsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->registerJsFile(BaseUrl::home() . 'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(BaseUrl::home() . 'js/brand.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = 'Brands';
$this->params['breadcrumbs'][] = $this->title;

$permissionStr = '';

$allowActivate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'activate', Yii::$app->user->identity->admin_id, 'A');
$allowCreate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
// $allowPush = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'send-push', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');

// if ($allowPush) {
//     $permissionStr .= '{push}';
// }
if ($allowUpdate) {
    $permissionStr .= '{update}';
}
if ($allowDelete) {
    $permissionStr .= '{delete}';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <?php // echo $this->render('_search', ['model' => $searchModel]);
                ?>

                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create brand', ['create'], ['class' => 'btn btn-success']) : "" ?>
                </p>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'image_name',
                            'value' => function ($model) {
                                return \yii\helpers\BaseUrl::home() . 'uploads/' . $model->image_name;
                            },
                            'format' => ['image', ['width' => '96']],
                            'filter' => false,
                        ],

                        'name_en',
                        'name_ar',
                        //'commission_percentage',
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function ($model, $url) use ($allowActivate) {
                                return '<div class="onoffswitch">'
                                    . Html::checkbox('onoffswitch', $model->is_active, [
                                        'class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->brand_id,
                                        'onclick' => 'brand.changeStatus("brand/activate",this,' . $model->brand_id . ')',
                                        'disabled' => ($allowActivate) ? false : true,
                                    ])
                                    . '<label class="onoffswitch-label" for="myonoffswitch' . $model->brand_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        'sort_order',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $permissionStr,
                            'buttons' => [
                                'push' => function ($url, $model) {
                                    if ($model->is_active == 1) {
                                        return Html::a('<i class="glyphicon glyphicon-export"></i> ', "javascript:;", [
                                            'title' => Yii::t('yii', 'Send push'),
                                            'onclick' => 'product.openPushPopup(' . $model->brand_id . ',"' . $model->name_en . '")',
                                        ]);
                                    }
                                }
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
                <button type="button" onclick="product.sendTargetedPush('brand/send-push')" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>