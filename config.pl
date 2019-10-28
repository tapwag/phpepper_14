#!/usr/bin/perl
# File:      config.pl
# Author:    Jose Fontanil, Reto Glanzmann
# Mit Ideen von: Marko Scheffler
# Project:   PhPepperShop
# Version:   1.4 (see also config.pm)
# CVS-Version / Datum: $Id: config.pl,v 1.57 2003/06/10 17:49:15 fontajos Exp $
# CVS-Tag: $Revision: 1.57 $
# Security:  *** ADMIN ***
# Dieses Script installiert einen PhPepperShop via Kommandozeile / Telnet / SSH
#---------------------------------------------

#----------------------------------------
# Benoetigte Module einbinden
#----------------------------------------
require "config.pm";

#----------------------------------------
# Variablendeklaration
#----------------------------------------

chomp ($install_path = `pwd`);
$mysqlname = "mysql";
$db_grantor = "root";
$rechnerip = "localhost";
$einusersequenz = 0; # ist <> 0 wenn eine für 1-User-Betrieb auszuklammernde Sequenz folgt
$nomakeuserseq = 0; # ist <> 0 wenn eine Sequenz folgt welche Benutzer erstellt
$einloggen_flag ="n";
$abort = 0;
$db_error = 0;
my $return_code = 0;

system "clear";
print "                                                          |-----------------|\n";
print "PhPepperShop Konfiguration (MySQL-DB) Abbruch mit CTRL+C  |Teil 1 - Eingaben|\n";
print "=====================================                     |-----------------|\n\n";

#----------------------------------------
# Maske zur Parameter Eingabe (Shopname, DB-User Anzahl, DB-Admin Name)
#----------------------------------------
print "ACHTUNG:\n";
print "Sind Sie in ihrem Webverzeichnis? - wenn nicht, kopieren sie dieses Verzeichnis\nzuerst dorthin. ";
print "Ansonsten ist ihr Shop nach der Installation nicht per Web zugaenglich.\n";
print "\nName der Shop-Datenbank (wird auch in der Shop-URL stehen): ";
$falsch = 1;
while ($falsch){
    $shopname = <STDIN>;
    $falsch = 0;
    if ($shopname !~ /^([^ \n\t][A-Z,a-z,0-9,_]{3,64}$)/){
        print "Als Shopnamen kann man nur EIN Wort eingeben (min. 4 Zeichen, max. 64 Zeichen,\nkeine Sonderzeichen ausser _)!\n";
        $falsch = 1;
    }
}
chomp($shopname);

print "\nGeben Sie nun den Standort ihrer Datenbank ein. Ist die Datenbank auf dem gleichen";
print "\nRechner, auf welchem Sie diesen Shop installieren und betreiben, so geben Sie hier";
print "\nlocalhost ein. Ist die Datenbank auf einem anderen Rechner, so geben Sie hier seine";
print "\nAdresse an (z.B. unidb.web.ch).\nDatenbank Hostname: ";
$falsch = 1;
while ($falsch){
    $hostname = <STDIN>;
    $falsch = 0;
    if ($hostname !~ /^([^ \n\t][A-Z,a-z,0-9,\.,\:,\-,\_]{1,254}$)/){
        print "Sie koennen nur EIN Wort eingeben!\n";
        $falsch = 1;
    }
}
chomp($hostname);

