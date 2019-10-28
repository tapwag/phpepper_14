<?php
  // Filename: kontakt.php
  //
  // Modul: AUFRUF-Module    KONTAKT
  //
  // Autoren: Jose Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Ermoeglicht dem Kunden, den Shopbetreiber zu kontaktieren
  //
  // Sicherheitsstufe:                     *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: kontakt.php,v 1.24 2003/06/30 08:49:53 fontajos Exp $
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $kontakt = true;

  // -----------------------------------------------------------------------

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Wenn der Haendlermodus aktiviert wurde (alle Kunden muessen sich zuerst einloggen), dann ueberprueft folgender Link,
  // ob man schon eingeloggt ist (hier deaktiviert, weil man sonst keinen Supportkontakt aufbauen kann)
  // if (!isset($USER_AUTH)) {include("USER_AUTH.php");}

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($initialize)) {include("initialize.php");}
  if (!isset($USER_SQL_BEFEHLE)) {include("USER_SQL_BEFEHLE.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($HTTP_GET_VARS);
  extract($HTTP_POST_VARS);
  extract($HTTP_SERVER_VARS);

?>

<html>
  <head>
    <title>Kontaktinformationen</title>
    <meta HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
    <meta HTTP-EQUIV="language" CONTENT="de">
    <meta HTTP-EQUIV="author" CONTENT="Jose Fontanil & Reto Glanzmann">
    <meta NAME="robots" CONTENT="all">
    <link REL=STYLESHEET TYPE="text/css" HREF="./shopstyles.css">
  </head>
  <body CLASS="content">

<?php
    // Auslesen der global benutzten Shopadresse:
    $shopadresse = getShopadresse();

    if ($darstellen == 1){
        // Aktuelles Datum berechnen (wird dem Mailheader angehaengt)
        $mydate = getdate();
        $Datum = $mydate[mday].".".$mydate[mon].".".$mydate[year];// Format dd-mm-yyyy

        // Passwort anhaengen
        $message="Shop-Feedback:\n--------------\n\n";
        $message.="Datum:   ".$Datum."\n";
        $message.="Name:    ".stripslashes($Name)."\n";
        $message.="Vorname: ".stripslashes($Vorname)."\n";
        $message.="E-Mail:  ".stripslashes($Email)."\n";
        $message.="Tel.     ".stripslashes($Tel)."\n";
        $message.="\n";
        $message.="Bemerkung:\n".stripslashes($Bemerkung);
        //Mail an Shopkunden versenden
        $to=getShopEmail();
        if ($subject == ""){
            $subject="Shop-Feedback \"".getshopname()."\"";
        }
        $header="From: ".getShopEmail();
        // notwendig, damit deutsche Umlaute richtig angezeigt werden
        $header.="\nContent-Type: text/plain; charset=iso-8859-1";
        if (!mail ($to, $subject, $message, $header)){
          die("<h1 class='content'>Probleme beim Mailversand.. Bitte nehmen sie per E-Mail oder telefonisch Kontakt mit uns auf! (mailPasswort)</h1>");
        }
        echo "<table width=100% height=100% class='content' border='0' cellpadding='0' cellspacing='5'>";
        echo "<tr class='content'><td class='content' align=center valign=middle>";
        echo "<b class='content' style='font-weight:bold'>Herzlichen Dank</b><br>";
        echo "Wir haben Ihre Mitteilung erhalten.";
        echo '<br><br><A href="javascript:window.close();" class="content" ';
        echo 'style="text-decoration:'.getcssarg("main_link_d").';
        color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").';
        font-weight:'.getcssarg("main_link_w").'"
        >Fenster schliessen</a>';
        echo "</td></tr>";
        echo "</table>";
    }
    else {
?>
  <script language="JavaScript">
  <!--

  function chkFormular() {

      // Nachname Check
      if(document.Formular.Name.value == "") {
          alert("Bitte einen Nachnamen eingeben!");
          document.Formular.Name.focus();
          return false;
      }
      // Vorname Check
      if(document.Formular.Vorname.value == "") {
          alert("Bitte einen Vornamen eingeben!");
          document.Formular.Vorname.focus();
          return false;
      }
      // Bemerkung Check
      if(document.Formular.Bemerkung.value == "") {
          alert("Bitte eine Bemerkung eingeben!");
          document.Formular.Bemerkung.focus();
          return false;
      }
      // E-Mail Adressen Check
      var ok = 1;
      var email = document.Formular.Email.value;
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
          document.Formular.Email.focus();
          return false;
      }
  }
  //-->
  </script>

    <table class='content' border="0" cellpadding="0" cellspacing="5">
      <tr class='content'>
        <td class='content'>
          <b class='content' style='font-weight:bold'><?php echo $shopadresse[0]; ?></b>
        </td>
      </tr>
      <tr>
        <td class='content'>
          <p>Haben Sie Fragen oder Anregungen zu unserem Onlineshop? Bitte benutzen Sie untenstehendes Kontaktformular, um mit uns in Verbindung zu treten.</p>
        </td>
      </tr>
    </table>
    <table class='content' border="0" cellpadding="0" cellspacing="5">
      <tr>
        <td class='content' colspan=4>
          <p><b class='content' style='font-weight:bold'>Kontaktadresse</b></p>
        </td>
      </tr>
      <tr>
        <td class='content' valign=top>
<?php
    if ($shopadresse[0] != "") { echo $shopadresse[0]."<br>"; };
    if ($shopadresse[1] != "") { echo $shopadresse[1]."<br>"; };
    if ($shopadresse[2] != "") { echo $shopadresse[2]."<br>"; };
    if ($shopadresse[3] != "") { echo $shopadresse[3]."<br>"; };
?>
        </td>
        <td class='content' valign=top>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </td>
        <td class='content' valign=top>
<?php
    if ($shopadresse[4] != "") { echo "E-Mail: <br>"; };
    if ($shopadresse[5] != "") { echo "Tel. <br>"; };
    if ($shopadresse[6] != "") { echo "Fax. <br>"; };
?>
        </td>
        <td class='content' valign=top>
<?php
    if ($shopadresse[4] != "") { echo $shopadresse[4]."&nbsp;&nbsp;<br>"; };
    if ($shopadresse[5] != "") { echo $shopadresse[5]."&nbsp;&nbsp;<br>"; };
    if ($shopadresse[6] != "") { echo $shopadresse[6]."&nbsp;&nbsp;<br>"; };
?>
        </td>

      </tr>
    </table>
    <form method=post action='<?php echo $PHP_SELF; ?>' title='Formular' name="Formular" onSubmit="return chkFormular()">
      <table class='content' border="0" cellpadding="0" cellspacing="5">
        <tr>
          <td class='content' colspan=5>
            <p><b class='content' style='font-weight:bold'><?php if ($subject == "") {echo "Feedback";} else {echo $subject;} ?></b></p>
          </td>
        </tr>
        <tr>
          <td class='content'>
            Name:
          </td>
          <td class='content'>
            <input type="text" size=20 name="Name" maxlength=50>
          </td>
          <td class='content'>
            &nbsp;&nbsp;
          </td>
          <td class='content'>
            Vorname:
          </td>
          <td class='content'>
            <input type="text" size=20 name="Vorname" maxlength=50>
          </td>
        </tr>
        <tr>
          <td class='content'>
            E-Mail:
          </td>
          <td class='content'>
            <input type="text" size=20 name="Email" maxlength=50>
          </td>
          <td class='content'>
            &nbsp;&nbsp;
          </td>
          <td class='content'>
            Tel.
          </td>
          <td class='content'>
            <input type="text" size=20 name="Tel" maxlength=50>
          </td>
        </tr>
        <tr>
          <td class='content' valign=top>
            Bemerkung:
          </td>
          <td class='content' colspan=4>
            <textarea style='font-family: Courier, Courier New, Monaco' name="Bemerkung" cols="40" rows="6" wrap=physical></textarea><input type="hidden" name="version" value="<?php echo getshopversion(); ?>">
          </td>
        </tr>
      </table>
      <center>
        <input type="hidden" name="darstellen" value="1">
        <input type="hidden" name="subject" value="<?php echo $subject ?>">
        <input type="image" src="Buttons/bt_weiter.gif" border="0" alt="Absenden" title="Absenden">
      </center>
    </form>
<?php
    } // end of else
?>

  </body>
</html>

<?php

  // End of file-----------------------------------------------------------------------
?>
