<?php
// Filename: shopindex.php (ehemals suchmaschinen.php)
//
// Modul: PhPeppershop Suchmaschinenoptimierung
//
// Autoren: Jose Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
//
// Zweck: Macht eine virtuelle statische Aufbereitung aller Artikel fuer die Suchmaschinen (DC)
//
// Sicherheitsstatus:        *** USER ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: shopindex.php,v 1.4 2003/08/11 14:08:14 glanzret Exp $
//
// -----------------------------------------------------------------------
// Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
// wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
$suchmaschinen = true; // historischer Name - entspricht ausnahmsweise nicht dem Filenamen

// include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
// Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
// Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

// Einbinden der benoetigten Module (PHP-Scripts)
// Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

// Server-Variablen 'entpacken'
extract($_SERVER);

// Suchmaschinen-Settings
$revisit_days = 5;     // Revisit after .. days
$strlen_beschr = 250;  // Anzahl Zeichen, die in die Meta-Tags Beschreibung geschrieben werden
$delay_time = 100;     // Delay-Time fuer Redirect in ms
$scriptname = "shopindex.php"; // Dateiname dieser Datei

// --- Ermitteln, ob der Shop im Haendlermodus laeft, wenn ja, Suchmaschinenfunktionalitaet ausschalten
$haendlersettings = getHaendlermodus();
$haendlermodus = $haendlersettings[0];

// --- Auswertung des REQUEST_URI ---
$artikel = "";
$req_uri = $REQUEST_URI;

// Trailing-Slash entfernen
if ($REQUEST_URI[strlen($REQUEST_URI)-1] == '/'){
    $req_uri = substr($REQUEST_URI,0,strlen($REQUEST_URI)-1);
}

// Artikel-ID extrahieren
$letzter_slash = strrpos($req_uri,'/');
$artikel_filename = substr($req_uri,$letzter_slash+1);
$artikel_split = split("\.",$artikel_filename);
$artikel = intval($artikel_split[0]);

// URI bis zum Scriptnamen ermitteln
$scriptpos = strpos($REQUEST_URI, $scriptname);
$base_path = substr($REQUEST_URI,0,$scriptpos).$scriptname."/";

$shopname = strip_tags(getShopname());


// --- ermitteln, was angezeigt werden soll
if (!empty($artikel) && $artikel != $scriptname){
    $darstellen = "artikel";
}
else if ($artikel_filename == "shopinfo.html"){
    $darstellen = "shopinfo";
}
else {
    $darstellen = "liste";
}

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
echo "<html> \n";
echo "  <head> \n";

