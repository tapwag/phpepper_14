<?php
  // Filename: USER_SQL_BEFEHLE.php
  //
  // Modul: Definitions
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet Variablenzuweisungen zu USER-SQL-Statements
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: USER_SQL_BEFEHLE.php,v 1.93 2003/08/15 09:18:06 glanzret Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_SQL_BEFEHLE = true;

  // Bei neuentwickelten Modulen (z.B. Kundenmanagement, ...) werden die SQL-Queries der
  // Modularitaet halber nicht mehr in dieses Zentrale Query-Repository gespeichert, sondern
  // in den jeweiligen Modulen am Anfang der Datei (Bsp. Admin/kundenliste_def.php).

  //-----------------------------------------------------------------------
  // Es folgen Artikel-Handling SQLs
  //-----------------------------------------------------------------------

  // Alter Algorithmus
  $sql_getArtikel_1_1 = '
  SELECT a.*,ao.*,av.*,ag.*
  FROM (artikel a LEFT JOIN artikel_optionen ao ON a.Artikel_ID = ao.FK_Artikel_ID)
       LEFT JOIN artikel_variationen av ON a.Artikel_ID = av.FK_Artikel_ID
       LEFT JOIN artikel_variationsgruppen ag ON a.Artikel_ID = ag.FK_Artikel_ID
  WHERE a.Artikel_ID = "';

  $sql_getArtikel_1_2 = '" ORDER BY ao.Optionen_Nr';

  $sql_getArtikel_1_3 = '
  SELECT a.*
  FROM artikel a
  WHERE a.Artikel_ID = ';

  $sql_getArtikel_1_4 = '
  SELECT ao.*
  FROM artikel_optionen ao
  WHERE ao.FK_Artikel_ID = ';

  $sql_getArtikel_1_5 = ' ORDER BY ao.Optionen_Nr';

  $sql_getArtikel_1_6 = '
  SELECT av.*
  FROM artikel_variationen av
  WHERE av.FK_Artikel_ID = ';

  $sql_getArtikel_1_7 = '
  SELECT ag.*
  FROM artikel_variationsgruppen ag
  WHERE ag.FK_Artikel_ID = ';

  //-----------------------------------------------------------------------

  $sql_getArtikeleinerKategorie_1_1 = "
    SELECT a.*,ao.*,av.*, ag.*
    FROM (artikel a LEFT JOIN artikel_optionen ao ON a.Artikel_ID = ao.FK_Artikel_ID)
         LEFT JOIN artikel_variationen av ON a.Artikel_ID = av.FK_Artikel_ID, kategorien k, artikel_kategorie ak
         LEFT JOIN artikel_variationsgruppen ag ON a.Artikel_ID = ag.FK_Artikel_ID
    WHERE a.Artikel_ID = ak.FK_Artikel_ID and k.Kategorie_ID = ak.FK_Kategorie_ID and k.Name='";

  $sql_getArtikeleinerKategorie_1_2 = "' and k.Unterkategorie_von='";

  $sql_getArtikeleinerKategorie_1_3 = "' ORDER BY a.Name, ao.Optionen_Nr, av.Variations_Nr";

  $sql_getArtikeleinerKategorie_1_2_2 = "' and k.Unterkategorie_von is NULL";

  $sql_getArtikeleinerKategorie_1_3_2 = " ORDER BY a.Name, ao.Optionen_Nr, av.Variations_Nr";

  $sql_getArtikeleinerKategorie_1_4 = "
    SELECT ak.FK_Artikel_ID, a.Name
    FROM artikel a, artikel_kategorie ak, kategorien k
    WHERE a.Artikel_ID = ak.FK_Artikel_ID AND k.Kategorie_ID = ak.FK_Kategorie_ID AND k.Name='";

  $sql_getArtikeleinerKategorie_1_5 = "' and k.Unterkategorie_von='";

  $sql_getArtikeleinerKategorie_1_6 = "' ORDER BY a.Name";

  $sql_getArtikeleinerKategorie_1_5_2 = "' and k.Unterkategorie_von is NULL";

  $sql_getArtikeleinerKategorie_1_6_2 = " ORDER BY a.Name";

  //-----------------------------------------------------------------------

  // Neuer Algorithmus - Teil 1/2
  $sql_IDgetArtikeleinerKategorie_1_3 = "
    SELECT ak.FK_Artikel_ID, a.Name
    FROM artikel a, artikel_kategorie ak
    WHERE a.Artikel_ID = ak.FK_Artikel_ID AND ak.FK_Kategorie_ID=";

  // Neuer Algorithmus - Teil 2/2
  $sql_IDgetArtikeleinerKategorie_1_4 = " ORDER BY ";

  // Alter Algorithmus - Teil 1/2 (Wird nicht mehr benutzt)
  $sql_IDgetArtikeleinerKategorie_1_1 = "
    SELECT a.*,ao.*,av.*, ag.*
    FROM (artikel a LEFT JOIN artikel_optionen ao ON a.Artikel_ID = ao.FK_Artikel_ID)
         LEFT JOIN artikel_variationen av ON a.Artikel_ID = av.FK_Artikel_ID, kategorien k, artikel_kategorie ak
         LEFT JOIN artikel_variationsgruppen ag ON a.Artikel_ID = ag.FK_Artikel_ID
    WHERE a.Artikel_ID = ak.FK_Artikel_ID and k.Kategorie_ID = ak.FK_Kategorie_ID and k.Kategorie_ID=";

  // Alter Algorithmus - Teil 2/2 (Wird nicht mehr benutzt)
  $sql_IDgetArtikeleinerKategorie_1_2 = " ORDER BY a.Name, ao.Optionen_Nr, av.Variations_Nr";

  //-----------------------------------------------------------------------

  // Neuer Algorithmus - Teil 1/2
  $sql_IDgetArtikeleinerKategorievonbis_1_1 = "
    SELECT ak.FK_Artikel_ID, a.Name
    FROM artikel a, artikel_kategorie ak
    WHERE a.Artikel_ID = ak.FK_Artikel_ID AND ak.FK_Kategorie_ID=";

  // Alter Algorithmus - Teil 2/2
  $sql_IDgetArtikeleinerKategorievonbis_1_3 = " ORDER BY a.Name, ao.Optionen_Nr, av.Variations_Nr";

  // Neuer Algorithmus - Teil 2/2
  $sql_IDgetArtikeleinerKategorievonbis_1_4 = " ORDER BY ";

  // Alter Algorithmus - Teil 1/2  ( WIRD NOCH VERWENDET!!! )
  $sql_IDgetArtikeleinerKategorievonbis_1_2 = "
    SELECT a.*,ao.*,av.*, ag.*
    FROM (artikel a LEFT JOIN artikel_optionen ao ON a.Artikel_ID = ao.FK_Artikel_ID)
         LEFT JOIN artikel_variationen av ON a.Artikel_ID = av.FK_Artikel_ID, kategorien k, artikel_kategorie ak
         LEFT JOIN artikel_variationsgruppen ag ON a.Artikel_ID = ag.FK_Artikel_ID
    WHERE a.Artikel_ID = ak.FK_Artikel_ID and k.Kategorie_ID = ak.FK_Kategorie_ID and k.Kategorie_ID=";

  //-----------------------------------------------------------------------

  $sql_getgesuchterArtikel_1_1 = "
  SELECT a.*
  FROM artikel a, artikel_kategorie ak, kategorien k
  WHERE (a.Name LIKE '%";

  $sql_getgesuchterArtikel_1_2 = "%' OR a.Beschreibung LIKE '%";

  $sql_getgesuchterArtikel_1_3 = "%') AND a.Artikel_ID = ak.FK_Artikel_ID AND k.Kategorie_ID = ak.FK_Kategorie_ID AND ((k.Unterkategorie_von IS NULL) OR (k.Unterkategorie_von <> '@PhPepperShop@')) ORDER BY a.Artikel_ID ";

  $sql_getgesuchterArtikel_1_4 = "
  SELECT k.Name,k.Unterkategorie_von,k.Kategorie_ID
  FROM artikel a, artikel_kategorie ak, kategorien k
  WHERE ak.FK_Artikel_ID = a.Artikel_ID AND ak.FK_Kategorie_ID = k.Kategorie_ID AND a.Artikel_ID =";

  $sql_getgesuchterArtikel_1_5 = "%') AND (a.Name LIKE '%";

  $sql_getgesuchterArtikel_1_6 = " LIMIT ";

  $sql_getgesuchterArtikel_1_7 = ",";

  //-----------------------------------------------------------------------

  $sql_getallKategorien_1 = "SELECT Kategorie_ID, Name, Positions_Nr, Beschreibung, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, MwSt_Satz FROM kategorien WHERE Unterkategorie_von is NULL ORDER BY Positions_Nr,Name";

  $sql_getallKategorien_1_2 = "SELECT Kategorie_ID, Name, Positions_Nr, Beschreibung, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, MwSt_Satz, Unterkategorie_von FROM kategorien WHERE Unterkategorie_von='";

  $sql_getallKategorien_1_3 = "' ORDER BY Positions_Nr, Name";

  //-----------------------------------------------------------------------

  $sql_getKategorie_eines_Artikels_1_1 = "
  SELECT kategorien.Name, kategorien.Unterkategorie_von
  FROM artikel_kategorie, kategorien
  WHERE artikel_kategorie.FK_Kategorie_ID = kategorien.Kategorie_ID AND artikel_kategorie.FK_Artikel_ID =";

  $sql_getKategorie_eines_Artikels_1_2 = "";

  //-----------------------------------------------------------------------

  $sql_getKategorieID_eines_Artikels_1_1 = "
  SELECT DISTINCT FK_Kategorie_ID
  FROM artikel_kategorie
  WHERE FK_Artikel_ID =";

  $sql_getKategorieID_eines_Artikels_1_2 = "";

  //-----------------------------------------------------------------------

  $sql_checkaufUnterkategorien_1_1 = "SELECT * FROM kategorien WHERE Unterkategorie_von = '";

  $sql_checkaufUnterkategorien_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_hatKategorieArtikel_1_1 = "SELECT a_k_ID FROM artikel_kategorie WHERE FK_Kategorie_ID = ";

  //-----------------------------------------------------------------------

  $sql_get_var_opt_preise_1_1 = "
  SELECT ao.Optionstext, ao.Preisdifferenz
  FROM artikel_optionen as ao
  WHERE ao.FK_Artikel_ID =";

  $sql_get_var_opt_preise_2_1 = "
  SELECT av.Variationstext, av.Aufpreis
  FROM artikel_variationen as av
  WHERE av.FK_Artikel_ID =";

  //-----------------------------------------------------------------------

  $sql_getKategorie_1_1 = "SELECT * FROM kategorien WHERE Kategorie_ID =";

  $sql_getKategorie_1_2 = "SELECT Kategorie_ID, Name, Positions_Nr, Beschreibung, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von FROM kategorien WHERE Unterkategorie_von=";

  $sql_getKategorie_1_3 = " ORDER BY Positions_Nr, Name";

  //-----------------------------------------------------------------------
  // Jetzt folgen Shop- und Bild-Setting SQLs
  //-----------------------------------------------------------------------

  $sql_getWaehrung_1 = "SELECT Waehrung FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getGewichts_Masseinheit_1 = "SELECT Gewichts_Masseinheit FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_bild_view_1_1 = "SELECT Bild_gross,Bildtyp,Bild_last_modified FROM artikel WHERE Artikel_ID=";

  $sql_bild_view_1_2 = "SELECT Bild_klein,Bildtyp,Bild_last_modified FROM artikel WHERE Artikel_ID=";

  //-----------------------------------------------------------------------

  $sql_getThumbnail_Breite_1 = "SELECT Thumbnail_Breite FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getShopname_1 = "SELECT Name FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getmax_session_time_1_1 = "SELECT max_session_time FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getBezahlungsart_1_1 = "SELECT Vorauskasse, Rechnung, Nachnahme, Lastschrift,  Kreditkarten_Postcard FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getAGB_1_1 = "SELECT AGB FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getEmail_1_1 = "SELECT Email FROM bestellung WHERE Session_ID='";

  $sql_getEmail_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_getBestellungsmanagement_1_1 = "SELECT Bestellungsmanagement FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getSSL_1_1 = "SELECT `TLS_value` FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getSSLsetting_1_1 = "SELECT `TLS_value` FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getShopEmail_1_1 = "SELECT Email FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getSuchInkrement_1_1 = "SELECT SuchInkrement FROM shop_settings";

  //-----------------------------------------------------------------------
  // Jetzt folgen Bestellungs-SQLs
  //-----------------------------------------------------------------------

  $sql_getBestellung_1_1 = "
  SELECT b.*, ab.*,a.Name as Artikelname, a.Gewicht, a.Preis, a.Aktionspreis, a.Aktionspreis_verwenden, a.Artikel_Nr
  FROM bestellung as b, artikel_bestellung as ab, artikel as a
  WHERE b.Bestellungs_ID = ab.FK_Bestellungs_ID and a.Artikel_ID = ab.FK_Artikel_ID and b.Session_ID ='";

  $sql_getBestellung_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_addArtikel_1_1 = "SELECT Bestellungs_ID FROM bestellung WHERE Session_ID='";

  $sql_addArtikel_1_2 = "'";

  $sql_addArtikel_1_3 = "INSERT INTO artikel_bestellung (FK_Artikel_ID, FK_Bestellungs_ID, Artikelname, Preis, Gewicht, Anzahl, Variation, Optionen, Zusatztexte) VALUES (";

  $sql_addArtikel_1_4 = ", ";

  $sql_addArtikel_1_5 = ", ";

  $sql_addArtikel_1_6 = ",'";

  $sql_addArtikel_1_6_1 = "',";

  $sql_addArtikel_1_7 = "', '";

  $sql_addArtikel_1_8 = "')";

  $sql_addArtikel_1_9 = "UPDATE artikel_bestellung SET Anzahl =";

  $sql_addArtikel_1_10 = ", Variation =";

  $sql_addArtikel_1_11 = ", Optionen =";

  $sql_addArtikel_1_12 = " WHERE FK_Artikel_ID=";

  $sql_addArtikel_1_13 = " AND FK_Bestellungs_ID=";

  $sql_addArtikel_1_14 = " AND Variation =";

  $sql_addArtikel_1_14_2 = " AND Optionen =";

  $sql_addArtikel_1_15 = "SELECT FK_Artikel_ID FROM artikel_bestellung WHERE FK_Artikel_ID =";

  $sql_addArtikel_1_16 = " and FK_Bestellungs_ID =";

  $sql_addArtikel_1_17 = "SELECT FK_Artikel_ID, Variation, Optionen, Zusatztexte, Anzahl FROM artikel_bestellung WHERE FK_Bestellungs_ID =";

  //-----------------------------------------------------------------------

  $sql_test_create_Bestellung_1_1 = "SELECT Session_ID, expired, Bestellungs_ID, Bestellung_abgeschlossen FROM bestellung WHERE Session_ID ='";

  $sql_test_create_Bestellung_1_2 = "'";

  $sql_test_create_Bestellung_1_3 = "INSERT INTO bestellung (Session_ID, expired) VALUES ('";

  $sql_test_create_Bestellung_1_4 = "', '";

  $sql_test_create_Bestellung_1_5 = "')";

  //-----------------------------------------------------------------------

  $sql_del_B_Artikel_1_1 = "DELETE FROM artikel_bestellung WHERE FK_Artikel_ID ='";

  $sql_del_B_Artikel_1_2 = "' AND FK_Bestellungs_ID ='";

  $sql_del_B_Artikel_1_3 = "' AND Variation= '";

  $sql_del_B_Artikel_1_4 = "' AND Optionen= '";

  $sql_del_B_Artikel_1_5 = "' AND Zusatztexte= '";

  $sql_del_B_Artikel_1_6 = "'";

  //-----------------------------------------------------------------------

  $sql_delSession_1_1 = "DELETE FROM bestellung WHERE Session_ID ='";

  $sql_delSession_1_2 = "'";

  $sql_delSession_1_3 = "DELETE FROM artikel_bestellung WHERE FK_Bestellungs_ID =";

  $sql_delSession_1_4 = "DELETE FROM kunde WHERE Session_ID ='";

  $sql_delSession_1_5 = "'";

  $sql_delSession_1_6 = "DELETE FROM bestellung_kunde WHERE FK_Bestellungs_ID =";

  //-----------------------------------------------------------------------

  $sql_delallexpiredSessions_1_1 = "SELECT Session_ID, expired, Bestellungs_ID, Bestellung_abgeschlossen FROM bestellung";

  //-----------------------------------------------------------------------

  $sql_setKundendaten_1_1 = "UPDATE bestellung SET ";

  $sql_setKundendaten_1_2 = "' WHERE Session_ID='";

  $sql_setKundendaten_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_schliessenBestellung_1_1 = "Update bestellung SET Bestellung_abgeschlossen='Y', Session_ID = '' WHERE Session_ID ='";

  $sql_schliessenBestellung_1_2 = "'";

  $sql_schliessenBestellung_1_3 = "Update bestellung SET expired=0 WHERE Session_ID ='";

  $sql_schliessenBestellung_1_4 = "'";

  //-----------------------------------------------------------------------
  // Jetzt folgen Hilfe-SQLs
  //-----------------------------------------------------------------------

  $sql_getHilfe_1_1 = "SELECT Hilfetext from hilfe WHERE Hilfe_ID='";

  $sql_getHilfe_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_cssget_1_1 = "SELECT * FROM css_file WHERE Attribut_ID='";

  $sql_cssget_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_getvaroptinc_1_1 = "SELECT Opt_inc, Var_inc, Opt_anz, Var_anz, Vargruppen_anz, Eingabefelder_anz FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getversandkostensettings_1_1 = "SELECT * FROM shop_settings WHERE Setting_Nr=";

  $sql_getversandkostensettings_1_2 = "SELECT * FROM versandkostenpreise WHERE FK_Setting_Nr=";

  $sql_getversandkostensettings_1_13 = " ORDER BY Von, Von_Bis_ID";

  //-----------------------------------------------------------------------

  $sql_getversandkostentext_1_1 = "SELECT Pauschale_text FROM shop_settings WHERE Setting_Nr=";

  //-----------------------------------------------------------------------

  $sql_berechneversandkosten_1_1 = "UPDATE bestellung SET Versandkosten=";

  $sql_berechneversandkosten_1_2 = " , Mindermengenzuschlag=";

  $sql_berechneversandkosten_1_3 = " , Rechnungsbetrag=";

  $sql_berechneversandkosten_1_5 = " , Nachnamebetrag=";

  $sql_berechneversandkosten_1_4 = " WHERE Bestellungs_ID=";

  //-----------------------------------------------------------------------

  $sql_getKunde_1_1 = "SELECT * FROM kunde WHERE Kunden_ID=";

  $sql_getKunde_1_2 = "SELECT b.Bestellungs_ID FROM bestellung as b, bestellung_kunde as bk, kunde as k
                       WHERE b.Bestellungs_ID = bk.FK_Bestellungs_ID AND k.Kunden_ID = bk.FK_Kunden_ID
                       AND k.Kunden_ID=";

  //-----------------------------------------------------------------------

  $sql_getallKunden_1_1 = "SELECT Kunden_ID FROM kunde";

  //-----------------------------------------------------------------------

  $sql_newKunde_1_1 = "INSERT INTO kunde (Kunden_ID,Session_ID,Kunden_Nr,Anrede,Vorname,Nachname,
  Firma,Abteilung,Strasse,Postfach,PLZ,Ort,Land,Tel,Fax,Email,Einkaufsvolumen,
  Login,Passwort,gesperrt,temp,Attribut1,Attribut2,Attribut3,Attribut4,Attributwert1,
  Attributwert2,Attributwert3,Attributwert4,AnmeldeDatum) VALUES (";

  $sql_newKunde_1_2 = ")";

  //-----------------------------------------------------------------------

  $sql_delKunde_1_1 = "DELETE FROM kunde WHERE Kunden_ID=";

  //-----------------------------------------------------------------------

  $sql_getKunde_einer_Bestellung_1_1 = "SELECT bk.FK_Kunden_ID, b.Session_ID FROM bestellung as b, bestellung_kunde as bk
                       WHERE b.Bestellungs_ID = bk.FK_Bestellungs_ID AND b.Bestellungs_ID=";

  $sql_getKunde_einer_Bestellung_1_2 = "SELECT k.Kunden_ID, b.Session_ID FROM bestellung as b, kunde as k
                       WHERE b.Session_ID = k.Session_ID AND b.Bestellungs_ID=";

  //-----------------------------------------------------------------------

  $sql_updKunde_1_1 = "UPDATE kunde SET Kunden_Nr=";

  $sql_updKunde_1_2 = ", Session_ID=";

  $sql_updKunde_1_3 = ", Anrede=";

  $sql_updKunde_1_4 = ", Vorname=";

  $sql_updKunde_1_5 = ", Nachname=";

  $sql_updKunde_1_6 = ", Firma=";

  $sql_updKunde_1_7 = ", Abteilung=";

  $sql_updKunde_1_8 = ", Strasse=";

  $sql_updKunde_1_9 = ", Postfach=";

  $sql_updKunde_1_10 = ", PLZ=";

  $sql_updKunde_1_11 = ", Ort=";

  $sql_updKunde_1_12 = ", Land=";

  $sql_updKunde_1_13 = ", Tel=";

  $sql_updKunde_1_14 = ", Fax=";

  $sql_updKunde_1_15 = ", Email=";

  $sql_updKunde_1_16 = ", Einkaufsvolumen=";

  $sql_updKunde_1_17 = ", Login=";

  $sql_updKunde_1_18 = ", Passwort=";

  $sql_updKunde_1_19 = ", gesperrt=";

  $sql_updKunde_1_20 = ", temp=";

  $sql_updKunde_1_21 = ", Attribut1=";

  $sql_updKunde_1_22 = ", Attribut2=";

  $sql_updKunde_1_23 = ", Attribut3=";

  $sql_updKunde_1_24 = ", Attribut4=";

  $sql_updKunde_1_25 = ", Attributwert1=";

  $sql_updKunde_1_26 = ", Attributwert2=";

  $sql_updKunde_1_27 = ", Attributwert3=";

  $sql_updKunde_1_28 = ", Attributwert4=";

  $sql_updKunde_1_29 = " WHERE Kunden_ID=";

  //-----------------------------------------------------------------------

  $sql_updKundenFelder_1_1 = "UPDATE kunde SET Session_ID=";

  $sql_updKundenFelder_1_2 = ", Anrede=";

  $sql_updKundenFelder_1_3 = ", Vorname=";

  $sql_updKundenFelder_1_4 = ", Nachname=";

  $sql_updKundenFelder_1_5 = ", Firma=";

  $sql_updKundenFelder_1_6 = ", Abteilung=";

  $sql_updKundenFelder_1_7 = ", Strasse=";

  $sql_updKundenFelder_1_8 = ", Postfach=";

  $sql_updKundenFelder_1_9 = ", PLZ=";

  $sql_updKundenFelder_1_10 = ", Ort=";

  $sql_updKundenFelder_1_11 = ", Land=";

  $sql_updKundenFelder_1_12 = ", Tel=";

  $sql_updKundenFelder_1_13 = ", Fax=";

  $sql_updKundenFelder_1_14 = ", Email=";

  $sql_updKundenFelder_1_15 = ", Attribut1=";

  $sql_updKundenFelder_1_16 = ", Attribut2=";

  $sql_updKundenFelder_1_17 = ", Attribut3=";

  $sql_updKundenFelder_1_18 = ", Attribut4=";

  $sql_updKundenFelder_1_19 = ", Attributwert1=";

  $sql_updKundenFelder_1_20 = ", Attributwert2=";

  $sql_updKundenFelder_1_21 = ", Attributwert3=";

  $sql_updKundenFelder_1_22 = ", Attributwert4=";

  $sql_updKundenFelder_1_23 = " WHERE Kunden_ID=";

  //-----------------------------------------------------------------------

  $sql_gibBestellung_an_Kunde_1_1 = "INSERT INTO bestellung_kunde (FK_Bestellungs_ID, FK_Kunden_ID) VALUES(";

  $sql_gibBestellung_an_Kunde_1_2 = ", '";

  $sql_gibBestellung_an_Kunde_1_3 = "')";

  //-----------------------------------------------------------------------

  $sql_delBestellung_von_Kunde_1_1 = "SELECT FK_Bestellungs_ID FROM bestellung_kunde WHERE FK_Kunden_ID";

  $sql_delBestellung_von_Kunde_1_2 = "DELETE FROM bestellung_kunde WHERE FK_Kunden_ID";

  $sql_delBestellung_von_Kunde_1_3 = "DELETE FROM bestellung WHERE Bestellungs_ID";

  //-----------------------------------------------------------------------

  $sql_getAttributobjekt_1_1 = "SELECT * FROM attribut ORDER BY Positions_Nr";

  //-----------------------------------------------------------------------

  $sql_setAttributobjekt_1_1 = "UPDATE attribut SET Name='";

  $sql_setAttributobjekt_1_2 = "', Wert='";

  $sql_setAttributobjekt_1_3 = "', anzeigen='";

  $sql_setAttributobjekt_1_4 = "', in_DB='";

  $sql_setAttributobjekt_1_5 = "', Eingabe_testen='";

  $sql_setAttributobjekt_1_6 = "', Positions_Nr=";

  $sql_setAttributobjekt_1_7 = " WHERE Attribut_ID=";

  //-----------------------------------------------------------------------

  $sql_checkLogin_1_1 = "SELECT Login, Passwort, Kunden_ID FROM kunde";

  $sql_checkLogin_1_2 = "SELECT Kunden_ID FROM kunde";

  $sql_checkLogin_1_3 = "UPDATE kunde SET Session_ID='";

  $sql_checkLogin_1_4 = "' WHERE Login='";

  $sql_checkLogin_1_5 = "'";

  //-----------------------------------------------------------------------

  $sql_checkSession_1_1 = "SELECT Session_ID, Kunden_ID FROM kunde";

  //-----------------------------------------------------------------------

  $sql_mailPasswort_1_1 = "SELECT Login, Passwort, Email FROM kunde WHERE Login='";

  $sql_mailPasswort_1_2 = "'";
  //-----------------------------------------------------------------------

  $sql_addEinkaufsvolumen_1_1 = "UPDATE kunde SET Einkaufsvolumen=Einkaufsvolumen + ";

  $sql_addEinkaufsvolumen_1_2 = ", LetzteBestellung='";

  $sql_addEinkaufsvolumen_1_3 = "' WHERE Session_ID='";

  $sql_addEinkaufsvolumen_1_4 = "'";

  //-----------------------------------------------------------------------

  $sql_updBestellungsFelder_1_1 = "UPDATE bestellung SET ";

  $sql_updBestellungsFelder_1_2 = " WHERE Session_ID='";

  $sql_updBestellungsFelder_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_getNachnahmebetrag_1_1 = "SELECT Nachnamebetrag FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getKontoinformation_1_1 = "SELECT Kontoinformation FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getKreditkarten_1_1 = "SELECT * FROM kreditkarte ORDER BY Kreditkarten_ID";

  //-----------------------------------------------------------------------

  $sql_getEmailMessage_1_1 = "SELECT temp_message_string FROM bestellung WHERE Session_ID='";

  $sql_getEmailMessage_1_2 = "'";

  $sql_getEmailMessage_1_3 = "UPDATE bestellung SET temp_message_string='' WHERE Session_ID='";

  $sql_getEmailMessage_1_4 = "'";

  //-----------------------------------------------------------------------

  $sql_putEmailMessage_1_1 = "UPDATE bestellung SET temp_message_string='";

  $sql_putEmailMessage_1_2 = "' WHERE Session_ID='";

  $sql_putEmailMessage_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_getShopadresse_1_1 = "SELECT Name, Adresse1, Adresse2, PLZOrt, Tel1, Tel2, Email FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getBackupSettings_1_1 = "SELECT * FROM backup ORDER BY Backup_ID";

  //-----------------------------------------------------------------------

  $sql_getAllezahlungen_1_1 = "SELECT * FROM zahlung_weitere ORDER BY Zahlung_ID";

  //-----------------------------------------------------------------------

  $sql_getBildervonArtikel_1_1 = "SELECT Bild_gross, Bild_klein, Bildtyp, Bild_last_modified FROM artikel ";

  $sql_getBildervonArtikel_1_2 = "WHERE Artikel_ID='";

  $sql_getBildervonArtikel_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_getmwstnr_1 = "SELECT MwStNummer, MwStpflichtig FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getmwstsettings_1_1 = "SELECT * FROM mehrwertsteuer ORDER BY Positions_Nr ASC";

  //-----------------------------------------------------------------------

  $sql_getDefaultMwStSatz_1 = "SELECT MwSt_Satz FROM kategorien WHERE Kategorie_ID=";

  $sql_getDefaultMwStSatz_2 = "SELECT MwSt_Satz FROM kategorien WHERE Name='";

  $sql_getDefaultMwStSatz_3 = "' AND Unterkategorie_von='";

  $sql_getDefaultMwStSatz_4 = "'";

  $sql_getDefaultMwStSatz_5 = "' AND Unterkategorie_von IS NULL";

  //-----------------------------------------------------------------------

  $sql_getmwstofArtikel_1_1 = "SELECT MwSt_Satz FROM artikel WHERE Artikel_ID=";

  //-----------------------------------------------------------------------

  $sql_getstandardmwstsatz_1_1 = "SELECT MwSt_Satz FROM mehrwertsteuer WHERE MwSt_default_Satz='Y'";

  //-----------------------------------------------------------------------

  $sql_getportoverpackungmwstsatz_1_1 = "SELECT MwSt_Satz FROM mehrwertsteuer WHERE Beschreibung='Porto und Verpackung'";

  //-----------------------------------------------------------------------

  $sql_getgesamtpreisrunden_1_1 = "SELECT Gesamtpreis_runden FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getArtikelInkrement_1_1 = "SELECT ArtikelSuchInkrement FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getTreuhandbetrag_1_1 = "SELECT * FROM zahlung_weitere WHERE Bezeichnung='";

  $sql_getTreuhandbetrag_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_extend_Session_1_1 = "SELECT Session_ID, expired, Bestellungs_ID, Bestellung_abgeschlossen FROM bestellung WHERE Session_ID ='";

  $sql_extend_Session_1_2 = "'";

  $sql_extend_Session_1_3 = "UPDATE bestellung SET expired =";

  $sql_extend_Session_1_4 = " WHERE Session_ID ='";

  $sql_extend_Session_1_5 = "'";

  //-----------------------------------------------------------------------

  $sql_get_kunden_bankdaten_1_1 = "SELECT kontoinhaber,bankname,blz,kontonummer,bankdaten_speichern,temp FROM kunde WHERE Session_ID='";
  $sql_get_kunden_bankdaten_1_2 = "SELECT kontoinhaber,bankname,blz,kontonummer,bankdaten_speichern,temp FROM kunde WHERE Kunden_ID=";
  $sql_get_kunden_bankdaten_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_set_kunden_bankdaten_1_1 = "UPDATE kunde SET kontoinhaber='";
  $sql_set_kunden_bankdaten_1_2 = "',bankname='";
  $sql_set_kunden_bankdaten_1_3 = "',blz='";
  $sql_set_kunden_bankdaten_1_4 = "',kontonummer='";
  $sql_set_kunden_bankdaten_1_5 = "',bankdaten_speichern='";
  $sql_set_kunden_bankdaten_1_6 = "' WHERE Kunden_ID=";
  $sql_set_kunden_bankdaten_1_7 = "' WHERE Session_ID='";
  $sql_set_kunden_bankdaten_1_8 = "'";

  //-----------------------------------------------------------------------

  $sql_getSortierung_1 = "SELECT Sortieren_nach, Sortiermethode FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getArtikelauswahl_1_1 = "
  SELECT a.Name, a.Artikel_ID, a.Artikel_Nr
  FROM artikel a
  WHERE (a.Artikel_Nr LIKE '%";

  $sql_getArtikelauswahl_1_2 = "%' OR a.Name LIKE '%";

  $sql_getArtikelauswahl_1_3 = "%')ORDER BY a.Name";

  //-----------------------------------------------------------------------

  $sql_getZahlenformat_1 = "SELECT Zahl_thousend_sep, Zahl_decimal_sep, Zahl_nachkomma FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_getshopversion_1_1 = "SELECT ShopVersion FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_checkSessionExpired_1_1 = "SELECT Session_ID, Kunden_ID, expired FROM kunde WHERE Session_ID ='";

  $sql_checkSessionExpired_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_test_create_Login_1_1 = "SELECT k_ID, Kunden_ID, Session_ID, expired, gesperrt FROM kunde WHERE Login='";

  $sql_test_create_Login_1_2 = "' AND Passwort='";

  $sql_test_create_Login_1_3 = "'";

  $sql_test_create_Login_1_4 = "UPDATE kunde SET Session_ID='";

  $sql_test_create_Login_1_5 = "', expired='";

  $sql_test_create_Login_1_6 = "' WHERE k_ID=";

  //-----------------------------------------------------------------------

  $sql_getHaendlermodus_1_1 = "SELECT Haendlermodus, Haendler_login_text FROM shop_settings";

  //-----------------------------------------------------------------------

  $sql_check_if_gesperrt_1_1 = "SELECT gesperrt FROM kunde WHERE Session_ID ='";

  $sql_check_if_gesperrt_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_get_tell_a_friend_1_1 = "SELECT tell_a_friend FROM shop_settings WHERE 1";

  //-----------------------------------------------------------------------

  $sql_createKundenID_1_1 = "SELECT * FROM `kunde` WHERE Kunden_ID='";

  $sql_createKundenID_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_existKundenNr_1_1 = "SELECT * FROM `kunde` WHERE Kunden_Nr='";

  $sql_existKundenNr_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_existLogin_1_1 = "SELECT * FROM `kunde` WHERE Login='";

  $sql_existLogin_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_del_kunden_session_1_1 = "UPDATE kunde SET Session_ID='' WHERE Session_ID='";

  $sql_del_kunden_session_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_set_Bestellung_string_1_1 = "UPDATE bestellung SET Bestellung_string='";

  $sql_set_Bestellung_string_1_2 = "' WHERE Session_ID='";

  $sql_set_Bestellung_string_1_3 = "'";

  //-----------------------------------------------------------------------

  $sql_existBestellung_1_1 = "SELECT * FROM `bestellung_kunde` WHERE FK_Bestellungs_ID='";

  $sql_existBestellung_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_is_tempKunde_1_1 = "SELECT temp FROM `kunde` WHERE Session_ID='";

  $sql_is_tempKunde_1_2 = "'";

  //-----------------------------------------------------------------------

  $sql_get_check_user_country_1 = "SELECT check_user_country FROM shop_settings WHERE 1";

  //-----------------------------------------------------------------------

  $sql_get_tell_a_friend_bcc_1 = "SELECT tell_a_friend_bcc FROM shop_settings WHERE 1";

  //-----------------------------------------------------------------------

  $sql_update_clearing_parameters_1_1 = "UPDATE bestellung SET clearing_id='";

  $sql_update_clearing_parameters_1_2 = "', clearing_extra='";

  $sql_update_clearing_parameters_1_3 = "' WHERE Bestellungs_ID=";

  //-----------------------------------------------------------------------

  $sql_get_new_shop_setting_1_1_user = "SELECT * FROM shop_settings_new WHERE gruppe='";

  $sql_get_new_shop_setting_1_2_user = "' AND name='";

  $sql_get_new_shop_setting_1_3_user = "' AND security='user'";

  //-----------------------------------------------------------------------

  $sql_setExklMwSt_1_1 = "UPDATE bestellung SET MwSt=";

  $sql_setExklMwSt_1_2 = " WHERE Bestellungs_ID=";

  //-----------------------------------------------------------------------

  $sql_get_alle_artikel_id_1_1 = "SELECT Kategorie_ID FROM kategorien WHERE Name='Nichtzugeordnet'";

  $sql_get_alle_artikel_id_1_2 = "SELECT DISTINCT Artikel_ID FROM artikel ";

  $sql_get_alle_artikel_id_1_3 = ", artikel_kategorie WHERE artikel.Artikel_ID = artikel_kategorie.FK_Artikel_ID AND FK_Kategorie_ID <> ";

  $sql_get_alle_artikel_id_1_4 = " ORDER BY ";

  //-----------------------------------------------------------------------

  $sql_get_var_grp_name_1_1 = "SELECT Gruppentext FROM artikel_variationsgruppen, artikel_variationen WHERE Variationstext='";

  $sql_get_var_grp_name_1_2 = "' AND Variations_Grp=Gruppen_ID ";

  $sql_get_var_grp_name_1_3 = "AND artikel_variationen.FK_Artikel_ID=";

  //-----------------------------------------------------------------------

  $get_kat_id_nichtzugeordnet_1_1 = "SELECT Kategorie_ID FROM kategorien WHERE Name='Nichtzugeordnet'";



  // End of file-----------------------------------------------------------
?>
