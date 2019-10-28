<?php
  // Filename: USER_ADMIN_HILFE.php
  //
  // Modul: Hilfe
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Hilfe-Texte für USER und ADMIN Pages
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: USER_ADMIN_HILFE.php,v 1.26 2003/06/15 21:21:09 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_ADMIN_HILFE = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Wenn der Haendlermodus aktiviert wurde (alle Kunden muessen sich zuerst einloggen), dann ueberprueft folgender Link,
  // ob man schon eingeloggt ist
  require('USER_AUTH.php');

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($session_mgmt)) {include("session_mgmt.php");}
  if (!isset($initialize)) {include("initialize.php");}
  if (!isset($USER_SQL_BEFEHLE)) {include("USER_SQL_BEFEHLE.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // -----------------------------------------------------------------------
  // HTML_HEAD + body open
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="content-language" content="de">
    <meta name="author" content="Jose Fontanil and Reto Glanzmann">
    <title>Shop</title>
    <LINK rel="stylesheet" href="shopstyles.css" TYPE="text/css">
  </head>
  <body class="content">
    <p>
<?php

  // -----------------------------------------------------------------------
  // Diese Funktion liefert einen String der den entsprechenden Hilfetext
  // der angeforderten Seite enthaelt
  // Hilfe_ID = Filename und optionalem suffix:  _1 _2 ...
  // Argument: Hilfe_ID
  // Rueckgabewert: Text zur aktuellen Seite (String)
  function getHilfe($Hilfe_ID) {
      global $Database;
      global $sql_getHilfe_1_1;
      global $sql_getHilfe_1_2;
      if (! is_object($Database)) {
          die("<P><H1>USER_ADMIN_HILFE_Error: Datenbank nicht erreichbar</H1></P><BR>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Query("$sql_getHilfe_1_1".$Hilfe_ID."$sql_getHilfe_1_2");
          if (is_object($RS) && $RS->NextRow()){
              $Hilfetext = $RS->Getfield("Hilfetext");
          }
          else {
              echo "USER_ADMIN_HILFE: getHilfe: Konnte Hilfetext nicht auslesen / Kein Text vorhanden <BR>";
              die("Query: $sql_getHilfe_1_1.$Hilfe_ID.$sql_getHilfe_1_2<BR>");
          }
      }
      return $Hilfetext;
  }// End getHilfe


  // Styles fuer die Links aus der Datenbank auslesen und den Stylestring zusammenbauen
  $stylestring = 'class="content" style="text-decoration:'.getcssarg("main_link_d").';
        color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").';
        font-weight:'.getcssarg("main_link_w").'"';

  // Falls die Haupthilfe aufgerufen wird, hat der Shopbenutzer die Moeglichkeit, per Link zu den Kontaktinformationen
  // des Shops zu kommen
  if ($Hilfe_ID == "top"){
      echo '<b class="content">Bei Fragen oder Problemen benutzen Sie bitte unsere <a '.$stylestring.' href="./kontakt.php"><b>Kontaktm&ouml;glichkeiten</b></a><br>';
      echo 'Bitte lesen Sie auch unsere <a '.$stylestring.' href="'.$PHP_SELF.'?Hilfe_ID=AGB"><b>AGBs</b></a></b><br><br>';
  }
  // Anzeige der AGBs
  elseif ($Hilfe_ID == "AGB") {
      echo getAGB();
      echo '<br><center><p><A '.$stylestring.' href="javascript:window.close();" class="content">Fenster schliessen</a>&nbsp;&nbsp;&nbsp;<a href="javascript:window.print();" class="content">AGBs ausdrucken</a></p></center></p></body></html>';
      exit; // Programmabbruch weil nach den AGBs keine Hilfedateien ausgelesen werden sollen
  }

  // Funktion aufrufen --> Hilfetext einfuegen
  echo "<B class='content'>".getHilfe($Hilfe_ID)."</B><BR>";
  echo '<center><p><A '.$stylestring.' href="javascript:window.close();" class="content">Fenster schliessen</a></p></center>';
?>
    </p>
  </body>
</html>
<?php
  // End of file------------------------------------------------------------
?>