// -----------------------------------------------------------------------
// darstellen = artikel
// Detailinfoseite eines Artikels anzeigen
// -----------------------------------------------------------------------
if ($darstellen == "artikel"){

    $my_artikel = getArtikel($artikel);
    if (!empty($my_artikel->name) && ($haendlermodus == 'N')){

        // Kategorien-ID's des Artikels auslesen
        $kategorie_ids =getKategorieID_eines_Artikels($artikel);

              // Artikel nur ausgeben, wenn nicht in der Kategorie Nichtzugeordnet
        $anzeigen = true;
        if (count($kategorie_ids < 2)){
            // Kategorie-ID der Kategorie Nichtzugerodnet auslesen
            $nichtzugeordnet_id = get_kat_id_nichtzugeordnet();
            if ($kategorie_ids[0] == $nichtzugeordnet_id || empty($kategorie_ids[0])){
                $anzeigen = false;
                $darstellen = "liste";
            }
        } // end of if

        if ($anzeigen){
            $shoplink = "../../index.php?Kategorie_ID=$kategorie_ids[0]&Artikel_ID=".$artikel;

            echo "    <meta name=\"robots\" content=\"index\">\n";
            echo "    <meta name=\"robots\" content=\"nofollow\">\n";
            echo "    <meta name=\"revisit-after\" content=\"".$revisit_days." days\">\n";
            echo "    <meta http-equiv=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">\n";
            echo "    <meta http-equiv=\"language\" CONTENT=\"de\">\n";
            echo "    <meta http-equiv=\"author\" CONTENT=\"Jos&eacute; Fontanil and Reto Glanzmann\">\n";

            // Meta-Angaben nach Dublin-Core
            echo "    <meta name=\"DC.Title\" content=\"".ereg_replace("\"","'",$shopname)." / ".ereg_replace("\"","'",strip_tags($my_artikel->name))."\"> \n";
            echo "    <meta name=\"DC.Creator\" content=\"PhPepperShop - searchoptimizer\"> \n";
            echo "    <meta name=\"DC.Subject\" content=\"".ereg_replace("\"","'",ereg_replace(" ",",",$my_artikel->name))."\"> \n";
            echo "    <meta name=\"DC.Description\"\n";
            echo "      content=\"".ereg_replace("\"","'",(substr(strip_tags($my_artikel->beschreibung),0,$strlen_beschr)))."\"> \n";
            echo "    <meta name=\"DC.Publisher\" content=\"PhPepperShop\"> \n";
            echo "    <meta name=\"DC.Contributor\" content=\"Jos&eacute; Fontanil and Reto Glanzmann\"> \n";
            echo "    <meta name=\"DC.Date\" content=\"".date("Y-m-d")."\"> \n";
            echo "    <meta name=\"DC.Type\" content=\"Text\"> \n";
            echo "    <meta name=\"DC.Format\" content=\"text/html\"> \n";
            echo "    <meta name=\"DC.Source\" content=\"".ereg_replace("\"","'",$shopname)."\"> \n";
            echo "    <meta name=\"DC.Language\" content=\"de\"> \n";
            echo "    <meta name=\"DC.Relation\" content=\"Artikel-Detailansicht\"> \n";
            echo "    <meta name=\"DC.Coverage\" content=\"CH, DE, AT\"> \n";
            echo "    <meta name=\"DC.Rights\" content=\"Alle Rechte liegen bei '".ereg_replace("\"","'",$shopname)."'\"> \n";

            echo "    <title>".$shopname." / ".strip_tags($my_artikel->name)."</title> \n";

            // Die Suchmaschinen bestrafen zum Teil Seiten, die Redirects auf andere Seiten machen, mit schlechten Rankings (was in vielen Faellen
            // auch sinnvoll ist). Da wir hier aber den Artikel wahrheitsgetreu fuer Suchmaschinen abbilden, wollen wir diese Bestrafung verhindert
            // unsere Weiterleitungsfunktion wird deshalb etwas verschleiert.
            echo "    <script language=\"JavaScript\" type=\"text/javascript\">\n";
            echo "    <!--\n";
            echo "    function umkehr (d) {\n";
            echo "        var a = \"\";\n";
            echo "        for(var i = 0; i < d.length; i++) {\n";
            echo "            a = d.charAt(i) + a;\n";
            echo "        }\n";
            echo "        return a;\n";
            echo "    }\n";
            echo "    function redir_delay () {\n";
            echo "        setTimeout(\"redir()\",".$delay_time.");\n";
            echo "    }\n";
            echo "    function redir () {\n";
            echo "        var b = \"".strrev("window.location.href")."\";\n";
            echo "        var c = \"".strrev($shoplink)."\";\n";
            echo "        eval(umkehr(b)+' = \"'+umkehr(c)+'\"');\n";
            echo "    }\n";
            echo "    //-->\n";
            echo "    </script>\n";

            echo "  </head>\n";
            echo "  <body onLoad=\"redir_delay()\"> \n";


            echo "<code>\n";
            echo "<h1>".$my_artikel->name."</h1>\n";
            echo "<h2>".$my_artikel->beschreibung."</h2><br>\n\n";

            // Variationen und Variationsgruppen aus Datenabank einlesen
            $myvariationen = $my_artikel->getallvariationen();
            $myvariationsgruppen = $my_artikel->getallvar_gruppe();

            // Anzahl Variationen zaehlen
            $varcount = count($myvariationen);

            // Die höchste Variationsgruppe bestimmen, die in diesem Artikel verwendet wird
            $grpcount = 1;
            foreach ($my_artikel->variationen_gruppe as $gruppe){
                if ($gruppe > $grpcount){ $grpcount = $gruppe; }
            } // end of foreach
            foreach ($myvariationsgruppen as $gruppe){
                if ($gruppe > $grpcount){ $grpcount = $gruppe; }
            } // end of foreach

            // --- Alle Variationsgruppen abarbeiten
            $var_gruppen_nr = 0;
            $erste_gruppe = true;
            for ($grp=1; $grp<=$grpcount; $grp++){
                $var_grp = array();
                // alle Variationen bestimmen, die zu der aktuellen Variationsgruppe gehören
                foreach ($myvariationen as $keyname => $value){
                    // wenn eine Variation der Gruppe 0 zugeordnet ist, handelt es sich um einen fehler
                    // oder der Shop wurde upgedated. Da die Variationsgruppe 0 nicht existiert, werden
                    // die Variationen der Variationsgruppe 1 zugeordnet
                    if($myvariationsgruppen[$keyname] == 0){
                        $myvariationsgruppen[$keyname] = 1;
                    } // end of if
                    // die Variationen, welche zu dieser Variationsgruppe in einen assoziativen Array schreiben
                    if ($myvariationsgruppen[$keyname] == $grp){
                        $var_grp[$keyname] = $value;
                    } // end of if
                } // end of foreach

                // wenn die Varaitionsgruppe mehr als eine Variation beinhaltet, wird sie in der
                // gewünschten Form (dropdown-radio)ausgegeben
                if (count($var_grp) > 1){
                    // falls zu der ersten Variantengruppe keine Beschreibung existiert, wird sie mit
                    // dem String "Varianten" belegt (Rückwärtskompatibilität)
                    if ($grp == 1 && urldecode($my_artikel->var_gruppen_text[1]) == ""){
                        $my_artikel->var_gruppen_text[1] = "Varianten";
                    } // end of if

                    // Variationsgruppen-Ueberschrift ausgeben
                    echo "<b>".urldecode($my_artikel->var_gruppen_text[$grp])."</b><br>\n";

                    foreach ($var_grp as $keyname_var => $value_var){
                        echo "- ".$keyname_var."<br>\n";
                    }
                }
            } // end of for

            // --- Optionen einfuegen
            $myoptionen = $my_artikel->getalloptionen();
            $temp = key($myoptionen);
            // Falls Artikeloptionen vorhanden...
            if(!empty($temp)){
                echo "\n<b>Optionen</b><br>\n";

                // Fuer jede Artikeloption
                foreach($myoptionen as $keyname => $value){
                    echo "- $keyname<br>\n";
                } // End of foreach Optionen
            } // End of if not empty Optionen

            // --- Ausgeben, in welchen Kategorien der Artikel enthalten ist
            if ($kategorie_ids > 1){
                echo "<br><b>Artikel ist in den Kategorien:</b><br>\n";
            }
            else{
                echo "<br><b>Artikel ist in der Kategorie:</b><br>\n";
            }
            foreach ($kategorie_ids as $kat_id){
                $my_kategorie = getKategorie($kat_id);
                echo "- ";
                if (!empty($my_kategorie->Unterkategorie_von)) {
                    echo $my_kategorie->Unterkategorie_von." -> ";
                }
                echo $my_kategorie->Name."<br>\n";
            } // end of foreach

            echo "<br><a href=\"".$shoplink."\">Diesen Artikel im Shop anzeigen..</a><br>";


            echo "</code>\n";
        } // end of if anzeigen
    } // end of if
    else {
        $darstellen = "liste";
    }
} // end of if darstellen = artikel


