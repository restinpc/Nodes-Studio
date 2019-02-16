/**
* JavaScript worker equirectangular-to-cubemap processor source file.
* Do not edit directly.
* @path /script/pano2cube.source.js
*/

const canvas = document.createElement('canvas');
const ctx = canvas.getContext('2d');
var t = 0;
class RadioInput {
  constructor(name, onChange) {
    this.inputs = document.querySelectorAll(`input[name=${name}]`);
    for (let input of this.inputs) {
      input.addEventListener('change', onChange);
    }
  }

  get value() {
    for (let input of this.inputs) {
      if (input.checked) {
        return input.value;
      }
    }
  }
}
 
class Input {
  constructor(id, onChange) {
    this.input = document.getElementById(id);
    try{
        this.input.addEventListener('change', onChange);
    }catch(e){}
    this.valueAttrib = this.input.type === 'checkbox' ? 'checked' : 'value';
  }

  get value() {
    return this.input[this.valueAttrib];
  }
}

class CubeFace {
  constructor(faceName) {
    this.faceName = faceName;
    this.img = document.createElement('input');
    this.img.type = "hidden";
    this.img.name="img_"+faceName;

  }

  setPreview(url, x, y) {

  }

  setDownload(url, fileExtension) {
    var fd = new FormData();
    var name = this.faceName;
    fd.append('url', url);
    try{
        var url_data = root_dir+'/cubemap.php?name='+name+'&img_id='+parseInt($id("cubemap_id").value);
    }catch(e){
        var url_data = root_dir+'/cubemap.php?name='+name;
    }
    $.ajax({
        type: 'POST',
        url: url_data,
        data: fd,
        processData: false,
        contentType: false
    }).done(function(data) {
        t++;
        $id("generating").innerHTML = "Building cubemaps.. "+parseInt(t/6*100)+"%";
        if(t == 6){
            setTimeout(function(){ 
                try{
                    $id("next_pano").submit(); 
                }catch(e){};
                try{
                    $id("worker_wnd").style.display = "none";
                    $id("new_scene_details").style.display = "block";
                }catch(e){}
            }, 3000);
        }
    });
 }
}

function removeChildren(node) {
  while (node.firstChild) {
    node.removeChild(node.firstChild);
  }
}

const mimeType = {
  'jpg': 'image/jpeg',
  'png': 'image/png'
};

function getDataURL(imgData, extension) {
  canvas.width = imgData.width;
  canvas.height = imgData.height;
  ctx.putImageData(imgData, 0, 0);
  return new Promise(resolve => {
    //canvas.toBlob(blob => resolve(URL.createObjectURL(blob)), mimeType[extension], 0.92);
    resolve(canvas.toDataURL(mimeType[extension]));
  });
}

const dom = {
  imageInput: document.getElementById('imageInput'),
  faces: document.getElementById('faces'),
  generating: document.getElementById('generating')
};

try{
    dom.imageInput.addEventListener('change', loadImage);
}catch(e){}

const settings = {
  cubeRotation: new Input('cubeRotation', loadImage),
  interpolation: new RadioInput('interpolation', loadImage),
  format: new RadioInput('format', loadImage),
};

const facePositions = {
  pz: {x: 1, y: 1},
  nz: {x: 3, y: 1},
  px: {x: 2, y: 1},
  nx: {x: 0, y: 1},
  py: {x: 1, y: 0},
  ny: {x: 1, y: 2}
};

function loadImage() {
    const img = new Image();
    try{
        var file = dom.imageInput.files[0];
        if (!file) {
          return;
        }
        img.src = URL.createObjectURL(file);
    }catch(e){
        var file = $id("inputImage").src;
        if (!file) {
          return;
        }
        img.src = file;
        console.log(file);
    }
    img.addEventListener('load', () => {
      const {width, height} = img;
      canvas.width = width;
      canvas.height = height;
      ctx.drawImage(img, 0, 0);
      const data = ctx.getImageData(0, 0, width, height);
      processImage(data);
    });
}

let finished = 0;
let workers = [];

function processImage(data) {
  removeChildren(dom.faces);
  dom.generating.style.visibility = 'visible';

  for (let worker of workers) {
    worker.terminate();
  }

  for (let [faceName, position] of Object.entries(facePositions)) {
    renderFace(data, faceName, position);
  }
}

function renderFace(data, faceName, position) {
  const face = new CubeFace(faceName);
  dom.faces.appendChild(face.img);

  const options = {
    data: data,
    face: faceName,
    rotation: Math.PI * settings.cubeRotation.value / 180,
    interpolation: settings.interpolation.value,
  };

  const worker = new Worker(root_dir+'/script/convert.js');

  const setDownload = ({data: imageData}) => {
    const extension = settings.format.value;

    getDataURL(imageData, extension)
      .then(url => face.setDownload(url, extension));

    finished++;

    if (finished === 6) {
        finished = 0;
        workers = [];
        $id("generating").innerHTML = "Building cubemaps.. 0%";
    }
  };

  const setPreview = ({data: imageData}) => {
    const x = imageData.width * position.x;
    const y = imageData.height * position.y;

    getDataURL(imageData, 'jpg')
      .then(url => face.setPreview(url, x, y));

    worker.onmessage = setDownload;
    worker.postMessage(options);
  };

  worker.onmessage = setPreview;
  worker.postMessage(Object.assign({}, options, {
    maxWidth: 200,
    interpolation: 'linear',
  }));

  workers.push(worker);
}
