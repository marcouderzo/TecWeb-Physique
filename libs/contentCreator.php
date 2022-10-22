<?php

function createAlimentationContent(string $data): string{

    $renderer = new Renderer();
    $DBaccess = new DBaccess();
    $contentArray = ($DBaccess->getConnection() !== false ? $DBaccess->getRecipeQuery() : null);
    if($contentArray !== null){

        $recipes = "";
        foreach($contentArray as $recipe){

            $recipes .= $renderer-> renderFile('alimentation/recipeTemplate', array(

                'name' => $recipe['Nome'],
                'singlerecipe' => '/mtesser/?r=singleRecipe&amp;id='.$recipe['ID'],
                'path' => $recipe['NomeImmagine'],
                'alt' => $recipe['AltImmagine'],
                'recipead' => $recipe['Descrizione']
            ));
        }

        $data = $renderer-> render($data, array('recipes' => $recipes));

    }else{

        $data = $renderer-> render($data, array('recipes' => 'Nessuna ricetta'));
    }
    return $data;
}

function createSingleRecipeContent(string $data, string $id): string{

    $renderer = new Renderer();
    $DBaccess = new DBaccess();
    $contentArray = ($DBaccess->getConnection() !== false ? $DBaccess->getSingleRecipeQuery($id) : null);


    if($contentArray !== null){

        $data = $renderer-> render($data, array('recipetitle' => $contentArray['Nome']));
        $tmpIngredientsArray = explode("\n", $contentArray['Ingredienti']);
        $tmpMethodArray = explode("\n", $contentArray['Procedimento']);

        $ingredientsList = "";
        foreach($tmpIngredientsArray as $ingredient) {

            if (!empty($ingredient)) {

                $ingredientsList .= $renderer->renderFile('alimentation/singleIngredient',
                    array('singleingredient' => $ingredient));
            }
        }

        $methodList = "";

        foreach ($tmpMethodArray as $step){

            if (!empty($step)) {

                $methodList .= $renderer->renderFile('alimentation/singleStep',
                    array('step' => $step));
            }
        }

        $hintPresent = $contentArray['Consigli'] != "Nessun consiglio.";
        $hintList = "";

        if($hintPresent){
            $tmpHintArray = explode("\n", $contentArray['Consigli']);

            foreach($tmpHintArray as $hint){

                if(!empty($hint)){

                    $hintList .= $renderer-> renderFile('alimentation/singleHint',
                        array('hint' => $hint));
                }
            }
        }

        $recipe = $renderer->renderFile('alimentation/singleRecipeTemplate', array(
            'recipename' => $contentArray['Nome'],
            'path' => $contentArray['NomeImmagine'],
            'alt' => $contentArray['AltImmagine'],
            'ingredients' => $ingredientsList,
            'number' => $contentArray['Persone'],
            'method' => $methodList,
            'hintpresent' => $hintPresent,
            'hints' => $hintList

        ));

        $data = $renderer-> render($data, array('singlerecipecontent' => $recipe));

    }else{

        $data = $renderer-> render($data, array());
    }

    return $data;
}

function createNewsContent(string $data, string $type): string{

    $renderer = new Renderer();
    $DBaccess = new DBaccess();
    $contentArray = ($DBaccess-> getConnection() !== false ? $DBaccess->getNewsQuery($type) : null);

    $newsList = "";
    $linkPresent = false;
    $all = $type == 'All';

    if($contentArray !== null){

        foreach ($contentArray as $element){

            $linkPresent = $element['link'] !== '';

            $newsList .= $renderer-> renderFile('news/newsTemplate', array(

                'newstitle' => $element['Titolo'],
                'all' => $all,
                'type' => $element['Tipo'],
                'text' => $element['Testo'],
                'linkpresent' => $linkPresent,
                'link' => $element['link']
            ));
        }

    }else{

        $newsList = $renderer-> renderFile('news/newsTemplate', array(
           'text' => "Nessuna notizia trovata",
            'Linkpresent' => $linkPresent
        ));
    }
    $data = $renderer->render($data, array(
        'news' => $newsList
    ));

    return $data;
}

