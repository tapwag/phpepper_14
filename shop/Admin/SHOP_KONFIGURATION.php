<?php
  // Filename: SHOP_KONFIGURATION.php
  //
  // Modul: PhPeppershop, Shopadministration, Konfiguration Menu
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Zeigt die Frames des Backup-Managements an
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_KONFIGURATION.php,v 1.35 2003/07/04 16:07:05 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_KONFIGURATION = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt ($pd = path delimiter)
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // -----------------------------------------------------------------------
  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($session_mgmt)) {include("session_mgmt.php");}
  if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

  // -----------------------------------------------------------------------
  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // ----------------------------POP-UP AUSGABE-----------------------------
  // Wenn nur GD-Infos angezeigt werden sollen (z.B. in einem PopUp-Fenster)
  if ($darstellen == 1) {
      echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
      echo "<html>\n";
      echo "<head>\n";
      echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
      echo "<meta http-equiv=\"content-language\" content=\"de\">\n";
      echo "<meta name=\"author\" content=\"Jos&eacute; Fontanil and Reto Glanzmann\">\n";
      echo "<title>Shop</title>\n";
      echo "<link rel=\"stylesheet\" href=\"shopstyles.css\" TYPE=\"text/css\">\n";
      echo "</head>\n";
      echo "<body class=\"content\">\n";
      echo "    <h1>Shop Administration</h1>\n";
      echo "    <h3>Shop Konfiguration</h3>\n";
      echo "    <p>\n";
      echo "      <b>Ausgabe der <a href=\"http://www.boutell.com/gd/\" target=\"_new\">GD-Library</a> Infos</b><br>\n";
      $gd_informations = get_gdlibrary_info("verbose");
      echo "<table border=\"0\" cellspacing=\"0\">\n";
      echo "<tr><td style=\"border-bottom: #000000 1px solid;\">&nbsp;\n";
      echo "</td><td style=\"border-bottom: #000000 1px solid;\">&nbsp;\n";
      echo "</td></tr>\n";
      foreach($gd_informations as $key=>$value) {
          echo "<tr><td style=\"border-bottom: #000000 1px solid;\">";
          echo $key."&nbsp;&nbsp;\n";
          echo "</td><td style=\"border-bottom: #000000 1px solid;\">\n";
          if ($value == "1") {
              echo "<font color=\"#009900\">enabled / installed</font>\n";
          }
          else if ($value == "") {
            echo "<font color=\"#ff0000\">disabled / not installed</font>\n";
          }
          else {
            echo $value;
          }
          echo "</td></tr>\n";
      }// End foreach
      echo "</table>\n";
      echo "</p>\n";
      echo '<p><a '.$stylestring.' href="javascript:window.print();" class="content">Inhalt ausdrucken</a>&nbsp;&nbsp;';
      echo '&nbsp;&nbsp;&nbsp;<a '.$stylestring.' href="javascript:window.close();" class="content">Fenster schliessen</a></p>'."\n";
      echo "</body>\n</html>\n";
      // Programmablauf beenden! (Ist ja nur das Pop-Up welches dargestellt werden sollte)
      exit;
  }// End if darstellen = 1

  // ----------------------------- AUSGABE DER KONFIGURATIONSDATEN -------------------------------------
  // Anzeigevariablen initialisieren
  $dbtype = "MySQL"; // Im Moment fix eingegeben, wird noch nicht verwendet
  $dbhost = "localhost";
  $dbname = "phpeppershop";
  $dbadmin = "dbadmin";
  $dbuser = "dbuser";
  $backupadmin = "backupadmin";
  $Shopsettings = getshopsettings();
  $shopversion = $Shopsettings["ShopVersion"];

  // Auslesen der Konfigurationsdaten

  // Auslesen der Datei initialize.php (User-DB-Informationen)
  $initialize_file = fopen("../initialize.php","r");
  // Wenn die Datei erfolgreich geoeffnet wurde:
  if ($initialize_file <>0) {
      // Solange kein EOF...
      while ($zeile = fgets($initialize_file,4096)) {
          // Ausgelesene Zeile nach dem Wort TMySQLDatabase absuchen
          $interessant = strstr($zeile, "TMySQLDatabase");
          if (strlen($interessant) > 0) {
              // Abpacken der gefundenen Eintraege in die entsprechenden Variablen:
              $wordarray1 = explode("\"", $interessant);
              $dbhost = $wordarray1[1];
              $dbname = $wordarray1[3];
              $dbuser = $wordarray1[5];
          }// End if interessant
      }// End while zeile
  }// End if initialize_file
  else {
      echo "<HTML><BODY><H1>initialize.php Datei (shop/initialize.php) konnte nicht gelesen werden! --> Abbruch</H1><BR></BODY></HTML>";
      exit;
  }// End else
  fclose($initialize_file);

  // Auslesen der ADMIN_initialize.php (Admin-DB-Informationen
  $ADMIN_initialize_file = fopen("ADMIN_initialize.php","r");
  // Wenn die Datei erfolgreich geoeffnet wurde:
  if ($ADMIN_initialize_file <>0) {
      // Solange kein EOF...
      while ($zeile = fgets($ADMIN_initialize_file,4096)) {
          // Ausgelesene Zeile nach dem Wort TMySQLDatabase absuchen
          $interessant = strstr($zeile, "TMySQLDatabase");
          if (strlen($interessant) > 0) {
              // Abpacken der gefundenen Eintraege in die entsprechenden Variablen:
              $wordarray2 = explode("\"", $interessant);
              $dbadmin = $wordarray2[5];
          }// End if interessant
      }// End while zeile
  }// End if ADMIN_initialize_file
  else {
      echo "<HTML><BODY><H1>ADMIN_initialize.php Datei (shop/Admin/ADMIN_initialize.php) konnte nicht gelesen werden! --> Abbruch</H1><BR></BODY></HTML>";
      exit;
  }// End else
  fclose($ADMIN_initialize_file);

  // Auslesen der Datei ADMIN_backup.php (Backup-DB-Anbindung)
  $ADMIN_backup_file = fopen("ADMIN_backup.php","r");
  // Wenn die Datei erfolgreich geoeffnet wurde:
  if ($ADMIN_backup_file <>0) {
      // Solange kein EOF...
      while ($zeile = fgets($ADMIN_backup_file,4096)) {
          // Ausgelesene Zeile nach dem Wort TMySQLDatabase absuchen
          $interessant = strstr($zeile, "\$dbhost=");
          if (strlen($interessant) > 0) {
              // Abpacken der gefundenen Eintraege in die entsprechenden Variablen:
              $wordarray3 = explode("\"", $interessant);
              $backuparray[0] = $wordarray3[1];
          }// End if interessant
          $interessant = strstr($zeile, "\$dbname=");
          if (strlen($interessant) > 0) {
              // Abpacken der gefundenen Eintraege in die entsprechenden Variablen:
              $wordarray4 = explode("\"", $interessant);
              $backuparray[1] = $wordarray4[1];
          }// End if interessant
          $interessant = strstr($zeile, "\$dbuser=");
          if (strlen($interessant) > 0) {
              // Abpacken der gefundenen Eintraege in die entsprechenden Variablen:
              $wordarray5 = explode("\"", $interessant);
              $backuparray[2] = $wordarray5[1];
              $backupadmin = $wordarray5[1];
          }// End if interessant
      }// End while zeile
  }// End if ADMIN_backup_file
  else {
      echo "<HTML><BODY><H1>ADMIN_backup.php Datei (shop/Admin/ADMIN_backup.php) konnte nicht gelesen werden! --> Abbruch</H1><BR></BODY></HTML>";
      exit;
  }// End else
  fclose($ADMIN_backup_file);

  // Diagnose (Vergleich der drei Woerterlisten auf Diskrepanzen:
  // Datenbank-Hostnamen
  if ($wordarray1[1] <> $wordarray2[1]) {
      $Diagnose[] = "In der Datei initialize.php ist der Datenbank-Hostname verschieden zum Hostnamen in der Datei ADMIN_initialize.php [".$wordarray1[1]." <> ".$wordarray2[1]."]<BR>";
  }
  if ($wordarray1[1] <> $backuparray[0]) {
      $Diagnose[] = "In der Datei initialize.php ist der Datenbank-Hostname verschieden zum Hostnamen in der Datei ADMIN_backup.php [".$wordarray1[1]." <> ".$backuparray[0]."]<BR>";
  }
  if ($wordarray2[1] <> $backuparray[0]) {
      $Diagnose[] = "In der Datei ADMIN_initialize.php ist der Datenbank-Hostname verschieden zum Hostnamen in der Datei ADMIN_backup.php [".$wordarray2[1]." <> ".$backuparray[0]."]<BR>";
  }
  if ($wordarray2[1] == "127.0.0.1" || $backuparray[0] == "127.0.0.1") {
      $Diagnose[] = "Ihr Datenbank Hostrechner wird mit 127.0.0.1 angesprochen. Falls Sie Datenbank-Verbindungsprobleme haben, &auml;ndern Sie diesen Eintrag zu localhost (Dateien: ADMIN_initialize.php, ADMIN_backup.php)<BR>";
  }
  // Datenbanknamen
  if ($wordarray1[3] <> $wordarray2[3]) {
      $Diagnose[] = "In der Datei initialize.php ist der Datenbankname verschieden zum Namen in der Datei ADMIN_initialize.php [".$wordarray1[3]." <> ".$wordarray2[3]."]<BR>";
  }
  if ($wordarray1[3] <> $backuparray[1]) {
      $Diagnose[] = "In der Datei initialize.php ist der Datenbankname verschieden zum Namen in der Datei ADMIN_backup.php [".$wordarray1[3]." <> ".$backuparray[1]."]<BR>";
  }
  if ($wordarray2[3] <> $backuparray[1]) {
      $Diagnose[] = "In der Datei ADMIN_initialize.php ist der Datenbankname verschieden zum Namen in der Datei ADMIN_backup.php [".$wordarray2[3]." <> ".$backuparray[1]."]<BR>";
  }
  // Datenbank Admin Usernamen
  if ($wordarray2[5] <> $backuparray[2]) {
      $Diagnose[] = "In der Datei ADMIN_initialize.php ist der Datenbank Administrator Name verschieden zum Namen in der Datei ADMIN_backup.php [".$wordarray2[5]." <> ".$backuparray[2]."]<BR>";
  }


  // HTML-Darstellung
