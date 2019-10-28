<?php
  // Filename: tell_a_friend.php
  //
  // Modul: Darstellen - Marketing / Weiterempfehlungen
  //
  // Autoren: Ralph Grad, José Fontanil & Reto Glanzmann
  //
  // Zweck: Stellt ein Formular zur Eingabe von einem Freund/Freundin dar
  //        um ihm/ihr einen Link mit Produktinformationen zu senden.
  //        Anmerkung: Diese Datei arbeitet mit mime_mail_def.php zusammen.
  //
  // Sicherheitsstufe:             *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: tell_a_friend.php,v 1.13 2003/07/13 15:27:37 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $tell_a_friend = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}
  if (!isset($initialize)) {include("initialize.php");}

  // ---------------------------------------------------------------------------------------------------
  //Absender-Email
  $email_from_mail = getShopEmail(); // Funktionsdefinition in <shopdir>/shop/USER_ARTIKEL_HANDLING.php
  //Absender-Name
  $email_from_name = getshopname(); // Funktionsdefinition in <shopdir>/shop/USER_ARTIKEL_HANDLING.php
  //Betreff in der Mail (Template, wird unten ueberschrieben)
  $email_betreff = "Ein Freund empfiehlt dir den Artikel $Artikelname von $email_from_name!";
  //BCC
  $email_to_bcc = get_tell_a_friend_bcc(); // Funktionsdefinition in <shopdir>/shop/USER_ARTIKEL_HANDLING.php

  // Absoluter Pfad und URI zum Shop-root Verzeichnis
  $shop_root_path = getShopRootPath();
  $shop_uri = getShopRootPath(true);

  // Cascading Style Sheet Angaben fuer den Link (Weitere Informationen) auslesen und in Variable abpacken:
  $cssargumente = ' style="text-decoration:'.getcssarg("main_link_d").';';
  $cssargumente.= ' color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").';';
  $cssargumente.= ' font-size:'.getcssarg("main_link_s").'; font-weight:'.getcssarg("main_link_w").'"';
  // Cascading Style Sheet Angaben fuer den aktiven Link (z.B. Blaettern) nie unterstreichen!:
  $cssargaktiv = ' style="text-decoration:none; color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").';';
  $cssargaktiv.= ' font-size:'.getcssarg("main_link_s").'; font-weight:'.getcssarg("main_link_w").'"';

  // ---------------------------------------------------------------------------------------------------
  // Ausgabe des HTML-Headers
  echo "  <html>\n";
  echo "    <head>\n";
  echo "      <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
  echo "      <meta http-equiv=\"content-language\" content=\"de\">\n";
  echo "      <meta name=\"author\" content=\"Ralf Grad, Jos&eacute; Fontanil and Reto Glanzmann\">\n";
  echo "      <title>".$email_from_name."</title>\n";
  echo "      <link rel=\"stylesheet\" href=\"shopstyles.css\" type=\"text/css\">\n";
  echo "      <script language=\"JavaScript\" type=\"text/javascript\">\n";
  echo "        <!--\n";
  echo "          function chkFormular() {\n";
  echo "            // ueberpruefen, ob der Name des Absenders / der Absenderin angegeben wurde\n";
  echo "            Name = document.Formular.name_absender.value;\n";
  echo "            if(Name == \"\") {\n";
  echo "                alert(\"Der Name des Absenders muss noch angegeben werden!\");\n";
  echo "                document.Formular.name_absender.focus();\n";
  echo "                return false;\n";
  echo "            }\n";
  echo "            // ueberpruefen, ob der Name des Freundes / der Freundin angegeben wurde\n";
  echo "            Name = document.Formular.name_freund.value;\n";
  echo "            if(Name == \"\") {\n";
  echo "                alert(\"Der Name des Empfängers muss noch angegeben werden!\");\n";
  echo "                document.Formular.name_freund.focus();\n";
  echo "                return false;\n";
  echo "            }\n";
  echo "            // ueberpruefen, ob die E-Mail Adresse angegeben wurde\n";
  echo "            var ok = 1;\n";
  echo "            var email = document.Formular.email_freund.value;\n";
  echo "            var geteilt = email.split (\"@\");\n";
  echo "            // falls mehr als ein oder gar kein '@' im string\n";
  echo "            if (geteilt.length != 2){\n";
  echo "                ok = 0;\n";
  echo "            }\n";
  echo "            else{\n";
  echo "                // falls vor oder nach dem '@' nichts mehr kommt\n";
  echo "                if (geteilt[0] == \"\" || geteilt[1] == \"\" ) { ok = 0; }\n";
  echo "                // falls nach dem '@' kein Punkt mehr kommt\n";
  echo "                if (geteilt[1].indexOf(\".\") == \"-1\" ) { ok = 0; }\n";
  echo "                // falls direkt nach dem '@' oder ganz am Schluss ein Punkt kommt\n";
  echo "                var laenge = geteilt[1].length;\n";
  echo "                if (geteilt[1].indexOf(\".\") == \"0\" || geteilt[1].charAt(laenge-1) == \".\") { ok = 0; }\n";
  echo "                // falls direkt vor dem '@' oder am Anfang ein Punkt kommt\n";
  echo "                var laenge = geteilt[0].length;\n";
  echo "                if (geteilt[0].indexOf(\".\") == \"0\" || geteilt[0].charAt(laenge-1) == \".\") { ok = 0; }\n";
  echo "            }\n";
  echo "            if (ok == 0){\n";
  echo "                alert (\"Die angegebene E-Mail-Adresse ist nicht gültig!\");\n";
  echo "                document.Formular.email_freund.focus();\n";
  echo "                return false;\n";
  echo "            }\n";
  echo "            Kommentar = document.Formular.kommentar.value;\n";
  echo "            if(Kommentar == \"\") {\n";
  echo "                alert(\"Bitte einen aussagekräftigeren Kommentar angeben!\");\n";
  echo "                document.Formular.kommentar.focus();\n";
  echo "                return false;\n";
  echo "            }\n";
  echo "          }\n";
  echo "        // -->\n";
  echo "      </script>\n";
  echo "  </head>\n";
  echo "  <body class=\"content\">\n";
  echo "   <div align=\"center\">\n";
  echo "   <h1>Diesen Artikel weiterempfehlen</h1>\n";
  echo "   <h4>[ $Artikelname ]</h4>\n";

  // ---------------------------------------------------------------------------------------------------
  // Formular zum ausfuellen (Angabe an wen zu senden u.s.w.)
  if(!isset($email_freund)){
      echo "<form action=\"tell_a_friend.php\" method=\"post\" name=\"Formular\" onSubmit=\"return chkFormular()\">\n";
      echo "  <font face=\"Arial\">\n";
      echo "   <table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"0\">\n";
      echo "    <tr>\n";
      echo "     <td valign=\"top\" colspan=\"2\">\n";
      echo "      <hr noshade size=\"1\">\n";
      echo "     </td>\n";
      echo "    </tr>\n";
      echo "    <tr>\n";
      echo "     <td valign=\"top\">\n";
      echo "      <br>Name des Absenders:<br>\n";
      echo "     </td>\n";
      echo "     <td>\n";
      echo "      <input type=\"text\" name=\"name_absender\" size=\"30\">\n";
      echo "     </td>\n";
      echo "    </tr>\n";
      echo "    <tr>\n";
      echo "     <td valign=\"top\">\n";
      echo "      <br>Empf&auml;nger:<br>\n";
      echo "     </td>\n";
      echo "     <td>\n";
      echo "      <input type=\"text\" name=\"name_freund\" size=\"30\">\n";
      echo "     </td>\n";
      echo "    </tr>\n";
      echo "    <tr>\n";
      echo "     <td valign=\"top\">\n";
      echo "      <br>E-Mail des Empf&auml;ngers:<br>\n";
      echo "     </td>\n";
      echo "     <td>\n";
      echo "      <input type=\"text\" name=\"email_freund\" size=\"30\">\n";
      echo "     </td>\n";
      echo "    </tr>\n";
      echo "    <tr>\n";
      echo "     <td width=\"28%\" valign=\"top\">\n";
      echo "      <br>Nachricht an den Empf&auml;nger:\n";
      echo "     </td>\n";
      echo "     <td width=\"72%\">\n";
      echo "      <textarea cols=\"30\" rows=\"6\" name=\"kommentar\">Ich habe diesen Artikel bei ".$email_from_name." gesehen und dachte, er interessiert dich vielleicht.</textarea>\n";
      echo '      <input type="hidden" name="link" value="'.$shop_uri.'/../index.php?Kategorie_ID='.$Kategorie_ID.'&amp;Artikel_ID='.$Artikel_ID.'">'."\n";
      echo '      <input type="hidden" name="Artikelname" value="'.$Artikelname.'">'."\n";
      echo "     </td>\n";
      echo "    </tr>\n";
      echo "   </table>\n";
      echo "   <table  width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"0\">\n";
      echo "    <tr>\n";
      echo "     <td align=\"center\" valign=\"top\" colspan=\"2\">\n<br>\n<hr noshade size=\"1\">\n<br>\n";
      echo "      <br><input type=\"submit\" value=\"Empfehlung senden\" name=\"submit\">\n";
      echo "     </td>\n";
      echo "    </tr>\n";
      echo "   </table>\n";
      echo "  </font>\n";
      echo "</form>\n";
      echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
      echo "  <!--\n";
      echo "  document.Formular.name_absender.focus();\n";
      echo "  // -->\n";
      echo "</script>\n";
  }
  // ---------------------------------------------------------------------------------------------------
  // Verarbeitung des ausgefuellten Formulars
  else {
      // Werte, welche vom Formular her uebertragen wurden in E-Mail Variable ablegen
      $email_to = $name_freund." <".$email_freund.">";
      $show = getcssarg("top_left");

      // Nun folgt der HTML-Text des zu versendenden E-Mails
      $htmlbody = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
      <html>
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="content-language" content="de">
        <meta name="author" content="Jos&eacute; Fontanil, Reto Glanzmann und Ralf Grad">
        <title>'.htmlentities(stripslashes($name_absender)).' empfiehlt dir einen Artikel aus '.htmlentities($email_from_name).'!</title>
        <link rel="stylesheet" href="'.$shop_uri.'/shopstyles.css" type="text/css">
      </head>
      <body class="content">
       <table cellpadding="4" cellspacing="0" border="0" width="100%">
        <tr>
         <td>';
      if ($show == "shopname") $htmlbody.="\n".'          '.htmlentities($email_from_name)."\n";
      if ($show == "shoplogo") $htmlbody.="\n".'          '."<img src=\"".$shop_uri."/Bilder/shoplogo.".getcssarg("logo_bg_img_typ")."\" border=\"0\" alt=\"$email_from_name\">\n";
      $htmlbody.= '          <p>Hallo '.htmlentities(stripslashes($name_freund)).'</p>
          <p>'.htmlentities(stripslashes($name_absender)).' empfiehlt den Artikel: <b>'.$Artikelname.'</b> von <a class="content" '.$cssargumente.' href="'.$shop_uri.'/index.html">'.$email_from_name.'</a>.</p>
          <p><a class="content" '.$cssargumente.' href="'.str_replace('&','&amp;',$link).'">Direkt zum Artikel gehen</a>
          <p>Nachricht von '.htmlentities(stripslashes($name_absender)).':</p>
          <i>'.htmlentities(stripslashes($kommentar)).'</i>
         </td>
        </tr>
       </table>
      </body>
      </html>';// Ende HTML E-Mail Body

    // ...und der Plaintext, welcher auch mitversandt wird
    $plaintextbody = stripslashes($name_absender).' empfiehlt dir einen Artikel aus '.$email_from_name.'!'."\r\n\r\n";
    $plaintextbody.= 'Hallo '.stripslashes($name_freund)."\r\n\r\n";
    $plaintextbody.= stripslashes($name_absender).' empfiehlt den Artikel: '.$Artikelname.' von '.$email_from_name.'.'."\r\n\r\n";
    $plaintextbody.= $link.' (Link zum Artikel)'."\r\n\r\n";
    $plaintextbody.= 'Nachricht von '.stripslashes($name_absender).':'."\r\n\r\n";
    $plaintextbody.= stripslashes($kommentar)."\r\n\r\n";
    $plaintextbody.= stripslashes($name_absender)."\r\n";

    // HTML E-Mail vorbereiten (Klassendefinition HTML_Email ist in <shopdir>/shop/mime_mail_def.php zu finden):
    $from = $email_from_name."<".$email_from_mail.">";
    $to = $email_to;
    $bcc = $email_to_bcc;
    $subject = stripslashes($name_absender)." empfiehlt den Artikel '$Artikelname' von $email_from_name!";
    $my_mail = new HTML_Email($to, $from, $subject, $bcc);
    $my_mail->html_data(preg_replace('°^ *°','',$htmlbody));
    $my_mail->plain_data($plaintextbody);
    // E-Mail absenden
    if (!$my_mail->send()) {
        // Fehlermeldung ausgeben, wenn der Mailversand nicht geklappt hat
        echo "<p><b>Aufgrund eines Mailversandfehlers konnte die Artikelempfehlung leider nicht versendet werden.</b></p><br>";
    }
    else {
        // Erfolgsmeldung anzeigen, wenn die Empfehlung abgesendet werden konnte
        echo "<p><b>Die Empfehlung wurde an ".htmlentities(stripslashes($name_freund))." <$email_freund> gesendet</b></p>";
    }

    echo "<p><button name=\"button_close\" type=\"button\" value=\"button_close\" onClick=\"window.close()\">Dieses Fenster schliessen</button></p>\n";
  }// End else !isset(email...)

  // ------------------------------------------------------------------------------------------------
  // HTML-Footer ausgeben
  echo "  </div>\n";
  echo "  </body>\n";
  echo "  </html>\n";

// End of file ---------------------------------------------------------------------------------------
?>
