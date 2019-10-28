<?php
  // Filename: USER_AUTH.php
  //
  // Modul: B2B-Modus
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Wickelt den Authentifizierungsprozess fuer Shopkunden ab wenn der
  //        sogenannte 'Haendlermodus' aktiviert wurde (Allg. Shopsettings)
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: USER_AUTH.php,v 1.11 2003/08/14 13:27:30 glanzret Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_AUTH = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd./shop/Frameset$pd../shop$pd".'shop'."$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($session_mgmt)) {include("session_mgmt.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}
  if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);
  extract($_SERVER);

  // Einige HTML-Darstellungsparameter
  $spalte1_breite = "33%";
  $spalte2_breite = "34%";
  $spalte3_breite = "33%";

  // -----------------------------------------------------------------------

  // Beschreibung des Authentifizierungsprozesses:
  // In dieser Datei ist der komplette Authentifizierungsprozess fuer Shopkunden abgelegt. Mit dem include dieser Datei
  // am Anfang eines Moduls mit potentieller HTML-Ausgaben reicht um sicher zu gehen, dass NUR EINGELOGGTE Kunden die
  // entsprechenden Seiten sehen koennen. Allen anderen User wird ein Loginfenster angezeigt.
  // Es kann in den Shopeinstellungen konfiguriert werden, ob der installierte PhPepperShop im 'Haendlermodus' laufen soll
  // oder nicht. Im 'Haendlermodus' ist das Login eines jeden (potentiellen) Kunden Pflicht. Wenn der 'Haendlermodus' ausge-
  // schaltet ist, so wird dieser Authentifizierungsmechanismus ausgeschaltet und man kann ohne sich zu identifizieren und
  // gegenueber dem Shop zu authentifizieren in den Shop gelangen. Der Kunde wird erst bei Betreten der Kasse nach einem Login
  // gefragt.
  // Weiter ist zu beachten, dass es bei einem Shop mit aktiviertem Haendlermodus logischerweise KEINE TEMPORAEREN KUNDEN gibt
  // (Kunden welche ihre Identitaet nur zu Bestellzwecken angeben wollen).

  // Check ob der Kunde eingeloggt ist (Beschreibung und Ablauf):
  // Im wesentlichen wird ueberprueft, ob:
  // 1.) Seine Session-ID einem registrierten Kunden zugewiesen werden kann (Eintrag in Tabelle kunde)
  // 2.) Wenn ja - ob er/sie eine abgelaufene Session hat (expired) oder ob die Session noch gueltig ist (expired Attribut).
  // --> Sind die Pruefungen positiv ausgefallen, wird das expired - Attribut erneuert und dem Kunden wird gestattet die Verarbeitung
  //     der aktuell benutzten PHP-Datei fortzusetzen.
  //     Anm. sobald der Kunde eine zugelassene Session besitzt, wird ihm auch gleich noch eine leere (neue) Bestellung erstellt
  //     Auch dort wird das expired-Attribut und das Session_ID-Attribut nachgefuehrt.
  // Bei einem negativen Entscheid wird dem Kunden ein Loginfenster angezeigt, wo er zum Login aufgefordert wird. Die Verarbeitung
  // wird danach eingestellt - Niemand kommt also unerlaubt weiter.

  // Die Funktion checkSessionExpired ueberprueft ob die aktuelle Session_ID einem registrierten Kunden zugeordnet werden kann
  // Weiter wird auch die Gueltigkeit dieser Session ueberprueft. (Definition der Funktion in USER_BESTELLUNG.php)

  // Wenn der 'Haendlermodus' eingeschaltet ist (Admintool in Allgemeine Shopsettings)
  $Haendlerarray = array(); // Initialisierung
  $Haendlerarray = getHaendlermodus(); // Element 0 = Haendlermodus ('Y' | 'N'), Element 1 = Haendler_login_text fuer darstellen = 10
  if ($Haendlerarray[0] == 'Y') {

      // ----------------------------
      // Test ob Kunde eingeloggt ist
      // ----------------------------
      $Kunden_ID = false;         // Initialisierung dieser Variable (Wenn = false, bedeutet das, dass der Kunde nicht eingeloggt ist)
      $Session_ID = session_id(); // Auslesen der Session_ID
      $login_ok = false;          // Wenn dieses Flag = true ist, so wird der Kunde weiter gelassen
      $Shopname = getShopname();  // Shopname aus der Datenbank lesen

      if (!defined(AUTH_PATH)) define('AUTH_PATH','./'); // Pfad zu Bildern, CSS-Files,... (muss von aussen definiert werden, wenn Pfad ungliche ..../shop/

      $Kunden_ID = checkSessionExpired($Session_ID);
      // Wenn diese Funktion eine Kunden_ID zurueck liefert, so wurden die vom Kunden angegebenen Benutzernamen und Passwort akzeptiert
      if ($Kunden_ID == false) {
          // Fall die Session abgelaufen ist und diese Datei nicht aus index.php aufgerufen wurde, wird ein redirect auf index.php gemacht
          if (AUTH_VIA_INDEX != 'yes') {
              $auth_darstellen = 14;
          }
          else {
              // Vergessen gegangenes Passwort wurde versendet (oder versucht zu versenden) - Entsprechende Erfolgsmeldung anzeigen
              if ($auth_darstellen == 12) {
              }
              // Loginscreen anzeigen
              else if ($test_create_login != 'true') {
                  $auth_darstellen = 10; // Loginscreen anzeigen
                  // Um Frame-in-Frame Situationen zu vermeiden, wird beim ersten Aufruf ein Redirect zum index.php durchgefuehrt (wird spaeter eingebaut)
                  // if ($auth_redirect != "true") {
                  //    $redirected_index_page = preg_replace('°/shop.*$°','/index.php',str_replace('https://','http://',getShopRootPath(true))).'?auth_redirect=true&'.$QUERY_STRING;
                  //    header("Location: $redirected_index_page");
                  // }
              }
              // Ueberpruefen des angegebenen Benutzernamens und des Passworts - gegebenenfalls eine gueltige Session erzeugen.
              else {
                  // Kundenangaben waren falsch -> Nochmaliges Login versuchen oder sich Passwort senden lassen
                  $last_login = test_create_Login($Benutzername, $Passwort, $Session_ID);
                  if ($last_login == 'false') {
                      $auth_darstellen = 11;
                  } // Ende Logindaten falsch eingegeben
                  // Wenn ein Kunde sich zwar korrekt einloggen konnte, aber sein Account gesperrt ist
                  elseif ($last_login == 'gesperrt') {
                      $auth_darstellen = 13;
                  } // Ende Kunde gesperrt
                  else {
                      // Kunde ist korrekte eingeloggt
                      $login_ok = true;
                  }// Ende erfolgreich authentifiziert
              }
          } // end of else
      }
      else {
          // Kunde ist korrekt eingeloggt
          $login_ok = true;
      }

  // -------------------------------
  // Loginscreen fuer Kunde anzeigen
  // -------------------------------
  if ($auth_darstellen == 10) {
      // HTML-Header ausgeben
      echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
      echo "<html>\n";
      echo "<head>\n";
      echo "  <meta HTTP-EQUIV=\"content-type\" content=\"text/html;charset=iso-8859-1\">\n";
      echo "  <meta HTTP-EQUIV=\"language\" content=\"de\">\n";
      echo "  <meta HTTP-EQUIV=\"author\" content=\"Jose Fontanil & Reto Glanzmann\">\n";
      echo "  <meta name=\"robots\" content=\"all\">\n";
      echo "  <link rel=\"STYLESHEET\" href=\"".AUTH_PATH."shopstyles.css\" type=\"text/css\">\n";
      echo "  <title>Shop</title>\n";

      // JavaScript chkFormular Funktion um Formulareingaben zu pruefen ausgeben
      echo "          <script type=\"text/javascript\">\n";
      echo "          <!--\n";
      echo "          function chkFormular() {\n";
      echo "            Benutzername = document.Formular.Benutzername.value;\n";
      echo "            Passwort = document.Formular.Passwort.value;\n";
      echo "\n";
      echo "            // ueberpruefen, ob der Benutzername mindestes 4 Zeichen hat\n";
      echo "            if(Benutzername.length < 4) {\n";
      echo "                alert(\"Benutzername muss mindestens 4 Zeichen haben!\");\n";
      echo "                document.Formular.Benutzername.focus();\n";
      echo "                return false;\n";
      echo "            }\n";
      echo "\n";
      echo "            // ueberpruefen, ob der Passwort mindestes 6 Zeichen hat\n";
      echo "            if(Passwort.length < 6 && Passwort != \"\" && Passwort !=\" \") {\n";
      echo "                alert(\"Passwort muss mindestens 6 Zeichen haben!\");\n";
      echo "                document.Formular.Passwort.focus();\n";
      echo "                return false;\n";
      echo "            }\n";
      echo "\n";
      echo "            // falls nur ein Benutzername eingegeben wurde\n";
      echo "            if(Benutzername.length > 3 && Passwort.length < 6) {\n";
      echo "                alert(\"Bitte geben Sie ein Passwort ein!\");\n";
      echo "                document.Formular.Passwort.focus();\n";
      echo "                return false;\n";
      echo "            }\n";
      echo "\n";
      echo "            // falls nur ein Passwort eingegeben wurde\n";
      echo "            if(Passwort.length > 5 && Benutzername.length < 4) {\n";
      echo "                alert(\"Bitte geben Sie einen Benutzernamen ein!\");\n";
      echo "                document.Formular.Benutzername.focus();\n";
      echo "                return false;\n";
      echo "            }\n";
      echo "          } // end of function chkFormular\n";
      echo "          // -->\n";
      echo "          </script>\n";
      echo "</head>\n";

      // HTML-body starten
      echo "<body class=\"content\" style=\"margin-top:0; margin-bottom:0; margin-left:0; margin-right:0;\">\n";
      echo "  <form name=\"Formular\" onSubmit=\"return chkFormular()\" action=\"".$PHP_SELF."\" method='POST' title='Formular' >\n";
      echo "    <table class=\"top_titel\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

      // Top-Frame (hier in einer Tabellenzeile)
      echo "  <tr class=\"top_titel\">\n";
      $top_height = getcssarg("top_height");
      echo "      <td class=\"top\" colspan=\"3\" height=\"".$top_height."\"align=\"center\" valign=\"middle\">\n";

      $show = getcssarg("top_left");
      if ($show == "shopname") echo $Shopname;
      if ($show == "shoplogo") echo "<img src=\"".AUTH_PATH."/Bilder/shoplogo.".getcssarg("logo_bg_img_typ")."\" border=0 alt='$Shopname'>";
      echo "    </td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Titelzeile
      echo "  <tr class=\"content\">\n";
      echo "    <td width=\"".$spalte1_breite."\"></td>\n";
      echo "    <td class=\"content\" width=\"".$spalte2_breite."\" align=\"left\">\n";
      echo "      <h3 class=\"content\">Anmeldung Shopbenutzer</h3>\n";
      echo "    </td>\n";
      echo "    <td width=\"".$spalte3_breite."\"></td>\n";
      echo "  </tr>\n";

      // Haendler Login Text (aus Datenbank)
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"left\">\n";
      echo "      ".$Haendlerarray[1]."\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Eingabefelder fuer Benutzernamen und Passwort
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"left\">\n";
      echo "      <b class=\"content\" style='font-weight:bold'>Benutzername:</b><br>\n";
      echo "      <input type=text name=\"Benutzername\" maxlength=\"50\" size=\"30\" value=\"".$Benutzername."\"><br>\n";
      echo "      <b class=\"content\" style='font-weight:bold'>Passwort:</b><br>\n";
      echo "      <input type=password name=\"Passwort\" maxlength=\"30\" size=\"30\">\n";
      echo "      <input type=\"hidden\" name=\"test_create_login\" value=\"true\">\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Absenden-Button
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"center\">\n";
      echo "      <input type=image src=\"".AUTH_PATH."Buttons/bt_weiter.gif\">\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      echo "</table>\n";
      echo "</form>\n";
      echo "</body>\n";
      echo "</html>\n";

      exit; // Programmablauf beenden (!)
  }// End of if $auth_darstellen == 10

  // -----------------------------------------------------------------------
  // Eingabeformular, falls jemand sein Passwort vergessen hat
  // -----------------------------------------------------------------------
  else if ($auth_darstellen == 11) {
      echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
      echo "<html>\n";
      echo "<head>\n";
      echo "  <meta HTTP-EQUIV=\"content-type\" content=\"text/html;charset=iso-8859-1\">\n";
      echo "  <meta HTTP-EQUIV=\"language\" content=\"de\">\n";
      echo "  <meta HTTP-EQUIV=\"author\" content=\"Jose Fontanil & Reto Glanzmann\">\n";
      echo "  <meta name=\"robots\" content=\"all\">\n";
      echo "  <link rel=\"STYLESHEET\" href=\"".AUTH_PATH."shopstyles.css\" type=\"text/css\">\n";
      echo "  <title>Shop</title>\n";

      // JavaScript
      echo "          <script type=\"text/javascript\">\n";
      echo "          <!--\n";
      echo "          function chkFormular() {\n";
      echo "            Benutzername = document.Formular.Benutzername.value;\n";
      echo "\n";
      echo "            // ueberpruefen, ob der Benutzername mindestes 4 Zeichen hat\n";
      echo "            if(Benutzername.length < 4) {\n";
      echo "                alert(\"Benutzername muss mindestens 4 Zeichen haben!\");\n";
      echo "                document.Formular.Benutzername.focus();\n";
      echo "                return false;\n";
      echo "            }\n";
      echo "\n";
      echo "          </script>\n";
      echo "</head>\n";

      // body
      echo "<body class=\"content\" style=\"margin-top:0; margin-bottom:0; margin-left:0; margin-right:0;\">\n";
      echo "  <form name=\"Formular\" onSubmit=\"return chkFormular()\" action=\"".$PHP_SELF."\" method='POST' title='Formular' >\n";
      echo "    <table class=\"top_titel\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

      // Top-Frame (hier in einer Tabellenzeile)
      echo "  <tr class=\"top_titel\">\n";
      $top_height = getcssarg("top_height");
      echo "      <td class=\"top\" colspan=\"3\" height=\"".$top_height."\"align=\"center\" valign=\"middle\">\n";
      $show = getcssarg("top_left");
      if ($show == "shopname") echo $Shopname;
      if ($show == "shoplogo") echo "<img src=\"".AUTH_PATH."/Bilder/shoplogo.".getcssarg("logo_bg_img_typ")."\" border=0 alt='$Shopname'>";
      echo "    </td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Titelzeile
      echo "  <tr class=\"content\">\n";
      echo "    <td width=\"".$spalte1_breite."\"></td>\n";
      echo "    <td class=\"content\" width=\"".$spalte2_breite."\" align=\"left\">\n";
      echo "      <h3 class='content'>Eingegebener Benutzername / Passwort war falsch</h3>\n";
      echo "    </td>\n";
      echo "    <td width=\"".$spalte3_breite."\"></td>\n";
      echo "  </tr>\n";

      // Haendler Login Text (aus Datenbank)
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"left\">\n";
      echo "      Wollen Sie sich Ihr Passwort zusenden lassen?\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Eingabefelder fuer Benutzernamen und Passwort
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"left\">\n";
      echo "      <b class=\"content\" style='font-weight:bold'>Benutzername:</b><br>\n";
      echo "      <input type=text name=\"Benutzername\" maxlength=\"50\" size=\"30\" value=\"".$Benutzername."\"><br>\n";
      echo "      <input type=\"hidden\" name=\"auth_darstellen\" value=\"12\">\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Absenden-Button
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"center\">\n";
      echo "      <a href=\"".$PHP_SELF."?Benutzername=".urlencode($Benutzername)."\"><img src=\"".AUTH_PATH."Buttons/bt_zurueck.gif\" border=\"0\" alt=\"zurueck\"></a>&nbsp;\n";
      echo "      <input type=image src=\"".AUTH_PATH."Buttons/bt_weiter.gif\" alt=\"Passwort senden\">\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      echo "</table>\n";
      echo "</form>\n";
      echo "</body>\n";
      echo "</html>\n";

      exit; // Programmbearbeitung hier beenden (!)
  }  // End $auth_darstellen == 11

  // ------------------------------
  // Passwort einem User zuschicken
  // ------------------------------
  else if ($auth_darstellen == 12){
      echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
      echo "<html>\n";
      echo "<head>\n";
      echo "  <meta HTTP-EQUIV=\"content-type\" content=\"text/html;charset=iso-8859-1\">\n";
      echo "  <meta HTTP-EQUIV=\"language\" content=\"de\">\n";
      echo "  <meta HTTP-EQUIV=\"author\" content=\"Jose Fontanil & Reto Glanzmann\">\n";
      echo "  <meta name=\"robots\" content=\"all\">\n";
      echo "  <link rel=\"STYLESHEET\" href=\"".AUTH_PATH."shopstyles.css\" type=\"text/css\">\n";
      echo "  <title>Shop</title>\n";

      // JavaScript
      echo "  <script type=\"text/javascript\">\n";
      echo "  <!--\n";
      echo "  function popUp(URL) {\n";
      echo "      day = new Date();\n";
      echo "      id = day.getTime();\n";
      echo "      eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=670,left = 312,top = 234');\");\n";
      echo "  } // end of function popUp\n";
      echo "  // -->\n";
      echo "  </script>\n";
      echo "</head>\n";

      // body
      echo "<body class=\"content\" style=\"margin-top:0; margin-bottom:0; margin-left:0; margin-right:0;\">\n";
      echo "  <form name=\"Formular\" onSubmit=\"return chkFormular()\" action=\"".$PHP_SELF."\" method='POST' title='Formular' >\n";
      echo "    <table class=\"top_titel\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

      // Top-Frame (hier in einer Tabellenzeile)
      echo "  <tr class=\"top_titel\">\n";
      $top_height = getcssarg("top_height");
      echo "      <td class=\"top\" colspan=\"3\" height=\"".$top_height."\"align=\"center\" valign=\"middle\">\n";

      $show = getcssarg("top_left");
      if ($show == "shopname") echo $Shopname;
      if ($show == "shoplogo") echo "<img src=\"".AUTH_PATH."/Bilder/shoplogo.".getcssarg("logo_bg_img_typ")."\" border=0 alt='$Shopname'>";
      echo "    </td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      //falls der Mailversand geklappt hat
      if(mailPasswort($Benutzername)){

          // Titelzeile
          echo "  <tr class=\"content\">\n";
          echo "    <td width=\"".$spalte1_breite."\"></td>\n";
          echo "    <td class=\"content\" width=\"".$spalte2_breite."\" align=\"left\">\n";
          echo "      <h3 class=\"content\">Passwort versendet</h3>\n";
          echo "    </td>\n";
          echo "    <td width=\"".$spalte3_breite."\"></td>\n";
          echo "  </tr>\n";

          // Hinweistext
          echo "  <tr class=\"content\">\n";
          echo "    <td></td>\n";
          echo "    <td class=\"content\" align=\"left\">\n";
          echo "      Wir haben Ihnen Ihr Passwort zugesendet. Sie sollten es in den n&auml;chsten Minuten erhalten.\n";
          echo "    </td>\n";
          echo "    <td></td>\n";
          echo "  </tr>\n";

          // Leerzeile
          echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

          // Weiter-Button
          echo "  <tr class=\"content\">\n";
          echo "    <td></td>\n";
          echo "    <td class=\"content\" align=\"center\">\n";
          echo "      <a href=\"".$PHP_SELF."?auth_darstellen=10\"><img src=\"".AUTH_PATH."Buttons/bt_weiter.gif\" border=\"0\" alt=\"weiter\"></a>\n";
          echo "    </td>\n";
          echo "    <td></td>\n";
          echo "  </tr>\n";

      } // end of if

      // Meldung, falls Mailversand des Passwortes NICHT geklappt hat
      else{
          // Titelzeile
          echo "  <tr class=\"content\">\n";
          echo "    <td width=\"".$spalte1_breite."\"></td>\n";
          echo "    <td class=\"content\" width=\"".$spalte2_breite."\" align=\"left\">\n";
          echo "      <h3 class=\"content\" align=\"center\">Passwortversand gescheitert!</h3>\n";
          echo "    </td>\n";
          echo "    <td width=\"".$spalte3_breite."\"></td>\n";
          echo "  </tr>\n";

          // Hinweistext
          echo "  <tr class=\"content\">\n";
          echo "    <td></td>\n";
          echo "    <td class=\"content\" align=\"left\">\n";
          echo "      <b class='content' style='font-weight:bold'>Wir konnten Ihnen Ihr Passwort leider nicht zusenden. Dies kann folgende Gr&uuml;nde haben:</b>\n";
          echo "      <ul>\n";
          echo "        <li>Der von Ihnen eingegebene Benutzername existiert nicht.<br></li>\n";
          echo "        <li>Sie haben bei Ihrer letzten Bestellung keine E-Mail Adresse angegeben.<br></li>\n";
          echo "      </ul>\n";
          echo "      <br><br>\n";
          echo "      Bitte wenden Sie sich an unseren <a href=\"javascript:popUp('".AUTH_PATH."kontakt.php?subject=Shopnachricht%20%20Passwort%20konnte%20nicht%20an%20User%20gesendet%20werden')\">Support</a>!<br><br>\n";
          echo "    </td>\n";
          echo "    <td></td>\n";
          echo "  </tr>\n";

          // Weiter-Button
          echo "  <tr class=\"content\">\n";
          echo "    <td></td>\n";
          echo "    <td class=\"content\" align=\"center\">\n";
          echo "      <a href=\"".$PHP_SELF."?auth_darstellen=10\"><img src=\"".AUTH_PATH."Buttons/bt_weiter.gif\" border=\"0\" alt=\"weiter\"></a>\n";
          echo "    </td>\n";
          echo "    <td></td>\n";
          echo "  </tr>\n";
      } // end of else

      echo "</table>\n";
      echo "</form>\n";
      echo "</body>\n";
      echo "</html>\n";

      exit; // Programmbearbeitung beenden (!)
  } // End $auth_darstellen == 12

  // ------------------------------
  // Kundenaccount gesperrt Meldung
  // ------------------------------
  else if ($auth_darstellen == 13){
      echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
      echo "<html>\n";
      echo "<head>\n";
      echo "  <meta HTTP-EQUIV=\"content-type\" content=\"text/html;charset=iso-8859-1\">\n";
      echo "  <meta HTTP-EQUIV=\"language\" content=\"de\">\n";
      echo "  <meta HTTP-EQUIV=\"author\" content=\"Jose Fontanil & Reto Glanzmann\">\n";
      echo "  <meta name=\"robots\" content=\"all\">\n";
      echo "  <link rel=\"STYLESHEET\" href=\"".AUTH_PATH."shopstyles.css\" type=\"text/css\">\n";
      echo "  <title>Shop</title>\n";

      // JavaScript
      echo "  <script type=\"text/javascript\">\n";
      echo "  <!--\n";
      echo "  function popUp(URL) {\n";
      echo "      day = new Date();\n";
      echo "      id = day.getTime();\n";
      echo "      eval(\"page\" + id + \" = window.open(URL, '\" + id + \"', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=670,left = 312,top = 234');\");\n";
      echo "  } // end of function popUp\n";
      echo "  // -->\n";
      echo "  </script>\n";
      echo "</head>\n";

      // body
      echo "<body class=\"content\" style=\"margin-top:0; margin-bottom:0; margin-left:0; margin-right:0;\">\n";
      echo "    <table class=\"top_titel\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

      // Top-Frame (hier in einer Tabellenzeile)
      echo "  <tr class=\"top_titel\">\n";
      $top_height = getcssarg("top_height");
      echo "      <td class=\"top\" colspan=\"3\" height=\"".$top_height."\"align=\"center\" valign=\"middle\">\n";

      $show = getcssarg("top_left");
      if ($show == "shopname") echo $Shopname;
      if ($show == "shoplogo") echo "<img src=\"".AUTH_PATH."/Bilder/shoplogo.".getcssarg("logo_bg_img_typ")."\" border=0 alt='$Shopname'>";
      echo "    </td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Titel
      echo "  <tr class=\"content\">\n";
      echo "    <td width=\"".$spalte1_breite."\"></td>\n";
      echo "    <td class=\"content\" width=\"".$spalte2_breite."\" align=\"left\">\n";
      echo "      <h3 class=\"content\">Kundenaccount gesperrt!</h3>\n";
      echo "    </td>\n";
      echo "    <td width=\"".$spalte3_breite."\"></td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Hinweistext
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"left\">\n";
      echo "      <b class='content' style='font-weight:bold'>Ihr Kundenaccount wurde gesperrt. Dies kann unter Umst&auml;nden an folgenden Gr&uuml;nden liegen:</b>\n";
      echo "      <ul>\n";
      echo "        <li>Neuer Account, welcher noch nicht freigeschaltet wurde<br></li>\n";
      echo "        <li>Es liegen noch ausstehende Zahlungen an uns vor<br></li>\n";
      echo "      </ul>\n";
      echo "      <br>\n";
      echo "      Bitte nehmen Sie mit uns <a href=\"javascript:popUp('".AUTH_PATH."kontakt.php?subject=Shopnachricht%20%20Kundenaccount%20gesperrt')\">Kontakt</a> auf um dieses Problem zu beheben.<br><br>\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      // Leerzeile
      echo "  <tr><td colspan=\"3\">&nbsp;</td></tr>\n";

      // Absenden-Button
      echo "  <tr class=\"content\">\n";
      echo "    <td></td>\n";
      echo "    <td class=\"content\" align=\"center\">\n";
      echo "      <a href=\"".$PHP_SELF."?auth_darstellen=10\"><img src=\"".AUTH_PATH."Buttons/bt_zurueck.gif\" border=\"0\" alt=\"zurueck\"></a>&nbsp;\n";
      echo "    </td>\n";
      echo "    <td></td>\n";
      echo "  </tr>\n";

      echo "</table>\n";
      echo "</body>\n";
      echo "</html>\n";

      exit; // Programmbearbeitung beenden (!)
  } // End $auth_darstellen == 13

  // ------------------------------------------------------------
  // Falls die Session abgelaufen ist, weiterleitung an index
  // ------------------------------------------------------------
  else if ($auth_darstellen == 14) {
      ?>
      <html>
      <head>
         <meta http-equiv="refresh" content="0; url=<?php echo '../index.php target=_top'; ?>"> -->
         <title>Shop</title>
      </head>
      <body onLoad='parent.location.href = "../index.php"'>
         <table border=0 width=100% height=100%>
            <tr>
            <td align=center valign=middle>
               <a href="../index.php" target="_top">Session abgelaufen! Zum Loginscreen (Falls sie nicht weitergeleitet wurden)</a>
            </td>
            </tr>
         </table>
      </body>
      </html>
      <?php
      exit;
  }// End else (auth_darstellen-Variablen Check)

  // ------------------------------------------------------------
  // Hier sollte man nie hinkommen (Weiterleitung an Loginscreen)
  // ------------------------------------------------------------
  else if ($login_ok == false) {
      ?>
      <html>
      <head>
         <meta http-equiv="refresh" content="0; url=<?php echo $PHP_SELF.'?darstellen=10'; ?>">
         <title>Shop</title>
      </head>
      <body>
         <table border=0 width=100% height=100%>
            <tr>
            <td align=center valign=middle>
               <a href="../index.php">Zum Shop Loginscreen (Falls sie nicht weitergeleitet wurden)</a>
            </td>
            </tr>
         </table>
      </body>
      </html>
      <?php
  }// End else (auth_darstellen-Variablen Check)

}// End if getHaendlermodus == Y

  // End of file -----------------------------------------------------------
?>
