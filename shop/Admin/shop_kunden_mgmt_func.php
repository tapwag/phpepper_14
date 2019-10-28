<?php
// Filename: shop_kunden_mgmt_func.php
//
// Modul: Funktions- und Aufruf-Module - SHOP_ADMINISTRATION (Kundenmanagement)
//
// Autoren: José Fontanil & Reto Glanzmann
//
// Zweck: Enthaelt die Funktionen fuer die Datei shop_kunden_mgmt.php
//
// Sicherheitsstatus:        *** ADMIN ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: shop_kunden_mgmt_func.php,v 1.30 2003/08/11 11:38:28 glanzret Exp $
//
// -----------------------------------------------------------------------
// Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
// wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
$SHOP_KUNDEN_MGMT_FUNC = true;

// include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
// Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
// Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

// Einbinden der benoetigten Module (PHP-Scripts)
// Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
if (!isset($ADMIN_Database)) {include("ADMIN_initialize.php");}
if (!isset($kundenliste_def)) {include("kundenliste_def.php");}
if (!isset($bestellungsliste_def)) {include("bestellungsliste_def.php");}

//----------------------------------------------------------------------------
// Zweck: Im folgenden Abschnitt werden die in diesem Modul benoetigten SQL-Queries definiert.
//        Weiter SQL-Queries finden sich in den Dateien ../USER_SQL_BEFEHLE.php und ADMIN_SQL_BEFEHLE.php.
// Variabelnamenaufbau: sql_ NAME DES SCRIPTS _suffix ... suffix = hochzaehlende Zahl
//        (Falls eine Query aus mehreren hier definierten Variablen zusammengesetzt wird, so
//        ist der suffix nochmals angehaengt und dort eine Laufvariable eingesetzt worden).
//----------------------------------------------------------------------------
$sql_getBestellung_ID_1 = "SELECT Bestellung_string FROM bestellung WHERE Bestellungs_ID=";
//-----------------------------------------------------------------------
$sql_delKunde_und_Bestellungen_1_1 = "DELETE FROM kunde WHERE Kunden_ID='";
$sql_delKunde_und_Bestellungen_1_2 = "'";
$sql_delKunde_und_Bestellungen_1_3 = "SELECT FK_Bestellungs_ID FROM bestellung_kunde WHERE FK_Kunden_ID='";
$sql_delKunde_und_Bestellungen_1_4 = "DELETE FROM bestellung_kunde WHERE FK_Kunden_ID='";
$sql_delKunde_und_Bestellungen_1_5 = "DELETE FROM bestellung WHERE Bestellungs_ID=";
$sql_delKunde_und_Bestellungen_1_6 = "DELETE FROM artikel_bestellung WHERE FK_Bestellungs_ID=";
//-----------------------------------------------------------------------
$sql_getKunden_Beschreibung_1_1 = "SELECT Beschreibung FROM kunde WHERE Kunden_ID='";
$sql_getKunden_Beschreibung_1_2 = "'";
//-----------------------------------------------------------------------
$sql_setKunden_Beschreibung_1_1 = "UPDATE kunde SET Beschreibung='";
$sql_setKunden_Beschreibung_1_2 = "' WHERE Kunden_ID='";
$sql_setKunden_Beschreibung_1_3 = "'";
//-----------------------------------------------------------------------
$sql_newKundeAdmin_1_1 = "INSERT INTO kunde (";
$sql_newKundeAdmin_1_2 = "','";
$sql_newKundeAdmin_1_3 = ",";
$sql_newKundeAdmin_1_4 = ",'";
$sql_newKundeAdmin_1_5 = "',";
$sql_newKundeAdmin_1_6 = ") VALUES (";
$sql_newKundeAdmin_1_7 = ")";
//-----------------------------------------------------------------------
$sql_updKundeAdmin_1_1 = "UPDATE kunde SET ";
$sql_updKundeAdmin_1_2 = "='";
$sql_updKundeAdmin_1_3 = ", ";
$sql_updKundeAdmin_1_4 = "=";
$sql_updKundeAdmin_1_5 = "'";
$sql_updKundeAdmin_1_6 = " WHERE Kunden_ID='";
$sql_updKundeAdmin_1_7 = "'";
//-----------------------------------------------------------------------
$sql_get_Bestellung_string_1_1 = "SELECT Bestellung_string FROM bestellung WHERE Bestellungs_ID=";
//-----------------------------------------------------------------------
// End of SQL-Variablendefinitionen fuer das Modul Kundenmanagement

//----------------------------------------------------------------------------
// Funktion.: kd_mgmt_header
// Zweck....: Gibt die Headerinformationen an den Browser aus
// Argumente: keine
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_header(){
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
    echo "<html>\n";
    echo "<head>\n";
    echo "  <title>Kundenmanagement</title>\n";
    echo "  <meta HTTP-EQUIV=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">\n";
    echo "  <meta HTTP-EQUIV=\"language\" CONTENT=\"de\">\n";
    echo "  <meta HTTP-EQUIV=\"author\" CONTENT=\"Jose Fontanil & Reto Glanzmann\">\n";
    echo "  <meta NAME=\"robots\" CONTENT=\"all\">\n";
    echo "  <link REL=STYLESHEET HREF=\"./shopstyles.css\" TYPE=\"text/css\">\n";
    echo "</head>\n";
    echo "<body>\n";
} // end of function kd_mgmt_header


//----------------------------------------------------------------------------
// Funktion.: kd_mgmt_abclinks
// Zweck....: Erzeugt eine Link-Zeile A B C D ..
// Argumente:
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_abclinks($sortieren_nach, $abc){
    echo "<table width=\"100%\" border=\"1\" rules=\"none\" bgcolor=\"#CCCCCC\" cellspacing=\"0\" cellpadding=\"4\">\n";
    echo "  <tr>\n";
    // <form>Tag vor <td> entspricht nicht dem W3C-Konventionen, muss aber wegen dem besch*** InternetExplorer gemacht werden,
    // da dieser nach jedem Formular noch eine Leerzeile einfuegt!
    echo "    <form action=\"".SELF."\" method=\"post\" name=\"sortieren_nach\">\n";
    echo "      <td valign=\"middle\">\n";
    echo "        <b>Liste nach&nbsp;</b>\n";
    echo "        <select name=\"sortieren_nach\" size=\"1\" onChange=\"self.location=this.options[this.selectedIndex].value; return true;\">\n";
    echo "          <option ";
    if ($sortieren_nach == "Nachnamen" || $sortieren_nach == "") {
        echo " selected ";
    }
    echo "value=\"".$SELF."?darstellen=liste&amp;sortieren_nach=Nachnamen&amp;abc=".$abc."\">Nachnamen</option>\n";
    echo "          <option ";
    if ($sortieren_nach == "Firmennamen") {
        echo " selected ";
    }
    echo "value=\"".$SELF."?darstellen=liste&amp;sortieren_nach=Firmennamen&amp;abc=".$abc."\">Firmennamen</option>\n";
    echo "        </select>\n";
    echo "        <b>&nbsp;sortiert</b>\n";
    echo "      </td>\n";
    echo "    </form>\n";
    echo "    <td align=\"right\">";
    echo "      <button type=\"button\" onClick=\"self.location.href='".SELF."?darstellen=neukunde&amp;sortieren_nach=".$sortieren_nach."&amp;abc=".$abc."'\">neuen Kunden erfassen</button>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td colspan=\"2\"valign=\"middle\">\n";

    // [A] [B] [C] .. [Z]
    for ($i="A";$i<>"AA";$i++) {
        echo "<a class=\"no_decoration\" href=\"".SELF."?sortieren_nach=".$sortieren_nach."&amp;abc=".$i."\">[ ";
        // falls der Link aktiv ist
        if ($abc == $i) {
            echo "<b>$i</b>";
        }
        else {
            echo $i;
        }
        echo " ]</a>&nbsp;";
    } // end of for

    // [0..9]
    echo "<a class=\"no_decoration\"  href=\"".SELF."?sortieren_nach=".$sortieren_nach."&amp;abc=num\">[ ";
    if ($abc == "num") {
        echo "<b>0..9</b>";
    }
    else {
        echo "0..9";
    }
    echo " ]</a>&nbsp;";

    // [alle]
    echo "&nbsp;<a  class=\"no_decoration\" href=\"".SELF."?sortieren_nach=".$sortieren_nach."&amp;abc=alle\">[ ";
    if ($abc == "alle") {
        echo "<b>alle</b>";
    }
    else {
        echo "alle";
    }
    echo "] </a>&nbsp;";

    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table>\n";
} // end of function kd_mgmt_abclinks

