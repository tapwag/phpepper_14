<?php
  // Filename: Shop_Einstellungen_Menu_MwSt.php
  //
  // Modul: PhPeppershop, Backup Menu
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Zeigt die Moeglichkeiten des MwSt-Managements an
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: Shop_Einstellungen_Menu_MwSt.php,v 1.9 2003/05/24 18:41:37 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $Shop_Einstellungen_Menu_MwSt = true;

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

  // HTML-Darstellung
?>

<HTML>
    <HEAD>
        <TITLE>MwSt-Management</TITLE>
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
                    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=400,height=300,left = 312,top = 234');");
                }

                // Ueberpruefen, ob Eingaben ok sind. Wenn ja, Formular abspeichern.
                function SaveForm() {
                    // pruefen, ob eine MwSt-Nummer eingegeben wurde
                    if(document.MwStpflicht_setzen.MwStNummer.value == "") {
                        alert("Bitte eine MwSt-Nummer eingeben!");
                        document.MwStpflicht_setzen.MwStNummer.focus();
                    } // end of if MwStNummer = ""
                    else {
                       // Formular 'MwStpflicht_setzen' uebermitteln
                       document.MwStpflicht_setzen.submit();
                   }
                } // end of function SaveForm

            // End -->
        </SCRIPT>
    </HEAD>
    <BODY>
    <h1>SHOP ADMINISTRATION</h1>
    <h3>MwSt-Management</h3>
    <table border='0' cellpadding='5' cellspacing='0'>
<?php
    //-------------------------------------------------------------------------------------------------------------------------------
    // Wenn die Variablen speichern das Wort speichern enthaelt, so sollen die Attribute MwStpflichtig und MwStNummer in der Tabelle
    // shop_settings upgedated werden.
    if ($HTTP_POST_VARS["speichern"] == "speichern") {
        if (setmwstnr($HTTP_POST_VARS["MwStNummer"])) { //Diese Funktion (definiert in SHOP_ADMINISTRATION.php) uebernimmt den Update
            echo "Der Shop wurde erfolgreich als MwSt-pflichtig konfiguriert.<br>\nJetzt m&uuml;ssen die MwSt-S&auml;tze definiert und zugeordnet werden.<br><br>\n";
            echo '<a href="./SHOP_MWST.php"><img src="../Buttons/bt_weiter_admin.gif" border="0" alt="Weiter"></a>&nbsp;';
            echo '<a href="./Shop_Einstellungen_Menu_1.php"><img src="../Buttons/bt_abbrechen_admin.gif" border="0" alt="Abbrechen"></a>';
            echo "</body></html>\n";
            exit; // Programmabbruch weil alles weiter unten nicht mehr verarbeitet werden soll
        }
        else {
            echo "Es gab einen Fehler beim Speichern der Einstellungen, bitte nochmals versuchen. (Stimmt das Format der MwSt-Nummer denn auch?)<br><br>\n";
            echo '<a href="./Shop_Einstellungen_Menu_MwSt.php"><img src="../Buttons/bt_zurueck_admin.gif" border="0" alt="Nochmals_versuchen"></a>';
            echo "</body></html>\n";
            exit; // Programmabbruch weil alles weiter unten nicht mehr verarbeitet werden soll
        }

    }// End if speichern

    //-------------------------------------------------------------------------------------------------------------------------------
    // Anzeigen, wenn die MwSt-Nummer nicht noch gespeichert werden muss.
    //Liefert 0, wenn der Shop nicht MwSt-pflichtig ist, sonst die MwSt-Nummer (UIN)
    $mwstpflichtig = getmwstnr();
    if ($mwstpflichtig != "0") {
?>
        <tr>
            <td>
                <a href="./SHOP_MWST.php">MwSt-S&auml;tze definieren</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="./SHOP_MWST.php?darstellen=11">MwSt-S&auml;tze zuordnen</a>
            </td>
        </tr>
        <tr>
            <td>
                <a href="./Shop_Einstellungen_Menu_1.php" target=_top><img src="../Buttons/bt_zurueck_admin.gif" border="0" alt="Abbrechen" align="absmiddle"></a>
            </td>
        </tr>
<?php
    }// End if mwstpflichtig
    else {
        echo "Der Shop ist momentan als '<i>nicht MwSt-pflichtig</i>' konfiguriert. Dieses Men&uuml; sollte also gar nicht angezeigt werden!<br><br>\n";
        echo "<form  action=\"$PHP_SELF\" method=\"post\" title=\"MwStpflicht_setzen\" name=\"MwStpflicht_setzen\">\n";
        echo "Soll der Shop als MwSt-pflichtig konfiguriert werden?<br>\n";
        echo 'MwSt Nummer: <input type="text" name="MwStNummer" size="24"><br><br>';
        echo "<input type=\"hidden\" name=\"speichern\" value=\"speichern\">";
        echo '<a href="javascript:SaveForm()"><img src="../Buttons/bt_speichern_admin.gif" border="0" alt="Weiter"></a>&nbsp;';
        echo '<a href="./Shop_Einstellungen_Menu_1.php"><img src="../Buttons/bt_zurueck_admin.gif" border="0" alt="Abbrechen"></a>';
        echo "</form>\n";
        echo "</td>\n";
        echo "</tr>\n";
    }
?>
    </table>
    </BODY>
    </HTML>
<?php
  // End of file-------------------------------------------------------------------------
?>
