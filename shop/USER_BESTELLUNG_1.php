<?php
  // Filename: USER_BESTELLUNG_1.php
  //
  // Modul: Aufruf-Module : Bestellungsvorgang
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Sponsoren:
  // billBOX-Funktionalitaet gesponsert von der Firma billBOX AG, www.billbox.ch
  //
  // Zweck: In dieser Datei wird der Bestellvorgang (nach betreten der Kasse) ausgefuehrt
  //        ...bis und mit dem Versenden der Bestellungs-E-Mails.
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: USER_BESTELLUNG_1.php,v 1.93 2003/09/26 15:16:03 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_BESTELLUNG_1 = true;

  // -----------------------------------------------------------------------
  // Weitere Konfigurationsschritte vornehmen
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux/MACOS X beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);
  extract($_SERVER);

  // Einbindung des Session Managements. Wir muessen hier einen Spezialfall abdecken: PostFinance Zahlungsinitierung.
  // Diese wird im $darstellen == 16 Bereich abgehandelt und darf wegen sonst auftretender Fehlermeldung: ...POSTDATA
  // has expired from cache... nicht eingebunden werden
  if ($darstellen != 16) {
      // Session-Management einbinden
      if (!isset($session_mgmt)) {include("session_mgmt.php");}
  }
  else {
      // Nur Session-Variable $mySession_ID zugaenglich machen
      session_name("mySession_ID");
      session_id($mySession_ID);
  }
  // Einbinden der restlichen, benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Vorgehen in der Dokumentation
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}
  if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}
  if (!isset($USER_BESTELLUNG_DARSTELLUNG)) {include("USER_BESTELLUNG_DARSTELLUNG.php");}
  if (!isset($payment_interface)) {include("payment_interface.php");}
  // Es wird noch bei Bedarf die Datei saferpay_init.php included (darstellen == 15)
  // Es wird noch bei Bedarf die Datei postfinance_interface.php included (darstellen == 16)

  // Wenn der Haendlermodus aktiviert wurde (alle Kunden muessen sich zuerst einloggen), dann ueberprueft folgender Link,
  // ob man schon eingeloggt ist
  require('USER_AUTH.php');

  // Da wir mit dieser Datei den 'zu sichernden' Bereich betreten, in welchem der Kunde sensitive Daten eingeben kann
  // so wird geprueft, ob SSL im Adminmenu eingeschaltet wurde. Eventuell muessen wir einen Redirect durchfuehren,
  // z.B. dann, wenn SSL aktiviert sein soll, aber der Kunde noch aus dem unverschluesselten Raum kommt. Aus Sicher-
  // heitsgruenden kann dieser Redirect nur einmal hintereinander erfolgen:
  if (getSSL("","",false) == "https://" && !empty($_SERVER['HTTPS']) == false && $ssl_redirect != "true") {
       $ssl_enabled_page = getSSL($PHP_SELF, $HTTP_HOST, false).'?ssl_redirect=true&'.$QUERY_STRING;
       header("Location: $ssl_enabled_page");
  }

  // Wenn versucht wird am Login vorbei in die Kasse zu kommen - diesen Schritt verhindern
  if ($darstellen == 11 && check_if_gesperrt(session_id())) {
      // Kunde ist gesperrt
      $darstellen = 14;
  }

  // -----------------------------------------------------------------------
  // HTML-Dokument Header ist für alle Seiten gleich, darum wird er immer zuerst ausgegeben
?>
<html>
  <head>
    <meta HTTP-EQUIV="content-type" content="text/html;charset=iso-8859-1">
    <meta HTTP-EQUIV="language" content="de">
    <meta HTTP-EQUIV="author" content="Jose Fontanil & Reto Glanzmann">
    <meta name="robots" content="all">
    <link rel="stylesheet" href="shopstyles.css" type="text/css">
    <title>Shop</title>
    <?php if ( $darstellen == 15) { ?>
      <script src="http://www.saferpay.com/OpenSaferpayScript.js"></script>
    <?php }/* End if darstellen == 15 (Saferpay / B+S JavaScript Einbindung) */ ?>
  </head>
  <body class="content">
<?php

// -----------------------------------------------------------------------
// Bevor der Login-Screen ausgegeben wird, pruefen ob sich der Kunde schon
// eingeloggt hat
// ja -> direkt darstellen = 11 aufrufen
// nein -> Login-Screen ausgeben (darstellen=1)
// -----------------------------------------------------------------------
if ($darstellen == 1){
    // Test ob Cookies eingeschaltet sind:
    checkifCookiesenabled("","",1,0);

    // Zuerst von der Datenbank allenfalls schon vorhandene Daten auslesen und erst dann darstellen
    $meineBestellung = new Bestellung;
    $meineBestellung = getBestellung(session_id());

    if ($meineBestellung->artikelanzahl() == 0) {
        // 'Warenkorb ist leer' - Meldung ausgeben
        echo '<body class="content"><BR>'."\n";
        echo "<P><h3 class='content'><CENTER>Ihr Warenkorb ist leer!</CENTER></h3></P><BR>\n";
        echo '<h4 class="content"><CENTER>Sie k&ouml;nnen die Kasse erst betreten, wenn sie Artikel in Ihrem Warenkorb haben.</CENTER></h4>';
        die (); // damit nicht noch der Rest des Files abgearbeitet wird
    } // end of if
    else {
        if ($Kunden_ID != 'P'){
            // pruefen, ob der Kunde sich schon eingeloggt hat
            $Kunden_ID = checkSession(session_id());
            if ($Kunden_ID != ""){
                // Kunde hat sich eingeloggt - Check ob er gesperrt ist
                if (check_if_gesperrt(session_id())) {
                    // Kunde ist gesperrt
                    $darstellen = 14;
                }
                else {
                    // Kunde ist nicht gesperrt
                    $darstellen = 11;
                }
            } // end of if Kunden_ID
        } // end of if Kunden_ID
    } // end of else
} // end of erstes darstellen == 1

// -----------------------------------------------------------------------
// Login-Screen fuer Kunde
// -----------------------------------------------------------------------
if ($darstellen == 1){
    // Die Variable $redirect enthaelt die URL (oder nur die Datei), wohin der Shopkunde
    // nach einem erfolgreichen Loginvorgang hin gesendet werden soll.
    if ($redirect == "") {
        $redirect = getSSL($PHP_SELF, $HTTP_HOST, false).'?darstellen=11';
    }
?>
    <script language="JavaScript" type="text/javascript">
    <!--

    function chkFormular() {

      Benutzername = document.Formular.Benutzername.value;
      Passwort = document.Formular.Passwort.value;

      // ueberpruefen, ob der Benutzername mindestes 4 Zeichen hat
      if(Benutzername.length < 4 && Benutzername != "" && Benutzername !=" ") {
          alert("Benutzername muss mindestens 4 Zeichen haben!");
          document.Formular.Benutzername.focus();
          return false;
      }

      // ueberpruefen, ob der Passwort mindestes 6 Zeichen hat
      if(Passwort.length < 6 && Passwort != "" && Passwort !=" ") {
          alert("Passwort muss mindestens 6 Zeichen haben!");
          document.Formular.Passwort.focus();
          return false;
      }

      // falls nur ein Benutzername eingegeben wurde
      if(Benutzername.length > 3 && Passwort.length < 6) {
          alert("Bitte geben Sie ein Passwort ein!");
          document.Formular.Passwort.focus();
          return false;
      }

      // falls nur ein Passwort eingegeben wurde
      if(Passwort.length > 5 && Benutzername.length < 4) {
          alert("Bitte geben Sie einen Benutzernamen ein!");
          document.Formular.Benutzername.focus();
          return false;
      }

    }
    //-->

    </script>
    <form name="Formular" onSubmit="return chkFormular()" action='<?php echo $redirect; ?>' method='POST' title='Formular'>
      <div align="center">
      <table class="content" border="0" width="80%">
        <tr class='content'>
          <td class='content' colspan="3">
            <h3 class='content'>Anmeldung Benutzer/Neukunde</h3>
          </td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr class='content'>
          <td class='content' colspan=3>
            <p>Falls Sie schon Kunde bei uns sind, k&ouml;nnen sie jetzt Ihren Benutzernamen und Ihr Passwort eingeben. Ihre Adressdaten&nbsp;
            werden danach geladen.</p>
            <p>Sind Sie Neukunde, k&ouml;nnen Sie einen beliebigen Benutzernamen (am besten Ihre E-Mail-Adresse, da sie weltweit&nbsp;
            eindeutig ist) und ein Passwort w&auml;hlen. Wenn Sie nicht wollen, dass wir Ihre Adressdaten f&uuml;r einen sp&auml;teren Einkauf speichern, lassen Sie das Benutzername- und Passwortfeld leer.</p>
          </td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr class='content'>
          <td width="50%">&nbsp;</td>
          <td align="center" class='content'>
            <nobr><b class='content' style='font-weight:bold'>Benutzername:</b>(mindestens 4 Zeichen)</nobr><br><input type="text" name="Benutzername" maxlength="50" size="30" value=""><br>
            <nobr><b class='content' style='font-weight:bold'>Passwort:</b>(mindestens 6 Zeichen)</nobr><br><input type="password" name="Passwort" maxlength="30" size="30">
          </td>
          <td width="50%">&nbsp;</td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr class='content'>
          <td class='content' align="center" colspan="3">
            <input type="image" src='Buttons/bt_weiter.gif' border="0" alt="Weiter" title="Weiter">
          </td>
        </tr>
      </table>
      </div>
    </form>
<?php

} // ende von darstellen = 1

// -----------------------------------------------------------------------
// Eingabeformular, falls jemand sein Passwort vergessen hat
// -----------------------------------------------------------------------
else if ($darstellen == 9){

?>
    <script language="JavaScript" type="text/javascript">
    <!--

    function chkFormular() {

      Benutzername = document.Formular.Benutzername.value;


      // ueberpruefen, ob der Benutzername mindestes 4 Zeichen hat
      if(Benutzername.length < 4) {
          alert("Der Benutzername muss mindestens 4 Zeichen haben!");
          document.Formular.Benutzername.focus();
          return false;
      }
    } // end of function chkFormular
    //-->

    </script>
    <form name="Formular" onSubmit="return chkFormular()" action='<?php echo $PHP_SELF; ?>?darstellen=10' method='POST' title='Formular' >
      <center>
      <table class="content" border="0" width="80%">
        <tr class='content'>
          <td class='content'>
            <h3 class='content'>Passwort vergessen</h3>
          </td>
        </tr>
        <tr class='content'>
          <td class='content' colspan=3>
            <b class='content' style='font-weight:bold'>Geben Sie Ihren Benutzernamen ein und klicken dann auf 'weiter'.</b>
          </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr class='content'>
          <td class='content'>
            <nobr><b class='content' style='font-weight:bold'>Benutzername:</b>(mindestens 4 Zeichen)</nobr><br><input type=text name=Benutzername maxlength=50 size=30 value=""><br>
          </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr class='content'>
          <td class='content' align=center colspan=3>
            <input type="image" src='Buttons/bt_weiter.gif' border="0" alt="Weiter" title="Weiter"></center>
          </td>
        </tr>
      </table>
    </form>

<?php
}  // end of darstellen == 9

// -----------------------------------------------------------------------
// Passwort einem User zuschicken
// -----------------------------------------------------------------------
else if ($darstellen == 10){
    if(mailPasswort($Benutzername)){
        //falls der Mailversand geklappt hat
?>
        <table border=0 width=80%>
          <tr class='content'>
            <td class='content'>
              <h3 class='content'>Passwort versendet</h3></td></tr>
            </td>
          </tr>
          <tr class='content'>
            <td class='content'>
              <b class='content' style='font-weight:bold'>Wir haben Ihnen Ihr Passwort zugesendet. Sie sollten es in den nächsten Minuten erhalten.</b>
            </td>
          </tr>
          <tr class='content'>
            <td class='content'>
              <p class='content'><center><a href="<?php echo $PHP_SELF; ?>?darstellen=1"><img src="Buttons/bt_weiter.gif" border="0" alt="Zur&uuml;ck" title="Zur&uuml;ck"></a></center></p>
            </td>
          </tr>
        </table>
<?php
    } // end of if
    else{
?>
        <table border=0 width=80%>
          <tr class='content'>
            <td class='content'>
              <h3 class='content'>Passwortversand gescheitert!</h3></td></tr>
            </td>
          </tr>
          <tr class='content'>
            <td class='content'>
              <b class='content' style='font-weight:bold'>Wir konnten Ihnen Ihr Passwort leider nicht zusenden. Dies kann folgende Gr&uuml;nde haben.</b>
                <ul>
                  <li>Der von Ihnen eingegebene Benutzername existiert nicht.<br></li>
                  <li>Sie haben bei Ihrer letzten Bestellung keine E-Mail Adresse angegeben.<br></li>
                </ul>
            </td>
          </tr>
          <tr><td>&nbsp;</td></tr>
          <tr class='content'>
            <td class='content'>
              <p class='content'><center><a href="<?php echo $PHP_SELF; ?>?darstellen=1"><img src="Buttons/bt_weiter.gif" border="0" alt="Weiter" title="Weiter"></a></center></p>
            </td>
          </tr>
        </table>

<?php
    } // end of else
} // end of darstellen == 10

// ------------------------------
// Kundenaccount gesperrt Meldung
// ------------------------------
else if ($darstellen == 14) {
    $Shopname = getShopname();  // Shopname aus der Datenbank lesen
    ?>
      <html>
        <head>
          <meta HTTP-EQUIV="content-type" content="text/html;charset=iso-8859-1">
          <meta HTTP-EQUIV="language" content="de">
          <meta HTTP-EQUIV="author" content="Jose Fontanil & Reto Glanzmann">
          <meta name="robots" content="all">
          <link rel="STYLESHEET" href="shopstyles.css" type="text/css">
          <title>Shop</title>
                <SCRIPT LANGUAGE="JavaScript">
                <!--
                function popUp(URL) {
                day = new Date();
                id = day.getTime();
                eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=670,left = 312,top = 234');");
                }
                // End -->
            </script>
        </head>
        <body class="content">
        <table border=0 width=80%>
          <tr class='content'>
            <td class='content'>
              <h3 class='content' align="center">Kundenaccount gesperrt!</h3>
            </td>
          </tr>
          <tr class='content'>
            <td class='content' align="center">
              <b class='content' style='font-weight:bold'>Ihr Kundenaccount wurde gesperrt. Dies kann unter Umst&auml;nden an folgenden Gr&uuml;nden liegen:</b>
                <ul>
                  <li>Neuer Account, welcher noch nicht freigeschaltet wurde<br></li>
                  <li>Es liegen noch ausstehende Zahlungen an uns vor<br></li>
                </ul>
                <br><br>
                Bitte nehmen Sie mit uns <a href="javascript:popUp('kontakt.php?subject=Shopnachricht%20%20Kundenaccount%20gesperrt')">Kontakt</a> auf um dieses Problem zu beheben.<br><br>
            </td>
          </tr>
          <tr><td>&nbsp;</td></tr>
          <tr class='content'>
            <td class='content'> <br><br>
              <p class='content'><center><a href="<?php echo $PHP_SELF; ?>?darstellen=1"><img src="./Buttons/bt_zurueck.gif" border="0" alt="zurueck zum Login" title="zurueck zum Login"></a></center></p>
            </td>
          </tr>
        </table>
        <?php
        del_kunden_session(session_id()); // Aktuelle Session_ID loesche, so dass sich der Kunde wieder einloggen kann (oder es zumindest versuchen kann)
} // End darstellen == 14

