<?php
  // Filename: payment_interface.php
  //
  // Modul: Interfaces
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert das Payment Interface (Zahlungs-Schnittstelle)
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: payment_interface.php,v 1.13 2003/06/04 22:42:02 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere PhPepperShop Modul ueberpruefen kann ob dieses hier
  // schon "included" ist wird folgende Vairable auf true gesetzt.
  // (Name = Name des Moduls ohne .php)
  $payment_interface = true;
  // =======================================================================

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off
  // funktioniert, werden die Request Arrays $HTTP_GET_VARS und dann $HTTP_POST_VARS
  // in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($HTTP_GET_VARS);
  extract($HTTP_POST_VARS);


  // Erklaerungen
  // ============
  // - Das Payment Interface definiert ein Formular, welches einem externen Zahlungssystem
  //   ueber hidden-Fields die benoetigten Daten zur Verfuegung stellt. In der Variable
  //   $pay_here kann man angeben wo das externe Zahlungssystem unsere Daten uebernimmt.
  // - Wenn Sie dieses Interface benutzen wollen, so koennen Sie die im oberen Teil dieser
  //   Datei aufgelisteten Werte an ihr Zahlungssystem uebertragen, am Formular unten muessen
  //   sie nichts veraendern, es sei denn, sie benoetigen weitere Attribute aus dem
  //   Zahlungs-Objekt $Pay. Oder sie wollen, dass die uebergebenen Werte unter anderem Namen
  //   uebergeben werden.
  // - Die hier definierte Funktion payment_extern($Pay) mit einem Pay-Objekt als Argument
  //   wird vom Shop aus von hier aufgerufen: Modul: USER_BESTELLUNG_1.php , darstellen == 6
  // - Ist die Zahlung abgeschlossen, so soll vom externen Zahlunssystem auf die Seite
  //   $Referrer zurueckgesprungen werden. Im Flag $Erfolg kann man mit 1 Erfolg, mit 0 keinen
  //   Erfolg der externen Zahlung mitgeben. Im Fehlerfall kann man dem PhPepperShop noch in
  //   der Variable $Errormessage einen String mit einem genaueren Fehlerbeschrieb mitgeben
  // =======================================================================


  // Funktion payment_extern($Pay)
  // =============================
  // Diese Funktion liefert nicht im ueblichen Sinne mittels Return-Wert einen Rueckgabewert
  // zurueck, sondern gibt mittels echo Kommandos direkt auf das Webdokument ein Formular aus.
  // Dieses Fomular beinhaltet wie oben erwaehnt hidden-fields, welche die Daten zur Zahlung
  // beinhalten. Will man weitere Daten an das externe Zahlungssystem senden, so kann man
  // diese aus dem uebergebenen Pay-Objekt herausholen und in einem weiteren hidden-field
  // dem externen Zahlungssystem mit uebergeben. Damit man versteht, was alles in einem
  // Pay-Objekt vorhanden ist, konsultieren Sie bitte folgende Klassendefinitionen:
  //    ->  pay_def.php
  //    ->  kunde_def.php (enthaelt nur benutzte Attribute, siehe 'Kundenattribute bearbeiten')
  //    ->  bestellung_def.php --> artikel_def.php --> kategorie_def.php
  // Diese Funktion besteht im wesentlichen aus vier Teilen:
  //    1.) Definition des URLs des externen Zahlungssystems. Auf diese Adresse wird
  //        gesprungen, wenn der Kunde die allgemeinen Geschaeftsbedingungen akzeptiert
  //    2.) Auspacken der benoetigten Variablen aus dem uebergebenen Pay-Objekt
  //    3.) Abfuellen der zu uebergebenen Werte in ihre hidden-fields
  //    4.) Rueckgabewert auf true setzen
  // Argumente: Pay-Objekt
  // Rueckgabewert: true bei Erfolg, sonst Abbruch mit der PHP 'die(String)'-Funktion
  function payment_extern($myPay) {
      // Teil 1: Definition des URLs des externen Zahlungssystems
      // -------------------------------------------------------------------
      // In der Variable $pay_here wird ein String von max. 255 Zeichen uebergeben. Dieser String
      // ist der URL des anzusteuernden externen Zahlungssystems. Beispielsweise:
      // http://www.saferpay.com/OpenSaferpayScript.asp wenn man SaferPay benutzen will.
      // $pay_here = "http://hier_kommt_die_URL_des_externen_Zahlungssystems_hin";
      /* TEST */ $pay_here = "./pay_ext_test.php";

      // Teil 2: Auspacken der benoetigten Attribute aus dem Pay-Objects
      // -------------------------------------------------------------------
      // $myPay ist ein Objekt der Klasse Pay (siehe pay_def.php). Wir nehmen
      // nun die Daten aus dem Objekt, welche wir dem externen Zahlungssystem uebermitteln
      // muessen. Diese Variablen werden dann im dritten Teil einem hidden-Field zugewiesen
      // weleches dem externen Zahlungssystem unter dem Namen welcher im hidden-Field Attribut
      // 'name=' zugewiesen wird.

      // Der URL wohin das externe Zahlungssystem den Kunden wieder zurueck zum PhPepperShop
      // fuehrt
      $Referrer                 = $myPay->myReferrer;

      // Ausfuehrliche Kundendaten
      $Anrede                   = $myPay->myKunde->Anrede;
      $Vorname                  = $myPay->myKunde->Vorname;
      $Nachname                 = $myPay->myKunde->Nachname;
      $Firma                    = $myPay->myKunde->Firma;
      $Abteilung                = $myPay->myKunde->Abteilung;
      $Strasse                  = $myPay->myKunde->Strasse;
      $Postfach                 = $myPay->myKunde->Postfach;
      $PLZ                      = $myPay->myKunde->PLZ;
      $Ort                      = $myPay->myKunde->Ort;
      $Land                     = $myPay->myKunde->Land;
      $Tel                      = $myPay->myKunde->Tel;
      $Fax                      = $myPay->myKunde->Fax;
      $Email                    = $myPay->myKunde->Email;

      // Daten der zu uebermittelnden Bestellung
      $Datum                    = $myPay->myBestellung->Datum;     //Format: yyyy-mm-dd
      $Kreditkarten_Hersteller  = $myPay->myBestellung->Kreditkarten_Hersteller;
      $Kreditkarten_Nummer      = $myPay->myBestellung->Kreditkarten_Nummer;
      $Kreditkarten_Ablaufdatum = $myPay->myBestellung->Kreditkarten_Ablaufdatum;
      $Kreditkarten_Vorname     = $myPay->myBestellung->Kreditkarten_Vorname;
      $Kreditkarten_Nachname    = $myPay->myBestellung->Kreditkarten_Nachname;
      $Rechnungsbetrag          = $myPay->myBestellung->Rechnungsbetrag;
      $Bemerkungen              = $myPay->myBestellung->Anmerkung;
      $Bestellungs_ID           = $myPay->myBestellung->Bestellungs_ID;
      $Referenz_Nr              = bestellungs_id_to_ref($myPay->myBestellung->Bestellungs_ID);

      // Selbstdefinierbare Kundenattribute koennen entweder an die Bestellung
      // oder an den Kunden gebunden sein, deshalb wird hier entschieden von
      // welchem Objekt diese Attribute ausgelesen werden. Hier sollte man nichts
      // weiter aendern muessen. Attribut1-4 beinhalten die Namen der Attribute,
      // in den Variablen Attributwert1-4 sind hingegen deren Werte gespeichert.
      if ($myPay->myKunde->Attribut1 == "") {                   //Attribut(-wert) 1
          $Attribut1 = $myPay->myBestellung->Attribut1;
          $Attributwert1 = $myPay->myBestellung->Attributwert1;
      }
      else {
          $Attribut1 = $myPay->myKunde->Attribut1;
          $Attributwert1 = $myPay->myKunde->Attributwert1;
      }
      if ($myPay->myKunde->Attribut2 == "") {                    //Attribut(-wert) 2
          $Attribut2 = $myPay->myBestellung->Attribut2;
          $Attributwert2 = $myPay->myBestellung->Attributwert2;
      }
      else {
          $Attribut2 = $myPay->myKunde->Attribut2;
          $Attributwert2 = $myPay->myKunde->Attributwert2;
      }
      if ($myPay->myKunde->Attribut3 == "") {                    //Attribut(-wert) 3
          $Attribut3 = $myPay->myBestellung->Attribut3;
          $Attributwert3 = $myPay->myBestellung->Attributwert3;
      }
      else {
          $Attribut3 = $myPay->myKunde->Attribut3;
          $Attributwert3 = $myPay->myKunde->Attributwert3;
      }
      if ($myPay->myKunde->Attribut4 == "") {                    //Attribut(-wert) 4
          $Attribut4 = $myPay->myBestellung->Attribut4;
          $Attributwert4 = $myPay->myBestellung->Attributwert4;
      }
      else {
          $Attribut4 = $myPay->myKunde->Attribut4;
          $Attributwert4 = $myPay->myKunde->Attributwert4;
      }

      // Zufallszahl generieren (hilft beim Wiedereinstieg Man-in-the-middle Attacken
      // zu erkennen) - wird aber defaultmaessig noch nicht benutzt, aber bereitgestellt
      srand((double)microtime()*1000000); // Ist nur bei PHP Versionen < 4.2.2 noetig
      $zufallszahl = md5(uniqid(rand()));

      // Teil 3: Abfuellen der Attribute in zu uebergebene hidden-Fields
      // -------------------------------------------------------------------
      // Formularstart und hidden-Fields: Wenn Sie im Teil 2 weitere Attribute
      // aus dem Pay-Objekt auslesen, muessen sie zu jedem ausgelesenen Attribut hier
      // im Teil 3 noch ein entsprechendes hidden-Field definieren. Im hidden-Field
      // (HTML-Tag <input>) kann man dem Attribut 'name=' den Namen angeben, unter
      // welchem die Variable beim externen Zahlungssystem angesprochen wird.

      // Kundendaten in hidden-Fields abfuellen
      echo '<form action="'.$pay_here.'" method="POST" name="Formular" onSubmit="return chkFormular()">'."\n";
      echo '<input type="hidden" name="Anrede" value="'.$Anrede.'">'."\n";
      echo '<input type="hidden" name="Vorname" value="'.$Vorname.'">'."\n";
      echo '<input type="hidden" name="Nachname" value="'.$Nachname.'">'."\n";
      echo '<input type="hidden" name="Firma" value="'.$Firma.'">'."\n";
      echo '<input type="hidden" name="Abteilung" value="'.$Abteilung.'">'."\n";
      echo '<input type="hidden" name="Strasse" value="'.$Strasse.'">'."\n";
      echo '<input type="hidden" name="Postfach" value="'.$Postfach.'">'."\n";
      echo '<input type="hidden" name="PLZ" value="'.$PLZ.'">'."\n";
      echo '<input type="hidden" name="Ort" value="'.$Ort.'">'."\n";
      echo '<input type="hidden" name="Land" value="'.$Land.'">'."\n";
      echo '<input type="hidden" name="Tel" value="'.$Tel.'">'."\n";
      echo '<input type="hidden" name="Fax" value="'.$Fax.'">'."\n";
      echo '<input type="hidden" name="Email" value="'.$Email.'">'."\n";

      // Bestellungsdaten in hidden-Fields abfuellen
      echo '<input type="hidden" name="Datum" value="'.$Datum.'">';
      echo '<input type="hidden" name="Bemerkungen" value="'.$Bemerkungen.'">';
      echo '<input type="hidden" name="Rechnungsbetrag" value="'.$Rechnungsbetrag.'">';
      echo '<input type="hidden" name="Kreditkarten_Hersteller" value="'.$Kreditkarten_Hersteller.'">'."\n";
      echo '<input type="hidden" name="Kreditkarten_Nummer" value="'.$Kreditkarten_Nummer.'">'."\n";
      echo '<input type="hidden" name="Kreditkarten_Ablaufdatum" value="'.$Kreditkarten_Ablaufdatum.'">'."\n";
      echo '<input type="hidden" name="Kreditkarten_Vorname" value="'.$Kreditkarten_Vorname.'">'."\n";
      echo '<input type="hidden" name="Kreditkarten_Nachname" value="'.$Kreditkarten_Nachname.'">'."\n";

      // Selbstdefinierbare Kundenattribute in hidden-Fields abfuellen
      echo '<input type="hidden" name="'.$Attribut1.'" value="'.$Attributwert1.'">'."\n";
      echo '<input type="hidden" name="'.$Attribut2.'" value="'.$Attributwert2.'">'."\n";
      echo '<input type="hidden" name="'.$Attribut3.'" value="'.$Attributwert3.'">'."\n";
      echo '<input type="hidden" name="'.$Attribut4.'" value="'.$Attributwert4.'">'."\n";

      // Uebertragung der Zufallszahl (sollte mit einem getrennten Aufruf abgegeben werden)
      echo '<input type="hidden" name="zufallszahl" value="'.$zufallszahl.'">'."\n";

      // Hierhin soll das externe Zahlungssystem nach der Zahlung zurueckspringen
      echo '<input type="hidden" name="Referrer" value="'.$Referrer.'">'."\n";

      // Teil 4: Rueckgabewert auf true setzen und Funktion beenden
      // -------------------------------------------------------------------
      return true;
  }// End function payment_extern($Pay)

  // End of file------------------------------------------------------------
?>
