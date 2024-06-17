const form = document.querySelector(".news"),
continueBtn = form.querySelector(".button"),
errorText = document.querySelector(".error-text");
const dataBox = document.querySelector('.databox');
const loaderIcon = document.querySelector('.loader-icon');

form.onsubmit = (e)=>{
  e.preventDefault();
}

continueBtn.onclick = ()=>{
  loaderIcon.style.display='block';
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "../ajax/php/news.php", true);
  xhr.onload = ()=>{
    if (xhr.readyState === XMLHttpRequest.DONE){
      if (xhr.status === 200) {
        let data = xhr.response;
        if (data.includes('success')) {
            errorText.style.display = "block";
            errorText.innerHTML = data;
            loaderIcon.style.display = 'none';
            form.reset();
            getnews();
        }
        else{
          errorText.style.display = "block";
          errorText.innerHTML = data;
          loaderIcon.style.display = 'none';
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



function getnews() {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../ajax/php/news.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send("action=getNews");

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
  getnews();
});


function deletenews() {
  $(document).ready(function() {
    $('.delete ').on('click', function(event) {
      event.preventDefault();
      var code = $(this).attr('value');
      if (confirm('Are you sure you want to delete this news?')) {
          $.ajax({
              type: 'POST',
              url: '../ajax/php/news.php',
              data: {'code': code, 'role': 'delete'},
              success: function(data) {
                  window.alert('news deleted successfully!');
                  getnews();
              }
          });
      }
    });
  });
}