<?php
  // Filename: SHOP_BACKUP_f1.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Funktionen um das Backup des Shops zu konfigurieren
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_BACKUP_f1.php,v 1.17 2003/05/24 18:41:32 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_BACKUP_f1 = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
  if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // HTML-Kopf, der bei jedem Aufruf des Files ausgegeben wird
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
                    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=620,height=420,left = 100,top = 100');");
                }
            // End -->
        </SCRIPT>
    </HEAD>
    <BODY>
<?php
// darstellen = 10
// Beschreibung
if ($darstellen == 10){
      echo '<h1>SHOP ADMINISTRATION</h1>';
      echo '<h3>Datenbank Backup (nur MySQL)</h3>';
      // index.php bearbeiten (je nach Automatisierung)
      if ($Automatisierung == "auto") {
          // Beim automatischen Backup wird jeweils wenn ein Kunde auf index.php kommt ueberprüft, ob ein
          // Backup zu machen ist. Ist es Zeit, so wird eines erstellt. Damit dieser Aufruf in index.php
          // ueberhaupt erst geschieht, wird ueber die SHOP-LAYOUT-Funktionen updatecssarg und mkindexphp
          // die Datei index.php mit einem include-Befehl versehen.
          updatecssarg("backup",'ini_set("include_path", "../:./shop/Admin:./:../"); if (!isset($ADMIN_backup)){require_once("ADMIN_backup.php");}');
      }
      else {
          // Ist das automatische Backup ausgeschaltet, so wird der Include Befehl aus index.php entfernt.
          // Dies ist die performance-schonenste Moeglichkeit, da nun nicht einmal mehr ein weiteres Script
          // angeschaut werden muss.
          updatecssarg("backup",'');
      }
      // Folgende Funktion updated die Datei index.php (generiert aus indextemplate.txt mit den Informationen aus der Tabelle 'css_file')
      if (!mkindexphp()) {
          die("<h1>S_BACKUP_Error: Konnte index.php nicht aktualisieren (nach Aufruf von mkindexphp)</h1>");
      }

      // Aufbereitung der Daten aus vorigem Formular:
      if ($Komprimierung == "on") {
          // Test ob die ZLib ueberhaupt installiert ist:
          $zlib_not_here = false; // Flag, wenn = true, so wollte der User die Komprimierung aktivieren, obwohl er keine ZLib hat
          if (function_exists(gzopen)) {
              $Komprimierung = "Y";
          }
          else {
              $Komprimierung = "N";
              $zlib_not_here = true;
          }
      }
      else {
          $Komprimierung = "N";
      }

      // Einfuegen der neuen Backup Einstellungen in die Datenbank mit entsprechender Erfolgsmeldung
    if (setBackupSettings($Anzahl_Backups, $Backup_Intervall, $Komprimierung, $Automatisierung)) {
        echo '<h4>Die Backup Einstellungen wurden erfolgreich gespeichert<h4>';
        if ($zlib_not_here) {
            echo '<h4>Die Komprimierung wurde <font color="ff0000">nicht</font> aktiviert, weil sie auf ihrem Webserver keine ZLib (Library) installiert haben! Wenden Sie sich mit diesem Problem an ihren Webserver-Administrator.</h4>';
        }
        echo '<a class="content" href="./SHOP_BACKUP.php"><img src="../Buttons/bt_weiter_admin.gif" border="0" alt="Weiter"></a>'."\n";
    }
    else {
        echo '<h4>Das Speichern der neuen Backup Einstellungen war <font color=#ff0000>NICHT ERFOLGREICH</f><h4>';
        echo '<a class="content" href="./SHOP_BACKUP.php"><img src="../Buttons/bt_zurueck_admin.gif" border="0" alt="Zurueck"></a>'."\n";
    }
} // end of if darstellen == 10

