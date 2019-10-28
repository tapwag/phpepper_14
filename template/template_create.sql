# /*
# * {shop_db}_create.sql
# *
# * ZHW Zuercher Hochschule Winterthur
# * PhPepperShop Diplomarbeit DA Fei01/1
# * Autoren: Jose Fontanil & Reto Glanzmann
# *
# * Version 1.4, basierend auf ER-Diagramm v.1.2.6
# *
# * CVS-Version / Datum: $Id: template_create.sql,v 1.104 2003/08/18 10:06:50 fontajos Exp $
# *
# * Initialisiert die Shop-Datenbank '{shop_db}' (getestet fuer MySQL)
# *
# * einlesen z.B. mit mysql -u {grantor} -p < ./{shop_db}_create.sql
# * (wobei mysql im Pfad sein muss. Datenbank Hostrechner: {hostname})
# *
# * Sicherheitsstatus:       *** ADMIN ***
# *
# * ------------------------------------------------------------------------------
# */

# */ {nomakeuser} */
# /*
# * Zuerst werden die beiden USER shopadmin und shopuser erzeugt
# * Um neue User in MySQL anzulegen bedarf es MySQL-root-Rechte!
# * Der shopuser '{shopuser}' darf nicht allgemein in der {shop_db} (Shop-Datenbank)
# * loeschen, deshalb erhaelt er nur das Loeschrecht in den
# * Tabellen bestellung und artikel_bestellung.
# * Achtung: Hier die Passwoerter aendern (nur falls man das config.pl Script nicht benutzt!)
# */

use {mysql_db};

INSERT INTO user (Host, User, Password)
       VALUES("{hostname}","{shopadmin}",PASSWORD('{shopadminpwd}'));
# */ {einuserseq} */
INSERT INTO user (Host, User, Password)
       VALUES("{hostname}","{shopuser}",PASSWORD('{shopuserpwd}'));

INSERT INTO db (Host, DB, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Alter_priv)
       VALUES("{hostname}","{shop_db}","{shopuser}","Y","Y","Y","N","Y");
# */ {einuserseq} */
INSERT INTO db (Host, DB, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Alter_priv, Create_priv, Drop_priv, Index_priv, References_priv)
       VALUES("{hostname}","{shop_db}","{shopadmin}","Y","Y","Y","Y","Y","Y","Y","Y","Y");

# */ {einuserseq} */
INSERT INTO tables_priv (Host, Db, User, Table_name, Grantor, Table_priv, Column_priv)
       VALUES("{hostname}", "{shop_db}", "{shopuser}", "artikel_bestellung", "{grantor}", "Delete", "Select");
INSERT INTO tables_priv (Host, Db, User, Table_name, Grantor, Table_priv, Column_priv)
       VALUES("{hostname}", "{shop_db}", "{shopuser}", "bestellung", "{grantor}", "Delete", "Select");
INSERT INTO tables_priv (Host, Db, User, Table_name, Grantor, Table_priv, Column_priv)
       VALUES("{hostname}", "{shop_db}", "{shopuser}", "kunde", "{grantor}", "Delete", "Select");
INSERT INTO tables_priv (Host, Db, User, Table_name, Grantor, Table_priv, Column_priv)
       VALUES("{hostname}", "{shop_db}", "{shopuser}", "bestellung_kunde", "{grantor}", "Delete", "Select");
# */ {einuserseq} */
flush privileges; #/*Nach diesem Kommando werden die neuen User akzeptiert*/
# */ {nomakeuser} */

# /*
# * ------------------------------------------------------------------------------
# *
# * Nun wird die Shop-Datenbank erzeugt --> {shop_db}
# * Es werden folgende Tabellen erzeugt:
# *    artikel, artikel_optionen, artikel_variationen, kategorien,
# *    bestellung, artikel_bestellung, artikel_kategorie,
# *    hilfe, shop_settings, versandkostenpreise, css_file
# */