# Test ob der MySQL Monitor (programm namens mysql) gefunden werden kann, ansonsten wird gefragt.
# Folgende Zeile fuehrt den Befehl in den Backticks aus (which mysql) und gibt das Resultat zurueck
$mysqlverwenden = "j"; #initialisieren dieser Variable
$mysqlpath = `which mysql`;
chomp($mysqlpath);
if ($mysqlpath !~ /^(which)/) {
    $mysqlpath= &pathWithoutFile($mysqlpath, 'mysql');  # Den eigentlichen Befehl mysql entferneen
    print "\nEin MySQL Monitor wurde unter $mysqlpath gefunden, wollen Sie diesen benutzen?\n(Empfohlen: ja, j/n): ";
    $falsch = 1;
    while ($falsch) {
        $mysqlverwenden = <STDIN>;
        $falsch = 0;
        if ($mysqlverwenden !~ /^([jn]$){1,1}/){
            print "Sie können nur j für ja und n für nein waehlen!\n";
            $falsch = 1;
        }
    }
    chomp($mysqlverwenden); #Ueberfluessiges \n entfernen
}
else {
    print "\nDer Pfad zum MySQL Monitor (Programm namens mysql) konnte nicht ausfindig gemacht\n";
    print "werden. Bitte geben Sie den Pfad ein (oder Abbruch des Programms mit CTRL+C).";
    print "\nPfad (Bsp. /usr/local/mysql/bin/):";
    $falsch = 1;
    while ($falsch){
        $mysqlpath = <STDIN>;
        $falsch = 0;
        if ($mysqlpath !~ /^([^ \n\t]{1,254}$)/){
            print "Ein UNIX Pfad ist immer EIN zusammenhaengendes Wort\n";
            $falsch = 1;
        }
    }
    chomp($mysqlpath);
    # Falls der Benutzer keinen Slash am Ende des Pfades angegeben hat, diesen noch hinzufuegen
    if ($mysqlpath !~ /\/\Z/) {
        $mysqlpath = $mysqlpath."/";
    }
}

# Hat man oben angewaehlt, dass man den MySQL-Monitor-Pfad selber angeben moechte, so kann man das hier tun:
if ($mysqlverwenden =~ /n/) {
    print "\nBitte geben Sie den Pfad des gewuenschten MySQL-Monitors ein.\nPfad (Bsp. /usr/local/mysql/bin/):";
    $falsch = 1;
    while ($falsch){
        $mysqlpath = <STDIN>;
        $falsch = 0;
        if ($mysqlpath !~ /^([^ \n\t]{1,254}$)/){
            print "Ein UNIX Pfad ist immer EIN zusammenhaengendes Wort\n";
            $falsch = 1;
        }
    }
    chomp($mysqlpath);
    # Falls der Benutzer keinen Slash am Ende des Pfades angegeben hat, diesen noch hinzufuegen
    if ($mysqlpath !~ /\/\Z/) {
        $mysqlpath = $mysqlpath."/";
    }
}

print "\nSollen fuer den Shop ein oder zwei Datenbank-User erstellt werden?";
print "\n\nWenn sie schon zwei Datenbank-User haben oder zwei erzeugen lassen wollen,";
print "\nwaehlen sie zwei. Wir empfehlen dies, da nur so unser Security Konzept\nvollstaendig umgesetzt werden kann!";
print "\nEin oder zwei Datenbank-User benutzen? (1|2): ";
$falsch = 1;
while ($falsch) {
    chomp($einzwei = <STDIN>);
    $falsch = 0;
    if (($einzwei < 1)||($einzwei > 2)){
        print "Sie koennen nur zwischen der Zahl 1 oder 2 wählen!\n";
        $falsch = 1;
    }
    # Da es nur einen User gibt, sind der shopuser und der shopadmin gleich
    # Weiter unten im Skript werden diese DB-User gleichgesetzt.
}

