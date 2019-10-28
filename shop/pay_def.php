<?php
  // Filename: pay_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Pay welche alle Informationen einer
  //        Zahlung enthaelt (Kundendaten und Bestellungsdaten, Flags)
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: pay_def.php,v 1.15 2003/05/24 18:41:24 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $pay_def = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (PHP_OS == "WINNT" || PHP_OS == "WIN32" || strlen($HTTP_ENV_VARS["windir"]) > 0) {$pd = ";";} else {$pd = ":";}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($initialize)) {include("initialize.php");}
  if (!isset($bestellung_def)) {include("bestellung_def.php");}

  // -----------------------------------------------------------------------
  // Definiert die Klasse Pay, welche alle Daten einer Zahlung fassen kann
  // Diese Klasse ist auch als Schnittstelle zu anderen Zahlungsinstitutionen
  // (z.B. SaferPay, Yelloworld,...) gedacht.
  class Pay {

      //Instanzvariablen:
      var $Pay_ID;       //Hier ist eine Bestellungs-Referenz-Nr gespeichert ($bestellungs_offset+Bestellungs_ID)
      var $myKunde;      //Kunden-Objekt, siehe auch kunde_def.php
      var $myBestellung; //Bestellung-Objekt, siehe auch bestellung_def.php
      var $myReferrer;   //URL zur Ruecksprung-Homepage nach erfolgter Zahlung
      var $Erfolg;       //Zahlung erfolgreich = 1, Zahlung nicht erfolgreich = 0
      var $Errormessage; //Bei nicht erfolgreicher Zahlung, kann hier optional eine Meldung hinterlegt werden

      //Konstruktor:
      function Pay() {
          //Zahlung als nicht erfolgreich initialisieren
          $Erfolg = 0;
      }

      //Methoden:

      //putKunde speichert ein Kunde-Objekt in dieses Pay-Objekt
      //Argumente: Kunde-Objekt
      //Rueckgabewert: immer true
      function putKunde($Kunde){
          $this->myKunde = $Kunde;
          return true;
      }

      //getKunde liefert den Kunden dieser Zahlung als Kunden-Objekt zurueck
      //Argumente: keine
      //Rueckgabewert: Kunde-Objekt
      function getKunde(){
          return $this->myKunde;
      }
      //putBestellung speichert ein Bestellung-Objekt in dieses Pay-Objekt
      //Argumente: Bestellung-Objekt
      //Rueckgabewert: immer true
      function putBestellung($Bestellung){
          $this->Pay_ID = bestellungs_id_to_ref($Bestellungs_ID);//Referenznummer schreiben
          $this->myBestellung = $Bestellung;
          return true;
      }

      //getBestellung liefert die Bestellung (Warenkorb) dieser Zahlung
      //Argumente: keine
      //Rueckgabewert: Bestellung-Objekt
      function getBestellung(){
          return $this->myBestellung;
      }
      //putReferrer speichert den URL der Ruecksprung Page
      //Rueckkehr nach erfolgter Zahlung
      //Argumente: String
      //Rueckgabewert: immer true
      function putReferrer($Referrer){
          $this->myReferrer = $Referrer;
          return true;
      }

      //getReferrer liefert den URL der Ruecksprung Page nach erfolgter Zahlung
      //Dort wird dann die Variable $Erfolg ausgewertet
      //Argumente: keine
      //Rueckgabewert: String
      function getReferrer(){
          return $this->myReferrer;
      }
      //putErfolg speichert in einem Flag, ob die externe Zahlung erfolgreich
      //oder erfolglos war: ERFOLG = 1   /   KEIN ERFOLG = 0
      //Rueckkehr nach erfolgter Zahlung
      //Argumente: Int (Es sind nur die Werte 0 oder 1 erlaubt!)
      //Rueckgabewert: true falls Wert akzeptiert wird, sonst false (dann: Erfolg auch false)
      function putErfolg($success){
          if (($success == 0) || ($success == 1)) {
              $this->Erfolg = $success;
              return true;
          }
          else {
              $this->Erfolg = 0;
              return false;
          }
      }
      //getErfolg liest das Flag $Erfolg aus. Es kann entweder den Wert 0 oder 1 haben
      //Bedeutung: Erfolgreiche Zahlung: 1, sonst 0.
      //Argumente: keine
      //Rueckgabewert: Int (es koennen nur die Werte 0 oder 1 vorkommen)
      function getErfolg(){
          return $this->Erfolg;
      }
      //putErrormessage erlaubt es eine beliebige Fehlermeldung als String zu uebergeben
      //Argumente: String
      //Rueckgabewert: immer true
      function putErrormessage($Fehler){
          return true;
      }
      //getErrormessage liest die im Fehlerfall optional mitgegebene Fehlermeldung aus
      //der Variable $Errormessage aus und gibt diesen String zurueck
      //Argumente: keine
      //Rueckgabewert: String
      function getErrormessage(){
          return $this->Errormessage;
      }

  }// End class Pay

  // End of file-----------------------------------------------------------------------
?>
