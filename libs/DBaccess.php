<?php

class DBaccess{

    private const HOST_DB = "127.0.0.1:3306";
    private const USERNAME = "mtesser";
    private const PASSWORD = "ikee4Doongaem7ju";
    private const DATABASE_NAME = "mtesser";

    private $connection;

    public function __construct(){

        $this->openDBConnection();
    }

    public function getConnection(){

        return $this-> connection;
    }

    public function openDBConnection():bool{


        $this-> connection = new mysqli(DBaccess::HOST_DB, DBaccess::USERNAME,
            DBaccess::PASSWORD, DBaccess::DATABASE_NAME);

        if(!$this->connection-> connect_error){

            return true;
        }
        return false;
    }

    public function closeConnection(): void{

        $this-> connection-> close();
    }

    public function getRecipeQuery(){

        $querySelect = "SELECT ID, Nome, Descrizione, NomeImmagine, AltImmagine FROM alimentazione ORDER BY ID ASC";
        $queryResult = $this-> connection-> query($querySelect);

        if($queryResult-> num_rows == 0){

            return null;
        }

        $recipeList = array();
        while($row = $queryResult-> fetch_assoc()){

           $singleRecipe = array(
              "ID" => $row['ID'],
              "Nome" => base64_decode($row['Nome']),
              "Descrizione" => base64_decode($row['Descrizione']),
              "NomeImmagine" => base64_decode($row['NomeImmagine']),
              "AltImmagine" => base64_decode($row['AltImmagine'])
           );

           array_push($recipeList, $singleRecipe);
        }

        return $recipeList;
    }

    public function getSingleRecipeQuery($id){

        $querySelect = "SELECT * FROM alimentazione WHERE ID = " . intval($id);

        $queryResult = $this-> connection-> query($querySelect);

        if($queryResult-> num_rows == 0){

            return null;
        }

        $row = $queryResult-> fetch_assoc();

        $singolaRicetta = array(
           "Nome" => base64_decode($row['Nome']),
           "NomeImmagine" => base64_decode($row['NomeImmagine']),
           "AltImmagine" => base64_decode($row['AltImmagine']),
           "Persone" => base64_decode($row['Persone']),
           "Ingredienti" => base64_decode($row['Ingredienti']),
           "Procedimento" => base64_decode($row['Procedimento']),
           "Consigli" => base64_decode($row['Consigli'])
        );

        return $singolaRicetta;

    }

    public function getNewsQuery($type){

        $querySelect = "SELECT * FROM news ";
        if($type == "workout" || $type == "alimentazione" || $type == "sito"){

            $querySelect .= "WHERE tipo = '". ucfirst($type) ."'";
        }
        $querySelect .= "ORDER BY ID DESC";

        $queryResult = $this->connection->query($querySelect);

        if($queryResult === false || $queryResult->num_rows == 0){

            return null;
        }

        $newsList = array();
        while($row = $queryResult->fetch_assoc()){

            $notice = array(

                'ID' => $row['ID'],
                'Tipo' => $row['Tipo'],
                'Titolo' => base64_decode($row['Titolo']),
                'Testo' => base64_decode($row['Testo']),
                'link' => $row['Link'] ?? ''
            );

            array_push($newsList, $notice);
        }

        return $newsList;
    }

    public function getUsernameQuery(string $userName): bool{

        $querySelect = "SELECT * FROM utente WHERE IDUtente = '".base64_encode($userName)."'";

        $queryResult = $this-> connection-> query($querySelect);

        return $queryResult !== false && $queryResult->num_rows != 0;

    }

    public function getMailQuery(string $mail): bool{

        $querySelect = "SELECT * FROM utente WHERE Email = '".base64_encode($mail)."'";

        $queryResult = $this-> connection-> query($querySelect);

        return $queryResult !== false && $queryResult->num_rows != 0;

    }

    public function getCorrectPasswordQuery(string $userName, string $hashedPssw):bool{

        $querySelect = "SELECT * FROM utente WHERE Password = '". $hashedPssw ."' AND IDUtente = '".base64_encode($userName)."'";
        $queryResult = $this-> connection-> query($querySelect);

        return $queryResult !== false && $queryResult->num_rows != 0;
    }

