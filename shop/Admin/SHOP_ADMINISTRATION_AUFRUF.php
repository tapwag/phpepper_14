<?php
  // Filename: SHOP_ADMINISTRATION_AUFRUF.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Ueber diese Funktion werden die Shop-Funktionen als Links angesprochen
  //
  // Sicherheitsstufe:                     *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_ADMINISTRATION_AUFRUF.php,v 1.55 2003/05/24 18:41:32 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_ADMINISTRATION_AUFRUF = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($database)) {include("database.php");}
  if (!isset($Database)) {include("initialize.php");}
  if (!isset($SHOP_ADMINISTRATION_ARTIKEL)){include("SHOP_ADMINISTRATION_ARTIKEL.php");}
  if (!isset($SHOP_ADMINISTRATION)) {include("SHOP_ADMINISTRATION.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);


  // Bei der Bearbeitung von bestehenden Artikeln, kann es sein, dass der Benutzer direkt via Header-Direktive weitergeleitet
  // werden muss, deshalb ist der Header-Teil von darstellen = 2 gleich hier am Anfang der Datei unter gebracht:
  if($darstellen == 2) { // Header-Teil
    // Alle Artikel auslesen (falls schon welche gewaehlt wurden...
    $myArtikelarry = array();
    // Wenn Schnellsuche benutzt wurde - entsprechende Artikel auslesen
    if ($Schnellsuche_benutzt == 'ja') {
        $myArtikelarray = getArtikelauswahl($Schnellsuche);
    }
    else {
        if ($Kategorienamen == "Nichtzugeordnet") {
            $Unterkategorie_von ="@PhPepperShop@";
        }
        $myArtikelarray = getArtikeleinerKategorie($Kategorienamen, $Unterkategorie_von);
    }
    // Wenn nur EIN Artikel als Loesung gefunden wurde, den Benutzer gleich zur Artikelbearbeitungsmaske forwarden, es sei
    // denn, der Artikel soll geloescht werden!
    if (count($myArtikelarray) == 1 && $up_loe == 1) {
        header("Location: SHOP_ADMINISTRATION_AUFRUF.php?darstellen=5&up_loe=$up_loe&Artikelname=".$myArtikelarray[0]->artikel_ID);
        exit;
    }

    // Ev. ausgelesene Datenangaben vom Formular her aufbereiten (wegen Sonderzeichen):
    $Schnellsuche = stripslashes($Schnellsuche);
  }

  // -----------------------------------------------------------------------
  // Ausgabe des HTML-Headers
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="content-language" content="de">
<meta name="author" content="José Fontanil and Reto Glanzmann">
<link REL=stylesheet href="./shopstyles.css" TYPE="text/css">
<title>Shop</title>

<SCRIPT LANGUAGE="JavaScript">
  <!-- Begin
  function popUp(URL) {
      day = new Date();
      id = day.getTime();
      eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=620,left = 100,top = 100');");
  }
  function delete_artikel() {
      var returnwert = false;
      if (confirm("Sind Sie sicher, dass sie den ausgewählten Artikel löschen möchten?")) {
          returnwert = true;
      }
      return returnwert;
  }
  // End -->
</script>
<?php

// -----------------------------------------------------------------------
// Die folgende Zuweisung definiert die Anzahl leeren Felder die bei einem
// neuen Artikel zur Verfuegung stehen
$anzahloptionen = 2;    //Default, wieviele Optionenfelder für Neuen Artikel
$anzahlvariationen = 2; //Default, wieviele Variationenfelder für Neuen Artikel

// -----------------------------------------------------------------------
// Weiche: Je ein IF resp. ELSE IF ist zustaendig fuer eine Funktion in diesem Modul
// In den Formularen weiter unten hat man ein hidden-field, welches den Wert der
// Variable $darstellen uebermittelt. Dadurch wird entschieden welche Funktion
// Benutzt werden soll.


// -----------------------------------------------------------------------
// Einen neuen Artikel einfuegen
// -----------------------------------------------------------------------
if ($darstellen == 1) {
    echo "</head>\n";
    echo "<body>\n";

    //Ein neues, leeres Artikel-Objekt instanzieren (def. siehe artikel_def.php)
    $newArtikel = new Artikel;

    //Maske zur Eingabe eines neuen Artikels oeffnen und Objekt uebergeben
    darstellenArtikel($newArtikel);

} // end of darstellen = 1



// -----------------------------------------------------------------------
// Einem Artikel eine oder mehrere Kategorien zuordnen
// -----------------------------------------------------------------------
elseif ($darstellen == 101) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";
    echo "<b>Den Artikel einer/mehreren Kategorie(en) zuordnen:</b>";
    echo '<form action="./bild_up.php" method="post" title="Kategorieauswahl &uuml;bermitteln">';

    // Optionen und Variationen als Hidden-Fields ausgeben
    // Optionen:
    $opt_counter = 1;
    $Optionsname = "Option$opt_counter";
    while (isset($$Optionsname)) {
        $Optionsname = "Option$opt_counter";
        $Preisdiffname = "Preisdifferenz$opt_counter";
        echo "<input TYPE='hidden'  name='".$Optionsname."' value='".urlencode($$Optionsname)."' >\n";
        echo "<input TYPE='hidden'  name='".$Preisdiffname."' value='".stripslashes($$Preisdiffname)."' >\n";
        echo "<input TYPE='hidden'  name='Gewicht_Opt[".$opt_counter."]' value='".$Gewicht_Opt[$opt_counter]."' >\n";
        $opt_counter++;
    }

    // Varianten (Name, Preis und Gruppenzugehörigkeit)
    $var_counter = 1;
    $Variationsname = "Variation$var_counter";
    while (isset($$Variationsname)) {
        $Aufpreisname = "Aufpreis$var_counter";
        $Variationsname = "Variation$var_counter";
        $Gruppenname = "Gruppe$var_counter";
        echo "<input TYPE='hidden'  name='".$Variationsname."' value='".urlencode($$Variationsname)."' >\n";
        echo "<input TYPE='hidden'  name='".$Aufpreisname."' value='".stripslashes($$Aufpreisname)."' >\n";
        echo "<input TYPE='hidden'  name='".$Gruppenname."' value='".stripslashes($$Gruppenname)."' >\n";
        echo "<input TYPE='hidden'  name='Gewicht_Var[".$var_counter."]' value='".$Gewicht_Var[$var_counter]."' >\n";
        $var_counter++;
    } // end of while

    // Variationsgruppen
    for ($i=1; $i<=$anzahl_var_grp; $i++){
        echo "<input TYPE='hidden'  name='Gruppentext[".$i."]' value='".urlencode($Gruppentext[$i])."' >\n";
        echo "<input TYPE='hidden'  name='Gruppe_darstellen[".$i."]' value='".urlencode($Gruppe_darstellen[$i])."' >\n";
    } // end of for
    echo "<input TYPE='hidden' name='anzahl_var_grp' value='$anzahl_var_grp'>";

    // Die anderen Artikel-Parameter als Hidden-Fields ausgeben
    echo "<input TYPE='hidden'  name='new_ID' value='".stripslashes($new_ID)."' >\n";
    if ($neuerArtikel != 'ja'){
        // Wenn ein bearbeiteter Artikel als neuer Artikel gespeichert werden soll, Artikel_ID nicht weitergeben
        echo "<input TYPE='hidden'  name='Artikel_ID' value='".stripslashes($Artikel_ID)."' >\n";
    } // end of if
    echo "<input TYPE='hidden'  name='Artikel_Nr' value='".urlencode($Artikel_Nr)."' >\n";
    echo "<input TYPE='hidden'  name='Name' value='".urlencode($Name)."' >\n";
    echo "<input TYPE='hidden'  name='Beschreibung' value='".urlencode($Beschreibung)."' >\n";
    echo "<input TYPE='hidden'  name='letzteAenderung' value='".stripslashes($letzteAenderung)."' >\n";
    echo "<input TYPE='hidden'  name='Preis' value='".stripslashes($Preis)."' >\n";
    echo "<input TYPE='hidden'  name='Aktionspreis' value='".stripslashes($Aktionspreis)."' >\n";
    echo "<input TYPE='hidden'  name='Gewicht' value='".stripslashes($Gewicht)."' >\n";
    echo "<input TYPE='hidden'  name='MwSt' value='".stripslashes($MwSt)."' >\n";
    echo "<input TYPE='hidden'  name='Link' value='".urlencode($Link)."' >\n";

    // Strings fuer die Zusatzfelder sonderzeichengetrennt 'encodieren'
    if ($eingabefelder_anz > 0){
        $eingabefeld_text = "";
        $eingabefeld_param = "";
        $ef_textarray = array();
        $ef_paramarray = array();
        for ($s=1; $s<=$eingabefelder_anz; $s++){
            if ($eingabefeld[$s] != ""){
                // den Parameternfeldern einen gueltigen Wert zuweisen, wenn ein Unsinn eingegeben wurde
                if ($eingabefeld_laenge[$s] < 1 || $eingabefeld_laenge[$s] > 99){
                    $eingabefeld_laenge[$s] = 20;
                }  // end of if
                if ($eingabefeld_max[$s] < $eingabefeld_laenge[$s]  || $eingabefeld_max[$s] > 9999){
                    $eingabefeld_max[$s] = $eingabefeld_laenge[$s];
                }  // end of if
                if ($eingabefeld_hoehe[$s] < 1 || $eingabefeld_hoehe[$s] > 99){
                    $eingabefeld_hoehe[$s] = 1;
                }  // end of if

                $ef_textarray[] = stripslashes($eingabefeld[$s]);
                // :0:0 sind Reserverfelder, die zur Zeit noch nicht verwendet werden
                $ef_paramarray[] = "$eingabefeld_laenge[$s]:$eingabefeld_max[$s]:$eingabefeld_hoehe[$s]:0:0";
            } // end of if
        } // end of for
        // Textstring erstellen: beschreibung1þbeschreibung2þ...
        $eingabefeld_text = spezial_string($ef_textarray);
        // Parameter-String erstellen: lange1:maxlaenge1:hoehe1:res1.1:res1.2þlange2:maxlaenge2:hoehe2:res2.2:res2.2þ...
        $eingabefeld_param = spezial_string($ef_paramarray);
        echo "<input TYPE='hidden'  name='Eingabefeld_text' value='".urlencode($eingabefeld_text)."' >\n";
        echo "<input TYPE='hidden'  name='Eingabefeld_param' value='".urlencode($eingabefeld_param)."' >\n";
    } // end of if

    // Kategorien- und Unterkategorienliste ausgeklappt ausgeben
    $myKategorien = array();
    $myKategorien = getallKategorien();
    // $value ist ein Kategorie-Objekt, welches im Array Unterkategorien seine Unterkategorien mitfuehrt (siehe auch getallKategorien())
    echo "<table border=\"0\">";

    // totale Anzahl Kategorien (und Unterkategorien) ermitteln..
    $katcounter = 0;
    foreach($myKategorien as $keyname => $value){
        if ($value->kategorienanzahl() > 0){
            $katcounter = $katcounter + $value->kategorienanzahl() + 1;
        }
        else {
            $katcounter++;
        } // end of if kategorienanzahl() > 0
    }  // end of foreach $myKategorien

    // ..wenn mehr als 20 Kategorien vorhanden, 'weiter' und 'abbrechen' Buttons auch oben ausgeben
    if ($katcounter > 20){
        echo '<nobr><br><input type=image src="../Buttons/bt_weiter_admin.gif" border="0" alt"weiter">&nbsp;';
        echo '<a href="./Shop_Einstellungen_Menu_1.php" title="Abbrechen">';
        echo '<img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a></nobr>';
    }

    // alle Kategorien-ID's, wo sich der Artikel drin befindet holen
    $kat_id_array = array();
    if (!empty($Artikel_ID)){
        $kat_id_array = getKategorieID_eines_Artikels($Artikel_ID);
    }

    foreach($myKategorien as $keyname => $value){
        $zeigUnterkategorien = false;
        if ($value->kategorienanzahl() > 0){
            $zeigUnterkategorien = true;
            echo "<tr><td colspan=3><b>-&nbsp;".$value->Name."</b></td></tr>"."\n";
        }
        else{
            echo "<tr><td colspan=2><b>-&nbsp;".$value->Name."</b></td><td><input type=checkbox ";
            $enthalten = false;
            foreach($kat_id_array as $kat_id){
                if ($kat_id == $value->Kategorie_ID) { $enthalten = true; }
            }
            if ($enthalten == true) { echo "checked "; }
            echo " name='Kategorie_IDarray[".$value->Kategorie_ID."]' value=".$value->Kategorie_ID."></td></tr>\n";
        }
        if ($zeigUnterkategorien){
            $myUnterkategorien = array();
            $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
            for($i=0;$i < $value->kategorienanzahl();$i++){
                echo "<tr><td>&nbsp;&nbsp;</td><td>-&nbsp;".$myUnterkategorien[$i]->Name."</td><td><input type=checkbox ";
                $enthalten = false;
                foreach($kat_id_array as $kat_id){
                    if ($kat_id == $myUnterkategorien[$i]->Kategorie_ID) { $enthalten = true; }
                }
                if ($enthalten == true) { echo "checked "; }
                echo "name='Kategorie_IDarray[".$myUnterkategorien[$i]->Kategorie_ID."]' value=".$myUnterkategorien[$i]->Kategorie_ID."></td></tr>\n";
            }// End for
        }//End if zeigUnterkategorien
    }// End foreach myKategorien
    echo "</table>";
    // weiter und abbrechen Button ausgeben
    echo '<br><nobr><input type=image src="../Buttons/bt_weiter_admin.gif" border="0" alt="weiter">&nbsp;';
    echo '<a href="./Shop_Einstellungen_Menu_1.php" title="Abbrechen">';
    echo '<img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a></nobr>';
    echo "</form>";

} // end of darstellen = 101


// -----------------------------------------------------------------------
// Einen Artikel bearbeiten 1/3 (inkl. 2/3) - Auswahl der Kategorie des zu bearbeitenden Artikels
// Der erste Teil wird ganz am Anfang dieser Datei gemacht -> noch vor dem senden der Header werden
// die allfaellig gefundenen Artikel ausgelesen!
// -----------------------------------------------------------------------
else if($darstellen == 2) { // (body-Teil)
    echo "</head>\n";
    echo "<body>\n";
?>
    <P><H1>SHOP ADMINISTRATION</H1></P>
    <B>Einen bestehenden Artikel <?php if($up_loe == 1) {echo "bearbeiten:";} else {echo "<font color=\"#ff0000\">l&ouml;schen</font>";} ?></B>

<?php
    // myKategorien-Array initialisieren
    $myKategorien = array();
    // Alle Kategorien in Array $my Kategorien schreiben
    $myKategorien = getallKategorien();

    // Kategorienanzahl ermitteln, falls mehr als 25 Hauptkategorien vorhanden, wird oberhalb des Formulars noch
    // ein zusaetzlicher Abbrechen-Button angezeigt, damit zum Abbrechen nicht nach unten gescrollt werden muss.
    $katcounter = 1;
    foreach($myKategorien as $keyname => $value){
            $katcounter++;
    }  // end of foreach $myKategorien

    // ..wenn mehr als 25 Haupt-Kategorien vorhanden, 'abbrechen' Button auch oben ausgeben
    if ($katcounter > 25){
        echo '<br><br><a href="./Shop_Einstellungen_Menu_1.php" title="Abbrechen">';
        echo '<img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a>';
    }
?>
    <br><br>
    <TABLE align="left" valign="MIDDLE" border='0' cellspacing="0" cellpadding="0">
        <TR>
            <TD colspan="2" align="bottom">
                <br>
                <div style="font-size:14px; font-weight:bold; fon-family:Arial,Helvetica,Geneva,Swiss,SunSans-Regular">Artikel via Schnellsuche ausfindig machen</div><small>(Artikelnummer oder Artikelnamen eingeben)</small>
            </TD>
            <TD>
                &nbsp;
            </TD>
            <TD colspan="2" align="bottom">
<?php
                // -----------------------------------------------------------------------
                // Einen Artikel bearbeiten 2/3 a - Auswahl des Artikels von der zuvor gewaehlten Kategorie
                // Anm. $darstellen = 3 wurde frueher in einer Testfunktion verwendet (jetzt nicht mehr gebraucht)
                // -----------------------------------------------------------------------
                // Zuerst alle Artikel einer Kategorie auslesen und
                // Fallunterscheidung: Ob ueberhaupt eine Kategorie ausgewaehlt wurde oder die Schnellsuche benutzt wurde.
                //                     Wenn ja, dann werden entsprechend der Eingabe die in Frage kommenden Artikel ausgelesen
                // A) Wenn die Schnellsuche benutzt wurde:
                if ($Schnellsuche_benutzt == 'ja') {
                    echo "<br>";
                    echo '<div style="font-size:14px; font-weight:bold; fon-family:Arial,Helvetica,Geneva,Swiss,SunSans-Regular">Bitte den zu bearbeitenden Artikel auswaehlen</div><small>(Artikelname [Artikelnummer])</small>';
                }
                // B) Wenn ueber die Kategorie, die Artikel ausgewaehlt wurde
                else {
                    // B2) Wenn keine Artikel in der auszulesenden Kategorie vorhanden waren
                    if ($selected != "") {
                        echo "<br>";
                        echo '<div style="font-size:14px; font-weight:bold; fon-family:Arial,Helvetica,Geneva,Swiss,SunSans-Regular">Bitte den zu bearbeitenden Artikel auswaehlen</div><small>(Artikelname [Artikelnummer])</small>';
                    }
                    // C) Wenn noch keine Auswahl getroffen wurde
                    else {
                        echo "&nbsp;\n";
                    }
                }
?>
            </TD>
        </TR>
        <TR>
            <FORM  action="<?php echo "SHOP_ADMINISTRATION_AUFRUF.php?up_loe=".$up_loe; ?>" method="post" name="Schnellsuche_Formular" title="Artikelnummer oder -namen eingeben">
                <TD align="left" valign="center">
                    &nbsp;
                </TD>
                <TD align="left" valign="center">
                    Schnellsuche: <input type="text" name="Schnellsuche" value="<?php echo htmlspecialchars($Schnellsuche); ?>">&nbsp;
                </TD>
                <TD align="left" valign="center">
                    <input type="image" src="../Buttons/bt_suchen_admin.gif" alt="Artikel bearbeiten_suchen" border="0">
                    &nbsp;&nbsp;&nbsp;
                    <input type="hidden" value="2" name="darstellen">
                    <input type="hidden" value="ja" name="Schnellsuche_benutzt">
                </TD>
            </FORM>
            <FORM  action="SHOP_ADMINISTRATION_AUFRUF.php" method="post" title="Artikel_ID angeben" name="Artikelauswahl_Formular" <?php if ($up_loe == 0) {echo 'onSubmit="return delete_artikel()"';} ?>>
                <TD align='right' valign='center'>
<?php
                    // -----------------------------------------------------------------------
                    // Einen Artikel bearbeiten 2/3 b - Darstellen der ausgelesenen Artikel
                    // -----------------------------------------------------------------------
                    // Fallunterscheidungen:
                    // Suche ueber Kategorien: Falls keine Artikel in dieser Kategorie vorhanden sind
                    if ($selected != '' && count($myArtikelarray) == 0){
                        echo "<br>";
                        echo '<div style="font-size:14px; color:#ff0000; font-weight:bold; fon-family:Arial,Helvetica,Geneva,Swiss,SunSans-Regular">Es befinden sich keine Artikel in der ausgew&auml;hlten Kategorie!</div>';
                    }
                    // Suche ueber Schnellsuche: Falls keine Artikel gefunden wurden
                    elseif ($Schnellsuche_benutzt == 'ja' && count($myArtikelarray) == 0) {
                        echo "<br>";
                        echo '<div style="font-size:14px; color:#ff0000; font-weight:bold; fon-family:Arial,Helvetica,Geneva,Swiss,SunSans-Regular">Es wurden keine Artikel gefunden. Bitte das Suchkriterium &auml;ndern!</div>';
                    }
                    // Suche ueber Kategorien oder Schnellsuche: Eigentliche Anzeige der Artikel zur Auswahl in einer Select-Box (Drop-Down Menu)
                    elseif ($selected != '' || $Schnellsuche_benutzt == 'ja') {
?>
                        <INPUT TYPE="hidden"  name=darstellen value=5   title="Suchen">
                        <INPUT TYPE="hidden"  name=up_loe value='<?php echo "$up_loe";?>' title="Updaten_oder_loeschen">
                        <SELECT name="Artikelname" size="1">
<?php
                            // Hier folgt nun etwas PHP-Code um alle Kategorien in die Select-Box abzufuellen
                            foreach($myArtikelarray as $artikelarray){
                                $myArtikel_ID = $artikelarray->artikel_ID;
                                echo "<option value='$myArtikel_ID' ";
                                echo " >".$artikelarray->name." [".$artikelarray->artikel_Nr."]</option>";
                            }
?>
                        </SELECT>
                </TD>
                <TD align="center">
<?php
                        if ($up_loe == 1) {
                            echo '<input type=image src="../Buttons/bt_bearbeiten_admin.gif" border="0" name="Bearbeiten_Button">';
                        }
                        else {
                            echo '<input type=image src="../Buttons/bt_loeschen_admin.gif" border="0" name="Bearbeiten_Button">';
                        }
                    }// End elseif
                    else {
                        echo "&nbsp;\n";
                    }
?>
                </TD>
            </FORM>
        </TR>
        <TR>
            <TD colspan="5">
                &nbsp;
            </TD>
        </TR>
        <TR>
            <TD colspan="2" align="bottom">
                <br>
                <div style="font-size:14px; font-weight:bold; font-family:Arial,Helvetica,Geneva,Swiss,SunSans-Regular">Artikel via Kategorie ausfindig machen</div><small>(Bitte eine Kategorie ausw&auml;hlen)</small>
            </TD>
            <TD colspan="3">
                <br><br>&nbsp;
            </TD>
        </TR>
        <TR>
            <TD>
                &nbsp;
            </TD>
            <TD align='left' valign='top'>
<?php
                // Hier folgt nun etwas PHP-Code um alle Kategorien ins Kategorien-Pull-Down-Menu abzufuellen
                // $wert ist ein Kategorie-Objekt mit gegebenenfalls in seinem Array enthaltenen Unterkategorien

                // Jetzt noch die spezielle Kategorie 'Nichtzugeordnet' als Kategorie dazufuegen
                $myKategorien[] = getNichtzugeordnetKategorie();
                // $value ist ein Kategorie-Objekt, welches im Array Unterkategorien seine Unterkategorien mitfuehrt (siehe auch getallKategorien())
                foreach($myKategorien as $keyname => $value){
                    $zeigUnterkategorien = false;
                    if ($selected == $value->Kategorie_ID) {
                        // Wenn die bereits selektierte Kategorie Unterkategorien hat, soll eine entsprechende Meldung erscheinen
                        echo "<a href=\"SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=$up_loe&Kategoriename=".urlencode($value->Name)."&Kategorienamen=".urlencode($value->Name)."&selected=".$value->Kategorie_ID."&Schnellsuche=".$Schnellsuche."\" class='no_decoration' style='text-decoration:none'>";
                        echo "<img src='../Bilder/kat_selected.gif' alt='ge&ouml;ffnete Kategorie' border='0' align='baseline'>";
                        $Kategorienamen = $value->Name;
                    }
                    elseif ($open == $value->Kategorie_ID){
                        echo "<a href=\"SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=$up_loe&open=''&Kategorienamen=".urlencode($value->Name)."&Schnellsuche=".$Schnellsuche."\" class='no_decoration' style='text-decoration:none'>";
                        echo "<img src='../Bilder/kat_minus.gif' alt='Unterkategorien' border='0' align='baseline'>";
                        $zeigUnterkategorien = true;
                    }
                    elseif ($value->kategorienanzahl() > 0) {
                        echo "<a href=\"SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=$up_loe&Kategorienamen=".urlencode($value->Name)."&open=".$value->Kategorie_ID."&Schnellsuche=".$Schnellsuche."\" class='no_decoration' style='text-decoration:none' style='text-decoration:none'>";
                        echo "<img src='../Bilder/kat_plus.gif' alt='Unterkategorien anschauen' border='0' align='baseline'>";
                    }
                    else {
                        echo "<a href=\"SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=$up_loe&Kategorienamen=".urlencode($value->Name)."&selected=".$value->Kategorie_ID."&open=".$value->Kategorie_ID."&Schnellsuche=".$Schnellsuche."\" class='no_decoration' style='text-decoration:none'>";
                        echo "<img src='../Bilder/kat_leer.gif' alt='Keine Unterkategorien vorhanden' border='0' align='baseline'>";
                    }
                    echo " ".$value->Name."</a><br>"."\n";
                    if ($zeigUnterkategorien){
                        $myUnterkategorien = array();
                        $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
                        for($i=0;$i < $value->kategorienanzahl();$i++){
                            if ($selected == $myUnterkategorien[$i]->Kategorie_ID) {
                                echo "<a href=\"SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=$up_loe&Kategorienamen=".urlencode($myUnterkategorien[$i]->Name)."&Unterkategorie_von=".urlencode($value->Name)."&open=".$value->Kategorie_ID."&selected=".$myUnterkategorien[$i]->Kategorie_ID."&Schnellsuche=".$Schnellsuche."\" class='no_decoration' style='text-decoration:none'>";
                                echo "&nbsp;&nbsp;<img src='../Bilder/kat_selected.gif' alt='ge&ouml;ffnete Kategorie' border='0' align='baseline'>";
                                $Kategorienamen = $myUnterkategorien[$i]->Name;
                                $Unterkategorie_von = $value->Name;
                            }
                            else {
                                echo "<a href=\"SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=$up_loe&Kategorienamen=".urlencode($myUnterkategorien[$i]->Name)."&Unterkategorie_von=".urlencode($value->Name)."&open=".$value->Kategorie_ID."&selected=".$myUnterkategorien[$i]->Kategorie_ID."&Schnellsuche=".$Schnellsuche."\" class='no_decoration' style='text-decoration:none'>";
                                echo "&nbsp;&nbsp;<img src='../Bilder/kat_leer.gif' alt='Keine Unterkategorien vorhanden' border='0' align='baseline'>";
                            }
                            echo " ".$myUnterkategorien[$i]->Name."</a><br>\n";
                        }// End for
                    }//End if zeigUnterkategorien
                }// End foreach myKategorien
?>
            </TD>
            <TD>
                &nbsp;
            </TD>
            <TD colspan="2" align="left" valign="center">
                &nbsp;
            </TD>
        <TR>
            <TD colspan="5">
                &nbsp;
            </TD>
        </TR>
        <TR>
            <TD>&nbsp;</TD>
            <TD colspan="4">
                <br><a href="./Shop_Einstellungen_Menu_1.php" title="Abbrechen">
                <img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a>
            </TD>
        </TR>
    </TABLE>
<?php
} // end of darstellen = 2

// -----------------------------------------------------------------------
// Einen Artikel bearbeiten 3/3 - Update oder Loeschen, je nachdem was gewaehlt wurde einleiten
// darstellen = 3 und darstellen = 4 wurden frueher mal verwendet -> deprecated
else if($darstellen == 5) {
    echo "</head>\n";
    echo "<body>\n";

    // Wenn $up_loe 1 ist so soll ein Update durchgefuehrt werden, sonst wurde ein Artikel zum loeschen
    // freigegeben
    if ($up_loe == 1) {
        // Artikel updaten
        updArtikel($Artikelname);
    }
    else {
        if (delArtikel($Artikelname)) {
            // Artikel loeschen - Meldung ausgeben
            echo "<P><H1>SHOP ADMINISTRATION</H1></P><BR>";
            echo "<b>Das L&ouml;schen des Artikels mit interner ID:$Artikelname war erfolgreich!</b><BR><BR>";
            echo "<BR><BR><a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'></a><BR>";
        }
        else {
            // Artikel loeschen - Meldung ausgeben
            echo "<P><H1>SHOP ADMINISTRATION</H1></P><BR>";
            echo "<b>Fehler: Das L&ouml;schen des Artikels mit interner ID:$Artikelname war <font color=\"#ff0000\">NICHT</font> erfolgreich!</b><BR><BR>";
            echo "<BR><BR><a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'></a><BR>";
        }
    }
}



// -----------------------------------------------------------------------
// Eine neue Kategorie erstellen Schritt 1/2
// Eingabe der Kategoriedaten
// -----------------------------------------------------------------------
else if ($darstellen == 6) {
    echo "</head>\n";
    echo "<body>\n";
?>
<SCRIPT LANGUAGE="JavaScript">

  // Ueberpruefen, ob ein Kategoriename eingegeben wurde. Wenn ja, Formular abspeichern.
  function SaveForm(zaehler) {
      // pruefen, ob ein Kategoriename eingegeben wurde
      if(document.Formularname.Kategoriename.value == "") {
          alert("Bitte einen Kategorienamen eingeben!");
          document.Formularname.Kategoriename.focus();
      } // end of if kategoriename = ""
      else {
         // Hidden-Feld "position" mit Positionsnummer, wo eingefuegt werden soll, beschreiben
         document.Formularname.position.value = zaehler;
         // Formular uebermitteln
         document.Formularname.submit();
     }
  } // end of function SaveForm

</SCRIPT>

    <p><h1>SHOP ADMINISTRATION</h1></p>
    <b>Neue Kategorie erstellen:</b>
    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Kategorie erstellen" name="Formularname">
      <table border="0">
        <tr><td>
          <INPUT TYPE="hidden" name=darstellen value=7 title="Weiche">
          <INPUT TYPE="hidden" name=position value=1 title="Kategorienposition">
          Kategoriename:<br>
        </td>
        <td>
           <INPUT TYPE='TEXT'  name='Kategoriename' value="<?php echo $HTTP_POST_VARS["Kategoriename"]; ?>" size='32' maxlength='128' title='Kategoriename'><BR>
        </td></tr>
        <tr>
          <td valign="top">
            Beschreibung:<br>
          </td>
          <td>
             <textarea name="Beschreibung" cols="50" rows="10" wrap=physical><?php echo htmlspecialchars($HTTP_POST_VARS["Beschreibung"]); ?></textarea><br>
          </td>
        </tr>
<?php
    // MwSt-Satz Eingabefeld (Dropdown-Liste)
    if (getmwstnr() != "0" && getmwstnr() != "") {
      // Zuerst die MwSt-Settings auslesen und in einen Array abpacken
      $array_of_mwstsettings = getmwstsettings();
      // Eigentliches Dropdown-Menu erzeugen
      echo "<tr>\n";
      echo "  <td valign=\"top\">\nMwSt default Satz:</td><td>\n";
      echo "    <select name=\"MwSt_Satz\" size=\"1\">\n";
      // Alle gespeicherten MwSt-Saetze zur Auswahl auflisten
      foreach ($array_of_mwstsettings as $value) {
          if ($value->Beschreibung != "Porto und Verpackung") { //Porto und Verpackung MwSt-Satz ausblenden
              echo "<option value=\"".$value->MwSt_Satz."\">".$value->MwSt_Satz."% (".$value->Beschreibung.")</option>\n";
          }
      }// end of foreach
      echo "<option value=\"0\">0% (MwSt-frei)</option>\n"; // 0% MwSt-Option anbieten
      echo "    </select>\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
?>
        <tr>
          <td>
            Anzeige:<br>
          </td>
          <td align="left">
            <INPUT TYPE='CHECKBOX' name="Details_anzeigen" title="Details_anzeigen">Beschreibung anzeigen<br>
          </td>
        </tr>
        <tr><td colspan="2">
          &nbsp;<br><br>Wo soll die neue Kategorie eingef&uuml;gt werden:<br>&nbsp;
        </td></tr>
<?php

    // Kategorienliste und zwischen jeder Kategorie "hier einfuegen"-Button ausgeben
    // Wenn keine Kategorie existiert, wird nur ein "hier einfuegen"-Button am Anfang angezeigt

    // Kategorien-Objekt-Array erzeugen und mit Kategorienobjekten füllen
    $myKategorien = array();
    $myKategorien = getallKategorien();

    // Erster Einfuegenbutton ausgeben
    echo '<tr><td colspan=\"2\"><a href=\'JavaScript:SaveForm("1")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';

    // Jede existierende Kategorie ausgeben. Jeweils auf den Zwischenzeilen ein Einfuegebutton
    foreach($myKategorien as $keyname => $value){
        echo "<tr><td colspan=\"2\"><b>".$value->Name."</b></td></tr>"."\n";
        echo '<tr><td colspan=\"2\"><a href=\'JavaScript:SaveForm("'.($value->Positions_Nr+1).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';
    }// End foreach myKategorien

    echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
    echo "<tr><td colspan=\"2\"><a href='./Shop_Einstellungen_Menu_Kategorien.php' title='Abbrechen'>";
    echo "<img src='../Buttons/bt_abbrechen_admin.gif' border='0' align='absmiddle' alt='Abbrechen, zum Hauptmenu'></a><td></tr>";

?>
      </table>
    </form>
<?php

}  // end of darstellen = 6

// -----------------------------------------------------------------------
// Eine neue Kategorie erstellen Schritt 2/2
// Speichern der Kategorie
// -----------------------------------------------------------------------
else if ($darstellen == 7) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";
    // Test, ob es schon eine gleichnamige Kategorie gibt --> Abbruch!
    $kategorienarray = getallkategorien();
    foreach ($kategorienarray as $value) {
        // Nur Hauptkategorien miteinander vergleichen, wenn eine gleichnamige Hauptkategorie bereits existiert -> Abbruch
        $name1 = strtoupper($value->Name);
        $name2 = strtoupper($Kategoriename);
        if ($value->Unterkategorie_von == "" && $name1 == $name2) {
            echo "<form action=\"$PHP_SELF\" method=\"post\" title=\"Kategorie erstellen - Fehler\" name=\"Zurueckformular\">\n";
            echo "<br><font color=\"#ff0000\"> ACHTUNG!</font> Der Hauptkategorienname '$Kategoriename'";
            if ($value->Name != $Kategoriename) { //Ausgabe der verschiedentlich gross-/kleingeschriebenen Kategorienamen
                echo " (= ".$value->Name.")";
            }
            echo " existiert schon.<br>\nEs sind keine gleichnamigen Hauptkategorien erlaubt.<br>\n";
            echo "(Die Gross-/Kleinschreibung wird <i>nicht</i> beachtet!)<br>\n";
            echo "<input type=\"hidden\" name=\"darstellen\" value=\"6\" title=\"darstellen\">\n";
            echo "<input type=\"hidden\" name=\"Kategoriename\" value=\"$Kategoriename\" title=\"Kategoriename\">\n";
            echo "Bitte den <i>Namen</i> &auml;ndern.<br><br>\n";
            echo "<input type=\"image\" src=\"../Buttons/bt_zurueck_admin.gif\">\n";
            echo "</form>\n</body>\n</html>\n";
            // Page wurde geschlossen und das PHP-Script wird hier beendet
            exit;
        }// End if
    }// End foreach

    // Details_anzeigen Checkbox auswerten:
    if ($HTTP_POST_VARS["Details_anzeigen"] == "on") {
        $Details_anzeigen = "Y";
    }
    else {
        $Details_anzeigen = "N";
    }

    // Wenn der Shop nicht MwSt-pflichtig ist, so wird hierhin keine MwSt uebertragen -> auf 0 setzen:
    if ($HTTP_POST_VARS["MwSt_Satz"] == "") {$MwSt_Satz = 0;}
    // Funktion, zur Erstellung einer neuen Kategorie aufrufen
    if (newKategorie(trim($Kategoriename), trim($position), $Beschreibung, $Details_anzeigen, $MwSt_Satz, "")) {
        echo "<b>Die Kategorie ".stripslashes($Kategoriename)." wurde erfolgreich angelegt</b>";
    } // end of if newKategorie

    // wenn Kategorie nicht angelegt werden konnte..
    else{
        echo "<b>Die Kategorie ".stripslashes($Kategoriename)." konnte nicht angelegt werden!</b>";
    }

    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";

} // end of darstellen = 7



// -----------------------------------------------------------------------
// Eine neue Unterkategorie erstellen Schritt 1/2
// Eingabe der Unterkategoriedaten
// -----------------------------------------------------------------------
else if ($darstellen == 61) {
    echo "</head>\n";
    echo "<body>\n";
?>
<SCRIPT LANGUAGE="JavaScript">

  // Ueberpruefen, ob ein Unterkategoriename eingegeben wurde. Wenn ja, Formular abspeichern.
  function SaveForm(zaehler) {
      // pruefen, ob ein Kategoriename eingegeben wurde
      if(document.Formularname.Kategoriename.value == "") {
          alert("Bitte einen Unterkategorienamen eingeben!");
          document.Formularname.Kategoriename.focus();
      } // end of if kategoriename = ""
     else {
         // Hidden-Feld "position" mit Positionsnummer, wo eingefuegt werden soll, beschreiben
         document.Formularname.position.value = zaehler;
         // Formular uebermitteln
         document.Formularname.submit();
     }
  } // end of function SaveForm

</SCRIPT>

    <p><h1>SHOP ADMINISTRATION</h1></p>
    <b>Neue Unterkategorie in der Kategorie <?php echo stripslashes($Ukat_von); ?> erstellen:</b>
    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Kategorie erstellen" name="Formularname">
      <table border=0>
        <tr>
          <td>
            <INPUT TYPE="hidden" name=darstellen value=71 title="Weiche">
            <INPUT TYPE="hidden" name=position value=1 title="Kategorienposition">
            <INPUT TYPE="hidden" name="Ukat_von_enc" value='<?php echo urlencode(stripslashes($Ukat_von)); ?>' title="Kategorienposition">
            Unterkategoriename:<br>
          </td>
          <td>
            <INPUT TYPE='TEXT'  name='Kategoriename' value="<?php echo $Kategoriename; ?>" size='32' maxlength='128' title='Kategoriename'><BR>
          </td>
        </tr>
        <tr>
          <td valign="top">
            Beschreibung:<br>
          </td>
          <td>
             <textarea name="Beschreibung" cols="50" rows="10" wrap=physical><?php echo htmlspecialchars($HTTP_POST_VARS["Beschreibung"]); ?></textarea><br>
          </td>
        </tr>
<?php
    // MwSt-Satz Eingabefeld (Dropdown-Liste)
    if (getmwstnr() != "0" && getmwstnr() != "") {
      // Zuerst die MwSt-Settings auslesen und in einen Array abpacken
      $array_of_mwstsettings = getmwstsettings();
      // Eigentliches Dropdown-Menu erzeugen
      echo "<tr>\n";
      echo "  <td valign=\"top\">\nMwSt default Satz:</td><td>\n";
      echo "    <select name=\"MwSt_Satz\" size=\"1\">\n";
      // Alle gespeicherten MwSt-Saetze zur Auswahl auflisten
      foreach ($array_of_mwstsettings as $value) {
          if ($value->Beschreibung != "Porto und Verpackung") {
              echo "<option value=\"".$value->MwSt_Satz."\">".$value->MwSt_Satz."% (".$value->Beschreibung.")</option>\n";
          }
      }// end of foreach
      echo "<option value=\"0\">0% (MwSt-frei)</option>\n"; // 0% MwSt-Option anbieten
      echo "    </select>\n";
      echo "  </td>\n";
      echo "</tr>\n";
    }
?>
        <tr>
          <td>
            Anzeige:<br>
          </td>
          <td align="left">
            <INPUT TYPE='CHECKBOX' name="Details_anzeigen" title="Details_anzeigen">Beschreibung anzeigen<br>
          </td>
        </tr>
        <tr><td colspan="2">
          &nbsp;<br><br>Wo soll die neue Unterkategorie eingef&uuml;gt werden:<br>&nbsp;
        </td></tr>
<?php
    // Unterkategorienliste und zwischen jeder Unterkategorie "hier einfuegen"-Button ausgeben
    // Wenn keine Unterkategorie existiert, wird nur ein "hier einfuegen"-Button am Anfang angezeigt

    // Kategorien-Objekt-Array erzeugen und mit Kategorienobjekten füllen
    $myKategorien = array();
    $myKategorien = getallKategorien();

    foreach($myKategorien as $keyname => $value){
        // nur die Kategorie abarbeiten, in der wir die Unterkategorie erstellen wollen..
        if ($value->Name == stripslashes($Ukat_von)){
        // der Knopf, welcher auch angezeigt wird, wenn die Kategorie keine Unterkategorien hat
        echo '<tr><td colspan=\"2\"><a href=\'JavaScript:SaveForm("1")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';
            // schauen, ob die Kategorie Unterkategorien hat
            if ($value->kategorienanzahl() > 0){
            $myUnterkategorien = array();
            $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
                // allfaellig vorhanden Unterkategorien ausgeben
                for($i=0;$i < $value->kategorienanzahl();$i++){
                    echo "<tr><td colspan=\"2\"><b>".$myUnterkategorien[$i]->Name."</b></td></tr>\n";
                    echo '<tr><td colspan=\"2\"><a href=\'JavaScript:SaveForm("'.($myUnterkategorien[$i]->Positions_Nr+1).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';
                }// end of for
            } // end of if
        } // end of if
    }// End foreach myKategorien

    echo "<tr><td>&nbsp;</td></tr>";
    echo "<tr><td><a href='./Shop_Einstellungen_Menu_Kategorien.php' title='Abbrechen'>";
    echo "<img src='../Buttons/bt_abbrechen_admin.gif' border='0' align='absmiddle' alt='Abbrechen, zum Hauptmenu'></a><td></tr>";

?>
      </table>
    </form>
<?php

} // end of darstellen = 61

// -----------------------------------------------------------------------
// Eine neue Unterkategorie erstellen Schritt 2/2
// Speichern der Unterkategorie
// -----------------------------------------------------------------------
else if ($darstellen == 71) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";

    // Url-Dekodierung der Variable $Ukat_von_enc
    $Ukat_von = urldecode($Ukat_von_enc);

    // Test, ob eine gleichnamige Unterkategorie schon existiert. Wenn ja, Abbruch.
    $kategorienarray = getallKategorien();
    foreach($kategorienarray as $keyname => $value){
        // nur die Kategorie abarbeiten, in der wir die Unterkategorie erstellen wollen..
        if ($value->Name == $Ukat_von){
            // schauen, ob die Kategorie Unterkategorien hat
            if ($value->kategorienanzahl() > 0){
            $myUnterkategorien = array();
            $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
                // allfaellig vorhanden Unterkategorien vergleichen
                for($i=0;$i < $value->kategorienanzahl();$i++){
                    $name1 = strtoupper($myUnterkategorien[$i]->Name);
                    $name2 = strtoupper($Kategoriename);
                    if ($name1 == $name2) {
                        echo "<form action=\"$PHP_SELF\" method=\"post\" title=\"Unterkategorie erstellen - Fehler\" name=\"Zurueckformular\">\n";
                        echo "<br><font color=\"#ff0000\"> ACHTUNG!</font> Der Unterkategorienname '$Kategoriename' existiert schon.<br>\n";
                        echo "Es sind keine gleichnamigen Unterkategorien in der selben Hauptkategorie erlaubt.<br>\n";
                        echo "(Achtung: Gross-/Kleinschreibung wird nicht beachtet!)<br>\n";
                        echo "<input type=\"hidden\" name=\"darstellen\" value=\"61\" title=\"darstellen\">\n";
                        echo "<input type=\"hidden\" name=\"Ukat_von\" value=\"$Ukat_von\" title=\"Ukat_von\">\n";
                        echo "<input type=\"hidden\" name=\"Kategoriename\" value=\"$Kategoriename\" title=\"Kategoriename\">\n";
                        echo "Bitte den <i>Namen &auml;ndern</i>.<br><br>\n";
                        echo "<input type=\"image\" src=\"../Buttons/bt_zurueck_admin.gif\">\n";
                        echo "</form>\n</body>\n</html>\n";
                        // Page wurde geschlossen und das PHP-Script wird hier beendet
                        exit;
                    }// end of if
                }// end of for
            } // end of if
        } // end of if
    }// End foreach myKategorien

    // Details_anzeigen Checkbox auswerten:
    if ($HTTP_POST_VARS["Details_anzeigen"] == "on") {
        $Details_anzeigen = "Y";
    }
    else {
        $Details_anzeigen = "N";
    }

    if ($HTTP_POST_VARS["MwSt_Satz"] == "") {$MwSt_Satz = 0;}
    // Versuchen, neue Unterkategorie anzulegen
    if (newKategorie(trim($Kategoriename), trim($position), $Beschreibung, $Details_anzeigen, $MwSt_Satz, addslashes(trim($Ukat_von)))) {
        echo "<b>Die Unterkategorie ".stripslashes($Kategoriename)." wurde erfolgreich angelegt</b>";
    } // end of if newKategorie

    // wenn Kategorie nicht angelegt werden konnte
    else{
        echo "<b>Die Unterkategorie ".stripslashes($Kategoriename)." konnte nicht angelegt werden!</b>";
    }
    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";

} // end of darstellen = 71



