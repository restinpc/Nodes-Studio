/**
* A-Frame JavaScript multiplayer processor.
* @path /script/aframe/multiplayer.source.js
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
class nodes_multiplayer {
    constructor() {
        this.data = { 
            current_id: null,
            last_timestamp: -1,
            model_position: {'x':255, 'y':255, 'z':255},
            model_rotation: {'x':255, 'y':255, 'z':255},
            last_upload_position: {'x':255, 'y':255, 'z':255},
            zoom: 0,
            fuse_click: null,
            submit_interval: 1000,
            last_submit: 0,
            frame: 0,
            request_time: 0,
            ping: 0
        };

        delete AFRAME.components['nodes-click'];
        AFRAME.registerComponent("nodes-click", {
            schema: {
                event: {default: "multiplayer.custom_function"}
            },
            init: function () {
                var data = this.data;
                var el = this.el; 
                el.addEventListener("mouseenter", function () {
                    multiplayer.data.current_id = this.id;
                    var duration = document.getElementById("nodes_fuse").getAttribute("dur");
                    multiplayer.data.fuse_click = setTimeout( multiplayer.fuse, duration, data.event, el);
                    document.querySelector('#cursor').emit('cursor-fusing');
                    document.getElementById("cursor").setAttribute("material", "opacity", "0.9");
                    try{
                        document.getElementById("model_name_"+this.id).setAttribute("opacity","1");
                        document.getElementById("model_select_"+this.id).setAttribute("opacity","1");
                    }catch(e){}
                });
                el.addEventListener("click", function () {
                    multiplayer.fuse(data.event, el);
                });
                el.addEventListener("mouseleave", function () {
                    if(this.id == multiplayer.data.current_id){
                        clearTimeout(multiplayer.data.fuse_click);
                        multiplayer.data.current_id = null;
                        document.querySelector('#nodes_fuse').stop();
                        document.querySelector('#cursor').emit("cursor-unfusing");
                        document.getElementById("cursor").setAttribute("material", "opacity","0.1");
                        document.getElementById("cursor").setAttribute("material", "opacity","0.1");
                        try{
                            document.getElementById("model_name_"+this.id).setAttribute("opacity","0");
                            document.getElementById("model_select_"+this.id).setAttribute("opacity","0");
                        }catch(e){}
                    }
                });
            }
        });

        delete AFRAME.components['nodes-scene'];
        AFRAME.registerComponent("nodes-scene", {
            init:function(){
                //console.log("nodes-scene -> init()");
                this.el.sceneEl.addEventListener("loaded",this.load.bind(this));
                multiplayer.download_data(document.querySelector("a-scene").getAttribute("nodes-lobby"));
            },
            load:function(){
                if(this.el.getAttribute("nodes-state") == "connected"){
                    this.el.setAttribute("nodes-state", "execute");
                    document.querySelector("a-scene").classList.remove("loading");
                }else{
                    this.el.setAttribute("nodes-state", "autorun"); 
                }
            }
        });

        delete AFRAME.components['nodes-camera'];
        AFRAME.registerComponent("nodes-camera", {
             tick: function () {
                multiplayer.base_event(this);
            }
        });
        
        delete AFRAME.components['nodes-terrain'];
        AFRAME.registerComponent('nodes-terrain',{
            init:function(){
                var d = this.el.innerHTML;
                var json = JSON.parse(d);
                try{
                    var p = 1;
                    var texture = THREE.ImageUtils.loadTexture(root_dir+'/img/vr/grass2.jpg');
                    do{
                        
                        var geom = new THREE.Geometry();
                        for(var j = 0; j < 3; j++){
                            geom.vertices.push(new THREE.Vector3( parseFloat(json[p][j][0]),  parseFloat(json[p][j][1]), parseFloat(json[p][j][2])  ));
                        }
                        var face = new THREE.Face3(0, 1, 2, new THREE.Vector3(0, 0, 0));
                        geom.faces.push(face);
                        var mat = new THREE.MeshLambertMaterial({color: 0xFFFFFF, map:texture, side: THREE.BackSide});
                        var mesh = new THREE.Mesh(geom, mat);
                        this.el.object3D.add( mesh );
                        
                        var geom = new THREE.Geometry();
                        for(var j = 3; j < 6; j++){
                            geom.vertices.push(new THREE.Vector3( parseFloat(json[p][j][0]),  parseFloat(json[p][j][1]), parseFloat(json[p][j][2])  ));
                        }
                        var face = new THREE.Face3(0, 1, 2, new THREE.Vector3(0, 0, 0));
                        geom.faces.push(face);
                        var mat = new THREE.MeshLambertMaterial({color: 0xFFFFFF, map:texture, side: THREE.FrontSide});
                        var mesh = new THREE.Mesh(geom, mat);
                        this.el.object3D.add( mesh );
                        
                    }while(p++);
                }catch(e){}  
                //$id("model-to-terrain").components.raycaster.refreshObjects();
            }
        });

        delete AFRAME.components['nodes-trigger'];
        AFRAME.registerComponent("nodes-trigger", {
            schema: {
                radius: {
                    type: 'number',
                    default: 0
                },
            },
            tick: function () {
                var data = this.data;
                // TODO ..
            }
        });

        try{
            document.addEventListener("dblclick", this.click_event);
            if (document.body.addEventListener) {
                if ('onwheel' in document) {
                  document.body.addEventListener("wheel", this.zooomScene);
                } else if ('onmousewheel' in document) {
                  document.body.addEventListener("mousewheel", this.zooomScene);
                } else {
                  document.body.addEventListener("MozMousePixelScroll", this.zooomScene);
                }
            } else {
                document.body.attachEvent("onmousewheel", this.zooomScene);
            }
        }catch(e){}
    }

    click_event(event){
        if(multiplayer.data.current_id != null){
            document.getElementById(multiplayer.data.current_id).click(event);
        }
    }

    base_event(sender){
        var position = sender.el.object3D.position;
        var rotation = sender.el.object3D.rotation;
        var object_rotation = document.querySelector("#user_model");
        object_rotation.object3D.rotation.set(
            object_rotation.object3D.rotation.x, 
            rotation.y-THREE.Math.degToRad(90), 
            object_rotation.object3D.rotation.z
        );
        var flag = 0;
        if( multiplayer.data.model_position.x == 255 && 
            multiplayer.data.model_position.y == 255 && 
            multiplayer.data.model_position.z == 255){
            multiplayer.data.model_position.x = position.x;
            multiplayer.data.model_position.y = position.y;
            multiplayer.data.model_position.z = position.z;
        }
        if( multiplayer.data.model_rotation.x == 255 && 
            multiplayer.data.model_rotation.y == 255 && 
            multiplayer.data.model_rotation.z == 255){
            multiplayer.data.model_rotation.x = rotation.x;
            multiplayer.data.model_rotation.y = rotation.y;
            multiplayer.data.model_rotation.z = rotation.z;
        }
        if( rotation.x != multiplayer.data.model_rotation.x ||
            rotation.y != multiplayer.data.model_rotation.y ||
            rotation.z != multiplayer.data.model_rotation.z){
            multiplayer.data.model_rotation.x = rotation.x;
            multiplayer.data.model_rotation.y = rotation.y;
            multiplayer.data.model_rotation.z = rotation.z;
        }
        if( position.x != multiplayer.data.model_position.x ||
            position.y != multiplayer.data.model_position.y ||
            position.z != multiplayer.data.model_position.z){
            /*
            try{
                $id("model-to-terrain").components.raycaster.refreshObjects();
                var raw = $id("model-to-terrain").components.raycaster.rawIntersections;
                for(var i = 0; i < raw.length; i++){
                    if(raw[i].object.el && raw[i].object.el.id != "model-to-terrain"){
                        var y = parseFloat(raw[i].point.y);
                        if(parseFloat(position.y + 3) > y){
                            position.y = y;
                            break;
                        }
                    }
                }
            }catch(e){
                console.log(e);
            }
            try{
                $id("model-forward").components.raycaster.refreshObjects();
                var raw = $id("model-forward").components.raycaster.rawIntersections;
                if(raw.length > 0 && raw[0].distance < 2){
                    document.getElementById("camera").setAttribute("position", multiplayer.data.model_position);
                    position = multiplayer.data.model_position;      
                }
            }catch(e){
                console.log(e);
            }
            */
            document.querySelector("#user_model").object3D.position.set(
                position.x, 
                position.y-10, 
                position.z
            );  
            multiplayer.data.model_position.x = position.x;
            multiplayer.data.model_position.y = position.y;
            multiplayer.data.model_position.z = position.z;
            //submit section
            if( multiplayer.data.last_upload_position.x == 255 && 
                multiplayer.data.last_upload_position.y == 255 && 
                multiplayer.data.last_upload_position.z == 255){
                var position = document.querySelector("#user_model").object3D.getWorldPosition(new THREE.Vector3());
                multiplayer.data.last_upload_position.x = position.x;
                multiplayer.data.last_upload_position.y = position.y;
                multiplayer.data.last_upload_position.z = position.z;
            }
            try{
                var n = new Date().getTime();
                if(multiplayer.data.last_submit < n - multiplayer.data.submit_interval){
                    multiplayer.data.last_submit = n;
                    var position = document.querySelector("#user_model").object3D.getWorldPosition(new THREE.Vector3());
                    if( multiplayer.data.last_upload_position.x != position.x ||
                        multiplayer.data.last_upload_position.y != position.y ||
                        multiplayer.data.last_upload_position.z != position.z){
                            multiplayer.data.last_upload_position.x = position.x;
                            multiplayer.data.last_upload_position.y = position.y;
                            multiplayer.data.last_upload_position.z = position.z;
                            var model = document.getElementById("user_model").object3D.position;
                            var rig = document.getElementById("rig").object3D.position; 
                            var value = {'x':model.x+rig.x, 'y':model.y+rig.y, 'z':model.z+rig.z};
                            var rotation = $id("user_model").object3D.rotation;
                            var rig_rotation = $id("rig").object3D.rotation;
                            var x = parseFloat(rotation._x) + parseFloat(rig_rotation._x);
                            var y = parseFloat(rotation._y) + parseFloat(rig_rotation._y);
                            var z = parseFloat(rotation._z) + parseFloat(rig_rotation._z);
                            var obj_rotation = {'x':x, 'y':y, 'z':z};
                            multiplayer.upload_data(document.querySelector("a-scene").getAttribute("nodes-lobby"), value, obj_rotation);
                    }
                }
            }catch(e){
                console.log("Error");
                console.log(e);
            }
        }
    }

    trigger(element_id){
        var rig = document.getElementById("rig").object3D.position;
        var camera = document.getElementById("camera").object3D.position;
        var trigger = document.getElementById(element_id).object3D.getWorldPosition(new THREE.Vector3());
        //document.getElementById("rig").object3D.position.set(trigger.x+camera.x, rig.y, trigger.z+camera.z);
        var animation = document.createElement('a-animation');
        var animationData = {
            id: 'move_animation_'+multiplayer.data.frame,
            class: 'rig_animation',
            attribute: 'position',
            begin: 'move_object_'+multiplayer.data.frame,
            direction: 'normal',
            dur: 1000,
            repeat: 0,
            to: (trigger.x+camera.x)+" "+(rig.y)+" "+(trigger.z+camera.z)
        };
        Object.keys(animationData).forEach(function (attr) {
            animation.setAttribute(attr, animationData[attr]);
        });
        document.getElementById("rig").appendChild(animation);
        document.getElementById("rig").emit('move_object_'+multiplayer.data.frame)
    }

    custom_function(sender){
        console.log("Click -> "+sender);
    }

    parse_data(data){
        var json = JSON.parse(data);
        if(json && json.timestamp > 0){
            if(json.cmd == "load"){
                for(var i = 0; i < json.count; i++){
                    if(json.fout[i].object_id == "rig"){
                        var rig = document.getElementById("rig").object3D.position;
                        var camera = document.getElementById("camera").object3D.position;
                        document.getElementById(json.fout[i].object_id).object3D.position.set(
                            parseFloat(json.fout[i].position[0])+parseFloat(camera.x),
                            parseFloat(rig.y),
                            parseFloat(json.fout[i].position[2])+parseFloat(camera.z)
                        );
                        document.getElementById("rig").setAttribute("rotation", json.fout[i].rotation);
                    }else{
                        document.getElementById(json.fout[i].object_id).object3D.position.set(
                            parseFloat(json.fout[i].position[0]),
                            parseFloat(json.fout[i].position[1]),
                            parseFloat(json.fout[i].position[2])
                        );
                        document.getElementById(json.fout[i].object_id).setAttribute("rotation", json.fout[i].rotation);
                    }
                }
            }else if(json.cmd == "action"){
                for(var i = 0; i < json.count; i++){
                    multiplayer.data.frame++;
                    try{
                        document.getElementById(json.fout[i].object_id).object3D.position.set(
                            json.fout[i].position[0],
                            json.fout[i].position[1],
                            json.fout[i].position[2]
                        );
                        document.getElementById(json.fout[i].object_id).setAttribute("rotation", json.fout[i].rotation);
                    }catch(e){}
                }
            }
            if(json.timestamp > multiplayer.data.last_timestamp){
                multiplayer.data.last_timestamp = json.timestamp;
            }
        }
    }

    download_data(lobby_id){
        try{
            if(multiplayer.data.last_timestamp != 0){
                var d = new Date();
                var g = d.getTime();
                multiplayer.data.request_time = parseFloat(g);
                jQuery.ajax({
                    url: root_dir+'/server.php?lobby_id='+lobby_id+'&timestamp='+multiplayer.data.last_timestamp,
                    type: "GET",
                    success: function (data) {
                        var d = new Date();
                        var g = d.getTime();
                        var delay = parseFloat(g)-multiplayer.data.request_time;
                        multiplayer.data.ping = parseInt(multiplayer.data.ping/2 + delay/2);
                        //console.log("Ping: "+multiplayer.data.ping);
                        if(data) multiplayer.parse_data(data);
                        if(document.querySelector("a-scene").getAttribute("nodes-state") == "autorun"){
                            console.log("Connection established");
                            document.querySelector("a-scene").setAttribute("nodes-state", "execute");
                            document.querySelector("a-scene").classList.remove("loading");
                        }else if(document.querySelector("a-scene").getAttribute("nodes-state") == "loading"){
                            document.querySelector("a-scene").setAttribute("nodes-state", "connected");
                            console.log("Connection established");
                        }
                        return multiplayer.download_data(lobby_id);
                    }
                });
            }
            if(multiplayer.data.last_timestamp == -1){
                multiplayer.data.last_timestamp = 0;
            }
        }catch(e){}
    }

    upload_data(lobby_id, value, rotation){
        if(multiplayer.data.last_timestamp != 0){
            try{
                jQuery.ajax({
                    url: '/server.php?lobby_id='+lobby_id+'&timestamp='+multiplayer.data.last_timestamp,
                    data: { "position": value.x+" "+value.y+" "+value.z, "rotation": rotation.x+" "+rotation.y+" "+rotation.z},
                    type: "POST",
                    success: function (data) { 
                        if(data.substr(0, 5) != "Error"){
                            multiplayer.data.last_timestamp = data;
                        }else{
                            alert(data);
                        }
                    }
                });
            }catch(e){
                console.log(e);
                console.log("upload_data error");
            }
        }
    }

    fuse(event, el){
        return eval('try{ multiplayer.'+event+'("'+el.id+'"); }catch(e){ console.log(e); }');
    }

    zooomScene(e) {
        /*
        e = e || window.event;
        var delta = e.deltaY || e.detail || e.wheelDelta;
        var y = 0;
        if(delta > 0 && multiplayer.data.zoom < 20){ 
            y=1;
            multiplayer.data.zoom++;
        }else if(multiplayer.data.zoom > 0){ 
            y=-1;
            multiplayer.data.zoom--;
        }
        var position = document.getElementById("camera").getAttribute("position");
        position.y += y;
        document.getElementById("camera").setAttribute("position", position);
        e.preventDefault ? e.preventDefault() : (e.returnValue = false);
        */
    }
  
}

var multiplayer = new nodes_multiplayer();
