<?php
  // Filename: pop_up_admin.php
  //
  // Modul: POP_UP_ADMIN
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Ein grosses Bild eines Artikels in einem Fenster darstellen.
  //        Mit der Mögichkeit, das Fenster per Link zu schliessen
  //        Diese Funktion muss als eigene Datei ausprogrammiert werden (HTML)
  //
  // Sicherheitsstufe:                     *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: pop_up_admin.php,v 1.19 2003/05/24 18:41:39 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Dieses Modul bietet die gleiche Funktionalitaet wie das im Shop
  // verwendete Modul pop_up.php. Die Stylesheet-Datei wird jedoch vom
  // Administrationsbereich verwendet, so dass Layoutaenderungen am Shop
  // keine Aenderung bei der Ausgabe dieser Funktion zur Folge haben
  // -----------------------------------------------------------------------

  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $pop_up_admin = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($initialize)) {include("initialize.php");}
  if (!isset($USER_SQL_BEFEHLE)) {include("USER_SQL_BEFEHLE.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

  echo "<TITLE>Shop</TITLE>";
  echo "<META HTTP-EQUIV=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">";
  echo "<META HTTP-EQUIV=\"language\" CONTENT=\"de\">";
  echo "<META HTTP-EQUIV=\"author\" CONTENT=\"José Fontanil & Reto Glanzmann\">";
  echo "<LINK REL=STYLESHEET HREF=\"shopstyles.css\" TYPE=\"text/css\">";
  echo "<BODY class=\"content\">";
  echo '<center><img src="../ProdukteBilder/'.$HTTP_GET_VARS["bild_gross"].'"  border="0"></center>';
  // Link ausgeben, mit dem das Fenster wieder geschlossen werden kann
  echo '<center><p><A class="content"
        style="text-decoration:'.getcssarg("main_link_d").';
        color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").';
        font-weight:'.getcssarg("main_link_w").'"href="javascript:window.close();">Fenster schliessen</a></p></center>';
  echo "</BODY>";
  echo "</HTML>";

  // End of file-----------------------------------------------------------------------
?>
