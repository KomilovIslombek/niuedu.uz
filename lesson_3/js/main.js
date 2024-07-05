let elInput = document.querySelector("input");
let elForm = document.querySelector("form");
let elIcon = document.querySelector("i");


elForm.addEventListener("submit", e => {
    e.preventDefault();
    randomPas(8)
});

randomPas(12);

function randomPas(length) {
    let randomPass = Math.random().toString(36).slice(-length);
    elInput.value = randomPass;
    console.log(randomPass.length);
}

elIcon.addEventListener("click", () => {
    var copyText = document.querySelector("input");
    copyText.select();
    copyText.focus();
    document.execCommand("copy");  
});