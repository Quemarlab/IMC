"use strict";

// DropzoneJS
if (window.Dropzone) {
  Dropzone.autoDiscover = false;
}

var dropzone = new Dropzone("#mydropzone", {
  url: "../ajax/php/gallery.php",
  addRemoveLinks: true,
});

var minSteps = 6,
  maxSteps = 60,
  timeBetweenSteps = 100,
  bytesPerStep = 100000;


document.querySelector('.upload').addEventListener('click', function (e) {
  e.preventDefault();
  dropzone.processQueue(); // Manually trigger file upload
});

dropzone.uploadFiles = function (files) {
  var self = this;
  var totalSteps;
  for (var i = 0; i < files.length; i++) {

    var file = files[i];
    totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

    for (var step = 0; step < totalSteps; step++) {
      var duration = timeBetweenSteps * (step + 1);
      setTimeout(function (file, totalSteps, step) {
        return function () {
          file.upload = {
            progress: 100 * (step + 1) / totalSteps,
            total: file.size,
            bytesSent: (step + 1) * file.size / totalSteps
          };

          self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
          if (file.upload.progress == 100) {
            file.status = Dropzone.SUCCESS;
            self.emit("success", file, 'success', null);
            self.emit("complete", file);
            self.processQueue();
          }
        };
      }(file, totalSteps, step), duration);
    }
  }
}

// Handling file removal
dropzone.on("removedfile", function (file) {
  console.log("File removed: " + file.name);
});