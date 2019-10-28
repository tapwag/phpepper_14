<?php
  // Filename: SHOP_KUNDE.php
  //
  // Modul: Aufruf-Module - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Funktionen um die Kundendaten des Shops zu veraendern
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_KUNDE.php,v 1.23 2003/05/24 18:41:33 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_KUNDE = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
  if (!isset($USER_BESTELLUNG)) {include("USER_BESTELLUNG.php");}
  if (!isset($SHOP_ADMINISTRATION)){include("SHOP_ADMINISTRATION.php");}

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  extract($_GET);
  extract($_POST);

  // HTML-Kopf, der bei jedem Aufruf des Files ausgegeben wird
?>
<HTML>
    <HEAD>
        <TITLE>Kundenmanagement</TITLE>
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

// -----------------------------------------------------------------------
// Abspeichern der Einstellungen
//
// -----------------------------------------------------------------------

if ($darstellen == 10){

    // neues Attributobjekt instanzieren
    $myAttribut = new Attribut;

    // Attributarrays in Attributobjekt abfüllen
    for ($zaehl = 0; $zaehl <= (count($Attribut_ID)-1); $zaehl++){
    $myAttribut->putAttribut($Attribut_ID[$zaehl], $Namen[$zaehl], $Wert[$zaehl],
                             $verwenden[$zaehl], $speichern[$zaehl], $pruefen[$zaehl],
                             $Positions_Nr[$zaehl]);
    }

    // Attributobjekt in Datenbank speichern
    $ok = setAttributobjekt($myAttribut);

    // falls Attributobjekt erfolgreich gespeichert werden konnte..
    if ($ok == true){
        echo "<p><h1><b>SHOP ADMINISTRATION</b></h1></p>";
        echo "<h4>Das Speichern aller Kundenattribute war erfolgreich!<h4><br>";
    } // end of if
    else {
        echo "<h4>Fehler beim Speichern der Kundenattribute!</h4>";
    } // end of else

    echo "<a href='./Shop_Einstellungen_Menu_1.php'><IMG src='../Buttons/bt_weiter_admin.gif' border='0'alt='weiter'></a>";

?>

<?php
  } // end of if darstellen == 10



