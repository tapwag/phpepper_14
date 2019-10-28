<?php
  // Filename: Shop_Einstellungen_Menu_1.php
  //
  // Modul: Administrations-Menu - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet das Hauptmenu (Top-Level Menu) der Shop-Administration
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: Shop_Einstellungen_Menu_1.php,v 1.35 2003/06/15 21:21:10 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $Shop_Einstellungen_Menu_1 = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // -----------------------------------------------------------------------
  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($session_mgmt)) {include("session_mgmt.php");}
  if (!isset($USER_ARTIKEL_HANDLING)){include("USER_ARTIKEL_HANDLING.php");}

  // -------------------------------------------------------------------------
  // HTML-Teil (JavaScript wird nur fuer Hilfe-PopUp-Windows benoetigt)
?>
<HTML>
<HEAD>
    <TITLE>Administration</TITLE>
    <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
    <META HTTP-EQUIV="language" CONTENT="de">
    <META HTTP-EQUIV="author" CONTENT="Jose Fontanil & Reto Glanzmann">
    <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
    <LINK REL=STYLESHEET HREF="./shopstyles.css" TYPE="text/css">
    <title>Shop</title>

    <SCRIPT LANGUAGE="JavaScript">
    <!-- Begin
    function popUp(URL) {
    day = new Date();
    id = day.getTime();
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=620,left = 100,top = 100');");
    }
    // End -->
    </script>


</HEAD>

<BODY CLASS="content">
    <center><p><h2>SHOP ADMINISTRATION</H2></p></center>
    <center><p><A href="http://www.phpeppershop.com"><img src='../Bilder/phpepper_grau.gif' alt='www.phpeppershop.com' border=0></A></p></center>
  <center><table border="0" cellspacing="10" bgcolor="#9999FF">
    <tr valign=top>
      <td><b>Artikel:</b></td>
      <td>
        <a href="./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=1"><b>Neuen Artikel einfügen</b><br></a>
        <a href="./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=1"><b>Bestehenden Artikel bearbeiten</b><br></a>
        <a href="./SHOP_ADMINISTRATION_AUFRUF.php?darstellen=2&up_loe=0"><b>Bestehenden Artikel löschen</b><br></a>
      </td>
    </tr>
    <tr valign=top>
      <td><b>Kategorien:</b></td>
      <td><a href="./Shop_Einstellungen_Menu_Kategorien.php"><b>Kategorienmanagement</b></a><br></td>
    </tr>
    <tr valign=top>
      <td><b>Kunden/Bestellungen:</b></td>
      <td>
        <A href="./shop_kunden_mgmt.php"><b>Kundenmanagement</b><br></a>
      </td>
    </tr>
    <tr valign=top>
      <td><b>Shop-Einstellungen:</b></td>
      <td>
        <a href="./SHOP_SETTINGS.php"><b>Allgemeine Shopeinstellungen</b></a><br>
<?php if (getmwstnr() != "0") { ?>
        <a href="./Shop_Einstellungen_Menu_MwSt.php"><b>Mehrwertsteuer Management</b></a><br>
<?php }  ?>
        <a href="./SHOP_LAYOUT.php"><b>Layout Management</b></a><br>
        <a href="./SHOP_LAYOUT.php?darstellen=20"><b>Bilder (Hintergrund & Shoplogo) hochladen</b></a><br>
        <a href="./SHOP_LAYOUT.php?darstellen=30"><b>Shopbuttons (Knöpfe) hochladen</b></a><br>
        <a href="./SHOP_VERSANDKOSTEN.php"><b>Versandkosten Einstellungen</b></a><br>
        <a href="./SHOP_KUNDE.php"><b>Kundenattribute bearbeiten</b></a><br>
        <a href="./SHOP_BACKUP.php"><b>Datenbank Backup</b></a><br>
        <a href="./SHOP_KONFIGURATION.php"><b>Shop Konfiguration ansehen</b></a><br>
      </td>
    </tr>
    <?php
    // Import-Export Menueintrag nur ausgeben, wenn das Import-/Export-Modul vorhanden ist
    if (is_readable('./shop_import.php')){
        echo "<tr valign=top>\n";
        echo "<td><b>Import/Export:</b></td>\n";
        echo "<td>\n";
        echo "<A href=\"./shop_import.php\"><b>Import/Export-Tool</b><br></a>\n";
        echo "</td>\n";
        echo "</tr>\n";
    } // end of if
    ?>
  </table></center>
  <center><br><br><a href="../../index.php"><b>Zur&uuml;ck zum Shop</b></a></center>
  <center><a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Einstellungen_Menu_1')"><b>Hilfe</b></A></center>

</BODY>
</HTML>
<?php
  // End of file-------------------------------------------------------------------------
?>
