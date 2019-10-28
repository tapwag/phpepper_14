#!/usr/bin/perl
# File:      remove.pl
# Author:    José Fontanil, Reto Glanzmann
# Project:   PhPepperShop
# Version:   1.4
# CVS-Version / Date: $Id: template_remove.pl,v 1.31 2003/05/24 18:41:47 fontajos Exp $
# Security:  *** ADMIN ***
# What it does: This script removes an installed shop(!)
#---------------------------------------------


# Variable um das DB-Loesch-Skript zu beschreiben
$zielname = "{shop_db}"."_del.sql";
$zielname2 = "{shop_db}"."_del_tables_only.sql";
$hostname = "{hostname}";
$mysqlpath = "{mysqlpath}";
$einloggen_flag ="n";
$abort = 0;
$db_error = 0;

#----------------------------------------
# Bildschirmdarstellung, Entscheidung
#----------------------------------------
system "clear";
print "                                                          |---------------|\n";
print "PhPepperShop Uninstall       (Abbruch mit CTRL+C)         |!!ENTSCHEIDEN!!|\n";
print "======================                                    |---------------|\n\n";


print "\nWollen sie den Shop:  >>> {shop_db} <<<\n";
print "wirklich deinstallieren? Diese Operation ist unwiederrufbar!\n";
print "Alle Shop-Dateien sind danach verloren, auch die {shop_db} Datenbank\nund ihre User werden optional geloescht!";
if (open(FILEHANDLE, './shop/Admin/Backups/0.sql') || (open(FILEHANDLE, './shop/Admin/Backups/0.sql.gz'))) {
    print "\n\n *** Es befinden sich auch noch Backups des Shops im Verzeichnis './shop/Admin/Backups/'! *** \n";
    close(FILEHANDLE);
}
print "\nja, nein (j|n): ";
$falsch = 1;
while ($falsch) {
    $loeschen = <STDIN>;
    $falsch = 0;
    if ($loeschen !~ /^([jn]$){1,1}/){
        print "Sie koennen nur j für ja und n für nein waehlen!\n";
        $falsch = 1;
    }
}
chomp($loeschen); #Ueberfluessiges \n entfernen

