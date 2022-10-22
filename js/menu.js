function loadmenu() {
  if (window.innerWidth > 640) {
    document.getElementById("menu").classList.remove("nascosto");
    document.getElementById("menuhamburger").classList.add("nascosto");
    document.getElementById("chiudimenu").classList.add("nascosto");
    document.getElementById("menu").classList.add("floatleft");
    document.getElementById("menuhamburger").classList.remove("float");
    document.getElementById("chiudimenu").classList.remove("float");
    document.getElementById("menu").classList.remove("padding");
    return;
  }
  document.getElementById("menu").classList.add("nascosto");
  document.getElementById("chiudimenu").classList.add("nascosto");
  document.getElementById("menuhamburger").classList.remove("nascosto");
  document.getElementById("menu").classList.remove("floatleft");
  document.getElementById("menuhamburger").classList.add("float");
  document.getElementById("chiudimenu").classList.remove("float");
  decidePadding();
}
window.addEventListener("resize", function (event) {
  loadmenu();
});
function openmenu() {
  document.getElementById("menuhamburger").classList.add("nascosto");
  document.getElementById("menuhamburger").classList.remove("float");
  document.getElementById("chiudimenu").classList.remove("nascosto");
  document.getElementById("chiudimenu").classList.add("float");
  document.getElementById("menu").classList.remove("nascosto");
  decidePadding();
}

function closemenu() {
  document.getElementById("menuhamburger").classList.remove("nascosto");
  document.getElementById("menuhamburger").classList.add("float");
  document.getElementById("chiudimenu").classList.add("nascosto");
  document.getElementById("chiudimenu").classList.remove("float");
  document.getElementById("menu").classList.add("nascosto");
}

document.addEventListener("DOMContentLoaded", function (event) {
  loadmenu();
});

function decidePadding(){

    let padding = window.location.href.split("&").forEach((element) => {

        if(element.indexOf("singleRecipe") != -1 || element.indexOf("split")){

            document.getElementById("menu").classList.add("padding");
        }
    })
}