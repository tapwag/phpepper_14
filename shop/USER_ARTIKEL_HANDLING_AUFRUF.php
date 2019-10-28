<?php
// Filename: USER_ARTIKEL_HANDLING_AUFRUF.php
//
// Modul: Aufruf Module - USER_ARTIKEL_HANDLING
//
// Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
//
// Sponsoren:
// Variationsgruppen gesponsert von der Firma appeto.de - Franke GbR, www.appeto.de
//
// Zweck: Funktionen zur Uebergabe der Daten an HTML-Teil
//
// Sicherheitsstatus:                     *** USER ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: USER_ARTIKEL_HANDLING_AUFRUF.php,v 1.101 2003/07/28 11:11:48 glanzret Exp $
//
// -----------------------------------------------------------------------
// Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
// wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
$USER_ARTIKEL_HANDLING_AUFRUF = true;

// -----------------------------------------------------------------------
// Weitere Konfigurationsschritte vornehmen
// include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
// Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
// Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

// -----------------------------------------------------------------------
// Wenn der Haendlermodus aktiviert wurde (alle Kunden muessen sich zuerst einloggen), dann ueberprueft folgender Link,
// ob man schon eingeloggt ist
if (!isset($USER_AUTH)) {include("USER_AUTH.php");}

// -----------------------------------------------------------------------
// Einbinden der benoetigten Module (PHP-Scripts)
// Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
if (!isset($session_mgmt)) {include("session_mgmt.php");}
if (!isset($USER_ARTIKEL_HANDLING)) {include("USER_ARTIKEL_HANDLING.php");}
if (!isset($initialize)) {include("initialize.php");}

// -----------------------------------------------------------------------
// Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
// $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
extract($_GET);
extract($_POST);

// -----------------------------------------------------------------------
// Erster Teil des Headers ausgeben (fuer alle Darstellungen)
// -----------------------------------------------------------------------
?>
  <html>
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
      <meta http-equiv="content-language" content="de">
      <meta name="author" content="José Fontanil and Reto Glanzmann">
      <meta name="robots" content="noindex">
      <title>PHP Shop</title>
      <LINK REL=STYLESHEET HREF="shopstyles.css" TYPE="text/css">
      <script language="JavaScript" type="text/javascript">
              function zweiframes(url1,url2) {
                  parent.left.location.href=url2;
                  parent.content.location.href=url1;
              }
      </script>
<?php

// -----------------------------------------------------------------------
// 2. Teil des Headers ausgeben
// Headerweiche für CSS und JavaScript-Funktionen
// -----------------------------------------------------------------------

  // Header fuer Darstellung im Top-Frame
  if ($darstellen == 4) {
    echo "</head>\n";
    echo "<body class=\"left\" style=\"margin-top:0; margin-bottom:0; margin-left:10; margin-right:0\">\n";
  }

  // Header für Artikeldarstellung. Mit JavaScript-Funktion, um ein POP-Up Fenster darzustellen
  else if ($darstellen == 1) {
    echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
    echo "function NeuFenster(bild_gr)\n";
    echo "{\n";
    echo ' MeinFenster = window.open("pop_up.php?bild_gross="+bild_gr+"", "GrossesBild", "width=600,height=550,scrollbars");'."\n";
    echo " MeinFenster.focus();\n";
    echo "}\n";
    echo "function showTellaFriend(Kategorie_ID, Artikel_ID, Name, Preis)\n";
    echo "{\n";
    echo ' MeinFenster = window.open("tell_a_friend.php?Kategorie_ID="+Kategorie_ID+"&Artikel_ID="+Artikel_ID+"&Artikelname="+Name+"","Tell_a_Friend","width=550,height=580");'."\n";
    echo " MeinFenster.focus();\n";
    echo "}\n";
    echo "</script>\n";
    echo "</head>\n";
    echo "<body class=\"content\">\n";
    }

  else if ($darstellen == 3) {
    echo "</head>\n";
    echo "<body class=\"no_decoration\">\n";
  }

  // Header für Darstellungen im Content-Frame
  else {
    echo "</head>\n";
    echo "<body class=\"content\">\n";
  }

// -----------------------------------------------------------------------
// Je ein IF resp. ELSE IF ist zustaendig fuer eine Funktionalitaet in diesem Modul.
// In den Formularen weiter unten hat man ein hidden-field, welches den Wert der
// Variable $darstellen uebermittelt. Dadurch wird entschieden welche Funktion(alitaet)
// dieses Moduls angesteuert und somit benutzt werden soll.
// -----------------------------------------------------------------------

