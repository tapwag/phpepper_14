<?php
  // Zweck: Datenbank-Anbindung für PHP-Skripte (Admin)
  //
  // Modul: Datenbank Anbindung (Datei wird von der Shop Installation personalisiert)
  //
  // Michael Baumer <baumi@vis.ethz.ch>
  //
  // 1999, Citrin, Feisthammel & Partner
  //
  // MySQL-Erweiterung 2001 Jose Fontanil & Reto Glanzmann
  //
  // Version (ZHW-Diplomarbeit Fei01/1): v.1.4
  //
  // Projekt: PhPepperShop (www.phpeppershop.com)
  //
  // Security-status:                ***ADMIN***
  //
  // CVS-Version / Datum: $Id: template_ADMIN_initialize.php,v 1.19 2003/05/24 18:41:45 fontajos Exp $
  //
  // ------------------------------------------------------------------------------------

  // Diese PHP-Datei erzeugt eine Instanz des benoetigten DB-Wrappers (database.php)

  // ------------------------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  // Achtung: $ADMIN_Database ist nicht gleich $Admin_Database (DB-Conn)
  $ADMIN_initialize = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($initialize)) {include("initialize.php");}

  // Pfadangaben --> werden vom Shop-System nicht verwendet
  $tmp= strrpos(getenv("PATH_INFO"), '/');
  $scriptname = substr(getenv("PATH_INFO"), $tmp+1);      // without /
  $scriptpath = substr(getenv("PATH_INFO"), 0, $tmp+1);   // including trailing /

  // ----- connect to database
  // Connect to sybase database as Shop-Administrator (using sybuser instead of localhost)
  // $Admin_Database = new TSybaseDatabase("sybuser", "{shop_db}", "{shopadmin}", '{shopadminpwd}');

  // Connect to mysql database as Shop-Administrator
  $Admin_Database = new TMySQLDatabase("{hostname}", "{shop_db}", "{shopadmin}", '{shopadminpwd}');
  // End of file---------------------------------------------------------------------------
?>
