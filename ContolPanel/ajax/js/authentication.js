const form = document.querySelector(".authentication"),
continueBtn = form.querySelector(".button"),
errorText = document.querySelector(".error-text"),
loaderIcon = document.querySelector('.loader-icon');

form.onsubmit = (e)=>{
  e.preventDefault();
}

continueBtn.onclick = ()=>{
  loaderIcon.style.display='block';
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "../ajax/php/authentication.php", true);
  xhr.onload = ()=>{
    if (xhr.readyState === XMLHttpRequest.DONE){
      if (xhr.status === 200) {
        let data = xhr.response;
        if (data.includes('success')) {
          location.href = "../manage/dashboard";
        }
        else{
          errorText.style.display = "block";
          errorText.textContent = data;
          continueBtn.innerHTML = "Login again";
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