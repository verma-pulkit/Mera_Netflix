<?php
class PreviewProvider {
    
    private $con;
    private $username;
    public function __construct($con, $username) {
        $this->con = $con;
        $this->username = $username;
    }

    public function createPreviewVideo($entity) {
        if ($entity==null){
            $entity = $this->getRandomEntity();
        }

        $name = $entity->getName();
        $id = $entity->getId();
        $preview = $entity->getPreview();
        $thumbnail = $entity->getthumbnail();
        
        // TODO subtitle
        return "<div class = 'previewContainer'>
            <img src='$thumbnail' class = 'previewImage' hidden>
            <video autoplay muted class = 'previewVideo'>
                <source src = '$preview' type = 'video/mp4'></>
            </video>
            <div class = 'previewOverlay'>
                <div class = 'mainDetails'>
                    <h3> $name</h3>
                    <div class = 'buttons'>
                        <button><i class='fas fa-play'></i>
                        Play </button>
                        
                        <button>
                        <i class='fas fa-volume-mute'></i>
                         <i class='fas fa-volume-up'></i></button>
                        

                    </div>
                </div>
            </div>
        </div>";
    }   
    private function getRandomEntity(){

        $query = $this->con->prepare("SELECT * FROM entities ORDER BY RAND() LIMIT 1");
        $query->execute();
        //var_dump($query->errorInfo());
        $row = $query->fetch(PDO::FETCH_ASSOC);
        
        return new Entity($this->con, $row);


    }
}


?> 