/**
* Uploader JavaScript library source file.
* Do not edit directly.
* @path /script/uploader.source.js
*
* @name    Nodes Studio    @version 2.0.1.9
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
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
/**
* Positions a crop-frame on image.
*/
function drag(e){
    if (!e) var e = window.event;
    if (e.pageX || e.pageY) 	{
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY) 	{
    posx = e.clientX + document.body.scrollLeft
            + document.documentElement.scrollLeft;
    posy = e.clientY + document.body.scrollTop
            + document.documentElement.scrollTop;
    }
    try{
        var touchobj = e.changedTouches[0] ;
        posx = parseInt(touchobj.clientX);
        posy = parseInt(touchobj.clientY);
    }catch(e){}
    if(drag_mode == 1){
        if(fx==0){
            fx = posx-parseInt(document.getElementById("frame").style.left);
        }if(fy==0){
            fy = posy-parseInt(document.getElementById("frame").style.top);
        }
        posx -= fx;
        posy -= fy;
        if(posx>document.getElementById("img").clientWidth-document.getElementById("frame").clientWidth+34) 
            posx = document.getElementById("img").clientWidth-document.getElementById("frame").clientWidth+34;
        if(posy>document.getElementById("img").clientHeight-document.getElementById("frame").clientHeight+34) 
            posy = document.getElementById("img").clientHeight-document.getElementById("frame").clientHeight+34;
        if(posy<34) posy = 34; 
        if(posx<34) posx = 34;
        document.getElementById("frame").style.left = (posx-6)+"px";
        document.getElementById("frame").style.top = (posy-6)+"px";
        document.getElementById("t").value = posy-32;
        document.getElementById("l").value = posx-32;
    }else if(drag_mode == 2){
        var width1 = posx - parseInt(document.getElementById("frame").style.left);
        var height1 = width1 * (twidth/theight);
        if( (width1-26 <= document.getElementById("img").clientWidth-parseInt(document.getElementById("frame").style.left))
            && (height1-26 <= document.getElementById("img").clientHeight-parseInt(document.getElementById("frame").style.top))
              && height1 >= theight/scale  && width1 >= twidth/scale  ) 
        {
            width = width1+4;
            height = height1+4;
            document.getElementById("frame").style.width = width+"px";
            document.getElementById("frame").style.height = height+"px";   
            document.getElementById("w").value = width*scale;
            document.getElementById("h").value = height*scale;
        }
    }else{
        fx = 0;
        fy = 0;
    }
}
//------------------------------------------------------------------------------
/**
* Submits image and crop details.
*/
function submit_img(){
    if(confirm(confirm_upload)){
        document.getElementById("form").submit();
    }
}
//------------------------------------------------------------------------------
/**
* Positions a crop-frame on image.
*/
function load(){
    if(document.getElementById("img").clientHeight < height){
        height = document.getElementById("img").clientHeight;
        width = parseInt(twidth/theight*height);
    }else if(document.getElementById("img").clientWidth < width){
        width = document.getElementById("img").clientWidth
        height = parseInt(theight/twidth*width);
    }
    document.getElementById("frame").style.width = width;
    document.getElementById("frame").style.height = height;
    document.getElementById("frame").style.display = "block"; 
}  
//------------------------------------------------------------------------------
/**
* Disables dragging mode.
*/
function undrag(){
    drag_mode=0;
}
//------------------------------------------------------------------------------
/**
* Gets an DOM element using ID.
* 
* @param {string} id Element ID.
* @return {object} Returns a DOM elemnt on success, or die with error.
* @usage <code> var id = $id("content"); </code>
*/
function $id(id) {
    return document.getElementById(id);
}
//------------------------------------------------------------------------------
/**
* Stops dragging mode.
*/
function FileDragHover(e) {
    e.stopPropagation();
    e.preventDefault();
    e.target.className = (e.type == "dragover" ? "hover" : "");
}
//------------------------------------------------------------------------------
/**
* Drag-n-drop handler.
*/
function FileSelectHandler(e) {
    FileDragHover(e);
    var files = e.target.files || e.dataTransfer.files;
    for (var i = 0, f; f = files[i]; i++) {
        ParseFile(f);
    }
}
//------------------------------------------------------------------------------
/**
* Parse file details.
*/
function ParseFile(file) {
    if (file.type.indexOf("image") == 0) {
        var reader = new FileReader();
        reader.onload = function(e) {
            UploadFile(file);
        }
        reader.readAsDataURL(file);
    }
}
//------------------------------------------------------------------------------
/**
* Uploads image to server.
*/
function UploadFile(file) {
    if (location.host.indexOf("sitepointstatic") >= 0) return;
    if( $id("fileselect").value != "") return;
    var xhr = new XMLHttpRequest();
    if (xhr.upload && (file.type == "image/jpeg" || file.type == "image/jpg" || file.type == "image/gif" || file.type == "image/png") && file.size <= $id("MAX_FILE_SIZE").value) {
        var fname = file.name;
        xhr.onreadystatechange = function(e) {
            if (xhr.readyState == 4) {
                if(xhr.responseText == "error"){
                    document.getElementById("fileselect").style.display="block";
                    document.getElementById("filedrag").style.display="none";
                    alert(no_drag_drop);
                }else{
                    document.getElementById("new_image").value=fname;
                    document.getElementById("new_image_form").submit();
                }
            }
        };
        xhr.open("POST", dir+"/uploader.php?dragndrop=1", true);
        xhr.setRequestHeader("X-FILENAME", file.name);
        xhr.setRequestHeader("X_FILENAME", file.name);
        xhr.setRequestHeader("HTTP_X_FILENAME", file.name);
        xhr.send(file);
        document.getElementById("filedrag").innerHTML = uploading+".."; 
    }
}
//------------------------------------------------------------------------------
/**
* Initialize file drag-n-drop field.
*/
function Init() {
    var fileselect = $id("fileselect"),
        filedrag = $id("filedrag"),
        submitbutton = $id("submitbutton");
    fileselect.addEventListener("change", FileSelectHandler, false);
    var xhr = new XMLHttpRequest();
    if (xhr.upload) {
        filedrag.addEventListener("dragover", FileDragHover, false);
        filedrag.addEventListener("dragleave", FileDragHover, false);
        filedrag.addEventListener("drop", FileSelectHandler, false);
        filedrag.style.display = "block";
        submitbutton.style.display = "none";
    }
}
//------------------------------------------------------------------------------
/**
* Initialize event functions.
*/
if(post_new_image){
    addHandler(window, "load", load);
    addHandler(window, "touchmove", drag);
    addHandler(window, "mousemove", drag);
    addHandler(window, "mouseup", undrag);
    addHandler(top, "mouseup", undrag);
    addHandler(window, "dblclick", submit_img);
}