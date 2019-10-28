<?php
  // Filename: mwst_def.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Definiert die Klasse MwSt
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: mwst_def.php,v 1.6 2003/05/30 12:22:08 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $mwst_def = true;

  // -----------------------------------------------------------------------
  // Definiert die Klasse MwSt - Abkuerzung fuer Mehrwertsteuer. Diese Klasse
  // beherbergt aber noch mehr, als nur den eigentlichen MwSt-Satz. Seit der
  // PhPepperShopversion v.1.2 hat jeder Artikel eine eigene Mehrwertsteuer.
  // Diese Konfiguration erlaubt es dem Shopbesitzer gleichzeitig Artikel mit
  // verschiedenen Mehrwertsteuersaetzen zu verkaufen.
  // Die Klasse hat mehrerer Attribute: Zuerst einmal eine ID, welche nur intern
  // verwendet wird. Zweitens den MwSt-Satz, welcher als Prozentzahl interpretiert
  // wird. Es wurde auch noch ein Beschreibungsattribut hinzugefuegt.
  // Weiter gibt es noch Tabellenweite Attribute: MwSt-default-Satz (bool-enum) wird
  // zu Rate gezogen, wenn eine neue Kategorie erstellt wird. Alle Artikel in dieser
  // Kategorie verwenden defaultmaessig diesen MwSt-Satz. Zuletzt gibt es noch das
  // Attribut Preise_inkl_MwSt. Ein boolscher Wert (Char: "Y","N"), entscheidet, ob
  // shopweit alle Artikelpreise inkl. oder exkl. Mehrwertsteuer angegeben wurden. Dem-
  // entsprechend faellt natuerlich auch die Preisberechnung und Warenkorbgestaltung aus.
  //
  // Anmerkung: Die Mehrwertsteuernummer und die boolsche Einstellung, ob der Shop Umsatz-
  // steuerpflichtig ist, werden in der Tabelle shop_settings gespeichert und sind ueber
  // die getmwstnr() Funktion abrufbar (0 = MwSt ist deaktiviert). Weitere MwSt-betreffende
  // Funktionen findet man in der Datei <shopdir>/shop/USER_ARTIKEL_HANDLING.php.
  class MwSt {
      var $Mehrwertsteuer_ID;
      var $MwSt_Satz;
      var $Beschreibung;
      var $MwSt_default_Satz;
      var $Preise_inkl_MwSt;  // Y fuer ja, inkl. oder N fuer nein, Preise sind exklusiv MwSt.
      var $Positions_Nr;

      //Konstruktor
      function MwSt() {
      }

  }// End class MwSt

  // End of file-----------------------------------------------------------------------
?>