?>

<html>
    <head>
        <title>Shop-Konfiguration</title>
        <meta HTTP-EQUIV="content-type" content="text/html;charset=iso-8859-1">
        <meta HTTP-EQUIV="language" content="de">
        <meta HTTP-EQUIV="author" content="Jose Fontanil & Reto Glanzmann">
        <meta name="robots" content="all">
        <link rel="stylesheet" href="./shopstyles.css" type="text/css">

        <script language="JavaScript" type="text/javascript">
            <!-- Begin
                function popUp(URL) {
                    day = new Date();
                    id = day.getTime();
                    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=480,height=490,left = 312,top = 234');");
                }
            // End -->
        </script>
    </head>
    <body>

    <h1>SHOP KONFIGURATION</h1>
    <h3>Information</h3>
    <p>Hier werden die Konfigurationsdaten dieser PhPepperShop-Installation angezeigt.<br>
    Sie helfen bei Problemen einfacher an Informationen heran zu kommen.<br>
    Die Diagnose-Ausgaben bez&uuml;glich der DB-Anbindung funktionieren nur korrekt <br>
    mit einer MySQL-Datenbank.</p>
    <hr align="left">
    <h3>PhPepperShop Konfigurationsdaten</h3>
    <table border="0" cellpadding="5" cellspacing="0" style="table-layout: auto;">
        <tr>
            <td colspan="4" style="border: #000000 0px solid;">
                <B>PhPepperShop Version</B>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Shopversion:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
                <?php echo $shopversion.' GPL'.'&nbsp;<a style="background-color:#dddddd;" target="_blank" href="http://www.phpeppershop.com/index.php?komme_von_gpl=true&version='.urlencode($shopversion).'"><small>(Aktuelle Shopversion)</small></a>'; ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: #000000 0px solid;">
                <BR>
                <B>Webserver</B>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Webserver Rechnertyp:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
