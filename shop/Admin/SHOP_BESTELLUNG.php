<?php
  // ****************** DEPRECATED - WIRD NICHT MEHR VERWENDET ************
  //
  // Filename: SHOP_BESTELLUNG.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet das GUI fuer die Bestellungsverwaltung *** BETA ***
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_BESTELLUNG.php,v 1.34 2003/05/24 18:41:33 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_BESTELLUNG = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
  if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}
  if (!isset($USER_BESTELLUNG_DARSTELLUNG)) {include("USER_BESTELLUNG_DARSTELLUNG.php");}
  if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // HTML-Kopf, der bei jedem Aufruf des Files ausgegeben wird
?>
  <HTML>
    <HEAD>
        <TITLE>Bestellungsmanagement</TITLE>
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
  // Kundendaten darstellen
  if ($darstellen == 10){

      // Die Bestellung von der Datenbank auslesen:
      $myBestellung = getBestellung_Ref($Referenz_Nr);

      if ($myBestellung->Bestellungs_ID == "") {
          // Abbruch, wenn keine Bestellung gefunden wurde
          echo '<h1>SHOP ADMINISTRATION</h1>';
          echo '<h3>Bestellungsmanagement</h3>';
          echo '<BR>Keine Bestellung mit dieser Referenznummer im System gespeichert!<BR><BR>';
          echo '<a class="content" href="./SHOP_BESTELLUNG.php" ><img src="../Buttons/bt_weiter_admin.gif" border="0"></a>'."\n";
          exit; // Abbruch
      }

      // Kundendaten zur entsprechenden Bestellung auslesen:
      $myKunde = getKunde_einer_Bestellung($myBestellung->Bestellungs_ID);

      // Auslesen der Waehrung zur Darstellung eines Preises
      $Waehrung = getWaehrung();

      // Aufbereitung der Bestellungsdaten:
      $Bestellungs_ID = $myBestellung->Bestellungs_ID;
      $Session_ID = $myBestellung->Session_ID;
      $diesesDatum = $myBestellung->Datum;
      $Rechnungsbetrag = $myBestellung->Rechnungsbetrag;
      $Bemerkungen = $myBestellung->Anmerkung;
      $Bezahlungsart = $myBestellung->Bezahlungsart;
      $Bestellung_abgeschlossen = $myBestellung->Bestellung_abgeschlossen;
      // amerikanisches Datumsformat in europaeisches umwandeln und ausgeben
      $temp1_datum = explode("-", $diesesDatum);
      $Datum1 = $temp1_datum[2].".".$temp1_datum[1].".".$temp1_datum[0];


      // Aufbereitung der Kundendaten:
      $Kunden_Nr = $myKunde->Kunden_Nr; // *** Wird noch nicht benutzt ***
      $Kunden_ID = $myKunde->Kunden_ID;
      $Einkaufsvolumen = $myKunde->Einkaufsvolumen;
      $gesperrt = $myKunde->gesperrt;
      $temp = $myKunde->temp;
      $Kunden_Session_ID = $myKunde->Session_ID;
      $erstesDatum = $myKunde->AnmeldeDatum;
      $letztesDatum = $myKunde->LetzteBestellung;
      // amerikanisches Datumsformat in europaeisches umwandeln und ausgeben
      $temp2_datum = explode("-", $erstesDatum);
      $AnmeldeDatum = $temp2_datum[2].".".$temp2_datum[1].".".$temp2_datum[0];
      // amerikanisches Datumsformat in europaeisches umwandeln und ausgeben
      $temp_datum = explode("-", $letztesDatum);
      $LetzteBestellung = $temp_datum[2].".".$temp_datum[1].".".$temp_datum[0];


      // Aktuelles Datum einfuegen (ist nur fuer die Datenbank -> wird nirgends angezeigt)
      $mydate = getdate();
      $Datum = $mydate[year]."-".$mydate[mon]."-".$mydate[mday];// Format yyyy-mm-dd

      // Formular zur Eingabe der Lieferadresse und Bezahlungsart ausgeben, und mit Werten, die
      // eventuell schon in der Datenbank gespeichert sind, fuellen (nicht beim ersten Anzeigen!)
?>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>Bestellungsmanagement</h3>
    <h3>Kundendaten:</h3>
<?php
  // Anzahl fest vordefinierter Felder
  $anz_vordef = 14;

  // Attributobjekt aus Datenbank holen
  $myAttribut = getAttributobjekt();

  // Anzahl Zusatzfelder
  $gesamt = $myAttribut->attributanzahl();
  $anz_zusatz = ($gesamt-$anz_vordef);

  // Die verschiedenen Werte und Einstellungen in Arrays abfuellen
  $Namen = $myAttribut->getallName();
  $verwenden = $myAttribut->getallanzeigen();
  $speichern = $myAttribut->getallin_DB();
  $pruefen = $myAttribut->getallEingabe_testen();

?>

  <script language="JavaScript">
  <!--

  function chkFormular() {

<?php
  // einen Array abfuellen, wo jedes Eingabefeld eine Arravariable erhält. Folgende Einträge
  // werden gemacht:
  // Y -> dieses Feld auf Eingabe überprüfen
  // N -> dieses Feld nicht auf Eingabe überprüfen
  // M -> dieses Feld auf nach E-Mail-Kriterien auf Eingabe überprüfen
  echo "var checkarray=new Array (";
  for ($zaehl = 0; $zaehl <= ($gesamt-1); $zaehl++){
      if ($verwenden[$zaehl] == "Y"){
          // E-Mail Eingabeüberprüfung
          if(($pruefen[$zaehl] == "Y") && ($Namen[$zaehl] == "E-Mail")){ echo "\"M\","; }
          // normale Eingabeüberprüfung
          else if($pruefen[$zaehl] == "Y"){ echo "\"Y\","; }
          // keine Eingabeüberprüfung
          else { echo "\"N\","; }
      } // end of if
  } // end of for
  echo "\"0\");";
?>

      for(count=0; count < (checkarray.length-2); count++){
          if (checkarray[count] == 'Y'){
              var wert = document.Formular[count].value;
              if ( wert == "" || wert == " "){
                  ersetz = /\+/gi;
                  feldname = document.Formular[count].name.replace(ersetz," ");
                  alert("Bitte füllen Sie das Feld "+unescape(feldname)+" aus!");
                  document.Formular[count].focus();
                  return false;
              } // end of if wert = ""
          }
          if (checkarray[count] == 'M'){
              var ok = 1;
              var email = document.Formular[count].value;
              var geteilt = email.split ("@");


              // falls mehr als ein oder gar kein '@' im string
              if (geteilt.length != 2){
                  ok = 0;
              }

              else{
                  // falls vor oder nach dem '@' nichts mehr kommt
                  if (geteilt[0] == "" || geteilt[1] == "" ) { ok = 0; }

                  // falls nach dem '@' kein Punkt mehr kommt
                  if (geteilt[1].indexOf(".") == "-1" ) { ok = 0; }

                  // falls direkt nach dem '@' oder ganz am Schluss ein Punkt kommt
                  var laenge = geteilt[1].length;
                  if (geteilt[1].indexOf(".") == "0" || geteilt[1].charAt(laenge-1) == ".") { ok = 0; }

                  // falls direkt vor dem '@' oder am Anfang ein Punkt kommt
                  var laenge = geteilt[0].length;
                  if (geteilt[0].indexOf(".") == "0" || geteilt[0].charAt(laenge-1) == ".") { ok = 0; }
              }

              if (ok == 0){
                  alert ("Keine gültige E-Mail-Adresse!");
                  document.Formular[count].focus();
                  return false;
              }
          }


      } // end of for
  }
  //-->
  </script>


  <FORM action='<?php echo $PHP_SELF; ?>' name='Formular' method='POST' onSubmit="return chkFormular()">
  <TABLE class="content" border="0" cellpadding="0" cellspacing="0" width="80%">
<?php

  // hier werden in einem Array die Anzeigelaengen für die Eingabefelder gespeichert
  // [0] = Anrede (wird natürlich nicht gebraucht) [1] = Vorname, usw.
  $laenge_array = array('0', '40', '40', '40', '40', '40', '10', '10', '40', '20', '16', '16', '40', '40', '40', '40', '40', '0');

  // hier werden in einem Array die maximalen Eingabelaengen für die Eingabefelder gespeichert
  // [0] = Anrede (wird natürlich nicht gebraucht) [1] = Vorname, usw.
  $max_array = array('0', '128', '128', '128', '128', '128', '16', '32', '128', '128', '32', '32', '128', '128', '128', '128', '128', '0');

  // Kunden-Daten in einen Array abfuellen
  $daten_array = array($myKunde->Anrede, $myKunde->Vorname, $myKunde->Nachname, $myKunde->Firma, $myKunde->Abteilung, $myKunde->Strasse,
                $myKunde->Postfach, $myKunde->PLZ, $myKunde->Ort, $myKunde->Land, $myKunde->Tel, $myKunde->Fax, $myKunde->Email,
                $myKunde->Attributwert1, $myKunde->Attributwert2, $myKunde->Attributwert3, $myKunde->Attributwert4, $myBestellung->Anmerkung);
  // Ueberpruefen, ob die selbstkonfigurierbaren Felder nicht an die Bestellung gebunden wurden. Wenn ja, so sollen
  // sie aus der Bestellung ausgelesen werden (ist hier momentan ziemlich statisch geloest):
  for ($i=13;$i <= 16; $i++) {
      if ($daten_array[$i] == "") {
          $name = "Attributwert".($i-12);
          $daten_array[$i] = $myBestellung->$name;
      }
  }
  // Hauptfelder ausgeben
  for ($zaehl = 0; $zaehl <= ($gesamt-1); $zaehl++){
      // nur Felder ausgeben, die der Shopbetreiber auch aktiviert hat
      if($verwenden[$zaehl] == 'Y'){

          // Anrede-Dropdown-Liste ausgeben..
          if ($zaehl == 0){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              echo "  <td class='content'>".$Namen[$zaehl].":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td><td class='content'>";
              echo "<select style='font-family: Courier, Courier New, Monaco' name='".$Namen[$zaehl]."'>\n";
              // default option -> keine Anrede
              echo "  <option ";
              if ($daten_array[$zaehl] == "") { echo "selected "; }
              echo "value=''> bitte ausw&auml;hlen&nbsp;&nbsp;&nbsp;\n";
              // Anrede: "Herr"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Herr") { echo "selected "; }
              echo "value='Herr'>Herr\n";
              // Anrede: "Frau"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Frau") { echo "selected "; }
              echo "value='Frau'>Frau\n";
              // Anrede: "Firma"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Firma") { echo "selected "; }
              echo "value='Firma'>Firma\n";
              // Anrede: "Familie"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Familie") { echo "selected "; }
              echo "value='Familie'>Familie\n";
              echo "</select>\n";
              echo "</td>\n</tr>\n";
          } // end of if $zaehl == 0

          // Standard-Textfelder Ausgeben, Name, Strasse,..
          if ($zaehl > 0 && $zaehl < ($anz_vordef-1)){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              echo "  <td class='content'>".htmlspecialchars($Namen[$zaehl]).":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td>\n";
              $einsetzen = rawurlencode($Namen[$zaehl]);
              $einsetzen_1 = str_replace(".", "%2E", $einsetzen);
              echo "<td class='content'><input type=text name=\"".$einsetzen_1."\" size='".$laenge_array[$zaehl]."' maxlength='".$max_array[$zaehl]."' value=\"".htmlspecialchars($daten_array[$zaehl])."\"></td>\n";
              echo "</tr>\n";
          } //

          // frei konfigurierbare Zusatzfelder ausgeben
          if ($zaehl > 12 && $zaehl < 17){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              echo "  <td class='content'>".htmlspecialchars($Namen[$zaehl]).":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td>\n";
              $einsetzen = rawurlencode($Namen[$zaehl]);
              $einsetzen_1 = str_replace(".", "%2E", $einsetzen);
              echo "<td class='content'><input type=text name=\"".$einsetzen_1."\" size='".$laenge_array[$zaehl]."' maxlength='".$max_array[$zaehl]."' value=\"".htmlspecialchars($daten_array[$zaehl])."\"></td>\n";
              echo "</tr>\n";
          } //

          // Bemerkungen-Textarea ausgeben
          if ($zaehl == 17){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              $einsetzen = rawurlencode($Namen[$zaehl]);
              $einsetzen_1 = str_replace(".", "%2E", $einsetzen);
              echo "  <td class='content' valign=top>".htmlspecialchars($Namen[$zaehl]).":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td>\n";
              echo "<td class='content' style='font-family: Courier, Courier New, Monaco'><textarea style='font-family: Courier, Courier New, Monaco' name=\"".$einsetzen_1."\" cols='38' rows='5' wrap=physical>".htmlspecialchars($daten_array[$zaehl])."</textarea></td>\n";
              echo "</tr>\n";
          }

      } // end of if $verwenden[$zaehl] == 'Y'

  } // end of for $zaehl = 0; $zaehl <= ($anz_vordef-1); $zaehl++

          // Ausgabe der Bankdaten des Kunden, wenn dieser die Bankdaten gespeichert hat und die Bazahlungsart dieser Bestellung = Lastschrift ist.
  if ($Bezahlungsart == "Lastschrift" && $myKunde->bankdaten_speichern == "Y") {
      echo "<tr class='content'><td class='content'><br><b>Bankdaten:</b> Kontoinhaber:</td><td><br><input type=text name=\"kontoinhaber\" size='32' value=\"".$myKunde->kontoinhaber."\"></td></tr>";
      echo "<tr class='content'><td class='content'>Name der Bank:</td><td><input type=text name=\"bankname\" size='32' value=\"".$myKunde->bankname."\"></td></tr>";
      echo "<tr class='content'><td class='content'>Bankleitzahl (BLZ):</td><td><input type=text name=\"blz\" size='32' value=\"".$myKunde->blz."\"></td></tr>";
      echo "<tr class='content'><td class='content'>Kontonummer:</td><td><input type=text name=\"kontonummer\" size='32' value=\"".$myKunde->kontonummer."\"><input type=hidden name=\"bankdaten_speichern\" size='32' value=\"".$myKunde->bankdaten_speichern."\"></td></tr>";
  }

  // fuer alle Zusatzattribute ermitteln, ob Sie zum Kundendatensatz oder zur Bestellung gespeichert werden
  // Information als Hidden-Feld weitergeben
  $attr_count = 1; // Attributzaehler

  for ($zaehl = 13; $zaehl <= 16; $zaehl++){
      if($verwenden[$zaehl] == 'Y'){
          if($speichern[$zaehl] == 'Y'){
              echo"<INPUT TYPE=hidden NAME=Attr_speichern[".$attr_count."] VALUE='Kunde'>\n";
          } // end of if speichern = Y
          else{
              echo"<INPUT TYPE=hidden NAME=Attr_speichern[".$attr_count."] VALUE='Bestellung'>\n";
          }
      $einsetzen = rawurlencode($Namen[$zaehl]);
      $einsetzen_1 = str_replace(".", "%2E", $einsetzen);
      echo"<INPUT TYPE=hidden NAME=Attr_name[".$attr_count."] VALUE=\"".$einsetzen_1."\">\n";
      } // end of if verwenden = Y
      else{
      echo"<INPUT TYPE=hidden NAME=Attr_name[".$attr_count."] VALUE=''>\n";
      }
      // Attributzaehler erhoehen
      $attr_count++;
  } // end of for

?>
    </TR><TR class='content'>
      <TD class='content'><br>Einkaufsvolumen:</TD>
      <TD class='content'><br><?php echo $Waehrung." ".getZahlenformat($Einkaufsvolumen); ?></TD>
    </TR>
    </TR><TR class='content'>
      <TD class='content'>Bei uns Kunde seit:</TD>
      <TD class='content'><?php echo $AnmeldeDatum; ?></TD>
    </TR>
    </TR><TR class='content'>
      <TD class='content'>LetzteBestellung:</TD>
      <TD class='content'><?php echo $LetzteBestellung; ?></TD>
    </TR>
    </TR>
    </TABLE border=0 cellspacing=0 cellpadding=0>
    <BR><BR>
    <H3>Bestellungsdaten:</H3>
    <TABLE>
    <TR class='content'>
      <TD class='content'><B>Referenznummer:</B></TD>
      <TD class='content'><?php echo bestellungs_id_to_ref($Bestellungs_ID); ?></TD>
    </TR>
    </TR><TR class='content'>
      <TD class='content'><B>Bestell-Datum:</B></TD>
      <TD class='content'><?php echo $Datum1; ?></TD>
    </TR>
    </TR><TR class='content'>
      <TD class='content'><B>Zahlungsart:</B></TD>
      <TD class='content'><?php echo $Bezahlungsart." "; if ($Bezahlungsart == "Lastschrift" && $myKunde->bankdaten_speichern == "N") {echo "(auf Kundenwunsch wurden die Bankdaten nicht gespeichert. Im E-Mail sind sie aber vorhanden.)";} ?></TD>
    </TR>
    </TABLE><BR>
<?php
      // Anzeige der Artikel der Bestellung:
      if ($myBestellung->artikelanzahl() == 0) {
          echo "<BR><H3>Diese Bestellung hat keine Artikel!</H3><BR>";
      }
      else {
          // (*** BETA ***) Hier muss die Funktion darstellenBestellung zuerst noch um ein
          // weiteres Flag ergaenzt werden um die Funktion berechneversandkosten auszuschalten
          // Dies, damit die Bestellung fuer immer statisch bleibt! Weiter muss die Tabelle
          // artikel_bestellung um den Namen und Einzelpreis des Artikels ergaenzt werden. Erst
          // so kann die Bestellung statisch, persistent gespeichert werden.
          darstellenBestellung($myBestellung, false, true);

      }
?>
    <INPUT TYPE=hidden NAME=darstellen VALUE=12>
    <INPUT TYPE=hidden NAME=Datum VALUE=<?php echo "$Datum"; ?>>
    <INPUT TYPE=hidden NAME=Bestellungs_ID VALUE=<?php echo $Bestellungs_ID; ?>>
    <INPUT TYPE=hidden NAME=Versandkosten VALUE=<?php echo "$Versandkosten"; ?>>
    <INPUT TYPE=hidden NAME=Mindermengenzuschlag VALUE=<?php echo "$Mindermengenzuschlag"; ?>>
    <INPUT TYPE=hidden NAME=Rechnungstotal VALUE=<?php echo "$Rechnungstotal"; ?>>
    <INPUT TYPE=hidden NAME=Datum VALUE=<?php echo "$Datum"; ?>>
    <INPUT TYPE=hidden NAME=Bestellungs_Datum VALUE=<?php echo "$Datum1"; ?>>
    <INPUT TYPE=hidden NAME=Bezahlungsart VALUE=<?php echo "$Bezahlungsart"; ?>>
    <INPUT TYPE=hidden NAME=Kunden_ID VALUE=<?php echo "$Kunden_ID"; ?>>
    <INPUT TYPE=hidden NAME=Kunden_Nr VALUE=<?php echo "$Kunden_Nr"; ?>>
    <INPUT TYPE=hidden NAME=temp VALUE=<?php echo "$temp"; ?>>
    <INPUT TYPE=hidden NAME=Einkaufsvolumen VALUE=<?php echo "$Einkaufsvolumen"; ?>>
    <INPUT TYPE=hidden NAME=Kunden_Session_ID VALUE=<?php echo "$Kunden_Session_ID"; ?>><BR>
    <INPUT type='image' src="../Buttons/bt_speichern_admin.gif" name='Speichern' value='Speichern'>
    <INPUT type='image' src="../Buttons/bt_loeschen_admin.gif" name='Loeschen' value='Loeschen'>
<?php
//<!--    <INPUT type='submit' name='Speichern' value='Speichern'>  -->
//<!--    <INPUT type='submit' name='Loeschen' value='Loeschen'>  -->
    if ($zurueck_button == "alle") {
        echo "<a href='$PHP_SELF?darstellen=13&order=$order'><img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='Abbrechen'></a><BR>";
    }
    else {
        echo '<a href="./SHOP_BESTELLUNG.php" ><img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen" align="absmiddle"></a><BR>';
    }
?>
    </FORM>

<?php
  } // end of if darstellen == 10

  // darstellen = 11. Bestaetigungsmeldung, beim Entfernen eines Artikels aus dem Warenkorb
  else if ($darstellen == 11){
    // Einen Artikel aus der Bestellung loeschen
    del_B_Artikel($FK_Artikel_ID,$FK_Bestellungs_ID,$Variation,$Optionen);

    // Kunden-Bestaetigungs-Meldung, Navigation
    echo '<h1>SHOP ADMINISTRATION</h1>';
    echo '<h3>Bestellungsmanagement</h3>';
    echo "<P>Der gew&auml;hlte Artikel wurde aus der Kundenbestellung entfernt!</P><BR><BR><BR>";
    echo '<a class="content" href="./SHOP_BESTELLUNG.php?darstellen=10&Referenz_Nr='.$Referenz_Nr.'" ><img src="../Buttons/bt_weiter_admin.gif" border="0"></a>'."\n";
  } // end of if darstellen == 11

  // Verarbeitung des Bestellungsformulars
  else if ($darstellen == 12){
    // Die aktuelle Bestellung loeschen oder sie updaten (abspeichern)
    if ($Speichern == "Speichern") {
        // Kundendaten updaten:
        // frei konfigurierbare Zusatzattribute in einen Array abfuellen
        for ($zaehl = 1; $zaehl <= count($Attr_name); $zaehl++){
            if ($Attr_speichern[$zaehl] == "Kunde"){
                $bezeichner = $Attr_name[$zaehl];
                $attr_namen[$zaehl] = rawurldecode($bezeichner);
                $attr_wert[$zaehl] = $$bezeichner;
            } // end of if
            else{
                $attr_wert[$zaehl] = "";
            } // end of else
        } // end of for

        $email = "E-Mail";
        $telefon = "Tel%2E";

        //Kundenattribute speichern (Bestellungsfelder werden weiter unten upgedated)
        updKundenFelder($Kunden_Session_ID, $Anrede, $Vorname, $Nachname, $Firma, $Abteilung, $Strasse,
                 $Postfach, $PLZ, $Ort, $Land, $$telefon, $Fax, $$email, $attr_namen[1], $attr_namen[2],
                 $attr_namen[3], $attr_namen[4], $attr_wert[1], $attr_wert[2], $attr_wert[3], $attr_wert[4], $Kunden_ID);

        // Bestellungsobjekt holen
        $myBestellung = getBestellung_Ref(bestellungs_id_to_ref($Bestellungs_ID));

        // Bestellungsattribute aufbereiten (Bestellung ergaenzen) und speichern:
        // Nur Kreditkartendaten werden an dieser Stelle noch nicht gespeichert
        // Aktuelles Datum berechnen (wird dem Mailheader angehaengt)
        $mydate = getdate();
        $myBestellung->Datum = $Bestellungs_Datum;
        $myBestellung->Anmerkung = $Bemerkungen;
        $myBestellung->Bezahlungsart = $Bezahlungsart;
        $Bestellungs_Session_ID = $myBestellung->Session_ID;

        // frei konfigurierbare Zusatzattribute in einen Array abfuellen
        for ($zaehl = 1; $zaehl <= count($Attr_name); $zaehl++){
            if ($Attr_speichern[$zaehl] == "Bestellung"){
                $bezeichner = $Attr_name[$zaehl];
                $attr_namen[$zaehl] = rawurldecode($bezeichner);
                $attr_wert[$zaehl] = $$bezeichner;
            } // end of if
            else{
                $attr_wert[$zaehl] = "";
                $attr_namen[$zaehl] = "";
            } // end of else
        } // end of for

        $myBestellung->Attribut1 = $attr_namen[1];
        $myBestellung->Attribut2 = $attr_namen[2];
        $myBestellung->Attribut3 = $attr_namen[3];
        $myBestellung->Attribut4 = $attr_namen[4];
        $myBestellung->Attributwert1 =  $attr_wert[1];
        $myBestellung->Attributwert2 =  $attr_wert[2];
        $myBestellung->Attributwert3 =  $attr_wert[3];
        $myBestellung->Attributwert4 =  $attr_wert[4];
        // Bestellungsfelder updaten
        updBestellungsFelder($Bestellungs_Session_ID, $myBestellung);

        // Falls der Kunde mit Lastschrift bezahlt hat und diese Bankdaten auch gespeichert wurden, so konnte sie der Administrator
        // in der Maske sehen und veraendern. Wir muessen die Einstellungen jetzt noch gesondert speichern.
        if ($Bezahlungsart == "Lastschrift" && $bankdaten_speichern == "Y") {
            set_kunden_bankdaten($Kunden_Session_ID, $kontoinhaber, $bankname, $blz, $kontonummer, $bankdaten_speichern,true);
        }
    }
    else {
      /* Fuer zukuenftiges Kundenmanagement: folgendes kopieren um Kunde zu loeschen:
        // Kunde und seine Bestellungen loeschen:
        // Alle Bestellungen des Kunden loeschen
        delBestellung_von_Kunde($Kunden_ID);
        // Kunde selbst loeschen
        delKunde($Kunden_ID);
      */
      // Bestellung und ihre Referenz auf den Kunden loeschen:
      delBestellung($Bestellungs_ID);
      // Wenn es sich um einen temporaeren Kunden gehandelt hat,
      // so soll dieser jetzt auch geloescht werden:
      if ($temp == "Y") {
          delKunde($Kunden_ID);
      }
    }
    // Kunden-Bestaetigungs-Meldung, Navigation
    echo '<h1>SHOP ADMINISTRATION</h1>';
    echo '<h3>Bestellungsmanagement</h3>';
    echo "<P>";
    if ($Speichern == "Speichern") {
        echo "Die Änderungen wurden gespeichert!";
    }
    else {
        echo "Die Bestellung wurde gel&ouml;scht!";
    }
    echo "</P><BR><BR><BR>";
    echo '<a class="content" href="./SHOP_BESTELLUNG.php" ><img src="../Buttons/bt_weiter_admin.gif" border="0"></a>'."\n";
  } // end of if darstellen == 12

  // darstellen = 13
  else if ($darstellen == 13){
    // Es wurde gewaehlt: Alle Bestellungen anzeigen, hier gibt es eine Auswahlliste die angezeigt wird:
    echo '<h1>SHOP ADMINISTRATION</h1>';
    echo '<h3>Bestellungsmanagement</h3>';
    echo "<P><B>Liste aller abgeschlossenen, aber noch nicht gel&ouml;schten Bestellungen</B><BR><BR>W&auml;hlen Sie eine Bestellung zum Bearbeiten aus, indem Sie z.B. auf den Namen der Person klicken:</P>";

    // Alle abgeschlossenen (noch nicht geloeschten) Bestellungen auslesen:
    // (Sortieren der Tabelle, je nach Sortierkriterium $order)
    $Bestellungsarray = getBestellung_Alle($order);

    // Zu jeder erhaltenen Bestellung die Kundeninformationen beschaffen und in einen Array schreiben
    $leerflag = true; // Ist true, wenn keine abgeschlossene Bestellung gefunden wurde
    if (count($Bestellungsarray) > 0) {
        foreach ($Bestellungsarray as $value) {
            $Kundenarray[] = getKunde_einer_Bestellung($value->Bestellungs_ID);
            $leerflag = false;
        }// End foreach
    }// End for

    // Darstellung der Resultate als Tabelle
?>
    <table border='0' cellpadding='0' cellspacing='10'>
      <tr>
        <td>
          <B><a style="text-decoration:none" href='<?php echo $PHP_SELF; ?>?darstellen=13&order=Datum'>Bestelldatum</a></B>
        </td>
        <td>
          <B>Nachname</B>
        </td>
        <td>
          <B>Vorname</B>
        </td>
        <td>
          <B><a style="text-decoration:none" href='<?php echo $PHP_SELF; ?>?darstellen=13&order=Bestellungs_ID'>Referenz Nr.</a></B>
        </td>
      </tr>
<?php
    if ($leerflag == false) {
     for($i=0;$i < count($Bestellungsarray);$i++) {
     $Bestellungs_ID = $Bestellungsarray[$i]->Bestellungs_ID;
      $Referenz_Nr = bestellungs_id_to_ref($Bestellungs_ID);
?>
      <tr>
        <td align="right">
          <a style="text-decoration:none" href='<?php echo $PHP_SELF; ?>?darstellen=10&order=<?php echo $order; ?>&zurueck_button=alle&Referenz_Nr=<?php echo $Referenz_Nr; ?>'>
<?php
             // amerikanisches Datumsformat in europaeisches umwandeln und ausgeben
             $temp_datum = explode("-", $Bestellungsarray[$i]->Datum);
             echo "$temp_datum[2].$temp_datum[1].$temp_datum[0]</a>\n";
?>
        </td>
        <td>
          <a style="text-decoration:none" href='<?php echo $PHP_SELF; ?>?darstellen=10&order=<?php echo $order; ?>&zurueck_button=alle&Referenz_Nr=<?php echo $Referenz_Nr; ?>'><?php echo $Kundenarray[$i]->Nachname; ?></a>
        </td>
        <td>
          <a style="text-decoration:none" href='<?php echo $PHP_SELF; ?>?darstellen=10&order=<?php echo $order; ?>&zurueck_button=alle&Referenz_Nr=<?php echo $Referenz_Nr; ?>'><?php echo $Kundenarray[$i]->Vorname; ?></a>
        </td>
        <td align="right">
          <a style="text-decoration:none" href='<?php echo $PHP_SELF; ?>?darstellen=10&order=<?php echo $order; ?>&zurueck_button=alle&Referenz_Nr=<?php echo $Referenz_Nr; ?>'><?php echo $Referenz_Nr; ?></a>
        </td>
      </tr>
<?php
     }// End for
    }// End if leerflag == false
    else {
        echo "<tr><td colspan=4><I>Es wurden keine abgeschlossenen Bestellungen gefunden</I></td></tr>";
    }// End else leerflag == false
    echo "</table>\n";
    echo "<BR><a class='content' href='$PHP_SELF' ><img src='../Buttons/bt_zurueck_admin.gif' border='0' alt='Zurueck'></a>\n";
  } // end of if darstellen == 13


  // else
  // Suchmaske fuer Bestellungen anzeigen
  else {
?>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>Bestellungsmanagement</h3>
    <P>Sie haben das Bestellungsmanagement aktiviert. Die abgeschlossenen Bestellungen werden jetzt nicht mehr gel&ouml;scht. Sie müssen Sie von Hand löschen. Daf&uuml;r k&ouml;nnen Sie die Bestellungen bequem und &uuml;berall verwalten.<BR>
    ACHTUNG: Die Artikelpreise in den Bestellungen sind die aktuellen Shop-Preise, wenn ein Artikelpreis &auml;ndert, &auml;ndern also auch die Preise in der jeweiligen Bestellung!
    Wir haben deshalb das Bestellungsmanagement vorerst deaktiviert.
    </P>
    <B>Bestellungen suchen:</B><BR><BR>
    <form action='./SHOP_BESTELLUNG.php' method="post" title="Bestellungs_Suchmaske">
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td>
                    Referenz Nr : <BR><BR>
<?php /*            Nachname  : <BR>
                    Vorname   : <BR>
*/?>
                </td>
                <td>
                    &nbsp;<INPUT type='text' name='Referenz_Nr' size='16' maxlength='32' value=''>&nbsp;(Die Referenz-Nummer steht im E-Mail einer Bestellung)<BR><BR>
<?php /*            &nbsp;<INPUT type='text' name='Nachname' size='16' maxlength='32' value=''><BR>
                    &nbsp;<INPUT type='text' name='Vorname' size='16' maxlength='32' value=''><BR>
*/?>
                </td>
            </tr>
            <tr>
                <td colspan=2>
<?php
echo "<a class=content href='$PHP_SELF?darstellen=13'>Abgeschlossene Bestellungen verwalten</a> <BR>";
?>
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
                <td>
                    <BR>
                    <input type=image src="../Buttons/bt_suchen_admin.gif" border="0" align="top">
                    <a href="./Shop_Einstellungen_Menu_1.php" ><img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen" align="absmiddle"></a>
                    <INPUT type='hidden' name='darstellen' value='10'>
                    <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Bestellung')">
                        <img src='../Buttons/bt_hilfe_admin.gif' border='0' align='absmiddle' alt='Hilfe'></a>
                </td>
            </tr>
        </table>
    </form>
<?php
  } // end of else
  echo "  </BODY>";
  echo "</HTML>";

// End of file ----------------------------------------------------------
?>
