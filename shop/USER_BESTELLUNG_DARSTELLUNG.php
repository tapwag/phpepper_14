<?php
  // Filename: USER_BESTELLUNG_DARSTELLUNG.php
  //
  // Modul: Darstellungs-Module - Darstellung des Warenkorbs (HTML und E-Mail)
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Stellt ein Formular bereit (HTML-/ Text-Schnittstelle)
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: USER_BESTELLUNG_DARSTELLUNG.php,v 1.67 2003/08/18 10:02:39 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_BESTELLUNG_DARSTELLUNG = true;


  // -----------------------------------------------------------------------
  // Weitere Konfigurationsschritte vornehmen
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($session_mgmt)) {include("session_mgmt.php");}
  if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

// -----------------------------------------------------------------------------------------------
// Diese Funktion stellt die im Argument gelieferte Bestellung dar und leitet die Daten dann
// zur Uebertragung (oder sagen wir mal, weiteren Verarbeitung) in die Datenbank weiter.
// Ist der uebergebene loeschen-Wert true, dann wird die Moeglichkeit angezeigt, Artikel aus dem
// Warenkorb zu entfernen (ist bei der Darstellung des Warenkorbes zusammen mit den Adressdaten
// der Bestellung im Adminbereich im Moment nicht gewuenscht!)
// Ist das Flag Admin = true, so werden u.a. fuer die Buttons andere Pfade benutzt.
// Mit dem impliziten Aufruf von berechneVersandkosten(...) und setExklMwSt(...) wird die Bestellung
// in der Datenbank upgedated.
// ***
// Damit die Bestellung auch fuer immer statisch bleibt und die Preise nicht jedes Mal neu berechnet
// werden, wenn man im Bestellungsmanagement die Bestellungen editiert, so muss noch ein weiteres
// Flag eingebaut werden, welches die Funktion berechneversandkosten ausklammert
// ***
// Argumente: Bestellung-Objekt, Loeschen-Flag (boolean), Admin-Flag (boolean)
// Rueckgabewert: Kein Rueckgabewert (HTML-Funktion) oder Abbruch per die-Funktion
// -----------------------------------------------------------------------------------------------
function darstellenBestellung($myBestellung,$loeschen, $Admin) {

  // Zuerst wird die Bestellung upgedated, wir berechnen die
  // Versandkosten, Nachnahmegebuehr, den allfaelligen Mindermengenzuschlag und das Rechnungstotal
  // Hier gibt es eine Entscheidung, sobald das Admin-Flag gesetzt ist, kann man nur noch anschauen
  // Die Versandkosten werden nicht mehr neu berechnet, sondern nur noch ausgegeben
  if ($Admin) {
      // Admin
      $Versandkosten = $myBestellung->Versandkosten;
      $Mindermengenzuschlag = $myBestellung->Mindermengenzuschlag;
      if ($myBestellung->Bezahlungsart == "Nachnahme") {
          $Nachnahmebetrag = getNachnahmebetrag();
      }
      $Rechnungstotal = $myBestellung->Rechnungsbetrag;
  }
  else {
      // Kunde
      $Versandkostenarray = berechneversandkosten($myBestellung->Session_ID);
      $Versandkosten = $Versandkostenarray[0];
      $Mindermengenzuschlag = $Versandkostenarray[1];
      $Rechnungstotal = $Versandkostenarray[2];
      $Nachnahmebetrag = $Versandkostenarray[3];
      // Update der Ablaufzeit der Kundensession
      extend_Session(session_id());
  }

  // Damit wir unten den Text fuer den Versandkosten Rechnungsposten ausgeben koennen
  // muessen wir zuerst die personalisierte Version von der Datenbank laden:
  $Setting_Nr = 1; // Vorlaeufig noch statische $Setting_Nr :-(
  $meineVersandkosten = getversandkostensettings($Setting_Nr);
  $Versandkostentext = $meineVersandkosten->Pauschale_text;
  // Aufbereitung der Daten um sie im Formular darstellen zu koennen:
  $Bestellungs_ID = $myBestellung->Bestellungs_ID;
  $Session_ID = $myBestellung->Session_ID;
  $Name = $myBestellung->Name;
  $Vorname = $myBestellung->Vorname;
  $Adresse1 = $myBestellung->Adresse1;
  $Adresse2 = $myBestellung->Adresse2;
  $PLZ = $myBestellung->PLZ;
  $Ort = $myBestellung->Ort;
  $Email = $myBestellung->Email;
  $Datum = $myBestellung->Datum;
  $Endpreis = $myBestellung->Endpreis;
  $Anmerkung = $myBestellung->Anmerkung;
  $Bezahlungsart = $myBestellung->Bezahlungsart;
  $Bestellung_abgeschlossen = $myBestellung->Bestellung_abgeschlossen;
  // Auslesen der MwSt-Settings um feststellen zu koennen ob die Artikelpreise inkl. oder exkl. MwSt sind
  $aktuelle_mwst_settings = getmwstsettings();

  // Die Artikel der Bestellung ($value entspricht einem Artikel_info-Objekt)
  // werden weiter unten direkt im HTML-Teil dynamisch aus dem Artikel_info-
  // Objekt gelesen und dargestellt

  // Tabellenheader (erste Zeile mit den Spaltenbezeichnungen) ausgeben
  // Waehrungsstring aus Datenbank holen (z.B SFr. oder €)
  $waehrung = getWaehrung();
  ?>
  <table class="content" border="0" cellpadding="0" cellspacing="2">
    <tr class="content" valign="middle" align="center">
      <td class="content" align=center><B style='font-weight:bold'>Anzahl</B></td>
      <td class="content" align=center><B style='font-weight:bold'>Artikel Name&nbsp;&nbsp;&nbsp;</B></td>
      <td></td>
      <td class="content" align=center><B style='font-weight:bold'>Variationen / Optionen&nbsp;&nbsp;&nbsp;</B></td>
      <td class="content" align=center><B style='font-weight:bold'>Einzelpreis&nbsp;&nbsp;&nbsp;</B></td>
      <td class="content" align=center><B style='font-weight:bold'>Preis in <?php echo $waehrung; ?></B></td>
      <td class="content"></td>
    </tr>
    <tr class="content">
      <td class="content" colspan=6><hr></td>
    </tr>
  <?php
  // Assoziativer Array, welcher als Index die MwSt-Saetze hat und als Werte deren aufsummierte Totale
  $MwStarray = array();
  // Zaehlvariable fuer die Anzahl Artikel im Warenkorb
  $counter = 1;
  // Variable, die den Gesamtpreis der Bestellung beinhaltet
  $gesamtpreis = 0;
  // Mit jedem Artikel in der Bestellung..
  foreach(($myBestellung->getallartikel()) as $keyname => $value) {
    // MwSt-Satz des Artikels auslesen
    $ArtikelMwSt_Satz = getmwstofArtikel($value->Artikel_ID);
    // Einzelpreis auslesen und Variationsaufpreis auf Einzelpreis aufaddieren
    $einzelpreis = $value->Preis;
    echo '<tr class="content" valign="top" align="center">';
    echo "<td class='content' align=right>".$value->Anzahl."&nbsp;&nbsp;&nbsp;</td>";
    echo "<td class='content' align=left>".$value->Name;
    if ($Admin && ($value->Artikel_Nr != "")) {
      echo " (".$value->Artikel_Nr.") ";
    }
    echo "&nbsp;&nbsp;&nbsp;</td>";
    echo "<td></td><td class='content' align=left>";
    foreach(($value->getallvariationen()) as $key_var => $val_var) {
        echo "- ";
        /* Falls man lieber die Namen der entsprechenden Variationsgruppe angezeigt haben moechte...
        $var_grp_name = get_var_grp_name($key_var, $value->Artikel_ID);
        if ($var_grp_name == "") {
            echo "- ";
        }
        else {
            echo $var_grp_name.": ";
        }
        */
        echo $key_var."&nbsp;&nbsp;&nbsp;<BR>";
        $einzelpreis = $einzelpreis + $val_var;
    } // end of foreach

    // jede Option eines Artikels in der Bestellung..
    foreach(($value->getalloptionen()) as $key => $val) {
      echo "- ".$key."&nbsp;&nbsp;&nbsp;<BR>";
      // Optionssaufpreis auf Einzelpreis aufaddieren
      $einzelpreis = $einzelpreis + $val;
    } // end of foreach

    if (count($value->Zusatzfelder) > 1 || ($value->Zusatzfelder[0] != "")){
        foreach($value->Zusatzfelder as $Zusatzfeld){
            // Zwichen den Optionen/Variationen und den Zusatzinformationen eine Leerzeile ausgeben
            if ($Zusatzfeld != ""){
                echo "- ".htmlentities(stripslashes($Zusatzfeld))."&nbsp;&nbsp;&nbsp;<BR>";
            } // end of if
        } // end of foreach
    } // end of if

    echo "&nbsp;&nbsp;&nbsp;</td>";
    // OLD echo "<td class='content' align='right'>".$waehrung." ";
    echo "<td class='content' align='right'>";
    // Einzelpreis des Artikels formatiert (2 Stellen nach dem Komma) ausgeben
    echo getZahlenformat($einzelpreis);
    echo "&nbsp;&nbsp;&nbsp;</td>";
    // Einzelgesamtpreis berechnen (Artikelpreis inkl. Variation und Optionen * Anzahl)
    $einzelgesamtpreis = $einzelpreis * $value->Anzahl;
    // MwStarray updaten (Geldbetrag, welcher unter des selben MwSt-Satzes liegt um Artikelpreis erhoehen):
    // Anm. Optionen und Variationen des Artikels werden mit dem gleichen MwSt-Satz des Artikels abgerechnet!
    $MwStarray[$ArtikelMwSt_Satz] = $MwStarray[$ArtikelMwSt_Satz] + $einzelgesamtpreis;
    // OLD echo "<td class='content' align='right'>".$waehrung." ";
    echo "<td class='content' align='right'>";
    // Einzelgesamtpreis des Artikels formatiert (2 Stellen nach dem Komma) ausgeben
    echo getZahlenformat($einzelgesamtpreis);
    echo "</td>";
    echo "<td class='content' align=left>";

    // Fuer den Loeschen-Button, muessen wir noch die Variation und Optionen in das Format
    // konvertieren, welches dafuer in der artikel_bestellung-Tabelle verwendet wird:
    $varstring = ""; //Initialisierung
    foreach(($value->getallvariationen()) as $key => $val) {
        if (empty($varstring)){
            $varstring = $key."þ".$val;
        }
        else {
            $varstring.= "þ".$key."þ".$val;
        }
    }

    // Optionen abfuellen (Wenn erstes Mal, kein einleitendes Delimiter Zeichen)
    // (Delimiter-Zeichen = Alt + 0254)
    $optstring = ""; //Muss als leer initialisiert werden (fuer jeden Artikel neu!)
    foreach(($value->getalloptionen()) as $key => $val) {
        if (empty($optstring)){
            $optstring = $key."þ".$val;
        }
        else {
            $optstring = $optstring."þ".$key."þ".$val;
        }
    }

    // Zusatzfelder-String erstellen
    $ZusatzString = spezial_string($value->Zusatzfelder);

    // Moeglichkeit zum Entfernen eines Artikels nur im Warenkorbmodus anzeigen
    if ($loeschen == true){
        if ($Admin == true) {
            // Vom Admin bereich aufgerufen, erfordert andere Pfade
            echo "&nbsp;<a class='content' href='SHOP_BESTELLUNG.php?darstellen=11&Referenz_Nr=".bestellungs_id_to_ref($Bestellungs_ID)."&FK_Artikel_ID=".$value->Artikel_ID."&FK_Bestellungs_ID=".$Bestellungs_ID."&Variation=".urlencode($varstring)."&Optionen=".urlencode($optstring)."&Zusatztexte=".urlencode($ZusatzString)."'>";
            echo '<img src="../Buttons/bt_loeschen.gif" border="0"></a>';
        }
        else {
            // Funktion wurde vom USER-Bereich aus aufgerufen
            echo "&nbsp;<a class='content' href='USER_BESTELLUNG_AUFRUF.php?darstellen=3&amp;".session_name()."=".session_id()."&FK_Artikel_ID=".$value->Artikel_ID."&FK_Bestellungs_ID=".$Bestellungs_ID."&Variation=".urlencode($varstring)."&Optionen=".urlencode($optstring)."&Zusatztexte=".urlencode($ZusatzString)."'>";
            echo '<img src="Buttons/bt_loeschen.gif" border="0"></a>';
        }
    }
    echo "</td></tr>";
    // Gesamtpreis der Bestellung aufsummieren
    $gesamtpreis = $gesamtpreis + $einzelgesamtpreis;
    // Artikelzaehler erhoehen
    $counter++;
  }// End Artikelkostenberechnung
  $anzahlartikel = $counter-1;
  echo "<tr class='content' valign=top>\n";
  echo "<td class='content' colspan=6><hr></td></tr>\n";

  // Auslesen des Standard MwSt-Satzes und des MwSt-Satzes fuer Porto und Verpackung (Versandkosten, Nachnahmegebuehren, Mindermengenzuschlaege)
  $DefaultMwSt_Satz = getstandardmwstsatz();
  $Porto_Verpackung_Satz = getportoverpackungmwstsatz();

  if (($Versandkosten > 0.0) && ($gesamtpreis > 0.0)) {
      echo "<tr class='content' valign=top>\n";
      echo "  <td class='content' colspan=5 align=right>\n";
      echo "    $Versandkostentext:\n";
      echo "  </td><td class='content' align='right'>\n";
      echo "    ".$waehrung." ";
      echo getZahlenformat($Versandkosten); // Versandkosten der Bestellung formatiert (2 Stellen nach dem Komma) ausgeben
      echo "</td>\n";
      echo "</tr>\n";
      // Versand- und Verpackungskosten kommen auch in die Mehrwertsteuer rein
      // Es gibt eine spezielle MwSt-Abrechnung fuer Porto und Verpackung

      switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Versandkosten zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Versandkosten;
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Versandkosten * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Versandkosten;
      }// End switch
  }// End Versandkostenberechnung

  if (($Nachnahmebetrag > 0.0) && ($gesamtpreis > 0.0)) {
      echo "<tr class='content' valign=top>\n";
      echo "  <td class='content' colspan=5 align=right>\n";
      echo "    Nachnahmegeb&uuml;hr:\n";
      echo "  </td><td class='content' align='right'>\n";
      echo "    ".$waehrung." ";
      echo getZahlenformat($Nachnahmebetrag); // Nachnahmegebuehr formatiert (2 Stellen nach dem Komma) ausgeben
      echo "</td>\n";
      echo "</tr>\n";
      // Auf den Nachnahmebetrag wird auch MwSt draufgeschlagen. In der Schweiz gibt es noch eine Limite bei Paketgewichten...
      switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Nachnahmegebuehr zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Nachnahmebetrag;
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Nachnahmebetrag * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Nachnahmebetrag;
      }// End switch
  }// End Nachnahmekostenberechnung

  if (($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) {
      echo "<tr class='content' valign=top>\n";
      echo "  <td class='content' colspan=5 align=right>\n";
      echo "    Mindermengenzuschlag (bis $waehrung ";
      echo getZahlenformat($meineVersandkosten->Mindermengenzuschlag_bis_Preis);
      echo "):\n";
      echo "  </td><td class='content' align='right'>\n";
      echo "    ".$waehrung." ";
      echo getZahlenformat($Mindermengenzuschlag); // Mindermengenzuschlag formatiert (2 Stellen nach dem Komma) ausgeben
      echo "</td>\n";
      echo "</tr>\n";
      // MwStarray updaten (auch Mindermengenzuschlag wird nach Porto und Verpackungsart versteuert)
      switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Mindermengenzuschlag zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Mindermengenzuschlag;
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Mindermengenzuschlag * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Mindermengenzuschlag;
      }// End switch
  }// End Mindermengenzuschlagberechnung

  // Wenn es sich um die gebuehrenpflichtige Zahlung ueber einen Treuhandservice handelt, diese Gebuehr
  // (abhaengig vom Bestellwert) berechnen und MwSt-versteuern (Porto und Versandkosten-Abrechnungsart)
  if (($Bezahlungsart == "Treuhandzahlung") && ($gesamtpreis > 0.0)) {

      // Mehrwertsteuerbetrag ausrechnen --> Damit Bestellsumme inkl. MwSt berechnet werden kann
      // die gleiche Berechnung findet unten ncohmals statt (inkl. Versandkosten,...)
      // Fuer jeden im Warenkorb vorkommenden MwSt-Satz Anzahl ausweisen und ein Total praesentieren
      $mwst_total = 0.0; //In dieser Variable werden die errechneten MwSt-Betraege aufsummiert
      if ($meineVersandkosten->MwStpflichtig == "Y" && $gesamtpreis > 0) {
          foreach ($MwStarray as $MWST_Satz => $Betrag) {
              // Ausgabe nur machen, wenn MwSt-Satz groesser als 0% ist.
              if ($MWST_Satz > 0.0) {
                  // Wenn MwSt-inkl. wird MwSt anders berechnet als wenn die Artikelpreise exkl. MwSt. sind:
                  if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "Y") {
                      // Artikelpreise sind schon inkl. MwSt, folgende Formel verwenden:
                      $mwst_anteil = ($MWST_Satz / (100 + $MWST_Satz)) * $Betrag;
                  }
                  else {
                      // Artikelpreise sind noch exkl. MwSt. Folgende MwSt-Berechnung anwenden:
                      $mwst_anteil = $Betrag * ($MWST_Satz/100);
                  }
                  $mwst_total = $mwst_total + $mwst_anteil; //MwSt Anteil aufsummieren
              }
          }// End foreach
      } // End if

      // $Treuhandzahlungskosten ist ein Array mit zwei Elementen. Wert 0 beinhaltet die Treuhaenderkosten
      // welche dem Kunden verrechnet werden (inkl. MwSt). Im Element 2 befindet sich der Kundenanteil, mit
      // welchem dieser Betrag errechnet wurde.
      $Treuhandzahlungkosten = getTreuhandbetrag(($gesamtpreis + $mwst_total));
      echo "<tr class='content' valign=top>\n";
      echo "  <td class='content' colspan=5 align=right>\n";
      if ($Treuhandzahlungkosten[1] == 0) {
          echo "    Die Treuhandservicekosten werden von uns &uuml;bernommen:\n";
          echo "  </td><td class='content' align='right'>\n";
          echo "    ".$waehrung." ";
          echo getZahlenformat(0); // Betrag, welcher Kunden die Treuhandzahlung kostet (hier natuerlich 0.00)
          echo "</td>\n";
          echo "</tr>\n";
      }
      else {
        echo "    Treuhandservicekosten:";
        echo "  </td><td class='content' align='right'>\n";
        echo "    ".$waehrung." ";
        echo getZahlenformat($Treuhandzahlungkosten[0]); // Betrag, welcher Kunden die Treuhandzahlung kostet
        echo "</td>\n";
        echo "</tr>\n";
        // Rechnungstotal beinhaltet noch keine Treuhandkosten, weil diese nicht im Bestellungsobjekt gespeichert werden, update:
        $Rechnungstotal = $Rechnungstotal + $Treuhandzahlungkosten[0];
        $gesamtpreis = $gesamtpreis + $Treuhandzahlungkosten[0];
        // MwStarray updaten (auch Treuhandkosten wird nach Porto und Verpackungsart versteuert)
        switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Treuhandkosten zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Treuhandzahlungkosten[0];
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Treuhandzahlungkosten[0] * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Treuhandzahlungkosten[0];
        }// End switch
      }// End else $Treuhandkosten
  }// End Treuhandservicekostenberechnung

  // MwSt:
  // Weitere Ausgaben:
  echo "<tr class='content' valign=top>\n";
  echo "  <td class='content' colspan=5 align=right>\n";
  if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || (($Versandkosten > 0.0)  && ($gesamtpreis > 0.0))) {echo "<BR>";}
  // Auswertung ob Gesamtpreis gerundet werden soll oder nicht und Hineinrechnen der MwSt-Totale
  if (getgesamtpreisrunden()) {
      $Rechnungstotal = runden_05($Rechnungstotal); // Gerundetes Rechnungstotal
      $gesamtpreis = runden_05($gesamtpreis);       // Gerundeter Gesamtpreis ohne Versandkosten und Mindermengenzuschlag
  }
  if ($meineVersandkosten->MwStpflichtig == "Y") {
      // Unterscheidung ob Artikelpreise schon inkl. oder noch exkl. MwSt eingegeben wurden
      if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "Y") {
          $mwst_vor_formatting = "    <b style='font-weight:bold'>"; // Wird weiter unten nochmals verwendet (MwSt inkl. --> Fett, sonst nicht)
          $mwst_nach_formatting = "    </b>";
          echo $mwst_vor_formatting."Gesamtpreis (inkl. MwSt)";
          echo ":".$mwst_nach_formatting."\n";
      }
      else {
          $mwst_vor_formatting = "";
          $mwst_nach_formatting = "";
          echo $mwst_vor_formatting."Artikel-Total (exkl. MwSt)";
          echo ":".$mwst_nach_formatting."\n";
      }
  }
  else {
      // Ausgabe des Gesamtpreises ohne MwSt-Angabe
      echo "    <b style='font-weight:bold'>Gesamtpreis";
      echo ":</b>\n";
  }
  echo "  </td><td class='content' align='right'>\n";
  if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || (($Versandkosten > 0.0)  && ($gesamtpreis > 0.0))) {echo "<BR>";}
  echo $mwst_vor_formatting.$waehrung." ";

  if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || ($gesamtpreis > 0.0)) {
      echo getZahlenformat($Rechnungstotal);// Gesamtpreis der Bestellung formatiert (2 Stellen nach dem Komma) ausgeben
  }
  else {
      echo getZahlenformat($gesamtpreis);// Gesamtpreis der Bestellung OHNE Versandkosten und Mindermengenzuschlag
  }
  echo $mwst_nach_formatting."</td>\n";
  echo "</tr>\n";

  // Mehrwertsteuerbetrag ausrechnen und anzeigen und in Datenbank schreiben
  if ($meineVersandkosten->MwStpflichtig == "Y" && $gesamtpreis > 0) {
      // Der Shop ist also MwSt-pflichtig und der Bestellungsbetrag ist groesser als Null...
      // Fuer jeden im Warenkorb vorkommenden MwSt-Satz Anzahl ausweisen und ein Total praesentieren
      $mwst_total = 0.0; //In dieser Variable werden die errechneten MwSt-Betraege aufsummiert
      foreach ($MwStarray as $MWST_Satz => $Betrag) {
          // Ausgabe nur machen, wenn MwSt-Satz groesser als 0% ist.
          if ($MWST_Satz > 0.0) {
              echo "<tr class='content' valign=top>\n";
              echo "  <td class='content' colspan=5 align=right>\n";
              echo "    MwSt-Anteil (".$MWST_Satz."%): ";
              echo "\n";
              echo "  </td><td class='content' align='right'>\n";
              if (count($MwStarray) == 1) {echo "    ".$waehrung." ";} // Wenn es mehrere MwSt-Saetze gibt, das Waehrungssymbol ausblenden
              // Wenn MwSt-inkl. wird MwSt anders berechnet als wenn die Artikelpreise exkl. MwSt. sind:
              if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "Y") {
                  // Artikelpreise sind schon inkl. MwSt, folgende Formel verwenden:
                  $mwst_anteil = ($MWST_Satz / (100 + $MWST_Satz)) * $Betrag;
              }
              else {
                  // Artikelpreise sind noch exkl. MwSt. Folgende MwSt-Berechnung anwenden:
                  $mwst_anteil = $Betrag * ($MWST_Satz/100);
              }
              $mwst_total = $mwst_total + $mwst_anteil; //MwSt Anteil aufsummieren
              echo getZahlenformat($mwst_anteil);// MwSt. Anteil formatiert (2 Stellen nach dem Komma) ausgeben
              echo "<br>\n";
              echo "</td>\n";
              echo "</tr>\n";
          }
      }// End foreach
      // Das MwSt-Total anzeigen, wenn mehrere MwSt-Saetze vorkommen (wenn aber nur zwei Saetze: 0% + x% drin sind, kein Total anzeigen)
      if (count($MwStarray) > 1) {
          $zerovalue = false; // Flag wird true, wenn es zwei MwSt-Saetze gibt und einer davon 0% ist (dann Total NICHT darstellen)
          if (count($MwStarray) == 2) {
              foreach($MwStarray as $keyvar=>$valuevar) {
                  if ($keyvar == 0) {
                      $zerovalue = true;
                  }
              }
          }// End if count(MwStarray) == 2
          if ($zerovalue == false) {
              echo "  <td class='content' colspan=5 align=right>\n";
              echo "    <i>MwSt-Total: ";
              echo "\n";
              echo "  </td><td class='content' align='right'>\n";
              echo "    ".$waehrung." ";
              echo getZahlenformat($mwst_total);// MwSt. Anteil formatiert (2 Stellen nach dem Komma) ausgeben
              echo "</i><br>\n";
              echo "</td>\n";
              echo "</tr>\n";
          }
      }
      // Jetzt wird noch der MwSt-Betrag in die Datenbank geschrieben (ins Attribut MwSt). Je nach Rundungsangabe
      // muss noch gerundet werden, weil bei Preisen exkl. MwSt im Endeffekt die MwSt zum Bestellungstotal dazu-
      // addiert wird.
      if (getgesamtpreisrunden()) {
          $mwst_betrag = runden_05($mwst_total);
      }
      else {
         $mwst_betrag = $mwst_total;
      }
      setExklMwSt($mwst_betrag, $Bestellungs_ID);
  } // End if (MwSt-Berechnung)
  else {
      // Wenn der Shop NICHT Mehrwertsteuerpflichtig ist, so soll das MwSt-Tabellenattribut = 0.0 gesetzt werden.
      setExklMwSt(0.0, $Bestellungs_ID);
  }

  // Wenn der Shop MwSt-pflichtig ist UND die Artikelpreise exkl. MwSt angegeben wurden, so wird hier das Gesamttotal
  // inkl. dazuaddierter MwSt ausgegeben.
  if ($meineVersandkosten->MwStpflichtig == "Y") {
      // Unterscheidung ob Artikelpreise schon inkl. oder noch exkl. MwSt eingegeben wurden
      if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "N") {
          // Auswertung ob Gesamtpreis gerundet werden soll oder nicht und Hineinrechnen der MwSt-Totale
          if (getgesamtpreisrunden()) {
              $Rechnungstotal = runden_05($Rechnungstotal + $mwst_total); // Gerundetes Rechnungstotal
              $gesamtpreis = runden_05($gesamtpreis + $mwst_total);       // Gerundeter Gesamtpreis ohne Versandkosten und Mindermengenzuschlag
          }
          else {
              $Rechnungstotal = $Rechnungstotal + $mwst_total; // Nicht gerundetes Rechnungstotal
              $gesamtpreis = $gesamtpreis + $mwst_total;       // Nicht gerundeter Gesamtpreis ohne Versandkosten und Mindermengenzuschlag
          }
          echo "<tr class='content' valign=top>\n";
          echo "  <td class='content' colspan=5 align=right>\n";
          if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || (($Versandkosten > 0.0)  && ($gesamtpreis > 0.0))) {echo "<BR>";}
          // Unterscheidung ob Artikelpreise schon inkl. oder noch exkl. MwSt eingegeben wurden
          $mwst_vor_formatting = "    <b style='font-weight:bold'>"; // Wird weiter unten nochmals verwendet (MwSt inkl. --> Fett, sonst nicht)
          $mwst_nach_formatting = "    </b>";
          echo $mwst_vor_formatting."Gesamtpreis (inkl. MwSt)";
          echo ":".$mwst_nach_formatting."\n";
          echo "  </td><td class='content' align='right'>\n";
          if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || (($Versandkosten > 0.0)  && ($gesamtpreis > 0.0))) {echo "<BR>";}
          echo $mwst_vor_formatting.$waehrung." ";

          if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || ($gesamtpreis > 0.0)) {
              echo getZahlenformat($Rechnungstotal);// Gesamtpreis der Bestellung formatiert (2 Stellen nach dem Komma) ausgeben
          }
          else {
              echo getZahlenformat($gesamtpreis);// Gesamtpreis der Bestellung OHNE Versandkosten und Mindermengenzuschlag
          }
          echo $mwst_nach_formatting."</td>\n";
          echo "</tr>\n";
      }// Ende MwSt pflichtig (2) und Preise exkl. MwSt
  }// Ende MwSt pflichtig (2)
  echo "</table>";

}// End function darstellenBestellung

