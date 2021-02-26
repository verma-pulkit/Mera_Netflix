<?php 
class Account {

    private $con;
    private $errorArray = array();

    public function __construct ($con){
        $this->con = $con;
    }

    public function register ($fn, $ln, $un, $em, $em2, $pw, $pw2){
        $this->validateFirstName($fn);
        $this->validateLastName($ln);
        $this->validateUserName($un);
        $this->validateEmail($em , $em2);
        $this->validatePassword($pw, $pw2);
        if(empty($this->errorArray)) {
            return $this->insertUserDetails($fn, $ln, $un, $em, $pw);
        }

        return false;
    }
    public function login($un, $pw) {
        $pw = hash("sha512", $pw);

        $query = $this->con->prepare("SELECT * FROM users WHERE username=:un AND password=:pw");
        $query->bindValue(":un", $un);
        $query->bindValue(":pw", $pw);

        $query->execute();

        if($query->rowCount() > 0) {
            return true;
        }

        array_push($this->errorArray, Constants::$loginFailed);
        return false;
    }


    private function insertUserDetails($fn, $ln, $un, $em, $pw) {
        
        $pw = hash("sha512", $pw);
        $query = $this->con->prepare("INSERT INTO users (firstname, lastName, username, email, password)
                                        VALUES (:fn, :ln, :un, :em, :pw)");
        $query->bindValue(":fn", $fn);
        $query->bindValue(":ln", $ln);
        $query->bindValue(":un", $un);
        $query->bindValue(":em", $em);
        $query->bindValue(":pw", $pw);
        $query->execute();
        //var_dump($query->ErrorInfo());
        return $query->execute();
    }

 
    private function validateFirstName($fn){
        if (strlen($fn) < 2 || strlen($fn) > 25){
            array_push($this->errorArray, Constants::$firstNameCharacters);
            return;
        }
    }

    private function validateLastName($ln){
        if (strlen($ln) < 2 || strlen($ln) > 25){
            array_push($this->errorArray, Constants::$lastNameCharacters);
            return;
        }
    }

    private function validateUsername($un){
        if (strlen($un) < 2 || strlen($un) > 25){
            array_push($this->errorArray, Constants::$userNameCharacters);
            return;
        }
        $query = $this->con->prepare("SELECT * FROM users WHERE username = :un");
        $query->bindValue(':un',$un);
        $query->execute();
        if ($query->rowCount!=0){
            array_push($this->errorArray, Constants::$userNameTaken);
        }

    }

    private function validateEmail ($em, $em2){
        if ($em!=$em2){
            array_push($this->errorArray, Constants::$emailDonotMatch);
            return;
        }
        if (!filter_var($em, FILTER_VALIDATE_EMAIL)){
            array_push($this->errorArray, Constants::$emailInvalid);
            return;
        }
        $query = $this->con->prepare("SELECT * FROM users WHERE email = :em");
        $query->bindValue(':em',$em);
        $query->execute();
        if ($query->rowCount!=0){
            array_push($this->errorArray, Constants::$emailTaken);
        }
    }

    private function validatePassword($password, $password2){
        if ($password!=$password2){
            array_push($this->errorArray, Constants::$passwordsDonotMatch);
            return;
        }

        if (strlen($password) < 5 || strlen($password) > 25){
            array_push($this->errorArray, Constants::$passwordLength);
            return;
        }
    }
    
    public function getError ($error){
        if (in_array($error, $this->errorArray)){
            return "<span class='errorMessage'>$error</span>";
        }
    }
} 
?>