// wird ausgefuehrt, wenn $darstellen nicht 10 ist
else {
    // Auslesen der Backup-Settings. (Was in welchem Element des Arrays liegt, siehe getShopSettings, USER_ARTIKEL_HANDLING.php)
    $myBackupSettings = getBackupSettings();

?>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>Datenbank Backup (nur mit MySQL)</h3>
    <form action='./SHOP_BACKUP_f1.php' method="post" title="Backup">
        <hr><h4>Einstellungen</h4>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td>
                    Anzahl Backups bis erstes wieder &uuml;berschrieben wird:&nbsp;
                </td>
                <td>
                    <INPUT type='text' name='Anzahl_Backups' size='4' maxlength='4' value='<?php echo $myBackupSettings[1]; ?>'>
                </td>
            </tr>
            <tr>
                <td>
                    Backup Intervall:&nbsp;
                </td>
                <td>
                    <INPUT type='text' name='Backup_Intervall' size='4' maxlength='4' value='<?php echo $myBackupSettings[5]; ?>'>&nbsp;Stunden
                </td>
            </tr>
            <tr>
                <td>
                    ZIP-Komprimierung f&uuml;r Datenbank-Backup aktivieren:&nbsp;
                </td>
                <td>
                    <INPUT type='checkbox' name='Komprimierung' <?php if ($myBackupSettings[7] == "Y") {echo "checked";} ?>>
                </td>
            </tr>
        </table>
        <br><hr><h4>Automatisierung</h4>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td>
                  <ul>
                    <li>Das automatische Backup birgt ein Sicherheitsrisiko in sich. Einem User-Skript wird erlaubt als Administrator ein Backup des Shops anzulegen.</li>
                    <li>Dies ist die sauberste L&ouml;sung eines automatisierten Backups, erfordert aber das Recht CRON-Jobs einrichten zu d&uuml;rfen.</li>
                    <li>Kein automatisiertes Backup umgeht das Risiko des automatisierten Backups, man muss aber jedes Backup von Hand anlegen.</li>
                  </ul>
                </td>
            </tr>
        </table>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td>
                    Automatisiertes Backup aktivieren:&nbsp;
                </td>
                <td align=left>
                    <INPUT type='radio' name='Automatisierung' value='auto' <?php if ($myBackupSettings[3] == "auto") {echo "checked";} ?>>
                </td>
            </tr>
            <tr>
                <td>
                    Backup wurde als CRON-Job eingerichtet:&nbsp;
                </td>
                <td align=left>
                    <INPUT type='radio' name='Automatisierung' value='cron' <?php if ($myBackupSettings[3] == "cron") {echo "checked";} ?>>
                </td>
            </tr>
            <tr>
                <td>
                    Kein automatisches Backup:&nbsp;
                </td>
                <td align=left>
                    <INPUT type='radio' name='Automatisierung' value='kein' <?php if ($myBackupSettings[3] == "kein") {echo "checked";} ?>>
                </td>
            </tr>
        </table>
        <br><hr><br>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td>
                    <input type=image src="../Buttons/bt_speichern_admin.gif" border="0" alt="Speichern" align="top">&nbsp;
                </td>
                <td>
                    <a href="./SHOP_BACKUP.php" target=_top><img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen" align="absmiddle"></a>&nbsp;
                </td>
                <td>
                    <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Backup')"><img src='../Buttons/bt_hilfe_admin.gif' border='0' align='absmiddle' alt='Hilfe'></a>&nbsp;
                </td>
                <td bgcolor=F0F0F0 valign='middle'>
                    &nbsp;&nbsp;<a href="./ADMIN_backup.php?override=1" style="text-decoration:none"><b><font color=#ff0000>JETZT EIN BACKUP ERSTELLEN!</font></b></a>&nbsp;
                </td>
            </tr>
        </table>
        <input type=hidden name="darstellen" value="10">
    </form>
<?php
} // end of else

// HTML-Datei abschliessen
echo "    </BODY>";
echo "</HTML>";

// End of file ----------------------------------------------------------
?>