//----------------------------------------------------------------------------
// Funktion.: kd_mgmt_suche
// Zweck....: gibt eine Zeile mit Suchmoeglichkeit nach Kundennummer oder
//            Bestellungsreferenz aus
// Argumente: keine
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_suche(){
    $td_height = "height=\"15px\"";
    echo "<table width=\"100%\" border=\"1\" rules=\"none\" bgcolor=\"#CCCCCC\" cellspacing=\"0\" cellpadding=\"4\">\n";
    echo "  <tr>\n";
    echo "    <td ".$td_height." valign=\"middle\">\n";
    echo "      <b>Suchen nach:</b>\n";
    echo "    </td>\n";
    echo "    <td ".$td_height." width=\"20\">\n";
    echo "      &nbsp;\n";
    echo "    </td>\n";
    echo "    <td ".$td_height." valign=\"middle\">\n";
    echo "      Kundennummer&nbsp;\n";
    echo "    </td>\n";
    // <form>Tag vor <td> entspricht nicht dem W3C-Konventionen, muss aber wegen dem besch*** InternetExplorer gemacht werden,
    // da dieser nach jedem Formular noch eine Leerzeile einfuegt!
    echo "      <form action=\"".SELF."\" method=\"post\" name=\"suche_kunde\">\n";
    echo "        <td ".$td_height." valign=\"middle\">\n";
    echo "          <input type=\"text\" size=\"20\" maxlength=\"128\" name=\"suchstring\" value=\"\">\n";
    echo "          <input type=\"hidden\" name=\"darstellen\" value='suche'>\n";
    echo "          <input type=\"hidden\" name=\"was\" value='kundennr'>\n";
    echo "          <input class=\"no_decoration\" type='submit' VALUE='Los'>\n";
    echo "        </td>\n";
    echo "      </form>\n";
    echo "    <td ".$td_height." width=\"20\">\n";
    echo "      &nbsp;\n";
    echo "    </td>\n";
    echo "    <td ".$td_height." valign=\"middle\">\n";
    echo "      Bestellungsreferenz&nbsp;\n";
    echo "    </td>\n";
    echo "      <form action=\"".SELF."\" method=\"post\" name=\"suche_bestellung\">\n";
    echo "      <td ".$td_height." valign=\"middle\">\n";
    echo "        <input type=\"text\" size=\"20\" maxlength=\"128\" name=\"suchstring\" value=\"\">\n";
    echo "        <input type=\"hidden\" name=\"darstellen\" value='suche'>\n";
    echo "        <input type=\"hidden\" name=\"was\" value='bestellungsref'>\n";
    echo "        <input class=\"no_decoration\" type='submit' VALUE='Los'>\n";
    echo "      </td>\n";
    echo "    </form>\n";
    echo "  </tr>\n";
    echo "</table>\n";
} // end of function kd_mgmt_suche


