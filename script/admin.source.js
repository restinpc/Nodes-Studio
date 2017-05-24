/**
* Admin JavaScript library source file.
* Do not edit directly.
* @path /script/admin.source.js
*
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
//------------------------------------------------------------------------------
/**
* Submits page meta information from admin.
*/
function edit_seo(id) {
    jQuery('#button_'+id).css("opacity","0.5");
    var title = jQuery("#title_"+id).val();
    var description = jQuery("#description_"+id).val();
    var keywords = jQuery("#keywords_"+id).val();
    var mode = jQuery("#mode_"+id).val();
    jQuery.ajax({
        type:"POST",
        data: {
            "seo_id":id,
            "title":title ,
            "description":description, 
            "keywords":keywords, 
            "mode":mode
        },
        url: root_dir+"/bin.php",
        success:function(data){
            console.log("edit_seo: "+data);
            jQuery('#button_'+id).css("opacity","1");
            jQuery('#button_'+id).css("display","none");
        }
    });
}
//------------------------------------------------------------------------------
/**
* Confirms a product order shipment.
* 
* @param {int} id @mysql[nodes_order]->id.
* @param {string} text Text of message.
* @param {string} shipment Text of button 1.
* @param {string} soldout Text of button 2.
*/
function confirm_order(id, text, shipment, soldout){
    alertify.prompt('<h3>'+text+'</h3><br/>', function (e, str) {if (e) {
        alertify.confirm('<h3>'+shipment+'</h3><br/>'+soldout+'<br/><br/>', 
        function (e, str1) {
            if (e) { 
                jQuery.ajax({
                    type: "POST",
                    data: {	"order_id" : id, "status" : "0", "track" : str },
                    url: root_dir+"/bin.php",
                    success: function(data){
                        console.log(data);
                        window.location = root_dir+"/admin/?mode=orders";
                    }
                });
            }else{
                jQuery.ajax({
                    type: "POST",
                    data: {	"order_id" : id, "status" : "1", "track" : str },
                    url: root_dir+"/bin.php",
                    success: function(data){
                        console.log(data);
                        window.location = root_dir+"/admin/?mode=orders";
                    }
                });
            }
        }, ""); 
    }}, ""); 
}
//------------------------------------------------------------------------------
/**
* Removes an image from a product.
*
* @param {int} id @mysql[nodes_product]->id.
* @param {int} pos Number of picture in product image list.
*/
function delete_image(id, pos){
    jQuery.ajax({
        type: "POST",
        data: {	"product_id" : id, "pos" : pos },
        url: root_dir+"/bin.php",
        success: function(data){ 
            console.log("delete_image: "+data);
            document.getElementById("edit_product_form").submit();
        }
    });
}
//------------------------------------------------------------------------------
/**
* Creates a new transaction.
*
* @param {int} id Target @mysql[nodes_user]->id.
* @param {int} pos Text of message.
*/
function new_transaction(id, text){
    alertify.prompt('<h3>'+text+'</h3><br/>', function (e, str) {if (e) {
        jQuery.ajax({
            type: "POST",
            data: {"user_id": id, "transaction" : str },
            url: root_dir+"/bin.php",
            success: function(data){ 
                console.log("transaction: "+data);
                alertify.alert(data);
                window.location.reload();
            }
        });
    }}, ""); 
}
//------------------------------------------------------------------------------
/**
* Archivates an order.
*
* @param {int} id @mysql[nodes_product]->id.
* @param {string} text Text of message.
*/
function archive_order(id, text){
    alertify.confirm('<h3>'+text+'?<br/><br/>', 
    function (e, str1) {
        if (e) { 
            jQuery.ajax({
                type: "POST",
                data: {	"archive_id" : id },
                url: root_dir+"/bin.php",
                success: function(data){ 
                    console.log("archive_order: "+data);
                    window.location = root_dir+"/admin/?mode=orders";
                }
            });
        }
    }, ""); 
}