if($einzwei == 2){
    print "\nSollen die User von den Skripten automatisch erzeugt (und bei der Deinstallation\nwieder geloescht) werden?\n";
    print "\nWenn sie keine Datenbank-User anlegen duerfen, waehlen sie n, ebenfalls wenn";
    print "\ndie User schon existieren. Geben sie n für nein ein, ansonsten ein j: (j|n): ";
    $falsch = 1;
    while ($falsch) {
        $makeuser = <STDIN>;
        $falsch = 0;
        if ($makeuser !~ /^([jn]$){1,1}/){
            print "Sie können nur j für ja und n für nein waehlen!\n";
            $falsch = 1;
        }
    }
    chomp($makeuser); #Ueberfluessiges \n entfernen

    # Herausfinden der lokalen IP-Adresse und:
    # Warnmeldung, wenn der Host auf einem anderen Rechner laeuft und neue User angelegt werden
    if ($hostname !~ /^(localhost)/) {
        use Socket;
        use Sys::Hostname;
        $rechnername = hostname();
        $rechnerip = inet_ntoa(scalar(gethostbyname($rechnername)) || 'localhost');
        print "\nINFORMATION: Da ihre Datenbank nicht auf dem lokalen Server liegt, werden die User der ";
        print "\nShop-Datenbank so eingerichtet, dass man sich von DIESEM Rechner, definiert durch folgende IP-Adresse ";
        print "\nin die Datenbank einloggen kann: Rechnername: $rechnername, IP-Adresse: $rechnerip \n";
    }
}
else {
    print "\nSoll der User von den Skripten automatisch erzeugt/gelöscht werden?\n";
    print "\nWenn sie keinen Datenbank-User anlegen duerfen waehlen sie n, ebenfalls wenn";
    print "\nder User schon existiert, geben sie n für nein ein, ansonsten ein j: (j|n): ";
    $falsch = 1;
    while ($falsch) {
        $makeuser = <STDIN>;
        $falsch = 0;
        if ($makeuser !~ /^([jn]$){1,1}/){
            print "Sie können nur j für ja und n für nein waehlen!\n";
            $falsch = 1;
        }
    }
    chomp($makeuser); #Ueberfluessiges \n entfernen

    # Herausfinden der lokalen IP-Adresse und:
    # Warnmeldung, wenn der Host auf einem anderen Rechner laeuft und neue User angelegt werden
    if ($hostname !~ /^(localhost)/) {
        use Socket;
        use Sys::Hostname;
        $rechnername = hostname();
        $rechnerip = inet_ntoa(scalar(gethostbyname($rechnername)) || 'localhost');
        print "\nINFORMATION: Da ihre Datenbank nicht auf dem lokalen Server liegt, wird der User der ";
        print "\nShop-Datenbank so eingerichtet, dass man sich von DIESEM Rechner, definiert durch folgende IP-Adresse ";
        print "\nin die Datenbank einloggen kann: Rechnername: $rechnername, IP-Adresse: $rechnerip \n";
    }
}

print "\nMySQL Admin Loginname: ";
$falsch = 1;
while ($falsch){
    $dbadmin = <STDIN>;
    $falsch = 0;
    if ($dbadmin !~ /^([^ \n\t]{4,16}$)/){
        print "Sie koennen nur EIN Wort eingeben (min. 4, max. 16 Zeichen)!\n";
        $falsch = 1;
    }
}
chomp($dbadmin);

print "\nMySQL Admin Passwort: ";
$falsch = 1;
while ($falsch){
    $dbadminpwd = <STDIN>;
    $falsch = 0;
    if ($dbadminpwd !~ /^([^ \n\t]{4,32}$)/){
        print "Sie koennen nur EIN Wort eingeben (min. 4, max. 32 Zeichen)!\n";
        $falsch = 1;
    }
}
chomp($dbadminpwd);

if ($einzwei == 2){
    # Wenn zwei User, weitere Angaben anfragen
    print "\nMySQL Shopuser Loginname: ";
    $falsch = 1;
    while ($falsch){
        $dbuser = <STDIN>;
        $falsch = 0;
        if ($dbuser !~ /^([^ \n\t]{4,16}$)/){
            print "Sie koennen nur EIN Wort eingeben (min. 4, max. 16 Zeichen)!\n";
            $falsch = 1;
        }
    }
    chomp($dbuser);
    print "\nMySQL Shopuser Passwort: ";
    $falsch = 1;
    while ($falsch){
        $dbuserpwd = <STDIN>;
        $falsch = 0;
        if ($dbuserpwd !~ /^([^ \n\t]{4,32}$)/){
            print "Sie koennen nur EIN Wort eingeben (min. 4, max. 32 Zeichen)!\n";
            $falsch = 1;
        }
    }
    chomp($dbuserpwd);
}
else {
    # Beim 1-User-Betrieb hat der User auch 'Admin-Rechte'
    $dbuser = $dbadmin;
    $dbuserpwd = $dbadminpwd;
}

