<?php
  // Filename: attribut_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Attribut
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: attribut_def.php,v 1.12 2003/05/24 18:41:21 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $attribut_def = true;

  // -----------------------------------------------------------------------
  // Definiert die Klasse Attribut. Es existiert eine Tabelle attribut, wo diese
  // Attribute gespeichert werden. Die Attribute werden dazu verwendet um dynamisch
  // Kundendatenfelder konfigurieren zu koennen. Man kann z.B. das Eingabefeld Strasse
  // erzeugen und diesem (Kunden-)Attribut dann mitteilen, ob es angezeigt werden soll,
  // ob seine Eingabe ueberprueft werden soll, an welcher Position es angezeigt werden soll
  // und ob es persistent zu jedem Kunden mit in der Datenbank gespeichert werden soll (CRM)
  class Attribut {
      var $Attribut_ID = array();
      var $Name = array();
      var $Wert = array();
      var $anzeigen = array();
      var $in_DB = array();
      var $Eingabe_testen = array();
      var $Positions_Nr = array();

      //Konstruktor
      function Attribut() {
      }

      //putAttribut legt ein komplettes Attribut in diesem Objekt ab
      function putAttribut($myAttribut_ID,$myName,$myWert,$myanzeigen,$myin_DB,$myEingabe_testen,$myPositions_Nr){
          $this->Attribut_ID[] = $myAttribut_ID;
          $this->Name[] = $myName;
          $this->Wert[] = $myWert;
          $this->anzeigen[] = $myanzeigen;
          $this->in_DB[] = $myin_DB;
          $this->Eingabe_testen[] = $myEingabe_testen;
          $this->Positions_Nr[] = $myPositions_Nr;
      }

      //getallAttribut_ID liefert in einem Array alle Attribut_IDs zurueck
      function getallAttribut_ID(){
          $optpaar = array();
          foreach(($this->Attribut_ID) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }

      //getallName liefert in einem Array alle Namen zurueck
      function getallName(){
          $optpaar = array();
          foreach(($this->Name) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }

      //getallWert liefert in einem Array alle Werte zurueck
      function getallWert(){
          $optpaar = array();
          foreach(($this->Wert) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }

      //getallanzeigen liefert in einem Array alle anzeigen-Flags zurueck
      function getallanzeigen(){
          $optpaar = array();
          foreach(($this->anzeigen) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }

      //getallin_DB liefert in einem Array alle in_DB-Flags zurueck
      function getallin_DB(){
          $optpaar = array();
          foreach(($this->in_DB) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }

      //getallEingabe_testen liefert in einem Array alle Eingabe_testen-Flags zurueck
      function getallEingabe_testen(){
          $optpaar = array();
          foreach(($this->Eingabe_testen) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }

      //getallPositions_Nr liefert in einem Array alle Positions-Nummern zurueck
      function getallPositions_Nr(){
          $optpaar = array();
          foreach(($this->Positions_Nr) as $keyname => $value){
              $optpaar[] = $value;
          }
          return $optpaar;
      }

      //attributanzahl liefert die Anzahl Elemente im Array $Bestellungsarray
      function attributanzahl(){
          return count($this->Attribut_ID);
      }

  }// End class Attribut

  // End of file-----------------------------------------------------------------------
?>
