# /*
# * {shop_db}_tables_only_del.sql
# *
# * ZHW Zuercher Hochschule Winterthur
# * PhPepperShop Diplomarbeit DA Fei01/1
# * Autoren: Jose Fontanil & Reto Glanzmann
# *
# * Version 1.4, basierend auf ER-Diagramm v.1.2.6
# *
# * CVS-Version / Datum: $Id: template_del_tables_only.sql,v 1.17 2003/05/24 16:58:16 fontajos Exp $
# *
# * Loescht die Tabellen der Shop-Datenbank {shop_db}
# * und optional die beiden USER: {shopadmin}, {shopuser}
# * (Tangierte Systemtabellen: ALLE!)
# *
# * !!!ACHTUNG: KEINE RUECKFRAGE, ALLE DATETN SIND DANACH GELOESCHT!!!
# *
# * einlesen z.B. mit mysql -u {grantor} -p < ./{shop_db}_create.sql
# * (wobei mysql im Pfad sein muss. Datenbank Hostrechner: {hostname})
# *
# * Sicherheitsstatus:       *** ADMIN ***
# *
# * ------------------------------------------------------------------------------
# */

DROP TABLE IF EXISTS artikel_bestellung;
DROP TABLE IF EXISTS artikel_kategorie;
DROP TABLE IF EXISTS artikel_optionen;
DROP TABLE IF EXISTS artikel_variationen;
DROP TABLE IF EXISTS artikel_variationsgruppen;
DROP TABLE IF EXISTS artikel;
DROP TABLE IF EXISTS attribut;
DROP TABLE IF EXISTS backup;
DROP TABLE IF EXISTS bestellung;
DROP TABLE IF EXISTS bestellung_kunde;
DROP TABLE IF EXISTS css_file;
DROP TABLE IF EXISTS hilfe;
DROP TABLE IF EXISTS kategorien;
DROP TABLE IF EXISTS kreditkarte;
DROP TABLE IF EXISTS kunde;
DROP TABLE IF EXISTS mehrwertsteuer;
DROP TABLE IF EXISTS shop_settings;
DROP TABLE IF EXISTS shop_settings_new;
DROP TABLE IF EXISTS versandkostenpreise;
DROP TABLE IF EXISTS zahlung_weitere;

# */ {nomakeuser} */
use {mysql_db}

delete from user where user="{shopadmin}" and Host="{hostname}";

# */ {einuserseq} */
delete from user where user="{shopuser}" and Host="{hostname}";
# */ {einuserseq} */

delete from db where User="{shopadmin}" and Host="{hostname}";

# */ {einuserseq} */
delete from db where User="{shopuser}" and Host="{hostname}";

delete from tables_priv where User="{shopuser}" and Host="{hostname}";
# */ {einuserseq} */
# */ {nomakeuser} */

# /*
# * ------------------------------------------------------------ End of File Marke
# */
