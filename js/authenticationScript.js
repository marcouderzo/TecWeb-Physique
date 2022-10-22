function verifylogin() {


    const username=  document.getElementById("usernameInputArea").value;
    const password = document.getElementById("passwordInputArea").value;

    const link = window.location.href;
    const parts = link.split("&");

    const possibleErrors = ['eusne', 'epane', 'eusnv', 'epanv', 'sqlit'];
    let actualErrors = [];

    parts.forEach((element) => {

        possibleErrors.forEach((error) => {

            if(element.includes(error)){

                actualErrors.push(error);
            }
        })
    })

    let ok = true;
    if(username == ""){

        if(actualErrors.indexOf('eusne') != -1 || actualErrors.indexOf('eusnv') != -1){

            const invalidUser = document.getElementById("invalidUser");
            const unknownUser = document.getElementById("unknownUser");

            if(unknownUser != null){

                unknownUser.remove();
                document.getElementById("erroreusername").classList.remove("nascosto");
            }

        }else{

            document.getElementById("erroreusername").classList.remove("nascosto");
        }

        ok = false;
    }
    if(password == ""){

        if(actualErrors.indexOf('epane') != -1 || actualErrors.indexOf('epanv') != -1){

            const invalidPass = document.getElementById("invalidPass");
            const wrongPass = document.getElementById("wrongPass");

            if(wrongPass != null){

                wrongPass.remove();
                document.getElementById("errorepassword").classList.remove("nascosto");
            }

        }else{

            document.getElementById("errorepassword").classList.remove("nascosto");
        }

        ok = false;
    }
    if(!ok && document.getElementById("sqlError") != null){
        document.getElementById("sqlError").remove();
    }
    if(!ok && document.getElementById("eusnv") != null && username != ""){
        document.getElementById("eusnv").remove();
    }
    if(!ok && document.getElementById("eusne") != null && username != ""){
        document.getElementById("eusne").remove();
    }
    if(!ok && document.getElementById("epanv") != null && password != ""){
        document.getElementById("epanv").remove();
    }
    if(!ok && document.getElementById("epane") != null && password != ""){
        document.getElementById("epane").remove();
    }

    return ok;
}


function verifysignup(){

    const username=  document.getElementById("username").value;
    const email = document.getElementById("mail").value;
    const name = document.getElementById("name").value;
    const surname = document.getElementById("surname").value;
    const password = document.getElementById("password").value;
    const rePassword = document.getElementById("rePassword").value;

    const link = window.location.href;
    const parts = link.split("&");


    const possibleErrors = ['edbfa','eusnv','euses','eemnv', 'eemes','enonv','econv', 'epanv','epanc'];
    let actualErrors = [];

    parts.forEach((element) => {

        possibleErrors.forEach((error) => {

            if(element.includes(error)){

                actualErrors.push(error);
            }
        })
    })

    console.log(actualErrors);

    let ok = true;
    if(username == ""){

        if(actualErrors.indexOf('eusnv') != -1 || actualErrors.indexOf('euses') != -1){

            const invalidUser = document.getElementById("eusnv");
            const knownUser = document.getElementById("euses");

            console.log(knownUser);

            if(knownUser != null){

                knownUser.remove();
                document.getElementById("erroreusername").classList.remove("nascosto");
            }

        }else{

            document.getElementById("erroreusername").classList.remove("nascosto");
        }
        ok = false;
    }
    if(email == ""){

        if(actualErrors.indexOf('eemnv') != -1 || actualErrors.indexOf('eemes') != -1){

            const invalidEmail = document.getElementById("eemnv");
            const knownEmail = document.getElementById("eemes");

            console.log(document.getElementById("emailnonvalida"));

            if(knownEmail != null){

                knownEmail.remove();
                document.getElementById("emailnonvalida").classList.remove("nascosto");
            }

        }else{

            document.getElementById("emailnonvalida").classList.remove("nascosto");
        }
        ok = false;
    }
    if(name == ""){

        if(actualErrors.indexOf('enonv') != -1){

            const invalidname = document.getElementById("enonv");
            

            if(invalidname != null){
                    
            }

        }else{

            document.getElementById("nomenonvalido").classList.remove("nascosto");
        }
        ok = false;
    }
    if(surname == ""){

        if(actualErrors.indexOf('econv') != -1){

            const invalidsurname = document.getElementById("econv");
            

            if(invalidsurname != null){
                    
            }

        }else{

            document.getElementById("cognomenonvalido").classList.remove("nascosto");
        }
        ok = false;
    }
    if(password == ""){

        if(actualErrors.indexOf('epanv') != -1 || actualErrors.indexOf('epanc') != -1){

            const invalidPassword = document.getElementById("epanv");
            const notCorrespondingPassword = document.getElementById("epanc");

            if(notCorrespondingPassword != null){

                notCorrespondingPassword.remove();
                document.getElementById("errorepassword").classList.remove("nascosto");
            }

        }else{

            document.getElementById("errorepassword").classList.remove("nascosto");
        }
        ok = false;
    }
    
    if(!ok && document.getElementById("edbfa") != null){
        document.getElementById("edbfa").remove();
    }    
    if(!ok && document.getElementById("eemnv") != null && email != ""){
        document.getElementById("eemnv").remove();
    }
    if(!ok && document.getElementById("eemes") != null && email != ""){
        document.getElementById("eemes").remove();
    }
    if(!ok && document.getElementById("eusnv") != null && username != ""){
        document.getElementById("eusnv").remove();
    }
    if(!ok && document.getElementById("euses") != null && username != ""){
        document.getElementById("euses").remove();
    }
    if(!ok && document.getElementById("enonv") != null && name != ""){
        document.getElementById("enonv").remove();
    }
    if(!ok && document.getElementById("econv") != null && surname != ""){
        document.getElementById("econv").remove();
    }
    if(!ok && document.getElementById("epanv") != null && password != ""){
        document.getElementById("epanv").remove();
    }
    if(!ok && document.getElementById("epanc") != null && password != ""){
        document.getElementById("epanc").remove();
    }
    

    return ok;    
}