<?php

include_once('config/database.php');

$errors = null;
    
if(isset($_POST['submit']))
{
    $email = ft_escape_str($_POST['email']);

    if (!empty($email)){

        try
        {
            $handler = new PDO($DB_DSN . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
            $handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $select = $handler->prepare("SELECT * FROM `Users` WHERE UserEmail = :email AND Verified = TRUE");
            $select->bindparam(':email', $email);
            $select->execute();
            $userRow = $select->fetch();
            
            if($select->rowCount() > 0)
            {
                $code = $userRow['code'];

                $to=$email;
                $subject="Reset Password Code For Matcha";
                $headers = "From: MATCHA <admin@matcha.com>\r\n". 
                "MIME-Version: 1.0" . "\r\n" . 
                "Content-type: text/html; charset=UTF-8" . "\r\n";
                $body='Your Password reset code is '.$code.' Please Click On This Link
                    <a href="http://'. $_SERVER['HTTP_HOST'] .'/matcha/reset.php?id='.$code. '">reset.php?id='.$code. '</a> to reset your password. If you did not request reset password just ignore this email';
                if (mail($to,$subject,$body,$headers))
                $errors = "Activation Code Sent, Please Check Your Emails. If you don't recieve this message, please check your junk folder.";
                else
                $errors = "Code Not Sent";
            }
            else
            $errors = "invalid email";
        }
        catch(PDOException $e){
            
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
  <link rel="stylesheet" href="css/logstyle.css">

  
</head>

<body>
<div class="bg"></div>
<div class="container animated fadeIn">
  <center>
    <h2>
      <a href="">Forgot</a>
    </h2>
  </center>
  <hr>
  <form action="forgot.php" method="post">
  <?php
            if ($errors){
                echo '<div class="alert alert-info" role="alert">';
                echo $errors;
                echo '</div>';
            }
        ?>
		<center>
      <div> 
      Email:<br/>
      <input email="email" value="" name="email" required/><br />
       </div>
    <p class="a-link"><a href="login.php">Login?</a></p>
    <p class="a-link"><a href="register.php">Register?</a></p>
		  <button type="submit" name="submit" value="OK">Get Password</button>
		</center>
		<br>
	</form>
</div>
  

</body>

</html>