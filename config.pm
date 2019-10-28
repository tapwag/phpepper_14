#!/usr/bin/perl
# File:      config.pm
# Author:    José Fontanil, Reto Glanzmann
# Project:   PhPepperShop
# Version:   1.4 (see also config.pl)
# CVS-Version / Datum: $Id: config.pm,v 1.15 2003/05/24 18:41:16 fontajos Exp $
# CVS-Tag: $Revision: 1.15 $
# Security:  *** ADMIN ***
# Enthaelt die Funktionen für die Installation des Shops (wird von config.pl) gebraucht
#---------------------------------------------

# true zurueckliefern, damit diese Datei per require von anderen Dateien included werden kann
return 1;

#----------------------------------------
# Function: installShop
# Installiert den PhPepperShop
# Argumente:
#   $install_path -> Pfad, worin der Shop installiert werden soll
#   $mysqlpath ->    Pfad zum MySQL-Monitor (normalerweise "/usr/bin")
#   $mysqlname ->    Name vom Binary des MysQL-Monitors (normalerweise "mysql")
#   $hostname ->     Hostname oder IP von dem Rechner, auf dem MySQL laeuft
#   $db_grantor ->   MySQL-Benutzer, welcher bei der Erstellung der MySQL-Shopuser (optional) als Grantor
#                    in die MySQL-Systemtabellen eingetragen wird ("root")
#   $rechnerip ->    IP des Rechners, auf dem der Shop läuft (Webserver) [nur notwendig, wenn webserver
#                    und mysqlserver auf einer anderen ip laufen]
#   $shopname ->     Shopname (=Datenbankname)
#   $makeuser ->     j -> MySQL-benutzer automatisch erzeugen
#                    n -> MySQL-benutzer existieren schon oder werden manuell erzeugt
#   $einzwei         1 -> Shop laeuft mit einem MySQL-Datenbankbenutzer
#                    2-> Shop läuft mit zwei MySQL-Datenbankbenutzer
#   $dbadmin ->      Benutzername des Shop-Administrator MySQL-Benutzers
#   $dbadminpwd ->   Passwort des Shop-Administrator MySQL-Benutzers
#   $dbuser ->       Benutzername des Shop-Kunden MySQL-Benutzers. Falls der Shop nur mit einem Benutzer
#                    betrieben wird, kann hier ein Leerstring übergeben werden
#   $dbuserpwd ->    Passwort des Shop-Kunden MySQL-Benutzers. Falls der Shop nur mit einem Benutzer
#                    betrieben wird, kann hier ein Leerstring übergeben werden
#   $makemysql       j -> die erzeugten MySQL-Scripte werden automatisch ausgeführt
#                    n -> die erzeugten MySQL-Scripte werden manuell (von Hand) in das DBMS eingespiesen
#   $grantor ->      Benutzername des MySQL-Administrators ("root")
#   $grantor_pwd ->  Passwort des MySQL-Administrator
#   $file_owner ->   Benutzer, der Besitzer der Shopfiles sein soll
#   $file_group ->   Benutzergruppe von $file_owner
#   $logfile ->      In diese Datei wird der Installationsvorgang protokolliert
#
# Rueckgabewert: 0 -> Installation gescheitert
#                1 -> Installation erfolgreich
#                2 -> Shop ist schon installiert
#----------------------------------------
sub installShop {
    my ($install_path, $mysqlpath, $mysqlname, $hostname, $db_grantor, $rechnerip, $shopname, $makeuser,
        $einzwei, $dbadmin, $dbadminpwd, $dbuser, $dbuserpwd, $makemysql, $grantor, $grantor_pwd, $file_owner,
        $file_group, $logfile)= @_;

    my $return_code = 0;

    #urspruengliche Handles auf STDOUT und STDERR sichern
    open(OLDOUT, ">&STDOUT");
    open(OLDERR, ">&STDERR");

    # STDOUT und STDERR auf Logfile umleiten
    open(STDOUT, ">$logfile");
    open(STDERR, ">&STDOUT");

    # Verhindern, dass STDERR und STDOUT gebuffert werden
    select(STDERR); $| = 1;     # make unbuffered
    select(STDOUT); $| = 1;     # make unbuffered

    my @pfad_geteilt = &split_path ($install_path);
    $install_path = $pfad_geteilt[0];

    $einusersequenz = 0; # ist <> 0 wenn eine für 1-User-Betrieb auszuklammernde Sequenz folgt
    $nomakeuserseq = 0; # ist <> 0 wenn eine Sequenz folgt welche Benutzer erstellt

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

    # Beim 1-User-Betrieb hat der User auch 'Admin-Rechte'
    if ($einzwei == 1){
        $dbuser = $dbadmin;
        $dbuserpwd = $dbadminpwd;
    } # end of if

    # Kontrolle, ob der Shop an der angegebenen Stelle nicht schon installiert ist
    $return_code = &checkAlreadyInstalled ($pfad_geteilt[1], $install_path, $shopname);
    if ($return_code == 1) { return 2; }

    # Shopverzeichnis erstellen und Shopdateien hinein kopieren
    $return_code = &copyShopfiles ($shopname, $install_path);
    if ($return_code == 0) { return 0; }

    # Aus den SQL-Templates für den Shop spezifische SQL-Statements erzeugen
    $return_code = &createSQLFiles ($shopname, $einzwei, $hostname, $dbadmin, $dbadminpwd, $dbuser, $dbuserpwd, $makeuser, $mysqlpath,
        $nomakeuserseq, $einusersequenz, $mysqlname, $rechnerip, $db_grantor, $install_path, $filename_del_tables_only, $filename_del);
    if ($return_code == 0) { return 0; }

    # Aus den PHP und Perl-Templates die Shop spezifische Datenbankanbindung und das Deinstallationsskript erzeugen
    $return_code = &createPHP_PL_Files ($shopname, $einzwei, $hostname, $dbadmin, $dbadminpwd, $dbuser, $dbuserpwd, $makeuser, $mysqlpath,
        $nomakeuserseq, $einusersequenz, $mysqlname, $db_grantor, $install_path, $filename_REMOVE, $filename_ADMIN, $filename_BACKUP, $pfad_geteilt[1]);
    if ($return_code == 0) { return 0; }

    # Dateirechte setzen
    $return_code = &set_file_perm ($shopname, $install_path);
    if ($return_code == 0) { return 0; }

    # CVS Verzeichnisse entfernen
    $return_code = &remove_cvs_dirs($shopname, $install_path);
    if ($return_code == 0) { return 0; }

    # SQL-Statement erzeugen, welches die Create- und Insert-Statements enthaelt
    $return_code = &createSQL_insert ($shopname, $filename_create, $filename_insert, $zielname, $install_path);
    if ($return_code == 0) { return 0; }

    if ($makemysql ne 'n'){
        # Erstellungsbefehl an die Datenbank absetzen
        $return_code = &DB_create_insert ($hostname, $mysqlpath, $mysqlname, $grantor, $grantor_pwd, $shopname, $zielname, $hostname, $install_path);
        if ($return_code == 0) { return 0; }
    }

    # nicht mehr benoetigte Dateien loeschen
    $return_code = &remove_unused_files ($install_path, $shopname, $zielname);
    if ($return_code == 0) { return 0; }

    # die Shopfiles dem richtigen User zuweisen
    $return_code = &set_uid_and_group ($file_owner, $file_group, $install_path);
    if ($return_code == 0) { return 0; }

    #Shopverzeichnis umbenennen, falls nicht der Name der Datenbank auch der Verzeichnisname ist
    $return_code = &rename_shopdir ($pfad_geteilt[1], $install_path, $shopname);
    if ($return_code == 0) { return 0; }

    # Handle für STDOUT und STDERR wieder schliessen
    close(STDOUT);
    close(STDERR);

    # urspruengliche Handles wieder herstellen
    open(STDOUT, ">&OLDOUT");
    open(STDERR, ">&OLDERR");

    return 1;

} # end of function installShop