// -----------------------------------------------------------------------
// Eingabeformular für Lieferadresse und Zahlungsart
// -----------------------------------------------------------------------
else if ($darstellen == 11){

  // Test ob Cookies eingeschaltet sind:
  checkifCookiesenabled("","",1,0);
  // Kunde einloggen, falls er noch nicht eingeloggt ist
  if ($Kunden_ID == "") {
      $Kunden_ID = checkLogin($Benutzername, $Passwort, session_id());
      // Kunde hat sich eingeloggt - Check ob er gesperrt ist
      if (check_if_gesperrt(session_id())) {
          // Kunde ist gesperrt
          $Shopname = getShopname();  // Shopname aus der Datenbank lesen
          ?>
      <html>
        <head>
          <meta HTTP-EQUIV="content-type" content="text/html;charset=iso-8859-1">
          <meta HTTP-EQUIV="language" content="de">
          <meta HTTP-EQUIV="author" content="Jose Fontanil & Reto Glanzmann">
          <meta name="robots" content="all">
          <link rel="STYLESHEET" href="shopstyles.css" type="text/css">
          <title>Shop</title>
                <SCRIPT LANGUAGE="JavaScript">
                <!--
                function popUp(URL) {
                day = new Date();
                id = day.getTime();
                eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=670,left = 312,top = 234');");
                }
                // End -->
            </script>

        </head>

        <body class="content">
        <table border=0 width=80%>
          <tr class='content'>
            <td class='content'>
              <h3 class='content' align="center">Kundenaccount gesperrt!</h3>
            </td>
          </tr>
          <tr class='content'>
            <td class='content' align="center">
              <b class='content' style='font-weight:bold'>Ihr Kundenaccount wurde gesperrt. Dies kann unter Umst&auml;nden an folgenden Gr&uuml;nden liegen:</b>
                <ul>
                  <li>Neuer Account, welcher noch nicht freigeschaltet wurde<br></li>
                  <li>Es liegen noch ausstehende Zahlungen an uns vor<br></li>
                </ul>
                <br><br>
                Bitte nehmen Sie mit uns <a href="javascript:popUp('kontakt.php?subject=Shopnachricht%20%20Kundenaccount%20gesperrt')">Kontakt</a> auf um dieses Problem zu beheben.<br><br>
            </td>
          </tr>
          <tr><td>&nbsp;</td></tr>
          <tr class='content'>
            <td class='content'> <br><br>
              <p class='content'><center><a href="<?php echo $PHP_SELF; ?>?darstellen=1"><img src="./Buttons/bt_zurueck.gif" border="0" alt="zurueck zum Login" title="zurueck zum Login"></a></center></p>
            </td>
          </tr>
        </table>
        </body>
        </html>
        <?php
        del_kunden_session(session_id()); // Aktuelle Session_ID loesche, so dass sich der Kunde wieder einloggen kann (oder es zumindest versuchen kann)
        exit; // Programmabbruch, da der Kunde gesperrt ist (analog darstellen == 14)
      }
      else {
          // Kunde ist nicht gesperrt
          $darstellen = 11;
      }

  }  // end of if
      // Wenn das eingegebene Passwort nicht zum Login passt
      if ($Kunden_ID == "P"){
?>
          <center>
          <table border=0 width=80%>
            <tr class='content'>
              <td class='content'>
                <h3 class='content'>Das von Ihnen eingegebene Passwort ist falsch!</h3></td></tr>
              </td>
            </tr>
            <tr class='content'>
              <td class='content'>
                <b class='content' style='font-weight:bold'>Dies kann folgende Gr&uuml;nde haben:</b>
                <ul>
                  <li>Sie haben versucht, sich das erste Mal anzumelden. Der von Ihnen gew&auml;hlte Benutzername ist jedoch schon vergeben.<br></li>
                  <li>Sie versuchen sich mit einem richtigen Benutzernamen, aber falschen Passwort anzumelden. Falls Sie Ihre Passwort vergessen haben, klicken Sie auf  &quot;Passwort vergessen&quot;. Ihr Passwort wird Ihnen dann per E-Mail zugestellt (an die E-Mail Adresse, die Sie bei Ihrer letzten Bestellung angegeben haben).<br></li>
                  <li>Achten Sie bitte auf korrekte Gross- / Kleinschreibung!<br></li>
                </ul>
              </td>
            </tr>
            <tr class='content'>
              <td class='content'>
                <?php $css_string = 'class="content" style="text-decoration:'.getcssarg("main_link_d").'; color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").'; font-weight:'.getcssarg("main_link_w").'"'; ?>
                <p class='content'><center><a href="<?php echo $PHP_SELF; ?>?darstellen=9" <?php echo $css_string; ?>><b class='content' style='font-weight:bold'>Passwort vergessen</b></a></center></p>
                <form action="<?php echo $PHP_SELF; ?>" name="Formular" method="POST">
                  <input type=hidden name=Kunden_ID value='P'>
                  <input type=hidden name=darstellen value='1'>
                  <center><input type="image" src='Buttons/bt_weiter.gif' border="0" alt="Weiter" title="Weiter"></center>
                </form>
              </td>
            </tr>
          </table>
          </center>
<?php
          // Abarbeitung hier abbrechen
          die();
      } // end of if $Kunden_ID == "P"

  // An dieser Stelle hat jeder Kunde definitiv eine eindeutige Kunden-ID erhalten
  // (entweder von checkSession oder von checkLogin)
  $myKunde = getKunde($Kunden_ID);
  $myBestellung = getBestellung(session_id());

  // Anzahl fest vordefinierter Felder
  $anz_vordef = 14;

  // Attributobjekt aus Datenbank holen
  $myAttribut = getAttributobjekt();

  // Anzahl Zusatzfelder
  $gesamt = $myAttribut->attributanzahl();
  $anz_zusatz = ($gesamt-$anz_vordef);

  // Die verschiedenen Werte und Einstellungen in Arrays abfuellen
  $Namen = $myAttribut->getallName();
  $verwenden = $myAttribut->getallanzeigen();
  $speichern = $myAttribut->getallin_DB();
  $pruefen = $myAttribut->getallEingabe_testen();

?>

  <script language="JavaScript" type="text/javascript">
  <!--

  function chkFormular() {

<?php
  // einen Array abfuellen, wo jedes Eingabefeld eine Arravariable erhält. Folgende Eintraege
  // werden gemacht:
  // Y -> dieses Feld auf Eingabe ueberprüfen
  // N -> dieses Feld nicht auf Eingabe ueberprüfen
  // M -> dieses Feld nach E-Mail-Kriterien auf Eingabe ueberpruefen
  echo "var checkarray=new Array (";
  for ($zaehl = 0; $zaehl <= ($gesamt-1); $zaehl++){
      if ($verwenden[$zaehl] == "Y"){
          // E-Mail Eingabeueberpruefung
          if(($pruefen[$zaehl] == "Y") && ($Namen[$zaehl] == "E-Mail")){ echo "\"M\","; }
          // normale Eingabeueberpruefung
          else if($pruefen[$zaehl] == "Y"){ echo "\"Y\","; }
          // keine Eingabeueberpruefung
          else { echo "\"N\","; }
      } // end of if
  } // end of for
  echo "\"0\");";
?>

      for(count=0; count < (checkarray.length-2); count++){
          if (checkarray[count] == 'Y'){
              var wert = document.Formular[count].value;
              if ( wert == "" || wert == " "){
                  ersetz = /\+/gi;
                  feldname = document.Formular[count].name.replace(ersetz," ");
                  var attr_count = 0;
                  for (attr_count = 1; attr_count <= 4; attr_count++){
                      var attr_name = "Attribut[" + attr_count + "]";
                      if (feldname == attr_name) {
                          feldname = document.getElementById("Attribut" + attr_count).value;
                      }
                  }
                  alert("Bitte füllen Sie das Feld "+unescape(feldname)+" aus!");
                  document.Formular[count].focus();
                  return false;
              } // end of if wert = ""
          }
          if (checkarray[count] == 'M'){
              var ok = 1;
              var email = document.Formular[count].value;
              var geteilt = email.split ("@");


              // falls mehr als ein oder gar kein '@' im string
              if (geteilt.length != 2){
                  ok = 0;
              }

              else{
                  // falls vor oder nach dem '@' nichts mehr kommt
                  if (geteilt[0] == "" || geteilt[1] == "" ) { ok = 0; }

                  // falls nach dem '@' kein Punkt mehr kommt
                  if (geteilt[1].indexOf(".") == "-1" ) { ok = 0; }

                  // falls direkt nach dem '@' oder ganz am Schluss ein Punkt kommt
                  var laenge = geteilt[1].length;
                  if (geteilt[1].indexOf(".") == "0" || geteilt[1].charAt(laenge-1) == ".") { ok = 0; }

                  // falls direkt vor dem '@' oder am Anfang ein Punkt kommt
                  var laenge = geteilt[0].length;
                  if (geteilt[0].indexOf(".") == "0" || geteilt[0].charAt(laenge-1) == ".") { ok = 0; }
              }

              if (ok == 0){
                  alert ("Keine gültige E-Mail-Adresse!");
                  document.Formular[count].focus();
                  return false;
              }
          }


      } // end of for
  }
  //-->
  </script>


  <FORM action='<?php echo $PHP_SELF; ?>' name='Formular' method='POST' onSubmit="return chkFormular()">
  <center>
  <TABLE class="content" border="0" cellpadding="0" cellspacing="0" width="80%">
    <tr>
      <td class='content' colspan=3>
        <h3 class='content'>Angaben zu Ihrer Bestellung</h3>
      </td>
    </tr>
    <tr>
      <td colspan=2 class='content'>
        <h5 class='content'>Bitte f&uuml;llen Sie alle mit <b class='content'>*</b> gekennzeichneten Felder korrekt aus,&nbsp;
                            damit wir Ihre Bestellung bearbeiten k&ouml;nnen</h5>
      </td>
    </tr>

<?php

  // hier werden in einem Array die Anzeigelaengen für die Eingabefelder gespeichert
  // [0] = Anrede (wird natürlich nicht gebraucht) [1] = Vorname, usw.
  $laenge_array = array('0', '40', '40', '40', '40', '40', '10', '10', '40', '20', '16', '16', '40', '40', '40', '40', '40', '0');

  // hier werden in einem Array die maximalen Eingabelaengen für die Eingabefelder gespeichert
  // [0] = Anrede (wird natürlich nicht gebraucht) [1] = Vorname, usw.
  $max_array = array('0', '128', '128', '128', '128', '128', '16', '32', '128', '128', '32', '32', '128', '128', '128', '128', '128', '0');

  // Kunden-Daten in einen Array abfuellen
  $daten_array = array($myKunde->Anrede, $myKunde->Vorname, $myKunde->Nachname, $myKunde->Firma, $myKunde->Abteilung, $myKunde->Strasse,
                $myKunde->Postfach, $myKunde->PLZ, $myKunde->Ort, $myKunde->Land, $myKunde->Tel, $myKunde->Fax, $myKunde->Email,
                $myKunde->Attributwert1, $myKunde->Attributwert2, $myKunde->Attributwert3, $myKunde->Attributwert4, $myBestellung->Anmerkung);


  // Ueberpruefen, ob die selbstkonfigurierbaren Felder nicht an die Bestellung gebunden wurden. Wenn ja, so sollen
  // sie aus der Bestellung ausgelesen werden (ist hier momentan ziemlich statisch geloest):
  for ($i=13;$i <= 16; $i++) {
      if ($daten_array[$i] == "") {
          $name = "Attributwert".($i-12);
          $daten_array[$i] = $myBestellung->$name;
      }
  }

  $attr_cnt = 1; // Attributcounter, fuer Attributindexierung

  // Hauptfelder ausgeben
  for ($zaehl = 0; $zaehl <= ($gesamt-1); $zaehl++){
      // nur Felder ausgeben, die der Shopbetreiber auch aktiviert hat
      if($verwenden[$zaehl] == 'Y'){

          // Anrede-Dropdown-Liste ausgeben..
          if ($zaehl == 0){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              echo "  <td class='content'>".$Namen[$zaehl].":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td><td class='content'>";
              echo "<select style='font-family: Courier, Courier New, Monaco' name='".$Namen[$zaehl]."'>\n";
              // default option -> keine Anrede
              echo "  <option ";
              if ($daten_array[$zaehl] == "") { echo "selected "; }
              echo "value=''> bitte ausw&auml;hlen&nbsp;&nbsp;&nbsp;\n";
              // Anrede: "Herr"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Herr") { echo "selected "; }
              echo "value='Herr'>Herr\n";
              // Anrede: "Frau"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Frau") { echo "selected "; }
              echo "value='Frau'>Frau\n";
              // Anrede: "Firma"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Firma") { echo "selected "; }
              echo "value='Firma'>Firma\n";
              // Anrede: "Familie"
              echo "  <option ";
              if ($daten_array[$zaehl] == "Familie") { echo "selected "; }
              echo "value='Familie'>Familie\n";
              echo "</select>\n";
              echo "</td>\n</tr>\n";
          } // end of if $zaehl == 0

          // Standard-Textfelder Ausgeben, Name, Strasse,..
          if ($zaehl > 0 && $zaehl < ($anz_vordef-1)){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              echo "  <td class='content'>".htmlspecialchars($Namen[$zaehl]).":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td>\n";
              $einsetzen = rawurlencode($Namen[$zaehl]);
              $einsetzen_1 = str_replace(".", "%2E", $einsetzen);
              // Wenn es sich um die Landesabfrage handelt, so koennen wir via IP-Check das Land des
              // Shopkunden vermuten (98%-Genauigkeit). Siehe auch: http://www.ip-to-country.com/.
              if ($Namen[$zaehl] == "Land") {
                  // Wenn wir das Land schon einmal angegeben haben, nicht nochmals einen Online-Check vornehmen.
                  if ($daten_array[$zaehl] == "" && get_check_user_country() == true) {
                      $landvermutung = check_user_country();
                      $daten_array[$zaehl] = $landvermutung;
                  }// End if $daten_array
              }// End if $Namen[$zaehl] == "Land"
              echo "<td class='content'><input type=text name=\"".$einsetzen_1."\" size='".$laenge_array[$zaehl]."' maxlength='".$max_array[$zaehl]."' value=\"".htmlspecialchars($daten_array[$zaehl])."\"></td>\n";
              echo "</tr>\n";
          } //

          // frei konfigurierbare Zusatzfelder ausgeben
          if ($zaehl > 12 && $zaehl < 17){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              echo "  <td class='content'>".htmlspecialchars($Namen[$zaehl]).":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td>\n";
              echo "<td class='content'>\n<input type=text name=\"Attribut[".$attr_cnt."]\" size='".$laenge_array[$zaehl]."' maxlength='".$max_array[$zaehl]."' value=\"".htmlspecialchars($daten_array[$zaehl])."\"></td>\n";
              echo "</tr>\n";
              $attr_cnt++;
          } //

          // Bemerkungen-Textarea ausgeben
          if ($zaehl == 17){
              echo "<tr class='content'>\n";
              // Feldbezeichnungsnamen ausgeben
              $einsetzen = rawurlencode($Namen[$zaehl]);
              $einsetzen_1 = str_replace(".", "%2E", $einsetzen);
              echo "  <td class='content' valign=top>\n".htmlspecialchars($Namen[$zaehl]).":";
              if($pruefen[$zaehl] == 'Y'){echo "*";};
              echo "</td>\n";
              echo "<td class='content' style='font-family: Courier, Courier New, Monaco'>\n";
              echo "<textarea style='font-family: Courier, Courier New, Monaco' name=\"".$einsetzen_1."\" cols='38' rows='5' wrap=physical>\n";
              echo htmlspecialchars($daten_array[$zaehl])."\n</textarea>\n</td>\n";
              echo "</tr>\n";
          }

      } // end of if $verwenden[$zaehl] == 'Y'
      // Attributzaehler fuer Zusatzattribute auch erhoehen, wenn 'verwenden' nicht 'Y'
      else {
          if ($zaehl > 12 && $zaehl < 17){
              $attr_cnt++;
          }
      }
  } // end of for $zaehl = 0; $zaehl <= ($anz_vordef-1); $zaehl++

  // fuer alle Zusatzattribute ermitteln, ob Sie zum Kundendatensatz oder zur Bestellung gespeichert werden
  // Information als Hidden-Feld weitergeben
  $attr_count = 1; // Attributzaehler

  for ($zaehl = 13; $zaehl <= 16; $zaehl++){
      if($verwenden[$zaehl] == 'Y'){
          if($speichern[$zaehl] == 'Y'){
              echo"<INPUT TYPE=hidden NAME=Attr_speichern[".$attr_count."] VALUE='Kunde'>\n";
          } // end of if speichern = Y
          else{
              echo"<INPUT TYPE=hidden NAME=Attr_speichern[".$attr_count."] VALUE='Bestellung'>\n";
          }
      $einsetzen = rawurlencode($Namen[$zaehl]);
      $einsetzen_1 = str_replace(".", "%2E", $einsetzen);
      echo"<INPUT TYPE=hidden NAME=Attr_name[".$attr_count."] id=\"Attribut".$attr_count."\" VALUE=\"".$einsetzen_1."\">\n";
      } // end of if verwenden = Y
      else{
      echo"<INPUT TYPE=hidden NAME=Attr_name[".$attr_count."] id=\"Attribut".$attr_count."\" VALUE=''>\n";
      }
      // Attributzaehler erhoehen
      $attr_count++;
  } // end of for
  echo "    </TR>\n<TR class='content'>\n";
  echo "    <TD class='content' colspan='2'><HR></TD>\n";
  echo "    </TR>\n<TR class='content'>\n";
  // Versandkosten berechnen und in die Bestellung einfuegen
  berechneVersandkosten(session_id());

  // Bestellungsobjekt instanzieren
  $myBestellung = getBestellung(session_id());

  // Rechnungsdaten aus Bestellungsobjekt in Variablen abfuellen
  $Rechnungstotal = $myBestellung->Rechnungsbetrag;
  $Mindermengenzuschlag = $myBestellung->Mindermengenzuschlag;
  $Versandkosten = $myBestellung->Versandkosten;
  if ($myBestellung->Bezahlungsart == "Nachnahme") {
      $Nachnahmebetrag =$myBestellung->Nachnahmebetrag;
  }
  // Falls es sich NICHT um eine kostenlose Bestellung handelt:
  if ($Rechnungstotal - ($Mindermengenzuschlag + $Versandkosten + $Nachnahmebetrag) > 0.0) {
      echo "<TD class='content' VALIGN=top>Zahlungsart:</TD>";
      echo "<TD class='content' VALIGN=top>";
      // Dem Shop-Kunden duerfen nur die in den Shop-Settings erlaubten Zahlungsmethoden
      // zur Verfuegung gestellt werden (Funktion in USER_ARTIKEL_HANDLING.php definiert)
      $Bezahlungsarray = array();
      $Bezahlungsarray = getBezahlungsart();
      // Die vom Kunden gewaehlte Bezahlungsart wird in seiner Bestellung gespeichert, falls er den Zurueck-
      // Button anklickt, kann die vorher geawaehlte Bezahlungsart wieder erkannt werden, indem man die Bezahlungs-
      // art aus seinem Bestellobjekt ausliest.
      $Bezahlungsart = $myBestellung->Bezahlungsart;

      // -------------------------------------------
      // Darstellung der Zahlungsarten zur Auswahl
      // -------------------------------------------
      $defaultmarkieren = ""; //defaultmarkieren waehlt eine Bezahlungsart vor (damit sicher eine gewaehlt ist)
      if ($Bezahlungsart == "") {
          if ($Bezahlungsarray["Vorauskasse"] == 'Y') {
              $defaultmarkieren = "Vorauskasse";
          }
          elseif(($defaultmarkieren == "") && ($Bezahlungsarray["billBOX"] == 'Y')) {
              $defaultmarkieren = "billBOX";
          }
          elseif(($defaultmarkieren == "") && ($Bezahlungsarray["Treuhandzahlung"] == 'Y')) {
              $defaultmarkieren = "Treuhandzahlung";
          }
          elseif(($defaultmarkieren == "") && ($Bezahlungsarray["Rechnung"] == 'Y')) {
              $defaultmarkieren = "Rechnung";
          }
          elseif(($defaultmarkieren == "") && ($Bezahlungsarray["Lastschrift"] == 'Y')) {
              $defaultmarkieren = "Lastschrift";
          }
          elseif(($defaultmarkieren == "") && ($Bezahlungsarray["Nachnahme"] == 'Y')) {
              $defaultmarkieren = "Nachnahme";
          }
          elseif(($defaultmarkieren == "") && ($Bezahlungsarray["Postcard"] == 'Y')) {
              $defaultmarkieren = "Postcard";
          }
          else {
              $defaultmarkieren = "Kreditkarte";
          }
      }
      // Moeglichkeit, per Vorauskasse zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet
      if ($Bezahlungsarray["Vorauskasse"] == 'Y') {
        echo "<INPUT TYPE=radio NAME='Bezahlungsart' id=\"Vorauskasse\"";
        if (($Bezahlungsart == "Vorauskasse") || ($defaultmarkieren == "Vorauskasse")) {echo "checked ";}
        echo " value='Vorauskasse'><span onClick='document.getElementById(\"Vorauskasse\").checked = true;'>Vorauskasse</span><br>\n";
      }

      // Moeglichkeit, per billBOX zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet. Der Schriftzug
      // billBOX ist mit einem Link hinterlegt, der direkt auf eine Infoseite von billBOX zeigt.
      if ($Bezahlungsarray["billBOX"] == 'Y') {
        echo "<INPUT TYPE=radio NAME='Bezahlungsart' id=\"billBOX\"";
        if (($Bezahlungsart == "billBOX") || ($defaultmarkieren == "billBOX")) {echo "checked ";}
        // Cascading Style Sheet Angaben fuer den Link (Weitere Informationen) auslesen und in Variable abpacken:
        $cssargumente = ' style="text-decoration:underline;
        color:'.getcssarg("main_font_c").'; font-style:'.getcssarg("main_font_i").'; font-size:'.getcssarg("main_font_s").';
        font-weight:'.getcssarg("main_font_w").'"';
        echo " value='billBOX'><a class='content' ".$cssargumente." href=\"http://www.billbox.ch/lvs.phtml\" target=\"_blank\">billBOX</a><br>\n";
      }

      // Moeglichkeit, per Treuhandzahlung zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet.
      if ($Bezahlungsarray["Treuhandzahlung"] == 'Y') {
        echo "<INPUT TYPE=radio NAME='Bezahlungsart' id=\"Treuhandzahlung\"";
        if (($Bezahlungsart == "Treuhandzahlung") || ($defaultmarkieren == "Treuhandzahlung")) {echo "checked ";}
        echo " value='Treuhandzahlung'><span onClick='document.getElementById(\"Treuhandzahlung\").checked = true;'>Zahlung &uuml;ber Treuh&auml;nder</span><br>\n";
      }

      // Moeglichkeit, mit Rechnung zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet
      if ($Bezahlungsarray["Rechnung"] == 'Y') {
        echo "<INPUT TYPE=radio NAME='Bezahlungsart' id=\"Rechnung\"";
        if (($Bezahlungsart == "Rechnung") || ($defaultmarkieren == "Rechnung")) {echo "checked ";}
        echo " value='Rechnung'><span onClick='document.getElementById(\"Rechnung\").checked = true;'>Rechnung</span><br>\n";
      }

      // Moeglichkeit, mit Nachnahme zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet
      if ($Bezahlungsarray["Nachnahme"] == 'Y') {
        echo "<INPUT TYPE=radio NAME='Bezahlungsart' id=\"Nachnahme\"";
        if (($Bezahlungsart == "Nachnahme") || ($defaultmarkieren == "Nachnahme")) {echo "checked ";}
        echo " value='Nachnahme'><span onClick='document.getElementById(\"Nachnahme\").checked = true;'>Nachnahme</span><br>\n";
      }

      // Moeglichkeit, mit Lastschrift zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet
      if ($Bezahlungsarray["Lastschrift"] == 'Y') {
        echo "<INPUT TYPE=radio NAME='Bezahlungsart' id=\"Lastschrift\"";
        if (($Bezahlungsart == "Lastschrift") || ($defaultmarkieren == "Lastschrift")) {echo "checked ";}
        echo " value='Lastschrift'><span onClick='document.getElementById(\"Lastschrift\").checked = true;'>Lastschrift</span><br>\n";
      }

      // Moeglichkeit, mit Postcard (PostFinance yellowpay) zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet
      if ($Bezahlungsarray["Postcard"] == 'Y') {
        echo "<INPUT TYPE=radio NAME='Bezahlungsart' id=\"Postcard\"";
        if (($Bezahlungsart == "Postcard") || ($defaultmarkieren == "Postcard")) {echo "checked ";}
        echo " value='Postcard'><span onClick='document.getElementById(\"Postcard\").checked = true;'>PostFinance Debit Direct (Postcard)</span><br>\n";
      }

      // Moeglichkeit, mit Kreditkate zu bezahlen anzeigen, wenn in Shopsettings freigeschaltet. Der Ausdruck
      // Kred_Post ist historisch entstanden. Heute steht Kred_Post NUR noch fuer Kreditkarten.
      if ($Bezahlungsarray["Kred_Post"] == 'Y'){
        echo "<INPUT id=\"Kred_Post\" TYPE=radio NAME='Bezahlungsart'";
        if (($Bezahlungsart == "Kreditkarte") || ($defaultmarkieren == "Kreditkarte")) {echo "checked ";}
        echo " value='Kreditkarte'><span onClick='document.getElementById(\"Kred_Post\").checked = true;'>Kreditkarte</span>\n";
        // Kreditkarten-Objekte auslesen
        $meineKreditkarten = getKreditkarten();
        // Feststellen ob es mehr als eine aktive (zu verwendende) Kreditkarte gibt
        $kred_counter = 0; // Wird inkementiert, sobald eine aktive Kreditkarte gefunden wurde
        $aktive_karte = 0; // Hier wird die Kreditkarten_ID der zuletzt als aktiv markierte Kreditkarte gespeichert
        foreach ($meineKreditkarten as $value) {
            if ($value->benutzen == 'Y') {
                $kred_counter ++;
                $aktive_karte = $value->Kreditkarten_ID;
            }
        }
        if ($kred_counter == 1) {
            echo "<input type=\"hidden\" name=\"Kreditkarten_ID\" value=\"$aktive_karte\">";
        }
        else {
            // Darstellen der aktiven Kreditkarten in einem Pulldown-Menu
            // Dabei wird die vom Kunde gewaehlte Kreditkarte angewaehlt (Falls er das gemacht hat)
            echo "\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kreditkarten Institut: &nbsp;\n";
            echo "<select name='Kreditkarten_ID' size='1' onClick='document.getElementById(\"Kred_Post\").checked = true;'>\n";
            foreach ($meineKreditkarten as $key=>$val) {
                // Nur aktive Kreditkarten anzeigen
                if ($val->benutzen == 'Y') {
                    echo "<option value='".$val->Kreditkarten_ID."'";
                    if ($myBestellung->Kreditkarten_Hersteller == $val->Hersteller) {
                        echo "selected";
                    }
                    echo ">".$val->Hersteller."</option>\n";
                }
            }
            echo "</select><br>\n";
        }// else $kred_counter == 1

      }
      echo "</TD>";

  } // end of if rechnungstotal > 0

  else {
      $Bezahlungsart = "kostenlos";
      echo "<TD class='content' VALIGN=top>Zahlungsart:</TD>";
      echo "<TD class='content' VALIGN=top><b>kostenlose Bestellung</b>";
      echo "<INPUT TYPE=hidden NAME=Bezahlungsart VALUE=$Bezahlungsart></TD>";
  } // end of else

  // Formular abschliessen
?>
      </TR>
    </TABLE>
    </center>
    <INPUT TYPE=hidden NAME=darstellen VALUE=2>
    <INPUT TYPE=hidden NAME=Versandkosten VALUE=<?php echo "$Versandkosten"; ?>>
    <INPUT TYPE=hidden NAME=Mindermengenzuschlag VALUE=<?php echo "$Mindermengenzuschlag"; ?>>
    <INPUT TYPE=hidden NAME=Rechnungstotal VALUE=<?php echo "$Rechnungstotal"; ?>>
    <INPUT TYPE=hidden NAME=Datum VALUE=<?php echo "$Datum"; ?>><BR>
    <INPUT TYPE=hidden NAME=Kunden_ID VALUE=<?php echo "$Kunden_ID"; ?>><BR>
    <input type=hidden name=darstellen value=2>
    <CENTER><INPUT TYPE="image" src='Buttons/bt_weiter.gif' border="0" alt="Weiter" title="Weiter"></CENTER>
    </FORM>
<?php

} // ende von darstellen = 11

