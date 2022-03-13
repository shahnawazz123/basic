/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var category = {
    showHideFeaturedImageSection:function(){
        var isChecked = $("#category-is_featured").is(":checked");
        console.log(isChecked);
        if(isChecked){
            $("#featured-img-section").show();
            common.addvalidation("categoryTree-nodeform", "category-featured_image_en", "Category[featured_image_en]", ".field-category-featured_image_en", "Featured Image in English can\'t be blank");
            common.addvalidation("categoryTree-nodeform", "category-featured_image_ar", "Category[featured_image_ar]", ".field-category-featured_image_ar", "Featured Image in Arabic can\'t be blank");
        }else{
            $("#featured-img-section").hide();
            common.removeValidation('categoryTree-nodeform', 'category-featured_image_en', '.field-category-featured_image_en');
            common.removeValidation('categoryTree-nodeform', 'category-featured_image_ar', '.field-category-featured_image_ar');
        }
    }
}

