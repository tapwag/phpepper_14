<?php
  // Filename: SHOP_HTACCESS.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION / OHNE TELNET/SSH-PAKET
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet das GUI fuer die .htaccess und .htpasswd Generierung
  //
  // Sicherheitsstatus:        *** ADMIN ***       (geringere Sicherheit!!!)
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_HTACCESS.php,v 1.12 2003/05/26 13:02:36 glanzret Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_HTACCESS = true;

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($HTTP_GET_VARS);
  extract($HTTP_POST_VARS);
  extract($HTTP_SERVER_VARS);

  // HTML-Kopf, der bei jedem Aufruf des Files ausgegeben wird
?>
  <HTML>
    <HEAD>
        <TITLE>Bestellungsmanagement</TITLE>
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
        </script>
    </HEAD>
    <BODY>

<?php
  // darstellen = 10
  // Generierung und Erfolgsmeldung nach Erstellen
  if ($darstellen == 10){
    echo "<h1>SHOP ADMINISTRATION</h1>\n";
    echo "<h3>Schutz des Administrationsverzeichnisses durch .htaccess</h3>\n";
    // Auswertung der Parameter
    if ($passwort != $passwortbestaetigung) {
        echo "<BR>\n<BR>\n<B>Die beiden Passworteingaben waren ungleich, bitte geben Sie das Passwort nochmals ein.</B>\n<BR>\n";
        die("<BR>\n<a href='".$PHP_SELF."?auth_type=$auth_type&username=$username' ><img src='../Buttons/bt_weiter_admin.gif' border='0' alt='htaccess_loeschen' align='absmiddle'></a>\n</body>\n</html>\n");
    }
    else if ($passwort == "" || $passwortbestaetigung == "" || $username == "" || $auth_type == "") {
        echo "<BR>\n<BR>\n<B>Es m&uuml;ssen alle Felder ausgef&uuml;llt werden!</B>\n<BR>\n";
        die("<BR>\n<a href='".$PHP_SELF."?auth_type=$auth_type&username=$username'><img src='../Buttons/bt_weiter_admin.gif' border='0' alt='htaccess_loeschen' align='absmiddle'></a>\n</body>\n</html>\n");
    }
    else {
        // falls auf dem Server die Standard-DES-Verschluesselung verfuegbar ist, zwingen wir die crypt-Funktion, diesen Algorithmus zu
        // verwenden. Bei nichtvorhandensein von DES ueberlassen wir crypt die Wahl des Verschluesselungsalgorithmus
        if(CRYPT_STD_DES == 1){
	    // zweistelliges Salt erzeugen (wird crypt ein zweistelliges salt uebergeben, so verwendet es DES zur Verschluesselung)
	    $salt = "";
            $salt_lenght = 2;
	    for($x=0;$x<$salt_lenght;$x++) {
		$salt .= substr(crypt(rand(1,65536)),3,1);
	    } // end of for
	    $encrypted_passwort = crypt($passwort,$salt);
	} // end of if
	else{
	    $encrypted_passwort = crypt($passwort);
	} // end of else
        $passwort_content .= $username.":".$encrypted_passwort."\n";
        $passwort_datei=".htpasswd";
        $wf = fopen ("./".$passwort_datei, "w+");
        if(!fwrite ($wf,$passwort_content)) {
            die("<h3>S_HTACCESS_Error: darstellen = 10: Die Datei .htpasswd konnte nicht geschrieben werden. (Verzeichnis Zugriffsrechte = 777?)</h3>\n</body>\n</html>\n");
        }
        fclose ($wf);
        $path = $SCRIPT_FILENAME;
        $path = ereg_replace('/SHOP_HTACCESS.php', '', $path);
        $htaccess_content = "AuthType $auth_type\nAuthName \"".$bereichsname."\"\nAuthUserFile ".$path."/".$passwort_datei."\nrequire valid-user";
        $wf = fopen ("./.htaccess", "w+");
        if(!fwrite ($wf,$htaccess_content)) {
            die("<h3>S_HTACCESS_Error: darstellen = 10: Die Datei .htaccess konnte nicht geschrieben werden. (Verzeichnis Zugriffsrechte = 777?)</h3>\n</body>\n</html>\n");
        }
        fclose ($wf);
    }
    echo "<B>Die Dateien .htaccess und .htpasswd wurden erfolgreich angelegt.</B><BR><BR>\n";
    echo "<BR>\n<a href='./Shop_Einstellungen_Menu_1.php' ><img src='../Buttons/bt_weiter_admin.gif' border='0' alt='htaccess_loeschen' align='absmiddle'></a>\n";

  } // end of if darstellen == 10

  // darstellen = 11
  // Loeschen von .htaccess und .htpasswd
  else if ($darstellen == 11){
    echo "<h1>SHOP ADMINISTRATION</h1>\n";
    echo "<h3>Schutz des Administrationsverzeichnisses durch .htaccess</h3>\n";

    if(file_exists("./.htaccess")) {
        if (!unlink("./.htaccess")) {
            // Wenn .htaccess nicht geloescht werden kann eine Fehlermeldung ausgeben:
            echo "<h3>Die Datei .htaccess konnte nicht gel&ouml;scht werden! Bitte manuell, per FTP l&ouml;schen.<BR>Hat das Admin-Verzeichnis die Zugriffsrechte 777 (chmod)?</h3>\n";
        }
        if(file_exists("./.htpasswd")) {
            // Wenn .htaccess nicht geloescht werden kann:
            if (!unlink("./.htpasswd")) {
                echo "<h3>Die Datei .htpasswd konnte nicht gel&ouml;scht werden! Bitte manuell, per FTP l&ouml;schen.<BR>Hat das Admin-Verzeichnis die Zugriffsrechte 777 (chmod)?</h3>\n";
            }
        }
        else {
            die("<h3>S_HTACCESS_Error: darstellen=11: Keine .htpasswd Datei zum l&ouml;schen vorhanden</h3>");
        }
    }
    else {
        die("<h3>S_HTACCESS_Error: darstellen=11: Keine .htaccess Datei zum l&ouml;schen vorhanden</h3>");
    }


    echo "<B>Die Dateien .htaccess und .htpasswd wurden gel&ouml;scht, das Administrationsverzeichnis ist jetzt <I>NICHT MEHR GESCH&Uuml;TZT</I>!!!</B>\n<BR><BR>\n";
    echo "<BR>\n<a href='".$PHP_SELF."' ><img src='../Buttons/bt_weiter_admin.gif' border='0' alt='htaccess_loeschen' align='absmiddle'></a>\n";

  } // end of if darstellen == 11

  // else
  // Hauptmenu htaccess, auch Anzeige (Status) von htaccess
  else {
    echo "<h1>SHOP ADMINISTRATION</h1>\n";
    echo "<h3>Schutz des Administrationsverzeichnisses durch .htaccess</h3>\n";
    // Test ob ein .htaccess schon existiert --> dann nur Loeschen anbieten, sonst Erstellen anzeigen
    if (file_exists("./.htaccess")) {
    // .htaccess (und damit auch .htpasswd) existieren shon, nur loeschen anbieten
    echo "";
    echo "<form action='./SHOP_HTACCESS.php' method='post' title='htaccess_erstellen_formular'>\n";
    echo "  <table border='0' cellpadding='0' cellspacing='0'>\n";
    echo "    <tr>\n";
    echo "      <td>\n";
    echo "        <B>Die .htaccess und .htpasswd Dateien sind schon vorhanden. Inhalt .htaccess:</B><BR>\n";
    echo "          <table border='1'>\n<tr>\n<td>\n";

    // Inhalt der .htaccess Datei auslesen und darstellen (in eigener Tabelle wegen Rahmen)
    $fp = fopen ("./.htaccess", "r");
    while ($buffer = fgets($fp, filesize("./.htaccess"))) {
        $htaccess .= $buffer."<BR>\n"; //Zeilenumbruch hinzufuegen
    }
    fclose ($fp);
    echo $htaccess."\n";

    echo "          </td>\n</tr>\n</table>\n";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "    <tr>\n";
    echo "      <td>\n";
    echo "        <BR>\n";
    echo "        <B>.htaccess und .htpasswd Dateien l&ouml;schen:</B>\n<BR><BR>\n";
    echo "        <a href='".$PHP_SELF."?darstellen=11' ><img src='../Buttons/bt_loeschen_admin.gif' border='0' alt='htaccess_loeschen' align='absmiddle'></a>";
    echo "        <a href='./Shop_Einstellungen_Menu_1.php' ><img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='Abbrechen' align='absmiddle'></a>";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "  </table>\n";
    echo "<INPUT type='hidden' name='darstellen' value='10'>\n";
    } // End if htaccess

    else {
    // .htaccess (und damit auch .htpasswd) existieren noch nicht, erstellen anbieten
    echo "<form action='./SHOP_HTACCESS.php?darstellen=10' method='post' title='htaccess_erstellen_formular'>\n";
    echo "  <table border='0' cellpadding='0' cellspacing='0'>\n";
    echo "    <tr>\n";
    echo "      <td>\n";
    echo "        <B>Erstellen eines .htaccess Schutzes</B><BR><BR>\n";
    echo "        <INPUT type='hidden' name='bereichsname' size='30' maxlength='30' value='Administrationsbereich'>\n";
    echo "        Username:<BR>\n";
    echo "        <INPUT type='text' name='username' size='16' maxlength='16' value='$username'><BR><BR>\n";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "    <tr>\n";
    echo "      <td>\n";
    echo "        Passwort:<BR>\n";
    echo "        <INPUT type='password' name='passwort' size='16' maxlength='16' value=''><BR>\n";
    echo "        Passwortbest&auml;tigung:<BR>\n";
    echo "        <INPUT type='password' name='passwortbestaetigung' size='16' maxlength='16' value=''><BR><BR>\n";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "    <tr>\n";
    echo "      <td>\n";
    echo "        <I>Authentication Type:</I><BR>\n";
    echo "        Basic: &nbsp<INPUT type='radio' name='auth_type' value='Basic' ";
    if ($auth_type == "Basic" || $auth_type == "") {echo " checked ";}
    echo " >\n";
    echo "        Advanced: &nbsp<INPUT type='radio' name='auth_type' value='Advanced' ";
    if ($auth_type == "Advanced") {echo " checked ";}
    echo " >\n<BR>\n<BR>\n";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "    <tr>\n";
    echo "      <td>\n";
    echo "        <input type=image src='../Buttons/bt_weiter_admin.gif' border='0' alt='Speichern' align='top'>\n";
    echo "        <a href='./Shop_Einstellungen_Menu_1.php' ><img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='Abbrechen' align='absmiddle'></a>";
    echo "      </td>\n";
    echo "    </tr>\n";
    echo "  </table>\n";
    echo "<INPUT type='hidden' name='darstellen' value='10'>\n";
    } // End else htaccess

  } // End else (darstellen)
  echo "  </BODY>";
  echo "</HTML>";

// End of file ----------------------------------------------------------
?>
