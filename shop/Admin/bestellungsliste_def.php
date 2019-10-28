<?php
  // Filename: kunde_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Bestellungsliste
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: bestellungsliste_def.php,v 1.5 2003/05/24 18:41:37 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $bestellungsliste_def = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux/MacOS beruecksichtigt.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux/MacOS --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_Database)) {include("ADMIN_initialize.php");}   // Administrationsdatenbank Connection
  if (!isset($bestellung_def)) {include("bestellung_def.php");}     // Hier sind die ref_nr <-->  bestellungs_id Funktionen drin

  //----------------------------------------------------------------------------
  // Zweck: Im folgenden Abschnitt werden die in diesem Modul benoetigten SQL-Queries definiert.
  //        Weiter SQL-Queries finden sich in den Dateien ../USER_SQL_BEFEHLE.php und ADMIN_SQL_BEFEHLE.php.
  // Variabelnamenaufbau: sql_ NAME DES SCRIPTS _suffix ... suffix = hochzaehlende Zahl
  //        (Falls eine Query aus mehreren hier definierten Variablen zusammengesetzt wird, so
  //        ist der suffix nochmals angehaengt und dort eine Laufvariable eingesetzt worden).
  //----------------------------------------------------------------------------
  $sql_get_bestellungs_liste_1_1 = "SELECT b.Bestellungs_ID, b.Datum, b.Bestellung_abgeschlossen, b.Bestellung_ausgeloest, b.Bestellung_bezahlt, b.Bezahlungsart
                                    FROM bestellung AS b, bestellung_kunde AS bk WHERE b.Bestellungs_ID = bk.FK_Bestellungs_ID AND bk.FK_Kunden_ID=";
  $sql_get_bestellungs_liste_1_2 = " AND b.Datum >= '";
  $sql_get_bestellungs_liste_1_3 = " AND b.Datum <= '";
  $sql_get_bestellungs_liste_1_4 = "'";
  $sql_get_bestellungs_liste_1_5 = " ORDER BY b.Bestellungs_ID DESC";

  //-----------------------------------------------------------------------
  // End of SQL-Variablendefinitionen fuer das Modul Kundenmanagement


  // -----------------------------------------------------------------------
  // Definiert die Klasse Bestellungsliste
  class Bestellungsliste {

      // -----------------------------------------------------------------------
      // Membervariablen
      var $bestellungs_id_array = array();
      var $bestelldatum_array = array();
      var $status_array = array();
      var $ref_nr_array = array();
      var $zahlungsart_array = array();
      var $Kunden_ID = 'undefined';

      // -----------------------------------------------------------------------
      // Konstruktor
      function Bestellungsliste() {

      }// End Konstruktor

      // -----------------------------------------------------------------------
      // Memberfunktionen

      // -----------------------------------------------------------------------
      // Liste der Bestellungen des Kunden (mit der Kunden_ID $kunden_id) aus der
      // Datenbank auslesen und in die internen Arrays abfuellen.
      // Die beiden optionalen Argumente $von und $bis sind Datumswerte. Wenn man
      // die Argumente mit Daten fuellt, so werden nur die Bestellungen von Datum
      // $von bis Datum $bis.
      // Argumente: $kunden_id (String), $von (String), $bis ($String)
      // Rueckgabewert: true | false (Boolean)
      function get_bestellungs_liste($kunden_id,$von="",$bis="") {

          // Globale Variablen einbinden
          global $Admin_Database;
          global $sql_get_bestellungs_liste_1_1;
          global $sql_get_bestellungs_liste_1_2;
          global $sql_get_bestellungs_liste_1_3;
          global $sql_get_bestellungs_liste_1_4;
          global $sql_get_bestellungs_liste_1_5;

          // Test ob Datenbank erreichbar ist
          if (! is_object($Admin_Database)) {
              die("<P><H1 class='content'>kundenliste_def_Error: Datenbank nicht erreichbar.get_kunden_liste</H1></P><BR>");
          }
          else {
              // Query zusammenstellen
              $query = $sql_get_bestellungs_liste_1_1.$kunden_id;

              if ($von != "") {
                  $query.=$sql_get_bestellungs_liste_1_2.$von.$sql_get_bestellungs_liste_1_4;
              }

              if ($bis != "") {
                  $query.=$sql_get_bestellungs_liste_1_3.$bis.$sql_get_bestellungs_liste_1_4;
              }

              $query.=$sql_get_bestellungs_liste_1_5;

              //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe <shopdir>/shop/database.php)
              $RS = $Admin_Database->Query($query);
              while (is_object($RS) && $RS->NextRow()){
                  // Status der Bestellung ausfuellen:
                  $status = "Bestellung noch nicht abgeschlossen"; // Initialisierung
                  if ($RS->GetField("Bestellung_abgeschlossen") == "Y") {
                      if ($status == "Bestellung noch nicht abgeschlossen") {
                          $status = "Bestellung ist abgeschlossen";
                      }
                      else {
                          $status.=", abgeschlossen";
                      }
                  }
                  elseif ($RS->GetField("Bestellung_ausgeloest") == "Y") {
                      if ($status == "Bestellung noch nicht abgeschlossen") {
                          $status = "Bestellung wurde ausgel&ouml;st";
                      }
                      else {
                          $status.=", ausgel&ouml;st";
                      }
                  }
                  elseif ($RS->GetField("Bestellung_bezahlt") == "Y") {
                      if ($status == "Bestellung noch nicht abgeschlossen") {
                      $status = "Bestellung wurde bezahlt";
                      }
                      else {
                          $status.=", bezahlt";
                      }
                  }
                  // Kundendaten des jeweiligen Kunden in interne Arrays abfuellen
                  $this->put_bestellung($RS->GetField("Bestellungs_ID"),$RS->GetField("Datum"),$status,bestellungs_id_to_ref($RS->GetField("Bestellungs_ID")),$RS->GetField("Bezahlungsart"));
              }//End while
          }// End else Datenbank erreichbar oder nicht

          // Speichern der aktuellen Kunden_ID des Kunden
          $this->Kunden_ID = $kunden_id;
          return true;
      }// End function get_bestellungs_liste

      // -----------------------------------------------------------------------
      // put_bestellung(...) legt einen Bestellungseintrag in die internen Arrays ab
      // Argumente: $bestellungs_id (String), $bestelldatum, $status (String), $ref_nr (Integer), $zahlungsart (String)
      function put_bestellung($bestellungs_id, $bestelldatum, $status, $ref_nr, $zahlungsart) {
          $this->bestellungs_id_array[] = $bestellungs_id;
          $this->bestelldatum_array[] = $bestelldatum;
          $this->status_array[] = $status;
          $this->ref_nr_array[] = $ref_nr;
          $this->zahlungsart_array[] = $zahlungsart;
          return true;
      }// End function put_bestellung

      // -----------------------------------------------------------------------
      // Diese Funktion liefert die Anzahl Bestellungen, welche momentan im Objekt
      // gehalten werden.
      // Argumente: keine
      // Rueckgabewert: Anzahl Bestellungen (Integer)
      function get_anzahl_bestellungen() {
          return count($this->bestellungs_id_array);
      }// End function get_anzahl_bestellungen

      // -----------------------------------------------------------------------
      // Diese Funktion liefert die Kunden_ID des Kunden, welchem alle momentan abge-
      // speicherten Bestellungen gehoeren
      // Argumente: keine
      // Rueckgabewert Kunden_ID (String)
      function get_kunden_id() {
          return $this->Kunden_ID;
      }// End function get_kunden_id

  }// End class Bestellungsliste

  // ----------------------------------------------------------------------------------
  // Test dieser Klasse
  //    $testklasse = new Bestellungsliste();
  //    $testklasse->get_bestellungs_liste("206788521011949891831034397988");  // Kunden_ID muss neu herein kopiert werden.
  //    debug("Kunden_ID: ".$testklasse->get_kunden_id().", Anzahl Bestellungen dieses Kunden: ".$testklasse->get_anzahl_bestellungen());
  //    debug($testklasse->bestellungs_id_array);
  //    debug($testklasse->datum_array);
  //    debug($testklasse->status_array);
  //    debug($testklasse->ref_nr_array);
  //    debug($testklasse->zahlungsart_array);
  // Ende Test der Klasse -------------------------------------------------------------

  // End of file-----------------------------------------------------------------------
?>
