<?php
  // Filename: top.php
  //
  // Modul: Frameset - Top-Frame / Hauptnavigation
  //
  // Autoren: Jose Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Zeigt top Frame an inkl. Shopname und Hauptnavigation an
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: top.php,v 1.32 2003/07/04 22:02:17 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $top = true;

  // -----------------------------------------------------------------------
  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop/Frameset$pd../$pd../../$pd../Frameset$pd/usr/local/lib/php");

  // Wenn der Haendlermodus aktiviert wurde (alle Kunden muessen sich zuerst einloggen), dann ueberprueft folgender Link,
  // ob man schon eingeloggt ist
  define('AUTH_PATH','../'); // Pfadkonstante fuer USER_AUTH.php
  if (!isset($USER_AUTH)) {include("../USER_AUTH.php");}

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($session_mgmt)) {include("../session_mgmt.php");}
  if (!isset($USER_ARTIKEL_HANDLING)) {include("../USER_ARTIKEL_HANDLING.php");}

  // Shopname aus der Datenbank lesen
  $Shopname = getShopname();

  // -----------------------------------------------------------------------
  // HTML-Ausgabe
?>
<html>
<head>
  <meta HTTP-EQUIV="content-type" content="text/html;charset=iso-8859-1">
  <meta HTTP-EQUIV="language" content="de">
  <meta HTTP-EQUIV="author" content="Jose Fontanil & Reto Glanzmann">
  <meta name="robots" content="all">
  <link rel="stylesheet" type="text/css" href="./shopstyles.css">
  <title>Shop</title>

  <script language="JavaScript" type="text/javascript">
    <!-- Begin
      function popUp(URL) {
        day = new Date();
        id = day.getTime();
        eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=670,left = 312,top = 234');");
      }
    // End -->
  </script>
</head>

<body class="top">
  <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" CLASS="top">
    <tr CLASS="top_stern">
      <td class="top_titel" valign="middle" style="margin-bottom:0pt">
        <a <?php echo "CLASS=\"top_titel\" style=\"text-decoration:".getcssarg("top_font_d").";color:".getcssarg("top_font_c")."\""; ?> href="../../index.php" target="_top">
        <?php
        $show = getcssarg("top_left");
        if ($show == "shopname") echo $Shopname;
        if ($show == "shoplogo") echo "<img src=\"../Bilder/shoplogo.".getcssarg("logo_bg_img_typ")."\" border=\"0\" alt=\"$Shopname\">\n";
        else echo "";
        ?>
        </a>
      </td>
      <td valign="middle" align="right" CLASS="top_stern">
        <?php if ($last_login != "") { echo "<!-- Letztes Login:&nbsp;&nbsp;&nbsp;".get_date_deutsch($last_login)."&nbsp; -->\n";} ?>
        <?php if (getcssarg("admin_stern") == "ja") echo "<a CLASS=\"top_stern\" style=\"text-decoration:".getcssarg("top_stern_d")."\" href='../Admin/Shop_Einstellungen_Menu_1.php?&amp;".session_name()."=".session_id()."' target=\"_top\"><font color=\"".getcssarg("top_stern_c")."\">*</font></a>"; ?>
        <a href="../USER_BESTELLUNG_AUFRUF.php?darstellen=1&amp;<?php echo session_name()."=".session_id(); ?>" target="content"><img src="../Buttons/bt_warenkorb_zeigen.gif" border="0" alt="Warenkorb" title="Warenkorb ansehen"></a>
        <a href="../USER_BESTELLUNG_1.php?darstellen=1&amp;<?php echo session_name()."=".session_id(); ?>" target="content"><img src="../Buttons/bt_zur_kasse_1.gif" border="0" alt="zur Kasse"  title="zur Kasse gehen"></a>
        <a href="../USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=30" target="content"><img src="../Buttons/bt_suchen.gif" border="0" alt="Suche"  title="Artikel suchen"></a>
        <a href="javascript:popUp('../USER_ADMIN_HILFE.php?Hilfe_ID=top')"><img src="../Buttons/bt_hilfe.gif" border="0" alt="Hilfe" title="Hilfe zur Shopbedienung, Kontakt"></a>
      </td>
    </tr>
  </table>
</body>
</html>
<?php
  // End of file-----------------------------------------------------------------------
?>
