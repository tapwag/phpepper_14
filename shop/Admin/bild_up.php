<?php
  // Filename: bild_up.php
  //
  // Modul: PHP-Funktionen - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Ein Bild in die Datenbank laden (und Thumbnail davon erzeugen),
  //        sowie der Update eines Artikels (letzter Teil) / neuer Artikel einfuegen.
  //        Diese Funktion musste als eigene Datei ausprogrammiert werden (HTML).
  //
  // Sicherheitsstufe:                     *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: bild_up.php,v 1.43 2003/07/03 07:05:39 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $bild_up = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // include, damit wir eine DB-Connection aufbauen koennen und Zugriff auf
  // die verwendeten Funktionen haben:
  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_Database)) {include("ADMIN_initialize.php");}
  if (!isset($SHOP_ADMINISTRATION)) {include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);
  extract($_FILES);

  // -----------------------------------------------------------------------
  // Kopf der HTML-Seite (unabhaengig von dem was angezeigt werden soll)
  // inkl. eroeffnendem BODY-Tag
  echo "<html>\n<head>\n";
  echo "<title>Shop</title>\n";
  echo "<META HTTP-EQUIV=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">";
  echo "<META HTTP-EQUIV=\"Expires\" CONTENT=\"Fri, Jan 01 1900 00:00:00 GMT\">";
  echo "<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">";
  echo "<META HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache\">";
  echo "<META HTTP-EQUIV=\"language\" CONTENT=\"de\">";
  echo "<META HTTP-EQUIV=\"author\" CONTENT=\"José Fontanil & Reto Glanzmann\">";
  echo "<LINK REL=STYLESHEET HREF=\"./shopstyles.css\" TYPE=\"text/css\">";
  echo "    <script LANGUAGE=\"JavaScript\">\n";
  echo "    <!-- Begin\n";
  echo "    function popUp(URL) {\n";
  echo "        day = new Date();\n";
  echo "        id = day.getTime();\n";
  echo "        eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=720,height=520,left = 100,top = 100');\");\n";
  echo "    }\n";
  echo "    // End -->\n";
  echo "    </script>\n";
  echo "</head>\n<body>\n";

  // -----------------------------------------------------------------------
  // Damit wir Zugriff auf in anderen Modulen deklarierte Variablen haben (z.B. SQL-Queries)
  global $Admin_Database;
  global $sql_bild_up_1_1; //Teil 1 der Query 1
  global $sql_bild_up_1_2; //Teil 2 der Query 1

  // -----------------------------------------------------------------------
  // Weiche (siehe hidden-input-Feld im Formular unten), wenn SUBMIT-Button gedrueckt
  // wurde, dann wird per hidden-Field diese Variable true gesetzt und es wird hierhin gesprungen.
  // Code, der nach Ausfüllen der Formulardaten ausgeführt wird: (Nach Submit-Button)
  if ($Speichern) {
         if (! is_object($Admin_Database)) {
             die("<P><H1>b_up_Error: Datenbank nicht erreichbar!</H1></P><BR>");
         }

         // Bild von Formular einlesen (aus temporaer erstellter Datei)
         if(!($form_data == 'none')){
             $gross_temp = "../Bilder/grossbild"; // Temporaerdatei, wo grosses Bild hineingespeichert wird
             $prod_dir = "../ProdukteBilder"; // Verzeichnis, wo sich die Produktebilder befinden
             $file_gr = $Artikel_ID."_gr"; // Dateiname fuer grosses Bild
             $file_kl = $Artikel_ID."_kl"; // Dateiname fuer kleines Bild

             // ueberpruefen, ob hochgeladenes File nicht manipuliert wurde und das File dann in eine temporaere
             // Datei kopieren ($gross_temp)
             if (!move_uploaded_file($form_data['tmp_name'], $gross_temp)){
                 echo "<h4>Das Bild wurde nicht korrekt hochgeladen!</h4>";
                 die("Dr&uuml;cken Sie den Zur&uuml;ck-Button Ihres Browsers, um zur letzen Maske zu gelangen.");
             } // end of if

             // Groesse des hochgeladenen Bildes ermitteln
             if (!$groesse = getimagesize($gross_temp)){
                 echo "<h4>Die angegebene Datei existiert nicht oder ist kein g&uuml;ltiges Bildformat!</h4>";
                 die("Dr&uuml;cken Sie den Zur&uuml;ck-Button Ihres Browsers, um zur letzen Maske zu gelangen.");
             } // end of if
             $breite=$groesse[0];
             $hoehe=$groesse[1];
             $neueBreite=getThumbnail_Breite(); //Funktion in SHOP_ADMINISTRATION.php definiert
             $neueHoehe=intval($hoehe*$neueBreite/$breite);

             // falls es sich um ein gif-bild handelt
             if($groesse[2]==1) {
                 // GIF
                 $file_gr .= ".gif";
                 $file_kl .= ".gif";
                 $altesBild=ImageCreateFromGIF($gross_temp);
                 $neuesBild = resize_image($altesBild,$neueBreite,$neueHoehe,$breite,$hoehe,true); // Def. in SHOP_ADMINISTRATION.php
                 // neu erstelltes Thumbnail ins Produktebilder-Verzeichnis speichern
                 if (!ImageGIF($neuesBild,"$prod_dir/$file_kl")){
                    echo "<h4>Problem beim Speichern des Thumbnails!<br>Die Datei $prod_dir/$file_kl konnte nicht geschrieben werden!</h4>";
                 } // end of if
                 // grosses Produktbild ins Produktebilder-Verzeichnis kopieren
                 if (!copy($gross_temp,"$prod_dir/$file_gr")) {
                    echo "<h4>Kopiervorgang gescheitert!<br>$gross_temp konnte nicht in $prod_dir/$file_gr kopiert werden!</h4>";
                 } // end of if
                 @chmod("$prod_dir/$file_gr", 0666); // versuchen, die Zugriffsrechte auf 666 zu setzen, damit die Bilder
                 @chmod("$prod_dir/$file_kl", 0666); // spaeter auch via FTP bearbeitet werden koennen
             }

             // falls es sich um ein jpg-bild handelt
             else if($groesse[2]==2) {
                 // JPG
                 $file_gr .= ".jpg";
                 $file_kl .= ".jpg";
                 $altesBild=ImageCreateFromJPEG($gross_temp);
                 $neuesBild = resize_image($altesBild,$neueBreite,$neueHoehe,$breite,$hoehe); // Def. in SHOP_ADMINISTRATION.php
                 // neu erstelltes Thumbnail ins Produktebilder-Verzeichnis speichern
                 if (!ImageJPEG($neuesBild,"$prod_dir/$file_kl")){
                    echo "<h4>Problem beim Speichern des Thumbnails!<br>Die Datei $prod_dir/$file_kl konnte nicht geschrieben werden!</h4>";
                 } // end of if
                 // grosses Produktbild ins Produktebilder-Verzeichnis kopieren
                 if (!copy($gross_temp,"$prod_dir/$file_gr")) {
                    echo "<h4>Kopiervorgang gescheitert!<br>$gross_temp konnte nicht in $prod_dir/$file_gr kopiert werden!</h4>";
                 } // end of if
                 @chmod("$prod_dir/$file_gr", 0666); // versuchen, die Zugriffsrechte auf 666 zu setzen, damit die Bilder
                 @chmod("$prod_dir/$file_kl", 0666); // spaeter auch via FTP bearbeitet werden koennen

             }

             // falls es sich um ein png-bild handelt
             else if($groesse[2]==3) {
                 // PNG
                 $file_gr .= ".png";
                 $file_kl .= ".png";
                 $altesBild=ImageCreateFromPNG($gross_temp);
                 $neuesBild = resize_image($altesBild,$neueBreite,$neueHoehe,$breite,$hoehe); // Def. in SHOP_ADMINISTRATION.php
                 // neu erstelltes Thumbnail ins Produktebilder-Verzeichnis speichern
                 if (!ImagePNG($neuesBild,"$prod_dir/$file_kl")){
                    echo "<h4>Problem beim Speichern des Thumbnails!<br>Die Datei $prod_dir/$file_kl konnte nicht geschrieben werden!</h4>";
                 } // end of if
                 // grosses Produktbild ins Produktebilder-Verzeichnis kopieren
                 if (!copy($gross_temp,"$prod_dir/$file_gr")) {
                    echo "<h4>Kopiervorgang gescheitert!<br>$gross_temp konnte nicht in $prod_dir/$file_gr kopiert werden!</h4>";
                 } // end of if
                 @chmod("$prod_dir/$file_gr", 0666); // versuchen, die Zugriffsrechte auf 666 zu setzen, damit die Bilder
                 @chmod("$prod_dir/$file_kl", 0666); // spaeter auch via FTP bearbeitet werden koennen
             }

             else {
                 die("kein g&uuml;ltiges Bildformat!");
             }

             // aktuelle Bilddaten des Artikels auslesen:
             // [0] -> Bild_gross
             // [1] -> Bild_klein
             // [2] -> Bildtyp
             // [3] -> Bild_last_modified
             $bilddaten = getArtikelBilder($Artikel_ID);

             // ermitteln, ob das alte Produktbild noch von einem anderen Artikel verwendet wird
             // wenn als Resultat 1 zurückgegeben wurde, wird das Bild nur von einem (diesem) Artikel verwendet
             $anzahl = bildmehrmals($bilddaten[0]);
             if ($anzahl < 2 && $bilddaten[0] != "" && $bilddaten[0] != $file_gr ){
                 // grosses Bild löschen
                 if (!unlink ($prod_dir."/".$bilddaten[0])){
                     echo "<br><b>Das grosse Bild $bilddaten[0] konnte nicht gel&ouml;scht werden!";
                 } // end of if
                 // kleines Bild löschen
                 if (!unlink ($prod_dir."/".$bilddaten[1])){
                     echo "<br><b>Das kleine Bild $bilddaten[1] konnte nicht gel&ouml;scht werden!";
                 } // end of if
             } // end of if


             // Informationen, welche Bilder zum Artikel gehoeren, in die Datenbank speichern
             $RS = $Admin_Database->Exec("$sql_bild_up_1_1".addslashes($file_gr)."$sql_bild_up_1_2".addslashes($file_kl)."$sql_bild_up_1_3".addslashes($form_data_type)."$sql_bild_up_1_4".$Artikel_ID."$sql_bild_up_1_5");
             if ($RS) {
                 // Die Query konnte ausgefuehrt werden und es wird true fuer das geglueckte Update zurueck
                 // gemeldet
             echo "<P><H1><B>SHOP ADMINISTRATION</B></H1></P><BR>";
             echo "<P>Der Artikel konnte erfolgreich gespeichert werden</P>";
             echo "<BR><BR><a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'></a><BR>";
             }
             else {
                echo "<P><H1>b_up_Error: $RS kein Objekt, die Bildlinks konnten nicht in die Datenbank gespeichert werden!</H1></P><BR>";
                die("<P>Query: $sql_bild_up_1_1".$data."$sql_bild_up_1_2".$mini_pic."$sql_bild_up_1_3".$form_data_type."$sql_bild_up_1_4".$Artikel_ID."$sql_bild_up_1_5</P><BR>");
             }
         }
         else {
             echo "<P><H1><B>SHOP ADMINISTRATION</B></H1></P><BR>";
             echo "<P><B><H3>Fehler! Die Datei wurde nicht korrekt hochgeladen</H3></B></P>";
             echo "<BR><BR><a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'></a><BR>";
         }
  }
  // Weiche: wenn loeschen_Bild uebergeben wird (per Bild-loeschen-Button (ist ein Link), dann
  // wird hierhin gesprungen und das Bild des aktuell bearbeiteten Artikels geloescht
  else if($loeschen_Bild == 1) {
      // aktuelle Bilddaten des Artikels auslesen:
      // [0] -> Bild_gross
      // [1] -> Bild_klein
      // [2] -> Bildtyp
      // [3] -> Bild_last_modified
      $bilddaten = getArtikelBilder($Artikel_ID);
      $anzahl = bildmehrmals($bilddaten[0]);
      if ($anzahl < 2 && $bilddaten[0] != ""){
          // grosses Bild löschen
          if (!unlink ("../ProdukteBilder/".$bilddaten[0])){
              echo "<br><b>Das grosse Bild $bilddaten[0] konnte nicht gel&ouml;scht werden!";
          } // end of if
          // kleines Bild löschen
          if (!unlink ("../ProdukteBilder/".$bilddaten[1])){
              echo "<br><b>Das kleine Bild $bilddaten[1] konnte nicht gel&ouml;scht werden!";
          } // end of if
      } // end of if

      // Eintrag der Bilddaten für den Artikel löschen
      delBild($Artikel_ID); //Funktion in SHOP_ADMINISTRATION.php definiert
      echo "<P><H1><B>SHOP ADMINISTRATION</B></H1></P><BR>";
      echo "<P>Der Artikel wurde ohne Bild erfolgreich gespeichert!</P>";
      echo "<BR><BR><a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";
  }

  // Weiche: wenn auswahl_Bild uebergeben wird, dann wird hierhin gesprungen und das Bild des aktuell
  // wird geaendert (Das Bild eines anderen Artikels auswaehlen)
  else if($auswahl_Bild == 1) {
        // Informationen, welche Bilder zum Artikel gehoeren, in die Datenbank speichern
    $query = "$sql_bild_up_1_1".addslashes($file_gr)."$sql_bild_up_1_2".addslashes($file_kl)."$sql_bild_up_1_3".addslashes($form_data_type)."$sql_bild_up_1_4".$Artikel_ID."$sql_bild_up_1_5";
        $RS = $Admin_Database->Exec($query);
        if ($RS) {
            // Die Query konnte ausgefuehrt werden und es wird true fuer das geglueckte Update zurueck
            // gemeldet
            echo "<P><H1><B>SHOP ADMINISTRATION</B></H1></P><BR>";
            echo "<P>Der Artikel konnte erfolgreich gespeichert werden!</P>";
        echo "<BR><BR><a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'></a><BR>";
        } // end of if
        else {
        echo "<P><H1>b_up_Error: $RS kein Objekt, die Bildlinks konnten nicht in die Datenbank gespeichert werden!</H1></P><BR>";
        die("<P>Query: $query</P><BR>");
        } // end of else
  } // end of else if auswahl_Bild = 1

  // Weiche --> Eingabeformular anzeigen (Bild-Eingabe)
  else {
    // Um einen neuen Artikel in der DB abzuspeichern, wird er an einer neuen
    // Position (ein SQL-INSERT) eingefuegt.

    // Bevor wir den Artikel der Funktion newArtikel uebergeben koennen, muessen wir aus
    // allen Formularfeldern alle Daten in Variablen speichern. Wenn wir einen bestehenden
    // Artikel updaten, so benoetigt die Funktion upd_Artikel_2(...) dieselben Parameter wie
    // newArtikel(...), nur noch $Artikel_ID dazu. Wir gehen also analog vor.

    // Das aktuelle Datum (Datum des Servers!!!) einfuegen:
    $mydate = array();
    $mydate = getdate();
    $letzteAenderung = $mydate[year]."-".$mydate[mon]."-".$mydate[mday];// Format yyyy-mm-dd

    // Nun gibt es eine Fallunterscheidung: Entweder es handelt sich um einen neuen Artikel,
    // dann muss ein neuer Artikel ge-inserted werden, andernfalls geht es um ein Update eines
    // Artikels. Dementsprechend anders muss man vorgehen.
    // Wenn empty($Artikel_ID) true ist, so handelt es sich um einen neuen Artikel!

    $Optionenarray = array();
    $Variationsarray = array();

    // Optionen und Variationen in Arrays verpacken:
    // Optionen:
    $opt_counter = 1;
    $Optionsname = "Option$opt_counter";
    $Preisname = "Preisdifferenz$opt_counter";
    while (isset($$Optionsname)) {
        $Optionsname = "Option$opt_counter";
        $Preisname = "Preisdifferenz$opt_counter";
        $Optionenarray[urldecode($$Optionsname)] = $$Preisname;
        $Gewichte_Opt[urldecode($$Optionsname)] = $Gewicht_Opt[$opt_counter];
        $opt_counter++;
    }
    // Dasselbe Spiel um herauszufinden wieviele Variationen dieses Artikels existieren
    $var_counter = 1;
    $Variationsname = "Variation$var_counter";
    $Preisname = "Aufpreis$var_counter";
    $Gruppenname = "Gruppe$var_counter";
    while (isset($$Variationsname)) {
        $Variationsname = "Variation$var_counter";
        $Preisname = "Aufpreis$var_counter";
        $Gruppenname = "Gruppe$var_counter";
        $Variationsarray[urldecode($$Variationsname)] = $$Preisname;
        $Variationsgruppenarray[urldecode($$Variationsname)] = $$Gruppenname;
        $Gewichte_Var[urldecode($$Variationsname)] = $Gewicht_Var[$var_counter];
        $var_counter++;
    }

    // Url-encodierte Variablen wieder decodieren

    $Name = urldecode($Name);
    $Artikel_Nr = urldecode($Artikel_Nr);
    $Beschreibung = urldecode($Beschreibung);
    $Link = urldecode($Link);
    $Eingabefeld_text = addslashes(urldecode($Eingabefeld_text));
    $Eingabefeld_param = urldecode($Eingabefeld_param);


    if (!empty($Artikel_ID)) {
        // Einen bestehenden Artikel updaten:
        // Nun wird der bestehende Artikel upgedated. Nur das Bild wird erst spaeter behandelt:
        if (($new_ID = updArtikel_2($Kategorie_IDarray, $Artikel_ID, $Artikel_Nr, $Name, $Beschreibung, $letzteAenderung,
             $Preis, $Aktionspreis, $Gewicht, $MwSt, $Link, $Optionenarray, $Variationsarray, $Variationsgruppenarray, $Gruppentext,
             $Gruppe_darstellen , $Eingabefeld_text, $Eingabefeld_param, $Gewichte_Opt, $Gewichte_Var, true))) {

            $erfolg = true; // Diese Variable liefert weiter unten (HTML-Teil) eine Meldung
        }
/*        else {
            die("<B><H1>Artikel-Update: Es gab ein Problem beim Einfuegen in die Datenbank! (bild_up.php->updArtikel_2)</H1></B><BR>");
        }*/
    }
    else {
        // Da jetzt bekannt ist, in welche(r) Kategorie(n) der neue Artikel eingefuegt wird, muss noch der MwSt-default-Satz der Kategorie
        // uebernommen werden (falls angewaehlt). Wenn der Artikel in mehrere Kategorien kommt, so wird der MwSt-Satz der ersten Kategorie
        // gewaehlt
        if ($MwSt == "MwSt_default_Satz") {
            reset($Kategorie_IDarray);
            $MwSt = getDefaultMwStSatz(key($Kategorie_IDarray));
        }

        // Einen neuen Artikel in die Datenbank einfuegen:
        // Nun wird der neue Artikel in der DB gespeichert
        if (($new_ID = newArtikel($Kategorie_IDarray, $Artikel_Nr, $Name, $Beschreibung, $letzteAenderung,
             $Preis, $Aktionspreis, $Gewicht, $MwSt, $Link, $Optionenarray, $Variationsarray, $Variationsgruppenarray, $Gruppentext,
             $Gruppe_darstellen, $Eingabefeld_text, $Eingabefeld_param, $Gewichte_Opt, $Gewichte_Var, true))) {

             $erfolg = true; // Diese Variable liefert weiter unten (HTML-Teil) eine Meldung
        } // end of if
    } // end of else

    // Im weiteren Script wird die neue Artikel_ID verwendet, aber unter dem Namen der alten ID
    $Artikel_ID = $new_ID;

  // ermitteln, welche Grafikformate mit der aktuellen GD-Library verwendet werden koennen und
  // ob ueberhaupt eine GD-Library installiert ist
  $gd_png = 0;
  $gd_jpg = 0;
  $gd_gif = 0;

  // bildcheck nur ausführen, wenn eine gd-library installiert ist
  if (function_exists(ImageTypes)){
      if (ImageTypes() & IMG_PNG) { $gd_png = 1; }
      if (ImageTypes() & IMG_JPG) { $gd_jpg = 1; }
      if (ImageTypes() & IMG_GIF) {
          $gd_gif = 1;
      }
      else {
          // Zusatzcheck fuer GIF, weil obiger Check z.T. nicht funktioniert
          $parsed_gd_info = get_gdlibrary_info("verbose");
          if ($parsed_gd_info['GIF Create Support'] == 1) {
              $gd_gif = 1;
          }
      }

  } // end of if function_exists

  // aktuelle Bilddaten des Artikels auslesen:
  // [0] -> Bild_gross
  // [1] -> Bild_klein
  // [2] -> Bildtyp
  // [3] -> Bild_last_modified
  $bilddaten = getArtikelBilder($Artikel_ID);


?>

<script language="JavaScript">

function NeuFenster(bild_gr){
 MeinFenster = window.open("pop_up_admin.php?bild_gross="+bild_gr+"", "GrossesBild", "width=640,height=480,scrollbars");
 MeinFenster.focus();
} // end of function NeuFenster

function AuswahlFenster(bild_gr){
 MeinFenster = window.open("./bild_wahl.php", "Produktebilder", "width=850,height=700,scrollbars");
 MeinFenster.focus();
} // end of function AuswahlFenster

 // Formular abschicken, wenn der Administrator ein bestehendes Bild gewaehlt hat
 function SubmitAuswahlForm(bild_kl, bild_gr) {
     // Hidden-Felder setzen
     document.ausw_formular.file_kl.value= bild_kl;
     document.ausw_formular.file_gr.value= bild_gr;
     // Formular abschicken
     document.ausw_formular.submit();
 } // end of function SubmitAuswahlForm

function chkFormular() {
    // ueberpruefen, ob eine .jpg oder .jpeg Datei hochgeladen werden soll
    pfad_datei = document.Formular.form_data.value.toLowerCase();

    // check, ob ueberhaupt ein string eingegeben wurde
    if(pfad_datei == "") {
        alert("Bitte zuerst eine Datei auswählen!");
        document.Formular.form_data.focus();
        return false;
    } // end of if

    // check, ob eingegebener string mit .jpg oder .jpeg aufhört
    file3_string = pfad_datei.substring(pfad_datei.length-4,pfad_datei.length);
    file4_string = pfad_datei.substring(pfad_datei.length-5,pfad_datei.length);
    format = 0;

    <?php
    if ($gd_png == 1){
        echo 'if(file3_string == ".png"){'."\n";
        echo "    format = 1;\n";
        echo "} // end of if\n";
    } // end of if

    if ($gd_jpg == 1){
        echo 'if(file3_string == ".jpg" || file4_string == ".jpeg"){'."\n";
        echo "    format = 1;\n";
        echo "} // end of if\n";
    } // end of if

    if ($gd_gif == 1){
        echo 'if(file3_string == ".gif"){'."\n";
        echo "    format = 1;\n";
        echo "} // end of if\n";
    } // end of if

    ?>
    if (format == 0){
        document.Formular.form_data.focus();
        alert("Ungültiges Dateiformat!");
        return false;
    } // end of if

} // end of function chkFormular

</script>

<p><h1><b>SHOP ADMINISTRATION</b></h1></p>
<center>
<table border=0 width=80%>
<?php
  // Abschnitt nur ausgeben, wenn schon ein Produktebild zum Artikel gespeichert ist
  if ($bilddaten[1] != "") {
?>
  <tr>
    <td colspan=3>
      Der Artikel <?php echo"<B>".stripslashes($Name)."</B>";?> wurde erfolgreich in der Datenbank gespeichert
    </td>
  </tr>
  <tr><td colspan=3><hr></td></tr>
  <tr>
    <td valign=top>
      <b>Momentan aktuelles Produktbild:</b><br>
      Folgendes Artikelbild ist momentan zum Artikel <?php echo"<B>".stripslashes($Name)."</B>";?> gespeichert:
    </td>
    <?php
    if ($bilddaten[1] != ""){
        ?>
        <td width=20% bgcolor=#CCCCCC align=center colspan=2><a href="javascript:NeuFenster('<?php echo "$bilddaten[0]";?>')"><img src="../ProdukteBilder/<?php echo "$bilddaten[1]";?>" border="0"></a></td>
        <?php
    } // end of if
    else{
        echo "<td width=20% bgcolor=#CCCCCC align=center colspan=2>&nbsp;</td>";
    } // end of else
    ?>
  </tr>
  <tr><td colspan=3><hr></td></tr>
<?php
  } // end of if
?>
  <form method='post' name='Formular' action='./bild_up.php?Speichern=true&Artikel_ID=<?php echo "$Artikel_ID";?>' enctype='multipart/form-data' onSubmit="return chkFormular()">
  <?php
  // falls, eine GD-Library installiert ist, die mindestens eines der Bildformate jpg, png oder gif unterstützt
  if ($gd_png == 1 || $gd_jpg == 1 || $gd_gif == 1){
      ?>
      <tr>
        <td colspan=3>
          <b>Ein
          <?php
            // Text nur ausgeben, wenn schon ein Produktebild zum Artikel gespeichert ist
            if ($bilddaten[1] != "") { echo " anderes "; }
          ?>
          Produktbild hochladen:</b><br>

          Wenn Sie f&uuml;r diesen Artikel ein Artikelbild hochladen wollen, dr&uuml;cken Sie auf den &quot;Durchsuchen-&quot; bzw. &quot;Browse&quot;-Knopf und w&auml;hlen dann ein Bild auf Ihrer Festplatte aus. Danach klicken Sie auf den Knopf  &quot;Bild hochladen&quot;, damit das Produktbild in die Datenbank gespeichert wird.
          <br><br>
          Folgende(s) Bildformat(e) werden von Ihrem Server unterstützt:
          <br>
            <?php
            if ($gd_png == 1) { echo "- Bilder im PNG-Format (.png)<br>"; }
            if ($gd_jpg == 1) { echo "- Bilder im JPG-Format (.jpg oder .jpeg)<br>"; }
            if ($gd_gif == 1) { echo "- Bilder im GIF-Format (.gif)<br>"; }
            ?>
        </td>
      </tr>
      <tr valign=middle>
        <td colspan=2 valign=middle>
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
            <input type="file" name="form_data"  size="40" accept="image/jpeg" align=absmiddle>
        </td>
        <td valign=middle align=center>
          <input type='image' src='../Buttons/bt_neues_bild_hochladen_admin.gif' border='0'>
        </td>
      </tr>
      </form>
      <?php
  } // end of if
  // falls keine GD-Library auf dem Server installiert ist..
  else{
      ?>
      <tr>
        <td colspan=3>
          <b style='color:#FF0000;'>Keine Grafikunterstützung installiert!</b><br><br>
          Auf Ihrem Webserver ist keine GD-Library installiert. Diese Library braucht der PhPepperShop, um die Thumbnails (kleine Produktebilder) zu erzeugen. Wenden Sie sich an Ihren Serverbetreiber und bitten Sie Ihn, eine aktuelle GD-Library zu installieren, damit Sie zu Ihren Artikeln auch Bilder hochladen k&ouml;nnen.
          <br><br>
        </td>
      </tr>
  <?php
  } // end of else
  ?>

  <tr><td colspan=3><hr></td></tr>
  <tr>
    <td colspan=2>
      <b>Folgendes Produktbild verwenden:</b><br>
      Falls ein anderer Artikel schon das gleiche Produktbild
  verwendet, k&ouml;nnen Sie es hier ausw&auml;hlen. (<a href="javascript:popUp('../ProdukteBilder/info.txt')">Infos</a>)
    </td>
    <td align=center>
      <a href="javascript:AuswahlFenster()" border="0">
         <img src="../Buttons/bt_bild_wahl_admin.gif" border="0" align="absmiddle"
         alt="Ein vorhandenes Bild w&auml;hlen">
      </a>

      <form name="ausw_formular"  action="./bild_up.php?auswahl_Bild=1" method="post">
          <input TYPE="hidden" name=Artikel_ID value="<?php echo"$new_ID";?>">
          <input TYPE="hidden" name="file_kl" value="">
          <input TYPE="hidden" name="file_gr" value="">
      </form>
    </td>
  </tr>

  <tr><td colspan=3><hr></td></tr>
  <?php
    // Abschnitt nur ausgeben, wenn schon ein Produktebild zum Artikel gespeichert ist
    if ($bilddaten[1] != "") {
  ?>
  <tr>
    <td colspan=2>
      <b>Das Produktbild löschen:</b><br>
      Wenn Sie das zu diesem Artikel geh&ouml;rende Bild l&ouml;schen wollen, dr&uuml;cken Sie auf den Knopf  &quot;vorhandenes Bild l&ouml;schen&quot;.
    </td>
    <td align=center>
      <a href="./bild_up.php?loeschen_Bild=1&Artikel_ID=<?php echo"$new_ID";?>" title="">
         <img src="../Buttons/bt_bild_loeschen_admin.gif" border="0" align="absmiddle"
         alt="Das vorhandene Bild loeschen"></a>
    </td>
  </tr>
  <tr><td colspan=3><hr></td></tr>
  <?php
    } // end of if
  ?>
  <tr>
    <td colspan=2>
      <b>Weiter, ohne &Auml;nderungen am Produktbild vorzunehmen:</b><br>
      Falls Sie weder ein neues Bild f&uuml;r diesen Artikel hochladen, noch das aktuelle Bild l&ouml;schen wollen, dr&uuml;cken Sie den "Weiter" Knopf.
    </td>
    <td align=center>
      <a href="./Shop_Einstellungen_Menu_1.php" title="Kein Bild hinzufuegen">
         <img src="../Buttons/bt_weiter_admin.gif" border="0" align="absmiddle"
         alt="weiter, ohne &Auml;nderungen"></a>
    </td>
  </tr>
</table>
</center>
<?php
  }// End else

  // Seite HTML-maessig abschliessen
  echo "</BODY>";
  echo "</HTML>";

  // End of file-----------------------------------------------------------------------
?>
