"PhPepperShop" Shopsystem
=========================

Note in English: There is no English version of the PhPepperShop available yet.


1.) Systemanforderungen
-----------------------
- UNIX System (z.B. Linux), der Shop wurde auch schon unter Windows installiert, siehe unser
  Forum auf der PhPepperShop Homepage. Der Windows-Betrieb wird erst ab der PhPepperShop Version 2.0 empfohlen.
- PHP v.4.03 oder höher (Entwickelt wurde mit PHP v.4.04pl1/4.06/4.11/4.22/4.30RC2/4.31)
- GD-Library mit JPEG und/oder PNG und/oder GIF Unterstützung (für Artikelbilder)
- MySQL ab Version v.3.23, v.3.22.xx macht Probleme (nur bei der Installation), siehe Forum.
- Um das Telnet/SSH basierte Installationsscript zu benutzen: Telnet oder SSH-Zugang zum Webserver
  Ansonsten muss mittels FTP-Upload und der Anleitung 'Installation ohne Telnet/SSH' installiert werden.
  (Diese Anleitung findet man unter http://www.phpeppershop.com/ in der Rubrik 'Anleitungen')
- Die PHP-Direktiven magic_quotes_gpc muss auf On gesetzt sein, magic_quotes_runtime auf Off (und 
  session_autostart sollte = Off sein).


2.) Infos zum Installationsvorgang
----------------------------------

Es gibt zwei Möglichkeiten den PhPepperShop zu installieren. Eine komfortable Variante, welche aber nur über
eine Telnet- bzw. eine SSH-Verbindung zum Webserver (oder dirket über seine Shell) funktioniert und eine eher
mühsame Variante, welche via FTP-Upload und mit der Anleitung 'Installation ohne Telnet/SSH' funktioniert.


2.1.) Installation des Shops via Telnet/SSH
-------------------------------------------

Die Installation des Webshops geschieht über das PERL-Script config.pl (nicht .pm!), welches im entpackten
Verzeichnis phpeppershop_src liegt. Um dieses Installationsscript benutzen zu können, benötigt
man einen Telnet oder einen SSH-Zugang zum Webserver. Wer keinen solchen Zugang sein Eigen nennt,
kann weiter unten unter 'Installation ohne Telnet/SSH-Zugang' eine weitere Installationsmethode
finden. Man startet die Telnet/SSH-basierte Installation wie folgt:

perl ./config.pl  (man muss sich natuerlich auch im Verzeichnis phpeppershop_src befinden)

Wir empfehlen das Herunterladen und lesen de PhPepperShop Dokumentation Manuals von der PhPepperShop Homepage
in der Rubrik Anleitungen!


2.2.) Installation des Shops ohne Telnet/SSH-Zugang
---------------------------------------------------

Weil viele Leute uns gebeten haben, den Shop auch für Benutzer ohne Telnet/SSH-Konsole zugänglich
zu machen, haben wir eine Installationsanleitung geschrieben, wie sich der Shop auch ohne Konsole
installieren lässt.
Anzumerken ist hier, dass es sich um keine perfekte Lösung handelt. Die Installation ist vor allem
auf Leute mit kleinem Budget und kleinem Abo ausgerichtet (eine fix benamste Datenbank, ein schon
existierender DB-User).

Man muss sich auf der Homepage http://www.phpeppershop.com in der Rubrik Anleitungen die entsprechende
Installationsanleitung: 'Installlation ohne Telnet/SSH' herunterladen und durcharbeiten. Für den Betrieb
des PhPepperShops ist weiter es von Vorteil das PhPepperShop Dokumentations Manual herunterzuladen.


3.) Für weitere Fragen
----------------------

Weitere Informationen findet man auf unserer Homepage: http://www.phpeppershop.com, .org, .de, .ch
in den Rubriken:
    - Anleitungen
    - Downloads
    - FAQs
    - Forum
    - Live-Shops 

Bei Unklarheiten bitte folgende Reihenfolge einhalten:
    1.) Überprüfen, ob alle verfügbaren Bugfixes installiert wurden (siehe Download-Bereich)
    2.) FAQs lesen
    3.) Foren durchsuchen (gute Suchfunktion integriert)
    4.) (Erst dann) Entwickler kontaktieren

Wir bitten Sie auch zu verstehen, dass wir fuer Nichtkunden nicht unendlich viel Supportenergie
aufwenden koennen. Wichtigster Ansatz ist sicherlich zuerst im sehr umfangreichen Forum nach einer
Loesung des Problems zu suchen (Die oeffentlichen Foren beinhalten ueber 9000 Eintraege und in den
Kundenforen sind nochmals ueber 10000 qualitativ hochwertige Postes zu finden. Stand Maerz 2006).


Viel Erfolg und Spass mit PhPepperShop  :-)

====================================================================================================
Dokumenten ID: $Id: README,v 1.16 2003/07/26 01:48:01 fontajos Exp $
PhPepperShop Version = v.1.4