// -----------------------------------------------------------------------------------------------
// Diese Funktion schreibt den Warenkorbinhalt formatiert in einen String, der zum Versand der
// E-Mail's an den Shopbenutzer und -betreiber gebraucht wird. Funktionalitaet ansonsten aehnlich
// wie Funktion 'darstellenBestellung'
// -----------------------------------------------------------------------------------------------
function darstellenStringBestellung($myBestellung) {

  $Attribut_Anzahl = 4; //Momentan noch statische Angabe der Anzahl

  // Aufbereitung der Daten um sie im Formular darstellen zu koennen:
  $Bestellungs_ID = $myBestellung->Bestellungs_ID;
  $Session_ID = $myBestellung->Session_ID;
  $Name = $myBestellung->Name;
  $Vorname = $myBestellung->Vorname;
  $Adresse1 = $myBestellung->Adresse1;
  $Adresse2 = $myBestellung->Adresse2;
  $PLZ = $myBestellung->PLZ;
  $Ort = $myBestellung->Ort;
  $Email = $myBestellung->Email;
  $Datum = $myBestellung->Datum;
  $Endpreis = $myBestellung->Endpreis;
  $Anmerkung = $myBestellung->Anmerkung;
  $Bezahlungsart = $myBestellung->Bezahlungsart;
  $Versandkosten = $myBestellung->Versandkosten;
  $Mindermengenzuschlag = $myBestellung->Mindermengenzuschlag;
  $Rechnungstotal = $myBestellung->Rechnungsbetrag;
  $Nachnahmebetrag = $myBestellung->Nachnahmebetrag;
  $Bestellung_abgeschlossen = $myBestellung->Bestellung_abgeschlossen;
  // Auslesen der MwSt-Settings um feststellen zu koennen ob die Artikelpreise inkl. oder exkl. MwSt sind
  $aktuelle_mwst_settings = getmwstsettings();

  // Damit wir den Text fuer den Versandkosten Rechnungsposten ausgeben koennen
  // muessen wir zuerst die personalisierte Version von der Datenbank laden:
  $Setting_Nr = 1; // Vorlaeufig noch statische $Setting_Nr :-(
  $meineVersandkosten = getversandkostensettings($Setting_Nr);
  $Versandkostentext = $meineVersandkosten->Pauschale_text;

  // Assoziativer Array, welcher als Index die MwSt-Saetze hat und als Werte deren aufsummierte Totale
  $MwStarray = array();
  $DefaultMwSt_Satz = getstandardmwstsatz(); // Shopweiter Standard MwSt-Satz auslesen

  // Die Artikel der Bestellung ($value entspricht einem Artikel_info-Objekt)
  // werden weiter unten direkt im HTML-Teil dynamisch aus dem Artikel_info-
  // Objekt gelesen und dargestellt
  // Die Referenznummer setzt sich aus der intern verwendeten Bestellungs_ID
  // und dem Offset $bestellungs_offset (bestellung_def.php) zusammen. Auf diese Weise sind Bestellungen einfach
  // zu handhaben. (Bemerkung: Der Bestellungstag </Zahlungsart> wird hier geschlossen, siehe auch bestellung_def.php)
  $mailstring="Ihre Referenznummer: ".bestellungs_id_to_ref($Bestellungs_ID)."</Zahlungsart>";
  $mailstring.= "\n\n<Artikelliste>Bestellte Artikel:\n------------------";
  // Waehrungsstring aus DB laden
  $waehrung = getWaehrung();
  // Spezielle Euro Behandlung: Sobald der Euro als Symbol gespeichert wurde, erscheint
  // in der Variablen $waehrung der Text &euro. Dieser muss durch das Euro-Symbol  (AltGr+E)
  // ersetzt werden
  if ($waehrung == "&euro;") {
      $waehrung = "EUR";
  }
  // Artikelanzahlzaehler
  $counter = 1;
  // Variable, mit der der Gesamtpreis aufsummiert werden kann
  $gesamtpreis = 0;
  foreach(($myBestellung->getallartikel()) as $keyname => $value) {
    // MwSt-Satz des Artikels auslesen
    $ArtikelMwSt_Satz = getmwstofArtikel($value->Artikel_ID);
    $einzelpreis = $value->Preis + $value->Aufpreis;
    $mailstring.="\n\n<Artikelliste_Artikel$counter>Artikel Name: ".$value->Name."\n";
    // Wenn die Artikel_Nr benutzt wird (ungleich Leerstring), diese hier anzeigen
    if ($value->Artikel_Nr != "") {
        $mailstring.="Artikel Nr:   ".$value->Artikel_Nr."\n";
    }
    $mailstring.="Anzahl:       ".$value->Anzahl."\n";
    // Variation ausgeben, falls Vorhanden

     // Ermittlen, ob bei Artikel irgendwelche Varianten gewaehlt wurden. Wenn
    // ja, die Variable hatVariationen auf true setzten
    foreach(($value->getallvariationen()) as $key => $val) {
      if (!empty($key)){
        $hatVariationen = true;
      }
    }
    // falls der Artikel Variationen hat
    if ($hatVariationen == true){
      $anzahlvariationen = count($value->getallvariationen()); // Anzahl Variationen bestimmen
      if ($anzahlvariationen > 0) {
        $mailstring.="Varianten:    ";
        // erste Variante wird auf der gleichen Zeile dargestellt, wie der Bezeichnungstext 'Optionen'
        $ersteVariation = true;
        $anzahlvariationen = count($value->getallvariationen());
        foreach(($value->getallvariationen()) as $key => $val) {
          // erste Variante..
          if ($ersteVariation == true){
            if ($anzahlvariationen > 1) {
                $mailstring.= "- ";
            }
            $mailstring.= $key."\n";
            $ersteVariation = false;
          }
          // jede weitere Variante eines Artikels..
          else{
            $mailstring.= "              - ".$key."\n";
          }
          // Variantenaufpreis zu einzelpreis aufaddieren
          $einzelpreis = $einzelpreis + $val;
        }
      }
    }
    // Optionen formatiert ausgeben, falls vorhanden
    $hatOptionen = false;
    // Ermittlen, ob bei Artikel irgendwelche Optionen gewaehlt wurden. Wenn
    // ja, die Variable hatOptionen auf true setzten
    foreach(($value->getalloptionen()) as $key => $val) {
      if (!empty($key)){
        $hatOptionen = true;
      }
    }
    // falls der Artikel Optionen gewaehlt hat
    if ($hatOptionen == true){
      $mailstring.="Optionen:     ";
      // erste Option wird auf der gleichen Zeile dargestellt, wie der Bezeichnungstext 'Optionen'
      $ersteOption = true;
      $anzahloptionen = count($value->getalloptionen());
      foreach(($value->getalloptionen()) as $key => $val) {
        // erste Option..
        if ($ersteOption == true){
          if ($anzahloptionen > 1) {
              $mailstring.= "- ";
          }
          $mailstring.= $key."\n";
          $ersteOption = false;
        }
        // jede weitere Option eines Artikels..
        else{
          $mailstring.= "              - ".$key."\n";
        }
      // Optionsaufpreis zu einzelpreis aufaddieren
      $einzelpreis = $einzelpreis + $val;
      }
    }

    if (count($value->Zusatzfelder) > 1 || ($value->Zusatzfelder[0] != "")){
        foreach($value->Zusatzfelder as $Zusatzfeld){
            if ($Zusatzfeld != ""){
                $mailstring.="- ".stripslashes($Zusatzfeld)."\n";
            } // end of if
        } // end of foreach
    } // end of if

    $mailstring.="Einzelpreis:  ".$waehrung." ";
    // Einzelpreis des Artikels formatiert (2 Stellen nach dem Komma) ausgeben
    $mailstring.= getZahlenformat($einzelpreis);
    // einzelgesamtpreis berechnen
    $einzelgesamtpreis = $einzelpreis * $value->Anzahl;
    // MwStarray updaten (Geldbetrag, welcher unter des selben MwSt-Satzes liegt um Artikelpreis erhoehen):
    // Anm. Optionen und Variationen des Artikels werden mit dem gleichen MwSt-Satz des Artikels abgerechnet!
    $MwStarray[$ArtikelMwSt_Satz] = $MwStarray[$ArtikelMwSt_Satz] + $einzelgesamtpreis;
    // Wenn keine Optionen/Variationen und nur ein Stueck gewaehlt wurde, so ist der Preis = Einzelpreis, also zeigen wir den Preis nicht auch noch an
    if ($einzelpreis != $einzelgesamtpreis) {
        $mailstring.="\nPreis:        ".$waehrung." ";
        // Einzelgesamtpreis des Artikels formatiert (2 Stellen nach dem Komma) ausgeben
        $mailstring.= getZahlenformat($einzelgesamtpreis)."</Artikelliste_Artikel$counter>";
    }
    else {
        $mailstring.="</Artikelliste_Artikel$counter>";
    }
    // Gesamtpreis berechnen
    $gesamtpreis = $gesamtpreis + $einzelgesamtpreis;
    // Artikelcounter inkementieren
    $counter++;
  }// End Artikelkostenberechnung
  $Porto_Verpackung_Satz = getportoverpackungmwstsatz(); // MwSt-Satz fuer Porto und Verpackung (Versandkosten, Nachnahme, Mindermengenzuschl.) auslesen
  $anzahlartikel = $counter-1;
  $mailstring.= "\n\n</Artikelliste><Gesamtpreis>===================================================== ";
  if (($Versandkosten > 0.0) && ($gesamtpreis > 0.0)) {
      $mailstring.= "\n$Versandkostentext: $waehrung ";
      $mailstring.= getZahlenformat($Versandkosten);
      // Versand- und Verpackungskosten kommen auch in die Mehrwertsteuer rein
      // Es gibt eine spezielle MwSt-Abrechnung fuer Porto und Verpackung
      switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Versandkosten zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Versandkosten;
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Versandkosten * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Versandkosten;
      }// End switch
  }// End Versandkostenberechnung

  if (($Nachnahmebetrag > 0.0) && ($gesamtpreis > 0.0)) {
      $mailstring.= "\nNachnahmegebühr: $waehrung ";
      $mailstring.= getZahlenformat($Nachnahmebetrag);
      switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Versandkosten zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Nachnahmebetrag;
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Nachnahmebetrag * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Nachnahmebetrag;
      }// End switch
  }// End Nachnahmegebuehrenberechnung

  if (($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) {
      $mailstring.= "\nMindermengenzuschlag (bis $waehrung ";
      $mailstring.= getZahlenformat($meineVersandkosten->Mindermengenzuschlag_bis_Preis);
      $mailstring.= "): $waehrung ";
      $mailstring.= getZahlenformat($Mindermengenzuschlag);
      switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Versandkosten zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Mindermengenzuschlag;
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Mindermengenzuschlag * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Mindermengenzuschlag;
      }// End switch
  }// End Mindermengenzuschlagberechnung

  // Wenn es sich um die gebuehrenpflichtige Zahlung ueber einen Treuhandservice handelt, diese Gebuehr
  // (abhaengig vom Bestellwert) berechnen und MwSt-versteuern (Porto und Versandkosten-Abrechnungsart)
  if (($Bezahlungsart == "Treuhandzahlung") && ($gesamtpreis > 0.0)) {

      // Mehrwertsteuerbetrag ausrechnen --> Damit Bestellsumme inkl. MwSt berechnet werden kann
      // die gleiche Berechnung findet unten ncohmals statt (inkl. Versandkosten,...)
      // Fuer jeden im Warenkorb vorkommenden MwSt-Satz Anzahl ausweisen und ein Total praesentieren
      $mwst_total = 0.0; //In dieser Variable werden die errechneten MwSt-Betraege aufsummiert
      if ($meineVersandkosten->MwStpflichtig == "Y" && $gesamtpreis > 0) {
          foreach ($MwStarray as $MWST_Satz => $Betrag) {
              // Ausgabe nur machen, wenn MwSt-Satz groesser als 0% ist.
              if ($MWST_Satz > 0.0) {
                  // Wenn MwSt-inkl. wird MwSt anders berechnet als wenn die Artikelpreise exkl. MwSt. sind:
                  if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "Y") {
                      // Artikelpreise sind schon inkl. MwSt, folgende Formel verwenden:
                      $mwst_anteil = ($MWST_Satz / (100 + $MWST_Satz)) * $Betrag;
                  }
                  else {
                      // Artikelpreise sind noch exkl. MwSt. Folgende MwSt-Berechnung anwenden:
                      $mwst_anteil = $Betrag * ($MWST_Satz/100);
                  }
                  $mwst_total = $mwst_total + $mwst_anteil; //MwSt Anteil aufsummieren
              }
          }// End foreach
      } // End if

      // $Treuhandzahlungskosten ist ein Array mit zwei Elementen. Wert 0 beinhaltet die Treuhaenderkosten
      // welche dem Kunden verrechnet werden (inkl. MwSt). Im Element 2 befindet sich der Kundenanteil, mit
      // welchem dieser Betrag errechnet wurde.
      $Treuhandzahlungkosten = getTreuhandbetrag(($gesamtpreis + $mwst_total));
      if ($Treuhandzahlungkosten[1] == 0) {
          $mailstring.= "\nDie Treuhandkosten werden von uns übernommen: $waehrung ";
          $mailstring.= getZahlenformat(0);
      }
      else {
        $mailstring.= "\nTreuhandkosten: $waehrung ";
        $mailstring.= getZahlenformat($Treuhandzahlungkosten[0]);
        // Rechnungstotal beinhaltet noch keine Treuhandkosten, weil diese nicht im Bestellungsobjekt gespeichert werden, update:
        $Rechnungstotal = $Rechnungstotal + $Treuhandzahlungkosten[0];
        $gesamtpreis = $gesamtpreis + $Treuhandzahlungkosten[0];
        switch ($Porto_Verpackung_Satz) {
          case -2:  // Versteuern mit MwSt-Satz, welcher groesster Anteil in der Rechnungssumme hat
              // Groesster Anteil an MwSt-Saetzen ausrechnen:
              $pv_satz = -5;   // Reset mit ungueltigem Wert
              $pv_anteil = 0;  // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  if ($pv_anteil < $anteil) {
                      $pv_satz = $satz;
                      $pv_anteil = $anteil;
                  }
              }
              if ($pv_satz <= -5) {
                  die("<h1>U_B_D_Error: Abbruch, weil Porto und Verpackung nicht versteuert werden k&ouml;nnen!</h1></body></html>"); //Abbruch da kein Satz gefunden wurde
              }
              // Versandkosten zu gefundenem Ansatz versteuern (in entsprechendes Feld im Array dazuaddieren)
              $MwStarray[$pv_satz] = $MwStarray[$pv_satz] + $Treuhandzahlungkosten[0];
              break;
          case -1:  // Anteilsmaessig versteuern
              // Gesamtbetrag der Rechnung (exkl. Versandkosten, Nachnahmegebuehr, Mindermengenzuschlag) berechnen
              $pv_total = 0; // Reset
              foreach ($MwStarray as $satz=>$anteil) {
                  $pv_total = $pv_total + $anteil;
              }
              // Anteilsmaessiges Versteuern der Porto und Verpackungskosten
              foreach ($MwStarray as $satz=>$anteil) {
                  //Momentaner Anteil ausrechnen
                  $pv_prozent = $anteil / $pv_total;
                  $pv_mwst_betrag = $Treuhandzahlungkosten[0] * $pv_prozent;
                  // Versandkosten zum ausgerechneten Anteil dem aktuellen MwSt-Satz verrechnen
                  $MwStarray[$satz] = $MwStarray[$satz] + $pv_mwst_betrag;
              }
              break;
          case 0:   // Porto und Verpackung NICHT versteuern (MwSt-frei)
              $pv_no_mwst_flag = true; // Dieses Flag wird true, wenn Porto und Verpackung als MwSt-frei definiert wurden
              break;
          default:  // Rest = Versteuern zu einem Festsatz (definiert in der Variable $Porto_Verpackung_Satz)
              // Versandkosten zum angegebenen Festsatz (MwSt-Satz) versteuern
              $MwStarray[$Porto_Verpackung_Satz] = $MwStarray[$Porto_Verpackung_Satz] + $Treuhandzahlungkosten[0];
        }// End switch
      }// End else $Treuhandzahlungkosten
  }

  // MwSt-Berechnung:
  // Wenn man nicht MwSt-pflichtig ist, oder die Preise inkl. MwSt sind, so wird das Total vor den MwSt-Angaben ausgegeben...
  if ($meineVersandkosten->MwStpflichtig == "N" || ($meineVersandkosten->MwStpflichtig == "Y" && $aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "Y")) {
      if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || (($Versandkosten > 0.0)  && ($gesamtpreis > 0.0)) || (($Nachnahmebetrag > 0.0)  && ($gesamtpreis > 0.0))) {
          $mailstring.= "\n===================================================== ";
      }
      $mailstring.= "\nGesamtpreis";
      if ($meineVersandkosten->MwStpflichtig == "Y") {
          $mailstring.= " (MwSt inkl.) ";
      }
      $mailstring.= ": ";
      $mailstring.= $waehrung." ";
      // Test ob man den Gesamtpreis auf 0.05 genau runden muss:
      if (getgesamtpreisrunden()) {
          $Rechnungstotal = runden_05($Rechnungstotal); // Gerundetes Rechnungstotal
          $gesamtpreis = runden_05($gesamtpreis);       // Gerundeter Gesamtpreis ohne Versandkosten und Mindermengenzuschlag
      }

      if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || ($gesamtpreis > 0.0) || (($Nachnahmebetrag > 0.0)  && ($gesamtpreis > 0.0))) {
          $mailstring.= getZahlenformat($Rechnungstotal);//Total mit Versandkosten, Nachnahmegebuehren, Treuhandkosten und Mindermengenzuschlag
      }
      else {
          $mailstring.= getZahlenformat($gesamtpreis);//Total ohne Versandkosten und Mindermengenzuschlag
      }
      if ($meineVersandkosten->MwStpflichtig == "Y") {
          if ($gesamtpreis > 0){
              // Fuer jeden im Warenkorb vorkommenden MwSt-Satz Anzahl ausweisen und ein Total praesentieren
              $mwst_total = 0.0; //In dieser Variable werden die errechneten MwSt-Betraege aufsummiert
              foreach ($MwStarray as $MWST_Satz => $Betrag) {
                  if ($MWST_Satz > 0) {
                      $mwst_anteil = ($MWST_Satz / (100 + $MWST_Satz)) * $Betrag;
                      $mwst_total = $mwst_total + $mwst_anteil; //MwSt Anteil aufsummieren
                      $mailstring.= "\nMwSt-Anteil ($MWST_Satz%): $waehrung ";
                      $mailstring.= getZahlenformat($mwst_anteil);// MwSt. Anteil formatiert (2 Stellen nach dem Komma) ausgeben
                  }
              }
          }
          $mailstring.= "\nMwSt-Nummer: ".$meineVersandkosten->MwStNummer;
          $mailstring.= "\n=====================================================</Gesamtpreis></Bestellung>\n ";
      }
      else {
          $mailstring.= "\n=====================================================</Gesamtpreis></Bestellung>\n ";
      }
  }
  else {
      //... wenn die Preise exkl. MwSt angegeben sind, so wird zuerst die MwSt zusammengerechnet und vor dem Gesamtpreis angezeigt
      if ($gesamtpreis > 0){
          // Fuer jeden im Warenkorb vorkommenden MwSt-Satz Anzahl ausweisen und ein Total praesentieren
          $mwst_total = 0.0; //In dieser Variable werden die errechneten MwSt-Betraege aufsummiert
          foreach ($MwStarray as $MWST_Satz => $Betrag) {
              if ($MWST_Satz > 0) {
                  $mwst_anteil = $Betrag * ($MWST_Satz / 100);
                  $mwst_total = $mwst_total + $mwst_anteil; //MwSt Anteil aufsummieren
                  $mailstring.= "\nMwSt-Anteil ($MWST_Satz%): $waehrung ";
                  $mailstring.= getZahlenformat($mwst_anteil);// MwSt. Anteil formatiert (2 Stellen nach dem Komma) ausgeben
              }
          }
      }
      $mailstring.= "\nMwSt-Nummer: ".$meineVersandkosten->MwStNummer;
      if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || (($Versandkosten > 0.0)  && ($gesamtpreis > 0.0)) || (($Nachnahmebetrag > 0.0)  && ($gesamtpreis > 0.0))) {
          $mailstring.= "\n=====================================================";
      }

      // Ausgabe des Gesamtpreises EXKL. MwSt
      $mailstring.= "\nGesamtpreis (exkl. MwSt)";
      $mailstring.= ": ";
      $mailstring.= $waehrung." ";

      if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || ($gesamtpreis > 0.0) || (($Nachnahmebetrag > 0.0)  && ($gesamtpreis > 0.0))) {
          $mailstring.= getZahlenformat($Rechnungstotal);//Total mit Versandkosten und Mindermengenzuschlag hier inkl. MwSt
      }
      else {
          $mailstring.= getZahlenformat($gesamtpreis);//Total ohne Versandkosten und Mindermengenzuschlag hier dann inkl. MwSt
      }
      // Ausgabe des Gesamtpreises INKL. MwSt
      $mailstring.= "\nGesamtpreis";
      if ($meineVersandkosten->MwStpflichtig == "Y") {
          $mailstring.= " (inkl. MwSt)";
      }
      $mailstring.= ": ";
      $mailstring.= $waehrung." ";
      // Test ob man den Gesamtpreis auf 0.05 genau runden muss:
      if (getgesamtpreisrunden()) {
          $Rechnungstotal = runden_05($Rechnungstotal + $mwst_total); // Auf 0.05 gerundetes Rechnungstotal
          $gesamtpreis = runden_05($gesamtpreis + $mwst_total);       // Auf 0.05 gerundeter Gesamtpreis ohne Versandkosten und Mindermengenzuschlag
      }
      else {
          $Rechnungstotal = $Rechnungstotal + $mwst_total; // Rechnungstotal
          $gesamtpreis = $gesamtpreis + $mwst_total;       // Gesamtpreis ohne Versandkosten und Mindermengenzuschlag
      }

      if ((($Mindermengenzuschlag > 0.0) && ($gesamtpreis > 0.0)) || ($gesamtpreis > 0.0) || (($Nachnahmebetrag > 0.0)  && ($gesamtpreis > 0.0))) {
          $mailstring.= getZahlenformat($Rechnungstotal);//Total mit Versandkosten und Mindermengenzuschlag hier inkl. MwSt
      }
      else {
          $mailstring.= getZahlenformat($gesamtpreis);//Total ohne Versandkosten und Mindermengenzuschlag hier dann inkl. MwSt
      }

      $mailstring.= "\n=====================================================</Gesamtpreis></Bestellung>\n ";
  }

  return ($mailstring);

}// End function darstellenStringBestellung