    public function getUserData(string $userName): array{

        $querySelect = "SELECT IDUtente, Nome, Cognome, Email, Amministratore, Bannato FROM utente WHERE IDUtente = '" . base64_encode($userName) ."'";
        $queryResult = $this-> connection-> query($querySelect);

        $row = $queryResult-> fetch_assoc();

        return array(
            'IDUtente' => base64_decode($row['IDUtente']),
            'Nome' => base64_decode($row['Nome']),
            'Cognome' => base64_decode($row['Cognome']),
            'Email' => base64_decode($row['Email']),
            'Admin' => $row['Amministratore'] == 1,
            'Bannato' => $row['Bannato'] == 1
        );
        
    }

    public function insertUser($username, $mail, $name, $surname, $hashedPassword): bool{

        $username = base64_encode($username);
        $name = base64_encode($name);
        $surname = base64_encode($surname);
        $mail = base64_encode($mail);

        $query = "INSERT INTO utente VALUES('$username', '$name', '$surname', '$mail', '$hashedPassword', 0, 0);";

        $queryResult = $this-> connection-> query($query);

        return $queryResult !== false;
    }

    public function getPostList($username =""){

        /*
         * Query formattata:
         * SELECT post.IDPost,post.IDUtente, post.Testo, COUNT(likes.IDUtente) AS 'NumeroLike', ABS(ISNULL(MyLikes.IDPost) - 1) AS 'LeftLike'
           FROM (post LEFT JOIN likes
                        ON post.IDPost = likes.IDPost)
                      LEFT JOIN (SELECT IDPost
                                 FROM likes
                                 WHERE IDUtente = '$username') AS MyLikes
                        ON MyLikes.IDPost = likes.IDPost
           GROUP BY post.IDPost
           ORDER BY post.IDPost DESC
         *
         * Prende gli id, i testi e gli id utenti di ogni post, in più conta i like e calcola se l'utente corrente
         * ha lasciato un like. Raggruppa per id del post e ordina i risultati tramite l'id del post.
         *
         * */
        $username = base64_encode($username);

        $querySelect = "SELECT post.IDPost,post.IDUtente, post.Testo, COUNT(likes.IDUtente) AS 'NumeroLike', ABS(ISNULL(MyLikes.IDPost) - 1) AS 'LeftLike' FROM (post LEFT JOIN likes ON post.IDPost = likes.IDPost) LEFT JOIN (SELECT IDPost FROM likes WHERE IDUtente = '".$username."') AS MyLikes ON MyLikes.IDPost = likes.IDPost GROUP BY post.IDPost ORDER BY post.IDPost DESC";

        $queryResult = $this-> connection-> query($querySelect);

        if($queryResult === false || $queryResult->num_rows == 0){

            return null;
        }

        $postList = array();

        while($row = $queryResult-> fetch_assoc()){

            $singlePost = array(

                "IDPost" => $row['IDPost'],
                "IDUtente" => base64_decode($row['IDUtente']),
                "NumeroLike" => $row['NumeroLike'],
                "Testo" => base64_decode($row['Testo']),
                "LeftLike" => $row['LeftLike'] == 1
            );
            array_push($postList, $singlePost);
        }

        return $postList;
    }

    public function leaveLike($username, $leftLike, $idPost){

        $query = "";

        $username = base64_encode($username);

        if($leftLike){

            $query = "INSERT IGNORE INTO likes VALUES ( $idPost , '$username')";
        }else{

            $query = "DELETE FROM likes WHERE IDUtente = '".$username."' AND IDPost = ".$idPost;
        }

        $queryResult = $this-> connection-> query($query);

        return $queryResult;
    }

    public function insertAnswer($username,$text, $idPost):bool{

        $username = base64_encode($username);
        $text = base64_encode($text);

        $query = "INSERT INTO risposta (IDUtente, Testo, IDPost) VALUES ('$username','$text', $idPost);";

        $queryResult = $this->connection->query($query);

        return $queryResult;
    }

    public function insertPost($username, $text){

        $username = base64_encode($username);
        $text = base64_encode($text);

        $query = "INSERT INTO post (IDUtente, numeroLike, Testo) VALUES ('$username',0,'$text')";

        $queryResult = $this-> connection-> query($query);

        return $queryResult;
    }

