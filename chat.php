<?php
	require_once ("classes/connectdb.php");
    require_once ("config/database.php");
    
    $userdata = NULL;
    $friend = Null;
    $friendlist = NULL;
    $friendmatch = NULL;
    
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
		if(!($userdata=$select->fetch()))
			header ("Location: login.php");
	}
	else{
		header ("Location: login.php");
    }

    if (isset($_GET) && !empty($_GET["userid"]) && $_GET["userid"] != $_SESSION["logged-in"]){

        $select = $handler->prepare("SELECT * FROM `Users`,`UserImages`  WHERE users.UserID = :id AND users.UserID = userImages.UserID AND ProfileImage = true");
		$select->bindParam(":id" , $_GET["userid"]);
		$select->execute();
		if(!($friend=$select->fetch()))
			header ("Location: login.php");

    }else{
        //header ("Location: home.php");
    }

    $status = TRUE;
    $select = $handler->prepare("SELECT * FROM `Likes`, `Users`, `UserImages` WHERE (Liker = :logid OR Liked = :logid) AND Stat = :stat AND users.UserID = userImages.UserID AND ProfileImage = true AND (likes.Liked = users.UserID OR likes.Liker = users.UserID)");
    $select->bindParam(":logid" , $_SESSION["logged-in"]);
    $select->bindParam(":stat" , $status);
    $select->execute();
    $friendlist = $select->fetchAll();

?>



<!DOCTYPE html>
<html lang='en'>
    <head>
            <meta charest="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>MATCHA | Chat</title>
            <link rel="stylesheet" href="css/style.css">
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
                    <a class="nav-link" href="update.php">Update Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-bell badge badge-secondary count" ></span> </a> <ul class="dropdown-menu"></ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signout.php">logout</a>
                </li>
                </ul>
            </div>
        </div>
    </nav>

    <input type="hidden" value="<?php echo $_GET['userid'] ?>" id="userid" />
    <input type="hidden" value="<?php echo $userdata['Username'] ?>" id="me" />
    <input type="hidden" value="<?php echo $friend['Username'] ?>" id="friend" />


    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">


                <div class="containers clearfix">
                    <div class="people-list" id="people-list">
                    <!-- <div class="search">
                        <input type="text" placeholder="search" />
                        <i class="fa fa-search"></i>
                    </div> -->
                        <ul class="list">
                            <!-- foreach to dispaly the list one one-->
                            <?php 
                                if($friendlist){
                                    foreach ($friendlist as $frnd){
                                        if ($frnd['UserID'] !== $_SESSION['logged-in']){
                                ?>
                                            <li class="clearfix">
                                                <img src="<?php echo $frnd['ImageName'] ?>" alt="avatar" />
                                                <div class="about">
                                                    <div class="name"><?php echo '<a href="chat.php?userid='.$frnd['UserID'].'"> '.$frnd['Username'].'</a>'; ?></div>
                                                    <div class="status">
                                                    <!--<i class="fa fa-circle online"></i> online-->
                                                    </div>
                                                </div>
                                            </li>
                            <?php 
                                        }
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                    
                    <div class="chat">
                        <div class="chat-header clearfix">
                            <img src="<?php echo $friend['ImageName'] ?>" alt="back/images.png" />
                            
                            <div class="chat-about">
                            <div class="chat-with"><?php echo $friend['Username']; ?></div>
                            </div>
                        </div> <!-- end chat-header -->
                    
                        <div class="chat-history">
                            <ul id="chat_container">
                               
                            </ul>
                        
                        </div> <!-- end chat-history -->
                    
                        <div class="chat-message clearfix">
                            <textarea name="message-to-send" id="message-to-send" placeholder ="Type your message" rows="3"></textarea>
                                    
                            <i class="fa fa-file-o"></i> &nbsp;&nbsp;&nbsp;
                            <i class="fa fa-file-image-o"></i>
                            
                            <button id="sendMssgBtn">Send</button>

                        </div> <!-- end chat-message -->
                    
                    </div> <!-- end chat -->
                    
                </div> <!-- end container -->

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
    <script type="text/javascript" src="js/chat.js">
    
        
    </script>
    
</body>
</html>