// -----------------------------------------------------------------------
// - Update der Kunden- und Bestellungsdaten.
// - Ausgabe der Bestellung zur Kontrolle fuer den Kunden (an den Browser).
// - Weiche fuer Bezahlungsarten. Je nachdem ob man entweder per Voraus-
//   kasse, Rechnung, Nachnahme oder per Kreditkarte bezahlt und weiter noch
//   je nachdem ob die Kreditkartenzahlung intern oder extern abgewickelt wird.
// -----------------------------------------------------------------------
else if ($darstellen == 2) {

  // frei konfigurierbare Zusatzattribute in einen Array abfuellen
  for ($zaehl = 1; $zaehl <= count($Attr_name); $zaehl++){
      if ($Attr_speichern[$zaehl] == "Kunde"){
          $attr_namen[$zaehl] = rawurldecode($Attr_name[$zaehl]);
          $attr_wert[$zaehl] = $Attribut[$zaehl];
      } // end of if
      else{
          $attr_wert[$zaehl] = "";
      } // end of else
  } // end of for

  $email = $HTTP_POST_VARS['E-Mail'];
  $telefon = $HTTP_POST_VARS['Tel%2E'];

  //Kundenattribute speichern (Bestellungsfelder werden weiter unten upgedated)
  updKundenFelder(session_id(), $Anrede, $Vorname, $Nachname, $Firma, $Abteilung, $Strasse,
           $Postfach, $PLZ, $Ort, $Land, $telefon, $Fax, $email, $attr_namen[1], $attr_namen[2],
           $attr_namen[3], $attr_namen[4], $attr_wert[1], $attr_wert[2], $attr_wert[3], $attr_wert[4], $Kunden_ID);

  // Attributobjekt aus Datenbank holen
  $myAttribut = getAttributobjekt();

  // Anzahl Zusatzfelder
  $gesamt = $myAttribut->attributanzahl();
  $anz_zusatz = ($gesamt-$anz_vordef);

  // Die verschiedenen Werte und Einstellungen in Arrays abfuellen
  $Namen = $myAttribut->getallName();
  $verwenden = $myAttribut->getallanzeigen();

  // einen Array erstellen, der alle Feldinhalte enthaelt
  $adresse = array($Anrede, $Vorname, $Nachname, $Firma, $Abteilung, $Strasse,
           $Postfach, $PLZ, $Ort, $Land, $telefon, $Fax, $email, $Attribut[1], $Attribut[2], $Attribut[3], $Attribut[4], $Bemerkungen);


  // einen Array machen, der alle Namen der Felder enthaehlt, die verwendet werden, sowie einen
  // Array, der dessen Inhalte enthaelt
  $anzahl_total = count($Namen);
  for ($zaehl = 0; $zaehl <= $anzahl_total-1; $zaehl++){
      if ($verwenden[$zaehl] == 'Y'){
          // Array fuer die verwendeten Feldnamen
          $adresse_feld[] = stripslashes($Namen[$zaehl]);
          // Array fuer die Feldinhalte
          $adresse_inhalt[] = stripslashes($adresse[$zaehl]);
      } // end of if
  } // end of for

  echo "<h3 class='content'>Ihre Bestellung:</h3><br>\n";
  echo "<table class='content' border='0' cellpadding='0' cellspacing='0'>\n";

  $anzahl_verwendet = count($adresse_feld);

  //  schauen, ob das letzte Feld "Bemerkungen" ist
  if ($adresse_feld[$anzahl_verwendet-1] == "Bemerkungen"){
      $bemerk = true;
      $wie_weit = $anzahl_verwendet-2;
  } // end of if
  else{
      $bemerk = false;
      $wie_weit = $anzahl_verwendet-1;
  } // end of else

  // Message-String fuer E-Mail
  $message_string_adr.="Lieferadresse:\n--------------";

  for ($zaehl = 0; $zaehl <= $wie_weit; $zaehl++){

      // Angaben, die in der linken Tabellenhaelfte angezeigt werden
      if ($zaehl % 2 == 0) {
          echo "<tr><td class='content'><b class='content' style='font-weight:bold'>".$adresse_feld[$zaehl].":&nbsp;&nbsp;</b></td>";
          echo "<td class='content'>".$adresse_inhalt[$zaehl]."</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
      } // end of if

      // Angaben, die in der rechten Tabellenhaelfte angezeigt werden
      else {
          echo "<td class='content'><b class='content' style='font-weight:bold'>".$adresse_feld[$zaehl].":&nbsp;&nbsp;</b></td>";
          echo "<td class='content'>".$adresse_inhalt[$zaehl]."</td></tr>";
      } // end of else

      // Adressstring für E-Mail um aktuelles Feld ergaenzen
      $message_string_adr.="\n".$adresse_feld[$zaehl].": ".$adresse_inhalt[$zaehl];

  } // end of for

  // falls erst eine halbe Zeile ausgegeben wurde
  if (($wie_weit) % 2 == 0){
      echo "<td colspan=2>&nbsp;</td>";
  } // end of if

  // falls existiert, Bemerkungsfeld ausgeben
  if ($bemerk == true){
      echo "<tr><td colspan=5>&nbsp;</td></tr>";
      echo "<tr><td class='content'><b class='content' style='font-weight:bold'>".$adresse_feld[$anzahl_verwendet-1].":&nbsp;&nbsp;</b></td>";
      echo "<td class='content' colspan=4>".$adresse_inhalt[$zaehl]."</td></tr>";

      // Adressstring für E-Mail um aktuelles Feld ergaenzen
      $message_string_adr.="\n".$adresse_feld[$zaehl].": ".$adresse_inhalt[$zaehl];
  } // end of if

  // Zahlungsart ausgeben
  if ($Bezahlungsart != ""){
      echo "<tr><td class='content'><b class='content' style='font-weight:bold'>Zahlungsart:&nbsp;&nbsp;</b></td>";
      echo "<td class='content' colspan=4>".$Bezahlungsart."</td></tr>";
  } // end of if

  echo "</table>\n";
  echo "<br><br>\n";

  // Bestellungsobjekt holen
  $myBestellung = getBestellung(session_id());

  // Bestellungsattribute aufbereiten (Bestellung ergaenzen) und speichern:
  // Nur Kreditkartendaten werden an dieser Stelle noch nicht gespeichert
  // Aktuelles Datum berechnen (wird dem Mailheader angehaengt)
  $mydate = getdate();
  $Datum = $mydate[year]."-".$mydate[mon]."-".$mydate[mday];// Format yyyy-mm-dd
  $myBestellung->Datum = $Datum;
  $myBestellung->Anmerkung = trim($Bemerkungen);
  $myBestellung->Bezahlungsart = $Bezahlungsart;

  // frei konfigurierbare Zusatzattribute in einen Array abfuellen
  for ($zaehl = 1; $zaehl <= count($Attr_name); $zaehl++){
      if ($Attr_speichern[$zaehl] == "Bestellung"){
          $attr_wert[$zaehl] = $Attribut[$zaehl];
      } // end of if
      else{
          $attr_wert[$zaehl] = "";
          $attr_namen[$zaehl] = "";
      } // end of else
  } // end of for

  /* -- Wird vermutlich in einer der naechsten Versionen geloescht --
  // Uebergebene Kreditkarten_ID auswerten und ausgewaehlte Kreditkarte in die
  // Bestellung speichern (TEMPORAER, da man sonst ev. gegen den Mail-Order (E-Commerce) Vertrag
  // mit dem jeweiligen Kreditkarteninstitut verstoesst)
  $meineKreditkarten = getKreditkarten();
  foreach ($meineKreditkarten as $key=>$val) {
      $myBestellung->Kreditkarten_Hersteller = $val->Hersteller;
      $myBestellung->Kreditkarten_Nummer = $val->;
      $myBestellung->Kreditkarten_Ablaufdatum = $;
      $myBestellung->Kreditkarten_Vorname = $;
      $myBestellung->Kreditkarten_Nachname = $;
  }
  */

  // Falls es sich um eine Zahlung via PostFinance yellowpay handelt, muss dies in
  // der Datenbank festgehalten werden. Ebenso bei einer Saferpay / B+S Card Service Zahlung
  // (zwecks Auswertung in darstellen == 4)
  if ($Bezahlungsart == "Postcard") {
      // PostFinance Debit Direct Zahlung (Postcard)
      $myBestellung->clearing_id = "postfinance";
  }
  else if ($Bezahlungsart == "Kreditkarte") {
      $meineKreditkarten = getKreditkarten();
      foreach ($meineKreditkarten as $key=>$value) {
          if ($value->Kreditkarten_ID == $Kreditkarten_ID) {
              $kredhandlingtest = $value->Handling;
              break;
          }
      }// End for
      if ($kredhandlingtest == "postfinance") {
          // PostFinance Kreditkartenzahlung
          $myBestellung->clearing_id = "postfinance";
      }
      else if ($kredhandlingtest == "saferpay") {
          // Saferpay / B+S Card Service Kreditkartenzahlung
          $myBestellung->clearing_id = "saferpay";
      }

  }
  else {
      $myBestellung->clearing_id = "";
  }

  $myBestellung->Attribut1 = $attr_namen[1];
  $myBestellung->Attribut2 = $attr_namen[2];
  $myBestellung->Attribut3 = $attr_namen[3];
  $myBestellung->Attribut4 = $attr_namen[4];
  $myBestellung->Attributwert1 = $attr_wert[1];
  $myBestellung->Attributwert2 = $attr_wert[2];
  $myBestellung->Attributwert3 = $attr_wert[3];
  $myBestellung->Attributwert4 = $attr_wert[4];

  // Bestellungsfelder updaten
  updBestellungsFelder(session_id(), $myBestellung);

  // Warenkorb darstellen, ohne die Moeglichkeit Artikel wieder daraus entfernen zu koennen! (und nicht fuer Admin)
  darstellenBestellung($myBestellung, false, false);

  // ----------------------------------------------------------------
  // Weichenstellung fuer weiteren Programmablauf je nach Zahlungsart
  // ----------------------------------------------------------------
  // Weiche 1: Entscheidung, ob es sich um eine Kreditkartenzahlung oder eine andere Zahlung handelt:
  if ($Bezahlungsart == "Kreditkarte") {
      // Es handelt sich also um eine Kreditkarenzahlung. Wir lesen nun unsere Kreditkareneinstellungen aus und
      // suchen die vom Kunden gewaehlte Kreditkarte aus. Dort lesen wir deren Handling aus und stellen danach die
      // zweite Weiche entsprechend
      $meineKreditkarten = getKreditkarten();
      foreach ($meineKreditkarten as $key=>$value) {
          if ($value->Kreditkarten_ID == $Kreditkarten_ID) {
              $Kreditkarten_Hersteller = $value->Hersteller;
              $Kreditkarten_Handling = $value->Handling;
              break;
          }
      }
      // Weiche 1.2: Entscheidung, ob es sich um eine interne oder externe Kreditkartenzahlung handelt:
      if ($Kreditkarten_Handling == "intern") {
          $darstellen_var = 5;   // intern, Kreditkarte
      }
      else if ($Kreditkarten_Handling == "saferpay") {
          $darstellen_var = 15;  // extern, Saferpay / B+S Card Service Zahlung (externe Zahlung -> siehe saferpay_init.php)
      }
      else if ($Kreditkarten_Handling == "postfinance") {
          $darstellen_var = 16;  // extern, Postfinance Yellowpay Kreditkarten Zahlung (Postcard und / oder Kreditkarte(n))
      }
      else {
          $darstellen_var = 6;   // extern, Kreditkarte
      }
  }
  // Weiche 2: billBOX-Zahlungsmethode
  elseif ($Bezahlungsart == "billBOX") {
      $darstellen_var = 8;       // extern, billBOX
  }
  // Weiche 3: Lastschriftzahlung
  else if ($Bezahlungsart == "Postcard") {
      $darstellen_var = 16;       // extern, Postcard von Postfinance yellowpay
      $special = "Postcard_only"; // Spez. Steueranweisung -> Postcard voranwaehlen
  }
  // Weiche 4: Lastschriftzahlung
  else if ($Bezahlungsart == "Lastschrift") {
      $darstellen_var = 12;      // intern, Lastschrift
  }
  // Weiche 5: Rest der Zahlungen (direkt zur E-Mail-Generierung)
  else {
      $darstellen_var = 3;       //intern, Vorauskasse, Rechnung, Nachnahme, Treuhandzahlung
  }

  // Hidden-Formular, damit wir die Variablen per Post weitergeben koennen
?>
  <form action="<?php echo $PHP_SELF; ?>" name="Formular" method="POST">
    <input type=hidden name=darstellen value=<?php echo $darstellen_var; ?>>
    <input type=hidden name=special value=<?php echo $special; ?>>
    <input type=hidden name=message_string_adr value="<?php echo urlencode($message_string_adr); ?>">
    <input type=hidden name=email value="<?php echo urlencode($email); ?>">
    <input type=hidden name=Bezahlungsart value="<?php echo urlencode($Bezahlungsart); ?>">
    <input type=hidden name=Vorname value="<?php echo urlencode($Vorname); ?>">
    <input type=hidden name=Nachname value="<?php echo urlencode($Nachname); ?>">
    <input type=hidden name=Kreditkarten_Hersteller value="<?php echo urlencode($Kreditkarten_Hersteller); ?>">
    <br>
    <center>
      <a href="<?php echo $PHP_SELF; ?>?darstellen=1"><img src='Buttons/bt_zurueck.gif' border="0" alt="Zur&uuml;ck" title="Zur&uuml;ck"></a>&nbsp;
      <input type="image" src='Buttons/bt_weiter.gif' border="0" alt="Weiter" title="Weiter">
    </center>
  </form>
<?php

}// End darstellen = 2