// -----------------------------------------------------------------------
// darstellen = 1: Alle Artikel einer Kategorie im Content-Frame darstellen.
// Wird das Artikelthumbnail (Minibild) angeklickt, geht ein POP-UP Fenster
// auf, worin das Artikelbild in Orginalgroesse angezeigt wird.
// -----------------------------------------------------------------------
if ($darstellen == 1) {
      // Update der Ablaufzeit der Kundensession (Def. siehe U_A_H.php)
      extend_Session(session_id());

      // Alle Artikel einer Kategorie in einen Array laden (auslesen).
      // Entscheid, ob Anzahl gleichzeitig angezeigter Artikel beschraenkt werden soll (je nach Admineinstellung):
      $anzahlartikelgleichzeitig = getArtikelInkrement();
      $blaettern_anzeige_definition = get_new_shop_setting("ArtikelSuchInkrementAnzeige","shop_settings");
      $blaettern_anzeige_definition = $blaettern_anzeige_definition['ArtikelSuchInkrementAnzeige'];
      if ($anzahlartikelgleichzeitig == -1) {
          // Alle Artikel gleichzeitig anzeigen
          $myArtikelarray = IDgetArtikeleinerKategorie($Kategorie_ID);
      }
      else {
          // Wenn geblaettert wurde, so werden die Parameter via GET uebergeben - auslesen, Flag setzen:
          if ($HTTP_GET_VARS["anzeigen_ab"] == "") {
              $anzeigen_ab = 0;
          }
          // Eingeschraenkte Anzahl Artikel gleichzeitig anzeigen (wenn Ziel_ID angegeben wird, so wird das Inkrement mit ebendiesem Artikel angezeigt)
          $myArtikelarray = IDgetArtikeleinerKategorievonbis($Kategorie_ID,$anzahlartikelgleichzeitig,$anzeigen_ab,$Ziel_ID);
          // Alle zum anzeigen der [zurueck] [1] [2] ... [n] [weiter] Anzeige sind in folgendem Artikel gespeichert (auslesen, abspeichern)
          $blaetterninfo = new Artikel();
          $blaetterninfo = array_pop($myArtikelarray);
          // Blaettern Anzeige auslesen, falls eingeschaltet
          $blaettern = artikel_blaettern_anzeige($blaetterninfo);
      }
      $meinekleineKategorie = getKategorie($Kategorie_ID);
      // Test ob jemand die Kategorie Nichtzugeordnet ausspionieren will, wenn ja, Fehlermeldung anzeigen und abbrechen
      if ($meinekleineKategorie->Name == "Nichtzugeordnet") {
          echo "<center><h3>Diese Kategorie ist nicht &ouml;ffentlich zug&auml;nglich!</h3></center></body></html>\n";
          exit; // Programmabbruch wenn die Kategorie Nichtzugeordnet angezeigt werden sollte
      }
      $Kategoriename = $meinekleineKategorie->Name;
      $ParentKat = $meinekleineKategorie->Unterkategorie_von;
      $Details_anzeigen = $meinekleineKategorie->Details_anzeigen;
      // Falls es sich um eine Unterkategorie handelt, so wird hier der Name der
      // uebergeordneten Kategorie dargestellt (kommt aus U_A_H_Aufruf darstellen=4)
      // (Unter-)Kategorienname ausgeben:
      if ($ParentKat != $Kategoriename) {
          echo "<h2 class='content'><center>".$ParentKat."</center>";
          echo "<center>".$Kategoriename."</center></h2>";
          if ($Details_anzeigen == "Y") {echo "<div class='content'><center>".$meinekleineKategorie->Beschreibung."</center>";}
      }
      else {
          echo "<h2 class='content'><center>$Kategoriename</center></h2>";
          if ($Details_anzeigen == "Y") {echo "<div class='content'><center>".$meinekleineKategorie->Beschreibung."</center>";}
      }
      if (count($myArtikelarray) == 0) {
          echo "<h4 class='content'><center>Es befinden sich noch keine Artikel in dieser Kategorie</center></h4>";
      }
      // Spaltenbreite, wo das Thumbnail angezeigt wird, in Abhaengigkeit der Thumbnailbreite bestimmen
      $SpaltenBreite=getThumbnail_Breite()+50;
      echo '<table class="content" border="0" cellpadding="0" cellspacing="2" width="100%">'."\n";
      // Cascading Style Sheet Angaben fuer den Link (Weitere Informationen) auslesen und in Variable abpacken:
      $cssargumente = ' style="text-decoration:'.getcssarg("main_link_d").';
        color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").';
        font-weight:'.getcssarg("main_link_w").'"';
      // Cascading Style Sheet Angaben fuer den aktiven Link (z.B. Blaettern) nie unterstreichen!:
      $cssargaktiv = ' style="text-decoration:none; color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").';
        font-weight:'.getcssarg("main_link_w").'"';

      // Obere Blaetternanzeige einblenden - falls noetig:
      if ($blaettern != false && ($blaettern_anzeige_definition == "unten_und_oben" || $blaettern_anzeige_definition == "oben")) {
          echo "<hr>\n";
          echo $blaettern;
      }// End if $blaettern != ""

      // fuer jeden Artikel der Kategorie..
      foreach($myArtikelarray as $myarray) {
        echo "  <form method='post' action='./USER_BESTELLUNG_AUFRUF.php'>";
        echo '  <tr class="content">'."\n";
        echo '    <td class="content" colspan="3">'."\n";
        echo '      <a name="Ziel'.$myarray->artikel_ID.'" class="content"><hr></a>'."\n";
        echo '    </td>'."\n";
        echo '  </tr>'."\n";
        echo '  <tr class="content">'."\n";

        // falls vorhanden, Thumbnail als Link darstellen
        if ($myarray->bild_klein != ""){
            echo '    <td class="content" width="'.$SpaltenBreite.'" rowspan="3" VALIGN=top><center><a href="javascript:NeuFenster(\''.$myarray->bild_gross;
            echo '\')" class="content"><img src="./ProdukteBilder/'.$myarray->bild_klein.'" border="0" alt="'.$myarray->name.'" title="Klicken f&uuml;r Detailansicht"></a></center></td>'."\n";
        } // end of if
        else{
            echo '    <td class="content" width="'.$SpaltenBreite.'" rowspan="3" VALIGN=top><center>&nbsp;</center></td>';
        }
        echo '    <td class="content" colspan ="2">'."\n";
        echo "<h3 class='content'>".$myarray->name."</h3>"."\n";
        // Preis nur darstellen, wenn er groesser als 0 ist
        $preiswarnull_flag = true; // initialisieren
        if ($myarray->preis != "0") {
            $preiswarnull_flag = false; // Flag ist true wenn Preis = 0 ist
            // Waehrungsformat aus Datenbank ermitteln
            echo '      <h4 class="content">'.getWaehrung()." ";
            // Produktpreis formatiert darstellen
            echo getZahlenformat($myarray->preis);
            echo "</h4>\n";
        }
        // Beschreibung anzeigen
        echo '      <p>'.$myarray->beschreibung.'<br>'."\n";
        // Herstellerlink, falls vorhanden anzeigen
        if (!empty($myarray->link)) echo '<a class="content" '.$cssargumente.' href="'.$myarray->link.'"target=_new>Weitere Informationen</a></p>'."\n";
        echo '    </td>'."\n";
        echo '  </tr>'."\n";
        echo '  <tr class="content">'."\n";
        echo '    <td class="content" colspan ="2">'."\n";
        // versteckte Felder fuer Warenkorbfunktion (werden an naechste Seite uebermittelt)
        if ($ParentKat=="") {$myParentKat="@leer@";}else{$myParentKat=$ParentKat;} //Damit ParentKat korrekt erkannt wird
        echo '    <INPUT TYPE="hidden" name=Kategoriename value='.urlencode($Kategoriename).' title="Fuer Warenkorb -> Zurueck1">'."\n";
        echo '    <INPUT TYPE="hidden" name=Kategorie_ID value='.$Kategorie_ID.' title="Fuer Warenkorb -> Zurueck1">'."\n";
        echo '    <INPUT TYPE="hidden" name=ParentKat value='.urlencode($myParentKat).' title="Fuer Warenkorb -> Zurueck1">'."\n";
        echo '    <INPUT TYPE="hidden" name=Artikel_ID value='.$myarray->artikel_ID.' title="Fuer Warenkorb -> Zurueck2">'."\n";

        // Variationen einfuegen (falls Vorhanden)
        $counter = 1;
        $myvariationen = $myarray->getallvariationen();
        $myvariationsgruppen = $myarray->getallvar_gruppe();
        // Wenn nur eine Variation vorhanden, dann Variationen nicht anzeigen, da man ja nur eine
        // Auswahl haette

        // Anzahl Variationen zaehlen
        $varcount = count($myvariationen);

        // Die höchste Variationsgruppe bestimmen, die in diesem Artikel verwendet wird
        $grpcount = 1;
        foreach ($myarray->variationen_gruppe as $gruppe){
            if ($gruppe > $grpcount){ $grpcount = $gruppe; }
        } // end of foreach
        foreach ($myvariationsgruppen as $gruppe){
            if ($gruppe > $grpcount){ $grpcount = $gruppe; }
        } // end of foreach

        // Tabelle für Variationen ausgeben
        echo '<table class="content" border="0" cellpadding="0" cellspacing="0">'."\n";

        // Alle Variationsgruppen abarbeiten
        $var_gruppen_nr = 0;
        $erste_gruppe = true;
        for ($grp=1; $grp<=$grpcount; $grp++){
            $var_grp = array();
            // alle Variationen bestimmen, die zu der aktuellen Variationsgruppe gehören
            foreach ($myvariationen as $keyname => $value){
                // wenn eine Variation der Gruppe 0 zugeordnet ist, handelt es sich um einen fehler
                // oder der Shop wurde upgedated. Da die Variationsgruppe 0 nicht existiert, werden
                // die Variationen der Variationsgruppe 1 zugeordnet
                if($myvariationsgruppen[$keyname] == 0){
                    $myvariationsgruppen[$keyname] = 1;
                } // end of if
                // die Variationen, welche zu dieser Variationsgruppe in einen assoziativen Array schreiben
                if ($myvariationsgruppen[$keyname] == $grp){
                    $var_grp[$keyname] = $value;
                } // end of if
            } // end of foreach

            // wenn die Varaitionsgruppe mehr als eine Variation beinhaltet, wird sie in der
            // gewünschten Form (dropdown-radio)ausgegeben
            if (count($var_grp) > 1){
                // falls zu der ersten Variantengruppe keine Beschreibung existiert, wird sie mit
                // dem String "Varianten" belegt (Rückwärtskompatibilität)
                if ($grp == 1 && urldecode($myarray->var_gruppen_text[1]) == ""){
                    $myarray->var_gruppen_text[1] = "Varianten";
                } // end of if

                // Variationsgruppen-Ueberschrift ausgeben
                echo '  <tr class="content">'."\n";
                echo '    <td class="content">'."\n";
                echo "      <br><b class='content' style='font-weight:bold'>".urldecode($myarray->var_gruppen_text[$grp])."</b>\n";
                echo '    </td>'."\n";
                echo '  </tr>'."\n";

                $erstes = true;
                echo '  <tr class="content">'."\n";
                echo '    <td class="content">'."\n";

                foreach ($var_grp as $keyname_var => $value_var){

                    // Auswahl der Variation mittels Dropdown-Feld
                    if ($myarray->var_gruppen_darst[$grp] == "dropdown"){
                        if ($erstes){
                            echo "<select name='Variation[".$var_gruppen_nr."]' size=1 >";
                            $erstes = false;
                        } // end of if
                        echo "<option value='".urlencode($keyname_var)."' >$keyname_var&nbsp;";
                        // Wenn die Variation einen Aufpreis hat..
                        if(!empty($value_var)){
                            // Wenn der Artikelpreis nicht 0 war..
                            if (!$preiswarnull_flag || !$erste_gruppe) {
                                echo " (Aufpreis: ".getWaehrung().'&nbsp;';
                                echo getZahlenformat($value_var);
                                echo ")"."\n";
                            } // end of if !$preiswarnull_flag
                            // Wenn der Artikelpreis 0 war..
                            else{
                                echo "&nbsp;Preis: ".getWaehrung().'&nbsp;';
                                echo getZahlenformat($value_var);
                                echo "</b>"."\n";
                            } // end of else
                        } // end of if !empty($value)
                    } // end of if dropdown

                    // Auswahl der Variation mittels Radio-Buttons (default)
                    else{
                        // der erste Radiobutton pro Gruppe soll ausgewählt sein
                        if ($erstes){
                            echo " <input type='radio' name='Variation[".$var_gruppen_nr."]' value='".urlencode($keyname_var)."' checked>$keyname_var&nbsp;";
                            $erstes = false;
                        } // end of if
                        else {
                            echo "<input type='radio' name='Variation[".$var_gruppen_nr."]' value='".urlencode($keyname_var)."'>$keyname_var&nbsp;";
                        } // end of else
                        if(!empty($value_var)){
                            // Wenn der Artikelpreis nicht 0 war..
                            if (!$preiswarnull_flag | !$erste_gruppe) {
                                echo " (Aufpreis: ".getWaehrung().'&nbsp;';
                                echo getZahlenformat($value_var);
                                echo ")";
                            } // end of if !$preiswarnull_flag
                            // Wenn der Artikelpreis 0 war..
                            else{
                                echo "<B style='font-weight:bold'>Preis: ".getWaehrung().'&nbsp;';
                                echo getZahlenformat($value_var);
                                echo "</b>";
                            } // end of else
                        } // end of if !empty($value
                        echo "<br>";
                    } // end of else
                 //   echo "<br>Variation: $keyname_var Aufpreis: $value_var";
                } // end of foreach

                if ($myarray->var_gruppen_darst[$grp] == "dropdown"){
                    echo "</select>";
                } // end of if
                echo '    </td></tr>'."\n";
            $var_gruppen_nr ++;
            $erste_gruppe = false;
            } // end of if
        } // end of for

        echo '    <br>'."\n";

        // Optionen einfuegen
        $counter = 1;
        // Optionen aus Array holen
        $myoptionen = $myarray->getalloptionen();
        $temp = key($myoptionen);
        // Falls Artikeloptionen vorhanden...
        if(!empty($temp)){
          echo ' <tr class="content">'."\n";
          echo '   <td class="content">'."\n";
          echo '     <br><b class="content" style="font-weight:bold">Optionen</b>'."\n";
          echo '   </td>'."\n";
          echo ' </tr>'."\n";
          echo ' <tr class="content">'."\n";
          echo '   <td class="content">'."\n";
          // Fuer jede Artikeloption
          foreach($myoptionen as $keyname => $value){
            echo "     <INPUT TYPE='CHECKBOX' name='Option$counter' value='".urlencode($keyname)."' title='Option1'>$keyname";
            // Optionsaufpreis darstellen (falls Vorhanden)
            if(!empty($value)){
              echo " (Aufpreis: ".getWaehrung().' ';
              echo getZahlenformat($value);
              echo ")";
            } // End if empty Aufpreis
            echo "<BR>"."\n";
            $counter++;
          } // End of foreach Optionen
          echo '   </td>'."\n";
          echo ' </tr>'."\n";
        } // End of if not empty Optionen
        echo '</table>'."\n";

        // Zusatzfelder einfuegen (falls vorhanden)
        if (count($myarray->zusatzfelder_text) > 1 || ($myarray->zusatzfelder_text[0] != "")){
            $feld_param = array();
            $feld_text = array();
            echo '<table class="content" border="0" cellpadding="0" cellspacing="0">'."\n";
            echo ' <tr class="content">'."\n";
            echo '   <td class="content" colspan="2">'."\n";
            echo '     <br><b class="content" style="font-weight:bold">Zus&auml;tzliche Angaben</b>'."\n";
            echo '   </td>'."\n";
            echo ' </tr>'."\n";
            $counter = 0;
            foreach($myarray->zusatzfelder_text as $zusatzfeld) {
                $feld_param = zusatzfeld_parameter($myarray->zusatzfelder_param[$counter]);
                $feld_text = zusatzfeld_beschreibung($zusatzfeld);
                echo ' <tr class="content">'."\n";
                echo '   <td class="content" valign=top>'."\n";
                // Feldtext ausgeben, der vor dem Eingabefeld steht
                echo $feld_text[vor].'&nbsp;';
                echo '   </td>'."\n";
                echo '   <td class="content">'."\n";
                // falls, die Feldhoehe mit 1 angegeben wurde, wird ein Text-Input-Feld ausgegeben
                if ($feld_param[hoehe_feld] < 2){
                    echo '     <input type=text name="Zusatzfeld['.$counter.']" maxlength='.$feld_param[laenge_max].' size='.$feld_param[laenge_feld].' value="">';
                } // End of if
                // ist die Feldhoehe groesser als 1, so wird eine Textarea ausgegeben
                else{
                    echo '     <textarea style="font-family: Courier, Courier New, Monaco" name="Zusatzfeld['.$counter.']" cols='.$feld_param[laenge_feld].' rows='.$feld_param[hoehe_feld].' wrap=physical></textarea>';
                } // End of else
                //
                // Feldtext ausgeben, der nach dem Eingabefeld steht
                echo '&nbsp;'.$feld_text[nach];
                echo '   </td>'."\n";
                echo ' </tr>'."\n";
                $counter++;
            } // End of foreach zusatzfelder_text
        echo '</table>'."\n";
        } // End of if !empty($myarray->zusatzfelder_text)


        //HTML-Teil (Ende des Formulars)
        echo '    </td>'."\n";
        echo '  </tr>'."\n";
        echo '  <tr class="content">'."\n";
        echo '    <td class="content" VALIGN=middle>'."\n";
        echo '      <br>'."\n".'<h4 class="content">'."\n";
        echo '      <select name="Anzahl">'."\n";
        echo '        <option value="1">1</option>'."\n";
        echo '        <option value="2">2</option>'."\n";
        echo '        <option value="3">3</option>'."\n";
        echo '        <option value="4">4</option>'."\n";
        echo '        <option value="5">5</option>'."\n";
        echo '        <option value="6">6</option>'."\n";
        echo '        <option value="7">7</option>'."\n";
        echo '        <option value="8">8</option>'."\n";
        echo '        <option value="9">9</option>'."\n";
        echo '        <option value="10">10</option>'."\n";
        echo '      </select>'."\n";
        echo '      St&uuml;ck </h4>'."\n";
        echo '    </td>'."\n".'<td class="content" VALIGN= middle ALIGN=right>'."\n";
        echo "    <input type='hidden' name='Artikel_ID' value='".$myarray->artikel_ID."'>"."\n";
        echo '    <INPUT TYPE="hidden"  name=darstellen value=2   title="Im Warenkorb gelandet">';
        echo '    <INPUT TYPE="hidden"  name=Session_ID value="'.session_id().'" title="Im Warenkorb gelandet">';
        // Optionale Meldung tell-a-friend einbauen
        if (get_tell_a_friend()) {
            echo '<a class="content" '.$cssargumente.' href="javascript:showTellaFriend('.$Kategorie_ID.','.$myarray->artikel_ID.',\''.urlencode($myarray->name).'\')">
            Diesen Artikel weiterempfehlen</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n";
        }
        echo "    <input type='image' src='Buttons/bt_in_warenkorb.gif' border=\"0\" alt=\"In den Warenkorb legen\"  title=\"Artikel in den Warenkorb legen\">\n";
        echo '    </td>'."\n";
        echo '  </tr>'."\n";
        echo '  </form>'."\n";
      }// End foreach (fuer jeden Artikel)
      echo '</table>'."\n";

      // Blaettern Anzeige anzeigen falls unten (an diesem Ort) erwuenscht
      if ($blaettern != false && ($blaettern_anzeige_definition == "unten" || $blaettern_anzeige_definition == "unten_und_oben")) {
          echo "<hr>\n";
          echo $blaettern;
      }
      else {
          echo "<br>\n"; // Etwas 'Luft' unter dem letzten Artikel
      }
}// End darstellen=1

