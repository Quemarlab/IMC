const form = document.querySelector(".project"),
continueBtn = form.querySelector(".button"),
errorText = document.querySelector(".error-text");
const dataBox = document.querySelector('.databox');

form.onsubmit = (e)=>{
  e.preventDefault();
}

continueBtn.onclick = ()=>{
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "../ajax/php/project", true);
  xhr.onload = ()=>{
    if (xhr.readyState === XMLHttpRequest.DONE){
      if (xhr.status === 200) {
        let data = xhr.response;
        if (data.includes('success')) {
            errorText.style.display = "block";
            errorText.innerHTML = data;
            form.reset();
            getProject();
        }
        else{
          errorText.style.display = "block";
          errorText.innerHTML = data;
        }
      }
      else {
        console.log("Error occured" + data);
      }
    }
  }
  let formData = new FormData(form);
  xhr.send(formData);
}



function getProject() {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../ajax/php/project", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("action=getProject");

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
  getProject();
});


$(document).ready(function() {
  $(dataBox).on('click', '.delete', function(event) {
      event.preventDefault();
      var code = $(this).attr('value');
      if (confirm('Are you sure you want to delete this project?')) {
          $.ajax({
              type: 'POST',
              url: '../ajax/php/project',
              data: {'code': code, 'role': 'delete'},
              success: function(data) {
                  window.alert('project deleted successfully!');
                  getnews();
              }
          });
      }
  });
});