# /*
# * ------------------------------------------------------------------------------
# *
# * Anm. Artikel_Nr: Die Artikel_Nr ist hier NICHT per UNIQUE INDEX vor Duplikaten
# * geschuetzt. Dies aus der Entscheidung heraus, dass wir hier die Artikel_Nr. gleich-
# * zeitig auch als Hersteller-Bestell-Nr benutzen kann (Dies ist oft der Fall bei
# * kleineren Shops, die auf diese Weise ihre Bestellnummern verwalten.
# *
# */

CREATE DATABASE IF NOT EXISTS {shop_db};

use {shop_db};

CREATE TABLE artikel
(
       Artikel_ID INT NOT NULL AUTO_INCREMENT,
       Artikel_Nr VARCHAR(128) NOT NULL,
       Name VARCHAR(128) NOT NULL,
       Beschreibung TEXT NOT NULL,
       Preis DOUBLE NOT NULL DEFAULT 0.0,
       Aktionspreis DOUBLE NOT NULL DEFAULT 0.0,
       Aktionspreis_verwenden enum('N','Y') NOT NULL DEFAULT 'N',
       Gewicht DOUBLE NOT NULL DEFAULT 0.0,
       Link VARCHAR(255),
       Bild_gross VARCHAR(255), # /* Seit v.1.1 sind die Bilder ins */
       Bild_klein VARCHAR(255), # /* Dateisystem ausgelagert worden */
       Bildtyp VARCHAR(15),
       Bild_last_modified TIMESTAMP(14),
       Lagerbestand INT NOT NULL DEFAULT 0,
       Mindestlagermenge INT NOT NULL DEFAULT 5,
       letzteAenderung DATE,
       Zusatzfeld_text text default NULL,
       Zusatzfeld_param text default NULL,
       MwSt_Satz DOUBLE NOT NULL default '0.0',
       Aktion_von datetime NOT NULL default '0000-00-00 00:00:00',
       Aktion_bis datetime NOT NULL default '0000-00-00 00:00:00',
       PRIMARY KEY (Artikel_ID),
       UNIQUE UC_Artikel_ID (Artikel_ID),
       INDEX IDX_artikel_1 (Name),
       INDEX IDX_artikel_2 (Artikel_ID),
       INDEX IDX_artikel_3 (Artikel_Nr) # /* Wichtig fuer Importtool */
);

CREATE TABLE kategorien
(
       Kategorie_ID INT NOT NULL AUTO_INCREMENT,
       Positions_Nr INT NOT NULL DEFAULT 0,
       Name VARCHAR(128) NOT NULL,
       Beschreibung TEXT NOT NULL,
       MwSt_Satz DOUBLE NOT NULL default '0.0',
       Bild_gross VARCHAR(255), # /* Seit v.1.1 sind die Bilder ins */
       Bild_klein VARCHAR(255), # /* Dateisystem ausgelagert worden */
       Bildtyp VARCHAR(15),
       Bild_last_modified TIMESTAMP(14),
       Unterkategorie_von VARCHAR(128) DEFAULT NULL,
       Details_anzeigen enum('N','Y') NOT NULL DEFAULT 'N',
       PRIMARY KEY (Kategorie_ID),
       UNIQUE UC_Kategorie_ID (Kategorie_ID),
       INDEX IDX_kategorien_1 (Name),
       INDEX IDX_kategorien_2 (Kategorie_ID)
);