// -----------------------------------------------------------------------
// Eine Kategorie an eine andere Position verschieben 1/2
// Auswahl der neuen Position
// -----------------------------------------------------------------------
else if ($darstellen == 20) {
    echo "</head>\n";
    echo "<body>\n";
?>
<SCRIPT LANGUAGE="JavaScript">

  // Hidden-Feld positionsnummer mit aktueller Positionsnummer fuellen, Formular abschicken
  function SaveForm(zaehler) {
      // Hidden-Feld "position" mit Positionsnummer, wo eingefuegt werden soll, beschreiben
      document.Formularname.position.value = zaehler;
      // Formular uebermitteln
      document.Formularname.submit();
  } // end of function SaveForm

</SCRIPT>

    <p><h1>SHOP ADMINISTRATION</h1></p>
    <b>Kategorie <?php echo stripslashes($Name); ?> verschieben</b>
    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Kategorie erstellen" name="Formularname">
      <table border=0>
        <tr><td>
          <INPUT TYPE="hidden" name=darstellen value=21 title="Weiche">
          <INPUT TYPE="hidden" name=position value=1 title="Kategorienposition">
          <INPUT TYPE="hidden" name=Kategorie_ID value=<?php echo  $Kategorie_ID; ?> title="Kategorie_ID">
          <INPUT TYPE="hidden" name=Kategoriename_enc value='<?php echo urlencode(stripslashes($Name)); ?>' title="Kategoriename">
          &nbsp;<br>Wohin soll die Kategorie <?php echo stripslashes($Name); ?> verschoben werden:<br>&nbsp;
        </td></tr>
<?php

    // Kategorien-Objekt-Array erzeugen und mit Kategorienobjekten füllen
    $myKategorien = array();
    $myKategorien = getallKategorien();

    // Variable, die die Positionsnummer der letzten Kategorie speichert (damit eine Kategorie
    // auch am Schluss eingefügt werden kann
    $letzte_kategorie = 1;

    // Kategorienliste und zwischen jeder Kategorie "hier einfuegen"-Button ausgeben
     foreach($myKategorien as $keyname => $value){
        if ($value->Kategorie_ID != $Kategorie_ID){
            echo '<tr><td><a href=\'JavaScript:SaveForm("'.($value->Positions_Nr).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';
            echo "<tr><td><b>".$value->Name."</b></td></tr>"."\n";
            $letzte_kategorie = $value->Positions_Nr;
        } // end of if
    }// end foreach myKategorien

    // "hier einfuegen"-Button ausgeben, der auch angezeigt wird, wenn keine Kategorie existiert"
    echo '<tr><td><a href=\'JavaScript:SaveForm("'.($letzte_kategorie+1).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';

    echo "<tr><td>&nbsp;</td></tr>";
    echo "<tr><td><a href='./Shop_Einstellungen_Menu_Kategorien.php' title='Abbrechen'>";
    echo "<img src='../Buttons/bt_abbrechen_admin.gif' border='0' align='absmiddle' alt='Abbrechen, zum Hauptmenu'></a><td></tr>";

?>
      </table>
    </form>
<?php

} // end of darstellen = 20

