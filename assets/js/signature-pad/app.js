const wrapper = document.getElementById("signature-pad");
const clearButton = wrapper.querySelector("[data-action=clear]");
const changeColorButton = wrapper.querySelector("[data-action=change-color]");
const undoButton = wrapper.querySelector("[data-action=undo]");
const savePNGButton = wrapper.querySelector("[data-action=save-png]");
const saveJPGButton = wrapper.querySelector("[data-action=save-jpg]");
const saveSVGButton = wrapper.querySelector("[data-action=save-svg]");
const canvas = wrapper.querySelector("canvas");
const signaturePad = new SignaturePad(canvas, {
  // It's Necessary to use an opaque color when saving image as JPEG;
  // this option can be omitted if only saving as PNG or SVG
  backgroundColor: 'rgb(255, 255, 255)'
});

// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
  // When zoomed out to less than 100%, for some very strange reason,
  // some browsers report devicePixelRatio as less than 1
  // and only part of the canvas is cleared then.
  const ratio =  Math.max(window.devicePixelRatio || 1, 1);

  // This part causes the canvas to be cleared
  canvas.width = canvas.offsetWidth * ratio;
  canvas.height = canvas.offsetHeight * ratio;
  // canvas.width = canvas.offsetWidth * ratio;
  // canvas.height = canvas.offsetHeight * ratio;
  canvas.getContext("2d").scale(ratio, ratio);

  // This library does not listen for canvas changes, so after the canvas is automatically
  // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
  // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
  // that the state of this library is consistent with visual state of the canvas, you
  // have to clear it manually.
  //signaturePad.clear();
  
  // If you want to keep the drawing on resize instead of clearing it you can reset the data.
  signaturePad.fromData(signaturePad.toData());
}

// On mobile devices it might make more sense to listen to orientation change,
// rather than window resize events.
window.onresize = resizeCanvas;
resizeCanvas();

function download(dataURL, filename) {
  const blob = dataURLToBlob(dataURL);
  const url = window.URL.createObjectURL(blob);

  const a = document.createElement("a");
  a.style = "display: none";
  a.href = url;
  a.download = filename;

  document.body.appendChild(a);
  a.click();

  window.URL.revokeObjectURL(url);
}

function getDataUrl() {
  if (signaturePad.isEmpty()) {
    alert("Please provide a signature first.");
    return false;
  } else {
    return signaturePad.toDataURL('image/svg+xml');
  }
}

// One could simply use Canvas#toBlob method instead, but it's just to show
// that it can be done using result of SignaturePad#toDataURL.
function dataURLToBlob(dataURL) {
  const parts = dataURL.split(';base64,');
  const contentType = parts[0].split(":")[1];
  const raw = window.atob(parts[1]);
  const rawLength = raw.length;
  const uInt8Array = new Uint8Array(rawLength);

  for (let i = 0; i < rawLength; ++i) {
    uInt8Array[i] = raw.charCodeAt(i);
  }

  return new Blob([uInt8Array], { type: contentType });
}

clearButton.addEventListener("click", () => {
  signaturePad.clear();
});

undoButton.addEventListener("click", () => {
  const data = signaturePad.toData();

  if (data) {
    data.pop(); // remove the last dot or line
    signaturePad.fromData(data);
  }
});

// changeColorButton.addEventListener("click", () => {
//   const r = Math.round(Math.random() * 255);
//   const g = Math.round(Math.random() * 255);
//   const b = Math.round(Math.random() * 255);
//   const color = "rgb(" + r + "," + g + "," + b +")";
//   signaturePad.penColor = color;
// });
// savePNGButton.addEventListener("click", () => {
//   if (signaturePad.isEmpty()) {
//     alert("Please provide a signature first.");
//   } else {
//     const dataURL = signaturePad.toDataURL();
//     download(dataURL, "signature.png");
//   }
// });
// saveJPGButton.addEventListener("click", () => {
//   if (signaturePad.isEmpty()) {
//     alert("Please provide a signature first.");
//   } else {
//     const dataURL = signaturePad.toDataURL("image/jpeg");
//     download(dataURL, "signature.jpg");
//   }
// });
// saveSVGButton.addEventListener("click", () => {
//   if (signaturePad.isEmpty()) {
//     alert("Please provide a signature first.");
//   } else {
//     const dataURL = signaturePad.toDataURL('image/svg+xml');
//     download(dataURL, "signature.svg");
//   }
// });


