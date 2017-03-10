/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function(){
    
jQuery(".add").click(function(){
var itemurl=jQuery("#item_url").val();
jQuery.ajax({
type:"POST",
url:"../wp-admin/admin-ajax.php",
dataType: 'json',
data:{action:"add_item",itemurl:itemurl},
success:function(data){
window.location.href=window.location;
alert(data.msg);
}
});
});

jQuery(".export").click(function(){
var itemurl=jQuery("#item_url").val();
jQuery.ajax({
type:"POST",
url:"../wp-admin/admin-ajax.php",
dataType: 'json',
data:{action:"export_table"},
success:function(data){
window.location.href=window.location;
alert(data.msg);
}
});
});

});


