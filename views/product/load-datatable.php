<?php

use yii\helpers\BaseUrl;

Yii::$app->assetManager->bundles['yii\web\JqueryAsset'] = false;
Yii::$app->assetManager->bundles['yii\web\YiiAsset'] = false;
?>
<style>
    th.dt-body-left{
        max-width: 15px;
    }
    #example_length label,#example_filter label{
        vertical-align: middle;
        line-height: 29px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <p>&nbsp;</p>
        <table id="example" class="display select table" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>
                        <!--<input name="select_all" value="1" id="example-select-all" type="checkbox">-->
                    </th>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Final price</th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
        </table>
    </div>
</div>
<div id="dynamicElement">
    <div id="relatedElement">
        <?php
        if (!empty($productCategory)) {
            foreach ($productCategory as $rp) {
                ?>
                <input id="p_id_<?php echo $rp->product_id; ?>" type="hidden" name="p_id[]" value="<?php echo $rp->product_id; ?>"/>
                <?php
            }
        }
        ?>
    </div>
</div>
<?php

//debugPrint($productCategory);
if (!empty($productCategory)) {
    $relatedList = [];
    foreach ($productCategory as $rp) {
        array_push($relatedList, $rp->product_id);
    }
    $relatedIds = implode(',', $relatedList);
}
else {
    $relatedIds = 0;
}

$this->registerJs("$(document).ready(function (){
   var relatedId = [" . $relatedIds . "];
   var table = $('#example').DataTable({
      'processing':true,
      'serverSide':true,
      'bInfo' : false,
      'ajax': {
         'url': '" . BaseUrl::home() . "product/get-approved-reviewed-list?type=&exclude=&category=".$category_id."' 
      },
      'columnDefs': [{
         'targets': 0,
         'searchable': false,
         'orderable': false,
         'className': 'dt-body-left',
         'render': function (data, type, full, meta){
            $('#relatedElement input').each(function(i,v){
                var selectedVal = parseInt(v.value);
                //console.log(selectedVal);
                if(jQuery.inArray(selectedVal, relatedId) ==-1){
                   relatedId.push(selectedVal);
                }
            });
            //
             var product_id = $('<div/>').text(data).html();
             var checkId = parseInt(product_id);
             var str = '';
             if(jQuery.inArray(checkId, relatedId) !==-1){
                str = 'checked=\"checked\"';
             }
             return '<input class=\"checkboxlist\" '+str+' type=\"checkbox\" name=\"product_id[]\" value=\"' + product_id + '\">';
         }
      }],
      'order': [[1, 'asc']],
//       dom: '<\"toolbar\"> l frtip',
//       initComplete: function(){
//          $(\"div.toolbar\").html('<button onclick=\"product.removeCategoryProduct(".$category_id.")\" class=\"btn btn-sm btn-warning pull pull-left\" type=\"button\" id=\"any_button\">Delete</button>');           
//       }
   });

   // Handle click on \"Select all\" control
   $('#example-select-all').on('click', function(){
      // Get all rows with search applied
      var rows = table.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type=\"checkbox\"]', rows).prop('checked', this.checked);
      if ($('#example-select-all').is(':checked')) {
        $('#relatedElement').html('');
        $('#example tbody :checkbox:checked').each(function (i) {
            var htm = '<input id=\"p_id_'+$(this).val()+'\" type=\"hidden\" name=\"p_id[]\" value=\"'+$(this).val()+'\"/>';
            $('#relatedElement').append(htm);
        })
      }
      else{
         $('#relatedElement').html('');
      }
   });

   // Handle click on checkbox to set state of \"Select all\" control
   $('#example tbody').on('change', 'input[type=\"checkbox\"]', function(){
      var id = $(this).val();
      // If checkbox is not checked
      if(!this.checked){
         var el = $('#example-select-all').get(0);
         // If \"Select all\" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of \"Select all\" control 
            // as 'indeterminate'
            el.indeterminate = true;
         }
         $('#p_id_'+id).remove();
      }
      else{
         var hasId = $('#relatedElement').find('#rp_'+id).length;
         if(hasId < 1)
         {
            var htm = '<input id=\"p_id_'+id+'\" type=\"hidden\" name=\"p_id[]\" value=\"'+id+'\"/>';
            $('#relatedElement').append(htm);
         }
      }
   });

});", \yii\web\View::POS_END, 'related-product-list');
?>