// -----------------------------------------------------------------------
// darstellen = liste
// Eine Linkliste mit allen Artikel-ID's ausgeben
// -----------------------------------------------------------------------
if ($darstellen == "liste"){

    // deaktiviert, da google zum teil die follow-direktive nicht beachtet, wenn noindex gesetzt ist
    // echo "    <meta name=\"robots\" content=\"noindex\">\n";
    echo "    <meta name=\"robots\" content=\"follow\">\n";
    echo "    <meta name=\"revisit-after\" content=\"".$revisit_days." days\">\n";
    echo "    <meta http-equiv=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">\n";
    echo "    <meta http-equiv=\"language\" CONTENT=\"de\">\n";
    echo "    <meta http-equiv=\"author\" CONTENT=\"Jos&eacute; Fontanil and Reto Glanzmann\">\n";

    // Meta-Angaben nach Dublin-Core
    echo "    <meta name=\"DC.Title\" content=\"".ereg_replace("\"","'",$shopname)."\"> \n";
    echo "    <meta name=\"DC.Creator\" content=\"PhPepperShop - searchoptimizer\"> \n";
    echo "    <meta name=\"DC.Subject\" content=\"Artikelindexierung f&uuml;r Suchmaschinen\"> \n";
    echo "    <meta name=\"DC.Description\"\n";
    echo "      content=\"Artikelindexierung f&uuml;r Suchmaschinen\"> \n";
    echo "    <meta name=\"DC.Publisher\" content=\"PhPepperShop\"> \n";
    echo "    <meta name=\"DC.Contributor\" content=\"Jos&eacute; Fontanil and Reto Glanzmann\"> \n";
    echo "    <meta name=\"DC.Date\" content=\"".date("Y-m-d")."\"> \n";
    echo "    <meta name=\"DC.Type\" content=\"Text\"> \n";
    echo "    <meta name=\"DC.Format\" content=\"text/html\"> \n";
    echo "    <meta name=\"DC.Source\" content=\"".ereg_replace("\"","'",$shopname)."\"> \n";
    echo "    <meta name=\"DC.Language\" content=\"de\"> \n";
    echo "    <meta name=\"DC.Relation\" content=\"Artikel-Detailansicht\"> \n";
    echo "    <meta name=\"DC.Coverage\" content=\"CH, DE, AT\"> \n";
    echo "    <meta name=\"DC.Rights\" content=\"Alle Rechte liegen bei '".ereg_replace("\"","'",$shopname)."'\"> \n";

    echo "    <title>".$shopname." / Artikelindexierung f&uuml;r Suchmaschinen</title> \n";

    echo "  </head>\n";
    echo "  <body> \n";
    echo "  <code> \n";

    echo "<a href=\"../index.php\">Hier geht es zum Shop..</a><br><br>\n";

    // ---- alle Artikel-ID's auslesen
    $artikelliste = get_alle_artikel_id(false);
    // Linkliste nur ausgeben, wenn haendlermodus ausgeschaltet
    if ($haendlermodus == 'N'){
        foreach($artikelliste as $artikel_id){
            echo "<a href=\"".$base_path.$artikel_id.".html\">".$artikel_id."</a>&nbsp;\n";
        }
    }
    // Link fuer Shopinfo ausgeben
    echo "<a href=\"".$base_path."shopinfo.html\">Shopinfo</a>&nbsp;\n";
    echo "</code> \n";

} // end of if darstellen = liste

