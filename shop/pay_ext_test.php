<?php
  // Filename: pay_ext_test.php
  //
  // Modul: TEST
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Diese Datei simuliert eine externe Zahlungsstelle welche gegebenenfalls
  //        die Zahlung akzeptiert oder auch ablehnt
  //        (Diese Datei funktioniert auch mit PHP3)
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: pay_ext_test.php,v 1.17 2003/06/15 21:21:10 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $pay_ext_test = true;

  // Damit der PhPepperShop auch mit der PHP-Einstellung Register Globals = Off funktioniert, werden die Request Arrays
  // $HTTP_GET_VARS und dann $HTTP_POST_VARS in die Standardsymboltabellen ausgelesen. (Post ueberschreibt dabei GET!)
  @ini_set('error_reporting',E_ALL & ~E_NOTICE); // Undefined Varialbe Meldung ausblenden
  extract($HTTP_GET_VARS);
  extract($HTTP_POST_VARS);

?>
<html>
  <head>
    <META HTTP-EQUIV="content-type" CONTENT="text/html;charset=iso-8859-1">
    <META HTTP-EQUIV="language" CONTENT="de">
    <META HTTP-EQUIV="author" CONTENT="Jose Fontanil & Reto Glanzmann">
    <title>PhPepperShop externe Zahlung - TEST (Emulation eines Payment Instituts)</title>
  </head>
<body bgcolor="#22AA55">
  <h1>Test der externen Zahlung</h1>
  <form action="<?php echo $Referrer; ?>" name="Formular" method="POST"><BR>
    <input type="text" name="Erfolg" value="1"> --> Erfolg: (1 = erfolgreiche Zahlung, 0 = erfolglose Zahlung<BR>
    <input type="text" name="Errormessage" value="Kein Fehler"> --> Errormessage<BR>
    <input type="hidden" name="zufallszahl" value="<?php echo $zufallszahl; ?>">
    <br>
    <center><input type="image" src="Buttons/bt_weiter.gif" border="0"></center>
  </form>
<?php
  echo "<h3>Testausgabe der uebertragenen Daten</h3>";
  echo "<table border='0'><tr><td>";

  echo "Referrer (wohin geht es zurueck):<BR><BR>";
  echo "Anrede:<BR>";
  echo "Vorname:<BR>";
  echo "Nachname:<BR>";
  echo "Firma:<BR>";
  echo "Abteilung:<BR>";
  echo "Strasse:<BR>";
  echo "Postfach:<BR>";
  echo "PLZ:<BR>";
  echo "Ort:<BR>";
  echo "Land:<BR>";
  echo "Tel.:<BR>";
  echo "Fax:<BR>";
  echo "E-Mail:<BR>";
  echo "Bemerkungen:<BR><BR>";
  echo "Datum:<BR><BR><I>Folgende Daten werden meistens erst hier erfasst:</I><BR>";
  echo "Kreditkarten Institut:<BR>";
  echo "Kreditkarten Nummer:<BR>";
  echo "Kreditkarte Ablaufdatum:<BR>";
  echo "Kreditkarte: Vorname:<BR>";
  echo "Kreditkarte: Nachname:<BR><BR>";
  echo "<B>Rechnungsbetrag:</B><BR><BR>";
  echo "Zufallszahl:<BR><BR>";
  echo $Attribut1."<BR>"; //Hier Attribut1-4 durch Namen ersetzen
  echo $Attribut2."<BR>"; //Die selbstkonfigurierbaren Felder haben
  echo $Attribut3."<BR>"; //jeweils ihren eigenen Namen unter welchem
  echo $Attribut4."<BR>"; //sie angesprochen werden koennen

  echo "</td><td>";

  echo "$Referrer<BR><BR>";
  echo "$Anrede<BR>";
  echo "$Vorname<BR>";
  echo "$Nachname<BR>";
  echo "$Firma<BR>";
  echo "$Abteilung<BR>";
  echo "$Strasse<BR>";
  echo "$Postfach<BR>";
  echo "$PLZ<BR>";
  echo "$Ort<BR>";
  echo "$Land<BR>";
  echo "$Tel<BR>";
  echo "$Fax<BR>";
  echo "$Email<BR>";
  echo "$Bemerkungen<BR><BR>";
  echo "$Datum<BR><BR><BR>";
  echo "$Kreditkarten_Hersteller<BR>";
  echo "$Kreditkarten_Nummer<BR>";
  echo "$Kreditkarte_Ablaufdatum<BR>";
  echo "$Kreditkarte_Vorname<BR>";
  echo "$Kreditkarte_Nachname<BR><BR>";
  echo "<B>$Rechnungsbetrag</B><BR><BR>";
  echo "$zufallszahl<BR><BR>";
  echo $Attributwert1."<BR>"; //Hier ebenfalls Attributwert1-4 durch Namen ersetzen
  echo $Attributwert2."<BR>";
  echo $Attributwert3."<BR>";
  echo $Attributwert4."<BR>";

  echo "</td></tr></table>";

?>
</body>
</html>
<?php
// END OF FILE---------------------------------------------------
?>