#----------------------------------------
# Function: checkAlreadyInstalled
# Ueberprueft, ob der Shop an der angegebenen Stelle schon installiert ist, damit verhindert werden kann, dass neuer
# Shop ueber einen funktionierenden kopiert wird
# Argumente: $shopname -> Verzeichnisname, worin Shop installiert werden soll
# Rueckgabewert: true
#----------------------------------------
sub checkAlreadyInstalled {
    my ($new_name, $install_path, $shopname) = @_;
    # enthaelt die Filenames der Dateien, die auf Existenz geprueft werden sollen
    my $gesucht;
    my @files = ();
    push @files, "shop/Admin/ADMIN_SQL_BEFEHLE.php";
    push @files, "shop/USER_SQL_BEFEHLE.php";
    push @files, "shop/shopstyles.css";
    # enthaelt die Pfade der Dateien, die auf Existenz geprueft werden sollen
    my @dirs = ();
    push @dirs, "$install_path/$shopname";
    push @dirs, "$install_path/$new_name";
    foreach $dir_name (@dirs) {
        foreach $file_name (@files){
            $gesucht = $dir_name."/".$file_name;
            print "\npruefe ob $gesucht schon existiert ";

            if (-e $gesucht){
                print "--> problem (existiert schon)\n";
                return 1;
            } # end of if
            else{
                print "--> ok (existiert nicht)\n";
            } # end of else
        } # end of foreach
    } # end of foreach
    return 0;
} # end of function copyShopfiles


