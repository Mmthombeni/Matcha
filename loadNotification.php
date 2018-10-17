 <?php
    require_once ("classes/connectdb.php");
    require_once ("config/database.php");
    
    $userdata = NULL;
    $friend = Null;
    $user_id = null;
    //$result = null;
    //$dataRow = null;
    
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
    
        if(isset($_POST["view"]))
        {
            $select = $handler->prepare("SELECT * FROM `Notification` WHERE ToUser= :id AND Notif_status= 0 ORDER BY date_updated DESC LIMIT 6");
            $select->bindParam(":id" , $_SESSION['logged-in']);
            $select->execute();
            $result = $select->fetchAll();
            $dataRow = $select->rowCount();
            $output = '';
            //result
            if($dataRow > 0)
            {
                foreach($result as $row)
                {
                    $output .= '
                    <li>
                        <a href="#">
                            '.$row["FromUser"].''.$row["Mssg"].'
                        </a>
                    </li>
                    ';
                }
            }
            else{
                $output .= '
                    <li>
                        <a href="#">
                            <strong>No notification found</strong>
                        </a>
                    </li>
                ';
            }
           
            if($_POST["view"] != '')
            {
                $select = $handler->prepare("SELECT * FROM `Notification` WHERE ToUser= :id");
                $select->bindParam(":id" , $_SESSION['logged-in']);
                $select->execute();
                $result = $select->fetchAll();
                //update to true
                foreach($result as $row){
                $update = $handler->prepare("UPDATE `Notification` SET  `Notif_status`=1 WHERE ToUser= :id");
                $update->bindParam(":id" , $_SESSION["logged-in"]);
                $update->execute();
                }
            }
            $select = $handler->prepare("SELECT * FROM `Notification` WHERE ToUser= :id AND Notif_status= 0 ORDER BY date_updated DESC LIMIT 6");
            $select->bindParam(":id" , $_SESSION['logged-in']);
            $select->execute();
            //$result = $select->fetchAll();
            $dataRow = $select->rowCount();
            //query again
            $data = array(
                'notification'  => $output,
                'unseen_notification' => $dataRow
            );
            echo json_encode($data);
        }
?>