// -----------------------------------------------------------------------
// Weiterer Zahlungsverlauf bei 'normalen' Bezahlungsarten:
// Allgemeine Geschaeftsbedingungen aus Datenbank auslesen und Darstellen.
// Bestellung wird erst abgesendet, wenn Shopbenutzer die AGB's akzeptiert
// hat.
// -----------------------------------------------------------------------
else if ( $darstellen == 3 ){
?>
  <center>
  <table border=0 cellspacing=0 cellpadding=0 class=content width='80%'>
    <tr class=content>
      <td class=content>
<?php
  // AGB ausgeben
  echo "<h3 class='content'>Allgemeine Gesch&auml;ftsbedingungen</h3>";
  echo getAGB();

  // Hidden-Formular, damit wir die Variablen per Post weitergeben koennen
?>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
        <form action="<?php echo $PHP_SELF; ?>" name="Formular" method="POST">
          <input type=hidden name=darstellen value=4>
          <input type=hidden name=message_string_adr value="<?php echo $message_string_adr; ?>">
          <input type=hidden name=email value="<?php echo $email; ?>">
          <input type=hidden name=Erfolg value="1">
          <input type=hidden name=Zahlungsart value="<?php echo $Bezahlungsart; ?>">
          <input type=radio name=agb value=ja>&nbsp;Ja, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen und will bestellen<BR>
          <input type=radio name=agb value=nein checked>&nbsp;Nein, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen <B style='font-weight:bold'>nicht</B><BR>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
          <center><input type="image" src='Buttons/bt_bestellung_absenden.gif' border="0" alt="Bestellung absenden" title="Bestellung absenden"></center>
        </form>
      </td>
    </tr>
  </table>
  </center>
<?php

} // end of darstellen = 3

// -----------------------------------------------------------------------
// Lastschriftzahlung
// - Bankdaten Eingabemaske und Weiterleitung an darstellen == 3
// -----------------------------------------------------------------------
else if ( $darstellen == 12){

    // Auslesen der Kunden-Bankdaten:
    $bankdaten = array(); // Initialisierung
    $bankdaten = get_kunden_bankdaten(session_id()); // Auslesen der Kundenbankdaten

    // Wenn keine Kontoinhaber-Angaben vorhanden sind, den Namen des Kunden einsetzen
    if ($bankdaten["kontoinhaber"] == "") {
        $bankdaten["kontoinhaber"] = urldecode($Vorname)." ".urldecode($Nachname);
    }
    // Wenn der Kunde eh nur temporaer vorhanden ist soll er seine Bankdaten gar nicht erst persistent speichern koennen.
    // Weiter unten wird deshalb zusaetzlich auch noch die Checkbox fuer diese Einstellung ausgeblendet.
    if (is_tempKunde(session_id())) {
        $bankdaten["bankdaten_speichern"] == "N";
    }
?>

  <script language="JavaScript" type="text/javascript">
  <!--
  function chkFormular() {
    // ueberpruefen, ob die eingegebenen Werte ok sind:
      if(document.Formular.kontoinhaber.value == "") {
          alert("Bitte den Namen des Kontoinhabers angeben!");
          document.Formular.kontoinhaber.focus();
          return false;
      }
      if(document.Formular.bankname.value == "") {
          alert("Bitte den Namen der Bank angeben! Schweizer Postkunden, bitte Post schreiben.");
          document.Formular.bankname.focus();
          return false;
      }
      if(document.Formular.blz.value == "") {
          alert("Bitte die Bankleitzahl (BLZ) angeben! Schweizer Postkunden hier bitte Post schreiben.");
          document.Formular.blz.focus();
          return false;
      }
      if(document.Formular.kontonummer.value == "") {
          alert("Bitte die Kontonummer angeben!");
          document.Formular.kontonummer.focus();
          return false;
      }
  } // end of fuction chkFormular
  //-->
  </script>

    <center>
    <form action="<?php echo $PHP_SELF; ?>" name="Formular" method="POST" onSubmit="return chkFormular()">
    <table class='content' border='0' cellpadding='0' cellspacing='0' class='content'>
      <tr class='content'>
        <td class='content'>
          <h3 class='content'>Lastschriftzahlung: Bankdatenerfassung</h3><br>
          Bitte Ihre Bankverbindung angeben:<br><br><br>
        </td>
      </tr>
      <tr class='content'>
        <td class='content'>

    <table class='content' border='0' cellpadding='0' cellspacing='0' class='content'>
      <tr class='content'>
        <td class='content'>
            Name des Kontoinhabers:&nbsp;
        </td>
        <td class='content'>
            <input type=text size=32 name="kontoinhaber" value="<?php echo $bankdaten["kontoinhaber"]; ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr class='content'>
        <td class='content'>
            Name der Bank:
        </td>
        <td class='content'>
            <input type=text size=32 name="bankname" value="<?php echo $bankdaten["bankname"]; ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr class='content'>
        <td class='content'>
            Bankleitzahl (BLZ):
        </td>
        <td class='content'>
            <input type=text size=32 name="blz" value="<?php echo $bankdaten["blz"]; ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr class='content'>
        <td class='content'>
            Kontonummer:
        </td>
        <td class='content'>
            <input type=text size=32 name="kontonummer" value="<?php echo $bankdaten["kontonummer"]; ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
<?php if ($bankdaten["temp"] == "N") { echo "<input type=hidden name='bankdaten_speichern' value='N'>"; // nur anzeigen, wenn Kunde NICHT temporaer ist! ?>
      <tr class='content'>
        <td class='content'>
            Eingaben speichern:
        </td>
        <td class='content'>
          <input type="checkbox" name="bankdaten_speichern" <?php if ($bankdaten["bankdaten_speichern"] == "Y") {echo "checked";}?>>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
<?php } // Ende: Nur anzeigen wenn Kunde NICHT temporaer ist!
?>
        <td class='content' align='right'>
            <BR>
            <a href="<?php echo $PHP_SELF; ?>?darstellen=1"><img src='Buttons/bt_zurueck.gif' border="0" alt="Zur&uuml;ck" title="Zur&uuml;ck"></a>
        </td>
        <td class='content'>
            <BR>
            <input type=hidden name='Kreditkarten_Hersteller' value='<?php echo $Kreditkarten_Hersteller; ?>'>
            <input type=hidden name='Erfolg' value='1'>
            <input type=hidden name='GNUPG' value='1'>
            <input type=hidden name='darstellen' value='13'>
            <input type=hidden name=message_string_adr value="<?php echo $message_string_adr; ?>">
            <input type=hidden name=email value="<?php echo $email; ?>">
            <input type=hidden name=Zahlungsart value="<?php echo $Bezahlungsart; ?>">
            &nbsp;<input type="image" src='Buttons/bt_weiter.gif' border="0" alt="Weiter" title="Weiter">
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>

        </td>
      </tr>
    </table>
    </form>
<?php
} // end of darstellen = 12

