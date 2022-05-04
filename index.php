<!DOCTYPE html>
<html lang="de" xml:lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="google-site-verification" content="f5CChKf1hAOzx_5WSQq-Itrmy73OHsPN4o40g0eQ5Mw"/>
    <meta name="description"
          content="Certa International Forwarding GmbH bietet das komplette Leistungsspektrum innerhalb der Transportkette per See, Luft, Straße & Bahn inklusive aller vor- und nachgelagerten Aufgaben.">
    <meta name="keywords"
          content="cif,cif-gmbH,Certa International Forwarding,Delivery Company,vehicle,business,deliveries,profits">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <link rel="icon" sizes="16x16" href="Images/favicon-16x16.png">
    <link rel="icon" sizes="32x32" href="Images/favicon-32x32.png">
    <link rel="icon" sizes="192x192" href="Images/android-chrome-192x192.png">
    <link rel="icon" sizes="512x512" href="Images/android-chrome-512x512.png">
    <link rel="stylesheet" href="css/styles.css">
    <title>Kullinar</title>
</head>
<body>
<?php

require("mysql.php");
session_start();

if (!isset($_SESSION["login"])) {
    $_SESSION["login"] = false;
}

$stmt = $mysql->prepare("SELECT * FROM personen");
$row = $stmt->fetch();


if ($_SESSION["login"] == true) {
    $sql = $mysql->prepare("SELECT * FROM personen WHERE Email = '" . $_SESSION["email"] . "'");
    $sql->execute();
    $UserId = $sql->fetch()["KundenNummer"];
} else {
    $UserId = random_int(0, time());
    if (isset($_COOKIE['UserId'])) {
        $UserId = (int)$_COOKIE['UserId'];
    }
    if (isset($_SESSION['UserId'])) {
        $UserId = (int)$_SESSION['UserId'];
    }
    setcookie('UserId', $UserId, strtotime('+30 days'));
}
$_SESSION['UserId'] = $UserId;


$date = date("Y-m-d H:i:s");
$url = $_SERVER['REQUEST_URI'];
$indexPHPPosition = strpos($url, 'index.php');
$route = substr($url, $indexPHPPosition);
$route = str_replace('index.php', '', $route);


if (strpos($route, '/cart/add/') !== false) {

    $routeParts = explode('/', $route);
    $productId = (int)$routeParts[3];

    $sql = $mysql->prepare("SELECT * FROM warenkorb WHERE Benutzer_id =".$UserId ." AND Produkt_id = ".$productId ." AND Bestellt = 0");
    $sql->execute();
    $Anzahl = $sql->fetch()["Anzahl"];



    if ($Anzahl == NULL) {
        $sql = "INSERT INTO Warenkorb SET Benutzer_id = :UserId,Produkt_id = :Produkt_id,Erstellt = :Erstellt,Anzahl = :Anzahl,Bestellt = :Bestellt";
        $statement = $mysql->prepare($sql);

        $statement->execute([
            ':UserId' => $UserId,
            ':Produkt_id' => $productId,
            ':Erstellt' => $date,
            ':Anzahl' => '1',
            ':Bestellt' => '0'

        ]);
        if ($productId > 9) {
            header("Location: /Kullinar/index.php#Getränke");
        } else {
            header("Location: /Kullinar/index.php#Delikatessen");
        }

        exit();
        
    } else {
        $sql = "UPDATE Warenkorb SET Anzahl = Anzahl + 1 WHERE Produkt_id = " . $productId . " AND Benutzer_id =" . $UserId . " AND Bestellt = 0 ";
        $statement = $mysql->prepare($sql);

        $statement->execute();
        if ($productId > 8) {
            header("Location: /Kullinar/index.php#Getränke");
        } else {
            header("Location: /Kullinar/index.php#Delikatessen");
        }
        exit();
    }


}

