<?php
  // Filename: USER_BESTELLUNG_AUFRUF.php
  //
  // Modul: Aufruf-Module - Warenkorb relevante Funktionalitaet
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Ueber dieses Modul werden die einzelnen Daten zur Darstellung geholt
  //
  // Sicherheitsstatus:                     *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: USER_BESTELLUNG_AUFRUF.php,v 1.39 2003/06/15 21:21:10 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_BESTELLUNG_AUFRUF = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Wenn der Haendlermodus aktiviert wurde (alle Kunden muessen sich zuerst einloggen), dann ueberprueft folgender Link,
  // ob man schon eingeloggt ist
  require('USER_AUTH.php');

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($session_mgmt)) {include("session_mgmt.php");}
  if (!isset($bestellung_def)) {include("bestellung_def.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}
  if (!isset($USER_BESTELLUNG_DARSTELLUNG)) {include("USER_BESTELLUNG_DARSTELLUNG.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

// -----------------------------------------------------------------------
// Darstellung des HTML-"Headers"
?>
<html>
  <head>
    <meta HTTP-EQUIV="content-type" content="text/html;charset=iso-8859-1">
    <meta HTTP-EQUIV="language" content="de">
    <meta HTTP-EQUIV="author" content="Jose Fontanil & Reto Glanzmann">
    <meta name="robots" content="all">
    <link rel="stylesheet" href="shopstyles.css" type="text/css">
    <title>Shop</title>
  </head>
<?php

// -----------------------------------------------------------------------
// In den Formularen weiter unten hat man ein hidden-field, welches den Wert der
// Variable $darstellen uebermittelt. Dadurch wird entschieden welche Funktion
// oder Darstellung benutzt werden soll.

// -----------------------------------------------------------------------
// Anzeige des Warenkorbs im Content-Frame
// -----------------------------------------------------------------------
if ($darstellen == 1) {

    // Test ob Cookies eingeschaltet sind:
    checkifCookiesenabled("","",1,0);

    // Wenn ich noch keine Bestellung zugewiesen bekommen habe
    // so wird eine neue Bestellung mit meiner Session_ID eroeffnet
    test_create_Bestellung(session_id());

    // Bestellung aus DB laden
    $myBestellung = getBestellung(session_id());

    // Wenn die Bestellung keine Artikel enthaelt, soll eine Meldung ausgegeben werden,
    // anstatt den Warenkorb
    if ($myBestellung->artikelanzahl() == 0) {
        // 'Warenkorb ist leer' - Meldung ausgeben
        echo '<body class="content"><BR>'."\n";
        echo "<P><h3 class='content'><center>Ihr Warenkorb ist leer!</center></h3></P><BR>\n";
        echo '<h4 class="content"><center>Bitte w&auml;hlen Sie zuerst einen Artikel</center></h4>'."\n";
    }
    else {
        echo '<body class="content">'."\n";
        echo "<h3 class='content'>Inhalt Ihres Warenkorbs:</h3>\n";
        // Inhalt des Warenkorbs an Browser ausgeben, mit Loeschmoeglichkeit aber nicht als Admin
        darstellenBestellung($myBestellung, true, false); //Funktionsdefinition siehe USER_BESTELLUNG_DARSTELLUNG.php
        echo '<center><p><a class="content" href="USER_BESTELLUNG_1.php?darstellen=1&amp;'.session_name().'='.session_id().'" target="content"><img src="Buttons/bt_zur_kasse_1.gif" border="0" alt="Zur Kasse gehen" title="Zur Kasse gehen"></a></p></center>'."\n";
    }
}

// -----------------------------------------------------------------------
// Einen Artikel in die eigene Bestellung legen
// -----------------------------------------------------------------------
else if($darstellen == 2) {
    // Test ob Cookies eingeschaltet sind:
    checkifCookiesenabled("","",1,1);

    // Wenn ich noch keine Bestellung zugewiesen bekommen habe
    // so wird eine neue Bestellung mit meiner Session_ID eroeffnet
    test_create_Bestellung(session_id());

    // Zusammenfassen der Daten in ein Artikel_info-Objekt (siehe bestellung_def.php)
    $myArtikel_info = new Artikel_info; //Objekt-Instantierung
    $myArtikel_info->Artikel_ID = $Artikel_ID;
    $myArtikel_info->Anzahl = $Anzahl;

    // Sobald die Bestellungsdaten persisten gespeichert werden sollen, auskommentieren:
    // Aus Sicherheitsgruenden, werden alle weiteren Daten direkt aus der Datenbank ausgelesen. Das kosten zwar (ein klein wenig) Performance
    // dafuer werden keine Sensitiven Daten von einem Formular ausgewertet (koennten gefaelscht sein)
    $tempartikel = getArtikel($Artikel_ID);
    $myArtikel_info->Name = addslashes($tempartikel->name);
    $myArtikel_info->Artikel_Nr = addslashes($tempartikel->artikel_Nr);
    $myArtikel_info->Preis = addslashes($tempartikel->preis);
    $myArtikel_info->Gewicht = addslashes($tempartikel->gewicht);

    // Artikel-Stammdaten aus Datenbank auslesen (fuer die Beschreibungen der Zusatzfelder)
    $myArtikel = getArtikel($Artikel_ID);

    // allfaellig vorhandene Zusatztexte zu dem Artikel_info-Objekt hinzufuegen
    if (isset($Zusatzfeld)){
        $counter = 0;
        foreach ($Zusatzfeld as $Zusatztext){
            $feld_text = zusatzfeld_beschreibung(addslashes($myArtikel->zusatzfelder_text[$counter]));
            $myArtikel_info->Zusatzfelder[$counter] = $feld_text[vor].' ';
            $myArtikel_info->Zusatzfelder[$counter].= $Zusatztext.' ';
            $myArtikel_info->Zusatzfelder[$counter].= $feld_text[nach];
            $counter++;
        } // end of foreach
    } // end of if

    $myArtikel = get_var_opt_preise($Artikel_ID);

    // Jetzt suchen wir die Optionen (falls vorhanden)
    // die vom Benutzer angewaehlt wurden und speichern
    // die Werte danach in einem Artikel_info-Objekt ab
    foreach(($myArtikel->getalloptionen()) as $key => $val) {
        $counter=1;
        $Optionsname = "Option1";
        for($i=0;$i < $myArtikel->optionenanzahl();$i++){
            if(urldecode($$Optionsname) == $key){
                $myArtikel_info->putoption(addslashes(urldecode($$Optionsname)), $val);
            }
            $counter++;
            $Optionsname = "Option$counter";
        }
    }
    // Dasselbe fuer die Variationen (falls gewaehlt)
    foreach(($myArtikel->getallvariationen()) as $keyname => $value) {
        $counter=1;
        for($i=0;$i<count($Variation);$i++){
            if(urldecode($Variation[$i]) == $keyname){
                $myArtikel_info->putvariation(urldecode(addslashes($Variation[$i])), $value);
            } // end of if
        } // end of for
    } // end of foreach
    echo '<body class="content">'."\n";

    // Securitycheck, ob Artikel nicht in der Kategorie Nichtzugeordnet liegt.
    // Wenn doch, eine Fehlermeldung ausgeben und Artikel NICHT in die Bestellung aufnehmen
    $kategorien_array = getKategorie_eines_Artikels($myArtikel_info->Artikel_ID);
    $foundflag = false; // Dieses Flag wird true, sobald der Artikel in der Kategorie Nichtzugeordnet liegt
    foreach ($kategorien_array as $key=>$value) {
        if ($key == "Nichtzugeordnet") {
            $foundflag = true;
        }
    }
    if ($foundflag == true) {
        echo "<BR><P><H3 class='content'><center>";
        echo "FEHLER: Dieser Artikel ist im Moment nicht verf&uuml;gbar</center></H3></P><BR>";
        echo "<BR><BR><center><a class='content' href='USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&amp;".session_name()."=".session_id()."&amp;Kategorie_ID=$Kategorie_ID#Ziel".$Artikel_ID."'><img src='./Buttons/bt_zurueck.gif' border='0' alt='Zur&uuml;ck' title='Zur&uuml;ck'></a>";
        echo "</body></html>";
        exit; // Programmabbruch
    }
    else {
        // Uebertragung der Artikeldaten in die Bestellung (DB)
        // Die Funktion addArtikel ist in USER_BESTELLUNG.php definiert
        addArtikel(session_id(),$myArtikel_info);
    }

    // Um den Zuruecklink zusammensetzen zu koennen, muss wegen den Unterkategorien
    // eine Fallunterscheidung vorgenommen werden
    if (urldecode($ParentKat) == "@leer@") {
        $ParentKat = "";
        $Ziel = "&amp;Kategoriename=$Kategoriename#Ziel".$Artikel_ID;
    }
    else {
        $Ziel = "&amp;Kategoriename=$Kategoriename&amp;ParentKat=$ParentKat#Ziel".$Artikel_ID;
    }
    // Kunden-Bestaetigungs-Meldung, Navigation
    echo "<BR><P><H3 class='content'><center>";
    if($Anzahl > 1){echo "Die gew&auml;hlten ";}
    else{echo "Der gew&auml;hlte ";}
    echo "Artikel wurden in ihren Warenkorb gelegt</center></H3></P><BR>";
    echo "<BR><BR><center><a class='content' href='USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&amp;".session_name()."=".session_id()."&amp;Kategorie_ID=$Kategorie_ID#Ziel".$Artikel_ID."'><img src=\"./Buttons/bt_zurueck.gif\" border=\"0\" alt=\"Zur&uuml;ck zu den Artikeln\"  title=\"Zur&uuml;ck zu den Artikeln\"></a>";
    echo "&nbsp&nbsp<a class='content' href='./USER_BESTELLUNG_AUFRUF.php?darstellen=1&amp;".session_name()."=".session_id()."'><img src=\"./Buttons/bt_warenkorb_zeigen.gif\" border=\"0\" alt=\"Warenkorb anzeigen\" title=\"Warenkorb anzeigen\"></a>\n";
    echo '&nbsp&nbsp<a class="content" href="./USER_BESTELLUNG_1.php?darstellen=1&amp;'.session_name().'='.session_id().'" target="content"><img src="Buttons/bt_zur_kasse_1.gif" border="0" alt="Zur Kasse gehen" title="Zur Kasse gehen"></a></center>'."\n";

}

// -----------------------------------------------------------------------
// Einen Artikel aus einer Bestellung loeschen
// -----------------------------------------------------------------------
else if ($darstellen == 3) {

    // Einen Artikel aus einer Bestellung loeschen
    del_B_Artikel($FK_Artikel_ID,$FK_Bestellungs_ID,$Variation,$Optionen,$Zusatztexte);

    // Kunden-Bestaetigungs-Meldung, Navigation
    echo '<BODY class="content"><BR>';
    echo "<P><H3 class='content'><CENTER>Der gewählte Artikel wurde aus Ihrem Warenkorb entfernt!</CENTER></H3></P><BR><BR><BR>";
    echo '<CENTER><a class="content" href="USER_BESTELLUNG_AUFRUF.php?darstellen=1&amp;'.session_name().'='.session_id().'" target="content"><img src="Buttons/bt_warenkorb_zeigen.gif" border="0" alt="Warenkorb anzeigen" title="Warenkorb anzeigen"></a></CENTER>'."\n";
}

// -----------------------------------------------------------------------
// Folgender Fall sollte eigentlich nie auftreten. Falls doch, wird eine
// fehlermeldung ausgegeben.
// -----------------------------------------------------------------------
else {
  echo "<h1 class='content'>fehlerhafter Aufruf!<BR><BR><a href='../index.php'><IMG src='./Buttons/bt_zurueck.gif' border='0' alt='Zur&uuml;ck' title='Zur&uuml;ck'></a></h1>";
  }

  // Footer ausgeben (bei allen Darstellungen gleich!)
  ?>
</body>
</html>

<?php
// End of file-----------------------------------------------------------------------
?>