//----------------------------------------------------------------------------
// Funktion.: kd_mgmt_kdliste
// Zweck....: gibt eine Uebersichtstabelle mit Kunden eines Anfangsbuchstaben
//            Wahlweise sortiert nach Firmenbezeichnung oder Name aus.
// Argumente: $sortieren_nach -> Sortierargument 'Nachname' oder 'Firmenname'
//            $abc -> Anfangsbuchstaben der Adressen, die Angezeigt werden
//            $par -> Tabellendarstellungsparameter
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_kdliste($sortieren_nach, $abc, $par){

    // Kundenlistenobjekt instanzieren und Suche starten
    $myKundenliste = new Kundenliste;
    $myKundenliste->get_kunden_liste($sortieren_nach, $abc);

    // Tabellenheader ausgeben
    switch ($sortieren_nach) {
        case "Firmennamen": $wer="Firmenkunden "; break;
        default: $wer="Kunden "; break;
    } // end of switch
    switch ($abc){
    case "num":
        $pre = "";
        $post = "'0..9'";
        break;
    case "alle":
        $pre = "Alle ";
        $post = "";
        break;
    default:
        $pre = "";
        $post = "mit dem Anfangsbuchstaben '$abc'";
        break;
    } // end of switch
    echo "<b>".$pre.$wer.$post."</b>";

    echo "<table width=\"100%\" border=\"1\" rules=\"rows\" bgcolor=\"#CCCCCC\" cellspacing=\"0\" cellpadding=\"2\">\n";

    // Falls keine Kunden gefunden wurden, eine Hinweismeldung ausgeben
    if(empty($myKundenliste->kunden_id_array)) {
        echo "  <tr>\n";
        echo "    <td valign=\"middle\">\n";
        echo "    <b>Keine Einträge vorhanden!</b>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } // end of if

    // ..wenn die Suche Treffer erzielt hat
    else {
        echo "  <tr>\n";
        echo "    <td ".$par['td_valign_1']." align=\"center\" width=\"20px\">\n";
        echo "      <b>Status</b>\n";
        echo "    </td>\n";
        if ($sortieren_nach == "Firmennamen"){
            echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
            echo "      <b>Firma</b>\n";
            echo "    </td>\n";
            echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
            echo "      <b>Name</b>\n";
            echo "    </td>\n";
            echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
            echo "      <b>Vorname:</b>\n";
            echo "    </td>\n";
        } // end of if
        else{
            echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
            echo "      <b>Name</b>\n";
            echo "    </td>\n";
            echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
            echo "      <b>Vorname</b>\n";
            echo "    </td>\n";
            echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
            echo "      <b>Firma</b>\n";
            echo "    </td>\n";
        } // end of else
        echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
        echo "      <b>Land</b>\n";
        echo "    </td>\n";
        echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
        echo "      <b>Ort</b>\n";
        echo "    </td>\n";
        echo "    <td colspan=\"3\">\n";
        echo "    </td>\n";
        echo "  </tr>\n";

        // Tabellenzeilen (Treffer) ausgeben
        $zeilen_count = 0;
        for($i = 0; $i < $myKundenliste->get_anzahl_kunden(); $i++) {

            // Formatierungsangaben fuer Zeilen bestimmen
            $zeilen_count++;   // Zeilenzaehler inkrementieren
            if ($zeilen_count % 2 == 0){
                $td_bg = $par['td_bgcolor_ger_1'];
            }
            else{
                $td_bg = $par['td_bgcolor_ung_1'];
            }

            echo "  <tr>\n";

            // Status ausgeben
            echo "    <td ".$td_bg." ".$par['td_valign_1']." align=\"center\">\n";
            if (strtolower($myKundenliste->status_array[$i]) != "gesperrt"){
                echo "<img src=\"../Buttons/status_gruen.gif\" border=\"0\" alt=\"aktiv\">\n";
            }
            else {
                echo "<img src=\"../Buttons/status_rot.gif\" border=\"0\" alt=\"inaktiv\">\n";
            }
            echo "    </td>\n";

            // Firma, Name, Vorname
            if ($sortieren_nach == "Firmennamen"){
                echo "    <td ".$td_bg." ".$par['td_valign_1']." align=\"left\">\n";
                echo "      <div>".stripstring($myKundenliste->firma_array[$i], 15, "..")."</div>\n";
                echo "    </td>\n";
                echo "    <td ".$td_bg." ".$par['td_valign_1']." align=\"left\">\n";
                echo "      <div>".stripstring($myKundenliste->name_array[$i], 15, "..")."</div>\n";
                echo "    </td>\n";
                echo "    <td ".$td_bg." ".$par['td_valign_1']." align=\"left\">\n";
                echo "      <div>".stripstring($myKundenliste->vorname_array[$i], 15, "..")."</div>\n";
                echo "    </td>\n";
            } // end of if
            // Name, Vorname, Firma
            else{
                echo "    <td ".$td_bg." ".$par['td_valign_1']." align=\"left\">\n";
                echo "      <div>".stripstring($myKundenliste->name_array[$i], 15, "..")."</div>\n";
                echo "    </td>\n";
                echo "    <td ".$td_bg." ".$par['td_valign_1']." align=\"left\">\n";
                echo "      <div>".stripstring($myKundenliste->vorname_array[$i], 15, "..")."</div>\n";
                echo "    </td>\n";
                echo "    <td ".$td_bg." ".$par['td_valign_1']." align=\"left\">\n";
                echo "      <div>".stripstring($myKundenliste->firma_array[$i], 15, "..")."</div>\n";
                echo "    </td>\n";
            } // end of else

            echo "    <td ".$td_bg." ".$par['td_valign_1'].">\n";
            echo "      <div>".stripstring($myKundenliste->land_array[$i], 15, "..")."</div>\n";
            echo "    </td>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1'].">\n";
            echo "      <div>".stripstring($myKundenliste->ort_array[$i], 15, "..")."</div>\n";
            echo "    </td>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1']." width=\"25\">\n";
            echo "        <button type=\"button\" onClick=\"self.location.href='".SELF."?darstellen=kd_bearb&amp;kd_id=".$myKundenliste->kunden_id_array[$i]."&amp;sortieren_nach=".$sortieren_nach."&amp;abc=".$abc."'\">Bearbeiten</button>\n";
            echo "    </td>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1']." width=\"25\">\n";
            echo "        <button type=\"button\" onClick=\"self.location.href='".SELF."?darstellen=kunde_bestellungen&amp;Kunden_ID=".$myKundenliste->kunden_id_array[$i]."&amp;sortieren_nach=".$sortieren_nach."&amp;abc=".$abc."'\">Bestellungen</button>\n";
            echo "    </td>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1']." width=\"25\">\n";
            $warn_string = "Wollen Sie diesen Kunden wirklich L&Ouml;SCHEN? \\n !!! Die Kundendaten und alle Bestellungen werden dabei unwiederruflich gel&ouml;scht !!!";
            $del_link = SELF."?darstellen=kd_loesch&amp;kd_id=".$myKundenliste->kunden_id_array[$i]."&amp;sortieren_nach=".$sortieren_nach."&amp;abc=".$abc;
            echo "      <button type=\"button\" onClick=\"javascript: if(confirm('".$warn_string."')) { self.location.href='".$del_link."'; }\">L&ouml;schen</button>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        } // end of for
    } // end of else
    echo "</table>\n";
} // end of function kd_mgmt_kdliste


//----------------------------------------------------------------------------
// Funktion.: kd_mgmt_kunde_edit
// Zweck....: Gibt alle Kundendaten in einem Formular zum Bearbeiten aus
// Argumente: $myKunde -> Kundendatenobjekt
//            $sortieren_nach -> Wie die Liste in der letzten Ansicht sortiert war
//            $abc -> nach welchem Buchstaben die Liste sortiert war
// optional.: $probleme -> enthält einen Problemstring zum Ausgeben, wenn beim
//                        Abspeichern des Formulars ein Problem aufgetreten ist
//            $Passwort2 -> enthaelt im Problemfall das Kontrollpasswort
//            $beschr -> enthaelt die Kundenbeschreibung im Problemfall
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_kunde_edit($myKunde, $sortieren_nach, $abc, $probleme="", $Passwort2="", $beschr=""){

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

    // hier werden in einem Array die Anzeigelaengen für die Eingabefelder gespeichert
    // [0] = Anrede (wird natürlich nicht gebraucht) [1] = Vorname, usw.
    $laenge_array = array('0', '40', '40', '40', '40', '40', '10', '10', '40', '20', '16', '16', '40', '40', '40', '40', '40', '0');

    // hier werden in einem Array die maximalen Eingabelaengen für die Eingabefelder gespeichert
    // [0] = Anrede (wird natürlich nicht gebraucht) [1] = Vorname, usw.
    $max_array = array('0', '128', '128', '128', '128', '128', '16', '32', '128', '128', '32', '32', '128', '128', '128', '128', '128', '0');

    // Kunden-Daten in einen Array abfuellen
    $daten_array = array($myKunde->Anrede, $myKunde->Vorname, $myKunde->Nachname, $myKunde->Firma,
                         $myKunde->Abteilung, $myKunde->Strasse, $myKunde->Postfach, $myKunde->PLZ,
                         $myKunde->Ort, $myKunde->Land, $myKunde->Tel, $myKunde->Fax, $myKunde->Email,
                         $myKunde->Attributwert1, $myKunde->Attributwert2, $myKunde->Attributwert3,
                         $myKunde->Attributwert4, $myBestellung->Anmerkung);


    // JavaScript zur Ueberpruefung, ob mindestens ein Benutzername und Passwort angegeben wurde
    echo "<script language=\"JavaScript\">\n";
    echo "<!--\n";
    echo "\n";
    echo "function chkFormular() {\n";
    echo "\n";
    echo "      Benutzername = document.Formular.Login.value;\n";
    echo "      Passwort = document.Formular.Passwort.value;\n";
    echo "      Passwort2 = document.Formular.Passwort2.value;\n";
    echo "      Nachname = document.Formular.Nachname.value;\n";
    echo "      Firma = document.Formular.Firma.value;\n";
    echo "\n";
    echo "      // ueberpruefen, ob der Benutzername mindestes 4 Zeichen hat\n";
    echo "      if(Benutzername.length < 4) {\n";
    echo "          alert(\"Benutzername muss mindestens 4 Zeichen haben!\");\n";
    echo "          document.Formular.Login.focus();\n";
    echo "          return false;\n";
    echo "      }\n";
    echo "\n";
    echo "      // ueberpruefen, ob der Benutzername mindestes 4 Zeichen hat\n";
    echo "      if(Nachname == \"\" && Firma == \"\") {\n";
    echo "          alert(\"Bitte geben Sie eine Nachnamen oder einen Firmennamen ein\");\n";
    echo "          document.Formular.Nachname.focus();\n";
    echo "          return false;\n";
    echo "      }\n";
    echo "\n";
    // Die Eingabe des Passwortes wird nur ueberprueft, wenn der Kunde neu angelegt wird
    if (empty($myKunde->Kunden_ID)) {
        echo "      if(Benutzername != \"".$no_login_string."\") {\n";
        echo "          // ueberpruefen, ob der Passwort mindestes 6 Zeichen hat\n";
        echo "          if(Passwort.length < 6) {\n";
        echo "              alert(\"Passwort muss mindestens 6 Zeichen haben!\");\n";
        echo "              document.Formular.Passwort.focus();\n";
        echo "              return false;\n";
        echo "          }\n";
        echo "\n";
        echo "          // falls das Kontrollpasswort nicht eingegeben wurde\n";
        echo "          if(Passwort.length > 5 && Passwort2.length < 4) {\n";
        echo "             alert(\"Bitte geben Sie zur Kontrolle das Passwort auch im zweiten Passwortfeld ein!\");\n";
        echo "             document.Formular.Passwort2.focus();\n";
        echo "             return false;\n";
        echo "          }\n";
        echo "\n";
        echo "          // falls nur ein Benutzername eingegeben wurde\n";
        echo "          if(Benutzername.length > 3 && Passwort.length < 6) {\n";
        echo "              alert(\"Bitte geben Sie ein Passwort ein!\");\n";
        echo "              document.Formular.Passwort.focus();\n";
        echo "              return false;\n";
        echo "          }\n";
        echo "      } // end of if\n";
        echo "\n";
   } // end of if
    echo "      // falls nur ein Passwort eingegeben wurde\n";
    echo "      if(Passwort.length > 5 && Benutzername.length < 4) {\n";
    echo "          alert(\"Bitte geben Sie einen Benutzernamen ein!\");\n";
    echo "          document.Formular.Login.focus();\n";
    echo "          return false;\n";
    echo "      }\n";
    echo "\n";
    echo "      // falls das Kontrollpasswort nicht mit den Hauptpasswort uebereinstimmt\n";
    echo "      if(Passwort !=  Passwort2) {\n";
    echo "          alert(\"Bitte geben Sie in beiden Passwortfeldern das gleiche Passwort ein!\");\n";
    echo "          document.Formular.Passwort2.focus();\n";
    echo "          return false;\n";
    echo "      }\n";
    echo "\n";
    echo "} // end of function chkFormular()\n";
    echo "//-->\n";
    echo "</script>\n";

    echo "<form action=\"".SELF."\" onSubmit=\"return chkFormular()\" method=\"post\" name=\"Formular\">\n";
    echo "<table width=\"100%\" border=\"1\" rules=\"none\" bgcolor=\"#CCCCCC\" cellspacing=\"0\" cellpadding=\"4\">\n";

    // Probleme ausgeben, falls welche aufgetreten sind (beim Abspeichern)
    if (!empty($probleme)) {
        echo "<tr>\n";
        echo "  <td colspan=\"2\">";
        if (count($probleme <= 1)) {
            echo "<b style=\"color:#FF0000;\">Folgendes Problem ist beim Abspeichern aufgetreten:</b>";
        }
        else {
            echo "<b style=\"color:#FF0000;\">Folgende Probleme sind beim Abspeichern aufgetreten:</b>";
        }
        echo "    <ul>";
        foreach ($probleme as $problem) {
            echo "    <li style=\"color:#FF0000;\">".$problem;
        }
        echo "    </ul>";
        echo "  </td>";
        echo "</tr>\n";
    }

    // Eingabefelder fuer Benutzername, Passwort und Kundennummer
    echo "<tr>\n";
    echo "  <td colspan=\"2\"><b>Benutzerdaten:</b></td>";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "  <td>Login*:</td>";
    echo "  <td><input type=text name=\"Login\" maxlength=\"50\" size=\"30\" value=\"";
    if ($myKunde->Login == $myKunde->Kunden_ID && !empty($myKunde->Kunden_ID)) {
        $no_login_string = "Kein Login definiert";
        echo $no_login_string;
    }
    // bei Neukunden einen Leerstring als Login ausgeben
    elseif (empty($myKunde->Kunden_ID)) {
        // nichts ausgeben
        echo "";
    }
    else {
        echo htmlspecialchars($myKunde->Login);
    }
    echo "\">\n";
    echo " (mindestens 4 Zeichen)</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "  <td>Passwort*:</td>";
    echo "  <td><input type=password name=\"Passwort\" maxlength=\"30\" size=\"30\" value=\"";
    // das Passwort wird nur als value ausgegeben, wenn das Formular wegen eines Eingabefehlers
    // noch einmal angezeigt werden muss. In diesem Fall existiert natürlich ein Passwort2
    if ($Passwort2 != "") {
        echo $Passwort2;
    }
    echo "\">\n";
    echo " (mindestens 6 Zeichen)</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "  <td>Passwort*:</td>";
    echo "  <td><input type=password name=\"Passwort2\" maxlength=\"30\" size=\"30\" value=\"";
    if ($Passwort2 != "") {
        echo $Passwort2;
    }
    echo "\">";
    echo "  (Passwort wiederholen)</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "  <td>Einkaufstatus:</td>";
    echo "  <td><select style=\"font-family: Courier, Courier New, Monaco\" name=\"gesperrt\">\n";
    echo "  <option ";
    if ($myKunde->gesperrt != "gesperrt") { echo "selected "; }
    echo "value='freigeschaltet'>Kunde freigeschaltet\n";
    echo "  <option ";
    if ($myKunde->gesperrt == "gesperrt") { echo "selected "; }
    echo "value='gesperrt'>Kunde gesperrt\n";
    echo "  </select>\n";
    echo "  </td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "  <td>Kundennummer:</td>";
    echo "  <td><input type=text name=\"Kunden_Nr\" maxlength=\"50\" size=\"30\" value=\"";
    if ($myKunde->Kunden_Nr != "0"){
        echo htmlspecialchars($myKunde->Kunden_Nr);
    }
    echo "\"></td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "  <td colspan=\"2\"><b>Adressdaten:</b></td>";
    echo "</tr>\n";

    $attr_cnt = 1; // Attributcounter, fuer Attributindexierung der Zusatzattribute

    // Hauptfelder ausgeben
    for ($zaehl = 0; $zaehl <= ($gesamt-1); $zaehl++){
        // nur Felder ausgeben, die der Shopbetreiber auch aktiviert hat
        if($verwenden[$zaehl] == 'Y'){

            // Anrede-Dropdown-Liste ausgeben..
            if ($zaehl == 0){
                echo "<tr>\n";
                // Feldbezeichnungsnamen ausgeben
                echo "  <td>".$Namen[$zaehl].":";
                echo "</td><td>";
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
                echo "<tr>\n";
                // Feldbezeichnungsnamen ausgeben
                echo "  <td>".htmlspecialchars($Namen[$zaehl]).":";
                echo "</td>\n";
                $einsetzen = rawurlencode($Namen[$zaehl]);
                $einsetzen_1 = str_replace(".", "", $einsetzen);
                $einsetzen_2 = str_replace("-", "", $einsetzen_1);
                echo "<td><input type=text name=\"".$einsetzen_2."\" size='".$laenge_array[$zaehl]."' maxlength='".$max_array[$zaehl]."' value=\"".htmlspecialchars($daten_array[$zaehl])."\"></td>\n";
                echo "</tr>\n";
            } //

            // frei konfigurierbare Zusatzfelder ausgeben
            if ($zaehl > 12 && $zaehl < 17){
                if ($speichern[$zaehl] == "Y"){
                    echo "<tr>\n";
                    // Feldbezeichnungsnamen ausgeben
                    echo "  <td>".htmlspecialchars($Namen[$zaehl]).":";
                    echo "</td>\n";
                    $einsetzen = rawurlencode($Namen[$zaehl]);
                    $einsetzen_1 = str_replace(".", "", $einsetzen);
                    $einsetzen_2 = str_replace("-", "", $einsetzen_1);
                    $attributstring = "Attributwert".($zaehl-12);
                    echo "<td><input type=text name=\"Attributwert[".$attr_cnt."]\" size='".$laenge_array[$zaehl]."' maxlength='".$max_array[$zaehl]."' value=\"".htmlspecialchars($myKunde->$attributstring)."\"></td>\n";
                    echo "<input type=\"hidden\" name=\"Attribut[".$attr_cnt."]\" value=\"".$einsetzen."\">";
                    echo "</tr>\n";
                }
                $attr_cnt++;
            } //

        } // end of if $verwenden[$zaehl] == 'Y'
        // Attributzaehler fuer Zusatzattribute auch erhoehen, wenn 'verwenden' nicht 'Y'
        else {
            if ($zaehl > 12 && $zaehl < 17){
                $attr_cnt++;
            }
        }
    } // end of for $zaehl = 0; $zaehl <= ($anz_vordef-1); $zaehl++


    // Ausgabe der Bankdaten des Kunden, wenn dieser die Bankdaten gespeichert hat
    echo "<tr>\n";
    echo "  <td colspan=\"2\"><b>Bankdaten:</b></td>";
    echo "</tr>\n";

    echo "<tr><td>Kontoinhaber:</td><td><input type=text name=\"kontoinhaber\" size='32' value=\"".$myKunde->kontoinhaber."\"></td></tr>";
      echo "<tr><td>Name der Bank:</td><td><input type=text name=\"bankname\" size='32' value=\"".$myKunde->bankname."\"></td></tr>";
      echo "<tr><td>Bankleitzahl (BLZ):</td><td><input type=text name=\"blz\" size='32' value=\"".$myKunde->blz."\"></td></tr>";
      echo "<tr><td>Kontonummer:</td><td><input type=text name=\"kontonummer\" size='32' value=\"".$myKunde->kontonummer."\"><input type=hidden name=\"bankdaten_speichern\" size='32' value=\"".$myKunde->bankdaten_speichern."\"></td></tr>";

    // Bemerkungsfeld fuer Notizen zum Kunden
    echo "<tr>\n";
    echo "  <td colspan=\"2\"><b>Notizen:</b> (k&ouml;nnen vom Kunden nicht eingesehen werden)</td>";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "  <td  colspan=\"2\" style='font-family: Courier, Courier New, Monaco'><textarea style='font-family: Courier, Courier New, Monaco' name=\"beschr\" cols='60' rows='10' wrap=physical>";
    if ($beschr != ""){
        echo htmlentities(stripslashes($beschr));
    }
    elseif(!empty($myKunde->Kunden_ID)) {
        echo htmlentities(getKunden_Beschreibung($myKunde->Kunden_ID));
    } // end of if
    echo "</textarea></td>\n";
    echo "</tr>\n";

    echo "<tr><td colspan=\"2\" align=\"center\">\n";
    echo "  <input type=\"hidden\" name=\"darstellen\" value=\"kd_speich\">\n";
    echo "  <input type=\"hidden\" name=\"Kunden_ID\" value=\"".$myKunde->Kunden_ID."\">\n";
    echo "  <input type=\"hidden\" name=\"abc\" value=\"".$abc."\">\n";
    echo "  <input type=\"hidden\" name=\"sortieren_nach\" value=\"".$sortieren_nach."\">\n";
    echo "  <input class=\"no_decoration\" type=\"submit\" VALUE=\"Kunde speichern\">\n";
    // Wenn $sortieren_nach und $abc leer sind, wurde die Bearbeitungsmaske aus einer Kundenansicht (Kunde und seine Bestellungen) aus
    // aufgerufen. Der 'Abbrechen'-Button soll dann auch darauf zeigen
    if ($sortieren_nach == "" || $abc == ""){
        echo "  <button type=\"button\" onClick=\"self.location.href='".SELF."?darstellen=kunde_bestellungen&amp;Kunden_ID=".$myKunde->Kunden_ID."'\">Abbrechen</button>\n";
    } // end of if
    else {
        echo "  <button type=\"button\" onClick=\"self.location.href='".SELF."?sortieren_nach=".$sortieren_nach."&amp;abc=".$abc."'\">Abbrechen</button>\n";
    } // end of else
    echo "</td></tr>\n";
    echo "</table>";
} // end of function kd_mgmt_kunde_edit


//----------------------------------------------------------------------------
// Funktion.: kd_mgmgt_show_kunde
// Zweck....: Gibt die Informationen zu einem Kunden an den Browser aus
// Argumente: $myKunde -> Kundenobjekt
//            $par     -> Darstellungsparameterarray
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_show_kunde($myKunde, $par, $abc, $sortieren_nach) {
    echo "<b>Kundendaten:</b>\n";
    echo "<table width=\"100%\" border=\"1\" rules=\"all\" bgcolor=\"#CCCCCC\" cellspacing=\"0\" cellpadding=\"2\">\n";
    // erste Zeile
    echo "  <tr>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>E-Mail:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Email)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Login:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    if ($myKunde->Login == $myKunde->Kunden_ID) {
        $myKunde->Login = "Kein Login";
    } // end of if
    echo "      <div>".htmlentities($myKunde->Login)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Registriert:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities(date_us_to_eur($myKunde->AnmeldeDatum))."</div>\n";
    echo "    </td>\n";
    echo "  </tr>\n";

    // zweite Zeile
    echo "  <tr>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Anrede:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Anrede)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Name:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Nachname)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Vorname:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Vorname)."</div>\n";
    echo "    </td>\n";
    echo "  </tr>\n";

    // dritte Zeile
    echo "  <tr>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Firma:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Firma)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Abteilung:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Abteilung)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Land:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Land)."</div>\n";
    echo "    </td>\n";
    echo "  </tr>\n";

    // vierte Zeile
    echo "  <tr>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Strasse:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Strasse)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>PLZ:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->PLZ)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Ort:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Ort)."</div>\n";
    echo "    </td>\n";
    echo "  </tr>\n";

    // fuenfte Zeile
    echo "  <tr>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Telefon:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Tel)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Fax:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Fax)."</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Postfach:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Postfach)."</div>\n";
    echo "    </td>\n";
    echo "  </tr>\n";

    // sechste Zeile
    echo "  <tr>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Einkaufsstatus:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1']."><div>\n";
    if ($myKunde->gesperrt != "gesperrt") {
        echo "Kunde freigeschaltet\n";
    }
    else {
        echo "Kunde gesperrt!\n";
    }
    echo "      </div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ger_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>Kundennummer:</div>\n";
    echo "    </td>\n";
    echo "    <td ".$par['td_bgcolor_ung_1']." ".$par['td_align_1']." ".$par['td_valign_1'].">\n";
    echo "      <div>".htmlentities($myKunde->Kunden_Nr)."</div>\n";
    echo "    </td>\n";
    echo "    <td colspan=\"2\" ".$par['td_bgcolor_ung_1']." ".$par['td_align_2']." ".$par['td_valign_1'].">\n";
    echo "        <button type=\"button\" onClick=\"self.location.href='".SELF."?darstellen=kd_bearb&amp;kd_id=".$myKunde->Kunden_ID."&amp;sortieren_nach=".$sortieren_nach."&amp;abc=".$abc."'\">Kunde bearbeiten</button>\n";
    echo "    </td>\n";
    echo "  </tr>\n";

    echo "</table>\n";
} // end of function kd_mgmt_show_kunde