    public function getPostAnswer($idPost = 1){

        $idPost = intval($idPost);

        $querySelect = "SELECT * FROM risposta WHERE IDPost = $idPost ORDER BY IDRisposta ASC";


        $queryResult = $this-> connection-> query($querySelect);

        if($queryResult === false || $queryResult-> num_rows == 0){

            return null;
        }
        else{

            $answers = array();
            while($row = mysqli_fetch_assoc($queryResult)){

                $singleAnswer = array(
                    "answerId" => $row['IDRisposta'],
                    "userID" => base64_decode($row['IDutente']),
                    "Text" => base64_decode($row['Testo']),
                    "IDPost" => $row['IDPost']
                );

                array_push($answers, $singleAnswer);
            }
            return $answers;
        }

    }

    public function getUserList(){

        $query = "SELECT IDUtente, Amministratore, Bannato FROM  utente";

        $result = $this-> connection-> query($query);

        //not checking number of results since there is at least one user (the admin)
        //if there are no users the page that calls this query is unreachable
        if($result === false){

            return null;
        }

        $userList = array();

        while($element = $result-> fetch_assoc()){

            $user = array(

                'username' => base64_decode($element['IDUtente']),
                'admin' => $element['Amministratore'] == 1,
                'banned' => $element['Bannato'] == 1
            );

            array_push($userList, $user);
        }

        return $userList;
    }

    public function promoteToAdmin(string $user): bool{

        $user = base64_encode($user);
        $query = "UPDATE utente SET Amministratore = 1 WHERE IDUtente='$user'";

        return $this-> connection-> query($query);
    }

    public function getRecipeList(){

        $query = "SELECT ID, nome FROM alimentazione";

        $result = $this-> connection-> query($query);

        if($result === false || $result-> num_rows == 0){

            return null;
        }

        $recipeList = array();

        while($element = $result-> fetch_assoc()){

            $user = array(

                'id' => $element['ID'],
                'name' => str_replace('%%','',base64_decode($element['nome']))
            );

            array_push($recipeList, $user);
        }

        return $recipeList;
    }

    public function getNewsList(){

        $query = "SELECT ID, titolo FROM news";

        $result = $this-> connection-> query($query);

        if($result === false || $result-> num_rows == 0){

            return null;
        }

        $newsList = array();

        while($element = $result-> fetch_assoc()){

            $user = array(

                'id' => $element['ID'],
                'title' => str_replace('%%','',(base64_decode($element['titolo'])))
            );

            array_push($newsList, $user);
        }

        return $newsList;
    }

    public function removeRecipe(int $id): bool{

        $id = intval($id);
        $query = "DELETE FROM alimentazione WHERE ID=$id;";

        return $this-> connection-> query($query);

    }

    public function removeNews(int $id): bool{

        $id = intval($id);
        $query = "DELETE FROM news WHERE ID=$id;";

        return $this-> connection-> query($query);

    }

    public function getNewsTypesList(){

        $query = "SHOW COLUMNS FROM news LIKE 'Tipo'";

        $result = $this-> connection-> query($query);

        if($result === false || $result-> num_rows == 0){

            return null;
        }

        $element = $result-> fetch_assoc();
        $types = $element['Type'];
        $types = substr($types, 6, strlen($types) - 7);
        $types = str_replace("'", "", $types);
        $typesList = explode(",", $types);

        return $typesList;
    }

    public function getPostsList(){

        $query = "SELECT IDPost, Testo FROM post";

        $result = $this-> connection-> query($query);

        if($result === false || $result-> num_rows == 0){

            return null;
        }

        $posts = array();

        while($element = $result-> fetch_assoc()){

            $post = array(

                'id' => $element['IDPost'],
                'text' => base64_decode($element['Testo'])
            );

            array_push($posts, $post);
        }

        return $posts;
    }

    public function getText($idPost): string{

        $query = "SELECT Testo FROM post WHERE IDPost = $idPost";

        $result = $this-> connection-> query($query);

        if($result === false || $result-> num_rows == 0){

            return "";
        }

        $element = $result-> fetch_assoc();

        return base64_decode($element['Testo']);

    }