if($loeschen =~ /n/){
    die "\n\n\n\n\n\nSie haben sich entschieden den Shop {shop_db} NICHT zu deinstallieren, -> Programmende\n\n";
}
else {
    system "clear";
    print "                                                          |--------------|\n";
    print "PhPepperShop Uninstall       (Abbruch mit CTRL+C)         | !!LOESCHEN!! |\n";
    print "======================                                    |--------------|\n\n";

    print "Sie haben sich entschieden den Shop {shop_db} zu deinstallieren:\n\n";
    print "Sollen auch die {shop_db}-User und -Datenbank deinstalliert werden?\n";
    print "ja, nein (j|n): ";
    $falsch = 1;
    while ($falsch) {
        $deluserdb = <STDIN>;
        $falsch = 0;
        if ($deluserdb !~ /^([jn]$){1,1}/){
            print "Sie koennen nur j für ja und n für nein waehlen!\n";
            $falsch = 1;
        }
    }
    chomp($deluserdb); # Ueberfluessiges \n entfernen

    print "\nSollen nur die Tabellen der Shop-Datenbank gelöscht werden?\n";
    print "Antworten sie hier mit j, wenn die Datenbank mit dem Namen {shop_db} auch noch\n";
    print "von anderen Anwendungen verwendet wird, oder sie nicht gelöscht werden darf,\n";
    print "sonst wählen Sie n. Bei n wird die gesamte Datenbank {shop_db} gelöscht!\n";
    print "ja, nein (j|n): ";
    $falsch = 1;
    while ($falsch) {
        $deltablesonly = <STDIN>;
        $falsch = 0;
        if ($deltablesonly !~ /^([jn]$){1,1}/){
            print "Sie koennen nur j für ja und n für nein waehlen!\n";
            $falsch = 1;
        }
    }
    chomp($deltablesonly); # Ueberfluessiges \n entfernen

    if ($deluserdb =~ /j/) {

      do {
        # Eingabe des Datenbank Users unter dessen Namen das SQL-Skript ausgeführt wird
        print "\nWelcher MySQL-User soll die {shop_db} Datenbank resp. ihre Tabellen";
        print "\naus dem MySQL-DBMS (Host: $hostname) entfernen?";
        print "\n(z.B. root): ";
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
        # User und Shopdatenbank loeschen (zuerst die Testflags neu initialisieren)
        $abort = 0;
        $db_error = 0;
        if ($deltablesonly =~ /j/) {
            # Nur die Shop-Tabellen (und gegebenenfalls die Shop-User) loeschen
            if ($hostname =~ /^(localhost)/) {
                system "$mysqlpath"."/"."mysql -u $grantor --password=".quotemeta($grantor_pwd)." --verbose --pager=cat > success_test {shop_db} < ./database/$zielname2";
            }
            else {
                system "$mysqlpath"."/"."mysql -u $grantor --password=".quotemeta($grantor_pwd)." --host=$hostname --verbose --pager=cat > success_test {shop_db} < ./database/$zielname2";
            }
        }
        else {
            # komplette Shop-Datenbank (und gegebenenfalls die Shop-User) entfernen
            if ($hostname =~ /^(localhost)/) {
                system "$mysqlpath"."/"."mysql -u $grantor --password=".quotemeta($grantor_pwd)." --verbose --pager=cat > success_test < ./database/$zielname";
            }
            else {
                system "$mysqlpath"."/"."mysql -u $grantor --password=".quotemeta($grantor_pwd)." --host=$hostname --verbose --pager=cat > success_test < ./database/$zielname";
            }
        }

        # Testen ob das SQL ausgefuehrt wurde: In der obigen Anweisung wird mit der Pager-
        # Anweisung eine Datei erzeugt (success_test), darin steht entweder nichts -->
        # Fehler oder es steht das ausgefuehrte SQL-Skript darin --> Erfolg
        open(s_t, "<./success_test") || die "Konnte die Datei ./success_test nicht oeffnen! -> Abbruch\n";
        # getc(s_t) liest das erste Zeichen der Zeile eins ein (bei den sql-scripts ist die erste Zeile immer in -)
        $_ = getc(s_t);
        if ($_ eq "") {
            $db_error = 1;
            print "(Bei Error 1045:) Entweder existiert kein Datenbank-User namens $grantor, oder ihr Passwort war falsch ($grantor_pwd)!";
            print "\n\nWollen sie nochmals versuchen einzuloggen? (j|n): ";
            $falsch = 1;
            while ($falsch) {
                $einloggen_flag = <STDIN>;
                $falsch = 0;
                if ($einloggen_flag !~ /^([jn]$){1,1}/){
                    print "Sie koennen nur j für ja und n für nein waehlen! (n = Abbruch der Deinstallation)\n";
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
        # Die ueberfluessige Datei success_test loeschen
        system "rm success_test";
      } while ($abort == 1);
    }
    # Shopverzeichnis loeschen, aber nur wenn kein Datenbank Fehler verursacht wurde!
    if ($db_error == 0) {
        system "rm -rf ../{dir_name}";
        print "\n\nWenn keine Datenbank-Fehler angezeigt werden,\n";
        print "ist der Shop erfolgreich deinstalliert worden!\n";
        if ($deltablesonly =~ /j/) {
            print "Es wurden nur die Shop-Tabellen aus der {shop_db} Datenbank entfernt!\n\n";
        }
    }
    else {
        print "\n\nDer Shop <{shop_db}> wurde NICHT deinstalliert!\n";
    }
}
print "\n\n---Ende des Skripts (remove.pl)---\n\n";
