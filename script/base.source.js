/* Nodes Studio source file. Do not edit directly */

var ua = navigator.userAgent.toLowerCase();
var isOpera = (ua.indexOf('opera')  > -1);
var isIE = (!isOpera && ua.indexOf('msie') > -1);

function getBodyScrollTop(){
    return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);
}

function getDocumentHeight(){
    return Math.max(document.compatMode!='CSS1Compat'?document.body.scrollHeight:document.documentElement.scrollHeight,getViewportHeight());
}

function getDocumentWidth(){
    return Math.max(document.compatMode!='CSS1Compat'?document.body.scrollWidth:document.documentElement.scrollWidth,getViewportWidth());
}

function getViewportHeight(){
    return ((document.compatMode||isIE)&&!isOpera)?(document.compatMode=='CSS1Compat')?document.documentElement.clientHeight:document.body.clientHeight:(document.parentWindow||document.defaultView).innerHeight;
}

function getViewportWidth(){
    return ((document.compatMode||isIE)&&!isOpera)?(document.compatMode=='CSS1Compat')?document.documentElement.clientWidth:document.body.clientWidth:(document.parentWindow||document.defaultView).innerWidth;
}

function getXmlHttp(){
  var xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function addHandler(object, event, handler, useCapture) {
     if (object.addEventListener) {
         object.addEventListener(event, handler, useCapture ? useCapture : false);
     } else if (object.attachEvent) {
         object.attachEvent('on' + event, handler);
     } else alert("Add handler is not supported");
}

function setCookie(name, value, options) {
  options = options || {};
  var expires = options.expires;
  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }
  value = encodeURIComponent(value);
  var updatedCookie = name + "=" + value;
  for (var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
    }
  }document.cookie = updatedCookie;
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function js_show_wnd(content){
    if(content&&content!="undefined"){
        disableScroll();
        parent.document.body.style.overflow = "hidden";
        var a = parent.document.createElement("div");
        a.id = "nodes_window";
        a.innerHTML= content;
        parent.document.body.appendChild(a);
        addSiteFade();
    }else{
        js_hide_wnd();
    }
}

function js_pos_wnd(){
    parent.document.getElementById("nodes_window").style.top = ((getViewportHeight()-parent.document.getElementById("nodes_window").clientHeight)/2)+"px";
}

function js_hide_wnd(){
    enableScroll();
    parent.document.body.style.overflow = "auto";
    parent.document.body.removeChild(parent.document.getElementById("nodes_window"));
    removeSiteFade();
}

function empty( mixed_var ) {	 
    return( mixed_var === "" || mixed_var === 0   || mixed_var === "0" || mixed_var === null  || mixed_var === false  ||  ( is_array(mixed_var) && mixed_var.length === 0 ) );
}

function show_login_form(){
    try{ hideMenu(); scrolltoTop(); }catch(e){};
    if(parent.document.getElementById("nodes_window")) js_hide_wnd();
    js_show_wnd('<div style="float:right; margin-top: -15px; margin-right: -15px;"><img style="cursor:pointer;" onClick=\'js_hide_wnd();\' title="Close window" src="'+root_dir+'/img/x.png" width=15 height=15 /></div><img src="'+root_dir+'/img/load.gif" id="loader"><iframe frameborder=0 style="display:none;" width=200 height=260 id="nodes_iframe" src="'+root_dir+'/account.php" onLoad=\'document.getElementById("loader").style.display="none";this.style.display="block";js_pos_wnd();addHandler(window, "resize", js_pos_wnd);\'></iframe>');
    preventDefault();
}

function logout(){
    try{ scrolltoTop(); }catch(e){};
    js_show_wnd('<img src="'+root_dir+'/img/load.gif" id="loader"><iframe frameborder=0 style="display:none;" width=1 height=1 id="nodes_iframe" src="'+root_dir+'/account.php?mode=logout"></iframe>');
}

var keys = {37: 1, 38: 1, 39: 1, 40: 1};

function preventDefault(e) {
  e = e || window.event;
  if (e.preventDefault)
      e.preventDefault();
  e.returnValue = false;  
}

function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}

function disableScroll() {
  if (window.addEventListener)
      window.addEventListener('DOMMouseScroll', preventDefault, false);
  window.onwheel = preventDefault;
  window.onmousewheel = document.onmousewheel = preventDefault;
  window.ontouchmove  = preventDefault;
  document.onkeydown  = preventDefaultForScrollKeys;
}

function enableScroll() {
    if (window.removeEventListener)
        window.removeEventListener('DOMMouseScroll', preventDefault, false);
    window.onmousewheel = document.onmousewheel = null; 
    window.onwheel = null; 
    window.ontouchmove = null;  
    document.onkeydown = null;  
}

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

function show_editor(file){
    if(content&&content!="undefined"){
        window.scrollTo(0,0);
        disableScroll();
        parent.document.body.style.overflow = "hidden";
        var a = parent.document.createElement("div");
        a.id = "editor_window";
        a.innerHTML='<div style="float:left;margin-top: -10px;"><b>'+file+'</b></div><div style="float:right; margin-top: -10px; margin-right: -10px;"><img style="cursor:pointer;" onClick=\'hide_editor();\' title="Close window" src="'+root_dir+'/img/x.png" width=15 height=15 /></div><br/><iframe width=100% height=100% frameborder=0 src="'+root_dir+'/edit.php?file='+file+'" />';
        parent.document.body.appendChild(a);
        addSiteFade();
    }else{
        hide_editor();
    }
}

function hide_editor(){
    enableScroll();
    parent.document.body.style.overflow = "auto";
    parent.document.body.removeChild(parent.document.getElementById("editor_window"));
    removeSiteFade();
}