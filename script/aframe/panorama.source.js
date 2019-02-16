/**
* A-Frame JavaScript panorama processor.
* @path /script/aframe/panorama.source.js
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
var current_object = null;
var current_function = null;
var coordinates = AFRAME.utils.coordinates;
var isCoordinates = coordinates.isCoordinates;
var popup_state = 0;
var popup_position = null;
var vr_load_state = 0;
var zoom = 1;
var object_id = 0;
var navigation_state = 1;
var camera_degree = '';
var coordinates_from = null;
var coordinates_vr = null;
var mouse = new THREE.Vector2(), INTERSECTED;
var ray = null;
var cubemap = null;
var content = null;
var canvas = null;
var scene = null;
var camera = null;
var rig = null;
var logo = null;
var move_point = null;
var marker = null;
var scene_state = 0;
var vr_state = 0;
//------------------------------------------------------------------------------
function pc_mode(e){
    try{
        $id("nodes_scene").resize();
    }catch(ex){}
    setTimeout(function(){
        try{
            $id("nodes_scene").resize();
        }catch(ex){}
    }, 0);
    scene_state = 1;
    $id("cursor").setAttribute("cursor", "rayOrigin", "mouse");
    camera.setAttribute("look-controls", "reverseMouseDrag", "true");
    var arr = document.getElementsByClassName("custom_object");
    for(var i = 0; i < arr.length; i++){
        arr[i].setAttribute("opacity", "1");
    }
    var arr = document.getElementsByClassName("hotpoint");
    for(var i = 0; i < arr.length; i++){
        arr[i].setAttribute("opacity", "1");
    }
    js_hide_wnd();
    marker.setAttribute("scale", "0.01 0.01 0.01");
    $id("fuse").setAttribute("scale", "0.01 0.01 0.01");
    $id("vr-block").style.display = "none";
    $id("nodes_vr_scene").style.opacity = "1";
    $id("vr_logo").setAttribute("opacity", "1");
}
//------------------------------------------------------------------------------
function mobile_mode(e){
    try{
        $id("nodes_scene").resize();
    }catch(ex){}
    setTimeout(function(){
        try{
            $id("nodes_scene").resize();
        }catch(ex){}
    }, 0);
    scene_state = 2;
    canvas.style.cursor = "none";
    $id("nodes_vr_scene").style.opacity = "1";
    logo.setAttribute("opacity", "1");
    $id("vr-block").style.display = "none";
    marker.setAttribute("material", "opacity", "1");
    var arr = document.getElementsByClassName("custom_object");
    for(var i = 0; i < arr.length; i++){
        arr[i].setAttribute("opacity", "1");
    }
    var arr = document.getElementsByClassName("hotpoint");
    for(var i = 0; i < arr.length; i++){
        arr[i].setAttribute("opacity", "1");
    }
    js_hide_wnd();
}
//------------------------------------------------------------------------------
function vr_mode(e){
    try{
        $id("nodes_scene").resize(e);
    }catch(e){}
    scene_state = 3;
    mobile_mode();
    scene.enterVR();
}
//------------------------------------------------------------------------------
function vr_load(){
    if(!vr_load_state){
        try{
            ray = $id("ray");
            cubemap = $id("cubemap");
            content = $id("content");
            canvas = document.getElementsByTagName("canvas")[0];
            scene = $id("nodes_scene");
            camera = $id("camera");
            rig = $id("rig");
            logo = $id("vr_logo");
            move_point = $id("move_point");
            marker = $id("marker");
            marker.setAttribute("material", "opacity", "0");
        }catch(e){ console.log("Error t"); }
        try{
            rotate_camera(); 
        }catch(e){console.log("Error a");}
        try{
            resize_scene();
            addHandler(window, "resize", resize_scene);
        }catch(e){console.log("Error b");}
        try{
            canvas.addEventListener('dblclick', function(e){ });
        }catch(e){console.log("Error c");}
        try{
            scene.addEventListener('enter-vr', function () {
                vr_state = 1;
                if(scene_state == 3){
                    try{
                        $id("sectionsNav").style.opacity="0";
                        $id("sectionsNav").style.display="none"; 
                    }catch(e){}
                    try{
                        $id("scene_map").style.opacity="0";
                        $id("scene_map").style.display="none"; 
                    }catch(e){}
                    try{
                        $id("scene_show_editor").style.opacity="0";
                        $id("scene_show_editor").style.display="none"; 
                    }catch(e){}
                }
             });
            scene.addEventListener('exit-vr', function () {
                vr_state = 0;
                try{
                    $id("sectionsNav").style.opacity="1";
                    $id("sectionsNav").style.display="block"; 
                }catch(e){}
                try{
                    $id("scene_map").style.opacity="1";
                    $id("scene_map").style.display="block"; 
                }catch(e){}
                try{
                    $id("scene_show_editor").style.opacity="1";
                    $id("scene_show_editor").style.display="block"; 
                }catch(e){}
             });
        }catch(e){console.log("Error d");}
        try{
            if (document.body.addEventListener) {
                if ('onwheel' in document) {
                  document.body.addEventListener("wheel", zoom_scene);
                } else if ('onmousewheel' in document) {
                  document.body.addEventListener("mousewheel", zoom_scene);
                } else {
                  document.body.addEventListener("MozMousePixelScroll", zoom_scene);
                } 
            } else {
                document.body.attachEvent("onmousewheel", zoom_scene);
            }
        }catch(e){}
            var pc_mode = '';
            if(getDocumentWidth() > 800){
                pc_mode = '<img src="/img/vr/pc.png" width=100 style="padding:10px; cursor: pointer;" title="PC" onClick=\'pc_mode(event);\'>';
            }            

            show_popup_window('<b style="font-size:28px;">Instructions</b><br/><br/>\n\
                <div style="text-align:left; line-height: 1.6; padding-left: 10px;">Use mouse pointer or your VR headset to look around.<br/>\n\
                Click or focus on object up to 2 seconds to trigger it.<br/>\n\
                Click of focus on floor to navigate to nearest panorama.<br/>\n\
                </div>\n\
                <br/>\n\
                <center>\n\
                Please, select your device<br/><br/>\n\
                '+pc_mode+'<img src="/img/vr/mobile.png" width=100 style="padding:10px; cursor: pointer;" title="Smartphone" onClick=\'mobile_mode(event);\'>\n\
                <img src="/img/vr/vr.png" width=100 style="padding:10px; cursor: pointer;" title="Cardboard" onClick=\'vr_mode(event);\'>\n\
                </center>'
            );
    }
    vr_load_state = 1;
}
//------------------------------------------------------------------------------
function vr_click(object){
    try{
        var func = object.getAttribute("action");
        try{
            eval(func);
        }catch(e){}
        end_fuse();
    }catch(e){}
}
//------------------------------------------------------------------------------
function show_scene_editor(){
    $id("add_area").style.display = "block";
    $id("scene_editor").style.display="block";
    $id("scene_show_editor").style.display="none";
    $id("floor").setAttribute("opacity", "0.1");
    $id("scene_map").style.display = "none";
}
//------------------------------------------------------------------------------
function delete_navigation(id){
    if(confirm("Are you sure?")){
        $id("action_"+id).value = "delete_point";
        $id("object_"+id+"_form").submit();
    }
}
//------------------------------------------------------------------------------
function delete_url(id){
    if(confirm("Are you sure?")){
        $id("action_"+id).value = "delete_url";
        $id("url_"+id+"_form").submit();
    }
}
//------------------------------------------------------------------------------
function apply_changes_url(id){
    $id("url_"+id).setAttribute("position", $id("url_"+id+"_position").value);
    $id("url_"+id).setAttribute("scale", $id("url_"+id+"_scale").value);
}
//------------------------------------------------------------------------------
function apply_changes_navigation(id){
    $id("point_"+id).setAttribute("position", $id("point_"+id+"_position").value);
    $id("point_"+id).setAttribute("scale", $id("point_"+id+"_scale").value);
}
//------------------------------------------------------------------------------
function delete_object(id){
    if(confirm("Are you sure?")){
        $id("action_"+id).value = "delete_object";
        $id("object_"+id+"_form").submit();
    }
}
//------------------------------------------------------------------------------
function apply_changes_object(id){
    $id("object_"+id).setAttribute("color", $id("object_"+id+"_color").value);
    $id("object_"+id).setAttribute("position", $id("object_"+id+"_position").value);
    $id("object_"+id).setAttribute("rotation", $id("object_"+id+"_rotation").value);
    $id("object_"+id).setAttribute("scale", $id("object_"+id+"_scale").value);
}
//------------------------------------------------------------------------------
function resize_scene(){
    try{
        $id("nodes_vr_scene").style.height=(getViewportHeight()-parseInt($id("sectionsNav").clientHeight))+"px";
    }catch(e){}
}
//------------------------------------------------------------------------------
function apply_scene_changes(){
    try{
        rig.setAttribute("position", $id("camera_position").value);
        rig.setAttribute("rotation", $id("camera_rotation").value);
        $id("floor").setAttribute("position", $id("floor_position").value);
        $id("floor").setAttribute("radius", $id("floor_radius").value);
        $id("vr_logo").setAttribute("width", $id("logo_size").value);
        $id("vr_logo").setAttribute("height", $id("logo_size").value);
    }catch(e){
        console.log("error f");
    }
}
//------------------------------------------------------------------------------
function default_settings(){
    if(confirm("Are you sure you want to restore default scene configuration?")){
        $id("act").name = "default";
        $id("scene_form").submit();
    }
}
//------------------------------------------------------------------------------
function navigate(){
    var position = move_point.object3D.getWorldPosition();
    var points = document.getElementsByClassName("hotpoint");
    var point_id = null;
    var lowest = 0;
    for(var i = 0; i < points.length; i++){
        var point = points[i].object3D.getWorldPosition();
        var distance = Math.sqrt(
                (position.x-point.x)*(position.x-point.x)+
                (position.y-point.y)*(position.y-point.y)+
                (position.z-point.z)*(position.z-point.z)
            );
        if(lowest == 0 || distance < lowest){
            lowest = distance;
            point_id = points[i].id;
        }
        //console.log(distance);
    };
    if(point_id != null && point_id != "point_new_nav"){
        vr_click($id(point_id));
    }
    
}
//------------------------------------------------------------------------------
function zoom_scene(e){
    try{
        e = e || window.event;
        var delta = e.deltaY || e.detail || e.wheelDelta;
        var y = 0;
        if(delta > 1 && zoom > 1){
            zoom-=0.5;
        }else if(delta < 1 && zoom < 4){
            zoom+=0.5;
        }
    }catch(e){}
    camera.setAttribute("zoom", zoom);
    if(zoom == 1){
        //console.log("cubemap_0");
        $id("cubemap_0").setAttribute("scale", '1 1 1');
        $id("cubemap_1").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_2").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_3").setAttribute("scale", '1.01 1.01 1.01');
        for(var x = 0; x < $id("cubemap_1").childNodes.length; x++){
            var k = $id("cubemap_1").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "0");
            }catch(e){}
        }
        for(var x = 0; x < $id("cubemap_2").childNodes.length; x++){
            var k = $id("cubemap_2").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "0");
            }catch(e){}
        }
        for(var x = 0; x < $id("cubemap_3").childNodes.length; x++){
            var k = $id("cubemap_3").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "0");
            }catch(e){}
        }
    }else if(zoom == 2){
        //console.log("cubemap_1");
        $id("cubemap_0").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_1").setAttribute("scale", '1 1 1');
        $id("cubemap_2").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_3").setAttribute("scale", '1.01 1.01 1.01');
        for(var x = 0; x < $id("cubemap_1").childNodes.length; x++){
            var k = $id("cubemap_1").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "1");
            }catch(e){}
        }
        for(var x = 0; x < $id("cubemap_2").childNodes.length; x++){
            var k = $id("cubemap_2").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "0");
            }catch(e){}
        }
        for(var x = 0; x < $id("cubemap_3").childNodes.length; x++){
            var k = $id("cubemap_3").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "0");
            }catch(e){}
        }
    }else if(zoom == 3){
        //console.log("cubemap_2");
        $id("cubemap_0").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_1").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_2").setAttribute("scale", '1 1 1');
        $id("cubemap_3").setAttribute("scale", '1.01 1.01 1.01');
        for(var x = 0; x < $id("cubemap_2").childNodes.length; x++){
            var k = $id("cubemap_2").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "1");
            }catch(e){}
        }
        for(var x = 0; x < $id("cubemap_3").childNodes.length; x++){
            var k = $id("cubemap_3").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "0");
            }catch(e){}
        }
    }else if(zoom > 3){
        //console.log("cubemap_3");
        $id("cubemap_0").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_1").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_2").setAttribute("scale", '1.01 1.01 1.01');
        $id("cubemap_3").setAttribute("scale", '1 1 1');
        for(var x = 0; x < $id("cubemap_3").childNodes.length; x++){
            var k = $id("cubemap_3").childNodes[x].id;
            try{
                $id(k).setAttribute("opacity", "1");
            }catch(e){}
        }
    }
    try{
        $id("nodes_scene").resize();
    }catch(ex){}
    setTimeout(function(){
        try{
            $id("nodes_scene").resize();
        }catch(ex){}
    }, 0);
    try{
        e.preventDefault ? e.preventDefault() : (e.returnValue = false); 
    }catch(e){}
}
//------------------------------------------------------------------------------
function start_fuse(object){
    if(object != current_object && vr_load_state){
        current_object = object;
        current_function = setTimeout(function(object){
            vr_click(object);
        }, 2500, object);
        $id("fuse").emit('cursor-fusing');
    }
}
//------------------------------------------------------------------------------
function end_fuse(){
    if(current_function){
        $id("fuse").emit('cursor-stop-fusing');
        $id("fuse").emit('cursor-unfusing');
        clearTimeout(current_function);
        current_function = null;
    }
}
//------------------------------------------------------------------------------
function add_object(){
    marker.setAttribute('radius', '0.001');
    $id("fuse").setAttribute('geometry', 'radiusInner', '0.001');
    $id("fuse").setAttribute('geometry', 'radiusOuter', '0.001');
    $id("add_area").style.display = "none";
    jQuery(".vr_object_window").css("display", "none");
    $id("object_new_obj").setAttribute("opacity", "1");
    $id("object_new_obj_window").style.display = "block";
    object_id = "new_obj";
}
//------------------------------------------------------------------------------
function add_navigation(){
    marker.setAttribute('radius', '0.001');
    $id("fuse").setAttribute('geometry', 'radiusInner', '0.001');
    $id("fuse").setAttribute('geometry', 'radiusOuter', '0.001');
    $id("add_area").style.display = "none";
    $id("point_new_nav").setAttribute("opacity", "1");
    $id("point_new_nav_window").style.display = "block";
    object_id = "new_nav";
}
//------------------------------------------------------------------------------
function rotate_camera(){
    if (window.location.hash) {
        var hash = window.location.hash.replace("#", "");
        hash = hash.split(";");
        if(parseFloat(hash[0])!=0 && parseFloat(hash[1])!=0){
            var rotation = "0 "+parseFloat(hash[1])+" 0";
            try{
                rig.setAttribute("rotation", rotation);
            }catch(e){ console.log("error j"); }
        }
    }
}
//------------------------------------------------------------------------------
function add_url(){
    marker.setAttribute('radius', '0.001');
    $id("fuse").setAttribute('geometry', 'radiusInner', '0.001');
    $id("fuse").setAttribute('geometry', 'radiusOuter', '0.001');
    $id("add_area").style.display = "none";
    $id("url_new_google").setAttribute("opacity", "1");
    $id("url_new_google_window").style.display = "block";
    object_id = "new_google";
}
//------------------------------------------------------------------------------
delete AFRAME.components['nodes-camera'];
AFRAME.registerComponent("nodes-camera", {
    tick: function () {
        if(!vr_load_state) return;
        if(vr_state){
            try{
                $id("sectionsNav").style.opacity="1";
                $id("sectionsNav").style.display="block"; 
            }catch(e){}
        }
        try{
            logo.object3D.rotation.y = camera.object3D.rotation.y+rig.object3D.rotation.y;
            var rotation = (camera.getAttribute("rotation").x+rig.getAttribute("rotation").x)+";"+(camera.getAttribute("rotation").y+rig.getAttribute("rotation").y);
            if(rotation != camera_degree){
                camera_degree = rotation;
                window.history.replaceState( {} , 'Panorama Viewer', root_dir+'/aframe/panorama/'+scene.getAttribute("scene-id")+"#"+rotation);
            }
        }catch(e){ console.log("error 1");}
        //raycaster objects
        try{
            if(scene_state > 0){
                var raycaster = AFRAME.scenes[0].querySelector('[raycaster]').components.raycaster;
                var func_flag = 0;
                var hidden_flag = 0;
                var move_flag = 0;
                for(var i = 0; i < ray.components.raycaster.intersectedEls.length; i++){
                    var t = ray.components.raycaster.intersectedEls[i];
                    if(t.className == "mesh load_later"){  
                        var pos1 = t.object3D.getWorldPosition(new THREE.Vector3);
                        if(t.getAttribute("zoom") == zoom){
                            t.className = "mesh";
                            t.setAttribute("src", t.getAttribute("xsrc"));
                            t.setAttribute("is_load", "true");
                            var meshed = document.getElementsByClassName("mesh load_later");
                            for(var y = 0; y < meshed.length; y++){
                                var tt = meshed[y];
                                if(tt.getAttribute("zoom") == zoom){
                                    var pos2 = tt.object3D.getWorldPosition(new THREE.Vector3());
                                    var dist =  Math.sqrt( (pos1.x-pos2.x)*(pos1.x-pos2.x) + (pos1.y-pos2.y)*(pos1.y-pos2.y) + (pos1.z-pos2.z)*(pos1.z-pos2.z) );
                                    if( (zoom < 3) || (dist < 500 && zoom==3) || dist < 300 ){
                                        setTimeout(function(tt){ tt.className = "mesh"; }, 1000, tt);
                                        tt.setAttribute("src", tt.getAttribute("xsrc")); 
                                        tt.setAttribute("is_load", "true");
                                    }
                                }
                            }
                        }
                    }
                }
                for(var i = 0; i < raycaster.intersectedEls.length; i++){
                    //display popup window
                    if( raycaster.intersectedEls[i].getAttribute("popup") == "true"){
                        popup_state = 1;
                        hidden_flag = 1;
                        break;
                    }
                    //disable fusing on logo
                    if(raycaster.intersectedEls[i].id == "vr_logo"){
                        break;
                    }
                    //display navigation at floor
                    if(raycaster.intersectedEls[i].id == "floor"){
                        var point = raycaster.intersections[i].point;
                        move_point.object3D.position.set(point.x, point.y+0.1, point.z);
                        move_point.setAttribute("material", "opacity", "0.5");
                        move_flag = 1;
                    }
                    //checking to fusing
                    var func = raycaster.intersectedEls[i].getAttribute("action");
                    if(func && scene_state > 1 && navigation_state){
                        func_flag = 1;
                        start_fuse(raycaster.intersectedEls[i]);
                    } else if(raycaster.intersectedEls[i].id == "sky_back"){
                        coordinates_vr = raycaster.intersections[i].point;
                        if(coordinates_from != null){
                            var c = $id("marker").object3D.getWorldPosition(new THREE.Vector3());
                            $id("line").setAttribute("line", "end", c.x+' '+c.y+' '+c.z);
                            $id("line").setAttribute("line", "opacity", "1"); 
                        }
                    }
                }
                if(!move_flag){
                   move_point.setAttribute("material", "opacity", "0.01"); 
                }
                if(!func_flag){
                    current_object = null;
                    end_fuse();
                }
                if(!hidden_flag && popup_state){
                    jQuery(".hidden_layer").attr("opacity", "0");
                    popup_state = 0;
                }
            }
        }catch(e){console.log("error 2");}
        var rotation = camera.getAttribute("rotation");
        var position = $id("vr_point").object3D.getWorldPosition(new THREE.Vector3());
        //Moving objects to cursor
        if(object_id != 0){
            try{
                $id("point_"+object_id+"_position").value = position.x+" "+position.y+" "+position.z;
                $id("point_"+object_id).object3D.position.set( position.x, position.y, position.z );
            }catch(e){};
            try{
                $id("object_"+object_id+"_position").value = position.x+" "+position.y+" "+position.z;
                $id("object_"+object_id).object3D.position.set( position.x, position.y, position.z ); 
            }catch(e){};
            try{
                $id("url_"+object_id+"_position").value = position.x+" "+position.y+" "+position.z;
                $id("url_"+object_id).object3D.position.set( position.x, position.y, position.z ); 
            }catch(e){};
        }
    }
});
//------------------------------------------------------------------------------
function show_map(id){
    show_popup_window('<div><iframe src="'+root_dir+'/level.php?id='+id+'" width=750 height=620></iframe></div>');
}
//------------------------------------------------------------------------------
function reset_scene_object(id){
    if(confirm("Are you sure you want to remove all custom objects from this scene?")){
        jQuery.ajax({
            type: "POST",
            data: {	"scene_reset" : id },
            url: root_dir+"/bin.php",
            success: function(data){ 
                window.location.reload();
            }
        });
    }
}
//------------------------------------------------------------------------------
function load_scene(id, object_id){
    if(!navigation_state) return;
    coordinates_from = null;
    vr_key = 1;
    vr_load_state = 0;
    navigation_state = 0;
    try{
        window.history.replaceState( {} , 'Panorama Viewer', root_dir+'/aframe/panorama/'+id );
    }catch(e){}

    if(move_point.getAttribute("opacity") != '0'){
        var trigger = move_point.object3D.getWorldPosition(new THREE.Vector3());
    }else{
        var trigger = document.getElementById(object_id).object3D.getWorldPosition(new THREE.Vector3());
    }
    move_point.setAttribute("material", "opacity", "0");
    zoom = 1;
    $id("camera").setAttribute("zoom", 1);
    for(var x = 0; x < $id("cubemap_1").childNodes.length; x++){
        var k = $id("cubemap_1").childNodes[x].id;
        try{
            $id(k).setAttribute("opacity", "0");
            $id(k).setAttribute("is_load", "0");
            $id(k).setAttribute("src", "#pixel");
        }catch(e){}
    }
    for(var x = 0; x < $id("cubemap_2").childNodes.length; x++){
        var k = $id("cubemap_2").childNodes[x].id;
        try{
            $id(k).setAttribute("opacity", "0");
            $id(k).setAttribute("is_load", "0");
            $id(k).setAttribute("src", "#pixel");
        }catch(e){}
    }
    for(var x = 0; x < $id("cubemap_3").childNodes.length; x++){
        var k = $id("cubemap_3").childNodes[x].id;
        try{
            $id(k).setAttribute("opacity", "0");
            $id(k).setAttribute("is_load", "0");
            $id(k).setAttribute("src", "#pixel");
        }catch(e){}
    }
    var camera = document.getElementById("camera").object3D.getWorldPosition(new THREE.Vector3());
    var rig = document.getElementById("rig").object3D.position;
    try{
        $id("move_animation").id = '';
    }catch(e){}
    var animation = document.createElement('a-animation');
    var animationData = {
        id: 'move_animation',
        class: 'rig_animation',
        attribute: 'position',
        begin: 'move_rig',
        direction: 'normal',
        dur: 2400,
        repeat: 0,
        to: ((trigger.x+camera.x)/3)+" "+(rig.y)+" "+((trigger.z+camera.z)/3)
    };
    Object.keys(animationData).forEach(function (attr) {
        animation.setAttribute(attr, animationData[attr]);
    });
    jQuery(".vr_hidden").attr("opacity", "0");
    document.getElementById("rig").appendChild(animation);
    var nav_time = parseInt(new Date().getTime());
    setTimeout(function(){
        document.getElementById("rig").emit('move_rig');
        jQuery("#vr-sound").trigger('play');
     }, 100);
    try{
        $id("scene_show_editor").style.display = "none";
        $id("scene_editor").style.display = "none";
        $id("add_area").style.display = "none";
        $id("temp_data").style.display = "none";
        $id("scene_map").style.display = "none";
    }catch(e){}
    var virtual_scene = $id('virtual_scene');
    virtual_scene.innerHTML = "";
    jQuery.ajax({
        type: "POST",
        data: {	"scene" : id },
        url: root_dir+"/bin.php",
        success: function(data){ 
            $id("temp_data").innerHTML = "";
            var json = JSON.parse(data);
            var new_scene = json.children[0].children[0];
            $id("nodes_scene").setAttribute("scene-id", new_scene["scene-id"]);
            for(var i = 0; i < new_scene.children.length; i++){
                var el = new_scene.children[i];
                if(el.id == "cubemap_0"){
                    for(var o = 0; o < new_scene.children[i].children.length; o++){
                         setTimeout(function(child){ 
                             $id(child.id).setAttribute("src", child.src);
                         }, 1, el.children[o]);
                    }
               }else if(el.id == "cubemap_1" || el.id == "cubemap_2" || el.id == "cubemap_3"){
                    for(var o = 0; o < new_scene.children[i].children.length; o++){
                         setTimeout(function(child){ 
                             $id(child.id).setAttribute("xsrc", child.xsrc);
                             $id(child.id).setAttribute("src", child.src);
                         }, 1000-(parseInt(new Date().getTime()) - nav_time), el.children[o]);
                    }
                }else if(el.id == "virtual_scene"){
                    var virtual_scene = $id('virtual_scene');
                    virtual_scene.innerHTML = "";
                    for(var j = 0; j < el.children.length; j++){
                        var obj = el.children[j];
                        var new_obj = document.createElement(obj.tag);
                        new_obj.id = obj.id;
                        virtual_scene.appendChild(new_obj);
                          $.each(obj, function(index, value) {
                            new_obj.setAttribute(index, value);
                        }); 
                        new_obj.className += " vr_hidden";
                        new_obj.setAttribute("opacity", "0");
                    }
                }else if(el.id == "floor"){
                    $id("floor").setAttribute("position", el.position);
                    $id("floor").setAttribute("radius", el.radius);
                }
            }
            setTimeout(function(){
                $id("rig").setAttribute("position", "0 "+document.getElementById("rig").object3D.position.y+" 0");
                vr_load_state = 1;
                
                jQuery(".vr_hidden").attr("opacity", "1");
                jQuery(".hidden_layer").attr("opacity", "0");
                setTimeout(function(){ move_point.object3D.position.set(0, 0.01, 0); navigation_state = 1; }, 1);
            }, 2500-(parseInt(new Date().getTime()) - nav_time));
        }
    });
}
//------------------------------------------------------------------------------
delete AFRAME.components['look-at'];
AFRAME.registerComponent('look-at', {
    schema: {
      default: '',
      parse: function (value) {
        if (isCoordinates(value) || typeof value === 'object') {
          return coordinates.parse(value);
        }
        return value;
      },
      stringify: function (data) {
        if (typeof data === 'object') {
          return coordinates.stringify(data);
        }
        return data;
      }
    },
    init: function () {
      this.target3D = null;
      this.vector = new THREE.Vector3();
    },
    update: function () {
      var self = this;
      var target = self.data;
      var object3D = self.el.object3D;
      var targetEl;
      if (!target || (typeof target === 'object' && !Object.keys(target).length)) {
        return self.remove();
      }
      if (typeof target === 'object') {
        return object3D.lookAt(new THREE.Vector3(target.x, target.y, target.z));
      }
      targetEl = self.el.sceneEl.querySelector(target);
      if (!targetEl) {
        return;
      }
      if (!targetEl.hasLoaded) {
        return targetEl.addEventListener('loaded', function () {
          self.beginTracking(targetEl);
        });
      }
      return self.beginTracking(targetEl);
    },
    tick: function (t) {
      var target;
      var target3D = this.target3D;
      var object3D = this.el.object3D;
      var vector = this.vector;
      if (target3D) {
        target = object3D.parent.worldToLocal(target3D.getWorldPosition(new THREE.Vector3()));
        target.y = object3D.position.y;
        if (this.el.getObject3D('camera')) {
          vector.subVectors(object3D.position, target).add(object3D.position);
        } else {
          vector = target;
        }
        object3D.lookAt(vector);
      }
    },
    beginTracking: function (targetEl) {
      this.target3D = targetEl.object3D;
    }
});