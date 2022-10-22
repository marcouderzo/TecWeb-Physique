function sendComment(){

    const val = document.getElementById("post0").value;
    document.getElementById("post0").value = "";

    fetch("/mtesser/api/commentSubmitter.php", {method: 'POST',
            credentials: 'same-origin',
            body: JSON.stringify({val: val, submit: true})})
        .then(response => response.json())
        .then(json => getComments())
        .catch((e) => console.log(e));

}

function getComments(){

    fetch("/mtesser/api/commentsGetter.php")
        .then(response => response.json())
        .then(json=> {

            if(json.ok){

                let res = json.result;
                let el = document.getElementById("commentsContainer");

                if(res.length == 0) {

                    el.remove();
                    let p = document.createElement("p");
                    p.innerHTML = "Non ci sono post";
                    document.getElementById("content").appendChild(p);
                    return;
                }

                el.replaceChildren();
                for(let commentIndex in res){

                    let dd = document.createElement("dd");
                    dd.classList.add("post");
                    dd.appendChild(

                        ((fieldset) => {

                            let form = document.createElement("form");
                            form.setAttribute("action", "#");
                            form.appendChild(fieldset);
                            return form;
                        })(
                            ((h3, label, textarea, button, risposta, button1, containerRisposte)=> {

                                let fieldset = document.createElement("fieldset");
                                fieldset.classList.add("forumFieldset");
                                fieldset.appendChild(h3);
                                fieldset.appendChild(label);
                                fieldset.appendChild(textarea);
                                fieldset.appendChild(button);
                                fieldset.appendChild(risposta);
                                fieldset.appendChild(button1);
                                fieldset.appendChild(containerRisposte);

                                return fieldset;
                            })(
                                (()=>{

                                    let h3 = document.createElement("h3");
                                    h3.innerText = res[commentIndex].IDUtente;
                                    return h3;
                                })(),
                                (()=>{

                                    let label = document.createElement("label");
                                    label.setAttribute("for", "post" + res[commentIndex].IDPost);
                                    label.innerText = "ha postato:";
                                    return label;

                                })(),
                                (()=>{

                                    let textarea = document.createElement("textarea");
                                    textarea.setAttribute("id", "post" + res[commentIndex].IDPost);
                                    textarea.classList.add("text");
                                    textarea.classList.add("forumTextarea");
                                    textarea.setAttribute("rows", "10");
                                    textarea.setAttribute("name", "post" + res[commentIndex].IDPost);
                                    textarea.setAttribute("readonly", "readonly");
                                    textarea.innerText = res[commentIndex].Testo;

                                    return textarea;

                                })(),
                                ((img, span)=>{

                                    let button = document.createElement("button");
                                    button.classList.add("miPiaceButton");
                                    button.setAttribute("type", "button");
                                    button.setAttribute("name", "miPiace");
                                    button.onclick = ()=>{
                                        if(json.banned){
                                            return;
                                        }
                                        fetch("/mtesser/api/leaveLike.php", {method: 'POST',
                                            credentials: 'same-origin',
                                            body: JSON.stringify({leavingLike: !res[commentIndex].LeftLike, idPost: res[commentIndex].IDPost})
                                        })
                                            .then(response=> response.json())
                                            .then(json=> getComments())
                                    };
                                    button.appendChild(img);
                                    button.appendChild(span);

                                    return button;
                                })(

                                    (()=>{

                                        let img = document.createElement("img");
                                        img.classList.add("likeBicipite");
                                        img.setAttribute("src", !res[commentIndex].LeftLike ? "images/WhiteBicio.png" : "images/RedBicio.png");

                                        return img;

                                    })(),
                                    (()=>{

                                        let span = document.createElement("span");
                                        span.classList.add("likeCount");
                                        span.innerText = res[commentIndex].NumeroLike;

                                        return span;
                                    })()
                                ),
                                (()=> {

                                    let textarea = document.createElement("textarea");
                                    textarea.setAttribute("id", "answer_post" + res[commentIndex].IDPost);
                                    textarea.classList.add("text");
                                    textarea.classList.add("answerPostTextarea")
                                    textarea.setAttribute("rows", "3");
                                    textarea.setAttribute("name", "answer_post" + res[commentIndex].IDPost);
                                    textarea.classList.add("nascosto");

                                    return textarea;

                                })(),
                                (()=>{

                                    let button2 = document.createElement("button");
                                    button2.classList.add("leaveAnswer");
                                    button2.setAttribute("type", "button");
                                    button2.setAttribute("name", "UserAnswer");
                                    button2.onclick = (btn2 = button2)=>{
                                        button2.classList.add("confirmLeaveAnswer");
                                        document.getElementById("answer_post" + res[commentIndex].IDPost).classList.remove("nascosto");
                                        document.getElementById("answer_post" + res[commentIndex].IDPost).classList.add("block");
                                        let el = document.getElementsByClassName("leaveAnswer")[commentIndex];
                                        el.innerText = "Invia Risposta";

                                        el.onclick = ()=>{

                                            fetch("/mtesser/api/answerSender.php",{method: 'POST',
                                                credentials: 'same-origin',
                                                body: JSON.stringify({val: document.getElementById("answer_post" + res[commentIndex].IDPost).value, idPost : res[commentIndex].IDPost})})
                                                .then(response=> response.json())
                                                .then(json=> {
                                                    document.getElementById("answer_post" + res[commentIndex].IDPost).value = "";
                                                    getComments();
                                                })
                                        };
                                    };
                                    button2.innerText = "Aggiungi una risposta";
                                    button2.classList.remove("confirmLeaveAnswer");
                                    if(!json.logged || json.banned){
                                        
                                        button2.classList.add("nascosto");
                                        button2.classList.remove("leaveAnswer");
                                    }
                                    return button2;

                                })(),
                                (()=>{

                                    let commentContainer = document.createElement("div");
                                    commentContainer.setAttribute("id", "answer_container" + res[commentIndex].IDPost);


                                    return commentContainer;
                                })()
                            )
                        )
                    )

                    el.appendChild(dd);
                    loadAnswers(res[commentIndex].IDPost);
                }
            }
        })
        .catch((e)=> console.log(e));
}

function loadAnswers(idPost){

    fetch("/mtesser/api/answerGetter.php?IDPost=" + idPost)
        .then(response=> response.json())
        .then(json=> {
            if(json.ok){

                let res = json.result;
                let el = document.getElementById("answer_container" + idPost);

                if(typeof el !== "undefined"){

                    el.replaceChildren();
                    for(let answerIndex in res){

                        let div = document.createElement("div");
                        div.classList.add("answer");
                        let p = document.createElement("p");
                        p.innerText = res[answerIndex].userID + " ha risposto:";


                        let textarea = document.createElement("textarea");
                        textarea.classList.add("text");
                        textarea.setAttribute("rows", "3");
                        textarea.setAttribute("readonly", "readonly");
                        textarea.value = res[answerIndex].Text;

                        div.appendChild(p);
                        div.appendChild(textarea);

                        el.appendChild(div);
                    }
                }

            }
        })
        .catch((e) => console.log(e));
}

document.addEventListener('DOMContentLoaded', function(event) {

    getComments();
});