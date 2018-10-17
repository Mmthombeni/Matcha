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

    $user_id = $_GET['userid'];
    $select = $handler->prepare("SELECT * FROM `chats` WHERE (user_id_from = :logged AND user_id_to = :userid) OR (user_id_from = :userid AND user_id_to = :logged) ORDER BY date_updated ASC");
    $select->bindParam(":userid" , $user_id);
    $select->bindParam(":logged" , $_SESSION['logged-in']);
	$select->execute();
    echo json_encode($select->fetchAll());
