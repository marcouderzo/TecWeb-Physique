function loadElements(){

    const select = document.getElementById("userToPromote");
    const alimentationSelect = document.getElementById("recipe");
    const newsSelect = document.getElementById("news");
    const newsTypesSelect = document.getElementById("type");
    const textArea = document.getElementById("postText");
    const postSelect = document.getElementById("post");
    const banSelect = document.getElementById("notBanned");
    const unbanSelect = document.getElementById("banned");
    const postSelect1 = document.getElementById("post1");
    const postTextArea1 = document.getElementById("postText1");
    const deleteUserSelect = document.getElementById("remove");

    select.innerHTML = "";
    alimentationSelect.innerHTML = "";
    newsSelect.innerHTML = "";
    postSelect.innerHTML = "";
    textArea.innerHTML = "";
    newsTypesSelect.innerHTML = "";
    banSelect.innerHTML = "";
    unbanSelect.innerHTML = "";
    postSelect1.innerHTML = "";
    postTextArea1.innerHTML = "";
    deleteUserSelect.innerHTML = "";

    fetch('/mtesser/api/userGetter.php')
        .then(response => response.json())
        .then(json => {

            const users = json.result;
            onlyBanned = onlyBannedUsers(users, json.current);
            noBanned = noUserBanned(users, json.current);

            users.forEach((element) => {

                let option = document.createElement("option");
                let option2 = document.createElement("option");
                let option3 = document.createElement("option");
                option.setAttribute("value", element.username);
                option2.setAttribute("value", element.username);
                option3.setAttribute("value", element.username);
                option.setAttribute("class", "userSelection");
                option2.setAttribute("class", "userSelection");
                option3.setAttribute("class", "userSelection");
                option.innerHTML = element.username;
                option2.innerHTML = element.username;
                option3.innerHTML = element.username;

                if(element.username != json.current){

                    document.getElementById("remove").appendChild(option3);
                }


                if(!element.admin){

                    select.appendChild(option);
                }

                if(!element.banned){
                    if(element.username != json.current){

                        banSelect.appendChild(option2);
                    }
                }else{
                   if(element.username != json.current){

                       unbanSelect.appendChild(option2);
                    }
                }
            });

            if(onlyBanned){

                const option = document.createElement("option");
                option.setAttribute("value", "-1");
                option.innerHTML = "Non ci sono utenti non bannati";

                banSelect.appendChild(option);
            }

            if(noBanned){

                const option = document.createElement("option");
                option.setAttribute("value", "-1");
                option.innerHTML = "Non ci sono utenti bannati";

                unbanSelect.appendChild(option);
            }
        });

    fetch('/mtesser/api/recipeGetter.php')
        .then(response => response.json())
        .then(json => {

            const recipes = json.result;

            recipes.forEach((element) => {

                let option = document.createElement("option");
                option.setAttribute("value", element.id);
                option.innerHTML = element.name;
                alimentationSelect.appendChild(option)
            })

        });

    fetch('/mtesser/api/newsGetter.php')
        .then(response => response.json())
        .then(json => {

            const news = json.result;

            news.forEach((element) => {

                let option = document.createElement("option");
                option.setAttribute("value", element.id);
                option.innerHTML = element.title;
                newsSelect.appendChild(option)
            })

        });

    fetch('/mtesser/api/commentGetter.php')
        .then(response => response.json())
        .then(json => {

            const posts = json.result;

            posts.forEach((element) => {

                const option = document.createElement("option");
                const option2 = document.createElement("option");
                option.setAttribute("value", element.id);
                option2.setAttribute("value", element.id);
                const firstPart = element.text.slice(0, 20);
                option.innerHTML = (element.id + " - " + firstPart + "...");
                option2.innerHTML = (element.id + " - " + firstPart + "...");
                postSelect.appendChild(option);
                postSelect1.appendChild(option2);
            });

            const text = posts[0].text;
            textArea.innerHTML = text;
            postTextArea1.innerHTML = text;
            findAnswers(posts[0].id);

        });

    fetch('/mtesser/api/newsTypesGetter.php')
        .then(response => response.json())
        .then(json => {

            const types = json.result;

            types.forEach((element) => {

                const option = document.createElement("option");
                option.setAttribute("value", element);
                option.innerHTML = element;
                newsTypesSelect.appendChild(option);
            });
        });
}

