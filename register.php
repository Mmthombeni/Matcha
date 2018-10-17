<?php

require_once ("config/database.php");
require_once ("classes/connectdb.php");


$errors = NULL;
$handler = NULL;
$status = array();

try{
    $handler = new PDO($DB_DSN . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
    $handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection Failed: " . $e->getMessage();
}

    if(isset($_POST['submit']))
    {
        $fname = ft_escape_str($_POST['fname']);
        $lname = ft_escape_str($_POST['lname']);
        $username = ft_escape_str($_POST['username']);
        $email = ft_escape_str($_POST['email']);
        $pass = ($_POST['password']);
        $verified = FALSE;
        $code = substr(md5(mt_rand()),0,15);

        $passError = null;
        $emailError = null;
        if (($passError = validate_password($pass)) !== true)
            $status[] = $passError;
        if (($emailError = validate_email($email)) !== true)
            $status[] = $emailError;
        if (!count($status)){
            if (!empty($lname) && !empty($fname) && !empty($username) && !empty($email) && !empty($pass)){

                $encryppass = password_hash($pass, PASSWORD_BCRYPT);
                try{
                    $select = $handler->prepare("SELECT UserEmail FROM `Users` WHERE UserEmail = :email");
                    $select->bindparam(':email', $email);
                    $select->execute();
                    $userRow = $select->fetch(PDO::FETCH_ASSOC);
                    if (!$userRow){
                        $select = $handler->prepare("SELECT Username FROM `Users` WHERE Username = :username");
                        $select->bindparam(':username', $username);
                        $select->execute();
                        $userRow = $select->fetch(PDO::FETCH_ASSOC);
                        if (!$userRow){
                            $insert = $handler->prepare("INSERT INTO `Users` (UserFirstName, UserLastName, Username, UserEmail, UserPassword, code, Verified)
                            VALUES (:fname, :lname, :username, :email, :password, :code, :verified)");
                            $insert->bindParam(':fname',$fname);
                            $insert->bindParam(':lname',$lname);
                            $insert->bindParam(':username',$username);
                            $insert->bindParam(':email',$email);
                            $insert->bindParam(':password',$encryppass);
                            $insert->bindParam(':code',$code);
                            $insert->bindParam(':verified', $verified);
                            $insert->execute();
                        
                            $to=$email;
                            $subject="Activation Code For MATCHA";
                            $headers = "From: MATCHA <admin@MATCHA.com>\r\n". 
                            "MIME-Version: 1.0" . "\r\n" . 
                            "Content-type: text/html; charset=UTF-8" . "\r\n";
                            $body='Your Activation Code is '.$code.' Please Click On This Link
                                <a href="http://'. $_SERVER['HTTP_HOST'] .'/matcha/verify.php?id='.$code.'">verify.php?id='.$code. '</a> to activate your account.';
                            if (mail($to,$subject,$body,$headers)){
                                $status[] = "Activation Code Sent, Please Check Your Emails To Verify Your Account. If you don't receive this message please check your junk folder.";
                                $_POST["fname"] = "";
                                $_POST["username"] = "";
                                $_POST["email"] = "";
        
                                $status[] = "Check email to verify your account.";
                            }else
                                $status[] = "Could not send email.";
                        }
                        else
                            $status[] = "username already exist!";
                    }
                    else
                        $status[] = "email already exist!";
                }catch(PDOException $e){
                    echo "Connection Failed: " . $e->getMessage();
                }
            }
            else{
                $status[] = "Fields incomplete";
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Matcha login</title>
  <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="css/logstyle.css">

  
</head>

<body>
<div class="bg"></div>
<div class="container animated fadeIn">
  <center>
    <h2>
      <a href="">Sign-Up for MATCHA</a>
    </h2>
  </center>
  <hr>
  <form action="Register.php" method="post">
        <?php
            if ($status){
                echo '<div class="alert alert-info" role="alert">';
                foreach ($status as $stat){
                    echo $stat.'.<br>';
                }
                echo '</div>';
            }
        ?>
		<center>
      <div> 
      Frist Name:<br />
      <input name="fname" value="<?php if(isset($_POST["fname"])) echo $_POST["fname"]; ?>" /><br />
  Last Name:<br />
      <input name="lname" value="<?php if(isset($_POST["lname"])) echo $_POST["lname"]; ?>" /><br />
  Username:<br />
      <input name="username" value="<?php if(isset($_POST["username"])) echo $_POST["username"]; ?>" /><br />
  Password:<br />
      <input name="password" value="" type="password" id="pass" /><br />
  Retype Password:<br />
      <input name="repasswd" value="" type="password" id="pass2"  onfocusout="varpass()"/><br />
  Email:<br />
      <input name="email" value="<?php if(isset($_POST["email"])) echo $_POST["email"]; ?>" /><br />
       </div>
    <p class="a-link"><a href="forgot.php">Forget password?</a></p>
    <p class="a-link"><a href="login.php">login?</a></p>
		  <button type="submit" name="submit" value="OK">Register</button>
		</center>
		<br>
    </form>
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

</div>

</body>

</html>
