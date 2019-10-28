<?php
  // Filename: ADMIN_restore.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autor: Holger Mauermann, mauermann@nm-service.de
  //
  // PhPepperShop Port: Jose Fontanil & Reto Glanzmann
  //
  // Zweck: Auswahlmenu um Restores der Shop Datenbank vorzunehmen
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: ADMIN_restore.php,v 1.21 2003/05/24 18:41:29 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $ADMIN_restore = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_backup)){include("ADMIN_backup.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

?>

<HTML>
    <HEAD>
        <TITLE>Backup-Management (phpMyBackup v.<? echo $version;?> PhPepperShop-Port)</TITLE>
        <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
        <META HTTP-EQUIV="language" CONTENT="de">
        <META HTTP-EQUIV="author" CONTENT="Holger Mauermann & Jose Fontanil & Reto Glanzmann">
        <META NAME="robots" CONTENT="all">
        <LINK REL=STYLESHEET HREF="./shopstyles.css" TYPE="text/css">
        <SCRIPT LANGUAGE="JavaScript">
            <!-- Begin
                function popUp(URL) {
                    day = new Date();
                    id = day.getTime();
                    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=600,height=400,left = 100,top = 100');");
                }
                function restore(what) {
                    if (confirm("Sind Sie sicher, dass sie die bestehende Datenbank\nmit dem Backup in der Datei " + what +  " überschreiben wollen?")) {
                        window.location = "ADMIN_restore.php?file=" + what;
                    }
                }
            // End -->
        </SCRIPT>
    </HEAD>
    <BODY>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>Datenbank Backup (nur mit MySQL)</h3>
<?php
    // Test ob ein Restore gestartet wurde. Wenn ja, so erscheint die entsprechende Meldung:

if ($file != "") {
    $filename = $file;
    //    set_time_limit(180); // Vom Safe-Mode her untersagt
    if ($compression == 1) {
        $fd = gzopen($path.$file, "r");
        $file = gzread ($fd, filesize (($path.$filename)));;
    }
    else {
        $fd = fopen($path.$file, "r");
        $file = fread ($fd, filesize (($path.$filename)));;
    }
    $query=explode(";#%%\n",$file);
    $anzahl_ops = count($query);
    $prozent_zaehler = 10;
    $anzeigen_inkrement = 10;
    echo "<b>Restore</b><br><br><br><br>Fortschrittsanzeige: [";
    for ($i=0;$i < count($query)-1;$i++) {
        if ((($i/$anzahl_ops)+100) >= $prozent_zaehler) {
            echo ">";
            $prozent_zaehler = $prozent_zaehler + $anzeigen_inkrement;
        }
        mysql_db_query($dbname,$query[$i],$conn) or die(mysql_error());
    }
    echo "] <b>100%</b><br>";
    echo "<h4>$filename erfolgreich zur&uuml;ckgelesen!</h4>";
}// End if (restore)
else {
?>
    <h4>Restore</h4>
      <table border="0" cellpadding='5' cellspacing='1'>
        <tr align="center">
          <td><u><i>Datei</i></u></td>
          <td><u><i>Gr&ouml;sse</i></u></td>
          <td><u><i>Datum / Zeit</i></u></td>
          <td colspan=2><u><i>Operation</i></u></td>
        </tr>
<?php
    // Die Variable $path wird aus dem includeten Files ADMIN_backup.php bezogen
    $dir=opendir($path);
    while ($Dateiname = readdir($dir)) {
        if ($Dateiname != "." && $Dateiname != ".." && $Dateiname != "CVS") {
            $Verzeichnis[] = $Dateiname;
        }
    }
    if (!empty($Verzeichnis)) {
        sort($Verzeichnis);


        // Loeschen nicht mehr benoetigter Backup-Sets (Bsp. wenn jemand von 5 auf 3 zu
        // behaltender Backups wechselt, muessen die beiden letzten geloescht werden):
        // (Die Variablen $backups und $path kommen von der includeten Datei ADMIN_backup.php)
        // Die Schleife wurd invertiert programmiert, damit man dem Array von hinten gleich
        // alle ueberfluessigen Dateinamen entfernen kann
        for ($i=count($Verzeichnis);$i > $backups;$i--) {
            unlink($path.$Verzeichnis[($i-1)]); // Backup loeschen
            array_pop($Verzeichnis); // Dateiname im Array loeschen
        }

        foreach ($Verzeichnis as $file) {
            if (eregi("\.sql",$file)) {
                echo "
                <tr>
                    <td>$file&nbsp;</td>
                    <td align=\"right\">&nbsp;".sprintf("%01.1f", filesize($path.$file)/1024)." KBytes&nbsp;</td>
                    <td>&nbsp;" . date("Y-m-d H:i",@filemtime($path.$file)) . "</td>
                    <td>&nbsp;<a href=\"javascript:restore('$file')\"><b>Restore</b></a>&nbsp;</td>
                    <td>&nbsp;<a href=\"javascript:popUp('$path$file')\">View</a></td>&nbsp;
                    <td>&nbsp;<a href=\"Backups/$file\">Download</a></td>&nbsp;
                </tr>";
            }
        }
        closedir($dir);
    }// End if ($Verzeichnis nicht leer)
    else {
        echo "
        <tr>
            <td colspan=5 align='center'><i>Es wurden noch keine Backups erstellt</i></td>
        </tr>";
    }// End else ($Verzeichnis nicht leer)
}// End else (restore)
echo "        \n</table>\n";
echo '        <br><a href="./SHOP_BACKUP.php" target=_top><img src="../Buttons/bt_zurueck_admin.gif" border="0" alt="zurueck" align="absmiddle"></a>';
echo "    \n</BODY>";
echo "\n</HTML>";
// End of file ----------------------------------------------------------
?>
