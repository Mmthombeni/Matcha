<?php
	include_once('config/database.php');

    $handler = NULL;
    $user_data = null;
    $error = NULL;
	try
	{
		$handler = new PDO($DB_DSN . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
		$handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	}
	catch(PDOException $e){
		echo "Connection Failed: " . $e->getMessage();
	}
	
	if (isset($_SESSION["logged-in"])){

		$select = $handler->prepare("SELECT * FROM `Users` WHERE UserID= :id");
		$select->bindParam(":id" , $_SESSION["logged-in"]);
		$select->execute();
		if(!($user_data = $select->fetchAll()))
            header ("Location: login.php");

       // $user_data = $user_data[0];

        if (isset($_POST['update_nam'])){
            
            $name = ft_escape_str($_POST['new_name']);
            $lname = ft_escape_str($_POST['lname']);

            if(!empty($name) && !empty($lname)){
                $insert = $handler->prepare("UPDATE Users SET UserFirstName = :fname, UserLastName = :lname WHERE UserID = :id;");
                $insert->bindParam(":fname" , $name);
                $insert->bindParam(":lname" , $lname);
                $insert->bindParam(":id" ,  $_SESSION["logged-in"]);
                $insert->execute();

                $error = "Name Updated successfully.";
            }else
                $error = "Name fieled is empty, could not update";
            $_SESSION['error'] = $error;
            header('Location: profile.php?change=name');
        }
        else if(isset($_POST['update_user'])){

            $user = ft_escape_str($_POST['new_user']);

            if(!empty($user)){
                $select = $handler->prepare("SELECT Username FROM `Users` WHERE Username = :username");
                $select->bindparam(':username', $user);
                $select->execute();
                $userRow = $select->fetch();
                    if (!$userRow){
                        $insert = $handler->prepare("UPDATE `Users` SET Username = :username WHERE UserID = :id; ");
                        $insert->bindParam(":username" , $user);
                        $insert->bindParam(":id" ,  $_SESSION["logged-in"]);
                        $insert->execute();

                        $select = $handler->prepare("SELECT * FROM `Users` WHERE UserID= :id");
                        $select->bindParam(":id" , $_SESSION["logged-in"]);
                        $select->execute();
                        

                        $user_data = $select->fetch();

                        $_SESSION['username'] = $user_data['Username'];
                       // $fname = $user_data['FirstName'];

                        $error = "Username Updated successfully.";
            }else
                $error = "User fieled is empty, could not update";
            $_SESSION['error'] = $error;
                header('Location: profile.php?change=username');
                    
            }
        }
        else if(isset($_POST['update_email'])){

            $email = ft_escape_str($_POST['new_email']);
            if (($error = validate_email($email)) !== true){
                $_SESSION['error'] = $error;
            }else{

                if(!empty($email)){
                    $select = $handler->prepare("SELECT UserEmail FROM `Users` WHERE UserEmail = :email");
                    $select->bindparam(':email', $email);
                    $select->execute();
                    $userRow = $select->fetch(PDO::FETCH_ASSOC);
                    if (!$userRow){  
                        $insert = $handler->prepare("UPDATE `Users` SET UserEmail = :email WHERE UserID = :id;");
                        $insert->bindParam(":email" , $email);
                        $insert->bindParam(":id" ,  $_SESSION["logged-in"]);
                        $insert->execute();
                    
                        $error = "New email Updated successfully.";
                    }else
                        $error = "Email fieled is empty, could not update";
                    $_SESSION['error'] = $error;
                    header('Location: profile.php?change=email');
                }
            }
        }
        else if(isset($_POST['update_pass'])){

            $pass = ($_POST['new_pass']);
            $oldpass = ($_POST['password']);


            if(!empty($pass) && !empty($oldpass)){

                if(($error = validate_password($pass)) === true){
                    $encryppass = password_hash($pass, PASSWORD_BCRYPT);


                    $select = $handler->prepare("SELECT UserPassword FROM `Users` WHERE UserID = :id; ");
                    $select->bindParam(":id" ,  $_SESSION["logged-in"]);
                    $select->execute();
                    $userRow = $select->fetch(PDO::FETCH_ASSOC);

                    if (password_verify($oldpass, $userRow['UserPassword'])){
                        $insert = $handler->prepare("UPDATE `Users` SET UserPassword = :password WHERE UserID = :id;");
                        $insert->bindParam(":password" , $encryppass);
                        $insert->bindParam(":id" ,  $_SESSION["logged-in"]);
                        $insert->execute();
                        
                        $error = "Password Updated successfully.";
                    }else{
                        $error = "Could not update, current password do not match";
                    }
                        
                }
                
                $_SESSION['error'] = $error;
                header('Location: profile.php?change=password');

            }else
                $error = "Password feild empty, could not update";
        }
	}
	else{
		header ("Location: login.php");
    }
    
   
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

                <?php
                    if ($error)
                        echo $error."<br/>";

                    if (isset($_GET['change'])){
                        if(isset($_SESSION['error']) && !empty($_SESSION['error'])){
                            echo $_SESSION['error'];
                            $_SESSION['error'] = '';
                        }
                        if ($_GET['change'] == "name"){
                        
                            echo '<br /><br/>';
                            echo '<form action="profile.php" method="post">';
                            echo 'First Name: ';
                            echo '<input class="form-control" name="new_name" value=""/><br/>';
                            echo 'Last Name: ';
                            echo '<input class="form-control" name="lname" value=""/><br/>';
                            echo '<input type="submit" name="update_nam" value="Update" id="update"/>';
        
                        }else if ($_GET['change'] == "username"){
                            echo '<br /><br/>';
                            echo '<form action="profile.php" method="post">';
                            echo 'Username: ';
                            echo '<input name="user" value=""/><br />';
                            echo 'New Username: ';
                            echo '<input class="form-control" name="new_user" value=""/><br/>';
                            echo '<input class="btn btn-primary" type="submit" name="update_user" value="Update" id="update"/>';
        
                        }else if ($_GET['change'] == "email"){
                            echo '<br /><br/>';
                            echo '<form action="profile.php" method="post">';
                            echo 'New Email: ';
                            echo '<input class="form-control" name="new_email" value=""/><br/>';
                            echo '<input class="btn btn-primary" type="submit" name="update_email" value="Update" id="update"/>';
        
                        }else if ($_GET['change'] == "password"){
                            echo '<br /><br/>';
                            echo '<form action="profile.php" method="post">';
                            echo 'Password: ';
                            echo '<input class="form-control" name="password" value="" type="password" required/><br />';
                            echo 'New Password: ';
                            echo '<input class="form-control" name="new_pass" value="" type="password" id="pass" required/><br/>';
                            echo 'Retype New Password: ';
                            echo '<input class="form-control" name="new_pass2" value="" type="password" id="pass2" required onfocusout="varpass()"/><br />';
                            echo '<input class="btn btn-primary" type="submit" name="update_pass" value="Update" id="update"/>';
        
                        }else{
                            //die("11111");
                            //header('Location: home.php');
                        }
                    }else{
                        //die("66666");
                        //header('Location: home.php');
                    }
                ?>
    
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                <!--sidebar-->
              
            </div>
        </div>
    </div>

    <nav class="navbar navbar-light bg-light">
        <div class="container">
            
        </div>
    </nav>
    <script src="jquery-3.3.1.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <script>
        function varpass(){
        var pass = document.getElementById("pass");
        var pass2 = document.getElementById("pass2");
        if ((pass.value != pass2.value))
        {
            pass2.style.borderColor = "red";
            pass2.value = "";
        }
        else if (pass2.value == "" || pass.value == "")
            pass2.style.borderColor = "red";
        else
        {
            pass2.style.borderColor = "green";
            pass.style.borderColor = "green";
        }
        };
    </script>
    <script src="js/notification.js"></script>
    <footer>
            <i>&copy; mmthombe</i>
                Matcha
    </footer>
</body>
</html>