function removeBlanks(canvas) {
  let croppedCanvas = document.createElement('canvas');
  let croppedCtx = croppedCanvas.getContext("2d");
  croppedCtx.fillStyle = "rgba(0, 0, 200, 0.5)";

  croppedCtx.fillRect(50,50,500,500);
  croppedCanvas.width = canvas.width;
  croppedCanvas.height = canvas.height;
  croppedCtx.drawImage(canvas, 0, 0);

  let w = croppedCanvas.width;
  let h = croppedCanvas.height;
  let pix = { x: [], y: [] };
  let imageData = croppedCtx.getImageData(0, 0, w, h);
  let index = 0;

  for (let y = 0; y < h; y++) {
    for (let x = 0; x < w; x++) {
      index = (y * w + x) * 4;
      // if (imageData.data[index + 3] > 0) {
      if (imageData.data[index] === 255 
        && imageData.data[index + 1] === 255 
        && imageData.data[index + 2] === 255 
        && imageData.data[index + 3] === 255 
        ) {
        continue;
      }
      if (imageData.data[index + 3] > 0) {
        pix.x.push(x);
        pix.y.push(y);
      }
    }
  }

  pix.x.sort((a, b) => a - b);
  pix.y.sort((a, b) => a - b);
  let n = pix.x.length - 1;

  w = pix.x[n] - pix.x[0];
  h = pix.y[n] - pix.y[0];
  var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

  croppedCanvas.width = w + 40; // extra width
  croppedCanvas.height = h + 40; // extra height
  croppedCtx.putImageData(cut, 20, 20); // extra height/width

  return croppedCanvas.toDataURL();
}

SignaturePad.prototype.removeBlanks = function () {
  var imgWidth = this._ctx.canvas.width;
  var imgHeight = this._ctx.canvas.height;
  var imageData = this._ctx.getImageData(0, 0, imgWidth, imgHeight),
  data = imageData.data,
  getAlpha = function(x, y) {
      return data[(imgWidth*y + x) * 4 + 3]
  },
  scanY = function (fromTop) {
      var offset = fromTop ? 1 : -1;

      // loop through each row
      for(var y = fromTop ? 0 : imgHeight - 1; fromTop ? (y < imgHeight) : (y > -1); y += offset) {

          // loop through each column
          for(var x = 0; x < imgWidth; x++) {
              if (getAlpha(x, y)) {
                  return y;                        
              }      
          }
      }
      return null; // all image is white
  },
  scanX = function (fromLeft) {
      var offset = fromLeft? 1 : -1;

      // loop through each column
      for(var x = fromLeft ? 0 : imgWidth - 1; fromLeft ? (x < imgWidth) : (x > -1); x += offset) {

          // loop through each row
          for(var y = 0; y < imgHeight; y++) {
              if (getAlpha(x, y)) {
                  return x;                        
              }      
          }
      }
      return null; // all image is white
  };

  var cropTop = scanY(true),
  cropBottom = scanY(false),
  cropLeft = scanX(true),
  cropRight = scanX(false);

  var relevantData = this._ctx.getImageData(cropLeft, cropTop, cropRight-cropLeft, cropBottom-cropTop);
  this.canvas.width = cropRight-cropLeft;
  this.canvas.height = cropBottom-cropTop;
  this._ctx.clearRect(0, 0, cropRight-cropLeft, cropBottom-cropTop);
  this._ctx.putImageData(relevantData, 0, 0);

  return this.canvas.toDataURL();
};

function cropSignatureCanvas (canvas) {

  // First duplicate the canvas to not alter the original
  var croppedCanvas = document.createElement('canvas'),
      croppedCtx    = croppedCanvas.getContext('2d');

      croppedCanvas.width  = canvas.width;
      croppedCanvas.height = canvas.height;
      croppedCtx.drawImage(canvas, 0, 0);

  // Next do the actual cropping
  var w         = croppedCanvas.width,
      h         = croppedCanvas.height,
      pix       = {x:[], y:[]},
      imageData = croppedCtx.getImageData(0,0,croppedCanvas.width,croppedCanvas.height),
      x, y, index;

  for (y = 0; y < h; y++) {
      for (x = 0; x < w; x++) {
          index = (y * w + x) * 4;
          if (imageData.data[index+3] > 0) {
              pix.x.push(x);
              pix.y.push(y);

          }
      }
  }
  pix.x.sort(function(a,b){return a-b});
  pix.y.sort(function(a,b){return a-b});
  var n = pix.x.length-1;

  w = pix.x[n] - pix.x[0];
  h = pix.y[n] - pix.y[0];
  var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

  croppedCanvas.width = w;
  croppedCanvas.height = h;
  croppedCtx.putImageData(cut, 0, 0);

  return croppedCanvas.toDataURL('image/svg+xml');
}

