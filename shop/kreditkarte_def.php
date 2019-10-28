<?php
  // Filename: kreditkarte_def.php
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
  // CVS-Version / Datum: $Id: kreditkarte_def.php,v 1.12 2003/05/24 18:41:23 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $kreditkarte_def = true;

  // -----------------------------------------------------------------------
  // Definiert die Klasse Kreditkarte. Sie speichert fuer jede Kreditkarte einzeln
  // den Namen des Kreditkarten-Instituts (Hersteller), das Handling und das benutzen-Flag
  // Das Handling kann von der Datenbank her nur zwei Werte annehmen: 'intern', 'extern'.
  // Mit intern ist gemeint, dass wir die Bestellung komplett selbststaendig abwickeln.
  // Wenn extern angewaehlt ist, kann man ein Pay-Objekt mit der kompletten
  // Bestellung und Kunden-Information an einem Punkt abholen und die Bestellung als
  // akzeptiert oder nicht_akzeptiert wieder zurueck geben. Man kann die eigentliche Zahlung
  // dann in einem externen Programm abwickeln.
  class Kreditkarte {
      var $Hersteller;
      var $Handling;
      var $benutzen;
      var $Kreditkarten_ID;

      //Konstruktor
      function Kreditkarte() {
      }

  }// End class Kreditkarte

  // End of file-----------------------------------------------------------------------
?>
