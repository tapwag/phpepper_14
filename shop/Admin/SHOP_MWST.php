<?php
  // Filename: SHOP_MWST.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet die Maske zu MwSt Mutationen des Administrators
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_MWST.php,v 1.17 2003/05/24 18:41:34 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_MWST = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
  if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);
  extract($_SERVER);

  // Vorlaeufig hardcodierte Anzahl MwSt-Settings:
  $anzahl_mwstsettings = 10;

  // HTML-Kopf, der bei jedem Aufruf des Files ausgegeben wird
?>
<HTML>
    <HEAD>
        <TITLE>Mehrwertsteuermanagement</TITLE>
        <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
        <META HTTP-EQUIV="language" CONTENT="de">
        <META HTTP-EQUIV="author" CONTENT="Jose Fontanil & Reto Glanzmann">
        <META NAME="robots" CONTENT="all">
        <LINK REL=STYLESHEET HREF="./shopstyles.css" TYPE="text/css">

        <SCRIPT LANGUAGE="JavaScript">
            <!-- Begin
                function popUp(URL) {
                    day = new Date();
                    id = day.getTime();
                    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=620,height=420,left = 100,top = 100');");
                }
            // End -->
        </script>
    </HEAD>
    <BODY>

<?php
  // ---------------------------------------------------------------------------------
  // darstellen = 10
  // hier werden die MwSt-Settings gespeichert (Funktion setmwstsettings)
  // und es wird eine entsprechende Ausgabe erfolgen
  if ($darstellen == 10){
    // Variableninitialisierung
    $array_of_mwstsettings = array();
    $MwSt_default_wurde_vergeben = false; // Sicherheitstest damit einer der Radiobuttons angewählt wird.
    $min_ein_Setting_vorhanden = false; // Test ob min. ein MwSt-Setting eingegeben wurde
    // Alle Formularwerte in MwSt-Objekte und diese in einen Array abpacken
    // Solange es Formularzeilen (MwSt-Settings) hat, einlesen und abpacken
    for ($counter = 0; $counter < $anzahl_mwstsettings; $counter++) {
        $myMwSt = new MwSt();
        // Auslesen der Felder und abpacken ins Objekt
        $myMwSt->MwSt_Satz = $HTTP_POST_VARS["MwSt_Satz$counter"];
        $myMwSt->MwSt_default_Satz = $HTTP_POST_VARS["MwSt_default_Satz"];
        $myMwSt->Beschreibung = $HTTP_POST_VARS["Beschreibung$counter"];
        $myMwSt->Preise_inkl_MwSt = $HTTP_POST_VARS["Preise_inkl_MwSt$counter"];
        $myMwSt->Mehrwertsteuer_ID = $HTTP_POST_VARS["Mehrwertsteuer_ID$counter"];
        $myMwSt->Positions_Nr = $HTTP_POST_VARS["Positions_Nr$counter"];
        // Aufbereitung der Checkboxen Felder:
        if ($myMwSt->Preise_inkl_MwSt == "on") {
            $myMwSt->Preise_inkl_MwSt = "Y";
        }
        elseif ($myMwSt->Preise_inkl_MwSt == "") {
            $myMwSt->Preise_inkl_MwSt = "N";
        }
        // Bevor wir nun die Schreiben-Funktion starten koennen, muessen wir noch drei
        // weitere Variablen aufbereiten:
        if ($myMwSt->MwSt_default_Satz == "MwSt_default_Satz$counter") {
            $myMwSt->MwSt_default_Satz = "Y";
            // Wenn dieses Setting nicht gleich nachher geloescht werden soll, wurde ein Default gesetzt
            if ($myMwSt->MwSt_Satz != "") {$MwSt_default_wurde_vergeben = true;}
        }
        else {
            $myMwSt->MwSt_default_Satz = "N";
        }
        // MwSt-Objekt in Array abfuellen
        $array_of_mwstsettings[] = $myMwSt;
        // Test ob ueberhaupt ein MwSt-Setting angegeben wurde
        if ($myMwSt->MwSt_Satz != "") {
            $min_ein_Setting_vorhanden = true;
        }
    }// End for
    if ($min_ein_Setting_vorhanden == false) {
?>        <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <h1>SHOP ADMINISTRATION</h1>
                    <h3>Sie m&uuml;ssen mindestens <i>einen</i> Mehrwertsteuersatz definieren!<BR></h3>
                    <a href='./SHOP_MWST.php' title='Abbrechen'>
                        <img src='../Buttons/bt_zurueck_admin.gif' border='0' align='absmiddle' alt='Abbruch wegen leeren Intervallen'></a>
                </td>
            </tr>
          </table>
        </BODY>
      </HTML>
<?php
      exit;// Abbruch, da keine MwSt-Settings uebergeben wurden!
    }
    // Test ob ein MwSt_default_Satz gewaelt wurde, wenn nicht, ersten Satz wählen und User darauf aufmerksam machen
    $achtung = false;
    if ($MwSt_default_wurde_vergeben == false) {
        $noch_nicht_fertig = true; // Abbruchvariable
        $zaehler = 0; // Countervariable
        while ($noch_nicht_fertig) {
            // Wenn das erste Setting geloescht werden soll, nicht dieses zum Default machen, sondern zum naechsten gehen - u.s.w.
            if ($array_of_mwstsettings[$zaehler]->MwSt_Satz != "" && $array_of_mwstsettings[$zaehler]->Beschreibung != "Porto und Verpackung") {
                $array_of_mwstsettings[$zaehler]->MwSt_default_Satz = "Y";
                $noch_nicht_fertig = false;
            }// End if
            $zaehler = $zaehler + 1;
        }// End while
        $achtung = true; // Es musste ein neuer MwSt-Default-Satz (shopweiter Standard MwSt-Satz) ausgewaehlt werden
    }
    // Eigentlicher Funktionsaufruf zum Schreiben der MwSt-Einstellungen in die Datenbank
    $Nr_setting = true; // Flag, welches false wird, wenn die Speicherung der MwST-Nummer nicht klappt
    if ($HTTP_POST_VARS["MwStpflichtig"] == "") {$MwStNummer = "0";} else {$MwStNummer = $HTTP_POST_VARS["MwStNummer"];}
    if (!setmwstnr($MwStNummer)) {
        $Nr_setting = false;
    }
    // Test ob ein geloeschter MwSt-Satz vorhanden ist, und ob gegebenenfalls irgendwelche Hauptkategorien/Unterkategorien/Artikel upgedated werden muessen
    // 1.) auslesen der alten MwSt-Einstellungen
    $oldmwst_array = getmwstsettings();
    // In einen Array werden alle MwSt-Saetze geschrieben, welche im neuen MwSt-Setting nicht mehr vorhanden sind
    $todo_array = array(); //Initialisierung eines temporaeren Arrays (enthaelt spaeter alle geloeschten MwSt-Saetze)
    foreach ($oldmwst_array as $oldvalue) {
        $found_flag = false; // Dieses Flag wird true, wenn dieser MwSt-Satz
        foreach ($array_of_mwstsettings as $newvalue) {
            if (($oldvalue->MwSt_Satz == $newvalue->MwSt_Satz && $newvalue->MwSt_Satz != "") || ($oldvalue->Beschreibung == "Porto und Verpackung")) {
                $found_flag = true;
                break;
            }
        }
        // Auswertung ob dieser oldmwst_array-MwSt-Satz geloescht wurde
        if ($found_flag == false) {
            $todo_array[] = $oldvalue->MwSt_Satz;
        }

    }
    // Neuen Standard-MwSt-Satz auslesen
    foreach ($array_of_mwstsettings as $newvalue) {
        if ($newvalue->MwSt_default_Satz == "Y" && $newvalue->MwSt_Satz != "") {
            $newdefault_satz = $newvalue->MwSt_Satz;
        }
    }
    // Wenn es geloeschte MwSt-Saetze gibt, alle betroffenen Kategorien/Unterkategorien/Artikel mit dem Standard-MwSt-Satz updaten
    $update_flag = false; // Dieses Flag wird true, wenn ein Update von geloeschten MwSt-Saetzen vorgenommen werden musste
    if (count($todo_array) > 0) {
        foreach ($todo_array as $alt_mwst) {
            updatewithmwst($alt_mwst, $newdefault_satz); //Update vornehmen
            $update_flag = true;
        }
    }
    if(setmwstsettings($array_of_mwstsettings) || $Nr_setting == false) {
        // Wenn man nicht mehr MwSt-pflichtig ist, andere Meldung anzeigen
        if ($MwStNummer == "" || $MwStNummer == "0") {
?>
                <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
                  <tr>
                    <td>
                        <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
                        <h4>Das Speichern aller MwSt-Einstellungen war erfolgreich!</h4>
                        Es wurde festgestellt dass der Shop nicht mehr MwSt-pflichtig ist (MwSt-Nummer = 0 oder leer, MwSt-Management wird nicht mehr angezeigt).
                        Sie k&ouml;nnen das MwSt-Management wieder einschalten, wenn sie den Shop in den <a href='./SHOP_SETTINGS.php'>Allgemeinen Shopeinstellungen</a> als MwSt-pflichtig angeben.<br><br>
                        <small>(Zur&uuml;ck zum Hauptmen&uuml;)</small><br>
                        <a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'alt='Weiter'></a>
                    </td>
                  </tr>
                </table>
              </BODY>
            </HTML>
<?php
            exit; //Programmabbruch da Shop nicht MwSt-pflichtig ist!
        }
?>
        <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
                    <h4>Das Speichern aller MwSt-Einstellungen war erfolgreich!<br>
                    <?php if ($achtung) {echo "<br><i>ACHTUNG: Sie haben keinen MwSt default Satz gew&auml;hlt, es wurde der erste MwSt-Satz als default markiert!</i><br>";}?>
                    <?php if ($update_flag) {echo "<br><i>ACHTUNG: Die MwSt-S&auml;tze aller Haupt-/Unterkategorien und ihren Artikeln mit<br>einem gel&ouml;schten MwSt-Satz wurden mit dem Standard-MwSt-Satz von $newdefault_satz% upgedated!</i><br>";}?>
                    </h4>Sie sollten die ver&auml;nderten MwSt-S&auml;tze jetzt den Kategorien und deren Artikel zuweisen.<br><br>
                    <a href='./Shop_Einstellungen_Menu_MwSt.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'alt='Weiter'></a>
                </td>
            </tr>
        </table>
      </BODY>
    </HTML>
<?php
    }// End if
    else {
?>      <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <h1>SHOP ADMINISTRATION</h1>
                    <h3>Es trat ein Fehler beim Speichern der MwSt-Einstellungen auf!</h3>
                    <?php if ($Nr_setting == false) {echo "Die MwSt-Nummer konnte nicht gespeichert werden!<br><br>";} ?>
                    <a href='./SHOP_MWST.php' title='Abbrechen'>
                        <img src='../Buttons/bt_abbrechen_admin.gif' border='0' align='absmiddle' alt='Abbruch wegen Fehler'></a>
                </td>
            </tr>
        </table>
      </BODY>
    </HTML>
<?php
    }// End else
  } // End of if darstellen == 10

  // ---------------------------------------------------------------------------------
  // darstellen = 11
  // Hier werden MwSt-Saetze Kategorien zugeordnet. Auf diese Weise koennen neue MwSt-
  // Saetze schnell adaptiert werden. Die Artikel-MwSt-Saetze werden dabei ueberschrieben!
  // darstellen = 11 enthaelt die Eingabemaske fuer diese Operation, darstellen = 12 macht den Rest
  elseif ($darstellen == 11){

    // JavaScript, damit die Unterkategorien einen neuen Wert zugewiesen bekommen, wenn in deren
    // Hauptkategorie der Mehrwertsteuersatz geaendert wird
    echo "<script language=\"JavaScript\">\n";
    echo "    function chopt(index,from,to){\n";
    echo "        for (i=from; i<=to;i++){\n";
    echo "            document.Zuordnung.elements[i].selectedIndex=index;\n";
    echo "        } // end of for\n";
    echo "    } // end of function\n";
    echo "</script>\n";

    // Alle MwSt-Settings auslesen und in Array abfuellen
    $array_of_mwstsettings = getmwstsettings();

    // Formular mit Dropdownmenu zur MwSt-Satz auswahl anzeigen
    echo "<P><H1>SHOP ADMINISTRATION</H1></P>";
    echo "<B>Mehrwertsteuers&auml;tze zuordnen</B><br>";
    echo "<br>Hier k&ouml;nnen die <a href=\"$PHP_SELF\">definierten MwSt-S&auml;tze</a> entweder einzelnen Haupt-/Unterkategorien<br>";
    echo "oder gleich allen Artikeln im Shop zugewiesen werden.<br><br>";
    echo "Einen MwSt-Satz ausw&auml;hlen und diesen danach Kategorien / Unterkategorien zuordnen:<br><nobr>\n";
    echo "<i>Achtung:</i> Alle Artikel &uuml;bernehmen den definierten MwSt-Satz<br><br>\n";

    echo "<form action=\"$PHP_SELF\" method=\"post\" title=\"Kategorien zuordnen\" name=\"Zuordnung\">\n";

    // Kategorien- und Unterkategorienliste ausgeklappt ausgeben
    $myKategorien = array();
    $myKategorien = getallKategorien();
    echo "<table border=\"0\">\n";
    // totale Anzahl Kategorien (und Unterkategorien) ermitteln..
    $katcounter = 0;
    // $value ist ein Kategorie-Objekt, welches im Array Unterkategorien seine Unterkategorien mitfuehrt (siehe auch getallKategorien())
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
        echo "<nobr><input type=\"image\" src=\"../Buttons/bt_weiter_admin.gif\" border=\"0\" alt=\"weiter\">\n";
        echo "<a href='./Shop_Einstellungen_Menu_MwSt.php' title='Abbrechen'>\n";
        echo "<img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='Abbrechen'></a>\n</nobr>\n";
    }
    // alle Kategorien-ID's, wo sich der Artikel drin befindet holen
    $kat_id_array = array();
    if (!empty($Artikel_ID)){
        $kat_id_array = getKategorieID_eines_Artikels($Artikel_ID);
    }

    // Ausgabe der Tabelle (Kategorien-/Unterkategoriennamen und dazu passende Select-Boxen)
    $counter = 0; // Zaehler
    $pos_count = 0; // Positionszaehler fuer die Select-Elemente im Formular
    foreach($myKategorien as $keyname => $value){
        $zeigUnterkategorien = false;
        if ($value->kategorienanzahl() > 0){
            $zeigUnterkategorien = true;
        }
        echo "<tr><td colspan=2><b>-&nbsp;".$value->Name."</b></td>";
        echo "<td>";
        // Alle gespeicherten MwSt-Saetze (ausser der fuer Porto und Verpackung) zur Auswahl auflisten

        // JavaScript-Funktionalität aktivieren, falls die Kategorie Unterkategorien hat
        if ($zeigUnterkategorien){
            echo "<select name=\"Kategorie_IDarray[".$value->Kategorie_ID."]\" size=\"1\" onChange=\"chopt(this.selectedIndex,".($pos_count+1).",".($pos_count+$value->kategorienanzahl()).")\">\n";
        } // end of if
        else{
            echo "<select name=\"Kategorie_IDarray[".$value->Kategorie_ID."]\" size=\"1\">\n";
        } // end of else

        foreach ($array_of_mwstsettings as $wert) {
            if ($wert->Beschreibung != "Porto und Verpackung") {
                echo "<option value=\"".$wert->MwSt_Satz."\"";
                if ($value->MwSt_default_Satz == $wert->MwSt_Satz) {
                    echo " selected ";
                }
                echo ">\n";
                echo $wert->MwSt_Satz."% (".$wert->Beschreibung.")</option>\n";
            }
        }// end of foreach
        echo "<option value=\"0\"";
        if ($value->MwSt_default_Satz == "0") {
            echo " selected ";
        }
        echo ">\n";
        echo "0% (MwSt-frei)</option>\n";
        echo "</select>\n";
        echo "&nbsp;</td>\n";
        echo "<td>&nbsp;</td>\n";
        echo "</tr>\n";
        $pos_count++;
        // Falls vorhanden, auch die Unterkategorien anzeigen
        if ($zeigUnterkategorien){
            $myUnterkategorien = array();
            $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
            for($i=0;$i < $value->kategorienanzahl();$i++){
                echo "<tr><td>&nbsp;&nbsp;</td><td>-&nbsp;".$myUnterkategorien[$i]->Name."</td>";
                echo "<td>";
                // Alle gespeicherten MwSt-Saetze (ausser der fuer Porto und Verpackung) zur Auswahl auflisten
                echo "<select name=\"Kategorie_IDarray[".$myUnterkategorien[$i]->Kategorie_ID."]\" size=\"1\">\n";
                foreach ($array_of_mwstsettings as $wert2) {
                    if ($wert2->Beschreibung != "Porto und Verpackung") {
                        echo "<option value=\"".$wert2->MwSt_Satz."\"";
                        if ($myUnterkategorien[$i]->MwSt_default_Satz == $wert2->MwSt_Satz) {
                            echo " selected ";
                        }
                        echo ">\n";
                        echo $wert2->MwSt_Satz."% (".$wert2->Beschreibung.")</option>\n";
                    }
                }// end of foreach
                echo "<option value=\"0\"";  // Auswahloption 0% MwSt-frei anzeigen
                if ($myUnterkategorien[$i]->MwSt_default_Satz == "0") {
                    echo " selected ";
                }
                echo ">\n";
                echo "0% (MwSt-frei)</option>\n";
                echo "</select>\n";
                echo "&nbsp;</td>\n";
                echo "<td>&nbsp;</td>\n";
                echo "</tr>\n";
                $pos_count++;
           }// End for
        }//End if zeigUnterkategorien
        $counter = $counter+1;
    }// End foreach myKategorien
    echo "</table>\n<br>\n";

    // weiter und abbrechen Button ausgeben
    echo "<nobr><input type=\"image\" src=\"../Buttons/bt_weiter_admin.gif\" border=\"0\" alt=\"weiter\">";
    echo "<input type='hidden' name='darstellen' value='12'>\n";
    echo "<a href='./Shop_Einstellungen_Menu_MwSt.php' title='Abbrechen'>\n";
    echo "<img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='Abbrechen'></a>\n</nobr>\n";
    echo "</form>\n";
    echo "</body>\n</html>\n";// HTML-Datei schliessen

  } // End of if darstellen == 11

  // ---------------------------------------------------------------------------------
  // darstellen = 12
  // Hier werden MwSt-Saetze Kategorien zugeordnet. Auf diese Weise koennen neue MwSt-
  // Saetze schnell adaptiert werden. Die Artikel-MwSt-Saetze werden dabei ueberschrieben!
  // darstellen = 12 uebernimmt die Eingaben der Maske und fuehrt die Updates aus
  elseif ($darstellen == 12){
    // Darstellungsheader ausgeben
    echo "<P><H1>SHOP ADMINISTRATION</H1></P>\n";
    // Test ob ueberhaupt Kategorien angewaehlt wurden, sonst zuruecksenden
    if (count($Kategorie_IDarray) == 0) {
        echo "<B>Mehrwertsteuerss&auml;tze zuordnen</B><br><br>\n";
        echo "<font color=\"#ff0000\">Achtung!</font> Es wurden keine Kategorien &uuml;bertragen! Es muss mindestens eine Kategorie ausgew&auml;hlt sein!<br>\n";
        echo "<img src='../Buttons/bt_zurueck_admin.gif' border='0' alt='Zurueck'></a>\n";//MwSt_Satz Variable reseten
        echo "</body>\n</html>\n";
        exit; // Programmabbruch, da keine Kategorie(n) ausgewaehlt wurden
    }
    // Im Array $Kategorie_IDarray sind die Kategorie_IDs der ausgewaehlten Haupt und Unterkategorien gespeichert -> Anpassen der MwSt-Saetze:
    $changedkatarray = setKatmwst($Kategorie_IDarray, $HTTP_POST_VARS["MwSt_Satz"]);

    // Wenn mehr als 20 Kategorien abgeaendert wurden, vor der langen anzeige zusaetzlich noch ein Buttonset einblenden
    if (count($changedkatarray) > 20) {
        echo "<br><br>\n<a href='./Shop_Einstellungen_Menu_1.php' title='Weiter'>\n";
        echo "<img src='../Buttons/bt_weiter_admin.gif' border='0' alt='Zurueck zum Hauptmenu'></a><br><br>\n";
    }
    echo "Folgende (Unter)Kategorien wurden angepasst:<br>\n<blockquote>\n";
    foreach ($changedkatarray as $value) {
        echo $value."<br>\n";
    }
    echo "</blockquote>\n<br><br>\n<a href='./Shop_Einstellungen_Menu_MwSt.php' title='Weiter'>\n";
    echo "<img src='../Buttons/bt_weiter_admin.gif' border='0' alt='Zurueck zum Hauptmenu'></a>\n";

  } // End of if darstellen == 12


  // ---------------------------------------------------------------------------------
  // Dieser Codeteil wird ausgefuehrt, wenn $darstellen nicht = 10/11/12 ist.
  // Hier wird entschieden, welches darstellen == ? man verwenden soll (else)
  else {

      // Auslesen der MwSt-Einstellungen und abspeichern in ein Array aus MwSt-Objekten.
      // Objektdefinition siehe ../mwst_def.php
      $array_of_mwstsettings = getmwstsettings();
?>
  <script language="JavaScript">
  <!--
  function chkFormular() {
<?php
      // Dynamisch fuer jedes Beschreibungseingabefeld eine JavaScript Ueberpruefung generieren
      // Damit das ganze funktioniert, muss der richtige Porto und Verpackungseintrag am SCHLUSS des Arrays liegen! (hoechste Pos.Nr.)
      for($i = 0; $i < $anzahl_mwstsettings; $i++) {
          if ($array_of_mwstsettings[$i]->Beschreibung != "Porto und Verpackung") {
              echo "  if (document.Formular.Beschreibung$i.value == \"Porto und Verpackung\") {\n";
              echo '    alert("Bitte eine andere Beschreibung wählen ´Porto und Verpackung´ ist ein reservierter Begriff.");'."\n";
              echo "    document.Formular.Beschreibung$i.focus();\n";
              echo "    return false;\n";
              echo "  }\n";
              echo "  nummerisch = 0;\n";
              echo "  for(i=0;i<document.Formular.MwSt_Satz$i.value.length;++i) {\n";
              echo "    if(document.Formular.MwSt_Satz$i.value.charAt(i) >= \"0\" && document.Formular.MwSt_Satz$i.value.charAt(i) <= \"9\" || (document.Formular.MwSt_Satz$i.value.charAt(i) == \".\")) {\n";
              echo "      nummerisch = 1;\n";
              echo "    }\n";
              echo "    if(nummerisch == 0) {\n";
              echo "      alert(\"MwSt-Satz enthält ungültige Zeichen!\");\n";
              echo "      document.Formular.MwSt_Satz$i.focus();\n";
              echo "      return false;\n";
              echo "    }\n";
              echo "  }\n";
          }// End if
      }//End for
      echo "  if (";
      for($i = 0; $i < ($anzahl_mwstsettings-1); $i++) {
          if ($array_of_mwstsettings[$i]->Beschreibung != "Porto und Verpackung") {
              echo "(document.Formular.MwSt_Satz$i.value == \"\") && ";
          }
      }
      if ($array_of_mwstsettings[($anzahl_mwstsettings-1)]->Beschreibung != "Porto und Verpackung") {
          echo "document.Formular.MwSt_Satz".($anzahl_mwstsettings-1).".value == \"\"";
      }
      echo ") {\n";
      echo '    alert("Minimum EIN Eintrag muss gemacht werden");'."\n";
      echo "    document.Formular.Beschreibung$i.focus();\n";
      echo "    return false;\n";
      echo "  }\n";
?>
  }
  //-->
  </script>

        <form action='./SHOP_MWST.php' method="post" name="Formular" title="MwSt-Saetze definieren und Kategorien zuordnen" onSubmit="return chkFormular()">
        <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <h1>SHOP ADMINISTRATION</h1>
                    <h3>Mehrwertsteuer Einstellungen
<?php
      if (getmwstnr() == "0") {
          echo "<small>  (<font color=\"#ff0000\">ACHTUNG!</font> Der Shop ist als <i>NICHT</i> MwSt-pflichtig konfiguriert!)</small>";
      }
?>
                    </h3>
                </td>
                <td>
                    &nbsp;
                </td>
<?php           echo "<td align = 'left'>\n";
                    echo "[<B>".getShopname()."</B>";
                    $mwstnummer = getmwstnr(); //MwSt Nummer aus DB auslesen (0 = Nicht MwSt-pflichtig)
                    if ($mwstnummer != "0") {echo " MwSt-Nummer=$mwstnummer"; $MwStpflichtig ="Y";} else {$MwStpflichtig ="N";}
                    echo "]\n";
                echo "</td>\n";
          echo "</tr>\n";
        echo "</table>\n";
        echo "<div class=\"content\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MwSt-pflichtig: <input type=\"checkbox\" checked value=\"$MwStpflichtig\" name=\"MwStpflichtig\">\n";
        echo "&nbsp;&nbsp;&nbsp;MwSt Nummer: <input type=\"text\" name=\"MwStNummer\" size=\"24\" value=\"$mwstnummer\"><br><br></div>";
?>
        <table border='0' cellpadding='3' cellspacing='0'>
           <tr>
               <td width="20">
                 &nbsp;
               </td>
               <td>
                 <center>MwSt-default</center>
               </td>
               <td width="10">
                 &nbsp;
               </td>
               <td>
                 <center>MwSt-Satz</center>
               </td>
               <td width="10">
                 &nbsp;
               </td>
               <td>
                 <center>Beschreibung (max. 255 Zeichen)</center>
               </td>
           </tr>
<?php           // Folgende foreach-Schleife fuellt das Formular aus (Tabelle)
                for($i = 0; $i < $anzahl_mwstsettings; $i++) {
                    if ($array_of_mwstsettings[$i]->Beschreibung != "Porto und Verpackung") {
                        echo "<tr>\n<td width=\"20\">\n&nbsp;</td>\n<td>\n";
                        echo "<center>";
                        echo "<input type=\"radio\" value=\"MwSt_default_Satz$i\" name=\"MwSt_default_Satz\"";
                        // Wenn das Tabellenattribut MwSt_default_Satz = Y ist, so ist dies der Standard-MwSt-Satz -> via HTML vorwaehlen -> checked
                        if ($array_of_mwstsettings[$i]->MwSt_default_Satz == "Y") {
                            echo " checked";
                        }
                        echo ">";
                        echo "</center>\n";
                        echo "</td>\n<td width=\"10\">\n&nbsp;</td>\n<td>\n";
                        echo "  <input type=\"text\" value=\"".$array_of_mwstsettings[$i]->MwSt_Satz."\" name=\"MwSt_Satz$i\" size=\"6\">%\n";
                        echo "  <input type=\"hidden\" value=\"".$array_of_mwstsettings[$i]->Mehrwertsteuer_ID."\" name=\"Mehrwertsteuer_ID$i\">\n";
                        echo "  <input type=\"hidden\" value=\"".($i+1)."\" name=\"Positions_Nr$i\">\n";
                        echo "  <input type=\"hidden\" value=\"".$array_of_mwstsettings[$i]->Preise_inkl_MwSt."\" name=\"Preise_inkl_MwSt$i\">\n";
                        echo "</td>\n<td width=\"10\">\n&nbsp;</td>\n<td>\n";
                        echo "<center>";
                        echo "  <input type=\"text\" value=\"".$array_of_mwstsettings[$i]->Beschreibung."\" name=\"Beschreibung$i\" size=\"30\">\n";
                        echo "</center>";
                        echo "</td>\n<td>\n";
                        echo "</tr>\n";
                    }// End if
                    else {
                        // Da wir die Angaben zu Porto und Verpackung erst am Ende der Tabelle ausdrucken, merken wir uns welche ID das Porto hat
                        $Porto_ID = $i;
                    }
                }// End for
                echo "<tr>\n<td colspan=\"6\">\n&nbsp;</td>\n</tr>\n";
                // Porto und Verpackung Ausgabe:
                echo "<tr>\n<td colspan=\"5\">\n";
                echo $array_of_mwstsettings[$Porto_ID]->Beschreibung.":\n";
                echo "  <input type=\"hidden\" value=\"".$array_of_mwstsettings[$Porto_ID]->Beschreibung."\" name=\"Beschreibung$Porto_ID\" size=\"30\">\n";
                echo "  <input type=\"hidden\" value=\"".$array_of_mwstsettings[$Porto_ID]->Preise_inkl_MwSt."\" name=\"Preise_inkl_MwSt$Porto_ID\" size=\"30\">\n";
                echo "  <input type=\"hidden\" value=\"".$array_of_mwstsettings[$Porto_ID]->Mehrwertsteuer_ID."\" name=\"Mehrwertsteuer_ID$Porto_ID\">\n";
                echo "</td>\n<td>\n";
                echo "<select name=\"MwSt_Satz$Porto_ID\" size=\"1\">\n";
                // Alle gespeicherten MwSt-Saetze zur Auswahl auflisten
                echo "<option value=\"-1\"";
                if ($array_of_mwstsettings[$Porto_ID]->MwSt_Satz == -1) { echo " selected "; }
                echo ">anteilsm&auml;ssig</option>\n";
                echo "<option value=\"-2\"";
                if ($array_of_mwstsettings[$Porto_ID]->MwSt_Satz == -2) { echo " selected "; }
                echo ">Mwst-Satz mit gr&ouml;sstem Anteil</option>\n";
                echo "<option value=\"0\"";
                if ($array_of_mwstsettings[$Porto_ID]->MwSt_Satz == 0) { echo " selected "; }
                echo ">gar nicht (MwSt-frei)</option>\n";
                foreach ($array_of_mwstsettings as $value) {
                    if ($value->Beschreibung != "Porto und Verpackung") {
                        echo "<option value=\"".$value->MwSt_Satz."\"";
                        if ($array_of_mwstsettings[$Porto_ID]->MwSt_Satz == $value->MwSt_Satz) { echo " selected "; }
                        echo ">";
                        echo $value->MwSt_Satz."% (".$value->Beschreibung.")</option>\n";
                    }
                }// end of foreach
                echo "</select>\n";
                echo "  <input type=\"hidden\" value=\"".$array_of_mwstsettings[$Porto_ID]->Mehrwertsteuer_ID."\" name=\"Mehrwertsteuer_ID$Porto_ID\">\n";
                echo "  <input type=\"hidden\" value=\"".($Porto_ID+1)."\" name=\"Positions_Nr$Porto_ID\">\n";
                echo "</td>\n";
                echo "</tr>\n";
                echo "<tr>\n<td colspan=\"6\">\n<BR>\n";
                echo "Angegebene Artikelpreise sind ";
                if ($array_of_mwstsettings[0]->Preise_inkl_MwSt == "Y") {
                    echo "<i>inkl.</i>";
                }
                else {
                    echo "<i>exkl.</i>";
                }
                echo " MwSt.<BR><BR>\n</td>\n</tr>\n";
?>
        </table>
        <table border="0">
            <tr>
              <td valign=middle>
                <INPUT type='hidden' name='darstellen' value='10'>
                <input type=image src="../Buttons/bt_speichern_admin.gif" border="0">
                <a href='./Shop_Einstellungen_Menu_MwSt.php' title='Abbrechen'>
                  <img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='Abbrechen'></a>
                <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_MwSt')">
                  <img src='../Buttons/bt_hilfe_admin.gif' border='0' alt='Hilfe'></a>
              </td>
            </tr>
        </table>
        </form>
<?php
  } // end of (if darstellen =) else

echo "    </BODY>";
echo "</HTML>";
// End of file ----------------------------------------------------------
?>
