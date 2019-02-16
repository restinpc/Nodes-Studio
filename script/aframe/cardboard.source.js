/**
* A-Frame JavaScript cardboard control file.
* Do not edit directly.
* @path /script/aframe/cardboard.source.js
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
//------------------------------------------------------------------------------
vr_control_state = 0;
var headeset_control_cursor = {"x":0, "y":0};
var headeset_control_cursor_default = {"x":0, "y":1.6};
var device_orientation_x = 0;
var device_orientation_y = 0;
var scroll_interval = null;
var scroll_mode = 0;
var autostart_timeout = null;
var countdown_number = 6;
var countdown_interval = null;
var fuse_array = new Array();
var fuse_timeout_array = new Array();
var last_timestamp = 0;
var vr_load_state = 0;
var vr_left_eye = null;
var vr_right_eye = null;
var vr_left_frame = null;
var vr_right_frame = null;
var vr_left_cursor = null;
var vr_right_cursor = null;
var fuse_interval = null;
var focused_input = null;
var virtual_keyboard_state = false;
var virtual_keyboard_shift_state = false;
var virtual_keyboard_lang = "en";
//------------------------------------------------------------------------------
function headset_control(){
    if(vr_control_state != 1) return;
    var scroll_value = parseInt(jQuery(".vr_left_eye").css("top"));
    headeset_control_cursor.x = device_orientation_x;
    headeset_control_cursor.y = device_orientation_y;
    var x_pos = (headeset_control_cursor.x-headeset_control_cursor_default.x);
    var y_pos = (headeset_control_cursor.y-headeset_control_cursor_default.y);
    try{
        var viewport_width = getViewportWidth();
        var viewport_height = getViewportHeight();
        var cursor_x_pos = (viewport_width/4)-(x_pos);
        var cursor_y_pos = (viewport_height/2)-(y_pos);
        if(cursor_x_pos > 0){
            if(cursor_x_pos < viewport_width/2/0.75-15){
                vr_left_cursor.style.left = cursor_x_pos+"px";
                vr_right_cursor.style.left = cursor_x_pos+"px";
            }else{
                vr_left_cursor.style.left = (viewport_width/2/0.75-15)+"px";
                vr_right_cursor.style.left = (viewport_width/2/0.75-15)+"px";
            }
        }else{
            vr_left_cursor.style.left = "0px";
            vr_right_cursor.style.left = "0px";
        }
        if(cursor_y_pos < 50){
            start_scroll_up();
        }else if(document.body.clientHeight < cursor_y_pos && 
            -scroll_value/0.75 < vr_left_frame.body.clientHeight-viewport_height){
            start_scroll_down();
        }else{
            stop_scroll();
        }
        cursor_y_pos = (-scroll_value/0.75+cursor_y_pos);
        if(cursor_y_pos > -scroll_value/0.75){
            if(cursor_y_pos < viewport_height/0.75-scroll_value/0.75){
                vr_left_cursor.style.top = cursor_y_pos+"px";
                vr_right_cursor.style.top = cursor_y_pos+"px";
            }else{
                vr_left_cursor.style.top = (viewport_height/0.75-scroll_value/0.75)+"px";
                vr_right_cursor.style.top = (viewport_height/0.75-scroll_value/0.75)+"px"; 
            }
        }else{
            vr_left_cursor.style.top = (-scroll_value/0.75)+"px";
            vr_right_cursor.style.top = (-scroll_value/0.75)+"px";
        }
    }catch(e){ console.log(e); }
}
//------------------------------------------------------------------------------
function fuse_function(){
    if(vr_control_state != 1) return;
    if(scroll_mode == 0){
        try{
            var keyboard_mode = 0;
            if(vr_left_frame.getElementById("virtual-keyboard")){
                keyboard_mode = 1;
            }
            var pos = vr_left_eye.find("#nodes-control-cursor").offset();
            var x = pos.left;
            var y = pos.top
            var all = vr_left_frame.body.getElementsByTagName("*");
            var new_fuse_array = new Array();
            for (var i=0; i < all.length; i++) {
                if(all[i].id != ""){
                    var obj = vr_left_eye.find("#"+all[i].id);
                    var pos = obj.offset();
                    var width = parseFloat(obj.outerWidth());
                    var height = parseFloat(obj.outerHeight());
                    if( parseFloat(pos.left) < x && 
                        parseFloat(pos.left)+width > x && 
                        parseFloat(pos.top) < y && 
                        parseFloat(pos.top)+height > y){
                        new_fuse_array.push(all[i].id); 
                        var flag = 0;
                        for(var j = 0; j < fuse_array.length; j++){
                            if(all[i].id == fuse_array[j]){
                                flag = 1;
                            }
                        }
                        if(!flag && all[i].id != "content"){
                            if(all[i].getAttribute("vr-control") != null){
                                if((keyboard_mode && all[i].id.substring(0, 14) == "keyboard-input") || !keyboard_mode){
                                    vr_left_cursor.src="/img/cms/fuse.gif";
                                    vr_right_cursor.src="/img/cms/fuse.gif";
                                    fuse_array.push(all[i].id);
                                    fuse_timeout_array.push(setTimeout(headset_fuse_click, 3000, all[i]));
                                    if(keyboard_mode){
                                        vr_left_eye.find("#virtual-keyboard input").each(function(i, v) {
                                            jQuery(this).css('background', '#4473ba');
                                        });
                                        vr_right_eye.find("#virtual-keyboard input").each(function(i, v) {
                                            jQuery(this).css('background', '#4473ba');
                                        });
                                        var l_el = vr_left_frame.getElementById(all[i].id);
                                        l_el.style.background = "#c0c0c0";
                                        var r_el = vr_right_frame.getElementById(all[i].id);
                                        r_el.style.background = "#c0c0c0";
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }catch(e){console.log(e);}
        try{
            for(var i = 0; i < fuse_array.length; i++){
                flag = 0;
                for(var j = 0; j < new_fuse_array.length; j++){
                    if(new_fuse_array[j] == fuse_array[i]){
                        flag = 1;
                        break;
                    }
                }
                if(!flag){
                    clearTimeout(fuse_timeout_array[i]);
                    fuse_timeout_array.splice(i, 1);
                    vr_left_eye.find("#virtual-keyboard #"+fuse_array[i]).css('background', '#4473ba');
                    vr_right_eye.find("#virtual-keyboard #"+fuse_array[i]).css('background', '#4473ba');
                    fuse_array.splice(i, 1);
                    if(!fuse_timeout_array.length){
                        vr_left_cursor.src="/img/cms/arrow.png";
                        vr_right_cursor.src="/img/cms/arrow.png";      
                    }
                }
            }
        }catch(e){console.log(e);}
    }else{
        try{
            for(var i = 0; i < fuse_array.length; i++){
                clearTimeout(fuse_timeout_array[i]);
                fuse_timeout_array.splice(i, 1);
                vr_left_eye.find("#virtual-keyboard #"+fuse_array[i]).css('background', '#4473ba');
                vr_right_eye.find("#virtual-keyboard #"+fuse_array[i]).css('background', '#4473ba');
                fuse_array.splice(i, 1);
                if(!fuse_timeout_array.length){
                    vr_left_cursor.src="/img/cms/arrow.png";
                    vr_right_cursor.src="/img/cms/arrow.png";      
                }
            }
        }catch(e){console.log(e);}
    }
}
//------------------------------------------------------------------------------
function headset_fuse_click(element){
    var pos = vr_left_eye.find("#nodes-control-cursor").offset();
    var x = pos.left;
    var y = pos.top
    var event = { 
        type: 'click',
        canBubble: true,
        cancelable: true,
        view: element.ownerDocument.defaultView,
        detail: 1,
        screenX: x, 
        screenY: y,
        clientX: x,
        clientY: y,
        ctrlKey: false,
        altKey: false,
        shiftKey: false,
        metaKey: false,
        button: 0,
        relatedTarget: null,
    };
    var e1 = document.createEvent("MouseEvent");
    e1.initMouseEvent(event.type, event.bubbles, event.cancelable, event.view, 
        event.detail, event.screenX, event.screenY, event.clientX, event.clientY, 
        event.ctrlKey, event.altKey, event.shiftKey, event.metaKey, event.button, null);
    vr_right_frame.body.dispatchEvent(e1);
    vr_right_frame.getElementById(element.id).click(e1);
    vr_left_frame.body.dispatchEvent(e1);
    vr_left_frame.getElementById(element.id).click(e1);
    vr_left_eye.find("#virtual-keyboard input").each(function(i, v) {
        jQuery(this).css('background', '#4473ba');
    });
    vr_right_eye.find("#virtual-keyboard input").each(function(i, v) {
        jQuery(this).css('background', '#4473ba');
    });
}
//------------------------------------------------------------------------------
function start_scroll_down(){
    if(!vr_left_frame.getElementById("virtual-keyboard")){
        if(scroll_mode != 2){
            clearInterval(scroll_interval);
            scroll_mode = 2;
            scroll_interval = setInterval(scroll_down, 10);
        }
    }
}
//------------------------------------------------------------------------------
function start_scroll_up(){
    if(scroll_mode != 1){
        clearInterval(scroll_interval);
        scroll_mode = 1;
        scroll_interval = setInterval(scroll_up, 10);
    }
}
//------------------------------------------------------------------------------
function scroll_down(){
    var scroll_value = parseInt(jQuery(".vr_left_eye").css("top"))-10;
    jQuery(".vr_left_eye").css("top", (scroll_value)+"px");
    jQuery(".vr_right_eye").css("top", (scroll_value)+"px");
}
//------------------------------------------------------------------------------
function scroll_up(){
    var scroll_value = parseInt(jQuery(".vr_left_eye").css("top"))+10;
    if(scroll_value > 0) return;
    jQuery(".vr_left_eye").css("top", (scroll_value)+"px");
    jQuery(".vr_right_eye").css("top", (scroll_value)+"px");
}
//------------------------------------------------------------------------------
function stop_scroll(){
    scroll_mode = 0;
    if(scroll_interval){
        clearInterval(scroll_interval);
        scroll_interval = null;
    }
}
//------------------------------------------------------------------------------
function enable_virtual_keyboard(id){
    focused_input = id;
    if(!virtual_keyboard_state){
        var keyboard = '<div style="width: 100%; max-width: 800px; margin: 0px auto;">'+
            '<textarea type="text" class="input" style="overflow:hidden; overflow-y:scroll; width: 90%" id="keyboard-input-data" rows=1 /></textarea>'+
            '<div style="text-align:center;">'+
                '<div id="en-normal">'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "`";\' vr-control id="keyboard-input-1" value="`" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "1";\' vr-control id="keyboard-input-2" value="1" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "2";\' vr-control id="keyboard-input-3" value="2" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "3";\' vr-control id="keyboard-input-4" value="3" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "4";\' vr-control id="keyboard-input-5" value="4" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "5";\' vr-control id="keyboard-input-6" value="5" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "6";\' vr-control id="keyboard-input-7" value="6" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "7";\' vr-control id="keyboard-input-8" value="7" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "8";\' vr-control id="keyboard-input-9" value="8" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "9";\' vr-control id="keyboard-input-10" value="9" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "0";\' vr-control id="keyboard-input-11" value="0" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "-";\' vr-control id="keyboard-input-12" value="-" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "=";\' vr-control id="keyboard-input-13" value="=" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "q";\' vr-control id="keyboard-input-14" value="q" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "w";\' vr-control id="keyboard-input-15" value="w" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "e";\' vr-control id="keyboard-input-16" value="e" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "r";\' vr-control id="keyboard-input-17" value="r" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "t";\' vr-control id="keyboard-input-18" value="t" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "y";\' vr-control id="keyboard-input-19" value="y" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "u";\' vr-control id="keyboard-input-20" value="u" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "i";\' vr-control id="keyboard-input-21" value="i" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "o";\' vr-control id="keyboard-input-22" value="o" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "p";\' vr-control id="keyboard-input-23" value="p" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "[";\' vr-control id="keyboard-input-24" value="[" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "]";\' vr-control id="keyboard-input-25" value="]" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "\\";\' vr-control id="keyboard-input-26" value="\\" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "a";\' vr-control id="keyboard-input-27" value="a" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "s";\' vr-control id="keyboard-input-28" value="s" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "d";\' vr-control id="keyboard-input-29" value="d" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "f";\' vr-control id="keyboard-input-30" value="f" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "g";\' vr-control id="keyboard-input-31" value="g" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "h";\' vr-control id="keyboard-input-32" value="h" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "j";\' vr-control id="keyboard-input-33" value="j" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "k";\' vr-control id="keyboard-input-34" value="k" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "l";\' vr-control id="keyboard-input-35" value="l" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ";";\' vr-control id="keyboard-input-36" value=";" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "\'";\' vr-control id="keyboard-input-37" value="\'" />'+
                    '<input type="button" class="btn small" style="width:13.8%;" onClick=\''+
                        'document.getElementById("keyboard-input-data").innerHTML += "\\n";'+
                        'document.getElementById("keyboard-input-data").scrollTop = document.getElementById("keyboard-input-data").scrollHeight;'+
                        '\' vr-control id="keyboard-input-38" value="Enter" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("en-normal").style.display="none";'+
                        'document.getElementById("en-shift").style.display="block"; virtual_keyboard_shift_state=1;\'  vr-control id="keyboard-input-39" value="&uarr; Shift" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "z";\' vr-control id="keyboard-input-40" value="z" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "x";\' vr-control id="keyboard-input-41" value="x" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "c";\' vr-control id="keyboard-input-42" value="c" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "v";\' vr-control id="keyboard-input-43" value="v" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "b";\' vr-control id="keyboard-input-44" value="b" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "n";\' vr-control id="keyboard-input-45" value="n" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "m";\' vr-control id="keyboard-input-46" value="m" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ",";\' vr-control id="keyboard-input-47" value="," />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ".";\' vr-control id="keyboard-input-48" value="." />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "/";\' vr-control id="keyboard-input-49" value="/" />'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("en-normal").style.display="none";'+
                        'document.getElementById("en-shift").style.display="block"; virtual_keyboard_shift_state=1;\' vr-control id="keyboard-input-50" value="&uarr; Shift" />'+
                '</div>'+
                '<div id="en-shift" style="display:none;">'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "~";\' vr-control id="keyboard-input-57" value="~" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "!";\' vr-control id="keyboard-input-58" value="!" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "@";\' vr-control id="keyboard-input-59" value="@" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "#";\' vr-control id="keyboard-input-60" value="#" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "$";\' vr-control id="keyboard-input-61" value="$" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "%";\' vr-control id="keyboard-input-62" value="%" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "^";\' vr-control id="keyboard-input-63" value="^" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "&";\' vr-control id="keyboard-input-64" value="&" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "*";\' vr-control id="keyboard-input-65" value="*" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "(";\' vr-control id="keyboard-input-66" value="(" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ")";\' vr-control id="keyboard-input-67" value=")" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "_";\' vr-control id="keyboard-input-68" value="_" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "+";\' vr-control id="keyboard-input-69" value="+" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Q";\' vr-control id="keyboard-input-70" value="Q" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "W";\' vr-control id="keyboard-input-71" value="W" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "E";\' vr-control id="keyboard-input-72" value="E" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "R";\' vr-control id="keyboard-input-73" value="R" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "T";\' vr-control id="keyboard-input-74" value="T" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Y";\' vr-control id="keyboard-input-75" value="Y" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "U";\' vr-control id="keyboard-input-76" value="U" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "I";\' vr-control id="keyboard-input-77" value="I" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "O";\' vr-control id="keyboard-input-78" value="O" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "P";\' vr-control id="keyboard-input-79" value="P" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "{";\' vr-control id="keyboard-input-80" value="{" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "}";\' vr-control id="keyboard-input-81" value="}" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "|";\' vr-control id="keyboard-input-82" value="|" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "A";\' vr-control id="keyboard-input-83" value="A" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "S";\' vr-control id="keyboard-input-84" value="S" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "D";\' vr-control id="keyboard-input-85" value="D" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "F";\' vr-control id="keyboard-input-86" value="F" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "G";\' vr-control id="keyboard-input-87" value="G" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "H";\' vr-control id="keyboard-input-88" value="H" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "J";\' vr-control id="keyboard-input-89" value="J" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "K";\' vr-control id="keyboard-input-90" value="K" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "L";\' vr-control id="keyboard-input-91" value="L" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ":";\' vr-control id="keyboard-input-92" value=":" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "\"";\' vr-control id="keyboard-input-93" value="\"" />'+
                    '<input type="button" class="btn small" style="width:13.8%;" onClick=\''+
                        'document.getElementById("keyboard-input-data").innerHTML += "\\n";'+
                        'document.getElementById("keyboard-input-data").scrollTop = document.getElementById("keyboard-input-data").scrollHeight;'+
                        '\' vr-control id="keyboard-input-94" value="Enter" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("en-normal").style.display="block";'+
                        'document.getElementById("en-shift").style.display="none"; virtual_keyboard_shift_state=0;\'  vr-control id="keyboard-input-95" value="&darr; Shift" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Z";\' vr-control id="keyboard-input-96" value="Z" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "X";\' vr-control id="keyboard-input-97" value="X" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "C";\' vr-control id="keyboard-input-98" value="C" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "V";\' vr-control id="keyboard-input-99" value="V" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "B";\' vr-control id="keyboard-input-100" value="B" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "N";\' vr-control id="keyboard-input-101" value="N" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "M";\' vr-control id="keyboard-input-102" value="M" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "<";\' vr-control id="keyboard-input-103" value="<" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ">";\' vr-control id="keyboard-input-104" value=">" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "?";\' vr-control id="keyboard-input-105" value="?" />'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("en-normal").style.display="block";'+
                        'document.getElementById("en-shift").style.display="none"; virtual_keyboard_shift_state=0;\' vr-control id="keyboard-input-106" value="&darr; Shift" />'+
                '</div>'+
                '<div id="ru-normal" style="display:none;">'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ё";\' vr-control id="keyboard-input-107" value="ё" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "1";\' vr-control id="keyboard-input-108" value="1" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "2";\' vr-control id="keyboard-input-109" value="2" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "3";\' vr-control id="keyboard-input-110" value="3" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "4";\' vr-control id="keyboard-input-111" value="4" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "5";\' vr-control id="keyboard-input-112" value="5" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "6";\' vr-control id="keyboard-input-113" value="6" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "7";\' vr-control id="keyboard-input-114" value="7" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "8";\' vr-control id="keyboard-input-115" value="8" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "9";\' vr-control id="keyboard-input-116" value="9" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "0";\' vr-control id="keyboard-input-117" value="0" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "-";\' vr-control id="keyboard-input-118" value="-" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "=";\' vr-control id="keyboard-input-119" value="=" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "й";\' vr-control id="keyboard-input-120" value="й" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ц";\' vr-control id="keyboard-input-121" value="ц" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "у";\' vr-control id="keyboard-input-122" value="у" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "к";\' vr-control id="keyboard-input-123" value="к" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "е";\' vr-control id="keyboard-input-124" value="е" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "н";\' vr-control id="keyboard-input-125" value="н" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "г";\' vr-control id="keyboard-input-126" value="г" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ш";\' vr-control id="keyboard-input-127" value="ш" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "щ";\' vr-control id="keyboard-input-128" value="щ" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "з";\' vr-control id="keyboard-input-129" value="з" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "х";\' vr-control id="keyboard-input-130" value="х" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ъ";\' vr-control id="keyboard-input-131" value="ъ" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "\\";\' vr-control id="keyboard-input-132" value="\\" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ф";\' vr-control id="keyboard-input-133" value="ф" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ы";\' vr-control id="keyboard-input-134" value="ы" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "в";\' vr-control id="keyboard-input-135" value="в" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "а";\' vr-control id="keyboard-input-136" value="а" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "п";\' vr-control id="keyboard-input-137" value="п" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "р";\' vr-control id="keyboard-input-138" value="р" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "о";\' vr-control id="keyboard-input-139" value="о" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "л";\' vr-control id="keyboard-input-140" value="л" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "д";\' vr-control id="keyboard-input-141" value="д" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ж";\' vr-control id="keyboard-input-142" value="ж" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "э";\' vr-control id="keyboard-input-143" value="э" />'+
                    '<input type="button" class="btn small" style="width:13.8%;" onClick=\''+
                        'document.getElementById("keyboard-input-data").innerHTML += "\\n";'+
                        'document.getElementById("keyboard-input-data").scrollTop = document.getElementById("keyboard-input-data").scrollHeight;'+
                        '\' vr-control id="keyboard-input-144" value="Enter" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("ru-normal").style.display="none";'+
                        'document.getElementById("ru-shift").style.display="block"; virtual_keyboard_shift_state=1;\'  vr-control id="keyboard-input-145" value="&uarr; Shift" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "я";\' vr-control id="keyboard-input-146" value="я" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ч";\' vr-control id="keyboard-input-147" value="ч" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "с";\' vr-control id="keyboard-input-148" value="с" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "м";\' vr-control id="keyboard-input-149" value="м" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "и";\' vr-control id="keyboard-input-150" value="и" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "т";\' vr-control id="keyboard-input-151" value="т" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ь";\' vr-control id="keyboard-input-152" value="ь" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "б";\' vr-control id="keyboard-input-153" value="б" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "ю";\' vr-control id="keyboard-input-154" value="ю" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ".";\' vr-control id="keyboard-input-155" value="." />'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("ru-normal").style.display="none";'+
                        'document.getElementById("ru-shift").style.display="block"; virtual_keyboard_shift_state=1;\' vr-control id="keyboard-input-156" value="&uarr; Shift" />'+
                '</div>'+
                '<div id="ru-shift" style="display:none;">'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ё";\' vr-control id="keyboard-input-157" value="Ё" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "!";\' vr-control id="keyboard-input-158" value="!" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "\\"";\' vr-control id="keyboard-input-159" value="\\"" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "№";\' vr-control id="keyboard-input-160" value="№" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ";";\' vr-control id="keyboard-input-161" value=";" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "%";\' vr-control id="keyboard-input-162" value="%" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ":";\' vr-control id="keyboard-input-163" value=":" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "?";\' vr-control id="keyboard-input-164" value="?" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "*";\' vr-control id="keyboard-input-165" value="*" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "(";\' vr-control id="keyboard-input-166" value="(" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ")";\' vr-control id="keyboard-input-167" value=")" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "_";\' vr-control id="keyboard-input-168" value="_" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "+";\' vr-control id="keyboard-input-169" value="+" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Й";\' vr-control id="keyboard-input-170" value="Й" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ц";\' vr-control id="keyboard-input-171" value="Ц" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "У";\' vr-control id="keyboard-input-172" value="У" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "К";\' vr-control id="keyboard-input-173" value="К" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Е";\' vr-control id="keyboard-input-174" value="Е" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Н";\' vr-control id="keyboard-input-175" value="Н" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Г";\' vr-control id="keyboard-input-176" value="Г" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ш";\' vr-control id="keyboard-input-177" value="Ш" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Щ";\' vr-control id="keyboard-input-178" value="Щ" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "З";\' vr-control id="keyboard-input-179" value="З" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Х";\' vr-control id="keyboard-input-180" value="Х" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ъ";\' vr-control id="keyboard-input-181" value="Ъ" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "/";\' vr-control id="keyboard-input-182" value="/" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ф";\' vr-control id="keyboard-input-183" value="Ф" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ы";\' vr-control id="keyboard-input-184" value="Ы" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "В";\' vr-control id="keyboard-input-185" value="В" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "А";\' vr-control id="keyboard-input-186" value="А" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "П";\' vr-control id="keyboard-input-187" value="П" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Р";\' vr-control id="keyboard-input-188" value="Р" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "О";\' vr-control id="keyboard-input-189" value="О" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Л";\' vr-control id="keyboard-input-190" value="Л" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Д";\' vr-control id="keyboard-input-191" value="Д" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ж";\' vr-control id="keyboard-input-192" value="Ж" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Э";\' vr-control id="keyboard-input-193" value="Э" />'+
                    '<input type="button" class="btn small" style="width:13.8%;" onClick=\''+
                        'document.getElementById("keyboard-input-data").innerHTML += "\\n";'+
                        'document.getElementById("keyboard-input-data").scrollTop = document.getElementById("keyboard-input-data").scrollHeight;'+
                        '\' vr-control id="keyboard-input-194" value="Enter" />'+
                    '<br/>'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("ru-normal").style.display="none";'+
                        'document.getElementById("ru-shift").style.display="block"; virtual_keyboard_shift_state=0;\'  vr-control id="keyboard-input-195" value="&uarr; Shift" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Я";\' vr-control id="keyboard-input-196" value="Я" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ч";\' vr-control id="keyboard-input-197" value="Ч" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "С";\' vr-control id="keyboard-input-198" value="С" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "М";\' vr-control id="keyboard-input-199" value="М" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "И";\' vr-control id="keyboard-input-200" value="И" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Т";\' vr-control id="keyboard-input-201" value="Т" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ь";\' vr-control id="keyboard-input-202" value="Ь" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Б";\' vr-control id="keyboard-input-203" value="Б" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "Ю";\' vr-control id="keyboard-input-204" value="Ю" />'+
                    '<input type="button" class="btn small" onClick=\'document.getElementById("keyboard-input-data").innerHTML += ",";\' vr-control id="keyboard-input-205" value="," />'+
                    '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                        'document.getElementById("ru-normal").style.display="none";'+
                        'document.getElementById("ru-shift").style.display="block"; virtual_keyboard_shift_state=0;\' vr-control id="keyboard-input-206" value="&uarr; Shift" />'+
                '</div>'+
                '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                    'if($id(focused_input).tagName.toUpperCase() == "INPUT"){'+
                        '$id(focused_input).value = $id("keyboard-input-data").innerHTML;'+
                    '}else{'+
                        '$id(focused_input).innerHTML = $id("keyboard-input-data").innerHTML;'+
                    '}'+
                    '$id("keyboard-input-data").innerHTML = "";'+
                    '$id("virtual-keyboard").style.display="none";'+
                    'document.body.removeChild($id("virtual-keyboard"));'+
                    'virtual_keyboard_state=false;'+
                    'removeSiteFade();'+
                    '\' vr-control id="keyboard-input-51" value="Accept" />'+
                '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                    'document.getElementById("en-normal").style.display="block";'+
                    'document.getElementById("en-shift").style.display="none";'+
                    'document.getElementById("ru-normal").style.display="none";'+
                    'document.getElementById("ru-shift").style.display="none";'+
                    'virtual_keyboard_lang="en";'+
                    'virtual_keyboard_shift_state=0;'+
                    '\' vr-control id="keyboard-input-53" value="EN" />'+
                '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                    'document.getElementById("en-normal").style.display="none";'+
                    'document.getElementById("en-shift").style.display="none";'+
                    'document.getElementById("ru-normal").style.display="block";'+
                    'document.getElementById("ru-shift").style.display="none";'+
                    'virtual_keyboard_lang="en";'+
                    'virtual_keyboard_shift_state=0;'+
                    '\' vr-control id="keyboard-input-55" value="RU" />'+
                '<input type="button" class="btn small" style="width:26.5%;" onClick=\'document.getElementById("keyboard-input-data").innerHTML += " ";\' vr-control id="keyboard-input-53" value="Space" />'+
                '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                'var str = document.getElementById("keyboard-input-data").innerHTML; str = str.substring(0, str.length - 1); document.getElementById("keyboard-input-data").innerHTML = str;'+
                '\' vr-control id="keyboard-input-54" value="&larr; Bksp" />'+
                '<input type="button" class="btn small" style="width:10.3%;" onClick=\'document.getElementById("keyboard-input-data").innerHTML += "\t";\' vr-control id="keyboard-input-52" value="&rarr; Tab" />'+
                '<input type="button" class="btn small" style="width:10.3%;" onClick=\''+
                    '$id("keyboard-input-data").innerHTML = "";'+
                    '$id("virtual-keyboard").style.display="none";'+
                    'removeSiteFade();'+
                    'document.body.removeChild($id("virtual-keyboard"));'+
                    'virtual_keyboard_state=false;'+
                    '\' vr-control id="keyboard-input-56" value="Cancel" />'+
            '</div>'+
        '</div>';
        var div = vr_left_frame.createElement("div");
        div.id = "virtual-keyboard";
        div.innerHTML= keyboard;
        div.style.top = ((document.body.clientHeight)/0.75-260)+"px";
        //document.body.appendChild(div);
        vr_left_frame.body.appendChild(div);
        var div = vr_right_frame.createElement("div");
        div.id = "virtual-keyboard";
        div.innerHTML= keyboard;
        div.style.top = ((document.body.clientHeight)/0.75-260)+"px";
        vr_right_frame.body.appendChild(div);
        //document.getElementById("keyboard-input-data").focus();
    }
    $id("keyboard-input-data").innerHTML=$id(focused_input).value; 
    virtual_keyboard_state = true;
}
//------------------------------------------------------------------------------
function startPlugin(){
    headeset_control_cursor_default.x = device_orientation_x;
    headeset_control_cursor_default.y = device_orientation_y;
    document.getElementById("vr_left_preloader").style.display = "none";
    document.getElementById("vr_right_preloader").style.display = "none";
    clearTimeout(autostart_timeout);
    clearInterval(countdown_interval);
    fuse_interval = setInterval(fuse_function, 100);
    vr_control_state = 1;
}
//------------------------------------------------------------------------------
function countdown(){
    document.getElementById("vr_left_countdown").innerHTML = --countdown_number;
    document.getElementById("vr_right_countdown").innerHTML = countdown_number;
}
//------------------------------------------------------------------------------
function startCardboard(){
    if(autostart_timeout == null && vr_control_state == 0){
        if(getDocumentWidth() > getDocumentHeight()){
            window.addEventListener("devicemotion", function(event) {
                device_orientation_x += event.rotationRate.alpha*2;
                device_orientation_y = event.accelerationIncludingGravity.z*75;
                headset_control();
            });
            autostart_timeout = setTimeout(startPlugin, 6000);
            countdown_interval = setInterval(countdown, 1000);
            document.getElementById("vr_center_preloader").style.display = "none";
        }
    }
}
//------------------------------------------------------------------------------
function load_frame(){
    vr_load_state++;
    if(vr_load_state == 3){
        try{
            vr_left_eye = jQuery('#nodes_left_frame').contents();
            vr_right_eye = jQuery('#nodes_right_frame').contents();
            vr_left_frame = vr_left_eye[0];
            vr_right_frame = vr_right_eye[0];
            var control_dom = '<img src="/img/cms/arrow.png" id="nodes-control-cursor" style="left: '+(getViewportWidth()/4)+'px; top: '+(getViewportHeight()/2)+'px;" />';
            vr_left_frame.getElementById("content").innerHTML += control_dom;
            vr_right_frame.getElementById("content").innerHTML += control_dom;
            vr_left_cursor = vr_left_frame.getElementById("nodes-control-cursor");
            vr_right_cursor = vr_right_frame.getElementById("nodes-control-cursor");
            jQuery(".vr_left_eye").css("top", "0px");
            jQuery(".vr_right_eye").css("top", "0px");
            vr_left_eye.find('input[type=text]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_left_eye.find('input[type=email]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_left_eye.find('input[type=number]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_left_eye.find('input[type=password]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_left_eye.find('input[type=url]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_left_eye.find('input[type=tel]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_left_eye.find('input[type=search]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_left_eye.find('textarea').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('input[type=text]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('input[type=email]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('input[type=number]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('input[type=password]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('input[type=url]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('input[type=tel]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('input[type=search]').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_right_eye.find('textarea').on('click', function(e){ enable_virtual_keyboard(this.id); });
            vr_load_state = 1;
        }catch(e){ alert("error"); }
    }
}
//------------------------------------------------------------------------------
document.body.onkeyup = function(e){
    if(e.keyCode == 32){;
        startPlugin();
    }
};
//------------------------------------------------------------------------------