// -----------------------------------------------------------------------
// Eine Kategorie an eine andere Position verschieben 2/2
// Speichern der neuen Position
// -----------------------------------------------------------------------
else if ($darstellen == 21) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";

    $Kategoriename = urldecode($Kategoriename_enc);

    if (verschiebenKategorie(trim($Kategorie_ID), trim($position), "", "")) {
        echo "<b>Die Kategorie ".$Kategoriename." wurde erfolgreich verschoben.</b>";
    } // end of if newKategorie
    // wenn Kategorie nicht angelegt werden konnte
    else{
        echo "<b>Die Kategorie ".$Kategoriename." konnte nicht verschoben werden!</b>";
    }
    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";

} // end of darstellen = 21



// -----------------------------------------------------------------------
// Eine Unterkategorie an eine andere Position innerhalb der
// gleichen Kategorieverschieben 1/2
// Auswahl der neuen Position
// -----------------------------------------------------------------------
else if ($darstellen == 25) {
    echo "</head>\n";
    echo "<body>\n";
?>
<SCRIPT LANGUAGE="JavaScript">

  // Hidden-Feld positionsnummer mit aktueller Positionsnummer fuellen, Formular abschicken
  function SaveForm(zaehler) {
      // Hidden-Feld "position" mit Positionsnummer, wo eingefuegt werden soll, beschreiben
      document.Formularname.position.value = zaehler;
      // Formular uebermitteln
      document.Formularname.submit();
  } // end of function SaveForm

</SCRIPT>

    <p><h1>SHOP ADMINISTRATION</h1></p>
    <b>Unterkategorie <?php echo stripslashes($Name); ?> innerhalb <?php echo stripslashes($Ukat_alt);  ?> verschieben</b>
    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Kategorie erstellen" name="Formularname">
      <table border=0>
        <tr><td>
          <INPUT TYPE="hidden" name=darstellen value=26 title="Weiche">
          <INPUT TYPE="hidden" name=position value=1 title="Kategorienposition">
          <INPUT TYPE="hidden" name=Kategorie_ID value=<?php echo $Kategorie_ID; ?> title="Kategorie_ID">
          <INPUT TYPE="hidden" name=Name_enc value='<?php echo urlencode(stripslashes($Name)); ?>' title="Kategoriename">
          <INPUT TYPE="hidden" name=Ukat_alt_enc value='<?php echo urlencode(stripslashes($Ukat_alt)); ?>' title="Unterkategorie von">
          &nbsp;<br>Wohin soll die Unterkategorie <?php echo stripslashes($Name); ?> verschoben werden:<br>&nbsp;
        </td></tr>

<?php

    // Kategorien-Objekt-Array erzeugen und mit Kategorienobjekten füllen
    $myKategorien = array();
    $myKategorien = getallKategorien();

    // Variable, die die Positionsnummer der letzten Kategorie speichert (damit eine Kategorie
    // auch am Schluss eingefügt werden kann
    $letzte_kategorie = 1;

    foreach($myKategorien as $keyname => $value){
        // Nur die Kategorie abarbeiten, wo wir die Unterkategorie drin verschieben wollen
        if ($value->Name == stripslashes($Ukat_alt)){
            // schauen, ob die Kategorie Unterkategorien hat
            if ($value->kategorienanzahl() > 0){
                $myUnterkategorien = array();
                //Alle Unterkategorien in einen Array kopieren
                $myUnterkategorien = $value->getallkategorien();
               // Unterkategorienliste und zwischen jeder Unterkategorie "hier einfuegen"-Button ausgeben
                for($i=0;$i < $value->kategorienanzahl();$i++){
                    if ($myUnterkategorien[$i]->Kategorie_ID != $Kategorie_ID){
                        echo '<tr><td><a href=\'JavaScript:SaveForm("'.($myUnterkategorien[$i]->Positions_Nr).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';
                        echo "<tr><td><b>".$myUnterkategorien[$i]->Name."</b></td></tr>\n";
                        $letzte_kategorie = $myUnterkategorien[$i]->Positions_Nr;
                    } // end of if $myUnterkategorien[$i]->Kategorie_ID != $Kategorie_ID
                }// End of for $i=0;$i < $value->kategorienanzahl();$i++
            } // end of if value->kategorienanzahl
        } // end of if $value->Name
    }// End foreach myKategorien

    // Wenn keine Kategorie existiert, wird nur dieser "hier einfuegen"-Button angezeigt
    echo '<tr><td><a href=\'JavaScript:SaveForm("'.($letzte_kategorie+1).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';

    echo "<tr><td>&nbsp;</td></tr>";
    echo "<tr><td><a href='./Shop_Einstellungen_Menu_Kategorien.php' title='Abbrechen'>";
    echo "<img src='../Buttons/bt_abbrechen_admin.gif' border='0' align='absmiddle' alt='Abbrechen, zum Hauptmenu'></a><td></tr>";

?>
      </table>
    </form>
<?php

} // end of darstellen = 25

