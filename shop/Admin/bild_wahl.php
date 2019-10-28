<?php
  // Filename: bild_wahl.php
  //
  // Modul: PHP-Funktionen - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Eine Uebersicht der hochgeladenen Produktebilder zur Auswahl
  // anzeigen
  //
  // Sicherheitsstufe:                     *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: bild_wahl.php,v 1.5 2003/05/24 18:41:38 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $bild_wahl = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // include, damit wir eine DB-Connection aufbauen koennen und Zugriff auf
  // die verwendeten Funktionen haben:
  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_Database)) {include("ADMIN_initialize.php");}
  if (!isset($SHOP_ADMINISTRATION)) {include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // -----------------------------------------------------------------------
  // Kopf der HTML-Seite (unabhaengig von dem was angezeigt werden soll)
  // inkl. eroeffnendem BODY-Tag
  echo "<HTML><HEAD>";
  echo "<TITLE>Shop</TITLE>";
  echo "<META HTTP-EQUIV=\"content-type\" CONTENT=\"text/html;charset=iso-8859-1\">";
  echo "<META HTTP-EQUIV=\"Expires\" CONTENT=\"Fri, Jan 01 1900 00:00:00 GMT\">";
  echo "<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">";
  echo "<META HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache\">";
  echo "<META HTTP-EQUIV=\"language\" CONTENT=\"de\">";
  echo "<META HTTP-EQUIV=\"author\" CONTENT=\"José Fontanil & Reto Glanzmann\">";
  echo "<LINK REL=STYLESHEET HREF=\"./shopstyles.css\" TYPE=\"text/css\">";
  // POP-UP-JavaScript
  echo "<script language=\"JavaScript\">\n";
  echo "function NeuFenster(bild_gr){\n";
  echo "    MeinFenster = window.open(\"../pop_up.php?bild_gross=\"+bild_gr+\"\", \"GrossesBild\", \"width=800,height=600,scrollbars\");\n";
  echo "    MeinFenster.focus();\n";
  echo "}\n";
  echo "function close_window(klein, gross) {\n";
  echo "    window.opener.SubmitAuswahlForm(klein, gross);\n";
  echo "    window.close();\n";
  echo "}\n";
  echo "</script>\n";

  echo "</HEAD><BODY>";

  $bild_dir = "../ProdukteBilder";
  $klein_arr = array(); // beinhaltet die Dateinamen der Thumbnails
  $gross_arr = array(); // beinhaltet die Dateinamen der grossen Bilder
  $kl = '_kl.';         // Indikatorstring fuer Thumbnail
  $gr = '_gr.';         // Indikatorstring fuer grosses Bild

  echo "<table border=\"0\" width=\"100%\">\n";
  echo "  <tr>\n";
  echo "    <td align=\"left\">\n";
  echo "      <b>W&auml;hlen Sie ein Produktebild aus. F&uuml;r eine Grossansicht auf das Bild klicken.</b>";
  echo "    </td>\n";
  echo "    <td width=\"120\" align=\"right\">\n";
  echo "      <a href=\"javascript:window.close()\" border=\"0\">\n";
  echo "        <img src=\"../Buttons/bt_abbrechen_admin.gif\" border=\"0\" align=\"absmiddle\" alt=\"Abbrechen\">\n";
  echo "      </a>\n";
  echo "    </td>\n";
  echo "  </tr>\n";
  echo "</table>\n";

  // -----------------------------------------------------------------------
  // Bestimmen, wie viele Bilder pro Zeile angezeigt werden

  $thumb_breite = getThumbnail_Breite();  // Thumbnail Breite aus Datenbank auslesen
  $anz_tmb_zeile = 1;                     // Anzahl Thumbnails pro Zeile
  if ($thumb_breite <= 120) { $anz_tmb_zeile = 6; }
  else if ($thumb_breite <= 144) { $anz_tmb_zeile = 5; }
  else if ($thumb_breite <= 170) { $anz_tmb_zeile = 4; }
  else if ($thumb_breite <= 230) { $anz_tmb_zeile = 3; }
  else if ($thumb_breite <= 350) { $anz_tmb_zeile = 2; }
  else { $anz_tmb_zeile = 1; }

  // Spaltenbreite ermitteln (in Abhaengigkeit der Anzahl Zeilen)
  $spaltenbreite = (720 / $anz_tmb_zeile);


  // -----------------------------------------------------------------------
  // Liste der Bilddateien im Verzeichnis Produktebilder auslesen, von denen
  // eine Klein- und eine Grossansicht existiert

  if ($handle = opendir($bild_dir)){
      // fuer jeden Verzeichniseintrag
      while (false !== ($file = readdir($handle))) {
          if (strstr($file, $kl)){
              array_push ($klein_arr, $file);
          }
          else if (strstr($file, $gr)){
              array_push ($gross_arr, $file);
          }
          else{
              // nichts zu tun, auskommentieren fuer DEBUG-Zwecke
              // echo "<br>KEIN BILD: $file\n";
          }
      } // end of while

     echo "<table>";
     $sp_count = 1; // Spaltencounter, zaehlt die aktuelle Spalte
     foreach($klein_arr as $datei){
         $datei_gross = ereg_replace($kl, $gr, $datei);
         // falls zum gefundenen Thumbnail ein grosses Bild existiert..
         if (in_array($datei_gross, $gross_arr)){
             // falls erste Spalte in Zeile..
             if ($sp_count == 1){
                 echo "<tr>";
             } // end of if

             echo "<td width=\"$spaltenbreite\" valign=\"bottom\" align=\"center\"><a href=\"javascript:NeuFenster('$datei_gross')\" border=\"0\"><img src=\"$bild_dir/$datei\" border=\"0\"></a>";
             echo "<div class=\"klein\">$datei</div>";
             echo "<a href=\"javascript:close_window('$datei', '$datei_gross')\"><img src=\"../Buttons/bt_dieses_wahl_admin.gif\" border=\"0\" alt=\"bild ausw&auml;hlen\"></a><br>&nbsp;</td>\n";

             // falls letze Spalte in Zeile..
             if ($sp_count == $anz_tmb_zeile){
                 echo "</tr>";
                 $sp_count = 1;
             } // end of if
             else {
                 $sp_count++;
             } // end of else

         } // end of if
         else {
             // nichts zu tun, auskommentieren fuer DEBUG-Zwecke
             // echo "<br>kein Grossbild: $datei_gross ($datei)\n";
         } // end of else
     } // end of foreach
     echo "</table>";


  } // end of if
  else {
      echo "<h3>Das Verzeichnis $bild_dir kann nicht zum Lesen ge&ouml;ffnet werden!<h3>";
  } // end of else

  echo "</BODY></HTML>";
  // End of file-------------------------------------------------------------
?>