// -----------------------------------------------------------------------
// Lastschriftzahlung, Teil 2
// Updaten der Kundendaten (in DB) und anzeigen der AGB
// -----------------------------------------------------------------------
else if ($darstellen == 13) {

    // Bankdaten des Kunden updaten
    if ($bankdaten_speichern == "on") {
        $bankdaten_speichern = "Y";
    }
    else {
        $bankdaten_speichern = "N";
    }
    // Die Bankdaten werden hier auf JEDEN FALL gespeichert. Erst in darstellen = 4 wird der Kundenwunsch die Daten nicht dauerhaft
    // zu behalten erfuellt. (Letzter Parameter ist dann dort = true)
    set_kunden_bankdaten(session_id(),$kontoinhaber,$bankname,$blz,$kontonummer,$bankdaten_speichern, false);
?>
  <script language="JavaScript" type="text/javascript">
  <!--
  function chkFormular() {
    // ueberpruefen, ob die AGB's akzeptiert wurden
    if(document.Formular.agb[1].checked) {
        alert("Sie müssen die AGB's akzeptieren, um bestellen zu können!");
        document.Formular.agb[1].focus();
        return false;
    } // end of if
    else {
        return true;
    }// end of else
  } // end of fuction chkFormular
  //-->
  </script>

  <center>
  <form action="<?php echo $PHP_SELF; ?>" method="POST" name="Formular" onSubmit="return chkFormular()">
  <table border=0 cellspacing=0 cellpadding=0 class=content width='80%'>
    <tr class=content>
      <td class=content>
<?php
  // AGB ausgeben
  echo "<h3 class='content'>Allgemeine Gesch&auml;ftsbedingungen</h3>";
  echo getAGB();

  // Hidden-Formular, damit wir die Variablen per Post weitergeben koennen
?>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
          <input type=radio name="agb" value="ja">&nbsp;Ja, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen und will bestellen<BR>
          <input type=radio name="agb" value="nein" checked>&nbsp;Nein, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen <B style='font-weight:bold'>nicht</B><BR>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
            <input type=hidden name='Erfolg' value='1'>
            <input type=hidden name='darstellen' value='4'>
            <input type=hidden name=message_string_adr value="<?php echo $message_string_adr; ?>">
            <input type=hidden name=email value="<?php echo $email; ?>">
            <input type=hidden name=Zahlungsart value="<?php echo $Bezahlungsart; ?>">
            <center><input type="image" src='Buttons/bt_bestellung_absenden.gif' border="0" alt="Bestellung absenden" title="Bestellung absenden"></center>
      </td>
    </tr>
  </table>
  </form>
  </center>
<?php

}// End darstellen == 13

// -----------------------------------------------------------------------
// Kreditkartenhandling *** INTERN ***
// - Kreditkartendaten Eingabemaske und Weiterleitung an darstellen == 3
// (weiter gehts im Ablauf bei darstellen == 7)
// -----------------------------------------------------------------------
else if ( $darstellen == 5){
?>
    <center>
    <form action="<?php echo $PHP_SELF; ?>" name="Formular" method="POST">
    <table class='content' border='0' cellpadding='0' cellspacing='0' class='content'>
      <tr class='content'>
        <td class='content'>
          <h3 class='content'>Kreditkarten Datenerfassung</h3><br>
          <LI>Ihre Kreditkartendaten werden nicht dauerhaft gespeichert.</LI>
          <LI>Wenn Sie mit der Kreditkarte einer anderen Person zahlen wollen, so geben Sie bitte den entsprechenden Vor- und Nachnamen an.</LI>
        </td>
      </tr>
      <tr class='content'>
        <td class='content'>
    <table class='content' border='0' cellpadding='0' cellspacing='0' class='content'>
      <tr class='content'>
        <td class='content'>
            <BR>Kreditkarten Institut:
        </td>
        <td class='content'>
            <BR><b class='content' style='font-weight:bold'><?php echo urldecode($Kreditkarten_Hersteller); ?></b>
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr class='content'>
        <td class='content'>
            Kreditkartennummer:
        </td>
        <td class='content'>
            <input type=text size=32 name=Kreditkarten_Nummer value="<?php echo $Kreditkarten_Nummer; ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr class='content'>
        <td class='content'>
            Ablaufdatum der Karte:
        </td>
        <td class='content'>
            <input type=text size=2 name=Kreditkarten_Ablaufdatum_1 value="<?php echo $Kreditkarten_Ablaufdatum_1; ?>">&nbsp;/&nbsp;
            <input type=text size=2 name=Kreditkarten_Ablaufdatum_2 value="<?php echo $Kreditkarten_Ablaufdatum_2; ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr class='content'>
        <td class='content'>
            Vorname:
        </td>
        <td class='content'>
            <input type=text size=32 maxsize=128 name=Kreditkarten_Vorname value="<?php echo urldecode($Vorname); ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr class='content'>
        <td class='content'>
            Nachname:
        </td>
        <td class='content'>
            <input type=text size=32 maxsize=128 name=Kreditkarten_Nachname value="<?php echo urldecode($Nachname); ?>">
        </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td class='content' align='right'>
            <BR>
            <a href="<?php echo $PHP_SELF; ?>?darstellen=1"><img src="Buttons/bt_zurueck.gif" align="middle" border="0" alt="Zur&uuml;ck" title="Zur&uuml;ck"></a>
            &nbsp;
        </td>
        <td class='content'>
            <BR>
            <input type="hidden" name='Kreditkarten_Hersteller' value='<?php echo $Kreditkarten_Hersteller; ?>'>
            <input type="hidden" name='Erfolg' value='1'>
            <input type="hidden" name='GNUPG' value='1'>
            <input type="hidden" name='darstellen' value='7'>
            <input type="hidden" name="message_string_adr" value="<?php echo $message_string_adr; ?>">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <input type="hidden" name="Zahlungsart" value="<?php echo $Bezahlungsart; ?>">
            <input type="image"  src="Buttons/bt_weiter.gif" border="0" alt="Weiter" title="Weiter">
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
        </td>
      </tr>
    </table>
    </form>
<?php
} // end of darstellen = 5

  // -----------------------------------------------------------------------
  // Kreditkartenhandling *** EXTERN ***
  // Hier wird die Funktion payment_extern($Pay) aufgerufen. Ihr wird im Argument
  // eine Pay mitgegeben (siehe auch pay_def.php), darin befinden sich alle
  // noetigen Informationen betreffend
  // -----------------------------------------------------------------------
else if ( $darstellen == 6){
  // Damit wir dem Interface-Modul payment_interface ein Pay-Objekt mit allen
  // benoetigten Daten uebergeben koennen, muessen wir hier eines erzeugen und
  // die entsprechenden Daten abfuellen:
  // Ein neues Pay-Objekt instanzieren
  $myPay = new Pay;
  // Session_ID des aktuellen Kunden auslesen
  $Session_ID = session_id();
  // Die Funktion checkSession liefert uns hier die Kunden_ID
  $Kunden_ID = checkSession($Session_ID);
  // Kunde abfuellen
  $myPay->myKunde = getKunde($Kunden_ID);
  // Bestellung abfuellen
  $myPay->myBestellung = getBestellung($Session_ID);
  // Falls der Shop die Preise exkl. MwSt. angegeben hat, diese noch zur Be-
  // stellsumme addieren:
  // Auslesen der MwSt-Settings um feststellen zu koennen ob die Artikelpreise inkl. oder exkl. MwSt sind
  $aktuelle_mwst_settings = getmwstsettings();
  if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "N" && getmwstnr() > 0) {
      // Der Shop ist MwSt-pflichtig und die Preise sind exkl. MwSt angegeben. Nun muss der MwSt-Betrag
      // der Bestellung noch zum Rechnungsbetrag hinzuaddiert werden.
      $myPay->myBestellung->Rechnungsbetrag = $myPay->myBestellung->Rechnungsbetrag + $myPay->myBestellung->MwSt;
  }
  // Hier wird die Ruecksprung-URL angegeben. Diese URL beschreibt den Ort im
  // PhPepperShop, wo nach der externen Zahlungsabwicklung die Bestellung entweder
  // akzeptiert und abgeschlossen wird oder aber wo die Bestellung nach nicht erfolg-
  // reicher Zahlungsabwicklung der Kunde an eine weiterleidende Page gefuehrt wird.
  $myPay->myReferrer = "http://".$SERVER_NAME.$PHP_SELF."?darstellen=4";
  // Wir speichern den E-Mail-Message-String temporaer in der Kunden-Bestellung.
  // Dieser String wird zur Erstellung des E-Mails an Kunde und Shopbetreiber benoetigt.
  putEmailMessage(addslashes(urldecode($message_string_adr)),$Session_ID)
?>
  <script language="JavaScript" type="text/javascript">
  <!--
  function chkFormular() {
    // ueberpruefen, ob die AGB's akzeptiert wurden
    if(document.Formular.agb[1].checked) {
        alert("Sie müssen die AGB's akzeptieren, um bestellen zu können!");
        document.Formular.agb[1].focus();
        return false;
    } // end of if
    else {
        return true;
    }// end of else
  } // end of fuction chkFormular
  //-->
  </script>

  <center>
  <table border=0 cellspacing=0 cellpadding=0 class=content width='80%'>
    <tr class=content>
      <td class=content>
<?php
  // AGB ausgeben
  echo "<h3 class='content'>Allgemeine Gesch&auml;ftsbedingungen</h3>";
  echo getAGB();

  // Hidden-Formular, damit wir die Variablen per Post weitergeben koennen
?>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
<?php
          // Nun wird die Funktion payment_extern($myPay) aufgerufen. Sie enthaelt den Beginn
          // des folgenden Formulars (damit im action=''-Teil der Ziel URL konfiguriert werden
          // kann. Um die Ansteuerung der externen Zahlungsstelle konfigurieren zu koennen,
          // editieren Sie die Datei payment_interface.php in diesem Verzeichnis.
          if (!payment_extern($myPay)) {
              die("<h2>U_B_1_Error: darstellen == 6: Die Funktion payment_extern(\$myPay) wurde nicht korrekt beendet</h2>");
          }
?>
          <input type=hidden name=darstellen value=4>
          <input type=hidden name=message_string_adr value="<?php echo $message_string_adr; ?>">
          <input type=hidden name=email value="<?php echo $email; ?>">
          <input type=hidden name=Zahlungsart value="<?php echo $Bezahlungsart; ?>">
          <input type=radio name="agb" value="ja">&nbsp;Ja, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen und will bestellen<BR>
          <input type=radio name="agb" value="nein" checked>&nbsp;Nein, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen <B style='font-weight:bold'>nicht</B><BR>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
          <center><input type="image" src='Buttons/bt_bestellung_absenden.gif' border="0" alt="Bestellung absenden" title="Bestellung absenden"></center>
        </form>
      </td>
    </tr>
  </table>
  </center>
<?php
} // end of darstellen = 6

// -----------------------------------------------------------------------
// Kreditkartenhandling *** INTERN (Teil 2) ***
// Auswertung der Kreditkartendaten, welche bei darstellen = 5 (interne Kreditkartenverarbeitung) eingegeben wurden
// -----------------------------------------------------------------------
else if ($darstellen == 7) {

  // Wenn wir eine interne Kreditkartenzahlung haben, so wird uns die Variable GNUPG mit dem
  // Wert 1 von darstellen == 5 her uebergeben. Wir ueberpruefen also zuerst die Kreditkarten
  // Nummer, falls sie falsch ist, senden wir sie zurueck und der Kunde soll sie nochmals eingeben.
  // Zur Erkennung geben wir das Flag $Kreditkarten_Nummer_ungueltig = 1 mit.
  if ($GNUPG == 1) {
      //Kreditkarten Institutname fuer validateCC Funktion umbenennen
      if (urldecode($Kreditkarten_Hersteller) == "VISA") {
          $creditcard = "visa";
      }
      elseif (urldecode($Kreditkarten_Hersteller) == "Eurocard/Mastercard") {
          $creditcard = "mastercard";
      }
      elseif (urldecode($Kreditkarten_Hersteller) == "American Express") {
          $creditcard = "amex";
      }
      elseif (urldecode($Kreditkarten_Hersteller) == "Diners Club") {
          $creditcard = "discover";
      }
      else {
          $creditcard = "discover";
      }

      //VALIDATE CREDITCARD!
      $ok = validateCC($Kreditkarten_Nummer, $creditcard);
      $mydate = getdate();
      $Jahr = substr($mydate[year],2,2);
      $Monat = $mydate[mon];
      if ($Jahr > $Kreditkarten_Ablaufdatum_2) {
          $ok = 0;
      }
      elseif ($Jahr == $Kreditkarten_Ablaufdatum_2) {
          if ($Monat > $Kreditkarten_Ablaufdatum_1) {
              $ok = 0;
          }
      }

      if (($ok == 0) || ($ok == -1)) {
        echo "<center>";
        echo "<form action='".$PHP_SELF."' method='post' name='Formular'>\n";
        echo "<table class='content'>\n";
        echo "<tr class='content'>\n";
        echo "  <td class='content'>\n";
        echo "      <h3 class='content'>Fehler!</h3>";
        echo "      <h4>Ihre Kreditkartennummer ist ung&uuml;ltig, oder Ihre Kredikarte ist abgelaufen!</h4>\n";
        echo "      <LI>Mit Weiter k&ouml;nnen Sie die Eingaben nochmals t&auml;tigen</LI>\n";
        echo "      <LI>Mit Zur&uuml;ck k&ouml;nnen Sie eine andere Zahlungsart w&auml;hlen</LI>\n";
        echo "      <input type=hidden name='darstellen' value='5'>\n";
        echo "      <input type=hidden name='Kreditkarten_Hersteller' value='$Kreditkarten_Hersteller'>\n";
        echo "      <input type=hidden name='Kreditkarten_Nummer' value='$Kreditkarten_Nummer'>\n";
        echo "      <input type=hidden name='Kreditkarten_Ablaufdatum_1' value='$Kreditkarten_Ablaufdatum_1'>\n";
        echo "      <input type=hidden name='Kreditkarten_Ablaufdatum_2' value='$Kreditkarten_Ablaufdatum_2'>\n";
        echo "      <input type=hidden name='Vorname' value='$Kreditkarten_Vorname'>\n";
        echo "      <input type=hidden name='Nachname' value='$Kreditkarten_Nachname'>\n";
        echo '      <BR><BR><a href="'.$PHP_SELF.'?darstellen=1"><img src="Buttons/bt_zurueck.gif" border="0" alt="Zurueck" title="Zurueck"></a>&nbsp;'."\n";
        echo "      <input type='image' src='Buttons/bt_weiter.gif' border='0' alt='Weiter' title='Weiter'>\n";
        echo "  </td>\n";
        echo "</tr>\n";
        echo "</table>\n";
        echo "</form>\n";
        echo "</center">
        exit; //Abbruch
      }// End if $wrong

?>
  <script language="JavaScript" type="text/javascript">
  <!--
  function chkFormular() {
    // ueberpruefen, ob die AGB's akzeptiert wurden
    if(document.Formular.agb[1].checked) {
        alert("Sie müssen die AGB's akzeptieren, um bestellen zu können!");
        document.Formular.agb[1].focus();
        return false;
    } // end of if
    else {
        return true;
    }// end of else
  } // end of fuction chkFormular
  //-->
  </script>

  <center>
  <form action="<?php echo $PHP_SELF; ?>" method="POST" name="Formular" onSubmit="return chkFormular()">
  <table border=0 cellspacing=0 cellpadding=0 class=content width='80%'>
    <tr class=content>
      <td class=content>
<?php
  // AGB ausgeben
  echo "<h3 class='content'>Allgemeine Gesch&auml;ftsbedingungen</h3>";
  echo getAGB();

  // Hidden-Formular, damit wir die Variablen per Post weitergeben koennen
?>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
          <input type=radio name="agb" value="ja">&nbsp;Ja, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen und will bestellen<BR>
          <input type=radio name="agb" value="nein" checked>&nbsp;Nein, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen <B style='font-weight:bold'>nicht</B><BR>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr class=content>
      <td class=content>
            <input type=hidden name='Kreditkarten_Hersteller' value='<?php echo $Kreditkarten_Hersteller; ?>'>
            <input type=hidden name='Kreditkarten_Nummer' value='<?php echo $Kreditkarten_Nummer; ?>'>
            <input type=hidden name='Kreditkarten_Ablaufdatum_1' value='<?php echo $Kreditkarten_Ablaufdatum_1; ?>'>
            <input type=hidden name='Kreditkarten_Ablaufdatum_2' value='<?php echo $Kreditkarten_Ablaufdatum_2; ?>'>
            <input type=hidden name='Kreditkarten_Vorname' value='<?php echo $Kreditkarten_Vorname; ?>'>
            <input type=hidden name='Kreditkarten_Nachname' value='<?php echo $Kreditkarten_Nachname; ?>'>
            <input type=hidden name='Erfolg' value='1'>
            <input type=hidden name='GNUPG' value='1'>
            <input type=hidden name='darstellen' value='4'>
            <input type=hidden name=message_string_adr value="<?php echo $message_string_adr; ?>">
            <input type=hidden name=email value="<?php echo $email; ?>">
            <input type=hidden name=Zahlungsart value="<?php echo $Bezahlungsart; ?>">
            <center><input type="image" src='Buttons/bt_bestellung_absenden.gif' border="0" alt="Bestellung absenden" title="Bestellung absenden"></center>
      </td>
    </tr>
  </table>
  </form>
  </center>
<?php
  exit;

  }// End if GNUPG == 1
}// End darstellen == 7


