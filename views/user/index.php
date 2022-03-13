<?php
use himiklab\sortablegrid\SortableGridView;
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\grid\GridView;
use app\helpers\PermissionHelper;
\app\assets\SelectAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(BaseUrl::home() . 'js/user.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$urlQuery = '';
if ($_SERVER['QUERY_STRING'] != "") {
    $urlQuery = '?' . $_SERVER['QUERY_STRING'];
}
$permissionStr = '';
$allowExport = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'export', Yii::$app->user->identity->admin_id, 'A');
$allowView = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'view', Yii::$app->user->identity->admin_id, 'A');
$allowDelete = PermissionHelper::checkUserHasAccess($this->context->action->controller->id, 'delete', Yii::$app->user->identity->admin_id, 'A');

if ($allowView) {
    $permissionStr .= '{view}';
}
if ($allowDelete) {
    $permissionStr .= '{delete}';
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <p class="pull pull-right">
                    <?= ($allowExport)?Html::a('Export to excel', ['export' . $urlQuery], ['class' => 'btn btn-info']):"" ?>
                </p>

                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        'user_name',
                        'email:email',
                        'phone',
                        [
                            'attribute' => 'is_social_register',
                            'value' => function($model) {
                                return ($model->is_social_register == 1) ? "Yes" : "No";
                            },
                            'filter' => Html::activeDropDownList($searchModel, 'is_social_register', ['1' => 'Yes', '0' => 'No'], ['class' => 'form-control select2', 'prompt' => 'Filter By Status']),
                        ],
                        
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $permissionStr
                        ],
                    ],
                ]);
                ?>
            </div>

        </div>
    </div>
</div>
<?php
$this->registerJs("$('.select2').select2({placeholder: \"Please Select\",});", \yii\web\View::POS_END, 'select-picker');
?>