// Funktion um zu ueberpruefen, ob Cookies gesetzt werden koennen oder nicht.
// Code von: http://download.php.net/manual/en/function.setcookie.php
// Im Argument $Errortitle [String] uebergibt man einen Titel, welcher dem Kunden
// den Fehler anzeigt, z.B. "die Kasse kann nicht betreten werden weil Cookies
// ausgeschaltet sind". Im zweiten Argument kann man einen Vorschlagstext uebergeben,
// wie der Kunde dieses Problem beheben kann, z.B. "Wir benutzen Cookies um Ihnen als Kunden
// einen Warenkorb zuweisen zu k&ouml;nnen. Das von uns gesetzte Cookie lebt nur solange, bis
// Sie den Browser wieder schliessen (Session-Cookie). Bitte schalten Sie Cookies jetzt ein. Beim
// Internet Explorer 6 kann man auch nur dieses Cookie akzeptieren oder auf Sicherheitsstufe
// "Mittel" wechseln. Bei vielen Browser muss man danach zuerst das Programm neu starten".
// Im dritten Argument (einer Integer-Zahl) kann man angeben, ob die Funktion im Fehlerfall (Fall, dass es
// kein gueltiges Cookie hat) selbststaendig eine Fehlerseite generieren und anzeigen soll, oder
// ob die Funktion nur eine Rueckmeldung in Form eines Rueckgabewertes taetigen soll.
// Wenn man der Variable $todo 0 uebergibt, gibt die Funktion nur einen Rueckgabewert zurueck:
// 0, wenn alles ok ist und ein gueltiges Cookie gesetzt wurde oder eine 1 wenn kein Cookie gesetzt ist.
// Gibt man der Variable $todo eine 1 mit, so wird im Fehlerfall gleich eine ganze Fehlerseite erzeugt.
// Ist das vierte Argument ($showlink) = 1, so wird ein Link mit dem Button 'Zurueck' eingeblendet.
// Argumente: String, String, Int, boolean
// Rueckgabewert: Array der Zeichen vom Eingabestring
function checkifCookiesenabled($Errortitle, $Proposition, $todo, $showlink) {
    global $HTTP_COOKIE_VARS;  //Globales Cookie-Variable-Array innerhalb der Funktion sichtbar machen
    global $Kategorie_ID;
    global $Artikel_ID;

    // Wenn Leerstrings als Parameter uebergeben wurden, werden Default-Parameter angenommen:
    if ($Errortitle == "") {
        $Errortitle = "<p align=\"center\"><h3 class='content'>Cookies sind ausgeschaltet</h3></p><br>";
    }
    if ($Proposition == "") {
        $Proposition = "<h4 class='content'><center>Wir benutzen Cookies um Ihnen als Kunden
                       einen Warenkorb zuweisen zu k&ouml;nnen.<BR>Das von uns gesetzte Cookie lebt nur solange, bis
                       Sie den Browser wieder schliessen (Session-Cookie).<BR><B>Bitte schalten Sie Cookies jetzt ein.</B><BR>Beim
                       Internet Explorer 6 kann man unser Cookie auch explizit akzeptieren oder auf Sicherheitsstufe
                       'Mittel' wechseln.<BR>Bei vielen Browser muss man danach zuerst das Programm neu starten<br>
                       Da der Internet Explorer nur begrenzt Speicherplatz f&uuml;r Cookies zur Verf&uuml;gung stellt
                       und danach einfach keine weiteren mehr annimmt, kann es oftmals helfen, einfach die Cookies zu
                       l&ouml;schen.</center></h4><BR><BR>";
    }
    if ($todo == "") {
        $todo = 0;
    }
    if ($showlink == "") {
        $showlink = 0;
    }
    // Cookie-Test --> Test ob die Session-Variable, welche beim Betreten des Shops fuer die Laenge einer
    // Session gesetzt wurde, existiert:
    $cookie_check = 0; //Initialisierung

    // Check ob Cookies aktivert sind. Wenn sie aktiviert sind, wird die Variable $cookie_check = 1;
    if (isset($_COOKIE["mySession_ID"])) {$cookie_check = 1;}
    else if (isset($HTTP_COOKIE_VARS["mySession_ID"])) {$cookie_check = 1;}
    if (($cookie_check == 0) && ($todo == 1)) {

        echo "<br>\n";
        echo "<p align=\"center\"><h3 class='content'>$Errortitle</h3></p><br>\n";
        echo "<h4 class='content'><center>$Proposition</center></h4>\n";
        // Rueckgabelink konfigurieren
        if ($showlink == 1) {
            if (($Kategorie_ID == "") && ($Artikel_ID == "")) {
                echo "<center><a class='content' href='../index.php' target='_top'><IMG src=\"./Buttons/bt_zurueck.gif\" border=\"0\" alt=\"Zur&uuml;ck\" title=\"Zur&uuml;ck\"></a></center>\n";
            }
            else {
                echo "<center><a class='content' href='USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&amp;".session_name()."=".session_id().
                     "&amp;Kategorie_ID=$Kategorie_ID#Ziel".$Artikel_ID."'><IMG src=\"./Buttons/bt_zurueck.gif\" border=\"0\" alt=\"Zur&uuml;ck\" title=\"Zur&uuml;ck\"></a></center>\n";
            }// End if Kategorie_ID
            echo "</body>\n</html>\n";
        }// End if showlink
        exit; //Beenden, damit nicht noch weiter gearbeitet wird!
    }// End if cookie_check
    // Rueckgabewert zurueck geben
    return $cookie_check;
    // Ausfuehren des Fehlercodes
}// End checkifCookiesenabled


  // End of file-----------------------------------------------------------------------
?>
