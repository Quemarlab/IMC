errorText = document.querySelector(".error-text");
const dataBox = document.querySelector('.databox');


function getgallery() {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../ajax/php/gallery", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("action=getGallery");

  xhr.onload = function() {
    if (xhr.status === 200) {
      const data = xhr.responseText;
      dataBox.innerHTML = data;
    } else {
      console.error("Error: " + xhr.status);
    }
  };
}

document.addEventListener("DOMContentLoaded", function() {
  getgallery();
});


$(document).ready(function() {
  $(dataBox).on('click', '.delete', function(event) {
      event.preventDefault();
      var code = $(this).attr('value');
      if (confirm('Are you sure you want to delete this gallery?')) {
          $.ajax({
              type: 'POST',
              url: '../ajax/php/gallery',
              data: {'code': code, 'role': 'delete'},
              success: function(data) {
                  window.alert('gallery deleted successfully!');
                  getnews();
              }
          });
      }
  });
});