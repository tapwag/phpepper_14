<?php
// Filename: admin_hilfe_neu.php
//
// Modul: ADMIN_HILFE
//
// Autoren: José Fontanil & Reto Glanzmann
//
// Zweck: Beinhaltet alle neuen Hilfe-Texte für ADMIN Pages.
//        Zuvor wurden die Hilfetexte in der Datenbank abgelegt.
//
// Sicherheitsstatus:        *** ADMIN ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: admin_hilfe_neu.php,v 1.5 2003/08/04 07:20:36 fontajos Exp $
//
// -----------------------------------------------------------------------
// Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
// wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
$admin_hilfe_neu = true;

// -----------------------------------------------------------------------
// include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
// Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
// Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

// Einbinden der benoetigten Module (PHP-Scripts)
if (!isset($initialize)) {include("initialize.php");}
if (!isset($USER_SQL_BEFEHLE)) {include("USER_SQL_BEFEHLE.php");}
if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

// Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
// $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
extract($_GET);
extract($_POST);
extract($_SERVER);

// -----------------------------------------------------------------------
// HTML Header ausgeben und den HTML Body oeffnen
echo "<html>\n";
echo "<head>\n";
echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
echo "  <meta http-equiv=\"content-language\" content=\"de\">\n";
echo "  <meta name=\"author\" content=\"Jos&eacute; Fontanil and Reto Glanzmann\">\n";
echo "  <title>PhPepperShop Shop-Hilfe</title>\n";
echo "  <link rel='stylesheet' href=\"shopstyles.css\" type=\"text/css\">\n";
echo "</head>\n";
echo "<body class=\"content\">\n";
// -----------------------------------------------------------------------
// Via darstellen-Variable wird festgelegt, welchen Hilfetext man sehen
// moechte.


// -----------------------------------------------------------------------
// Hilfetext zur Postfinance yellowpay Anmeldung
if ($darstellen == 1) {
    // Benoetigte Daten vorbereiten
    $ssl_ein_aus = getSSL("","",false);
    $shop_root_url = getShopRootPath(true);
    if ($ssl_ein_aus == "https://") {
        $shop_root_url = str_replace("http://",$ssl_ein_aus,$shop_root_url);
        $ssl_meldung = "<p><i>Achtung</i>: Wenn sie SSL/TLS ausschalten, so m&uuml;ssen Sie bei allen R&uuml;cksprungadressen ";
        $ssl_meldung.= "https:// durch http:// (ohne <b>s</b>) ersetzen.</p>\n";
    }
    else {
        $ssl_meldung = "<p><i>Achtung</i>: Wenn sie SSL/TLS einschalten, so m&uuml;ssen Sie bei allen R&uuml;cksprungadressen ";
        $ssl_meldung.= "http:// durch http<b>s</b>:// ersetzen.</p>\n";
    }
    $shop_root_url = preg_replace('°/[^/]*$°','/',$shop_root_url);
    $shop_url = preg_replace('°/[^/]*/$°','/',$shop_root_url);
    $success = $shop_root_url."USER_BESTELLUNG_1.php?darstellen=4&Erfolg=1";
    $failed = $shop_root_url."USER_BESTELLUNG_1.php?darstellen=4&Erfolg=0";
    $aborted = $shop_root_url."USER_BESTELLUNG_1.php?darstellen=1";
    $http_post = $shop_root_url."postfinance_payment.php";
    $email = getShopEmail();
    $shopname = getshopname();


    // Ausgabe des Hilfetextes
    echo "<h2>Ben&ouml;tigte Angaben zum Ausf&uuml;llen eines PostFinance yellowpay Antragformulars</h2>\n";
    echo "<p>Um <a href=\"http://www.postfinance.ch/yellowpay\" target=\"_new\">Postfinance yellowpay</a> zu benutzen, muss\n";
    echo "man sich anmelden, Tel. (+41) 0848 848 848 (max. 8 Rp./Min.) und ein Anmeldeformular ausf&uuml;llen. Hier\n";
    echo "muss man gewisse Angaben zum eingesetzten Shopsystem wissen. Wir geben hier die ben&ouml;tigten \n";
    echo "Daten an, so dass Sie vom einmaligen Angebot der Schweizer Post gleich profitieren k&ouml;nnen.</p>\n";
    echo "<p><b>Formulardaten:</b></p>\n";
    echo "<ul>\n";
    echo "  <li>";
    echo "    <i>Shop-URL:</i><br>$shop_url\n";
    echo "  </li>\n";
    echo "  <li>";
    echo "    <i>Name des Shops:</i><br>$shopname\n";
    echo "  </li>\n";
    echo "  <li>";
    echo "    <i>'Wie wollen Sie die Parameter nach der Zahlung zur&uuml;ck erhalten?':</i>\n";
    echo "      <ul>\n";
    echo "        <li>";
    echo "          <i>[<tt>X</tt>] Per http-post an URL:</i><br>$http_post\n";
    echo "        </li>\n";
    echo "        <li>";
    echo "          <i>[<tt>&nbsp;</tt>] Per E-Mail an:</i><br>$email\n";
    echo "        </li>\n";
    echo "      </ul>\n";
    echo "  </li>\n";
    echo "  <li>";
    echo "    <i>R&uuml;cksprungadressen:</i><br>\n";
    echo "      <ul>\n";
    echo "        <li>";
    echo "          <i>Zahlung erfolgreich:</i><br>$success\n";
    echo "        </li>\n";
    echo "        <li>";
    echo "          <i>Zahlung fehlgeschlagen:</i><br>$failed\n";
    echo "        </li>\n";
    echo "        <li>";
    echo "          <i>Zahlung abgebrochen:</i><br>$aborted\n";
    echo "        </li>\n";
    echo "      </ul>\n";
    echo "  </li>\n";
    echo "</ul>\n";
    echo $ssl_meldung;
    echo "Eingesetzte Shopsoftware: <tt>PhPepperShop, Produkt der Glarotech Idl</tt>\n";
}// End darstellen == 1

// -----------------------------------------------------------------------
// Falls vergessen wurde eine darstellen-Variable anzugeben
else {
    echo "<h2>Es wurde leider keine Angaben mitgegeben, WELCHEN Hilfetext man anschauen m&ouml;chte.</h2>\n";
}// End else darstellen

// Angaben zum schliessen und drucken des Popup-Fensters
echo '<center><p><A href="javascript:window.close();">Fenster schliessen</a>&nbsp;&nbsp;&nbsp;<a href="javascript:window.print();">Hilfe ausdrucken</a></p></center>';
echo "</body>\n";
echo "</html>\n";

// End of file------------------------------------------------------------
?>
