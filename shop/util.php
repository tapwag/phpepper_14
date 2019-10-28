<?php
  // Filename: util.php
  //
  // Modul: Error Handling/Logging
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Tools und Fehlerbehandlungsroutinen
  //
  // Sicherheitsstatus:                 *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: util.php,v 1.2 2003/06/13 17:54:16 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $util = true;

  // Folgende Konstante wird spaeter einmal in eine Konfigurationsdatei ausgelagert werden
  // Dann kann einfach gesteuert werden, ob debug-Meldungen angezeigt werden sollen oder nicht.
  define("DEBUG",true); // Im Moment wird die debug-Funktion nur zum Programmieren benötigt
                        // Die Meldungen werden nach den Tests wieder entfernt.
  define('MYSQL_5_PLUS_NO_STRICT',true); // Def: false | MySQL bietet seit Version 5.0.2
                                          // die Moeglichkeit SQLs strikter zu pruefen und die
                                          // Anwendung von Type-Casts und Defaultwert-Benutzung
                                          // strikter zu steuern. Dies muss fuer die Verwendung
                                          // des PhPepperShops ausgeschaltet werden.

  // Pfad Delimiter je nach Betriebssystem setzen (fuer zukuenftige Verwendung)
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}

  get_error_reporting_const();

  // --------------------------
  // Einfache Config Abstraction
  @set_magic_quotes_runtime(0);
  @ini_set('display_errors','1');
  @ini_set('magic_quotes_sybase','0');
  @ini_set('session.auto_start','0');    // Muesste vor (!) dem Session-Management gesetzt sein, ist aber nicht mehr so relevant.
  @ini_set('session.use_trans_sid','0'); // Muss via .htaccess gesetzt werden (obwohl laut Dokumentation php_ini_all Direktive!)
  @ini_set('url_rewriter.tags','');      // Sicherstellen, dass ein nicht daktivierbares session.use_trans_sid keinen Effekt hat
  @ini_set('error_reporting',PPS_ERROR_REPORTING_LEVEL); // Undefined Varialbe Meldung ausblenden
  // ini_set('magic_quotes_gpc',1); // Ist eine PERDIR Direktive, siehe dazu FAQ magic_quotes_gpc auf www.phpeppershop.com


  // Mach Sanity-Check fuer alle uebertragenen Daten, welche nicht Strings sind
  do_sanity_check();

  // Magic-Quotes GPC Emulation layer (wenn = Off)
  do_magic_quotes_gpc_check();

  // Register-Long-Vars = Off Korrektur
  correct_register_long_arrays_off();

  // -----------------------------------------------------------------------
  // Die debug-Funktion schreibt die darin angegebene Variable speziell formatiert auf die Standardausgabe.
  // Die beiden optionalen Parameter $arg1 und $arg2 koennen je entweder die Linie (__LINE__) oder
  // den Dateinamen der entsprechenden Datei (__FILE__) beinhalten von welcher die Meldung ausgegeben werden sollte.
  // Falls es sich bei der Variable $data um ein Objekt oder um einen Array handelt, wird dieses detailliert angezeigt.
  // In den letzten beiden, optionalen, Argumenten, kann man noch den Titel der Debug-Tabelle und die Schriftfarbe de-
  // finieren - Per default erscheint 'Debug message' in rot.
  // Argumente: $data (String|Array|Object), arg1 (String - optional), $arg2 (Integer - optional)
  //            $title (String - optional), $color (String - optional)
  // Rueckgabewert: true (boolean) bei Erfolg
  function debug($data, $arg1="", $arg2="", $title="Debug message:", $color="#ff0000") {
      // Debug Anweisungen werden nur ausgegeben, wenn der Debug Modus eingeschaltet ist.
      // Dies ist der Fall, wenn in der ppd.cfg.php Datei die DEBUG Konstante auf true gesetzt ist.
      if (DEBUG == "true") {
          // Sonderbehandlung, wenn der uebergebene Wert lediglich true | false (Boolean) ist.
          $data_title = '';
          if (gettype($data) == "boolean") {
              if ($data == true) {
                  $data = "true";
              }
              else {
                  $data = "false";
              }
          }
          else if (!is_object($data) || method_exists($data, '__toString')) {
              $data_title = (string)$data;
          }
          else {
              $data_title = 'Object';
          }

          if ($data_title == '') {
              $data_title = $data;
          }

          // Tabelle zur uebersichtlicheren Gestaltung erstellen
          echo "<table border=\"1\" bordercolor=\"#550088\">\n<tr><td>\n<b><font color=\"$color\">$title</font> $data_title</b>\n</td></tr>\n";

          // Fallentscheidungen ob zuerst die Linie und dann der Dateiname oder andersrum angegeben wird, oder nur eins von beiden
          if (is_string($arg1) && $arg1 != "") {
              echo "<tr><td>\nin file:<b> ".$arg1."</b>\n</td></tr>\n";
          }
          elseif (is_int($arg1) && $arg1 != "") {
              echo "<tr><td>\nat line:<b> ".$arg1."</b>\n</td></tr>\n";
          }
          if (is_string($arg2) && $arg2 != "") {
              echo "<tr><td>\nin file:<b> ".$arg2."</b>\n</td></tr>\n";
          }
          elseif (is_int($arg2) && $arg2 != "") {
              echo "<tr><td>\nat line:<b> ".$arg2."</b>\n</td></tr>\n";
          }
          if (gettype($data) == "array" || gettype($data) == "object") {
              if (count($data) > 0) {
                  echo "<tr><td><pre>";
                  print_r($data);
                  echo "</pre></td></tr>";
              }
          }
          echo "</table>\n";
      }// End if DEBUG == true
  }// End function debug

  // -----------------------------------------------------------------------
  // Diese Funktion liefert Benchmarking-Funktionalitaet (primaer zu Debug-Zwecken)
  // Man kann einen Timer starten und dann an beliebigen Orten Stoppmarken setzen und
  // sich am Ende eine Zusammenfassungen der Zeiten anzeigen lassen.
  // Argumente: $label kann folgende Werte annehmen:
  // - init  : Initialisierung eines neuen Timers (Timer starten)
  // - print : Anzeigen aller Stoppmarken und der Totalzeit
  // - oder den Namen (Beschreibung) der Stoppmarke
  // Rueckgabewert:
  function debug_timing ($label) {
      static $basetime,$totaltime,$rpttimes;

      // Initialisierung des Timers
      if ($label == "init") {
          $rpttimes = array();
          $basetime = microtime();
          $totaltime = 0;
          ereg("^([^ ]+) (.+)", $basetime, $r);
          $basetime = doubleval($r[2]) + doubleval($r[1]);
          return;
      }

      // Ausgabe der Messungen
      if ($label == "print") {
          echo "<b>Timing Resultate:</b><br>\n";
          for ($i = 0;$i < count($rpttimes); $i++) {
              echo "    $rpttimes[$i]<br>\n";
          }
          echo "Total: $totaltime\n";
          return;
      }

      // sonst: aufzeichnen und aufkumulieren der bis jetzt abgelaufenen Zeit
      $newtime = microtime();
      ereg("^([^ ]+) (.+)", $newtime, $r);
      $newtime = doubleval($r[2]) + doubleval($r[1]);
      $diff = $newtime - $basetime;
      $rpttimes[] = sprintf("%-20s [%s]", $label, $diff);
      $basetime = $newtime;
      $totaltime += $diff;

  }// End function debug_timing

  // -----------------------------------------------------------------------
  // Diese Funktion liefert entweder einen UNIX Timestamp zurueck oder einen
  // kurzen ($arg = short) oder ausfuehrlichen,
  // deutschen Datumsstring zurueck.
  // Argumente: $timestamp (Boolean), $short (Boolean)
  // Rueckgabewert (String oder Integer)
  function get_date_deutsch($today) {
    $today = getdate($today);
    $weekday = $today['weekday'];
    switch ($weekday) {
            case "Monday":
                $weekday = "Montag";
                break;
            case "Tuesday":
                $weekday = "Dienstag";
                break;
            case "Wednesday":
                $weekday = "Mittwoch";
                break;
            case "Thursday":
                $weekday = "Donnerstag";
                break;
            case "Friday":
                $weekday = "Freitag";
                break;
            case "Saturday":
                $weekday = "Samstag";
                break;
            case "Sunday":
                $weekday = "Sonntag";
                break;
    }
    $month = $today['month'];
    switch ($month) {
            case "January":
                $month = "Januar";
                break;
            case "February":
                $month = "Februar";
                break;
            case "March":
                $month = "März";
                break;
            case "April":
                $month = "April";
                break;
            case "May":
                $month = "Mai";
                break;
            case "June":
                $month = "Juni";
                break;
            case "July":
                $month = "Juli";
                break;
            case "August":
                $month = "August";
                break;
            case "September":
                $month = "September";
                break;
            case "October":
                $month = "Oktober";
                break;
            case "November":
                $month = "November";
                break;
            case "December":
                $month = "Dezember";
                break;
    }
    $mday = $today['mday'];
    $year = $today['year'];
    $hour = $today['hours'];
    $minutes = $today['minutes'];
    if ($hour < 10) {
        $hour = "0".$hour;
    }
    if ($minutes < 10) {
        $minutes = "0".$minutes;
    }
    return "$weekday den $mday. $month $year ($hour:$minutes"."h".") ";
  }// End function get_date

  // -----------------------------------------------------------------------
  // Mit dieser Funktion kann man via path_info uebergebene Parameter auslesen.
  // Die Variablen werden via $PATH_INFO aus dem URI ausgelesen und in einem
  // Rueckgabearray zurueckgegeben. Wenn keine Variablen vorhanden sind, so wird
  // ein leerer Array zurueckgegeben. ACHTUNG: Leere Elemente werden gefiltert!
  // Argumente: keine
  // Rueckgabewert Array mit Path-Info-Variablen (Array)
  function pathinforeader() {

    global $HTTP_SERVER_VARS;
    $pathinfo = ""; // Initialisierung
    $result_filtered = array(); // Initialisierung

    // Auslesen des PATH-INFO-Strings
    $pathinfo = $_SERVER["PATH_INFO"];
    if ($pathinfo == "") {
        $pathinfo = $HTTP_SERVER_VARS["PATH_INFO"];
    }
    if ($pathinfo != "") {
        $result = array(); // Initialisierung
        // Auslesen der einzelnen Parameter aus dem Path-Info-String
        $result = explode("/",$pathinfo);
        // Leere Elemente aussortieren (typischerweise das erste und je nach Schreibweise
        // auch noch das letzte Element)
        function sortout_empty_elements($element) {
            if ($element != "") return $element;
        }// End implicit function sortout_empty_elements
        // Leere Elemente aus dem Path-Info-Variablen Array filtern
        $result_filtered = array_filter($result, "sortout_empty_elements");
    }
    return $result_filtered;
  }// End function pathinforeader

  // -----------------------------------------------------------------------
  // Diese Funktion liefert den entsprechenden boolschen Wert, jenachdem ob der
  // Suchwert als Key in einem assoziativen Array existiert. Diese Funktion gibt es
  // in PHP 4.0.6 als key_exists und ab PHP 4.1.0 als array_key_exists.
  // Damit die Funktion nur dann benutzt wird, wenn ein altes PHP verwendet wird,
  // wird die Funktion erst dann eingebunden, wenn festgestellt wurde, dass die
  // array_key_exists-Funktion von PHP selbst nicht existiert. Auf diese Weise
  // erhalten wir die Performance der neuen Funktion und die Kompatibilitaet zu aelteren
  // versionen.
  // Argumente Name des gesuchten Keys (String), Name des Arrays (Array)
  // Rueckgabewerte: true | false (Boolean)
  $array_key_exists_code = '
      function array_key_exists($key, $search) {
          if (in_array($key, array_keys($search))) {
              return true;
          }
          else {
              return false;
          }
      }// End function arrayKeyExists
  ';
  if (!function_exists("array_key_exists")) {
      eval($array_key_exists_code);
      $my_array_key_exists = true; // Dieses Flag wird von der Shopkonfigurationsanzeige ausgewertet
  }

  // -----------------------------------------------------------------------
  // Loescht ein beliebiges Element im Array $array_with_elements welches
  // durch den Array Key $key_name referenziert wird.
  // Argumente: $array_with_elements (Array), $key_name (String)
  // Rueckgabewert: Array ohne Element $key_name (Array)
  function delArrayElementByKey($array_with_elements, $key_name) {
      $key_index = array_keys(array_keys($array_with_elements), $key_name);
      array_splice($array_with_elements, $key_index[0], 1);
      return $array_with_elements;
  }// End function delArrayElementByKey

  // -----------------------------------------------------------------------
  // Diese Funktion engt den Wertebereich gewisser Variablen ein. So dass Missbrauch verhindert wird.
  // Argumente: keine
  // Rueckgabe: true (Boolean)
  function do_sanity_check() {
      // Zu bereinigende Arrays sichtbar machen
      global $HTTP_GET_VARS;
      global $HTTP_POST_VARS;
      global $HTTP_COOKIE_VARS;

      // Variablendefinitionen als Arrays
      $values['integers'] = array('selected',
                                 'open',
                                 'Kategorie_ID',
                                 'Artikel_ID',
                                 'x',
                                 'y',
                                 'Kreditkarten_ID',
                                 'Erfolg',
                                 'lowlimit',
                                 'highlimit',
                                 'anzeigen_ab  ',
                                 'open'
                                );

      $values['double']  = array('Anzahl');

      // Integer-Variablen in den Arrays bereinigen
      foreach($values['integers'] as $variable) {
          if (isset($HTTP_GET_VARS[$variable])) $HTTP_GET_VARS[$variable]       = intval($HTTP_GET_VARS[$variable]);
          if (isset($HTTP_POST_VARS[$variable])) $HTTP_POST_VARS[$variable]     = intval($HTTP_POST_VARS[$variable]);
          if (isset($HTTP_COOKIE_VARS[$variable])) $HTTP_COOKIE_VARS[$variable] = intval($HTTP_COOKIE_VARS[$variable]);
      }

      // Integer-Variablen in den Arrays bereinigen
      foreach($values['double'] as $variable) {
          if (isset($HTTP_GET_VARS[$variable])) $HTTP_GET_VARS[$variable]       = (double)$HTTP_GET_VARS[$variable];
          if (isset($HTTP_POST_VARS[$variable])) $HTTP_POST_VARS[$variable]     = (double)$HTTP_POST_VARS[$variable];
          if (isset($HTTP_COOKIE_VARS[$variable])) $HTTP_COOKIE_VARS[$variable] = (double)$HTTP_COOKIE_VARS[$variable];
      }

      return true;
  }// End function do_sanity_check


  // -----------------------------------------------------------------------
  // Funktion : do_magic_quotes_gpc_check
  // Zweck    : Diese Funktion prueft, ob die PHP-Direktive magic_quotes_gpc
  //            ausgeschaltet ist. Wenn dem so ist, werden alle Variablen 'von Hand'
  //            nachtraeglich mit addslashes() behandelt, so dass magic_quotes_gcp=On
  //            emuliert wird. Achtung: Die PHP-Funktion array_map setzt PHP 4.0.6 voraus.
  //            Der Code stammt teilweise von http://www.php.net/ -> magic_quotes_gpc
  //            Manual (EN) Eintrag.
  // Argumente: keine
  // Rueckgabe: true (Boolean)
  function do_magic_quotes_gpc_check() {
      // ...natuerlich machen wir uns die Arbeit nur, wenn dies noetig ist.
      if (!get_magic_quotes_gpc()) {
          // Ok, wir muessen magic_quotes_gpc selbst machen...
          // Wir machen dazu zuerst die nicht superglobalen Arrays sichtbar
          global $HTTP_GET_VARS;
          global $HTTP_POST_VARS;
          global $HTTP_COOKIE_VARS;
          // Superglobale Arrays addslashen (auch mehrdimensionale Arrays beruecksichtigen)
          if (isset($_GET) && is_array($_GET))       $_GET    = array_map('addslashes_deep', $_GET);
          if (isset($_POST) && is_array($_POST))     $_POST   = array_map('addslashes_deep', $_POST);
          if (isset($_COOKIE) && is_array($_COOKIE)) $_COOKIE = array_map('addslashes_deep', $_COOKIE);
          // Nicht supergolbale GPC-Arrays behandeln, wenn sie existieren. Ansonsten
          // kopieren der superglobalen Arrays auf die Standardarrays
          if (isset($HTTP_GET_VARS)) {
              $HTTP_GET_VARS = array_map('addslashes_deep', $HTTP_GET_VARS);
          }
          else {
              $HTTP_GET_VARS = $_GET;
          }
          if (isset($HTTP_POST_VARS)) {
              $HTTP_POST_VARS = array_map('addslashes_deep', $HTTP_POST_VARS);
          }
          else {
              $HTTP_POST_VARS = $_POST;
          }
          if (isset($HTTP_COOKIE_VARS)) {
              $HTTP_COOKIE_VARS = array_map('addslashes_deep', $HTTP_COOKIE_VARS);
          }
          else {
              $HTTP_COOKIE_VARS = $_COOKIE;
          }
          // Wir setzen die Konstante MAGIC_QUOTES_GPC_EMULATION auf true
          define('MAGIC_QUOTES_GPC_EMULATION',true);
      }
      else {
          // Wir muessen nichts machen und setzen die Erkennungskonstante dementsprechend
          define('MAGIC_QUOTES_GPC_EMULATION',false);
      }

      return true;
  }// End function do_magic_quotes_gpc_check

  // -----------------------------------------------------------------------
  // Funktion : addslashes_deep
  // Zweck    : Diese Funktion wird benutzt um in do_magic_quotes_gpc_check benoetigte Rekursion
  //            abzubilden, so dass alle mehrdimensionalen Arrays korrekt geaddslashed werden.
  //            Achtung die Funktion wird nur implizit via array_map-Argument aufgerufen, nicht direkt.
  // Argumente: $value (mixed)
  // Rueckgabe: geaddslashter Wert (mixed)
  function addslashes_deep($value) {
      return (is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value));
  }// End function addslashes_deep

  // -----------------------------------------------------------------------
  // Funktion : php_version_check
  // Zweck    : Diese Funktion dient zur Bestimmung, ob die Mindest-
  //            Versionsanforderung bei der aktuellen PHP-Installation
  //            erfuellt ist. Gibt man als Argument $min_vers z.B '4.1.0' ein,
  //            wird nur TRUE zurueckgegeben, wenn PHP 4.1.0 oder eine neuere
  //            Version installiert ist.
  //            Um eine definierte Version zu ueberpruefen, kann in $test_vers
  //            optional eine Vergleichsversion angegeben werden.
  // Gruppe   : Allgemein
  // Argumente: $min_vers (String), $test_vers (String / NULL, optional, default = NULL)
  // Rueckgabe: true (Boolean)  -> Minimalanforderung erfuellt
  //            false (Boolean) -> Minimalanforderung nicht erfuellt
  function php_version_check($min_vers,$test_vers=null) {
      $phpversion = null;
      if ($test_vers !== null) $phpversion = $test_vers;
      if ($phpversion === null) $phpversion = phpversion();
      // falls die funktion version_compare exisitert (ab php 4.1.0), wird der vergleich darüber durchgeführt
      if(function_exists('version_compare')){
          if (version_compare($phpversion, $min_vers, '>=')) {
              return true;
          }
          return false;
      }
      else{
          // folgender Teil wird nur bei installiertem php < 4.1.0 aufgerufen
          $php_min = '';
          for($i=0;$i<strlen($min_vers);$i++){
              $chr_ord = ord(substr($min_vers,$i,1));
              // nur 0..9 und der Punkt duerfen in der Versionsbeschreibung verbleiben
              if(($chr_ord >= 48 && $chr_ord <= 57) || $chr_ord == 46){
                  $php_min.= chr($chr_ord);
              }
          }

          $php_inst = '';
          for($i=0;$i<strlen($phpversion);$i++){
              $chr_ord = ord(substr($phpversion,$i,1));
              // nur 0..9 und der Punkt duerfen in der Versionsbeschreibung verbleiben
              if(($chr_ord >= 48 && $chr_ord <= 57) || $chr_ord == 46){
                  $php_inst.= chr($chr_ord);
              }
          }
          $php_min_arr = explode('.',$php_min);
          $php_inst_arr = explode('.',$php_inst);

          for($i=0;$i<count($php_min_arr);$i++){
              if($php_inst_arr[$i] > $php_min_arr[$i]){
                  return true;
              }
              elseif($php_inst_arr[$i] < $php_min_arr[$i]){
                  return false;
              }
          }
          // wenn wir diese stelle erreichen, ist genau die version installiert, welche als mindestanforderung angegeben ist
          return true;
      }
  } // End function php_version_check

  // ---------------------------------------------------------------------------------
  // Funktion : correct_register_long_arrays_off
  // Zweck    : Ab PHP5 kann man mit der php.ini Konfigurationsdirektive register_long_-
  //            arrays ausschalten. Dann werden die aelteren Zugriffarrays HTTP_*_VARS
  //            nicht mehr mit Daten abgefuellt. Da der PhPepperShop bis hin zu PHP 4.0.6
  //            (Shopversion 2.0) kompatibel ist, arbeiten die internen API-Funktionen
  //            meist mit den HTTP_*_VARS Arrays und deshalb kopiert diese Funktion wenn
  //            benoetigt die Daten von $_* auf HTTP_*_VARS um.
  // Argumente: keine
  // Rueckgabe: Immer true (Bollean)
  function correct_register_long_arrays_off() {
      if (php_version_check('5.0.0') && ini_get('register_long_arrays') == 0) {
          // Variablen als global deklarieren
          global $HTTP_GET_VARS;
          global $HTTP_POST_VARS;
          global $HTTP_COOKIE_VARS;
          global $HTTP_POST_FILES;
          global $HTTP_SERVER_VARS;
          global $HTTP_ENV_VARS;

          // Variablen von Superglobals $_* auf HTTP_*_VARS kopieren
          $HTTP_GET_VARS    = $_GET;
          $HTTP_POST_VARS   = $_POST;
          $HTTP_COOKIE_VARS = $_COOKIE;
          $HTTP_POST_FILES  = $_FILES;
          $HTTP_SERVER_VARS = $_SERVER;
          $HTTP_ENV_VARS    = $_ENV;

          // Konstante setzen, so dass in Shop Konfiguration ansehen
          // erkannt wird, dass diese Funktion aufgerufen wurde
          define('REGISTER_LONG_ARRAYS_OFF_MODE','compatibility');
      }
  }// End function correct_register_long_arrays_off


  // -----------------------------------------------------------------------
  // Funktion : get_error_reporting_const
  // Zweck    : Diese Funktion setzt die Konstante PPS_ERROR_REPORTING_LEVEL, welcher
  //            noetig ist, um abhaengig von der PHP Hauptversion unterschiedliche
  //            Einstellungen zu beruecksichtigen. Die hier definierten Einstellungen
  //            entsprechen einer Produktivumgebung. Genauere Informationen:
  //            Hier wird eingestellt, wie der Konfigurationsabstraktionslayer die
  //            PHP-Error Reporting Einstellung setzen soll. Default PHP4: E_ALL & ~E_NOTICE
  //            (entspricht Integer Zahl 2039), Default PHP5: E_ALL & ~E_NOTICE & ~E_STRICT.
  //            Wenn Sie nicht genau wissen, was sie tun, belassen Sie hier die Default-
  //            Einstellung! (Anmerkung: E_STRICT in PHP5 entspricht Integer 2048). E_DEPRECATED
  //            wird angezeigt, wenn als veraltet markierte Funktionen verwendet werden.
  //            Weitere Infos, siehe: http://ch.php.net/manual/de/function.error-reporting.php
  // Gruppe   : Debugging_Error_Reporting
  // Argumente: keine
  // Rueckgabewerte: Immer true (Boolean)
  function get_error_reporting_const() {
      $php_major_version = substr(phpversion(),0,1); // Entweder 4 oder 5
      // Je nach PHP-Version einen anderen Level waehlen
      switch($php_major_version) {
          case '4':
              define('PPS_ERROR_REPORTING_LEVEL',E_ALL & ~E_NOTICE);
              break;
          case '5':
              if (defined('E_DEPRECATED')) {
                  define('PPS_ERROR_REPORTING_LEVEL',E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
              }
              else {
                  define('PPS_ERROR_REPORTING_LEVEL',E_ALL & ~E_NOTICE & ~E_STRICT);
              }
              break;
          default:
              define('PPS_ERROR_REPORTING_LEVEL',E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
      }

      return true;
  }// End function get_error_reporting_const

  // End of file-----------------------------------------------------------------------
?>