#----------------------------------------
# Function: copyShopfiles
# Kopiert den Template-Shop in das entsprechende Verzeichnis und entfernt Dateien, die nicht mehr
# gebraucht werden.
# Argumente: $shopname -> Verzeichnisname, worin Shop installiert werden soll
# Rueckgabewert: true
#----------------------------------------
sub copyShopfiles {
    my ($shopname, $install_path)= @_;
    print "                                      |----------------------------------------|\n";
    print "PhPepperShop File-Struktur erstellen  |Teil 2 - Verzeichnisse/Dateien erstellen|\n";
    print "====================================  |----------------------------------------|\n\n";

    print "Es wird nun vom Shoptemplate ausgehend ihr Shop ('$shopname') erzeugt.\n";
    print "Dazu wird ein Verzeichnis $shopname angelegt, worin sich danach der\n";
    print "neue Shop befindet\n\n";

    if (!mkdir("$install_path/$shopname/",0755)) {
        print "Fehler beim Erstellen des Verzeichnisses $install_path/$shopname/, \nFehlermeldung: $! --> Abbruch!\n";
        return 0;
    };
    print "-> Verzeichnis $install_path/$shopname/ erstellt\n";
    system "cp -R * $install_path/$shopname/";
    system "rm -rf $install_path/$shopname/template";
    system "rm $install_path/$shopname/config.pl";
    system "rm $install_path/$shopname/config.pm";
    # Bei der 'Hochsicherheits Variante' des PhPepperShops soll der .htaccess-Schutz von Hand erstel
    system "rm $install_path/$shopname/shop/Admin/SHOP_HTACCESS.php";

    print "-> Template-Shop kopiert, es folgt nun die Anpassung des Template-Shops\n";

    # Die Shopdaten werden in ein File geschrieben
    # Momentan noch unverschlüsselt -->  deshalb noch auskommentiert!
    # open (admindatei,">../$shopname/$shopname.admin");
    # print admindatei "\n-> Shopadministrationsdaten:\n";
    # print admindatei "     Shopname:                   $shopname\n";
    # print admindatei "     Anzahl DB-User:             $einzwei\n";
    # print admindatei "     DB-Hostname:                $hostname\n";
    # print admindatei "     Pfad zum MySQL Monitor:     $mysqlpath\n";
    # print admindatei "     DB-Admin Login:             $dbadmin\n";
    # print admindatei "     DB-Admin Pwd:               $dbadminpwd\n";
    # if ($einzwei == 2){
    #     print admindatei "     Shopuser:                   $dbuser\n";
    #     print admindatei "     Shopuser Pwd:               $dbuserpwd\n";
    # }
    # print admindatei "     User automatisch erstellen: $makeuser\n\n";
    # print admindatei "     Erstellungsdatum des Shops: ";
    # print admindatei system "date";
    # print admindatei "     Shops wurd erstellt von:    ";
    # print admindatei system "whoami";
    # close(admindatei);
    # system "chown root:root ../$shopname/$shopname.admin"; # Vor Zugriff gesichert Teil 1/2
    # system "chmod 600 ../$shopname/$shopname.admin";       # Vor Zugriff gesichert Teil 2/2
    # print "\nDie Shop-Administrationsdaten wurden in die Datei $shopname.admin gespeichert!\n";

    return 1;
} # end of function copyShopfiles