//----------------------------------------------------------------------------
// Funktion.: kd_mgmt_show_best_liste
// Zweck....: Gibt die Liste von Bestellungen eines Kunden aus
// Argumente: $Kunden_ID -> Kunden-ID des Kunden
//            $par       -> Darstellungsparameterarray
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_show_best_liste($Kunden_ID, $par) {
    echo "<b>Bestellungen:</b>\n";
    echo "<table width=\"100%\" border=\"1\" rules=\"rows\" bgcolor=\"#CCCCCC\" cellspacing=\"0\" cellpadding=\"2\">\n";

    $myBestellungen = new Bestellungsliste;
    $myBestellungen->get_bestellungs_liste($Kunden_ID);
    $anzahl_bestellungen = $myBestellungen->get_anzahl_bestellungen();

    if ($anzahl_bestellungen < 1) {
        echo "  <tr>\n";
        echo "    <td valign=\"middle\">\n";
        echo "    <b>Keine Bestellungen vorhanden!</b>\n";
        echo "    </td>\n";
        echo "  </tr>\n";
    } // end of if
    else {
        // Tabellen-Header
        echo "  <tr>\n";
        echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
        echo "      <b>Bestelldatum</b>\n";
        echo "    </td>\n";
        echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
        echo "      <b>Bestellreferenz</b>\n";
        echo "    </td>\n";
        echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
        echo "      <b>Zahlungsart</b>\n";
        echo "    </td>\n";
        echo "    <td ".$par['td_valign_1']." align=\"left\">\n";
        echo "      <b>Status</b>\n";
        echo "    </td>\n";
        echo "    <td colspan=\"2\">\n";
        echo "      &nbsp;\n";
        echo "    </td>\n";
        echo "  </tr>\n";
        // Tabellenheader augeben

        // Tabellenzeilen (Treffer) ausgeben
        $zeilen_count = 0;
        for($i = 0; $i < $anzahl_bestellungen; $i++) {

            // Formatierungsangaben fuer Zeilen bestimmen
            $zeilen_count++;   // Zeilenzaehler inkrementieren
            if ($zeilen_count % 2 == 0){
                $td_bg = $par['td_bgcolor_ger_1'];
            }
            else{
                $td_bg = $par['td_bgcolor_ung_1'];
            }

            // Tabellen-Inhalt
            echo "  <tr>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1']." ".$par['td_align_1']."\">\n";
            echo        date_us_to_eur($myBestellungen->bestelldatum_array[$i]);
            echo "    </td>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1']." ".$par['td_align_1']."\">\n";
            echo        htmlentities($myBestellungen->ref_nr_array[$i]);
            echo "    </td>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1']." ".$par['td_align_1']."\">\n";
            echo        htmlentities($myBestellungen->zahlungsart_array[$i]);
            echo "    </td>\n";
            echo "    <td ".$td_bg." ".$par['td_valign_1']." ".$par['td_align_1']."\">\n";
            echo        htmlentities($myBestellungen->status_array[$i]);
            echo "    </td>\n";
            echo "    <td width=\"20px\" ".$td_bg." ".$par['td_valign_1']." ".$par['td_align_1']."\">\n";
            echo "      <button type=\"button\" onClick=\"self.location.href='".SELF."?darstellen=best_darst&amp;Kunden_ID=".$Kunden_ID."&amp;best_id=".$myBestellungen->bestellungs_id_array[$i]."'\">Anzeigen</button>\n";
            echo "    </td>\n";
            echo "    <td width=\"20px\" ".$td_bg." ".$par['td_valign_1']." ".$par['td_align_1']."\">\n";
            $warn_string = "Wollen Sie diese Bestellung wirklich L&Ouml;SCHEN?";
            $del_link = SELF."?darstellen=best_loesch&amp;Kunden_ID=".$Kunden_ID."&amp;best_id=".$myBestellungen->bestellungs_id_array[$i];
            echo "      <button type=\"button\" onClick=\"javascript: if(confirm('".$warn_string."')) { self.location.href='".$del_link."'; }\">L&ouml;schen</button>\n";
            echo "    </td>\n";
            echo "  </tr>\n";

        } // end of for
    } // end of else

    echo "</table>\n";
} // end of function kd_mgmt_show_best_liste


