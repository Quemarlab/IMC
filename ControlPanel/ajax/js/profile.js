const form = document.querySelector('.userInfo');
saveUBtn = form.querySelector('.saveUser');

const form2 = document.querySelector('.userPass');
savePBtn = form2.querySelector('.savePass');

const feedback = document.querySelector('.error-text');
const loaderIcon = document.querySelector('.loader-icon');

form.onsubmit = (e) => {
    e.preventDefault();
}

form2.onsubmit = (e) => {
    e.preventDefault();
}

saveUBtn.onclick = () => {
    loaderIcon.style.display = ''; 
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../ajax/php/profile.php?role=saveUser', true);

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

savePBtn.onclick = () => {
    loaderIcon.style.display = ''; 
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../ajax/php/profile.php?action=savePass', true);

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
    xhr.send(new FormData(form2));
};