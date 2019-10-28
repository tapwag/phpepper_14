<?php
// Filename: ADMIN_backup.php (per Installtool personalisiert)
//
// Modul: Backup
//
// Autor: Holger Mauermann, mauermann@nm-service.de
//
// PhPepperShop Port: Jose Fontanil & Reto Glanzmann
//
// Zweck: Erstellt ein Backup einer MySQL Shop Datenbank
//
// Sicherheitsstatus:        *** ADMIN ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: template_backup.php,v 1.20 2003/07/03 07:05:39 fontajos Exp $
//
// -----------------------------------------------------------------------

// -----------------------------------------------------------------------
// Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
// wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
// -----------------------------------------------------------------------
$ADMIN_backup = true;

// include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
// Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
// Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

// Einbinden der benoetigten Module (PHP-Scripts)
// Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

// Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
// $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
extract($HTTP_GET_VARS);
extract($HTTP_POST_VARS);

// Version des zugrundeliegenden phpMyBackup von Holder Mauermann
$version = "0.4 beta";

// MySQL Variablen (werden bei der Shop Installation personalisiert gesetzt)
$dbhost="{hostname}";     // Rechnername, worauf die DB laeuft
$dbuser="{shopadmin}";    // Name des DB-Benutzers
$dbpass='{shopadminpwd}'; // Passwort des DB-Benutzers
$dbname="{shop_db}";      // Name der Datenbank

// Setzen der Backup Einstellungen:
// Was wo im Rueckgabe-Array gespeichert wurde sieht man in USER_ARTIKEL_HANDLING.php, Funktion getShopSettings()
$Backupsettings = getBackupSettings();

// Anzahl zu behaltender Backups
$backups = $Backupsettings[1];

// Stunden zwischen Backups (Intervall)
$interval = $Backupsettings[5];

// Automatisierung des Backups
// Moeglichkeiten: Keine (kein), automatisch (auto), per CRON-Job (cron)
$automatisierung = $Backupsettings[3];

// $compression steuert die Komprimierung des Datenbank Dumps
// $compression soll 1 sein, wenn die ZLib vorhanden und eingebunden ist
// ansonsten muss $compression = 0 sein.
if ($Backupsettings[7] == "Y") {
    $compression = 1;
}
else {
    $compression = 0;
}

// Pfad zu den Dateien ADMIN_backup.php und ADMIN_restore.php
// Dies ist hier etwas aufwaendig gemacht, weil das Script von verschiedenen
// Pfaden aufgerufen werden kann und deshalb das Backups-Verzeichnis
// nicht einfach so verwendet werden kann.

if (is_dir("./Backups/")) {
    // Aufruf direkt aus ADMIN_backup.php
    $path = "./Backups/";
}
else if (is_dir("./shop/Admin/Backups/")) {
    // Aufruf von index.php aus
    $path = "./shop/Admin/Backups/";
}
else if (is_dir("./Admin/Backups/")) {
    // Aufruf aus USER-shop Verzeichnis
    $path = "./Admin/Backups/";
}
else if (is_dir("../Admin/Backups/")) {
    // Aufruf aus Frameset, Bilder, Buttons Verzeichnis
    $path = "../Admin/Backups/";
}
else {
    die("ADMIN_backup Error: Abbruch weil Backup Verzeichnis (Backups) nicht gefunden wurde. (Position ausgehend von:\$PHP_SELF =$PHP_SELF)");
}

// Ausgabepuffer loeschen und Inhalt zum Webserver senden
flush();

// Verbindung zur MySQL Datenbank aufnehmen
$conn = mysql_connect($dbhost,$dbuser,$dbpass) or die(mysql_error());

// Falls das Backup-Verzeichnis nicht existiert eines erstellen mit den
// Schreibrechten 777 (wegen Safe-Mode)
if (!is_dir($path)) mkdir($path, 0777);

