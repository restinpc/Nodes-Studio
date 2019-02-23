/**
* A-Frame JavaScript visualizer scene source file.
* Do not edit directly.
* @path /script/aframe/visualizer.source.js
*
* @name    Nodes Studio    @version 3.0.0.2
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
var audioContext = null;
var rafID = null;
var analyser;
var audio;
var frequencyData;
var mediaStreamSource = null;
var fps = 0;
var last_timestamp = 0;
var color = new Array(128, 128, 128);
var radius = 0;
var audio_image_id = 1;
var opacity_interval = null;
var is_start = 0;

function rgbToHex(rgb) {
    var re = /^rgb\(.*\)$/;
    var bits;
    function z(n){return (n.length<2?'0':'') + n;}
    if (re.test(rgb)) {
      bits = rgb.match(/\d+/g);
      return '#' + z((+bits[0]).toString(16)) + 
                   z((+bits[1]).toString(16)) +
                   z((+bits[2]).toString(16));
    }
    return rgb;
}

function didntGetStream() {
    try{
        audio = new Audio(root_dir+'/res/sounds/dmt.mp3');
        audio.play();
    }catch(E){}
    try{
        analyser = audioContext.createAnalyser();
        analyser.smoothingTimeConstant = 0.1;
        analyser.fftSize = 1024;
        sourceNode = audioContext.createBufferSource();
        var mediaStreamSource = audioContext.createMediaElementSource(audio); 
        mediaStreamSource.connect(analyser);
        mediaStreamSource.connect(audioContext.destination);
        sourceNode.connect(audioContext.destination);
        frequencyData = new Uint8Array(analyser.frequencyBinCount);
        update();
    }catch(e){ alert("error");}

}

function gotStream(stream) {
    mediaStreamSource = audioContext.createMediaStreamSource(stream);
    analyser = audioContext.createAnalyser();
    analyser.fftSize = 128;
    mediaStreamSource.connect(analyser);
    frequencyData = new Uint8Array(analyser.frequencyBinCount);
    update();
}

function update( ) {
    analyser.getByteFrequencyData(frequencyData);
    rafID = window.requestAnimationFrame( update );
}

function change_image(){
    audio_image_id++;
    if(audio_image_id > 5) audio_image_id = 1;
    opacity_interval = setInterval(hide_sphere, 1);
}

function hide_sphere(){
    var opacity = parseFloat(document.getElementById("sphere").getAttribute("opacity"));
    if(opacity > 0){
        opacity -= 0.01;
        document.getElementById("sphere").setAttribute("opacity", opacity);
        document.getElementById("sky").setAttribute("opacity", opacity);
    }else{
        clearInterval(opacity_interval);
        document.getElementById("sphere").setAttribute('src', "#equirectangular_"+audio_image_id);
        document.getElementById("sky").setAttribute('src', "#equirectangular_"+audio_image_id);
        opacity_interval = setInterval(show_sphere, 1);
    }
}

function show_sphere(){
    var opacity = parseFloat(document.getElementById("sphere").getAttribute("opacity"));
    if(opacity < 1){
        opacity += 0.01;
        document.getElementById("sphere").setAttribute("opacity", opacity);
        document.getElementById("sky").setAttribute("opacity", opacity);
    }else{
        clearInterval(opacity_interval);
    }
}

function start_visualizer(){
    if(!is_start) is_start = 1;
    else return;
    $id('nodes_scene').enterVR();
    setInterval(change_image, 30000);
    window.AudioContext = window.AudioContext || window.webkitAudioContext;
    audioContext = new AudioContext();
    try {
        navigator.getUserMedia =
            navigator.getUserMedia ||
            navigator.webkitGetUserMedia ||
            navigator.mozGetUserMedia;
            navigator.getUserMedia(
            {
                audio: {
                  optional: [
                    {echoCancellation: false},
                    {mozAutoGainControl: false},
                    {mozNoiseSuppression: false},
                    {googEchoCancellation: false},
                    {googAutoGainControl: false},
                    {googNoiseSuppression: false},
                    {googHighpassFilter: false}
                  ]
                }
            }, gotStream, didntGetStream);
    } catch (e) {
        document.getElementById("dmt").play();
    }
}

delete AFRAME.components['nodes-camera'];
AFRAME.registerComponent("nodes-camera", {
    tick: function () {
    	try{
            for(var i = 0; i < 3; i++){
                if(color[i] < frequencyData[i] && color[i] < 245){
                    color[i]+=10;
                }else if(color[0] > frequencyData[i] && color[i] > 0){
                    color[i]-=1;
                }
            }
            var sum = color[0]+color[1]+color[2];
            var red_color = parseInt(color[0]/sum *450);
            var green_color = parseInt(color[1]/sum *450);
            var blue_color = parseInt(color[2]/sum *450);
            if(red_color > 255) red_color = 255;
            if(green_color > 255) green_color = 255;
            if(blue_color > 255) blue_color = 255;
            document.querySelector("#sky").setAttribute('color', rgbToHex('rgb('+red_color+','+green_color+','+blue_color+')'));
    	}catch(e){}
        if(last_timestamp == 0){
            last_timestamp = document.querySelector('a-scene').time;
        }else{
            fps = document.querySelector('a-scene').time - last_timestamp;
            last_timestamp = document.querySelector('a-scene').time;
        }
        try{
            radius = (radius*99+(frequencyData[128]/10))/100;
	    if(radius < 5) radius = 5;
            if(radius > 15) radius = 15;
            document.querySelector("#sphere").setAttribute('radius', radius);
        }catch(e){}
        
        var rotation = document.querySelector("#sky").getAttribute('rotation');
        var camera = document.querySelector("#camera").getAttribute('rotation');
        document.querySelector("#sphere").setAttribute('rotation', (rotation.x-camera.x)+" "+(rotation.y-camera.y)+" "+(rotation.z-camera.z));
    }
});