# Anzeige aller eingegebenen Daten
print "\n-> Alle Eingaben erfasst:      (Bitte aufschreiben!)\n";
print "     Name der Shop Datenbank:    $shopname\n";
print "     Anzahl DB-User:             $einzwei\n";
print "     DB-Hostname:                $hostname\n";
print "     DB-Admin Login:             $dbadmin\n";
print "     DB-Admin Pwd:               $dbadminpwd\n";
if ($einzwei == 2){
    print "     Shopuser:                   $dbuser\n";
    print "     Shopuser Pwd:               $dbuserpwd\n";
}
print "     User automatisch erstellen: $makeuser\n";
print "     Pfad zum MySQL Monitor:     $mysqlpath\n";

print "\n\nUm fortzufahren bitte Enter oder Return druecken";
$dummy = <STDIN>;

# vom der per pwd erfassten Pfadangabe das letzte Verzeichnis (typisch '/phpeppershop_src' ) entfernen
$install_path = pathWithoutlastDir($install_path);

# Dateinamen für die SQL-Statements zusammenbauen
$filename_del = "$shopname"."_del.sql";
$filename_del_tables_only = "$shopname"."_del_tables_only.sql";
$filename_create = "$shopname"."_create.sql";
$filename_insert = "$shopname"."_insert.sql";
$zielname = "$shopname"."_mysql.sql";

# Dateinamen der zu erzeugenden PHP und Perl Dateien
$filename_ADMIN = "ADMIN_initialize.php";
$filename_initialize = "initialize.php";
$filename_REMOVE = "remove.pl";
$filename_BACKUP = "ADMIN_backup.php";

system "clear";

# Shopverzeichnis erstellen und Shopdateien hinein kopieren
$return_code = &copyShopfiles ($shopname, $install_path);
if ($return_code == 0) { &quit_install; }

print "\n\n\n\n\n\nUm fortzufahren bitte Enter oder Return druecken";
$dummy = <STDIN>;

system "clear";

# Aus den SQL-Templates für den Shop spezifische SQL-Statements erzeugen
$return_code = &createSQLFiles ($shopname, $einzwei, $hostname, $dbadmin, $dbadminpwd, $dbuser, $dbuserpwd, $makeuser, $mysqlpath,
    $nomakeuserseq, $einusersequenz, $mysqlname, $rechnerip, $db_grantor, $install_path, $filename_del_tables_only, $filename_del);
if ($return_code == 0) { &quit_install; }

# Aus den PHP und Perl-Templates die Shop spezifische Datenbankanbindung und das Deinstallationsskript erzeugen
$return_code = &createPHP_PL_Files ($shopname, $einzwei, $hostname, $dbadmin, $dbadminpwd, $dbuser, $dbuserpwd, $makeuser, $mysqlpath,
    $nomakeuserseq, $einusersequenz, $mysqlname, $db_grantor, $install_path, $filename_REMOVE, $filename_ADMIN, $filename_BACKUP, $shopname);
if ($return_code == 0) { &quit_install; }

#----------------------------------------
# Nun werden alle Datei Zugriffsrechte gesetzt. Manche Dateien und Verzeichnisse
# benoetigen spezielle Rechte.
#----------------------------------------
print "\n\nUm fortzufahren bitte Enter oder Return druecken";
$dummy = <STDIN>;

system "clear";
print "                                  |--------------------------------------|\n";
print "PhPepperShop: chmod               |Teil 4 - Zugriffsrechte konfigurieren |\n";
print "===================               |--------------------------------------|\n\n";

print "Die Shop-Installation wird nun vervollständigt:\n";
print "Datei und Verzeichnis Zugriffsrechte werden konfiguriert.\n\n";

