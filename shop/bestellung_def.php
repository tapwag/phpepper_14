<?php
  // Filename: bestellung_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Bestellung und Artikel_info
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: bestellung_def.php,v 1.28 2003/05/30 12:22:08 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $bestellung_def = true;

  // -----------------------------------------------------------------------
  // Bestellungs Referenznummer Offset (Wenn der Shop läuft, darf diese Variable
  // NICHT mehr geaendert werden, da es sonst zu ueberschneidungen bei den Bestellungen
  // kommen koennte!!!)
  $bestellungs_offset = 154870; /**** ACHTUNG: NICHT AENDERN WENN DER SHOP SCHON LAEUFT ****/

  // -----------------------------------------------------------------------
  // Definition der Bestellungstags. Die Bestellung wird nach Abschluss in einem String gespeichert
  // Damit man diesen String rudimentaer parsen kann, sind SML-Tags definiert. Hier ist das Set der
  // Bestellungstags definiert (wird zum Parsen benoetigt).
  // Die Bestellungstags werden in den Modulen USER_BESTELLUNG_DARSTELLUNG.php und USER_BESTELLUNG_1.php verwendet.
  $bestellungs_tags = array('Bestellung','Absender','Kunde','Zahlungsart','Artikelliste','Artikelliste_Artikel','Gesamtpreis');

  // -----------------------------------------------------------------------
  // Definition eines Artikel_info-Objekts:
  // Artikel_info Objekte werden (voraussichtlich) nur in der weiter unten
  // beschriebenen Klasse Bestellung benutzt. Artikel_info Objekte enthalten
  // die Informationen eines Artikels wie er bei einer Bestellung relevant ist
  // Artikel_info-Objekte werden in der Klasse Bestellung im Array Artikelarray
  // abgelegt
  class Artikel_info {
      var $Artikel_ID;
      var $Artikel_Nr;
      var $Name;
      var $Anzahl;
      var $Preis;
      var $Gewicht; // Im Moment leider nur das Gewicht des Artikels (Opt / Var nicht dabei)
      var $Variationen = array();
      var $Optionen = array();
      var $Zusatzfelder = array();

      //Konstruktor
      function Artikel_info() {
      }

      //putoption legt eine Option eines Artikels im internen Array ab
      function putoption($cle, $wert){
          $this->Optionen[$cle] = $wert;
      }

      //putvariation legt eine Variation eines Artikels im internen Array ab
      function putvariation($cle, $wert){
          $this->Variationen[$cle] = $wert;
      }

      //getalloptionen liefert in einem assoz. Array alle Optionen zurück
      function getalloptionen(){
          $optpaar = array();
          foreach(($this->Optionen) as $keyname => $value){
              $optpaar[$keyname] = $value;
          }
          return $optpaar;
      }

      //getallvariationen liefert in einem assoz. Array alle Variationen zurück
      function getallvariationen(){
          $varpaar = array();
          foreach(($this->Variationen) as $keyname => $value){
              $varpaar[$keyname] = $value;
          }
          return $varpaar;
      }

      //Optionenanzahl liefert die Anzahl Elemente im Array $optionen
      //In v.1.05 wurde die Funktion sizeof() durch count() ersetzt!
      function Optionenanzahl(){
          return count($this->Optionen);
      }
  }// End class Artikel_info

  // --------------------------------------------------------------------------
  // Definition eines Bestellung-Objekts (Treuhandkosten sind nicht hier gespeichert):
  // Kann alle noetigen Information einer Bestellung aufnehmen (Wird fuer
  // Warenkorb-Operationen benoetigt)
  class Bestellung {
      var $Bestellungs_ID;
      var $Session_ID;
      var $Referenz_Nr;           // Nicht in der DB gespeichert. $Referen_Nr = $Bestellungs_ID + $bestellungs_offset
      var $Bestellung_abgeschlossen;
      var $Bestellung_ausgeloest;
      var $Bestellung_bezahlt;
      var $Bestellung_string;     // Enthaelt nach Abschluss der Bestellung, die ganze Bestellung wie im E-Mail inkl. SML-Tags
      var $Datum;
      var $Bezahlungsart;
      var $Kreditkarten_Hersteller;
      var $Kreditkarten_Nummer;
      var $Kreditkarten_Ablaufdatum;
      var $Kreditkarten_Vorname;
      var $Kreditkarten_Nachname;
      var $Attribut1;
      var $Attribut2;
      var $Attribut3;
      var $Attribut4;
      var $Attributwert1;
      var $Attributwert2;
      var $Attributwert3;
      var $Attributwert4;
      var $clearing_id;           // Transaktions-ID des externen Payment Instituts
      var $clearing_extra;        // Zusatzinfos vom externen Payment Institut
      var $Versandkosten;
      var $Mindermengenzuschlag;
      var $Rechnungsbetrag;       // Totalpreis der Bestellung. Bei Preisen exkl. MwSt OHNE MwSt., sonst. inkl. MwSt.
      var $Nachnahmebetrag;       // Zusaetzliche Nachnahmekosten (im Rechnungsbetrag bereits enthalten!)
      var $MwSt;                  // MwSt Anteil des Rechnungsbetrags. Bei Preisen exkl. MwSt. die faellige zusaetzliche MwSt.
      var $Anmerkung;
      var $Artikelarray = array();// Ein Array von Artikel_infos
      var $temp_message_string;   // Wenn mit Kreditkarten und externer Zahlungsabwicklung
                                  // gearbeitet wird, so muss der E-Mail-Message-String temporaer
                                  // zwischengespeichert werden.

      //Konstruktor
      function Bestellung() {
      }

      //putartikel legt einen Artikel (resp. dessen ID und Anzahl) im Array ab
      function putartikel($key, $value){
          $this->Artikelarray[$key] = $value;
      }

      //putallartikel kopiert den angegebenen Array in den eigenen Artikelarray
      function putallartikel($myarray){
          $this->Artikelarray = $myarray;
      }

      //getallartikel liefert in einem assoz. Array alle Artikel-IDs + Anzahlen zurueck
      function getallartikel(){
          $artpaar = array();
          foreach(($this->Artikelarray) as $keyname => $value){
              $artpaar[$keyname] = $value;
          }
          return $artpaar;
      }
     //artikelanzahl liefert die Anzahl Elemente im Array $Artikelarray
      function artikelanzahl(){
          return count($this->Artikelarray);
      }

  }// End class Bestellung

  // -----------------------------------------------------------------------------------
  // Funktionen um von der Referenznummer zur Bestellungs_ID und zurueck zu konvertieren

  // Konvertiert die angegebene Bestellungs Referenznummer in eine Bestellungs_ID
  function ref_to_bestellungs_id($ref_nr) {
      global $bestellungs_offset;
      return trim($ref_nr) - $bestellungs_offset;
  }// End function ref_to_bestellungs_id

  // Konvertiert die angegebene Bestellungs_ID in eine Bestellungs Referenznummer
  function bestellungs_id_to_ref($bestellungs_id) {
      global $bestellungs_offset;
      return trim($bestellungs_id) + $bestellungs_offset;
  }// End function bestellungs_id_to_ref

  // Liefert die in dieser Datei (oben) definierten Bestellungstags als Array zurueck
  // Argumente: keine
  // Rueckgabewert: Array mit Namen der Bestellungstags (Array)
  function getBestellungsTags() {
      global $bestellungs_tags;
      return $bestellungs_tags;
  }// End function getBestellungsTags

  // Filtert alle Bestellungstags aus einem uebergebenen String heraus
  // Argumente: zu filternder String (String)
  // Rueckgabewert: gefilterter String (String)
  function filterBestellungsTags($Bestellung) {
        $tagarray = getBestellungsTags();  // Definition der Funktion, siehe bestellung_def.php
        foreach ($tagarray as $value) {
            $Bestellung = preg_replace("°</?".$value."[0-9]*>°","",$Bestellung);
        }// End foreach
      return $Bestellung;
  }// End function getBestellungsTags

  // End of file--------------------------------------------------------------------------
?>
