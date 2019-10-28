<?php
// Filename: shop_kunden_mgmt.php
//
// Modul: Aufruf-Module - SHOP_ADMINISTRATION
//
// Autoren: José Fontanil & Reto Glanzmann
//
// Zweck: Beinhaltet die Funktionalitaet, um Kunden zu erstellen und zu bearbeiten
//
// Sicherheitsstatus:        *** ADMIN ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: shop_kunden_mgmt.php,v 1.21 2003/08/11 11:38:28 glanzret Exp $
//
// -----------------------------------------------------------------------
// Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
// wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
$SHOP_KUNDEN_MGMT = true;

// include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
// Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
// Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

// Einbinden der benoetigten Module (PHP-Scripts)
// Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
if (!isset($SHOP_KUNDEN_MGMT_FUNC)) {include("shop_kunden_mgmt_func.php");}
if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}
if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

// Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
// $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
extract($_GET);
extract($_POST);

// PHP-Self Variable abfuellen
define ("SELF", $_SERVER["PHP_SELF"]);

// einige Tabellenparameter
$par['td_valign_1'] = "valign=\"middle\"";
$par['td_align_1'] = "align=\"left\"";
$par['td_align_2'] = "align=\"center\"";
$par['td_bgcolor_ger_1'] = "bgcolor=\"#CCCCCC\"";
$par['td_bgcolor_ung_1'] = "bgcolor=\"#DDDDDD\"";

// Bestellungsaufruf aus Administrator-E-Mail
// Bestellt ein Kunde per Kreditkarte oder Bankeinzug und die Zahlungsdaten werden "intern" erfasst, so bekommt er einen
// https-Link, der direkt auf die Bestellung im Kundenmanagement zeigt. Dort findet er dann die Kreditkartendaten zur
// Bestellung.
// falls dieses File mit dem Argument r=referenznummer aufgerufen wurde, eine Bestellungssuche ausloesen
if ((!empty($r)) && $darstellen == ""){
    $darstellen = "suche";
    $was = "bestellungsref";
    $suchstring = $r;
} // end of if

// Header ausgeben
kd_mgmt_header();

echo "<table border=\"0\">\n";

// Titelzeile
echo "  <tr>\n";
echo "    <td height=\"20px\">\n";
echo "      <h3><b>Kundenmanagement</b></h3>\n";
echo "    </td>\n";
echo "  </tr>\n";

// Zeile mit Suchmöglichkeiten ausgeben
echo "  <tr>\n";
echo "    <td height=\"15px\">\n";
kd_mgmt_suche();
echo "    </td>\n";
echo "  </tr>\n";

// Wenn ein Kunde abgespeichert wird, kann sein Name oder die Firmenbezeichnung aendern.
// Damit der Kunde doch direkt in der angezeigten Kundenliste angezeigt wird, ermitteln
// wir einen gueltigen Wert fuer $abc und $sortieren_nach
if ($darstellen == "kd_speich") {
    // falls überhaupt nichts übergeben wurde
    if ($abc == "" || $sortieren_nach == "") {
        if (!empty($Firma)) {
            $abc = strtoupper(substr($Firma, 0, 1));
            $sortieren_nach = "Firmennamen";
        } // end of if
        elseif (!empty($Nachname)) {
            $abc = strtoupper(substr($Nachname, 0, 1));
            $sortieren_nach = "Nachnamen";
        } // end of elseif
        else {
            $abc = "A";
            $sortieren_nach = "Nachnamen";
        } // end of else
    } // end of if

    // Falls vorher nach Firmennamen sortiert wurde
    elseif ($sortieren_nach == "Firmennamen") {
        $erster_buchstabe = strtoupper(substr($Firma, 0, 1));
        // es existiert kein erster buchstabe -> es muss einen Nachnamen geben
        if ($erster_buchstabe == "") {
            $sortieren_nach = "Nachnamen";
            $abc = strtoupper(substr($Nachname, 0, 1));
        }
        // erster buchstabe gleich geblieben
        elseif ($erster_buchstabe == $abc) {
            // nix zu tun
        }
        // erster buchstabe existiert und hat geaendert
        else {
            $abc = $erster_buchstabe;
        }
    } // end of elseif

    // Falls vorher nach Nachnamen sortiert wurde
    else {
        $erster_buchstabe = strtoupper(substr($Nachname, 0, 1));
        // es existiert kein erster buchstabe -> es muss einen Firmennamen geben
        if ($erster_buchstabe == "") {
            $sortieren_nach = "Firmennamen";
            $abc = strtoupper(substr($Firma, 0, 1));
        }
        // erster buchstabe gleich geblieben
        elseif ($erster_buchstabe == $abc) {
            // nix zu tun
        }
        // erster buchstabe existiert und hat geaendert
        else {
            $abc = $erster_buchstabe;
        }
    } // end of elseif

    // Wenn der erste Buchstabe eine Ziffer ist, $abc auf num umbauen
    if ($abc >= "0" && $abc <= "9"){
        $abc = "num";
    }
} // end of if