// Tabellen Struktur auslesen, DROP / CREATE Statements erzeugen
function get_def($dbname, $table) {
    global $conn;
    $def = '';
    $def .= "DROP TABLE IF EXISTS `$table`;#%%\n";
    $def .= "CREATE TABLE `$table` (\n";
    $result = mysql_db_query($dbname, "SHOW FIELDS FROM `$table`",$conn);
    while($row = mysql_fetch_array($result)) {
        $def .= "    `$row[Field]` $row[Type]";
        if ($row['Default'] != '') {
            if (strtolower($row['Type']) != 'timestamp') {
                $def .= " DEFAULT '".addslashes($row["Default"])."'";
            }
        }
        if ($row['Null'] != 'YES') {
            $def .= ' NOT NULL';
        }
        if ($row[Extra] != '') {
            $def .= " $row[Extra]";
        }
        $def .= ",\n";
     }
     $def = ereg_replace(",\n$",'', $def);
     $result = mysql_db_query($dbname, "SHOW KEYS FROM `$table`",$conn);
     while($row = mysql_fetch_array($result)) {
          $kname=$row[Key_name];
          if(($kname != 'PRIMARY') && ($row[Non_unique] == 0)) $kname="UNIQUE|$kname";
          if(!isset($index[$kname])) $index[$kname] = array();
          $index[$kname][] = $row[Column_name];
     }
     while(list($x, $columns) = @each($index)) {
          $def .= ",\n";
          if($x == 'PRIMARY') $def .= '   PRIMARY KEY (' . implode($columns, ', ') . ')';
          else if (substr($x,0,6) == 'UNIQUE') $def .= '   UNIQUE '.substr($x,7).' (' . implode($columns, ', ') . ')';
          else $def .= "   KEY $x (" . implode($columns, ', ') . ')';
     }

     $def .= "\n);#%%";
     // return (stripslashes($def));  // Testweise entfernt - zur Sicherheit aber noch im Code zurueckbelassen
     return ($def);
}// End function get_def

// Tabellen Inhalt auslesen, INSERT Statements erzeugen
function get_content($dbname, $table) {
     global $conn;
     $content='';
     $result = mysql_db_query($dbname, "SELECT * FROM `$table`",$conn);
     while($row = mysql_fetch_row($result)) {
         $insert = "INSERT INTO `$table` VALUES (";
         for($j=0; $j<mysql_num_fields($result);$j++) {
            if (!isset($row[$j])) { // Eintrag = NULL (is not set)
                $insert .= "NULL,";
            }
            else if($row[$j] != '') { // Eintrag != "", es hat also was drin => addslashes ausfuehren
                $insert .= "'".addslashes($row[$j])."',";
            }
            else { // ...bei allem anderen leeren Eintrag mit Komma erzeugen ('',)
                $insert .= "'',";
            }
         }
         $insert = ereg_replace(",$","",$insert);
         $insert .= ");#%%\n";
         $content .= $insert;
     }
     return $content;
}// End function get_content

// Wenn der Datenbank-Dump komprimiert werden soll, muss die Dateiendung .gz sein, sonst nicht:
if ($compression==1) {
    $filetype = "sql.gz";
    $secondtype = "sql";
}
else {
    $filetype = "sql";
    $secondtype = "sql.gz";
}

