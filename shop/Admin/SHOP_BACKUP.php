<?php
  // Filename: SHOP_BACKUP.php
  //
  // Modul: PhPeppershop, Backup Menu
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Zeigt die Frames des Backup-Managements an
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_BACKUP.php,v 1.12 2003/05/24 18:41:32 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_BACKUP = true;

  // HTML-Darstellung
?>

<HTML>
    <HEAD>
        <TITLE>Backup-Management</TITLE>
        <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
        <META HTTP-EQUIV="language" CONTENT="de">
        <META HTTP-EQUIV="author" CONTENT="Jose Fontanil & Reto Glanzmann">
        <META NAME="robots" CONTENT="all">
        <LINK REL=STYLESHEET HREF="./shopstyles.css" TYPE="text/css">

        <SCRIPT LANGUAGE="JavaScript">
            <!-- Begin
                function popUp(URL) {
                    day = new Date();
                    id = day.getTime();
                    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=400,height=300,left = 312,top = 234');");
                }
            // End -->
        </SCRIPT>
    </HEAD>
    <BODY>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>Datenbank Backup (nur mit MySQL)</h3>
    <table border='0' cellpadding='5' cellspacing='0'>
        <tr>
            <td>
                <a href="./SHOP_BACKUP_f1.php">Backup</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="./ADMIN_restore.php">Restore</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="./Shop_Einstellungen_Menu_1.php" target=_top><img src="../Buttons/bt_zurueck_admin.gif" border="0" alt="Abbrechen" align="absmiddle"></a>
            </td>
        </tr>
    </table>
    </BODY>
    </HTML>
<?php
  // End of file-------------------------------------------------------------------------
?>