// -----------------------------------------------------------------------
// Eine Unterkategorie an eine andere Position innerhalb der
// gleichen Kategorieverschieben 2/2
// Speichern der neuen Position
// -----------------------------------------------------------------------
else if ($darstellen == 26) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";

    // Url-encodierte Variablen wieder decodieren
    $Ukat_alt = urldecode($Ukat_alt_enc);
    $Name = urldecode($Name_enc);

    // versuchen, die Unterkategorie innerhalb der Kategorie zu verschieben..
    if (verschiebenKategorie(trim($Kategorie_ID), trim($position), addslashes(trim($Ukat_alt)), addslashes(trim($Ukat_alt)))) {
        echo "<b>Die Unterkategorie ".$Name." wurde erfolgreich verschoben.</b>";
    } // end of if newKategorie

    // wenn Unterkategorie nicht verschoben werden konnnte..
    else{
        echo "<b>Die Unterkategorie ".$Name." konnte nicht verschoben werden!</b>";
    }

    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";

} // end of darstellen = 26



// -----------------------------------------------------------------------
// Eine Unterkategorie an eine beliebige Stelle verschieben 1/2
// Auswahl der neuen Position
// -----------------------------------------------------------------------
else if ($darstellen == 30) {
    echo "</head>\n";
    echo "<body>\n";
?>
<SCRIPT LANGUAGE="JavaScript">

  // Hidden-Feld positionsnummer mit aktueller Positionsnummer fuellen, Formular abschicken
  function SaveForm(zaehler, neue_kat) {
      // Hidden-Feld "position" mit Positionsnummer, wo eingefuegt werden soll, beschreiben
      document.Formularname.position.value = zaehler;
      document.Formularname.Ukat_neu.value = neue_kat;
      // Formular uebermitteln
      document.Formularname.submit();
  } // end of function SaveForm

</SCRIPT>

    <p><h1>SHOP ADMINISTRATION</h1></p>
    <b>Unterkategorie <?php echo stripslashes($Name); ?> an beliebige Stelle verschieben</b>
    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Kategorie erstellen" name="Formularname">
      <table border=0>
        <tr><td colspan=2>
          <INPUT TYPE="hidden" name=darstellen value=31 title="Weiche">
          <INPUT TYPE="hidden" name=position value=1 title="Kategorienposition">
          <INPUT TYPE="hidden" name=Kategorie_ID value=<?php echo $Kategorie_ID; ?> title="Kategorie_ID">
          <INPUT TYPE="hidden" name=Name_enc value='<?php echo urlencode(stripslashes($Name)); ?>' title="Kategoriename">
          <INPUT TYPE="hidden" name=Ukat_alt_enc value='<?php echo urlencode(stripslashes($Ukat_alt)); ?>' title="Unterkategorie von">
          <INPUT TYPE="hidden" name=Ukat_neu value=1 title="Unerkategorie neu von">
          &nbsp;<br>Wohin soll die Unterkategorie <?php echo stripslashes($Name); ?> verschoben werden:
        </td></tr>
        <tr><td colspan=2>&nbsp;<td></tr>
<?php


    // Kategorien-Objekt-Array erzeugen und mit Kategorienobjekten füllen
    $myKategorien = array();
    $myKategorien = getallKategorien();

    // Alle Kategorien mit Ihren Unterkategorien sowie "hier einfuegen"-Buttons ausgeben
    foreach($myKategorien as $keyname => $value){
        if ($value->Name != stripslashes($Ukat_alt)){
            echo "<tr><td colspan=2 bgcolor=#CCCCCC><b>".$value->Name."</b></td></tr>";
            // Variable, die die Positionsnummer der letzten Unterkategorie speichert
            $letzte_kategorie = 0;
            // schauen, ob die Kategorie Unterkategorien hat..
            if ($value->kategorienanzahl() > 0){
                $myUnterkategorien = array();
                $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
                // Jede Unterkategorien dieser Kategorie mit einem folgende "hier einfuegen"-Buttons ausgeben
                for($i=0;$i < $value->kategorienanzahl();$i++){
                    if ($myUnterkategorien[$i]->Kategorie_ID != $Kategorie_ID){
                        echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><a href=\'JavaScript:SaveForm("'.($myUnterkategorien[$i]->Positions_Nr).'","'.urlencode(addslashes($value->Name)).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';
                        echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>".$myUnterkategorien[$i]->Name."</td></tr>\n";
                        $letzte_kategorie = $myUnterkategorien[$i]->Positions_Nr;
                    }  // end of if myUnterkategorien[$i]->Kategorie_ID
                }// end of for $i=0;$i < $value->kategorienanzahl();$i++
            } // end of if value->kategorienanzahl
            // Wenn keine Unterkategorie in dieser Kategorie existiert, wird nur ein "hier einfuegen"-Button angezeigt
            echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><a href=\'JavaScript:SaveForm("'.($letzte_kategorie+1).'","'.urlencode(addslashes($value->Name)).'")\'><img src="../Buttons/bt_hier_einfuegen.gif" border="0" alt="hier einf&uuml;gen"></a><td></tr>';
        } // end of if $value->Name == $Ukat_alt
    }// End foreach myKategorien

    echo "<tr><td colspan=2>&nbsp;</td></tr>";
    echo "<tr><td colspan=2><a href='./Shop_Einstellungen_Menu_Kategorien.php' title='Abbrechen'>";
    echo "<img src='../Buttons/bt_abbrechen_admin.gif' border='0' align='absmiddle' alt='Abbrechen, zum Hauptmenu'></a><td></tr>";

?>
      </table>
    </form>
<?php

} // end of darstellen = 30