#----------------------------------------
# Function: createSQLFiles
# Ersetzt in den drei Template-Files: template_create.sql, template_del.sql und template_insert.sql
# die Template-Variablen {xyz} durch die erhaltenen Werte.
# Argumente:
# Rueckgabewert: true
#----------------------------------------
sub createSQLFiles {
    my ($shopname, $einzwei, $hostname, $dbadmin, $dbadminpwd, $dbuser, $dbuserpwd, $makeuser, $mysqlpath,
        $nomakeuserseq, $einusersequenz, $mysqlname, $rechnerip, $db_grantor, $install_path, $filename_del_tables_only, $filename_del)= @_;

    # Sonderzeichen in den Passwörtern quoten
    $dbadminpwd = quotemeta($dbadminpwd);
    $dbuserpwd = quotemeta($dbuserpwd);

    # Bildschirmanzeige des zweiten Teils
    print "                                    |--------------------------------------|\n";
    print "PhPepperShop Konfiguration          |Teil 3 - Konfigurationsfiles erstellen|\n";
    print "==========================          |--------------------------------------|\n\n";

    print "\n-> Es werden nun folgende Dateien im Verzeichnis ..$shopname/database/ erzeugt:\n";
    print "   Diese drei Dateien erzeugen die personalisierte Shop-Datenbank.\n";
    print "   $shopname"."_del.sql\n   $shopname"."_create.sql\n-| $shopname"."_insert.sql\n\n";
    print "-> Die Dateien\n   initialize.php        im Verzeichnis ..$shopname/shop/\n";
    print "   ADMIN_initialize.php  im Verzeichnis ..$shopname/shop/Admin/\n";
    print "-| erzeugen die Datenbank-Connection.\n\n";
    print "-> Die Datei\n   ADMIN_backup.php      im Verzeichnis ..$shopname/shop/Admin\n";
    print "-| erzeugt ein Datenbank-Backup.\n------------------------------------------------------------------\n";

    # database-Verzeichnis erstellen
    system "mkdir $install_path/$shopname/database";

    # del.sql Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/database/$filename_del")){
        print "Konnte die Datei $filename_del nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_del.sql")){
        print "Die Datei template_del.sql wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        if ($hostname =~ /^(localhost)/) {
            $zeile =~ s/{hostname}/$hostname/g;
        }
        else {
            $zeile =~ s/{hostname}/$rechnerip/g;
        }
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        print newfile $zeile;

    }
    close(myfile);
    close(newfile);
    print "\n-> $shopname"."_del.sql erzeugt!";


    # del_tables_only.sql Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/database/$filename_del_tables_only")){
        print "Konnte die Datei $filename_del_tables_only nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_del_tables_only.sql")){
        print "Die Datei template_del_tables_only.sql wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        if ($hostname =~ /^(localhost)/) {
            $zeile =~ s/{hostname}/$hostname/g;
        }
        else {
            $zeile =~ s/{hostname}/$rechnerip/g;
        }
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        print newfile $zeile;

    }
    close(myfile);
    close(newfile);
    print "\n-> $shopname"."_del_tables_only.sql erzeugt!";

    # create.sql Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/database/$filename_create")){
        print "Konnte die Datei $filename_create nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_create.sql")){
        print "Die Datei template_create.sql wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        #print "makuser: $makeuser, nomakuserseq: $nomakeuserseq"."\n";
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        if ($hostname =~ /^(localhost)/) {
            $zeile =~ s/{hostname}/$hostname/g;
        }
        else {
            $zeile =~ s/{hostname}/$rechnerip/g;
        }
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        print newfile $zeile;

    }
    close(myfile);
    close(newfile);
    print "\n-> $shopname"."_create.sql erzeugt!";

    # insert.sql Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/database/$filename_insert")){
        print "Konnte die Datei $filename_insert nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_insert.sql")){
        print "Die Datei template_insert.sql wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        $zeile =~ s/{hostname}/$hostname/g;
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        print newfile $zeile;

    }
    close(myfile);
    close(newfile);
    print "\n-> $shopname"."_insert.sql erzeugt!\n";

    return 1;
} # end of function createSQLFiles


