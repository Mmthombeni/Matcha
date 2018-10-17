<?php
	require_once ("classes/connectdb.php");
    require_once ("config/database.php");
    
    $userdata = NULL;
    $friend = Null;
    $user_id = null;
    
    try{
        $handler = new PDO($DB_DSN . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
        $handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        echo "Connection Failed: " . $e->getMessage();
    }

    if (isset($_SESSION["logged-in"])){
		$select = $handler->prepare("SELECT * FROM `Users` WHERE UserID= :id");
		$select->bindParam(":id" , $_SESSION["logged-in"]);
		$select->execute();
		if(!($userdata=$select->fetchAll())){
            echo json_encode(array("error"=> "You must be loggin1"));
            die;
        }
	}
	else{
        echo json_encode(array("error"=> "you must b logged to"));
        die;
    }
    
    if(isset($_POST)){
        $interest = null;
        $gender = null;
        $ageMin = null;
        $ageMax = null;
        $fameMin = null;
        $fameMax = null;
        $city = null;
        
        if (isset($_POST['interest']) && !empty($_POST['interest'])){
            //SETECT * WHERE name=$_POST
            $interest = ft_escape_str($_POST['interest']);
            $select = $handler->prepare("SELECT * FROM `TagLink` WHERE TagID= :id");
            $select->bindParam(":id" , $interest);
            $select->execute();
            
        }else{
            $interest = '';
        }

        if (isset($_POST['gender']) && !empty($_POST['gender'])){
            $gender = ft_escape_str($_POST['gender']);
            $select = $handler->prepare("SELECT * FROM `UserProfile` WHERE `Gender`= :gender");
            $select->bindParam(":gender" , $gender);
            $select->execute();
        }else{
            $gender = '';
        }

        if (isset($_POST['city']) && !empty($_POST['city'])){
            //SETECT * WHERE area=$_POST
            $city = ft_escape_str($_POST['city']);
            $select = $handler->prepare("SELECT * FROM `UserProfile` WHERE `Area`= :city");
            $select->bindParam(":city" , $city);
            $select->execute();
        }else{
            $city = '';
        }

        if (isset($_POST['age']) && !empty($_POST['age'])){
            if(isset($_POST['age']['min']) && !empty($_POST['age']['min']) && isset($_POST['age']['max']) && !empty($_POST['age']['max'])){
                $ageMin = ft_escape_str($_POST['age']['min']);
                $ageMax = ft_escape_str($_POST['age']['max']);
            }else{
                $ageMin = 0;
                $ageMax = 100;
            }
        }else{
            $ageMin = 0;
            $ageMax = 100;
        }

        if (isset($_POST['fame']) && !empty($_POST['fame'])){
            if(isset($_POST['fame']['min']) && !empty($_POST['fame']['min']) && isset($_POST['fame']['max']) && !empty($_POST['fame']['max'])){
                $fameMin = ft_escape_str($_POST['fame']['min']);
                $fameMax = ft_escape_str($_POST['fame']['max']);
            }else{
                $fameMin = 0;
                $fameMax = 1000;
            }
        }else{
            $fameMin = 0;
            $fameMax = 1000;
        }

        $select = $handler->prepare("SELECT * FROM userprofile, tags, taglink, users, userImages WHERE users.UserID != :id AND users.UserID = userprofile.UserID AND userImages.UserID = userprofile.UserID AND ProfileImage = true AND age BETWEEN :agemin AND :agemax AND fame BETWEEN :famemin AND :famemax AND gender LIKE '%".$gender."%' AND area LIKE '%". $city ."%' AND userprofile.UserID = taglink.UserID AND taglink.TagID = tags.TagID AND tags.TagName LIKE '%". $interest ."%' ORDER BY age, area, fame, tagName;");
        $select->bindParam(":id" , $_SESSION["logged-in"]);
        $select->bindParam(":agemin" , $ageMin);
        $select->bindParam(":agemax" , $ageMax);
        $select->bindParam(":famemin" , $fameMin);
        $select->bindParam(":famemax" , $fameMax);
        //$select->bindParam(":gender" , $gender);
        $select->execute();
        echo json_encode($select->fetchAll());

        
        //printf("$ageMax, $ageMin, $city, $fameMax, $fameMin, $interest, $gender");
    }
    else{
        echo "{}";
    }