//----------------------------------------------------------------------------
// Funktion.: kd_mgmt_show_best
// Zweck....: Zeigt eine Bestellung an
// Argumente: $Kunden_ID -> Kunden-ID des Kunden
//            $par       -> Darstellungsparameterarray
//            $back      -> URI fuer den Zurueck-Button
// Rückgabe.: keine
//----------------------------------------------------------------------------
function kd_mgmt_show_best($best_id, $par, $back) {
    $Bestellung_string = get_Bestellung_string($best_id);
    echo "<b>Bestellung:</b>\n";
    echo "<table width=\"100%\" border=\"1\" rules=\"rows\" bgcolor=\"#CCCCCC\" cellspacing=\"0\" cellpadding=\"2\">\n";
    echo "  <tr>\n";
    echo "    <td>\n";
    echo "      <pre>Inhalt der Bestellung:\n".$Bestellung_string."</pre>\n";
    echo "      <div align=\"center\">\n";
    echo "        <button type=\"button\" onClick=\"self.location.href='".$back."'\">Zur&uuml;ck</button>\n";
    echo "      </div>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "<table>\n";
} // end of function kd_mgmt_show_best


//----------------------------------------------------------------------------
// Funktion.: stripstring
// Zweck....: Beschneidet einen String, der zu lange ist, um ihn vollstaendig
//            darzustellen und fuehrt danach ein htmlentities aus
// Argumente: $string -> String, der unter umstaenden gekuerzt wird
//            $len    -> maximale Laenge
//            $post   -> Zeichenfolge, die angehaengt wird, wenn der String
//                       gekuerzt werden muss
// Rückgabe.: keine
//----------------------------------------------------------------------------
function stripstring($string, $len, $post){
    $nutzlaenge = $len - strlen($post);
    if ($nutzlaenge <= 0){
        $nutzlaenge = $len;
    }
    if (strlen($string) <= $len){
        $newstring = $string;
    }
    else {
        $newstring = substr($string, 0, $nutzlaenge);
        $newstring.= $post;
    }
    return htmlentities($newstring);
} // end of function stripstring