# Dateirechte setzen
$return_code = &set_file_perm ($shopname, $install_path);
if ($return_code == 0) { &quit_install; }

# CVS Verzeichnisse entfernen
$return_code = &remove_cvs_dirs($shopname, $install_path);
if ($return_code == 0) { &quit_install; }

#----------------------------------------
# Optional kann hier noch die Shop-Datenbank installiert werden (sqls laufen lassen)
#----------------------------------------
print "\n\nUm fortzufahren bitte Enter oder Return druecken";
$dummy = <STDIN>;

system "clear";
print "                                  |------------------------------------------|\n";
print "PhPepperShop: MySQL-Shopdb        |Teil 5 - Shop-Datenbank im MySQL erstellen|\n";
print "==========================        |------------------------------------------|\n\n";

print "Nun kommt der letzte Teil der Shop-Installation:\n\n";
print "Soll versucht werden, die $shopname-Datenbank\n";
print "ins MySQL-DBMS einzufuegen (wenn sie schon existiert,";
print "werden nur die Tabellen eingefuegt)? (j|n): ";
$falsch = 1;
while ($falsch) {
    $makemysql = <STDIN>;
    $falsch = 0;
    if ($makemysql !~ /^([jn]$){1,1}/){
        print "Sie koennen nur j für ja und n für nein waehlen!\n";
        $falsch = 1;
    }
}
chomp($makemysql); #Ueberfluessiges \n entfernen
if ($makemysql =~ /j/){
    do {

        # SQL-Statement erzeugen, welches die Create- und Insert-Statements enthaelt
        $return_code = &createSQL_insert ($shopname, $filename_create, $filename_insert, $zielname, $install_path);
        if ($return_code == 0) { &quit_install; }

        # Eingabe des Datenbank Users welcher die SQL-Skripte einlesen wird ($grantor)
        print "\nWelcher MySQL-User soll die $shopname-Datenbank\n";
        print "ins MySQL-DBMS einfuegen (z.B. root): ";
        $falsch = 1;
        while ($falsch){
            $grantor = <STDIN>;
            $falsch = 0;
            if ($grantor !~ /^([^ \n\t]{4,32}$)/){
                print "Sie koennen nur EIN Wort eingeben (min. 4 Zeichen)!\n";
                $falsch = 1;
            }
        }
        chomp($grantor);
        # Eingabe des Passworts
        print "\nPasswort eingeben: ";
        $falsch = 1;
        while ($falsch){
            $grantor_pwd = <STDIN>;
            $falsch = 0;
            if ($grantor_pwd !~ /^([^ \n\t]{4,32}$)/){
                print "Sie koennen nur EIN Wort eingeben (min. 4 Zeichen)!\n";
                $falsch = 1;
            }
        }
        chomp($grantor_pwd);
        $abort = 0;
        $db_error = 0;

        # Erstellungsbefehl an die Datenbank absetzen
        &DB_create_insert ($hostname, $mysqlpath, $mysqlname, $grantor, $grantor_pwd, $shopname, $zielname, $hostname, $install_path);

        # Testen ob das SQL ausgefuehrt wurde: In der obigen Anweisung wird mit der Pager-
        # Anweisung eine Datei erzeugt (success_test), darin steht entweder nichts --> Fehler,
        # oder es steht das ausgefuehrte SQL-Skript darin --> Erfolg
        open(s_t, "<../$shopname/success_test") || die "Konnte die Datei ../$shopname/success_test nicht oeffnen! -> Abbruch\n";
        # getc(s_t) liest das erste Zeichen der Zeile eins ein (bei den sql-scripts ist die erste Zeile immer in -)
        $_ = getc(s_t);
        if ($_ eq "") {
            $db_error = 1;
            print "(Bei Error 1045) Entweder existiert kein Datenbank-User namens $grantor, oder ihr Passwort war falsch ($grantor_pwd)!";
            print "\n\nWollen sie nochmals versuchen einzuloggen? (j|n): ";
            $falsch = 1;
            while ($falsch) {
                $einloggen_flag = <STDIN>;
                $falsch = 0;
                if ($einloggen_flag !~ /^([jn]$){1,1}/){
                    print "Sie koennen nur j für ja und n für nein waehlen! (n = Abbruch der Installation)\n";
                    $falsch = 1;
                }
            }
            chomp($einloggen_flag); # Ueberfluessiges \n entfernen
            if ($einloggen_flag =~ /j/){
                $abort = 1;
            }
            else {
                $abort = 0;
            }
        }
        close(s_t);

        # nicht mehr benoetigte Dateien loeschen
        $return_code = &remove_unused_files ($install_path, $shopname, $zielname);

    } while ($abort == 1);
    # Endmeldung angeben
    if ($db_error == 0) {
        print "\n\n**********************************************************************";
        print "\n*    ACHTUNG: das Admin-Verzeichnis muss jetzt noch per htaccess     *";
        print "\n* geschuetzt werden! (siehe auch demo_htaccess im Admin Verzeichnis) *";
        print "\n**********************************************************************";
        print "\n\nIhr Shop ($shopname) sollte jetzt einsatzbereit sein.";
        print "\n(Die Shop-Dateien liegen im Verzeichnis ../$shopname/)\n\n";
        die "---Ende der Installation config.pl---\n\n";
    }
    else {
        print "\nNun muessen nur noch die MySQL Scripts (im Verzeichnis database) in MySQL eingelesen werden\n";
        print "Einlesen mit: $mysqlpath"."mysql -u $grantor -p < ./$filename_create";
        print "\n              $mysqlpath"."mysql -u $grantor -p < ./$filename_insert\n";
        print "\n\n**********************************************************************";
        print "\n*    ACHTUNG: das Admin-Verzeichnis muss danach noch per htaccess    *";
        print "\n* geschuetzt werden! (siehe auch demo_htaccess im Admin Verzeichnis) *";
        print "\n**********************************************************************";
        print "\n\n            Ihr Shop sollte danach einsatzbereit sein.\n\n";
        print "\n---Ende der Installation config.pl---\n\n";
    }
}

