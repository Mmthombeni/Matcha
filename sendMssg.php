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
    
    if (isset($_GET) && !empty($_GET["userid"]) && $_GET["userid"] != $_SESSION["logged-in"]){

        $select = $handler->prepare("SELECT * FROM `Users` WHERE UserID= :id");
		$select->bindParam(":id" , $_GET["userid"]);
		$select->execute();
		if(!($friend=$select->fetchAll())){
            echo json_encode(array("error"=> "User not found"));
            die;
        }

    }else{
        echo json_encode(array("error"=> "User not found"));
        die;
    }

    $insert = $handler->prepare("INSERT INTO `chats` (`user_id_from`, `user_id_to`, `message`, `date_updated`) VALUES (:logged, :userid, :mssg, CURRENT_TIMESTAMP);");
    $insert->bindParam(":logged" , $_SESSION["logged-in"]);
    $insert->bindParam(":userid" , $_GET["userid"]);
    $insert->bindParam(":mssg" , $_POST["message"]);
    if($insert->execute()){
        echo json_encode(array("success"=> "Message sent"));  

    $insert = $handler->prepare("INSERT INTO `Notification` (FromUser, ToUser, Mssg) VALUES (:logid, :usrid, ' Sent you a message');");
    $insert->bindParam(":logid" , $_SESSION["logged-in"]);
    $insert->bindParam(":usrid" , $_GET["userid"]);
    $insert->execute();

    };