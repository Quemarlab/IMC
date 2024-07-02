const form = document.querySelector('.forgotPassword');
resetBtn = form.querySelector('.clickBtn');
const feedback = document.querySelector('.error-text');
const loaderIcon = document.querySelector('.loader-icon');

form.onsubmit = (e) => {
    e.preventDefault();
}

resetBtn.onclick = () => {
    loaderIcon.style.display = ''; 
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../ajax/php/forgotPassword', true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response;
                feedback.style.display = 'block';
                feedback.innerHTML = data;
                loaderIcon.style.display = 'none'; 
            } else {
                feedback.innerHTML = 'Error';
            }
        }
    }
    xhr.send(new FormData(form));
};