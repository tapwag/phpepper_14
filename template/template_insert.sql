# /*
# * {shop_db}_insert.sql
# *
# * ZHW Zuercher Hochschule Winterthur
# * PhPepperShop Diplomarbeit DA Fei01/1
# * Autoren: Jose Fontanil & Reto Glanzmann
# *
# * Version 1.4, basierend auf ER-Diagramm v.1.2.6
# *
# * CVS-Version / Datum: $Id: template_insert.sql,v 1.147 2003/08/18 10:06:50 fontajos Exp $
# *
# * Gibt einen Satz von Daten zu Testzwecken ein (spart v.a. Zeit)
# * Der Shop hat aber noch keine Bilder zu den Artikel drin
# *
# * einlesen z.B. mit mysql -u {grantor} -p {shop_db} < ./{shop_db}_insert.sql
# * Datenbank Hostrechner: {hostname}
# *
# * Sicherheitsstatus:      *** ADMIN ***
# *
# * ------------------------------------------------------------------------------
# */

# /*
# * ------------------------------------------------------------------------------
# *
# * Nun werden gewisse Initialwerte in verschiedene Tabellen eingefuegt:
# */


# /*
# * ------------------------------------------------------------------------------
# *
# * 6 Kategorien und 7 Unterkategorien:
# */
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (20,2,'Spezial-Pizza','',0,NULL,NULL,NULL,20020930163507,'Pizzeria','N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (21,3,'Pasta','',0,NULL,NULL,NULL,20020930163507,'Pizzeria','N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (22,4,'Salate','',0,NULL,NULL,NULL,20020930163507,'Pizzeria','N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (23,5,'Getränke','',0,NULL,NULL,NULL,20020930163507,'Pizzeria','N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (17,2,'bis 6 Pers.','',0,NULL,NULL,NULL,20020930201257,'Brettspiele','N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (18,3,'Pizzeria','',0,NULL,NULL,NULL,20020930163507,NULL,'N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (19,1,'Pizza','',0,NULL,NULL,NULL,20020930163507,'Pizzeria','N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (16,1,'für 2 Pers.','',0,NULL,NULL,NULL,20020930201238,'Brettspiele','N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (15,4,'Brettspiele','',0,NULL,NULL,NULL,20020930160021,NULL,'N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (14,1,'Pfeffer','',0,NULL,NULL,NULL,20020930144959,NULL,'N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (13,2,'DVD','',0,NULL,NULL,NULL,20020930144959,NULL,'N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (25,5,'Stempel','',0,NULL,NULL,NULL,20020930165028,NULL,'N');
INSERT INTO kategorien (Kategorie_ID, Positions_Nr, Name, Beschreibung, MwSt_Satz, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Unterkategorie_von, Details_anzeigen)
VALUES (12,6,'Nichtzugeordnet','Nicht zugeordnete Artikel',0,NULL,NULL,NULL,20020930164951,'@PhPepperShop@','N');


# /*
# * ------------------------------------------------------------------------------
# *
# * 70 verschiedene Artikel
# */
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (29,'dvd003','Epoch','Im Himalaja werden erhöhte seismographische Aktivitäten gemessen, die eine weltweite Panik verursachen. Der amerikanische Präsident schickt den Waffenspezialisten Mason und die Geologin Kasia in das Krisengebiet. Sie entdecken eine riesige, mehr als 4 Millionen Jahre alte Formation, von der eine stärkere Energie als von der Sonne ausgeht. Als die chinesische Regierung versucht den fremdartigen Koloss mittels Atomwaffen zu zerstören, beginnt ein Rennen gegen die Zeit. Der Monolith beginnt dunkle Asche auszuwerfen, die innerhalb von 48 Stunden den gesamten Erdhimmel verdunkeln und alles Leben auf dem Planeten vernichten wird...',39.9,0,'N',0,'',NULL,NULL,NULL,20020930092507,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (32,'dvd006','Ghost Dog: Der Weg des Samurai','Ghost Dog, so genannt, weil er sich unbemerkt durch die Straßen der Stadt bewegen kann und im Dunkel der Nacht verschwindet, ist ein Profikiller, der nach dem Kodex eines Samurai lebt. Seine einzigen Freunde sind die Tauben auf dem Dach des verlassenen Gebäudes, in dem er wohnt, und der afrikanische Eisverkäufer Raymond, der nur französisch spricht. Nach der Western-Paraphrase \'Dead Man\' legt Kultregisseur Jim Jarmusch (\'Night on Earth\') nun eine ironisch melancholische Gangster-Elegie vor, die sich an Melvilles \'Der eiskalte Engel\' ebenso orientiert wie an zeitgenössischem Gangsta-Rap (der Soundtrack stammt von RZA). Die Rolle des schweigsamen Killers füllt \'Bird\' Forest Whitaker perfekt aus.',39.9,0,'N',0,'',NULL,NULL,NULL,20020930091827,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (28,'dvd002','Auf der Jagd (Special Edition)','Dem Spezialagenten Mark Roberts wird ein kaltblütiger Mord untergeschoben. Während eines Gefangenentransports gelingt ihm die Flucht, doch jetzt wird er gnadenlos gejagt: Von US-Marshal Sam Gerard, dem härtesten Fahnder der amerikanischen Bundespolizei. Während Roberts immer weiter in die Enge getrieben wird, versucht er verzweifelt, die wahren Drahtzieher der Verschwörung zu stellen..',24.9,0,'N',0,'',NULL,NULL,NULL,20020930090410,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (27,'dvd001','A.I. Artificial Intelligence','In einer nicht allzu fernen Zukunft entsteht in einem Forschungslabor der erste intelligente Roboter mit menschlichen Gefühlen in der Gestalt des elfjährigen David. Aber seine \"Adoptiveltern\" sind mit dem künstlichen Ersatzkind überfordert und setzen ihn aus. Auf sich allein gestellt versucht David, seine Herkunft und das Geheimnis seiner Existenz zu ergründen. Damit beginnt eine unglaubliche Odyssee voller Abenteuer, Gefahren und geheimnisvoller Begegnungen...',36.9,0,'N',0,'',NULL,NULL,NULL,20020930090109,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (30,'dvd004','Final Fantasy (2 DVDs)','Das Jahr 2065: Die Erde ist verwüstet und wird von Ausserirdischen kontrolliert. Sie ernähren sich von den spirituellen Energien getöteter Menschen. Während ein verblendeter General diese Wesen mit einer auch die Erde gefährdenden Superwaffe vernichten will, suchen Ärztin Aki und ihr Mentor Dr. Sid fieberhaft nach einer spirituellen Lösung.',55.9,0,'N',0,'',NULL,NULL,NULL,20020930090807,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (31,'dvd005','Galaxy Quest - Planlos durchs Weltall','Vier Jahre lang reiste die heldenhafte Crew des Raumschiffs NSEA Protector - bestehend aus \"Commander Peter Quincy Taggart\" (Tim Allen), \"lt. Tawny Madison\" (Sigourney Weaver) und \"dr. Lazarus\" (Alan Rickman) - in spannender und häufig lebensgefährlicher Mission durch den Weltraum... bis ihre Serie abgesetzt wurde!\r\nZwanzig Jahre später halten bedrohte Aliens die \"Galaxy Quest\"- Wiederholungen für \"Historische Ddokumente\" und \'beamen\' die abgehalfterten Mimen als vermeintliche Retter des Universums zu sich herauf. ohne Drehbuch, Regisseur oder Plan müssen die Schauspieler in diesem urkomischen Abenteuer - von Jeffrey Lions (nbc-tv) als \"witzigste Komödie des Jahres\" bezeichnet - die Rolle ihres Lebens spielen.',18.9,0,'N',0,'',NULL,NULL,NULL,20020930092536,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (33,'dvd007','Heartbreakers','Schön, sexy, charmant - Max ist eine Frau,, die jeden Mann um den Verstand bringt. sie heiratet regelmässig stinkreiche Herren, die sich sofort danach unfreiwillig in sehr peinlichen Situationen wiederfinden. Daraufhin reicht Max die Scheidung ein und macht sich mit einer fettern Abfindung aus dem Staub. Ihre nicht weniger sexy Komplizin heisst Page und ist ihre Tochter. Schon 14-mal hat die eiskalte Tour geklappt. der Lohn für die lästige Heiraterei ist ein Leben in Luxus. Gefährlich wird\'s allerdings, wenn die Steuerbehörde plötzlich in der Tür steht. Und noch gefährlicher wird\'s, wenn geschieht, was auf keinen Fall geschehen darf: eine der Damen verliebt sich ernsthaft in jemanden.',39.9,0,'N',0,'',NULL,NULL,NULL,20020930092344,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (34,'dvd008','Der Herr der Ringe - Die Gefährten','Von Zauberer Gandalf erfährt Frodo, daß es bei dem Ring, den ihn sein Onkel Bilbo geschenkt hat, um den mächtigen Ring des bösen Sauron handelt. Um dessen Rückkehr zur Macht zu verhindern, muss der Ring in Saurons Reich vernichtet werden. Frodo macht sich auf den Weg, doch die Feinde sind nicht weit.',59.9,0,'N',0,'',NULL,NULL,NULL,20020930092701,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (35,'dvd009','Im Auftrag des Teufels','Der junge Staatsanwalt Kevin Lomax bekommt die Chance seines Lebens: die grösste und mächtigste Kanzlei des Landes holt ihn nach New York. und damit nicht genug: sein neuer Boss, der geheimnisvolle John Milton, nimmt ihn persönlich unter seine Fittiche. Unaufhaltsam verfällt Kevin dem Rausch von Macht, Ruhm, Sex und dem ganz grossen Geld. Doch nach einer Serie unheimlicher Vorfälle wird Kevin klar, dass ihn der Leibhaftige persönlich unter Vertrag genommen hat... ein Horrorthriller der Spitzenklasse, mit Superstar Keanu Reeves (\"Matrix\") und dem teuflisch genialen Al Pacino (\"Heat\") in den Hauptrollen. ein wahrhaft diabolisches Meisterwerk.',18.9,0,'N',0,'',NULL,NULL,NULL,20020930093149,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (36,'dvd010','Lethal Weapon 4 - Zwei Profis räumen auf','Hinter einem Menschenhändlerring stossen Riggs und Murtaugh auf ein mörderisches Komplott der chinesischen Triaden-Mafia und geraten dabei selber auf die Abschußliste. Doch zusammen sind sie nicht zu schlagen - auch wenn sie bei ihrem haarsträubenden Shoot-Outs und Verfolgungen eine Schneise der Verwüstung quer durch ganz Los Angeles ziehen.',24.9,0,'N',0,'',NULL,NULL,NULL,20020930093502,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (37,'dvd011','Mission to Mars','Das grösste Abenteuer der Menschheit!\r\nEpochale Science-Fiction-Odyssee zum Tor einer neuen Welt!\r\nTrotz minutiöser Vorbereitung und schärfster Sicherheitsvorkehrungen mündet die erste Mars-Mission in eine Katastrophe. Nun soll ein Rettungsteam nach möglichen Überlebenden und den genauen Ursachen des Desasters suchen. Mit an Bord sind die NASA-Astronauten Woody Blake (Tim Robbins, \"The Shawshank Redemption\") und Jim Mcconnell (Gary Sinise, \"Forrest Gump\"). Die Odyssee schuf ein atemberaubendes Science-Fiction-Abenteuer ganz im Stil von Stanley Kubricks bahnbrechendem Kinoklassiker \"2001 - a space odyssey\". Ein unvergesslicher Trip in die unendlichen Weiten des Alls!',34.9,0,'N',0,'',NULL,NULL,NULL,20020930093927,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (38,'dvd033','Planet der Affen (2001)','Kurz nachdem der Astronaut Captain Leo Davidson (Mark Wahlberg) durch einen Sprung im Raum-Zeit-Kontinuum auf einem mysteriösen Planeten gelandet ist, wird er zusammen mit einigen anderen Menschen von einer Gruppe Affen gejagt und schliesslich als Gefangener zu einem Orang-Utan gebracht, der mit menschlichen Sklaven handelt. Danach hat Davidson nur noch ein Ziel: Er will frei kommen und diesen Planeten wieder verlassen. Ari (Helena Bonham-Carter), ein Affenweibchen, das an die Gleichheit der Spezies glaubt, hilft ihm. So wird er zum Gegenspieler des Affengenerals Thade (Tim Roth), der die Herrschaft über den Planeten an sich reißen und alle noch lebenden Menschen vernichten will.',24.9,0,'N',0,'',NULL,NULL,NULL,20020930130736,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (39,'dvd012','Practical magic','Sally und Gillian Owens sind alles andere als normale junge Frauen: Als Abkömmlinge eines uralten Hexengeschlechts beherrschen sie alle Tricks und Kniffe der weißen Magie. Doch auf den beiden lebenslustigen Schwestern lastet ein unheimlicher Fluch: Jeder Mann, der sich in sie verliebt, stirbt eines frühzeitigen, unnatürlichen Todes.',24.9,0,'N',0,'',NULL,NULL,NULL,20020930094535,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (40,'dvd013','P. Tinto\'s Miracle','P. Tinto wünscht sich schon seit Kindheit nichts anderes als einmal eine grosse, kinderreiche Familie zu haben. Diesen Wunsch teilt er mit seiner Schulkameradin Olivia, die er später auch heiratet. Eine fatale Fehlinterpretation bei der Aufklärung verhindert jedoch das Kinderglück. Da ist es dem Paar natürlich nur recht, dass sich 2 zwergenwüchsige Aliens, die in ihrem Garten notlanden mussten, als Kinderersatz anbieten, solange sie nur genug zu essen bekommen. So eine Zweckgemeinschaft ist natürlich kein Ersatz für eine richtige Familie, also beschliessen P. Tinto und Olivia einen kleinen schwarzen Jungen zu adoptieren. Der Antrag kommt aber erst gar nicht bei den Behörden an, sondern fällt durch Zufall Joselito, der vor ein paar Jahren aus einer russischen Anstalt entflohen ist, in die Hände. Von Statur und Gemüt eher ein Riesenbaby, wird er von P. Tinto trotzdem mit offenen Armen empfangen - trotz weisser Hautfarbe könnte er schließlich immer noch ein Massai sein. Joselito quälen immer noch Alpträume vom Tod seiner Mutter, an dem er nicht ganz unschuldig war. Nichts würde er lieber tun, als dies ungeschehen machen. Und nichts wollen unsere beiden Alien lieber als Joselito loszuwerden, da er ihnen das ganze Essen wegfrisst. Zum Glück hat ihr altes Raumschiff auch eine Zeitreisefunktion. Doch die Kutsche muss erst einmal repariert werden, was nicht so ganz einfach ist. Erschwerend kommt noch hinzu, dass es sich ein allzu aufdringlicher Handwerker zur Berufung gemacht hat, Aliens zu finden, die laut seinem Handbuch exakt wie Joselito aussehen, und ausserdem der Vatikan den Abnahmevertrag mit P. Tintos Oblatenfabrik aufkündigt, weshalb diese zu einem Pizzalieferservice umfunktioniert werden soll. Als dann auch noch die NASA auftaucht ist das Chaos perfekt.',39.9,0,'N',0,'',NULL,NULL,NULL,20020930094947,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (41,'dvd014','Rush Hour 2','Als Hongkong-Inspektor Lee LAPD-Detective Carter seine Heimatstadt zeigen will, wird er in einen dramatischen Fall verwickelt. Durch einen Bombenanschlag wurden zwei Agenten getötet, die auf einen Falschgeldring angesetzt waren. Die Spur führt nach Las Vegas zur Neueröffnung des Red Dragon Casino, wo das Falschgeld gewaschen werden soll.',36.9,0,'N',0,'',NULL,NULL,NULL,20020930095401,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (42,'dvd015','Mit Schirm, Charme und Melone','Das diabolische Genie Sir August de Wynter hat eine Maschine erfunden, die ihn das Klima beherrschen lässt. Und er droht, die Welt in ein eisiges Inferno zu stürzen, wenn seine wahnsinnigen Forderungen nicht erfüllt werden. Höchste Zeit für den Secret Service, seine besten Leute auf den Fall anzusetzen - zwei unschlagbare Super-Agenten, die Sir August mit eiskalter Präzision, knochentrockenem Humor und absolut makellosem Stil den Kampf ansagen.',24.9,0,'N',0,'',NULL,NULL,NULL,20020930095559,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (43,'dvd034','Shrek','Monstrum Shrek möchte endlich wieder Ruhe in seinem Sumpf und lässt sich von dem durchtriebenen Fürsten Farquaad dazu überreden, für den Edelmann Prinzessin Fiona zu freien. Die Prinzessin wird von einem Drachen in einer alten Schlossruine bewacht. Unterstützung erhält er von einem sprechenden Esel, der in der Gefahr zum Freund wird.',19.9,0,'N',0,'',NULL,NULL,NULL,20020930130817,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (44,'dbd016','The sixth sense','Nicht jede Gabe ist ein Segen.\r\nPsychospannung pur: Dr. Malcolm Crowe (Bruce Willis) will dem jungen Cole (Haley Joel Osment) helfen, der von Angstvorstellungen geplagt wird. Doch schon bald muss der seelendoktor erkennen, dass die von ihm diagnostizierten Halluziationen wirklich sind. Es beginnt ein spannendes Vordringen in eine neue, spirituelle Welt des Grauens! -erfolgreiches Thriller-Drama mit talentierter Starbesetzung und überraschendem Finale. ein Must-See-Movie längst nicht nur für Fantasy-Fans!',34.9,0,'N',0,'',NULL,NULL,NULL,20020930100223,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (45,'dvd017','Auf die stürmische Art','Ben ist ein Mann, der hält, was er verspricht! Und seiner Verlobten Bridget hat er die Ehe versprochen und dabei bleibt\'s! Was soll da noch schiefgehen? Alles! Ben wird geprüft! Gleich mehrere Wirbelstürme bringen sein geordnetes Leben durcheinander. Der eine fegt über Georgia, wo die Hochzeit stattfinden soll. Die andere Naturgewalt heißt Sarah. Die ausgeflippte Chaotin ist genau die Sorte Mensch, die Ben bisher immer gemieden hat. Doch Schicksal oder gegenseitige Anziehungskraft - sie kommen nicht voneinander los. Kontrollfreak Ben stürmt von einer Katastrophe in die nächste. Sein Flugzeug stürzt beinahe ab, beim Trampen geraten die beiden an einen Wahnsinnigen und landen im Knast, ihr Zug geht in die falsche Richtung, ihr ganzes Geld wird geklaut. Die Irrfahrt durch die Lande wird zur Achterbahn der Gefühle.',18.9,0,'N',0,'',NULL,NULL,NULL,20020930104859,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (46,'dvd018','Supernova','Das Universum im 22.Jahrhundert ist grausam und düster. Um Leiden zu lindern, patrouilliert das Sanitätsschiff \"Nightingale\" an den äussersten Grenzen der menschlichen Zivilisation. Als die sechsköpfige Besatzung einem Hilferuf folgt, beginnt für sie ein Flug in die Hölle...Nach einem beinahe missglückten Hyper-Raum-Sprung landet die Mannschaft im Gravitationsfeld eines gigantischen Sterns, der fast erloschen ist. Ein junger Mann kann gerade noch gerettet werden, doch bringt er ein schreckliches Gehemnis mit an Bord. Die grösste Gefahr lauert jedoch im Stern selbst - bald ist sein Licht verglüht und er wird alles um sich als Supernova mit in den Tod reißen! Für die Besatzung beginnt ein Kampf an vielen Fronten.',32.9,0,'N',0,'',NULL,NULL,NULL,20020930105122,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (47,'dvd019','The cell','Der geisteskranke Serienkiller Carl Stargher hat es auf junge Frauen abgesehen. Doch FBI-Agent Peter Novak kommt ihm sehr schnell auf die Spur. Bevor Stargher verraten könnte, wo er sein letztes Opfer gefangen hält, fällt er in eine Art Koma. Novak ist unter Zeitdruck: Sollte er innerhalb der nächsten 40 Stunden die junge Frau nicht finden, wird sie in einer hermetischen Zelle, die über eine Zeitautomatik mit Wasser volläuft, qualvoll ertrinken. Als letzten Ausweg wendet Novak sich an die Psychotherapeutin Catherine Deane. Mit Hilfe einer neuen Methode soll sie in die Gedankenwelt des Killers eintauchen, um dort nach Hinweisen auf das Versteck zu suchen. Catherine gerät dabei jedoch in eine Alptraumwelt von so monströsen Ausmassen, dass sie bald selbst nicht mehr zwischen virtueller und tatsächlicher Wirklichkeit unterscheiden kann. Und schließlich stellt sie fest, dass Stargher sie in seiner kranken Phantasie bereits längst erwartet hat.',48.9,0,'N',0,'',NULL,NULL,NULL,20020930105325,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (48,'dvd020','The Time Machine (2002)','Alexander Hartdegen ist Erfinder und versucht zu beweisen, das Zeitreisen möglich sind. Mit dem Mut der Verzweiflung testet er eine selbst konstruierte Zeitmaschine und landet nach Umwegen 800.000 Jahre später in der Zukunft. Dort ist die Menschheit aufgeteilt zwischen in der Unterwelt hausenden Morloks und den hedonistischen Elois.',29.9,0,'N',0,'',NULL,NULL,NULL,20020930105554,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (49,'dvd021','Titan A.E.','Im Jahre 3028 wird die Erde vom Alien-Schiff der Drej zerstört. Nur ein paar Menschen überleben, unter ihnen auch der junge Cale. 15 Jahre später erfährt er, dass er in seiner Hand die Karte hält, die den Weg zum legendären verschollenen Raumschiff \"titan\" weist, in dem sein Vater damals floh. da die \"titan\" den Schlüssel für das Überleben der Menschheit besitzen soll, begibt sich Cale mit seinen Freunden auf die Suche nach dem Raumschiff. Eine gefahrvolle und abenteuerliche Reise in fremden Welten beginnt.',27.9,0,'N',0,'',NULL,NULL,NULL,20020930105907,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (50,'dvd022','Toy story 2','Cowboy Woody wurde entführt! Da gibt es für seinen besten Freund Space Ranger Buzz Lightyear nur eins: raus aus der Spielzeugkiste, rein ins grösste Rettungsabenteuer! Und mit ihm all die witzigen, chaotischen und tapferen Spielzeuge aus Andys Zimmer. Sie riskieren wirklich alles, um ihren Freund aus den Händen des skrupellosen Spielzeughändlers Al zu befreien. Der will Woody als Sammlerstück an ein japanisches Museum verkaufen. Ihre spannende Suche führt über gefährliche Kreuzungen, in gigantische Spielzeuggeschäfte und durch abgrundtiefe Fahrstuhlschächte. Ein Gag jagt den nächsten, wenn sie die ganze Stadt auf den Kopf stellen. Doch als sie Woody endlich finden, ist es fast zu spät. Er wird, gut verpackt, schon zum Abflug verfrachtet.',45.9,0,'N',0,'',NULL,NULL,NULL,20020930110139,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (51,'dvd023','Unbreakable','Ein verheerendes Zugunglück vor den Toren von Philadelphia. 131 Tote. Nur ein Mann hat die Katastrophe überlebt...völlig unverletzt, ganz ohne Schrammen. Hatte David Dunn nur Glück? Oder gibt es einen tieferen Grund für sein Überleben? Er hat keine Antworten auf seine Fragen. Doch dann tritt der mysteriöse Elijah Price in sein Leben. Er behauptet, David Dunn sein unzerbrechlich und er wüsste warum...Seien Sie gefasst auf geheimnisvolle, übernatürliche Kräfte und ein unerwartetes Ende!',39.9,0,'N',0,'',NULL,NULL,NULL,20020930110438,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (52,'dvd024','X-men (2000)','Für Bösewichte brechen schwere Zeiten an, die X-Men kommen! Aufgrund einer genetischen Mutation verfügen sie nämlich über Superkräfte. unter der Führung von Dr. Charles Xavier trotzen die Unerschrockenen den irrwitzigsten Herausforderungen, um eine bessere Welt zu schaffen. Doch die Welt, die sie beschützen wollen, fürchtet sie in Wirklichkeit.',27.9,0,'N',0,'',NULL,NULL,NULL,20020930111036,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (53,'dvd025','Big Mama\'s Haus','Der smarte FBI-Agent Malcom ist ein Meister der Tarnung. Um die alleinstehende Mutter Sherry vor ihrem Ex-Mann, einem entflohenen Sträfling, zu beschützen, schlüpft er kurzerhand in die Rolle der abwesenden Großmutter \"Big Momma\". Soweit so gut, doch zwischen Haushalts- und Polizeiroutine kommt Malcom schließlich Sherry näher. Die einzige, die jetzt noch das Chaos bändigen kann, ist natürlich die echte Big Momma.',34.9,0,'N',0,'',NULL,NULL,NULL,20020930113903,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (54,'dvd026','The 13th floor','Die Computerspezialisten Douglas Hall und Hannon Fuller haben ein Universum, Los Angeles im Jahre 1937, entwickelt...auf einem Computerchip.\r\nals Fuller tot aufgefunden wird und die Ermittlungen zu Douglas führen, wird dieser zum Hauptverdächtigen. Doch er kann sich an nichts erinnern. um die Wahrheit herauszufinden, muss sich Douglas nach 1937 begeben. Schnell findet er heraus, dass die Verschwörung Dimensionsübergreifend ist. Auf der Suche nach der Wahrheit stösst Hall auf eine geheimnisvolle Schönheit, die vorgibt, Fullers Tochter zu sein. Hält sie den Schlüssel zur Lösung des Geheimnisses in der Hand? Douglas nimmt die lebensgefährliche Spur auf.',48.9,0,'N',0,'',NULL,NULL,NULL,20020930114447,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (55,'dvd027','Blade runner','Los Angeles im Jahre 2019. Ex-Polizist Deckard erhält den Auftrag, vier geflohene \"Replikanten\" zu töten - künstliche Androiden, die von Menschen kaum zu unterscheiden sind. eine atemberaubende Hetzjagd durch eine futuristische Welt beginnt! Nun endlich erscheint der legedäre \"Blade runner\" erstmals in der von Regisseur Ridley Scott (Alien) autorisierten Fassung auf DVD.',27.9,0,'N',0,'',NULL,NULL,NULL,20020930115259,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (56,'dvd028','Chicken Run - Hennen Rennen','Die Hühner Ginger, Bunty, Babs und Mac sind fest entschlossen, ihrem Schicksal als Geflügelpastete zu entgehen und träumen von einer grünen Welt jenseits ihrer eingezäunten Eierfarm. Doch so einfach ist es nicht, in die Freiheit zu gelangen: Die böse Mrs.Tweedy und ihr tumber Ehemann ertappen sie bei all ihren irrwitzigen und noch so ausgeklügelten Fluchtversuchen. Als der amerikanische Zirkus-Hahn Rocky auf der Hühnerfarm bruchlandet, sieht Ginger endlich den rettenden Ausweg: Sie und die anderen Hühner werden die Farm einfach fliegend verlassen und Rocky muss ihnen dabei helfen...Zusammen werden sie beweisen, dass sie keine dummen Hühner sind. Sie planen einen spektakulären und äusserst gewagten Ausbruch.',48.9,0,'N',0,'',NULL,NULL,NULL,20020930115542,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (57,'dvd029','Gattaca','Schöne neue Welt! Ein Gentest entscheidet in der Gesellschaft der Zukunft über das Schicksal frisch geborener Kinder. Vincent hat Pech: Weil er nicht über die nötigen genetischen Voraussetzungen verfügt, steht fest, daß sich sein Traum niemals erfüllen wird, eines Tages als Gattaca-Pilot das Weltall zu erforschen. Doch Vincent hat einen todesmutigen Plan: Der einstige Sportstar Jerome hat beste Gene, ist seit einem Unfall jedoch an den Rollstuhl gefesselt. Vincent übernimmt seine Identität und kann sich so in das Pilotenprogramm einschleusen. Als jedoch ein Mord geschieht, droht Vincents Täuschungsmanöver aufzufliegen. Schnell ist ihm der Geheimdienst auf den Fersen, und Vincent muss alles auf eine Karte setzen.',44.9,0,'N',0,'',NULL,NULL,NULL,20020930115807,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (58,'dvd030','Matrix','Der Hacker Neo wird übers Internet von einer geheimnisvollen Untergrund-Organisation kontaktiert. Der Kopf der Gruppe - der gesuchte Terrorist Morpheus - weiht ihn in ein entsetzliches Geheimnis ein: die Realität, wie wir sie erleben, ist nur eine Scheinwelt. In Wahrheit werden die Menschen längst von einer unheimlichen virtuellen Macht beherrscht - der Matrix, deren Agenten Neo bereits im Visier haben...\r\nKeanu Reeves und Laurence Fishburne in einem Cyber-Sci-fi-Action-Thriller der Superlative: \"ein Meilenstein in der Filmgeschichte. Kino total - und kultverdächtig\".',21.9,0,'N',0,'',NULL,NULL,NULL,20020930120121,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (59,'dvd035','Reine Nervensache','Eigentlich gilt er als der Härteste unter den ganz harten Jungs, doch urplötzlich wird der New Yorker Mafiaboss Paul Vitti von Angstneurosen, nervösen Atembeschwerden und Potenzproblemen geplagt. Hilfe sucht er bei dem sensiblen High-Society Psychiater Ben Sobel. Der verfällt zwar zunächst selbst in Panik - aber das Angebot des Paten kann er nun wirklich nicht abschlagen.',24.9,0,'N',0,'',NULL,NULL,NULL,20020930130858,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (60,'dvd031','Romeo must die','In einer Stadt, die von Verbrechern regiert wird, haben zwei Familien den Respekt vor dem Gesetz verloren.\r\nOakland, an der Bucht von San Francisco: ein unvorhersehbares Ereignis entzündet einen blutigen Krieg zwischen einem chinesischen und einem schwarzen Klan. Ihren Kampf um die Vorherrschaft im Hafenviertel müssen sie mit mehr bezahlen als mit Geld. Während ihre Familien sich bekämpfen, entdecken Ex-Cop Han Sing (Jet Li) und die schwarze Schönheit Trish o\' Day (Sängerin Aaliyah in ihrem Leinwand-debüt) ihre Zuneigung. Gemeinsam versuchen sie das Geheimnis hinter den Morden zu lüften und begeben sich dabei in grösste Gefahr.\r\nMartial art Fights an den Grenzen der Schwerkraft, atemberaubende Visual Effects plus die heissen Sounds der angesagtesten Hip Hop- und R&V-Stars-mit \"Romeo must die\" setzen Produzent Joel Silver (\"the matrix\" und Hauptdarsteller Jet Li (\"lethal weapon 4\") neue Massstäbe im Action Film.',18.9,0,'N',0,'',NULL,NULL,NULL,20020930120742,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (61,'dvd036','Schwer verliebt','Durch Hypnose soll der oberflächliche Hal nur noch die innere Schönheit von Frauen wahrnehmen. Er verliebt sich in die 150 Kilo schwere Rosemary, die er als gertenschlanke Schönheit wahrnimmt. Als sie ein Paar werden, schwebt Hal im siebten Himmel, doch sein Kumpel Mauricio macht die Gehirnwäsche wieder rückgängig.',36.9,0,'N',0,'',NULL,NULL,NULL,20020930130930,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (62,'dvd037','Snatch - Schweine und Diamanten','Schon mal was von \"One Punch Mickey\" gehört? Oder von \"Franky four Fingers\"? Wahrscheinlich nicht, aber sehen Sie hier: Brad Pitt als \"One Punch Mickey\", ein irischer Zigeuner, der den härtesten Schlag in der Geschichte des illegalen Boxens hat. Man versteht nie genau, worüber er spricht, aber wenn er zuschlägt, sind alle Unklarheiten beseitigt. In dieser Wahnsinnsgeschichte aus der englischen Unterwelt geben nur die schrägsten Gestalten den Ton an. Mit von der Partie: Benico del Toro (\"die üblichen Verdächtigen\") als spielsüchtiger Mafiosi \"Franky four Fingers\" und Dennis Farina (\"schnappt shorty\").',44.9,0,'N',0,'',NULL,NULL,NULL,20020930131041,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (63,'dvd038','Der Staatsfeind Nr. 1','Robert Clayton Dean (Will Smith) ist ein junger, erfolgreicher Anwalt, der mit seiner Frau und seinen Kindern ein ruhiges Leben in der Vorstadt führt. Bis zu jenem Tag, an dem er einen alten College-Freund (Jason Lee) ausgerechnet in einem Geschäft für Damenunterwäsche über den Weg läuft. Ohne es zu bemerken, steckt ihm sein alter Freund ein Videoband in die Einkaufstasche, auf dem zufällig die Ermordung eines Kongress-Abgeordneten aufgezeichnet worden ist. Diesem Band ist eine unbarmherzige Gruppe von National Security Agenten auf der Spur, die von dem hochrangigen, ehrgeizigen Geheimdienst-Direktor Reynolds (Jon Voight) angeführt wird. Die NSA nutzt Spionagesatelliten, High-Tech-Mikrofone und andere ausgeklügelte Spionage-Utensilien, um jede Bewegung von Dean zu verfolgen. Dean bleiben die Aktivitäten des Geheimdienstes nicht verborgen. Und so bittet er schließlich den inzwischen aus dem Geheimdienst ausgeschiedenen Agenten Brill (Gene Hackman) um Hilfe.',39.9,0,'N',0,'',NULL,NULL,NULL,20020930131118,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (64,'dvd032','Taxi','Daniel, ein ehemaliger Pizza-Lieferant, der jetzt als Taxifahrer jobbt, ist geschwindigkeitssüchtig. Sein Taxi hat er zur ultimativen High-Tech Speed-Maschine umgebaut. Radarkontrollen können die Geschwindigkeit, mit denen er das Taxi durch die Strassen peitscht, nicht mehr wahrnehmen. Emilien ist Polizist, und kein wirklich perfekter. Er ist zum achten Mal durch die Führerscheinprüfung gefallen, und es gelingt ihm einfach nicht, einer berüchtigten Bankräuber-Bande auf die Schliche zu kommen. Unfreiwillig kommt Daniel mit Emilien ins Geschäft. Daniel darf weiter mit dem Taxi durch die City hetzen. Wenn er Emilien hilft, die Gangster zur Strecke zu bringen. Mit der Unterstützung eines Riesengeschwaders an Pizza-Lieferanten machen sie sich auf den Weg. Und diese Jungs fahren einen verdammt heissen Reifen.',32.9,0,'N',0,'',NULL,NULL,NULL,20020930121912,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (65,'pepper001','Teufelsküche. Höllisch scharfe Sachen.','Früher kostbarste Ware, stehen sie heute in jeder Küche: Gewürze. Pfeffer, Safran, Chili wurden einst in Gold aufgewogen und erregten entsprechende Begehrlichkeiten, die zum ersten weltweiten Handel, aber auch einer permanenten Auseinandersetzung der \"Pfeffersäcke\" führten. Die Autorin berichtet von diesen Querelen, beschreibt die historische Verwendung aromatischer Pflanzen bei der Herstellung von Parfums und Medizin, läßt die Atmosphäre mittelalterlicher Alchemistenküchen wiederauferstehen. Sie schildert, wie man richtig dosiert und zeigt, wie Gewürze ihre optimale Wirkung entfalten. Rund 120 Rezepte verführen dazu, den Gaumen und die 10.000 menschlichen Geschmacksknospen \"zu kitzeln\". ',18.9,0,'N',0,'',NULL,NULL,NULL,20020930145434,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (66,'pfeffer_002','Wo der Pfeffer wächst','Schon die alten Ägypter, Griechen und Römer verstanden es, ihre Speisen mit erlesenen Gewürzen zu verfeinern. Lorbeer, Kümmel, Kerbel, Safran und natürlich Pfeffer waren Bestandteile ihrer Küchen. Im Mittelalter dann wurden Gewürze zum Statussymbol; Handelshäuser kamen ihretwegen zu großem Reichtum. Auf der Suche nach einem direkten Zugang zu den Gewürzländern landete Columbus 1492 statt in Indien in Amerika und brachte von dort neue Gewürze mit. 1497 schließlich entdeckte Vasco da Gama den Seeweg ums Kap der Guten Hoffnung nach Indien. Portugiesen, Holländer und Briten fuhren in die Welt und errichteten neue Monopole, die großen Handelskompanien entstanden, Gewürzkriege wurden ausgetragen. Klaus Trebes stellt im vorliegenden Band die wichtigsten Gewürze und Krauter vor: ihre Geschichte und die Koch-und Würzgewohnheiten der Menschen von der Antike bis zur Gegenwart. Zu jedem Gewürz gibt es zwei Rezepte von Klaus Trebes, einem der besten Köche Deutschlands.',16.1,0,'N',0,'',NULL,NULL,NULL,20020930145902,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (67,'pfeffer003','PhPepperShop Pfefferschote','Die Original PhPepperShop Pfefferschote in verschiedenen Grössen, Farben und Schärfegraden. Topfrisch, einzigartig und nur in diesem Shop!',10,0,'N',0,'http://www.phpeppershop.com','67_gr.jpg','67_kl.jpg','image/jpeg',20021007090946,0,5,'2002-10-07','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (68,'spiele001','Siedler von Catan','Als Spiel mit Karten ist die Besiedelung Catans eine neue Herausforderung. Bauen Sie Ihre Siedlungen und Städte aus. Errichten Sie Badhäuser, um gegen Seuchen gewappnet zu sein; vervielfachen Sie Ihre Rohstoffeinkommen mit Getreidemühlen und Wollmanufakturen. Ritter schützen Ihr Land und wenn Sie sich bei Turnieren wacker schlagen, springen zusätzliche Rohstoffe heraus. \"Die Siedler von Catan\" für Zwei - ein Spiel mit Karten, das es Ihnen ermöglicht, die Atmosphäre und Spannung des Brettspiels mit nur einem Partner zu erleben.',22.45,0,'N',0,'http://www.diesiedlervoncatan.de',NULL,NULL,NULL,20020930153705,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (69,'spiele_002','Sternenschiff von Catan','Jeder der Sternenfahrer startet vom eigenen Sonnensystem mit Kolonieraumschiffen und Handelsraumschiffen auf zu neuen Galaxien. So wie die Vorfahren der Sternenfahrer die Felder Catans kultiviert haben, um Rohstoffe zu gewinnen, so erschließen nun die Pilger im Weltraum unbekannte Planeten, um neue Rohstoffe zu erwirtschaften, die für die Weltraumreise unverzichtbar sind.\r\nGenau so wichtig sind jedoch auch die Handelsraumschiffe, um Kontakt mit fremden galaktischen Völkern aufzunehmen. Jedes dieser Völker - vier verschiedene gibt es - verfügt über spezielle Fähigkeiten und Möglichkeiten, die die Cataner nur allzu gern nutzen möchten.',39.5,0,'N',0,'http://www.kosmos.de/',NULL,NULL,NULL,20020930153611,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (70,'spiele003','Die Siedler von Catan','Versetzen Sie sich in das Zeitalter der Entdeckungen: Ihre Schiffe haben nach langer, entbehrungsreicher Seefahrt die Küste einer unbekannten Insel erreicht. Catan soll sie heissen!\r\nDoch sie sind nicht der einzige Entdecker. Auch andere, unerschrockene Seefahrer sind an der Küste Catans gelandet: Der Wettlauf um die Besiedelung hat begonnen!',47.95,0,'N',0,'http://www.diesiedlervoncatan.de',NULL,NULL,NULL,20020930154418,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (71,'spiele005','Trans America','Amerika im 19. Jahrhundert: der Eisenbahnbau boomt. Pioniergeist und Weitsicht sind gefragt. Jeder möchte der Schnellste sein beim Ausbau des Schienennetzes quer durch die Staaten. Wer wird als Erster seine Städte anschliessen? Charlie, der als Einziger schon in den Westen vorgedrungen ist? Oder doch Helena, die  es geschickt versteht, die Schienen der anderen für sich zu nutzen?',35,0,'N',0,'http://wwww.winning-moves.de/',NULL,NULL,NULL,20020930155451,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (72,'spiele_006','Metro','Die Weltaustellung 1900 steht vor der Tür. Auf den grossen Plätzen wird gegraben und gebuddelt. Überall entstehen seltsame Gerüste. Tunnel werden auf den Strassen gebaut, um später in der Erde versenkt zu werden. Bauen Sie mit an der Pariser Metro!',39,0,'N',0,'http://www.queen-games.de/pages/11121.htm',NULL,NULL,NULL,20020930155811,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (73,'food1','Margherita','Tomatensauce, Mozzarella, Oregano',0,0,'N',0,'',NULL,NULL,NULL,20020930160506,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (74,'food2','Cipolla','Tomatensauce, Mozzarella, Zwiebeln, Oregano',0,0,'N',0,'',NULL,NULL,NULL,20020930160827,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (75,'food3','Prosciutto e funghi','Tomatensauce, Mozzarella, Schinken, Champignons, Oregano',0,0,'N',0,'',NULL,NULL,NULL,20020930161219,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (76,'food4','Napoli','Tomatensauce, Mozzarella, Sardellen, Kapern, Oregano',0,0,'N',0,'',NULL,NULL,NULL,20020930161041,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (77,'food5','Prosciutto','Tomatensauce, Mozzarella, Schinken, Oregano',0,0,'N',0,'',NULL,NULL,NULL,20020930161131,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (78,'food6','Rustica','Tomatensauce, Mozzarella, Schinken, Speck, Zwiebeln, Knoblauch',0,0,'N',0,'',NULL,NULL,NULL,20020930161325,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (79,'food7','Kickiricki','Tomatensauce, Mozzarella, Curry-Pouletwürfeli, Oregano',0,0,'N',0,'',NULL,NULL,NULL,20020930161421,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (80,'food8','Hawaii','Tomatensauce, Mozzarella, Schinken, Ananas, Oregano',0,0,'N',0,'',NULL,NULL,NULL,20020930161555,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (81,'food9','Pizza selbstgebaut <b>klein (20cm)</b>','Stellen Sie sich Ihre persönliche Lieblingspizza zusammen. Alle Zutaten werden frisch zubereitet. Tomatensauce und Mozzarella sind im Grundpreis inbegriffen.',10,0,'N',0,'',NULL,NULL,NULL,20020930162953,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (82,'food10','Pizza selbstgebaut <b>normal (30cm)</b>','Stellen Sie sich Ihre persönliche Lieblingspizza zusammen. Alle Zutaten werden frisch zubereitet. Tomatensauce und Mozzarella sind im Grundpreis inbegriffen.',12,0,'N',0,'',NULL,NULL,NULL,20020930163209,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (83,'food11','Pizza selbstgebaut <b>gross (40cm)</b>','Stellen Sie sich Ihre persönliche Lieblingspizza zusammen. Alle Zutaten werden frisch zubereitet. Tomatensauce und Mozzarella sind im Grundpreis inbegriffen.',14,0,'N',0,'',NULL,NULL,NULL,20020930163116,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (84,'food12','Lasagne','500g',15,0,'N',0,'',NULL,NULL,NULL,20020930163324,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (85,'food13','Cannelloni','500g',15,0,'N',0,'',NULL,NULL,NULL,20020930163359,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (86,'food14','Tortelloni alla panna','500g',15,0,'N',0,'',NULL,NULL,NULL,20020930163426,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (87,'food15','Grüner Salat','Mit frischen Salaten aus der Region.',5,0,'N',0,'',NULL,NULL,NULL,20020930164057,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (88,'food16','Gemischter Salat','Mit frischen Salaten aus der Region.',6,0,'N',0,'',NULL,NULL,NULL,20020930164014,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (89,'food17','Tomatensalat mit Zwiebeln','Mit frischen Salaten und Zwiebeln aus der Region sowie Tomaten aus Italien.',6,0,'N',0,'',NULL,NULL,NULL,20020930164216,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (90,'food18','Tomatensalat mit Mozzarella','Mit frischen Salaten aus der Region und Tomaten aus Italien.',7,0,'N',0,'',NULL,NULL,NULL,20020930164138,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (91,'food19','Coca-Cola','In 5dl oder 2l PET Flasche.',0,0,'N',0,'',NULL,NULL,NULL,20020930164506,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (92,'food20','Fanta','In 5dl oder 2l PET Flasche.',0,0,'N',0,'',NULL,NULL,NULL,20020930164551,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (93,'food21','Sprite','In 5dl oder 2l PET Flasche.',0,0,'N',0,'',NULL,NULL,NULL,20020930164611,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (94,'food22','Vitiano, rot (7dl)','Dieser volle intensive dunkelrote Rotwein ist schön erdig, samtig und hat viel Frucht. Er passt ausgezeichnet zu Käse, Fleisch und Grilladen. Es ist wichtig dass er mindestens eine Stunde lang dekantiert wird, denn erst dann entfaltet er sein intensives, wuchtiges, und dennoch elegantes Potential.',15.5,0,'N',0,'',NULL,NULL,NULL,20020930164747,0,5,'2002-09-30','','',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (95,'stemp001','Stempelautomat Trodat','Seit über 20 Jahren befindet sich der Klassiker Trodat Printy in vielen verschiedenen Grössen und Farben erfolgreich am Markt. Der Printy ist aber nicht nur - mit bisher über 100 Millionen Stück - einer der meistverkauften, sondern auch einer der meistkopierten Stempel.\r\n',15,0,'N',0,'http://www.trodat.de',NULL,NULL,NULL,20020930181851,0,5,'2002-09-30','Text 1. Zeile:þText 2. Zeile:þText 3. Zeile:','20:20:1:0:0þ20:20:1:0:0þ20:20:1:0:0',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO artikel (Artikel_ID, Artikel_Nr, Name, Beschreibung, Preis, Aktionspreis, Aktionspreis_verwenden, Gewicht, Link, Bild_gross, Bild_klein, Bildtyp, Bild_last_modified, Lagerbestand, Mindestlagermenge, letzteAenderung, Zusatzfeld_text, Zusatzfeld_param, MwSt_Satz, Aktion_von, Aktion_bis)
  VALUES (96,'stemp002','Stempelautomat Professional','Innovation pur für den Geschäftsalltag. Dieses Produkt aus der Serie Professional Line bieten die ideale Kombination von robuster Technik und modernem Design.',35,0,'N',0,'http://www.trodat.de',NULL,NULL,NULL,20020930183225,0,5,'2002-09-30','Text 1. Zeile:þText 2. Zeile:þText 3. Zeile:þText 4. Zeile:þText 5. Zeile:','20:20:1:0:0þ20:20:1:0:0þ20:20:1:0:0þ20:20:1:0:0þ20:20:1:0:0',0,'0000-00-00 00:00:00','0000-00-00 00:00:00');


# /*
# * ------------------------------------------------------------------------------
# *
# * Jeden Artikel einer Kategorie zuweisen:
# */
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (29,29,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (46,46,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (45,45,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (44,44,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (50,50,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (49,49,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (48,48,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (47,47,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (43,43,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (39,39,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (38,38,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (37,37,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (36,36,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (31,31,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (30,30,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (27,27,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (28,28,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (32,32,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (42,42,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (41,41,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (35,35,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (34,34,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (40,40,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (33,33,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (51,51,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (52,52,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (53,53,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (54,54,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (55,55,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (56,56,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (57,57,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (58,58,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (59,59,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (60,60,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (61,61,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (62,62,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (63,63,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (64,64,13);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (65,65,14);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (66,66,14);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (67,67,14);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (68,68,16);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (69,69,16);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (70,70,17);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (71,71,17);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (72,72,17);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (73,73,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (74,74,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (75,75,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (76,76,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (77,77,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (78,78,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (79,79,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (80,80,19);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (81,81,20);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (82,82,20);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (83,83,20);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (84,84,21);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (85,85,21);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (86,86,21);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (87,87,22);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (88,88,22);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (89,89,22);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (90,90,22);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (91,91,23);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (92,92,23);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (93,93,23);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (94,94,23);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (95,95,25);
INSERT INTO artikel_kategorie (a_k_ID, FK_Artikel_ID, FK_Kategorie_ID) VALUES (96,96,25);


# /*
# * ------------------------------------------------------------------------------
# *
# * Jeder Artikel kann Optionen haben, auch mehrere, hier die Deklarationen:
# */
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (3,'Themen-Set, Politik & Intrige',8.95,68,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'mit Besteck',0.5,75,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,75,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'mit Besteck',0.5,77,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,77,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'mit Besteck',0.5,76,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,76,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (6,'Peperoncini',1,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (5,'Spinat',1.5,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (4,'Steinpilze',3,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (3,'Salami',2,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'Schinken',2,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'Oregano',1,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'mit Besteck',0.5,78,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,78,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'mit Besteck',0.5,73,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,73,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'mit Besteck',0.5,79,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,79,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'mit Besteck',0.5,74,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,80,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'Besteck',0.5,80,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'geschnitten',0,74,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (7,'Das Buch zum Spielen, m. Materialbox',57.7,70,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (6,'Seefahrer-Erweiterung(5 und 6 Spieler)',12.2,70,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (3,'Seefahrer-Erweiterung',40.45,70,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'Ergänzungs-Set(5 und 6 Spieler)',20.25,70,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'Städte und Ritter-Erweiterung',40.45,70,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (5,'Themen-Set, Zauberer & Drachen',8.95,68,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (4,'Themen-Set, Ritter & Händler',8.95,68,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'Themen-Set, Wissenschaft & Fortschritt',8.95,68,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'Themen-Set, Handel & Wandel',9.2,68,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (4,'Troja & Die Grosse Mauer',25.9,70,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (5,'Alexander der Große & Cheops',20.95,70,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (7,'Knoblauch',1,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (8,'Kapern',1,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (9,'Gorgonzola',1,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (10,'Speck',2,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (11,'Meeresfrüchte',2,81,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'Oregano',1.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'Schinken',2.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (3,'Salami',2.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (4,'Steinpilze',3.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (5,'Spinat',2,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (6,'Peperoncini',1.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (7,'Knoblauch',1.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (8,'Kapern',1.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (9,'Gorgonzola',1.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (10,'Speck',2.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (11,'Meeresfrüchte',2.5,82,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (1,'Oregano',2,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (2,'Schinken',3,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (3,'Salami',3,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (4,'Steinpilze',4,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (5,'Spinat',2.5,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (6,'Peperoncini',2,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (7,'Knoblauch',2,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (8,'Kapern',2,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (9,'Gorgonzola',2,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (10,'Speck',3,83,0);
INSERT INTO artikel_optionen (Optionen_Nr, Optionstext, Preisdifferenz, FK_Artikel_ID, Gewicht_Opt) VALUES (11,'Meeresfrüchte',3,83,0);


# /*
# * ------------------------------------------------------------------------------
# *
# * Jeder Artikel kann Variationen haben, auch mehrere, hier die Deklarationen:
# */
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',16,75,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',19,74,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',16,74,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (6,'scharf',1,67,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (5,'mittel',0,67,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (4,'schwach',0,67,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gelb',0,67,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'grün',0,67,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'rot',0,67,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',13,73,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (11,'9cm',4,67,3,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (10,'7cm',3,67,3,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (9,'5cm',2,67,3,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (8,'3cm',0,67,3,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (7,'extrascharf',3,67,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',14,74,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',18,73,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',15,73,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',18,75,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',21,75,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',14,76,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',16,76,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',19,76,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',15,77,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',17,77,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',20,77,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',16,78,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',18,78,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',21,78,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',16,79,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',18,79,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',21,79,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'klein (20cm)',17,80,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'normal (30cm)',19,80,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'gross (40cm)',22,80,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'italienisch',0,87,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'französisch',0,87,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'spezial',0,87,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'französisch',0,88,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'italienisch',0,88,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'spezial',0,88,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'französisch',0,89,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'italienisch',0,89,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'spezial',0,89,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'französisch',0,90,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'italienisch',0,90,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'spezial',0,90,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'5dl',2.5,91,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'2l',6.5,91,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'5dl',2.5,92,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'2l',6.5,92,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'5dl',2.5,93,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'2l',6.5,93,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'rot (Gehäusefarbe)',0,95,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'schwarz (Gehäusefarbe)',0,95,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'blau (Gehäusefarbe)',0,95,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (4,'grün (Gehäusefarbe)',0,95,1,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (5,'rot (Druckfarbe)',0,95,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (6,'schwarz (Druckfarbe)',0,95,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (7,'blau (Druckfarbe)',0,95,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (8,'grün (Druckfarbe)',0,95,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (2,'schwarz (Druckfarbe)',0,96,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (1,'rot (Druckfarbe)',0,96,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (3,'blau (Druckfarbe)',0,96,2,0);
INSERT INTO artikel_variationen (Variations_Nr, Variationstext, Aufpreis, FK_Artikel_ID, Variations_Grp, Gewicht_Var) VALUES (4,'grün (Druckfarbe)',0,96,2,0);


# /*
# * ------------------------------------------------------------------------------
# *
# * Variationen der Artikel koennen in Variationsgruppen eingeteilt sein
# */
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (1,67,1,'Farbe','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (2,67,2,'Schärfegrad','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (3,67,3,'Länge','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (4,74,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (5,73,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (6,75,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (7,76,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (8,77,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (9,78,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (10,79,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (11,80,1,'Grösse','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (12,87,1,'Sauce','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (13,88,1,'Sauce','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (14,89,1,'Sauce','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (15,90,1,'Sauce','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (16,91,1,'Flasche','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (17,92,1,'Flasche','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (18,93,1,'Flasche','radio');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (19,95,1,'Gehäusefarbe','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (20,95,2,'Stempelkissen','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (22,96,1,'Gehäusefarbe','dropdown');
INSERT INTO artikel_variationsgruppen (Gruppen_ID, FK_Artikel_ID, Gruppen_Nr, Gruppentext, Gruppe_darstellen) VALUES (23,96,2,'Stempelkissen','dropdown');


# /*
# * ------------------------------------------------------------------------------
# *
# * Attribute setzen:
# */
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Anrede','','Y','Y','N','1');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Vorname','','Y','Y','Y','2');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Nachname','','Y','Y','Y','3');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Firma','','Y','Y','N','4');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Abteilung','','Y','Y','N','5');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Strasse','','Y','Y','Y','6');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Postfach','','Y','Y','N','7');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('PLZ','','Y','Y','Y','8');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Ort','','Y','Y','Y','9');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Land','','Y','Y','N','10');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Tel.','','Y','Y','N','11');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Fax','','Y','Y','N','12');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('E-Mail','','Y','Y','Y','13');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Zusatzfeld 1','','N','N','N','14');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Zusatzfeld 2','','N','N','N','15');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Zusatzfeld 3','','N','N','N','16');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Zusatzfeld 4','','N','N','N','17');
INSERT INTO attribut (Name,Wert,anzeigen,in_DB,Eingabe_testen,Positions_Nr)
       VALUES ('Bemerkungen','','Y','N','N','18');


# /*
# * ------------------------------------------------------------------------------
# *
# * Jetzt folgen die Shop-Settings (erfundener Shop)
# */
INSERT INTO shop_settings (MwStsatz, MwStpflichtig, MwStNummer, Name, Adresse1, Adresse2,
            PLZOrt, Tel1, Tel2, Email, Admin_pwd, Abrechnung_nach_Preis, Abrechnung_nach_Gewicht,
            Abrechnung_nach_Pauschale, keineVersandkostenmehr, keineVersandkostenmehr_ab, Mindermengenzuschlag,
            Mindermengenzuschlag_bis_Preis, Mindermengenzuschlag_Aufpreis, Rechnung, Nachnamebetrag,
            Nachnahme, Kreditkarten_Postcard, Waehrung, ShopVersion, Gewichts_Masseinheit,
            Thumbnail_Breite, max_session_time, AGB, TLS_value, Bestellungsmanagement, SuchInkrement, Gesamtpreis_runden,
            ArtikelSuchInkrement,Lastschrift,Haendlermodus, Haendler_login_text)
       VALUES (7.6, 'N', 100100,'{shop_db}','Pepperstrasse 1','Postfach 1000',
            '8001 {shop_db} city', '01 405 67 00', '079 322 56 88', 'Ihre E-Mail Adresse',
            '{shopadminpwd}', 'N', 'N', 'Y', 'N', 500.00, 'Y', 50.00, 5.00, 'Y', 12.00, 'Y', 'N', 'SFr.',
            'Januar 2007, Version v.1.4.010', 'kg',100, 1440, '{shop_db} Geschäftsbedingungen','N','Y',15,'Y',-1,'Y',
            'N','Falls Sie schon Kunde bei uns sind, k&ouml;nnen sie jetzt Ihren Benutzernamen und Ihr Passwort eingeben. Sind Sie Neukunde, registrieren Sie sich bitte zuerst.');

# /*
# * ------------------------------------------------------------------------------
# *
# * Seit PhPepperShop v.1.4 werden neue Shopsettings nur noch in der neuen Tabelle
# * shop_settings_new nach dem Key:Value Prinzip gespeichert
# */
INSERT INTO shop_settings_new (name,gruppe,wert,security)
       VALUES ('ArtikelSuchInkrementAnzeige','shop_settings','unten','user');

# /*
# * ------------------------------------------------------------------------------
# *
# * Die Default CSS Einstellungend des CSS-Layout-Managements
# */
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('admin_stern', 'ja');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('fontset_1', 'Arial,');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('fontset_2', 'Helvetica,');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('fontset_3', 'Geneva,');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('fontset_4', 'Swiss,');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('fontset_5', 'SunSans-Regular');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_bg_c', '#759CFF');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_bg_img', '');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_bg_img_typ', 'jpg');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_c', '#ffffff');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_s', '16px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_hover_c', '#c80000');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_hover_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_hover_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_hover_s', '16px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_font_hover_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('left_width', '165');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('logo_bg_img_typ', 'gif');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_bg_c', '#d3d3d3');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_bg_img', 'background-image:url(Bilder/bg_main.jpg);');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_bg_img_typ', 'jpg');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_bg_c', '#759CFF');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_bg_img', '');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_bg_img_typ', 'jpg');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_font_c', '#ffffff');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_font_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_font_i', 'italic');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_font_s', '30px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_font_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_height', '65');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_left', 'shoplogo');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_stern_c', '#ffffff');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_stern_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_stern_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_stern_s', '15px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('top_stern_w', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h1_c', '#000000');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h1_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h1_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h1_s', '25px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h1_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h2_c', '#000000');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h2_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h2_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h2_s', '23px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h2_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h3_c', '#000000');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h3_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h3_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h3_s', '20px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h3_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h4_c', '#000000');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h4_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h4_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h4_s', '16px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h4_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h5_c', '#000000');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h5_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h5_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h5_s', '12px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_h5_w', 'bold');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_font_c', '#000000');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_font_d', 'none');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_font_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_font_s', '16px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_font_w', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_link_c', '#00008b');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_link_d', 'underline');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_link_i', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_link_s', '16px');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('main_link_w', 'normal');
INSERT INTO css_file (Attribut_ID, CSS_String) VALUES ('backup', '');


# /*
# * ------------------------------------------------------------------------------
# *
# * Die Default Versandkostenpauschale (Tabelle versandkostenpreise)
# */
INSERT INTO versandkostenpreise (Von, Bis, Betrag, Vorauskasse, Rechnung, Nachname,
            Kreditkarte,billBOX,Treuhandzahlung,Postcard,FK_Setting_Nr)
       VALUES (0.00,9999999999,12.00,'Y','Y','Y','Y','Y','Y','Y',1);


# /*
# * ------------------------------------------------------------------------------
# *
# * Die Default Kreditkarten Einstellungen
# */
INSERT INTO kreditkarte (Hersteller, Handling, benutzen)
       VALUES ('VISA','intern','Y');
INSERT INTO kreditkarte (Hersteller, Handling, benutzen)
       VALUES ('Eurocard/Mastercard','intern','Y');
INSERT INTO kreditkarte (Hersteller, Handling, benutzen)
       VALUES ('American Express','extern','Y');
INSERT INTO kreditkarte (Hersteller, Handling, benutzen)
       VALUES ('Diners Club','extern','Y');


# /*
# * ------------------------------------------------------------------------------
# *
# * Die Default Backup Einstellungen
# */
INSERT INTO backup (Backup_ID, Wert)
       VALUES ('Anzahl_Backups','7');
INSERT INTO backup (Backup_ID, Wert)
       VALUES ('Backup_Intervall','24');
INSERT INTO backup (Backup_ID, Wert)
       VALUES ('Komprimierung','N');
INSERT INTO backup (Backup_ID, Wert)
       VALUES ('Automatisierung','kein');


# /*
# * ------------------------------------------------------------------------------
# *
# * Die Default Einstellungen der Tabelle zahlung_weitere (Reihenfolge ist WICHTIG)
# * (in dieser Tabelle werden Einstellungen zu weiteren Zahlungsmethoden gespeichert)
# */
INSERT INTO zahlung_weitere (Gruppe, Bezeichnung, verwenden, payment_interface_name,
            Par1, Par2, Par3, Par4, Par5, Par6, Par7, Par8, Par9, Par10)
       VALUES ('billBOX', 'billBOX', 'N', 'payment_interface_billBOX.php',
            '', '', '', '', '', '', '', '', '', '');
INSERT INTO zahlung_weitere (Gruppe, Bezeichnung, verwenden, payment_interface_name,
            Par1, Par2, Par3, Par4, Par5, Par6, Par7, Par8, Par9, Par10)
       VALUES ('Treuhand', 'Treuhandzahlung', 'N', 'Kein payment_interface verwendet',
            '100þ2', '500þ5', '1000þ10', '1500þ15', '2000þ20', '2500þ25', '5000þ50', '25000þ60', '50000þ70', '50þ50');

# /*
# * ------------------------------------------------------------------------------
# *
# * Die Default Einstellungen der Tabelle mehrwertsteuer
# * (in dieser Tabelle werden Einstellungen zu den Mehrwertsteuersaetzen gemacht)
# */
INSERT INTO mehrwertsteuer (MwSt_Satz, MwSt_default_Satz, Preise_inkl_MwSt, Beschreibung, Positions_Nr)
       VALUES ('7.6', 'Y', 'Y', 'Standard Mehrwertsteuersatz', 1);
INSERT INTO mehrwertsteuer (MwSt_Satz, MwSt_default_Satz, Preise_inkl_MwSt, Beschreibung, Positions_Nr)
       VALUES ('2.4', 'N', 'Y', 'Lebensmittel', 2);
INSERT INTO mehrwertsteuer (MwSt_Satz, MwSt_default_Satz, Preise_inkl_MwSt, Beschreibung, Positions_Nr)
       VALUES ('5.4', 'N', 'Y', 'Bevorzugt', 3);
INSERT INTO mehrwertsteuer (MwSt_Satz, MwSt_default_Satz, Preise_inkl_MwSt, Beschreibung, Positions_Nr)
       VALUES ('-1', 'N', 'Y', 'Porto und Verpackung', 99);

# /*
# * ------------------------------------------------------------------------------
# *
# * Hilfetexte
# */
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("top","
         Klicken Sie auf:
  <table class=\'content\' border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
    <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Bilder/kat_plus.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um eine Kategorie aufzuklappen und die in ihr enthaltenen Unterkategorien einzublenden.
      </td>
    </tr>
    <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Bilder/kat_leer.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um die Artikel der Kategorie anzuzeigen.
      </td>
    </tr>
    <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Bilder/kat_minus.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um eine Kategorie zuzuklappen.
      </td>
    </tr>
    <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Buttons/bt_warenkorb_zeigen.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um Ihren Warenkorb anzusehen und &Auml;nderungen daran vorzunehmen.
      </td>
    </tr>
   <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Buttons/bt_zur_kasse_1.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um Ihren Einkauf abzuschliessen und die gew&auml;hlten Produkte zu bestellen.
      </td>
    </tr>
   <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Buttons/bt_suchen.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um einen Artikel nach Stichworten zu suchen.
      </td>
    </tr>
   <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Buttons/bt_in_warenkorb.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um einen angezeigten Artikel in der gew&uuml;nschten Anzahl in den Warenkorb zu legen.
      </td>
    </tr>
   <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Buttons/bt_loeschen.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um einen Artikel aus dem Warenkorb zu entfernen
      </td>
    </tr>
   <tr class=\'content\'>
      <td class=\'content\' valign=middle align=center>
        <img src=\"Buttons/bt_hilfe.gif\" boder=0>
      </td>
      <td class=\'content\' valign=middle align=left>
        um in diese Hilfe zu gelangen.
      </td>
    </tr>
   <tr class=\'content\'>
      <td class=\'content\' colspan=2 valign=middle align=center>
        <br><a href=\"http://www.phpeppershop.com/\" target=\"_new\"><img src=\"Bilder/phpepper_logo.gif\" border=\"0\" alt=\"Home of PhPepperShop\"></a>
      </td>
    </tr>
  </table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Einstellungen_Menu_1","
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr>
    <td colspan=\"2\">
        <h3>Administrationsmodus</h3>
    </td>
  </tr>
    <td colspan=\"2\">
        Die Administration des Shops ist in zwei Teile eingeteilt: Betrieb des Shops und Konfiguration des Shops. Dieser Hilfetext stellt eine &Uuml;bersicht &uuml;ber die Funktionen im Administrationsmodus dar.
        <br><br>ACHTUNG: Teile des Administrationstools lassen sich nicht mehr mit dem Netscape in der Version 4.7x bearbeiten. Getestet wurde mit den Browsern Mozilla/Netscape 7.x und mit dem Microsoft<small><sup>®</sup></small>
        Internet Explorer 6.0.
        <BR><BR>
    </td>
  </tr>
  <tr>
    <td colspan=\"2\">
        <hr><h4><i>Betrieb des Shops</i></h4><br>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <B>Artikel</B>
    </td>
    <td>
        Im Artikelmanagement k&ouml\;nnen Sie die Artikel des Shops verwalten. Die Anzahl defaultm&auml\;ssig dargestellter Optionen und Variationen k&ouml\;nnen Sie in den allgemeinen Shop-Einstellungen konfigurieren. Sie k&ouml\;nnen:
        <ul>
          <li>Neuen Artikel einf&uuml\;gen</li>
          <li>Bestehenden Artikel bearbeiten</li>
          <li>Bestehenden Artikel l&ouml\;schen</li>
        </ul><br>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <B>Kategorien</B>
    </td>
    <td>
        Im Kategorienmanagement k&ouml\;nnen Sie die Kategorien und Unterkategorien des Shops verwalten. Sie k&ouml\;nnen:
        <ul>
          <li>Neuen Kategorien / Unterkategorien einf&uuml\;gen</li>
          <li>Bestehenden Kategorien / Unterkategorien umbenennen</li>
          <li>Bestehenden Kategorien / Unterkategorien l&ouml\;schen</li>
          <li>Bestehende Kategorien / Unterkategorien frei verschieben</li>
        </ul><br>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <B>Kunden/<br>Bestellungen</B>
    </td>
    <td>
        Dieser Punkt wurde neu &uuml;berarbeitet. Hier k&ouml;nnen die Kunden verwaltet werden und die Bestellungen der
        einzelnen Kunden. Man kann auch neue Kunden anlegen, Kunden sperren und l&ouml;schen und deren Bestellungen l&ouml;schen.
        <BR><BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <B>Import/<br>Export</B>
    </td>
    <td>
        (Befindet sich zuunterst im Administrations Men&uuml;) Falls dieser Punkt nicht vorhanden ist, so wurde das ev. optional
        erh&auml;ltliche Import-/Exportmodul zum PhPepperShop nicht installiert. Wenden Sie sich dann bitte an die <a href=\"mailto:developers@phpeppershop.com\">
        Entwickler</a>. Mit diesem Modul lassen sich eine nahezu unbeschraenkte Anzahl von Artikeln einf&uuml;gen / updaten und / oder
        exportieren. Das Dateiformat ist jeweils CSV (Komma separierte Daten).
        <BR><BR>
    </td>
  </tr>
  <tr>
    <td colspan=\"2\">
        <hr><h4><i>Konfiguration des Shops</i></h4><br>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <B>Shop-Einstellungen&nbsp;</B>
    </td>
    <td>
        <B>Allgemeine Shop-Einstellungen</B>
        <BR>
        Hier werden die allgemeinen Shop-Einstellungen konfiguriert. Nach der Installation des Shops, sollten Sie hier zuerst alle Einstellungen &uuml\;berpr&uuml\;fen:
        <ul>
          <li>Adressinformationen des Shopbetreibers &auml\;ndern</li>
          <li>Akzeptierte Zahlungsarten konfigurieren</li>
          <li>Masseinheit / W&auml\;hrung festlegen</li>
          <li>Weitere Shop-Konfigurationen vornehmen (`SSL`, Thumbnail-Breite, ...)</li>
          <li>Anzahl Optionen / Variationen zur Artikel Bearbeitung festlegen</li>
          <li>Anzahl gleichzeitig angezeigte Suchresultate festlegen</li>
          <li>Allgemeine Gesch&auml\;ftsbedingungen definieren</li>
          <li>...</li>
        </ul>
        <BR><B>Layout Management</B>
        <BR>
        Hier k&ouml\;nnen Sie den Look des Shops Ihren W&uuml\;nschen anpassen. Sie k&ouml\;nnen:
        <ul>
          <li>Hintergrundfarbe, -bild und die Framegr&ouml\;sse f&uuml\;r jeden Frame individuell einstellen</li>
          <li>Oben links dynamisch entweder ein Logo, den Shopnamen oder gar nichts anzeigen lassen</li>
          <li>Den Administrationsstern ein- und ausschalten</li>
          <li>Das im Shop verwendete Font-Set &auml\;ndern</li>
          <li>F&uuml\;r jeden Frame die Schrifteinstellungen konfigurieren</li>
        </ul>
        <BR><B>MwSt Management</B>
        <BR>
        Hier k&ouml\;nnen Sie die MwSt-Einstellungen f&uuml;r den Shop vornehmen:
        <ul>
          <li>Einstellung: MwSt-pflichtig oder nicht vornehmen</li>
          <li>MwSt-Nummer angeben</li>
          <li>MwSt-S&auml;tze definieren</li>
          <li>MwSt-S&auml;tze Kategorien und Unterkategorien (und somit deren Artikel) zuordnen</li>
          <li>Beschreibungen der MwSt-S&auml;tze editieren</li>
        </ul>
        <BR><B>Bilder (Hintergrund & Shoplogo) hochladen</B>
        <BR>
        Hier k&ouml\;nnen Sie das Logo oben links und die Hintergrundbilder der Frames hochladen. Es werden die Formate GIF, JPG und PNG unterst&uuml\;tzt. Wenn Sie ein JPG Bild mit der Dateiendung JPEG haben, benennen Sie es vor dem Einf&uuml\;gen in JPG um, weil es sonst nicht als JPG-Bild erkannt wird.
        <BR><BR><B>Versandkosteneinstellungen</B>
        <BR>
        Hier k&ouml\;nnen Sie die vom Shop verwendeten Versandkosten konfiguriert werden. Es werden diverse Berechnungsmethoden zur Verf&uuml\;gung gestellt. Sie k&ouml\;nnen hier zudem w&auml\;hlen, welche Zahlungsmittel bei welchen Versandkosten erlaubt sind. Weiter l&auml\;sst sich ein Mindermengenzuschlag und eine Nachnahmegeb&uuml\;hr festlegen. Diese kann aber nur bearbeitet werden, wenn in den allgemeinen Shop-Einstellungen Nachnahme als erlaubte Zahlungsart eingeschaltet wurde.
        <BR><BR><B>Kundenattribute bearbeiten</B>
        <BR>
        Sie k&ouml\;nnen hier w&auml\;hlen, welche Informationen Sie vom Kunden ben&ouml\;tigen. Ein Pizza-Service wird voraussichtlich kein Interesse am Land eines Kunden haben, wohin gegen eine internationale Spedition dieses Kundenattribut ben&ouml\;tigt. Man kann auch selbst definierte Attribute definieren und konfigurieren.
        <BR><BR><B>Datenbank Backup</B>
        <BR>
        Hier k&ouml\;nnen Sie ein Backup ihres Shops anlegen. Weiter kann die Backup-Strategie konfiguiert werden. Auch Restores (Zur&uuml\;cklesen der Backups) werden hier erledigt.
        <BR><BR><B>Shop Konfiguration ansehen</B>
        <BR>
        Unter diesem Men&uuml;punkt verbirgt sich eine Konfigurations&uuml;bersicht. Man sieht auf einen Blick die gesamte Shopkonfiguration. Diese beinhaltet neben der PhPepperShop-Version auch die Konfiguration der Datenbankanbindung und des Webservers.<BR>Weiter ist eine kleine <I>Diagnosefunktion</I> integriert. Sie &uuml;berpr&uuml;ft die Konsistenz der Datenbankkonfiguration und zeigt an, welche Bildformate PHP hier unterst&uuml;tzt.<BR>Zum Schluss wird noch die gesamte PHP-Konfiguration des Webservers ausgegeben (phpinfo-Funktion).
    </td>
  </tr>
</table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Einstellungen_Menu_Artikel","Einen neuen Artike einfügen<BR><BR> Einen schon in der Datenbank bestehenden Artikel bearbeiten<BR><BR> Einen bestehenden Artikel l&ouml;schen");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Settings","
<h3 ALIGN=LEFT>Allgemeine Shopeinstellungen</h3>
<p ALIGN=LEFT>Hier k&ouml;nnen Sie die zentralen Shopeinstellungen
vornehmen.</p>
<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0 FRAME=VOID RULES=NONE>
    <COL WIDTH=16*>
    <COL WIDTH=52*>
    <COL WIDTH=189*>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Adressinformationen Shopbetreiber</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>F&uuml;llen Sie in diesem Abschnitt Ihre Adresse
            ein.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Shopname:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Geben Sie in dieses Feld Ihren Shopnamen ein. Dieser
            wird an verschiedenen Stellen des Shops verwendet und erscheint
            auch auf der E-Mail-Bestellbest&auml;tigung f&uuml;r den Kunden.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>E-Mail Adresse:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Die E-Mail-Adresse m&uuml;ssen Sie unbedingt
            angeben! An diese Adresse werden Ihnen die Bestellungen Ihrer
            Kunden gemailt. Wenn Sie hier keine &Auml;nderunge vornehmen,
            wird es eine Fehlermdelung nach dem E-Mailversand geben.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Mehrwertsteuereinstellungen</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>In den Shopversionen bis und mit v.1.1 konnte man an dieser Stelle alle
            MwSt-Einstellungen vornehmen.<br><br>Seit v.1.2 hat man nun die M&ouml;glichkeit
            pro Artikel einen eigenen MwSt-Satz zu definieren. Diese Funktionalit&auml;t ben&ouml;tigte
            ein umfangreicheres MwSt-Management.
            An dieser Stelle wird das MwSt-Management nur noch ein- und ausgeschaltet, sowie global geltende Angaben gemacht.
            Sobald der Shop als MwSt-pflichtig konfiguriert wurde, erscheint im Hauptmen&uuml; ein Eintrag f&uuml;r das
            MwSt-Management. Alle Einstellungen k&ouml;nnen jetzt dort durchgef&uuml;hrt werden. Neu kann man die Artikelpreise
            seit dieser Shopversion auch exkl. MwSt angeben. Die Mehrwertsteuer wird dann jeweils noch auf den Artikelpreis dazuaddiert.
            <br><br>Sie finden das neue MwSt-Men&uuml; an zweiter Stelle in der Rubrik Allgemeine Shopeinstellungen im Adminmen&uuml;.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Akzeptierte Zahlungsarten</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>In diesem Abschnitt k&ouml;nnen Sie w&auml;hlen,
            welche Zahlungsarten Sie in Ihrem Shop akzeptieren wollen.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Vorauskasse:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Aktivieren Sie diese Checkbox, wenn Sie die
            Bezahlung per Vorauskasse aktivieren wollen.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Konto:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Dieser Text wird dem Kunden in der
            Bestellbest&auml;tigung ausgegeben, falls dieser die Zahlungsart
            Vorauskasse w&auml;hlt. Sie k&ouml;nnen hier zum Beispiel Ihre
            Kontoinformationen eingeben, damit der Kunde das Geld darauf
            einzahlen kann.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Rechnung:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Zahlungsart Rechnung aktiviert/nicht aktiviert</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Lastschrift:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Zahlungsart Lastschrift aktiviert/nicht aktiviert</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Nachnahme:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Zahlungsart Nachnahme aktiviert/nicht aktiviert.
            W&auml;hlt ein Kunde die Zahlungsart Nachnahme, wird ihm ein
            Pauschalpreis f&uuml;r die entstehenden Nachnahmegeb&uuml;hren auf
            den Rechnungsbetrag addiert. Die H&ouml;he des Betrags kann im
            Men&uuml; Versandkosten-Einstellungen fest gesetzt werden.</P>
        </TD>
    </TR>


    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>billBOX:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><a href=\"http://www.billbox.ch/\" target=\"_new\">billBOX</a> bietet eine raffinierte
            Payment Methode an. Der Kunde kann im Shop via Handy einkaufen. F&uuml;r
            PhPepperShop-Betreiber bietet billBOX spezielle Konditionen an, informieren
            Sie sich <a href=\"http://www.phpeppershop.com/index_billbox.html\" target=\"_new\">hier</a>. </P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Kreditkarten:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Aktivieren Sie diese Checkbox, wenn Sie die
            Bezahlung mit Kreditkarten aktivieren wollen.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>&nbsp;&nbsp;&nbsp;- Institut:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Geben Sie den Namen der Kreditkartenanbieter ein.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>&nbsp;&nbsp;&nbsp;- aktiv:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Mit diesen Checkboxen k&ouml;nnen Sie alle
            Kreditkarten getrennt voneinander aktivieren/deaktivieren.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>&nbsp;&nbsp;&nbsp;- Handling:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>F&uuml;r jede Kreditkarte kann eingestellt werden,
            wie die Kreditkartendaten erfasst werden.<BR>
            <B>extern:</B> Die Kreditkartendaten werden von
            einem externen Zahlungsinstitut erfasst.<BR>
            <B>intern:</B> Der Kunde gibt seine
            Kreditkartendaten direkt im Shop ein. Wir raten Ihnen <I>dringend</I>,
            die SSL-Verschl&uuml;sselung einzuschalten! Damit VISA / Eurocard
            einen Mailorder-Vertrag eingehen, muss der Mailversand an Sie
            zudem noch verschl&uuml;sselt werden -> Siehe Kapitel
            Erfahrungen, GNU-PG in der Shopanleitung (Bericht).</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Treuhandzahlung:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Aktivieren Sie diese Checkbox, wenn Sie den Kunden
            die Bezahlung &uuml;ber einen Treuhandservice erlauben wollen.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>&nbsp;&nbsp;&nbsp;- Kosten-<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;verteilung:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Hier wird mit einem Prozentwert (0-100) festgelegt, wieviel % der anfallenden
            Treuhandkosten dem Kunden belastet werden. Wenn man hier z.B. 50 eingibt, so werden die Kosten zu
            gleichen Teilen vom Versender und Kunden getragen. Im Warenkorb, in der Bestellbest&auml;tigung und
            im Bestellungs-E-Mail kommt jeweils nur der vom Kunden getragene Anteil zum Vorschein.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>&nbsp;&nbsp;&nbsp;- Kosten-<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;zuordnung:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Hier wird festgelegt, bis zu welchem Bestellwert wieviele Treuhandskosten anfallen. Man hat
            insgesamt neun Zuordnungsfelder zur Verf&uuml;gung, welche schon einmal mit einem Standardwert vorbelegt
            sind. Beim eingegebenen Bestellwert, sind Versand und Verpackungskosten (auch Nachnahmegeb&uuml;hr und
            Mindermengenzuschlag) nicht miteingerechnet.<br>Wenn der Bestellungswert h&ouml;her ausf&auml;llt, als der
            h&ouml;chste hier definierte Bestellungswert, so werden fiktive, proportional zum h&ouml;chsten hier
            definierten Bestellungswert, berechnete Treuhandskosten verwendet (Bsp. Max. definierter Bestellungswert = 50´000.00
            mit zugeordneten Treuhandskosten von 70.00, aktueller Bestellungswert im Warenkorb = 100´000.00 -> Treuhandskosten =
            140.00). Auf diese Weise sind alle denkbaren Betr&auml;ge mit vern&uuml;nftig berechneten Treuhandkosten abgedeckt.<br>
            Wenn der Shop MwSt pflichtig ist, so gelten f&uuml;r die Treuhandkosten folgende Eingaberegeln: Sind die Preise inkl. MwSt
            definiert, so muss man auch die Treuhandkosten inkl. MwST eingeben. Sind die Preise im Shop exkl. MwSt definiert, so m&uuml;ssen
            auch die Treuhandkosten exkl. MwSt eingegeben werden.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Masseinheiten</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>Zur Einstellung von L&auml;nder- und
            Branchenspezifischen Masseinheiten</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>W&auml;hrung:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Legt fest, welche W&auml;hrung im Shop verwendet
            werden soll. Wenn man den Euro (\&euro) verwenden will, kann
            man auch das Euro-Zeichen (AltGr+E) verwenden.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Gewichtsmass:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Definiert die Gewichtseinheit, die f&uuml;r die
            Versandkostenberechnung nach Gewicht benutzt wird.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Shop-Konfiguration</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>Verschiedene Shopeinstellungen</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Breite der Mini-Bilder in Pixel:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Wenn Sie ein Artikelbild hochladen, wird davon
            automatisch eine kleine Voransicht erstellt, die dann im Shop
            angezeigt wird. Diese sogenannten Thumbnails haben alle die
            gleiche Breite, welche hier festgelegt werden kann.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Maximale Session Zeit:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Jedem Shopbesucher wird beim Betreten des Shops eine
            Identifikationsnummer zugeteilt. Sie wird nach einer gewissen Zeit
            wieder gel&ouml;scht. Diese Zeit k&ouml;nnen Sie hier in Anzahl
            Sekunden einstellen.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>SSL:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Wenn Sie die SSL-Verschl&uuml;sselung aktivieren,
            sind alle Seiten, die ausgegeben werden, nachdem sich der Kunde
            eingeloggt hat, SSL-verschl&uuml;sselt. ACHTUNG!! Der Web-Server
            muss f&uuml;r SSL-Verschl&uuml;sselung eingerichtet sein, damit
            diese Funktion gebraucht werden kann. Wenden Sie sich bei
            Unklarheiten an Ihren Provider.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Anzahl Suchresultate:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>In diesem Feld k&ouml;nnen Sie eingeben, wie viele
            Suchresultate pro Suchresultatseite ausgegeben werden. Wenn auf
            eine Suchanfrage eines Kunden viele Treffer zur&uuml;ckgegeben
            werden, kann er sich einfach durch die Suchresultatseiten
            durchbl&auml;ttern.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Anzahl gleichzeitig angezeigter Artikel einschr&auml;nken:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Ist diese Option aktiviert, so erscheint rechts daneben eine Eingabebox
            in welcher man die Anzahl gleichzeitig angezeigter Artikel einer Kategorie definieren
            kann und ob man die Anzeige oberhalb der Artikel, unterhalb oder an beiden Orten sehen m&ouml;chte.
            Werden danach die Allgemeinen Shopeinstellungen gespeichert, so werden den Kunden
            fortan nur noch die definierte Anzahl Artikel einer Kategorie gleichzeitig angezeigt.<br>
            <i>Hinweis:</i> Wenn sie zuvor direkte Links auf Artikel erstellt haben (siehe Hilfe in der
            Artikeleingabemaske) so m&uuml;ssen diese nach dem Aktivieren dieses Features wieder neu
            angelegt werden, da nun zusaetzlich in diesem Link die Seite angegeben werden muss, in welchem
            sich der Artikel befindet.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Artikel sortieren nach:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Hier kann man festlegen, nach welchen Attributen die aufgelisteten Artikel einer Kategorie
            sortiert angezeigt werden sollen. Man kann z.B. nach Preis sortiert anzeigen. Wenn man ein anderes Sortierkriterium
            als den Artikelnamen w&auml;hlt, so wird automatisch als sekund&auml;res Sortierkriterium der Artikelname hinzu
            gef&uuml;gt (immer aufsteigend). Auf diese Weise hat man innerhalb der bevorzugten Sortierung immer noch ein
            alphabetisches Sortiersystem.</P>
            <P>Weiter kann man noch definieren ob man die Sortierung aufsteigend (a zuerst) oder absteigend (Z zuerst) haben will.
            </P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Zahlenformat der Preise &auml;ndern:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Weil in verschiedenen L&auml;ndern die Zahlen anders formatiert dargestellt werden, muss man je nach Land
            diese Einstellungen anpassen.<br>Um eine Zahl einfach lesbar darzustellen gibt es drei Angaben, welche man definieren muss:
            <ul>
                <li>
                  Tausender Trennzeichen: Dieses Zeichen trennt die Zahl in tausender-Schritten auf (1\'000, 1\'000\'000).
                </li>
                <li>
                  Dezimal Trennzeichen: Hier geht es um die Trennung des Ganzzahlbereichs und des Fliesskommabereichs (10.95)
                </li>
                <li>
                  Anzahl Nachkommastellen: Diese Anzahl gibt die Stellen an, welche nach dem Dezimal Trennzeichen noch angegeben werden. (Kein Runden!)
                </li>
            </ul>
            Die Defaulteinstellungen sind in der Schweiz &uuml;blich. In Deutschland z.B. m&uuml;sste man das Tausender Trennzeichen
            zu einem Punkt und das Dezimaltrennzeichen in ein Komma ab&auml;ndern.
            </P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Tell-a-Friend:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Wenn man Tell-a-Friend einschaltet, so erscheint in der Artikelansicht f&uuml;r jeden Artikel ein Link
            mit welchem Shopkunden ihren Freunden den jeweiligen Artikel via E-Mail empfehlen k&ouml;nnen. Vielen Dank an dieser
            Stelle an Ralph Grad f&uuml;r seine Vorlage.
            </P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Automatische Landeserkennung:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Wenn die automatische Erkennung des Landes vom Shopkunden eingeschaltet ist, so wird beim
            Betreten der Kasse eines noch nicht registrierten Shopkunden ein Zugriff auf die Datenbank von ip-to-country.com
            durchgef&uuml;hrt. Dieser kostenlose Service erlaubt es nummerische IP-Adressen L&auml;ndern zuzuordnen.<br>
            Da dieser Zugriff etwas Zeit kostet, kann es je nach Performance des eigenen Webservers zu Verz&ouml;gerungen
            bei der Darstellung kommen, wir haben aus Sicherheitsgr&uuml;nden dieses Feature optional eingebaut und per
            default ausgeschaltet.<br>
            Als Anmerkung sei hier noch erw&auml;hnt, dass die Erkennung der L&auml;nder nur etwa zu 98% gelingt. Dies weil
            gewisse Leute ueber Proxy Server in anderen L&auml;ndern surfen und andererseits, weil gewisse IP-Eintr&auml;ge
            auch veraltet sein k&ouml;nnen. Die Datenbank wird monatlich erneuert. Man kann auch auf den Fernzugriff verzichten
            indem man die Datenbank herunterl&auml;dt und lokal benutzt.
            </P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>H&auml;ndlermodus:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Wenn man seinen Shop f&uuml;r einen geschlossenen Kundenkreis anbieten will, wie es viele Grossisten und H&auml;ndler tun,
            so muss man den H&auml;ndlermodus einschalten. Wenn der H&auml;ndlermodus eingeschaltet ist, wird jeder (potentielle) Kunde
            vor dem Betreten des Shops nach einem Login und einem Passwort gefragt. Hat er keinen, vorher vom Shopadministrator angelegten
            Useraccount, so kann der Kunde den Shop <i>nicht</i> betreten. ACHTUNG: Bevor man den Shop im H&auml;ndlermodus betreibt, sollte man
            tunlichst vorher einen eigenen Useraccount f&uuml;r sich selbst anlegen.<br>Weiter ist zu beachten, dass die Indizierung f&uuml;r
            Suchmaschinen bei eingeschaltetem H&auml;ndlermodus deaktiviert ist.
            </P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>H&auml;ndlermodus Login Text:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>In diesem Textfeld kann man den Begr&uuml;ssungstext angeben, welche Kunden zu Gesicht bekommen, wenn sie
            den Shop betreten wollen. HTML-Tags werden vollumf&auml;nglich ausgewertet, man kann also auch Links zu weiteren Seiten benutzen
            (z.B. ein eigens verfasstes Anmeldeformular, ...). Es k&ouml;nnen maximal 64kByte Textdaten verwendet werden (MySQL Datenbank).
            </P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT>&nbsp;
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>&nbsp;
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>&nbsp;
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Artikel bearbeiten</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>In dieser Eingabegruppe k&ouml;nnen sie bestimmen,
            wie viele Options-/Varianten-Eingabefelder bei der
            Bearbeitung/Erstellung eines Artikels angezeigt werden sollen.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Anzahl Optionsfelder:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Anzahl Optionsfelder, die mindestens ausgegeben
            werden, wenn der Administrator einen Artikel neu erstellt oder
            bearbeitet.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Anzahl Variationsfelder:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Anzahl Variationsfelder, die mindestens ausgegeben
            werden, wenn der Administrator einen Artikel neu erstellt oder
            bearbeitet.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>leere Optionsfelder:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Anzahl der leeren Optionsfelder, die beim Bearbeiten
            des Artikels ausgegeben werden, wenn ein Artikel schon viele
            Optionen hat.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>leere Variationsfelder:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Anzahl der leeren Variationsfelder, die beim
            Bearbeiten des Artikels ausgegeben werden, wenn ein Artikel schon
            viele Varianten hat.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Anzahl Variationsgruppen:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Hier kann man die Anzahl der in der Artikeleingabemaske
            verf&uuml;gbaren Variationsgruppeneingabefelder definieren. Mit Variationsgruppen
            kann man komfortabel Variationen in einzelnen Gruppen, passend benamst, zusammenfassen.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Anzahl Texteingabefelder:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Hier wird die Anzahl gleichzeitig verf&uuml;gbarer Texteingabefelder pro Artikel
            definiert. Die hier angegebene Anzahl definiert die Anzahl konfigurierbarer Texteingabefelder in
            der Artikeleingabemaske. Kunden k&ouml;nnen auf diese unkomplizierte Weise preisunabh&auml;ngige
            Informationen angeben. Diese Daten werden im Warenkorb, in der Bestellbest&auml;tigung und im Bestellungs-
            E-Mail aufgelistet.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT>Gesamtpreis auf 0.05 runden:</P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT>Ist diese Funktionalit&auml;t eingeschaltet (H&auml;ckchen sichtbar), so wird der Gesamtpreis (Total
            einer Bestellung) auf 0.05 gerundet. Dies ist insbesondere in der Schweiz usus. Diese Funktion arbeitet dennoch
            W&auml;hrungsunabh&auml;ngig.</P>
        </TD>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Allgemeine Gesch&auml;ftsbedingungen</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>In dieses Textfeld k&ouml;nnen Sie Ihre Allgemeinen
            Gesch&auml;ftsbedingungen einf&uuml;gen. Diese werden dem Kunden
            zur Akzeptierung angezeigt, bevor die Bestellung abgeschickt
            werden kann. HTML-Tags, die sich im Text befinden, werden
            interpretiert. Sie k&ouml;nnen zum Beispiel einen Teil des Textes
            Fett formatiert ausgeben.</P>
        </TD>
    </TR>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Anmerkung zur Impressumspflicht in Deutschland</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>In Deutschland ist es seit dem 1. Januar 2002 <i>Pflicht</i> auf jeglicher
            Homepage leicht erkennbar, unmittelbar erreichbar und st&auml;ndig verf&uuml;gbar ein Impressum
            anzubieten. Da ein Shopsystem grunds&auml;tzlich nicht die Portalseite einer Onlinepr&auml;senz darstellt
            wurde darauf Verzichtet ein dynamisch erzeugtes Impressum einzubinden.<br>Am einfachsten erstellt man
            eine statische HTML-Seite mit allen ben&ouml;tigten Angaben und verlinkt sie mit der Portalseite.
            </P>
        </TD>
    </TR>
    </TR>
    <TR VALIGN=TOP>
        <TD WIDTH=6%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=20%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
        <TD WIDTH=74%>
            <P ALIGN=LEFT><BR>
            </P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT><I><B>Anmerkung zur MwSt-Nummer und UIN in Deutschland</B></I></P>
        </TD>
    </TR>
    <TR>
        <TD COLSPAN=3 WIDTH=100% VALIGN=TOP>
            <P ALIGN=LEFT>In Deutschland muss man seit dem 1. Juli 2002 zwei MwSt-Nummern angeben.
            Die eine f&uuml;r Gesch&auml;fte mit dem Rest der Welt (MwSt.-Id. Nummer), die andere
            wird vom &ouml;rtlichen Finanzamt vergeben und soll dem Umsatzsteuerbetrug vorbeugen.<br>
            Da die eine via Admintool zu definierende MwSt-Nummer (in der Schweiz gibts nur eine...)
            in der Datenbank als VARCHAR(127)-Typ implementiert ist, kann man hier ohne Probleme auch
            einen bis zu 127-Zeichen langen Text eingeben. Wir schalgen deshalb vor, hier einfach
            folgenden Eintrag zu machen:
            <blockquote>(MwSt-ID:127/128/129&nbsp;&nbsp;&nbsp;UIN:DE16431200)</blockquote>
            (oder wie auch immer diese beiden Nummern genau heissen / aussehen). Bitte nicht vergessen,
            dass im E-Mail noch die Bezeichnung \'MwSt-Nummer:\' vorneweg erscheint.<br> Wo erscheint
            dieser MwSt-Nummer-Eintrag &uuml;berall: Diese Nummer wird nur im Admintool zu Editierzwecken
            angezeigt und im jeweiligen Bestellungs-E-Mail an die Kunden resp. den Administrator &uuml;bergeben.
            </P>
        </TD>
    </TR>
</TABLE>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Einstellungen_Menu_Kategorien","
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>
<H3>Kategorienmanagement</H3>
<H4>Kategorien und ihre Unterkategorien</H4>
<P>Es gibt Kategorien und Unterkategorien. Eine Kategorie kann
beliebig viele Unterkategorien enthalten. Unterkategorien k&ouml;nnen
aber keine weiteren Unterkategorien enthalten. Man hat also eine
zweistufige Hierarchie.</P>
<H4>Funktionen f&uuml;r Kategorien und Unterkategorien</H4>
<TABLE BORDER=1 BORDERCOLOR=\"#000000\" CELLPADDING=4 CELLSPACING=0 RULES=GROUPS>
    <COL WIDTH=59*>
    <COL WIDTH=197*>
    <TBODY>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P ALIGN=LEFT STYLE=\"font-weight: medium\">Kategorie:</P>
            </TD>
            <TD WIDTH=77%>
                <P ALIGN=LEFT>- neue Kategorie erstellen</P>
            </TD>
        </TR>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P ALIGN=LEFT><BR>
                </P>
            </TD>
            <TD WIDTH=77%>
                <P ALIGN=LEFT>- neue Unterkategorie erstellen</P>
            </TD>
        </TR>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P ALIGN=LEFT><BR>
                </P>
            </TD>
            <TD WIDTH=77%>
                <P ALIGN=LEFT>- Kategorie verschieben</P>
            </TD>
        </TR>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P ALIGN=LEFT><BR>
                </P>
            </TD>
            <TD WIDTH=77%>
                <P ALIGN=LEFT>- Eigenschaften der Kategorie bearbeiten</P>
            </TD>
        </TR>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P ALIGN=LEFT><BR>
                </P>
            </TD>
            <TD WIDTH=77%>
                <P ALIGN=LEFT>- Kategorie l&ouml;schen</P>
            </TD>
        </TR>
    </TBODY>
    <TBODY>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P STYLE=\"font-weight: medium\">Unterkategorie:</P>
            </TD>
            <TD WIDTH=77%>
                <P>- Unterkategorie innerhalb Kategorie verschieben</P>
            </TD>
        </TR>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P><BR>
                </P>
            </TD>
            <TD WIDTH=77%>
                <P>- Unterkategorie in eine andere Kategorie verschieben</P>
            </TD>
        </TR>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P><BR>
                </P>
            </TD>
            <TD WIDTH=77%>
                <P>- Eigenschaften der Unterkategorie bearbeiten</P>
            </TD>
        </TR>
        <TR VALIGN=TOP>
            <TD WIDTH=23%>
                <P><BR>
                </P>
            </TD>
            <TD WIDTH=77%>
                <P>- Unterkategorie l&ouml;schen</P>
            </TD>
        </TR>
    </TBODY>
</TABLE>
<P>Damit man diese Funktionen benutzen kann, muss man einfach auf den
Namen der Kategorie / Unterkategorie klicken, welche man bearbeiten
will. Es wird danach ein JavaScript Men&uuml; mit dem m&ouml;glichen
Funktionen eingeblendet.</P>
<H4>Netscape 4.7x - Eingeschr&auml;nkte Darstellungsqualit&auml;t</H4>
<P>Netscape 4.7x hatte massiv Schwierigkeiten mit diesem Men&uuml;.
Wir mussten deshalb eine Browserweiche einbauen, so dass man das
Kategorienmanagement &uuml;berhaupt benutzen kann. Es ist hier nicht
optimal. Wir empfehlen den Einsatz von Mozilla / Netscape 6.x oder
den Microsoft&reg; Internet Explorer.</P>
<H4>L&ouml;schen einer Kategorie / Unterkategorie</H4>
<P>Bevor sie eine Kategorie oder eine Unterkategorie endg&uuml;ltig
l&ouml;schen, k&ouml;nnen Sie entscheiden, was mit den Artikeln in
der zu l&ouml;schenden Kategorie / Unterkategorie geschehen soll. Man
kann entweder alle Artikel mit l&ouml;schen oder diese in die
<I>Kategorie Nichtzugeordnet</I> ablegen. Von dort aus k&ouml;nnen
diese Artikel &uuml;ber das Artikelmanagement wieder einer anderen
Kategorie zugeordnet werden.</P>
</td></tr></table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Versandkosten","
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>
<H3>Versandkosten Einstellungen</H3>
<P>In diesem Men&uuml;punkt k&ouml;nnen Sie sehr flexibel einstellen,
welche zus&auml;tzlichen Geb&uuml;hren Ihren Kunden verrechnet
werden.</P>
<H4>Berechnungsmethoden</H4>
<P>Sie haben drei M&ouml;glichkeiten, die Versandkosten zu berechnen:</P>
<OL>
    <LI><P><B>nach Preis</B>: Die H&ouml;he der Versandkosten wird aus
    der Summe des Gesamtpreises aller Artikel, die der Kunde im
    Warenkorb hat, berechnet. Es k&ouml;nnen zwischen 1 und 999 verschiedene
    Preisintervalle eingegeben werden, f&uuml;r die jeweils ein
    eigenst&auml;ndiger Versandkostenbetrag definiert werden kann.</P>
    <LI><P><B>nach Gewicht</B>: Die H&ouml;he der Versandkosten wird aus
    der Summe des Gewichts aller Artikel, die der Kunde im Warenkorb
    hat, berechnet. Es k&ouml;nnen zwischen 1 und 999 verschiedene Gewichtsintervalle
    eingegeben werden, f&uuml;r die jeweils ein eigenst&auml;ndiger
    Versandkostenbetrag definiert werden kann.</P>
    <LI><P><B>nach Pauschale</B>: Der Kunde bezahlt immer den gleichen
    Betrag f&uuml;r Porto und Verpackung, egal was und wie viel er
    einkauft.</P>
</OL>
<P>Bei der Versandkostenberechnung nach Gewicht und Preis k&ouml;nnen
Sie f&uuml;r jedes Intervall bestimmen, welche
Bezahlungsm&ouml;glichkeiten akzeptiert werden. Zum Beispiel k&ouml;nnen
Sie die Bezahlung per Kreditkarte erst ab SFr. 50.-- erlauben, oder
Bestellungen ab SFr. 300.-- nicht mehr mit einer Rechnung ausliefern.</P>
<P>Die jeweils aktivierte Berechnungsmethode ist durch einen
vorangestellten Pfeil zu erkennen.</P>


<H4>Angezeigter Rechnungsposten im Warenkorb</H4>
<P>In diesem Eingabefeld k&ouml;nnen Sie den Text editieren, mit welchem
die Versandkosten im Warenkorb und Bestellungs-E-Mail betitelt werden.</P>

<H4>Anzahl Berechnungs-Intervalle</H4>
<P>Sie k&ouml;nnen hier mit einer Zahl von 1 bis 999 angeben, wieviele
Preis/Gewichts-Intervalle Sie f&uuml;r ihre Versandkostenberechnung haben
m&ouml;chten. Bitte beachten Sie zwei Bedingungen:<BR><I>1.)</I> Die von Ihnen
angegebene Anzahl Intervallen erscheint erst <I>nach</I> dem Speichern<BR>
<I>2.)</I> Diese Angabe macht nat&uuml;rlich nur Sinn, wenn die Versandkosten
entweder nach Preis oder nach Gewicht berechnet werden. Bei einer
Berechnung nach Pauschale gibt es keine Intervalle.</P>


<H4>Keine Versandkosten mehr berechnen ab Betrag XYZ</H4>
<P>V&ouml;llig unabh&auml;ngig von den Versandkosteneinstellungen
k&ouml;nnen Sie zus&auml;tzlich einen Rechnungsbtrag festlegen, bei
dessen &Uuml;berschreitung keine Versandkosten mehr berechnet werden.</P>
<H4>Mindermengenzuschlag</H4>
<P>Bei Unterschreiten eines gewissen Totalbetrags besteht die
M&ouml;glichkeit, diesem einen Mindermengenzuschlag hinzuzuf&uuml;gen.
H&ouml;he und Betrag sind frei w&auml;hlbar.</P>
<H4>Nachnahmegeb&uuml;hr definieren</H4>
<P>Letzer Einstellungspunkt in diesem Men&uuml; ist die H&ouml;he
einer eventuell anfallenden Nachnahmegeb&uuml;hr. Diese kann nur
eingegeben werden, wenn Sie die Bezahlungsart &#132;Nachnahme&#147;
aktiviert haben. Die Nachnahmegeb&uuml;hr wird auf der Bestellung
separat aufgelistet.</P>
<P><BR></P>
</td></tr></table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Bestellung","
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>
<H3>Bestellungsmanagement</H3>
<P>Hier hat man die M&ouml;glichkeit, abgeschlossene, noch nicht
gel&ouml;schte Bestellungen abzurufen, sie zu l&ouml;schen und
Kundenadressen (Lieferadressen) zu mutieren - eigentlich eine
Verwaltung von abgeschlossenen Bestellungen.</P>
<P>Eine Bestellung gilt als abgeschlossen, wenn der Kunde die
Bestellbest&auml;tigung gesendet bekommt. Dies geschieht nach dem
Akzeptieren der allgemeinen Gesch&auml;ftsbedingungen.</P>
<H4>Allgemeines</H4>
<P>Der Shopadministrator kann das Bestellungsmanagement in den
allgemeinen Shop-Einstellungen ein- oder ausschalten. Wenn das
Bestellungsmanagement ausgeschaltet ist erscheint dieses Men&uuml;
hier nicht mehr. Es gibt viele kleinere Shops, bei welchen der
Shopadministrator die ganze Bestellungsverwaltung &uuml;ber die
E-Mails abwickelt, deshalb diese M&ouml;glichkeit.</P>
<P>Man kann eine Bestellung &uuml;ber ihre Referenznummer (auf dem
E-Mail ersichtlich) suchen, oder aber alle abgeschlossenen
Bestellungen anzeigen lassen.</P>
<H4>Alle abgeschlossenen Bestellungen verwalten</H4>
<P>Wenn man diesen Link anklickt, erh&auml;lt man eine Tabelle,
welche einem alle abgeschlossenen Bestellungen anzeigen. Man sieht
ihr Erstellungsdatum, ihre Besitzer (Kunden) und ihre Referenznummer.</P>
<P><I>Sortieren</I> kann man die Liste nach Bestelldatum oder
Referenznummer. Dies macht man indem man entweder auf Bestelldatum
oder auf Referenz Nr. klickt (Titel der Tabelle).</P>
<P>Um eine Bestellung zu bearbeiten, klickt man einfach auf einen
Eintrag in der entsprechenden Zeile der Tabelle (z.B. den Namen des
Kunden).</P>
<H4>Bestellungen bearbeiten</H4>
<P>Wenn man eine Bestellung bearbeitet hat man alle Informationen des
Kunden der Bestellung und den gesamten Warenkorbinhalt vor sich. Der
Warenkorb kann nicht mutiert werden, die Kundeneinstellungen aber
sehr wohl. Mit Speichern wird der ge&auml;nderte Datensatz
gespeichert, mit L&ouml;schen wird die Bestellung (unwiderruflich,
ohne weitere Aufforderung) gel&ouml;scht.</P>
</td></tr></table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Kunde","
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><H3>Kundenattribute bearbeiten</H3>
<P>Sie k&ouml;nnen bestimmen, welche Adressinformationen Sie von
Ihren Kunden erhalten m&ouml;chten und welche davon unbedingt
ausgef&uuml;llt werden m&uuml;ssen, damit die Bestellung abgesendet
werden kann.</P>
<H4>Vordefinierte Felder</H4>
<P>Sie haben 14 vordefinierte Felder zur Verf&uuml;gung, die sie nach
Belieben anzeigen (Checkbox &#132;<B>verwenden</B>&#147;) und
&uuml;berpr&uuml;fen (Checkbox &#132;<B>pr&uuml;fen</B>&#147;) lassen
k&ouml;nnen. Die vordefinierten Felder werden immer zum Kundendatensatz gespeichert.</P>
<P>Ist die &Uuml;berpr&uuml;fung eingeschaltet wird das entsprechende
Feld mit einem <B>*</B> als obligatorisch markiert und es muss ein
Text eingegeben werden (JavaScript &Uuml;berpr&uuml;fung).</P>
<H4>Zusatzfelder</H4>
<P>Zus&auml;tzlich k&ouml;nnen bis zu vier <I>Zusatzfelder</I>
aktiviert werden, deren Beschreibungstext Sie selbst bestimmen
k&ouml;nnen.</P>
<P>Diese Felder besitzen eine zus&auml;tzliche Checkbox &#132;<B>speichern</B>&#147;.
Ist diese aktiviert, wird das Feld in den Kundendatensatz
aufgenommen. Die in diesem Feld eingegebenen Daten werden demzufolge
mit den anderen Adressdaten des Kunden gespeichert. Meldet sich der
Kunde beim n&auml;chsten Besuch Ihres Shops mit seinem Benutzernamen
und Passwort an, wird dieses Feld automatisch wieder ausgef&uuml;llt.
Dies ist f&uuml;r Angaben w&uuml;nschenswert, die sich nie, oder
selten &auml;ndern (z.B. Kundennummer, Geburtsdatum,..).</P>
<P>Falls Sie das Feld f&uuml;r Informationen verwenden, die sich bei
jeder Bestellung &auml;ndern (z.B. gew&uuml;nschter Liefertermin,..),
w&auml;hlen Sie die Checkbox &#132;speichern&#147; nicht an! Die
Daten des entsprechenden Feldes werden in diesem Fall zur Bestellung
anstatt zum Kunden gespeichert und verfallen sobald die Bestellung
gel&ouml;scht wird.</P>
</td></tr></table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Artikel","
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr>
    <td colspan=\"2\">
        <h3>Artikeldaten bearbeiten / einf&uuml;gen</h3>
    </td>
  </tr>
  <tr>
    <td colspan=\"2\">
        <P>Hier k&ouml;nnen Sie die Daten des Artikels eingeben oder ver&auml;ndern.</P>
        <BR>
        <hr>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>&Auml;hnliche Artikel schneller erfassen</h4>
    </td>
    <td>
        <P>Um &auml;hnliche Artikel schneller erfassen zu k&ouml;nnen wurde die Artikelbearbeitungsmaske um die Funktion \'Als neuen Artikel speichern\' (ganz unten in der Eingabemaske) erweitert.<br>Wenn man hier ein H&auml;kchen setzt, so wird der zuvor zum Bearbeiten ge&ouml;ffnete Artikel mit den bearbeiteten Werten als <i>neuer</i> Artikel angelegt - dies spart die Zeit um redundante Eingaben nicht mehr immer wieder eintippen zu m&uuml;ssen.</P>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>Mehr Felder (Option/Variation)</h4>
    </td>
    <td>
        <P>Um mehr Felder f&uuml;r Optionen / Variationen zu erhalten, gehen Sie in die allgemeinen Shop-Einstellungen und konfigurieren Sie dort ihre gew&uuml;nschte Anzahl Felder (Punkt: \'Artikel bearbeiten').</P>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>Kategorien und Artikelbild zuweisen</h4>
    </td>
    <td>
        <P>In dieser Maske, k&ouml;nnen Sie nur die Daten des Artikels bearbeiten. Die Zuweisung zu einer oder mehrerer Kategorien geschieht erst in der n&auml;chsten Maske (Klick auf Weiter). Um dem Artikel dann noch ein Bild zuzuweisen, gibt es noch eine dritte Eingabemaske. Damit ist ein Artikel vollst&auml;ndig erfasst.</P>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>Artikelbild<BR>(oben rechts)</h4>
    </td>
    <td>
        <P>Um ein Artikelbild oben rechts sehen zu k&ouml;nnen, m&uuml;ssen Sie dem Artikel zuerst eines zuweisen. Dies geschieht in der &uuml;bern&auml;chsten Seite.<br>
        Man kann Artikelbilder auch via FTP hochladen. Weitere Infos hierzu finden Sie in der <a href=\"../ProdukteBilder/info.txt\">info.txt</a> Datei im shop/ProdukteBilder Unterverzeichnis</P>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>Masseinheiten</h4>
    </td>
    <td>
        <P>Wenn Sie eine andere Masseinheit f&uuml;r das Gewicht und / oder eine andere W&auml;hrung einstellen wollen, k&ouml;nnen Sie das in den allgemeinen Shop-Einstellungen unter 'Masseinheiten' vornehmen.</P>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>Gewicht eines Artikels</h4>
    </td>
    <td>
        <P>Das Gewicht eines Artikels wird dem Shopbenutzer nicht angezeigt. Es wird dazu verwendet die Versandkosten nach Gewicht zu berechnen. Wollen Sie das Gewicht eines Artikels trotzdem anzeigen, empfehlen wir Ihnen, dieses doch im Beschreibungstext zu erw&auml;hnen.</P>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>Artikel Nr.</h4>
    </td>
    <td>
        <P>Die Artikel Nr. wird vom Shopsystem nicht benutzt und kann von Ihnen frei definiert werden. Dies ist z.B. praktisch um die Artikelbezeichnung eines H&auml;ndlers mit zu speichern.</P>
        <BR>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <h4>Artikelpreis = 0</h4>
    </td>
    <td>
        <P>Falls der Artikelpreis = 0 ist, so wird die Darstellung des Artikels f&uuml;r den Kunden leicht ver&auml;ndert. Der eigentliche Artikelpreis (=0) wird ausgeblendet. Daf&uuml;r wird die Darstellung der Varianten so abge&auml;ndert, dass anstatt Aufpreis Preis steht. Die Preise werden nun Fett gedruckt in einer Tabelle gegliedert dargestellt. Dieses Feature wurde eingerichtet, damit man von einem \'Grund-Artikel\' mehrere Varianten verkaufen kann, ohne den Grund-Artikel alleine zu verkaufen.</P>
        <BR>
    </td>
  </tr>
    <tr>
    <td valign=\"top\">
        <h4>Variationsgruppen</h4>
    </td>
    <td>
        <P>Jede Variation kann einer Variationsgruppe zugeordnet werden. Pro Variationsgruppe ist dann im Shop genau eine Auswahl m&ouml;glich. Die Anzahl der maximal verwendeten Variationsgruppen kann in den Shopeinstellungen eingestellt werden. Typische Variationsgruppen sind zum Beispiel: Farbe, Gr&ouml;sse, L&auml;nge, Spannung, Durchmesser,... F&uuml;r jede Variationsgruppe kann gew&auml;hlt werden, ob sie im Shop als Radiobuttons (kleine runde Kreise zum Anklicken) oder als Dropdown-Feld (man sieht nur die erste Variation) dargestellt wird. </P>
        <BR>
    </td>
  </tr>
  </tr>
    <tr>
    <td valign=\"top\">
        <h4>Texteingabefelder</h4>
    </td>
    <td>
        <P>Wenn in den Allgemeinen Shopsettings mindestens ein Texteingabefeld aktiviert wurde, so kann man pro Artikel (mindestens) ein vom K&auml;ufer ausf&uuml;llbares Textfeld einblenden lassen. Man kann den Namen des Textfeldes wie auch seine L&auml;nge und die Feldh&ouml;he definieren.</P>
        <BR>
    </td>
  </tr>
    <tr>
    <td valign=\"top\">
        <h4>MwSt-Satz</h4>
    </td>
    <td>
        <P>Seit der Shopversion v.1.2 kann man jedem Artikel einen eigenen (im Mehrwertsteuer Management vordefinierten) MwSt-Satz zuweisen. Per Default wird der von der Kategorie des Artikels vordefinierte MwSt-Satz verwendet.</P>
        <BR>
    </td>
  </tr>
  </tr>
    <tr>
    <td valign=\"top\">
        <h4>Via Link direkt auf Artikel springen</h4>
    </td>
    <td>
        <P>Um von irgendwoher vom Web direkt einen Artikel im Shop anzeigen zu lassen, muss man einen Hyperlink zu ebendiesem haben. Dieser Link kann ganz einfach erstellt werden, indem man in der Shop-Artikelsuche den zu verlinkenden Artikel sucht und bei seinem Knopf \'Artikel anzeigen\' den darunterliegenden Link kopiert (meistens Rechtsklick und Verkn&uuml;pfung kopieren / Copy Link Location w&auml;hlen). Hier den Link / die Links f&uuml;r den aktuellen Artikel:</P>
    </td>
  </tr>
</table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Backup","
<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>
<H3>Backup der Shop Datenbank</H3>
<P>Das automatisierte Datenbank-Backup erlaubt es ein Backup-Set
anzulegen, welches optional automatisch aktualisiert wird. Wir
m&ouml;chten hier ausdr&uuml;cklich darauf hinweisen, dass es sich
hierbei um ein Datenbank-Backup handelt. <K>Seit der Shopversion v.1.1
werden die Artikelbilder im Dateisystem gespeichert, dies hat viele
Performance-Vorteile (v.a. bei Verwendung von MySQL). Deshalb sind
ab sofort die <B>Artikelbilder</B> nicht mehr im Datenbank-Backup
enthalten!</K> Die <B>User-Buttons</B> und <B>Hintergrundbilder</B> werden
mit diesem Backup auch NICHT gespeichert (liegen im Dateisystem)!
Die Bilder m&uuml;ssen also immer noch von Hand gespeichert werden (siehe
Manuals: \'Backup von Hand\').</P>
<P>Ein Hinweis noch: Sie m&uuml;ssen die gemachten Einstellungen immer zuerst
speichern, erst danach ein Backup erstellen. Es reicht nicht nur die Werte in den
Eingabefelder zu &auml;ndern.</P>
<H4>Einstellungen</H4>
<P>In den Einstellungen kann man den Umfang des Backup-Sets und das
Backup-Intervall festlegen. Optional l&auml;sst sich noch die
ZIP-Komprimierung aktivieren. <I>Die ZIP-Komprimierung funktioniert
allerdings nur wenn die ZLib-Library eingebunden ist</I>.</P>
<P>Da ein Shop 24h offen ist und 7 Tage in der Woche erreichbar ist,
empfehlen wir ein Backup-Set von sieben Dateien anzulegen und das
Backup-Intervall auf 24 Stunden einzustellen. Wenn es m&ouml;glich
ist die ZIP-Komprimierung zu aktivieren, sollte dies auch gemacht
werden.</P>
<H4>Automatisierung</H4>
<P>Wenn man von PHP und Automatisierung redet, gibt es grunds&auml;tzlich
ein Problem: Man hat das Problem, dass man ein programmiertes
Ereignis nicht einfach nach Ablauf einer gewissen Frist aufrufen
kann. Wir bieten deshalb unter dem Men&uuml;punkt \'Automatisierung\'
drei verschiedene Einstellungsm&ouml;glichkeiten:</P>
<P STYLE=\"margin-left: 0.5cm; text-indent: -0.5cm\"><SPAN STYLE=\"font-style: normal\">1.    </SPAN><B><I>Automatisiertes
Backup aktivieren:</I></B> Hierbei wird der Datei <FONT FACE=\"Courier New, monospace\">index.php</FONT>
ein Backup Aufruf eingef&uuml;gt (include von
<FONT FACE=\"Courier New, monospace\">&lt;shopdir&gt;/shop/Admin/ADMIN_backup.php</FONT>).
Diese Datei &uuml;berpr&uuml;ft ob es Zeit ist ein weiteres Backup
anzulegen und tut dies falls notwendig auch gleich.</P>
<P STYLE=\"margin-left: 0.5cm\">Bei diesem Ansatz gibt es eine
Sicherheitsl&uuml;cke. Es ist der einzige Ort im Shop bei welchem wir
mit unserer Sicherheits-Policy brechen mussten. Ein USER-Script ruft
ein ADMIN-Script auf. Schliesslich ist das Anlegen eines
Datenbank-Backups Administrator-Sache. Dies ist also ein Kompromiss
zwischen Usability und Security. Wir empfehlen hier dringend, dass
man wenn immer m&ouml;glich einen CRON-Job einrichten sollte, welcher
dieses Backup periodisch aufruft (Aufruf: <FONT FACE=\"Courier New, monospace\">php
ADMIN_backup.php</FONT>).</P>
<P STYLE=\"margin-left: 0.5cm\">Der Aufruf des Backups in der index.php
Datei ist \'atomar\'. Es handelt sich um einen einzigen Aufruf welcher
entweder komplett ausgef&uuml;hrt wird oder nicht aufgerufen wird.</P>
<P STYLE=\"margin-left: 0.5cm; text-indent: -0.5cm\"><SPAN STYLE=\"font-weight: medium\"><SPAN STYLE=\"font-style: normal\">2.
</SPAN></SPAN><B><I>Backup wurde als CRON-Job eingerichtet:</I></B>
Dies ist wohl die eleganteste Methode des automatisierten Backups und
zugleich auch die sicherste. Man muss jetzt per Telnet oder besser
SSH einen CRON-Job einrichten. Dieser muss lediglich im
Backup-Intervall die Datei <FONT FACE=\"Courier New, monospace\">ADMIN_backup.php</FONT>
aufrufen (im Admin Unterverzeichnis). Das Backup-Set wird automatisch
gepflegt.</P>
<P STYLE=\"margin-left: 0.5cm; text-indent: -0.5cm\"><SPAN STYLE=\"font-style: normal\">3.
</SPAN><B><I>Kein automatisches Backup:</I></B> Es wird kein
automatisiertes Backup vorgenommen. Jedes Backup muss von Hand
angelegt werden. Wer keine Kompromisse betreffend der Security
eingehen will aber auch keine CRON-Jobs einrichten darf (manche
Provider verbieten dies), dem raten wir zu dieser Einstellung.</P>
<H4>Dateinamen</H4>
<P>Der Datenbank-Backup legt das aktuelle Backup in der Datei
<FONT FACE=\"Courier New, monospace\">0.sql[.gz]</FONT> ab. Gibt es ein
Backup-Set gr&ouml;sser als eins, so wird der Dateiname der schon
existierenden Backups um eins dekrementiert. Das neueste Backup ist
also immer 0. Das &auml;lteste Backup bei einem Backup-Set von 5 w&auml;re
also <FONT FACE=\"Courier New, monospace\">4.sql[.gz]</FONT>.</P>
<P>Es wird immer zuerst umbenannt und danach das neue Backup
<FONT FACE=\"Courier New, monospace\">0.sql[.gz]</FONT> erzeugt.</P>
<H4>phpMyBackup</H4>
<P>Die Grundlage unserer automatisierten Backupl&ouml;sung stammt aus
dem Programm phpMyBackup von Holger Mauermann
(<A HREF=\"mailto:mauermann@nm-service.de\">mauermann@nm-service.de</A>).
Er hat dieses vielversprechende PHP-Script f&uuml;r den Backup und
Restore einer MySQL Datenbank geschrieben. Wir haben als Grundlage
phpMyBackup in der Version 0.4 Beta benutzt. Das Script kann unter
<A HREF=\"http://www.m-tecs.net/?a=products&b=pmb&c=de\">http://www.m-tecs.net/</A>
gefunden werden. Wir m&ouml;chten Holger an dieser Stelle nochmals
recht herzlich f&uuml;r die Erlaubnis, sein Programm benutzen zu
d&uuml;rfen, danken.</P>
<H3>Restore eines Datenbank-Backups</H3>
<P><FONT SIZE=3>Wenn man im Backup-Men&uuml; auf \'Restore\' klickt
kann ein Datenbank-Backup angesehen oder zur&uuml;ck gelesen werden.
Mit \'view/download\' kann man die SQL-Datei ansehen. Achtung: Wenn
viele Bilder in der Datenbank sind, so werden diese mit &uuml;bertragen!</FONT></P>
<H4>Ernstfall &#150; Ablauf</H4>
<P><FONT SIZE=3>Wenn Sie Ihren Shop aus irgendwelchen Gr&uuml;nden
verloren haben und ein Restore ihrer Shop-Datenbank machen wollen
m&uuml;ssen Sie wie folgt vorgehen:</FONT></P>
<OL>
    <LI><P><FONT SIZE=3>Speichern Sie ihr Datenbank Backup an einem
    sicheren Ort (haben Sie wahrscheinlich schon gemacht).</FONT></P>
    <LI><P><FONT SIZE=3>Erstellen Sie ein Backup ihrer selbst erstellten
    Hintergrundbilder und Shopbuttons (siehe Manuals: \'Backup von
    Hand\').</FONT></P>
    <LI><P STYLE=\"margin-bottom: 0cm\"><FONT SIZE=3>Schreiben Sie sich
    folgende Angaben auf. Vielleicht haben Sie das schon nach der
    Installation des Shops gemacht:</FONT>
<UL>
    <LI><FONT SIZE=3>Name der
    Shop-Datenbank</FONT>
    <LI><FONT SIZE=3>DB-Username und Passwort des Shopadministrators
    und Shopbenutzers</FONT>
</UL>
    </P><LI><P><FONT SIZE=3>Deinstallieren Sie jetzt den Shop (siehe
    Manuals: \'Installation / Deinstallation\'), wenn das
    Deinstallationsprogramm nicht mehr vorhanden sein sollte, oder nicht
    mehr funktioniert, l&ouml;schen Sie das defekte Shop-Verzeichnis
    einfach von Hand (ACHTUNG: Folgender Befehl l&ouml;scht das
    Shopverzeichnis inkl. allen Unterverzeichnissen OHNE R&Uuml;CKFRAGE:
    rm -rf Shopverzeichnis).</FONT></P>
    <LI><P><FONT SIZE=3>Installieren Sie den Shop neu (Sie m&uuml;ssen
    hier die Angaben aus Punkt 3 bereit halten).</FONT></P>
    <LI><P><FONT SIZE=3>Kopieren Sie ihr erstelltes Backup
    (</FONT><FONT FACE=\"Courier New, monospace\"><FONT SIZE=3>0.sql[.gz]<FONT FACE=\"Century Schoolbook, serif\">,...</FONT></FONT></FONT><FONT SIZE=3>)
    in das folgende Verzeichnis, wobei &lt;shopdir&gt; das
    Shopverzeichnis darstellt:</FONT></P>
    <P><FONT SIZE=3><FONT FACE=\"Courier New, monospace\">&lt;shopdir&gt;/shop/Admin/Backups</FONT></FONT></P>
    <LI><P><FONT SIZE=3>Gehen Sie ins Untermen&uuml; Datenbank-Backup
    <FONT FACE=\"Courier New, monospace\">--&gt;</FONT> Restore und
    klicken Sie auf Restore ihres angezeigten Backups, best&auml;tigen
    Sie das &uuml;berschreiben der Datenbank.</FONT></P>
    <LI><P><FONT SIZE=3>Jetzt k&ouml;nnen Sie ihre selbst erstellten
    Hintergrundbilder und Shop-Buttons wieder zur&uuml;cklesen (siehe
    Manuals: \'Restore von Hand\').</FONT></P>
    <LI><P><FONT SIZE=3>&Uuml;berpr&uuml;fen Sie zur Sicherheit noch
    einmal alle Administrationseinstellungen (v.a. die allgemeinen
    Shop-Einstellungen, die Versandkosten und Kundenattribute).</FONT></P>
</OL>
<H3>Backup und Restore von Hand</H3>
<P>Im Dokument PhPepperShop &#150; Manuals (Anleitungen) kann man
nachlesen, wie man ein Shop-Backup von Hand erstellt und wieder
zur&uuml;ckliest. Hier wird auch beschrieben wie man ein <B>Backup
der Artikelbilder, Hintergrundbilder und Shopbuttons</B> erstellt.</P>
</td></tr></table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_Layout","
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td>
<H3>Layout Management</H3>
<P>Hier k&ouml;nnen Sie den Look des Shops nach Herzenslust ver&auml;ndern.</P>
<H4>Welcome Page &auml;ndern</H4>
<P>Um die Willkommens-Page abzu&auml;ndern, muss man folgende Datei
&auml;ndern:<FONT FACE=\"Courier New, monospace\">    &lt;shopdir&gt;/shop/Frameset/content.php</FONT></P>
<H4>Eigentliches Layout Management</H4>
<P>Im Layout-Management k&ouml;nnen Sie das Aussehen Ihres Shops
komplett Ihren Bed&uuml;rfnissen anpassen.</P>
<P>So ist es m&ouml;glich, f&uuml;r jedes Frame (Fensterteil) eine
eigene Hintergrundfarbe zu definieren oder ein Hintergrundbild
anzeigen zu lassen.</P>
<P ALIGN=JUSTIFY>Im Top-Frame (oberer Fensterteil) kann entweder der
Shopname, ihr Shoplogo oder &uuml;berhaupt nichts angezeigt werden.
Den kleinen Stern, der neben dem &#132;Warenkorb anzeigen&#147;-
Button (Knopf) eingeblendet wird, um in den Administrationsbereich zu
gelangen, k&ouml;nnen Sie bei Bedarf ausblenden. ACHTUNG! Danach
kommen Sie nur noch in den Administrationsbereich, indem Sie
\'<BR><FONT FACE=\"Courier New, monospace\">http://www.ihreshop.top_level_domain/shopname<B>/shop/Admin/</B></FONT><B>\'</B>
im Browser eingeben!</P>
<P>Beispiel: <FONT FACE=\"Courier New, monospace\">http://www.pizzashop.de/pizzashop/shop/Admin/</FONT></P>
<P>Sie haben f&uuml;nf Eingabefelder f&uuml;r das Fontset zur
Verf&uuml;gung. Geben Sie in dem Feld \'1. Priorit&auml;t\' die
Schriftart ein, die mit h&ouml;chster Priorit&auml;t zur Anzeige des
Shops verwendet werden soll. \'2. Priorit&auml;t\' ist die Schriftart,
die verwendet wird, wenn der Surfer die unter \'1. Priorit&auml;t\'
eingetragene Schriftart auf seinem Computer nicht installiert hat. So
geht es weiter bis zur 5. Priorit&auml;t.</P>
<P>Die Schriftfarbe, Schriftgr&ouml;sse, das Schriftgewicht, die
Text-Dekoration und der Schriftstil k&ouml;nnen f&uuml;r alle
verwendeten Tags (Schriftgruppen) gesondert eingestellt werden. Bitte
beachten Sie, dass die Schriftstil-Einstellungen Browser-abh&auml;ngig
interpretiert werden.</P>
<P>Falls Sie die Gr&ouml;sse des Left-Frame (linker Fensterteil,
welcher die Kategogien und Unterkategorien enth&auml;lt) ver&auml;ndern
wollen, k&ouml;nnen Sie das im Feld &#132;Breite Left-Frame&#147;
tun. Die H&ouml;he des Top-Frames (oberer Fensterteil) kann im Feld
&#132;H&ouml;he Top-Frame&#147; eingestellt werden.</P>
<H4>Bilder (Hintergrund &amp; Shoplogo) hochladen</H4>
<P><I>Im Administrationshauptmen&uuml; auf \'Bilder (Hintergrund &amp; Shoplogo) hochladen\' klicken</I>. Sie k&ouml;nnen f&uuml;r jedes Frame (Fensterteil) ein eigenes
Hintergrundbild hochladen. Wenn Sie auf den Durchsuchen- bzw.
Browse-Button klicken, &ouml;ffnet sich ein Fenster, mit dem Sie eine
Grafikdatei im Format GIF, JPG oder PNG auf Ihrer Festplatte
ausw&auml;hlen k&ouml;nnen.</P>
<P>Danach m&uuml;ssen Sie den Verwendungszweck f&uuml;r das
ausgew&auml;hlte Bild bestimmen. Es kann entweder als Hintergrundbild
f&uuml;r eines der drei Frames verwendet, oder als Shoplogo
hochgeladen werden.</P>
<P>Sobald Sie ein Hintergrundbild oder ein Shoplogo hochgeladen
haben, ist es aktiviert. Sollten Sie den Shop danach immer noch im
alten Erscheinungsbild sehen, liegt das nur daran, dass die alte
Version noch in ihrem Browsercache gespeichert ist.</P>
<H4>Shopbuttons (Kn&ouml;pfe hochladen)</H4>
<P><I>Im Administrationshauptmen&uuml; auf \'Shopbuttons (Kn&ouml;pfe hochladen)\' klicken</I>. Sie k&ouml;nnen jeden im Shop verwendeten Button durch einen
eigenen Ihrer Wahl ersetzen. Die einzige Bedingung ist, dass der
Button im GIF Format (erkennbar an der Dateiendung \'.gif\')
abgespeichert wurde.</P>
<P>W&auml;hlen Sie, wie in \'Bilder (Hintergrund &amp; Shoplogo)
hochladen\' beschrieben, eine Datei auf Ihrerer Festplatte aus. W&auml;hlen
Sie dann, welcher Button damit ersetzt werden soll. Durch einen Klick
auf \'Bild hochladen\' wird der neue Button in den Shop hochgeladen.</P>
</td></tr></table>
       ");
INSERT INTO hilfe (Hilfe_ID, Hilfetext)
       VALUES ("Shop_MwSt","
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr>
    <td colspan=\"2\">
        <h3>MwSt Management</h3>
    </td>
  </tr>
    <td colspan=\"2\">
        <BR>Hilfe zur MwSt mit dem PhPepperShop. Hier wird die Konfiguration der MwSt-Einstellungsm&ouml;glichkeiten und deren Auswirkungen erkl&auml;rt.
        <BR><BR>
    </td>
  </tr>
  <tr>
    <td colspan=\"2\">
        <hr><h4><i>MwSt-Einstellungen</i></h4><br>
    </td>
  </tr>
  <tr>
    <td valign=\"top\">
        <BR><B>&nbsp;</B>
    </td>
    <td>
        Seit der PhPepperShop Version 1.2 kann jedem Artikel ein eigener MwSt-Satz zugeordnet werden. Damit ist es m&ouml;glich Produkte mit verschiedenen
        MwSt-Prozents&auml;tzen im gleichen Webshop zum Verkauf anzubieten. Es ist z.B. m&ouml;glich Lebensmittel mit bevorzugtem MwSt-Satz und gleichzeitig auch
        andere Waren, oder Dienstleistungen zu verkaufen - dies nur so als Beispiel.<br>
        <p>
            <h4>Granularit&auml;t</h4>
            Die feinste Einteilung eines MwSt-Satzes im PhPepperShop ist ein Artikel. Das heisst, dass man gleichzeitig den ganzen Shop, mehrere Kategorien, eine Gruppe von
            Artikel oder eben nur einen einzelnen Artikel dem gleichen MwSt-Satz unterstellen kann. Es ist also derzeit <i>nicht</i> m&ouml;glich,
            einem Artikel z.B. 7.6% MwSt. zuzuweisen, seinen Optionen und Variationen aber einen anderen MwSt-Satz. In einer Kategorie k&ouml;nnen
            aber verschiedene Artikel durchaus auch verschiedene MwSt-S&auml;tze besitzen.<br>Man muss sich bei der Erstellung der Artikel einfach der
            feinstm&ouml;glichen Aufl&ouml;sung der MwSt-Satz-Verteilung (Granularit&auml;t) bewusst sein, dann geht auch nichts schief ;-).
        </p>
        <p>
            <h4>MwSt-Pflichtig! Wie gehe ich vor...</h4>
            In der Schweiz ist jedes Unternehmen, welches pro Fiskaljahr mehr als CHF 75 000.00 Umsatz erwirtschaftet MwSt-pflichtig. Das heisst, man muss dem Staat f&uuml;r
            seine gebotenen Leistungen auch entsprechend Mehrwertsteuer bezahlen, welche man nat&uuml;rlich auch den Kunden weiter verrechnen darf. (Ausserdem
            ist man Vorsteuerabzugsberechtigt - hierzu mehr in der amtlichen MwSt-Wegleitung). In anderen L&auml;ndern existieren &auml;hnliche Regelungen.<br>
            Wer im PhPepperShop Waren zum Verkauf anbietet und MwSt-pflichtig ist, kann im Men&uuml; Allgemeine Shopeinstellungen den Shop als MwSt-pflichtig deklarieren, seine
            MwSt-Nummer eingeben und festlegen, ob seine Artikelpreise inkl. oder exkl. MwSt sind. Danach wird das MwSt-Management im Hauptmen&uuml; freigeschaltet.
            Darin kann man danach seine MwSt-S&auml;tze definieren und diese Kategorien zuordnen.
        </p>
        <p>
            <h4>MwSt-S&auml;tze definieren</h4>
            Ist der Webshop neu installiert trifft man in der angezeigten Tabelle wohl schon mal auf den 2002 in der Schweiz standardm&auml;ssig verwendeten 7.6%
            MwSt-Satz. Man kann <i>bis zu 10 verschiedene MwSt-S&auml;tze</i> definieren. Wer mehr ben&ouml;tigt kann in der Datei SHOP_MWST.php im Admin-Verzeichnis
            die Variable anzahl_mwstsettings (etwa in Zeile 54) auf seine gew&uuml;nschte Anzahl ab&auml;ndern.<br>
            In der Tabellenmaske MwSt-S&auml;tze definieren kann man folgende Einstellungen (z.T. f&uuml;r jeden MwSt-Satz einzeln) vornehmen:<br><br>
         </p>
            <table border=0>
                <tr>
                    <td valign=top>
                        MwSt-pflichtig:
                    </td>
                    <td>
                        Wenn diese Checkbox ein H&auml;kchen besitzt, so nimmt der PhPepperShop an, dass der Shop MwSt-pflichtig ist. Das Mehrwertsteuer Management
                        wird freigeschaltet und dem Kunden wird im Warenkorb und im Bestellungs E-mail detailliert die MwSt-Anteile der Gesamtkosten aufgelistet.<br>
                        MwSt-pflichtig ist man, wenn man eine g&uuml;ltige MwSt-Nummer eingibt und diese mit Speichern best&auml;tigt.<br>
                    </td>
                </tr>
                <tr>
                    <td valign=top>
                        MwSt-Nummer:
                    </td>
                    <td>
                        Die MwSt-Nummer wird einem von der Regierung zugestellt. Sie wird im Bestellungs-E-Mail ausgewiesen.<br>
                    </td>
                </tr>
                <tr>
                    <td valign=top>
                        MwSt-default:
                    </td>
                    <td>
                        Mit dieser Einstellung kann man <i>einen</i> der definierten MwSt-S&auml;tze als Standard (default) MwSt-Satz ausweisen. Dieser Standard-Prozentsatz
                        wird immer dann verwendet, wenn kein anderer Prozentsatz Sinn macht. Dieser Satz kann und wird shopweit als Standard herangezogen werden. Man sollte hier den Standard MwSt-Satz des Landes verwenden, selbst dann, wenn
                        man im Shop sonst aussschliesslich Artikel verkauft, welche einem bevorzugten MwSt-Satz unterliegen! In der Schweiz w&auml;re dies im Moment der MwSt-Satz 7.6%.<br>
                    </td>
                </tr>
                <tr>
                    <td valign=top>
                        MwSt-Satz:
                    </td>
                    <td>
                        Hier wird nat&uuml;rlich der eigentliche MwSt-Satz als Prozentzahl eingegeben. Wenn man also 7.6% MwSt-Satz hat, so gibt man im Feld die Zahl
                        7.6 ein (bitte <i>kein</i> Prozentzeichen miteingeben!). Die Zuordnung zu einzelnen Artikeln oder Kategorien kommt noch sp&auml;ter.<br>
                    </td>
                </tr>
                <tr>
                    <td valign=top>
                        Beschreibung:
                    </td>
                    <td>
                        Mit der Beschreibung kann man mit einem Stichwort oder so den MwSt-Satz charakterisieren. Z.B. 2.3% Lebensmittel, 7.6% Standard-Satz, ... Dies dient einzig
                        der &Uuml;bersichtlichtkeit und hat auf jegliche Berechnungen keinen Einfluss. Die Beschreibung wird jeweils bei der Auswahl eines MwSt-Satzes zur Bearbeitung
                        angezeigt.<br>
                    </td>
                </tr>
                <tr>
                    <td valign=top>
                        Porto und Verpackung:
                    </td>
                    <td>
                        Versand- und Verpackungskosten, Nachnahmegeb&uuml;hren und allf&auml;llige Mindermengenzuschl&auml;ge k&ouml;nnen nach verschiedenen Abrechnungsarten
                        versteuert werden:
                        <ul>
                        <li><b>Anteilsm&auml;ssig</b>: Der zu versteuernde Betrag von Porto und Verpackung wird anteilsm&auml;ssig versteuert. Wenn man in der aktuellen Bestellung
                        zu 60% Waren mit 7.6% MwSt. hat, 35% G&uuml;ter zu 2.3% und 5% Waren à 4%, so wird der zu versteuernde Betrag (von Porto und Verpackung) prozentual aufgespalten
                        und im gleichen Verh&auml;ltnis mit den gleichen MwSt-S&auml;tzen versteuert. Dies d&uuml;rfte die gel&auml;ufigste Variante sein.</li>
                        <li><b>Nach dem MwSt-Satz mit gr&ouml;sstem Rechnungsanteil</b>: Hier wird der MwSt-Satz herangezogen, welcher prozentual den gr&ouml;ssten Anteil an der Bestellsumme hat.</li>
                        <li><b>Gar nicht (MwSt-frei)</b>: Porto und Verpackung (Versandkosten, Nachnahmegeb&uuml;hr, Mindermengenzuschlag) sind MwSt-frei. (In der Schweiz verboten!)</li>
                        <li><b>Festsatz</b>: Hier kann einer der oben selbst definierten MwSt-S&auml;tze zur Berechnung heranziehen. Falls ihre neu eingegebenen MwSt-S&auml;tze noch nicht zur Auswahl
                        bereitstehen, bitte zuerst speichern und nochmals in dieses Formular gehen, dann sollten alle vorher neu eingegebenen MwSt-S&auml;tze zur Auswahl bereitstehen.</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td valign=top>
                        Preise inkl., exkl. MwSt:
                    </td>
                    <td>
                        Im Men&uuml; <a href=\"SHOP_SETTINGS.php\">Allgemeine Shopeinstellungen</a> kann man definieren, ob die Artikelpreise die Mehrwertsteuer schon enthalten,
                        oder ob sie exkl. MwSt. sind. Im Men&uuml; MwSt definieren und in der Artikel-Editier-Maske wir zus&auml;tzlich angezeigt welcher Preismodus gew&auml;hlt wurde.
                        <br>
                    </td>
                </tr>
            </table>
        <p>
            <h4>MwSt-S&auml;tze zuweisen</h4>
            Hat man seine(n) MwSt-Satz(e) definiert und erfolgreich abgespeichert, kann man diese nun einzelnen Haupt-/Unterkategorien und ihren Artikeln zuweisen:<br>
            Zu jeder Haupt-/Unterkategorie gibt es ein Dropdown Men&uuml;, in welchem man den gew&uuml;nschten MwSt-Satz zuweisen kann. Beim Zuweisen gilt ausserdem folgende Regel:<br>
            Alle MwSt-S&auml;tze der Artikel, welche in den ausgew&auml;hlten Kategorien liegen, werden automatisch mit upgedated</li>
            In der danach erscheinenden Erfolgsmeldung werden einem detailliert alle upgedateten Haupt- und Unterkategorien mit den neuen MwSt-S&auml;tzen aufgelistet.
         </p>
    </td>
  </tr>
</table>
       ");

# /*
# * ------------------------------------------------------------ End of File Marke
# */
