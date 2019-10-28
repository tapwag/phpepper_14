<?php
  // Filename: versandkosten_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Versandkosten und Versandkostenpreis
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: versandkosten_def.php,v 1.15 2003/05/24 16:36:46 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $versandkosten_def = true;

  // -----------------------------------------------------------------------
  // Versandkosten Klasse, enthaelt alle Attribute welche die Versandkosten
  // und die Versandkostenkalkulation betreffen. Weiter enthaelt die Klasse
  // einen Array von Versandkostenpreisen und weiteren Arrays.
  class Versandkosten {
      var $Abrechnung_nach_Preis;
      var $Abrechnung_nach_Gewicht;
      var $Abrechnung_nach_Pauschale;
      var $Pauschale_kostenfrei_ab;
      var $Pauschale_text;
      var $Rechnung;
      var $Vorauskasse;
      var $Kreditkarte;
      var $Nachname;
      var $Nachnamebetrag;
      var $Lastschrift;
      var $Postcard;
      var $keineVersandkostenmehr;
      var $keineVersandkostenmehr_ab;
      var $anzahl_Versandkostensettings;
      var $Mindermengenzuschlag;
      var $Mindermengenzuschlag_Aufpreis;
      var $Mindermengenzuschlag_bis_Preis;
      var $Waehrung;
      var $Gewichts_Masseinheit;
      var $MwStpflichtig;
      var $MwStNummer;
      var $MwStsatz;
      var $Setting_Nr;
      var $Shopname;
      var $Versandkostenpreise = array();
      var $Rechnungsarray = array();
      var $Vorauskassenarray = array();
      var $Nachnamearray = array();
      var $Kreditkartenarray = array();
      var $billBOXarray = array();
      var $Treuhandzahlungarray = array();
      var $Lastschriftarray = array();
      var $Postcardarray = array();

      //Konstruktor
      function Versandkosten() {
      }

      //putversandkostenpreis legt einen Versandkostenpreis im internen Array ab
      function putversandkostenpreis($Preis){
          $this->Versandkostenpreise[] = $Preis;
      }

      //getallversandkostenpreise liefert in einem Array alle Versandkostenpreise
      function getallversandkostenpreise(){
          return $this->Versandkostenpreise;
      }

      //versandkostenpreiseanzahl() liefert die Anzahl Elemente im Array $Versandkostenpreise
      function versandkostenpreiseanzahl(){
          return count($this->Versandkostenpreise);
      }

      function putzeile($preis, $rech, $voraus, $nach, $last, $kredit, $bill, $treuhand, $postc) {
          $this->Versandkostenpreise[] = $preis;
          $this->Rechnungsarray[] = $rech;
          $this->Vorauskassenarray[] = $voraus;
          $this->Nachnamearray[] = $nach;
          $this->Lastschriftarray[] = $last;
          $this->Kreditkartenarray[] = $kredit;
          $this->billBOXarray[] = $bill;
          $this->Treuhandzahlungarray[] = $treuhand;
          $this->Postcardarray[] = $postc;
      }
  }// End class Versandkosten

  // -----------------------------------------------------------------------
  // Definition der Klasse Versandkostenpreis. Sie enthaelt jeweils ein Intervall
  // deklariert durch die beiden Membervariablen Von und Bis. Jedem Intervall wird
  // ein Betrag zugeordnet.
  class Versandkostenpreis {
      var $Von_Bis_ID;
      var $Von;
      var $Bis;
      var $Betrag;
      var $Vorauskasse;
      var $Rechnung;
      var $Nachname;
      var $Lastschrift;
      var $Kreditkarte;
      var $billBOX;
      var $Treuhandzahlung;
      var $Postcard;

      //Konstruktor
      function Versandkostenpreis() {
      }

  }// End class Versandkostenpreis

  // End of file-----------------------------------------------------------------------
?>