#----------------------------------------
# Function: createPHP_PL_Files
# Erzeugt die drei Files: initialize.php, ADMIN_initialize.php, remove.pl und ADMIN_backup.php
# Argumente:
# Rueckgabewert: true
#----------------------------------------
sub createPHP_PL_Files {
    my ($shopname, $einzwei, $hostname, $dbadmin, $dbadminpwd, $dbuser, $dbuserpwd, $makeuser, $mysqlpath,
        $nomakeuserseq, $einusersequenz, $mysqlname, $db_grantor, $install_path, $filename_REMOVE, $filename_ADMIN, $filename_BACKUP, $dir_name)= @_;

    # Sonderzeichen ' in den Passwörtern quoten
    $dbadminpwd =~ s/'/\\'/g;
    $dbuserpwd =~ s/'/\\'/g;

    # initialize.php Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/shop/$filename_initialize")){
        print "Konnte die Datei $filename_initialize nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_initialize.php")){
        print "Die Datei template_initialize.php wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        # in der aktuellen Zeile ($zeile) suchenundersetzen / suchpattern / ersetzenpattern
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        $zeile =~ s/{hostname}/$hostname/g;
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        print newfile $zeile;

    }
    close(myfile);
    close(newfile);
    print "\n-> initialize.php erzeugt!";

    # ADMIN_initialize.php Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/shop/Admin/$filename_ADMIN")){
        print "Konnte die Datei $filename_ADMIN nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_ADMIN_initialize.php")){
        print "Die Datei template_ADMIN_initialize.php wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        $zeile =~ s/{hostname}/$hostname/g;
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        print newfile $zeile;

    }
    close(myfile);
    close(newfile);
    print "\n-> ADMIN_initialize.php erzeugt!\n";

    # remove.pl (Uninstall-)Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/$filename_REMOVE")){
        print "Konnte die Datei $filename_REMOVE nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_remove.pl")){
        print "Die Datei template_remove.pl wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        $zeile =~ s/{hostname}/$hostname/g;
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        $zeile =~ s/{dir_name}/$dir_name/g;
        print newfile $zeile;
    }
    close(myfile);
    close(newfile);
    print "\n-> remove.pl erzeugt!\n";

    # ADMIN_backup.php Backup-Skript erzeugen:
    if (!open(newfile, ">$install_path/$shopname/shop/Admin/$filename_BACKUP")){
        print "Konnte die Datei $filename_BACKUP nicht anlegen! -> Abbruch\n";
        return 0;
    } # end of if
    if (!open(myfile, "<./template/template_backup.php")){
        print "Die Datei template_backup.php wurde nicht gefunden! -> Abbruch\n";
        return 0;
    } # end of if
    while(<myfile>){
        $zeile = $_;
        if (($makeuser =~ /n/)&&($zeile =~ m/{nomakeuser}/)){
            if($nomakeuserseq == 0){$nomakeuserseq = 1;}
            else{$nomakeuserseq = 0; $zeile = "";}
        }
        if (($einzwei==1)&&($zeile =~ /{einuserseq}/)){
            if($einusersequenz == 0){$einusersequenz = 1;}
            else{$einusersequenz = 0; $zeile = "";}
        }
        if ($nomakeuserseq == 1){
            $zeile = "";
        }
        if ($einusersequenz == 1){
            $zeile = "";
        }
        $zeile =~ s/{mysql_db}/$mysqlname/g;
        $zeile =~ s/{shop_db}/$shopname/g;
        $zeile =~ s/{hostname}/$hostname/g;
        $zeile =~ s/{mysqlpath}/$mysqlpath/g;
        $zeile =~ s/{shopadmin}/$dbadmin/g;
        $zeile =~ s/{shopadminpwd}/$dbadminpwd/g;
        $zeile =~ s/{shopuser}/$dbuser/g;
        $zeile =~ s/{shopuserpwd}/$dbuserpwd/g;
        $zeile =~ s/{grantor}/$db_grantor/g;
        print newfile $zeile;

    }
    close(myfile);
    close(newfile);
    print "\n-> ADMIN_backup.php erzeugt!\n";
    return 1;
} # end of function createPHP_PL_Files