// -----------------------------------------------------------------------
// Zahlungsart billBOX gewaehlt
// Drueckt der Shopkunde auf den "Bestellung absenden" Button, wird ein
// auf dem Server der Firma billBOX befindliches Perl-Script ausgeführt.
// Der Shopkunde bekommt eine Telefonnummer angezeigt, auf die er anrufen
// muss. Wenn er ein bei billBOX registrierter Kunde ist, wird die Zahlung
// ausgeloest.
// -----------------------------------------------------------------------
else if ($darstellen == 8) {
  // Ein neues Bestellungs-Objekt instanzieren
  $myBestellung = new Bestellung;
  // Session_ID des aktuellen Kunden auslesen
  $Session_ID = session_id();
  // Bestellung abfuellen
  $myBestellung = getBestellung($Session_ID);
  // Falls der Shop die Preise exkl. MwSt. angegeben hat, diese noch zur Bestellsumme addieren:
  // Auslesen der MwSt-Settings um feststellen zu koennen ob die Artikelpreise inkl. oder exkl. MwSt sind
  $aktuelle_mwst_settings = getmwstsettings();
  if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "N" && getmwstnr() > 0) {
      // Der Shop ist MwSt-pflichtig und die Preise sind exkl. MwSt angegeben. Nun muss der MwSt-Betrag
      // der Bestellung noch zum Rechnungsbetrag hinzuaddiert werden.
      $myBestellung->Rechnungsbetrag = $myBestellung->Rechnungsbetrag + $myBestellung->MwSt;
  }
  // Auslesen des Rechnungsbetrags
  $Rechnungsbetrag = $myBestellung->Rechnungsbetrag;
  // Auslesen des einzusetzenden Scriptnamens
  // Dieses wird in der Tabelle zahlung_weitere in der Zeile eins unter dem Attribut Par1 gespeichert.
  $weitereZahlungsmethoden = getAllezahlungen();
  $myZahlungsarray = $weitereZahlungsmethoden->getallzahlungen();
  $myParameterarray = $myZahlungsarray[0]->getallparameter();
  $billBOX_Scriptname = $myParameterarray[0];
  // Wir speichern den E-Mail-Message-String temporaer in der Kunden-Bestellung.
  // Dieser String wird zur Erstellung des E-Mails an Kunde und Shopbetreiber benoetigt.
  putEmailMessage(addslashes(urldecode($message_string_adr)),$Session_ID);
  echo "  <script language=\"JavaScript\" type=\"text/javascript\">\n";
  echo "  <!--\n";
  echo "  function chkFormular() {\n";
  echo "    // ueberpruefen, ob die AGB's akzeptiert wurden\n";
  echo "    if(document.Formular.agb[1].checked) {\n";
  echo "        alert(\"Sie müssen die AGB's akzeptieren, um bestellen zu können!\");\n";
  echo "        document.Formular.agb[1].focus();\n";
  echo "        return false;\n";
  echo "    } // end of if\n";
  echo "    else {\n";
  echo "        return true;\n";
  echo "    }// end of else\n";
  echo "  } // end of fuction chkFormular\n";
  echo "  //-->\n";
  echo "  </script>\n";
  echo "\n";
  echo "  <center>\n";
  echo "  <table border=0 cellspacing=0 cellpadding=0 class=content width='80%'>\n";
  echo "    <tr class=content>\n";
  echo "      <td class=content>\n";

  // AGB ausgeben
  echo "        <h3 class='content'>Allgemeine Gesch&auml;ftsbedingungen</h3>";
  echo "          ".getAGB();

  // Hidden-Formular, damit die darstellen-Variable weitergegeben werden kann
  // Hier wurde ABSICHTLICH die Preisdarstellung fix auf x'xxx.xx gehalten (kein Aufruf mit getZahlenformat())

echo "      </td>\n";
echo "    </tr>\n";
echo "    <tr><td>&nbsp;</td></tr>\n";
echo "    <tr class=content>\n";
echo "      <td class=content>\n";
echo "        <form action=\"$PHP_SELF\" method=\"POST\" name=\"Formular\" onSubmit=\"return chkFormular()\">\n";
echo "          <input type=radio name=\"agb\" value=\"ja\">&nbsp;Ja, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen und will bestellen<BR>\n";
echo "          <input type=radio name=\"agb\" value=\"nein\" checked>&nbsp;Nein, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen <B style='font-weight:bold'>nicht</B><BR>\n";
echo "        </form>\n";
echo "      </td>\n";
echo "    </tr>\n";
echo "    <tr><td>&nbsp;</td></tr>\n";
echo "    <tr class=content>\n";
echo "      <td class=content>\n";
echo "         <center><a href=\"http://www.phonegate.ch/cgi-bin/billbox/$billBOX_Scriptname?".printf ("%01.2f", $Rechnungsbetrag)."\" onClick=\"return chkFormular()\" ><img src=\"Buttons/bt_bestellung_absenden.gif\" border=\"0\" alt=\"Bestellung absenden\" title=\"Bestellung absenden\"></a></center>\n";
echo "      </td>\n";
echo "    </tr>\n";
echo "  </table>\n";
echo "  </center>\n";
} // end of darstellen = 8

// -----------------------------------------------------------------------
// Saferpay / B+S Card Service Kreditkartenhandling. Zustaendig fuer das
// Handling sind die Dateien saferpay_config.php, saferpay_init.php,
// saferpay_success.php und saferpay_failed.php. In der _config-Datei werden
// die benutzten Daten zusammengestellt, ausgelesen und vorbereitet. In der
// _init-Datei wird eine Zahlungsinitiierung vorgenommen, welche in der Datei
// _success bestaetigt (confirmed) und abgeschlossen (completed) wird. Falls
// ein Fehler auftreten sollte, so wird entweder die _failed-Datei ange-
// sprungen, oder man wird direkt auf darstellen=4 mit Erfolg=0 in dieser
// Datei weitergeleitet.
// -----------------------------------------------------------------------
else if ($darstellen == 15) {
  // Damit wir dem Interface-Modul saferpay_payment ein Pay-Objekt mit allen
  // benoetigten Daten uebergeben koennen, muessen wir hier eines erzeugen und
  // die entsprechenden Daten abfuellen:
  // Ein neues Pay-Objekt instanzieren
  $myPay = new Pay;
  // Session_ID des aktuellen Kunden auslesen
  $Session_ID = session_id();
  // Die Funktion checkSession liefert uns hier die Kunden_ID
  $Kunden_ID = checkSession($Session_ID);
  // Kunde abfuellen
  $myPay->myKunde = getKunde($Kunden_ID);
  // Bestellung abfuellen
  $myPay->myBestellung = getBestellung($Session_ID);
  // Falls der Shop die Preise exkl. MwSt. angegeben hat, diese noch zur Bestellsumme addieren:
  // Auslesen der MwSt-Settings um feststellen zu koennen ob die Artikelpreise inkl. oder exkl. MwSt sind
  $aktuelle_mwst_settings = getmwstsettings();
  if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "N" && getmwstnr() > 0) {
      // Der Shop ist MwSt-pflichtig und die Preise sind exkl. MwSt angegeben. Nun muss der MwSt-Betrag
      // der Bestellung noch zum Rechnungsbetrag hinzuaddiert werden.
      $myPay->myBestellung->Rechnungsbetrag = $myPay->myBestellung->Rechnungsbetrag + $myPay->myBestellung->MwSt;
  }
  // Hier wird die Ruecksprung-URL angegeben. Diese URL beschreibt den Ort im
  // PhPepperShop, wo nach der externen Zahlungsabwicklung die Bestellung entweder
  // akzeptiert und abgeschlossen wird oder aber wo die Bestellung nach nicht erfolg-
  // reicher Zahlungsabwicklung der Kunde an eine weiterleidende Page gefuehrt wird.
  $myPay->myReferrer = "http://".$SERVER_NAME.$PHP_SELF."?darstellen=4";
  // Wir speichern den E-Mail-Message-String temporaer in der Kunden-Bestellung.
  // Dieser String wird zur Erstellung des E-Mails an Kunde und Shopbetreiber benoetigt.
  putEmailMessage(addslashes(urldecode($message_string_adr)),$Session_ID);
  // Die gleich includete Datei saferpay_config.php benoetigt die Variable $session_id
  $session_id = session_id();
  echo "  <script language=\"JavaScript\" type=\"text/javascript\">\n";
  echo "  <!--\n";
  echo "  function chkFormular() {\n";
  echo "    // ueberpruefen, ob die AGB's akzeptiert wurden\n";
  echo "    if(document.Formular.agb[1].checked) {\n";
  echo "        alert(\"Sie müssen die AGB's akzeptieren, um bestellen zu können!\");\n";
  echo "        document.Formular.agb[1].focus();\n";
  echo "        return false;\n";
  echo "    } // end of if\n";
  echo "    else {\n";
  echo "        return true;\n";
  echo "    }// end of else\n";
  echo "  } // end of fuction chkFormular\n";
  echo "  //-->\n";
  echo "  </script>\n";
  echo "\n";
  echo "  <center>\n";
  echo "  <table border=0 cellspacing=0 cellpadding=0 class=content width='80%'>\n";
  echo "    <tr class=content>\n";
  echo "      <td class=content>\n";

  // AGB ausgeben
  echo "        <h3 class='content'>Allgemeine Gesch&auml;ftsbedingungen</h3>";
  echo "          ".getAGB();

  // Hidden-Formular, damit wir die Variablen per Post weitergeben koennen
  echo "      </td>\n";
  echo "    </tr>\n";
  echo "    <tr>\n";
  echo "      <td>\n";
  echo "        &nbsp;\n";
  echo "      </td>\n";
  echo "    </tr>\n";
  // Nun wird die Datei saferpay_init.php included. Sie wiederum included die Datei
  // saferpay_config.php welche alle Konfigurationsparameter enthaelt / ausliest / aufbereitet.
  // Im folgenden wird ein Formular dargestellt und die AGBs. Wenn der Kunde die AGBs akzeptiert
  // und auf den Link (Bestellung absenden) klickt wird ein PopUp (JavaScript) geoeffnet in welchem
  // der Bezahlungsprozess ueber Saferpay oder B+S Card Service ablaeuft. (Es wird eine Zeile geschrieben)
  if (!isset($saferpay_init)) {
      include("saferpay_init.php");
  }
  echo "  </table>\n";
  echo "  </center>\n";
} // end of darstellen = 15

// -----------------------------------------------------------------------
// Postfinance Yellowpay Postcard und optional Kreditkartenhandling
// Hier wird die Funktion postfinance_extern($Pay) aufgerufen. Ihr wird im Argument
// eine Pay mitgegeben (siehe auch pay_def.php), darin befinden sich alle
// noetigen Informationen betreffend der Zahlung. Der ganze Ablauf entspricht
// weitgehend dem einer extern eingebundenen Kreditkartenzahlung.
// -----------------------------------------------------------------------
else if ($darstellen == 16) {
  if (!isset($postfinance_interface)) {include("postfinance_interface.php");}
  // Damit wir dem Interface-Modul postfinance_interface ein Pay-Objekt mit allen
  // benoetigten Daten uebergeben koennen, muessen wir hier eines erzeugen und
  // die entsprechenden Daten abfuellen:
  // Ein neues Pay-Objekt instanzieren
  $myPay = new Pay;
  // Session_ID des aktuellen Kunden auslesen
  $Session_ID = session_id();
  // Die Funktion checkSession liefert uns hier die Kunden_ID
  $Kunden_ID = checkSession($Session_ID);
  // Kunde abfuellen
  $myPay->myKunde = getKunde($Kunden_ID);
  // Bestellung abfuellen
  $myPay->myBestellung = getBestellung($Session_ID);
  // Falls der Shop die Preise exkl. MwSt. angegeben hat, diese noch zur Bestellsumme addieren:
  // Auslesen der MwSt-Settings um feststellen zu koennen ob die Artikelpreise inkl. oder exkl. MwSt sind
  $aktuelle_mwst_settings = getmwstsettings();
  if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "N" && getmwstnr() > 0) {
      // Der Shop ist MwSt-pflichtig und die Preise sind exkl. MwSt angegeben. Nun muss der MwSt-Betrag
      // der Bestellung noch zum Rechnungsbetrag hinzuaddiert werden.
      $myPay->myBestellung->Rechnungsbetrag = $myPay->myBestellung->Rechnungsbetrag + $myPay->myBestellung->MwSt;
  }
  // Hier wird die Ruecksprung-URL angegeben. Diese URL beschreibt den Ort im
  // PhPepperShop, wo nach der externen Zahlungsabwicklung die Bestellung entweder
  // akzeptiert und abgeschlossen wird oder aber wo die Bestellung nach nicht erfolg-
  // reicher Zahlungsabwicklung der Kunde an eine weiterleidende Page gefuehrt wird.
  $myPay->myReferrer = "http://".$SERVER_NAME.$PHP_SELF."?darstellen=4";
  // Wir speichern den E-Mail-Message-String temporaer in der Kunden-Bestellung.
  // Dieser String wird zur Erstellung des E-Mails an Kunde und Shopbetreiber benoetigt.
  putEmailMessage(addslashes(urldecode($message_string_adr)),$Session_ID);
  echo "  <script language=\"JavaScript\" type=\"text/javascript\">\n";
  echo "  <!--\n";
  echo "  function chkFormular() {\n";
  echo "    // ueberpruefen, ob die AGB's akzeptiert wurden\n";
  echo "    if(document.Formular.agb[1].checked) {\n";
  echo "        alert(\"Sie müssen die AGB's akzeptieren, um bestellen zu können!\");\n";
  echo "        document.Formular.agb[1].focus();\n";
  echo "        return false;\n";
  echo "    } // end of if\n";
  echo "    else {\n";
  echo "        return true;\n";
  echo "    }// end of else\n";
  echo "  } // end of fuction chkFormular\n";
  echo "  //-->\n";
  echo "  </script>\n";
  echo "\n";
  echo "  <center>\n";
  echo "  <table border=0 cellspacing=0 cellpadding=0 class=content width='80%'>\n";
  echo "    <tr class=content>\n";
  echo "      <td class=content>\n";
  // AGB ausgeben
  echo "        <h3 class='content'>Allgemeine Gesch&auml;ftsbedingungen</h3>";
  echo "          ".getAGB();

  // Hidden-Formular, damit wir die Variablen per Post weitergeben koennen
  echo "      </td>\n";
  echo "    </tr>\n";
  echo "    <tr><td>&nbsp;</td></tr>\n";
  echo "    <tr class=content>\n";
  echo "      <td class=content>\n";
  // Nun wird die Funktion postfinance_extern($myPay) aufgerufen. Sie enthaelt den Beginn
  // des folgenden Formulars (damit im action=''-Teil der Ziel URL konfiguriert werden
  // kann. Um die Ansteuerung der externen Zahlungsstelle konfigurieren zu koennen,
  // editieren Sie die Datei postfinance_interface.php in diesem Verzeichnis.
  // In $special kann 'Postcard_only' stehen um die Postcard vorauszuwaehlen.
  if (!postfinance_extern($myPay, $special)) {
      die("<h2>U_B_1_Error: darstellen == 16: Die Funktion postfinance_extern(\$myPay) wurde nicht korrekt beendet</h2></body></html>\n");
  }
  echo "          <input type=hidden name=\"darstellen\" value=\"4\">\n";
  echo "          <input type=hidden name=\"message_string_adr\" value=\"$message_string_adr\">\n";
  echo "          <input type=hidden name=\"email\" value=\"$email\">\n";
  echo "          <input type=hidden name=\"Zahlungsart\" value=\"$Bezahlungsart\">\n";
  echo "          <input type=radio name=\"agb\" value=\"ja\">&nbsp;Ja, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen und will bestellen<BR>\n";
  echo "          <input type=radio name=\"agb\" value=\"nein\" checked>&nbsp;Nein, ich akzeptiere die allgemeinen Gesch&auml;ftsbedingungen <B style='font-weight:bold'>nicht</B><br>\n";
  echo "          <br>Um diese Zahlung veranlassen zu k&ouml;nnen, muss JavaScript eingeschaltet sein.\n";
  echo "          <br>\n<br>\n<br>\n";
  echo "          <center>\n";
  echo "            <!-- <input type=\"image\" src='Buttons/bt_bestellung_absenden.gif' border=\"0\" alt=\"Bestellung absenden\" title=\"Bestellung absenden\"> -->\n";
  echo "            <a href=\"javascript:if (chkFormular()) {document.forms[0].submit();}\"><img src='Buttons/bt_bestellung_absenden.gif' border=\"0\" alt=\"Bestellung absenden\" title=\"Bestellung absenden\"></a>\n";
  echo "          </center>\n";
  echo "        </form>\n";
  echo "      </td>\n";
  echo "    </tr>\n";
  echo "  </table>\n";
  echo "  </center>\n";
} // end of darstellen = 16

