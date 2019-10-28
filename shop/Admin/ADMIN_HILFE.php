<?php
  // Filename: ADMIN_HILFE.php
  //
  // Modul: ADMIN_HILFE
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Hilfe-Texte für USER und ADMIN Pages
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: ADMIN_HILFE.php,v 1.21 2003/05/24 18:41:27 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $ADMIN_HILFE = true;

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

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);
  extract($_SERVER);

  // -----------------------------------------------------------------------
  // HTML_HEAD + body open
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="content-language" content="de">
<meta name="author" content="José Fontanil and Reto Glanzmann">
<title>Shophilfe</title>
<LINK REL=STYLESHEET HREF="shopstyles.css" TYPE="text/css">
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

  // -----------------------------------------------------------------------
  //Funktion aufrufen --> Hilfetext einfuegen
  echo "<table border=\"0\" width=\"100%\"><tr><td align=\"right\"><b><small>Suche: CTRL + F</small></b></td></tr></table>\n";
  echo "<B>".getHilfe($Hilfe_ID)."</B><BR>";

  // -----------------------------------------------------------------------
  // Wenn der Aufruf aus der Artikeleingabemaske erfolgt, folgenden dynamisch erzeugten Zusatz hinzufuegen.
  // Hier werden die Link(s) zusammengestellt um von aussen DIREKT den Artikel anzeigen zu lassen (deep link):
  if ($Hilfe_ID == "Shop_Artikel") {
      // Wenn es sich um einen neuen Artikel handelt, Info ausgeben und beenden:
      if ($Artikel_ID == "") {
          echo "<center>Dies ist ein neuer Artikel, es bestehen noch keine Verweise auf ihn.</center>";
      }
      else {
          // Erstellen des Links von index.php aus
          $Pfadarray = pathinfo($PHP_SELF); // Pfad von PHP_SELF parsen
          $LinkURL = "http://".$HTTP_HOST.str_replace("shop/Admin","",$Pfadarray["dirname"])."index.php";
          $Kategorienarray = getKategorieID_eines_Artikels($Artikel_ID); // Kategorien des Artikels auslesen
          // Fuer jede Kategorie, in welcher sich dieser Artikel befindet einen Deep-Link erstellen:
          foreach ($Kategorienarray as $value) {
              $aktuelleKategorie = new Unterkategorie(); // Kategorieobjekt instanzieren
              $aktuelleKategorie = getKategorie($value); // Kategoriedaten auslesen
              echo "Kategorie: "; // Ausgabestring zusammenstellen...
              if ($aktuelleKategorie->Unterkategorie_von != "") {
                  echo "<i>".$aktuelleKategorie->Unterkategorie_von."</i><tt> --> </tt>";
              }
              echo $aktuelleKategorie->Name.":<br><small>$LinkURL?Kategorie_ID=$value&Artikel_ID=$Artikel_ID</small><br>";
          }
          echo "<br>";
      }
  } // End if Hilfe_ID = Shop_Artikel
  echo '<center><p><A href="javascript:window.close();">Fenster schliessen</a>&nbsp;&nbsp;&nbsp;<a href="javascript:window.print();">Hilfe ausdrucken</a></p></center>';
?>
    </p>
</body>
</html>
<?php
  // End of file------------------------------------------------------------
?>
