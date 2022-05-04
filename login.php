<!DOCTYPE html>
<html lang="de" xml:lang="de">
<head>
		<meta charset="UTF-8">
	<meta name="google-site-verification" content="f5CChKf1hAOzx_5WSQq-Itrmy73OHsPN4o40g0eQ5Mw" />
	<meta name="description" content="Certa International Forwarding GmbH bietet das komplette Leistungsspektrum innerhalb der Transportkette per See, Luft, Straße & Bahn inklusive aller vor- und nachgelagerten Aufgaben.">
	<meta name="keywords" content="cif,cif-gmbH,Certa International Forwarding,Delivery Company,vehicle,business,deliveries,profits">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
		<link rel="icon" sizes="16x16" href="Images/favicon-16x16.png">
	<link rel="icon" sizes="32x32" href="Images/favicon-32x32.png">
	<link rel="icon" sizes="192x192" href="Images/android-chrome-192x192.png">
	<link rel="icon" sizes="512x512" href="Images/android-chrome-512x512.png">
	<link rel="stylesheet" href="css/styles.css">
  	<link rel="stylesheet" href="css/login.css">
	<title>Kullinar</title>
</head>
<body>

	<nav id="nav">
		<img id="logo"	 src="Images/Logo.svg">
		<ul>
			<li><a href="login.php">Login</a></li>
			<li><a href="index.php#Getränke">Getränke</a></li>
		  	<li><a href="index.php#Map">Map</a></li>
		  	<li><a href="index.php#Delikatessen">Delikatessen</a></li>

		</ul>
	</nav>

<?php
session_start();

if(isset($_POST["submit"])){
  require("mysql.php");
  $stmt = $mysql->prepare("SELECT * FROM personen WHERE Email = :email");
  $stmt->bindParam(":email", $_POST["email"]);
  $stmt->execute();
  $count = $stmt->rowCount();
  if($count == 1){

    $row = $stmt->fetch();
    if(password_verify($_POST["pw"], $row["Passwort"])){
      $_SESSION["email"] = $row["Email"];
      $_SESSION["login"] = true;
    if($_SESSION["login"] == true){
      header("Location: index.php");
    }
    } else {
      echo "Der Login ist fehlgeschlagen";
    }
  } else {
    echo "Der Login ist fehlgeschlagen";
  }
}
 ?>
<h1 style="margin-top: 100px; margin-left: 200px;">Anmelden</h1>
<form action="login.php" method="post">
  <input type="text" name="email" placeholder="Email" required><br>
  <input type="password" name="pw" placeholder="Passwort" required><br>
  <button type="submit" name="submit">Einloggen</button>
</form>
<br>
<a href="register.php">Noch keinen Account?</a>



	<footer>
		<div class="social">

			<div class="nav2">
			<ul>
			<li><a href="#Getränke">Getränke</a></li>
		  	<li><a href="#Map">Map</a></li>
		  	<li><a href="#Delikatessen">Delikatessen</a></li>
		  	<li><a href="#Home">Home</a></li>
			</ul>
			</div>
		</div>
	</footer>
</body>


</html>