// -----------------------------------------------------------------------
// darstellen = 30: Artikel Suchen 1/2. Eingabefenster einblenden, danach weiter
// im darstellen = 3
// -----------------------------------------------------------------------
else if ($darstellen == 30) {
// HTML-Teil (Formular in einer Tabelle)
?>
    <TABLE class="content" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
            <TR class='content'>
            <TD class='content' ALIGN='center' VALIGN='middle' >
            <H2 CLASS="content">Artikel suchen</H2>
            <FORM  action='<?php echo "$PHP_SELF"; ?>' method='POST' name="Formular">
              <H3 CLASS="content">Geben Sie die Suchbegriffe in das Eingabefeld ein.</H3>
              <INPUT TYPE='hidden' NAME='darstellen' VALUE='3'>
              <INPUT TYPE='hidden' NAME='lowlimit' VALUE='0'>
              <INPUT TYPE='hidden' NAME='highlimit' VALUE='<?php echo getSuchInkrement(); ?>'>
              <INPUT TYPE='text' NAME='Suchstring' value="<?php echo $Suchstring; ?>" SIZE='20'>
              <INPUT TYPE='submit' VALUE='suchen'><BR>
              Bilder der Artikel anzeigen <INPUT TYPE='checkbox' NAME='bilderanzeigen' VALUE='false'>
            </FORM>
            </TD>
          </TR>
        </TABLE>
        <script language="JavaScript">
        <!--
        document.Formular.Suchstring.focus();
        //-->
        </script>

<?php
}