// -----------------------------------------------------------------------
// darstellen = shopinfo
// Shopinformationen ausgeben
// -----------------------------------------------------------------------
if ($darstellen == "shopinfo"){
        echo "    <meta name=\"robots\" content=\"index\">\n";
        echo "    <meta name=\"robots\" content=\"follow\">\n";
        echo "    <meta name=\"revisit-after\" content=\"".$revisit_days." days\">\n";
        echo "    <meta http-equiv=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">\n";
        echo "    <meta http-equiv=\"language\" CONTENT=\"de\">\n";
        echo "    <meta http-equiv=\"author\" CONTENT=\"Jos&eacute; Fontanil and Reto Glanzmann\">\n";

        // Meta-Angaben nach Dublin-Core
        echo "    <meta name=\"DC.Title\" content=\"".$shopname." / PhPepperShop\"> \n";
        echo "    <meta name=\"DC.Creator\" content=\"PhPepperShop - searchoptimizer\"> \n";
        echo "    <meta name=\"DC.Subject\" content=\"".$shopname." / PhPepperShop Shopinfo\"> \n";
        echo "    <meta name=\"DC.Description\"\n";
        echo "      content=\"PhPepperShop Shopsoftware - phpeppershopbyglarotech\"> \n";
        echo "    <meta name=\"DC.Publisher\" content=\"PhPepperShop\"> \n";
        echo "    <meta name=\"DC.Contributor\" content=\"Jos&eacute; Fontanil and Reto Glanzmann\"> \n";
        echo "    <meta name=\"DC.Date\" content=\"".date("Y-m-d")."\"> \n";
        echo "    <meta name=\"DC.Type\" content=\"Text\"> \n";
        echo "    <meta name=\"DC.Format\" content=\"text/html\"> \n";
        echo "    <meta name=\"DC.Source\" content=\"".$shopname."\"> \n";
        echo "    <meta name=\"DC.Language\" content=\"de\"> \n";
        echo "    <meta name=\"DC.Relation\" content=\"Shopinfo\"> \n";
        echo "    <meta name=\"DC.Coverage\" content=\"CH, DE, AT\"> \n";
        echo "    <meta name=\"DC.Rights\" content=\"Alle Rechte liegen bei '".$shopname."'\"> \n";

        echo "    <title>".$shopname." / phpeppershopbyglarotech</title> \n";

        echo "    <script language=\"JavaScript\" type=\"text/javascript\">\n";
        echo "    <!--\n";
        echo "    function redir_delay () {\n";
        echo "        setTimeout(\"redir()\",".$delay_time.");\n";
        echo "    }\n";
        echo "    function redir () {\n";
        echo "        window.location.href = \"http://www.phpeppershop.com/\";\n";
        echo "    }\n";
        echo "    //-->\n";
        echo "    </script>\n";

        echo "  </head>\n";
        echo "  <body onLoad=\"redir_delay()\"> \n";


        echo "<code>\n";
        echo "<h1>Shopname...: ".$shopname."</h1><br>\n";
        echo "<h2>Shopsystem.: PhPepperShop - phpeppershopbyglarotech</h2>\n";
        echo "<h3>Shopversion: ".getshopversion()."</h3><br>\n";

        echo "weitere Informationen zum PhPepperShop Webshopsystem: <br>\n";
        echo "<a href=\"http://www.phpeppershop.com/\">PhPepperShop (http://www.phpeppershop.com/)</a><br>\n";
        echo "<a href=\"http://www.glaro.ch/\">Glarotech (http://www.glaro.ch/)</a><br>\n";
        echo "</code>\n";
} // end of if darstellen = shopinfo


// HTML-Footer
echo "  </body>\n";
echo "</html>\n";


?>