// -----------------------------------------------------------------------
// Wird ausgeführt, wenn dieses File nicht mit einem speziellen darstellen-
// Wert aufgerufen wird (beim direkten Aufruf)
// -----------------------------------------------------------------------
else {

  // Anzahl fest vordefinierter Felder
  $anz_vordef = 14;

  // Zaehler grauen Balken
  $zaehler = 1;

  // Attributobjekt aus Datenbank holen
  $myAttribut = getAttributobjekt();

  // Anzahl Zusatzfelder
  $anz_zusatz = ($myAttribut->attributanzahl())-$anz_vordef;

  // Die verschiedenen Werte und Einstellungen in Arrays abfuellen
  $Namen = $myAttribut->getallName();
  $verwenden = $myAttribut->getallanzeigen();
  $speichern = $myAttribut->getallin_DB();
  $pruefen = $myAttribut->getallEingabe_testen();

  // Arrays, die nicht verwendet werden, jedoch schon fuer einen spaeteren Gebrauch zur Verfuegung stehen
  $Wert = $myAttribut->getallWert();
  $Attribut_ID = $myAttribut->getallAttribut_ID();
  $Positions_Nr = $myAttribut->getallPositions_Nr();

?>
  <p><h1><b>SHOP ADMINISTRATION</b></h1></p>
  <p><h3><b>Kundenattribute bearbeiten</b></h3></p>

  <form action='./SHOP_KUNDE.php' method="post" title="Versandkosten_Eingabe">
    <input type=hidden name=darstellen value=10>
    <table border=0 cellspacing=0>
      <tr bgcolor=#CCCCCC>
        <td><b>Feldbezeichnung&nbsp;&nbsp;</b></td>
        <td><b>&nbsp;&nbsp;verwenden&nbsp;&nbsp;</b></td>
        <td><b>&nbsp;&nbsp;pr&uuml;fen&nbsp;&nbsp;</b></td>
        <td><b>&nbsp;&nbsp;speichern&nbsp;&nbsp;</b></td>
      </tr>


<?php

  // Hauptfelder ausgeben
  for ($zaehl = 0; $zaehl <= ($myAttribut->attributanzahl()-1); $zaehl++){

      // die Einstellungen für die Bemerkungen müssen auch noch ausgegeben werden
      if ($zaehl < ($anz_vordef-1) || $zaehl > ($anz_vordef+$anz_zusatz-2)){

          // jede zweite Zeile soll der Uebersichtlichkeit halber grau hinterlegt werden
          if ($zaehler % 2 == 0){
              echo "<tr bgcolor=#CCCCCC>\n";
          } // end of if
          else{
              echo "<tr>\n";
          } // end of else

          // Feldbezeichnungsnamen ausgeben
          echo "  <td>$Namen[$zaehl]</td>\n";

          // ausgefuellte Checkbox ausgeben, ob das Feld ueberhaupt verwendet werden soll
          echo "<td align=center><input type='checkbox'";
               if($verwenden[$zaehl] == 'Y'){echo " checked";};
          echo " value='Y' name='verwenden[$zaehl]'></td>\n";

          // ausgefuellte Checkbox ausgeben, ob das Feld auf Eingabe geprueft werden soll
          echo "<td align=center><input type='checkbox'";
               if($pruefen[$zaehl] == 'Y'){echo " checked";};
          echo " value='Y' name='pruefen[$zaehl]'></td>\n";

          // ausgefuellte Checkbox ausgeben, ob das Feld zum Kunden gespeichert werden soll
          echo "<td align=center><input type='hidden' value='";
               if($speichern[$zaehl] == 'Y'){ echo "Y";};
          echo "' name='speichern[$zaehl]'>immer</td>\n";
          echo "</tr>\n";

          // Zeilenzaehler (fuer graue Balken) um 1 erhoehen
          $zaehler++;
      } // end of if $zaehl < ($anz_vordef-1) || $zaehl > ($anz_vordef+$anz_zusatz-2)
  } // end of for

  // Zusatzfelder ausgeben
  for ($zaehl = ($anz_vordef-1); $zaehl <= ($anz_vordef+$anz_zusatz-2); $zaehl++){

      // jede zweite Zeile soll der Uebersichtlichkeit halber grau hinterlegt werden
      if ($zaehler % 2 == 0){
          echo "<tr bgcolor=#CCCCCC>\n";
      } // end of if
      else{
          echo "<tr>\n";
      } // end of else

      // Feldbezeichnungsname in Textfeld
      echo "<td><input type='text' name='Namen[$zaehl]' size='25' maxlength='128' value=\"".htmlspecialchars($Namen[$zaehl])."\"></td>\n";

      // ausgefuellte Checkbox ausgeben, ob das Feld ueberhaupt verwendet werden soll
      echo "<td align=center><input type='checkbox'";
           if($verwenden[$zaehl] == 'Y'){echo " checked";};
      echo " value='Y' name='verwenden[$zaehl]'></td>\n";

      // ausgefuellte Checkbox ausgeben, ob das Feld auf Eingabe geprueft werden soll
      echo "<td align=center><input type='checkbox'";
           if($pruefen[$zaehl] == 'Y'){echo " checked";};
      echo " value='Y' name='pruefen[$zaehl]'></td>\n";

      // ausgefuellte Checkbox ausgeben, ob das Feld zum Kunden gespeichert werden soll
      echo "<td align=center><input type='checkbox'";
           if($speichern[$zaehl] == 'Y'){echo " checked";};
      echo " value='Y' name='speichern[$zaehl]'></td>\n";
      echo "</tr>";

      // Zeilenzaehler (fuer graue Balken) um 1 erhoehen
      $zaehler++;

  } // end of for

  // Attribute, welche nicht direkt verwendet werden, als Hidden-Felder weitergeben
  for ($zaehl = 0; $zaehl <= (($myAttribut->attributanzahl())-1); $zaehl++){
      echo "<input type='hidden' name='Wert[$zaehl]' value='".$Wert[$zaehl]."'>\n";
      echo "<input type='hidden' name='Attribut_ID[$zaehl]' value='".$Attribut_ID[$zaehl]."'>\n";
      echo "<input type='hidden' name='Positions_Nr[$zaehl]' value='".$Positions_Nr[$zaehl]."'>\n";
      if ($zaehl <= $anz_vordef-2 || $zaehl > ($anz_vordef+$anz_zusatz-2)){
          echo "<input type='hidden' name='Namen[$zaehl]' value='".$Namen[$zaehl]."'>\n";
      } // end of if
  } // end of for

?>
      <tr><td colspan=4>&nbsp</td></tr>
    </table>
   <input type=image src="../Buttons/bt_speichern_admin.gif" border='0'>
   <a href='./Shop_Einstellungen_Menu_1.php'><img src='../Buttons/bt_abbrechen_admin.gif' border='0' alt='abbrechen'></a>
   <a href="javascript:popUp('ADMIN_HILFE.php?Hilfe_ID=Shop_Kunde')" title="Hilfe">
   <img src="../Buttons/bt_hilfe_admin.gif" border="0" alt="Hilfe"></a>
   </form>


<?php
  } // end of else

echo "    </BODY>";
echo "</HTML>";
// End of file ----------------------------------------------------------
?>
