<?php

require_once ("classes/connectdb.php");
require_once ("config/database.php");

$errors = NULL;
$handler = NULL;

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
    if($userdata=$select->fetchAll())
        header ("Location: home.php");
}

if(isset($_POST['submit']))
{
    $username = ft_escape_str($_POST['username']);
    //$pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $pass = ($_POST['password']);

    if (!empty($username) && !empty($pass)){
        try
        {
            $select = $handler->prepare("SELECT * FROM `Users` WHERE Username = :username AND Verified = TRUE");
            $select->bindparam(':username', $username);
            $select->execute();
            $userRow = $select->fetch(PDO::FETCH_ASSOC);
            if($select->rowCount() > 0)
            {
                if(password_verify($pass, $userRow['UserPassword']))
                {
                    $_SESSION['logged-in'] = $userRow['UserID'];
                    $_SESSION['username'] = $userRow['Username'];
                }
                else
                    $errors = "Incorrect username/password";
            }
            else
                $errors = "Incorrect username/password";
        }
        catch(PDOException $e){
            echo "Connection Failed: " . $e->getMessage();
        }

        if(isset($_SESSION['logged-in']))
        {
            header("Location: home.php");
            exit();
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
      <a href="">Login</a>
    </h2>
  </center>
  <hr>
  <form action="login.php" method="post">
          <?php
            if ($errors){
                echo '<div class="alert alert-info" role="alert">';
                echo $errors;
                echo '</div>';
            }
        ?>
		<center>
      <div> 
          Username: <br />
          <input name="username" value="<?php if(isset($_POST["username"])) echo $_POST["username"]; ?>" required/><br />
          Password: <br />
          <input name="password" value="" type="password" required/><br />
       </div>
    <p class="a-link"><a href="forgot.php">Forget password?</a></p>
    <p class="a-link"><a href="register.php">Register?</a></p>
		  <button type="submit" name="submit" value="OK">Login</button>
		</center>
		<br>
	</form>
</div>
  

</body>

</html>