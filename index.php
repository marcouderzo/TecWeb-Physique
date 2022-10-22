<?php

require_once "libs/Parser.php";
require_once "libs/Renderer.php";
require_once "libs/contentCreator.php";
require_once "libs/DBaccess.php";
require_once "vendor/autoload.php";

session_start();

$parser = new Parser();
$parser-> addRoute('login', function(string $data){

    return $data;
}, array(
    'title' => 'Login - Physique',
    'redirect' => str_replace('&','&amp;',($_GET['prev'] ?? urlencode('/mtesser/?r=home'))),
    'admin' => $_SESSION['admin'] ?? false,
    'id' => 'content',
    'logged' => $_SESSION['username'] ?? false,
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina di login del sito Physique',
    'keywords' => 'Login, Accedi, Autenticazione',
    'errorusernamenonvalido' => ($_GET['eusnv'] ?? "") == "error",
    'errorpasswordnonvalido' => ($_GET['epanv'] ?? "") == "error",
    'errorusernamenonesistente' => ($_GET['eusne'] ?? "") == "error",
    'errorpasswordnonesistente' => ($_GET['epane'] ?? "") == "error",
    'errorsqlinjectiontry' => ($_GET['sqlit'] ?? "") == "error"
));

$parser-> addRoute('logout', function(string $data){

    session_destroy();
    $toRedirect = urldecode($_GET['prev'] ?? urlencode('/mtesser/?r=home'));

    header("location: $toRedirect");
});

$parser-> addRoute('signup', function(string $data){
    return $data;
}, array(
    'title' => 'Signup - Physique',
    'redirect' => str_replace('&','&amp;',($_GET['prev'] ?? urlencode('/mtesser/?r=home'))),
    'admin' => $_SESSION['admin'] ?? false,
    'id' => 'content',
    'logged' => $_SESSION['username'] ?? false,
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina di signup del sito Physique',
    'keywords' => 'Signup, Registrazione, Autenticazione',
    'errorusernamenonvalido' => ($_GET['eusnv'] ?? "") == "error",
    'errorusernameesistente' => ($_GET['euses'] ?? "") == "error",
    'erroremailnonvalida' => ($_GET['eemnv'] ?? "") == "error",
    'erroremailesistente' => ($_GET['eemes'] ?? "") == "error",
    'errornomenonvalido' => ($_GET['enonv'] ?? "") == "error",
    'errorcognomenonvalido' => ($_GET['econv'] ?? "") == "error",
    'errorpasswordnonvalida' => ($_GET['epanv'] ?? "") == "error",
    'errorpasswordnoncorrispondente' => ($_GET['epanc'] ?? "") == "error",
    'errordatabasefail' => ($_GET['edbfa'] ?? "") == "error"
));

$parser-> addRoute('home', function (string $data){
    return $data;
}, array(
    'title' => 'Home - Physique',
    'id' => 'contenthome',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina principale del sito Physique',
    'keywords' => 'Physique, Home, Palestra, Alimentazione, Fitness, Forum, News',
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false
));

$parser-> addRoute('workout', function (string $data){
    return $data;
}, array(
    'title' => 'Workout - Physique',
    'id' => 'content',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione workout del sito Physique',
    'keywords' => 'Workout, Allenamento, Fitness, Esercizio, Dimagrire, Muscoli, Palestra',
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false
));

$parser-> addRoute('adminPanel', function (string $data){
    return createAdminContent($data);
}, array(
    'title' => 'Pannello amministratore - Physique',
    'id' => 'content',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione pannello amministratore del sito Physique',
    'keywords' => 'Amministratore, Admin',
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false,
    'notadmin' => !($_SESSION['admin'] ?? false)
));

$parser-> addRoute('profile', function(string $data){

        return createProfileContent($data);
},array(

    'title' => 'Profilo utente - Physique',
    'id' => 'content',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione profilo del sito Physique',
    'keywords' => 'Profilo, Utente, Modifica',
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' => !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false
));

$parser-> addRoute('split', function (string $data){
    return $data;
}, array(
    'title' => '<typePlaceholder /> - Workout - Physique',
    'id' => 'content',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione split del sito Physique',
    'keywords' => 'Workout, Split, Allenamento, Esercizi, Palestra',
    'brosplit' => ($_GET['type'] ?? "") == "bro",
    'ppl' => ($_GET['type'] ?? "") == "ppl",
    'ul' => ($_GET['type'] ?? "") == "ul",
    'fb' => ($_GET['type'] ?? "") == "fb",
    'type' => (($_GET['type'] ?? "") == "bro" ? "Bro split" :
        (($_GET['type'] ?? "") == "ppl" ? "Push pull legs" :
            (($_GET['type'] ?? "") == "ul" ? "Upper lower" :
                (($_GET['type'] ?? "") == "fb" ? "Full body" : "")))),
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false
));

$parser-> addRoute('alimentation', function (string $data){

    return createAlimentationContent($data);
}, array(
    'title' => 'Alimentazione - Physique',
    'id' => 'content',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione alimentazione del sito Physique',
    'keywords' => 'Alimentazione, Ricette, Dimagrire, Calorie, Peso',
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false
));

$parser-> addRoute('singleRecipe', function (string $data){

    return createSingleRecipeContent($data, $_GET['id']);
}, array(
    'title' => '<recipeTitlePlaceholder /> - Alimentazione - Physique',
    'id' => 'content',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione ricetta singola del sito Physique',
    'keywords' => 'Alimentazione, Ricetta, Dimagrire, Calorie, Peso',
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false,
    'recipename' => '<recipeTitlePlaceholder />'
));

$parser-> addRoute('forum', function (string $data){

    return $data;
}, array(
    'title' => 'Forum - Physique',
    'id' => 'content',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione forum del sito Physique',
    'keywords' => 'Forum, Discussione, Consigli, Risposte, Alimentazione, Workout, Palestra, Dimagrire',
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false,
    'banned' => $_SESSION['banned'] ?? false,
    'loggednotbanned' => ($_SESSION['username'] ?? false) && !($_SESSION['banned'] ?? false)
));

$parser-> addRoute('news', function (string $data){

    $data = createNavMenuNews($data, $_GET['type'] ?? 'All');
    return createNewsContent($data, $_GET['type'] ?? 'All');
}, array(
    'title' => 'News - Physique',
    'id' => 'newscontent',
    'author' => 'Samuel Kostadinov, Emma Roveroni, Marco Tesser, Marco Uderzo',
    'description' => 'Pagina relativa alla sezione notizie del sito Physique',
    'keywords' => 'Notizie, Aggiornamenti, Workout, Alimentazione, Sito',
    'type' => ($_GET['type'] ?? '%%All%%'),
    'logged' => $_SESSION['username'] ?? false,
    'notlogged' =>  !($_SESSION['username'] ?? false),
    'redirect' => urlencode("/mtesser/?".http_build_query($_GET)),
    'admin' => $_SESSION['admin'] ?? false
));

$parser-> onNotFound(function (){
						  header('location: 404.html');
						  });

$parser->addDefault('home');

$parser-> parse();
