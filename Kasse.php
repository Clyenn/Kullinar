<!DOCTYPE html>
<html lang="de" xml:lang="de">
<head>
		<meta charset="UTF-8">
	<meta name="google-site-verification" content="f5CChKf1hAOzx_5WSQq-Itrmy73OHsPN4o40g0eQ5Mw" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
		<link rel="icon" sizes="16x16" href="Images/favicon-16x16.png">
	<link rel="icon" sizes="32x32" href="Images/favicon-32x32.png">
	<link rel="icon" sizes="192x192" href="Images/android-chrome-192x192.png">
	<link rel="icon" sizes="512x512" href="Images/android-chrome-512x512.png">
	<link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/Kasse.css">
	<title>Kullinar</title>
</head>
<body>
<?php
session_start();
require("mysql.php")
?>

	<nav id="nav">
		<img id="logo"	 src="Images/Logo.svg">
		<ul>
            <li><a <?php if($_SESSION["login"] == false){echo "href='login.php'";}else{echo "href='logout.php'";}?>><?php if($_SESSION["login"] == false){echo "Login";}else{echo "Abmelden";}?></a></li>
			<li><a href="index.php#Getränke">Getränke</a></li>
		  	<li><a href="index.php#Map">Map</a></li>
		  	<li><a href="index.php#Delikatessen">Delikatessen</a></li>

		</ul>
	</nav>
  <?php


   $UserId = $_SESSION['UserId'];



  $stmt = $mysql->prepare("SELECT * FROM personen WHERE KundenNummer=". $UserId ." ");
  $stmt->execute();
  $UserData = $stmt->fetch();

    ?>

   


  <form action="Kassenbeleg.php" method="post">

   <h1>Deine Lieferadresse</h1>
    <input type="text" name="vorname" placeholder="Vorname" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Vorname'];}?>" required><br>
    <input type="text" name="nachname" placeholder="Nachname" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Nachname'];}?>"  required><br>
    <input type="text" name="strasse" placeholder="Straße" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Strasse'];}?>" required><br>
    <input type="text" name="hausnummer" placeholder="Hausnummer" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Hausnummer'];}?>" required><br>
    <input type="text" name="postleitzahl" placeholder="Postleitzahl" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Postleitzahl'];}?>" required><br>
    <input type="text" name="stadt" placeholder="Stadt" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Stadt'];}?>" required><br>

    <?php



    ?>


    <h1>Bei Ruckfragen</h1>

    <input type="text" name="telefon" placeholder="Telefon" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Telefon'];}?>" required><br>
    <input type="email" name="email" placeholder="Email" value="<?php if ($_SESSION['login'] == true){ echo $UserData['Email'];}?>" required><br>

    <textarea name="Comment" id="styled" placeholder="Ihre Nachticht an uns..." onfocus="this.value=''; setbg('#4C241A');"onblur="setbg('#4C241A')"></textarea>

    <button type="submit" name="submit">Bestellen</button>




  </form>



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