// -----------------------------------------------------------------------
// Eine Unterkategorie an eine beliebige Stelle verschieben 2/2
// Speichern der neuen Position
// -----------------------------------------------------------------------
else if ($darstellen == 31) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";

    // Url-encodierte Variablen decodieren
    $Name = urldecode($Name_enc);
    $Ukat_alt = urldecode($Ukat_alt_enc);
    $Ukat_neu = stripslashes(urldecode($Ukat_neu));

    // versuchen, die Unterkategorie zu verschieben
    if (verschiebenKategorie(trim($Kategorie_ID), trim($position), addslashes(trim($Ukat_alt)), addslashes(trim($Ukat_neu)))) {
        echo "<b>Die Unterkategorie ".$Name." wurde erfolgreich in die Kategorie ".$Ukat_neu." verschoben.</b>";
    } // end of if newKategorie

    // wenn Unterkategorie nicht verschoben werden konnte..
    else{
        echo "<b>Die Unterkategorie ".$Name." konnte nicht in die Kategorie ".$Ukat_neu." verschoben werden!</b>";
    }

    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";
} // end of darstellen = 31



// -----------------------------------------------------------------------
// Die Eigenschaften (Attribute) einer Hauptkategorie updaten 1/2
// Editieren der Attribute
// -----------------------------------------------------------------------
else if ($darstellen == 35) {
    echo "</head>\n";
    echo "<body>\n";

    // Auslesen und aufbereiten der Kategoriedaten
    $Kategorie = getKategorie($Kategorie_ID);
?>

<SCRIPT LANGUAGE="JavaScript">

  function chkFormular() {
       // ueberpruefen, ob ein Kategoriename eingegeben wurde
      if(document.Formular.Name.value == "") {
          alert("Bitte einen Kategorienamen eingeben!");
          document.Formular.Name.focus();
      }
      else {
         // Formular uebermitteln
         document.Formular.submit();
      }
  } // end of function chkFormular

</SCRIPT>

<?php
    // Wenn ein Kategorienname ein einfaches Hochkomma enthaelt, so wird das als Ende des Imput Feldes angesehen.
    // Wir muessen also alle Hochkommas durch ihren HTML-Code (&#39;) ersetzen, diese Positionen uns merken und dann, beim
    // Speichern ev. diese wieder als ge-addslashte Hochkommas zurueckwandeln (darstellen == 36). Im Moment noch nicht noetig.
    $positionen = "";         // Initialisierung. Via Strichpunkt delimitierte Positionen von einfachen Hochkommas im Namen
    $gefunden = false;        // Initialisierung
    $Name_bearbeitet = $Name; // Initialisierung
    while ($gefunden = strpos($Name_bearbeitet, "'")) {
        if ($positionen == "") {
            $positionen = $gefunden;
        }
        else {
            $positionen = $positionen.";".$gefunden;
        }
        $teil1 = substr($Name_bearbeitet,0,$gefunden);
        // Test ob das Hochkomma das letzte Zeichen war
        if ($gefunden == (strlen($Name_bearbeitet)-1)) {
            $teil2 = "&#39;";
        }
        else {
            $teil2 = "&#39;".substr($Name_bearbeitet,($gefunden+1),strlen($Name_bearbeitet));
        }
        $Name_bearbeitet = $teil1.$teil2;
    }
    // Damit Statements wie <!-- den Browser nicht durcheinander bringen, ersetzen wir das Konstrukt durch &lt;!--
    $Name_bearbeitet = str_replace("<!--","&lt;&#33;--", $Name_bearbeitet);
    $Name = str_replace("<!--","&lt;&#33;--", $Name);
?>
    <p><h1>SHOP ADMINISTRATION</h1></p>
    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Kategorie Eigenschaften bearbeiten" onSubmit="return chkFormular()" name="Formular">
      <table border=0>
        <tr><td colspan="2">
          &nbsp;<br><b>Eigenschaften der Hauptkategorie '<?php echo stripslashes($Name); ?>' bearbeiten</b><br>&nbsp;
        </td></tr>
        <tr>
          <td>
            Name:<br>
          </td>
          <td>
            <INPUT TYPE='TEXT'  name='Name' value='<?php echo stripslashes($Name_bearbeitet); ?>' size='32' maxlength='128' title='Kategoriename'><br>
          </td>
        </tr>
        <tr>
          <td valign="top">
            Beschreibung:<br>
          </td>
          <td>
            <textarea name="Beschreibung" cols="50" rows="10" wrap=physical><?php echo htmlspecialchars($Kategorie->Beschreibung); ?></textarea><br>
          </td>
        </tr>
<?php
      // MwSt-Satz Eingabefeld (Dropdown-Liste)
    if (getmwstnr() != "0" && getmwstnr() != "") {
      // Zuerst die MwSt-Settings auslesen und in einen Array abpacken
      $array_of_mwstsettings = getmwstsettings();
      // Eigentliches Dropdown-Menu erzeugen
      echo "<tr>\n";
      echo "  <td valign=\"top\">\nMwSt default Satz:\n</td>\n";
      echo "  <td valign=\"middle\">\n";
      echo "    <select name=\"MwSt\" size=\"1\">\n";
      // Alle gespeicherten MwSt-Saetze zur Auswahl auflisten
      $selected = false; // Flag falls noch kein Feld selektiert wurde
      $no_mwst = false; // Flag um in obigem Falle eine Meldung ausgeben zu koennen
      // Test ob die Tabelle schon einen gueltigen MwSt-Default-Satz besitzt
      foreach ($array_of_mwstsettings as $value) {
          if ($value->MwSt_Satz == $Kategorie->MwSt_default_Satz) {$selected = true; $no_mwst = true;}
      }
      if ($Kategorie->MwSt_default_Satz == "0") {$selected = true; $no_mwst = true;}
      foreach ($array_of_mwstsettings as $value) {
          if ($value->Beschreibung != "Porto und Verpackung") { //Porto und Verpackung MwSt-Satz ausblenden
              echo "<option";
              if ($value->MwSt_Satz == $Kategorie->MwSt_default_Satz || $selected == false) {echo " selected"; $selected = true;}
              echo " value=\"".$value->MwSt_Satz."\">".$value->MwSt_Satz."% (".$value->Beschreibung.")</option>\n";
          }
      }// end of foreach
      echo "<option value=\"0\" ";
      if ("0" == $Kategorie->MwSt_default_Satz) {echo "selected";}
      echo ">0% (MwSt-frei)</option>\n"; // 0% MwSt-Option anbieten
      echo "    </select>\n";
      if ($no_mwst == false) {echo "<small>(Info: Es war kein g&uuml;ltiger MwSt-Satz vorgew&auml;hlt!)</small>";}
      echo "  </td>\n";
      echo "</tr>\n";
    }
?>
        <tr>
          <td>
            Anzeige:<br>
          </td>
          <td align="left">
            <INPUT TYPE='CHECKBOX' name="Details_anzeigen" title="Details_anzeigen" <?php if ($Kategorie->Details_anzeigen == "Y") {echo "checked";}?>> Kategorienbeschreibung anzeigen<br>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <br>
            <nobr>
            <INPUT TYPE="hidden" name=darstellen value=36 title="Weiche">
            <INPUT TYPE="hidden" name=Kategorie_ID value=<?php echo $Kategorie_ID; ?> title="Kategorie_ID">
            <a href="JavaScript:chkFormular()" title="Speichern">
            <img src="../Buttons/bt_speichern_admin.gif" border="0" alt="speichern"></a>
            <a href="./Shop_Einstellungen_Menu_Kategorien.php" title="Abbrechen">
            <img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a>
            </nobr>
          </td>
        </tr>
      </table>
    </form>
<?php

} // end of darstellen = 35

