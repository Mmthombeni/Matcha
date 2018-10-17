<?php
	require_once ("classes/connectdb.php");
    require_once ("config/database.php");
    
    $errors = NULL;
    $handler = NULL;
    $userRow = NULL;
    $userdata = NULL;
    $userpics = NULL;
    $userprof = array();
    
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
    
    $select = $handler->prepare("SELECT * FROM `UserProfile` WHERE UserID = :userID");
	$select->bindParam(":userID" , $_SESSION["logged-in"]);
	$select->execute();
    $userdata = $select->fetch();

    $gender = $userdata["Gender"];

	$select = $handler->prepare("SELECT * FROM `Users` WHERE UserID = :userID");
	$select->bindParam(":userID" , $_SESSION["logged-in"]);
    $select->execute();
    $userRow = $select->fetch();
        $fname = $userRow['UserFirstName'];
        $lname = $userRow['UserLastName'];
        $email = $userRow['UserEmail'];
    
       
        if(isset($_POST["submit"])) {
           
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "Image Uploaded";
                $uploadOk = 1;
               
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            if ($_FILES["fileToUpload"]["size"] > 500000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;  
            }
            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file); 
            
           /* $select = $handler->prepare("SELECT * FROM `UserImages` WHERE UserID= :id");
            $select->bindParam(":id" , $_SESSION["logged-in"]);
            $select->execute();
            $userda = $select->fetch();
            if ($userda <= 5){*/
                $insert =$handler->prepare("INSERT INTO `UserImages` (UserID, ImageName)
                VALUES(:id, :filenym)");
                $insert->bindParam(":id" , $_SESSION["logged-in"]);
                $insert->bindParam(':filenym', $target_file);
                $insert->execute();
        }

        $select = $handler->prepare("SELECT * FROM `UserImages` WHERE UserID = :id");
            $select->bindParam(":id" , $_SESSION["logged-in"]);
            $select->execute();
            $userpics = $select->fetchAll();   

        if(isset($_GET["view"])) {
            $select = $handler->prepare("SELECT * FROM `UserImages`, `Users` WHERE userimages.UserID != :id AND ProfileImage = TRUE AND userimages.UserID = users.UserID");
            $select->bindParam(":id" , $_SESSION["logged-in"]);
            $select->execute();
            $userprof = $select->fetchAll();
            print_r($userprof);   

        }
    
    ?>
    
    <!DOCTYPE html>
    <html lang='en'>
        <head>
                <meta charest="UTF-8">
                <title>MATCHA | Home Page</title>
    
                <link rel="stylesheet" href="bootstrap.min.css" type="text/css">
                <link rel="stylesheet" href="cam.css" type="text/css" media="all">
                
        </head>
        <body>
            <nav class="nav_bar">
                <div class="left">
                    <h4>MATCHA</h4>
                </div>
                <div class="right">
                <ul>
                    <li><a class="active" href="#home">login: <?php if (isset($_SESSION['username'])) echo $_SESSION['username']; else echo "username"; ?></a></li>
                    <li><a href="update.php">Update Profile</a></li>
                    <li><a href="signout.php">logout</a></li>
                </ul>
                </div>
            </nav>
    
            <input type="hidden" name="username" id="username" value="<?php if (isset($_SESSION['username'])) echo $_SESSION['username']; else echo "username"; ?>">
    
            <div class ="container">
                <div class="main">
                    <div class="camera_container">
                    
                        <table width="398" border="0" align="center" cellpadding="0">
                    <tr>
                        <td height="26" colspan="2">Your Profile Information </td>

                    </tr>
                    <tr>
                        <td width="129" rowspan="5"><img src="<?php echo $picture ?>" width="129" height="129" alt="no image found"/></td>
                        <td width="82" valign="top"><div align="left">FirstName:</div></td>
                        <td width="165" valign="top"><?php echo $userRow['UserFirstName'] ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">LastName:</div></td>
                        <td valign="top"><?php echo $lname ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Gender:</div></td>
                        <td valign="top"><?php echo $gender ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Email:</div></td>
                        <td valign="top"><?php echo $email ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Age: </div></td>
                        <td valign="top"><?php echo $userdata["Age"] ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Preference: </div></td>
                        <td valign="top"><?php echo $userdata["Preference"] ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Bio: </div></td>
                        <td valign="top"><?php echo $userdata["Bio"] ?></td>
                    </tr>
                    </table>
                    </div> 
                    <br/>
                    <br/>
                    <div class="super">
                    <form action="" method="post" enctype="multipart/form-data">
                         Select image to upload:
                        <input type="file" name="fileToUpload" id="fileToUpload">
                        <input type="submit" value="Upload Image" name="submit">
                    </form>

                    </div>
                    <?php
                        /*
                        echo '<div class="row">';
                        foreach($userpics as $userpic){
                            echo '</div><div class="row">';
                        echo '<p><img src="'.$userpic['ImageName'].'"></p>';
                        echo '</div>';
                        }*/

                        
                    ?>

                    <br/>
					<br/>
					<br/>
					<a href="profile.php?change=name">Change Name</a>
					<a href="profile.php?change=username">Change Username</a>
					<a href="profile.php?change=email">Change Email</a>
					<a href="profile.php?change=email">Change Email</a>                  
                </div>

            <div class="side_nav">
                <a href="home.php?view=view">View Profile</a>

                    <?php
                        
                        foreach($userprof as $userprofl){
                            echo '<a>';
                            echo '<div class="">';
                            echo '<h5> '.$userprofl['UserFirstName'].'</h5>';
                            echo '<p><img src="'.$userprofl['ImageName'].'"></p>';
                            echo '</div>';
                            echo '</a>';
                        }

                        
                    ?>
                
            </div>
            </div>
            <footer>
            <i>&copy; mmthombe</i>
                Matcha
            </footer>
        </body>
    </html>