CREATE TABLE bestellung
(
       Bestellungs_ID INT NOT NULL AUTO_INCREMENT,
       Session_ID CHAR(32) NOT NULL DEFAULT 0,
       # /* Unsere getesteten Session_IDs waren immer CHAR(32)... aber man weiss ja nie */
       expired BIGINT NOT NULL DEFAULT 0, # /* In andernen DBs ev. anderer Typ (10 Stellen) */
       Datum Date,
       Endpreis DOUBLE NOT NULL DEFAULT 0.0,
       Anmerkung TEXT,
       Bezahlungsart CHAR(32) NOT NULL,
       Kreditkarten_Hersteller CHAR(32) NOT NULL,
       Kreditkarten_Nummer CHAR(16) NOT NULL,
       Kreditkarten_Ablaufdatum VARCHAR(10) NOT NULL,
       Kreditkarten_Vorname VARCHAR(128) NOT NULL,
       Kreditkarten_Nachname VARCHAR(128) NOT NULL,
       Attribut1 VARCHAR(128) NOT NULL,
       Attribut2 VARCHAR(128) NOT NULL,
       Attribut3 VARCHAR(128) NOT NULL,
       Attribut4 VARCHAR(128) NOT NULL,
       Attributwert1 VARCHAR(128) NOT NULL,
       Attributwert2 VARCHAR(128) NOT NULL,
       Attributwert3 VARCHAR(128) NOT NULL,
       Attributwert4 VARCHAR(128) NOT NULL,
       clearing_id VARCHAR(255) NOT NULL, # /* ID des externen Zahlungsinstituts */
       clearing_extra TEXT NOT NULL, # /* Zusaetzliches Attribut von einem externen Institut */
       Versandkosten DOUBLE NOT NULL DEFAULT 0.0,
       Nachnamebetrag DOUBLE NOT NULL DEFAULT 0.0,
       Mindermengenzuschlag DOUBLE NOT NULL DEFAULT 0.0,
       Rechnungsbetrag DOUBLE NOT NULL DEFAULT 0.0,
       MwSt DOUBLE NOT NULL DEFAULT 0.0,
       Bestellung_abgeschlossen enum('N','Y') NOT NULL DEFAULT 'N',
       Bestellung_ausgeloest enum('N','Y') NOT NULL DEFAULT 'N',
       Bestellung_bezahlt enum('N','Y') NOT NULL DEFAULT 'N',
       Bestellung_string TEXT NOT NULL DEFAULT '',
       temp_message_string TEXT NOT NULL,
       PRIMARY KEY (Bestellungs_ID),
       UNIQUE UC_Bestellungs_ID (Bestellungs_ID),
       INDEX IDX_bestellung_1 (Session_ID)
);

CREATE TABLE kunde
(
       k_ID INT NOT NULL AUTO_INCREMENT,
       Kunden_ID VARCHAR(255) NOT NULL,
       Kunden_Nr VARCHAR(255) NOT NULL DEFAULT 0,
       Session_ID CHAR(32) NOT NULL DEFAULT 0,
       expired BIGINT(20) NOT NULL DEFAULT 0,
       Adresstyp ENUM('Kunde','Rechnungsadresse','Lieferadresse') NOT NULL DEFAULT 'Kunde',
       Adresstyp_Kunden_ID VARCHAR(255) NOT NULL DEFAULT '0',
       Anrede VARCHAR(24) NOT NULL,
       Vorname VARCHAR(128) NOT NULL,
       Nachname VARCHAR(128) NOT NULL,
       Firma VARCHAR(128) NOT NULL,
       Abteilung VARCHAR(128) NOT NULL,
       Strasse VARCHAR(128) NOT NULL,
       Postfach VARCHAR(16) NOT NULL,
       PLZ VARCHAR(32) NOT NULL,
       Ort VARCHAR(128) NOT NULL,
       Land VARCHAR(128) NOT NULL,
       Tel VARCHAR(32) NOT NULL,
       Fax VARCHAR(32) NOT NULL,
       Email VARCHAR(128) NOT NULL,
       Login VARCHAR(32) NOT NULL,
       Passwort VARCHAR(32) NOT NULL,
       gesperrt VARCHAR(255) NOT NULL DEFAULT 'freigeschaltet',
       temp enum('N','Y') NOT NULL DEFAULT 'Y',
       Bezahlungsart VARCHAR(32) NOT NULL,
       AnmeldeDatum Date,
       LetzteBestellung Date,
       Einkaufsvolumen DOUBLE NOT NULL DEFAULT 0.0,
       Beschreibung TEXT NOT NULL,
       Neukunde enum('N','Y') NOT NULL DEFAULT 'N',
       kontoinhaber varchar(128) NOT NULL,
       bankname varchar(128) NOT NULL,
       blz varchar(128) NOT NULL,
       kontonummer varchar(128) NOT NULL,
       bankdaten_speichern enum('N','Y') NOT NULL DEFAULT 'Y',
       #/* Attribute sind selbstkonfigurierbare Eintraege */
       Attribut1 VARCHAR(128) NOT NULL,
       Attribut2 VARCHAR(128) NOT NULL,
       Attribut3 VARCHAR(128) NOT NULL,
       Attribut4 VARCHAR(128) NOT NULL,
       Attributwert1 VARCHAR(128) NOT NULL,
       Attributwert2 VARCHAR(128) NOT NULL,
       Attributwert3 VARCHAR(128) NOT NULL,
       Attributwert4 VARCHAR(128) NOT NULL,
       PRIMARY KEY (k_ID),
       UNIQUE UC_k_ID (k_ID),
       UNIQUE UC_Kunden_ID (Kunden_ID),
       UNIQUE UC_Login (Login),
       INDEX IDX_Kunden_ID_1 (Kunden_ID),
       INDEX IDX_kunde_2 (Kunden_Nr)
);