// -----------------------------------------------------------------------
// Gibt auf Grund einer Bestellungs_ID oder einer Referenz_Nr die dazugehoerende Bestellung als String zurueck.
// Diese Funktion arbeitet nur mit abgeschlossenen, ausgeloesten und oder bezahlten Bestellungen.
// Das zweite Argument steuert die Interpretation des ersten Arguments - der uebergebenen Nummer.
// Wenn die erste Nummer eine Referenz-Nr ist, so muss das zweite Argument 'true' lauten.
// Argument: Bestellungs_ID (Integer), Flag, ob erstes Argument eine Ref.Nr. ist ['true'|'false'] (String)
// Rueckgabewert: Eine Bestellung als String (wie im E-Mail)
//----------------------------------------------------------------------------
function getBestellung_ID($Bestellungs_ID, $is_ref_nr='false') {

    //Einbinden von in anderen Modulen deklarierten Variablen
    global $Admin_Database;
    global $sql_getBestellung_ID_1;

    // Umwandeln einer allfaellig angegebenen Referenz-Nr in eine Bestellungs-ID:
    if ($is_ref_nr == "true") {
        $Bestellungs_ID = ref_to_bestellungs_id($Bestellungs_ID); // Def. der Funktion siehe bestellung_def.php
    }

    // Test ob die Datenbank erreichbar ist
    if (! is_object($Admin_Database)) {
        die("<h3 class='content'>shop_kunden_mgmt_func_Error: Datenbank nicht erreichbar (getBestellung_ID)</h3>\n");
    }
    else {
          // Benoetigte Query zusammenstellen
          $query = $sql_getBestellung_ID_1.$Bestellungs_ID;

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Query($query);
          if ($RS && $RS->NextRow()){
              $myBestellung = $RS->GetField("Bestellung_string");
          }
          else {
              echo "Ein Fehler ist aufgetreten:<br><br>\nQuery: $query<br><br>\n";
              die("<h3>shop_kunden_mgmt_func_Error: Fehler beim Auslesen der Bestellung mit der Bestellungs_ID: $Bestellungs_ID</h3>");
          }
      }//End else
      return $myBestellung;
 }//End of function getBestellung_ID