// -----------------------------------------------------------------------
// darstellen = 3: Artikel Suchen 2/2. Suchstring auswerten und Resultat
// anzeigen
// -----------------------------------------------------------------------
else if ($darstellen == 3) {
    // Test ob ueberhaupt ein Suchstring uebergeben wurde, sonst Abbruch mit Fehlermeldung
    if ($Suchstring == "") {
        // Keine Artikel gefunden weil der uebergebene Suchstring leer war -> Spezielle Meldung ausgeben
        echo "<center><br>\n<h3 class='content'>Bitte einen Suchbegriff eingeben!</h3><br>\n";
        echo "<a class=\"no_decoration\" href=\"".$PHP_SELF."?darstellen=30\" target=\"content\">\n";
        echo "<img src=\"Buttons/bt_zurueck.gif\" border=\"0\"></a></center>\n";
        exit;
    }

    // Folgende Funktion liefert in einem Array jeweils die gefundenen Artikel und
    // die Kategorie(-n) worin der Artikel eingeteilt ist (ein Artikelmitkategorien-Objekt)
    $myArtikelarray = getgesuchterArtikel(urldecode($Suchstring),$lowlimit,$highlimit);
    // Zuerst lesen wir die Gesamtanzahl Treffer aus. Diese Anzahl ist in der Artikel_ID des
    // letzten Artikels im letzten Artikelmitkategorien-Objekt im $myArtikelarray gespeichert.
    // Dieser Artikel enthaelt kein Resultat-Artikel und wird nun ausgelesen und entfernt
    $anzahlArtikelmitkategorien = array_pop($myArtikelarray);
    $Anzahl_Treffer = $anzahlArtikelmitkategorien->myArtikel->artikel_ID;
    $lowlimit = $anzahlArtikelmitkategorien->myArtikel->preis;
    $highlimit = $anzahlArtikelmitkategorien->myArtikel->aktionspreis;
    echo "<table class='content' border=0><BR>";
    echo "<tr class='content'><td class='content' ";
    if ($bilderanzeigen) {
        echo "colspan='2'";
    }
    echo " ><center><h1 class='content'>Suchergebnis: \"$Suchstring\"</h1></center></tr></td><BR>";
    $artikel_gefunden = false;
    // Test ob ueberhaupt irgendwelche Artikel gefunden wurden
    if (count($myArtikelarray) > 0){
        $artikel_gefunden = true;
    }
    // Wenn es ein Such-Resultat gibt:
    if ($artikel_gefunden){
      foreach($myArtikelarray as $counter =>$myArtikelmitkategorien) {
        // Kategorie und Unterkategorie des Artikels in Variablen speichern
        // Der Suchende kann wird wenn er den Link anklickt auf den Artikel in der
        // ersten Kategorie weitergeleitet. Um diesen Link zu bilden werden zwei Variablen benoetigt:
        $Kat = $myArtikelmitkategorien->myKategorienarray[0]->Kategorie_ID;
        // Nun wird die Kategorie_ID des Ziel-Artikels jeweils
        // an die (Unter-)kategorie angehaengt
        $Ziel = "&Ziel_ID=".$myArtikelmitkategorien->myArtikel->artikel_ID."&Kategorie_ID=$Kat#Ziel".$myArtikelmitkategorien->myArtikel->artikel_ID;
        // Gefundene Artikel passend formatiert (kompakt) anzeigen -> mit Link-Button
        // ...bei mehreren Kategorien zeigt der Link auf die erste Kategorie
        echo '<tr class="no_decoration">'."\n";
        if ($bilderanzeigen) {
            $SpaltenBreite=getThumbnail_Breite()+50;
            echo '<td class="no_decoration" valign="middle" align="center" width="'.$SpaltenBreite.'" VALIGN=top>'."\n";
            echo '<a class="no_decoration" style="text-decoration:none" href="./USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1'.$Ziel.'">';
            if ($myArtikelmitkategorien->myArtikel->bild_klein != "") {
                echo '<img src="./ProdukteBilder/'.$myArtikelmitkategorien->myArtikel->bild_klein.'" border="0" alt='.$myArtikelmitkategorien->myArtikel->name.'>';
            }
            echo '</a></td">'."\n";
        }
        echo '  <td class="no_decoration">'."\n";
        echo '    <hr class="no_decoration">'."\n";
        if (count($myArtikelmitkategorien->myKategorienarray) > 1) {
            echo "<B style='font-weight:bold'>Dieser Artikel ist in mehreren Kategorien vorhanden: ";
            foreach($myArtikelmitkategorien->myKategorienarray as $key=>$value) {
                if ($value->Unterkategorie_von <> "") {
                    echo "<a class='no_decoration' style='text-decoration:none' href='./USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&Kategorie_ID=".$value->Kategorie_ID."#Ziel".$myArtikelmitkategorien->myArtikel->artikel_ID."'>
                         [".$value->Unterkategorie_von." -> ".$value->Name."]</a> ";
                }
                else {
                    echo "<a class='no_decoration' style='text-decoration:none' href='./USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&Kategorie_ID=".$value->Kategorie_ID."#Ziel".$myArtikelmitkategorien->myArtikel->artikel_ID."'>
                         [".$value->Name."]</a> ";
                }
            }
            echo "</B><BR>";
        }
        echo '    <a class="no_decoration" style="text-decoration:none" href="./USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1';
        echo $Ziel.'">';
        echo '    <h3 class="no_decoration">'.$myArtikelmitkategorien->myArtikel->name.'<h3>'."\n";
        echo '    <h4 class="no_decoration">'.$myArtikelmitkategorien->myArtikel->beschreibung.'</h4>'."\n";
        echo '    <img src="Buttons/bt_artikel_anzeigen.gif" border="0"></a>';
        echo '  </td>'."\n";
        echo '</tr>'."\n";
      } // End of foreach
    } // End of if artikel gefunden
    else{
      // Keine Artikel gefunden, zurueck zum Suchen-Eingabefenster
      echo "<tr class='no_decoration'><td class='content'><center><h3 class='content'>Es wurden keine Artikel gefunden, die den Begriff \"$Suchstring\"enthalten!</h3>\n";
      echo '<a class="no_decoration" href="'.$PHP_SELF.'?darstellen=30&Suchstring='.$Suchstring.'" target="content"><img src="Buttons/bt_zurueck.gif" border="0"></a></center><tr class="content"><td class="content">';
    }// End of keinen artikel gefunden
    echo '</table>'."\n";
    //Damit der Link richtig dargestellt wird, werden die CSS-Argumente aus der Datenbank ausgelesen
    //(Tribut an Netscape 4.7x)
    $css_string = 'class="content" style="text-decoration:'.getcssarg("main_link_d").'; color:'.getcssarg("main_link_c").'; font-style:'.getcssarg("main_link_i").'; font-size:'.getcssarg("main_link_s").'; font-weight:'.getcssarg("main_link_w").'"';
    //Berechnungen fuer Navigation in den Suchresultaten:
    $Inkrement = getSuchInkrement();//Suchinkrement aus der Datenbank auslesen
    if ($Inkrement <= 0) {
        //Wir haben uns entschieden, dass, falls jemals jemand das Inkrement 0 waehlt, dieses
        //durch den Divisor 1 ersetzt wird. Eigentlich muesste man hier abbrechen.
        //die("<h1>U_A_H_Error: Abbruch vor einer Division durch Inkrement = Null! -> Funktion getSuchInkrement &uuml;berpr&uuml;fen (getgesuchterArtikel)</h1>");
        $Inkrement = 1;
    }
    $Anzahl_Inkremente = $Anzahl_Treffer / $Inkrement; //Ganzzahldivision zweier Integer
    //Wenn es mehr als in einem Darstellungsinkement hat, soll die Auswahlanzeige erscheinen
    //$low sagt von wo, $high sagt wieviel ab $low ausgelesen werden soll
    if ($Anzahl_Inkremente > 1) {
        echo "<hr>\n";
        for ($i=0;$i<$Anzahl_Inkremente;$i++) {
            $low = $i * $Inkrement;
            $high = $Inkrement;
            echo "<a $css_string href='".$PHP_SELF."?lowlimit=$low&highlimit=$high&darstellen=3&Suchstring=".urlencode($Suchstring)."&bilderanzeigen=$bilderanzeigen'>";
            //Aktuelles Inkrement Fett anzeigen
            if ($lowlimit == $low) {
                echo "<B style='font-weight:bold'>[".($i+1)."]</B>";
            }
            else {
                echo "[".($i+1)."]";
            }
            echo "</a>&nbsp;\n";
        }//End for
    }//End if
}// End darstellen = 3