CREATE TABLE attribut
(
       Attribut_ID INT NOT NULL AUTO_INCREMENT,
       Name VARCHAR(128) NOT NULL,
       Wert VARCHAR(128) NOT NULL,
       anzeigen enum('N','Y') NOT NULL DEFAULT 'Y',
       in_DB enum('N','Y') NOT NULL DEFAULT 'Y',
       Eingabe_testen enum('N','Y') NOT NULL DEFAULT 'Y',
       Positions_Nr INT NOT NULL DEFAULT 0,
       PRIMARY KEY (Attribut_ID),
       UNIQUE UC_Attribut_ID (Attribut_ID),
       UNIQUE UC_Positions_Nr (Positions_Nr)
);

CREATE TABLE artikel_kategorie
(
       a_k_ID INT NOT NULL AUTO_INCREMENT,
       FK_Artikel_ID INT NOT NULL,
       FK_Kategorie_ID INT NOT NULL,
       FOREIGN KEY (FK_Artikel_ID) REFERENCES artikel (Artikel_ID),
       FOREIGN KEY (FK_Kategorie_ID) REFERENCES kategorien (Kategorie_ID),
       PRIMARY KEY (a_k_ID)
);

CREATE TABLE artikel_optionen
(
       Optionen_Nr INT NOT NULL DEFAULT 1,
       Optionstext CHAR(64),
       Preisdifferenz DOUBLE NOT NULL DEFAULT 0.0,
       FK_Artikel_ID INT NOT NULL,
       Gewicht_Opt DOUBLE NOT NULL default '0',
       FOREIGN KEY (FK_Artikel_ID) REFERENCES artikel (Artikel_ID)
);


CREATE TABLE artikel_variationen
(
       Variations_Nr INT NOT NULL DEFAULT 1,
       Variationstext CHAR(64),
       Aufpreis DOUBLE NOT NULL DEFAULT 0.0,
       FK_Artikel_ID INT NOT NULL,
       Variations_Grp INT(11) NOT NULL default '1',
       Gewicht_Var DOUBLE NOT NULL default '0',
       FOREIGN KEY (FK_Artikel_ID) REFERENCES artikel (Artikel_ID)
);

CREATE TABLE artikel_variationsgruppen
(
       Gruppen_ID int(11) NOT NULL AUTO_INCREMENT,
       FK_Artikel_ID int(11) NOT NULL default '0',
       Gruppen_Nr int(11) NOT NULL default '0',
       Gruppentext varchar(255) default '',
       Gruppe_darstellen varchar(255) NOT NULL default 'radio',
       FOREIGN KEY (FK_Artikel_ID) REFERENCES artikel (Artikel_ID),
       PRIMARY KEY (Gruppen_ID)
);