// -----------------------------------------------------------------------
// Die Eigenschaften (Attribute) einer Hauptkategorie updaten 2/2
// Speichern der Eingaben von vorigem Formular
// -----------------------------------------------------------------------
else if ($darstellen == 36) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";

/*  // Url-decodierte Variable encodieren
    $Name = urldecode($Name_enc);
    $Beschreibung = urldecode($Beschreibung_enc);
*/
    // Kategorieobjekt instanzieren und mit Werten abfuellen
    $myKategorie = new Kategorie();
    $myKategorie->Kategorie_ID = trim($HTTP_POST_VARS["Kategorie_ID"]);
    $Name = $HTTP_POST_VARS["Name"];
    // Damit Statements wie <!-- den Browser nicht durcheinander bringen, ersetzen wir das Konstrukt durch &lt;!--
    $Name = str_replace("<!--","&lt;&#33;--", $Name);
    $myKategorie->Name = trim($Name);
    $myKategorie->Beschreibung = trim($HTTP_POST_VARS["Beschreibung"]);
    // Wenn der Shop nicht MwSt-pflichtig ist, so wird hierhin keine MwSt uebertragen -> auf 0 setzen:
    if ($HTTP_POST_VARS["MwSt"] == "") {
        $myKategorie->MwSt_Satz = 0;
    }
    else {
        $myKategorie->MwSt_Satz = $HTTP_POST_VARS["MwSt"];
    }
    if ($HTTP_POST_VARS["Details_anzeigen"] == "on") {
        $myKategorie->Details_anzeigen = "Y";
        $Ukat_array = checkaufUnterkategorien($myKategorie->Name);
        if (count($Ukat_array) > 0) {
            $myKategorie->Details_anzeigen = "N";
            echo "<font color=\"#ff0000\">ACHTUNG! </font>Sie k&ouml;nnen die Beschreibung dieser Hauptkategorie nicht anzeigen lassen, wenn diese noch Unterkategorien besitzt.<br>";
            echo "Es wird dann die Beschreibung der entsprechenden Unterkategorie angezeigt. Beide miteinander w&uuml;rden verwirrend wirken.<br>";
            echo "<i>Die Anzeigeoption wurde automatisch wieder deaktiviert.</i><br><br>";
        }// enf of if count...
    }// end of if HTTP_...
    else {
        $myKategorie->Details_anzeigen = "N";
    }// end of else

    // Versuchen, die Kategorie umzubenennen..
    if ($myKategorie->Name == "@PhPepperShop@") {
        echo "<b>Die Kategorie '".stripslashes($myKategorie->Name)."' konnte nicht upgedated werden, dieser Name ist reserviert und darf nicht verwendet werden!</b>";
    }
    else if (setKategorie($myKategorie)) {
        echo "<b>Die Hauptkategorie '".stripslashes($myKategorie->Name)."' wurde erfolgreich upgedated.</b>";
    } // end of if umbenennKategorie

    // wenn Kategorie nicht upgedated werden konnte...
    else{
        echo "<b>Die Hauptkategorie '".stripslashes($myKategorie->Name)."' konnte nicht upgedated werden!</b>";
    } // end of else

    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";

} // end of darstellen = 36



