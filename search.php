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

    $select = $handler->prepare("SELECT * FROM `Tags`");
    $select->execute();
    $tags = $select->fetchAll();

    ?>


<!DOCTYPE html>
<html lang='en'>
    <head>
            <meta charest="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>MATCHA | Search</title>
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
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-bell badge badge-secondary count" ></span>Notification </a> <ul class="dropdown-menu"></ul>
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
                <div class="row" id='searchResults'>
                    
                </div>
    
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <!--sidebar-->
            
                    <form action="search.php" method="post" id="find">
                        <div class="form-group">
                            <label for="interest">Interest</label>
                            <select name="interest" class="form-control" id="interest">
                                <option value=""></option>
                                <?php 
                                    if ($tags){
                                        foreach($tags as $tag){
                                            ?>
                                                <option value="<?php echo $tag['TagName'] ?>"><?php echo $tag['TagName'] ?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" name="gender" id="gender">
                                <option value=""></option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="row">  
                                <div class="col-6">
                                    <label for="ageMin">Age between</label>
                                    <input type="number" name="ageMin" class="form-control" id="ageMin" placeholder="Minimum Age">
                                </div>
                                <div class="col-6">
                                    <label for="ageMax">and</label>
                                    <input type="number" name="ageMax" class="form-control" id="ageMax" placeholder="Maximum Age">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">  
                                <div class="col-6">
                                    <label for="fameMin">Fame between</label>
                                    <input type="number" name="fameMin" class="form-control" id="fameMin" placeholder="minimum number">
                                </div>
                                <div class="col-6">
                                    <label for="fameMax">and</label>
                                    <input type="number" name="fameMax" class="form-control" id="fameMax" placeholder="maximum number">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" name="city" class="form-control" id="city" placeholder="City">
                        </div>
                        <input class="btn btn-primary" type="submit" name="search" value="Find your love" id="search"/>
                    </form >  

                <?php 
                  
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
    <script src="js/search.js"></script>
    <script src="js/notification.js"></script>