<?php
  // Filename: Shop_Einstellungen_Menu_Kategorien.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Startmenue fuer Kategorienmanagement
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: Shop_Einstellungen_Menu_Kategorien.php,v 1.27 2003/05/24 18:41:37 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $Shop_Einstellungen_Menu_Kategorien = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($database)) {include("database.php");}
  if (!isset($Database)) {include("initialize.php");}
  if (!isset($SHOP_ADMINISTRATION_ARTIKEL)){include("SHOP_ADMINISTRATION_ARTIKEL.php");}
  if (!isset($SHOP_ADMINISTRATION)) {include("SHOP_ADMINISTRATION.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // -------------------------------------------------------------------------
  // HTML-Teil (JavaScript wird nur fuer Hilfe-PopUp-Windows benoetigt)
?>
<HTML>
  <HEAD>
    <TITLE>Kategorienmanagement</TITLE>
    <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
    <META HTTP-EQUIV="language" CONTENT="de">
    <META HTTP-EQUIV="author" CONTENT="Jose Fontanil & Reto Glanzmann">
    <META NAME="robots" CONTENT="all">
    <LINK REL=STYLESHEET HREF="./shopstyles.css" TYPE="text/css">
    <title>Shop</title>

    <script language="JavaScript">

    // globale Variable, die sich merkt, welche Zeile aufgeklappt ist
    last_act = 0;

    var bt_kat1 = new Image();
    bt_kat1.src = "../Buttons/bt_ukat_neu.gif";
    var bt_kat2 = new Image();
    bt_kat2.src = "../Buttons/bt_kat_versch.gif";
    var bt_kat3 = new Image();
    bt_kat3.src = "../Buttons/bt_kat_edit.gif";
    var bt_kat4 = new Image();
    bt_kat4.src = "../Buttons/bt_kat_loesch.gif";

    var bt_ukat1 = new Image();
    bt_ukat1.src = "../Buttons/bt_ukat_versch1.gif";
    var bt_ukat2 = new Image();
    bt_ukat2.src = "../Buttons/bt_ukat_versch2.gif";
    var bt_ukat3 = new Image();
    bt_ukat3.src = "../Buttons/bt_ukat_edit.gif";
    var bt_ukat4 = new Image();
    bt_ukat4.src = "../Buttons/bt_ukat_loesch.gif";

    var bt_leer = new Image();
    bt_leer.src = "../Buttons/bt_kein_bild.gif";


    // Funktion fuer die Einblendung der Kategoriebuttons
    function chimg_kat(nr)
    {
        if (last_act != 0){
            chimg_leer(last_act);
        }
        document.images[nr].src = bt_kat1.src;
        document.images[nr+1].src = bt_kat2.src;
        document.images[nr+2].src = bt_kat3.src;
        document.images[nr+3].src = bt_kat4.src;
        last_act = nr;
    }

    // Funktion fuer die Einblendung der Unterkategoriebuttons
    function chimg_ukat(nr)
    {
        if (last_act != 0){
            chimg_leer(last_act);
        }
        document.images[nr].src = bt_ukat1.src;
        document.images[nr+1].src = bt_ukat2.src;
        document.images[nr+2].src = bt_ukat3.src;
        document.images[nr+3].src = bt_ukat4.src;
        last_act = nr;
    }

    // Funktion fuer die Ausgabe von leerButtons
    function chimg_leer(nr)
    {

        document.images[nr].src = bt_leer.src;
        document.images[nr+1].src = bt_leer.src;
        document.images[nr+2].src = bt_leer.src;
        document.images[nr+3].src = bt_leer.src;
    }

    // Funktion fuer Alertbox welche erscheint, wenn
    // jemand eine Unterkategorie erstellen will, obwohl
    // die Hauptkategorie noch Artikel enthaelt
    function no_Ukat_allowed()
    {
        alert("Wenn eine Hauptkategorie noch Artikel enthält, kann keine Unterkategorie erstellt werden. Es macht keinen Sinn in einer Hauptkategorie gleichzeitig Artikel UND Unterkategorien zu verwenden.");
    }


    function popUp(URL)
    {
        day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=620,height=420,left = 100,top = 100');");
    }

    </script>

<?php
    // hier wird der Browsertyp ermittelt, da der Netscape 4.xx bei Bildern die Groesse per Java-Script nicht
    // veraendern kann. Wird festgestellt, dass es sich um einen Netscape 4.7x Browser handelt, wird die Variable
    // netscape_47 -> true und dem Benutzer wird ein Warnhinweis ausgegeben.
    $netscape_47 = false;
    if (eregi('Mozilla/4.7',$HTTP_USER_AGENT)){
        $netscape_47 = true;
    }
?>

  </HEAD>
<BODY>

    <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
    <p><h3><b>Kategorien Management</b></h3></p>
    <?php
      // Warnmeldung fuer Netscape 4.xx Benutzer ausgeben
      if ($netscape_47 == true){
          echo"<h4>Diese Shopadministrationsmodus ist nicht f&uuml;r Browser der&nbsp;";
          echo "Generation Netscape 4.xx optimiert. Es sind alle Funktionen verf&uuml;gbar. Die Darstellung ist jedoch&nbsp;";
          echo "nicht optimal!</h4>";
      } // end of if netscape_47
    ?>

    <p>Klicken Sie eine Kategorie zum Bearbeiten an oder dr&uuml;cken Sie den Knopf "neue Kategorie erstellen"</p>
    <table border=0>
      <tr>
        <td colspan=2>
          <A href="./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=6"><img src='../Buttons/bt_kat_erstell.gif' border='0' alt='Neue Kategorie erstellen'></A>
        </td>
      </tr>
      <tr>
        <td colspan=2>
          &nbsp;
        </td>
      </tr>

<?php
    // Variablen fuer Javaskript, um Buttons ein- auszublenden
    $erste_nr = 1; // Nummer vom Ersten Button im Dokument, der ersetzt werden soll
    $offset = 4;   // Wie viele Buttons eine Kategorie-Zeile enthaelt
    $zaehler = 0;  // Zeilenzaehler

    // Kategorienobjekte in einen Kategorienarray abfuellen
    $myKategorien = array();
    $myKategorien = getallKategorien();

    // Alle Kategorien und Unterkategorien in einer Tabelle anzeigen
    foreach($myKategorien as $keyname => $value){
        $zeigUnterkategorien = false;

        // ermittlen, ob die Kategorie Unterkategorien hat
        if ($value->kategorienanzahl() > 0){
            $zeigUnterkategorien = true;
        }
        // Bildposition von erstem Bild ermitteln, das mit dem ersten Menubutton ersetzt werden soll
        $ersetz = $erste_nr + ($offset * $zaehler);

        // Ueberpruefen ob die Kategorie Artikel enthaelt
        $AnzahlArtikel = hatKategorieArtikel($value->Kategorie_ID);

        // Kategoriename ausgeben (Anzahl Artikel nur anzeigen, wenn keine Unterkategorien vorhanden sind)
        if ($zeigUnterkategorien == false) {
            echo "<tr><td><a href='$PHP_SELF' onClick='return false' style='text-decoration:none' onMouseDown='chimg_kat($ersetz);return true'><b>-&nbsp;".$value->Name." <small>(".$AnzahlArtikel.")</small></b></a></td>"."\n";
        }
        else {
            echo "<tr><td><a href='$PHP_SELF' onClick='return false' style='text-decoration:none' onMouseDown='chimg_kat($ersetz);return true'><b>-&nbsp;".$value->Name."</b></a></td>"."\n";
        }

        // Buttons fuer die Operationen, die auf einer Kategorie ausgefuehrt werden koennen, ausgeben
        // Button X---
        // Link, weil Netscape 4.xx onClick nicht auf td ausfuehren kann. Dient nur zur aktual. der Zeile per JavaSkript
        if ($AnzahlArtikel == 0) {
            echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=61&Ukat_von=".urlencode($value->Name)."'><img src='../Buttons/bt_kein_bild.gif'";
            if ($netscape_47 == true) echo "width='107' height='32'";
            echo " border='0' alt='Neue Unterkategorie erstellen'></A></td>\n";
        }
        else {
            echo "<td><A href='javascript:no_Ukat_allowed()'><img src='../Buttons/bt_kein_bild.gif'";
            if ($netscape_47 == true) echo "width='107' height='32'";
            echo " border='0' alt='Neue Unterkategorie erstellen'></A></td>\n";
        }
        // Button -X--
        echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=20&Name=".urlencode($value->Name)."&Kategorie_ID=".$value->Kategorie_ID."'><img src='../Buttons/bt_kein_bild.gif'";
        if ($netscape_47 == true) echo "width='107' height='32'";
        echo " border='0' alt='Kategorie verschieben'></A></td>\n";
        // Button --X-
        echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=35&Name=".urlencode($value->Name)."&Kategorie_ID=".$value->Kategorie_ID."'><img src='../Buttons/bt_kein_bild.gif'";
        if ($netscape_47 == true) echo "width='81' height='32'";
        echo " border='0' alt='Kategorie Eigenschaften bearbeiten'></A></td>\n";
        // Button ---X
        echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=45&typ=kat&Name=".urlencode($value->Name)."&Kategorie_ID=".$value->Kategorie_ID."'><img src='../Buttons/bt_kein_bild.gif'";
        if ($netscape_47 == true) echo "width='81' height='32'";
        echo " border='0' alt='Kategorie löschen'></A></td>\n";
        echo "</tr>\n";

        // Zeilenzaehler, damit fuer Javaskript die Bildnummer ermittelt werden kann, die ersetzt werden soll
        $zaehler++;

        if ($zeigUnterkategorien){
            $myUnterkategorien = array();
            $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
            for($i=0;$i < $value->kategorienanzahl();$i++){

                // Bildposition von erstem Bild ermitteln, das mit dem ersten Menubutton ersetzt werden soll
                $ersetz = $erste_nr + ($offset * $zaehler);

                // Unterkategoriename ausgeben
                echo "<tr><td><a href='$PHP_SELF' onClick='return false' style='text-decoration:none' onMouseDown='chimg_ukat($ersetz);return true'>&nbsp;&nbsp;-&nbsp;".$myUnterkategorien[$i]->Name." <small>(".hatKategorieArtikel($myUnterkategorien[$i]->Kategorie_ID).")</small></a></td>\n";

                // Buttons fuer die Operationen, die auf einer Kategorie ausgefuehrt werden koennen, ausgeben
                // Button X---
                // Link, weil Netscape 4.xx onClick nicht auf td ausfuehren kann. Dient nur zur aktual. der Zeile per JavaSkript
                echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=25&Ukat_alt=".urlencode($value->Name)."&Name=".urlencode($myUnterkategorien[$i]->Name)."&Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."'><img src='../Buttons/bt_kein_bild.gif'";
                if ($netscape_47 == true) echo "width='107' height='32'";
                echo" border='0' alt='Unterkategorie innerhalb Kategorie verschieben'></A></td>\n";

                // Buttons fuer die Operationen, die auf einer Kategorie ausgefuehrt werden koennen, ausgeben
                // Button -X--
                echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=30&Ukat_alt=".urlencode($value->Name)."&Name=".urlencode($myUnterkategorien[$i]->Name)."&Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."'><img src='../Buttons/bt_kein_bild.gif'";
                if ($netscape_47 == true) echo "width='107' height='32'";
                echo" border='0' alt='Unterkategorie in eine andere Kategorie verschieben'></A></td>\n";

                // Buttons fuer die Operationen, die auf einer Kategorie ausgefuehrt werden koennen, ausgeben
                // Button --X-
                echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=40&Name=".urlencode($myUnterkategorien[$i]->Name)."&Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."'><img src='../Buttons/bt_kein_bild.gif'";
                if ($netscape_47 == true) echo "width='81' height='32'";
                echo" border='0' alt='Unterkategorie Eigenschaften bearbeiten'></A></td>\n";

                // Buttons fuer die Operationen, die auf einer Kategorie ausgefuehrt werden koennen, ausgeben
                // Button ---X
                echo "<td><A href='./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=45&typ=ukat&Name=".urlencode($myUnterkategorien[$i]->Name)."&Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."'><img src='../Buttons/bt_kein_bild.gif'";
                if ($netscape_47 == true) echo "width='81' height='32'";
                echo" border='0' alt='Unterkategorie löschen'></A></td>\n";

                echo "</tr>\n";

                // Zeilenzaehler erhoehen
                $zaehler++;

            }// End for
        }//End if zeigUnterkategorien
    }// End foreach myKategorien
    ?>
    </table>
    <br><br>
    <a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_zurueck_admin.gif' border='0' alt="Zurueck zum Hauptmenu"></a>
    <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Einstellungen_Menu_Kategorien')"><img src='../Buttons/bt_hilfe_admin.gif' border='0' alt='Hilfe'></A>
  </BODY>
</HTML>

<?php
  // End of file-------------------------------------------------------------------------
?>