<?php
                // Auslesen des OS-Typs. Anscheinend gibt es dafuer zwei verschiedene Env. Variablen:
                // Mac Erkennung (Basierend auf Erkenntnissen von PHP 4.06, MacOS-X)
                // Windows Erkennung (Basierend auf Erkenntnissen von WinNT 4.0, Win9x/ME, Win2000/XP, PHP 4.04pl/4.2.2)
                // Systemzusatzinformationen aus der phpinfo-Ausgabe extrahieren
                $server_zusatz = get_phpinfo_value('System');
                // OS-Type Erkennung
                if (PHP_OS == "WINNT" || PHP_OS == "WIN32" || strlen($HTTP_ENV_VARS["WINDIR"]) != "") {
                    // Bei Windows NT Installationen steht hier oft noch etwas im OS-Feld... falls ja, dann auslesen.
                    $webserver = "Microsoft© Windows Betriebssystem";
                    $wintype = ""; // Variable initialisieren
                    if (strlen($HTTP_ENV_VARS["OS"]) > 0) {
                        $wintype = "OS = ".$HTTP_ENV_VARS["OS"]." | ";
                    }
                    if ($HTTP_ENV_VARS["WINDIR"] != "") {
                    $ostype = str_replace("\\\\","\\","$wintype Windowsverzeichnis = ".$HTTP_ENV_VARS["WINDIR"]);
                    }
                    else {
                    $ostype = $wintype;
                    }
                }
                else if (strlen($HTTP_ENV_VARS["HTTP_UA_OS"]) > 0) {
                    $webserver = "Apple Macintosh";
                    if ((strlen($HTTP_ENV_VARS["HTTP_UA_OS"]) > 0) && (strlen($HTTP_ENV_VARS["HTTP_UA_OS"]) > 0)) {
                        $ostype = "UA_OS = ".$HTTP_ENV_VARS["HTTP_UA_OS"]." | UA_CPU = ".$HTTP_ENV_VARS["HTTP_UA_CPU"];
                    }
                }
                // UNIX/Linux Erkennung basierend auf Erkenntnissen von (SuSE Linux 7.1, PHP 4.06)
                else {
                    $webserver = "UNIX/Linux Betriebssystem";
                    if ($HTTP_ENV_VARS["OSTYPE"] != "") {
                        $ostype = "OSTYPE = ".$HTTP_ENV_VARS["OSTYPE"];
                    }
                }
                // Wenn die Variable $ostype etwas enthaelt, dies ausgeben:
                if (trim($ostype) != "") {
                    echo $webserver." [".$ostype."]";
                    echo "\n<br>\n".$server_zusatz["1) System"]."\n";
                }
                elseif (trim($webserver) != "") {
                    echo $webserver." [".$server_zusatz["1) System"]."]\n";
                }
                else {
                    echo $server_zusatz["1) System"];
                }