// -----------------------------------------------------------------------
// Letzter Abschnitt der Bestellung: Hier wird schliesslich das E-Mail aufbereitet (z.T...) und abgesendet
// - Unterscheidung des Workflows je nach Bezahlungsart:
//   - Vorauskasse, Rechnung, Nachnahme: AGB akzeptiert?, dann weiter zur E-Mail Erstellung
//   - Interne Kreditkartenzahlung: Nummer validieren, GnuPG enable, weiter...
//   - Externe Kreditkartenzahlung: E-Mail String zuruecklesen, weiter...
// - Bestellungs-String in Datenbank schreiben (fuer Kundenmanagement)
// - Bestellung an Kunden und Shopinhaber mailen
// - Danke an Shopuser ausgeben (mit Kontaktierungsmoeglichkeit bei Fragen)
// - Bestellung abschliessen
// - Alle alten Bestellungen (via Session-ID's) aus Datenbank loeschen (aufraeumen)
// -----------------------------------------------------------------------
else if ( $darstellen == 4) {
  if ($agb == "nein") {
?>
          <center>
          <table border=0 width=80%>
            <tr class='content'>
              <td class='content'>
                <h3 class='content'>AGB nicht akzeptiert!</h3></td></tr>
              </td>
            </tr>
            <tr class='content'>
              <td class='content'>
                <b class='content' style='font-weight:bold'>Damit eine Bestellung ausgel&ouml;st werden kann, m&uuml;ssen die allgemeinen Gesch&auml;ftsbedingungen akzeptiert werden</b>
                <ul>
                  <li>Wenn Sie die Bestellung ausl&ouml;sen wollen, klicken Sie auf zur&uuml;ck</li>
                  <li>Falls Sie unsere allgemeinen Gesch&auml;ftsbedingungen nicht akzeptieren, so k&ouml;nnen Sie jetzt Ihren Browser schliessen. Ihre Bestellung wird nicht ausgef&uuml;hrt.</li>
                </ul>
              </td>
            </tr>
            <tr class='content'>
              <td class='content'>
                <form action="<?php echo $PHP_SELF; ?>" name="Formular" method="POST">
                  <input type=hidden name=message_string_adr value="<?php echo $message_string_adr; ?>">
                  <input type=hidden name=email value="<?php echo $email; ?>">
                  <input type=hidden name=Zahlungsart value="<?php echo $Bezahlungsart; ?>">
                  <input type=hidden name=darstellen value='3'>
                  <center><input type="image" src='Buttons/bt_zurueck.gif' border="0" alt="Zurueck" title="Zurueck"></center>
                </form>
              </td>
            </tr>
          </table>
          </center>
          </body>
          </html>
<?php
      // Falls der Kunde mit den AGB nicht einverstanden ist, so wird das Programm hier beendet
      // andernfalls, kann der Kunde mit dem zurück-Button wider zurück gehen (darstellen == 3)
      exit;
  }//End if agb == nein
  if ($Erfolg == 0) {
      // Die Programmausfuehrung muss hier beendet werden, weil es einen Fehler bei der externen
      // Zahlungsverarbeitung gab (Billbox / Externe Kreditkarteninstitute ueber das Payment-Interface / Saferpay / Yellowpay)
?>
          <center>
          <table border=0 width=80%>
            <tr class='content'>
              <td class='content'>
                <h3 class='content'>Fehler bei Zahlungsverarbeitung!</h3></td></tr>
              </td>
            </tr>
            <tr class='content'>
              <td class='content'>
                <?php if ($Errormessage != "") { ?>
                    <b class='content' style='font-weight:bold'>Genauere Fehlermeldung: <?php echo $Errormessage; ?></b>
                <?php }?>
                <ul>
                  <li>Wenn Sie nochmals versuchen wollen zu zahlen oder eine andere Zahlungsmethode w&auml;hlen wollen, so klicken sie auf Zur&uuml;ck</li>
                  <li>Falls Sie die Bestellung nicht ausl&ouml;sen wollen, so k&ouml;nnen Sie jetzt Ihren Browser schliessen. Ihre Bestellung wird <i>nicht</i> ausgel&ouml;st.</li>
                </ul>
              </td>
            </tr>
            <tr class='content'>
              <td class='content'>
                <form action="<?php echo $PHP_SELF; ?>" name="Formular" method="POST">
                  <input type=hidden name=darstellen value='1'>
                  <center><input type="image" src='Buttons/bt_zurueck.gif' border="0" alt="Zurueck" title="Zurueck"></center>
                </form>
              </td>
            </tr>
          </table>
          </center>
          </body>
          </html>
<?php
      // Da die Bezahlung fehlgeschlagen ist, wird eine Fehlermeldung an den Administrator gemailt:
      $myBestellung = new Bestellung();
      $myBestellung = getBestellung(session_id());
      $myKunde = new Kunde();
      $myKunde = getKunde_einer_Bestellung($myBestellung->Bestellungs_ID);
      $error_msg = "Eine Zahlung (".$myBestellung->Bezahlungsart.") konnte nicht bestätigt (confirmed) werden. Dies kann an mehreren Ursachen liegen.\n";
      $error_msg.= "Es ist gut möglich, dass der Kunde die Bestellung nun mit einer anderen Zahlungsart abgeschlossen hat,\n";
      $error_msg.= "so noch weitere Zahlungsarten freigeschaltet sind. Aus Sicherheitsgründen sollte man das aber abklären.\n\n";
      $error_msg.= "Verfuegbare Daten des Kunden, bei welchem der Fehler aufgetreten ist: \n";
      $kunde_string.= "Kunden_ID : ".$myKunde->Kunden_ID." (PhPepperShop intern vergebene ID)\n";
      $kunde_string.= "Kunden Nr.: ".$myKunde->Kunden_Nr."\n";
      $kunde_string.= "Vorname   : ".$myKunde->Vorname."\n";
      $kunde_string.= "Nachname  : ".$myKunde->Nachname."\n";
      $kunde_string.= "Strasse   : ".$myKunde->Strasse."\n";
      $kunde_string.= "PLZ/Ort   : ".$myKunde->PLZ." ".$myKunde->Ort."\n";
      $kunde_string.= "Tel       : ".$myKunde->Tel."\n";
      $kunde_string.= "Fax       : ".$myKunde->Fax."\n";
      $kunde_string.= "E-Mail    : ".$myKunde->Email."\n";
      $kunde_string.= "Zahlung m.: ".$myBestellung->Bezahlungsart."\n";
      $kunde_string.= "Account   : ".$myKunde->gesperrt."\n\n";
      $bestellung_string = filterBestellungsTags(darstellenStringBestellung($myBestellung))."\n\n";
      $error_msg.= $kunde_string."\nBis dato verfuegbare Bestellungsdaten:\n".$bestellung_string;
      $error_msg.= "Dieser Fehlerbericht wurde in der Datei ".__FILE__." vor der Zeile ".__LINE__." ausgelöst. Die darstellen Variable = $darstellen, Erfolg = $Erfolg.\n\n";
      send_error_mail($error_msg);

      // Programmausfuehrung beenden
      exit;
  }//End if Erfolg == 0


  // Zuerst von der Datenbank Daten auslesen
  $meineBestellung = new Bestellung;
  $meineBestellung = getBestellung(session_id());
  // Falls ein Kunde nach erfolgreicher Bestellung den Zurueck-Button des Browsers verwendet
  // so wird das hier abgefangen, er sieht dann nur die Fehlermeldung
  if ($meineBestellung->Bestellung_abgeschlossen == "" || $meineBestellung->Bestellung_abgeschlossen == "Y") {
?>
          <SCRIPT LANGUAGE="JavaScript">
                <!--
                function popUp(URL) {
                day = new Date();
                id = day.getTime();
                eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=670,left = 312,top = 234');");
                }
                // End -->
          </script>

          <center>
          <table border=0 width=80%>
            <tr class='content'>
              <td class='content'>
                <h3 class='content'>Sie haben Ihre Bestellung schon abgeschlossen!</h3></td></tr>
              </td>
            </tr>
            <tr class='content'>
              <td class='content'>
                <ul>
                  <li>Um eine weitere Bestellung aufzugeben, klicken Sie bitte auf eine Artikelkategorie</li>
                  <li>Um Ihre soeben aufgegebene Bestellung zu verifizieren, schauen Sie in Ihr E-Mail Konto</li>
                </ul>
              </td>
            </tr>
            <tr>
              <td>
                  <br><br>
                  Wenn Sie Fragen oder Probleme haben, wenden Sie sich doch an unseren <a href="javascript:popUp('kontakt.php?subject=Shopnachricht%20%20Bestellung%20abgeschlossen%20und%20jetzt%20ein%20Problem')">Support</a>!
              </td>
            </tr>
          </table>
          </center>
          </body>
          </html>
<?php
      // Programmabbruch weil Bestellung schon abgeschlossen wurde (z.B. wenn jemand Zurueck klickt)
      exit;
  }// End if Bestellung schon abgeschlossen

  // Wenn wir von darstellen == 6 her kommen, dann wurde die Zahlung extern abge-
  // wickelt und der E-Mail-Message-String temporaer in der Kunden-Bestellung in der
  // Datenbank gespeichert. Wir holen ihn gegebenenfalls aus der DB zurueck.
  if (urldecode($message_string_adr) == "") {
      // String ist leer, in diesem Fall muessen wir ihn von der Kundentabelle auslesen
      // und ihn dort danach loeschen (die Funktion getEmailMessage uebernimmt beides)
      $message_string_adr = getEmailMessage(session_id());
      // Weiter muessen wir hier noch die E-Mail Adresse des Kunden und die Bezahlungs-
      // art in Variablen fuer die weitere Bearbeitung bereitstellen
      $Kunden_ID = checkSession(session_id());
      $meinKunde = getKunde($Kunden_ID);
      $abc = substr($meinKunde->Nachname,0,1);
      $email = $meinKunde->Email;
  }
  else {
      // Ersten Buchstaben des Kundennachnamens auslesen (fuer Link ins Admin Kundenmanagement)
      $meinKunde = getKunde($Kunden_ID);
      $abc = substr($meinKunde->Nachname,0,1);
  }

  // abfuellen der Bestellungsdaten in Variablen
  $Bestellungs_ID = $meineBestellung->Bestellungs_ID;
  $Session_ID = $meineBestellung->Session_ID;
  $Datum = $meineBestellung->Datum;
  $Bezahlungsart = $meineBestellung->Bezahlungsart;
  $Zahlungsart = $meineBestellung->Bezahlungsart;
  $Versandkosten = $meineBestellung->Versandkosten;
  $Mindermengenzuschlag = $meineBestellung->Mindermengenzuschlag;
  $Rechnungstotal = $meineBestellung->Rechnungsbetrag;
  $clearing_id = $meineBestellung->clearing_id;
  $clearing_extra = $meineBestellung->clearing_extra;

  // Wenn es sich um eine PostFinance Zahlung handelt, so muss abgewartet werden, dass PostFinance
  // in einem parallel laufenden Aufruf postfinance_payment.php aufgerufen hat. Ist dies der Fall,
  // so wissen wir ob die Zahlung geglueckt ist oder nicht.
  $zufallszahl_fehlerhaft = false; // Dieses Flag wird true, wenn die Zufallszahl manipuliert wurde
  if (substr($clearing_id,0,11) == 'postfinance') {
      while($clearing_extra == "") {
          $myBestellung = getBestellung($session_id);
          $clearing_id = $myBestellung->clearing_id;
          $clearing_extra = $myBestellung->clearing_extra; // enthaelt Fehlermeldung oder sonstige Daten
          usleep(1000); // Eine halbe Sekunde warten (um Race-Condition zu vermeiden)
      }// End while
      if (substr($clearing_extra,0,4) != "OK: ") {
          $zufallszahl_fehlerhaft = true;
      }
  }
  else if (substr($clearing_id,0,8) == 'saferpay') {
      $clearing_id = $meineBestellung->clearing_id;
      $clearing_extra = $meineBestellung->clearing_extra; // enthaelt Fehlermeldung oder sonstige Daten
      if (substr($clearing_extra,0,4) != "OK: ") {
          $zufallszahl_fehlerhaft = true;
      }
  }
  // Falls die Zufallszahl eines externen Kreditkarteninstituts (PostFinance oder Saferpay / B+S Card Service)
  // manipuliert wurde, so wird der Bestellungsvorgang hier unterbrochen. Der Administrator wurde unterdessen
  // von den entsprechenden Modulen via E-Mail auf den Vorfall hingewiesen.
  if ($zufallszahl_fehlerhaft == true) {
      // Fehlerausgabe und Beenden der Verarbeitung
      echo "  <TABLE class=\"content\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"100%\">\n";
      echo "    <TR class='content'>\n";
      echo "      <TD class='content' ALIGN=center VALIGN=middle >\n";
      echo "        <h3 class='content'>Zahlung nicht abgeschlossen!</h3>";
      echo "        <h4>Es gab ein Problem bei der externen Zahlungsverarbeitung:<br>$clearing_extra</h4>\n";
      echo "        <h4>Bitte versuchen Sie es nochmals oder w&auml;hlen Sie eine andere Zahlungsart.</h4>\n";
      echo "        <h4 class='content'><A ".$stylestring." href=\"".$PHP_SELF."?darstellen=1\">Zur&uuml;ck zur Kasse</a></h4>";
      echo "      </TD>\n";
      echo "    </TR>\n";
      echo "  </TABLE>\n";
      // HTML-Dokument schliessen
      echo "</body>\n";
      echo "</html>\n";
      // Programmverarbeitung abbrechen
      exit;
  }

  // Auslesen der global benutzten Shopadresse:
  $shopadresse = getShopadresse();

  // Aktuelles Datum berechnen (wird dem Mailheader angehaengt)
  $mydate = getdate();
  $Datum = $mydate[mday].".".$mydate[mon].".".$mydate[year];// Format dd-mm-yyyy
  $message = "<Absender>--------------------------";
  if ($shopadresse[0] != "") { $message.="\nBestellung bei ".$shopadresse[0]; }
  if ($shopadresse[1] != "") { $message.="\n".$shopadresse[1]; }
  if ($shopadresse[2] != "") { $message.="\n".$shopadresse[2]; }
  if ($shopadresse[3] != "") { $message.="\n".$shopadresse[3]; }
  if ($shopadresse[4] != "") { $message.="\nE-Mail: ".$shopadresse[4]; }
  if ($shopadresse[5] != "") { $message.="\nTel. ".$shopadresse[5]; }
  if ($shopadresse[6] != "") { $message.="\nFax. ".$shopadresse[6]; }
  $message.="\nBestellung vom: ".$Datum."\n";
  $message.="--------------------------</Absender>\n\n";

  // die Bezahlungsart wieder URL-decodieren
  $Bezahlungsart = urldecode($Zahlungsart);

  // Adressdaten anhaengen
  $message.= urldecode("<Kunde>".$message_string_adr."</Kunde>");

  $message.= "\n\n<Zahlungsart>Bestellungsinformationen:\n-------------------------\n";

  // Test, ob es eine kostenlose Bestellung war (Rechnungstotal = 0.00)
  if ($Rechnungstotal - ($Mindermengenzuschlag + $Versandkosten) > 0.0) {
      // Es war eine Bezahlungsart, welche kostete. Nun testen wir, ob es
      // eine Saferpay / B+S Card Service Zahlung war, dann haben wir spezielle
      // Zusatzinfos auszuwerten
      if ($payment_institut == "saferpay" && $Bezahlungsart == "Kreditkarte") {
          $message.="Zahlungsart: ".$Bezahlungsart." via Saferpay / B+S Card Service\n";
      }
      else {
          $message.="Zahlungsart: ".$Bezahlungsart."\n";
      }
  }
  else {
      $message.="Keine Bezahlung nötig, da Rechnungstotal = ".getWaehrung()." 0.00\n";
  }

  if ($Bezahlungsart == "Vorauskasse") {
      $message.=getKontoinformation()."\n";
  }

  // Artikel auslesen
  $warenkorb = darstellenStringBestellung($meineBestellung);

  // Mail an Shopinhaber versenden
  $mailproblem = 0; // Flag, welches beim Auftreten von Mailproblemen auf 1 gesetzt wird
  $to=getShopEmail();
  $subject="neue Bestellung Webshop vom ".$Datum." !";
  $header="From: ".getShopEmail()."\n";
  // notwendig, damit deutsche Umlaute richtig angezeigt werden
  $header.="Content-Type: text/plain; charset=iso-8859-1";

  // Link erstellen um im Kundenmanagement direkt auf die Bestellung zu kommen
  $newmessage ="<Bestellung>Neue Shopbestellung eingetroffen\n================================\n";
  $newmessage.="Um die Bestellung im Kundenmanagement zu bearbeiten, diesem Link folgen:\n";
  // Bei eingeschaltetem SSL wird https:// verwendet, sonst http://
  $newmessage.=getShopRootPath(true)."/Admin/shop_kunden_mgmt.php?abc=$abc".substr($nachname,0,1)."&r=".bestellungs_id_to_ref($Bestellungs_ID)."\n\n";

  // $newmessage ist der Mailinhalt an den Shop-Betreiber, $message ist der Mailinhalt an den Shopkunden
  // Es werden jetzt noch weitere Daten zum bestehenden Mailinhalt hinzugefuegt. Der Shopbetreiber sieht
  // natuerlich noch andere Daten als der Shopkunde. (Zuerst eine Kopie des Kundenmailinhalts fuer den Shopadmin erstellen):
  $newmessage.=$message;
  $message = "<Bestellung>".$message; // Bestellungstag an den Anfang setzen

  // Meldung, dass KK-Daten und Lastschriftdaten im Kundenmanagement ersichtlich sind
  // Dieser String wird bei einem Parsevorgang verwendet.
  $daten_ersichtlich_message['kk_daten'] = "(Kreditkartendaten sind im Kundenmanagement ersichtlich)";
  $daten_ersichtlich_message['lastschrift_daten'] = "(Lastschriftdaten sind im Kundenmanagement ersichtlich)";
  $daten_ersichtlich_message['saferpay'] = "(Saferpay / B+S Card Service Daten sind im Kundenmanagement ersichtlich)";
  $daten_ersichtlich_message['postfinance'] = "(PostFinance yellowpay Daten sind im Kundenmanagement ersichtlich)";

  // Falls es sich bei der Zahlung um eine Postfinance yellowpay Zahlung handelt, muss die Variable $payment_insititut
  // noch gesetzt werden
  if (substr($clearing_id,0,11) == 'postfinance') {
      $payment_institut = 'postfinance';
  }

  if ($Bezahlungsart == "Lastschrift") {
      // Bankdaten aus dem in darstellen = 12 und 13 gespeicherten Datensatz herauslesen
      $bankdaten = get_kunden_bankdaten(session_id());
      $message.="Bankverbindungsdaten wurden dem Shopadministrator übermittelt\n"; // Anzeige fuer Shopkunde
      $newmessage.="Bankverbindungsdaten wurden übermittelt:\n".$daten_ersichtlich_message['lastschrift_daten']."\n"; // Anzeige fuer Shopbetreiber
      // Hier werden die Kundendaten 'nochmals' gespeichert. Hier wird aber auch dem Kundenwunsch entsprochen seine Bankdaten NICHT zu speichern - so er das will
      set_kunden_bankdaten(session_id(),$bankdaten["kontoinhaber"],$bankdaten["bankname"],$bankdaten["blz"],$bankdaten["kontonummer"],$bankdaten["bankdaten_speichern"],true);
  }
  else if ($payment_institut == "saferpay" && $Bezahlungsart == "Kreditkarte") {
      $newmessage.="Kreditkartenzahlung war erfolgreich:\n".$daten_ersichtlich_message['saferpay']."\n"; // Anzeige fuer Shopbetreiber
  }
  else if ($payment_institut == "postfinance") {
      $newmessage.="PostFinancezahlung war erfolgreich:\n".$daten_ersichtlich_message['postfinance']."\n"; // Anzeige fuer Shopbetreiber
  }

  // Wenn es sich um eine interne Kreditkartenzahlung handelt, so ist das Flag
  // GNUPG = 1. Wir werden nun zuerst das Shopbetreiber-Email mit GnuPG verschluesseln
  // und es erst so an den Shop-Betreiber versenden
  if ($GNUPG == 1) {
      $newmessage.="Kreditkarten Institut:    ".urldecode($Kreditkarten_Hersteller)."\n".$daten_ersichtlich_message['kk_daten']."\n";
      /* Die Kreditkartendaten werden im Moment nicht mehr uebertragen, weil die GnuPG Verschluesselung nicht
         standardmaessig enthalten ist. Wenn jemand die E-Maildaten verschluesselt, kann er folgenden Kommentar
         wieder ausklammern und die Kreditkartendaten mitsenden. Neu, wird hier ein direkter https-Link auf die
         entsprechende Bestellung im Kundenmanagement gesetzt.
      $newmessage.="\n  - Kreditkartennummer:       ".urldecode($Kreditkarten_Nummer);
      $newmessage.="\n  - Kreditkarten Ablaufdatum: ".urldecode($Kreditkarten_Ablaufdatum_1)."/".urldecode($Kreditkarten_Ablaufdatum_2);
      $newmessage.="\n  - Vorname des Inhabers:     ".urldecode($Kreditkarten_Vorname);
      $newmessage.="\n  - Nachname des Inhabers:    ".urldecode($Kreditkarten_Nachname)."</Zahlungsart>\n";
      */
      // IP-Adresse des Kunden mit uebertragen:
      if ($mydate[hours] < 10) { $uhrzeit_std = "0".$mydate[hours];} else {$uhrzeit_std = $mydate[hours];}
      if ($mydate[minutes] < 10) { $uhrzeit_min = "0".$mydate[minutes];} else {$uhrzeit_min = $mydate[minutes];}
      $newmessage.="IP-Adresse des Kunden: ".$HTTP_SERVER_VARS["REMOTE_ADDR"]." (Uhrzeit: ".$uhrzeit_std.":".$uhrzeit_min."h)\n";
      $newmessage.=$warenkorb; // Warenkorbausgabe anfuegen

      // ******** NOCH NICHT IMPLEMENTIERT ********
      // An dieser Stelle sollte das E-Mail mit GNUPG verschluesselt werden!!!
      // ******** NOCH NICHT IMPLEMENTIERT ********

      // Die Funktion filterBestellungsTags loescht die BestellungsTags (siehe bestellung_def.php) aus dem Mailstring heraus
      if (!mail ($to, $subject, filterBestellungsTags($newmessage), $header) || strrchr($to, '@') == ""){
        echo "<h1 class='content'>Probleme beim Mailversand.. bitte nehmen sie per E-Mail oder telefonisch Kontakt mit uns auf!</h1>";
        echo "<B>Vielleicht hat der Shop-Betreiber seine eigene Shop E-Mail Adresse noch nicht konfiguriert!</B>";
        $mailproblem = 1;
      }

  }
  else {
      if ($Bezahlungsart == "billBOX"){
          $newmessage.="\nDie billBOX Tracking-Nummer ist: $tracking\n";
      }
      // IP-Adresse des Kunden mit uebertragen:
      if ($mydate[hours] < 10) { $uhrzeit_std = "0".$mydate[hours];} else {$uhrzeit_std = $mydate[hours];}
      if ($mydate[minutes] < 10) { $uhrzeit_min = "0".$mydate[minutes];} else {$uhrzeit_min = $mydate[minutes];}
      $newmessage.="IP-Adresse des Kunden: ".$HTTP_SERVER_VARS["REMOTE_ADDR"]." (Uhrzeit: ".$uhrzeit_std.":".$uhrzeit_min."h)\n";
      $newmessage.=$warenkorb; // Warenkorbausgabe anfuegen
      // E-Mail an Shopadministrator senden
      // Die Funktion filterBestellungsTags loescht die BestellungsTags (siehe bestellung_def.php) aus dem Mailstring heraus
      if (!mail ($to, $subject, filterBestellungsTags($newmessage), $header) || strrchr($to, '@') == ""){
        echo "<h1 class='content'>Probleme beim Mailversand.. bitte nehmen sie per E-Mail oder telefonisch Kontakt mit uns auf!</h1>";
        echo "<B>Vielleicht hat der Shop-Betreiber seine eigene Shop E-Mail Adresse noch nicht konfiguriert!</B>";
        $mailproblem = 1;
      } // end of if !mail
  } // end of else

  // Bestellungstabelle updaten: Die Bestellung, so wie an den Kunden versendet wird als 'Textabbild' in der DB festgehalten
  // Sie kann ueber das Kundenmanagement abgerufen werden. Die BestellungsTags werden mit kopiert.
  // Dabei werden allfaellige Kreditkartendaten und Bankdaten bei Lastschriftzahlung wieder eingefuegt.
  $lastschrift_daten ="  - Kontoinhaber:  ".$bankdaten["kontoinhaber"]."\n";
  $lastschrift_daten.="  - Name der Bank: ".$bankdaten["bankname"]."\n";
  $lastschrift_daten.="  - Bankleitzahl:  ".$bankdaten["blz"]."\n";
  $lastschrift_daten.="  - Kontonummer:   ".$bankdaten["kontonummer"]."\n";
  $kk_daten ="  - Kreditkartennummer:       ".urldecode($Kreditkarten_Nummer);
  $kk_daten.="\n  - Kreditkarten Ablaufdatum: ".urldecode($Kreditkarten_Ablaufdatum_1)."/".urldecode($Kreditkarten_Ablaufdatum_2);
  $kk_daten.="\n  - Vorname des Inhabers:     ".urldecode($Kreditkarten_Vorname);
  $kk_daten.="\n  - Nachname des Inhabers:    ".urldecode($Kreditkarten_Nachname)."</Zahlungsart>\n";
  $saferpay_daten = "Saferpay / B+S Card Service Daten:\n";
  $saferpay_daten.= "ID:    ".$clearing_id." (Transaktionskennung)\n";
  // *** Im Moment deaktiviert weil zu lang *** $saferpay_daten.= "TOKEN: ".$clearing_extra."</Zahlungsart>\n";
  $postfinance_daten = "PostFinance yellowpay Daten:\n";
  $postfinance_daten.= "Transaktions-ID:      ".preg_replace("°postfinance_?°","",$clearing_id)."\n";
  $postfinance_daten.= "Benutzte Zahlungsart: ".str_replace("OK: ","",$clearing_extra)."</Zahlungsart>\n";
  $newmessage = str_replace($daten_ersichtlich_message['kk_daten'],$kk_daten,$newmessage);
  $newmessage = str_replace($daten_ersichtlich_message['lastschrift_daten'],$lastschrift_daten,$newmessage);
  $newmessage = str_replace($daten_ersichtlich_message['saferpay'],$saferpay_daten,$newmessage);
  $newmessage = str_replace($daten_ersichtlich_message['postfinance'],$postfinance_daten,$newmessage);
  set_Bestellung_string(session_id(),addslashes($newmessage));

  //Mail an Shopkunden versenden, falls dieser eine E-Mail-Adresse eingegeben hat
  $email_kunde = urldecode($email);
  if ($email_kunde != ""){
      $to=$email_kunde;
      $subject="Ihre Bestellung bei ".getshopname();
      $header="From: ".getShopEmail();
      $message.=$warenkorb;
      $zusatz_bemerkungen = ""; // Momentan noch statisch, ideal z.B. fuer 'Kaufvertrag entsteht bei Lieferung der Ware,...'
      $message.=$zusatz_bemerkungen;
      // ...notwendig, damit z.B. Deutsche Umlaute richtig angezeigt werden
      $header.="\nContent-Type: text/plain; charset=iso-8859-1";
      // Mail an Kunden nur versenden, wenn beim Admin-Mail keine Probleme aufgetreten sind
      // Die Funktion filterBestellungsTags loescht die BestellungsTags (siehe bestellung_def.php) aus dem Mailstring heraus
      if ($mailproblem == 0){
          if (!mail ($to, $subject, filterBestellungsTags($message), $header)){
              echo "<h1 class='content'>Probleme beim Mailversand.. bitte nehmen sie per E-Mail oder telefonisch Kontakt mit uns auf!</h1>";
          } // end of if
      } // end of if
  } // end of if $email_kunde != ""

  // Bestellung einem Kunden fest zuordnen
  gibBestellung_an_Kunde($Bestellungs_ID, checkSession(session_id()));

  // 'Einkaufsvolumen' und 'letztes Bestelldatum' aktualisieren
  // Bei Preisen exkl. MwSt muss auch noch die MwSt mitgegeben werden

  // Auslesen der MwSt-Settings um feststellen zu koennen ob die Artikelpreise inkl. oder exkl. MwSt sind
  $aktuelle_mwst_settings = getmwstsettings();
  if ($aktuelle_mwst_settings[0]->Preise_inkl_MwSt == "N" && getmwstnr() > 0) {
      // Der Shop ist MwSt-pflichtig und die Preise sind exkl. MwSt angegeben. Nun muss der MwSt-Betrag
      // der Bestellung noch zum Rechnungsbetrag hinzuaddiert werden.
      addEinkaufsvolumen(session_id(), ($meineBestellung->Rechnungsbetrag + $meineBestellung->MwSt));
  }
  else {
      // Die Angabe des Rechnungsbetrags der Bestellung reicht, da hier nicht noch weitere MwSt-Betraege anfallen
      addEinkaufsvolumen(session_id(), $meineBestellung->Rechnungsbetrag);
  }

  // Bestellung abschliessen, sodass sie nicht geloescht wird wenn man das
  // Bestellungsmanagement (ab v.1.4 Kundenmanagement) eingeschaltet hat. Als zweites Argument wird ein Flag
  // uebergeben, mit welchem gesagt wird, ob die Bestellung als abgeschlossen markiert
  // werden soll. Abgeschlossene Bestellungen werden nicht mehr automatisch geloescht.
  schliessenBestellung(session_id(), getBestellungsmanagement());

  // Alle alten Session-ID's aus Datenbank loeschen, damit werden auch alle temporaeren Bestellungen / -Kunden geloescht
  delallexpiredSessions();

  // Styles fuer die Links aus der Datenbank auslesen und den Stylestring zusammenbauen
  $stylestring = 'class="content" style="text-decoration:'.getcssarg("main_link_d").';
    color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").';
    font-weight:'.getcssarg("main_link_w").'"';

  // Bestaetigung und Dank an Browser ausgeben
  ?>

  <SCRIPT LANGUAGE="JavaScript">
  <!-- Begin
    function popUp(URL) {
        day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=480,left = 312,top = 234');");
    }
  // End -->
  </script>

  <TABLE class="content" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
    <TR class='content'>
      <TD class='content' ALIGN=center VALIGN=middle >
        <?php
        if ($mailproblem == 0){
            echo "<h3 class='content'>Herzlichen Dank für Ihre Bestellung</h3>";
            echo "<h4 class='content'>Falls Sie noch Fragen haben, benutzen <br>Sie bitte unsere <A ".$stylestring." href=\"javascript:popUp('./kontakt.php')\">Kontaktm&ouml;glichkeiten</a></h4>";
        } // end of if
        ?>
      </TD>
    </TR>
  </TABLE>
  <?php
}

// -----------------------------------------------------------------------
// Folgender Fall sollte eigentlich nie auftreten. Falls doch, wird eine
// Fehlermeldung ausgegeben.
// -----------------------------------------------------------------------
else {
  echo "<h2 class='content'>Fehlerhafter Aufruf! Die darstellen-Variable wurde nicht &uuml;bergeben oder es existiert kein Handler daf&uuml;r! $darstellen</h2>";
  }

// Footer ausgeben (für alle Darstellungen gleich)
?>
  </body>
</html>

<?php
  // End of file-----------------------------------------------------------------------
?>
