<?php
  // Filename: pop_up.php
  //
  // Modul: POP_UP
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Ein grosses Bild eines Artikels in einem Fenster darstellen.
  //        Mit der Mögichkeit, das Fenster per Link zu schliessen
  //        Diese Funktion muss als eigene Datei ausprogrammiert werden (HTML)
  //
  // Sicherheitsstufe:                     *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: pop_up.php,v 1.20 2003/06/15 21:21:10 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $pop_up = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Wenn der Haendlermodus aktiviert wurde (alle Kunden muessen sich zuerst einloggen),
  // dann ueberprueft folgender Link, ob man schon eingeloggt ist:
  require('USER_AUTH.php');

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($initialize)) {include("initialize.php");}
  if (!isset($USER_SQL_BEFEHLE)) {include("USER_SQL_BEFEHLE.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

  echo "<html>\n";
  echo "<head>\n";
  echo "<title>Shop</title>\n";
  echo "<META HTTP-EQUIV=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">\n";
  echo "<META HTTP-EQUIV=\"language\" CONTENT=\"de\">\n";
  echo "<META HTTP-EQUIV=\"author\" CONTENT=\"José Fontanil & Reto Glanzmann\">\n";
  echo "<LINK rel=\"STYLESHEET\" href=\"shopstyles.css\" type=\"text/css\">\n";
  echo "</head>\n";
  echo "<body class=\"content\">\n";
  echo '<center><img src="./ProdukteBilder/'.$_GET["bild_gross"].'"  border="0"></center>'."\n";

  // Link ausgeben, mit dem das Fenster wieder geschlossen werden kann
  echo '<center><p><A class="content"
        style="text-decoration:'.getcssarg("main_link_d").';
        color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").';
        font-weight:'.getcssarg("main_link_w").'"href="javascript:window.close();">Fenster schliessen</a></p></center>'."\n";
  echo "</body>\n";
  echo "</html>\n";

  // End of file-----------------------------------------------------------------------
?>