?>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Webserver Software:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
<?php
                echo $HTTP_SERVER_VARS["SERVER_SOFTWARE"];
?>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                PHP Version:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
<?php
                echo "PHP ".phpversion();
                $server_api = get_phpinfo_value('Server API');
                if (trim($server_api['1) Server API']) == "CGI") {
                    $server_api['1) Server API'] = "<font color='0000AA'>CGI</font>";
                }
                echo "&nbsp;(via ".$server_api['1) Server API'];
                // PHP5 Check - Falls PHP5 eingesetzt wird, so erscheint eine Warnung
                $phpversion = phpversion();
                $phpversion_array = explode('.',$phpversion);
                /*
                if ($phpversion_array[0] > 4) {
                    echo ", <b><font color=\"ff0000\">Warnung: PHP Version 5 wird verwendet!</b><br>Der PhPepperShop v.1.4 wurde unter PHP5 nicht getestet!</font>";
                }
                */
                echo ")";
?>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: #000000 0px solid;">
                <BR>
                <B>MySQL-Datenbankanbindung</B>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Datenbank Hostrechner:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
<?php




                $dbvers = '';
                $RS = $Admin_Database->Query('SHOW VARIABLES LIKE \'version\'');
                if (is_object($RS) && $RS->NextRow()) {
                    // SQL returns 'Variable_name' and 'Value'
                    $dbvers = $RS->GetField('Value');
                }

                $mysql_version_array = get_phpinfo_value('Client API version');
                $mysql_version_array = trim($mysql_version_array['1) Client API version']);
                $mysql_version = $mysql_version_array;
                $mysql_version_array = explode('.',$mysql_version_array);
                if (count($mysql_version_array) > 0 && $mysql_version_array[0] != '') {
                    if (intval($mysql_version_array[0]) < 3) {
                        $mysql_version = ' (<font color="ff0000">ACHTUNG: MySQL-Version: '.$mysql_version." ist zu alt!</font>)\n";
                    }
                    else if (intval($mysql_version_array[0]) <= 3 && intval($mysql_version_array[1]) < 23) {
                        $mysql_version = ' (<font color="ff0000">ACHTUNG: MySQL-Version: '.$mysql_version." ist zu alt!</font>)\n";
                    }
                    else if (intval($dbvers) >= 5) {
                        // Ab MySQL 5.0.2 kann es Restriktionen geben, welche den korrekten Shopbetrieb verhindern, Test...
                        /* Test mit eingeschaltetem MySQL5 Strict Mode */ // $RS = $Admin_Database->Exec('SET sql_mode = \'STRICT_ALL_TABLES\'');
                        $RS = $Admin_Database->Query('SELECT @@sql_mode');
                        $restriction_flags = '';
                        $problemflags_gefunden = false;
                        if (is_object($RS) && $RS->NextRow()) {
                            $restriction_flags = $RS->myRow['0']; // Zugriff ueber $RS->GetField['@@sql_mode'] funktioniert hier nicht
                            if ($restriction_flags != '') {
                                // Check, ob problematische MySQL5 SQL-Mode Flags gesetzt sind
                                $problem_flags = array('STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'NO_ZERO_IN_DATE', 'NO_ZERO_DATE', 'TRADITIONAL_SQL');
                                foreach($problem_flags as $flag) {
                                    if (strpos($restriction_flags,$flag) !== false) {
                                        $problemflags_gefunden = true;
                                    }
                                }
                            }
                        }
                        if ($problemflags_gefunden == true) {
                            $mysql_version = ' (<b><font color="ff0000">ACHTUNG: MySQL-Version: '.$mysql_version." benutzt <span title=\"".$restriction_flags."\">SQL-Mode Strict Flags</span>!</font></b>), Systemerkennung: ".$dbvers;
                            $mysql_version.= ", <b><font color=\"ff0000\">Schalten Sie in der Datei <span title=\"{shopverzeichnis}/shop/util.php\">util.php</span> MYSQL_5_PLUS_NO_STRICT auf true!</font></b>\n";
                        }
                        else {
                            $mysql_version = ' (<font color="009900">MySQL-Version: '.$mysql_version." <tt>-></tt> ok</font>), Systemerkennung: ".$dbvers."\n";
                        }

                    }
                    else {
                        $mysql_version = ' (<font color="009900">MySQL-Version: '.$mysql_version." <tt>-></tt> ok</font>), Systemerkennung: ".$dbvers."\n";
                    }
                }
                else {
                    $mysql_version = ' (<font color="0000AA">MySQL-Version konnte nicht korrekt ausgelesen werden -> siehe phpinfo()-Ausgabe weiter unten</font>)';
                }
                if (defined('MYSQL_5_PLUS_NO_STRICT') && MYSQL_5_PLUS_NO_STRICT == true) {
                    $mysql_version.= ' (MYSQL_5_PLUS_NO_STRICT in effect!)';
                }

                echo $dbhost.$mysql_version;

















/*

                $mysql_version_array = get_phpinfo_value('Client API version');
                $mysql_version_array = trim($mysql_version_array['1) Client API version']);
                $mysql_version = $mysql_version_array;
                $mysql_version_array = explode('.',$mysql_version_array);
                if (count($mysql_version_array) > 0 && $mysql_version_array[0] != '') {
                    if (intval($mysql_version_array[0]) < 3) {
                        $mysql_version = ' (<font color="ff0000">ACHTUNG: MySQL-Version: '.$mysql_version." ist zu alt!</font>)\n";
                    }
                    else if (intval($mysql_version_array[0]) <= 3 && intval($mysql_version_array[1]) < 23) {
                        $mysql_version = ' (<font color="ff0000">ACHTUNG: MySQL-Version: '.$mysql_version." ist zu alt!</font>)\n";
                    }
                    else {
                        $mysql_version = ' (<font color="009900">MySQL-Version: '.$mysql_version." <tt>-></tt> ok</font>)\n";
                    }
                }
                else {
                    $mysql_version = ' (<font color="0000AA">MySQL-Version konnte nicht korrekt ausgelesen werden -> siehe phpinfo()-Ausgabe weiter unten</font>)';
                }
                echo $dbhost.$mysql_version;
*/
?>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Datenbankname:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
                <?php echo $dbname; ?>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Datenbank Administrator:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
                <?php echo $dbadmin; ?>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Datenbank Backup Administrator:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
                <?php echo $backupadmin; ?>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                Datenbank User:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
                <?php echo $dbuser; ?>
            </td>
        </tr>
        <tr>
            <td valign="top" style="border: #000000 0px solid;">
<?php           if (count($Diagnose) == 0) {
                    echo "<font color='009900'>";
                }
                else {
                    echo "<font color='FF0000'>";
                }
?>
                Diagnose:</font>
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
<?php
                if (count($Diagnose) == 0) {
                    echo "<font color='009900'> Die Datenbank Konfiguration ist konsistent</font><BR>";
                }
                else {
                    foreach($Diagnose as $value) {
                        echo "<font color='ff0000'>".$value."</font>";
                    }
                    echo "<font color='ff0000'><tt>&nbsp;&nbsp;&nbsp;--></tt> Bitte die entsprechenden Dateien korrigieren, sonst wird der Shop nicht korrekt funktionieren!</font><BR>";
                }
                // Warnung, wenn PhPepperShop im Ein-User-Modus laeuft:
                if ($wordarray1[5] == $wordarray2[5]) {
                    echo "<font color='0000AA'><I>Warnung</I>: Der Shop l&auml;uft im Ein-DB-User-Modus, dies reduziert die Sicherheit, da die Shop-Kunden mit dem gleichen Datenbank Benutzer auf die Datenbank zugreifen, wie der Administrator</font><BR>";
                    echo "<font color='0000AA'><tt>&nbsp;&nbsp;&nbsp;--></tt> Weitere Informationen dazu finden Sie in der Dokumentation (Kapitel Security). Der Shop funktioniert aber auch mit reduzierter Sicherheit uneingeschr&auml;nkt.</font><BR>";
                }
?>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border: #000000 0px solid;">
                <BR>
                <B>Grafikunterst&uuml;tzung (GD-Library)</B>
            </td>
        </tr>
        <tr>
            <td valign="top" style="border: #000000 0px solid;">
<?php
                // Ermitteln, welche Grafikformate mit der aktuellen GD-Library verwendet werden koennen und
                // ob ueberhaupt eine GD-Library installiert ist --> Ausgabe des Resultats
                $gd_png = 0;
                $gd_jpg = 0;
                $gd_gif = 0;
                // bildcheck nur ausführen, wenn eine gd-library installiert ist
                if (function_exists(ImageTypes)){
                    if (ImageTypes() & IMG_PNG) { $gd_png = 1; }
                    if (ImageTypes() & IMG_JPG) { $gd_jpg = 1; }
                    if (ImageTypes() & IMG_GIF) {
                        $gd_gif = 1;
                    }
                    else {
                        // Zusatzcheck fuer GIF, weil obiger Check z.T. nicht funktioniert
                        $parsed_gd_info = get_gdlibrary_info("verbose");
                        if ($parsed_gd_info['GIF Create Support'] == 1) {
                            $gd_gif = 1;
                        }
                    }
                } // end of if function_exists
                if (!$gd_png && !$gd_gif && !$gd_jpg) {
                    echo "<font color='ff0000'>Diagnose:</font>";
                }
                else {
                    echo "Version der GD-Library:<br><br>\n";
                    echo "<font color='009900'>Diagnose:</font>";
                }
?>
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
<?php
                // Ausgabe der Resultate (unterstuetzte Bildformate)
                if (!$gd_png && !$gd_gif && !$gd_jpg) {
                    echo "<font color='ff0000'>Es ist weder PNG noch JPEG noch GIF Support auf diesem Webserver vorhanden. Es k&ouml;nnen somit keine Produktebilder hochgeladen werden. Wenden Sie sich mit diesem Problem an ihren Webserver-Administrator (GD-Library Problem).</font>";
                }
                else {
                    echo get_gdlibrary_info("short")." [<a  style='background-color:#dddddd;' href=\"JavaScript:popUp('".$_SERVER["PHP_SELF"]."?darstellen=1')\"><small><font color=\"#00005F\">Mehr Informationen</font></small></a>]<br>";
                }
                echo "<br>";
                if ($gd_jpg == 1) { echo "<font color='009900'>Artikelbilder im JPG-Format unterst&uuml;tzt(.jpg oder .jpeg)<br></font>"; }
                if ($gd_png == 1) { echo "<font color='009900'>Artikelbilder im PNG-Format unterst&uuml;tzt(.png)<br></font>"; }
                if ($gd_gif == 1) { echo "<font color='009900'>Artikelbilder im GIF-Format unterst&uuml;tzt(.gif)<br></font>"; }
?>
            </td>
        </tr>
<?php if ($my_array_key_exists == true) { /* Falls die Ersatzfunktion von array_key_exists benutzt wird */ ?>
        <tr>
            <td colspan="4" style="border: #000000 0px solid;">
                <B>Import Utility Message</B>
            </td>
        </tr>
        <tr>
            <td style="border: #000000 0px solid;">
                <font color="ff9900">array_key_exists</font>:
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
                Die PHP-Funktion 'array_key_exists($key, $array)' ist in dieser PHP-Version nicht vorhanden und wird emuliert.<br>
                Es kann sein, dass das Import/Export-Utility deshalb etwas an Performance einb&uuml;sst. Um dieses Problem zu <br>
                umgehen, bitte eine PHP Version ab 4.1.0 verwenden. Im Moment wird <?php echo phpversion(); ?> verwendet.
            </td>
        </tr>
<?php }/* End if */ ?>
        <tr>
            <td colspan="4" style="border: #000000 0px solid;">
                <BR>
                <b>Wichtige PHP-Direktiven</b>
            </td>
        </tr>
<?php
        // Zu ueberpruefende PHP-Direktiven in einen Array abpacken. Dasselbe fuer den
        // Sollwert dieser Direktiven (z.B. On oder Off). Ein Leerstring bedeuetet, dass der Sollwert egal ist.
        $php_direktiven = array('magic_quotes_gpc','magic_quotes_runtime','session.auto_start','safe_mode','register_globals','error_reporting','ZLib Support','disable_functions');
        $php_sollwert =   array('On'              ,'Off'                 ,'Off'               ,''         ,''                ,''               ,''            ,'no value'         );
        $php_diagnose_fehler = false; // Dieses Flag wird true, wenn ein PHP-Direktiven Diagnosefehler vorliegt.
        for($i = 0; $i < count($php_direktiven); $i++) {
            $antwort = array(); // Initialisierung
            $antwort = get_phpinfo_value($php_direktiven[$i]);

            // Ausgabe der PHP-Direktiven Einstellung:
            echo "        <tr>\n";
            echo "            <td valign=\"middle\" style=\"border: #000000 0px solid;\">\n";
            echo "                ".$php_direktiven[$i].":";
            echo "            </td>\n";
            echo "            <td style=\"border: #000000 0px solid;\">\n";
            echo "                &nbsp;\n";
            echo "            </td>\n";
            echo "            <td valign=\"middle\" style=\"border: #000000 0px solid;\">\n";
            echo "                ".$antwort["1) ".$php_direktiven[$i]];
            echo "            </td>\n";
            echo "        </tr>\n";

            // Diagnose: Suche, ob das Wort On vorkommt (darf nicht sein!):
            if ($php_sollwert[$i] != '') {
                if (!strpos($antwort["1) ".$php_direktiven[$i]],$php_sollwert[$i])) {
                     // Spezialfall disable_functions:
                     if ($php_direktiven[$i] == "disable_functions") {
                         $php_diagnose[$i] = "<font color='ffaa00'>".$php_direktiven[$i]." Direktive wird benutzt! Es sind folgende PHP-Funktionen gesperrt worden: ".$php_sollwert[$i]."!</font>";
                         $php_diagnose_fehler = true;
                     }
                     // alle anderen Faelle
                     else {
                         $php_diagnose[$i] = "<font color='ff0000'>".$php_direktiven[$i]." muss = ".$php_sollwert[$i]." sein!</font>";
                         $php_diagnose_fehler = true;
                     }
                }
                else {
                     $php_diagnose[$i] = "<font color='009900'>".$php_direktiven[$i]." ok</font>";
                }
            }
            else {
                 $php_diagnose[$i] = "<font color='009900'>".$php_direktiven[$i]." ok</font>";
            }// End else $php_sollwert[$i] != ''
        }// End for-Schleife
?>
        <tr>
            <td valign="top" style="border: #000000 0px solid;">
<?php           if ($php_diagnose_fehler == false) {
                    echo "<font color='009900'>";
                }
                else {
                    echo "<font color='FF0000'>";
                }
?>
                Diagnose:</font>
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">
<?php
                foreach($php_diagnose as $value) {
                    echo $value."<br>";
                }
?>
            </td>
        </tr>

        <tr>
            <td valign="middle" style="border: #000000 0px solid;">
                <BR>
                <B><font color="000011">Zur&uuml;ck zum Hauptmen&uuml; &nbsp;</font></B>
            </td>
            <td style="border: #000000 0px solid;">
                &nbsp;
            </td>
            <td style="border: #000000 0px solid;">&nbsp;
            </td>
        </tr>
    </table>
    <table border="0">
      <tr>
        <td width="205px" style="border: #000000 0px solid;">
          &nbsp;
        </td>
        <td align="left" style="border: #000000 0px solid;">
          <a href="./Shop_Einstellungen_Menu_1.php" target="_top">
            <img name="zurueck_grafik" src="../Buttons/bt_zurueck_admin.gif" border="0" alt="Zur&uuml;ck" vspace="transitional">
          </a>
        </td>
      </tr>
    </table>
    <hr align="left">
    <h3>PHP Konfigurationsinformation</h3>
    <P>Folgende Daten wurden &uuml;ber die PHP-interne Funktion <i><tt>phpinfo()</tt></i> geladen und dargestellt:</P>
    <?php phpinfo(); ?>
    <hr>
    <br><b><font color="0000AA">Zur&uuml;ck zum Hauptmen&uuml; &nbsp;</font></b>
    <table border="0">
      <tr>
        <td style="border: #000000 0px solid;">
          <a href="./Shop_Einstellungen_Menu_1.php" target="_top">
            <img name="zurueck_grafik" src="../Buttons/bt_zurueck_admin.gif" border="0" alt="Zur&uuml;ck" vspace="transitional">
          </a>
        </td>
      </tr>
    </table>
    </body>
    </html>
<?php
  // End of file-------------------------------------------------------------------------
?>
