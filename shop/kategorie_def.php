<?php
  // Filename: kategorie_def.php
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
  // CVS-Version / Datum: $Id: kategorie_def.php,v 1.14 2003/05/24 18:41:23 fontajos Exp $
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $kategorie_def = true;

  // -----------------------------------------------------------------------
  // Definition eines Kategorie-Objekts:
  class Kategorie {
      //Instanzvariablen
      var $Kategorie_ID;
      var $Name;
      var $Positions_Nr;
      var $Beschreibung;
      var $Details_anzeigen;
      var $Bild_gross;
      var $Bild_klein;
      var $Bildtyp;
      var $Bild_last_modified;
      var $MwSt_default_Satz;  //Vorgewaehlter MwSt-Satz
      var $Unterkategorien = array();

      //Konstruktor
      function Kategorie() {
      }

      //putkategorie legt eine Unterkategorie einer Kategorie im internen Array ab
      function putunterkategorie($cle, $wert){
          $this->Unterkategorien[$cle] = $wert;
      }

      //getallkategorien liefert in einem assoz. Array alle Unterkategorien zurück
      function getallkategorien(){
          $kat = array();
          foreach(($this->Unterkategorien) as $keyname => $value){
              $kat[$keyname] = $value;
          }
          return $kat;
      }

      //getallkategorien liefert in einem assoz. Array alle Unterkategorien zurück
      function getFirstUkat(){
          return $this->Unterkategorien[0];
      }

      //kategorienanzahl liefert die Anzahl Elemente im Array $Unterkategorien
      function kategorienanzahl(){
              return count($this->Unterkategorien);
      }

  }// End class Kategorie

  // Eine Unterkategorie kann nur EINER Kategorie zugeordnet werden
  class Unterkategorie extends Kategorie {
      //Instanzvariablen
      var $Unterkategorie_von;

      //Konstruktor
      function Unterkategorie() {
          $Unterkategorie_von = "";
      }


      //getUnterkategorie_von liefert den Namen der Kategorie, welcher diese
      //Unterkategorie zugeordnet ist
      function getUnterkategorie_von(){
          return $this->Unterkategorie_von;
      }

      //setUnterkategorie_von setzt den Namen der Kategorie, welcher diese
      //Unterkategorie zugeordnet ist
      function setUnterkategorie_von($Eingabewert){
          $this->Unterkategorie_von = $Eingabewert;
      }

  }// End class Unterkategorie

  // End of file-----------------------------------------------------------------------
?>