#----------------------------------------
# Function: quit_install
# Laesst die Installation im Fehlerfall per "die" sterben und gibt eine Nachricht aus, dass die Installation gescheitert ist
# Argumente: keine
# Rueckgabewert: keine
#----------------------------------------
sub quit_install {
        # Hinweis ausgeben
        print "\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
        print "\n!                   INSTALLATION GESCHEITERT                         !";
        print "\n!                     Fehler: siehe oben                             !";
        print "\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n";

        #Installation abbrechen
        die ;
} # end of function quit_install


#--------------------------------------------------------------
# Anmerkungen zu den Laengenlimitierungen bei Namen un MySQL
# Da der PhPepperShop im Moment zu 99,9% mit MySQL eingesetzt
# wird, haben wir zu den Laengenbeschraenkungen bei Namen
# einige Hinweise zusammengefasst:
#
# http://www.mysql.com/doc/en/User_names.html
# -------------------------------------------
#
# DB-Usernamen (shopuser, shopadmin):
# MySQL user names can be up to 16 characters long
#
# Passwort:
# On some systems, the library call that MySQL uses to prompt
# for a password will automatically cut the password to 8 characters.
# Internally MySQL doesn't have any limit for the length of the password.
#
#
# http://www.mysql.com/doc/en/Legal_names.html
# --------------------------------------------
#
# Datenbanknamen, Tabellennamen, Spaltennamen und Aliasnamen:
#
# Identifier   Max length   Allowed characters
# Database     64           Any character that is allowed in a directory name except `/', `\' or `.'.
# Table        64           Any character that is allowed in a file name, except `/' or `.'.
# Column       64           All characters.
# Alias        255          All characters.
#
#--------------------------------------------------------------


# End of file -------------------------------------------------

