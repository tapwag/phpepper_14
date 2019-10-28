<?php
  // Filename: SHOP_SETTINGS.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Funktionen um den Shop zu konfigurieren
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_SETTINGS.php,v 1.76 2003/06/30 10:41:32 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_SETTINGS = true;

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

  // -------------------------------------------------------------------------------------------------------
  // HTML-Header ausgeben
  // -------------------------------------------------------------------------------------------------------
?>
  <html>
  <head>
      <meta HTTP-EQUIV="content-type" content="text/html;charset=iso-8859-1">
      <meta HTTP-EQUIV="language" content="de">
      <meta HTTP-EQUIV="author" content="Jose Fontanil & Reto Glanzmann">
      <meta name="robots" content="all">
      <title><?php echo getshopname();?> - Allgemeine Shop-Einstellungen</title>
      <link rel='stylesheet' href="./shopstyles.css" type="text/css">

    <SCRIPT LANGUAGE="JavaScript" type="text/javascript">
    <!-- Begin
    function popUp(URL) {
        day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=620,height=620,left = 100,top = 100');");
    }

    function popUpSize(URL, my_width, my_height) {
        day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=' + my_width + ',height=' + my_height + ',left = 60,top = 60');");
    }


    function showHaendlerInfo() {
        if (document.Formular.Haendlermodus.checked == true) {
            alert("ACHTUNG, Sie wollen den Händlermodus aktivieren!\n\nWenn der Händlermodus aktiviert ist wird kein unregistrierter Kunde mehr in den Shop gelassen.\nWenn also noch kein Kunde erstellt wurde, muss man zuerst einen erstellen.");
        }
        return true;
    }
    // End -->
    </script>
  </head>
  <body>