// beim ersten (oder einem falschen) Aufruf soll der Buchstabe A sortiert nach Nachnamen dargestellt werden
if ($abc == "" || $sortieren_nach == ""){
    $abc = "A";
    $sortieren_nach = "Nachnamen";
}

// Zeile mit A B C D E ..
if ($darstellen != "neukunde"){
    echo "  <tr>\n";
    echo "    <td height=\"15px\">\n";
    kd_mgmt_abclinks($sortieren_nach, $abc);
    echo "    </td>\n";
    echo "  </tr>\n";
} // end of if

// -----------------------------------------------------------------------
// Einen Kunden mit allen Bestellungen loeschen
// -----------------------------------------------------------------------
if ($darstellen == "kd_loesch") {
    delKunde_und_Bestellungen($kd_id);
    // darstellen-Variable leeren, damit Kundenliste angezeigt wird
    $darstellen = "";
} //en of elseif darstellen = kd_loesch

// -----------------------------------------------------------------------
// Eine Bestellungen loeschen
// -----------------------------------------------------------------------
if ($darstellen == "best_loesch") {
    delBestellung($best_id);
    $darstellen = "kunde_bestellungen";
} // end of darstellen = best_loesch


// -----------------------------------------------------------------------
// Einen Kunden speichern
// -----------------------------------------------------------------------
if ($darstellen == "kd_speich") {

    // Kundenobjekt erzeugen und abfuellen
    $myKunde = new Kunde;
    $myKunde->Kunden_ID = $Kunden_ID;
    $myKunde->Kunden_Nr = $Kunden_Nr;
    $myKunde->Session_ID = $Session_ID;
    $myKunde->Anrede = $Anrede;
    $myKunde->Vorname = $Vorname;
    $myKunde->Nachname = $Nachname;
    $myKunde->Firma = $Firma;
    $myKunde->Abteilung = $Abteilung;
    $myKunde->Strasse = $Strasse;
    $myKunde->Postfach = $Postfach;
    $myKunde->PLZ = $PLZ;
    $myKunde->Ort = $Ort;
    $myKunde->Land = $Land;
    $myKunde->Tel = $Tel;
    $myKunde->Fax = $Fax;
    $myKunde->Email = $EMail;
    $myKunde->Einkaufsvolumen = $Einkaufsvolumen;
    $myKunde->Beschreibung = $beschr;
    $myKunde->LetzteBestellung = $LetzteBestellung;
    $myKunde->AnmeldeDatum = $AnmeldeDatum;
    $myKunde->Login = $Login;
    $myKunde->Passwort = $Passwort;
    $myKunde->gesperrt = $gesperrt;
    $myKunde->temp = $temp;
    $myKunde->Attribut1 = $Attribut[1];
    $myKunde->Attribut2 = $Attribut[2];
    $myKunde->Attribut3 = $Attribut[3];
    $myKunde->Attribut4 = $Attribut[4];
    $myKunde->Attributwert1 = $Attributwert[1];
    $myKunde->Attributwert2 = $Attributwert[2];
    $myKunde->Attributwert3 = $Attributwert[3];
    $myKunde->Attributwert4 = $Attributwert[4];
    $myKunde->kontoinhaber = $kontoinhaber;
    $myKunde->bankname = $bankname;
    $myKunde->blz = $blz;
    $myKunde->kontonummer = $kontonummer;

    // Neukunden: Test ob Kundennummer und Login nicht schon vergeben sind
    $ok = true;
    $probleme = array();
    if(empty($Kunden_ID)) {
        if (existKundenNr($Kunden_Nr) != false && $Kunden_Nr != ""){
            $probleme[] = "Die Kundennummer '".$Kunden_Nr."' ist schon einem anderen Kunden vergeben!";
            $ok = false;
        } // end of if
        if (existLogin($Login) != false) {
            $probleme[] = "Der Login '".$Login."' wird schon von einem anderen Kunden verwendet!</b>";
            $ok = false;
        }
        if ($ok == true) {
            $myKunde->Kunden_ID = createKundenID();
            newKundeAdmin($myKunde);
        } // end of if
    } // end of if

    // bestehender Kunde Updaten
    else {
        // ermitteln, ob die Kunden_ID schon vergeben ist
        $Kunden_Nr_ID = existKundenNr($myKunde->Kunden_Nr);
        if ($Kunden_Nr_ID != false && $Kunden_Nr_ID != $myKunde->Kunden_ID && $myKunde->Kunden_Nr != ""){
            $probleme[] = "Die Kundennummer '".$myKunde->Kunden_Nr."' ist schon einem anderen Kunden vergeben!";
            $ok = false;
        } // end of if
        $Kunden_Nr_ID = existLogin($myKunde->Login);
        if ($Kunden_Nr_ID != false && $Kunden_Nr_ID != $myKunde->Kunden_ID){
            $probleme[] = "Der Login '".$myKunde->Login."' wird schon von einem anderen Kunden verwendet!</b>";
            $ok = false;
        } // end of if
        if ($ok == true) {
            updKundeAdmin($myKunde);
        } // end of if
    } // end of else

    // Wenn alles ok ist, Kundenliste anzeigen
    if ($ok == true) {
        $darstellen = "";
    }
    // ..falls ein Problem aufgetreten ist, Bearbeitungsformular nochmals ausgeben
    else {

        // Bearbeitungsformular ausgeben
        echo "  <tr>\n";
        echo "    <td height=\"90%\" valign=\"top\">\n";
        echo "      <b>Kunde bearbeiten:</b>\n";
        kd_mgmt_kunde_edit($myKunde, $sortieren_nach, $abc, $probleme, $Passwort2, $beschr);
        echo "    </td>\n";
        echo "  </tr>\n";
    } // end of else
} //end of elseif darstellen = kd_speich

