<?php
  // Zweck: Datenbank-Anbindung fuer PHP-Skripte
  //
  // Modul: Datenbank Anbindung (Datei wird von der Shop Installation personalisiert)
  //
  // Michael Baumer <baumi@vis.ethz.ch>
  //
  // 1999, Citrin, Feisthammel & Partner
  //
  // MySQL-Enhancement 2001 Jose Fontanil, Reto Glanzmann
  //
  // Security status:              *** USER ***
  //
  // PhPepperShop (ZHW DA Fei01/1) Version: v.1.4
  //
  // CVS-Version / Datum: $Id: template_initialize.php,v 1.20 2003/05/24 18:41:47 fontajos Exp $
  //
  // -----------------------------------------------------------------------

  // Diese PHP-Datei erzeugt eine Instanz des benoetigten DB-Wrappers

  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $initialize = true;

  // Den Include-Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");


  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($database)) {include("database.php");}

  // Diese Pfadangaben werden vom Shop nicht benutzt
  $tmp= strrpos(getenv("PATH_INFO"), '/');
  $scriptname = substr(getenv("PATH_INFO"), $tmp+1);      // without /
  $scriptpath = substr(getenv("PATH_INFO"), 0, $tmp+1);   // including trailing /

  // ----- connect to database -----

  // Connect to sybase database (using sybuser instead of localhost)
  // $Database = new TSybaseDatabase("sybuser", "{shop_db}", "{shopuser}", '{shopuserpwd}');

  // Connect to mysql database
  $Database = new TMySQLDatabase("{hostname}", "{shop_db}", "{shopuser}", '{shopuserpwd}');
  // End of file-----------------------------------------------------------------------
?>
