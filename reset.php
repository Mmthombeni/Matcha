<?php
include_once('config/database.php');


$status = null;

if(isset($_POST['submit']))
{
    $pass = ($_POST['password']);
    
    if (($status= validate_password($pass)) === true){
        if(!empty($pass)){

            $encryppass = password_hash($pass, PASSWORD_BCRYPT);
            try
            {
                $handler = new PDO($DB_DSN . ';dbname=' . $DB_NAME, $DB_USER, $DB_PASSWORD);
                $handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $insert = $handler->prepare("UPDATE `Users` SET users.UserPassword = :pass WHERE users.Code LIKE :code");
                $insert->bindParam(':pass',$encryppass);
                $insert->bindParam(':code', $_POST['code']);
                $insert->execute();
                /*if (){
                    $code = $_GET['id'];
                    
                    header("Location: login.php");
                    exit();
                }*/
                  //  echo 'query error';
            }
            catch(PDOException $e){
                //echo "Connection Failed: " . $e->message();
            }
        }else
            $status= "feild is empty";
    }
    header ("Location: login.php");
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
      <a href="">Reset Your Password</a>
    </h2>
  </center>
  <hr>
  <form action="reset.php" method="post">
   
  <?php
            if ($status){
                echo '<div class="alert alert-info" role="alert">';
                echo $status;
                echo '</div>';
            }
        ?>
		<center>
      <div> 
      Password: <input name="password" value="" type="password" id="pass" required/><br />
      Retype Password: <input name="repasswd" value="" type="password" id="pass2" required onfocusout="varpass()"/><br />
      <input type="hidden" name="code" value="<?php echo $_GET['id']?>">
		  <button type="submit" name="submit" value="OK">Reset</button>
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