// -----------------------------------------------------------------------
// Nach einer Kundennummer oder Bestellungsreferenz suchen
// -----------------------------------------------------------------------
if ($darstellen == 'suche'){

    // Suche nach einer Kundennummer
    if ($was == "kundennr"){
        $Kunden_ID = existKundenNr($suchstring);
        if(!empty($Kunden_ID)) {
            $darstellen = 'kunde_bestellungen';
        } // end of if
        else {
            echo "  <tr>\n";
            echo "    <td height=\"90%\" valign=\"top\">\n";
            echo "    <b>Es existiert kein Kunde mit der Kundennummer '$suchstring'</b>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        } // end of else
    } // end of if

    // Suche nach einer Bestellungsreferenz
    else {
        // Bestellungs_ID aus Referenznummer berechnen (offset abziehen)
        $best_id = ref_to_bestellungs_id(intval($suchstring));
        $Kunden_ID = existBestellung($best_id);
        if(!empty($Kunden_ID)) {
            $darstellen = "best_darst";
        } // end of if
        else {
            echo "  <tr>\n";
            echo "    <td height=\"90%\" valign=\"top\">\n";
            echo "    <b>Es existiert keine Bestellung mit der Referenznummer '$suchstring'</b>\n";
            echo "    </td>\n";
            echo "  </tr>\n";
        } // end of else
    } // end of else

    //         echo "+".$myBestellung->Bestellungs_ID."+";
    //   debug($myBestellung);

} // end of if darstellen == suche

// -----------------------------------------------------------------------
// Einen Kunden und dessen Bestellungen ausgeben
// -----------------------------------------------------------------------
if ($darstellen == 'kunde_bestellungen'){
    echo "  <tr>\n";
    echo "    <td height=\"90%\" valign=\"top\">\n";
    $myKunde = getKunde($Kunden_ID);
    kd_mgmt_show_kunde($myKunde, $par, $abc ,$sortieren_nach);
    kd_mgmt_show_best_liste($myKunde->Kunden_ID, $par);
    echo "    </td>\n";
    echo "  </tr>\n";
} // end of if darstellen = kunde_bestellungen

// -----------------------------------------------------------------------
// Eine Bestellungen darstellen
// -----------------------------------------------------------------------
elseif ($darstellen == "best_darst") {
    echo "  <tr>\n";
    echo "    <td height=\"90%\" valign=\"top\">\n";
    $myKunde = getKunde($Kunden_ID);
    kd_mgmt_show_kunde($myKunde, $par, $abc, $sortieren_nach);
    $return = SELF."?darstellen=kunde_bestellungen&amp;Kunden_ID=".$Kunden_ID."&amp;sortieren_nach=".$sortieren_nach."&amp;abc=".$abc;
    kd_mgmt_show_best($best_id, $par, $return);
    echo "    </td>\n";
    echo "  </tr>\n";
} // end of darstellen = best_darst

// -----------------------------------------------------------------------
// Einen neuen Kunden erfassen
// -----------------------------------------------------------------------
elseif ($darstellen == 'neukunde'){
    echo "  <tr>\n";
    echo "    <td height=\"90%\" valign=\"top\">\n";
    echo "      <b>Neuen Kunden erfassen:</b>\n";
    $myKunde = new Kunde;
    kd_mgmt_kunde_edit($myKunde, $sortieren_nach, $abc);
    echo "    </td>\n";
    echo "  </tr>\n";
} // end of if darstellen = neukunde


// -----------------------------------------------------------------------
// Einen bestehenden Kunden bearbeiten
// -----------------------------------------------------------------------
elseif ($darstellen == 'kd_bearb'){
    echo "  <tr>\n";
    echo "    <td height=\"90%\" valign=\"top\">\n";
    echo "      <b>Kunde bearbeiten:</b>\n";
    $myKunde = getKunde($kd_id);
    kd_mgmt_kunde_edit($myKunde, $sortieren_nach, $abc, $returnstring);
    echo "    </td>\n";
    echo "  </tr>\n";
} // end of if darstellen = neukunde


// -----------------------------------------------------------------------
// Wird ausgeführt, wenn dieses File nicht mit einem speziellen darstellen-
// Wert aufgerufen wird (Kundenliste ausgeben)
// -----------------------------------------------------------------------
elseif ($darstellen == "" || $darstellen == "liste")  {
    echo "  <tr>\n";
    echo "    <td height=\"90%\" valign=\"top\">\n";
    if ($sortieren_nach == "Nachnamen" || $sortieren_nach == "") {
        kd_mgmt_kdliste($sortieren_nach, $abc, $par);
    }
    if ($sortieren_nach == "Firmennamen") {
        kd_mgmt_kdliste($sortieren_nach, $abc, $par);
    }
    echo "    </td>\n";
    echo "  </tr>\n";
} // end of else


// Zurueck Button wird nur teilweise ausgegeben
if ($darstellen == "" || $darstellen == "kunde_bestellungen") {
    echo "<tr>\n";
    echo "  <td height=\"15px\" align=\"center\">\n";
    echo "    <button type=\"button\" onClick=\"self.location.href='./Shop_Einstellungen_Menu_1.php'\">Zur&uuml;ck zum Hauptmenu</button>\n";
    echo "  </td>\n";
    echo "</tr>\n";
} // end of if

echo "</table>";
echo "</body>";
echo "</html>";

// End of file ----------------------------------------------------------
?>