#----------------------------------------
# Function: createSQL_insert
# Die beiden SQL-Scripts $shopname_create.sql und $shopname_insert.sql werden
# nacheinander in ein neues Script $shopname_mysql.sql geschrieben (merge).
# Argumente:
# Rueckgabewert: true
#----------------------------------------
sub createSQL_insert {
    my ($shopname, $filename_create, $filename_insert, $zielname, $install_path) = @_;

    if (!open(create,"<$install_path/$shopname/database/$filename_create")){
        print "konnte db nicht mergen:$filename_create open -> Abbruch!\n";
        return 0;
    } # end of if
    if (!open(ziel,">$install_path/$shopname/database/$zielname")){
        print "konnte db nicht mergen:$zielname create -> Abbruch!\n";
        return 0;
    } # end of if
    while(<create>){
        print ziel $_;
    } # end of while
    close(create);
    close(ziel);

    if (!open(insert,"<$install_path/$shopname/database/$filename_insert")){
        print "konnte db nicht mergen:$filename_insert open -> Abbruch!\n";
        return 0;
    } # end of if
    if (!open(ziel,">>$install_path/$shopname/database/$zielname")){
        print "konnte db nicht mergen:$zielname append -> Abbruch!";
        return 0;
    } # end of if
    while(<insert>){
        print ziel $_;
    }
    close(insert);
    close(ziel);
} # end of function createSQL_insert


#----------------------------------------
# Function: DB_create_insert
# Erstellt (wenn notwendig) die Datenbank und fügt die Daten ein
# Argumente:
# Rueckgabewert: true
#----------------------------------------
sub DB_create_insert {
    my ($hostname, $mysqlpath, $mysqlname, $grantor, $grantor_pwd, $shopname, $zielname, $hostname, $install_path) = @_;
    if ($hostname =~ /^(localhost)/) {
        system "$mysqlpath"."/"."$mysqlname -u $grantor --password=".quotemeta($grantor_pwd)." --verbose --pager=cat > $install_path/$shopname/success_test < $install_path/$shopname/database/$zielname";
        if ($? != 0) { print "\nkonnten Datenbank nicht ins DBMS einfuegen!"; return 0; }
    }
    else {
        system "$mysqlpath"."/"."$mysqlname -u $grantor --password=".quotemeta($grantor_pwd)." --host=$hostname --verbose --pager=cat > $install_path/$shopname/success_test < $install_path/$shopname/database/$zielname";
        if ($? != 0) { print "\nkonnten Datenbank nicht ins DBMS einfuegen!"; return 0; }
    }
    return 1;
} # end of function DB_create_insert


#----------------------------------------
# Function: remove_unused_files
# Löscht nicht mehr benötigte Dateien
# Argumente: keine
# Rueckgabewert: true
#----------------------------------------
sub remove_unused_files {
    my ($install_path, $shopname, $zielname) = @_;
    # Die ueberfluessige Datei success_test loeschen
    system "rm $install_path/$shopname/success_test";
    system "rm $install_path/$shopname/database/$zielname";
    system "rm -f $install_path/$shopname/*.log";
    return 1;
} # end of function remove_unused_files


