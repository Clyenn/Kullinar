<?php
session_start();
require("mysql.php");
$UserId = $_SESSION['UserId'];
$gesPreis = 0;


$stmt = $mysql->prepare("SELECT * FROM personen WHERE KundenNummer ='" . $UserId . "'");
$stmt->execute();
$row = $stmt->fetchAll();

$sql = $mysql->prepare("SELECT * FROM warenkorb WHERE Benutzer_id ='" . $UserId . "' AND Bestellt = 0");
$sql->execute();


$Bestellnummer = $sql->fetch()["id"];
$Comment = $_POST['Comment'];

$stmt = $mysql->prepare("SELECT * FROM warenkorb, products WHERE Benutzer_id ='" . $UserId . "' AND products.id= warenkorb.Produkt_id AND Bestellt = 0");
$stmt->execute();

$row = $stmt->fetchAll();


$config = '<!DOCTYPE HTML>
<html>
<head>
    <title>Bestellbestätigung</title>
    <meta charset="utf-8">
</head>
<body>
<div style="width: 500px;">
    <center>
        <img src="./../Images/Logo.svg">
        <p style="font-size: x-small">Kullinar</p>
        <p style="font-size: x-small">Oldenburger Str. 65</p>
    </center>
    <hr>
    <p>Bestellnummer: ' . $Bestellnummer . '</p>
    <br/>
    <p>Ihr Kommentar an uns:</p>
    <p>' . $Comment . '</p>
    <hr>
';

foreach ($row as $key) {
    $Preis = $key["Preis"] * $key["Anzahl"];
    $gesPreis += $Preis;
    $config .= "<p>" . $key['Anzahl'] . "x " . $key['Name'] . "<a style='float:right;'>" . number_format($Preis, 2) . " €</a></p>";
    $sql = "UPDATE Warenkorb SET Bestellt = 1, Bestellnummer = " . $Bestellnummer . " WHERE Produkt_id = " . $key['Produkt_id'] . " AND Benutzer_id =" . $UserId . " ";
    $statement = $mysql->prepare($sql);
    $statement->execute();

}


$config .= '
<hr>
<p style="float: left">Gesamtpreis: <p style="float: right">' . number_format($gesPreis, 2) . ' €</p></p>

<br/>
    <br/>
    <br />

    <p>Kunde: ' . $_POST['vorname'] . ' ' . $_POST['nachname'] . '</p>
    <p>Adresse: ' . $_POST['strasse'] . ' ' . $_POST['hausnummer'] . '</p>
    <p style="margin-left: 60px;">' . $_POST['postleitzahl'] . ' ' . $_POST['stadt'] . '</p>
    <p>Tel.: ' . $_POST['telefon'] . ' </p>
    <hr>
    <h2>Wir wünschen einen Guten Appetit</h2>
    <h3>Ihr Bestellung kommt leider erst an, wenn wir Eröffnen</h3>
</div>
</body>
</html>';
$temp = fopen("./Bestellungen/" . $Bestellnummer . ".html", "w");
fwrite($temp, $config);
fclose($temp);


?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Bestellbestätigung</title>
    <meta http-equiv="refresh" content="1; URL=./index.php">
</head>
<body>


<script>
    window.open('./Bestellungen/<?php echo $Bestellnummer; ?>.html', '_blank');
</script>


</body>
</html>


