<?php
  // Filename: artikel_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Artikel
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: artikel_def.php,v 1.19 2003/05/24 18:41:21 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $artikel_def = true;

  // -----------------------------------------------------------------------
  // Definition eines Artikel-Objekts:
  class Artikel {
      var $artikel_ID;
      var $artikel_Nr;
      var $name;
      var $beschreibung;
      var $letzteAenderung;
      var $gewicht;
      var $preis;
      var $aktionspreis;
      var $link;
      var $bild_gross;
      var $bild_klein;
      var $bildtyp;
      var $bild_last_modified;
      var $aktionspreis_verwenden;
      var $optionen = array();
      var $optionen_gewicht = array();
      var $variationen = array();
      var $variationen_gruppe = array();
      var $variationen_gewicht = array();    // Gewicht der Variation
      var $var_gruppen_text = array();       // Variationsgruppen-Nummern
      var $var_gruppen_darst = array();      // Variationsgruppen-Bezeichnung
      var $zusatzfelder_text = array ();     // Text fuerr die zusaetzlichen Eingabefelder
      var $zusatzfelder_param = array ();    // Parameter fuer die zusaetzlichen Eingabefelder
      var $mwst_satz;                        // Mehrwertsteuer-Satz
      var $aktion_von;                       // Wann die Aktion für den Artikel starten soll
      var $aktion_bis;                       // Bis wann die Aktion für den Artikel dauern soll

      //Konstruktor
      function Artikel() {
      }

      //putoption legt eine Option eines Artikels im internen Array ab
      function putoption($cle, $wert){
          $this->optionen[$cle] = $wert;
      }

      //putopt_gewicht legt den Gewichstaufschlag einer Option in einem Array ab
      function putopt_gewicht($cle, $wert){
          $this->optionen_gewicht[$cle] = $wert;
      }

      //getalloptionen liefert in einem assoz. Array alle Optionen zurück
      function getalloptionen(){
          $optpaar = array();
          foreach(($this->optionen) as $keyname => $value){
              $optpaar[$keyname] = $value;
          }
          return $optpaar;
      }
      //optionenanzahl liefert die Anzahl Elemente im Array $optionen
      function optionenanzahl(){
          return count($this->optionen);
      }

      //putvariation legt eine Option eines Artikels im internen Array ab
      function putvariation($cle, $wert){
          $this->variationen[$cle] = $wert;
      }

      //putvar_gruppe legt eine Variations-Gruppe einer Variation des Artikels in einem Array ab
      function putvar_gruppe($cle, $wert){
          $this->variationen_gruppe[$cle] = $wert;
      }

      //putvar_gewicht legt den Gewichstaufschlag einer Variation in einem Array ab
      function putvar_gewicht($cle, $wert){
          $this->variationen_gewicht[$cle] = $wert;
      }

      //getallvar_gruppe liefert in einem assoz. Array alle Variationsgruppen zurueck
      function getallvar_gruppe(){
          $optpaar = array();
          foreach(($this->variationen_gruppe) as $keyname => $value){
              $optpaar[$keyname] = $value;
          }
          return $optpaar;
      }

      //getallvariationen liefert in einem assoz. Array alle Optionen zurück
      function getallvariationen(){
          $optpaar = array();
          foreach(($this->variationen) as $keyname => $value){
              $optpaar[$keyname] = $value;
          }
          return $optpaar;
      }

      //variationenanzahl liefert die Anzahl Elemente im Array $variationen
      function variationenanzahl(){
          return count($this->variationen);
      }
  }// End class Artikel

  // -----------------------------------------------------------------------
  // Wird in gewissen Funktionen zur einfacheren Parameteruebergabe verwendet
  // Definition eines Options-Objekts:
  class Option {
      var $Optionen_Nr;
      var $Optionstext;
      var $Preisdifferenz;
      var $FK_Artikel_ID;
      var $Gewicht;
  }// End class Option

  // -----------------------------------------------------------------------
  // Wird in gewissen Funktionen zur einfacheren Parameteruebergabe verwendet
  // Definition eines Variations-Objekts:
  class Variation {
      var $Variations_Nr;
      var $Variationstext;
      var $Aufpreis;
      var $Gruppe_darstellen;
      var $FK_Artikel_ID;
      var $Gewicht;
  }// End class Variation

  // -----------------------------------------------------------------------
  // Damit wir einen Artikel mit zugehoerigen Kategorien sinnvoll transportieren
  // koennen, haben wir diese Klasse definiert
  class Artikelmitkategorien {
      var $myArtikel;
      var $myKategorienarray = array();

      //Konstruktor
      function Artikelmitkategorien() {
          $myArtikel = new Artikel;
      }
  }// End class Artikelmitkategorien

  // End of file-----------------------------------------------------------------------
?>
