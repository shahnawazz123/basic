<?php
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use kartik\tree\TreeView;
use app\models\Tree;

$this->title = 'Category';
$this->params['breadcrumbs'][] = $this->title;

\app\assets\DataTableAsset::register($this);
$this->registerJsFile(BaseUrl::home() . 'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
if($type=='P'){
    $viewName = '_productview';
}
if($type=='C'){
    $viewName = '_clinicview';
}
if($type=='D'){
    $viewName = '_doctorview';
}
if($type=='T'){
    $viewName = '_testlabview';
}

if($type=='F'){
    $viewName = '_pharmacyview';
}

if($type=='L'){
    $viewName = '_labview';
}
?>
<style>
    #categoryTree-detail{
        display: none
    }
    #categoryTree-toolbar .kv-create-root, li.kv-empty{ display: none; }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-body">

                <?php
                echo \app\modules\treemanager\TreeView::widget([
                    'query' => \app\models\Category::find()
                        ->addOrderBy('root, lft')
                        ->where(['is_deleted' => 0,'is_active' => 1,'type' => $type]),
                        'headingOptions' => ['label' => 'Categories'],
                        'rootOptions' => ['label' => '<span class="text-primary">Root</span>'],
                        'fontAwesome' => true,
                        'isAdmin' => true,
                        'displayValue' => 1,
                        'id' => 'categoryTree',
                        'nodeView' => '@app/views/category/'.$viewName,
                        // 'iconEditSettings' => [
                        // 'show' => 'list',
                        // 'listData' => [
                        // 'folder' => 'Folder',
                        // 'file' => 'File',
                        // 'mobile' => 'Phone',
                        // 'bell' => 'Bell',
                        // ]
                        // ],
                        'softDelete' => true,
                        'cacheSettings' => ['enableCache' => true]
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("$(document).ready(function (){
    setTimeout(function(){
       $('.kv-create-root').trigger('click');
       $('#categoryTree-detail').show()
    },100)  
});", \yii\web\View::POS_END);
?>