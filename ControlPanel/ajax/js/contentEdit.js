const form = document.querySelector(".contentEdit"),
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
  xhr.open("POST", "../ajax/php/contentEdit", true);
  xhr.onload = ()=>{
    if (xhr.readyState === XMLHttpRequest.DONE){
      if (xhr.status === 200) {
        let data = xhr.response;
        if (data.includes('successful')) {
            errorText.style.display = "block";
            errorText.innerHTML = data;
            loaderIcon.style.display = 'none';
            form.reset();
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