// -----------------------------------------------------------------------
// Die Eigenschaften (Attribute) einer Unterkategorie updaten 1/2
// Editieren der Attribute
// -----------------------------------------------------------------------
else if ($darstellen == 40) {
    echo "</head>\n";
    echo "<body>\n";

    // Auslesen und aufbereiten der Kategoriedaten
    $Kategorie = getKategorie($Kategorie_ID);
    // Wenn ein Kategorienname ein einfaches Hochkomma enthaelt, so wird das als Ende des Imput Feldes angesehen.
    // Wir muessen also alle Hochkommas durch ihren HTML-Code (&#39;) ersetzen, diese Positionen uns merken und dann, beim
    // Speichern ev. diese wieder als ge-addslashte Hochkommas zurueckwandeln (darstellen == 36). Im Moment noch nicht noetig.
    $positionen = "";         // Initialisierung. Via Strichpunkt delimitierte Positionen von einfachen Hochkommas im Namen
    $gefunden = false;        // Initialisierung
    $Name_bearbeitet = $Name; // Initialisierung
    while ($gefunden = strpos($Name_bearbeitet, "'")) {
        if ($positionen == "") {
            $positionen = $gefunden;
        }
        else {
            $positionen = $positionen.";".$gefunden;
        }
        $teil1 = substr($Name_bearbeitet,0,$gefunden);
        // Test ob das Hochkomma das letzte Zeichen war
        if ($gefunden == (strlen($Name_bearbeitet)-1)) {
            $teil2 = "&#39;";
        }
        else {
            $teil2 = "&#39;".substr($Name_bearbeitet,($gefunden+1),strlen($Name_bearbeitet));
        }
        $Name_bearbeitet = $teil1.$teil2;
    }
    // Damit Statements wie <!-- den Browser nicht durcheinander bringen, ersetzen wir das Konstrukt durch &lt;!--
    $Name_bearbeitet = str_replace("<!--","&lt;&#33;--", $Name_bearbeitet);
    $Name = str_replace("<!--","&lt;&#33;--", $Name);
?>

<SCRIPT LANGUAGE="JavaScript">

  function chkFormular() {
       // ueberpruefen, ob ein Kategoriename eingegeben wurde
      if(document.Formular.Name.value == "") {
          alert("Bitte einen Unterkategorienamen eingeben!");
          document.Formular.Name.focus();
      }
      else {
         // Formular uebermitteln
         document.Formular.submit();
      }
  } // end of function chkFormular

</SCRIPT>

    <p><h1>SHOP ADMINISTRATION</h1></p>
    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Unterkategorie Eigenschaften bearbeiten" onSubmit="return chkFormular()" name="Formular">
      <table border=0>
        <tr><td colspan="2">
          &nbsp;<br><b>Eigenschaften der Unterkategorie '<?php echo stripslashes($Name); ?>' bearbeiten</b><br>&nbsp;
        </td></tr>
        <tr>
          <td>
            Name:<br>
          </td>
          <td>
            <input type='text'  name='Name' value='<?php echo stripslashes($Name_bearbeitet); ?>' size='32' maxlength='128' title='Kategoriename'><br>
          </td>
        </tr>
        <tr>
          <td valign="top">
            Beschreibung:<br>
          </td>
          <td>
            <textarea name="Beschreibung" cols="50" rows="10" wrap=physical><?php echo htmlspecialchars($Kategorie->Beschreibung); ?></textarea><br>
          </td>
        </tr>
<?php
      // MwSt-Satz Eingabefeld (Dropdown-Liste)
    if (getmwstnr() != "0" && getmwstnr() != "") {
      // Zuerst die MwSt-Settings auslesen und in einen Array abpacken
      $array_of_mwstsettings = getmwstsettings();
      // Eigentliches Dropdown-Menu erzeugen
      echo "<tr>\n";
      echo "  <td valign=\"top\">\nMwSt default Satz:\n</td>\n";
      echo "  <td valign=\"middle\">\n";
      echo "    <select name=\"MwSt\" size=\"1\">\n";
      // Alle gespeicherten MwSt-Saetze zur Auswahl auflisten
      $selected = false; // Flag falls noch kein Feld selektiert wurde
      $no_mwst = false; // Flag um in obigem Falle eine Meldung ausgeben zu koennen
      // Test ob die Tabelle schon einen gueltigen MwSt-Default-Satz besitzt
      foreach ($array_of_mwstsettings as $value) {
          if ($value->MwSt_Satz == $Kategorie->MwSt_default_Satz) {$selected = true; $no_mwst = true;}
      }
      if ($Kategorie->MwSt_default_Satz == "0") {$selected = true; $no_mwst = true;} // Fuer Spezialfall MwSt-frei (0%)
      foreach ($array_of_mwstsettings as $value) {
          if ($value->Beschreibung != "Porto und Verpackung") { //Porto und Verpackung MwSt-Satz ausblenden
              echo "<option";
              if ($value->MwSt_Satz == $Kategorie->MwSt_default_Satz || $selected == false) {echo " selected"; $selected = true;}
              echo " value=\"".$value->MwSt_Satz."\">".$value->MwSt_Satz."% (".$value->Beschreibung.")</option>\n";
          }
      }// end of foreach
      // Spezialfall MwSt-frei (0% MwSt):
      echo "<option value=\"0\" ";
      if ("0" == $Kategorie->MwSt_default_Satz) {echo "selected";}
      echo ">0% (MwSt-frei)</option>\n"; // 0% MwSt-Option anbieten
      echo "    </select>\n";
      if ($no_mwst == false) {echo "<small>(Info: Es war kein g&uuml;ltiger MwSt-Satz vorgew&auml;hlt!)</small>";}
      echo "  </td>\n";
      echo "</tr>\n";
    }
?>
        <tr>
          <td>
            Anzeige:<br>
          </td>
          <td align="left">
            <INPUT TYPE='CHECKBOX' name="Details_anzeigen" title="Details_anzeigen" <?php if ($Kategorie->Details_anzeigen == "Y") {echo "checked";}?>> Kategorienbeschreibung auch anzeigen<br>
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <br>
            <nobr>
            <INPUT TYPE="hidden" name="darstellen" value="41" title="Weiche">
            <INPUT TYPE="hidden" name="Kategorie_ID" value="<?php echo $Kategorie_ID; ?>" title="Kategorie_ID">
            <a href="JavaScript:chkFormular()" title="Speichern">
            <img src="../Buttons/bt_speichern_admin.gif" border="0" alt="speichern"></a>
            <a href="./Shop_Einstellungen_Menu_Kategorien.php" title="Abbrechen">
            <img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a>
            </nobr>
          </td>
        </tr>
      </table>
    </form>
<?php

} // end of darstellen = 40

