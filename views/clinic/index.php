<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
use app\helpers\AppHelper;
use yii\helpers\ArrayHelper;
\app\assets\SelectAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClinicsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerJsFile(BaseUrl::home() . 'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$controller = $this->context->action->controller->id;
$this->title = ucwords($controller);//'Clinics';
$this->params['breadcrumbs'][] = $this->title;
$btnStr = '';
$allowPush = true;
$allowCreate = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'create', Yii::$app->user->identity->admin_id, 'A');
$allowView = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowUpdate = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = true;//PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
//$allowPublish = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'publish', Yii::$app->user->identity->admin_id, 'A');

$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}

if ($allowPush) {
    $btnStr .= '{push} ';
}

if ($allowCreate) {
    $btnStr .= '{create} ';
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
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <div class="table-responsive">
                <p class="pull pull-right">
                    <?= ($allowCreate) ? Html::a('Create '.$controller, ['create'], ['class' => 'btn btn-success']):""; ?>
                </p>
                <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'image_en',
                            'value' => function($image) {
                                return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_en;
                            },
                            'format' => ['image', ['width' => '96']],
                            'filter' => false,
                        ],

                        [
                            'attribute' => 'image_ar',
                            'value' => function($image) {
                                return \yii\helpers\BaseUrl::home() . 'uploads/' . $image->image_ar;
                            },
                            'format' => ['image', ['width' => '96']],
                            'filter' => false,
                        ],
                        'name_en',
                        'name_ar',
                        /*[
                            'attribute' => 'type',
                            'value' => function($model) {
                                return ($model->type == 'H') ? 'Hospital' : 'Clinic';
                            },
                            'format' => 'raw',
                            'filter' => false,
                        ],*/
                        [
                            'attribute' => 'category_id',
                            'value' => function($model) 
                            {
                                $list = '';
                                if(isset($model->clinicCategories))
                                {
                                    foreach($model->clinicCategories as $row)
                                    {
                                        $list .= '-'.$row->category->name_en.'<br>';
                                         //array_push($c_list,$cats->category->name_en);
                                    }   
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'category_id', AppHelper::getRecursiveCategory('C'), ['class' => 'form-control select2', 'prompt' => 'Filter By Category']),
                        ],
                        [
                            'attribute' => 'insurance_id',
                            'value' => function($model) 
                            {
                                $list = '';
                                if(isset($model->clinicInsurances))
                                {
                                    foreach($model->clinicInsurances as $row)
                                    {
                                        $list .= '-'.$row->insurance->name_en.'<br>';
                                         //array_push($c_list,$cats->category->name_en);
                                    }   
                                }
                                return $list;
                            },
                            'format' => 'raw',
                            'filter' => Html::activeDropDownList($searchModel, 'insurance_id', AppHelper::getInsuranceList(), ['class' => 'form-control select2', 'prompt' => 'Filter By Insurance']),
                        ],
                        'admin_commission',
                        [
                            'label' => 'Status',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_active, ['class' => "onoffswitch-checkbox", 'id' => "myonoffswitch" . $model->clinic_id,
                                            'onclick' => 'common.changeStatus("clinic/publish",this,' . $model->clinic_id . ')'
                                        ])
                                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->clinic_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                        ],
                        [
                            'label' => 'Featured',
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<div class="onoffswitch">'
                                        . Html::checkbox('onoffswitch', $model->is_featured, ['class' => "onoffswitch-checkbox", 'id' => "featuredButton" . $model->clinic_id,
                                            'onclick' => 'common.changeStatus("clinic/featured",this,' . $model->clinic_id . ')'
                                        ])
                                        . '<label class="onoffswitch-label" for="featuredButton' . $model->clinic_id . '"></label></div>';
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_featured', [1 => 'Featured', 0 => 'Non-Featured'], ['class' => 'form-control select2', 'prompt' => 'Filter'])
                        ],

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $btnStr,
                            'buttons' => [
                                'push' => function($url, $model) {
                                    if ($model->is_active == 1) {
                                        return Html::a('<i class="glyphicon glyphicon-export"></i> ', "javascript:;", [
                                                    'title' => Yii::t('yii', 'Send push'),
                                                    'onclick' => 'common.openPushPopup("' . $model->clinic_id . '","' . $model->name_en . '")',
                                        ]);
                                    } else {
                                        return '';
                                    }
                                },
                            ],
                        
                        ],
                        ],
                    ]); ?>
                </div>
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
                <input id="pushItem" type="hidden" name="push_item_id" value=""/>
                <input id="pushTitle" name="txtMessage" class="form-control" placeholder="Title"/>
                <span class="clearfix">&nbsp;</span>
                <textarea id="pushMsg" name="txtMessage" class="form-control" placeholder="Message"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="common.sendTargetedPush('clinic/send-push')" class="btn btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>