function onlyBannedUsers(users, current){

    let onlyBanned = true;
    users.forEach((user) => {

        if(user.username != current){

            if(!user.banned){

                onlyBanned = false;
            }
        }
    });

    return onlyBanned;
}

function noUserBanned(users, current){

    let noBanned = true;

    users.forEach((user) => {

        if(user.username != current) {

            if (user.banned) {

                noBanned = false;
            }
        }
    });
    return noBanned;
}


function promote(){

    fetch('/mtesser/api/promoteUser.php',{
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({user: document.getElementById("userToPromote").value})
    })
        .then(response => response.json())
        .then(json => {

            if(!json.ok){

                const el = document.getElementById("errorMessage").classList.remove("nascosto");
            }
            loadElements();
        });
    clearErrors();
}


function deleteRecipe(){

    fetch('/mtesser/api/deleteRecipe.php',{
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify(
            {recipe: document.getElementById("recipe").value})
    })
        .then(response=> response.json())
        .then(json => {

            if(!json.ok){

                const el = document.getElementById("deleteRecipeError");
                el.classList.remove("nascosto");
            }
            loadElements();
        })
    clearErrors();
}


function deleteNews(){

    fetch('/mtesser/api/deleteNews.php',{
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify(
            {id: document.getElementById("news").value})
    })
        .then(response=> response.json())
        .then(json => {

            if(!json.ok){

                const el = document.getElementById("deleteNewsError");
                el.classList.remove("nascosto");
            }
            loadElements();
        })
    clearErrors();
}


function sendNews(){

    fetch('/mtesser/api/newsSender.php',{
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({

            type: document.getElementById("type").value,
            title: document.getElementById("newsTitle").value,
            text: document.getElementById("newsText").value,
            link: document.getElementById("newsLink").value
        })
    })
        .then(response => response.json())
        .then(json => {

            window.location.replace(json.red);
        })
}


function findText(){

    const postId = document.getElementById("post").value;
    const textArea = document.getElementById("postText");

    fetch('/mtesser/api/textFinder.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            id: postId
        })
    })
        .then(response => response.json())
        .then(json => {

            textArea.innerHTML = json.result;
        });
}

function deletePost(){

    const postId = document.getElementById("post").value;
    document.getElementById("postText").innerHTML = "";

    fetch('/mtesser/api/deletePost.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            id: postId
        })
    })
        .then(response => response.json())
        .then(json => {

            const el = document.getElementById("deletePostError");
            if(!json.ok){

                el.classList.remove("nascosto");
            }else{
                el.classList.add("nascosto");
                loadElements();

            }
        });
    clearErrors();
}

function sendRecipe(){

    console.log()

    fetch('/mtesser/api/recipeSender.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({

            name: document.getElementById("recipeTitle").value,
            description: document.getElementById("recipeDescription").value,
            link: document.getElementById("recipeImage").value,
            imageName: document.getElementById("recipeImage").value,
            alt: document.getElementById("recipeAlt").value,
            ingredients: document.getElementById("recipeIngredients").value,
            method: document.getElementById("recipeMethod").value,
            hints: document.getElementById("recipeHint").value,
            people: document.getElementById("recipePeople").value
        })
    })
        .then(response => response.json())
        .then(json => {

            window.location.replace(json.red);
        })
}

function banUser(){

    const userToBan = document.getElementById("notBanned").value;

    fetch('/mtesser/api/banUser.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            user: userToBan
        })
    })
        .then(response => response.json())
        .then(json => {

            if(!json.ok){

                const el = document.getElementById("errorBanMessage").classList.remove("nascosto");
            }
            loadElements();
        });
    clearErrors();
}

function unbanUser(){

    const userToUnban = document.getElementById("banned").value;

    fetch('/mtesser/api/unbanUser.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            user: userToUnban
        })
    })
            .then(response => response.json())
            .then(json => {

                if(!json.ok){

                    const el = document.getElementById("errorUnbanMessage").classList.remove("nascosto");
                }
                loadElements();
            });
    clearErrors();
}