// -----------------------------------------------------------------------
// Die Eigenschaften (Attribute) einer Unterkategorie updaten 2/2
// Speichern der neuen Eingaben
// -----------------------------------------------------------------------
else if ($darstellen == 41) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";
    // Kategorieobjekt instanzieren und mit Werten abfuellen
    $myKategorie = new Kategorie();
    $myKategorie->Kategorie_ID = trim($HTTP_POST_VARS["Kategorie_ID"]);
    // Damit Statements wie <!-- den Browser nicht durcheinander bringen, ersetzen wir das Konstrukt durch &lt;!--
    $Name = str_replace("<!--","&lt;&#33;--", $Name);
    $myKategorie->Name = trim($Name);
    $myKategorie->Beschreibung = trim($HTTP_POST_VARS["Beschreibung"]);
    // Wenn der Shop nicht MwSt-pflichtig ist, so wird hierhin keine MwSt uebertragen -> auf 0 setzen:
    if ($HTTP_POST_VARS["MwSt"] == "") {
        $myKategorie->MwSt_Satz = 0;
    }
    else {
        $myKategorie->MwSt_Satz = $HTTP_POST_VARS["MwSt"];
    }

    if ($HTTP_POST_VARS["Details_anzeigen"] == "on") {
        $myKategorie->Details_anzeigen = "Y";
    }
    else {
        $myKategorie->Details_anzeigen = "N";
    }
    // Versuchen, die Kategorie umzubenennen..
    if ($myKategorie->Name == "@PhPepperShop@") {
        echo "<b>Die Unterkategorie '".stripslashes($myKategorie->Name)."' konnte nicht upgedated werden, dieser Name ist reserviert und darf nicht verwendet werden!</b>";
    }
    else if (setKategorie($myKategorie)) {
        echo "<b>Die Unterkategorie '".stripslashes($myKategorie->Name)."' wurde erfolgreich upgedated.</b>";
    } // end of if umbenennKategorie
    // wenn Kategorie nicht upgedated werden konnte...
    else{
        echo "<b>Die Unterkategorie '".stripslashes($myKategorie->Name)."' konnte nicht upgedated werden!</b>";
    } // end of else
    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";
} // end of darstellen = 41



// -----------------------------------------------------------------------
// Eine Kategorie/Unterkategorie loeschen 1/2
// Bestätigungsanfrage!
// -----------------------------------------------------------------------
else if ($darstellen == 45) {
    echo "</head>\n";
    echo "<body>\n";
?>

    <p><h1>SHOP ADMINISTRATION</h1></p>

<?php
    // Variable $typ: wenn Inhalt = 'kat' -> eine Kategorie soll geloescht werden
    //                wenn Inhalt != 'kat' -> eine Unterkategorie soll geloescht werden
    if ($typ == 'kat') echo"<p><b>Kategorie ".stripslashes($Name)." l&ouml;schen</b></p>";
    else echo"<p><b>Unterkategorie ".stripslashes($Name)." l&ouml;schen</b></p>";
?>

    <form action="<?php echo $PHP_SELF; ?>" method="post" title="Unter-/Kategorie loeschen"  name="Formular">
      <table border=0 width=80%>
        <tr><td>
<?php
    // Loeschtext fuer Kategorie ausgeben
    if ($typ == 'kat') { ?>
          <p>Was wollen Sie mit den allf&auml;llig noch vorhandenen Artikeln machen
          , die sich noch in der Kategorie <?php echo stripslashes($Name); ?>(oder ihrer/n Unterkategorie/n) befinden
          &nbsp;und keiner weiteren Kategorie/Unterkategorie zugeordnet sind, welche in diesem
          &nbsp;Schritt nicht gel&ouml;scht wird:</p>
<?php }
    // Loeschtext fuer Unterategorie ausgeben
    else { ?>
          <p>Was wollen Sie mit den allf&auml;llig noch vorhandenen Artikeln machen
          , die sich noch in der Unterkategorie <?php echo stripslashes($Name); ?> befinden
          &nbsp;und keiner weiteren Kategorie/Unterkategorie zugeordnet sind, welche in diesem
          &nbsp;Schritt nicht gel&ouml;scht wird:</p>
<?php } ?>

        </td></tr>
        <tr><td>
          &nbsp;<br>
          <input type="radio" value=0 name="Artikel_loeschen" checked>Artikel der Kategorie "Nicht zugeordnete Artikel" zuweisen<br>
          <input type="radio" value=1 name="Artikel_loeschen"><font color=#ff0000>Artikel unwiederruflich l&ouml;schen!</font><br>
          <INPUT TYPE="hidden" name=darstellen value=46 title="Weiche">
          <INPUT TYPE="hidden" name=Kategorie_ID value=<?php echo $Kategorie_ID; ?> title="Kategorie_ID">
          <INPUT TYPE="hidden" name=Name_enc value='<?php echo urlencode(stripslashes($Name)); ?>' title="Name">
          <INPUT TYPE="hidden" name=typ value='<?php echo $typ; ?>' title="Kategorietyp">
        </td></tr>
        <tr><td>
          <br><nobr>
          <input type=image src="../Buttons/bt_weiter_admin.gif" border="0" alt"löschen">
          <a href="./Shop_Einstellungen_Menu_Kategorien.php" title="Abbrechen">
          <img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a>
          </nobr>
        </td></tr>
      </table>
    </form>
<?php

} // end of darstellen = 45

// -----------------------------------------------------------------------
// Eine Kategorie/Unterkategorie löschen 1/2
// Löschvorgang ausführen!
// -----------------------------------------------------------------------
else if ($darstellen == 46) {
    echo "</head>\n";
    echo "<body>\n";

    echo "<p><h1>SHOP ADMINISTRATION</h1></p>";

    // Url-encodierte Variable decodieren
    $Name = urldecode($Name_enc);

    // versuchen, Kategorie bzw. Unterkategorie zu loeschen
    if (delKategorie(trim($Kategorie_ID), trim($Artikel_loeschen))) {
        if ($typ == 'kat') echo "<b>Die Kategorie $Name wurde erfolgreich gelöscht!</b>";
        else echo "<b>Die Unterkategorie $Name wurde erfolgreich gelöscht!</b>";
    } // end of if del Kategorie

    // wenn Kategorie bzw. Unterkategorie nicht geloescht werden konnte..
    else{
        if ($typ == 'kat') echo "<b>Die Kategorie $Name konnte nicht gelöscht werden!</b>";
        else echo "<b>Die Unterkategorie $Name konnte nicht gelöscht werden!</b>";
    }

    echo "<br><br><a href='./Shop_Einstellungen_Menu_Kategorien.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";

} // end of darstellen = 46

// -----------------------------------------------------------------------
// falls kein gueltige darstellen uebergeben wurde..
// -----------------------------------------------------------------------
else {
    echo "</head>\n";
    echo "<body>\n";

    echo "<H1>Fehlerhafter Aufruf! (darstellen-Variable nicht gesetzt)</H1><BR>";
}//End else

echo "</body>\n";
echo "</html>\n";

  // End of file ------------------------------------------------------------------
?>
