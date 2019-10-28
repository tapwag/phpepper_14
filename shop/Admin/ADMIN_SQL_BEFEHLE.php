<?php
  // Filename: ADMIN_SQL_BEFEHLE.php
  //
  // Modul: ADMIN_SQL_BEFEHLE
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet Variablenzuweisungen zu ADMIN-SQL-Statements
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: ADMIN_SQL_BEFEHLE.php,v 1.45 2003/05/24 18:41:28 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $ADMIN_SQL_BEFEHLE = true;

  // ------------------------------------------------------------------------------------
  // Queries:
  // Die Queries wurden in Teile zerlegt, so dass das aufrufende Script dort jeweils
  // Variablen einsetzten kann. Wo immer moeglich, wurde darauf geachtet, dass auch Trenn-
  // Zeichen als Variablen hier aufgefuehrt wurden.
  // Variabelnamenaufbau: sql_ NAME DES SCRIPTS _suffix ... suffix = hochzaehlende Zahl
  // (Falls eine Query aus mehreren hier definierten Variablen zusammengesetzt wird, so
  // ist der suffix nochmals angehaengt und dort eine Laufvariable eingesetzt worden).
  //
  // Bei neuentwickelten Modulen (z.B. Kundenmanagement, ...) werden die SQL-Queries der
  // Modularitaet halber nicht mehr in dieses Zentrale Query-Repository gespeichert, sondern
  // in den jeweiligen Modulen am Anfang der Datei (Bsp. kundenliste_def.php).

  $sql_newArtikel_1_1 = "
    INSERT INTO artikel (Artikel_Nr, Name, Beschreibung, letzteAenderung, Preis, Aktionspreis, Gewicht, MwSt_Satz, Link, Zusatzfeld_text, Zusatzfeld_param)
       VALUES (";

  $sql_newArtikel_1_2 = "
    );";

  $sql_newArtikel_1_3 = "
    INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt)
       VALUES (";

  $sql_newArtikel_1_4 = "
    );";

  $sql_newArtikel_1_5 = "
    INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var)
       VALUES (";

  $sql_newArtikel_1_6 = "
    );";

  $sql_newArtikel_1_7 = "
    INSERT INTO artikel_kategorie (FK_Artikel_ID, FK_Kategorie_ID)
       VALUES (";

  $sql_newArtikel_1_8 = "
    );";

  $sql_newArtikel_1_9 = "SELECT Kategorie_ID FROM kategorien WHERE Unterkategorie_von='@PhPepperShop@'";

  $sql_newArtikel_2_0 = "
    INSERT INTO artikel_variationsgruppen (FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen)
       VALUES (";

  $sql_newArtikel_2_1 = "
    );";

  $sql_newArtikel_3_1 = "
    SELECT Kategorie_ID
    FROM kategorien
    WHERE Name =";

  $sql_newArtikel_3_2 = "";

  //-----------------------------------------------------------------------

  $sql_updArtikel_1_1 = "UPDATE artikel SET Artikel_Nr=";

  $sql_updArtikel_1_2 = ", Name=";

  $sql_updArtikel_1_3 = ", Beschreibung=";

  $sql_updArtikel_1_4 = ", letzteAenderung=";

  $sql_updArtikel_1_5 = ", Preis=";

  $sql_updArtikel_1_6 = ", Aktionspreis=";

  $sql_updArtikel_1_7 = ", Gewicht=";

  $sql_updArtikel_1_7_1 = ", MwSt_Satz=";

  $sql_updArtikel_1_8 = ", Link=";

  $sql_updArtikel_1_8_1 = ", Zusatzfeld_text=";

  $sql_updArtikel_1_8_2  = ", Zusatzfeld_param=";

  $sql_updArtikel_1_9 = " WHERE Artikel_ID=";

  $sql_updArtikel_2_1 = "UPDATE artikel_optionen SET Optionstext=";

  $sql_updArtikel_2_2 = ", Preisdifferenz=";

  $sql_updArtikel_2_2_1 = ", Gewicht_Opt=";

  $sql_updArtikel_2_3 = " WHERE FK_Artikel_ID=";

  $sql_updArtikel_2_4 = " AND Optionen_Nr=";

  $sql_updArtikel_3_1 = "
    SELECT Kategorie_ID
    FROM kategorien
    WHERE Name =";

  $sql_updArtikel_3_2 = "";

  $sql_updArtikel_4_1 = "UPDATE artikel_variationen SET Variationstext=";

  $sql_updArtikel_4_2 = ", Aufpreis=";

  $sql_updArtikel_4_3 = ", Variations_Grp=";

  $sql_updArtikel_4_3_1 = ", Gewicht_Var=";

  $sql_updArtikel_4_4 = " WHERE FK_Artikel_ID=";

  $sql_updArtikel_4_5 = " AND Variations_Nr=";

  $sql_updArtikel_5_1 = "SELECT * FROM artikel_kategorie WHERE FK_Artikel_ID=";

  $sql_updArtikel_5_2 = "UPDATE artikel_kategorie SET FK_Kategorie_ID=";

  $sql_updArtikel_5_3 = " WHERE a_k_ID=";

  $sql_updArtikel_5_4 = "INSERT INTO artikel_kategorie (FK_Artikel_ID, FK_Kategorie_ID) VALUES(";

  $sql_updArtikel_5_5 = ", ";

  $sql_updArtikel_5_6 = ")";

  $sql_updArtikel_5_7 = "DELETE FROM artikel_kategorie WHERE a_k_ID=";

  $sql_updArtikel_5_8 = "SELECT Kategorie_ID FROM kategorien WHERE Unterkategorie_von='@PhPepperShop@'";

  $sql_updArtikel_6_1 = "SELECT Optionen_Nr from artikel_optionen WHERE FK_Artikel_ID=";

  $sql_updArtikel_6_2 = " ORDER BY Optionen_Nr";

  $sql_updArtikel_7_1 = "SELECT Variations_Nr from artikel_variationen WHERE FK_Artikel_ID=";

  $sql_updArtikel_7_2 = " ORDER BY Variations_Nr";

  $sql_updArtikel_8_1 = "DELETE FROM artikel_optionen WHERE FK_Artikel_ID=";

  $sql_updArtikel_8_2 = " AND Optionen_Nr=";

  $sql_updArtikel_9_1 = "DELETE FROM artikel_variationen WHERE FK_Artikel_ID=";

  $sql_updArtikel_9_2 = " AND Variations_Nr=";

  $sql_updArtikel_10_1 = "SELECT Gruppen_Nr from artikel_variationsgruppen WHERE FK_Artikel_ID=";

  $sql_updArtikel_10_2 = " ORDER BY Gruppen_Nr";

  $sql_updArtikel_11_1 = "UPDATE artikel_variationsgruppen SET Gruppentext=";

  $sql_updArtikel_11_2 = ", Gruppe_darstellen=";

  $sql_updArtikel_11_3 = " WHERE FK_Artikel_ID=";

  $sql_updArtikel_11_4 = " AND Gruppen_Nr=";

  $sql_updArtikel_12_1 = "INSERT INTO artikel_variationsgruppen (FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES(";

  $sql_updArtikel_12_2 = ", ";

  $sql_updArtikel_12_3 = ", ";

  $sql_updArtikel_12_4 = ", ";

  $sql_updArtikel_12_5 = ")";

  $sql_updArtikel_13_1 = "DELETE FROM artikel_variationsgruppen WHERE FK_Artikel_ID=";

  $sql_updArtikel_13_2 = " AND Gruppen_Nr=";



  //-----------------------------------------------------------------------

  $sql_delArtikel_1_1 = "
    DELETE
    FROM artikel
    WHERE Artikel_ID = ";

  $sql_delArtikel_1_2 = "";

  $sql_delArtikel_2_1 = "
    DELETE
    FROM artikel_optionen
    WHERE FK_Artikel_ID = ";

  $sql_delArtikel_2_2 = "";

  $sql_delArtikel_3_1 = "
    DELETE
    FROM artikel_variationen
    WHERE FK_Artikel_ID = ";

  $sql_delArtikel_3_2 = "";

  $sql_delArtikel_4_1 = "
    DELETE
    FROM artikel_kategorie
    WHERE FK_Artikel_ID = ";

  $sql_delArtikel_4_2 = "";

  $sql_delArtikel_5_1 = "
    DELETE
    FROM artikel_variationsgruppen
    WHERE FK_Artikel_ID = ";

  $sql_delArtikel_5_2 = "";

  //-----------------------------------------------------------------------

  $sql_movengzArtikel_1_1 = "SELECT Kategorie_ID FROM kategorien WHERE Unterkategorie_von = '@PhPepperShop@'";

  $sql_movengzArtikel_1_2 = "UPDATE artikel_kategorie SET FK_Kategorie_ID=";

  $sql_movengzArtikel_1_3 = " WHERE FK_Artikel_ID=";

  //-----------------------------------------------------------------------

  $sql_bild_up_1_1 = "UPDATE artikel SET Bild_gross='";

  $sql_bild_up_1_2 = "', Bild_klein ='";

  $sql_bild_up_1_3 = "', Bildtyp = '";

  $sql_bild_up_1_4 = "' WHERE Artikel_ID = '";

  $sql_bild_up_1_5 = "'";

  //-----------------------------------------------------------------------

  $sql_bild_kopieren_1_1 = "SELECT Bild_gross, Bild_klein, Bildtyp FROM artikel WHERE Artikel_ID=";

  $sql_bild_kopieren_1_2 = "UPDATE artikel SET Bild_gross='";

  $sql_bild_kopieren_1_3 = "', Bild_klein ='";

  $sql_bild_kopieren_1_4 = "', Bildtyp = '";

  $sql_bild_kopieren_1_5 = "' WHERE Artikel_ID = '";

  $sql_bild_kopieren_1_6 = "'";

  //-----------------------------------------------------------------------

  $sql_delBild_1_1 = "UPDATE artikel SET Bild_gross=NULL, Bild_klein=NULL, Bildtyp=NULL WHERE Artikel_ID=";

  //-----------------------------------------------------------------------
  $sql_getshopsettings_1 = "SELECT * from shop_settings";

  //-----------------------------------------------------------------------

  $sql_setshopsettings_1_1 = "UPDATE shop_settings SET ";

  $sql_setshopsettings_1_2 = " WHERE Setting_Nr=1";

  //-----------------------------------------------------------------------

  $sql_updbestellungen_1_1 = "UPDATE artikel_bestellung SET FK_Artikel_ID=";

  $sql_updbestellungen_1_2 = " WHERE FK_Artikel_ID=";

  //-----------------------------------------------------------------------

  $sql_updkategorien_1_1 = "UPDATE artikel_kategorie SET FK_Artikel_ID=";

  $sql_updkategorien_1_2 = " WHERE FK_Artikel_ID=";

  //-----------------------------------------------------------------------

  $sql_newKategorie_1_1 = "INSERT INTO kategorien (Name, Beschreibung, Details_anzeigen, MwSt_Satz, Unterkategorie_von, Positions_Nr) VALUES('";

  $sql_newKategorie_1_2 = "', '";

  $sql_newKategorie_1_3 = "', '";

  $sql_newKategorie_1_3_1 = "', ";

  $sql_newKategorie_1_3_3 = ", '";

  $sql_newKategorie_1_3_4 = "', ";

  $sql_newKategorie_1_4 = ")";

  $sql_newKategorie_1_2_2 = ",NULL, ";

  $sql_newKategorie_1_3_2 = ")";

  //-----------------------------------------------------------------------

  $sql_delKategorie_1_1 = "DELETE FROM kategorien WHERE Kategorie_ID=";

  $sql_delKategorie_1_2 = "DELETE FROM artikel_kategorie WHERE FK_Kategorie_ID=";

  $sql_delKategorie_1_3 = " AND FK_Artikel_ID=";

  //-----------------------------------------------------------------------

  $sql_verschiebenKategorie_1_1 = "UPDATE kategorien SET Unterkategorie_von='";

  $sql_verschiebenKategorie_1_2 = "', Positions_Nr=";

  $sql_verschiebenKategorie_1_3 = " WHERE Kategorie_ID=";

  //-----------------------------------------------------------------------

  $sql_umbenennenKategorie_1_1 = "UPDATE kategorien SET Name='";

  $sql_umbenennenKategorie_1_2 = "' WHERE Kategorie_ID=";

  $sql_umbenennenKategorie_1_3 = "UPDATE kategorien SET Unterkategorie_von='";

  $sql_umbenennenKategorie_1_4 = "' WHERE Unterkategorie_von='";

  $sql_umbenennenKategorie_1_5 = "'";

  //-----------------------------------------------------------------------

  $sql_katposschieben_1_1 = "SELECT * FROM kategorien WHERE Unterkategorie_von='";

  $sql_katposschieben_1_4 = "' ORDER BY Positions_Nr, Name";

  $sql_katposschieben_1_5 = "SELECT * FROM kategorien WHERE Unterkategorie_von is NULL ORDER BY Positions_Nr, Name";

  $sql_katposschieben_1_2 = "UPDATE kategorien SET Positions_Nr=";

  $sql_katposschieben_1_3 = " WHERE Kategorie_ID=";

  $sql_katposschieben_1_6 = "UPDATE kategorien SET Positions_Nr=";

  $sql_katposschieben_1_7 = ", Unterkategorie_von='";

  $sql_katposschieben_1_8 = "' WHERE Kategorie_ID=";


  //-----------------------------------------------------------------------

  $getNichtzugeordnetKategorie_1_1 = "SELECT * FROM kategorien WHERE Unterkategorie_von = '@PhPepperShop@'";

  //-----------------------------------------------------------------------

  $sql_setallKategorien_1_1 = "UPDATE kategorien SET ";

  $sql_setallKategorien_1_2 = " WHERE Kategorie_ID=";

  $sql_setallKategorien_1_3 = "";

  $sql_setallKategorien_1_4 = "SELECT Kategorie_ID from kategorien ORDER BY Positions_Nr";

  //-----------------------------------------------------------------------

  $sql_cssput_1_1 = "UPDATE css_file SET CSS_String='";

  $sql_cssput_1_2 = "' WHERE Attribut_ID = '";

  $sql_cssput_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_setvaroptinc_1_1 = "UPDATE shop_settings SET Opt_inc=";

  $sql_setvaroptinc_1_2 = " , Var_inc=";

  //-----------------------------------------------------------------------

  $sql_setversandkostensettings_1_1 = "UPDATE shop_settings SET ";

  $sql_setversandkostensettings_1_2 = " WHERE Setting_Nr=";

  $sql_setversandkostensettings_1_3 = "UPDATE versandkostenpreise SET Von=";

  $sql_setversandkostensettings_1_4 = ",Bis=";

  $sql_setversandkostensettings_1_5 = ",Betrag=";

  $sql_setversandkostensettings_1_6 = ",Vorauskasse=";

  $sql_setversandkostensettings_1_7 = ",Rechnung=";

  $sql_setversandkostensettings_1_8 = ",Nachname=";

  $sql_setversandkostensettings_1_9 = ",Kreditkarte=";

  $sql_setversandkostensettings_1_9_1 = ",billBOX=";

  $sql_setversandkostensettings_1_9_2 = ",Treuhandzahlung=";

  $sql_setversandkostensettings_1_9_3 = ",Lastschrift=";

  $sql_setversandkostensettings_1_9_4 = ",Postcard=";

  $sql_setversandkostensettings_1_10 = " ,FK_Setting_Nr=1 WHERE Von_Bis_ID=";

  $sql_setversandkostensettings_1_11 = "DELETE FROM versandkostenpreise WHERE Von_Bis_ID=";

  $sql_setversandkostensettings_1_12 = "INSERT into versandkostenpreise (Von,Bis,Betrag,Vorauskasse,Rechnung,Nachname,Kreditkarte,billBOX,Treuhandzahlung,Lastschrift,Postcard,FK_Setting_Nr) VALUES(";

  $sql_setversandkostensettings_1_13 = ",";

  $sql_setversandkostensettings_1_14= ",";

  $sql_setversandkostensettings_1_15= ",";

  $sql_setversandkostensettings_1_16 = ",";

  $sql_setversandkostensettings_1_17= ",";

  $sql_setversandkostensettings_1_18= ",";

  $sql_setversandkostensettings_1_18_1= ",";

  $sql_setversandkostensettings_1_18_2= ",";

  $sql_setversandkostensettings_1_18_3= ",";

  $sql_setversandkostensettings_1_18_4= ",";

  $sql_setversandkostensettings_1_19= ",";

  $sql_setversandkostensettings_1_20= ")";

  //-----------------------------------------------------------------------

  $sql_getBestellung_Ref_1_1 = "
  SELECT b.*, ab.*,a.Name as Artikelname, a.Artikel_Nr, a.Gewicht, a.Preis, a.Aktionspreis, a.Aktionspreis_verwenden
  FROM bestellung as b, artikel_bestellung as ab, artikel as a
  WHERE b.Bestellungs_ID = ab.FK_Bestellungs_ID and a.Artikel_ID = ab.FK_Artikel_ID and b.Bestellungs_ID ='";

  $sql_getBestellung_Ref_1_2 = "'";

  $sql_getBestellung_Ref_1_3 = "
  SELECT * FROM bestellung WHERE Bestellungs_ID=";

  //-----------------------------------------------------------------------

  $sql_getBestellung_Kunde_1_1 = "SELECT Bestellungs_ID FROM bestellung WHERE Name='";

  $sql_getBestellung_Kunde_1_2 = "', Vorname='";

  $sql_getBestellung_Kunde_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_getBestellung_Alle_1_1 = "SELECT Bestellungs_ID FROM bestellung WHERE Bestellung_abgeschlossen='Y' ORDER BY ";

  $sql_getBestellung_Alle_1_2 = "";

  //-----------------------------------------------------------------------

  $sql_setKundendatenAdmin_1_1 = "UPDATE bestellung SET ";

  $sql_setKundendatenAdmin_1_2 = "' WHERE Bestellungs_ID='";

  $sql_setKundendatenAdmin_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_delBestellung_1_1 = "DELETE FROM bestellung WHERE Bestellungs_ID=";

  $sql_delBestellung_1_2 = "DELETE FROM artikel_bestellung WHERE FK_Bestellungs_ID=";

  $sql_delBestellung_1_3 = "DELETE FROM bestellung_kunde WHERE FK_Bestellungs_ID=";

  //-----------------------------------------------------------------------

  $sql_setKreditkarten_1_1 = "SELECT * FROM kreditkarte ORDER BY Kreditkarten_ID";

  $sql_setKreditkarten_1_2 = "DELETE FROM kreditkarte";

  $sql_setKreditkarten_1_3 = "INSERT INTO kreditkarte (Hersteller, benutzen, Handling) VALUES(";

  $sql_setKreditkarten_1_4 = ")";

  //-----------------------------------------------------------------------

  $sql_setBackupSettings_1_1 = "DELETE FROM backup";

  $sql_setBackupSettings_1_2 = "INSERT INTO backup (Backup_ID, Wert) VALUES(";

  $sql_setBackupSettings_1_3 = ")";

  //-----------------------------------------------------------------------

  $sql_setAllezahlungen_1_1 = "DELETE FROM zahlung_weitere";

  $sql_setAllezahlungen_1_2 = "INSERT INTO zahlung_weitere (Gruppe, Bezeichnung, verwenden, payment_interface_name, Par1, Par2, Par3, Par4, Par5, Par6, Par7, Par8, Par9, Par10)
                              VALUES(";

  $sql_setAllezahlungen_1_3 = ")";

  //-----------------------------------------------------------------------

  $sql_getAlleArtikelvomBild_1_1 = "SELECT * FROM artikel ";

  $sql_getAlleArtikelvomBild_1_2 = "WHERE Bild_gross='";

  $sql_getAlleArtikelvomBild_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_setmwstsettings_1_1 = "UPDATE mehrwertsteuer SET MwSt_Satz='";

  $sql_setmwstsettings_1_2 = "', Beschreibung='";

  $sql_setmwstsettings_1_3 = "', MwSt_default_Satz='";

  $sql_setmwstsettings_1_4 = "', Preise_inkl_MwSt='";

  $sql_setmwstsettings_1_4_1 = "', Positions_Nr='";

  $sql_setmwstsettings_1_5 = "' WHERE Mehrwertsteuer_ID='";

  $sql_setmwstsettings_1_6 = "'";

  $sql_setmwstsettings_1_7 = "INSERT INTO mehrwertsteuer (MwSt_Satz, Beschreibung, MwSt_default_Satz, Preise_inkl_MwSt, Positions_Nr) VALUES(";

  $sql_setmwstsettings_1_8 = ",'";

  $sql_setmwstsettings_1_9 = "','";

  $sql_setmwstsettings_1_10 = "','";

  $sql_setmwstsettings_1_11 = "',";

  $sql_setmwstsettings_1_12 = ")";

  $sql_setmwstsettings_1_13 = "DELETE FROM mehrwertsteuer WHERE Mehrwertsteuer_ID = ";

  //-----------------------------------------------------------------------

  $sql_setmwstnr_1_1 = "UPDATE shop_settings SET MwStNummer='";

  $sql_setmwstnr_1_2 = "', MwStpflichtig='";

  $sql_setmwstnr_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_setKategorie_1_1 = "UPDATE kategorien SET Name='";

  $sql_setKategorie_1_2 = "', Beschreibung='";

  $sql_setKategorie_1_3 = "', MwSt_Satz=";

  $sql_setKategorie_1_4 = ", Details_anzeigen='";

  $sql_setKategorie_1_5 = "' WHERE Kategorie_ID=";

  //-----------------------------------------------------------------------

  $sql_setKatmwst_1_1 = "UPDATE kategorien SET MwSt_Satz=";

  $sql_setKatmwst_1_2 = " WHERE Kategorie_ID=";

  $sql_setKatmwst_1_3 = "SELECT FK_Artikel_ID FROM artikel_kategorie WHERE FK_Kategorie_ID=";

  $sql_setKatmwst_1_4 = "UPDATE artikel SET MwSt_Satz=";

  $sql_setKatmwst_1_5 = " WHERE Artikel_ID=";

  //-----------------------------------------------------------------------

  $sql_updatewithmwst_1_1 = "UPDATE kategorien SET MwSt_Satz=";

  $sql_updatewithmwst_1_2 = " WHERE Beschreibung <> 'Nichtzugeordnet' AND MwSt_Satz=";

  $sql_updatewithmwst_1_3 = "UPDATE artikel SET MwSt_Satz=";

  $sql_updatewithmwst_1_4 = " WHERE MwSt_Satz=";

  //-----------------------------------------------------------------------

  $sql_IDgetBestellung_1_1 = "SELECT b.*, ab.*,a.Name AS Artikelname, a.Artikel_Nr, a.Gewicht, a.Preis, a.Aktionspreis, a.Aktionspreis_verwenden
    FROM bestellung AS b, artikel_bestellung AS ab, artikel AS a
    WHERE b.Bestellungs_ID = ab.FK_Bestellungs_ID AND a.Artikel_ID = ab.FK_Artikel_ID AND b.Bestellungs_ID ='";

  $sql_IDgetBestellung_1_2 = "'";

  $sql_IDgetBestellung_1_3 = "SELECT * FROM bestellung WHERE Bestellungs_ID=";

  //-----------------------------------------------------------------------

  $sql_get_new_shop_setting_1_1_admin = "SELECT * FROM shop_settings_new WHERE gruppe='";

  $sql_get_new_shop_setting_1_2_admin = "' AND name='";

  $sql_get_new_shop_setting_1_3_admin = "'";

  //-----------------------------------------------------------------------

  $sql_set_new_shop_setting_1_1 = "UPDATE shop_settings_new SET name='";

  $sql_set_new_shop_setting_1_2 = "', gruppe='";

  $sql_set_new_shop_setting_1_3 = "', wert='";

  $sql_set_new_shop_setting_1_4 = "', security='";

  $sql_set_new_shop_setting_1_5 = "' WHERE name='";

  $sql_set_new_shop_setting_1_6 = "' AND gruppe='";

  $sql_set_new_shop_setting_1_7 = "'";

  //End of file-------------------------------------------------------------
?>
