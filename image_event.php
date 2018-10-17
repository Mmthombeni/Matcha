<?php
	require_once ("classes/connectdb.php");
    require_once ("config/database.php");

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
		if(!($userdata=$select->fetchAll()))
			header ("Location: login.php");
	}
	else{
		header ("Location: login.php");
    }

    if (isset($_POST["delete"])){

        $imageDelete = $handler->prepare("DELETE FROM `UserImages` WHERE id=:id");
        $imageDelete->bindParam(":id" , $_POST["imgid"]);
        $imageDelete->execute();

        header("Location: update.php");
    }

    if (isset($_POST["set"])){

        $select = $handler->prepare("UPDATE UserImages SET ProfileImage = false WHERE UserID = :userid AND ProfileImage = true");
        $select->bindParam(":userid", $_SESSION["logged-in"]);
        $select->execute();

        $set = $handler->prepare("UPDATE UserImages SET ProfileImage = true WHERE   id = :id");
        $set->bindParam(":id" , $_POST["imgid"]);
        $set->execute();

        header("Location: home.php");
    }
