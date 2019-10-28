<?php
  // Filename: SHOP_VERSANDKOSTEN.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Funktionen um die Versandkosten des Shops zu veraendern
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_VERSANDKOSTEN.php,v 1.38 2003/05/26 10:05:26 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_VERSANDKOSTEN = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
  if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}
  if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // HTML-Kopf, der bei jedem Aufruf des Files ausgegeben wird
?>
<HTML>
    <HEAD>
        <TITLE>Versandkostenmanagement</TITLE>
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
  // darstellen = 10
  // hier werden die Versandkostensettings gespeichert (Funktion setversandkostensettings)
  // und es wird eine entsprechende Ausgabe erfolgen
  if ($darstellen == 10){
    // Die Anzahl der Versandkostenintervalle wurde vom Formular her uebergeben (Variable $anzahl_Versandkostenintervalle)
    // Test ob diese Anzahl > 0 und < als 999 ist: (Wenn ausserhalb des erlaubten Bereichs, auf 5 zurueck-stellen)
    $intervall_flag = false; // Dieses Flag wird true, wenn eine Zwangs-Korrektur durchgefuehrt werden musste (fuer Meldung)
    if ($neue_anzahl_Versandkostenintervalle < 1) {
        $neue_anzahl_Versandkostenintervalle = 5;
        $intervall_flag = true;
    }
    else if ($neue_anzahl_Versandkostenintervalle > 999) {
        $neue_anzahl_Versandkostenintervalle = 5;
        $intervall_flag = true;
    }

    // Zuerst muss noch der Array mit den Versandkostenpreisen zusammengestellt werden
    // Die einzelnen Felder wurden vom Formular her auf diese Weise uebermittelt:
    $Versandkostenpreise = array();
    $meinVersandkostenpreis = new Versandkostenpreis;
    // Counter initialisieren
    $counter = 1;
    // Wenn es ein Intervall gibt, welches keine aktivierte Zahlungsmethode enthaelt, wird dieses Flag true.
    $Warnungflag = false;
    $Fehlerhafte_Intervalle = "";
    // Zugriffsvariablen fuer das erste Intervall vorbereiten
    $newVon = "Von".($counter+1);
    if ($$newVon == "") {unset($$newVon);}
    $Vonname = "Von$counter";
    if ($$Vonname == "") {unset($$Vonname);}
    $Bisname = "Bis$counter";
    if ($$Bisname == "") {unset($$Bisname);}
    $Betragname = "Betrag$counter";
    if ($$Betragname == "") {unset($$Betragname);}
    $Vorauskassenname = "Vorauskasse$counter";
    if ($$Vorauskassenname == "") {unset($$Vorauskassenname);}
    $Rechnungsname = "Rechnung$counter";
    if ($$Rechnungsname == "") {unset($$Rechnungsname);}
    $Nachnamename = "Nachname$counter";
    if ($$Nachnamename == "") {unset($$Nachnamename);}
    $Lastschriftname = "Lastschrift$counter";
    if ($$Lastschriftname == "") {unset($$Lastschriftname);}
    $Kreditkartenname = "Kreditkarte$counter";
    if ($$Kreditkartenname == "") {unset($$Kreditkartenname);}
    $billBOXname = "billBOX$counter";
    if ($$billBOXname == "") {unset($$billBOXname);}
    $Treuhandzahlungname = "Treuhandzahlung$counter";
    if ($$Postcardname == "") {unset($$Postcardname);}
    $Postcardname = "Postcard$counter";
    if ($$Treuhandzahlungname == "") {unset($$Treuhandzahlungname);}
    $Von_Bis_IDname = "Von_Bis_ID$counter";
    if ($$Von_Bis_IDname == "") {unset($$Von_Bis_IDname);}
    // Test: Wenn jemand das erste Intervall (Ab-Betrag) eingibt / eroeffnet, aber vergisst
    // einen Betrag zu setzen, so wird der Betrag automatisch auf 0.00 gesetzt, wenn
    // eine der verfuegbaren Zahlungsarten in diesem Intervall als aktiv gesetzt ist:
    if (isset($$Vonname) && !(isset($$Betragname))) {
        if ($Vorauskassenname == "on" || $Rechnungsname == "on" || $Nachnamenname == "on" || $Kreditkartenname == "on" || $billBOXname == "on" || $Treuhandzahlungname == "on" || $Lastschriftname == "on" || $Postcardname == "on") {
            $$Betragname = 0.00;
        }
    }
    while (isset($$Betragname)) {
        //Schreiben der Versandkosten ins Versandkostenpreis-Objekt
        $meinVersandkostenpreis->Von = $$Vonname;
        if (isset($$newVon)) {
            $$Bisname = ($$newVon-1);
        }
        else {
            $$Bisname = 9999999999;
        }
        $meinVersandkostenpreis->Bis = $$Bisname;
        $meinVersandkostenpreis->Betrag = $$Betragname;
        if ($$Vorauskassenname == "on"){
            $meinVersandkostenpreis->Vorauskasse = 'Y';
        }
        elseif($$Vorauskassenname == "") {
            $meinVersandkostenpreis->Vorauskasse = 'N';
        }
        if ($$Rechnungsname == "on"){
            $meinVersandkostenpreis->Rechnung = 'Y';
        }
        elseif($$Rechnungsname == "") {
            $meinVersandkostenpreis->Rechnung = 'N';
        }
        if ($$Nachnamename == "on"){
            $meinVersandkostenpreis->Nachname = 'Y';
        }
        elseif($$Nachnamename == "") {
            $meinVersandkostenpreis->Nachname = 'N';
        }
        if ($$Lastschriftname == "on"){
            $meinVersandkostenpreis->Lastschrift = 'Y';
        }
        elseif($$Lastschriftname == "") {
            $meinVersandkostenpreis->Lastschrift = 'N';
        }
        if ($$Kreditkartenname == "on"){
            $meinVersandkostenpreis->Kreditkarte = 'Y';
        }
        elseif($$Kreditkartenname == "") {
            $meinVersandkostenpreis->Kreditkarte = 'N';
        }
        if ($$billBOXname == "on"){
            $meinVersandkostenpreis->billBOX = 'Y';
        }
        elseif($$billBOXname == "") {
            $meinVersandkostenpreis->billBOX = 'N';
        }
        if ($$Treuhandzahlungname == "on"){
            $meinVersandkostenpreis->Treuhandzahlung = 'Y';
        }
        elseif($$Treuhandzahlungname == "") {
            $meinVersandkostenpreis->Treuhandzahlung = 'N';
        }
        if ($$Postcardname == "on"){
            $meinVersandkostenpreis->Postcard = 'Y';
        }
        elseif($$Postcardname == "") {
            $meinVersandkostenpreis->Postcard = 'N';
        }
        $meinVersandkostenpreis->Von_Bis_ID = $$Von_Bis_IDname;
        // Schreiben des Versandkostenpreis-Objekts in einen Array
        $Versandkostenpreise[] = $meinVersandkostenpreis;
        $counter++;
        // Zugriffsvariablen fuer das naechste Intervall vorbereiten
        $newVon = "Von".($counter+1);
        if ($$newVon == "") {unset($$newVon);}
        $Vonname = "Von$counter";
        if ($$Vonname == "") {unset($$Vonname);}
        $Bisname = "Bis$counter";
        if ($$Bisname == "") {unset($$Bisname);}
        $Betragname = "Betrag$counter";
        if ($$Betragname == "") {unset($$Betragname);}
        $Vorauskassenname = "Vorauskasse$counter";
        if ($$Vorauskassenname == "") {unset($$Vorauskassenname);}
        $Rechnungsname = "Rechnung$counter";
        if ($$Rechnungsname == "") {unset($$Rechnungsname);}
        $Nachnamename = "Nachname$counter";
        if ($$Nachnamename == "") {unset($$Nachnamename);}
        $Lastschriftname = "Lastschrift$counter";
        if ($$Lastschriftname == "") {unset($$Lastschriftname);}
        $Kreditkartenname = "Kreditkarte$counter";
        if ($$Kreditkartenname == "") {unset($$Kreditkartenname);}
        $billBOXname = "billBOX$counter";
        if ($$billBOXname == "") {unset($$billBOXname);}
        $Treuhandzahlungname = "Treuhandzahlung$counter";
        if ($$Treuhandzahlungname == "") {unset($$Treuhandzahlungname);}
        $Postcardname = "Postcard$counter";
        if ($$Postcardname == "") {unset($$Postcardname);}
        $Von_Bis_IDname = "Von_Bis_ID$counter";
        if ($$Von_Bis_IDname == "") {unset($$Von_Bis_IDname);}
        // Wenn jemand ein Intervall (Ab-Betrag) eingibt / eroeffnet, aber vergisst
        // einen Betrag zu setzen, so wird der Betrag automatisch auf 0.00 gesetzt, wenn
        // eine der verfuegbaren Zahlungsarten in diesem Intervall als aktiv gesetzt ist:
        if (isset($$Vonname) && !(isset($$Betragname))) {
            if ($$Vorauskassenname == "on" || $$Rechnungsname == "on" || $$Nachnamenname == "on" || $$Kreditkartenname == "on" || $$billBOXname == "on" || $$Treuhandzahlungname == "on" || $$Lastschriftname == "on" || $$Postcardname == "on") {
                 $$Betragname = 0.00;
            }
        }// End if
        // Setzen des Warnung-Flags, wenn es sich um ein Intervall handelt, welches keine aktivierte Zahlungsmethode beinhaltet:
        if ($$Vorauskassenname == "" && $$Rechnungsname == "" && $$Nachnamenname == "" && $$Kreditkartenname == "" && $$billBOXname == "" && $$Treuhandzahlungname == "" && $$Lastschriftname == "" && $$Postcardname == "" && !($$Vonname == "")) {
             $Warnungflag = true;
             $Fehlerhafte_Intervalle .= " [".$$Vonname."]";
        }
    }// End while
    // Aufbereitung der Checkboxen Felder:
    if ($keineVersandkostenmehr == "on") {
        $keineVersandkostenmehr = "Y";
    }
    elseif ($keineVersandkostenmehr == "") {
        $keineVersandkostenmehr = "N";
    }
    if ($Mindermengenzuschlag == "on") {
        $Mindermengenzuschlag = "Y";
    }
    elseif ($Mindermengenzuschlag == "") {
        $Mindermengenzuschlag = "N";
    }

    // Falls die Zahlungsmethode Nachnahme deaktiviert wurde, kommt hier ein leerer
    // Betrag an, wir setzen ihn auf 0 (Null)
    if ($Nachnamebetrag == "") {
        $Nachnamebetrag = 0;
    }

    // Bevor wir nun die Schreiben-Funktion starten koennen, muessen wir noch drei
    // weitere Variablen aufbereiten:
    if (trim($Abrechnung_nach) == "Abrechnung_nach_Pauschale") {
        $Abrechnung_nach_Preis = "N";
        $Abrechnung_nach_Gewicht = "N";
        $Abrechnung_nach_Pauschale = "Y";
    }
    elseif (trim($Abrechnung_nach) == "Abrechnung_nach_Preis") {
        $Abrechnung_nach_Preis = "Y";
        $Abrechnung_nach_Gewicht = "N";
        $Abrechnung_nach_Pauschale = "N";
    }
    else {                           //Abrechnung_nach_Gewicht
        $Abrechnung_nach_Preis = "N";
        $Abrechnung_nach_Gewicht = "Y";
        $Abrechnung_nach_Pauschale = "N";
    }
    if ((count($Versandkostenpreise) == 0) || ($Versandkostenpreise[0]->Von == '')) {
?>      <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <h1>SHOP ADMINISTRATION</h1>
                    <h3>Sie m&uuml;ssen mindestens ein Intervall (Von-Bis-Betrag) angeben!<BR>
                        Ausserdem muss das erste Intervall benutzt werden</h3>
                    <a href='./SHOP_VERSANDKOSTEN.php' title='Abbrechen'>
                        <img src='../Buttons/bt_zurueck_admin.gif' border='0' align='absmiddle' alt='Abbruch wegen leeren Intervallen'></a>
                </td>
            </tr>
        </table>
      </BODY>
    </HTML>
<?php
    exit;// Abbruch, da keine Intervalle uebergeben wurden!
    }
    // Eigentlicher Funktionsaufruf zum Schreiben der Versandkosten Einstellungen in die Datenbank
    if(setversandkostensettings($Abrechnung_nach_Preis,$Abrechnung_nach_Gewicht,$Abrechnung_nach_Pauschale,$Pauschale_text,
           $keineVersandkostenmehr,$keineVersandkostenmehr_ab,$neue_anzahl_Versandkostenintervalle,$Mindermengenzuschlag,
           $Mindermengenzuschlag_bis_Preis,$Mindermengenzuschlag_Aufpreis,$Nachnamebetrag,$Setting_Nr,$Versandkostenpreise)) {
?>
        <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
                    <h4>Das Speichern aller Versandkosten Einstellungen war erfolgreich!<h4><br>
<?php
                    if ($intervall_flag == true) {
                        echo "<B><font color=\"#ff0000\">ACHTUNG</font>: Die von Ihnen angegebene Anzahl Intervalle war ausserhalb des erlaubten Bereichs [1..999] und wurde auf 5 korrigiert!</B>\n<BR><BR>\n";
                    }
                    if ($Warnungflag == true) {
                        echo "<B><font color=\"#ff0000\">ACHTUNG</font>: Folgende Betrags-Intervalle, besitzen keine aktivierten Zahlungsmethoden:$Fehlerhafte_Intervalle. Bitte korrigieren!</B>\n<BR><BR>\n";
                    }
?>
                    <a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'alt='weiter'></a>
                </td>
            </tr>
        </table>
      </BODY>
    </HTML>
<?php
    }// end if
    else {
?>      <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <h1>SHOP ADMINISTRATION</h1>
                    <h3>Es trat ein Fehler beim Speichern der Versandkosten Einstellungen auf!</h3>
                    <a href='./SHOP_VERSANDKOSTEN.php' title='Abbrechen'>
                        <img src='../Buttons/bt_abbrechen_admin.gif' border='0' align='absmiddle' alt='Abbruch wegen Fehler'></a>
                </td>
            </tr>
        </table>
      </BODY>
    </HTML>
<?php
    }
  } // end of if darstellen == 10

  // wird ausgefuehrt, wenn $darstellen nicht 10 ist
  // hier wird entschieden, welches darstellen == ? man verwenden soll
  else {
      $Setting_Nr = 1;
      $meineVersandkosten = getversandkostensettings($Setting_Nr);
      $anzahl_Versandkostenintervalle = $meineVersandkosten->anzahl_Versandkostenintervalle;
      $meinVersandkostenpreis = $meineVersandkosten->getallversandkostenpreise();
      $myAllezahlungen = getAllezahlungen(); //Auslesen der weiteren Zahlungsmethoden
      $weitereZahlungen = $myAllezahlungen->getallzahlungen();
      // Beim aendern des Abrechnungsmodus wird hier der richtige gewaelt (Anzeigen)
      if (stripslashes($Abrechnung) == "Pauschale") {
          $meineVersandkosten->Abrechnung_nach_Preis = "N";
          $meineVersandkosten->Abrechnung_nach_Gewicht = "N";
          $meineVersandkosten->Abrechnung_nach_Pauschale = "Y";
      }
      elseif (stripslashes($Abrechnung) == "Preis") {
          $meineVersandkosten->Abrechnung_nach_Preis = "Y";
          $meineVersandkosten->Abrechnung_nach_Gewicht = "N";
          $meineVersandkosten->Abrechnung_nach_Pauschale = "N";
      }
      elseif (stripslashes($Abrechnung) == "Gewicht") {
          $meineVersandkosten->Abrechnung_nach_Preis = "N";
          $meineVersandkosten->Abrechnung_nach_Gewicht = "Y";
          $meineVersandkosten->Abrechnung_nach_Pauschale = "N";
      }
  ?>

  <script language="JavaScript">
  <!--
    // Ueberpruefung der eingegebenen Daten des Versandkosteneingabeformulars:
    function chkFormular() {

      if(document.Formular.Pauschale_text.value == "") {
          alert("Bitte einen Definitionstext für die Versandkosten eingeben!");
          document.Formular.Pauschale_text.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.neue_anzahl_Versandkostenintervalle.value.length;++i) {
          if(document.Formular.neue_anzahl_Versandkostenintervalle.value.charAt(i) < "0" || document.Formular.neue_anzahl_Versandkostenintervalle.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Die Intervallangabe enthält ungültige Zeichen!");
          document.Formular.neue_anzahl_Versandkostenintervalle.focus();
          return false;
      }
      if(document.Formular.neue_anzahl_Versandkostenintervalle.value < "1" || document.Formular.neue_anzahl_Versandkostenintervalle.value > "999") {
          alert("Die Intervallangabe ist ausserhalb der erlaubten Grössen: 1-999!");
          document.Formular.neue_anzahl_Versandkostenintervalle.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.keineVersandkostenmehr_ab.value.length;++i) {
          if(document.Formular.keineVersandkostenmehr_ab.value.charAt(i) < "0" || document.Formular.keineVersandkostenmehr_ab.value.charAt(i) > "9") {
              if(document.Formular.keineVersandkostenmehr_ab.value.charAt(i) != ".") {
                  nummerisch = 0;
              }
          }
      }
      if(nummerisch == 0) {
          alert("Der Betrag 'Keine Versandkosten mehr ab ...' enthaelt ungueltige Zeichen. Erlaubt sind: Ziffern und ein Punkt als Dezimaltrennzeichen!");
          document.Formular.keineVersandkostenmehr_ab.focus();
          return false;
      }

      nummerisch = 1;
      for(i=0;i<document.Formular.Mindermengenzuschlag_bis_Preis.value.length;++i) {
          if(document.Formular.Mindermengenzuschlag_bis_Preis.value.charAt(i) < "0" || document.Formular.Mindermengenzuschlag_bis_Preis.value.charAt(i) > "9") {
              if(document.Formular.Mindermengenzuschlag_bis_Preis.value.charAt(i) != ".") {
                  nummerisch = 0;
              }
          }
      }
      if(nummerisch == 0) {
          alert("Der Betrag, welcher regelt, bis zu welchem Betrag Mindermengenzuschläge anfallen enthaelt ungueltige Zeichen. Erlaubt sind: Ziffern und ein Punkt als Dezimaltrennzeichen!");
          document.Formular.Mindermengenzuschlag_bis_Preis.focus();
          return false;
      }

      nummerisch = 1;
      for(i=0;i<document.Formular.Mindermengenzuschlag_Aufpreis.value.length;++i) {
          if(document.Formular.Mindermengenzuschlag_Aufpreis.value.charAt(i) < "0" || document.Formular.Mindermengenzuschlag_Aufpreis.value.charAt(i) > "9") {
              if(document.Formular.Mindermengenzuschlag_Aufpreis.value.charAt(i) != ".") {
                  nummerisch = 0;
              }
          }
      }
      if(nummerisch == 0) {
          alert("Der Mindermengenzuschlag enthaelt ungueltige Zeichen. Erlaubt sind: Ziffern und ein Punkt als Dezimaltrennzeichen!");
          document.Formular.Mindermengenzuschlag_Aufpreis.focus();
          return false;
      }

      nummerisch = 1;
      for(i=0;i<document.Formular.Nachnamebetrag.value.length;++i) {
          if(document.Formular.Nachnamebetrag.value.charAt(i) < "0" || document.Formular.Nachnamebetrag.value.charAt(i) > "9") {
              if(document.Formular.Nachnamebetrag.value.charAt(i) != ".") {
                  nummerisch = 0;
              }
          }
      }
      if(nummerisch == 0) {
          alert("Der Nachnahmebetrag enthaelt ungueltige Zeichen. Erlaubt sind: Ziffern und ein Punkt als Dezimaltrennzeichen!");
          document.Formular.Nachnamebetrag.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Betrag1.value.length;++i) {
          if(document.Formular.Betrag1.value.charAt(i) < "0" || document.Formular.Betrag1.value.charAt(i) > "9") {
              if(document.Formular.Betrag1.value.charAt(i) != ".") {
                  nummerisch = 0;
              }
          }
      }
      if(nummerisch == 0) {
          alert("Der Betrag enthaelt ungueltige Zeichen. Erlaubt sind: Ziffern und ein Punkt als Dezimaltrennzeichen!");
          document.Formular.Betrag1.focus();
          return false;
      }
    }// End function chkFormular
  //-->
  </script>

        <table border='0' cellpadding='0' cellspacing='0' width ='100%'>
            <tr>
                <td>
                    <h1>SHOP ADMINISTRATION</h1>
                    <h3>Versandkosten Einstellungen</h3>
        <form action='./SHOP_VERSANDKOSTEN.php' method="post" title="Versandkosten_Eingabe" name="Formular" onSubmit="return chkFormular()">
                    Angezeigter Rechnungsposten im Warenkorb:
                    <INPUT type='text' name='Pauschale_text' size='64' maxlength='127' value='<?php echo $meineVersandkosten->Pauschale_text;?>'>
                    <BR>
                    Anzahl Berechnungs-Intervalle:
                    <INPUT type='text' name='neue_anzahl_Versandkostenintervalle' size='3' maxlength='3' value='<?php echo $anzahl_Versandkostenintervalle; ?>'>
                    &nbsp;[1-999] &nbsp;&nbsp;&nbsp;&nbsp;Um die aktualisierte Anzahl sehen zu k&ouml;nnen, bitte zuerst speichern!
                    <BR>
                </td>
                <td>
                    &nbsp;
                </td>
<?php           echo "<td align = 'right'>";
                    echo "[<B>".$meineVersandkosten->Shopname."</B>";
                          if ($meineVersandkosten->MwStpflichtig == "Y") {echo " MwST-Nummer: <I>".$meineVersandkosten->MwStNummer."</I>";}
                    echo "]";
                echo "</td>";
?>          </tr>
        </table>
        <HR>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td valign=top>
                    <P><BR><BR>
<?php               // Folgende for-Schleife fuellt BRs ein damit der Text in der Tabellenmitte erscheint
                    if (($meineVersandkosten->Abrechnung_nach_Preis == "Y") || ($meineVersandkosten->Abrechnung_nach_Gewicht == "Y")) {
                        for($i=0;$i < ($anzahl_Versandkostenintervalle/2);$i++){
                            echo "<BR>\n";
                        }
                    }
                    if ($meineVersandkosten->Abrechnung_nach_Preis == "Y") {
                        echo "<tt style='font-family: Courier, Courier New, Monaco'><font size=\"4\" color=\"#ff2222\">->&nbsp;</font></tt><input type=hidden name=Abrechnung_nach value=Abrechnung_nach_Preis><BR><BR>";
                    }
                    else {
                        echo "&nbsp;<BR><BR>";
                    }
                    if ($meineVersandkosten->Abrechnung_nach_Gewicht == "Y") {
                        echo "<tt style='font-family: Courier, Courier New, Monaco'><font size=\"4\" color=\"#ff2222\">->&nbsp;</font></tt><input type=hidden name=Abrechnung_nach value=Abrechnung_nach_Gewicht><BR><BR>";
                    }
                    else {
                        echo "&nbsp;<BR><BR>";
                    }
                    if ($meineVersandkosten->Abrechnung_nach_Pauschale == "Y") {
                        echo "<tt style='font-family: Courier, Courier New, Monaco'><font size=\"4\" color=\"#ff2222\">->&nbsp;</font></tt><input type=hidden name=Abrechnung_nach value=Abrechnung_nach_Pauschale><BR><BR>";
                    }
                    else {
                        echo "&nbsp;<BR><BR>";
                    }
/*                    if (($meineVersandkosten->Abrechnung_nach_Preis == "Y") || ($meineVersandkosten->Abrechnung_nach_Gewicht == "Y")) {
                        for($i=0;$i < ($anzahl_Versandkostenintervalle/2);$i++){
                            echo "<BR>\n";
                        }
                    }*/
?>
                    <BR></P>
                </td>
                <td valign=top>
                    <B>Aktive Berechnung</B><P>
<?php               if (($meineVersandkosten->Abrechnung_nach_Preis == "Y") || ($meineVersandkosten->Abrechnung_nach_Gewicht == "Y")) {
                        for($i=0;$i < ($anzahl_Versandkostenintervalle/2);$i++){
                            echo "<BR>\n";
                        }
                    }
                    echo '<a href="'.$PHP_SELF.'?Abrechnung=Preis">Nach Preis</a><BR><BR>';
                    echo '<a href="'.$PHP_SELF.'?Abrechnung=Gewicht">Nach Gewicht</a><BR><BR>';
                    echo '<a href="'.$PHP_SELF.'?Abrechnung=Pauschale">Nach Pauschale</a><BR>';
/*                    if (($meineVersandkosten->Abrechnung_nach_Preis == "Y") || ($meineVersandkosten->Abrechnung_nach_Gewicht == "Y")) {
                        for($i=0;$i < ($anzahl_Versandkostenintervalle/2);$i++){
                            echo "<BR>\n";
                        }
                    }*/
?>                  </P>
                </td>
                <td>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                </td>
<?php
        if ($meineVersandkosten->Abrechnung_nach_Pauschale == "Y") {
?>
                <td valign="middle">
                    Versandkostenpauschale:
                    <INPUT type='text' name='Betrag1' size='5' maxlength='10' value='<?php echo $meinVersandkostenpreis[0]->Betrag; ?>'>
                    <?php echo $meineVersandkosten->Waehrung; ?>
<?php
                    echo "<INPUT type='hidden' name='Vorauskasse1' size='5' maxlength='10' value='on'>\n";
                    echo "<INPUT type='hidden' name='Rechnung1' size='5' maxlength='10' value='on'>\n";
                    echo "<INPUT type='hidden' name='Nachname1' size='5' maxlength='10' value='on'>\n";
                    echo "<INPUT type='hidden' name='Lastschrift1' size='5' maxlength='10' value='on'>\n";
                    echo "<INPUT type='hidden' name='Kreditkarte1' size='5' maxlength='10' value='on'>\n";
                    // Alle weiteren Zahlungsmethoden (z.B. billBOX, Treuhandzahlung, Saferpay / B+S Card Service, PostFinance yellwopay)
                    foreach($weitereZahlungen as $value) {
                        // Sonderbehandlung fuer PostFinance yellowpay (Debit Direct - Postcard)
                        if ($value->Bezeichnung == 'Postfinance') {
                            $Zahlungsmethode_name = 'Postcard1';
                        }
                        else {
                            $Zahlungsmethode_name = $value->Bezeichnung."1";
                        }
                        echo "<INPUT type='hidden' name='".$Zahlungsmethode_name."' size='5' maxlength='10' value='on'>\n";
                    }// End foreach
?>
                    <INPUT type='hidden' name='Von1' size='5' maxlength='10' value='0'>
                    <INPUT type='hidden' name='Bis1' size='5' maxlength='10' value='9999999999'>
                    <INPUT type='hidden' name='Von_Bis_ID1' size='5' maxlength='10' value='1'>
                </td>
                <td>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                </td>
<?php
        }
        else {
?>
                <td valign="top">
                    <B>&nbsp;&nbsp;Ab</B>
                    <img src="../Bilder/spacer.gif" width="1" height="35" align="top" alt="spacer">
                    <BR>
                    <tt style='font-family: Courier, Courier New, Monaco'>0.00&nbsp;</tt>&nbsp;

                    <INPUT type='hidden' name='Von1' size='5' maxlength='10' value='0'>
<?php                   if ($meineVersandkosten->Abrechnung_nach_Preis == "Y") {echo $meineVersandkosten->Waehrung."<BR>";}else {echo $meineVersandkosten->Gewichts_Masseinheit."<BR>";}?><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Darstellen der Spalte mit den ab-Preisen/Gewichtsangaben (Beim ersten Von (Von1) ist
                    //       der value immer = 0, deshalb beginnt hier der Zaehler mit 2 anstatt mit 1.
                    for($i = 2; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Von_name = "Von".$i;
                        echo "<INPUT type='text' name='".$Von_name."' size='5' maxlength='10' value='".$meinVersandkostenpreis[($i-1)]->Von."'>\n";
                        if ($meineVersandkosten->Abrechnung_nach_Preis == "Y") {
                            echo $meineVersandkosten->Waehrung."<BR>";
                        }
                        else {
                            echo $meineVersandkosten->Gewichts_Masseinheit."<BR>";
                        }
                        echo "<BR>\n";
                    }// End for
?>
                </td>
                <td>
                    &nbsp;&nbsp;&nbsp;
                </td>
                <td>
                    &nbsp;&nbsp;&nbsp;
                </td>
                <td valign="top">
               <!--     <B>Bis</B><BR>  Frueher (v.1.0.4-Entwicklerversion gab es Intervalle mit Von-Bis, jetzt wird Von als Ab verwendet -->
               <!--     <INPUT type='hidden' name='Bis1' size='5' maxlength='10' value='<?php echo $meinVersandkostenpreis[0]->Bis; ?>'>   -->
                    <BR>
                    <INPUT type='hidden' name='Bis2' size='5' maxlength='10' value='<?php echo $meinVersandkostenpreis[1]->Bis; ?>'>
                    <BR>
                    <INPUT type='hidden' name='Bis3' size='5' maxlength='10' value='<?php echo $meinVersandkostenpreis[2]->Bis; ?>'>
                    <BR>
                    <INPUT type='hidden' name='Bis4' size='5' maxlength='10' value='<?php echo $meinVersandkostenpreis[3]->Bis; ?>'>

                </td>
                <td>
                    &nbsp;&nbsp;&nbsp;
                </td>
                <td>
                    &nbsp;&nbsp;&nbsp;
                </td>
                <td valign="top">
                    <B>Betrag</B>
                    <img src="../Bilder/spacer.gif" width="1" height="31" align="top" alti="spacer">
                    <BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Darstellen der Spalte mit den Betraegen (Preise/Gewichtsangaben)
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Betrag_name = "Betrag".$i;
                        echo "<INPUT type='text' name='".$Betrag_name."' size='5' maxlength='10' value='".$meinVersandkostenpreis[($i-1)]->Betrag."'>\n";
                        echo $meineVersandkosten->Waehrung."<BR>";
                        echo "<BR>\n";
                    }// End for
?>
                </td>
                <td>
                    &nbsp;&nbsp;&nbsp;
                </td>
                <td>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Erstellen der als hidden-field uebergebenen Von_Bis_ID
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Von_Bis_ID_name = "Von_Bis_ID".$i;
                        echo "<INPUT type='hidden' name='".$Von_Bis_ID_name."' size='5' maxlength='10' value='".$meinVersandkostenpreis[($i-1)]->Von_Bis_ID."'>";
                        echo "<BR>\n";
                    }// End for
?>
                </td>
<?php           if ($meineVersandkosten->Vorauskasse == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;Voraus-&nbsp;<br>&nbsp;kasse&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Vorauskasse Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Vorauskasse_name = "Vorauskasse".$i;
                        echo "<INPUT type='checkbox' name='".$Vorauskasse_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->Vorauskasse == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php           }
                if ($meineVersandkosten->Rechnung == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;Rech-&nbsp;<br>&nbsp;nung&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Rechnungs Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Rechnungs_name = "Rechnung".$i;
                        echo "<INPUT type='checkbox' name='".$Rechnungs_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->Rechnung == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php           }
                if ($meineVersandkosten->Nachname == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;Nach-&nbsp;<br>&nbsp;name&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Nachnahme Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Nachnahme_name = "Nachname".$i;
                        echo "<INPUT type='checkbox' name='".$Nachnahme_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->Nachname == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php           }
                if ($meineVersandkosten->Lastschrift == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;Last-&nbsp;<br>&nbsp;schrift&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Lastschrift Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Lastschrift_name = "Lastschrift".$i;
                        echo "<INPUT type='checkbox' name='".$Lastschrift_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->Lastschrift == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php           }
                if ($meineVersandkosten->Kreditkarte == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;Kredit-&nbsp;<br>karten&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Kreditkarten Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Kreditkarte_name = "Kreditkarte".$i;
                        echo "<INPUT type='checkbox' name='".$Kreditkarte_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->Kreditkarte == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php
                }//End if Kreditkarte == Y
                // billBOX Zahlungsmethode wird in Zeile 1 referenziert (Zeile 1 = $weitereZahlungen[0])
                if ($weitereZahlungen[0]->verwenden == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;&nbsp;bill-&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;BOX&nbsp;&nbsp;&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: billBOX-Online Payment Methode Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $billBOX_name = "billBOX".$i;
                        echo "<INPUT type='checkbox' name='".$billBOX_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->billBOX == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php
                }//End if billBOX == Y
                // Treuhandzahlung Zahlungsmethode wird in Zeile 2 referenziert (Zeile 2 = $weitereZahlungen[1])
                if ($weitereZahlungen[1]->verwenden == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;Treu-&nbsp;<br>&nbsp;hand&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Treuhandzahlung Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Treuhandzahlung_name = "Treuhandzahlung".$i;
                        echo "<INPUT type='checkbox' name='".$Treuhandzahlung_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->Treuhandzahlung == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php
                }//End if Treuhandzahlung == Y
                // Postcard Zahlungsmethode wird in Zeile 4 (3=Saferpay) referenziert (Zeile 4 = $weitereZahlungen[3])
                if ($weitereZahlungen[3]->verwenden == 'Y') {
?>
                <td valign="top" align="center">
                    <CENTER>
                    <B><small>&nbsp;&nbsp;&nbsp;Post-&nbsp;<br>&nbsp;&nbsp;&nbsp;card&nbsp;</small></B><BR>
<?php
                    // Erstellen der Felder fuer die Auswertung des Formulars in diesem Modul (darstellen == 10)
                    // Hier: Treuhandzahlung Checkboxen fuer jeden Zahlungsintervall (Ab).
                    // ACHTUNG: Hier wird der Array von 0 bis ($anzahl_Versandkostenintervalle - 1) adressiert
                    for($i = 1; $i <= $anzahl_Versandkostenintervalle;$i++) {
                        $Postcard_name = "Postcard".$i;
                        echo "<INPUT type='checkbox' name='".$Postcard_name."'";
                        if ($meinVersandkostenpreis[($i-1)]->Postcard == "Y") {
                            echo " checked ";
                        }// End if
                        echo "><br><img src=\"../Bilder/spacer.gif\" width=\"1\" height=\"23\"><br>\n";
                    }// End for
?>
                    </CENTER>
                </td>
<?php
                }//End if Treuhandzahlung == Y
        }// End else nach Preis oder Gewicht anzeigen
?>
            </tr>
        </table>
        <HR>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td>
                    <INPUT type='checkbox' name='keineVersandkostenmehr'
<?php                   if ($meineVersandkosten->keineVersandkostenmehr == "Y") {
                            echo " checked ";
                        }
?>
                    >
                    Keine Versandkosten mehr berechnen ab Betrag:
                    <INPUT type='text' name='keineVersandkostenmehr_ab' size='5' maxlength='10' value='<?php echo $meineVersandkosten->keineVersandkostenmehr_ab; ?>'>
                    <?php echo $meineVersandkosten->Waehrung; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <BR>
                    <INPUT type='checkbox' name='Mindermengenzuschlag'
<?php                   if ($meineVersandkosten->Mindermengenzuschlag == "Y") {
                            echo " checked ";
                        }
?>
                    >
                    Mindermengenzuschlag<BR>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mindermengenzuschlag berechnen bis Bestellungstotal von:
                        <INPUT type='text' name='Mindermengenzuschlag_bis_Preis' size='5' maxlength='10' value='<?php echo $meineVersandkosten->Mindermengenzuschlag_bis_Preis; ?>'>
                        <?php echo $meineVersandkosten->Waehrung; ?><BR>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mindermengenzuschlag Aufpreis:
                        <INPUT type='text' name='Mindermengenzuschlag_Aufpreis' size='5' maxlength='10' value='<?php echo $meineVersandkosten->Mindermengenzuschlag_Aufpreis; ?>'>
                        <?php echo $meineVersandkosten->Waehrung; ?>
                </td>
            </tr>
            <tr>
                <td>
<?php               if ($meineVersandkosten->Nachname == "Y") {
?>                  <BR>
                    Nachnahmegeb&uuml;hr:
                    <INPUT type='text' name='Nachnamebetrag' size='5' maxlength='10' value='<?php echo $meineVersandkosten->Nachnamebetrag; ?>'>
<?php                   echo $meineVersandkosten->Waehrung;
                    }//End if
?>

                </td>
            </tr>
            <tr><td><br>&nbsp;</td></tr>
            <tr>
              <td valign=middle>
                <INPUT type='hidden' name='anzahl_Versandkostenintervalle' value='<?php echo $anzahl_Versandkostenintervalle; ?>'>
                <INPUT type='hidden' name='Setting_Nr' value='1'>
                <INPUT type='hidden' name='darstellen' value='10'>
                <input type=image src="../Buttons/bt_speichern_admin.gif" border="0">
                <a href='./Shop_Einstellungen_Menu_1.php' title='Abbrechen'>
                  <img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='Abbrechen'></a>
                <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Versandkosten')">
                  <img src='../Buttons/bt_hilfe_admin.gif' border='0' alt='Hilfe'></a>
              </td>
            </tr>
        </table>
        </form>
<?php
  } // end of else

echo "    </BODY>";
echo "</HTML>";
// End of file ----------------------------------------------------------
?>