    public function deletePost($idPost):bool{

        $query1 = "DELETE FROM risposta WHERE IDPost = $idPost";
        $query2 = "DELETE FROM post WHERE IDPost = $idPost";
        $query3 = "DELETE FROM likes WHERE IDPost = $idPost";

        $result1 = $this->connection-> query($query1);
        $result3 = $this->connection-> query($query3);
        $result2 = $this->connection-> query($query2);

        return $result2;

    }

    public function insertNews($type, $title, $text, $link): bool{

        $title = base64_encode($title);
        $text = base64_encode($text);

        $query = "";
        if($link != "") {
            $query = "INSERT INTO news (Tipo, Titolo, Testo, Link) VALUES ('$type', '$title', '$text', '$link')";
        }else{
            $query = "INSERT INTO news (Tipo, Titolo, Testo) VALUES ('$type', '$title', '$text')";
        }

        return $this-> connection-> query($query);
    }

    public function insertRecipe($name, $description, $link, $alt, $ingredients, $method, $hints, $people): bool{

        $name = base64_encode($name);
        $description = base64_encode($description);
        $alt = base64_encode($alt);
        $ingredients = base64_encode($ingredients);
        $method = base64_encode($method);
        $hints = base64_encode($hints);
        $people = base64_encode($people);
        /*
         *
         * INSERT INTO alimentazione (Nome, Descrizione, NomeImmagine, AltImmagine, Ingredienti, Procedimento, Consigli, Persone)
         *      VALUES('$name', '$description', '$link', '$alt', '$ingredients', '$method', '$hints', '$people')
         *
         * */
        $query = "INSERT INTO alimentazione (Nome, Descrizione, NomeImmagine, AltImmagine, Ingredienti, Procedimento, Consigli, Persone) VALUES('$name', '$description', '$link', '$alt', '$ingredients', '$method', '$hints', '$people')";

        return $this-> connection-> query($query);
    }

    public function banUser(string $user): bool{

        $user = base64_encode($user);

        $query = "UPDATE utente SET Bannato=1 WHERE IDUtente='$user'";

        return $this-> connection-> query($query);

    }

    public function unbanUser(string $user): bool{

        $user = base64_encode($user);

        $query = "UPDATE utente SET Bannato=0 WHERE IDUtente='$user'";

        return $this-> connection-> query($query);

    }

    public function deleteAnswer(int $idAnswer){

        $query = "DELETE FROM risposta WHERE IDRisposta = $idAnswer";

        return $this-> connection-> query($query);
    }

    public function deleteUser(string $user): bool{

        $user = base64_encode($user);

        $queryLikes = "DELETE FROM likes WHERE IDUtente = '$user'";
        $queryAnswers = "DELETE FROM risposta WHERE IDUtente = '$user'";
        $queryOtherAnswers = "DELETE FROM risposta WHERE IDPost IN (SELECT IDPost FROM post WHERE IDUtente = '$user')";
        $queryPosts = "DELETE FROM post WHERE IDUtente = '$user' ";
        $queryUser = "DELETE FROM utente WHERE IDUtente = '$user'";
        $queryUsersLikes = "DELETE FROM likes WHERE IDPost IN (SELECT IDPost FROM post WHERE IDUtente = '$user')";

        $resultLikes = $this-> connection-> query($queryLikes);
        $resultAnswers = $this-> connection-> query($queryAnswers);
        $resultOtherAnswers = $this-> connection-> query($queryOtherAnswers);
        $resultUsersLikes = $this-> connection-> query($queryUsersLikes);
        $resultPosts = $this-> connection-> query($queryPosts);
        $resultUser = $this-> connection-> query($queryUser);

        return $resultUser;
    }

    public function changeData(string $user, string $name, string $surname, string $email, string $password): bool{

        $user = base64_encode($user);
        $name = base64_encode($name);
        $surname = base64_encode($surname);
        $email = base64_encode($email);


        $query = "UPDATE utente SET Nome = '$name', Cognome = '$surname', Email = '$email', Password = '$password' WHERE IDUtente = '$user'";

        return $this-> connection-> query($query);
    }

}

