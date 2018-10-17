<?php
	require_once ("classes/connectdb.php");
    require_once ("config/database.php");
    
    $user_id = 0;
    $errors = NULL;
    $handler = NULL;
    $userRow = NULL;
    $userdata = NULL;
    $userpics = NULL;
    $userprof = array();
    $userlike = null;
    $blocked = array();
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
            
        $select = $handler->prepare("SELECT * FROM `UserImages` WHERE UserID= :id");
        $select->bindParam(":id" , $_SESSION["logged-in"]);
        $select->execute();
        $loggedpics=$select->fetchAll();
	}
	else{
		header ("Location: login.php");
    }

    if (isset($_GET["user"]) && !empty($_GET["user"])){
        $user_id = $_GET["user"];

        $select = $handler->prepare("UPDATE UserProfile SET Fame = Fame + 1 WHERE UserID = :userid;");
        $select->bindParam(":userid", $user_id);
        $select->execute();

        $select = $handler->prepare("SELECT * FROM `Likes` WHERE (Liker = :logid AND Liked = :user) OR (Liker = :user AND Liked = :logid) ");
        $select->bindParam(":logid" , $_SESSION["logged-in"]);
        $select->bindParam(":user" , $user_id);
        $select->execute();
        $userlike = $select->fetch();  
        
        $select = $handler->prepare("SELECT * FROM `Notification` WHERE (FromUser = :logid AND ToUser = :user)");
        $select->bindParam(":logid" , $_SESSION["logged-in"]);
        $select->bindParam(":user" , $user_id);
        $select->execute();
        $note = $select->fetch();

        if (!$note){
            $insert = $handler->prepare("INSERT INTO `Notification` (FromUser, ToUser, Mssg) VALUES (:logid, :usrid, ' Viewed your Profile');");
            $insert->bindParam(":logid" , $_SESSION["logged-in"]);
            $insert->bindParam(":usrid" , $user_id);
            $insert->execute();
        }
    }
    else{
        $user_id = $_SESSION["logged-in"];
    }

    $select = $handler->prepare("SELECT * FROM `TagLink` WHERE UserID= :id");
    $select->bindParam(":id" , $_SESSION["logged-in"]);
    $select->execute();
    $usertags = $select->fetch();


    if (isset($_GET["block"]) && !empty($_GET["block"]) && isset($_GET["userid"]) && !empty($_GET["userid"])){

        $insert = $handler->prepare("INSERT INTO Blocked (Blocker, Blocked) VALUES (:logid, :usrid);");
        $insert->bindParam(":logid" , $_SESSION["logged-in"]);
        $insert->bindParam(":usrid" , $_GET["userid"]);
        $insert->execute();
        
    }

    if (isset($_GET["fake"]) && isset($_GET["userid"]) && !empty($_GET["userid"])){

        $insert = $handler->prepare("INSERT INTO Fakes (UserID) VALUES (:usrid)");
        $insert->bindParam(":usrid" , $_GET["userid"]);
        $insert->execute();

        $insert = $handler->prepare("INSERT INTO `Notification` (FromUser, ToUser, Mssg) VALUES (:logid, :usrid, ' Reported you fake');");
        $insert->bindParam(":logid" , $_SESSION["logged-in"]);
        $insert->bindParam(":usrid" , $user_id);
        $insert->execute();
    }

    if((isset($_GET['like']) && !empty($_GET['like'])) && (isset($_GET['userid']) && !empty($_GET['userid']))){            
    
        if($_GET['like'] === "unlike"){

            $delete = $handler->prepare("DELETE FROM Likes WHERE (Liker = :logid AND Liked = :user) OR (Liker = :user AND Liked = :logid);");
            $delete->bindParam(":logid" , $_SESSION["logged-in"]);
            $delete->bindParam(":user" , $_GET['userid']);
            $delete->execute();

            $insert = $handler->prepare("INSERT INTO `Notification` (FromUser, ToUser, Mssg) VALUES (:logid, :usrid, ' DisConnected with you ');");
            $insert->bindParam(":logid" , $_SESSION["logged-in"]);
            $insert->bindParam(":usrid" , $user_id);
            $insert->execute();
            
        }
        else if($_GET['like'] === "liked"){

            $insert = $handler->prepare("INSERT INTO Likes (Liker, Liked) VALUES (:logid, :usrid);");
            $insert->bindParam(":logid" , $_SESSION["logged-in"]);
            $insert->bindParam(":usrid" , $_GET['userid']);
            $insert->execute();

            $insert = $handler->prepare("INSERT INTO `Notification` (FromUser, ToUser, Mssg) VALUES (:logid, :usrid, ' Liked your profile, check their profile to accept');");
            $insert->bindParam(":logid" , $_SESSION["logged-in"]);
            $insert->bindParam(":usrid" , $user_id);
            $insert->execute();

            
        }
        else if($_GET['like'] === "accept"){

            $like = TRUE;
            $update = $handler->prepare("UPDATE Likes  SET Stat = :unlike  WHERE (Liker = :logid AND Liked = :user) OR (Liker = :user AND Liked = :logid); ");
            $update->bindParam(":logid" , $_SESSION["logged-in"]);
            $update->bindParam(":user" , $_GET['userid']);
            $update->bindParam(":unlike" , $like);
            $update->execute();

            $insert = $handler->prepare("INSERT INTO `Notification` (FromUser, ToUser, Mssg) VALUES (:logid, :usrid, ' accepted you, start chating.');");
            $insert->bindParam(":logid" , $_SESSION["logged-in"]);
            $insert->bindParam(":usrid" , $user_id);
            $insert->execute();
        }
    }   
    
    $select = $handler->prepare("SELECT * FROM `UserProfile`, `Tags`, `TagLink` WHERE UserProfile.UserID = :userID AND UserProfile.UserID = TagLink.UserID AND TagLink.TagID = Tags.TagID");
	$select->bindParam(":userID" , $user_id);
	$select->execute();
    $userdata = $select->fetch();

    $gender = $userdata["Gender"];

	$select = $handler->prepare("SELECT * FROM `Users` WHERE UserID = :userID");
	$select->bindParam(":userID" , $user_id);
    $select->execute();
    $userRow = $select->fetch();
        $fname = $userRow['UserFirstName'];
        $lname = $userRow['UserLastName'];
        $email = $userRow['UserEmail'];
    
    $select = $handler->prepare("SELECT * FROM `UserImages` WHERE UserID = :userID AND ProfileImage = true ");
    $select->bindParam(":userID" , $user_id);
    $select->execute();
    $picture = $select->fetch();
       
        $select = $handler->prepare("SELECT * FROM `UserImages` WHERE UserID = :id");
            $select->bindParam(":id" , $user_id);
            $select->execute();
            $userpics = $select->fetchAll();   

        if(isset($_GET["view"])) {

            $select = null;

            if ($userdata['Preference'] === "bi"){
                $select = $handler->prepare("SELECT * FROM `UserImages`, `Users`, `UserProfile`  WHERE userimages.UserID != :id AND userimages.ProfileImage = true AND users.UserID = userprofile.UserID AND users.UserID=userimages.UserID AND (Age BETWEEN (:userage - 5) AND (:userage + 5)) AND Area = :area");
                $select->bindParam(":id" , $user_id);
                $select->bindParam(":userage" , $userdata['Age']);
                $select->bindParam(":area" , $userdata['Area']);

            }else{
                $select = $handler->prepare("SELECT * FROM `UserImages`, `Users`, `UserProfile`  WHERE userimages.UserID != :id AND userimages.ProfileImage = true AND users.UserID = userprofile.UserID AND users.UserID=userimages.UserID AND Gender=:pref AND (Age BETWEEN (:userage - 5) AND (:userage + 5))");
                $select->bindParam(":id" , $user_id);
                $select->bindParam(":pref" , $userdata['Preference']);
                $select->bindParam(":userage" , $userdata['Age']);
                $select->bindParam(":area" , $userdata['Area']);
            }
            $select->execute();
            $userprof = $select->fetchAll();

            
            if($usertags){
                $matchtaguserid= array();

                foreach ($userprof as $el){
                    $select = $handler->prepare("SELECT * FROM `Users`, `tags`, `taglink` WHERE users.UserID=taglink.UserID AND tags.TagID=taglink.TagID AND users.UserID= ".$el['UserID']." AND tags.TagID=:tags");
                    $select->bindParam(":tags" , $usertags['TagID']);
                    $select->execute();
                    $matchtag = $select->fetchAll(); 

                    if ($matchtag){
                        foreach($matchtag as $match){
                            $matchtaguserid[] = $match['UserID'];
                        }
                    }
                }
                
                foreach ($userprof as $ele){
                    if (!in_array($ele['UserID'], $matchtaguserid)){
                        $matchtaguserid[] = $ele['UserID'];
                    }
                }
                $tempUserProf = array();
                foreach ($matchtaguserid as $ele){                
                    $select = $handler->prepare("SELECT * FROM users, userimages, userprofile WHERE users.UserID=userimages.UserID AND userprofile.UserID=users.UserID AND users.UserID=:id AND userimages.ProfileImage = true");
                    $select->bindParam(":id" , $ele);
                    $select->execute();
                    $tempUserProf[] = $select->fetch();
                    
                }
                $userprof = $tempUserProf;
            }

            $select = $handler->prepare("SELECT * FROM `blocked` WHERE blocker = :id");
            $select->bindParam(":id" , $user_id);
            $select->execute();
            $blocked = $select->fetchAll();
        }
    
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MATCHA</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <?php
        include ("fonts.php");
    ?>
    
    
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
                    <a class="nav-link" href="update.php"><span class="fa fa-pencil"></span>Update Profile</a>
                </li>
                
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-bell badge badge-secondary count" ></span>Notification </a> <ul class="dropdown-menu"></ul>
                </li>
                <li>
                    <a href="search.php" class="nav-link"><span class="fa fa-search"></span>Search</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signout.php"><span class="fa fa-power-off" ></span></a>
                    
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <input type="hidden" value="<?php echo $_SESSION['logged-in'] ?>" id="userid" />
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                <!--Main-->
                <table class="table" width="100">
                <!--<table width="398" border="0" align="center" cellpadding="0">-->
                    <tr>
                        <?php
                            if($user_id == $_SESSION['logged-in']){
                                echo '<td height="26" colspan="2"> Your Profile Information </td>';
                            }else{
                                echo '<td height="26" colspan="2">'.$userRow['Username'].' Profile Information </td>';
                            }
                        ?>

                    </tr>
                    <tr>
                        <td width="129" rowspan="5"><img src="<?php echo $picture['ImageName'] ?>" style="width:100%" alt="no image found"/></td>
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
                        <?php
                            if($user_id == $_SESSION['logged-in']){                      
                                echo '<td valign="top"><div align="left">Email:</div></td>';
                                echo '<td valign="top">' .$email. '</td>';
                            }else {
                                echo '<td valign="top"><div align="left">User Name:</div></td>';
                                echo '<td valign="top">' .$userRow['Username']. '</td>';
                            }
                        ?>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Age: </div></td>
                        <td valign="top"><?php echo $userdata["Age"] ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Area: </div></td>
                        <td valign="top"><?php echo $userdata["Area"] ?></td>
                    </tr>
                    <tr>
                        <td valign="top"><div align="left">Interest </div></td>
                        <td valign="top"><?php echo $userdata["TagName"] ?></td>
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

                    <?php
                        if ($user_id == $_SESSION['logged-in']){
                            ?>
                                <hr>

                                <nav class="nav">
                                    <a class="nav-link" href="profile.php?change=name">Change Name</a>
                                    <a class="nav-link" href="profile.php?change=username">Change Username</a>
                                    <a class="nav-link" href="profile.php?change=email">Change Email</a>
                                    <a class="nav-link" href="profile.php?change=password">Change Password</a>
                                </nav>
                            <?php
                        }
                        else{
                                echo '<hr>';
                                echo '<nav class="nav">';
                            if(!$loggedpics){
                                echo '<div class="alert alert-warning" role="alert">Please Uplaod a picture to be able to like and connect to users, also ensure to set profile picture to ensure other users see you </div>'; 
                            }else{
                                if ($userlike){
                                    if($userlike['Stat'] == true){
                                        echo '<a class="nav-link" href="home.php?like=unlike&userid='.$user_id.'"> <span class="fa fa-thumbs-down"> unlike </span> </a>';
                                        echo '<a class="nav-link" href="chat.php?userid='.$user_id.'"> <span class="fa fa-comment">Chat</span></a>';//liked - unlike
                                    }
                                    else{
                                        if($userlike['Liked'] == $_SESSION["logged-in"]){
                                            echo '<a class="nav-link" href="home.php?like=accept&userid=' .$user_id.'"> <span class="fa fa-user-plus"> Accept </span></a>';//appending - cfrm like
                                        }
                                        else{
                                            echo '<a class="nav-link" href="home.php?like=unlike&userid=' .$user_id.'"> <span class="fa fa-user-times"> Cancel </span> </a>';
                                        }
                                    }
                                }else{
                                    
                                    echo '<a class="nav-link" href="home.php?like=liked&userid=' .$user_id.'"> <span class="fa fa-thumbs-up"> like </span> </a>';
                                }
                                echo '<a class="nav-link" href="home.php?block=blocked&userid=' .$user_id.'"> <span class="fa fa-trash"> block </span> </a>';
                                echo '<a class="nav-link" href="home.php?fake=fake&userid=' .$user_id.'"> <span class="fa fa-bomb"> Fake </span></a>';
                            }
                            echo '</nav>';
                        }
                    ?>

                    <hr>
                    <div class="row"> 
                        <?php
                            foreach($userpics as $userpic){
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <img src="<?php echo $userpic['ImageName']?>" style="width:100%" alt="" srcset="">
                                </div>
                                <?php
                            }
                        ?>
                    </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <!--sidebar-->
                <?php
                    if($userdata){
                        
                        echo '<a class="btn btn-primary btn-block" href="home.php?view=view">View Profiles</a>';
                    }else
                        echo '<div class="alert alert-warning" role="alert"> To view other Users and make friends make sure you COMPLETE your Profile AND SET Profile picture </div>';
                ?>
                <?php
                    $final_blocked = array();
                    foreach ($blocked as $el){
                        $final_blocked[] = $el['Blocked'];
                    }

                    if ($userprof){
                        foreach($userprof as $userprofl){
                            if (!in_array($userprofl['UserID'], $final_blocked)){
                                ?>
                                    <div class="card" style="width: 100%; margin-top: 10px;">
                                        <img class="card-img-top" src="<?php echo $userprofl['ImageName'] ?>" alt="Card image cap">
                                        <div class="card-body">
                                            <h5 class="card-title"><a href="home.php?user=<?php echo $userprofl['UserID']; ?>"><?php echo $userprofl['Username'] ?> </a></h5>
                                            <p class="card-text">
                                                Fame Rating: <?php echo $userprofl['Fame'] ?>
                                            </p>
                                        </div>
                                    </div> 
                                <?php
                            }
                        }
                    }
                ?>
                 
            </div>
        </div>
    </div>

    <nav class="navbar navbar-light bg-light">
        <div class="container">
        <i>&copy; mmthombe</i>
                Matcha
        </div>
    </nav>
    <script src="jquery-3.3.1.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <script src="js/notification.js"></script>
</body>
</html>