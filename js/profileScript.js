function loadElements(){

    const nameInput = document.getElementById("name");
    const surnameInput = document.getElementById("surname");
    const emailInput = document.getElementById("email");

    fetch('/mtesser/api/userDataGetter.php')
        .then(response => response.json())
        .then(json => {

            nameInput.value = json.name;
            surnameInput.value = json.surname;
            emailInput.value = json.email;
        });
}

function changeValues(){

    fetch('/mtesser/api/changeData.php',{
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            name: document.getElementById("name").value,
            surname: document.getElementById("surname").value,
            email: document.getElementById("email").value,
            password: document.getElementById("password").value
        })
    })
        .then(response => response.json())
        .then(json => {

            if(!json.ok){

                document.getElementById("error").classList.remove("nascosto");
            }else{

                document.getElementById("error").classList.add("nascosto");
            }
            loadElements();
        })
}

document.addEventListener('DOMContentLoaded', function(event) {

    loadElements();
});