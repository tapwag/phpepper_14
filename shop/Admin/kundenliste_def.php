<?php
  // Filename: kunde_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Kundenliste
  //
  // Sicherheitsstatus:                 *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: kundenliste_def.php,v 1.9 2003/05/24 18:41:39 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $kundenliste_def = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux/MacOS beruecksichtigt.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux/MacOS --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_Database)) {include("ADMIN_initialize.php");}

  //----------------------------------------------------------------------------
  // Zweck: Im folgenden Abschnitt werden die in diesem Modul benoetigten SQL-Queries definiert.
  //        Weiter SQL-Queries finden sich in den Dateien ../USER_SQL_BEFEHLE.php und ADMIN_SQL_BEFEHLE.php.
  // Variabelnamenaufbau: sql_ NAME DES SCRIPTS _suffix ... suffix = hochzaehlende Zahl
  //        (Falls eine Query aus mehreren hier definierten Variablen zusammengesetzt wird, so
  //        ist der suffix nochmals angehaengt und dort eine Laufvariable eingesetzt worden).
  //----------------------------------------------------------------------------
  $sql_get_kunden_liste_1     = "SELECT Kunden_ID, Kunden_Nr, gesperrt, Neukunde, Nachname, Vorname, Firma, Ort, Land FROM kunde WHERE NOT (temp = 'Y' AND LetzteBestellung IS NULL) AND "; // Filter fuer temp. Kunden mit keiner abgeschlossenen Bestellung
  $sql_get_kunden_liste_1_2_1 = " LIKE '";        // Suche nach Buchstabe Teil 1
  $sql_get_kunden_liste_1_2_3 = "%' OR ";         // Suche nach Buchstabe Teil 3 (Umlaute)
  $sql_get_kunden_liste_1_2_2 = "%' ORDER BY ";   // Suche nach Buchstabe Teil 2
  $sql_get_kunden_liste_1_3_1 = " 1";             // Suche alle Teil 1
  $sql_get_kunden_liste_1_3_2 = " ORDER BY ";     // Suche alle Teil 2
  $sql_get_kunden_liste_1_4_1 = " Nachname LIKE '0%' OR Nachname LIKE '1%' OR Nachname LIKE '2%' OR Nachname LIKE '3%' OR Nachname LIKE '4%' OR Nachname LIKE '5%' OR Nachname LIKE '6%' OR Nachname LIKE '7%' OR Nachname LIKE '8%' OR Nachname LIKE '9%' "; // Suche Nummer Teil 1a
  $sql_get_kunden_liste_1_4_2 = " Firma LIKE '0%' OR Firma LIKE '1%' OR Firma LIKE '2%' OR Firma LIKE '3%' OR Firma LIKE '4%' OR Firma LIKE '5%' OR Firma LIKE '6%' OR Firma LIKE '7%' OR Firma LIKE '8%' OR Firma LIKE '9%' "; // Suche Nummer Teil 1b
  $sql_get_kunden_liste_1_4_3 = " ORDER BY ";     // Suche Nummer Teil 2
  //-----------------------------------------------------------------------
  // End of SQL-Variablendefinitionen fuer das Modul Kundenmanagement

  // -----------------------------------------------------------------------
  // Definiert die Klasse Kundenliste. Eine Kundenliste wird im Kundenmanagement benutzt
  // Sie enthaelt verschiedene Arrays um einen Ueberblick ueber die vorhandenen Kunden zu geben
  class Kundenliste {

      // -----------------------------------------------------------------------
      // Membervariablen
      var $kunden_id_array = array();
      var $kunden_nr_array = array();
      var $status_array = array();
      var $neukunde_array = array();
      var $name_array = array();
      var $vorname_array = array();
      var $firma_array = array();
      var $ort_array = array();
      var $land_array = array();
      var $sortieren_nach = "undefined";
      var $abc = "undefined";

      // -----------------------------------------------------------------------
      // Konstruktor
      function Kundenliste() {

      }// End Konstruktor

      // -----------------------------------------------------------------------
      // Memberfunktionen

      // -----------------------------------------------------------------------
      // Diese Funktion macht eine Datenbankverbindung und liest die entsprechenden Kundendatensaetze aus.
      // Nachdem diese Funktion abgearbeitet wurde, stehen die Datensaetze in den entsprechenden Arrays zur Verfuegung.
      // Man kann nach Nachname oder nach Firmenname sortiert anzeigen. Es werden nur jeweils die Kunden
      // ausgelesen deren Firmen- resp. Nachname mit dem in $abc gegebenen Buchstaben / Zahl beginnt. Mit
      // $abc = alle, werden alle Kundendatensaetze ausgelesen.
      // Argumente: $sortieren_nach: Nachnamen | Firmennamen (String), $abc: A-Z | num | alle (String)
      // Rueckgabewert: true | false (Boolean)
      function get_kunden_liste($sortieren_nach, $abc) {

          // Globale Variablen einbinden
          global $Admin_Database;
          global $sql_get_kunden_liste_1;
          global $sql_get_kunden_liste_1_2_1;
          global $sql_get_kunden_liste_1_2_2;
          global $sql_get_kunden_liste_1_2_3;
          global $sql_get_kunden_liste_1_3_1;
          global $sql_get_kunden_liste_1_3_2;
          global $sql_get_kunden_liste_1_4_1;
          global $sql_get_kunden_liste_1_4_2;
          global $sql_get_kunden_liste_1_4_3;

          // Test ob Datenbank erreichbar ist
          if (! is_object($Admin_Database)) {
              die("<P><H1 class='content'>kundenliste_def_Error: Datenbank nicht erreichbar.get_kunden_liste</H1></P><BR>");
          }
          else {
              // Query zusammensetzen, abhaengig vom Sortierkriterium ($sortieren_nach) und vom Anfangsbuchstaben des
              // Sortierkriteriums ($abc) muessen SQL-Bestandteile aneinandergereiht werden.
              $query = $sql_get_kunden_liste_1;

              if ($sortieren_nach == "Nachnamen") {
                  // Nach Nachnamen sortieren
                  $sortier_krit = "Nachname";
                  switch ($abc) {
                      case 'num':
                          $query.=$sql_get_kunden_liste_1_4_1.$sql_get_kunden_liste_1_4_3.$sortier_krit;
                          break;
                      case 'alle':
                          $query.=$sql_get_kunden_liste_1_3_1.$sql_get_kunden_liste_1_3_2.$sortier_krit;
                          break;
                      case "A":
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1."A".$sql_get_kunden_liste_1_2_3.$sortier_krit.$sql_get_kunden_liste_1_2_1."Ä".$sql_get_kunden_liste_1_2_2.$sortier_krit;
                          break;
                      case "O":
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1."O".$sql_get_kunden_liste_1_2_3.$sortier_krit.$sql_get_kunden_liste_1_2_1."Ö".$sql_get_kunden_liste_1_2_2.$sortier_krit;
                          break;
                      case "U":
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1."U".$sql_get_kunden_liste_1_2_3.$sortier_krit.$sql_get_kunden_liste_1_2_1."Ü".$sql_get_kunden_liste_1_2_2.$sortier_krit;
                          break;
                      default:
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1.$abc.$sql_get_kunden_liste_1_2_2.$sortier_krit;
                  }// End switch
              }
              else {
                  // Nach Firmennamen sortieren
                  $sortier_krit = "Firma";
                  switch ($abc) {
                      case 'num':
                          $query.=$sql_get_kunden_liste_1_4_2.$sql_get_kunden_liste_1_4_3.$sortier_krit;
                          break;
                      case 'alle':
                          $query.=$sql_get_kunden_liste_1_3_1.$sql_get_kunden_liste_1_3_2.$sortier_krit;
                          break;
                      case "A":
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1."A".$sql_get_kunden_liste_1_2_3.$sortier_krit.$sql_get_kunden_liste_1_2_1."Ä".$sql_get_kunden_liste_1_2_2.$sortier_krit;
                          break;
                      case "O":
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1."O".$sql_get_kunden_liste_1_2_3.$sortier_krit.$sql_get_kunden_liste_1_2_1."Ö".$sql_get_kunden_liste_1_2_2.$sortier_krit;
                          break;
                      case "U":
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1."U".$sql_get_kunden_liste_1_2_3.$sortier_krit.$sql_get_kunden_liste_1_2_1."Ü".$sql_get_kunden_liste_1_2_2.$sortier_krit;
                          break;
                      default:
                          $query.=$sortier_krit.$sql_get_kunden_liste_1_2_1.$abc.$sql_get_kunden_liste_1_2_2.$sortier_krit;
                  }// End switch
              }
              //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query($query);
              while (is_object($RS) && $RS->NextRow()){
                  // Status des Kunden festlegen:
                  $status = $RS->GetField("gesperrt");
                  // Kundendaten des jeweiligen Kunden in interne Arrays abfuellen
                  $this->put_kunde($RS->GetField("Kunden_ID"),$RS->GetField("Kunden_Nr"),$status,
                                   $RS->GetField("Neukunde"),$RS->GetField("Nachname"),$RS->GetField("Vorname"),
                                   $RS->GetField("Firma"),$RS->GetField("Ort"),$RS->GetField("Land"));
              }//End while
          }// End else Datenbank erreichbar oder nicht

          // Speichern der aktuellen Sortier- und Alphabetseinstellung
          $this->sortieren_nach = $sortieren_nach;
          $this->abc = $abc;
          return true;
      }// End function get_kunden_liste

      // -----------------------------------------------------------------------
      // Diese Funktion legt einen Kundendatensatz in den entsprechenden Objektinternen Arrays dieser Instanz ab
      // Argumente: $kunden_id, $kunden_nr, $status, $neukunde, $name, $vorname, $firma, $ort, $land (Strings)
      // Rueckgabewert: true bei Erfolg, false bei Fehler (oder Abbruch bei Fehler)
      function put_kunde($kunden_id, $kunden_nr, $status, $neukunde, $name, $vorname, $firma, $ort, $land) {
          // Eingabeargumente in interne Arrays schreiben
          $this->kunden_id_array[] = $kunden_id;
          $this->kunden_nr_array[] = $kunden_nr;
          $this->status_array[] = $status;
          $this->neukunde_array[] = $neukunde;
          $this->name_array[] = $name;
          $this->vorname_array[] = $vorname;
          $this->firma_array[] = $firma;
          $this->ort_array[] = $ort;
          $this->land_array[] = $land;
          // Rueckgabewert zurueckgeben
          return true;
      }// End function put_kunde

      // -----------------------------------------------------------------------
      // Gibt die Anzahl Kundendatensaetze in diesem Objekt zurueck
      // Argumente: keine
      // Rueckgabewert: Anzahl Kundendatensaetze (Integer)
      function get_anzahl_kunden() {
          return count($this->kunden_id_array);
      }// End function get_anzahl_kunden

      // -----------------------------------------------------------------------
      // Gibt das Sortierkriterium Nachnamen/Firmennamen der aktuellen Daten zurueck
      // Argumente: keine
      // Ruckgabewert: Sortierkriterium (String, entweder: Nachnamen oder Firmennamen)
      function get_sortieren_nach() {
          return $this->sortieren_nach;
      }// End function get_sortieren_nach

      // -----------------------------------------------------------------------
      // Gibt den Buchstaben/num/alle zurueck, welches der Anfangsbuchstabe der abgelegten Arraydaten ist
      // Argumente: keine
      // Rueckgabewert: Anfangsbuchstabe des Sortierkriteriums (String, entweder: A..Z oder num oder alle)
      function get_abc() {
          return $this->abc;
      }// End function get_abc

  }// End class Kundenliste

  // ----------------------------------------------------------------------------------
  // Test dieser Klasse
  //    $testklasse = new Kundenliste();
  //    $testklasse->get_kunden_liste("Nachnamen","alle");
  //    debug("Sortierkriterium: ".$testklasse->get_sortieren_nach().", Anfangsbuchstaben: ".$testklasse->get_abc().", Anzahl Kunden: ".$testklasse->get_anzahl_kunden());
  //    debug($testklasse->kunden_id_array);
  //    debug($testklasse->kunden_nr_array);
  //    debug($testklasse->status_array);
  //    debug($testklasse->neukunde_array);
  //    debug($testklasse->name_array);
  //    debug($testklasse->vorname_array);
  //    debug($testklasse->firma_array);
  //    debug($testklasse->ort_array);
  //    debug($testklasse->land_array);
  // Ende Test der Klasse -------------------------------------------------------------

  // End of file-----------------------------------------------------------------------
?>
