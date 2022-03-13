<?php

use yii\helpers\Html;
//use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\BaseUrl;
use app\helpers\PermissionHelper;
use app\helpers\AppHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->registerJsFile(BaseUrl::home() . 'js/tagsinput.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

\app\assets\SelectAsset::register($this);

$this->title = $model->first_name . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("th {text-align: left !important;}");

$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');
$allowUpdateShippingAddress = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'update-shipping-address', Yii::$app->user->identity->admin_id, 'A');
$allowViewOrder = PermissionHelper::checkUserHasAccess("order", 'view', Yii::$app->user->identity->admin_id, 'A');
$allowViewProduct = PermissionHelper::checkUserHasAccess("product", 'view', Yii::$app->user->identity->admin_id, 'A');
$sBtnStr = '';
if ($allowUpdateShippingAddress) {
    $sBtnStr = "{update}";
}
$oBtnStr = '';
if ($allowViewOrder) {
    $oBtnStr = "{view}";
}
$pBtnStr = '';
if ($allowViewProduct) {
    $pBtnStr = "{view}";
}
?>

<link href="<?=BaseUrl::home();?>/css/tagsinput.css" rel="stylesheet">
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">
                <p class="pull pull-right">
                    <?=
                    ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->user_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) : ""
                    ?>
                </p>

                <div class="clearfix"></div>

                <div class="row">

                    <div class="col-md-12">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                  'label' => 'Image',
                                  'value' => ($model->image!=null)?\yii\helpers\BaseUrl::home() . 'uploads/' . $model->image:"",
                                  'format' => ['image', ['width' => '96']],
                                ],
                                [
                                    'label' => 'User Name',
                                    'value' => $model->first_name.' '.$model->last_name,

                                ],
                                //'first_name',
                                //'last_name',
                                [
                                  'attribute' => 'gender',
                                  'value' => $model->gender,
                                ],
                                'dob', 
                                'blood_group', 
                                'email:email',
                                'height',
                                'weight',
                                'civil_id',
                                [
                                  'attribute' => 'insurance_id',
                                  'label'=>'Insurance',
                                  'value' => (!empty($model->insurance)) ? $model->insurance->name_en : '',
                                ],
                                'insurance_numbar',
                                
                                /*'phone',
                                'code',
                                [
                                    'attribute' => 'is_phone_verified',
                                    'value' => ($model->is_phone_verified == 1) ? 'Yes' : 'No'
                                ],
                                [
                                    'attribute' => 'is_email_verified',
                                    'value' => ($model->is_email_verified == 1) ? 'Yes' : 'No'
                                ],
                                [
                                    'attribute' => 'is_social_register',
                                    'value' => ($model->is_social_register == 1) ? 'Yes' : 'No'
                                ],
                                [
                                    'attribute' => 'social_register_type',
                                    'value' => ($model->is_social_register == 1) ? (($model->social_register_type == 'F') ? "Facebook" : "Google") : 'No'
                                ],*/
                                'device_token',
                                [
                                    'attribute' => 'device_type',
                                    'value' => ($model->device_type == 'I') ? 'iOs' : 'Android',
                                ],
                                'device_model',
                                'app_version',
                                'os_version',
                                [
                                    'attribute' => 'push_enabled',
                                    'value' => ($model->push_enabled == 1) ? 'Yes' : 'No'
                                ],
                                /*[
                                    'attribute' => 'newsletter_subscribed',
                                    'value' => ($model->newsletter_subscribed == 1) ? 'Yes' : 'No'
                                ],*/
                                'create_date',
                            ],
                        ])
                        ?>
                    </div>
                    <?php //print_r($model->kids);?>
                    <div class="col-md-12">
                        <?php
                        $dataProvider0 = new \yii\data\ActiveDataProvider([
                            'query' => $model->getShippingAddresses()
                                ->andWhere(['is_deleted' => 0])
                                ->orderBy(['is_default' => SORT_DESC]),
                            'pagination' => [
                                'pageSize' => 10,
                            ],
                        ]);
                        if (!empty($dataProvider0)) {
                            echo \kartik\grid\GridView::widget([
                                'dataProvider' => $dataProvider0,
                                //'filterModel' => $searchModel,
                                'panel' => [
                                    'heading' => 'Shipping Address',
                                    'type' => DetailView::TYPE_PRIMARY,
                                    /*'headingOptions' => [
                                        'template' => '{title}'
                                    ],*/
                                ],
                                'export' => false,
                                'toggleData' => false,
                                'summary' => '',
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    'first_name',
                                    'last_name',
                                    [
                                        'attribute' => 'country_id',
                                        'value' => function ($data) {
                                            $country = $data->country;
                                            return (!empty($country)) ? $country->name_en : "";
                                        }
                                    ],
                                    [
                                        'attribute' => 'state_id',
                                        'value' => function ($data) {
                                            $state = $data->state;
                                            return (!empty($state)) ? $state->name_en : "";
                                        }
                                    ],
                                    [
                                        'attribute' => 'area_id',
                                        'value' => function ($data) {
                                            $area = $data->area;
                                            return (!empty($area)) ? $area->name_en : "";
                                        }
                                    ],
                                    [
                                        'attribute' => 'block_id',
                                        'value' => function ($data) {
                                            $block = $data->block;
                                            return (!empty($block)) ? $block->name_en : "";
                                        }
                                    ],
                                    'street',
                                    'addressline_1',
                                    'mobile_number',
                                    //'alt_phone_number',
                                    //'location_type',
                                    //'notes',
                                    [
                                        'attribute' => 'is_default',
                                        'value' => function ($data) {
                                            return ($data->is_default == 0) ? "No" : "Yes";
                                        }
                                    ],
                                    /*[
                                        'class' => 'yii\grid\ActionColumn',
                                        'template' => $sBtnStr,
                                        'buttons' => [
                                            'update' => function ($url, $data) {
                                                return Html::a('<i class="glyphicon glyphicon-pencil"></i> ', ['user/update-shipping-address', 'id' => $data->shipping_address_id], [
                                                    'title' => Yii::t('yii', 'Update')
                                                ]);
                                            }
                                        ]
                                    ],*/
                                ],
                            ]);
                        }
                        ?>
                    </div>

                    <div class="col-md-12">
                        <?php
                        $dataProvider1 = new \yii\data\ActiveDataProvider([
                            'query' => $model->getKids()
                                ->andWhere(['is_deleted' => 0])
                                ->orderBy(['kid_id' => SORT_DESC]),
                            'pagination' => [
                                'pageSize' => 10,
                            ],
                        ]);
                        if (!empty($dataProvider1)) {
                            echo \kartik\grid\GridView::widget([
                                'dataProvider' => $dataProvider1,
                                //'filterModel' => $searchModel,
                                'panel' => [
                                    'heading' => 'Family Details',
                                    'type' => DetailView::TYPE_PRIMARY,
                                ],
                                'export' => false,
                                'toggleData' => false,
                                'summary' => '',
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    [
                                      'label' => 'Image',
                                      'value' => function($data){
                                          return  ($data->image!=null)?\yii\helpers\BaseUrl::home() . 'uploads/' . $data->image:"";
                                        },
                                      'format' => ['image', ['width' => '96']],
                                    ],
                                    'name_en',
                                    'gender',
                                    'dob',
                                    'blood_group',
                                    'relation',
                                    'civil_id',
                                ],
                            ]);
                        }
                        ?>
                    </div>

                    <div class="col-md-12">
                        <?php
                        $dataProvider1 = new \yii\data\ActiveDataProvider([
                            'query' => $model->getReports()
                                ->andWhere(['is_deleted' => 0])
                                ->orderBy(['report_id' => SORT_ASC]),
                            'pagination' => [
                                'pageSize' => 10,
                            ],
                        ]);
                        if (!empty($dataProvider1)) {
                            echo \kartik\grid\GridView::widget([
                                'dataProvider' => $dataProvider1,
                                //'filterModel' => $searchModel,
                                
                                'panel' => [
                                    'heading' => 'User Report',
                                    'type' => DetailView::TYPE_PRIMARY,
                                ],
                                'export' => false,
                                'toggleData' => false,
                                'summary' => '',
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    'report_id',
                                    'title',
                                    [
                                        'label'=>'Image',
                                        'value'=> function($data)
                                        {
                                            $images = '';
                                            if(!empty($data->userReportsImages))
                                            {
                                                foreach($data->userReportsImages as $img)
                                                {
                                                   $img1 = (!empty($img->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $img->image) : '';
                                                   
                                                    $images.= '<img src="'.$img1.'" class="img-responsive imgPop" style="width:80px; height:60px;cursor:pointer;display:block;float:left;padding:5px;" title="View Image" data-src="'.$img1.'" data-title="'.$data->title.'">';
                                                }
                                            }
                                            return $images;

                                            /*if(!empty($images))
                                            {
                                                foreach($images as $row)
                                                {
                                                    return '<a href="'.$row.'" title="View Image" target="_blank"><img src="'.$row.'" class="img-responsive" style="width:50px; height:40px;cursor:pointer;"></a>';
                                                }
                                            }*/
                                        },
                                            'format'=>'raw',
                                    ]  
                                ],
                            ]);
                        }
                        ?>
                    </div>

                    <div class="col-md-12">
                        <h4>Report Request</h4>
                    </div>
                    <div class="col-md-12">
                        <?php
                        $dataProvider1 = new \yii\data\ActiveDataProvider([
                            'query' => $model->getRequestReports()
                                ->orderBy(['doctor_report_request_id' => SORT_ASC]),
                            'pagination' => [
                                'pageSize' => 10,
                            ],
                        ]);
                        if (!empty($dataProvider1)) {
                            echo \kartik\grid\GridView::widget([
                                'dataProvider' => $dataProvider1,
                                'panel' => [
                                    'heading' => 'User Report',
                                    'type' => DetailView::TYPE_PRIMARY,
                                ],
                                'export' => false,
                                'toggleData' => false,
                                'summary' => '',
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                        //'doctor_report_request_id',
                                        [
                                            'label'=>'Doctor',
                                            'value'=> function($model) 
                                            {
                                              return  (!empty($model->doctorAppointment)) ? $model->doctorAppointment->doctor->name_en : '';
                                            },
                                        ],
                                        'doctor_request_for',
                                        'request_date',
                                        /*[
                                            'attribute' => 'Request Status',
                                            'value' => function($model) 
                                            {
                                                if($model->status == 'P')
                                                {
                                                    return 'Pending';
                                                }else if($model->status == 'A')
                                                {
                                                    return 'Accepted';
                                                }else if($model->status == 'R')
                                                {
                                                    return 'Rejected';
                                                }
                                            },
                                            'format' => 'raw',
                                        ],*/
                                        [
                                            'label'=>'Report',
                                            'value'=> function($data)
                                            {
                                                $images = '';
                                                if(!empty($data->userReport->userReportsImages))
                                                {
                                                    foreach($data->userReport->userReportsImages as $img)
                                                    {
                                                       $img1 = (!empty($img->image)) ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $img->image) : '';
                                                        
                                                        $images.= '<img src="'.$img1.'" class="img-responsive imgPop" style="width:80px; height:60px;cursor:pointer;display:block;float:left;padding:5px;" title="View Image" data-src="'.$img1.'" data-title="'.$data->userReport->title.'">';
                                                        
                                                    }
                                                }else{
                                                    $img1 = Yii::$app->urlManager->createAbsoluteUrl('images/nopreview_thumb.png');
                                                            $images.= '<img src="'.$img1.'" class="img-responsive" style="width:80px; height:60px;cursor:pointer;display:block;float:left;padding:5px;" title="Not Uploaded Yet">';
                                                }
                                                return $images;
                                            },
                                                'format'=>'raw',
                                        ]
                                    ] 
                            ]);
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="padding: 5px 30px; !important">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <img src="" class="modal_report_src" style="width: 100%;"> <br><br>
        <center><a href="" class="modal_download btn btn-primary btn-sm" download title="Download Image"><i class="fa fa-download"></i> Download</a></center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="myReport" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="padding: 5px 30px; !important">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Report</h4>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin(); ?>

            <div class="row">

                    <?= $form->field($model, 'req_doctor_appointment_id')->hiddenInput(['maxlength' => true,'class'=>'doctor_appointment_id'])->label("") ?>
                <div class="col-md-12">
                    <label>Upload Report</label>
                    <?= 
                         Html::activeDropDownList($model, 'reports', AppHelper::getUserReport($model->user_id), ['class' => 'select2 form-control select2', 'prompt' => 'Filter By User','required'=>true,'multiple'=>true]);?>
                </div>

                <div class="col-md-12"><br>
                    <label>Is Approved?</label>
                    <?= 
                         Html::activeDropDownList($model, 'is_approved', ["1"=>"Yes","0"=>"No"], ['class' => 'select2 form-control', 'prompt' => 'Filter By User','required'=>true]);?>
                </div>
            </div>

            <div class="form-group"><br>
                <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<?php 
$this->registerJs("
    $('.imgPop').click(function(){
        var title = $(this).data('title');
        var src = $(this).data('src');
        $('#myModal').modal('show');
        $('#myModal .modal-title').text(title);
        $('#myModal .modal_report_src').attr('src',src);
        $('#myModal .modal_download').attr('href',src);
    });

    $('.uploadDoc').click(function(){
        var apid = $(this).data('apid');
        $('#myReport .doctor_appointment_id').val(apid);
        $('#myReport').modal('show');
    });
", \yii\web\View::POS_END);
?>

<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>