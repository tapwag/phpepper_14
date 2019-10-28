<?php
  // Filename: session_mgmt.php
  //
  // Modul: Session
  //
  // Autoren: Jose Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Zentralisierte Verwaltung des Sessionhandling. Diese Datei wird von
  //        all jenen Dateien included, welche mit einer Session arbeiten.
  //
  // Sicherheitsstatus:     *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: session_mgmt.php,v 1.2 2003/06/15 21:29:52 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $session_mgmt = true;

  // PHP 5.3 Timezone setzen
  @date_default_timezone_set(@date_default_timezone_get());

  // -----------------------------------------------------------------------
  // Funktion : sanitize_php_path_vars
  // Zweck    : Diese Funktion verhindert XSS Exploits der folgenden Art:
  //            aufgerufene_php_datei.php/'<script>alert(evil_xss_code)</script>.
  //            Im Wesentlichen werden Tags in der URL entfernt.
  // Gruppe   : Security
  // Argumente: keine
  // Rueckgabewerte: keine
  function sanitize_php_path_vars() {
      // Globalen long Array sichtbar machen und pruefen, ob er existiert
      global $HTTP_SERVER_VARS;
      $clean_long_array = false;
      if (isset($HTTP_SERVER_VARS)) {
          $clean_long_array = true;
      }

      // Array mit zu saeubernden Variablen definieren:
      $path_vars = array('PHP_SELF','PATH_INFO','PATH_TRANSLATED');
      // Jede Variable saeubern...
      foreach($path_vars as $path_varname) {
          // Saeuberung durchfuehren, wenn die Variable existiert:
          if (isset($_SERVER[$path_varname])) {
              $_SERVER[$path_varname] = strip_tags($_SERVER[$path_varname]);
              // Falls Variable in long-Arrays existieren, dort auch saeubern
              if ($clean_long_array == true) {
                   $HTTP_SERVER_VARS[$path_varname] = $_SERVER[$path_varname];
              }
          }
      }
  }// End function sanitize_php_path_vars

  sanitize_php_path_vars();

  // -----------------------------------------------------------------------
  // Session Management Header v.1.41 - zentralisiert:
  // Ueberpruefen ob eine Session_ID uebergeben wurde, sonst eine neue erzeugen
  // Inkl. Fallunterscheidung fuer PHP Versionen >= 4.1.0
  // 1.) Sessionname auf mySession_ID setzen
  session_name('mySession_ID');

  // 2.) Testen, ob PHP >= 4.1.0 verwendet wird, weil dann der Registrierungsvorgang fuer Sessionvariablen
  // anders empfohlen ist als bei aelteren Versionen.
  $minver = array('4','1','0'); // Erforderliche PHP Version = 4.1.0
  $curver = explode(".", phpversion());
  if(($curver[0] <= $minver[0]) && ($curver[1] <= $minver[1]) && ($curver[1] <= $minver[1]) && ($curver[2][0] < $minver[2][0])) {
      // PHP < 4.1.0 (alt)
      // 3.) Wenn Register Globals ausgeschaltet ist, die Sessionvariable $mySession_ID global sichtbar machen (GET,POST)
      if ($mySession_ID == "") {
          if (($mySession_ID = $HTTP_GET_VARS['mySession_ID']) == '') {
              $mySession_ID = $HTTP_POST_VARS['mySession_ID'];
          }
      }
      // 4a.) Falls jetzt die Session_Variable immer noch leer ist, muss eine neue Session initiiert werden
      if(empty($mySession_ID)) {
          if (session_register("mySession_ID")) {
              /** Zu debug zwecken ausklammern
                  echo "<B>top.php: Meine <I>NEUE</I> Session Name: Begin---".session_name()."---End Session Name<br>\n";
                  echo "Session-ID: Begin---".session_id()."---End Session_ID</B><BR><BR>";
              **/
          }
          else {
              $fehlermeldung = "<html>\n<h1>\nEin Fehler ist aufgetreten: session_register()\n";
              $fehlermeldung.= "= false, konnte keine Session_ID zuteilen</h1>\n";
              $fehlermeldung.= $HTTP_SERVER_VARS['PHP_SELF']."</body>\n</html>\n";
              die($fehlermeldung); // Abbruch weil dem Shopkunden keine Session zugeteilt werden konnte
          }
      }
      // 4b.) Wenn die Session_Variable $mySession_ID schon etwas beinhaltet, muss keine neue Session erzeugt werden
      else {
          // Die Session wurde via Variable via GET-Link oder POST-Formular uebergeben
          session_start();
          session_id($mySession_ID);
      }
  }
  else {
      // PHP >= 4.1.0 (neu)
      // 3.) Wenn Register Globals ausgeschaltet ist, die Sessionvariable $mySession_ID global sichtbar machen
      if (isset($mySession_ID) && $mySession_ID == "") {
          $mySession_ID = $_REQUEST['mySession_ID'];
      }
      // 4a.) Falls jetzt die Session_Variable immer noch leer ist, muss eine neue Session initiiert werden
      if(empty($mySession_ID)) {
          session_start();
          $_SESSION["mySession_ID"] = session_id();
      }
      // 4b.) Wenn die Session_Variable $mySession_ID schon etwas beinhaltet, muss keine neue Session erzeugt werden
      else {
          // Die Session wurde via Variable via GET-Link oder POST-Formular uebergeben. Wir holen uns also einfach
          // die Session-ID und starten die Session danach (uebernehmen der vorhandenen Session)
          session_id($mySession_ID);
          /* MUSS EV WIEDER REAKTIVIERT WERDEN session_start(); */
      }
  }

/* // Session Debugging (funktioniert erst ab PHP >= 4.1.0):
  debug("Vorher:<br>my_empty: $my_empty<br>mySession_ID: $my_mySession_ID");
  debug($my_sessions);
  debug($my_cookies);
  debug("Nachher:<br>mySession_ID: $mySession_ID");
  debug($_SESSION);
  debug($_COOKIE);
  debug("SSL: ".$_SERVER['HTTPS']."<br>\$PHP_SELF: ".$_SERVER['PHP_SELF']);
*/

// End of file ----------------------------------------------------------------------------------
?>
