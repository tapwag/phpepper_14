<?php
// Filename: SHOP_ADMINISTRATION_ARTIKEL.php
//
// Modul: Darstellungs-Module - SHOP_ADMINISTRATION
//
// Autoren: José Fontanil & Reto Glanzmann
//
// Zweck: HTML-Darstellung eines Artikels zur Administration
//        (Um Artikel einzugeben oder abzuaendern)
//
// Sicherheitsstatus:        *** ADMIN ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: SHOP_ADMINISTRATION_ARTIKEL.php,v 1.48 2003/05/24 18:41:31 fontajos Exp $
//
// -----------------------------------------------------------------------
// Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
// wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
$SHOP_ADMINISTRATION_ARTIKEL = true;

// include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
// Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
// Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

// Einbinden der benoetigten Module (PHP-Scripts)
// Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
if (!isset($SHOP_ADMINISTRATION)) {include("SHOP_ADMINISTRATION.php");}
if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}

// Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
// $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
extract($_GET);
extract($_POST);

// ------------------------------------------------------------------------
// Diese Funktion stellt den im Argument gelieferten Artikel dar und leitet die Daten dann
// zur Uebertragung in die Datenbank weiter an die Bild-Eintragung (bild_up.php)
// Argument: Ein Artikel als Artikel-Objekt (Definition siehe artikel_def.php)
// Rueckgabewert: HTML-Teil einer Page (Formular, ohne Head, ohne Body-Tags)
function darstellenArtikel($myArtikel) {

    // Hier werden an einem Zentralen Ort in dieser Funktion alle Artikel aus dem
    // Artikel-Objekt ausgepackt und lokalen Variablen zugeordnet die weiter unten im HTML-Teil
    // dargestellt werden.

    // Artikel-Attribute:
    $Artikel_ID = $myArtikel->artikel_ID;
    $Artikel_Nr = $myArtikel->artikel_Nr;
    $Name = $myArtikel->name;
    $Beschreibung = $myArtikel->beschreibung;
    $letzteAenderung = $myArtikel->letzteAenderung;
    $Gewicht = $myArtikel->gewicht;
    $MwSt_Satz_vorgewaehlt = $myArtikel->mwst_satz;
    $Preis = $myArtikel->preis;
    $Aktionspreis = $myArtikel->aktionspreis;
    $Link = $myArtikel->link;

    // Optionen:
    $counter = 1;
    foreach(($myArtikel->getalloptionen()) as $keyname => $value){
        $Optionsname = "Optionstext$counter";
        $Preisname = "Preisdifferenz$counter";
        $$Optionsname = $keyname;
        $$Preisname = $value;
        $counter++;
    }
    $anzahloptionen = $counter-1;

    // Variationen:
    $counter = 1;
    foreach(($myArtikel->getallvariationen()) as $keyname => $value){
        $Variationsname = "Variationstext$counter";
        $Aufpreisname = "Aufpreis$counter";
        $$Variationsname = $keyname;
        $$Aufpreisname = $value;
        $counter++;
    }
    $anzahlvariationen = $counter-1;

    // Variationen-Gruppen
    $counter = 1;
    foreach(($myArtikel->getallvar_gruppe()) as $keyname => $value){
        $Gruppenname = "Gruppe$counter";
        $$Gruppenname = $value;
        $counter++;
    }

    // Die Waehrung (SFr., CHF, DEM, DM, Ös, ...)
    // Die benutzte Funktion ist in USER_ARTIKEL_HANDLING definiert
    $Waehrung = getWaehrung();

    // Die Gewichts-Masseinheit (kg, g, ml, l, ...)
    // Die benutzte Funktion ist in USER_ARTIKEL_HANDLING definiert
    $Gewichts_Masseinheit = getGewichts_Masseinheit();

    // Ermitteln, ob der Shop die Versandkosten per Gewicht abrechnet. Ist dies nicht
    // der Fall, werden die Eingabefelder fuer die Gewichtseingaben nicht angezeigt.
    $Versandkostensettings = new Versandkosten;
    $Versandkostensettings = getversandkostensettings(1);
    $Versand_Gewicht = strtoupper($Versandkostensettings->Abrechnung_nach_Gewicht);

    // Aus den Shopsettings auslesen, wie viele Options- und Variationsfelder mindestens
    // angezeigt werden, und wie viele leere.
    $var_opt_anz_array = getvaroptinc();
    $Opt_inc = $var_opt_anz_array[0]; // Anzahl Leerfelder Optionen
    $Var_inc = $var_opt_anz_array[1]; // Anzahl Leerfelder Variationen
    $Opt_anz = $var_opt_anz_array[2]; // Mindestanzahl Optionsfelder
    $Var_anz = $var_opt_anz_array[3]; // Mindestanzahl Variationsfelder
    $anzahl_var_grp = $var_opt_anz_array[4]; // Anzahl Variationsgruppen
    $eingabefelder_anz = $var_opt_anz_array[5]; // maximale Anzahl Texteingabefelder pro Artikel
    if ($anzahl_var_grp < 1) { $anzahl_var_grp = 1; }

    // Wir muessen noch wissen wie die Kategorie dieses Artikels heisst.
    // Bei einem neuen Artikel ist diese logischerweise noch nicht def.
    // getKategorie_eines_Artikels($Artikel_ID) ist in USER_ARTIKEL_HANDLING.php definiert
    if (empty($Artikel_ID)){
        $Kategorienamen = "";
        $MwSt_default_Satz = "MwSt_default_Satz"; //Noch keine Kategorie definiert MwSt-default-Satz 'leer'
    } // end of if empty Artikel_ID
    else {
        $Kat = key(getKategorie_eines_Artikels($Artikel_ID));
        $Ukat = current(getKategorie_eines_Artikels($Artikel_ID));
        if ($Kat == $Ukat) {
            $Kategorienamen = $Kat;
            $MwSt_default_Satz =  getDefaultMwStSatz(addslashes($Kat), "");
        } // end of if Kat = Ukat
        else {
            $Kategorienamen = "$Kat -> $Ukat";
            // Auslesen des in der Kategorie definierten MwSt-default-Satzes
            $MwSt_default_Satz =  getDefaultMwStSatz(addslashes($Kat), addslashes($Ukat));
        } // end of else Kat = Ukat
    } // end of else empty Artikel_ID

    // Auslesen der MwSt-Settings
    $array_of_mwstsettings = getmwstsettings();

    // Nun folgt der HTML-Code um den Artikel darzustellen: (Zuerst nur JavaScript Validationen)
?>

  <script language="JavaScript">
  <!--
  function chkFormular() {
      if(document.Formular.Name.value == "") {
          alert("Bitte einen Artikelnamen eingeben!");
          document.Formular.Name.focus();
          return false;
      }
       if(document.Formular.Preis.value == "") {
          alert("Bitte einen Preis für den Artikel eingeben!");
          document.Formular.Preis.focus();
          return false;
      }

      // Preis auf richtige Eingabe überprüfen
      var punkt = 0;
      var nummerisch = 1;
      for(i=0;i<document.Formular.Preis.value.length;++i) {
          if(document.Formular.Preis.value.charAt(i) == "."){
              punkt++;
          }
          if((document.Formular.Preis.value.charAt(i) < "0"
          || document.Formular.Preis.value.charAt(i) > "9") && document.Formular.Preis.value.charAt(i) !="." )
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Preis enthält ungültige Zeichen!");
          document.Formular.Preis.focus();
          return false;
      }
      if(punkt > 1) {
          alert("Preis enthält zu viele '.'!");
          document.Formular.Preis.focus();
          return false;
      }

      // Aktionspreis auf richtige Eingabe überprüfen (Aktionspreis noch nicht implementiert!!)
      // var punkt = 0;
      // var nummerisch = 1;
      // for(i=0;i<document.Formular.Aktionspreis.value.length;++i) {
      //    if(document.Formular.Aktionspreis.value.charAt(i) == "."){
      //        punkt++;
      //    }
      //    if((document.Formular.Aktionspreis.value.charAt(i) < "0"
      //    || document.Formular.Aktionspreis.value.charAt(i) > "9") && document.Formular.Aktionspreis.value.charAt(i) !="." )
      //    nummerisch = 0;
      // }

      // if(nummerisch == 0) {
      //    alert("Aktionspreis enthält ungültige Zeichen!");
      //    document.Formular.Aktionspreis.focus();
      //    return false;
      // }
      // if(punkt > 1) {
      //    alert("Aktionspreis enthält zu viele '.'!");
      //    document.Formular.Aktionspreis.focus();
      //    return false;
      // }

      <?php // Ueberpruefung nur Vornehmen, wenn auch eine Gewichtsberechnung verwendet wird
      if ($Versand_Gewicht == "Y"){
      ?>

      // Gewicht auf richtige Eingabe überprüfen
      var punkt = 0;
      var nummerisch = 1;
      for(i=0;i<document.Formular.Gewicht.value.length;++i) {
          if(document.Formular.Gewicht.value.charAt(i) == "."){
              punkt++;
          }
          if((document.Formular.Gewicht.value.charAt(i) < "0"
          || document.Formular.Gewicht.value.charAt(i) > "9") && document.Formular.Gewicht.value.charAt(i) !="." )
          nummerisch = 0;
      }
      if(nummerisch == 0) {
          alert("Gewicht enthält ungültige Zeichen!");
          document.Formular.Gewicht.focus();
          return false;
      }
      if(punkt > 1) {
          alert("Gewicht enthält zu viele '.'!");
          document.Formular.Gewicht.focus();
          return false;
      }

      <?php
      } // end of if  (Jetzt kommt das eigentliche Eingabeformular - HTML)
      ?>

      // Wenn der Artikel als 'neuer Artikel' gespeichert wird, muss mindestens der Artikelname verschieden sein
      if(document.Formular.neuerArtikel.checked && document.Formular.Name.value == "<?php echo addslashes($Name); ?>"){
          alert("Bitte geben Sie für den neuen Artikel einen eindeutigen Artikelnamen ein!");
          document.Formular.Name.focus();
          return false;
      } // end of if
  }  // end of function chkFormular
  //-->
  </script>

  <form action='./SHOP_ADMINISTRATION_AUFRUF.php' method="post" name='Formular' onSubmit="return chkFormular()">
    <table border="0" cellpadding="0" cellspacing="3">
      <tr>
        <td colspan="3"><P><H1><B>SHOP ADMINISTRATION</B></H1></P></td>
      </tr>
      <tr>
        <td colspan=3>
          <B>Letzte Aktualisierung des Artikels:
           <?php
             // amerikanisches Datumsformat in europaeisches umwandeln und ausgeben
             $temp_datum = explode("-", $letzteAenderung);
             echo " $temp_datum[2].$temp_datum[1].$temp_datum[0]";
           ?>
          </B></td>
      </tr>
      <tr>
        <td colspan=3>&nbsp</td>
      </tr>
      <tr>
        <td>Name:</td>
        <td>
          <input type="text" name="Name" size="52" maxlength="128" value="<?php echo htmlspecialchars($Name); ?>">
        </td>
        <td rowspan=4 valign=top><center>
          Artikelbild (falls vorhanden):<br>
          <?php
            // wenn vorhanden, Artikelbild ausgeben
            if (!empty($Artikel_ID) && $myArtikel->bild_klein != ""){
                echo '<img src="../ProdukteBilder/'.$myArtikel->bild_klein.'" border="0">';
            }
          ?>
        </center></td>
      </tr>
      <tr>
        <td>Artikel Nr.:</td>
        <td>
          <input type='text' name='Artikel_Nr' size='52' maxlength='128' value="<?php echo htmlspecialchars($Artikel_Nr); ?>">
        </td>
      </tr>
      <tr>
        <td valign=top>Beschreibung:</td>
        <td style="font-size:15px; font-family: Courier, Courier New, Monaco" >
          <textarea name="Beschreibung" cols="50" rows="10" wrap=physical><?php echo htmlspecialchars($Beschreibung); ?></textarea>
          &nbsp;&nbsp;&nbsp;
        </td>
      </tr>
      <tr>
        <td>Link:</td>
        <td><input type="text" name="Link" size="52" maxlength="255" value="<?php echo htmlspecialchars($Link); ?>">
        <BR>ACHTUNG: Bei Links welche auf andere Rechner verweisen,<BR>http:// am Anfang nicht vergessen!</td>
      </tr>
      <tr>
        <td colspan=3>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="1">Preis:</td>
        <td colspan="2"><input type="text" name="Preis" size="12" maxlength="12" value="<?php echo "$Preis";?>">
          <label><?php echo "$Waehrung"; ?></label>
<?php
          // Anzeige, ob die Preise shopweit inkl. oder exkl. MwSt sind
          echo " [Preis ist ";
          if ($array_of_mwstsettings[0]->Preise_inkl_MwSt == "Y") {echo "inkl.";} else {echo "exkl.";}
          echo " MwSt.]";
?>
          </td>
        <td></td>
      </tr>
<?php
    if (getmwstnr() != "0" && getmwstnr() != "") {
      // MwSt-Satz Eingabefeld (Dropdown-Liste):
      echo "<tr>\n";
      echo "  <td colspan=\"1\">MwSt-Satz:</td>\n";
      echo "  <td colspan=\"2\">\n";
      echo "<select name=\"MwSt\" size=\"1\">\n";
      // Erste Option ist immer das Kategoriendefault zu verwenden
      echo "<option value=\"$MwSt_default_Satz\" ";
      if ($Kategorienamen == "") {echo "selected";}
      echo ">";
      // Wenn man einen neuen Artikel erstellt, weiss man noch nicht in welcher Kategorie er eingeordnet sein wird
      // In diesem Fall wird der MwSt-Default-Satz der Kategorie nicht ausgegeben
      if (is_numeric($MwSt_default_Satz)) {echo "$MwSt_default_Satz% (Default MwSt-Satz der Kategorie $Kategorienamen)";}
      else {echo "Default MwSt-Satz der Kategorie verwenden";}
      // Alle gespeicherten MwSt-Saetze zur Auswahl auflisten
      foreach ($array_of_mwstsettings as $value) {
          // Jeden MwSt-Satz anzeigen, aussser den, welcher schon Kategoriendefault ist und den vom 'Porto und Verpackung' Posten
          if ($value->MwSt_Satz != $MwSt_default_Satz && $value->Beschreibung != "Porto und Verpackung") {
              echo "<option value=\"".$value->MwSt_Satz."\" ";
              if ($Kategorienamen != "" && $value->MwSt_Satz == $MwSt_Satz_vorgewaehlt) {echo "selected";}
              echo ">".$value->MwSt_Satz."% (".$value->Beschreibung.")";
          }
      }// end of foreach
      // Zusaetzlich noch die Option MwSt-frei anbieten
      echo "<option value=\"0\" ";
      if ($Kategorienamen != "" && $MwSt_Satz_vorgewaehlt == 0) {echo "selected";}
      echo ">0% (MwSt-frei)";
      echo "</select>\n";
      echo "  </td>\n";
      echo "  <td></td>\n";
      echo "</tr>\n";
    } // End if MwSt
?>

      <tr>
        <td colspan="1">
<?php /*Aktionspreis: (Noch deaktiviert, weil zusaetzliche Funktionalitaet fehlt)*/?>
        </td>
        <td colspan="2">
<?php /*<input type="text" name="Aktionspreis" size="12" maxlength="12" value="<?php echo "$Aktionspreis";?>">
          <label><?php echo "$Waehrung"; ?></label>*/?>
        </td>
      </tr>
      <tr>
      <?php // Eingabefeld fuer Gewicht nur ausgeben, wenn die Versandkostenabrechnung nach Gewicht verwendet wird
      if ($Versand_Gewicht == "Y"){
          echo '<td colspan="1">Gewicht:</td>';
          echo '<td colspan="2"><input type="text" name="Gewicht" size="12" maxlength="12" value="'.$Gewicht.'">&nbsp;';
          echo '<label>'.$Gewichts_Masseinheit.'</label></td>';
      } // end of if
      else {
          echo '<td colspan="3"></td>';
      } // end of else
      ?>
      </tr>
    </table>

    <table  border="0" cellpadding="0" cellspacing="3">
      <tr>
        <td colspan="6">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="6"><b>Optionen</b>:</td>
      </tr>
      <tr>
        <td colspan="3">Optionstext</td>
        <td>Aufpreis</td>
        <td colspan="2">
        <?php
        if ($Versand_Gewicht == "Y"){
            echo "Mehrgewicht";
        } // end of if
        ?>
        </td>
      </tr>
      <?php
        // Hier werden alle Optionen, die fuer den Artikel schon eingegeben worden sind, angezeigt. Es werden
        // mindestens $Opt_anz Optionsfelder angezeigt. Sind mehr Optionen vorhanden, werden $Opt_inc Leerfelder
        // angezeigt
        if ($anzahloptionen < $Opt_anz){
            if ($anzahloptionen <= ($Opt_anz - $Opt_inc)){
                $felder = $Opt_anz;
            } // end of if
            else{
            $felder = $anzahloptionen + $Opt_inc;
            } // end of else
        } // end of if
        else {
            $felder = $anzahloptionen + $Opt_inc;
        } // end of else

        for ($i = 1; $i <= ($felder);$i++){
            $Optionsname = "Optionstext$i";
            $Preisname = "Preisdifferenz$i";
            echo "<tr>";
            echo "<td colspan='3'><input type='text' name='Option$i' size='45' maxlength='255' value=\"";
            // Da PHP4 bei einem echo "$$Optionsname" nur das erste Wort eines Strings ausgibt, folgendes Konstrukt:
            $Option = $$Optionsname;
            echo htmlspecialchars($Option);
            echo "\">&nbsp;&nbsp;</td>";
            echo "<td align='left'><input type='text' name='Preisdifferenz$i' size='12' maxlength='12' value='";
            $Preis = $$Preisname;
            echo "$Preis";
            echo "'>&nbsp;";
            echo "<label>$Waehrung</label>&nbsp;</td>";
            echo "<td align='left' colspan='2'>";
            if ($Versand_Gewicht == "Y"){
                echo "<input type='text' name='Gewicht_Opt[$i]' size='12' maxlength='12' value='";
                echo $myArtikel->optionen_gewicht[$Option];
                echo "'>&nbsp;";
                echo '<label>'.$Gewichts_Masseinheit.'</label>';
            } // end of if
            echo "</td></tr>";
        } // end of for i
      ?>
      <tr>
        <td colspan="6">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="6"><b>Variationen</b>:</td>
      </tr>
      <tr>
        <td colspan="3">Variationstext</td>
        <td>Preisdifferenz</td>
        <td>
        <?php
        if ($Versand_Gewicht == "Y"){
            echo "Mehrgewicht";
        } // end of if
        ?>
        </td>
        <td>Variationsgrp.</td>
      </tr>
      <?php

        // Hier werden alle Variationen, die fuer den Artikel schon eingegeben worden sind, angezeigt. Es werden
        // mindestens $Var_anz Variationsfelder angezeigt. Sind mehr Variationen vorhanden, werden $Var_inc Leerfelder
        // angezeigt
        if ($anzahlvariationen < $Var_anz){
            if ($anzahlvariationen <= ($Var_anz - $Var_inc)){
                $felder = $Var_anz;
            } // end of if
            else{
            $felder = $anzahlvariationen + $Var_inc;
            } // end of else
        } // end of if
        else {
            $felder = $anzahlvariationen + $Var_inc;
        } // end of else

        for ($i = 1; $i <= ($felder);$i++){
            $Variationsnamen = "Variationstext$i";
            $Aufpreisnamen = "Aufpreis$i";
            $Gruppe = "Gruppe$i";
            echo "<tr>";
            echo "<td colspan='3'><input type='text' name='Variation$i' size='45' maxlength='255' value=\"";
            $Variation = $$Variationsnamen;
            echo htmlspecialchars($Variation);
            echo "\">&nbsp;&nbsp;</td>";
            echo "<td align='left'><input type='text' name='Aufpreis$i' size='12' maxlength='12' value='";
            $Apreis = $$Aufpreisnamen;
            echo "$Apreis";
            echo "'>&nbsp;";
            echo "<label>$Waehrung</label></td>";
            echo "<td align='left'>";

            // Gewichtsfelder nur ausgeben, wenn auch die Versandkosten nach Gewicht berechnet werden
            if ($Versand_Gewicht == "Y"){
                echo "<input type='text' name='Gewicht_Var[$i]' size='12' maxlength='12' value='";
                echo $myArtikel->variationen_gewicht[$Variation];
                echo "'>&nbsp;";
                echo '<label>'.$Gewichts_Masseinheit.'</label>';
            } // end of if

            echo "</td><td><center>";
            // Drop-Down Feld ausgeben, damit jede Variation einer Variationsgruppe zugeordnet werden kann
            echo "<select name='Gruppe$i' size=1 >";
            for ($g=1; $g<=$anzahl_var_grp; $g++){
                echo "<option value='$g' ";
                if ($$Gruppe == $g) { echo "selected"; }
                echo ">$g";
            } // end of for
            echo "</select>";
            echo "</center></td></tr>";
        }
      ?>
      </table>
      <table  border="0" cellpadding="0" cellspacing="3">
      <tr>
        <td colspan=4>&nbsp;</td>
      </tr>
      <tr>
        <td colspan=4><b>Variationsgruppen:</b></td>
      </tr>
      <tr>
        <td></td>
        <td>Beschreibung (z.B. Farbe, Länge, Grösse,..)</td>
        <td>Darstellung</td>
        <td></td>
      </tr>
      <?php
      for ($s=1; $s<=$anzahl_var_grp; $s++){
          echo "<tr><td>$s.&nbsp</td>";
          echo "<td><input type='text' name='Gruppentext[".$s."]' size=40 maxlength=255 ";
          echo "value='".urldecode($myArtikel->var_gruppen_text[$s])."'>&nbsp;&nbsp;</td>";
          echo "<td><select name='Gruppe_darstellen[".$s."]' size=1 >";
          echo "<option value='radio' ";
          if ($myArtikel->var_gruppen_darst[$s] == "radio") { echo "selected"; }
          echo ">Radio-Button<option value='dropdown' ";
          if ($myArtikel->var_gruppen_darst[$s] == "dropdown") { echo "selected"; }
          echo ">Dropdown-Liste</select></td></tr>";
      } // end of for
      // in einem hidden-Feld die Information überbegeben, wie viele Gruppen verwendet werden
      echo "</table>";
      echo "<input type='hidden' name='anzahl_var_grp' value='$anzahl_var_grp'>";

      // Eingabefelder und Parameter fuer Zusatzeingabefelder ausgeben, falls vorhanden
      if ($eingabefelder_anz > 0){
          echo '<table  border="0" cellpadding="0" cellspacing="3">';
          echo "<tr>";
          echo "  <td colspan=4>&nbsp;</td>";
          echo "</tr>";
          echo "<tr>";
          echo "  <td colspan=4><b>Texteigabefelder:</b></td>";
          echo "</tr>";
          echo "<tr>";
          echo "  <td>Beschreibungstext</td>";
          echo "  <td>Feld-<br>l&auml;nge</td>";
          echo "  <td>max.<br>L&auml;nge</td>";
          echo "  <td>Feld-<br>h&ouml;he</td>";
          echo "</tr>";

          for ($s=1; $s<=$eingabefelder_anz; $s++){
              // Daten aufbereiten
              $zusatzfeld_param = zusatzfeld_parameter($myArtikel->zusatzfelder_param[$s-1]);
              if ($zusatzfeld_param[laenge_feld] == "" || $zusatzfeld_param[laenge_feld] <= 0){
                  $zusatzfeld_param[laenge_feld] = 20;
              }  // end of if
              if ($zusatzfeld_param[laenge_max] == "" || $zusatzfeld_param[laenge_max] <= $zusatzfeld_param[laenge_feld]){
                  $zusatzfeld_param[laenge_max] = $zusatzfeld_param[laenge_feld];
              }  // end of if
              if ($zusatzfeld_param[hoehe_feld] == "" || $zusatzfeld_param[hoehe_feld] <= 0){
                  $zusatzfeld_param[hoehe_feld] = 1;
              }  // end of if
              echo "<tr>";
              echo "<td><input type='text' name='eingabefeld[$s]' size=35 maxlength=255 ";
              echo "value='".htmlspecialchars($myArtikel->zusatzfelder_text[$s-1])."'>&nbsp;&nbsp;</td>";
              echo "<td align=center><input type='text' name='eingabefeld_laenge[$s]' size=2 maxlength=2 ";
              echo "value='".$zusatzfeld_param[laenge_feld]."'>&nbsp;&nbsp;</td>";
              echo "<td align=center><input type='text' name='eingabefeld_max[$s]' size=4 maxlength=4 ";
              echo "value='".$zusatzfeld_param[laenge_max]."'>&nbsp;&nbsp;</td>";
              echo "<td align=center><input type='text' name='eingabefeld_hoehe[$s]' size=2 maxlength=2 ";
              echo "value='".$zusatzfeld_param[hoehe_feld]."'>&nbsp;&nbsp;</td>";
              echo "</tr>";
          } // end of for
          echo "</table>";
          echo "<input type='hidden' name='eingabefelder_anz' value='$eingabefelder_anz'>";
      } // end of if
      ?>

      <table  border="0" cellpadding="0" cellspacing="3">
      <tr>
        <td colspan=6>&nbsp;</td>
      </tr>
    <?php
      // Hier wird unterschieden ob es ein neuer Artikel ist (Artikel_ID = empty)
      // oder ob es schon ein bestehender ist. Je nachdem wird die entsprechende
      // Artikel-ID weitergeleitet (an bild_up.php)
      if (!empty($Artikel_ID)){
          echo "<tr>\n";
          echo "  <input type='hidden' name='Artikel_ID' value='$Artikel_ID' title='Artikel_ID'>\n";
          echo "  <td colspan=\"6\"><input type=checkbox name='neuerArtikel' value='ja'>Artikel als 'neuen Artikel' speichern<br><br></td>\n";
          echo "</tr>\n";
      }
      else {
          echo "<input type='hidden' name='Artikel_ID' value='' title='Artikel_ID'>\n";
      }
    ?>
      <tr>
        <td colspan="6" valign="middle">
          <input type="hidden" name=darstellen value=101>
          <input type=image src="../Buttons/bt_weiter_admin.gif" border="0">
          <a href="./Shop_Einstellungen_Menu_1.php" title="Abbrechen">
            <img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a>
          <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Artikel&Artikel_ID=<?php echo $Artikel_ID; ?>')">
            <img src="../Buttons/bt_hilfe_admin.gif" border="0" alt="Hilfe"></a>
        </td>
      </tr>
    </table>
  </form>
<?php
  }//End function darstellenArtikel

  // End of file-----------------------------------------------------------------------------------------------
?>
