<?php
  // Filename: kunde_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Kunde
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: kunde_def.php,v 1.20 2003/05/24 18:41:23 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $kunde_def = true;

  // -----------------------------------------------------------------------
  // Definiert die Klasse Kunde, welche alle Daten eines Kunden fassen kann
  // Diese Klasse wurde vor allem dafuer ausgelegt, alle moeglichen Daten eines
  // Kunden speichern zu koennen. Wenn man nicht alle Variablen benutzt macht
  // das aber auch nichts, es sollen hiermit einfach alle Moeglichkeiten
  // abgedeckt sein.
  class Kunde {
      var $k_ID;       //Autoincrement ID von der Datenbank her
      var $Kunden_ID;  //Unique, nicht nachvollziehbare Zahl (Security, siehe createKundenID(),newKunde(...)) --> muss nummerisch sein!
      var $Kunden_Nr;  //Frei waehlbare Kunden-Nr fuer Geschaeftsprozesse
      var $Session_ID; //ist leer, wenn Kunde gerade nicht am bestellen ist
      var $Anrede;
      var $Vorname;
      var $Nachname;
      var $Firma;
      var $Abteilung;
      var $Strasse;
      var $Postfach;
      var $PLZ;
      var $Ort;
      var $Land;
      var $Tel;
      var $Fax;
      var $Email;
      var $Einkaufsvolumen;
      var $Beschreibung;  //Notizen des Shopadmins zum Kunden - der Kunde sieht dieses Feld nie
      var $LetzteBestellung;
      var $Login;
      var $Passwort;
      var $gesperrt;
      var $temp; //bedeutet, dass dieser Kunde nur bis Bestellungs_Abschluss existiert
      var $Attribut1;//Selbstkonfigurierte Eintraege
      var $Attribut2;
      var $Attribut3;
      var $Attribut4;
      var $Attributwert1;
      var $Attributwert2;
      var $Attributwert3;
      var $Attributwert4;
      var $kontoinhaber;
      var $bankname;
      var $blz;
      var $kontonummer;
      var $bankdaten_speichern;
      var $Bestellungsarray = array();

      //Konstruktor
      function Kunde() {
      }

      //putbestellung legt eine Bestellungs-Referenz (ID) im internen Array ab
      function putbestellung($Bestellungs_ID){
          $this->Bestellungsarray[] = $Bestellungs_ID;
      }

      //getallbestellungen liefert in einem Array alle Bestellungs_IDs zurueck
      function getallbestellungen(){
          $optpaar = array();
          foreach(($this->Bestellungsarray) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }
      //bestellungsanzahl liefert die Anzahl Elemente im Array $Bestellungsarray
      function bestellungsanzahl(){
          return count($this->Bestellungsarray);
      }
  }// End class Kunde

  // End of file-----------------------------------------------------------------------
?>