#----------------------------------------
# Function: set_file_perm
# Setzt die Zugriffsrechte für die Shopdateien
# Argumente: keine
# Rueckgabewert: true
#----------------------------------------
sub set_file_perm {
    my ($shopname, $install_path) = @_;
    chdir "$install_path/$shopname";
    print "Alle Dateien ausfuehrbar machen (chmod 755):";
    system "chmod -R 755 *";
    if ($? != 0) { $return_value = 0}
    print "-> ausgeführt";
    print "\n";
    print "Setze spezielle Datei-Attribute:";
    print "\n    ...'Shoproot'-Verzeichnis...";
    system "chmod 750 $install_path/$shopname/remove.pl";
    if ($? != 0) { return 0; }
    system "chmod 666 $install_path/$shopname/index.php";
    if ($? != 0) { return 0; }
    print "\n    ...database-Verzeichnis...";
    system "chmod 700 $install_path/$shopname/database/*";
    if ($? != 0) { return 0; }
    system "chmod 700 $install_path/$shopname/database";
    if ($? != 0) { return 0; }
    print "\n    ...shop-Verzeichnis...";
    system "chmod 666 $install_path/$shopname/shop/shopstyles.css";
    if ($? != 0) { return 0; }
    print "\n    ...Frameset-Verzeichnis...";
    system "chmod 666 $install_path/$shopname/shop/Frameset/shopstyles.css";
    if ($? != 0) { return 0; }
    print "\n    ...Admin-Verzeichnis...";
    print "\n    ...Bilder-Verzeichnis...";
    system "chmod 777 $install_path/$shopname/shop/Bilder/*";
    if ($? != 0) { return 0; }
    print "\n    ...Buttons-Verzeichnis...";
    system "chmod 777 $install_path/$shopname/shop/Buttons/*";
    if ($? != 0) { return 0; }
    print "\n    ...Backups-Verzeichnis...";
    system "chmod 777 $install_path/$shopname/shop/Admin/Backups";
    if ($? != 0) { return 0; }
    print "\n    ...Produktebilder-Verzeichnis...";
    system "chmod 777 $install_path/$shopname/shop/ProdukteBilder";
    if ($? != 0) { return 0; }
    print "\n    ...Beispiel-Produktebilder...";
    system "chmod 666 $install_path/$shopname/shop/ProdukteBilder/*.jpg";
    if ($? != 0) { return 0; }
    print "-> ausgeführt";
    # Wenn ein Import-Verzeichnis existiert, die Rechte auf 777 setzen
    if (-e "$install_path/$shopname/shop/Admin/Import") {
        print "\n    ...Import-Verzeichnis...";
        system "chmod 777 $install_path/$shopname/shop/Admin/Import";
        if ($? != 0) { return 0; }
        # Dateien im Importverzeichnis 666
        system "chmod 666 $install_path/$shopname/shop/Admin/Import/*.txt";
        if ($? != 0) { return 0; }
    print "-> ausgeführt";
    } # end of if
    print "\n";
    return 1;
} # end of function set_file_perm


#----------------------------------------
# Function: remove_cvs_dirs
# Entfernt die CVS-Verzeichnisse, da sie für den Betrieb des Shops nicht gebraucht werden
# Argumente: keine
# Rueckgabewert: true
#----------------------------------------
sub remove_cvs_dirs {
    my ($shopname, $install_path) = @_;
    print "CVS-Verzeichnisse entfernen:";
    system "rm -rf $install_path/$shopname/CVS";
    if ($? != 0) { return 0; }
    system "rm -rf $install_path/$shopname/shop/CVS";
    if ($? != 0) { return 0; }
    system "rm -rf $install_path/$shopname/shop/Admin/CVS";
    if ($? != 0) { return 0; }
    system "rm -rf $install_path/$shopname/shop/Admin/Backups/CVS";
    if ($? != 0) { return 0; }
    system "rm -rf $install_path/$shopname/shop/Bilder/CVS";
    if ($? != 0) { return 0; }
    system "rm -rf $install_path/$shopname/shop/Buttons/CVS";
    if ($? != 0) { return 0; }
    system "rm -rf $install_path/$shopname/shop/Frameset/CVS";
    if ($? != 0) { return 0; }
    system "rm -rf $install_path/$shopname/shop/ProdukteBilder/CVS";
    if ($? != 0) { return 0; }
    if (-e "$install_path/$shopname/shop/Admin/Import") {
        system "rm -rf $install_path/$shopname/shop/Admin/Import/CVS";
        if ($? != 0) { return 0; }
    } # end of if
    print "-> ausgeführt";
    print "\n";
    return 1;
} # end of function remove_cvs_dirs