function findAnswers(id){

    const answerSelect = document.getElementById("answer");
    answerSelect.innerHTML = "";

    fetch("/mtesser/api/answerGetter.php?IDPost=" + id)
        .then(response => response.json())
        .then(json => {

            const answers = json.result;
            const textArea = document.getElementById("answerText1");

            if(answers.length > 0){

                const answerSelect = document.getElementById("answer");
                answers.forEach((element) => {

                    const option = document.createElement("option");
                    option.setAttribute("value", element.answerId);
                    const string = element.answerId + " - " + element.Text.slice(0, 20);
                    option.innerHTML = string;

                    answerSelect.appendChild(option);
                });

                textArea.innerHTML = answers[0].Text;
            }
            else{

                textArea.innerHTML = "Non ci sono risposte a questo post";
            }

        });
}


function findAnswerText(){

    const id = document.getElementById("post1").value;
    const answerId = document.getElementById("answer").value;
    const textArea = document.getElementById("answerText1");

    console.log(answerId);

    fetch('/mtesser/api/answerGetter.php?IDPost=' + id)
        .then(response => response.json())
        .then(json => {

            let answers = json.result;
            let answer;

            if(answers.length > 0){
                answers.forEach((element) => {

                    if(element.answerId == answerId){
                        console.log(element);
                        answer = element.Text;
                    }
                })
                console.log(answer);
                textArea.innerHTML = answer;
            }else{

                textArea.innerHTML = "Non ci sono risposte a questo post";
            }

        });
}


function findTextAndAnswer(){

    const idPost = document.getElementById("post1").value;
    const textArea = document.getElementById("postText1");


    fetch('/mtesser/api/commentsGetter.php')
        .then(response => response.json())
        .then(json => {

            const result = json.result;
            result.forEach((element) => {

                if(element.IDPost == idPost){

                    textArea.innerHTML = element.Testo;
                }
            });

            findAnswers(idPost);
        });
}


function deleteAnswer(){

    const idAnswer = document.getElementById("answer").value;

    fetch('/mtesser/api/deleteAnswer.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            answerId: idAnswer
        })
    })
        .then(response => response.json())
        .then(json => {

            json.ok = false;
            if(!json.ok){

                document.getElementById("deleteAnswerError").classList.remove("nascosto");
            }else{

               document.getElementById("deleteAnswerError").classList.add("nascosto");
            }
            loadElements();
        });
    clearErrors();
}



function deleteAccount(){

    fetch('/mtesser/api/deleteAccount.php',{
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            user: document.getElementById("remove").value
        })
    })
        .then(response => response.json())
        .then(json => {

            if(!json.ok){

                document.getElementById("errorRemoveMessage").classList.remove("nascosto");
            }else{

                document.getElementById("errorRemoveMessage").classList.add("nascosto");
            }
            loadElements();

        });
    clearErrors();
}


function clearErrors(){

    if(document.getElementById("typeError")){

        document.getElementById("typeError").remove();
    }

    if(document.getElementById("newsTitleError")){

        document.getElementById("newsTitleError").remove();
    }

    if(document.getElementById("newsTextError")){

        document.getElementById("newsTextError").remove();
    }

    if(document.getElementById("newsLinkError")){

        document.getElementById("newsLinkError").remove();
    }

    if(document.getElementById("recipeNameError")){

        document.getElementById("recipeNameError").remove();
    }

    if(document.getElementById("recipeDescriptionError")){

        document.getElementById("recipeDescriptionError").remove();
    }

    if(document.getElementById("recipeImageError")){

        document.getElementById("recipeImageError").remove();
    }

    if(document.getElementById("recipeIngredientsError")){

        document.getElementById("recipeIngredientsError").remove();
    }

    if(document.getElementById("recipeMethodError")){

        document.getElementById("recipeMethodError").remove();
    }

    if(document.getElementById("recipePeopleError")){

        document.getElementById("recipePeopleError").remove();
    }
}

document.addEventListener('DOMContentLoaded', function(event) {

    loadElements();
});