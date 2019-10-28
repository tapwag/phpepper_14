# /*
# * {shop_db}_del.sql                               !!!ACHTUNG!!! GEFAEHRLICH --> LESEN!
# *
# * ZHW Zuercher Hochschule Winterthur
# * PhPepperShop Diplomarbeit DA Fei01/1
# * Autoren: Jose Fontanil & Reto Glanzmann
# *
# * Version 1.4 (basierend auf {shop_db}_create.sql Version 1.4)
# *
# * CVS-Version / Datum: $Id: template_del.sql,v 1.15 2003/05/05 14:43:38 fontajos Exp $
# *
# * Loescht die Shop-Datenbank {shop_db} und die beiden USER: {shopadmin}, {shopuser}
# * (Tangierte Systemtabellen: ALLE!)
# *
# * !!!ACHTUNG: KEINE RUECKFRAGE, ALLE DATETN SIND WEG!!!
# *
# * einlesen z.B. mit mysql -u {grantor} -p <./{shop_db}_del.sql
# * Datenbank Hostrechner: {hostname}
# *
# * Sicherheitsstatus:    *** ADMIN ***
# *
# * ------------------------------------------------------------------------------
# */

drop database {shop_db};

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

#/*
# * --------------------------------------------------------- End of File Marke
# */