$cartResults = $mysql->prepare("SELECT SUM(Anzahl) FROM Warenkorb WHERE Benutzer_id =" . $UserId . " AND Bestellt = 0");
$cartResults->execute();
$cartItems = $cartResults->fetchColumn();
if ($cartItems == NULL) {
    $cartItems = '0';
}
?>
<header id="Home">
    <nav id="nav">
        <img id="logo" src="Images/Logo.svg">


        <ul>
            <li><a <?php if ($_SESSION["login"] == false) {
                    echo "href='login.php'";
                } else {
                    echo "href='logout.php'";
                } ?>><?php if ($_SESSION["login"] == false) {
                        echo "Login";
                    } else {
                        echo "Abmelden";
                    } ?></a></li>
            <li><a href="#Getränke">Getränke</a></li>
            <li><a href="#Map">Map</a></li>
            <li><a href="#Delikatessen">Delikatessen</a></li>
            <li>
                <button class="openbtn" id="openbtn" onclick="openNav()"><p>Warenkorb(<?php echo $cartItems; ?>)</p>
                </button>
            </li>


            <div id="mySidebar" class="sidebar">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>


                <div>
                    <h1 itemprop="name">Warenkorb</h1>


                    <?php
                    $gesPreis = 0;
                    $stmt = $mysql->prepare("SELECT * FROM warenkorb, products WHERE Benutzer_id ='" . $UserId . "' AND products.id= warenkorb.Produkt_id AND Bestellt = 0");
                    $stmt->execute();

                    $row = $stmt->fetchAll();

                    foreach ($row as $key) {
                        $Preis = $key['Preis'] * $key['Anzahl'];
                        $gesPreis += $Preis;

                        if ($cartItems > 0) {
                            echo "<p><a style='color:#990000; padding-right: 5px; text-decoration: none;' href='?delete&id=" . $key['Produkt_id'] . "'>X</a>" . $key['Anzahl'] . "x " . $key['Name'] . "<a style='float:right;'>" . number_format($Preis, 2) . " €</a></p>";

                        }


                        if (isset($_GET['delete']) && $key['Produkt_id'] === $_GET['id']) {

                            $stmt = $mysql->prepare("DELETE FROM warenkorb WHERE Benutzer_id =" . $UserId . " AND Produkt_id =" . $_GET['id']);
                            $stmt->execute();

                            header("Location: /Kullinar/index.php");


                        }
                    }
?>
                    <hr>
                    <?php
                    echo "<p style='float: right;'>" . number_format($gesPreis, 2) . " €</p>";


                    if ($cartItems > 0) {
                        ?>

                        <button class="bestellen" onclick="window.location.href = './Kasse.php'">Zur Kasse</button>
                        <?php
                    }
                    ?>

                </div>
        </ul>
    </nav>
    <div class="info">
        <?php

     

        if ($_SESSION["login"] == true) {
            $sql = $mysql->prepare("SELECT * FROM personen WHERE Email = '" . $_SESSION["email"] . "'");
            $sql->execute();

            echo "<h1>Hallo ";
            echo $sql->fetch()["Vorname"];


            echo "</h1>";


        } else {
            echo "<br />";


        }

        ?>

        <h1>Kullinar</h1>

        <p>Traditionell wurde Fleisch in Russland bis zum 17. Jahrhundert nur sehr wenig zum Kochen verwendet.
            Inzwischen werden in der Russischen Küche verschiedene Fleischsorten verwendet - Schweinefleisch,
            Rindfleisch, Lammfleisch, Geflügelfleisch und alle Arten von Wild (Wildschwein, Elch, Wildente, Hase).</p>
        <ul>
            <li><img src="Images/Icon 1.svg" alt=""></li>
            <li><img src="Images/Icon 2.svg" alt=""></li>
            <li><img src="Images/Icon 3.svg" alt=""></li>
            <li><img src="Images/Icon 4.svg" alt=""></li>
        </ul>
    </div>
</header>
<section class="main_items">
    <div class="second_items">
        <img src="Images/Gaina.svg" alt="">
        <h2>7</h2>
        <p>Traditionelle<br>Russische<br>Delikatessen</p>
    </div>

    <div class="second_items">
        <img src="Images/Sticla.svg" alt="">
        <h2>10</h2>
        <p>Traditionelle<br>Russische<br>Getränke</p>
    </div>

    <div class="second_items">
        <img src="Images/Magazin.svg" alt="">
        <h2>2</h2>
        <p>Russische<br>Laden<br>in Delmenhorst</p>
    </div>

    <div class="second_items">
        <img src="Images/Magazin 2.svg" alt="">
        <h2>10</h2>
        <p>Russische<br>Magazine<br>in Berlin</p>
    </div>