CREATE TABLE artikel_bestellung
(
       a_b_ID INT NOT NULL AUTO_INCREMENT,
       FK_Artikel_ID INT NOT NULL,
       FK_Bestellungs_ID INT NOT NULL,
       Artikelname VARCHAR(255) NOT NULL DEFAULT '',
       Preis DOUBLE NOT NULL DEFAULT 0.0,
       Gewicht DOUBLE NOT NULL DEFAULT 0.0,
       Anzahl INT NOT NULL DEFAULT 1,
       Variation TEXT NOT NULL,
       #/* Aufbau einer Variation:  Variationstext þ Aufpreis */
       Optionen TEXT NOT NULL,
       #/* Aufbau einer Option:  Optionstext þ Preisdifferenz ... */
       Zusatztexte text,
       FOREIGN KEY (FK_Artikel_ID) REFERENCES artikel (Artikel_ID),
       FOREIGN KEY (FK_Bestellungs_ID) REFERENCES bestellung (Bestellungs_ID),
       PRIMARY KEY (a_b_ID,FK_Artikel_ID,FK_Bestellungs_ID)
       #/* Anm. Das Trennzeichen ist ALT+0254, dieser Design muss allenfalls */
       #/* ueberarbeitet werden! */
);

CREATE TABLE bestellung_kunde
(
       b_k_ID INT NOT NULL AUTO_INCREMENT,
       FK_Kunden_ID VARCHAR(255) NOT NULL,
       FK_Bestellungs_ID INT NOT NULL,
       FOREIGN KEY (FK_Kunden_ID) REFERENCES kunde (Kunden_ID),
       PRIMARY KEY (b_k_ID)
);

CREATE TABLE hilfe
(
       Hilfe_ID VARCHAR(128) NOT NULL,
       Hilfetext TEXT,
       PRIMARY KEY (Hilfe_ID),
       UNIQUE UC_Hilfe_ID (Hilfe_ID)
);