function createNavMenuNews(string $data, string $type): string{

    $renderer = new Renderer();

    $menuItemAll = $renderer->renderFile('news/singleNavElement', array(
       'getlink' => '/mtesser/?r=news',
       'navtext' => '%%All news%%'
    ));

    $menuItemWorkout = $renderer->renderFile('news/singleNavElement', array(
        'getlink' => '/mtesser/?r=news&amp;type=workout',
        'navtext' => '%%Workout news%%'
    ));

    $menuItemAlimentation = $renderer->renderFile('news/singleNavElement', array(
        'getlink' => '/mtesser/?r=news&amp;type=alimentazione',
        'navtext' => 'Alimentazione %%news%%'
    ));

    $menuItemSite = $renderer->renderFile('news/singleNavElement', array(
        'getlink' => '/mtesser/?r=news&amp;type=sito',
        'navtext' => '%%News%% del sito'
    ));


    $menuItemAllNonLink = $renderer->renderFile('news/nonLinkNavElement', array(
        'text' => '%%All news%%'
    ));

    $menuItemWorkoutNonLink = $renderer->renderFile('news/nonLinkNavElement', array(
        'text' => '%%Workout news%%'
    ));

    $menuItemAlimentationNonLink = $renderer->renderFile('news/nonLinkNavElement', array(
        'text' => 'Alimentazione %%news%%'
    ));

    $menuItemSiteNonLink = $renderer->renderFile('news/nonLinkNavElement', array(
        'text' => '%%News%% del sito'
    ));

    $navList = "";

    if($type == "All"){

        $navList .= $menuItemAllNonLink;
        $navList .= $menuItemWorkout;
        $navList .= $menuItemAlimentation;
        $navList .= $menuItemSite;
    }

    if($type == "sito"){

        $navList .= $menuItemAll;
        $navList .= $menuItemWorkout;
        $navList .= $menuItemAlimentation;
        $navList .= $menuItemSiteNonLink;
    }

    if($type == "workout"){

        $navList .= $menuItemAll;
        $navList .= $menuItemWorkoutNonLink;
        $navList .= $menuItemAlimentation;
        $navList .= $menuItemSite;
    }

    if($type == "alimentazione"){

        $navList .= $menuItemAll;
        $navList .= $menuItemWorkout;
        $navList .= $menuItemAlimentationNonLink;
        $navList .= $menuItemSite;
    }
    $data = $renderer->render($data, array(
       'navnews' => $navList
    ));

    return $data;
}

function createAdminContent(string $data): string{

    $renderer = new Renderer();

    $content = $renderer-> renderFile('adminPanel/adminPanel', array(
        'eti' => ($_GET['eti'] ?? "") == "error",
        'errorlink' => ($_GET['eli'] ?? "" ) == "error",
        'errortext' => ($_GET['ete'] ?? "") == "error",
        'errortype' => ($_GET['ety'] ?? "") == "error",
        'errorrecipetitle' => ($_GET['ert'] ?? "") == 'error',
        'errorrecipedecr' => ($_GET['erd'] ?? "") == 'error',
        'errorrecipeimage' => ($_GET['erim'] ?? "") == 'error',
        'errorrecipeingredients' => ($_GET['eri'] ?? "") == 'error',
        'errorrecipemethod' => ($_GET['erm'] ?? "") == 'error',
        'errorrecipepeople' => ($_GET['erp'] ?? "") == 'error',
        'errorrecipehint' => ($_GET['erh'] ?? "") == 'error',
        'errorrecipealt' => ($_GET['era'] ?? "") == 'error'
    ));

    $data = $renderer-> render($data, array(
        'admincontent' => $content
    ));

    return $data;

}

function createProfileContent(string $data):string{
    $renderer = new Renderer();

    $content = $renderer-> renderFile('profile/profile', array());

    $data = $renderer-> render($data, array(
        'usercontent' => $content
    ));

    return $data;
}