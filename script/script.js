/**
* Framework JavaScript library source file.
* Do not edit directly.
* @path /script/script.source.js
*
* @name    Nodes Studio    @version 2.0.4
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
var ua = navigator.userAgent.toLowerCase();         // Navigator
var isOpera = (ua.indexOf('opera')  > -1);          // is Opera browser
var isIE = (!isOpera && ua.indexOf('msie') > -1);   // is IE browser
var keys = {37: 1, 38: 1, 39: 1, 40: 1};            // Keyboard "arrows"
var window_state = 0;                               // is page loading
var image_rotator;                                  // Image rotator
var error;                                          // Gateway Timeout HTML data
var pattern = new Array();                          // Array of patterns to swap
var pattern_catch = 0;                              // Pattern flag
var pattern_size = 0;                               // Count of patterns
var seconds;                                        // Timer of session
window.stateChangeIsLocal = true;
History.enabled = true; 
//------------------------------------------------------------------------------
/**
* Gets an DOM element using id.
* 
* @param {string} id Element id.
* @return {object} Returns a DOM elemnt on success, or die with error.
* @usage <code> var id = $id("content"); </code>
*/
function $id(id) {
    return document.getElementById(id);
}
//------------------------------------------------------------------------------
/**
* Gets height of hidden top part of page after scrolling in px.
*/
function getBodyScrollTop(){
    return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) 
        || (document.body && document.body.scrollTop);
}
//------------------------------------------------------------------------------
/**
* Gets a document height in px.
*/
function getDocumentHeight(){
    return Math.max(document.compatMode!='CSS1Compat'?document.body.scrollHeight:
        document.documentElement.scrollHeight,getViewportHeight());
}
//------------------------------------------------------------------------------
/**
* Gets a document width in px.
*/
function getDocumentWidth(){
    return Math.max(document.compatMode!='CSS1Compat'?document.body.scrollWidth:
        document.documentElement.scrollWidth,getViewportWidth());
}
//------------------------------------------------------------------------------
/**
* Gets a viewport height in px.
*/
function getViewportHeight(){
    return ((document.compatMode||isIE)&&!isOpera)?(document.compatMode=='CSS1Compat')?
        document.documentElement.clientHeight:document.body.clientHeight:
        (document.parentWindow||document.defaultView).innerHeight;
}
//------------------------------------------------------------------------------
/**
* Gets a viewport width in px.
*/
function getViewportWidth(){
    return ((document.compatMode||isIE)&&!isOpera)?(document.compatMode=='CSS1Compat')?
        document.documentElement.clientWidth:document.body.clientWidth:
        (document.parentWindow||document.defaultView).innerWidth;
}
//------------------------------------------------------------------------------
/**
* Attaches an event handler to the specified element.
* 
* @param {object} object DOM Element.
* @param {string} event A String that specifies the name of the event.
* @param {function} handler Callback function.
* @param {bool} useCapture Flag to execute in the capturing or in the bubbling phase.
* @usage <code> addHandler(window, "resize", resize_footer); </code>
*/
function addHandler(object, event, handler, useCapture) {
     if (object.addEventListener) {
         object.addEventListener(event, handler, useCapture ? useCapture : false);
     } else if (object.attachEvent) {
         object.attachEvent('on' + event, handler);
     } else alert("Add handler is not supported");
}
//------------------------------------------------------------------------------
/*
* Check is element exist in array.
* 
* @param {mixed} needle
* @param {array} haystack
* @param {bool} strict
* @usage <code> in_array('1', ['1','2','3'], false); </code>
*/
function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    } return found;
}
//------------------------------------------------------------------------------
/**
* Positions any popup windows.
*/
function js_pos_wnd(){
    try{
        var wnd_height = parent.document.getElementById("nodes_login").clientHeight;
        var top = ((getViewportHeight()-wnd_height)/3);
        if(top<0) top = 0;
        parent.document.getElementById("nodes_login").style.top = top+"px"; 
    }catch(e){}
    try{
        var wnd_height = parent.document.getElementById("nodes_popup").clientHeight;
        var wnd_width = parent.document.getElementById("nodes_popup").clientWidth;
        if(getViewportWidth()>600){
            parent.document.getElementById("nodes_popup").style.marginLeft = "-"+(wnd_width/2)+"px"; 
        }else{
            parent.document.getElementById("nodes_popup").style.marginLeft = "0px";
        }
        var top = ((getViewportHeight()-wnd_height)/3);
        if(top<0) top = 0;
        parent.document.getElementById("nodes_popup").style.top = top+"px"; 
    }catch(e){}
}
//------------------------------------------------------------------------------
/**
* Hides any popup windows.
*/
function js_hide_wnd(){
    enableScroll();
    parent.document.body.style.overflow = "auto";
    try{
        parent.document.body.removeChild(parent.document.getElementById("nodes_window"));
    }catch(e){}
    try{
        parent.document.body.removeChild(parent.document.getElementById("nodes_popup"));
    }catch(e){}
    try{
        parent.document.body.removeChild(parent.document.getElementById("nodes_login")); 
    }catch(e){}
    removeSiteFade();
}
//------------------------------------------------------------------------------
/**
* Displays a fullscreen window with specified content.
*/
function show_window(content){
    if(content&&content!="undefined"){
        window.scrollTo(0,0);
        disableScroll();
        parent.document.body.style.overflow = "hidden";
        var a = parent.document.createElement("div");
        a.id = "nodes_window";
        a.innerHTML='<div class="close_button close_wnd" onClick=\'js_hide_wnd();\'>&nbsp;</div>'+content;
        top.document.body.appendChild(a);
        addSiteFade();
    }else{
        js_hide_wnd();
    }
}
//------------------------------------------------------------------------------
/**
* Displays a popup window with specified content.
*/
function show_popup_window(content){
    if(content&&content!="undefined"){
        //window.scrollTo(0,0);
        disableScroll();
        parent.document.body.style.overflow = "hidden";
        var a = parent.document.createElement("div");
        a.id = "nodes_popup";
        a.innerHTML='<div class="close_button close_wnd" onClick=\'js_hide_wnd();\'>&nbsp;</div>'+content;
        top.document.body.appendChild(a);
        addSiteFade();
        js_pos_wnd();
        addHandler(window, "resize", js_pos_wnd);
    }else{
        js_hide_wnd();
    }
}
//------------------------------------------------------------------------------
/**
* Displays file source code viewer.
*/
function show_editor(file){
    show_window('<div class="fl m5"><b>'+file+'</b></div><div class="clear"><br/></div><img src="'+root_dir+'/img/load.gif" id="loader" class="mt18p">'+
        '<iframe width=100% height=95% frameborder=0 src="'+root_dir+'/edit.php?file='+file+'" onLoad=\'document.getElementById("loader").style.display="none";\' />');  
}
//------------------------------------------------------------------------------
/**
* Displays new comment form.
* 
* @param {string} caption Header of form.
* @param {string} submit Button text.
* @param {int} reply @mysql[nodes_comment]->id.
*/
function add_comment(caption, submit, reply){
    show_popup_window('<form method="POST">'+'\n'+
            '<div id="new_comment">'+'\n'+
            '<input type="hidden" name="reply" value="'+reply+'" />'+'\n'+
                '<strong>'+caption+'</strong><br/><br/>'+'\n'+
                '<textarea name="comment" cols=50 class="comment_textarea"></textarea><br/><br/>'+'\n'+
                '<center><input type="submit" class="btn w280" value="'+submit+'" /></center><br/>'+'\n'+
            '</div>'+'\n'+
        '</form>');  
}
//------------------------------------------------------------------------------
/**
* Removes a comment.
* 
* @param {string} text Text of message.
* @param {int} id @mysql[nodes_order]->id.
*/
function delete_comment(text, id){
    if(confirm(text)){
        jQuery.ajax({
            type: "POST",
            data: {	"comment_id" : id },
            url: root_dir+"/bin.php",
            success: function(data){ 
                console.log("comment deleted: "+data);
                window.location.reload();
            }
        });
    }
}
//------------------------------------------------------------------------------
/**
* Displays photo editor.
*/
function show_photo_editor(id, pos){
    show_window('<iframe width=100% height=95% frameborder=0 src="'+root_dir+'/images.php?id='+id+'&pos='+pos+'" scrolling="yes" style="margin-top: 10px;" />');
}
//------------------------------------------------------------------------------
/**
* Displays order window.
*/
function show_order(){
    show_window('<iframe width=100% height=95% frameborder=0 src="'+root_dir+'/order.php" scrolling="yes" style="margin-top: 10px;" />');
}
//------------------------------------------------------------------------------
/**
* Displays login popup window.
*/
function login(){
    if(parent.document.getElementById("nodes_login"))  js_hide_wnd();
    var content = '<div class="close_button close_wnd" onClick=\'js_hide_wnd();\'>&nbsp;</div><img src="'+root_dir+'/img/load.gif" id="loader">'+
        '<iframe frameborder=0 style="display:none;" width=200 height=260 id="nodes_iframe" src="'+root_dir+'/account.php" '+
        'onLoad=\'document.getElementById("loader").style.display="none"; this.style.display="block"; js_pos_wnd();\'></iframe>';
    disableScroll();
    parent.document.body.style.overflow = "hidden";
    var a = parent.document.createElement("div");
    a.id = "nodes_login";
    a.innerHTML= content;
    parent.document.body.appendChild(a);
    addSiteFade();
    addHandler(window, "resize", js_pos_wnd);
    js_pos_wnd();
    try{ 
        scrolltoTop();
        hideMenu(); 
    }catch(e){};
}
//------------------------------------------------------------------------------
/**
* Destorys current user http-session and resets cookie data.
*/
function logout(){
    try{ scrolltoTop(); }catch(e){};
    var content = '<iframe frameborder=0 id="nodes_iframe" class="hidden" src="'+root_dir+'/account.php?mode=logout"></iframe>';
    disableScroll();
    parent.document.body.style.overflow = "hidden";
    var a = parent.document.createElement("div");
    a.id = "nodes_login";
    a.innerHTML= content;
    parent.document.body.appendChild(a);
    addSiteFade();
}
//------------------------------------------------------------------------------
/**
* Checking if variable an array.
*/
function is_array( mixed_var ) {
    return ( mixed_var instanceof Array );
}
//------------------------------------------------------------------------------
/**
* Checking if variable is empty.
*/
function empty( mixed_var ) {	 
    return( mixed_var === "" || mixed_var === 0 || mixed_var === "0" 
        || mixed_var === null || mixed_var === false  
        || (is_array(mixed_var) && mixed_var.length === 0 ));
}
//------------------------------------------------------------------------------
/**
* Prevents default event-listener function.
*/
function preventDefault(e) {
  e = e || window.event;
  if (e.preventDefault)
      e.preventDefault();
  e.returnValue = false;  
}
//------------------------------------------------------------------------------
/**
* Prevents scrolling by keys.
*/
function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}
//------------------------------------------------------------------------------
/**
* Disables page scrolling.
*/
function disableScroll() {
  if (window.addEventListener)
      window.addEventListener('DOMMouseScroll', preventDefault, false);
  window.onwheel = preventDefault;
  window.onmousewheel = document.onmousewheel = preventDefault;
  window.ontouchmove  = preventDefault;
  document.onkeydown  = preventDefaultForScrollKeys;
}
//------------------------------------------------------------------------------
/**
* Enables page scrolling.
*/
function enableScroll() {
    if (window.removeEventListener)
        window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.onmousewheel = document.onmousewheel = null; 
    window.onwheel = null; 
    window.ontouchmove = null;  
    document.onkeydown = null;  
}
//------------------------------------------------------------------------------
/**
* Decodes a string from base64-encode.
*/
function base64_decode( data ) {
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i=0, enc='';
    do { 
        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));
        bits = h1<<18 | h2<<12 | h3<<6 | h4;
        o1 = bits>>16 & 0xff;
        o2 = bits>>8 & 0xff;
        o3 = bits & 0xff;
        if (h3 == 64) enc += String.fromCharCode(o1);
        else if (h4 == 64) enc += String.fromCharCode(o1, o2);
        else enc += String.fromCharCode(o1, o2, o3);
    } while (i < data.length);
    return enc;
}
//------------------------------------------------------------------------------
/**
* Prints human-readable information about a variable.
*/
function print_r(arr, level) {
    var print_red_text = "";
    if(!level) level = 0;
    var level_padding = "";
    for(var j=0; j<level+1; j++) level_padding += "    ";
    if(typeof(arr) == 'object') {
        for(var item in arr) {
            var value = arr[item];
            if(typeof(value) == 'object') {
                print_red_text += level_padding + "'" + item + "' :\n";
                print_red_text += print_r(value,level+1);
            }else print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
        }
    }else print_red_text = "===>"+arr+"<===("+typeof(arr)+")";
    return print_red_text;
}
//------------------------------------------------------------------------------ 
/**
* Requests and 504-error page and update alert function.
*/
if(window.jQuery){
jQuery(function() {
    setTimeout(function(){
        jQuery.ajax({
            url: root_dir+'/timeout.php',
            type: "GET",
            success: function (data) {
                error = data;
            }
        });
    }, 3000);
    if(!alertify){
        alert = function alert(text){
            show_popup_window('<br/><p>'+text+'</p><br/><br/><input type="button" value="OK" onClick=\'js_hide_wnd();\' class="btn w130" /><br/><br/>');
            return false;
        };
    }
    window.onpopstate = function() {              
        goto(window.location.href); 
    }
    ajaxing();
    browser_time();
    checkAnchors();
});
//------------------------------------------------------------------------------
/**
* Submits patterns from user to server.
*/
function submitPatterns() {
    if(pattern[0]){
        jQuery.ajax({
            url: root_dir+'/behavior.php',
            data: { "patterns" : pattern },
            type: "POST",
            success: function (data) {
                pattern = Array();
                pattern_size = 0;
                seconds = new Date().getTime() / 1000;
            }
        });
    }
}
//------------------------------------------------------------------------------
/**
* Checks for an occurrence of a substring in a string.
*/
function searchText( string, needle ) {
   return !!(string.search( needle ) + 1);
}
//------------------------------------------------------------------------------
/**
* Updates <a> tag onclick event with async jquery page loading function.
*/
function ajaxing(){
    window_state = 0;
    jQuery('a').on('click', function(e) {
        if(jQuery(this).attr('href')){
            if(jQuery(this).attr('target') != "_blank" && jQuery(this).attr('target') != "_parent"){
                try{
                    if(jQuery('.mdl-layout__drawer').attr('aria-hidden')=="false"){
                        jQuery('.mdl-layout__obfuscator').click();
                    }jQuery('.android-content').scrollTop(0);
                    jQuery('.android-header').removeClass("is-casting-shadow");
                }catch(err){};
                try{
                    hideMenu();
                    jQuery('body,html').scrollTop(0);
                }catch(err){};
                if(searchText(jQuery(this).attr('href'), location.hostname)){
                    e.preventDefault();
                    history.pushState('', '', jQuery(this).attr('href'));
                    goto(jQuery(this).attr('href'));
                }else if(!searchText(jQuery(this).attr('href'), "http")){
                    e.preventDefault();
                    history.pushState('', '', jQuery(this).attr('href'));
                    goto(jQuery(this).attr('href'));
                }
            }
        }
    });
}
//------------------------------------------------------------------------------
/**
* Submits a search results details form.
*/
function refresh_page(){
    jQuery("#content").animate({opacity: 0}, 300);
    document.getElementById("query_form").submit();
}
//------------------------------------------------------------------------------
/**
* Async page loading using AJAX.
*/
function goto(href) {
    if(!window_state){
        if( href[0] != "#"){
            submitPatterns();
            document.documentElement.style.background = "#fff url(/img/load.gif) no-repeat center center fixed";
            window_state = 1;
            jQuery("#content").animate({opacity: 0}, 300);
            try{ scrolltoTop(); }catch(e){}
            var to = setTimeout(function(){ 
                jQuery("#content").html(error); 
                jQuery("#content").animate({opacity: 1}, 500);
            }, 30000);
            var anchor = '';
            var details = href.split('#');
            if(details[1]){
                href = details[0];
                anchor = details[1];
            }
            jQuery.ajax({
            url: href,
            async: true,
            type: "POST",
            data: {'jQuery': 'true'},
            success: function (data) {
                if(data[data.length-1]=="=") 
                    data = base64_decode(data);
                setTimeout(ajaxing, 1);
                setTimeout(checkAnchors, 1);
                var title = jQuery(data).filter('title').text();
                document.title = title;
                try{ jQuery('.site_title').text(title); }catch(e){}
                setTimeout(function(){ 
                    jQuery("#content").html(data); 
                    jQuery("#content").animate({opacity: 1}, 500); 
                    clearTimeout(to); 
                    try{
                        onload_print_footer();
                    }catch(e){}
                    if(anchor != ''){
                        showAnchor(anchor);
                    }
                }, 300);
            },
            error: function(){
                jQuery("#content").html(error); 
                jQuery("#content").animate({opacity: 1}, 500);
                try{
                    onload_print_footer();
                }catch(e){}
            }
            });
        }else{
            var hash = href.split("#");
            showAnchor(hash[1]);
        }
    }
} 
//------------------------------------------------------------------------------
/**
* Scrolls page to top.
*/
function scrolltoTop(){
    jQuery('body,html').scrollTop(0);
}
//------------------------------------------------------------------------------
/**
* Scrolls a page to specified anchor.
*/
function showAnchor(anchor){
    if(!empty(anchor)){
        try{
            jQuery('.android-content').animate({scrollTop:parseInt(jQuery("a[name='"+anchor+"']").offset().top-80)}, 200,'swing');  
        }catch(e){};
        try{
            jQuery('html, body').animate({scrollTop:parseInt(jQuery("a[name='"+anchor+"']").offset().top-80)}, 200,'swing');  
        }catch(e){};
    }
}
//------------------------------------------------------------------------------
/**
* Checking for # in URL and scroll page to anchor if exists.
*/
function checkAnchors(){
    var hash = window.location.href.split("#");
    if(hash[1]!=""){
        showAnchor(hash[1]);
    }
}
//------------------------------------------------------------------------------
/**
* Loading specified table's page.
*/
function goto_page(page){
    document.getElementById("page_field").value=page;
    refresh_page();
}
//------------------------------------------------------------------------------
/**
* Initialize admin functions.
*/
function admin_init(){
    var js = document.createElement("script");
    js.type = "text/javascript";
    js.src = root_dir+"/script/admin.js";
    document.body.appendChild(js);
}
//------------------------------------------------------------------------------
/**
* Initialize event tinymce library.
*/
function tinymce_init(){
    var script = document.createElement('script');
    script.src = root_dir+"/script/tinymce.js"
    document.body.appendChild(script);
    script.onload = function() { tinymce.init({ selector:'textarea#editable',   
        plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste code'
        ],
      toolbar1: 'insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist link image media preview | forecolor backcolor emoticons | codesample'
    });}
}
//------------------------------------------------------------------------------
/**
* Submits search form.
*/
function submit_search_form(){
    document.getElementById("page_field").value=1;
    refresh_page();
}
//------------------------------------------------------------------------------
/**
* Displays screen fade.
*/
function addSiteFade() {
    if (jQuery('#nodes_fade').length == 0) {
        return jQuery("<div id='nodes_fade'></div>").appendTo('body').fadeIn(500);
    }
}
//------------------------------------------------------------------------------
/**
* Removes screen fade.
*/
function removeSiteFade() {
    jQuery('#nodes_fade').fadeOut(function() {
        jQuery(this).remove()
    });
}
//------------------------------------------------------------------------------
/**
* Converts dates in Unixtime format to current Local time format.
*/
function browser_time(){
    jQuery('.utc_date').each(function (i) {
        var utc = new Date(jQuery(this).attr("alt")*1000); 
        jQuery(this).html(utc.toLocaleString());
    });
}
//------------------------------------------------------------------------------
/**
* Removes a product from cart.
*/
function remove_from_bin(id){
    jQuery.ajax({
        type: "POST",
        data: {	"remove" : id },
        url: root_dir+"/bin.php",
        success: function(data){ 
            window.location = root_dir+"/order.php";
        }
    });
}
//------------------------------------------------------------------------------
/**
* Displays Add-To-Cart message.
*/
function buy_now(id, t0, t1, t2){
    jQuery.ajax({
        type: "POST",
        data: {	"id" : id },
        url: root_dir+"/bin.php",
        success: function (data) {
            try{ show_bin(); }catch(e){ }
        }
    });
    show_popup_window('<br/><p>'+t0+'</p><br/><br/><input type="button" value="'+t1+'" onClick=\'js_hide_wnd();\' class="btn w130" /> &nbsp; <input value="'+t2+'" class="btn w130" type="button" onClick=\'js_hide_wnd(); setTimeout(show_order, 500);\' /><br/><br/>');
}
//------------------------------------------------------------------------------
/**
* Displays money withdrawal form.
*/
function withdrawal(text){
    alertify.prompt('<h3>'+text+'</h3><br/>', function (e, str) {if (e) {
        jQuery.ajax({
            type: "POST",
            data: {"paypal" : str },
            url: root_dir+"/bin.php",
            success: function(data){ 
                console.log("withdrawal: "+data);
                alertify.alert(data);
            }
        });
    }}, ""); 
}
//------------------------------------------------------------------------------
/**
* Displays money deposit form.
*/
function deposit(text){
    alertify.prompt('<h3>'+text+'</h3><br/>', function (e, str) {if (e) {
        try{
            document.getElementById("paypal_price").value = str;
        }catch(err){}
        document.getElementById("pay_button").click();
    }}, ""); 
}
//------------------------------------------------------------------------------
/**
* Redirects to PayPal payment page.
*/
function process_payment(id, price){
    jQuery.ajax({
        type: "POST",
        data: {	"price" : price },
        url: root_dir+"/paypal.php?order_id="+id,
        success: function(data){ 
            console.log("process_payment: "+data);
            window.location = root_dir+"/account/purchases";
        }
    });
}
//------------------------------------------------------------------------------
/**
* Submits a new message to chat.
*/
function post_message(id){
    var txt = jQuery("#nodes_message_text").val();
    jQuery("#nodes_message_text").val("");
    jQuery.ajax({
        type: "POST",
        data: { "text" : txt },
        url: root_dir+'/bin.php?message='+id,
        success: function(data){
            jQuery("#chat").html(data);
            jQuery("#chat").scrollTop(jQuery("#chat")[0].scrollHeight);
        }
    });
}
//------------------------------------------------------------------------------
/**
* Refreshes chat window.
*/
function refresh_chat(id){
    jQuery.ajax({
        type: "GET",
        url: root_dir+'/bin.php?message='+id,
        success: function(data){ 
            jQuery("#nodes_chat").html(data);
            jQuery("#nodes_chat").scrollTop(jQuery("#nodes_chat")[0].scrollHeight);
        }
    });
}
//------------------------------------------------------------------------------
/**
* Displays 1-to-5 stars vote form.
*/
function star_rating(total_rating){
    var star_widht = total_rating * 17 ;
    jQuery('.rating_votes').width(star_widht);
    jQuery('.rating_stars').hover(function() {
      jQuery('.rating_votes, .rating_hover').toggle();
    },
    function() {
      jQuery('.rating_votes, .rating_hover').toggle();
    });
    var margin_doc = jQuery(".rating_stars").offset();
    jQuery(".rating_stars").mousemove(function(e){
        var widht_votes = e.pageX - margin_doc.left;
        if (widht_votes == 0) widht_votes = 1 ;
        user_votes = Math.ceil(widht_votes/17);  
        jQuery('.rating_hover').width(user_votes*17);
    });
    jQuery('.rating_stars').click(function(){
        jQuery('.rating_votes').width((user_votes)*17);
        document.getElementById("nodes_rating").value = user_votes;
    });
}
//------------------------------------------------------------------------------
/**
* Scales an image rotator.
*/
function ScaleSlider() {
    var refSize = image_rotator.$Elmt.parentNode.clientWidth;
    if (refSize) {
        refSize = Math.min(refSize, 600);
        image_rotator.$ScaleWidth(refSize);
    }else {
        window.setTimeout(ScaleSlider, 30);
    }
}
//------------------------------------------------------------------------------
/**
* Displays an image rotator.
*/
function show_rotator(obj){
    try{
        image_rotator = new $JssorSlider$("jssor_1", {
            $AutoPlay: true,
            $FillMode: 5,
            $SlideshowOptions: {
                $Class: $JssorSlideshowRunner$,
                $Transitions: [
                    {$Duration:1200,$Zoom:11,$Rotate:-1,$Easing:{$Zoom:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Round:{$Rotate:0.5},$Brother:{$Duration:1200,$Zoom:1,$Rotate:1,$Easing:$Jease$.$Swing,$Opacity:2,$Round:{$Rotate:0.5},$Shift:90}},
                    {$Duration:1400,x:0.25,$Zoom:1.5,$Easing:{$Left:$Jease$.$InWave,$Zoom:$Jease$.$InSine},$Opacity:2,$ZIndex:-10,$Brother:{$Duration:1400,x:-0.25,$Zoom:1.5,$Easing:{$Left:$Jease$.$InWave,$Zoom:$Jease$.$InSine},$Opacity:2,$ZIndex:-10}},
                    {$Duration:1200,$Zoom:11,$Rotate:1,$Easing:{$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Round:{$Rotate:1},$ZIndex:-10,$Brother:{$Duration:1200,$Zoom:11,$Rotate:-1,$Easing:{$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Round:{$Rotate:1},$ZIndex:-10,$Shift:600}},
                    {$Duration:1500,x:0.5,$Cols:2,$ChessMode:{$Column:3},$Easing:{$Left:$Jease$.$InOutCubic},$Opacity:2,$Brother:{$Duration:1500,$Opacity:2}},
                    {$Duration:1500,x:-0.3,y:0.5,$Zoom:1,$Rotate:0.1,$During:{$Left:[0.6,0.4],$Top:[0.6,0.4],$Rotate:[0.6,0.4],$Zoom:[0.6,0.4]},$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Brother:{$Duration:1000,$Zoom:11,$Rotate:-0.5,$Easing:{$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Shift:200}},
                    {$Duration:1500,$Zoom:11,$Rotate:0.5,$During:{$Left:[0.4,0.6],$Top:[0.4,0.6],$Rotate:[0.4,0.6],$Zoom:[0.4,0.6]},$Easing:{$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Brother:{$Duration:1000,$Zoom:1,$Rotate:-0.5,$Easing:{$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Shift:200}},
                    {$Duration:1500,x:0.3,$During:{$Left:[0.6,0.4]},$Easing:{$Left:$Jease$.$InQuad,$Opacity:$Jease$.$Linear},$Opacity:2,$Outside:true,$Brother:{$Duration:1000,x:-0.3,$Easing:{$Left:$Jease$.$InQuad,$Opacity:$Jease$.$Linear},$Opacity:2}},
                    {$Duration:1200,x:0.25,y:0.5,$Rotate:-0.1,$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Brother:{$Duration:1200,x:-0.1,y:-0.7,$Rotate:0.1,$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2}},
                    {$Duration:1600,x:1,$Rows:2,$ChessMode:{$Row:3},$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:1600,x:-1,$Rows:2,$ChessMode:{$Row:3},$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
                    {$Duration:1600,x:1,$Rows:2,$ChessMode:{$Row:3},$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:1600,x:-1,$Rows:2,$ChessMode:{$Row:3},$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
                    {$Duration:1600,y:-1,$Cols:2,$ChessMode:{$Column:12},$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:1600,y:1,$Cols:2,$ChessMode:{$Column:12},$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
                    {$Duration:1200,y:1,$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:1200,y:-1,$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
                    {$Duration:1200,x:1,$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$Brother:{$Duration:1200,x:-1,$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2}},
                    {$Duration:1200,y:-1,$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$ZIndex:-10,$Brother:{$Duration:1200,y:-1,$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$ZIndex:-10,$Shift:-100}},
                    {$Duration:1200,x:1,$Delay:40,$Cols:6,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Easing:{$Left:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$ZIndex:-10,$Brother:{$Duration:1200,x:1,$Delay:40,$Cols:6,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Easing:{$Top:$Jease$.$InOutQuart,$Opacity:$Jease$.$Linear},$Opacity:2,$ZIndex:-10,$Shift:-100}},
                    {$Duration:1500,x:-0.1,y:-0.7,$Rotate:0.1,$During:{$Left:[0.6,0.4],$Top:[0.6,0.4],$Rotate:[0.6,0.4]},$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2,$Brother:{$Duration:1000,x:0.2,y:0.5,$Rotate:-0.1,$Easing:{$Left:$Jease$.$InQuad,$Top:$Jease$.$InQuad,$Opacity:$Jease$.$Linear,$Rotate:$Jease$.$InQuad},$Opacity:2}},
                    {$Duration:1600,x:-0.2,$Delay:40,$Cols:12,$During:{$Left:[0.4,0.6]},$SlideOut:true,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Assembly:260,$Easing:{$Left:$Jease$.$InOutExpo,$Opacity:$Jease$.$InOutQuad},$Opacity:2,$Outside:true,$Round:{$Top:0.5},$Brother:{$Duration:1000,x:0.2,$Delay:40,$Cols:12,$Formation:$JssorSlideshowFormations$.$FormationStraight,$Assembly:1028,$Easing:{$Left:$Jease$.$InOutExpo,$Opacity:$Jease$.$InOutQuad},$Opacity:2,$Round:{$Top:0.5}}}
                ],
                $TransitionsOrder: 1
            },
            $BulletNavigatorOptions: {
              $Class: $JssorBulletNavigator$
            }
        });
        ScaleSlider();
        addHandler(window, "load", ScaleSlider);
        addHandler(window, "resize", ScaleSlider);
        addHandler(window, "orientationchange", ScaleSlider);
    }catch(e){}
    initPhotoSwipeFromDOM(obj);
}
//------------------------------------------------------------------------------
/**
* Displays an image viewer.
*/
function nodes_galery(src){
    onpop_state = 1;
    for(var i = 0; i<20; i++){
        try{
            if(document.getElementById('nodes_galery_'+i).alt == src){
                document.getElementById('nodes_galery_'+i).click();
            }
        }catch(e){}
    }
}
//------------------------------------------------------------------------------
/**
* Captures a click 
*/
function capture_click(e){
    var left_seconds = new Date().getTime() / 1000;
    pattern[pattern_size++] = Array("1", e.clientX, e.clientY, jQuery(window).scrollTop(), getViewportWidth(), getViewportHeight(), (left_seconds-seconds));
}
//------------------------------------------------------------------------------
/**
* Captures a mouse movement
*/
function capture_mousemove(e){
    if(!pattern_catch){
        pattern_catch = 1;
        var left_seconds = new Date().getTime() / 1000;
        pattern[pattern_size++] = Array("2", e.clientX, e.clientY, jQuery(window).scrollTop(), getViewportWidth(), getViewportHeight(), (left_seconds-seconds));
        setTimeout( function(){
            pattern_catch = 0;
        }, 1000);
    }
}
//------------------------------------------------------------------------------
/**
* Enabling handlers
*/
if(submit_patterns){
    addHandler(window, "click", capture_click);
    addHandler(window, "mousemove", capture_mousemove);
    seconds = new Date().getTime() / 1000;
    setInterval(submitPatterns, 10000);
}
}