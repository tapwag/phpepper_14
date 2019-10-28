<?php
  // Filename: SHOP_LAYOUT.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Funktionen um das Layout des Shops zu konfigurieren
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_LAYOUT.php,v 1.34 2003/05/24 18:41:34 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_LAYOUT = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
  if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);
  extract($_FILES);

  // HTML-Kopf, der bei jedem Aufruf des Files ausgegeben wird
?>
 <HTML>
  <HEAD>
      <TITLE>Layoutmanagement</TITLE>
      <META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
      <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
      <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
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
  // darstellen = 10: speichert die veraenderten Layout-Parameter in die Datenbank und ruft die Funktionen zur
  // erzeugung der CSS-Files sowie von index.php (beinhaltet Frameset) auf.
  if ($darstellen == 10){
      // fontseteinstellungen in Datenbank speichern (fontset_1 .. fontset_5). Ist bei einem Fontsetfeld keine
      // Schriftart eingegeben worden, so wird ein leerer String in die Datenbank gespeichert. Wurde eine Schrift-
      // art eingegeben, wird dem String noch ein Komma angehaengt (nicht bei fontset_5, da es das letzte Element
      // der Aufzaehlungsliste ist)
      if ($fontset_1 != "") updatecssarg("fontset_1", $fontset_1.",");
      else updatecssarg("fontset_1", "");
      if ($fontset_2 != "") updatecssarg("fontset_2", $fontset_2.",");
      else updatecssarg("fontset_2", "");
      if ($fontset_3 != "") updatecssarg("fontset_3", $fontset_3.",");
      else updatecssarg("fontset_3", "");
      if ($fontset_4 != "") updatecssarg("fontset_4", $fontset_4.",");
      else updatecssarg("fontset_4", "");
      updatecssarg("fontset_5", $fontset_5);

      // Hintergrundfarben fuer die drei Frames in DB speichern
      updatecssarg("top_bg_c", rgbdechex($top_bg_c));
      updatecssarg("left_bg_c", rgbdechex($left_bg_c));
      updatecssarg("main_bg_c", rgbdechex($main_bg_c));

      // Wenn Hintergrundbild gewuenscht wird, Typ ermittlen und Dateiextension ergaenzen, Hintergrundbildstring
      // komplett in DB speichern
      if ($main_bg_img == "ja") updatecssarg("main_bg_img", "background-image:url(Bilder/bg_main.".getcssarg("main_bg_img_typ").");");
      else updatecssarg("main_bg_img", "");
      if ($left_bg_img == "ja") updatecssarg("left_bg_img", "background-image:url(Bilder/bg_left.".getcssarg("left_bg_img_typ").");");
      else updatecssarg("left_bg_img", "");
      if ($top_bg_img == "ja") updatecssarg("top_bg_img", "background-image:url(Bilder/bg_top.".getcssarg("top_bg_img_typ").");");
      else updatecssarg("top_bg_img", "");

      // Hoehe des Topframes und Breite des Left-Frames in DB speichern
      updatecssarg("top_height", $top_height);
      updatecssarg("left_width", $left_width);

      // Argument, was oben links im Shop angezeigt werden soll (Shoplogo, -name, nichts) in DB speichern
      updatecssarg("top_left", $top_left);
      // Flag, ob Administrationsstern angezeigt werden soll, in DB speichern
      updatecssarg("admin_stern", $admin_stern);

      // Einstellungen fuer die veraenderbaren Schriftarten in Datenbank speichern
      savefont("top_font", $top_font_c, $top_font_w, $top_font_s, $top_font_d, $top_font_i);
      savefont("top_stern", $top_stern_c, $top_stern_w, $top_stern_s, $top_stern_d, $top_stern_i);
      savefont("left_font", $left_font_c, $left_font_w, $left_font_s, $left_font_d, $left_font_i);
      savefont("left_font_hover", $left_font_hover_c, $left_font_hover_w, $left_font_hover_s, $left_font_hover_d, $left_font_hover_i);
      savefont("main_font", $main_font_c, $main_font_w, $main_font_s, $main_font_d, $main_font_i);
      savefont("main_link", $main_link_c, $main_link_w, $main_link_s, $main_link_d, $main_link_i);
      savefont("main_h1", $main_h1_c, $main_h1_w, $main_h1_s, $main_h1_d, $main_h1_i);
      savefont("main_h2", $main_h2_c, $main_h2_w, $main_h2_s, $main_h2_d, $main_h2_i);
      savefont("main_h3", $main_h3_c, $main_h3_w, $main_h3_s, $main_h3_d, $main_h3_i);
      savefont("main_h4", $main_h4_c, $main_h4_w, $main_h4_s, $main_h4_d, $main_h4_i);
      savefont("main_h5", $main_h5_c, $main_h5_w, $main_h5_s, $main_h5_d, $main_h5_i);

      // Funktion aufrufen, welche die CSS-Files erstellt
      $ok1 = mkcssfiles();
      // Funktion aufrufen, welche das index.php (Frameset) File erstellt
      $ok2 = mkindexphp();

      if ($ok1 == true && $ok2 = true){
        echo "<p><h1><b>SHOP ADMINISTRATION</b></h1></p>";
        echo "<h4>Das Speichern aller Layout-Settings war erfolgreich!<h4><br>";
      }
      else echo "<h4>Fehler beim Speichern der Einstellungen!</h4>";
      echo "<a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'alt='weiter'></a>";

  } // end of if darstellen == 10


  // darstellen = 20: gibt ein Formular aus, wo man per Browsebutton eine Bildatei auf der Festplatte auswaehlen
  // kann, die dann als eines der folgenden Bilder hochgeladen wird: Shoplogo, Hintergrundbild linkes, oberes oder
  // Hauptframe.
  elseif ($darstellen == 20){
  ?>
    <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
    <table border=0>
    <tr><td>
      Wählen sie den Verwendungszweck für das Bild, welches Sie hochladen wollen:
    </td></tr><tr><td>
      <form method="post" action="<?php echo $PHP_SELF; ?>?darstellen=21" enctype="multipart/form-data">
        <input type="radio" value="shoplogo" name="zweck" checked>Shoplogo (wird immer oben links angezeigt)<br>
        <input type="radio" value="mainframe" name="zweck">Hintergrundbild für Main-Frame (Hauptframe, wo die Artikel angezeigt werden)<br>
        <input type="radio" value="leftframe" name="zweck">Hintergrundbild für Left-Frame (linkes Frame, für die Anzeige der Kategorien)<br>
        <input type="radio" value="topframe" name="zweck">Hintergrundbild für Top-Frame (oberes Frame)<br><br>
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
    </td></tr><tr><td>
        Bild (gif, jpg oder png) zum hochladen (mit "Durchsuchen/Browse"<br>kann ein Bild auf der Festplatte ausgewählt werden):<br>
        <input type="file" name="bg_pic" size="40"><br><br>
    </td></tr><tr><td>
        <input type="image" src="../Buttons/bt_bild_hochladen_admin.gif" border=0>
        <a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='abbrechen'></a>
      </form>
    </table>
  <?php
  } // end of elseif darstellen == 20


  // darstellen = 21: ist die Folgeseite von darstellen=20. Analysiert den Dateinamen der hochgeladene Datei
  // handelt es sich um eine Datei vom Typ .gif, .jpg oder .png so wird sie in die entsprechende Bilddatei
  // im Verzeichnis "Bilder" hineinkopiert.
  elseif ($darstellen == 21){
    // filename-string in kleinbuchstaben umwandeln und die letzten drei Buchstaben rauskopieren
    $file_ext = substr(strtolower($bg_pic['name']),strlen($bg_pic['name'])-3,3);
    // falls es sich um ein gif, jpeg oder png-file handelt..
    if ($file_ext == "gif" || $file_ext == "jpg" || $file_ext == "png" ) {
      // zur Kontrolle, ob File per http-post hochgeladen wurde..
      if (!is_uploaded_file($bg_pic['tmp_name'])) {
        echo "<h4>Die Datei wurde nicht korrekt hochgeladen!</h4>";
        echo "<a href=".$PHP_SELF."?darstellen=20><img src=../Buttons/bt_zurueck_admin.gif border=0></a>";
      } // end of if not is_uploaded_file
      else {

        // falls das hochgeladene Bild als Shoplogo verwendet werden soll..
        if ( $zweck == "shoplogo") {
          // file-Extension fuer Kopierdestinationspfad anhaengen (gif, jpg od. png)
          $dest_string = "../Bilder/shoplogo.".$file_ext;
          $dest = "logo_bg_img_typ";
        } // end of if $zweck == shoplogo

        // falls das hochgeladene Bild als Hintergrundbild fuer das Main-Frame verwendet werden soll..
        elseif ( $zweck == "mainframe") {
          // file-Extension fuer Kopierdestinationspfad anhaengen (gif, jpg od. png)
          $dest_string = "../Bilder/bg_main.".$file_ext;
          $dest = "main_bg_img_typ";
          // Einfuegestring fuer CSS-File erstellen
          $insert_string = "background-image:url(Bilder/bg_main.".$file_ext.");";
          // Destinationtag in DB
          $insert_id = "main_bg_img";
        } // end of elsif $zweck == mainframe

        // falls das hochgeladene Bild als Hintergrundbild fuer das Left-Frame verwendet werden soll..
        elseif ( $zweck == "leftframe") {
          // file-Extension fuer Kopierdestinationspfad anhaengen (gif, jpg od. png)
          $dest_string = "../Bilder/bg_left.".$file_ext;
          $dest = "left_bg_img_typ";
          // Einfuegestring fuer CSS-File erstellen
          $insert_string = "background-image:url(Bilder/bg_left.".$file_ext.");";
          // Destinationtag in DB
          $insert_id = "left_bg_img";
        } // end of elseif $zweck == leftframe

        // falls das hochgeladene Bild als Hintergrundbild fuer das Top-Frame verwendet werden soll..
        elseif ( $zweck == "topframe") {
          // file-Extension fuer Kopierdestinationspfad anhaengen (gif, jpg od. png)
          $dest_string = "../Bilder/bg_top.".$file_ext;
          $dest = "top_bg_img_typ";
          // Einfuegestring fuer CSS-File erstellen
          $insert_string = "background-image:url(Bilder/bg_top.".$file_ext.");";
          // Destinationtag in DB
          $insert_id = "top_bg_img";
        } // end of if $zweck == topframe

        else echo "<h1>schwerer Fehler!</h1>";

         // hochgeladenes Bild in seine Zieldatei kopieren (mit dem im letzten Schritt aufbereiteten
         // Kopierstring)
        if (!move_uploaded_file($bg_pic['tmp_name'], $dest_string)){
          echo "<h4>Das Bild konnte nicht erstellt werden!</h4>";
          echo "<a href=".$PHP_SELF."?darstellen=20><img src=../Buttons/bt_zurueck_admin.gif border=0></a>";
        }
        // falls hochgeladenes Bild erfolgreich kopiert werden konnte..
        else {

          // Versuch, einen chmod auf das hochgeladene Bild auszufuehren. Keine Fehlermeldung bei Misserfolg ausgeben (@)
          // falls PHP als CGI-Modul laeuft, kann der chmod-Wert auf 0644 geaendert werden
          @chmod($dest_string, 0666);

          // Bildtyp (gif, jpg od. png))fuer hochgeladenes Bild in Datenbank aktualisieren
          updatecssarg($dest, $file_ext);
          // fuer CSS-File aufbereiteter Hintergrundbildstring in Datenbank speichern
          updatecssarg($insert_id, $insert_string);

          // CSS-Files erstellen
          $ok = mkcssfiles();
          if ($ok == true){
              echo "<p><h1><b>SHOP ADMINISTRATION</b></h1></p>";
              echo "<h4>Das Bild wurde erfolgreich hochgeladen!</h4>";
          }
          else echo "<h4>Fehler beim Erzeugen von index.php!</h4>";
          echo "<a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'alt='weiter'></a>";

        } // end of else if not move_uploaded_file
      } // end of else if not is_uploaded_file
    } // end of if gif, jpeg oder png
    else {
      echo "<h4>Die von Ihnen angegebene Datei ist kein gültiges Bildformat (gif,jpg oder png)!</h4>";
      echo "<a href=".$PHP_SELF."?darstellen=20><img src=../Buttons/bt_zurueck_admin.gif border=0></a>";
    }
  } // end of if darstellen == 21


  // darstellen = 20: gibt ein Formular aus, wo man per Browsebutton eine Bildatei auf der Festplatte auswaehlen
  // kann, die mit der man einen Shopbutton ersetzen kann
  elseif ($darstellen == 30){
  ?>
    <center>
    <table border=0 width=80%>
      <tr><td colspan=4>
        <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
      </td></tr><tr><td colspan=4>
      Wählen Sie eine GIF-Grafikdatei(.gif) auf Ihrer Festplatte und danach, welchen Shop-Button (Knopf) sie damit ersetzen wollen:
      </td></tr>
      <form method="post" action="<?php echo $PHP_SELF; ?>?darstellen=31" enctype="multipart/form-data">
        <tr><td colspan=4>&nbsp;</td></tr>
        <tr><td colspan=4>
          mit "Browse/Durchsuchen" kann ein Bild auf der Festplatte ausgewählt werden (nur GIF-Dateien!)<br>
          <input type="file" name="bg_pic" size="40"><br><br>
          <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
        </td></tr>
        <tr><td colspan=4>&nbsp;</td></tr>
        <tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_bestellung_absenden.gif" name="zweck" checked>&nbsp;</td><td><nobr>Bestellung absenden</nobr></td><td><img src=../Buttons/bt_bestellung_absenden.gif border=0><br>
        </td><td rowspan=13>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
        <tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_hilfe.gif" name="zweck">&nbsp;</td><td><nobr>Hilfe</nobr></td><td><img src=../Buttons/bt_hilfe.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_suchen.gif" name="zweck">&nbsp;</td><td><nobr>Artikel suchen</nobr></td><td><img src=../Buttons/bt_suchen.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_warenkorb_zeigen.gif" name="zweck">&nbsp;</td><td><nobr>Warenkorb anzeigen</nobr></td><td><img src=../Buttons/bt_warenkorb_zeigen.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_artikel_anzeigen.gif" name="zweck">&nbsp;</td><td><nobr>Artikel anzeigen (nach Suche)</nobr></td><td><img src=../Buttons/bt_artikel_anzeigen.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_weiter.gif" name="zweck">&nbsp;</td><td><nobr>weiter</nobr></td><td><img src=../Buttons/bt_weiter.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_zur_kasse_1.gif" name="zweck">&nbsp;</td><td><nobr>zur Kasse</nobr></td><td><img src=../Buttons/bt_zur_kasse_1.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_zurueck.gif" name="zweck">&nbsp;</td><td><nobr>zur&uuml;ck</nobr></td><td><img src=../Buttons/bt_zurueck.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_in_warenkorb.gif" name="zweck">&nbsp;</td><td><nobr>In den Warenkorb legen</nobr></td><td><img src=../Buttons/bt_in_warenkorb.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Buttons/bt_loeschen.gif" name="zweck">&nbsp;</td><td><nobr>Artikel aus Warenkorb l&ouml;schen&nbsp;</nobr></td><td><img src=../Buttons/bt_loeschen.gif border=0><br>
        </td></tr>
        <tr><td colspan=3>&nbsp;</td></tr>
        <tr valign=middle><td>
          <input type="radio" value="../Bilder/kat_leer.gif" name="zweck">&nbsp;</td><td><nobr>Kategorie leer</nobr></td><td><img src=../Bilder/kat_leer.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Bilder/kat_minus.gif" name="zweck">&nbsp;</td><td><nobr>Kategorie schliessen</nobr></td><td><img src=../Bilder/kat_minus.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Bilder/kat_plus.gif" name="zweck">&nbsp;</td><td><nobr>Kategorie &ouml;ffnen</nobr></td><td><img src=../Bilder/kat_plus.gif border=0><br>
        </td></tr><tr valign=middle><td>
          <input type="radio" value="../Bilder/kat_selected.gif" name="zweck">&nbsp;</td><td><nobr>Kategorie ausgew&auml;hlt</nobr></td><td><img src=../Bilder/kat_selected.gif border=0><br>
        </td></tr>
        <tr><td colspan=4>&nbsp;</td></tr>
        <tr><td colspan=4>
          <input type="image" src="../Buttons/bt_bild_hochladen_admin.gif" border=0>
          <a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='abbrechen'></a>
        </td></tr>
      </form>
    </table>
    </center>
  <?php
  } // end of elseif darstellen == 30


  // darstellen = 31: ist die Folgeseite von darstellen=30. Analysiert den Dateinamen der hochgeladene Datei
  // handelt es sich um eine Datei vom Typ .gif, so wird der gewaehlte Button ersetzt
  elseif ($darstellen == 31){
    // filename-string in kleinbuchstaben umwandeln und die letzten drei Buchstaben rauskopieren
    $file_ext = substr(strtolower($bg_pic['name']),strlen($bg_pic['name'])-3,3);
    // falls es sich um ein gif, jpeg oder png-file handelt..
    if ($file_ext == "gif") {
      // zur Kontrolle, ob File per http-post hochgeladen wurde..
      if (!is_uploaded_file($bg_pic['tmp_name'])) {
        echo "<h4>Die Datei wurde nicht korrekt hochgeladen!</h4>";
        echo "<a href=".$PHP_SELF."?darstellen=30><img src=../Buttons/bt_zurueck_admin.gif border=0></a>";
      } // end of if not is_uploaded_file
      else {

     // String zusammenbauen, welches Bild ersetzt werden soll
     $dest_string = $zweck;

     // hochgeladenes Bild in seine Zieldatei kopieren (mit dem im letzten Schritt aufbereiteten
     // Kopierstring)
     if (!move_uploaded_file($bg_pic['tmp_name'], $dest_string)){
         echo "<p><h1><b>SHOP ADMINISTRATION</b></h1></p>";
         echo "<h4>Das Bild konnte nicht erstellt werden!</h4>";
         echo "<a href=\"".$PHP_SELF."?darstellen=30\"><img src=../Buttons/bt_zurueck_admin.gif border=0></a>";
     }

     // falls hochgeladenes Bild erfolgreich kopiert werden konnte..
     else {

          echo "<p><h1><b>SHOP ADMINISTRATION</b></h1></p>";
          echo "<p>Das Bild wurde korrekt hochgeladen!</p><br>";
          echo "<a href=\"".$PHP_SELF."?darstellen=30\"><img src=../Buttons/bt_weiter_admin.gif border=0></a>";
          }

        } // end of else if not move_uploaded_file
      } // end of else if not is_uploaded_file

    else {
      echo "<p><h1><b>SHOP ADMINISTRATION</b></h1></p>";
      echo "<h4>Die von Ihnen angegebene Datei ist kein gültiges GIF-Bildformat!</h4>";
      echo "<a href=".$PHP_SELF."?darstellen=30><img src=../Buttons/bt_zurueck_admin.gif border=0></a>";
    }
  } // end of if darstellen == 31



  // wird ausgefuehrt, wenn $darstellen nicht 10, 20, 21, 30 oder 31 ist (z.B beim erstmaligen Aufruf ohne Paramenter)
  // Gibt die Eingabemaske fuer die Layout Settings als Formular aus (mit den momentan Eingestellten Werten
  // aus der Datenbank eingefuellt!)
  else {
  ?>
    <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
    <p><h3><b>Layout Management</b></h3></p>
    <p>In dieser Maske k&ouml;nnen Sie das Aussehen ihres Shops einstellen. Mit den Eingabefeldern R, G und B&nbsp;
    definieren Sie die Farbe nach dem Muster  <b>R</b>ot, <b>G</b>r&uuml;n, <b>B</b>lau.
    Sie k&ouml;nnen in jedes der Farbfelder einen Wert zwischen 0 und 255 eingeben (z.B. weiss: R:255 G:255
    B:255;&nbsp;schwarz: R:0 G:0 B:0).</p>
    <p>Sie haben f&uuml;nf Eingabefelder f&uuml;r das Fontset zur Verf&uuml;gung. Geben Sie in dem Feld &quot;
    1. Priorit&auml;t&quot; die Schriftart  ein, die als erste Priorit&auml;t zur Anzeige des Shops verwendet
    werden soll. &quot;2. Priorit&auml;t&quot; ist die Schriftart, die verwendet wird, wenn der Surfer die
    unter &quot;1. Priorit&auml;t&quot; eingetragene Schriftart auf seinem Computer nicht installiert hat.
    Und so weiter bis zur 5. Priorit&auml;t.</p>

    <form name="Shop_Settings" action="<?php echo $PHP_SELF; ?>?darstellen=10" method="post" title="Shop_Settings">
      <table border="2" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td colspan="2">
            <p><b>Einstellungen Top-Frame</b></p>
            <table border=0 cellpadding=0 cellspacing=10>
              <tr valign=top><td>
                Hintergrundfarbe:
              </td><td>
                <?php rgbshow("top_bg_c") ?>
              </td></tr>
              <tr valign=top><td>
                Hintergrundbild:
              </td><td>
                <input type=checkbox name='top_bg_img' value='ja'
                <?php if(getcssarg("top_bg_img") != "") echo "checked";?>>
              </td></tr>
              <tr valign=top><td>
                H&ouml;he Top-Frame:&nbsp;
              </td><td>
                <input type=text name="top_height" size=3 value="<?php echo getcssarg("top_height"); ?>">Pixel
              </td></tr>
              <tr valign=top><td>
                Was soll oben links angezeigt werden:
              </td><td>
                <?php $show = getcssarg("top_left"); ?>
                <input type="radio" value="shopname" name="top_left" <?php if ($show == "shopname") echo " checked"; ?>>Shopname<br>
                <input type="radio" value="shoplogo" name="top_left" <?php if ($show == "shoplogo") echo " checked"; ?>>Shoplogo<br>
                <input type="radio" value="nichts" name="top_left" <?php if ($show == "nichts") echo " checked"; ?>>nichts<br><br>
              </td></tr>
              <tr valign=top><td>
                Administrationsstern einblenden:<br> (neben den Knopf "Warenkorb")
              </td><td>
                <?php $show = getcssarg("admin_stern"); ?>
                <input type="radio" value="ja" name="admin_stern" <?php if ($show == "ja") echo " checked"; ?>>ja (Administrationsstern (*) anzeigen)<br>
                <input type="radio" value="nein" name="admin_stern" <?php if ($show == "nein") echo " checked"; ?>>nein
                (Administrationsmodus nur über "..shopname/shop/Admin/" aufrufbar!!!)<br>
              </td></tr>
            </table>
          </td>
        </tr>
        <tr valign=top>
          <td><p><b>Einstellungen Left-Frame</b></p>
            <table border=0 cellpadding=0 cellspacing=5>
              <tr valign=top><td>
                Hintergrundfarbe:
              </td><td>
                <?php rgbshow("left_bg_c") ?>
              </td></tr>
              <tr valign=top><td>
                Hintergrundbild:
              </td><td>
                <input type=checkbox name='left_bg_img' value='ja' <?php if(getcssarg("left_bg_img") != "") echo "checked";?>>
              </td></tr>
              <tr valign=top><td>
              Breite Left-Frame:&nbsp;
              </td><td>
                <input type=text name="left_width" size=3 value="<?php echo getcssarg("left_width"); ?>"> Pixel
              </td></tr>
            </table>
          </td>
          <td><p><b>Einstellungen Main-Frame</b></p>
            <table border=0 cellpadding=0 cellspacing=5>
              <tr valign=top><td>
                Hintergrundfarbe:
              </td><td>
                <?php rgbshow("main_bg_c") ?>
              </td></tr>
              <tr valign=top><td>
                Hintergrundbild:
              </td><td>
                <input type=checkbox name='main_bg_img' value='ja'<?php if(getcssarg("main_bg_img") != "") echo "checked";?>>
              </td></tr>
            </table>
          </td>
        </tr>
      </table>
      <br><hr>
      <table border=0 cellpadding=0 cellspacing=0>
        <tr><td colspan=2><b>Fontset Einstellungen:</b><br>&nbsp;</td></tr>
        <tr><td>1. Priorit&auml;t&nbsp;</td>
          <td><input type=text name=fontset_1 size=30 value="<?php echo getnocomma("fontset_1"); ?>">
        </td></tr>
        <tr><td>2. Priorit&auml;t:&nbsp;</td>
          <td><input type=text name=fontset_2 size=30 value="<?php echo getnocomma("fontset_2"); ?>">
        </td></tr>
        <tr><td>3. Priorit&auml;t:&nbsp;</td>
          <td><input type=text name=fontset_3 size=30 value="<?php echo getnocomma("fontset_3"); ?>">
        </td></tr>
        <tr><td>4. Priorit&auml;t:&nbsp;</td>
          <td><input type=text name=fontset_4 size=30 value="<?php echo getnocomma("fontset_4"); ?>">
        </td></tr>
        <tr><td>5. Priorit&auml;t:&nbsp;</td>
          <td><input type=text name=fontset_5 size=30 value="<?php echo getnocomma("fontset_5"); ?>">
        </td></tr>
      </table>

      <br><hr>
      <table border=0 cellpadding=0 cellspacing=0>
        <tr><td colspan=3><b>Top-Frame (oberer Fensterteil) Schrifteinstellungen:</b><br>&nbsp;</td></tr>
        <tr>
          <td><?php htmltext("top_font",1,1,1,1,1,"Shoptitel:")?></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td><?php htmltext("top_stern",1,1,1,1,1,"Admin-Stern(*):")?></td>
        </tr>
      </table>

      <br><hr>
      <table border=0 cellpadding=0 cellspacing=0>
        <tr><td colspan=3><b>Left-Frame (linker Fensterteil) Schrifteinstellungen:</b><br>&nbsp;</td></tr>
        <tr>
          <td><?php htmltext("left_font",1,1,1,1,1,"Kategorien und Unterkategorien:")?></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td><?php htmltext("left_font_hover",1,1,1,1,1,"Mausover -> Kategorien und Unterkategorien:")?></td>
        </tr>
      </table>

      <br><hr>
      <table border=0 cellpadding=0 cellspacing=0>
        <tr><td colspan=3><b>Main-Frame (Haupt-Fensterteil) Schrifteinstellungen:</b><br>&nbsp;</td></tr>
        <tr>
          <td><?php htmltext("main_font",1,1,1,1,1,"Normaler Text:")?></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td><?php htmltext("main_link",1,1,1,1,1,"Links:")?></td>
        <tr><td colspan=3>&nbsp;<td></tr>
        </tr><tr>
          <td><?php htmltext("main_h1",1,1,1,1,1,"&Uuml;berschrift 1 (gr&ouml;sste):")?></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td><?php htmltext("main_h2",1,1,1,1,1,"&Uuml;berschrift 2:")?></td>
        </tr>
        <tr><td colspan=3>&nbsp;<td></tr>
        </tr><tr>
          <td><?php htmltext("main_h3",1,1,1,1,1,"&Uuml;berschrift 3:")?></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td><?php htmltext("main_h4",1,1,1,1,1,"&Uuml;berschrift 4:")?></td>
        </tr>
        <tr><td colspan=3>&nbsp;<td></tr>
        </tr><tr>
          <td><?php htmltext("main_h5",1,1,1,1,1,"&Uuml;berschrift 5 (kleinste):")?></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr><td colspan=3>&nbsp;<td></tr>
      </table>

      <input type=image src="../Buttons/bt_speichern_admin.gif" border='0'>
      <a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='abbrechen'></a>
      <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Layout')" title="Hilfe">
      <img src="../Buttons/bt_hilfe_admin.gif" border="0" alt="Hilfe"></a>
    </form>
  <?php
  } // end of else

  // -----------------------------------------------------------------------
  // erstellt die CSS-Files (shopstyles.css) in den Verzeichnissen
  // shop, shop/Frameset und shop/Admin aus dem Templatefile csstemplate.txt
  // Tags, welche im Templatefile in Doppelklammern << >> stehen, werden durch
  // die entsprechenden Einträge in der Datenbank (Tabelle: cssfile)ersetzt.
  // Argumente: keine
  // Rueckgabewert: False, wenn ein Fehler aufgetreten ist
  function mkcssfiles(){
    // am Anfang ist alles in Ordnung
    $ok = true;
    // css-templatefile "csstemplate.txt" zum lesen oeffnen
    $fp_src = fopen("csstemplate.txt","r");
    // css-file "shopstyles.css" in verschiedenen Verzeichnissen zum Ueberschreiben oeffnen
    $fp_dest = fopen("../shopstyles.css","w");
    $fp_dest_2 = fopen("../Frameset/shopstyles.css","w");

    // wenn ../shopstyles.css erfolgreich zum Schreiben geoeffnet werden konnte..
    if ($fp_dest <>0){
        // wenn ../Frameset/shopstyles.css erfolgreich zum Schreiben geoeffnet werden konnte..
        if ($fp_dest_2 <>0){
            // wenn csstemplate.txt erfolgreich zum Lesen geoeffnet werden konnte..
            if ($fp_src <>0){
                // solange EOF nicht erreicht ist..
                while ($zeile = fgets($fp_src,4096)){
                    // Tags suchen, welche in Doppelklammern eingeschlossen sind (<<tag>>)
                    preg_match_all("|<{2,2}(.*)>{2,2}|U", $zeile, $output);
                    foreach($output[1] as $csstag){
                        $replace = "<<".$csstag.">>";
                        // CSS-Wert aus Datenbank auslesen!
                        $replacement = getcssarg($csstag); ;
                        // Tags in Doppelklammern durch den aus der Datenbank ausgelesenen
                        // Ausdruck ersetzen
                        $zeile = ereg_replace($replace, $replacement, $zeile);
                        // Ausnahmebehandlung beim Hintergrundbild. Da die Kompatibilitaet zu
                        // Netscape 4.7x es erfordert, dass man im Unterverzeichnis Frameset eine
                        // Kopie der Datei shopstyles.css haben muss, muessen wir den relativen
                        // Pfad zu den Hintergrundbildern abaendern
                        if ($csstag == "main_bg_img" || $csstag == "left_bg_img" || $csstag == "top_bg_img" || $csstag == "fontset_5") {
                            $zeile2 = ereg_replace("Bilder","../Bilder",$zeile);
                            $zeilezweinehmen = true;
                        }

                        else {
                            $zeile2 = $zeile;
                        }
                    } // end foreach
                    // veranderte Zeile in Zieldatei im shop-Verzeichnis schreiben
                    fputs($fp_dest, $zeile);
                    // veranderte Zeile in Zieldatei im shop/Frameset-Verzeichnis schreiben
                    if ($zeilezweinehmen) {
                        // Das Flag $zeilezweinehmen signalisiert, dass man einer Ersetzung fuer
                        // die shopstyles.css Datei im Verzeichnis Frameset vorgenommen hat. Dann wird
                        // dementsprechend die veraenderte Zeile benutzt
                        $zeilezweinehmen = false;
                        fputs($fp_dest_2, $zeile2);
                    }
                    else {
                        fputs($fp_dest_2, $zeile);
                    }
                } // end of while $zeile
                fclose ($fp_src);   // template-file schliessen
            } // end of if $fp_src
            else{
              echo "CSS-Template-Datei shop/Admin/csstemplate.txt konnte nicht gelesen werden!";
              $ok = false;
            }
            fclose ($fp_dest_2);  // css-file schliessen
        } // end of if $fp_dest_2

        else{
          echo "CSS-Datei shop/Frameset/shopstyles.css konnte nicht zum schreiben geöffnet werden!";
          $ok = false;
        }
        fclose ($fp_dest);  // css-file schliessen
    } // end of if $fp_dest
    else{
      echo "CSS-Datei shop/shopstyles.css konnte nicht zum schreiben geöffnet werden!";
      $ok = false;
    }
  return $ok;
  } // end of function mkcssfile


  // -----------------------------------------------------------------------
  // Gibt eine Tabelle im HTML-Format aus, die folgende Elemente enthält:
  // - R,G,B Editierfelder für Schriftfarbe
  // - dropdown-Box fuer Schriftgewicht
  // - dropdown-Box fuer Schriftgroesse
  // - dropdown-Box fuer Text Decoration
  // die Parameter werden aus der Datenbank ausgelesen und direkt in die
  // angezeigten Felder eingefüllt
  // Argumente: $id -> identifier in datenbank
  //            $label -> Benennung des Tags z.B ShopTitel
  //            $color -> wenn 1 (true) wird Farbauswahl angezeigt
  //            $weight -> wenn 1 (true) wird Schriftgewicht angezeigt
  //            $size -> wenn 1 (true) wird Schriftgroesse angezeigt
  //            $style -> wenn 1 (true) wird Text Style angezeigt
  // Rueckgabewert: HTML Tabelle mit oabengenannten Formularelementen
  // Formular Bezeichner bestehen aus der Variablen $id und folgender ext.:
  //            _c  -> fuer RGB-Wert (color)
  //            _w  -> fuer Schriftgewicht (width)
  //            _s  -> fuer Schriftgroesse (size)
  //            _d  -> fuer Text-Dekoration (decoration)
  //            _i  -> fuer Text-Stil (style)
  function htmltext($id, $color, $weight, $size, $deco, $style, $label){
      echo "<p><table border=0 cellpadding=0 cellspacing=0>";
      echo "<tr><td colspan='2'><b>".$label."</b><td></tr>";

      // Eingabefelder für R,G,B ausgeben (wenn $color == true) ausgeben
      if ($color == true){
          echo "<tr><td>";
          echo "Schriftfarbe:</td><td align=right>";
          rgbshow($id."_c");
          echo "</td></tr>";
      } // end of if $color == true

      // dropdown-Feld fuer Schriftgewicht (normal, bold, bolder, lighter)
      // (wenn $weight == true) ausgeben
      if ($weight == true){
          echo "<tr><td>";
          echo "Schriftgewicht:</td><td align=right>";
          $css = getcssarg($id."_w");
          echo "<select name=".$id."_w>";
          if ($css == "normal") { echo "  <option selected value=normal>normal"; }
          else { echo "  <option value=normal>normal"; }
          if ($css == "bold") { echo "  <option selected value=bold>fett"; }
          else {echo "  <option value=bold>fett"; }
          if ($css == "bolder") { echo "  <option selected value=bolder>extrafett"; }
          else {echo "  <option value=bolder>extrafett"; }
          if ($css == "lighter") { echo "  <option selected value=lighter>d&uuml;nn"; }
          else {echo "  <option value=lighter>d&uuml;nn"; }
          echo "</select>";
          echo "</td></tr>";
          } // end of if $weight == true

      // dropdown-Feld fuer Schriftgrösse (wenn $size == true) ausgeben
      if ($size == true){
          echo "<tr><td>";
          echo "Schriftgr&ouml;sse:</td><td align=right>";
          $css = getcssarg($id."_s");
          echo "<select name=".$id."_s>";
          // Schriftgroesse 8..35 zum Auswaehlen erzeugen
          for ($var=8; $var <= 35; $var++) {
              if ($css == ($var."px")) { echo "  <option selected value='".$var."px'>".$var."px"; }
              else { echo "  <option value='".$var."px'>".$var."px"; }
          }
          echo "</select>";
          echo "</td></tr>";
      } // end of if $size == true

      // dropdown-Feld fuer Text-Decoration (wenn $deco == true) ausgeben
      if ($deco == true){
          echo "<tr><td>";
          echo "Text Dekoration:</td><td align=right>";
          // dropdown-Feld fuer Text-Decoration (none, underline, overline, blink, line-through)
          $css = getcssarg($id."_d");
          echo "<select name=".$id."_d>";
          if ($css == "none") { echo "  <option selected value=none>keine"; }
          else { echo "  <option value=none>keine"; }
          if ($css == "underline") { echo "  <option selected value=underline>unterstrichen"; }
          else {echo "  <option value=underline>unterstrichen"; }
          if ($css == "overline") { echo "  <option selected value=overline>&uuml;berstrichen"; }
          else {echo "  <option value=overline>&uuml;berstrichen"; }
          if ($css == "blink") { echo "  <option selected value=blink>blinkend"; }
          else {echo "  <option value=blink>blinkend"; }
          if ($css == "line-through") { echo "  <option selected value=line-through>durchgestrichen"; }
          else {echo "  <option value=line-through>durchgestrichen"; }
          echo "</select>";
          echo "</td></tr>";
      } // end of if $style == true

      // dropdown-Feld fuer Text-Style (wenn $style == true) ausgeben
      if ($style == true){
          echo "<tr><td>";
          echo "Schriftstil:</td><td align=right>";
          // dropdown-Feld fuer Schriftstil (italic, oblique, normal)
          $css = getcssarg($id."_i");
          echo "<select name=".$id."_i>";
          if ($css == "normal") { echo "  <option selected value=normal>normal"; }
          else { echo "  <option value=normal>normal"; }
          if ($css == "italic") { echo "  <option selected value=italic>italic (kursiv)"; }
          else {echo "  <option value=italic>italic (kursiv)"; }
          if ($css == "oblique") { echo "  <option selected value=oblique>oblique (kursiv)"; }
          else {echo "  <option value=oblique>oblique (kursiv)"; }
          echo "</select>";
          echo "</td></tr>";
      } // end of if $style == true

      echo "</table></p>";
  } // end of function htmltext


  // -----------------------------------------------------------------------
  // Speichert die Fonteinstellungen eines Font-Tags in der DB
  // Das Tag wird um folgende Erweiterungen ergänzt in der CSS-Tabelle
  // gespeichert:
  //            _c  -> fuer RGB-Wert (color)
  //            _w  -> fuer Schriftgewicht (width)
  //            _s  -> fuer Schriftgroesse (size)
  //            _d  -> fuer Text-Dekoration (decoration)
  //
  // Argumente: Font-tag
  // Rueckgabewert: nichts
  function savefont($font_tag, $c_wert, $w_wert, $s_wert, $d_wert, $i_wert){
      updatecssarg($font_tag."_c", rgbdechex($c_wert));
      updatecssarg($font_tag."_w", $w_wert);
      updatecssarg($font_tag."_s", $s_wert);
      updatecssarg($font_tag."_d", $d_wert);
      updatecssarg($font_tag."_i", $i_wert);
  } // end of function savefont


  // -----------------------------------------------------------------------
  // holt einen RGB-CSS-Wert aus der DB und gibt ihn als Teil eines HTML-
  // Formulars mit Eingabefeldern für R,G,B aus.
  // Argumente: rgb-identifier
  // Rueckgabewert: HTML-Formularteil mit Eingabemoeglichkeit fuer R,G,B
  function rgbshow($id){
      $rgb = rgbhexdec(getcssarg($id));
      echo "<table border=1 cellpadding=0 cellspacing=0><tr>";
      echo "<td width=64 bgcolor=".getcssarg($id).">&nbsp;&nbsp;&nbsp;</td><td valign=middle>";
      echo "&nbsp;";
      echo "R:<input type=text name=".$id."[0] size=3 value=".$rgb[0]."> ";
      echo "G:<input type=text name=".$id."[1] size=3 value=".$rgb[1]."> ";
      echo "B:<input type=text name=".$id."[2] size=3 value=".$rgb[2].">&nbsp;</td>";
      echo "</tr></table>";
  } // end of function rgbshow


  // -----------------------------------------------------------------------
  // holt einen CSS-Wert aus der DB und entfernt allfällig vorhanden Kommas.
  // Wird für das Fontset benoetigt
  // Argumente: CSS-Identifier
  // Rueckgabewert: CSS-String ohne Kommas
  function getnocomma ($id){
    $temp = getcssarg($id);
    $temp = str_replace(",","",$temp );
    return $temp;
  }


  // -----------------------------------------------------------------------
  // Wandelt die dezimal in einem Array übergebenen Farbkomponenten (r,g,b)
  // in den für das CSS-File notwendigen Hex-String um.
  // Argumente: Farbkomponenten-Array
  //            [0] = r {0..255}
  //            [1] = g {0..255}
  //            [2] = b {0..255}
  // Rueckgabewert: rgb-Hex-String im Format (#rrggbb)
  function rgbdechex ($rgb_wert){
      $h_string = "#";
      foreach($rgb_wert as $count){
          $count = $count % 256;
          $temp = dechex($count);
          if ($count < 16){ $h_string.="0".$temp;}
          else { $h_string.=$temp;}
      } // end of foreach
      return $h_string;
  } // end of function rgbdechex


  // -----------------------------------------------------------------------
  // Wandelt einen Hex-String in der Form #rrggbb in die entsprechenden
  // Farbkomponenten (r,g,b) im Dezimalformat um und gibt diese in einem
  // Array zurueck:
  // Argumente: HTML-Hex-String Format: #rrggbb
  // Rueckgabewert: Array mit den Farbkomponenten r,g,b im Dezimalformat
  //                [0] = r
  //                [1] = g
  //                [2] = b
  function rgbhexdec ($rgb_hex_string){
      $rgb_dec_wert[0] = substr($rgb_hex_string,1,2);
      $rgb_dec_wert[1] = substr($rgb_hex_string,3,2);
      $rgb_dec_wert[2] = substr($rgb_hex_string,5,2);
      $rgb_dec_wert[0] = hexdec($rgb_dec_wert[0]);
      $rgb_dec_wert[1] = hexdec($rgb_dec_wert[1]);
      $rgb_dec_wert[2] = hexdec($rgb_dec_wert[2]);
      return $rgb_dec_wert;
  } // end of function rgbhexdec

// End of file ----------------------------------------------------------
?>

</BODY>
</HTML>