// -----------------------------------------------------------------------
// Loescht einen Kunden inkl. allen seinen Bestellungen. Der Kunde wird durch die Kunden_ID angegeben.
// Argument: Kunden_ID (INT)
// Rueckgabewert: true | false (Boolean)
//----------------------------------------------------------------------------
function delKunde_und_Bestellungen($Kunden_ID) {

    //Einbinden von in anderen Modulen deklarierten Variablen
    global $Admin_Database;
    global $sql_delKunde_und_Bestellungen_1_1;
    global $sql_delKunde_und_Bestellungen_1_2;
    global $sql_delKunde_und_Bestellungen_1_3;
    global $sql_delKunde_und_Bestellungen_1_4;
    global $sql_delKunde_und_Bestellungen_1_5;
    global $sql_delKunde_und_Bestellungen_1_6;

    // Test ob die Datenbank erreichbar ist
    if (! is_object($Admin_Database)) {
        die("<h3 class='content'>shop_kunden_mgmt_func_Error: Datenbank nicht erreichbar (delKunde_und_Bestellungen)</h3>\n");
    }
    else {
          // Benoetigte Queries zusammenstellen
          $query1 = $sql_delKunde_und_Bestellungen_1_1.$Kunden_ID.$sql_delKunde_und_Bestellungen_1_2; // Tbl. kunde              del
          $query2 = $sql_delKunde_und_Bestellungen_1_3.$Kunden_ID.$sql_delKunde_und_Bestellungen_1_2; // Tbl. bestellung_kunde   select
          $query3 = $sql_delKunde_und_Bestellungen_1_4.$Kunden_ID.$sql_delKunde_und_Bestellungen_1_2; // Tbl. bestellung_kunde   del
          $query4 = ""; // Initialisierung                                                               Tbl. bestellung         init
          $query5 = ""; // Initialisierung                                                               Tbl. artikel_bestellung init

          $kunden_bestellungen = array(); // Initialisierung: Dieser Array wird die Bestellungen des Kunden beinhalten (nur IDs)

          // Queries ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // query1 ausfuehren: Tabelle kunde - zu loeschender Kundeneintrag entfernen
          $RS = $Admin_Database->Exec($query1);
          // query2 ausfuehren: Tabelle bestellung_kunde: Alle Bestellungen des Kunden auslesen (Bestellungs_IDs)
          $RS = $Admin_Database->Query($query2);
          while (is_object($RS) && $RS->NextRow()){
              $kunden_bestellungen[] = $RS->GetField("FK_Bestellungs_ID");
          }
          // query3 ausfuehren: Tabelle bestellung_kunde - alle Verbindungen von Bestellungen zum zu loeschenden Kunden entfernen
          $RS = $Admin_Database->Exec($query3);
          // Jetzt werden alle Bestellungen des Kunden und die Referenzen zu Artikeln der jeweiligen Bestellungen entfernt
          foreach ($kunden_bestellungen as $key=>$Bestellungs_ID) {
              // Benoetigte Queries zusammenstellen
              $query4 = $sql_delKunde_und_Bestellungen_1_5.$Bestellungs_ID;
              $query5 = $sql_delKunde_und_Bestellungen_1_6.$Bestellungs_ID;

              // query4 ausfuehren: Tabelle bestellung - alle Bestellungen des zu loeschenden Kunden entfernen
              $RS = $Admin_Database->Exec($query4);
              // query5 ausfuehren: Tabelle artikel_bestellung - alle Artikel Referenzen der jeweiligen Bestellung des Kunden loeschen
              $RS = $Admin_Database->Exec($query5);
          } // End foreach
    }// End else
    return true;
}// End of function delKunde_und_Bestellungen

// ---------------------------------------------------------------------------
// Liest die Beschreibung eines Kunden aus der Datenbank aus. Dieses Feld ist nur fuer den Administrator gedacht (interne Bemerkungen)
// Argument: Kunden_ID (Long Integer)
// Rueckgabewert: Beschreibung des Kunden (String) oder Abbruch via die-Funktion
//----------------------------------------------------------------------------
function getKunden_Beschreibung($Kunden_ID) {

    //Einbinden von in anderen Modulen deklarierten Variablen
    global $Admin_Database;
    global $sql_getKunden_Beschreibung_1_1;
    global $sql_getKunden_Beschreibung_1_2;

    // Test ob die Datenbank erreichbar ist
    if (! is_object($Admin_Database)) {
        die("<h3 class='content'>shop_kunden_mgmt_func_Error: Datenbank nicht erreichbar (getKunden_Beschreibung)</h3>\n");
    }
    else {
          // Benoetigte Query zusammenstellen
          $query = $sql_getKunden_Beschreibung_1_1.$Kunden_ID.$sql_getKunden_Beschreibung_1_2;

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Query($query);
          if ($RS && $RS->NextRow()){
              $Beschreibung = $RS->GetField("Beschreibung");
          }
          else {
              echo "Ein Fehler ist aufgetreten:<br><br>\nQuery: $query<br><br>\n";
              die("<h3>shop_kunden_mgmt_func_Error: Fehler beim Auslesen des Beschreibungsattribut des Kunden mit der Kunden_ID: $Kunden_ID</h3>");
          }
    }// End else
    return $Beschreibung;
}// End function getKunden_Beschreibung

// ---------------------------------------------------------------------------
// Legt die Beschreibung eines Kunden in die Datenbank ab. In der Datenbank ist das Beschreibungsattribut vom Typ Text.
// Argument: Kunden_ID (Long Integer), Beschreibung (String)
// Rueckgabewert: true | false (Boolean)
//----------------------------------------------------------------------------
function setKunden_Beschreibung($Kunden_ID, $Beschreibung) {

    //Einbinden von in anderen Modulen deklarierten Variablen
    global $Admin_Database;
    global $sql_setKunden_Beschreibung_1_1;
    global $sql_setKunden_Beschreibung_1_2;
    global $sql_setKunden_Beschreibung_1_3;

    // Test ob die Datenbank erreichbar ist
    if (! is_object($Admin_Database)) {
        die("<h3 class='content'>shop_kunden_mgmt_func_Error: Datenbank nicht erreichbar (getKunden_Beschreibung)</h3>\n");
    }
    else {
          // Benoetigte Query zusammenstellen
          $query = $sql_setKunden_Beschreibung_1_1.$Beschreibung.$sql_getKunden_Beschreibung_1_2.$Kunden_ID.$sql_setKunden_Beschreibung_1_3;
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Exec($query);
    } // end of else

    return true;
}// End function setKunden_Beschreibung

// ---------------------------------------------------------------------------
// Mit dieser Funktion wird ein neuer Kunde vom Admininterface angelegt. Diese
// Funktion wurde zusaetzlich zur schon bestehenden Funktion newKunde() (SHOP_BESTELLUNG.php)
// angelegt, weil das Kundenmanagement andere Parameter uebergeben kann. Die
// bestehende Funktion newKunde wird shopseitig (Kundenteil) verwendet und nicht vom
// Die Kunden_ID muss vorher erzeugt werden und ist im Kundenobjekt schon enthalten.
// Administrationstool.
// Argument: Kundenobjekt (siehe kunde_def.php)
// Rueckgabewert: true | false (Boolean
//----------------------------------------------------------------------------
function newKundeAdmin($KundenObjekt) {

    //Einbinden von in anderen Modulen deklarierten Variablen
    global $Admin_Database;
    global $sql_newKundeAdmin_1_1;
    global $sql_newKundeAdmin_1_2;
    global $sql_newKundeAdmin_1_3;
    global $sql_newKundeAdmin_1_4;
    global $sql_newKundeAdmin_1_5;
    global $sql_newKundeAdmin_1_6;
    global $sql_newKundeAdmin_1_7;

    // Test ob die Datenbank erreichbar ist
    if (! is_object($Admin_Database)) {
        die("<h3 class='content'>shop_kunden_mgmt_func_Error: Datenbank nicht erreichbar (newKundeAdmin)</h3>\n");
    }
    else {

          // Variablen des Kundenobjekts auslesen
          $obj_vars = get_object_vars($KundenObjekt);

          // Wenn die Bankdaten des Kunden eingegeben wurden, so sollen diese auch
          // automatisch verwendet werden (autofill des entsprechenden Formulars bei
          // Lastsch$riftzahlung)
          if ($obj_vars["bankname"] != "") {
              $obj_vars["bankdaten_speichern"] = "Y";
          }
          // Der vom Administrator bearbeitete und gespeicherte Kunde ist immer persistent (temp='N')
          $obj_vars["temp"] = "N";

          // Aktuelles Datum holen
          $mydate = getdate();
          // Datum entsprechend formatieren um es in die Datenbank einfuegen zu koennen
          $AnmeldeDatum = $mydate[year]."-".$mydate[mon]."-".$mydate[mday];// Format yyyy-mm-dd

          // Anmeldedatum dem Objekt-Array hinzufuegen
          $obj_vars['AnmeldeDatum'] = $AnmeldeDatum;

          // SQL-Query initialisieren
          $query_keys = $sql_newKundeAdmin_1_1;   // Initialisierung
          $query_values = $sql_newKundeAdmin_1_6; // Initialisierung

          // Abfuellen der Objektdaten in eine generierte SQL-INSERT-Query
          // Daten werden schon als ge-addslashed angenommen
          foreach($obj_vars as $key=>$value) {
              // Die Attribute k_ID und Session_ID sollen nicht in den INSERT-Query uebernommen werden
              if ($key != "k_ID" && $key != "Session_ID" && $key != "Bestellungsarray") {
                  // Unterscheidung ob es sich um den ersten Durchlauf handelt
                  if ($query_keys == $sql_newKundeAdmin_1_1) {
                      if (gettype($value) != "string") {
                          // Wenn der Wert nicht leer ist (sonst gibt es einen SQL-Error (,,))
                          if ($value != "") {
                              $query_keys.=$key;
                              $query_values.=$value;
                          }
                      }
                      else {
                          $query_keys.=$key;
                          $query_values.="'".$value."'";
                      }
                  }
                  else {
                      if (gettype($value) != "string") {
                          // Wenn der Wert nicht leer ist (sonst gibt es einen SQL-Error (,,))
                          if ($value != "") {
                              $query_keys.=",".$key;
                              $query_values.=",".$value;
                          }
                      }
                      else {
                          $query_keys.=",".$key;
                          $query_values.=",'".$value."'";
                      }
                  }
              }// End if $key != ...
          }// End foreach

          // Query zusammenstellen
          $query = $query_keys.$query_values.$sql_newKundeAdmin_1_7;

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Exec($query);
    }// End else $Admin_Database

    return true;
}// End function newKundeAdmin

