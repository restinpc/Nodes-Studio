/**
* Default template JavaScript source file.
* Do not edit directly.
* @path /template/default/template.source.js
*
* @name    Nodes Studio    @version 2.0.4
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
//------------------------------------------------------------------------------
/**
* jQuery-based redirects to search page at specified request.
* 
* @param {string} search Text to search.
* @usage <code> search("Hello world!"); </code>
*/
function search(search){
    if(alertify){
        alertify.prompt('', '<h3>'+search+'</h3><br/>', '', 
            function(evt, value) {
                window.location=root_dir+"/search/"+encodeURIComponent(value); 
            }, 
            function() { jQuery('.alertify').remove(); }
        ).set('closable', true);
    }else{
        result = prompt(search, ""); 
        if(result){ 
            window.location=root_dir+"/search/"+encodeURIComponent(result);  
        }
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
    jQuery('#mainHead').toggleClass('popupMenu');
    jQuery('#menuIcon .nav_button').toggleClass('hidden');
}
//------------------------------------------------------------------------------
/**
* Hides mobile-navigation menu.
*/
function hideMenu() {
    jQuery('#bigNav').fadeOut(150);
    jQuery('#menuIcon').removeClass('x');
    jQuery('#mainHead').removeClass('popupMenu');
    jQuery('#menuIcon .nav_button').removeClass('hidden');
}
//------------------------------------------------------------------------------
/**
* Displays "Up to top".
*/
var lastPos = 0;
function navScroll() {
    if(jQuery(window).scrollTop() > 0){
        jQuery('#floater').show();
    }else{
        jQuery('#floater').hide();
    }
    if(jQuery(window).scrollTop() > 0 || jQuery("#menuIcon").hasClass("x")){
        if(lastPos > jQuery(window).scrollTop()){
            jQuery('#mainHead').addClass('scrollDown');
            $i = 0;
            while($i <= 100){
                setTimeout(function(){jQuery('#mainHead').css('opacity', ($i/100));}, $i);
                $i++;
            }
        }else{
            $i = 100;
            while($i >= 0){
                setTimeout(function(){jQuery('#mainHead').css('opacity', ($i/100));}, $i);
                $i--;
            }
            jQuery('#mainHead').removeClass('scrollDown');
            hideMenu();
        }
    }else{
        jQuery('#mainHead').removeClass('scrollDown');
        jQuery('#mainHead').css('opacity', '1');
    }
    lastPos = jQuery(window).scrollTop();
}
//------------------------------------------------------------------------------
/**
* Initialize event functions.
*/
jQuery(function() {
    navScroll();
    if(load_events){
        jQuery(window).scroll(navScroll).resize(navScroll);
        jQuery('#menuIcon').click(function() { showHideMenu(); });
        jQuery('#floater').click(function() { scrolltoTop(); });
    }
});
}