</section>
<section id="Delikatessen" class="Delikatessen">
    <h1>Delikatessen</h1>
    <div class="item_1">
        <div class="overlay">
            <div class="info_overlay">

                <?php
               
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sowjetischer Olivier' ");
                $sql->execute();


                echo "<h2>Sowjetischer Olivier</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sowjetischer Olivier' ");
                $sql->execute();

                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sowjetischer Olivier' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_2">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Shchi' ");
                $sql->execute();


                echo "<h2>Shchi</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Shchi' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Shchi' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_3">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Pfannkuchen' ");
                $sql->execute();


                echo "<h2>Pfannkuchen</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Pfannkuchen' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Pfannkuchen' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_4">
        <div class="overlay">
            <div class="info_overlay">
                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Okroshka' ");
                $sql->execute();


                echo "<h2>Okroshka</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Okroshka' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Okroshka' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_5">
        <div class="overlay">
            <div class="info_overlay">
                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Soljanka' ");
                $sql->execute();


                echo "<h2>Soljanka</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Soljanka' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Soljanka' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_6">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Russische Knödel' ");
                $sql->execute();


                echo "<h2>Russische Knödel</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Russische Knödel' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Russische Knödel' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_7">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sülze' ");
                $sql->execute();


                echo "<h2>Sülze</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sülze' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sülze' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_8">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Salzgurken' ");
                $sql->execute();


                echo "<h2>Salzgurken</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Salzgurken' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Salzgurken' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
</section>
<section itemscope itemtype="http://schema.org/Place">
    <div id="Map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2398.424442497172!2d8.607984915827227!3d53.048680679917716!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47b0d53cedf8c77d%3A0xc2dcf3bdc1fa04e8!2sMix%20Markt!5e0!3m2!1sde!2sde!4v1619514864014!5m2!1sde!2sde"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>

<section id="Getränke" class="Getranke">
    <h1>Getränke</h1>
    <div class="item_9">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Honigwein' ");
                $sql->execute();


                echo "<h2>Honigwein</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Honigwein' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Honigwein' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_10">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sauermilch' ");
                $sql->execute();


                echo "<h2>Sauermilch</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sauermilch' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sauermilch' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_11">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sbiten' ");
                $sql->execute();


                echo "<h2>Sbiten</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sbiten' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Sbiten' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_13">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Tee' ");
                $sql->execute();


                echo "<h2>Tee</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Tee' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Tee' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_14">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Vodka' ");
                $sql->execute();


                echo "<h2>Vodka</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Vodka' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Vodka' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_15">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Kwas' ");
                $sql->execute();


                echo "<h2>Kwas</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Kwas' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Kwas' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
    <div class="item_16">
        <div class="overlay">
            <div class="info_overlay">

                <?php

                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Kissel' ");
                $sql->execute();


                echo "<h2>Kissel</h2>";
                echo "<p>" . $sql->fetch()["Beschreibung"] . "</p>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Kissel' ");
                $sql->execute();
                echo "<a href='index.php/cart/add/" . $sql->fetch()["ID"] . "'><p>Bestellen</p></a>";
                $sql = $mysql->prepare("SELECT * FROM products WHERE Name ='Kissel' ");
                $sql->execute();
                echo "<p style='text-align: right;'>" . $sql->fetch()["Preis"] . "€</p>";
                ?>
            </div>
        </div>
    </div>
</section>
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
    <div class="quote">
        <h3>Russland hat Elemente der Kultur vieler Nationen aufgenommen, einschließlich kulinarischer Traditionen.</h3>


    </div>
</footer>
<script>
    function openNav() {
        document.getElementById("mySidebar").style.width = "400px", document.getElementById("main").style.marginLeft = "250px"
    }

    function closeNav() {
        document.getElementById("mySidebar").style.width = "0", document.getElementById("main").style.marginLeft = "0"
    }


    $(window).on("scroll", function () {
        $(window).scrollTop() ? $("nav").addClass("black") : $("nav").removeClass("black")
    });

   
    function onScroll(a) {
        var t = $(document).scrollTop();
        $("#nav a").each(function () {
            var a = $(this), o = $(a.attr("href"));
            o.position().top <= t && o.position().top + o.height() > t ? ($("#nav ul li a").removeClass("active"), a.addClass("active")) : a.removeClass("active")
        })
    }
</script>
</body>


</html>