// ---------------------------------------------------------------------------
// Mit dieser Funktion wird ein bestehender Kunde vom Admininterface upgedated. Diese
// Funktion wurde zusaetzlich zur schon bestehenden Funktion updKunde() (SHOP_BESTELLUNG.php)
// angelegt, weil das Kundenmanagement andere Parameter uebergeben kann. Die
// bestehende Funktion updKunde wird shopseitig (Kundenteil) verwendet und nicht vom
// Administrationstool. Zudem ist updKunde nur ein Teil des Kundenupdates.
// Argument: Kundenobjekt (siehe kunde_def.php)
// Rueckgabewert: true | false (Boolean
//----------------------------------------------------------------------------
function updKundeAdmin($KundenObjekt) {

    //Einbinden von in anderen Modulen deklarierten Variablen
    global $Admin_Database;
    global $sql_updKundeAdmin_1_1;
    global $sql_updKundeAdmin_1_2;
    global $sql_updKundeAdmin_1_3;
    global $sql_updKundeAdmin_1_4;
    global $sql_updKundeAdmin_1_5;
    global $sql_updKundeAdmin_1_6;
    global $sql_updKundeAdmin_1_7;

    // Test ob die Datenbank erreichbar ist
    if (! is_object($Admin_Database)) {
        die("<h3 class='content'>shop_kunden_mgmt_func_Error: Datenbank nicht erreichbar (updKundeAdmin)</h3>\n");
    }
    else {
          // Variablen des Kundenobjekts auslesen
          $obj_vars = get_object_vars($KundenObjekt);

          // Wenn die Bankdaten des Kunden eingegeben wurden, so sollen diese auch
          // automatisch verwendet werden (autofill des entsprechenden Formulars bei
          // Lastsch$riftzahlung)
          if ($obj_vars["bankname"] != "") {
              $obj_vars["bankdaten_speichern"] = "Y";
          }

          // SQL-Query initialisieren
          $query = $sql_updKundeAdmin_1_1;   // Initialisierung

          // Abfuellen der Objektdaten in eine generierte SQL-INSERT-Query
          // Daten werden als schon ge-addslashed angenommen
          foreach($obj_vars as $key=>$value) {
              // Die Attribute k_ID und Session_ID sollen nicht in den UPDATE-Query uebernommen werden
              if ($key != "k_ID" && $key != "Session_ID" && $key != "Bestellungsarray" && !($key == "Passwort" && $value == "")) {
                  // Unterscheidung ob es sich um den ersten Durchlauf handelt
                  if ($query == $sql_updKundeAdmin_1_1) {
                      if (gettype($value) != "string") {
                          // Wenn der Wert nicht leer ist (sonst gibt es einen SQL-Error (,,))
                          if ($value != "") {
                              $query.=$key.$sql_updKundeAdmin_1_4.$value;
                          }
                      }
                      else {
                          $query.=$key.$sql_updKundeAdmin_1_2.$value.$sql_updKundeAdmin_1_5;
                      }
                  }
                  else {
                      if (gettype($value) != "string") {
                          // Wenn der Wert nicht leer ist (sonst gibt es einen SQL-Error (,,))
                          if ($value != "") {
                              $query.=$sql_updKundeAdmin_1_3.$key.$sql_updKundeAdmin_1_4.$value;
                          }
                      }
                      else {
                          $query.=$sql_updKundeAdmin_1_3.$key.$sql_updKundeAdmin_1_2.$value.$sql_updKundeAdmin_1_5;
                      }
                  }
              }// End if $key != ...
          }// End foreach

          // WHERE Statement hinzufuegen
          $query.=$sql_updKundeAdmin_1_6.$obj_vars['Kunden_ID'].$sql_updKundeAdmin_1_7;

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Exec($query);
    }// End else $Admin_Database

    return true;
}// End function updKundeAdmin

// -----------------------------------------------------------------------
// Diese Funktion liefert die Bestellung als String dargestellt - so wie beim Bestellvorgang das E-Mail an den Shopadmin.
// gesendet wurde. Die Bestellung_strings werden in USER_BESTELLUNG_1.php ganz am Ende (darstellen == 4) erstellt.
// Genauer beschrieben wird einfach das Tabellenattribut Bestellung_string der Tabelle bestellung ausgelesen.
// Das (optionale) zweite Argument ($filter_tags) steuert die Filterung der Bestellungstags. Wenn $filter_tags auf true ist (default),
// so werden die Tags mit einem Regexp. aus dem String herausgefiltert, sonst nicht. Def. Bestellungstags: bestellung_def.php
// Argument: Bestellungs_ID (Integer), $filter_tags (Boolean)
// Rueckgabewert: Bestellung in Form eines Strings (String)
function get_Bestellung_string($Bestellungs_ID, $filter_tags=true) {

    // Benoetigte Variablen aus anderen Modulen einbinden
    global $Admin_Database;
    global $sql_get_Bestellung_string_1_1;

    $Bestellung_string = ""; // Initialisierung

    // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
    if (!is_object($Admin_Database)) {
        die("<P><H1 class='content'>shop_kunden_mgmt_func_Error: get_Bestellung_string: Datenbank nicht erreichbar</H1></P><BR>");
    }
    else {
        // Query ausfuehren
        $RS = $Admin_Database->Query($sql_get_Bestellung_string_1_1.$Bestellungs_ID);
        if ($RS && $RS->NextRow()){
            $Bestellung_string = $RS->GetField("Bestellung_string");
        }
        else {
            echo "Ein Fehler ist aufgetreten:<br><br>\nQuery: $query<br><br>\n";
            die("<h3>shop_kunden_mgmt_func_Error: Fehler beim Auslesen der Bestellung als Textstring. Bestellungs_ID: $Bestellungs_ID</h3>");
        }
    }// End else

    // Info, wenn leer
    if ($Bestellung_string == "") {
        $Bestellung_string = "\n\nDer Inhalt der Bestellung scheint nicht abrufbar! (Leeres Dokument erhalten)\n";
    }
    else {
        // Wenn der Bestellungs-String nicht leer ist, wird ausgewertet, ob die Bestellungstags heraus
        // gefiltert werden sollen oder nicht (abhaengig vom zweiten Argument $filter_flags)
        // Die benutzten Tags sind nach dem SML-Standard (XML-Subset: Simplified Markup Language)
        if ($filter_tags) {
            $Bestellung_string = filterBestellungsTags($Bestellung_string);
        }// End if filter_tags
    }// End else Bestellung_string == ""
    return $Bestellung_string;
}//End get_Bestellung_string

// End of file ----------------------------------------------------------
?>
