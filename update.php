<?php

require_once ("config/database.php");
require_once ("classes/connectdb.php");


$errors = NULL;
$handler = NULL;
$status = NULL;
$userdata = NULL;
$userpics = NULL;
$usertags = array();

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

$select = $handler->prepare("SELECT * FROM `TagLink` WHERE UserID= :id");
$select->bindParam(":id" , $_SESSION["logged-in"]);
$select->execute();
$usertags = $select->fetch();

$select = $handler->prepare("SELECT * FROM `Tags`");
$select->execute();
$tags = $select->fetchAll();

if(isset($_POST["submit"])) {
           
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if ($_FILES["fileToUpload"]["name"]){
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
        $select->bindParam(":id" , $user_id);
        $select->execute();
        $userda = $select->fetch();
        if ($userda <= 5){*/
            $insert =$handler->prepare("INSERT INTO `UserImages` (UserID, ImageName)
            VALUES(:id, :filenym)");
            $insert->bindParam(":id" , $_SESSION['logged-in']);
            $insert->bindParam(':filenym', $target_file);
            $insert->execute();
    }
}

if(isset($_POST['update']))
{
    $gender = ft_escape_str($_POST['gender']);
    $age = ft_escape_str($_POST['age']);
    $pref = ft_escape_str($_POST['pref']);
    $intrest = ft_escape_str($_POST['tag']);
    $bio = ft_escape_str($_POST['bio']);
    $area = ft_escape_str($_POST['area']);

    if (!empty($gender) && !empty($age) && !empty($pref) && !empty($bio) && !empty($intrest) && !empty($area)){

        if($usertags){
            $update = $handler->prepare("UPDATE `TagLink` SET  `TagID`=:tag WHERE UserID= :id");
            $update->bindParam(":id" , $_SESSION["logged-in"]);
            $update->bindParam(":tag" , $intrest);
            $update->execute();

        }else{
            $insert =$handler->prepare("INSERT INTO `TagLink` (UserID, TagID) VALUES (:id, :tag)");
            $insert->bindParam(":id" , $_SESSION["logged-in"]);
            $insert->bindParam(":tag" , $intrest);
            $insert->execute();

        }

        $select = $handler->prepare("SELECT UserID FROM `UserProfile` WHERE UserID= :id");
        $select->bindParam(":id" , $_SESSION["logged-in"]);
        $select->execute();
        $userda = $select->fetch();
        if (!$userda){
            $insert =$handler->prepare("INSERT INTO `UserProfile` (UserID, Gender, Age, Area, Preference, Bio)
            VALUES (:id, :gender, :age, :area, :pref, :bio)");
            $insert->bindParam(':id',$_SESSION["logged-in"]);
            $insert->bindParam(':gender',$gender);
            $insert->bindParam(':age',$age);
            $insert->bindParam(':area',$area);
            $insert->bindParam(':pref',$pref);
            $insert->bindParam(':bio',$bio);
            $insert->execute();
        } 
        else {
            $insert = $handler->prepare("UPDATE `userprofile` SET `Gender`=:gender,`Age`= :age,`Bio`=:bio,`Preference`=:pref WHERE UserID = :id");
            $insert->bindParam(':id',$_SESSION["logged-in"]);
            $insert->bindParam(':gender',$gender);
            $insert->bindParam(':age',$age);
            $insert->bindParam(':pref',$pref);
            $insert->bindParam(':bio',$bio);
            $insert->execute();
        }

        $select = $handler->prepare("SELECT * FROM `Location` WHERE UserID= :id");
        $select->bindParam(":id" , $_SESSION["logged-in"]);
        $select->execute();
        $local = $select->fetch();
        if (!$local){
            $insert =$handler->prepare("INSERT INTO `Location` (UserID, Lati, Logi)
            VALUES (:id, :lat, :log)");
            $insert->bindParam(':id',$_SESSION["logged-in"]);
            $insert->bindParam(':lat',$_POST['latitude']);
            $insert->bindParam(':log',$_POST['longitude']);
            $insert->execute();
        } 
        else {
            $insert = $handler->prepare("UPDATE `Location` SET `Lati`=:lat,`Logi`= :log WHERE UserID = :id");
            $insert->bindParam(':id',$_SESSION["logged-in"]);
            $insert->bindParam(':lat',$_POST['latitude']);
            $insert->bindParam(':log',$_POST['longitude']);
            $insert->execute();
        }
    }
    else
        $status = "Fields incomplete";

    header ("Location: home.php");
}

$select = $handler->prepare("SELECT * FROM `UserProfile` WHERE UserID= :id");
$select->bindParam(":id" , $_SESSION["logged-in"]);
$select->execute();
$userdata = $select->fetch();

$select = $handler->prepare("SELECT * FROM `UserImages` WHERE UserID = :id");
$select->bindParam(":id" , $_SESSION["logged-in"]);
$select->execute();
$userpics = $select->fetchAll(); 

?>




<!DOCTYPE html>
<html lang='en'>
    <head>
            <meta charest="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>MATCHA | Update Profile</title>
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <link rel="stylesheet" href="bootstrap.min.css" type="text/css">
    </head>
    <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">MACTHA</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#">login: <?php if (isset($_SESSION['username'])) echo $_SESSION['username']; else echo "username"; ?><span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="home.php">HOME</a></a>
                </li>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-bell badge badge-secondary count" ></span> Notification</a> <ul class="dropdown-menu"></ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signout.php">logout</a>
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                <!--main-->

                <form action="update.php" method="post">
                    <?php
                    if ($status)
                        echo $status."<br/>";
                    ?>
                    Gender:<br />
                    <select class="form-control" name = "gender">
                        <option value=''></option>
                        <option value="female" <?php if(isset($userdata["Gender"]) && $userdata["Gender"] == "female") echo "selected"; ?>>Female</option>
                        <option value="male" <?php if(isset($userdata["Gender"]) && $userdata["Gender"] == "male") echo "selected"; ?>>Male</option>
                    </select>
                        
                    Age:<br />
                        <input class="form-control" name="age" value="<?php if(isset($userdata["Age"])) echo $userdata["Age"]; ?>" required/><br />
                    Area:<br />
                        <input class="form-control" name="area" value="<?php if(isset($userdata["Area"])) echo $userdata["Area"]; ?>" required/><br />
                    Sexual Preference:<br />
                        <select class="form-control" name = "pref">
                            <option value=''></option>
                            <option value="bi" <?php if(isset($userdata["Preference"]) && $userdata["Preference"] == "bi") echo "selected"; ?>>Both</option>
                            <option value="female" <?php if(isset($userdata["Preference"]) && $userdata["Preference"] == "female") echo "selected"; ?>>Women</option>
                            <option value="male" <?php if(isset($userdata["Preference"]) && $userdata["Preference"] == "male") echo "selected"; ?>>Men</option>
                        </select>
                        <br />
                    Update Your Interest:<br />
                    <?php 
                        if ($tags){
                            $usertag = 0;
                            if ($usertags){
                                $usertag = $usertags['TagID'];
                            }

                            foreach($tags as $tag){
                                ?>
                                    <div >
                                        <input type="radio" id="tag-<?php echo $tag['TagID']?>" name="tag" value="<?php echo $tag['TagID'];?>" <?php if ($tag['TagID'] == $usertag) echo 'checked'; ?>>
                                        <label for="tag-<?php echo $tag['TagID']?>"><?php echo $tag['TagName'] ?></label>
                                    </div>
                                <?php
                            }
                        }
                    ?>
                    <br>

                    Bio:<br />
                        <textarea name="bio" class="form-control" required><?php if(isset($userdata["Bio"])) echo $userdata["Bio"]; ?></textarea>
                        <br />

                    Location:<br>
                        <div class="form-row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Latitude" id="latitude" name="latitude" readonly> 
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Longitude" id="longitude" name="longitude" readonly>
                            </div>
                        </div>
                        <br>
                    <input class="btn btn-primary" type="submit" name="update" value="Update Profile" id="register"/>
                </form>
    
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <!--sidebar-->
                <?php
                    if (count($userpics) < 5){
                ?>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="fileToUpload" id="fileToUpload">
                                    <label class="custom-file-label" for="fileToUpload">Choose file</label>
                                </div>
                                <div class="input-group-append">
                                    <input type="submit" value="Upload Image" name="submit" class="btn btn-outline-secondary">
                                </div>
                            </div>
                        </form>
                        <?php
                    }
                            if (!$userpics){
                                echo '<div class="alert alert-warning" role="alert">Please Uplaod a picture to be able to like and connect to users and ensure to set profile picture to ensure other users see you </div>';
                            }
                        ?>
                <hr>

                <?php
                    if ($userpics){
                        
                        foreach($userpics as $userpic){
                ?>
                        <img src="<?php echo $userpic['ImageName']?>" style="width:100%" alt="" srcset="">
                <?php   
                        echo '<form action="image_event.php" method="post">';
                        echo '<input type="hidden" name="imgid" value="' . $userpic['id'] .'"/>';
                        echo '<input class="btn btn-danger" type="submit" name="delete" value="Delete"/>';
                        echo '<input class="btn btn-primary" type="submit" name="set" value="Set as Profile"/>';
                        echo '</form>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-light bg-light">
        <div class="container">
            
        </div>
    </nav>
    <script src="jquery-3.3.1.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <script type="text/javascript"> 
       var lat_input = document.getElementById("latitude"); 
       var log_input = document.getElementById("longitude");

       function setGeolocation(){
           if (navigator.geolocation){
                navigator.geolocation.getCurrentPosition(function(position){
                    lat_input.value = position.coords.latitude;
                    log_input.value = position.coords.longitude;
                });
           }
       }
       setGeolocation();
    </script>
    <script src="js/notification.js"></script>
    <footer>
            <i>&copy; mmthombe</i>
                Matcha
    </footer>
</body>
</html>


