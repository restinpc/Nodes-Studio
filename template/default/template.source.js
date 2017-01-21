/**
* Default template JavaScript source file.
* Do not edit directly.
* @path /template/android/template.source.js
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
//------------------------------------------------------------------------------
/**
* Redirects to search page at specified request.
* 
* @param {string} search Text to search.
* @usage <code> search("Hello world!"); </code>
*/
function search(search){
    result = prompt(search, ""); 
    if(result){ 
        window.location=root_dir+"/search/"+encodeURIComponent(result);  
    }
}
//------------------------------------------------------------------------------
if(window.jQuery){
/**
* Displays a cart.
*/
function show_bin(){
    jQuery.ajax({
        type: "POST",
        data: {	"show_bin" : "1" },
        url: root_dir+"/bin.php",
        success: function(data){
            if(!jQuery(".buy_cart").length){
                jQuery('body').append(data);
            }else{
                jQuery(".buy_cart").replaceWith(data);
            }
        }
    });
} 
/**
* Shows mobile-navigation menu.
*/
function showHideMenu() {
    jQuery('#bigNav').fadeToggle(150);
    jQuery('#menuIcon').toggleClass('x');
    jQuery('#menuIcon .nav_button').toggleClass('hidden');
    navScroll();
}
//------------------------------------------------------------------------------
/**
* Hides mobile-navigation menu.
*/
function hideMenu() {
    jQuery('#bigNav').fadeOut(150);
    jQuery('#menuIcon').removeClass('x');
    navScroll();
}
//------------------------------------------------------------------------------
/**
* Displays "Up to top".
*/
function navScroll() {
    if(jQuery(window).scrollTop() > 0){
        jQuery('#floater').show();
    }else{
        jQuery('#floater').hide();
    }
    if(jQuery(window).scrollTop() > 0 || jQuery("#menuIcon").hasClass("x")){
        jQuery('#mainHead').addClass('scrollDown');
    }else{
        jQuery('#mainHead').removeClass('scrollDown');
    }
}
//------------------------------------------------------------------------------
/**
* jQuery-based redirects to search page at specified request.
* 
* @param {string} search Text to search.
* @usage <code> search("Hello world!"); </code>
*/
function search(text){
    alertify.prompt('<h3>'+text+'</h3><br/>', function (e, str) {if (e) {
        if(str){
            window.location=root_dir+"/search/"+encodeURIComponent(str);  
        }
    }}, ""); 
}
//------------------------------------------------------------------------------
/**
* Initialize event functions.
*/
jQuery(function() {
    navScroll();
    jQuery(window).scroll(navScroll).resize(navScroll);
    jQuery('#menuIcon').click(function() { showHideMenu(); });
    jQuery('#floater').click(function() { scrolltoTop(); });
});
}