#----------------------------------------
# Function: set_uid_and_group
# Weist die Shopfiles dem richtigen User und dessen Gruppe zu
# Argumente: keine
# Rueckgabewert: true
#----------------------------------------
sub set_uid_and_group {
    my ($file_owner, $file_group, $path) = @_;
    my $I_Am = `whoami`;
    chomp ($I_Am);
    # Dateirechte nur ändern, wenn das Script nicht von dem User ausgefuehrt wird, auf den die Rechte gesetzt werden sollen
    # Im Normalfall wird das Script vom Root ausgefuehrt. Ist dies nicht der Fall, wird der User auch keine chown-Rechte haben
    if ($I_Am ne $file_owner){
        system "chown -R $file_owner.$file_group $path";
        if ($? != 0) { return 0; }
    } # end of if
    return 1;
} # end of function set_uid_and_group

#----------------------------------------
# Function: rename_shopdir
# Benennt das Shopverzeichnis um
# Argumente: neuer Verzeichnisname, Pfad zum Shop
# Rueckgabewert: true
#----------------------------------------
sub rename_shopdir {
    my ($new_name, $install_path, $shopname) = @_;
    # Shopverzeichnis nur umbenennen, wenn es verschieden vom Shopnamen ist
    if ($shopname ne $new_name){
        system "mv $install_path/$shopname $install_path/$new_name";
    }
    if ($? != 0) { return 0; }
    return 1;
} # end of function rename_shopdir

#----------------------------------------
# Function: pathWithoutFile
# Entferne abschliesenden Dateinamen aus dem Pfad.
# Z.B. pathWithoutFile("/usr/bin/local/bin/mysql", "mysql") -> "/usr/bin/local/bin/"
# Argumente: $in, $filename
# Rueckgabewert: $res
#----------------------------------------
sub pathWithoutFile {
    my ($in, $filename)= @_;
    my $res= $in;
    $res=~ s/\/($filename)$//;  # Filenamen am Ende entfernen
    return $res;
} # end of function pathWithoutFile


#----------------------------------------
# Function: pathWithoutlastDir
# Entfernt letzte Verzeichnisstufe aus einer Pfadangabe
# abschliessende '/' werden auch entfernt
# Z.B. pathWithoutlastDir("/home/websites/mypage/shop") -> "/home/websites/mypage"
# Argumente: $path
# Rueckgabewert: $res
#----------------------------------------
sub pathWithoutlastDir {
    my ($path)= @_;
    chomp ($path);
    $path =~ s/\/[^\/]*(\/*)$//;
    return $path;
} # end of function pathWithoutlastDir

#----------------------------------------
# Function: split_path
# schneidet von einem uebergebenen Pfad die letzte Verzeichnisstufe ab und gibt beide Teile zurueck
# abschliessende '/' werden auch entfernt
# Z.B. split_path("/home/websites/mypage/shop") -> "/home/websites/mypage" und "shop"
# Argumente: $path
# Rueckgabewert: $res
#----------------------------------------
sub split_path {
    my ($path)= @_;
    my @return_arr;
    chomp ($path);
    my $pre = $path;
    my $post = $path;
    $pre =~ s/\/[^\/]*(\/*)$//;
    $post =~ s/^.*\/([^\/]*)(\/*)$/$1/;
    $return_arr[0] = $pre;
    $return_arr[1] = $post;
    return @return_arr;
} # end of function pathWithoutlastDir