CREATE TABLE shop_settings
(
       Setting_Nr INT NOT NULL AUTO_INCREMENT,
       MwStsatz DOUBLE NOT NULL DEFAULT 7.6,
       MwStpflichtig enum('N','Y') NOT NULL DEFAULT 'N',
       MwStNummer VARCHAR(127) NOT NULL DEFAULT 0,
       Name CHAR(48) NOT NULL DEFAULT ' {shop_db} ',
       Adresse1 CHAR(48) NOT NULL DEFAULT 'Adresse 1',
       Adresse2 CHAR(48) NOT NULL DEFAULT 'Adresse 2',
       PLZOrt CHAR(48) NOT NULL DEFAULT 'PLZ und Ort',
       Tel1 CHAR(24) NOT NULL DEFAULT 'Telefon Nummer 1',
       Tel2 CHAR(24) NOT NULL DEFAULT 'Telefon Nummer 2',
       Email VARCHAR(128) NOT NULL DEFAULT 'IhrShop@shopserver.com',
       Admin_pwd CHAR(16) NOT NULL DEFAULT 'machshop',
       Abrechnung_nach_Preis enum('N','Y') NOT NULL DEFAULT 'N',
       Abrechnung_nach_Gewicht enum('N','Y') NOT NULL DEFAULT 'N',
       Abrechnung_nach_Pauschale enum('N','Y') NOT NULL DEFAULT 'Y',
       Pauschale_text varchar(127) NOT NULL DEFAULT 'Versand- und Verpackungskosten',
       Mindermengenzuschlag enum('N','Y') NOT NULL DEFAULT 'N',
       Mindermengenzuschlag_bis_Preis DOUBLE NOT NULL DEFAULT 0.0,
       Mindermengenzuschlag_Aufpreis DOUBLE NOT NULL DEFAULT 0.0,
       keineVersandkostenmehr enum('N','Y') NOT NULL DEFAULT 'N',
       keineVersandkostenmehr_ab DOUBLE NOT NULL DEFAULT 0.0,
       anzahl_Versandkostenintervalle INT NOT NULL DEFAULT 5,
       Rechnung enum('N','Y') NOT NULL DEFAULT 'Y',
       Lastschrift enum('N','Y') NOT NULL DEFAULT 'Y',
       Nachnahme enum('N','Y') NOT NULL DEFAULT 'Y',
       Vorauskasse enum('N','Y') NOT NULL DEFAULT 'Y',
       Kreditkarten_Postcard enum('N','Y') NOT NULL DEFAULT 'N',
       Nachnamebetrag DOUBLE NOT NULL DEFAULT 0.0,
       Kontoinformation VARCHAR(255) NOT NULL DEFAULT 'Einzahlungen bitte auf unser Postkonto PC 00-000000-0',
       Waehrung CHAR(6) NOT NULL DEFAULT 'CHF',
       ShopVersion CHAR(56) NOT NULL DEFAULT 'Januar 2007, Version v.1.4.010',
       Gewichts_Masseinheit CHAR(16) NOT NULL DEFAULT 'kg',
       Thumbnail_Breite INT NOT NULL DEFAULT 100,
       AGB TEXT, #/* Max. 65535 Bytes gross in MySQL */
       TLS_value enum('N','Y') NOT NULL DEFAULT 'N', #/* Dieses Attribut hiess frueher SSL */
       Bestellungsmanagement enum('N','Y') NOT NULL DEFAULT 'Y',  #/* Seit v.1.4 default = Y */
       SuchInkrement INT NOT NULL DEFAULT 10,
       max_session_time BIGINT, #/* In anderen DBs ev. ein anderer Typ (10 Stellen) */
       Opt_inc INT NOT NULL DEFAULT 3,
       Var_inc INT NOT NULL DEFAULT 3,
       Opt_anz INT NOT NULL DEFAULT 5,
       Var_anz INT NOT NULL DEFAULT 5,
       Vargruppen_anz INT(11) NOT NULL DEFAULT 3,
       Eingabefelder_anz INT(11) NOT NULL DEFAULT 0,
       Gesamtpreis_runden enum('N','Y') NOT NULL DEFAULT 'N',
       ArtikelSuchInkrement INT NOT NULL DEFAULT -1,
       Sortieren_nach VARCHAR(128) DEFAULT 'a.Name' NOT NULL,
       Sortiermethode enum('ASC','DESC') DEFAULT 'ASC' NOT NULL,
       Zahl_thousend_sep char(2) NOT NULL DEFAULT '''',
       Zahl_decimal_sep char(2) NOT NULL DEFAULT '.',
       Zahl_nachkomma tinyint(3) NOT NULL DEFAULT '2',
       Haendlermodus ENUM('Y','N') NOT NULL DEFAULT 'N',
       Haendler_login_text TEXT, #/* Max. 65535 Bytes gross in MySQL */
       tell_a_friend ENUM('Y','N') NOT NULL DEFAULT 'N',
       tell_a_friend_bcc VARCHAR(255) DEFAULT '' NOT NULL,
       my_account ENUM('Y','N') NOT NULL DEFAULT 'N',
       check_user_country ENUM('Y','N') NOT NULL DEFAULT 'N',
       PRIMARY KEY (Setting_Nr),
       UNIQUE UC_Setting_Nr (Setting_Nr)
);

CREATE TABLE shop_settings_new
(
       name VARCHAR(250) NOT NULL DEFAULT '',  #/* Auf 250 beschraenkt, weil MySQL Keys maximal  */
       gruppe VARCHAR(250) NOT NULL DEFAULT '',#/* 500 Zeichen lang sein duerfen (name + gruppe) */
       wert TEXT NOT NULL DEFAULT '',
       security ENUM('user','admin') NOT NULL DEFAULT 'admin',
       PRIMARY KEY (name, gruppe),
       UNIQUE UC_name_gruppe (name, gruppe)
);

CREATE TABLE versandkostenpreise
(
       Von_Bis_ID INT NOT NULL AUTO_INCREMENT,
       Von DOUBLE DEFAULT 0.0,
       Bis DOUBLE DEFAULT 100.0,
       Betrag DOUBLE DEFAULT 0.0,
       Vorauskasse enum('N','Y') NOT NULL DEFAULT 'Y',
       Rechnung enum('N','Y') NOT NULL DEFAULT 'Y',
       Nachname enum('N','Y') NOT NULL DEFAULT 'Y',
       Lastschrift enum('N','Y') NOT NULL DEFAULT 'Y',
       Kreditkarte enum('N','Y') NOT NULL DEFAULT 'N',
       billBOX enum('N','Y') NOT NULL DEFAULT 'N',
       Treuhandzahlung enum('N','Y') NOT NULL DEFAULT 'N',
       Postcard enum('N','Y') NOT NULL default 'N',
       FK_Setting_Nr INT NOT NULL DEFAULT 1,
       FOREIGN KEY (FK_Setting_Nr) REFERENCES shop_settings (Setting_Nr),
       PRIMARY KEY (Von_Bis_ID),
       UNIQUE UC_Von_Bis_ID (Von_Bis_ID)
);

CREATE TABLE kreditkarte
(
       Kreditkarten_ID INT NOT NULL AUTO_INCREMENT,
       Hersteller CHAR(32) NOT NULL,
       Handling enum('intern','extern','saferpay','postfinance') NOT NULL DEFAULT 'intern',
       benutzen enum('N','Y') NOT NULL DEFAULT 'Y',
       PRIMARY KEY (Kreditkarten_ID),
       UNIQUE UC_Kreditkarten_ID (Kreditkarten_ID)
);

CREATE TABLE css_file
(
       Attribut_ID CHAR(32) NOT NULL,
       CSS_String CHAR(255),
       PRIMARY KEY (Attribut_ID),
       UNIQUE UC_Attribut_ID (Attribut_ID),
       INDEX IDX_Attribut_ID_1 (Attribut_ID)
);

CREATE TABLE backup
(
       Backup_ID CHAR(32) NOT NULL,
       Wert CHAR(255),
       PRIMARY KEY (Backup_ID),
       UNIQUE UC_Backup_ID (Backup_ID)
);

CREATE TABLE zahlung_weitere
(
       Zahlung_ID INT NOT NULL AUTO_INCREMENT,
       Gruppe VARCHAR(128) NOT NULL,
       Bezeichnung VARCHAR(128) NOT NULL,
       verwenden enum('N','Y') NOT NULL DEFAULT 'N',
       payment_interface_name VARCHAR(128) NOT NULL,
       Par1 VARCHAR(128) NOT NULL,
       Par2 VARCHAR(128) NOT NULL,
       Par3 VARCHAR(128) NOT NULL,
       Par4 VARCHAR(128) NOT NULL,
       Par5 VARCHAR(128) NOT NULL,
       Par6 VARCHAR(128) NOT NULL,
       Par7 VARCHAR(128) NOT NULL,
       Par8 VARCHAR(128) NOT NULL,
       Par9 VARCHAR(128) NOT NULL,
       Par10 VARCHAR(128) NOT NULL,
       PRIMARY KEY (Zahlung_ID),
       UNIQUE UC_Gruppe_Bezeichnung (Gruppe,Bezeichnung)
);

CREATE TABLE mehrwertsteuer
(
       Mehrwertsteuer_ID INT NOT NULL AUTO_INCREMENT,
       MwSt_Satz DOUBLE NOT NULL default '0.0',
       MwSt_default_Satz enum('N','Y') NOT NULL DEFAULT 'N',
       Preise_inkl_MwSt enum('N','Y') NOT NULL DEFAULT 'Y',
       Beschreibung VARCHAR(255) NOT NULL,
       Positions_Nr INT NOT NULL,
       PRIMARY KEY (Mehrwertsteuer_ID),
       UNIQUE UC_Positions_Nr (Positions_Nr)
);

# /*
# * End of File Marke -------------------------------------------------------------
# */