// Wenn override = 1 ist, so soll sofort ein Backup erstellt werden (auch ausserplanmaessig)
// Wenn also override = 1 ist ODER es 'Zeit ist' ein Backup zu erstellen UND die Automatisierung
// auf auto oder cron ist, dieses nun erstellen.
// Die Backup Dateien werden umbenannt: 0.sql[.gz] ist das neueste Backup, inkrementierende Dateinamen
// deklarieren chronologisch früher erstellte Backups
// Die Funktion filetime wurde mit @ Fehlermeldung-stumm gestellt, weil wenn noch kein Backup erstellt wurde, es sonst eine Warnung geben wuerde
if (($override == 1) || ((@filemtime($path . "0.$filetype") < time() - $interval * 3600 && !eregi("/restore\.",$PHP_SELF))) && ($automatisierung != "kein")) {
    for ($i = $backups-1; $i > 0; $i--) {
        $oldname = $i-1 . ".$filetype";
        $newname = $i . ".$filetype";
        if (!is_file($path.$oldname)) {
            $oldname = $i-1 . ".$secondtype";
            $newname = $i . ".$secondtype";
        }
        @rename($path.$oldname,$path.$newname);
    }

    $cur_time=date("Y-m-d H:i");
    $newfile="# Datenbank Backup wurde erstellt mit 'phpMyBackup v.$version PhPepperShop port' am $cur_time\r\n";
    $tables = mysql_list_tables($dbname,$conn);
    $num_tables = @mysql_num_rows($tables);
    $i = 0;
    while($i < $num_tables) {
       $table = mysql_tablename($tables, $i);

       $newfile .= "\n# ----------------------------------------------------------\n#\n";
       $newfile .= "# structur for table '$table'\n#\n";
       $newfile .= get_def($dbname,$table);
       $newfile .= "\n\n";
       $newfile .= "#\n# data for table '$table'\n#\n";
       $newfile .= get_content($dbname,$table);
       $newfile .= "\n\n";
       $i++;
    }

    // Error-Flag wird true, wenn ein Fehler geschieht
    $error_flag = false;

    // Falls Komprimierung eingeschaltet ist, mit der Option w9 komprimieren
    if ($compression==1) {
        if (!($fp = @gzopen($path."0.$filetype","w9"))) {
            echo "<p><h3>A_Backup_Error: Konnte die Datei nicht &ouml;ffnen, in welche das Backup h&auml;tte geschrieben werden sollen.<br>Das Verzeichnis Backups muss 777 Rechte haben.</h3></p><br></body></html>";
            $error_flag = true;
        }
        else {
            gzwrite ($fp,$newfile);
            gzclose ($fp);
            @chmod($path."0.$filetype", 0666); // Backup via FTP veraenderbar machen (Versuch dies zu tun, falls moeglich)
        }
    }
    // Ohne Kompression, einfach nur die Datei erzeugen
    else {
        if (!($fp = @fopen($path."0.$filetype","w"))) {
            echo "<p><h3>A_Backup_Error: Konnte die Datei nicht &ouml;ffnen, in welche das Backup h&auml;tte geschrieben werden sollen.<br>Das Verzeichnis Backups muss 777 Rechte haben.</h3></p><br></body></html>";
            $error_flag = true;
        }
        else {
            fwrite ($fp,$newfile);
            fclose ($fp);
            @chmod($path."0.$filetype", 0666); // Backup via FTP veraenderbar machen (Versuch dies zu tun, falls moeglich)
        }
    }
    if ($override == 1) {
// HTML-Ausgabe einer positiven Meldung
?>
<HTML>
    <HEAD>
        <TITLE>Datenbank Backup (phpMyBackup v.<? echo $version;?> PhPepperShop-Port)</TITLE>
        <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
        <META HTTP-EQUIV="language" CONTENT="de">
        <META HTTP-EQUIV="author" CONTENT="Holger Mauermann & Jose Fontanil & Reto Glanzmann">
        <META NAME="robots" CONTENT="all">
        <LINK REL=STYLESHEET HREF="./shopstyles.css" TYPE="text/css">
    </HEAD>
    <BODY>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>Datenbank Backup (nur mit MySQL)</h3>
<?php if ($error_flag == false) { ?>
    <h4>Das Backup wurde erfolgreich erstellt</h4>
<?php } else {?>
    <h4>Es konnte <font color=#ff0000>KEIN</font> Backup erstellt werden!</h4>
<?php }?>
    <a class="content" href="./SHOP_BACKUP.php"><img src="../Buttons/bt_weiter_admin.gif" border="0" alt='Weiter'></a>
    </BODY>
</HTML>
<?php
    }// End if override=1
}// End if Backup erstellen oder nicht
else {
    if ($override == 1) {
// HTML-Ausgabe einer negativen Meldung
?>
<HTML>
    <HEAD>
        <TITLE>Datenbank Backup (phpMyBackup v.<? echo $version;?> PhPepperShop-Port)</TITLE>
        <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
        <META HTTP-EQUIV="language" CONTENT="de">
        <META HTTP-EQUIV="author" CONTENT="Holger Mauermann & Jose Fontanil & Reto Glanzmann">
        <META NAME="robots" CONTENT="all">
        <LINK REL=STYLESHEET HREF="./shopstyles.css" TYPE="text/css">
    </HEAD>
    <BODY>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>Datenbank Backup (nur mit MySQL)</h3>
    <h4>Es konnte <font color=#ff0000>KEIN</font> Backup erstellt werden!</h4>
    <a class="content" href="./SHOP_BACKUP.php"><img src="../Buttons/bt_zurueck_admin.gif" border="0" alt='Zur&uuml;ck'></a>
    </BODY>
</HTML>
<?php
    }// End if override=1
} // End else Backup erstellen oder nicht
// End of file ----------------------------------------------------------
?>