<?php
  // Weiche um zu unterscheiden ob das Formular abgeschickt wurde (Speichern-Button)
  // -------------------------------------------------------------------------------------------------------
  // Alle vom Formular her uebergebenen Daten in die Datenbank speichern
  // -------------------------------------------------------------------------------------------------------
  if ($darstellen == 10) {
    // ArtikelSuchInkrement (Anzahl gleichzeitig angezeigter Artikel einer Kategorie) auswerten
    if($ArtikelSuchInkrement_onoff == "" || $ArtikelSuchInkrement <= "0") {
        $ArtikelSuchInkrement = -1;
    }

    // MwSt-Einstellungen uebernehmen
    $mwstarray[] = new MwSt();
    $mwstarray = getmwstsettings();
    if($MwSt_on_off == "") {
        $MwStnummer = 0;
    }
    elseif ($MwSt_waehlen == "mwst_inkl" && $MwSt_on_off == "on") {
        for($i = 0; $i < count($mwstarray); $i++) {
            $mwstarray[$i]->Preise_inkl_MwSt = "Y";
        }
        // MwSt-Preise_inkl_MwSt-Einstellung in der Tabelle mehrwertsteuer updaten
        setmwstsettings($mwstarray);
    }
    elseif ($MwSt_waehlen == "mwst_exkl" && $MwSt_on_off == "on") {
        for($i = 0; $i < count($mwstarray); $i++) {
            $mwstarray[$i]->Preise_inkl_MwSt = "N";
        }
        // MwSt-Preise_inkl_MwSt-Einstellung in der Tabelle mehrwertsteuer updaten
        setmwstsettings($mwstarray);
    }
    else {
        die("<b>Fehler (3):</b> Die Variable MwSt_waehlen / MwSt_on_off lieferten einen nichtbehandelten Wert zur&uuml;ck: -".$MwSt_waehlen."-  <tt>--></tt> Abbruch!</body></html>");
    }
    // Aktuelle (noch nicht upgedatete MwSt-Nummer auslesen. Wenn bemerkt wird, dass das MwSt-Management NEU eingeschaltet wurde, so wird weiter
    // ueberprueft ob der erste Artikel (soritert nach Artikel_ID) den MwSt_Satz von 0% haben. Ist dem so, wird angenommen, dass noch keine
    // MwSt-Saetze verteilt wurde, und deshalb wird der MwSt-Satz ALLER Artikel und ALLER Kategorien INKL. ihren Unterkategorien mit dem
    // default-MwSt-Satz upgedated.
    $alteMwStnummer = getmwstnr();
    $mwst_all_flag = false; // Dieses Flag wird true, wenn allen Artikel des Shops der Default-MwSt-Satz zugeteilt wurde
    if ($alteMwStnummer == "0" && $MwStnummer != "0") {
        // MwSt-Nummer (und MwStpflichtig Attribut der Tabelle shop_settings) updaten
        setmwstnr($MwStnummer);
        // Alle Kategorien mit dem shopweiten Standard-MwSt-Satz updaten (da der Shop zuvor nich MwSt-pflichtig war)
        $ersterArtikel = getArtikel(1); //Artikel mit Artikel_ID 1 auslesen
        if ($ersterArtikel->MwSt_Satz == 0) {
            // Array mit Kategorie-IDs erstellen (Unterkat. werden automatisch mit ihren Hauptkat. upgedated)
            $Std_MwSt_Satz = getstandardmwstsatz();
            $myKategorien = getallKategorien();
            foreach ($myKategorien as $value) {
                // Alle Unterkategorien ausfindig machen und IDs auslesen
                $Ukat = array();
                $Ukat = $value->getallkategorien();
                if (count($Ukat) > 0) {
                    foreach ($Ukat as $wert) {
                        $Kategorie_IDarray[$wert->Kategorie_ID] = $Std_MwSt_Satz;
                    }
                }
                $Kategorie_IDarray[$value->Kategorie_ID] = $Std_MwSt_Satz;
            }
            setKatmwst($Kategorie_IDarray);
            $mwst_all_flag = true;
        }
    }
    else {
        // Nur MwSt-Nummer (und MwStpflichtig Attribut der Tabelle shop_settings) updaten
        setmwstnr($MwStnummer);
    }

    // Momentan noch statische Variable
    $Anzahl_Kreditkarten = 10;

    // Falls das Such-Inkrement kleiner als 1 gewaehlt wurde, so wird es auf einen vernuenftigen Wert gesetzt
    if ($SuchInkrement <= 0) {
        $SuchInkrement = 1;
    }
    // Kreditkartendaten aufbereiten und speichern
    // Herstellerarray ist schon ok
    $minimum = false; // Initialisierung, Wenn dieses Flag = true ist, so ist minimum eine Kreditkarte vorhanden
    for ($i=0;$i < $Anzahl_Kreditkarten; $i++) {
        if (($Herstellerarray[$i] != "") && ($benutzenarray[$i] == "on")) {
            $benutzenarray[$i] = "Y";
            $minimum = true; // Kreditkarten-Einstellung gefunden (siehe 4 Zeilen weiter oben)
        }
        else {
            $benutzenarray[$i] = "N";
        }
        if ($Herstellerarray[$i] == "") {
            $Handlingarray[$i] = "intern";
        }
    }//End foreach
    if (!setKreditkarten($Herstellerarray, $benutzenarray, $Handlingarray)) {
        die("<P><H1>S_S_Error: Kreditkarten-Konfiguration konnte nicht gespeichert werden, setKreditkarten() war nicht erfolgreich!</H1></P><BR>");
    }

    //Kreditkartenaktivierung
    if ($Kreditkarten_Postcard == "on") {
        $Kreditkarten_Postcard = "Y";
    }
    else {
        $Kreditkarten_Postcard = "N";
    }

    //Bezahlungsarten formatieren
    if ($Vorauskasse == "on") {
        $Vorauskasse = "Y";
    }
    else {
        $Vorauskasse = "N";
    }
    if ($Rechnung == "on") {
        $Rechnung = "Y";
    }
    else {
        $Rechnung = "N";
    }
    if ($Nachnahme == "on") {
        $Nachnahme = "Y";
    }
    else {
        $Nachnahme = "N";
    }
    if ($Lastschrift == "on") {
        $Lastschrift = "Y";
    }
    else {
        $Lastschrift = "N";
    }

    // Wenn keine einzige 'gueltige' Kreditkarte eingegeben wurde, wird die Kreditkarte als
    // moegliche Bezahlungsart deaktiviert und weiter unten der Benutzer darueber unterrichtet
    $Kreditkarte_war_leer = false; // True, wenn $Kreditkarten_Postcard abgeaendert wird
    if ((!$minimum) && ($Kreditkarten_Postcard == "Y")) {
        $Kreditkarten_Postcard = "N";
        $Kreditkarte_war_leer = true;
    }

    // Aufbereiten der zehn Treuhandzahlungsparameter (welche je zur Haelfte als Treuhandparameterx ankommen (x = 0-17, Spezialfall 18)
    $counter = 0; // Zaehler, welcher pro Schleife + 2 gerechnet wird
    for ($i = 1; $i < 10; $i++) {
        $Parameterteil1 = "Treuhandzahlung_Par".$counter;
        $Parameterteil2 = "Treuhandzahlung_Par".($counter+1);
        $Zielparameter = "Treuhandzahlung_Par".$i;
        $$Zielparameter = $$Parameterteil1."þ".$$Parameterteil2;
        $counter = $counter + 2;
    }
    // Abfuellen der Kundenkostenverteilung
    $Treuhandzahlung_Par18 = (100-(FLOAT)$Treuhandzahlung_Par18)."þ".(FLOAT)$Treuhandzahlung_Par18;
    $Treuhandzahlung_Par10 = $Treuhandzahlung_Par18;

    // Weitere Zahlungen - Einstellungen in ihre Tabelle zurueck schreiben
    $existierendeZahlungen = getAllezahlungen();
    // Es wird zuerst ueberprueft, wieviele eingetragene weitere Zahlungsmethoden in der Tabelle zahlung_weiter existieren.
    // Diese Anzahl wird durch ein hidden-field (anzahl_weitere_Zahlungsmethoden) vom Eingabeforumular her mit uebertragen.
    // Danach werden die Parameter der existierenden weiteren Zahlungsmethoden eingelesen und ueber die setAllezahlungen-Funktion
    // in der Datenbank gespeichert.
    if ($anzahl_weitere_Zahlungsmethoden >= 1) {
        for ($i = 0; $i < $anzahl_weitere_Zahlungsmethoden; $i++) {
            // 1/3.) Bezeichnung der aktuellen weiteren Zahlungsmethode auslesen:
            $aktuelle_Bezeichnung = $existierendeZahlungen->Zahlungsarray[$i]->Bezeichnung;
            // 2/3.) Variablennamen der aktuellen weiteren Zahlungsmethode zusammenstellen und unter aktuelle_xyz zur Verfuegung stellen
            $aktuelle_Gruppe = $aktuelle_Bezeichnung."_Gruppe";
            $aktuelle_verwenden = $aktuelle_Bezeichnung."_verwenden";
            $aktuelle_payment_interface_name = $aktuelle_Bezeichnung."_payment_interface_name";
            $aktuelle_Par1 = $aktuelle_Bezeichnung."_Par1";
            $aktuelle_Par2 = $aktuelle_Bezeichnung."_Par2";
            $aktuelle_Par3 = $aktuelle_Bezeichnung."_Par3";
            $aktuelle_Par4 = $aktuelle_Bezeichnung."_Par4";
            $aktuelle_Par5 = $aktuelle_Bezeichnung."_Par5";
            $aktuelle_Par6 = $aktuelle_Bezeichnung."_Par6";
            $aktuelle_Par7 = $aktuelle_Bezeichnung."_Par7";
            $aktuelle_Par8 = $aktuelle_Bezeichnung."_Par8";
            $aktuelle_Par9 = $aktuelle_Bezeichnung."_Par9";
            $aktuelle_Par10 = $aktuelle_Bezeichnung."_Par10";
            // 3/3.) Die aktuelle weitere Zahlungsmethode im Allezahlungen-Objekt mit den neuen Einstellungen anpassen
            // Zuerst aber noch die Checkbox-Auswertung vom Attribut verwenden:
            if ($$aktuelle_verwenden == "on") {
                $$aktuelle_verwenden = "Y";
            }
            else {
                $$aktuelle_verwenden = "N";
            }
            $existierendeZahlungen->Zahlungsarray[$i]->delarray(); //Löschen der alten Parameterwerte
            $existierendeZahlungen->Zahlungsarray[$i]->Gruppe = $$aktuelle_Gruppe;
            $existierendeZahlungen->Zahlungsarray[$i]->Bezeichnung = $aktuelle_Bezeichnung;
            $existierendeZahlungen->Zahlungsarray[$i]->verwenden = $$aktuelle_verwenden;
            $existierendeZahlungen->Zahlungsarray[$i]->payment_interface_name = $$aktuelle_payment_interface_name;
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par1);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par2);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par3);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par4);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par5);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par6);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par7);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par8);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par9);
            $existierendeZahlungen->Zahlungsarray[$i]->putparameter($$aktuelle_Par10);
        }// End for-Schleife
        // Jetzt sind alle weiteren Zahlungsmethoden mit den aktualisierten Einstellungen im Objekt $existierendeZahlungen vorhanden
        // Sie werden jetzt mit der Funktion [Boolean setAllezahlungen(Allezahlungen-Objekt)] in die Datenbank gespeichert.
        if (!setAllezahlungen($existierendeZahlungen)) {
            die("<h3>S_S_Error: Konnte die weiteren Zahlungsmethoden-Einstellungen nicht in der Datenbank speichern (setAllezahlungen). Die Kreditkartendaten wurden schon gespeichert.</h3></body></html>");
        }
    }// End if-Schleife
    // Aufbereiten des HTML-Codierten Euro-Symbols, bei Eingabe der Waehrung AltGr+E
    if ($Waehrung == "" || $Waehrung == "&#8364" || $Waehrung == "&#8364;") {
        $Waehrung = "&euro;";
    }

    // Auswertung, ob der Gesamtpreis einer Bestellung gerundet werden soll
    if ($Gesamtpreis_runden == "on") {
        $Gesamtpreis_runden = "Y";
    }
    else {
        $Gesamtpreis_runden = "N";
    }

    // Auswertung, ob der Haendlermodus eingeschaltet werden soll
    if ($Haendlermodus == "on") {
        $Haendlermodus = "Y";
    }
    else {
        $Haendlermodus = "N";
    }

    /* Auswertung, ob das Bestellungsmanagement eingeschaltet werden soll (ab v.1.4 immer = on und deshalb hier deaktiviert)
    if ($Bestellungsmanagement == "on") {
        $Bestellungsmanagement = "Y";
    }
    else {
        $Bestellungsmanagement = "N";
    }
    */

    // Auswertung, ob die Tell-A-Friend Funktionalitaet eingeschaltet werden soll (temp. ausgeschaltet)
    // Tell-a-Friend Funktionalitaet ein / ausschalten auswerten
    if ($tell_a_friend == "on") {
        $tell_a_friend = "Y";
    }
    else {
        $tell_a_friend = "N";
    }

    // Mein Konto (my-account) Funktionalitaet ein / ausschalten auswerten
    if ($my_account == "on") {
        $my_account = "Y";
    }
    else {
        $my_account = "N";
    }

    // Automatische Landeserkennung der Shopkunden aktivieren / deaktivieren
    if ($check_user_country == "on") {
        $check_user_country = "Y";
    }
    else {
        $check_user_country = "N";
    }

    // Alle (alten) Shop-Settings zurueck in die Datenbank speichern (Funktionsdefinition in SHOP_ADMINISTRATION.php)
    // Ab PhPepperShop v.1.4 werden alle neu hinzugekommenen Einstellungen einzeln via set_new_shop_setting() gesetzt.
    if (setshopsettings($Admin_pwd, $Name, $Adresse1, $Adresse2, $PLZOrt, $Tel1, $Tel2,
                $Email, $Thumbnail_Breite, $Mindermengenzuschlag_Aufpreis, $Mindermengenzuschlag, $Kreditkarten_Postcard, $Rechnung,
                $Waehrung, $Nachnahme, $Mindermengenzuschlag_bis_Preis, $keineVersandkostenmehr_ab, $keineVersandkostenmehr, $SSL,
                $Bestellungsmanagement, $Gewichts_Masseinheit,$max_session_time, $AGB, $Opt_inc, $Var_inc, $Opt_anz, $Var_anz,
                $SuchInkrement,$Vorauskasse, $Kontoinformation, $Vargruppen_anz, $Eingabefelder_anz, $Gesamtpreis_runden, $ArtikelSuchInkrement,$Lastschrift,
                $Sortieren_nach, $Sortiermethode,$Zahl_thousend_sep,$Zahl_decimal_sep,$Zahl_nachkomma, $Haendlermodus, $Haendler_login_text,
                $tell_a_friend, $tell_a_friend_bcc, $my_account,$check_user_country) && set_new_shop_setting("ArtikelSuchInkrementAnzeige","shop_settings",$ArtikelSuchInkrementAnzeige,"user")) {

         echo "<P><H1><B>SHOP ADMINISTRATION</B></H1></P>";
         echo "<p>Das Speichern aller Shop-Settings war erfolgreich!</p>";
         if ((!$minimum) && ($Kreditkarte_war_leer)) {
             echo "<p>ACHTUNG: Solange kein Kreditkarten Institut benutzt wird, ist die Bezahlung per Kreditkarte deaktiviert</p>";
         }
         if ($mwst_all_flag == true) {
             echo "<p>Allen Kategorien / Unterkategorien und ihren Artikeln wurde der default-MwSt-Satz von ".getstandardmwstsatz()."% zugewiesen.</p>";
         }
         echo "<BR><a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0' alt='weiter'></a><BR>";
    }
    else {
        echo "<P><H1>S_S_Error: Shop-Settings konnten nicht gespeichert werden, setshopsettings() war nicht erfolgreich!</H1><b>Die MwSt-Settings wurden aber &uuml;bernommen!</b></P><BR>";
    }
  }
  // -------------------------------------------------------------------------------------------------------
  // Allgemeine Shopeinstellungen Formular (Shop Settings):
  // -------------------------------------------------------------------------------------------------------
  else {
    // Einlesen ALLER Shopsettings (Alles aus der Tabelle shop_settings)
    $Anzahl_Kreditkarten = 10; // Momentan noch fixe Anzahl Kreditkarten (auch in setKreditkarten)
    $Shopsettings = array();
    $Shopsettings = getshopsettings();
    $shopsettings_new = get_new_shop_setting("*","shop_settings");
    // Kreditkartendaten auslesen und zur Anzeige aufbereiten
    $Kreditkartenarray = array();
    $Kreditkartenarray = getKreditkarten();
    // Einlesen der weiteren Zahlungsmethoden
    $Zahlung_weiter = new Allezahlungen;
    $Zahlung_weiter = getAllezahlungen();
    foreach ($Kreditkartenarray as $key=>$value) {
        $Herstellerarray[] = $value->Hersteller;
        $Handlingarray[] = $value->Handling;
        $benutzenarray[] = $value->benutzen;
    }
    // Test, ob die weiteren Zahlungen wie erwartet in der Datenbank vorhanden sind oder nicht (oft ein Problem, weil bei der
    // Installation das Einfuegen der Datei template_insert.sql (oder shopname_insert.sql) nicht klappt (Zeitlimitenbeschraenkung, ...)
    if ($Zahlung_weiter->Zahlungsarray[0]->Bezeichnung != "billBOX") {
        die("<h1>Abbruch weil Shop nicht richtig installiert ist</h1><p>Die Tabelle zahlung_weitere hat nicht den korrekten Inhalt (billBOX) -> bitte den Shop nochmals korrekt installieren</body></html>");
    }
    elseif ($Zahlung_weiter->Zahlungsarray[1]->Bezeichnung != "Treuhandzahlung") {
        die("<h1>Abbruch weil Shop nicht richtig installiert ist</h1><p>Die Tabelle zahlung_weitere hat nicht den korrekten Inhalt (Treuhandzahlung) -> bitte den Shop nochmals korrekt installieren</body></html>");
    }

    // Einlesen der MwSt-Einstellungen
    $MwStnummer = getmwstnr();
    $MwStarray = getmwstsettings();
  ?>
  <script language="JavaScript">
  <!--
  function chkFormular() {
      if(document.Formular.Thumbnail_Breite.value == "") {
          alert("Bitte einen Breitenwert für die Thumbnails eingeben (normal:100)!");
          document.Formular.Thumbnail_Breite.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Thumbnail_Breite.value.length;++i) {
          if(document.Formular.Thumbnail_Breite.value.charAt(i) < "0" || document.Formular.Thumbnail_Breite.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Thumbnail-Breite enthält ungültige Zeichen!");
          document.Formular.Thumbnail_Breite.focus();
          return false;
      }
      if(document.Formular.max_session_time.value == "") {
          alert("Bitte einen Verfallswert für die Session eingeben! normal:1440");
          document.Formular.max_session_time.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.max_session_time.value.length;++i) {
          if(document.Formular.max_session_time.value.charAt(i) < "0" || document.Formular.max_session_time.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("maximale Session Zeit enthält ungültige Zeichen!");
          document.Formular.max_session_time.focus();
          return false;
      }
      if(document.Formular.Opt_anz.value == "") {
          alert("Bitte Anzahl Optionsfelder eingeben! normal:5");
          document.Formular.Opt_anz.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Opt_anz.value.length;++i) {
          if(document.Formular.Opt_anz.value.charAt(i) < "0" || document.Formular.Opt_anz.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Anzahl Optionsfelder enthält ungültige Zeichen!");
          document.Formular.Opt_anz.focus();
          return false;
      }
      if(document.Formular.Var_anz.value == "") {
          alert("Bitte Anzahl Variationsfelder eingeben (Normal:5)!");
          document.Formular.Var_anz.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Var_anz.value.length;++i) {
          if(document.Formular.Var_anz.value.charAt(i) < "0" || document.Formular.Var_anz.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Anzahl Variationsfelder enthält ungültige Zeichen!");
          document.Formular.Var_anz.focus();
          return false;
      }
      if(document.Formular.Opt_inc.value == "") {
          alert("Bitte Anzahl leere Optionsfelder eingeben (Normal:3)!");
          document.Formular.Opt_inc.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Opt_inc.value.length;++i) {
          if(document.Formular.Opt_inc.value.charAt(i) < "0" || document.Formular.Opt_inc.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("leere Optionsfelder enthält ungültige Zeichen!");
          document.Formular.Opt_inc.focus();
          return false;
      }
      if(document.Formular.Var_inc.value == "") {
          alert("Bitte Anzahl leere Variationsfelder eingeben (Normal:5)!");
          document.Formular.Var_inc.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Var_inc.value.length;++i) {
          if(document.Formular.Var_inc.value.charAt(i) < "0" || document.Formular.Var_inc.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("leere Variationsfelder enthält ungültige Zeichen!");
          document.Formular.Var_inc.focus();
          return false;
      }
      if(document.Formular.Vargruppen_anz.value == "") {
          alert("Bitte Anzahl Variationsgruppen eingeben (Normal:3)!");
          document.Formular.Vargruppen_anz.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Vargruppen_anz.value.length;++i) {
          if(document.Formular.Vargruppen_anz.value.charAt(i) < "0" || document.Formular.Vargruppen_anz.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Anzahl Variationsgruppen enthält ungültige Zeichen!");
          document.Formular.Vargruppen_anz.focus();
          return false;
      }
      if(document.Formular.Eingabefelder_anz.value == "") {
          alert("Bitte Anzahl Texteingabefelder eingeben!");
          document.Formular.Vargruppen_anz.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.Eingabefelder_anz.value.length;++i) {
          if(document.Formular.Eingabefelder_anz.value.charAt(i) < "0" || document.Formular.Eingabefelder_anz.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Anzahl Texteingabefelder enthält ungültige Zeichen!");
          document.Formular.Eingabefelder_anz.focus();
          return false;
      }
      nummerisch = 1;
      for(i=0;i<document.Formular.ArtikelSuchInkrement.value.length;++i) {
          if(document.Formular.ArtikelSuchInkrement.value.charAt(i) < "-1" || document.Formular.ArtikelSuchInkrement.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Anzahl gleichzeitig angezeigter Artikel enthält ungültige Zeichen!");
          document.Formular.ArtikelSuchInkrement.focus();
          return false;
      }
      // Wenn MwSt-pflichtig angewaehlt wurde, check ob auch ein Preis inkl / exkl angewaehlt wurde
      if (document.Formular.MwSt_on_off.checked == true) {
          if (document.Formular.elements[15].checked == false) {
              if (document.Formular.elements[16].checked == false) {
                  if (document.Formular.MwSt_on_off.checked == true) {
                      alert("Der Shop wurde als MwSt-pflichtig deklariert. Bitte definieren, ob die im Shop angegebenen Artikelpreise inkl. oder exkl. MwSt sind.");
                      document.Formular.MwSt_on_off.focus();
                      return false;
                  }
              }
          }
      }
      if(document.Formular.elements[14].value == 0 || document.Formular.elements[14].value == "") {
          if (document.Formular.MwSt_on_off.checked == true) {
            alert("Die MwSt-Nummer muss noch definiert werden");
            document.Formular.elements[14].focus();
            return false;
          }
      }
      // Treuhandkostenverteilung (Prozentwert 0-100), 1.) Test auf nummerische Zeichen, 2.) Test auf 0-100
      nummerisch = 1;
      for(i=0;i<document.Formular.Treuhandzahlung_Par18.value.length;++i) {
          if(document.Formular.Treuhandzahlung_Par18.value.charAt(i) < "0" || document.Formular.Treuhandzahlung_Par18.value.charAt(i) > "9")
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Die Kostenverteilung enthält ungültige Zeichen. Sie ist ein Prozentwert. Bitte nur Werte von 0-100 eingeben!");
          document.Formular.Treuhandzahlung_Par18.focus();
          return false;
      }
      if((document.Formular.Treuhandzahlung_Par18.value > 100) || (document.Formular.Treuhandzahlung_Par18.value < 0)) {
          alert("Die Kostenverteilung enthält einen zu hohen / zu niedrigen Wert. Sie ist ein Prozentwert. Bitte nur Werte von 0-100 eingeben!");
          document.Formular.Treuhandzahlung_Par18.focus();
          return false;
      }
<?php
      // JavaScript Ueberpruefung fuer die Treuhandeingabefelder (9x2 Zahleneingabefelder)
      for ($i = 0; $i < 18; $i ++) {
          echo "nummerisch = 1;\n";
          echo "for(i=0;i<document.Formular.Treuhandzahlung_Par$i.value.length;++i) {\n";
          echo "    if(document.Formular.Treuhandzahlung_Par$i.value.charAt(i) < \"0\" || document.Formular.Treuhandzahlung_Par$i.value.charAt(i) > \"9\")\n";
          echo "    nummerisch = 0;\n";
          echo "}\n";
          echo "if(nummerisch == 0) {\n";
          echo "    alert(\"Dieser Bestellwert / Treuhandkostenwert enthält ungültige Zeichen. Es können nur nummerische Werte eingegeben werden!\");\n";
          echo "    document.Formular.Treuhandzahlung_Par$i.focus();\n";
          echo "    return false;\n";
          echo "}\n";
      }
?>
  }
  // Folgende JavaScript Funktion liefert je nach Payment Institut Warnungen, wenn man sie benutzen will aber nicht konfiguriert hat
  function test_if_available(wert) {
      if (document.getElementById("saferpay_test2").value == '' && wert == 'saferpay') {
          alert("Achtung, Saferpay / B+S Card Service kann nicht benutzt werden, wenn die Zugangsdaten nicht eingegeben wurden!");
          document.getElementById("saferpay_test2").focus();
      }
      if (document.getElementById("postfinance_check").checked == false && wert == 'postfinance') {
          alert("Achtung, PostFinance yellowpay kann nicht benutzt werden, wenn Sie als Shopbetreiber keinen Vertrag mit PostFinance haben!");
          document.getElementById("postfinance_check").focus();
      }
  }
  //-->
  </script>
  <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
  <p><h3><b>Allgemeine Shopeinstellungen</b></h3></p>
  <?php
      // Test ob die Tabelle zahlung_weitere korrekt ausgelesen wurde
      if (!is_object($Zahlung_weiter->Zahlungsarray[0])) {
          die("<h1>S_S_Error: Zahlungsarray[0] ist kein Objekt. Konnte die weiteren Zahlungsmethoden nicht darstellen -> Wurde der Shop korrekt installiert? (database/shopname_insert.sql?)</h1></body></html>");
      }
  ?>

  <form name="Formular" action="<?php echo"./SHOP_SETTINGS.php";?>" method="post" title="Shop_Settings" onSubmit="return chkFormular()">
  <table border="0" cellpadding="0" cellspacing="2">
    <tr>
      <td colspan=6><b>Adressinformationen Shopbetreiber<b></td>
    </tr>
    <tr>
      <td>Shopname:</td>
      <td><input type="text" name="Name" size="24" maxlength='48' value="<?php echo $Shopsettings[Name];?>"></td>
      <td>&nbsp;&nbsp;&nbsp;</td>
      <td>Shopversion:</td>
      <td>
        <B><I><?php echo $Shopsettings[ShopVersion].' GPL <a target="_blank" href="http://www.phpeppershop.com/index.php?komme_von_gpl=true&version='.urlencode($Shopsettings[ShopVersion]).'"><small>(Aktuelle Shopversion)</small></a>';?></I></B>
        <!-- <input type="hidden" name="ShopVersion" value="<?php echo $Shopsettings[ShopVersion] ?>"> -->
        <input type="hidden" name="Versionsanzeige" value="<?php echo $Shopsettings[ShopVersion] ?>">
        <input type="hidden" name="keineVersandkostenmehr" value="<?php echo $Shopsettings[keineVersandkostenmehr] ?>">
        <input type="hidden" name="keineVersandkostenmehr_ab" value="<?php echo $Shopsettings[keineVersandkostenmehr_ab] ?>">
        <input type="hidden" name="Mindermengenzuschlag" value="<?php echo $Shopsettings[Mindermengenzuschlag] ?>">
        <input type="hidden" name="Mindermengenzuschlag_Aufpreis" value="<?php echo $Shopsettings[Mindermengenzuschlag_Aufpreis] ?>">
        <input type="hidden" name="Mindermengenzuschlag_bis_Preis" value="<?php echo $Shopsettings[Mindermengenzuschlag_bis_Preis] ?>">
      </td>
    </tr>
    <tr>
      <td>Adresse 1:</td>
      <td><input type="text" name="Adresse1" size="24" maxlength='48' value="<?php echo $Shopsettings[Adresse1];?>"></td>
      <td></td>
      <td>E-Mail Adresse des Shops:</td>
      <td><input type="text" name="Email" size="30" maxlength='64' value="<?php echo $Shopsettings[Email];?>"></td>
    </tr>
    <tr>
      <td>Adresse 2:</td>
      <td><input type="text" name="Adresse2" size="24" maxlength='48' value="<?php echo $Shopsettings[Adresse2];?>"></td>
      <td></td>
      <td>Telefonnummer:</td>
      <td><input type="text" name="Tel1" size="16" maxlength='16' value="<?php echo $Shopsettings[Tel1];?>"></td>
    </tr>
    <tr>
      <td>PLZ und Ort:</td>
      <td><input type="text" name="PLZOrt" size="24" maxlength='48' value="<?php echo $Shopsettings[PLZOrt];?>"></td>
      <td></td>
      <td>Faxnummer:</td>
      <td><input type="text" name="Tel2" size="16" maxlength='16' value="<?php echo $Shopsettings[Tel2];?>"></td>
    </tr>
  </table>
  <hr>

  <table border="0" cellpadding="0" cellspacing="2">
    <tr>
      <td colspan=5><b>Mehrwertsteuereinstellungen<b></td>
    </tr>
    <tr>
      <td colspan="4">
          <input type="checkbox" name="MwSt_on_off" <?php if ($MwStnummer != "0") {echo "checked";}?> onClick="erweitern()">Shop ist MwSt-pflichtig
          <nobr><table id="visible_div" border="0" frame="void"><tr><td>
            MwSt-Nummer: <input type="text" name="MwStnummer" value="<?php echo $MwStnummer; ?>" size="24">&nbsp;
            <input type="radio" value="mwst_inkl" name="MwSt_waehlen" <?php if ($MwStarray[0]->Preise_inkl_MwSt == "Y"  && $MwStnummer != "0") {echo "checked";}?>>Preisangaben sind inkl. MwSt
            <input type="radio" value="mwst_exkl" name="MwSt_waehlen" <?php if ($MwStarray[0]->Preise_inkl_MwSt == "N" && $MwStnummer != "0") {echo "checked";}?>>Preisangaben sind exkl. MwSt
          </td></tr></table></nobr>
          <script type="text/javascript">
          <!--
          // Zeigt folgende Tabelle an, wenn die Checkbox 'MwSt_on_off' angewaehlt wird
          // sonst, wird sie ausgeblendet:
          function erweitern() {
              if (document.Formular.MwSt_on_off.checked == true) {
                  document.Formular.elements[15].checked = true;
                  document.getElementById("visible_div").style.visibility = "visible";
              }
              else {
                  document.getElementById("visible_div").style.visibility = "hidden";
                  document.Formular.elements[15].checked = false;
                  document.Formular.elements[16].checked = false;
              }
          }// End function erweitern

          // Wenn nicht MwSt-pflichtig -> folgende Tabelle via JavaScript ausblenden:
          if (document.Formular.MwSt_on_off.defaultChecked == false) {
              document.getElementById("visible_div").style.visibility = "hidden";
          }
          //-->
          </script>
      </td>
    </tr>
  </table>
  <hr>

  <table border="0" cellpadding="0" cellspacing="2">
    <tr>
      <td colspan="7"><b>Akzeptierte Zahlungsarten</b></td>
    </tr>
    <tr>
      <td>Vorauskasse:</td>
      <td colspan="2"><input type="checkbox" <?php if($Shopsettings[Vorauskasse] == 'Y'){echo "checked";};?> name="Vorauskasse">&nbsp;&nbsp;&nbsp;</td>
      <td colspan="4">Konto:&nbsp;<input type="text" value="<?php echo $Shopsettings[Kontoinformation]; ?>" name="Kontoinformation" size="64"></td>
    </tr>
    <tr>
      <td>Rechnung:</td>
      <td colspan="6"><input type="checkbox" <?php if($Shopsettings[Rechnung] == 'Y'){echo "checked";};?> name="Rechnung"></td>
    </tr>
    <tr>
      <td>Lastschrift:</td>
      <td colspan="6"><input type="checkbox" <?php if($Shopsettings[Lastschrift] == 'Y'){echo "checked";};?> name="Lastschrift"></td>
    </tr>
    <tr>
      <td>Nachnahme:</td>
      <td colspan="2"><input type="checkbox" <?php if($Shopsettings[Nachnahme] == 'Y'){echo "checked";};?> name="Nachnahme"></td>
      <td colspan="4">Die Nachnahme Geb&uuml;hr kann in den Versandkosten-Einstellungen definiert werden.</td>
    </tr>
    <tr valign = top>
      <td  valign = top>billBOX:</td>
      <td colspan="2"  valign = top><input type="checkbox" <?php if($Zahlung_weiter->Zahlungsarray[0]->verwenden == 'Y'){echo "checked";};?> name="<?php echo $Zahlung_weiter->Zahlungsarray[0]->Bezeichnung; ?>_verwenden">&nbsp;&nbsp;&nbsp;</td>
      <td colspan="3"  valign = top>
        billBOX-Scriptname:&nbsp;<input type="text" value="<?php $Par1 = $Zahlung_weiter->Zahlungsarray[0]->getallparameter(); echo $Par1[0]; ?>" name="<?php echo $Zahlung_weiter->Zahlungsarray[0]->Bezeichnung; ?>_Par1" size="48">
        <br><a href="http://www.phpeppershop.com/index_billbox.html" target=_new>Was ist billBOX?</a>
        <input type="hidden" name="billBOX_Bezeichnung" value="billBOX">
        <input type="hidden" name="billBOX_payment_interface_name" value="Kein payment_interface verwendet, U_B_1_darstellen = 8">
        <input type="hidden" name="billBOX_Gruppe" value="billBOX">
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr><td colspan="7">&nbsp;</td></tr>
    <tr>
      <td>Kreditkarten:</td>
      <td colspan="2"><input type="checkbox" <?php if($Shopsettings[Kreditkarten_Postcard] == 'Y'){echo "checked";};?> name="Kreditkarten_Postcard"></td>
      <td><center>Institut</center></td>
      <td><center>aktiv</center></td>
      <td><center>Handling</center></td>
      <td>&nbsp;</td>
    </tr>
<?php
    //Alle Kreditkartendaten anzeigen
    for ($i=0; $i < $Anzahl_Kreditkarten; $i++) {
?>
    <tr>
      <td colspan=3>&nbsp;</td>
      <td align="center"><input type="text" value="<?php echo $Herstellerarray[$i]; ?>" name="Herstellerarray[<?php echo $i; ?>]" size="32"></td>
      <td align="center"><input type="checkbox" name="benutzenarray[<?php echo $i; ?>]"<?php if ($benutzenarray[$i] == 'Y') {echo " checked ";}?>></td>
      <td align="center" style='font-size:15px; font-family: Courier, Courier New, Monaco'>
          <select name="Handlingarray[<?php echo $i; ?>]" size="1" onChange="test_if_available(this.value)">
              <option value='intern' <?php if ($Handlingarray[$i] == "intern") {echo "selected";}?>>intern</option>
              <option value='extern' <?php if ($Handlingarray[$i] == "extern") {echo "selected";}?>>extern</option>
          </select>
      </td>
      <td>&nbsp;</td>
    </tr>
<?php
    }// End for $i
?>
    <tr valign = top>
      <td  valign = top><br><?php echo $Zahlung_weiter->Zahlungsarray[1]->Bezeichnung; ?>:</td>
      <td colspan="2" valign="top"><br><input type="checkbox" <?php if($Zahlung_weiter->Zahlungsarray[1]->verwenden == 'Y'){echo "checked";};?> name="<?php echo $Zahlung_weiter->Zahlungsarray[1]->Bezeichnung; ?>_verwenden">&nbsp;&nbsp;&nbsp;</td>
      <td colspan="4" valign="top" align="left"><br>Wieviel Prozent der Treuhandkosten sollen dem Kunden belastet werden:
<?php
        // Parsen der Treuhandsparameter (Wert1þWert2). Das Delimiterzeichen þ entspricht ALT + 0254
        $Parparsed = $Zahlung_weiter->Zahlungsarray[1]->getallparameter();
        $Partemp = explode("þ",$Parparsed[9]);
        // Ausgabe der Kostenaufteilung in %:
        echo "<input type=\"text\" value=\"".$Partemp[1]."\" name=\"Treuhandzahlung_Par18\" size=\"3\">%";
        // Ausgabe der Bestellungssumme und zugeordneten Treuhandservicegebuehr
        // Enthaelt zu allen geraden (inkl. 0) Indexzahlen den Wert1 und zu allen ungeraden Indexzahlen den entsprechenden Wert2
        // Bsp. Beim Tabelleneintrag Wert1þWert2 finden sich die Werte hier: $Parparsed[0] fuer Wert1, $Parparsed[1] fuer Wert2
        // Darstellen der Textinputfelder fuer die Treuhandparameter 1-9:
        echo "<br><br>Treuhandskosten im Verh&auml;ltnis zum Bestellungswert ohne Versandkosten, Werte in ".$Shopsettings[Waehrung].":<br>\n";
        echo "<table border=\"0\"><tr>\n";
        echo "<td align=\"center\">Bis Bestellwert:</td><td align=\"right\">Geb&uuml;hr:&nbsp;</td><td>&nbsp;&nbsp;</td>\n";
        echo "<td align=\"center\">Bis Bestellwert:</td><td align=\"right\">Geb&uuml;hr:&nbsp;</td><td>&nbsp;&nbsp;</td>\n";
        echo "<td align=\"center\">Bis Bestellwert:</td><td align=\"right\">Geb&uuml;hr:&nbsp;&nbsp;</td>\n";
        echo "<tr>\n";
        $counter = 0; // Zaehlt pro Schleife + 2 hoch
        $Partemp = array(); // Neu initialisieren
        for ($i = 0; $i < (count($Parparsed)-1); $i++) {
            $Partemp = explode("þ",$Parparsed[$i]);
            echo "<td align=\"center\" valign=\"middle\">".($i+1).".)&nbsp;<input type=\"text\" value=\"".$Partemp[0]."\" name=\"Treuhandzahlung_Par".$counter."\" size=\"6\"><tt>-></tt></td>\n";
            echo "<td><input type=\"text\" value=\"".$Partemp[1]."\"name=\"Treuhandzahlung_Par".($counter+1)."\" size=\"4\">&nbsp;&nbsp;</td>\n";
            if ($i == 2 || $i == 5 || $i == 8) {echo "</tr>\n<tr>\n";} else { echo "<td>&nbsp;</td>"; }
            $counter = $counter + 2;
        }
        echo "</tr>\n</table>\n";
?>
        <input type="hidden" name="<?php echo $Zahlung_weiter->Zahlungsarray[1]->Bezeichnung; ?>_Bezeichnung" value="<?php echo $Zahlung_weiter->Zahlungsarray[1]->Bezeichnung; ?>">
        <input type="hidden" name="<?php echo $Zahlung_weiter->Zahlungsarray[1]->Bezeichnung; ?>_payment_interface_name" value="Kein payment_interface verwendet, U_B_1_darstellen = 9">
        <input type="hidden" name="<?php echo $Zahlung_weiter->Zahlungsarray[1]->Bezeichnung; ?>_Gruppe" value="<?php echo $Zahlung_weiter->Zahlungsarray[1]->Gruppe; ?>">
      </td>
    </tr>
  </table>
  <hr>
  <table border="0" cellpadding="0" cellspacing="2">
    <tr>
      <td colspan=5><b>Masseinheiten</b></td>
      <td></td>
    </tr>
    <tr>
      <td>W&auml;hrung:</td>
      <td><input type="text" value="<?php echo $Shopsettings[Waehrung];?>" name="Waehrung" size="4"></td>
    </tr>
    <tr>
      <td>Gewichtsmass:</td>
      <td><input type="text" value="<?php echo $Shopsettings[Gewichts_Masseinheit];?>" name="Gewichts_Masseinheit" size="16"></td>
    </tr>
  </table>
  <hr>
  <table border="0" cellpadding="0" cellspacing="2" width="100%">
    <tr>
      <td colspan=4><b>Shop-Konfiguration</b></td>
    </tr>
    <tr>
      <td>Breite der Mini-Bilder in Pixel:</td>
      <td><input type="text" size=10 value="<?php echo $Shopsettings[Thumbnail_Breite];?>" name="Thumbnail_Breite" size="24"></td>
      <td>&nbsp;&nbsp;</td>
      <td><I>ACHTUNG: Erst aktiv f&uuml;r Bilder die nach dem &Auml;ndern eingef&uuml;gt werden!</I></td>
    </tr>
    <tr>
      <td>maximale Session Zeit:</td>
      <td><nobr><input type="text" size=10 value="<?php echo $Shopsettings[max_session_time];?>" name="max_session_time" size="24"> Sek.</nobr></td>
      <td>&nbsp;</td>
      <td><I>ACHTUNG: Eine weitere Restriktion nach oben in der <tt>php.ini!</tt></I></td>
    </tr>
    <tr>
      <td valign="middle">SSL / TLS:</td>
      <td valign="middle"><input type="checkbox" value="Y" <?php if($Shopsettings[SSL] == 'Y'){echo "checked";};?> name="SSL" id="SSL_TLS"></td>
      <td>&nbsp;&nbsp;</td>
      <td><I>ACHTUNG: Nur einschalten wenn ihr Webserver auch SSL-Unterst&uuml;tzung bietet!<BR>(Kundendateneingabe und Login werden dann mit einer SSL-Verbindung gesch&uuml;tzt)</I>
          <input type="hidden" name="Bestellungsmanagement" value="Y">
      </td>
    </tr>
    <!-- Das Bestellungsmanagement wurde in v.1.4 durch ein Kundenmanagment ersetzt und ist jetzt defaultmaessig immer eingeschaltet
    <tr>
      <td>Bestellungsmanagement:</td>
      <td>
        <input type="checkbox" <?php if ($Shopsettings[Bestellungsmanagement] == 'Y'){echo "checked";};?> name="Bestellungsmanagement">
      </td>
      <td>&nbsp;&nbsp;</td>
      <td><I>ACHTUNG: Wenn das Bestellungsmanagement ausgeschaltet wird, so werden<BR>die Bestellungen nicht mehr gespeichert!</I></td>
    </tr>
    -->
    <tr>
      <td>Anzahl gleichzeitig angezeigter Suchresultate:</td>
      <td><input type="text" size=10 value="<?php echo $Shopsettings[SuchInkrement];?>" name="SuchInkrement" size="24"></td>
      <td>&nbsp;</td>
      <td><I>Beschr&auml;nkt die Anzahl gleichzeitig angezeigter Artikel bei einer Suche.</I></td>
    </tr>
    <tr>
      <td>Anzahl gleichzeitig angezeigter Artikel einschr&auml;nken:
      <td><input type="checkbox" name="ArtikelSuchInkrement_onoff" <?php if ($Shopsettings[ArtikelSuchInkrement] > 0) {echo "checked";}?> onClick="erweiternArtInkr()"></td>
      <td colspan="2">
          <div id="visible_td">
              &nbsp;&nbsp;&nbsp;Wieviele Artikel einer Kategorie sollen gleichzeitig angezeigt werden:
              <input type="text" size="3" value="<?php if ($Shopsettings[ArtikelSuchInkrement] <= -1) {echo 5;} else {echo $Shopsettings[ArtikelSuchInkrement];}?>" name="ArtikelSuchInkrement" maxlength="4">
              <br>&nbsp;&nbsp;&nbsp;Anzeige:
              <select name="ArtikelSuchInkrementAnzeige">
                <option value="unten" <?php if ($shopsettings_new['ArtikelSuchInkrementAnzeige'] == "unten") {echo " selected";} ?>>unten</option>
                <option value="oben" <?php if ($shopsettings_new['ArtikelSuchInkrementAnzeige'] == "oben") {echo " selected";} ?>>oben</option>
                <option value="unten_und_oben" <?php if ($shopsettings_new['ArtikelSuchInkrementAnzeige'] == "unten_und_oben") {echo " selected";} ?>>unten und oben</option>
          </div>
      </td>
    </tr>
    <tr>
      <td>Artikelanzeige sortieren nach:</td>
      <td colspan="3">
        <select name="Sortieren_nach" size="1">
          <!-- Der Wert entspricht auch gleich dem Tabellenattribut der Tabelle artikel -->
          <option value="a.Name" <?php if ($Shopsettings[Sortieren_nach] == "a.Name") {echo "selected";} ?>>Name
          <option value="a.Preis" <?php if ($Shopsettings[Sortieren_nach] == "a.Preis") {echo "selected";} ?>>Preis
          <option value="a.Artikel_Nr" <?php if ($Shopsettings[Sortieren_nach] == "a.Artikel_Nr") {echo "selected";} ?>>Artikel Nr.
          <option value="a.Gewicht" <?php if ($Shopsettings[Sortieren_nach] == "a.Gewicht") {echo "selected";} ?>>Gewicht
          <option value="a.letzteAenderung" <?php if ($Shopsettings[Sortieren_nach] == "a.letzteAenderung") {echo "selected";} ?>>Letzter &Auml;nderung
          <option value="a.Beschreibung" <?php if ($Shopsettings[Sortieren_nach] == "a.Beschreibung") {echo "selected";} ?>>Beschreibung
        </select>
        &nbsp;
        <input type="radio" value="ASC" name="Sortiermethode" <?php if ($Shopsettings[Sortiermethode] == "ASC") {echo "checked";}?>>aufsteigend&nbsp;&nbsp;&nbsp;
        <input type="radio" value="DESC" name="Sortiermethode" <?php if ($Shopsettings[Sortiermethode] == "DESC") {echo "checked";}?>>absteigend
      </td>
    </tr>
    <tr>
      <td>Zahlenformat der Preise:<br>
      (nur kundenseitig)</td>
      <td colspan="2">
          &nbsp;
      </td>
      <td>
          <table border="0" cellpadding="0" cellspacing="0">
              <tr><td>Tausender Trennzeichen:</td><td><input type="text" size="3" value="<?php echo $Shopsettings[Zahl_thousend_sep]; ?>" name="Zahl_thousend_sep" maxlength="2"></td></tr>
              <tr><td>Dezimal Trennzeichen:</td><td><input type="text" size="3" value="<?php echo $Shopsettings[Zahl_decimal_sep]; ?>" name="Zahl_decimal_sep" maxlength="2"></td></tr>
              <tr><td>Anzahl Nachkommastellen:</td><td><input type="text" size="3" value="<?php echo $Shopsettings[Zahl_nachkomma]; ?>" name="Zahl_nachkomma" maxlength="3"></td></tr>
          </table>
      </td>
    </tr>
    <tr>
      <td>Tell-a-Friend:</td>
      <td><input type="checkbox" <?php if($Shopsettings[tell_a_friend] == 'Y'){echo "checked";};?> name="tell_a_friend" onClick="erweiternTellAFriend()"></td>
      <td>&nbsp;&nbsp;</td>
      <td>
        <i>Wenn Tell-a-Friend eingeschaltet ist, k&ouml;nnen Kunden ihren Freunden Artikelempfehlungen senden.</i>&nbsp;
        <div id="visible_td2">
          E-Mailkopie senden an: <input type="text" size="30" value="<?php echo $Shopsettings[tell_a_friend_bcc]; ?>" name="tell_a_friend_bcc" maxlength="255">
        </div>
      </td>
    </tr>
    <!--
    <tr>
      <td>Autom. Erkennung des Landes:</td>
      <td>
        <input type="checkbox" <?php if($Shopsettings[check_user_country] == 'Y'){echo "checked";};?> name="check_user_country">
      </td>
      <td>&nbsp;&nbsp;</td>
      <td><I>ACHTUNG: Wird die automatische Erkennung des Landes des Shopkunden aktiviert, kann der erstmalige Zugriff von noch nicht registrierten Shopkunden auf die Kasse verlangsamt werden.</I></td>
    </tr>
    -->
    <tr>
      <td>H&auml;ndlermodus (Loginpflicht):</td>
      <td>
        <input type="checkbox" <?php if($Shopsettings[Haendlermodus] == 'Y'){echo "checked";};?> name="Haendlermodus" onClick="return showHaendlerInfo()">
      </td>
      <td>&nbsp;&nbsp;</td>
      <td><I>ACHTUNG: Wird der H&auml;ndlermodus aktiviert, muss sich jeder Kunde authentifizieren, bevor er/sie den Shop betreten kann. Ein Kunde muss vorher vom Shopadministrator registriert werden.</I></td>
    </tr>
    <tr>
      <td colspan="4">H&auml;ndlermodus Login Text: (Sie können hier ihre Begr&uuml;ssung f&uuml;r den Loginscreen angeben. HTML-Tags werden ausgewertet.)</td>
    </tr>
    <tr>
      <td colspan="4" style="font-family: Courier, Courier New, Monaco"><TEXTAREA NAME="Haendler_login_text" cols="100" rows="6" wrap="physical"><?php echo $Shopsettings[Haendler_login_text];?></TEXTAREA></td>
    </tr>
  </table>
  <script type="text/javascript">
  <!--
  // Zeigt folgenden Spalteninhalt an, wenn die Checkbox 'ArtikelSuchInkrement_onoff' angewaehlt wird
  // sonst, wird der Inhalt ausgeblendet:
  function erweiternArtInkr() {
      if (document.Formular.ArtikelSuchInkrement_onoff.checked == true) {
          document.getElementById("visible_td").style.visibility = "visible";
      }
      else {
          document.getElementById("visible_td").style.visibility = "hidden";
      }
  }// End function erweiternArtInkr
    // Wenn nicht Anzahl gleichzeitig angezeigter Artikel beschraenkt ist -> folgenden Spalteninhalt via JavaScript unsichtbar machen:
  if (document.Formular.ArtikelSuchInkrement_onoff.defaultChecked == false) {
      document.getElementById("visible_td").style.visibility = "hidden";
  }
  // Zeigt folgenden Spalteninhalt an, wenn die Checkbox 'tell_a_friend' angewaehlt wird
  // sonst, wird der Inhalt ausgeblendet:
  function erweiternTellAFriend() {
      if (document.Formular.tell_a_friend.checked == true) {
          document.getElementById("visible_td2").style.visibility = "visible";
      }
      else {
          document.getElementById("visible_td2").style.visibility = "hidden";
      }
  }// End function erweiternTellAFriend
    // Wenn Tell-A-Friend deaktiviert ist -> folgenden Spalteninhalt via JavaScript unsichtbar machen:
  if (document.Formular.tell_a_friend.defaultChecked == false) {
      document.getElementById("visible_td2").style.visibility = "hidden";
  }
  //-->
  </script>

  <hr>

  <table border="0" cellpadding="0" cellspacing="2">
    <tr>
      <td colspan="3"><b>Artikel bearbeiten</b></td>
    </tr>
    <tr>
      <td><nobr>Anzahl Optionsfelder:</nobr></td>
      <td><input type="text" value="<?php echo $Shopsettings[Opt_anz];?>" name="Opt_anz" size="3">&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td valign="top" rowspan="5"><i>Sie k&ouml;nnen hier einstellen, wie viele Options- und Variationsfelder mindestens angezeigt
      werden, wenn Sie einen neuen Artikel erstellen oder einen schon Vorhandenen bearbeiten. Ausserdem
      kann gew&auml;hlt werden, wie viele leere Felder eingeblendet werden, wenn ein Artikel schon
      mehr als die Mindestanzahl Variationen/Optionen hat. Geben Sie im Feld "Anzahl Variationsgruppen" ein, wie viele verschiedene
      Variationsgruppen (Farbe, L&auml;nge und Gr&ouml;sse sind zum Beispiel Variationsgruppen) Sie in Ihrem Shop verwenden wollen.
      </i></td>
    </tr>
    <tr>
      <td><nobr>Anzahl Variationsfelder:</nobr></td>
      <td><input type="text" value="<?php echo $Shopsettings[Var_anz];?>" name="Var_anz" size="3"></td>
    </tr>
    <tr>
      <td><nobr>leere Optionsfelder:</nobr></td>
      <td><input type="text" value="<?php echo $Shopsettings[Opt_inc];?>" name="Opt_inc" size="3"></td>
    </tr>
    <tr>
      <td><nobr>leere Variationsfelder:</nobr></td>
      <td><input type="text" value="<?php echo $Shopsettings[Var_inc];?>" name="Var_inc" size="3"></td>
    </tr>
    <tr>
      <td><nobr>Anzahl Variationsgruppen:</nobr></td>
      <td><input type="text" value="<?php echo $Shopsettings[Vargruppen_anz];?>" name="Vargruppen_anz" size="3"></td>
    </tr>
    <tr>
      <td><nobr>Anzahl Texteingabefelder:</nobr></td>
      <td><input type="text" value="<?php echo $Shopsettings[Eingabefelder_anz];?>" name="Eingabefelder_anz" size="3"></td>
      <td><i>Texteingabefelder erm&ouml;glichen dem Shopkunden die Eingabe von Zusatzinformationen bzw. Bemerkungen pro bestellten Artikel</i></td>
    </tr>
    <tr>
      <td><nobr>Gesamtpreis auf 0.05 runden:</nobr></td>
      <td><input type="checkbox" name="Gesamtpreis_runden" <?php if ($Shopsettings[Gesamtpreis_runden] == "Y") {echo "checked";} ?>></td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <hr>
  <table border="0" cellpadding="0" cellspacing="2">
    <tr>
      <td align="left"><b>Allgemeine Gesch&auml;ftsbedingungen</b></td>
    </tr>
    <tr>
      <td align="left">Sie können hier ihre Allgemeinen Geschäftsbedingungen einf&uuml;gen. HTML-Tags werden ber&uuml;cksichtigt und entsprechend ausgewertet ausgegeben.</td>
    </tr>
    <tr>
      <td style="font-family: Courier, Courier New, Monaco"><textarea name="AGB" cols="100" rows="12" wrap=physical><?php echo $Shopsettings[AGB];?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td valign=middle>
        <input type="hidden" name="darstellen" value=10 title="Weiche">
        <input type="hidden" name="anzahl_weitere_Zahlungsmethoden" value=<?php echo $Zahlung_weiter->zahlungsanzahl(); ?>>
        <input type=image src="../Buttons/bt_speichern_admin.gif" border="0" alt="Speichern">
        <a href="./Shop_Einstellungen_Menu_1.php" title="Abbrechen">
        <img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen, zum Hauptmenu"></a>
        <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Settings')" title="Hilfe">
        <img src="../Buttons/bt_hilfe_admin.gif" border="0" alt="Hilfe"></a>
      </td>
    </tr>
  </table>

  </form>
<?php
  } // End else
  echo "</body>\n";
  echo "</html>\n";
  // End of file ---------------------------------------------------------------------------------------
?>
