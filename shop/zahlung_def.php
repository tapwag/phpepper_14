<?php
  // Filename: zahlung_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse Zahlung (beinhaltet weitere Zahlungsmethoden)
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: zahlung_def.php,v 1.9 2003/05/24 18:41:27 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $zahlung_def = true;

  // -----------------------------------------------------------------------
  // Definiert die Klasse Zahlung. Zahlung ist eine Klasse, deren Objekte verwendet
  // werden um Zeilen aus der Tabelle zahlung_weitere einfach transportieren zu koennen.
  // Mit dem Tabellenattribut Gruppe ist die Beschreibung der Zahlungsmethode gedacht.
  // Man kann auf diese Weise mehrere Zahlungsmethoden in Gruppen zusammenfassen, dies
  // hat auf die Darstellung der Zahlungsmethoden (Auswahlbox) einen Einfluss. Gibt es
  // von einer Gruppe nur einen Bezeichner, so wird eine Checkbox mit der Bezeichnung der
  // Zahlungsmethode erzeugt. Gibt es mehrere Bezeichnungen, welche alle derselben Gruppe
  // angehoeren, so wird ein Pulldown-Menü kreiert, in welcher die Zahlungen aufgelistet
  // werden.
  // Wenn das Flag $verwenden = 'Y' ist, so wird diese Zahlungsmethode als aktiv angesehen
  // und dem Kunden zur Auswahl angeboten. $verwendet kann nur die beiden Zustaende 'Y' und
  // 'N' annehmen. In der Variable $payment_interface_name wird der Name des fuer diese
  // Zahlungsmethode zustaendigen payment_interfaces gespeichert. (Bei billBOX z.B.
  // payment_interface_billBOX.php). Wenn der Kunde die entsprechende Zahlungsmethode an-
  // waehlt, so wird das speziell fuer diese Zahlungsmethode entwickelte payment_interface-Modul
  // verwendet. Das entsprechende Payment-Interface muss auf die Parameter abgeglichen sein!
  // Wenn eine Zahlung intern abgearbeitet wird (im Modul USER_BESTELLUNG_1.php), so soll in diesem
  // Feld eine Anmerkung dazu geschrieben werden. Wichtig ist dann v.a., welches darstellen=xyz es ist.
  // Im $Parameterarray werden alle Parameter (1-10) gespeichert. Man kann also zu den 25 schon
  // vom Payment-Interface bereitgestellten Parameter (Name, Rechnungsbetrag,...) noch weitere 10
  // definieren. Auf den internen Parameterarray kann man via die bereitgestellten Funktionen zugreifen.
  class Zahlung {
      var $Gruppe;
      var $Bezeichnung;
      var $verwenden;
      var $payment_interface_name;
      var $Parameterarray = array();

      //Konstruktor
      function Zahlung() {
      }

      //putzahlung legt eine Zahlung im internen Array ab (ans Array-Ende)
      function delarray(){
          $this->Parameterarray = array();
      }

      //putparameter legt einen Parameter im internen Array ab (ans Array-Ende)
      function putparameter($wert){
          $this->Parameterarray[] = $wert;
      }

      //getallparameter liefert in einem Array alle Parameter zurueck
      function getallparameter(){
          $kat = array();
          foreach(($this->Parameterarray) as $keyname => $value){
              $kat[$keyname] = $value;
          }
          return $kat;
      }

      //parameteranzahl liefert die Anzahl Elemente im Array $Parameterarray
      function parameteranzahl(){
              return count($this->Parameterarray);
      }


  }// End class Zahlung

  // Die Klasse Allezahlungen beinhaltet eine bis mehrere Zahlungsobjekte in einem Array
  // Auf den Array kann komfortabel via Zugriffsfunktionen zugegriffen werden.
  class Allezahlungen {
      var $Zahlungsarray = array();

      //Konstruktor
      function Allezahlungen() {
      }

      //putzahlung legt eine Zahlung im internen Array ab (ans Array-Ende)
      function putzahlung($wert){
          $this->Zahlungsarray[] = $wert;
      }

      //getallzahlungen liefert in einem Array alle Zahlungen zurueck
      function getallzahlungen(){
          $kat = array();
          foreach(($this->Zahlungsarray) as $keyname => $value){
              $kat[$keyname] = $value;
          }
          return $kat;
      }

      //zahlungsanzahl liefert die Anzahl Elemente im Array $Zahlungsarray
      function zahlungsanzahl(){
              return count($this->Zahlungsarray);
      }
  }// End class Allezahlungen

  // End of file-----------------------------------------------------------------------
?>
