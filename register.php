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
  <link rel="stylesheet" href="css/register.css">
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
  if(isset($_POST["submit"])){
    require("mysql.php");
    $stmt = $mysql->prepare("SELECT * FROM personen WHERE Email = :email");
    $stmt->bindParam(":email", $_POST["email"]);
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($count == 0){
      if($_POST["pw"] === $_POST["pw2"]){

      $stmt = $mysql->prepare("INSERT INTO personen (Nachname, Vorname, Strasse, Hausnummer, Postleitzahl, Stadt, Telefon, Email, Passwort) VALUES (:nachname, :vorname, :strasse, :hausnummer, :postleitzahl, :stadt, :telefon, :email, :pw)");

        $stmt->bindParam(":nachname", $_POST["nachname"]);
        $stmt->bindParam(":vorname", $_POST["vorname"]);
        $stmt->bindParam(":strasse", $_POST["strasse"]);
        $stmt->bindParam(":hausnummer", $_POST["hausnummer"]);
        $stmt->bindParam(":postleitzahl", $_POST["postleitzahl"]);
        $stmt->bindParam(":stadt", $_POST["stadt"]);
        $stmt->bindParam(":telefon", $_POST["telefon"]);
        $stmt->bindParam(":email", $_POST["email"]);
        $hash = password_hash($_POST["pw"], PASSWORD_BCRYPT);
        $stmt->bindParam(":pw", $hash);

        $stmt->execute();
        echo "Der User wurde angelegt!";
        header("Location: login.php");




      }else {
        echo "Die Passwörter stimmen nicht überein!";
      }
    }else {
      echo "Diese Email ist bereits regestriert!";
    }
    ?>

    <?php
  }
   ?>
  <form action="register.php" method="post">
    <input type="text" name="vorname" placeholder="Vorname" required>
    <input type="text" name="nachname" placeholder="Nachname" required><br>
    <input type="text" name="strasse" placeholder="Straße" required>
    <input type="text" name="hausnummer" placeholder="Hausnummer" required><br>
    <input type="text" name="postleitzahl" placeholder="Postleitzahl" required>
    <input type="text" name="stadt" placeholder="Stadt" required><br>
    <input type="text" name="telefon" placeholder="Telefon" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="pw" placeholder="Passwort" required><br>
    <input type="password" name="pw2" placeholder="Passwort bestätigen" required><br>
    <button type="submit" name="submit">Absenden</button>
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
