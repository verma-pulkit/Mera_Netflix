<?php

class Entity {

    private $con, $sql_data;
    public function __construct($con, $input){
        $this->con = $con;
        if (is_array($input)){
            $this->sql_data = $input;
        }
        else {
            $query = $this->con->prepare("SELECT * FROM entities WHERE id =:id");
            $query->bindValue(':id',$input);
            $query->execute();
 
            $this->sql_data =$query->fetch(PDO::FETCH_ASSOC); 
        }
    }

    public function getId (){
        return $this->sql_data['id'];
    }
    
    public function getName (){
        return $this->sql_data['name'];
    }
    
    public function getThumbnail (){
        return $this->sql_data['thumbnail'];
    }
    
    public function getPreview (){
        return $this->sql_data['preview'];
    }

}
?>