// -----------------------------------------------------------------------
// darstellen = 4: Im left-Frame alle Kategorien zur Auswahl anzeigen (Navigationsmenu)
// Die selektierte Kategorie oder Unterkategorie wird in der Variable $selected uebergeben
// Darin steht die Kategorie_ID der entsprechenden Kategorie, resp. Unterkategorie
// (Diese Funktion wird von ../index.php aufgerufen -> siehe dortiges Frameset)
// Seit PhPepperShop v.1.4 wird hier auch eine JavaScript-freie Navigation ermoeglicht.
// -----------------------------------------------------------------------
else if ($darstellen == 4) {
    $myKategorien = array();
    $myKategorien = getallKategorien();

    // Darstellungsangaben definieren
    // ------------------------------
    // Tabellenbreite (wie left-Frame) aus Datenbank
    $darst['tbl_width'] = getcssarg('left_width')-10;
    // img-Tag Kategorie-Piktogramme
    $darst['align_img'] = "";
    // Bildspalten
    $darst['td_1_align'] = "left";
    $darst['td_1_valign'] = "middle";
    $darst['td_1_width'] = "17px";
    // Textspalten
    $darst['td_2_align'] = "left";
    $darst['td_2_valign'] = "middle";
    // Spaltenbreite Unterkategorienbeschreibung
    $darst['ukat_width'] = $darst['tbl_width'] - (2*$darst['td_1_width']);

    // Eigentliche Darstellung der Kategoriennavigation
    // ------------------------------------------------
    //Die Kategorien werden in einer Tabelle dargestellt, da diese von den Browsern
    //zuerst komplett geladen wird, und erst dann dargestellt wird.
    echo "<table class='left' border='0' cellspacing='0' cellpadding='0' width='".$darst['tbl_width']."px'>\n";
    // $value ist ein Kategorie-Objekt, welches im Array Unterkategorien seine Unterkategorien
    // mitfuehrt (siehe auch USER_ARTIKEL_HANDLING.php, Funktion: getallKategorien()).
    foreach($myKategorien as $keyname => $value){
        echo "<tr class='left'>\n<td class='left' width='".$darst['td_1_width']."' align='".$darst['td_1_align']."' valign='".$darst['td_1_valign']."'>\n";
        // Wenn der Browser kein JavaScript beherrscht anderen Link verwenden
        if ($javascript_enabled != "true") {
            $zeigUnterkategorien = true; // Dieses Flag wird hier true gesetzt, weil alle Kategorien aufgeklappt werden sollen (kein JavaScript)
        }
        else {
            $zeigUnterkategorien = false; // Dieses Flag wird true, wenn die Unterkategorien einer Hauptkategorie angezeigt werden sollen
        }
        if ($selected == $value->Kategorie_ID) {
            // Ausgewaehlte Hauptkategorie ohne Unterkategorie darstellen
            // Wenn die bereits selektierte Kategorie Unterkategorien hat, soll eine entsprechende Meldung erscheinen (ev. alter Kommentar)
            $link_string = "<a class='left' href='USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=1&amp;Kategorie_ID=".$value->Kategorie_ID."' target='content'>\n";
            echo $link_string;
            echo "<img src='./Bilder/kat_selected.gif' alt='[X]' border='0'>\n</a>\n";
            echo "</td>\n<td class='left' align='".$darst['td_2_align']."' valign='".$darst['td_2_valign']."' colspan='2'>\n";
            echo "".$link_string;
            echo $value->Name."</a>"."\n";
        }
        elseif ($open == $value->Kategorie_ID){
            // Ausgewaehlte (geoeffnete) Hauptkategorie mit Unterkategorie(n) darstellen
            $link_string = '<a class="left" href="USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=4&amp;open=leer">'."\n";
            echo $link_string;
            echo "<img src='./Bilder/kat_minus.gif' alt='[-]' border='0'>\n</a>\n";
            echo "</td>\n<td class='left' align='".$darst['td_2_align']."' valign='".$darst['td_2_valign']."' colspan='2'>\n";
            echo " ".$link_string;
            echo $value->Name."</a>"."\n";
            $zeigUnterkategorien = true;
        }
        elseif ($value->kategorienanzahl() > 0) {
            // Nicht ausgewaehlte (noch nicht geoeffnete) Hauptkategorie mit Unterkategorie(n) darstellen
            $ersteUnterkategorie = $value->getFirstUkat(); //Die erste Unterkategorie auslesen
            // Nur die Kategorie oeffnen, ohne Ukat: $link_string = "<a class='left' href='USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=4&amp;open=".urlencode($value->Kategorie_ID)."' target=left>";
            $link_string = "<a class='left' href='JavaScript:zweiframes(\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=1&amp;Kategorie_ID=".$ersteUnterkategorie->Kategorie_ID."\",\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=4&amp;open=".urlencode($value->Kategorie_ID)."&amp;active_Ukat_ID=".$ersteUnterkategorie->Kategorie_ID."\")'>\n";
            $link_string_ohne_js = "<a class='left' href=\"../index.php?Kategorie_ID=".$ersteUnterkategorie->Kategorie_ID."\" target=\"_parent\">\n";
            if ($javascript_enabled != "true") {$link_string = $link_string_ohne_js;} // Wenn der Browser kein JavaScript beherrscht anderen Link verwenden
            echo $link_string;
            echo "<img src='./Bilder/kat_plus.gif' alt='[+]' border='0'>\n</a>\n";
            echo "</td>\n<td class='left' align='".$darst['td_2_align']."' valign='".$darst['td_2_valign']."' colspan='2'>\n";
            echo " ".$link_string;
            echo $value->Name."</a>"."\n";
        }
        else {
            // Nicht ausgewaehlte Hauptkategorie ohne Unterkategorie darstellen
            $link_string = "<a class='left' href='JavaScript:zweiframes(\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=1&amp;Kategorie_ID=".$value->Kategorie_ID."\",\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=4&amp;selected=".$value->Kategorie_ID."&amp;open=".$value->Kategorie_ID."\")'>\n";
            $link_string_ohne_js = "<a class='left' href=\"../index.php?Kategorie_ID=".$value->Kategorie_ID."\" target=\"_parent\">\n";
            if ($javascript_enabled != "true") {$link_string = $link_string_ohne_js;} // Wenn der Browser kein JavaScript beherrscht anderen Link verwenden
            echo $link_string;
            echo "<img src='./Bilder/kat_leer.gif' alt='[ ]' border='0'>\n</a>\n";
            echo "</td>\n<td class='left' align='".$darst['td_2_align']."' valign='".$darst['td_2_valign']."' colspan='2'>\n";
            echo " ".$link_string;
            echo $value->Name."</a>"."\n";
        }
        echo "</td>\n</tr>\n";
        // Wenn fuer diese Kategorie ihre Unterkategorien angezeigt werden sollen (Bei abgeschaltetem JavaScript immer der Fall):
        if ($zeigUnterkategorien){
            $myUnterkategorien = array();
            $myUnterkategorien = $value->getallkategorien(); //Alle Unterkategorien in einen Array kopieren
            for($i=0;$i < $value->kategorienanzahl();$i++){
            echo "<tr class='left'>\n<td class='left' style=\"text-decoration:none;\"><img src=\"Bilder/spacer.gif\" width=\"1\" alt=\" \"></td><td class='left' width='".$darst['td_1_width']."' align='".$darst['td_1_align']."' valign='".$darst['td_1_valign']."'>";
                if ($selected == $myUnterkategorien[$i]->Kategorie_ID || $active_Ukat_ID == $myUnterkategorien[$i]->Kategorie_ID) {
                    echo "<!-- START MARKER -->\n";
                    // Ausgewaehlte Unterkategorie darstellen
                    $link_string = "<a class='left' href='javascript:zweiframes(\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=1&amp;Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."\",\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=4&amp;selected=".$myUnterkategorien[$i]->Kategorie_ID."&amp;open=".$value->Kategorie_ID."\")'>\n";
                    $link_string_ohne_js = "<a class='left' href=\"../index.php?Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."\" target=\"_parent\">\n";
                    if ($javascript_enabled != "true") {$link_string = $link_string_ohne_js;} // Wenn der Browser kein JavaScript beherrscht anderen Link verwenden                    echo $link_string;
                    echo $link_string;
                    echo "<img src='./Bilder/kat_selected.gif' alt='[X]' border='0'>\n</a>\n";
                    echo "</td>\n<td class='left' width='".$darst['ukat_width']."px' align='".$darst['td_2_align']."' valign='".$darst['td_2_valign']."'>\n";
                    echo " ".$link_string;
                    echo $myUnterkategorien[$i]->Name."</a>\n";
                    echo "<!-- END MARKER -->\n";
                }
                else {
                    // Nicht ausgewaehlte Unterkategorie darstellen
                    $link_string = "<a class='left' href='javascript:zweiframes(\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=1&amp;Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."\",\"USER_ARTIKEL_HANDLING_AUFRUF.php?javascript_enabled=true&amp;darstellen=4&amp;selected=".$myUnterkategorien[$i]->Kategorie_ID."&amp;open=".$value->Kategorie_ID."\")'>\n";
                    $link_string_ohne_js = "<a class='left' href=\"../index.php?Kategorie_ID=".$myUnterkategorien[$i]->Kategorie_ID."\" target=\"_parent\">\n";
                    if ($javascript_enabled != "true") {$link_string = $link_string_ohne_js;} // Wenn der Browser kein JavaScript beherrscht anderen Link verwenden                    echo $link_string;                    echo $link_string;
                    echo $link_string;
                    echo "<img src='./Bilder/kat_leer.gif' alt='[ ]' border='0'>\n</a>\n";
                    echo "</td>\n<td class='left' width='".$darst['ukat_width']."px' align='".$darst['td_2_align']."' valign='".$darst['td_2_valign']."'>\n";
                    echo " ".$link_string;
                    echo $myUnterkategorien[$i]->Name."\n</a>\n";
                }
            echo "</td>\n</tr>\n";
            }// End for
        }//End if zeigUnterkategorien
    }// End foreach myKategorien
    echo "</table>";
}
// -----------------------------------------------------------------------
// darstellen = 5: <<Deprecated call>>
// *** Wird seit v.1.05 nicht mehr verwendet und wird folglich irgendwann entfernt werden ***
// Meldung, dass man doch bitte eine Unterkategorie anwaehlen soll:
// -----------------------------------------------------------------------
else if ($darstellen == 5) {
    echo "<h1 class='content'>Bitte w&auml;hlen Sie eine Unterkategorie von $Kategoriename aus</h1><br>";
}
// -----------------------------------------------------------------------
// Meldung wenn keine darstellen-Variable mitangegeben wurde --> Dieser Fall sollte eigentlich nie eintreten!
else {
    echo "<h1 class='content'>Fehlerhafter Aufruf! Wahrscheinlich wurde vergessen die darstellen-Variable mit zu &uuml;bergenben</h1><br>";
    echo "<blockquote><a class='content' href='../index.php'>Zur&uuml;ck zum Shop</a></blockquote>";
}//End else

// HTML-Dokument abschliessen
echo "</body>\n";
echo "</html>\n";

// End of file-------------------------------------------------------------
?>
