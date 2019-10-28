<?php
  // Filename: USER_BESTELLUNG.php
  //
  // Modul: PHP Funktionen - USER_BESTELLUNG
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Beinhaltet alle Funktionen fuer Shop-User zum Bestellwesen
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: USER_BESTELLUNG.php,v 1.74 2003/08/16 09:07:48 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_BESTELLUNG = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($initialize)) {include("initialize.php");}
  if (!isset($bestellung_def)) {include("bestellung_def.php");}
  if (!isset($versandkosten_def)) {include("versandkosten_def.php");}
  if (!isset($kunde_def)) {include("kunde_def.php");}
  if (!isset($pay_def)) {include("pay_def.php");}
  if (!isset($attribut_def)) {include("attribut_def.php");}
  if (!isset($kreditkarte_def)) {include("kreditkarte_def.php");}
  if (!isset($zahlung_def)) {include("zahlung_def.php");}
  if (!isset($USER_SQL_BEFEHLE)) {include("USER_SQL_BEFEHLE.php");}

  // -----------------------------------------------------------------------
  // Gibt auf Grund einer Session_ID (falls existent) die dazugehoerende Bestellung zurueck
  // Argument: Session_ID (INT)
  // Rueckgabewert: Eine Bestellung als Bestellungs-Objekt (Definition siehe bestellung_def.php)
  function getBestellung($Session_ID) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getBestellung_1_1;
      global $sql_getBestellung_1_2;

      // Wenn keine Session_ID angegeben wurde eine leere Bestellung zurueckgeben.
      if ($Session_ID == "") {
          return new Bestellung;
      }

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (getBestellung)</H1></P>\n");
      }
      else {
          // Zuerst ein Test ob es diese Session_ID ueberhaupt gibt, sonst eine Neue erstellen:
          // ***wurde auskommentiert:*** test_create_Bestellung($Session_ID);
          // Wenn ja, Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $sql_exec = $sql_getBestellung_1_1.trim($Session_ID).$sql_getBestellung_1_2;
          $RS = $Database->Query($sql_exec);
          $myBestellung = new Bestellung; //Ein neues Bestellungs-Objekt instanzieren
          $art_counter = 1; //Counter um Artikel einer Bestellung zu zaehlen (=Array-Key)
          while (is_object($RS) && $RS->NextRow()){
              $myArtikel_info = new Artikel_info; //Ein neues Artikel_info-Objekt instanzieren
              // Bestellung einlesen:
              $myBestellung->Bestellungs_ID = $RS->GetField("Bestellungs_ID");
              $myBestellung->Session_ID = $RS->GetField("Session_ID");
              $myBestellung->Bestellung_abgeschlossen = $RS->GetField("Bestellung_abgeschlossen");
              $myBestellung->Bestellung_ausgeloest = $RS->GetField("Bestellung_ausgeloest");
              $myBestellung->Bestellung_bezahlt = $RS->GetField("Bestellung_bezahlt");
              $myBestellung->Bestellung_string = $RS->GetField("Bestellung_string"); // Komplette Bestellung als Text
              $myBestellung->Anmerkung = $RS->GetField("Anmerkung");
              $myBestellung->Datum = $RS->GetField("Datum");
              $myBestellung->Bezahlungsart = $RS->GetField("Bezahlungsart");
              $myBestellung->Versandkosten = $RS->GetField("Versandkosten");
              $myBestellung->Mindermengenzuschlag = $RS->GetField("Mindermengenzuschlag");
              $myBestellung->Rechnungsbetrag = $RS->GetField("Rechnungsbetrag");
              $myBestellung->Nachnahmebetrag = $RS->GetField("Nachnamebetrag");
              $myBestellung->Kreditkarten_Hersteller = $RS->GetField("Kreditkarten_Hersteller");
              $myBestellung->Kreditkarten_Nummer = $RS->GetField("Kreditkarten_Nummer");
              $myBestellung->Kreditkarten_Ablaufdatum = $RS->GetField("Kreditkarten_Ablaufdatum");
              $myBestellung->Kreditkarten_Vorname = $RS->GetField("Kreditkarten_Vorname");
              $myBestellung->Kreditkarten_Nachname = $RS->GetField("Kreditkarten_Nachname");
              $myBestellung->Attribut1 = $RS->GetField("Attribut1");
              $myBestellung->Attribut2 = $RS->GetField("Attribut2");
              $myBestellung->Attribut3 = $RS->GetField("Attribut3");
              $myBestellung->Attribut4 = $RS->GetField("Attribut4");
              $myBestellung->Attributwert1 = $RS->GetField("Attributwert1");
              $myBestellung->Attributwert2 = $RS->GetField("Attributwert2");
              $myBestellung->Attributwert3 = $RS->GetField("Attributwert3");
              $myBestellung->Attributwert4 = $RS->GetField("Attributwert4");
              $myBestellung->clearing_id = $RS->GetField("clearing_id");
              $myBestellung->clearing_extra = $RS->GetField("clearing_extra");
              $myBestellung->MwSt = $RS->GetField("MwSt");

              // Info des Artikels in ein Artikel_info-Objekt ablegen (Def. siehe bestellung_def.php)
              $myArtikel_info->Artikel_ID = $RS->GetField("FK_Artikel_ID");
              $myArtikel_info->Artikel_Nr = $RS->GetField("Artikel_Nr");
              $myArtikel_info->Name = $RS->GetField("Artikelname");
              $myArtikel_info->Anzahl = $RS->GetField("Anzahl");
              $myArtikel_info->Preis = $RS->GetField("Preis");
              $myArtikel_info->Gewicht = $RS->GetField("Gewicht");

              // Die Zusatztexte, die ein Artikel haben kann, in das Array des Objekts abfuellen
              $myArtikel_info->Zusatzfelder = explode("þ",$RS->GetField("Zusatztexte"));


              // Je ein Variationstext und danach die Preisdifferenz werden per explode der Reihe
              // nach in den temporaeren Array $vararry abgelegt. Trennzeichen = Alt + 0254
              $vararray = array();
              $vararray = explode("þ", $RS->GetField("Variation"));
              // Der erste Wert ist der Variationstext und das zweite $vararray-Element
              // ist der Aufpreis. Diese beiden Werte werden nun ins Artikel_info Objekt
              // gespeichert

              // Nun muss unterschieden werden zwischen Variationstext und der Preisdifferenz
              // Man weiss, dass sie immer paarweise auftreten, deshalb ist hier eine kleine
              // "Weiche" noetig (hier per Modulo-Operation realisiert. % = Modulo-Operator)
              $counter=1; // Counter initialisieren
              foreach($vararray as $key => $value){
                  // Wenn counter ungerade ist, dann handelt es sich um einen Variationstext.
                  // Dieser wird zwischengespeichert und beim naechsten Durchlauf
                  // mit der dazugehoerigen Preisdifferenz abgelegt
                  if(($counter%2)==1){
                      // All ungerade male Optionstext in Temp. Variable schreiben
                      $tempVariationstext = $value;
                  }
                  else {
                      // Wenn die Option nicht leer ist, ins Artikel_info-Objekt speichern
                      if(!empty($tempVariationstext)){
                         // Jedes zweite mal unser Artikel-Info Objekt aktualisieren
                         $myArtikel_info->putvariation($tempVariationstext,$value);
                      }
                  }
                  $counter++;
              }


              // Je ein Optionstext und danach die Preisdifferenz werden per explode der Reihe
              // nach in den temporaeren Array $temparray abgelegt. Trennzeichen = Alt + 0254
              $temparray = array();
              $temparray = explode("þ",$RS->GetField("Optionen"));
              // Nun muss unterschieden werden zwischen Optionstext und der Preisdifferenz
              // Man weiss, dass sie immer paarweise auftreten, deshalb ist hier eine kleine
              // "Weiche" noetig (hier per Modulo-Operation realisiert. % = Modulo-Operator)
              $counter=1; // Counter initialisieren

              foreach($temparray as $key => $value){
                  // Wenn counter ungerade ist, dann handelt es sich um einen Optionstext.
                  // Dieser wird zwischengespeichert und beim naechsten Durchlauf
                  // mit der dazugehoerigen Preisdifferenz abgelegt
                  if(($counter%2)==1){
                      // All ungerade male Optionstext in Temp. Variable schreiben
                      $tempOptionstext = $value;
                  }
                  else {
                      // Wenn die Option nicht leer ist, ins Artikel_info-Objekt speichern
                      if(!empty($tempOptionstext)){
                         // Jedes zweite mal unser Artikel-Info Objekt aktualisieren
                         $myArtikel_info->putoption($tempOptionstext,$value);
                      }
                  }
                  $counter++;
              }
              $art_counter++;
              //Den gewaehlten Artikel mit ablegen
              $myBestellung->putartikel($art_counter,$myArtikel_info);
         }//End while
         return $myBestellung;
      }//End else
  }//End function getBestellung

  // -----------------------------------------------------------------------
  // Zuerst wird ueberprueft ob in der Datenbank schon einen Bestellung fuer
  // diese Session existiert, dann wird je nach Resultat eine leere neue
  // erstellt oder nichts weiter gemacht und true zurueck gemeldet.
  // Abgelaufene Sessions werden geloescht und durch Neue ersetzt.
  // Argument: Session_ID
  // Rueckgabewert: true bei Erfolg (sonst Abbruch durch die-Funktion)
  function test_create_Bestellung($Session_ID) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_test_create_Bestellung_1_1;
      global $sql_test_create_Bestellung_1_2;
      global $sql_test_create_Bestellung_1_3;
      global $sql_test_create_Bestellung_1_4;
      global $sql_test_create_Bestellung_1_5;

      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: test_create_Bestellung</H1></P><BR>");
      }
      else {
          // Mit der folgenden Query wird ueberprueft, ob man schon eine Bestellung hat
          // Nebenbei werden vorsorgleich gleich schon mal das Ablaufdatum der Session und
          // Die maximale Lebensdauer einer Session mit runtergeladen (um eine Neue zu erstellen)
          $RS = $Database->Query("$sql_test_create_Bestellung_1_1".$Session_ID."$sql_test_create_Bestellung_1_2");
          if (is_object($RS) && $RS->NextRow()) {
              // Eine Bestellung existiert bereits, nun wird ueberprueft ob die Session
              // noch gueltig ist, wenn ja, ist alles ok, andernfalls wird eine
              // neue Session erzeugt
              $tempsessionid = $RS->GetField("Session_ID");
              $expired = $RS->GetField("expired");
              $Bestellungs_ID = $RS->GetField("Bestellungs_ID");
              $Bestellung_abgeschlossen = $RS->GetField("Bestellung_abgeschlossen");
              // Jetzige Zeit: (Ev. gibts spaeter mal Probleme bei dieser Funktion (UNIX-Zeit))
              $now = time();
              if($expired < $now) {
                  // Kunde hat zwar eine Session_ID, diese ist aber abgelaufen,
                  // deshalb erhaelt er eine Neue (Alte zuerst loeschen, dann Neue einfuegen)
                  delSession($tempsessionid, $Bestellungs_ID);//Session UND Bestellung loeschen
                  $max_session_time = getmax_session_time();
                  $now = time(); //Aktuelle Zeit
                  $expired = time() + $max_session_time;
                  $RS = $Database->Exec("$sql_test_create_Bestellung_1_3".session_id()."$sql_test_create_Bestellung_1_4".$expired."$sql_test_create_Bestellung_1_5");
                  if (!$RS) {
                      echo "<H1 class='content'>U_B_Error:test_create_Bestellung: Neue Session_ID erzeugen 1</H1><BR>";
                      die("Now = $now, expired = $expired. Query =  $sql_test_create_Bestellung_1_3".$tempsessionid."$sql_test_create_Bestellung_1_4".$expired."$sql_test_create_Bestellung_1_5");
                  }
              }
              else if ($Bestellung_abgeschlossen == 'Y') {
                  // Kunde hat zwar eine Session_ID, diese ist aber abgeschlossen worden,
                  // deshalb erhaelt er eine Neue (Alte Session loeschen Bestellung aber in Ruhe lassen
                  // , dann eine Neue einfuegen)
                  delSession($tempsessionid, $Bestellungs_ID);//Session UND Bestellung loeschen
                  $tempsessionid = session_id();
                  $max_session_time = getmax_session_time();
                  $now = time(); //Aktuelle Zeit
                  $expired = time() + $max_session_time;
                  $RS = $Database->Exec("$sql_test_create_Bestellung_1_3".session_id()."$sql_test_create_Bestellung_1_4".$expired."$sql_test_create_Bestellung_1_5");
                  if (!$RS) {
                      echo "<H1 class='content'>U_B_Error:test_create_Bestellung: Neue Session_ID erzeugen 1</H1><BR>";
                      die("Now = $now, expired = $expired. Query =  $sql_test_create_Bestellung_1_3".$tempsessionid."$sql_test_create_Bestellung_1_4".$expired."$sql_test_create_Bestellung_1_5");
                  }
              }
          }
          else {
              // Es existiert noch keine Bestellung fuer diese Session_ID, deshalb
              // wird jetzt einen Neue erstellt (eingefuegt, INSERT)
              $max_session_time = getmax_session_time();
              $now = time(); //Aktuelle Zeit
              $expired = time() + $max_session_time;
              $RS = $Database->Exec("$sql_test_create_Bestellung_1_3".session_id()."$sql_test_create_Bestellung_1_4".$expired."$sql_test_create_Bestellung_1_5");
              if (!$RS) {
                  die("<H1 class='content'>U_B_Error:test_create_Bestellung: Neue Session_ID erzeugen 2</H1><BR>");
              }
          }
      }
      return true;
  }//End function test_create_Bestellung

  // -----------------------------------------------------------------------
  // Fuegt einen Artikel einer Bestellung hinzu
  // Argumente: Session_ID, damit man weiss welcher Bestellung der Artikel zuzuordnen ist
  //            Artikel_info-Objekt, enthaelt alle fuer die Bestellung relevanten Felder
  //            eines Artikels (inkl. Anzahl u.s.w.)
  // Rueckgabewert: true bei Erfolg, sonst Abbruch mit Fehlermeldung per die-Funktion
  function addArtikel($Session_ID, $myArtikel_info) {

      // Variablen aus anderen Modulen
      global $Database;
      global $sql_addArtikel_1_1;
      global $sql_addArtikel_1_2;
      global $sql_addArtikel_1_3;
      global $sql_addArtikel_1_4;
      global $sql_addArtikel_1_5;
      global $sql_addArtikel_1_6;
      global $sql_addArtikel_1_6_1;
      global $sql_addArtikel_1_7;
      global $sql_addArtikel_1_8;
      global $sql_addArtikel_1_9;
      global $sql_addArtikel_1_10;
      global $sql_addArtikel_1_11;
      global $sql_addArtikel_1_12;
      global $sql_addArtikel_1_13;
      global $sql_addArtikel_1_14;
      global $sql_addArtikel_1_14_2;
      global $sql_addArtikel_1_15;
      global $sql_addArtikel_1_16;
      global $sql_addArtikel_1_17;

      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (addArtikel)</H1></P><BR>");
      }
      else {
          // Damit wir einer Bestellung einen weiteren Artikel hinzufuegen koennen, muss der
          // neue Artikel in der Tabelle artikel_bestellung mit einem Verweis auf die ent-
          // sprechende Bestellungs_ID eingefuegt werden. Bestellungs_ID holen:
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_addArtikel_1_1".$Session_ID."$sql_addArtikel_1_2");
          if (is_object($RS) && $RS->NextRow()) {
              $Bestellungs_ID = $RS->GetField("Bestellungs_ID");
          }
          else {
              // Es existiert noch keine Bestellung fuer diese Session, eine neue
              // muss erstellt werden (sollte eigentlich dank test_create_Bestellung nicht mehr vorkommen)
              echo "<P><H1 class='content'>U_B_Error: Fehler beim Einfuegen eines Artikels in eine Bestellung(1/2)</H1></P><BR>";
              test_create_Bestellung($Session_ID);
          }

          // Bevor wir die Ziel-Tabelle updaten koennen, muessen wir die einzugebenden Daten
          // etwas formatieren, sprich bereit legen: Variationen und Optionen muessen noch
          // bearbeitet werden -> in array packen: (Delimiter Zeichen = Alt + 0254):
          foreach(($myArtikel_info->getallvariationen()) as $key_var => $val_var) {
              if (empty($varString)){
                  $varString = $key_var."þ".$val_var;
              }
              else {
                  $varString.= "þ".$key_var."þ".$val_var;
              }
          } // end of foreach

          // Optionen abfuellen (Wenn erstes Mal, kein einleitendes Delimiter Zeichen)
          // (Delimiter-Zeichen = Alt + 0254) -> historisch gewachsen: optionenString heisst hier tempstring.
          foreach(($myArtikel_info->getalloptionen()) as $key => $val) {
              if (empty($tempString)){
                  $tempString = $key."þ".$val;
              }
              else {
                  $tempString = $tempString."þ".$key."þ".$val;
              }
          } // end of foreach


          // Sonderzeichen getrennten (þ) String erstellen, welcher die Inahlte der Zusatzfelder enthaelt
          $ZusatzString = addslashes(spezial_string($myArtikel_info->Zusatzfelder));

          // Jetzt geht es ans Einfuegen der eigentlichen Daten in die Tabelle artikel_bestellung:

          // Zuerst muss auf Duplikate (siehe unten) getestet werden
          // Dann kommt entweder ein Update oder ein Insert zum Zuge.
          // Was ist hier mit Duplikate gemeint: Duplikate sind Artikel, welche
          // die gleichen Variationen UND Optionen haben, wie schon ein anderer Artikel
          // im Warenkorb.
          $Duplikat_ersetzt = false; //Flag ob ein Update gemacht wurde (siehe weiter unten)
          $Duplikattest_var = ""; // Variablen initialisieren
          $Duplikattest_opt = ""; // Variablen initialisieren
          $testcounter = 0;

          $RS = $Database->Query("$sql_addArtikel_1_17".$Bestellungs_ID);
          while (is_object($RS) && $RS->NextRow()){
              $testcounter++;
              $Duplikattest =  $RS->GetField("FK_Artikel_ID");
              $Duplikattest_var = $RS->GetField("Variation");
              $Duplikattest_opt = $RS->GetField("Optionen");
              $Duplikattest_zus = $RS->GetField("Zusatztexte");
              $oldAnzahl = $RS->GetField("Anzahl");
              if($Duplikattest == $myArtikel_info->Artikel_ID) {
                  // Es KOENNTE ein Duplikat vorhanden sein, nun muss noch auf gleiche Variationen UND Optionen UND Zusatztexte getestet werden.
                  if (($Duplikattest_var == $varString) && ($Duplikattest_opt == $tempString) && ($Duplikattest_zus == $ZusatzString)) {
                      // Duplikat vorhanden -> Update
                      // Der Kunde hat diesen Artikel bereits im Warenkorb (Ev. wollte er die Anzahl erhoehen)
                      // Wir muessen nun einen Update anstatt einen Insert durchfuehren
                      // Das Artikelpaar wird nun upgedated
                      $neuAnzahl = $oldAnzahl + $myArtikel_info->Anzahl;
                      $RS_2 = $Database->Exec("$sql_addArtikel_1_9".$neuAnzahl."$sql_addArtikel_1_10 '".
                                     addslashes($varString)."' $sql_addArtikel_1_11 '".addslashes($tempString).
                                     "' $sql_addArtikel_1_12".$myArtikel_info->Artikel_ID."$sql_addArtikel_1_13".$Bestellungs_ID."$sql_addArtikel_1_14 '".addslashes($Duplikattest_var)."' $sql_addArtikel_1_14_2 '".addslashes($Duplikattest_opt)."'");
                      if (!$RS_2) {
                          die("<P><H1 class='content'>U_B_Error: Fehler beim Updaten eines Artikels in eine Bestellung(2/2_1)</H1></P><BR>");
                      }
                      $Duplikat_ersetzt = true; // Es soll kein Insert mehr durchgefuehrt werden
                      break; //While Schleife vorzeitig verlassen
                  }
              }
          }// End while
          if (!$Duplikat_ersetzt) {
              // Da es keine Duplikate gibt, muss der gewaehlte Artikel der Tabelle neu hinzugefuegt werden:
              $RS = $Database->Exec("$sql_addArtikel_1_3".$myArtikel_info->Artikel_ID."$sql_addArtikel_1_4".
                             $Bestellungs_ID.$sql_addArtikel_1_6.$myArtikel_info->Name.$sql_addArtikel_1_6_1.
                             $myArtikel_info->Preis.$sql_addArtikel_1_5.$myArtikel_info->Gewicht.
                             "$sql_addArtikel_1_5".$myArtikel_info->Anzahl.
                             "$sql_addArtikel_1_6".addslashes($varString)."$sql_addArtikel_1_7".addslashes($tempString)."$sql_addArtikel_1_7".$ZusatzString."$sql_addArtikel_1_8");
              if (!$RS) {
                  die("<P><H1 class='content'>U_B_Error: Fehler beim Einfuegen eines Artikels in eine Bestellung(2/2_2)</H1></P><BR>");
              }
          }
      }// End else database
      return true;
  }// End function addArtikel

  // -----------------------------------------------------------------------
  // Loescht einen Artikel-Eintrag fuer eine Bestellung
  // (Komische Namensgebung weil es delArtikel schon gibt)
  // Argumente: Artikel_ID und Bestellungs_ID um die betroffenen Artikel eindeutig zu bestimmen
  // Rueckgabewert: true bei Erfolg (Abbruch (die) und eine Meldung bei Misserfolg)
  function del_B_Artikel($FK_Artikel_ID, $FK_Bestellungs_ID, $varstring, $optstring, $zusatzstring) {

      // Einbinden von Variablen von anderen Modulen
      global $Database;
      global $sql_del_B_Artikel_1_1;
      global $sql_del_B_Artikel_1_2;
      global $sql_del_B_Artikel_1_3;
      global $sql_del_B_Artikel_1_4;
      global $sql_del_B_Artikel_1_5;
      global $sql_del_B_Artikel_1_6;

      // Testen ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (del_B_Artikel)</H1></P><BR>");
      }
      else {
          // ...wenn ja, Query ausfuehren und in ResultSet schreiben
          // (Typ des ResultSets, siehe database.php)
          $RS = $Database->Exec("$sql_del_B_Artikel_1_1".$FK_Artikel_ID."$sql_del_B_Artikel_1_2".$FK_Bestellungs_ID."$sql_del_B_Artikel_1_3".$varstring."$sql_del_B_Artikel_1_4".$optstring."$sql_del_B_Artikel_1_5".$zusatzstring."$sql_del_B_Artikel_1_6");
          if (!$RS) {
              // Es gab einen Fehler beim Loeschen --> Script mit einer Fehlermeldung beenden
              echo "<P><H1 class='content'>U_B_Error: Fehler beim Loeschen eines Artikels aus einer Bestellung (del_B_Artikel)</H1></P><BR>";
              die("Query: $sql_del_B_Artikel_1_1".$FK_Artikel_ID."$sql_del_B_Artikel_1_2".$FK_Bestellungs_ID."$sql_del_B_Artikel_1_3".$varstring."$sql_del_B_Artikel_1_4".$optstring."$sql_del_B_Artikel_1_5".$zusatzstring."$sql_del_B_Artikel_1_6<BR>");
          }
      }//End else
      return true;
  }//End function del_B_Artikel

  // -----------------------------------------------------------------------
  // Eine Session (und damit auch die dazugehoerige Bestellung) loeschen
  // Es werden vier Schritte abgearbeitet:
  // 1.) Bestellung loeschen
  // 2.) Artikelliste der Bestellung loeschen
  // 3.) Kunde-Bestellung-Referenz loeschen (Tabelle bestellung_kunde)
  // *** ausgeschaltet *** 4.) Temporaerer Kunde loeschen (ist sicher temporaer!)
  // Argumente:
  // ($Session_ID wird benoetigt um die Bestellung in der Tabelle
  // bestellung zu referenzieren, $Bestellungs_ID wird benoetigt damit man OHNE
  // eine weitere Query zu starten gleich noch alle zur geloeschten Bestellung
  // gehoerenden Artikel in der artikel_bestellung-Tabelle loeschen kann)
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion
  function delSession($Session_ID, $Bestellungs_ID) {

      // Variablen aus anderen Modulen einbinden (muessen included sein)
      global $Database;
      global $sql_delSession_1_1;
      global $sql_delSession_1_2;
      global $sql_delSession_1_3;
      global $sql_delSession_1_4;
      global $sql_delSession_1_5;
      global $sql_delSession_1_6;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: delSession</H1></P><BR>");
      }
      else {
          // Die angegebene Session loeschen: (1.) Bestellung, 2.) deren Artikel-Liste, 3.) den Kunden, 4.) Kunde-Bestellung)
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          // 1.) Bestellung loeschen, Tabelle: bestellung
          $RS = $Database->Exec("$sql_delSession_1_1".$Session_ID."$sql_delSession_1_2");
          if ($RS) {
              // Hier wird das Resultat nicht mehr geprueft, da das Ergebnis auch 0 sein kann
              // Wenn es ein Query-Fehler ist, wird das Modul database.php einen Fehler liefern
              // 2.) Verknuepfung von Bestellung und deren Artikel loeschen, Tabelle: artikel_bestellung
              $RS = $Database->Exec("$sql_delSession_1_3".$Bestellungs_ID);
              // 3.) Verknuepfung von Kunde und Bestellung loeschen, Tabelle: bestellung_kunde
              $RS = $Database->Exec("$sql_delSession_1_6".$Bestellungs_ID);
              /* Nochmals ueberpruefen // 4.) (Temporaerer) Kunde loeschen, Tabelle: kunde
              $RS = $Database->Exec("$sql_delSession_1_4".$Session_ID."$sql_delSession_1_5");   */
              return true;
          }
          else {
              echo "<P><H1 class='content'>U_B_Error: Konnte Bestellung nicht l&ouml;schen delSession</H1></P><BR>";
              die("Query 1: $sql_delSession_1_1".$Session_ID."$sql_delSession_1_2 und Query 2: ".$sql_delSession_1_3."$Bestellungs_ID<BR>");
          }
       }
  }// End function delSession

  // -----------------------------------------------------------------------
  // Loescht ALLE abgelaufenen (expired) Bestellungen und ihre
  // Artikel-Zuweisungen. Problematik: Dies ist eine USER-Funktion die aber eine
  // Administrator-Operation ausfuehrt --> Sicherheitsloch(!?) Auf jeden Fall nicht sauber.
  // Besser waere eine Art Cron-Job, der mit Admin-Rechten diese Operation ausfuehrt
  // Wir dachten uns aber, auf diese Weise muss der Admin nichts machen und der Shop
  // haelt die Anzahl eingetragener Bestellungen (bei eingeschaltetem Bestellungsmanagement
  // (ab v.1.4 Kundenmanagement)) von alleine in Grenzen.
  // Diese Funktion ist "aufwaendig" vom benoetigten Zeitaufwand her und soll an einem
  // weniger stark frequentierten Ort aufgerufen werden. (Bei uns nach Bestellungs-Abschluss)
  function delallexpiredSessions() {

      // Einbinden von Variablen von anderen Modulen
      global $Database;
      global $sql_delallexpiredSessions_1_1;

      // Testen ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (delallexpiredSessions)</H1></P><BR>");
      }
      else {
          // Wenn ja, Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          // Mit der folgenden Query werden die Attribute Session_ID, expired, Bestellungs_ID, Bestellung_abgeschlossen ausgelesen
          $RS = $Database->Query("$sql_delallexpiredSessions_1_1");
          while (is_object($RS) && $RS->NextRow()) {
              $Session_ID = $RS->GetField("Session_ID");
              $expired = $RS->GetField("expired");
              $Bestellungs_ID = $RS->GetField("Bestellungs_ID");
              $Bestellung_abgeschlossen = $RS->GetField("Bestellung_abgeschlossen");
              $now = time();
              // Wenn es sich um eine nicht abgeschlossene Bestellung handelt UND
              // die Bestellung abgelaufen ist, so soll diese Bestellung und falls
              // ein temporaerer Kunde dazu vorhanden ist geloescht werden.
              if(($Bestellung_abgeschlossen == 'N') && ($expired < $now)){
                  // Wenn es sich um einen temporaeren Kunden handelt, diesen erkennen und loeschen
                  $meinKunde = getKunde_einer_Bestellung($Bestellungs_ID);
                  if ($meinKunde->temp == "Y") {
                      // Temporaerer Kunde loeschen
                      delKunde($meinKunde->Kunden_ID);
                  }// End if Kunde
                  delSession($Session_ID, $Bestellungs_ID);
              }// End if
          }// End while
      }// End else
      return true;
  }// End function delallexpiredSessions

  // -----------------------------------------------------------------------
  // *** Wird in v.1.05 nicht mehr verwendet und wird in den nächsten Versionen geloescht ***
  // Kunden koennen so ihre Daten in ihre Bestellung eingeben
  // Daten = Sachen wie Name, Vorname, Adresse, Tel, Email, ...
  // Argumente: Session_ID, damit man weiss zu welcher Bestellung die Daten gehoeren werden
  //            Bestellungs-Objekt, enthaelt die User-Daten (Def. siehe bestellung_def.php)
  // Rueckgabewert: true bei Erfolg, Abbruch per die-Funktion bei allfaelligem Fehler
  function setKundendaten($Session_ID, $Bestellung) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_setKundendaten_1_1;
      global $sql_setKundendaten_1_2;
      global $sql_setKundendaten_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: setKundendaten</H1></P><BR>");
      }
      else {
          // Bevor wir die Kundendaten in der Datenbank speichern, fuellen wir alle Kundendaten
          // in den String $Kundendaten ab. So dass der Update Befehl alles korrekt erkennt
          $Kundendaten = "Anrede='".$Bestellung->Anrede."', Vorname='".$Bestellung->Vorname."', Name='".$Bestellung->Name.
                         "', Firma='".$Bestellung->Firma."', Abteilung='".$Bestellung->Abteilung."', Adresse1='".$Bestellung->Adresse1.
                         "', Adresse2='".$Bestellung->Adresse2."', PLZ='".$Bestellung->PLZ."', Ort='".$Bestellung->Ort.
                         "', Land='".$Bestellung->Land."', Telefon='".$Bestellung->Telefon."', Email='".$Bestellung->Email.
                         "', Anmerkung='".$Bestellung->Anmerkung."', Bezahlungsart='".$Bestellung->Bezahlungsart.
                         "', Datum='".$Bestellung->Datum;
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Exec("$sql_setKundendaten_1_1".$Kundendaten."$sql_setKundendaten_1_2".$Session_ID."$sql_setKundendaten_1_3");
          if ($RS) {
              return true;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1 class='content'>U_B_Error: Fehler bei der Eingabe der Kundendaten</H1></P><BR>";
              die("Query: $sql_setKundendaten_1_1".$Kundendaten."$sql_setKundendaten_1_2".$Session_ID."$sql_setKundendaten_1_3<BR>");
          }
      }//End else
  }//End function setKundendaten

  // -----------------------------------------------------------------------
  // Damit ein Kunde seine Bestellung nicht mehr abaendern kann wird seine
  // Bestellung mit dem Attribut Bestellung_abgeschlossen in der Tabelle bestellung
  // als unveraenderbar markiert, in der Datenbank wird ferner die Session-ID
  // bei diesem Eintrag geloescht. Somit erhaelt er mit der gleichen Session-ID eine
  // neue Bestellung in der Datenbank.
  // Argument: Session_ID (String), abschliessen_Flag (Y oder N)
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion
  function schliessenBestellung($Session_ID, $abschliessen_Flag) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_schliessenBestellung_1_1;
      global $sql_schliessenBestellung_1_2;
      global $sql_schliessenBestellung_1_3;
      global $sql_schliessenBestellung_1_4;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: schliessenBestellung</H1></P><BR>");
      }
      else {
          // Nun folgt zuerst noch eine kleine Fallunterscheidung. Wenn das abschliessen_Flag = Y ist, so
          // sollen die Bestellungen als abgeschlossen markiert werden. Damit werden sie nicht automatisch
          // von der Funktion delallexpiredSessions() geloescht. -> Bestellungsmanagement eingeschaltet
          if ($abschliessen_Flag == 'Y') {
              // Bei der korrespondierenden Bestellung Bestellung_abgeschlossen = 'Y' und Sesion_ID = '' setzen
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Database->Exec("$sql_schliessenBestellung_1_1".$Session_ID."$sql_schliessenBestellung_1_2");
              if (!$RS) {
                  //Script mit einer Fehlermeldung beenden.
                  echo "<P><H1 class='content'>U_B_Error: Fehler beim Schliessen einer Bestellung (schliessenBestellung)_2</H1></P><BR>";
                  die("Query: $sql_schliessenBestellung_1_1".$Session_ID."$sql_schliessenBestellung_1_2<BR>");
              }
          }
          else {
              // Bei der korrespondierenden Bestellung nur die Sesion_ID = '' setzen (erzeugt fuer den Kunden einen neuen leeren Warenkorb)
              $RS = $Database->Exec("$sql_schliessenBestellung_1_3".$Session_ID."$sql_schliessenBestellung_1_4");
              if (!$RS) {
                  //Script mit einer Fehlermeldung beenden.
                  echo "<P><H1 class='content'>U_B_Error: Fehler beim Schliessen einer Bestellung (schliessenBestellung)_2</H1></P><BR>";
                  die("Query: $sql_schliessenBestellung_1_3".$Session_ID."$sql_schliessenBestellung_1_4<BR>");
              }
          }
          return true;
      }//End else
  }//End function schliessenBestellung

  // -----------------------------------------------------------------------
  // Liefert die Versandkosteneinstellungen des Shops
  // Argumente: Setting_Nr (im Hinblick auf mehrere Shops verwalten mit einem GUI)
  // Rueckgabewert: Versandkosten-Objkekt
  function getversandkostensettings($Setting_Nr) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getversandkostensettings_1_1;
      global $sql_getversandkostensettings_1_2;
      global $sql_getversandkostensettings_1_13;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: getversandkostensettings</H1></P><BR>");
      }
      else {
          // Zuerst werden die relevanten Einstellungen aus der Tabelle shop_settings ausgelesen
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $meineVersandkosten = new Versandkosten;
          $RS = $Database->Query("$sql_getversandkostensettings_1_1 $Setting_Nr");
          if ($RS && $RS->NextRow()) {
              $meineVersandkosten->Setting_Nr = $RS->GetField("Setting_Nr");
              $meineVersandkosten->Abrechnung_nach_Preis = $RS->GetField("Abrechnung_nach_Preis");
              $meineVersandkosten->Abrechnung_nach_Gewicht = $RS->GetField("Abrechnung_nach_Gewicht");
              $meineVersandkosten->Abrechnung_nach_Pauschale = $RS->GetField("Abrechnung_nach_Pauschale");
              $meineVersandkosten->Pauschale_text = $RS->GetField("Pauschale_text");
              $meineVersandkosten->keineVersandkostenmehr = $RS->GetField("keineVersandkostenmehr");
              $meineVersandkosten->keineVersandkostenmehr_ab = $RS->GetField("keineVersandkostenmehr_ab");
              $meineVersandkosten->anzahl_Versandkostenintervalle = $RS->GetField("anzahl_Versandkostenintervalle");
              $meineVersandkosten->Mindermengenzuschlag = $RS->GetField("Mindermengenzuschlag");
              $meineVersandkosten->Mindermengenzuschlag_bis_Preis = $RS->GetField("Mindermengenzuschlag_bis_Preis");
              $meineVersandkosten->Mindermengenzuschlag_Aufpreis = $RS->GetField("Mindermengenzuschlag_Aufpreis");
              $meineVersandkosten->Waehrung = $RS->GetField("Waehrung");
              $meineVersandkosten->Gewichts_Masseinheit = $RS->GetField("Gewichts_Masseinheit");
              $meineVersandkosten->MwStpflichtig = $RS->GetField("MwStpflichtig");
              $meineVersandkosten->MwStNummer = $RS->GetField("MwStNummer");
              $meineVersandkosten->MwStsatz = $RS->GetField("MwStsatz");
              $meineVersandkosten->Shopname = $RS->GetField("Name");
              $meineVersandkosten->Nachname = $RS->GetField("Nachnahme");
              $meineVersandkosten->Rechnung = $RS->GetField("Rechnung");
              $meineVersandkosten->Vorauskasse = $RS->GetField("Vorauskasse");
              $meineVersandkosten->Lastschrift = $RS->GetField("Lastschrift");
              $meineVersandkosten->Kreditkarte = $RS->GetField("Kreditkarten_Postcard");
              $meineVersandkosten->Nachnamebetrag = $RS->GetField("Nachnamebetrag");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1 class='content'>U_B_Error: Fehler beim auslesen der Versandkosten Settings (getversandkostensettings)</H1></P><BR>";
              die("Query: $sql_getversandkostensettings_1_1 $Setting_Nr<BR>");
          }
          // Jetzt werden die dazu gehoerigen Zeilen aus der Tabelle versandkostenpreise ausgelesen
          // und in den Array des Versandkostenobjekts abgelegt
          $meinVersandkostenpreis = new Versandkostenpreis;
          $RS = $Database->Query("$sql_getversandkostensettings_1_2 $Setting_Nr $sql_getversandkostensettings_1_13");
          if (!$RS) {
              echo "<B>Query:</B>$sql_getversandkostensettings_1_2 $Setting_Nr $sql_getversandkostensettings_1_13<BR>";
              die("<h1>U_B_Error: Musste bei zweitem SLQ-Query abbrechen (getversandkostensettings)</h1>");
          }
          while (is_object($RS) && $RS->NextRow()){
              $meinVersandkostenpreis->Von_Bis_ID = $RS->GetField("Von_Bis_ID");
              $meinVersandkostenpreis->Von = $RS->GetField("Von");
              $meinVersandkostenpreis->Bis = $RS->GetField("Bis");
              $meinVersandkostenpreis->Betrag = $RS->GetField("Betrag");
              $meinVersandkostenpreis->Vorauskasse = $RS->GetField("Vorauskasse");
              $meinVersandkostenpreis->Rechnung = $RS->GetField("Rechnung");
              $meinVersandkostenpreis->Nachname = $RS->GetField("Nachname");
              $meinVersandkostenpreis->Lastschrift = $RS->GetField("Lastschrift");
              $meinVersandkostenpreis->Kreditkarte = $RS->GetField("Kreditkarte");
              $meinVersandkostenpreis->billBOX = $RS->GetField("billBOX");
              $meinVersandkostenpreis->Treuhandzahlung = $RS->GetField("Treuhandzahlung");
              $meinVersandkostenpreis->Postcard = $RS->GetField("Postcard");
              $meineVersandkosten->putversandkostenpreis($meinVersandkostenpreis);
          }//End while
      }//End else
      return $meineVersandkosten;
  }//End function getversandkostensettings

  // -----------------------------------------------------------------------
  // Liefert den Beschreibungstext des Rechnungspostens fuer die Versandkosten im Warenkorb
  // Argumente: Setting_Nr (im Hinblick auf mehrere Shops verwalten mit einem GUI -> verworfen)
  // Rueckgabewert: String
  function getversandkostentext($Setting_Nr) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getversandkostentext_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1>U_B_Error: Datenbank nicht erreichbar: getversandkostentext</H1></P><BR>");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getversandkostentext_1_1 $Setting_Nr");
          if ($RS && $RS->NextRow()) {
              $text = $RS->GetField("Pauschale_text");
          }
          else {
              die("<H1>U_B_Error: Fehler beim Auslesen des Versandkosten Textes (getversandkostentext)</H1>");
          }
          return $text;
      }
  }// End function getversandkostentext

  // -----------------------------------------------------------------------
  // Berechnet die Versandkosten einer Bestellung und schreibt die Werte
  // auch gleich in die entsprechenden Variablen der Bestellung
  // Diese Funktion sieht kompliziert aus (intervall-Berechnungen) weil
  // in der Entwicklerversion v.1.04 des Shops mit Von-Bis-Intervallen
  // gerechnet wurde. Diese wurden aber ab der Version v.1.05 zugunsten
  // der Ab-Intervalle fallen gelassen. Es wird nun mit Zwischen-Intervallen
  // gearbeitet (Diese Funktion ist 'ready for redesign'). Anmerkung: Bei
  // Angaben der Preise exkl. MwSt. wird die MwSt. zum Rechnungstotal erst
  // dazuaddiert (und in der Datenbank festgehalten), wenn die Funktion
  // darstellenBestellung($Bestellungs_ID) aus USER_BESTELLUNG_DARSTELLUNG.php
  // aufgerufen wurde.
  // Argumente: Session_ID (String)
  // Rueckgabewert: Array: 1.Element = Versandkosten,
  //                       2.Element = Mindermengenzuschlag,
  //                       3.Element = Rechnungstotal (ALLES inkl. allem)
  //                       4.Element = Nachnahmegebuehr
  function berechneversandkosten($Session_ID) {

        // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_berechneversandkosten_1_1;
      global $sql_berechneversandkosten_1_2;
      global $sql_berechneversandkosten_1_3;
      global $sql_berechneversandkosten_1_4;
      global $sql_berechneversandkosten_1_5;

      // Wir holen uns zuerst die zu bearbeitende Bestellung und die aktuellen
      // Versandkosten Einstellungen (Setting_Nr, hier einmal auf 1 gesetzt):
      $Setting_Nr = 1; // Statisch = 1, es war einmal geplant hier mehr zuzulassen
      $rechnungstotal = 0.0; // Initialisierung
      $myBestellung = new Bestellung;
      $myVersandkosten = new Versandkosten;
      $myBestellung = getBestellung($Session_ID);
      $myVersandkosten = getversandkostensettings($Setting_Nr);
      if ($myBestellung->Bezahlungsart == "Nachnahme") {
          $Nachnahmebetrag = getNachnahmebetrag();
      }
      else {
          $Nachnahmebetrag = 0.0;
      }
      // Treuhandkostenbetrag auslesen und berechnen, so dieser verwendet wird
      if ($myBestellung->Bezahlungsart == "Treuhandzahlung") {
          $treuhandkostenarray = getTreuhandbetrag($myBestellung->Rechnungsbetrag);
          $treuhandbetrag = $treuhandkostenarray[0];
      }
      else {
          $treuhandbetrag = 0.0;
      }

      // Als erstes muessen wir das Versandkostenrelevante Total berechnen, oder bei
      // einer Pauschale, diese verwenden. Weiter muss ueberprueft werden:
      // Mindermengenzuschlag und keineVersandkostenmehr

      // Initialisierung des Rueckgabearrays
      $rueckarray = array();
      // Abrechnung nach *** Pauschale ***
      if ($myVersandkosten->Abrechnung_nach_Pauschale == "Y") {
          $Artikelarray = $myBestellung->getallartikel();
          $total = 0.0;
          foreach ($Artikelarray as $key=>$value) {
              $total = $total + $value->Preis;     //Artikelpreis
              foreach ($value->getalloptionen() as $ukey=>$uvalue) {
                  $total = $total + $uvalue;       //Optionen
              }
              foreach ($value->getallvariationen() as $vkey=>$vvalue) {
                  $total = $total + $vvalue;       //Variationen
              }
              $total = $total * $value->Anzahl;    //Anzahl
              $gesamttotal = $gesamttotal + $total;//Artikeltotale kumulieren
              $total = 0.0;
          }
          $total = $gesamttotal; // Weiter wird mit der Variable $total gearbeitet
          // Auslesen des Pauschalbetrags
          $Versandkostenarray = $myVersandkosten->getallversandkostenpreise();
          $vk_preis = $Versandkostenarray[0]->Betrag;
          // Behandlung des Mindermengenzuschlags
          $Mindermengenzuschlag = 0.0;
          if (($myVersandkosten->Mindermengenzuschlag == "Y") && ($myVersandkosten->Mindermengenzuschlag_bis_Preis >= $total)) {
              $Mindermengenzuschlag = $myVersandkosten->Mindermengenzuschlag_Aufpreis;
          }
          // Ueberpruefung auf kostenfreie Versandkosten auf Grund des Kauf-Volumens
          // (Haengt von den Einstellungen des Versandkostenmanagements ab)
          if (($myVersandkosten->keineVersandkostenmehr == "Y") && ($myVersandkosten->keineVersandkostenmehr_ab <= $total)) {
              $vk_preis = 0.0;
              $Mindermengenzuschlag = 0.0;
              $Nachnahmebetrag = 0.0;
              // Da bei Ueberschreitung des keineVersandkostenmehr_ab Rechnungsbetrags keine Treuhandkosten
              // zum Rechnungstotal addiert werden, wird das hier gemacht:
              // $rechnungstotal = $treuhandbetrag;
          }

          // Rechnungstotal berechnen
          // Das Rechnungstotal umfasst die kumulierten Preise aller in der Bestellung enthalteten
          // Artikel und die Versandkosten inkl. allfaelligem Mindermengenzuschlag Falls die
          // Preise exkl. MwSt angegeben werden, sind die MwSt. Zuschlaege noch NICHT inbegriffen!
          $rechnungstotal = $rechnungstotal + $total + $vk_preis + $Mindermengenzuschlag + $Nachnahmebetrag;

          // Zusammenfuegen des zurueckzugebenden Arrays
          $rueckarray[0] = $vk_preis;
          $rueckarray[1] = $Mindermengenzuschlag;
          $rueckarray[2] = $rechnungstotal;
          $rueckarray[3] = $Nachnahmebetrag;

          // Update der Bestellung -> Eintragen der berechneten Werte
          $RS = $Database->Exec("$sql_berechneversandkosten_1_1".$vk_preis."$sql_berechneversandkosten_1_2".$Mindermengenzuschlag."$sql_berechneversandkosten_1_3".$rechnungstotal."$sql_berechneversandkosten_1_5".$Nachnahmebetrag."$sql_berechneversandkosten_1_4".$myBestellung->Bestellungs_ID);
          if (!$RS) {
              //Bei einem Fehler im SQL: Script mit einer Fehlermeldung beenden.
              echo "<P><H1 class='content'>U_B_Error: Fehler beim Versandkosteneintragen in eine Bestellung (berechneversandkosten) (P)</H1></P><BR>";
              die("Query: $sql_berechneversandkosten_1_1".$vk_preis."$sql_berechneversandkosten_1_2".$Mindermengenzuschlag."$sql_berechneversandkosten_1_3".$rechnungstotal."$sql_berechneversandkosten_1_5".$Nachnahmebetrag."$sql_berechneversandkosten_1_4".$myBestellung->Bestellungs_ID."<BR>");
          }

          // Rueckgabe der Versandkosten, des Mindermengenzuschalgs und des Rechnungtotals:
          return $rueckarray; // Es gibt drei Returnstatements in dieser Funktion
      }
      // Abrechnung nach Preis
      elseif ($myVersandkosten->Abrechnung_nach_Preis == "Y") {
          $Artikelarray = $myBestellung->getallartikel();
          $total = 0.0;
          foreach ($Artikelarray as $key=>$value) {
              $total = $total + $value->Preis;     //Artikelpreis
              $total = $total + $value->Aufpreis;  //Variationsaufpreis
              // Optionspreise kumulieren
              foreach ($value->getalloptionen() as $ukey=>$uvalue) {
                  $total = $total + $uvalue;       //Optionen
              } // end of foreach
              // Variationspreise kumulieren
              foreach ($value->getallvariationen() as $ukey=>$uvalue) {
                  $total = $total + $uvalue;       //Optionen
              } // end of foreach
             $total = $total * $value->Anzahl;    //Anzahl
              $gesamttotal = $gesamttotal + $total;//Artikeltotale kumulieren
              $total = 0.0;
          }
          $total = $gesamttotal; // Weiter wird mit der Variable $total gearbeitet
          // Auslesen der Preisintervalle
          $Versandkostenarray = $myVersandkosten->getallversandkostenpreise();
          // Berechnung des Versandkostenpreises aufgrund der Intervalle in der Tabelle versandkostenpreise
          $intervall_gefunden = false; // Flag ob ein Preisintervall gefunden wurde
          foreach ($Versandkostenarray as $key=>$value) {
              // Suche nach regulaeren Intervallen
              if (($value->Von <= $total) && ($value->Bis >= $total)) {
                  $vk_preis = $value->Betrag;
                  $intervall_gefunden = true;
                  break;
              }
          }
          // Falls kein Intervall gefunden wurde: Suche nach Zwischenraeumen zwischen den Intervallen
          if (!$intervall_gefunden) {
              reset($Versandkostenarray); //interner Zeiger an den Anfang setzen
              for ($i=0;$i < (count($Versandkostenarray)-1);$i++) {
                  $von1 = $Versandkostenarray[$i]->Von;
                  $bis1 = $Versandkostenarray[$i]->Bis;
                  $betrag1 = $Versandkostenarray[$i]->Betrag;
                  $von2 = $Versandkostenarray[($i+1)]->Von;
                  $bis2 = $Versandkostenarray[($i+1)]->Bis;
                  $betrag2 = $Versandkostenarray[($i+1)]->Betrag;
                  // Betrag liegt in einem Zwischenintervall
                  if (($bis1 <= $total) && ($von2 >= $total)) {
                      $vk_preis = $betrag1;
                      $intervall_gefunden = true;
                      break;
                  }
              }
              reset($Versandkostenarray); //interner Zeiger an den Anfang setzen
          }
          // Wenn immer noch ken Intervall gefunden wurde, so wird defaultmaessig der Preis des ersten Intervalls
          // benutzt. Dies um eine Fehlermeldung zu vermeiden. Das Intervall 0, da dieses immer vorhanden ist
          if (!$intervall_gefunden) {
              $vk_preis = $Versandkostenarray[0]->Betrag;
          }
          // Behandlung des Mindermengenzuschlags
          $Mindermengenzuschlag = 0.0;
          if (($myVersandkosten->Mindermengenzuschlag == "Y") && ($myVersandkosten->Mindermengenzuschlag_bis_Preis >= $total)) {
              $Mindermengenzuschlag = $myVersandkosten->Mindermengenzuschlag_Aufpreis;
          }
          // Ueberpruefung auf kostenfreie Versandkosten auf Grund des Kauf-Volumens
          if (($myVersandkosten->keineVersandkostenmehr == "Y") && ($myVersandkosten->keineVersandkostenmehr_ab <= $total)) {
              $vk_preis = 0.0;
              $Mindermengenzuschlag = 0.0;
              $Nachnahmebetrag = 0.0;
              // Da bei Ueberschreitung des keineVersandkostenmehr_ab Rechnungsbetrags keine Treuhandkosten
              // zum Rechnungstotal addiert werden, wird das hier gemacht:
              // $rechnungstotal = $treuhandbetrag;
          }
          // Rechnungstotal berechnen
          // Das Rechnungstotal umfasst die kumulierten Preise aller in der Bestellung enthalteten
          // Artikel und die Versandkosten inkl. allfaelligem Mindermengenzuschlag
          $rechnungstotal = $rechnungstotal + $total + $vk_preis + $Mindermengenzuschlag + $Nachnahmebetrag;

          // Zusammenfuegen des zurueckzugebenden Arrays
          $rueckarray[0] = $vk_preis;
          $rueckarray[1] = $Mindermengenzuschlag;
          $rueckarray[2] = $rechnungstotal;
          $rueckarray[3] = $Nachnahmebetrag;

          // Update der Bestellung -> Eintragen der berechneten Werte
          $RS = $Database->Exec("$sql_berechneversandkosten_1_1".$vk_preis."$sql_berechneversandkosten_1_2".$Mindermengenzuschlag."$sql_berechneversandkosten_1_3".$rechnungstotal."$sql_berechneversandkosten_1_5".$Nachnahmebetrag."$sql_berechneversandkosten_1_4".$myBestellung->Bestellungs_ID);
          if (!$RS) {
              //Script mit einer Fehlermeldung beenden.
              echo "<P><H1 class='content'>U_B_Error: Fehler beim Versandkosteneintragen in eine Bestellung (berechneversandkosten) (Pr)</H1></P><BR>";
              die("Query: $sql_berechneversandkosten_1_1".$vk_preis."$sql_berechneversandkosten_1_2".$Mindermengenzuschlag."$sql_berechneversandkosten_1_3".$rechnungstotal."$sql_berechneversandkosten_1_5".$Nachnahmebetrag."$sql_berechneversandkosten_1_4".$myBestellung->Bestellungs_ID."<BR>");
          }

          // Rueckgabe der Versandkosten, des Mindermengenzuschalgs und des Rechnungtotals:
          return $rueckarray; // Es gibt drei Returnstatements in dieser Funktion
      }
      // Abrechnung nach Gewicht
      else {
          $Artikelarray = $myBestellung->getallartikel();
          $total = 0.0;
          $preistotal = 0.0;
          foreach ($Artikelarray as $key=>$value) {
              // Artikel einlesen, damit wir die Gewichte zum Artikel und zu den Varaitionen/Optionen haben
              $myArtikel = getArtikel($value->Artikel_ID);
              $total = $total + $value->Gewicht; // Gewicht anstatt des Artikelpreises auslesen
              $preistotal = $preistotal + $value->Preis;     //Artikelpreis
              $preistotal = $preistotal + $value->Aufpreis;  //Variationsaufpreis
              // Gewichte der Optionen zusammenzaehlen
              foreach ($value->getalloptionen() as $ukey=>$uvalue) {
                  $preistotal = $preistotal + $uvalue;       //Optionen
                  $total = $total + $myArtikel->optionen_gewicht[$ukey];
              } // end of foreach
              // Gewichte der Variationen zusammenzaehlen
              foreach ($value->getallvariationen() as $ukey=>$uvalue) {
                  $preistotal = $preistotal + $uvalue;       //Optionen
                  $total = $total + $myArtikel->variationen_gewicht[$ukey];
              } // end of foreach
              $total = $total * $value->Anzahl;              //Anzahl fuer Gewicht
              $preistotal = $preistotal * $value->Anzahl;    //Anzahl fuer Preis
              $gesamttotal = $gesamttotal + $preistotal;//Artikeltotale kumulieren
              $gesamtgewicht = $gesamtgewicht + $total; //Gewichttotale kumulieren
              $preistotal = 0.0;
              $total = 0.0;
          }
          $preistotal = $gesamttotal; // Weiter wird mit der Variable $preistotal gearbeitet
          $total = $gesamtgewicht;    // Weiter wird mit der Variable $total gearbeitet
          // Auslesen der Gewichtsintervalle
          $Versandkostenarray = $myVersandkosten->getallversandkostenpreise();
          // Berechnung des Versandkostenpreises aufgrund der Intervalle in der Tabelle versandkostenpreise
          $intervall_gefunden = false; // Flag ob ein Gewichtsintervall gefunden wurde
          foreach ($Versandkostenarray as $key=>$value) {
              // Suche nach regulaeren Intervallen
              if (($value->Von <= $total) && ($value->Bis >= $total)) {
                  $vk_preis = $value->Betrag;
                  $intervall_gefunden = true;
                  break;
              }
          }
          // Falls kein Intervall gefunden wurde: Suche nach Zwischenraeumen zwischen den Intervallen
          if (!$intervall_gefunden) {
              reset($Versandkostenarray); //interner Zeiger an den Anfang setzen
              for ($i=0;$i < (count($Versandkostenarray)-1);$i++) {
                  $von1 = $Versandkostenarray[$i]->Von;
                  $bis1 = $Versandkostenarray[$i]->Bis;
                  $betrag1 = $Versandkostenarray[$i]->Betrag;
                  $von2 = $Versandkostenarray[($i+1)]->Von;
                  $bis2 = $Versandkostenarray[($i+1)]->Bis;
                  $betrag2 = $Versandkostenarray[($i+1)]->Betrag;
                  // Betrag liegt in einem Zwischenintervall
                  if (($bis1 <= $total) && ($von2 >= $total)) {
                      $vk_preis = $betrag1;
                      $intervall_gefunden = true;
                      break;
                  }
              }
              reset($Versandkostenarray); //interner Zeiger an den Anfang setzen
          }
          // Wenn immer noch ken Intervall gefunden wurde, so wird defaultmaessig der Preis des ersten Intervalls
          // benutzt. Dies um eine Fehlermeldung zu vermeiden. Das Intervall 0, da dieses immer vorhanden ist
          if (!$intervall_gefunden) {
              $vk_preis = $Versandkostenarray[0]->Betrag;
          }
          // Behandlung des Mindermengenzuschlags
          $Mindermengenzuschlag = 0.0;
          if (($myVersandkosten->Mindermengenzuschlag == "Y") && ($myVersandkosten->Mindermengenzuschlag_bis_Preis >= $preistotal)) {
              $Mindermengenzuschlag = $myVersandkosten->Mindermengenzuschlag_Aufpreis;
          }
          // Ueberpruefung auf kostenfreie Versandkosten auf Grund des Kauf-Volumens
          if (($myVersandkosten->keineVersandkostenmehr == "Y") && ($myVersandkosten->keineVersandkostenmehr_ab <= $preistotal)) {
              $vk_preis = 0.0;
              $Mindermengenzuschlag = 0.0;
              $Nachnahmebetrag = 0.0;
              // Da bei Ueberschreitung des keineVersandkostenmehr_ab Rechnungsbetrags keine Treuhandkosten
              // zum Rechnungstotal addiert werden, wird das hier gemacht:
              // $rechnungstotal = $treuhandbetrag;
          }
          // Rechnungstotal berechnen
          // Das Rechnungstotal umfasst die kumulierten Preise aller in der Bestellung enthalteten
          // Artikel und die Versandkosten inkl. allfaelligem Mindermengenzuschlag
          $rechnungstotal = $rechnungstotal + $preistotal + $vk_preis + $Mindermengenzuschlag + $Nachnahmebetrag;

          // Zusammenfuegen des zurueckzugebenden Arrays
          $rueckarray[0] = $vk_preis;
          $rueckarray[1] = $Mindermengenzuschlag;
          $rueckarray[2] = $rechnungstotal;
          $rueckarray[3] = $Nachnahmebetrag;
          // Update der Bestellung -> Eintragen der berechneten Werte
          $RS = $Database->Exec("$sql_berechneversandkosten_1_1".$vk_preis."$sql_berechneversandkosten_1_2".$Mindermengenzuschlag."$sql_berechneversandkosten_1_3".$rechnungstotal."$sql_berechneversandkosten_1_5".$Nachnahmebetrag."$sql_berechneversandkosten_1_4".$myBestellung->Bestellungs_ID);
          if (!$RS) {
              //Script mit einer Fehlermeldung beenden.
              echo "<P><H1 class='content'>U_B_Error: Fehler beim Versandkosteneintragen in eine Bestellung (berechneversandkosten) (G)</H1></P><BR>";
              die("Query: $sql_berechneversandkosten_1_1".$vk_preis."$sql_berechneversandkosten_1_2".$Mindermengenzuschlag."$sql_berechneversandkosten_1_3".$rechnungstotal."$sql_berechneversandkosten_1_5".$Nachnahmebetrag."$sql_berechneversandkosten_1_4".$myBestellung->Bestellungs_ID."<BR>");
          }

          // Rueckgabe der Versandkosten, des Mindermengenzuschalgs und des Rechnungtotals:
          return $rueckarray; // Es gibt drei return-Statements in dieser Funktion
      }//End else
  }// End function berechneversandkosten

  // -----------------------------------------------------------------------
  // Gibt auf Grund einer Kunden_ID ein Kunde-Objekt zurueck. Im optionalen Flag
  // $mit_Bestellungen kann man angeben, ob auch alle Bestellungen des auszulesenden
  // Kunden ins Objekt gespeichert werden. True (Default) = Ja, false = keine Bestellungen
  // ins Objekt abfuellen.
  // Argument: Kunden_ID (Integer), $mit_Bestellungen (Boolean)
  // Rueckgabewert: ein Kunde in Form eines Kunde-Objekts (siehe kunde_def.php)
  function getKunde($Kunden_ID,$mit_Bestellungen=true) {

      //Kunden_ID wird im SQL nicht explizit als String behandelt, hier per Hochkommata als Strings ausweisen:
      //Dies wurde wegen der Kompatibilitaet zu MySQL 3.22.x eingefuegt (Weiter noch an den Stellen 962, 1113, 1347)
      $Kunden_ID = "'".$Kunden_ID."'";

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getKunde_1_1; //Einlesen der Kundendaten
      global $sql_getKunde_1_2; //Einlesen der Bestellungen zu diesem Kunden

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (getKunde)</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Query($sql_getKunde_1_1.$Kunden_ID);
          if(!$RS) {
              die("<P><H1 class='content'>U_B_Error: RS-> nicht true (getKunde)_1_1</H1></P>\n");
          }
          $myKunde = new Kunde; //Ein neues Kunde-Objekt
          $art_counter = 1; //Counter um Bestellungen eine Kunde-Objekt zu zaehlen (=Array-Key)
          while (is_object($RS) && $RS->NextRow()){
              $myBestellung = new Bestellung; //Ein neues Bestellungs-Objekt instanzieren
              // Kunde einlesen:
              $myKunde->Kunden_ID = $RS->GetField("Kunden_ID");
              $myKunde->Kunden_Nr = $RS->GetField("Kunden_Nr");
              $myKunde->Session_ID = $RS->GetField("Session_ID");
              $myKunde->Anrede = $RS->GetField("Anrede");
              $myKunde->Vorname = $RS->GetField("Vorname");
              $myKunde->Nachname = $RS->GetField("Nachname");
              $myKunde->Firma = $RS->GetField("Firma");
              $myKunde->Abteilung = $RS->GetField("Abteilung");
              $myKunde->Strasse = $RS->GetField("Strasse");
              $myKunde->Postfach = $RS->GetField("Postfach");
              $myKunde->PLZ = $RS->GetField("PLZ");
              $myKunde->Ort = $RS->GetField("Ort");
              $myKunde->Land = $RS->GetField("Land");
              $myKunde->Tel = $RS->GetField("Tel");
              $myKunde->Fax = $RS->GetField("Fax");
              $myKunde->Email = $RS->GetField("Email");
              $myKunde->Einkaufsvolumen = $RS->GetField("Einkaufsvolumen");
              $myKunde->LetzteBestellung = $RS->GetField("LetzteBestellung");
              $myKunde->AnmeldeDatum = $RS->GetField("AnmeldeDatum");
              $myKunde->Login = $RS->GetField("Login");
              $myKunde->Passwort = $RS->GetField("Passwort");
              $myKunde->gesperrt = $RS->GetField("gesperrt");
              $myKunde->temp = $RS->GetField("temp");
              $myKunde->Attribut1 = $RS->GetField("Attribut1");
              $myKunde->Attribut2 = $RS->GetField("Attribut2");
              $myKunde->Attribut3 = $RS->GetField("Attribut3");
              $myKunde->Attribut4 = $RS->GetField("Attribut4");
              $myKunde->Attributwert1 = $RS->GetField("Attributwert1");
              $myKunde->Attributwert2 = $RS->GetField("Attributwert2");
              $myKunde->Attributwert3 = $RS->GetField("Attributwert3");
              $myKunde->Attributwert4 = $RS->GetField("Attributwert4");
              $myKunde->kontoinhaber = $RS->GetField("kontoinhaber");
              $myKunde->bankname = $RS->GetField("bankname");
              $myKunde->blz = $RS->GetField("blz");
              $myKunde->kontonummer = $RS->GetField("kontonummer");
              $myKunde->bankdaten_speichern = $RS->GetField("bankdaten_speichern");

              // Wenn es gewuenscht wird, werden auch alle Bestellungen des Kunden ausgelesen (Default)
              if ($mit_Bestellungen) {
                  // Die zu diesem Kunden gehoerenden Bestellungs-Objekte werden
                  // in einem Array festgehalten
                  // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
                  $RS_2 = $Database->Query($sql_getKunde_1_2.$Kunden_ID);
                  if(!$RS_2) {
                      die("<P><H1 class='content'>U_B_Error: RS_2-> nicht true (getKunde)_1_2</H1></P>\n");
                  }
                  while (is_object($RS_2) && $RS_2->NextRow()){
                      // Die Bestellungen werden von der Funktion getBestellung in Bestellungs-Objekte
                      // abgepackt und dann via putBestellung in den internen Array des Kunden-Objekts
                      // abgelelgt
                      $myKunde->putBestellung(getBestellung($RS_2->GetField("Bestellungs_ID")));
                  }//End while
              }// End if $mit_Bestellungen
          }//End while
          return $myKunde;
      }//End else
  }//End function getKunde


  // -----------------------------------------------------------------------
  // Gibt auf Grund einer Kunden_ID ein Kunden-Objekt zurueck
  // Argumente: keine
  // Rueckgabewert: Ein Array mit Kunden-Objekten (siehe kunde_def.php)
  function getallKunden() {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getallKunden_1_1; //Einlesen aller Kundendaten

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (getallKunden)</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Query($sql_getallKunden_1_1);
          if(!$RS) {
              die("<P><H1 class='content'>U_B_Error: RS-> nicht true (getallKunden)</H1></P>\n");
          }
          $myKunde = new Kunde; //Ein neues Kunden-Objekt
          $myKundenarray = array();
          while (is_object($RS) && $RS->NextRow()){
              // Die gefundenen Kunden werden von der Funktion getKunde in Kunden-Objekte abgepackt
              // und dann in den Array gespeichert
              $myKundenarray[] = getKunde($RS->GetField("Kunden_ID"));
         }//End while
         return $myKundenarray;
      }//End else
  }//End function getallKunden

  // -----------------------------------------------------------------------
  // Fuegt der Datenbank ein neuer Kunde hinzu (Tabellen kunde, bestellung_kunde)
  // Argumente: Ein Kunde in 'Einzelteilen'
  // Rueckgabewert: die Kunden_ID des neuen Kunden oder Abbruch via die-Funktion
  function newKunde($Kunden_ID,$Session_ID,$Kunden_Nr,$Anrede,$Vorname,$Nachname,$Firma,$Abteilung,
              $Strasse,$Postfach,$PLZ,$Ort,$Land,$Tel,$Fax,$Email,$Einkaufsvolumen,
              $Login,$Passwort,$gesperrt,$temp,$Attribut1,$Attribut2,$Attribut3,$Attribut4,
              $Attributwert1,$Attributwert2,$Attributwert3,$Attributwert4) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_newKunde_1_1;
      global $sql_newKunde_1_2;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (newKunde)</H1></P>\n");
      }
      else {
          // Aktuelles Datum holen
          $mydate = getdate();
          // Datum entsprechend formatieren um es in die Datenbank einfuegen zu koennen
          $AnmeldeDatum = $mydate[year]."-".$mydate[mon]."-".$mydate[mday];// Format yyyy-mm-dd
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // ...sowie Fehlerbehandlung und Ausgabe, sieht etwas unuebersichtlich aus
          if (!($Kunden_ID = $Database->Exec("$sql_newKunde_1_1'$Kunden_ID','$Session_ID','$Kunden_Nr',
              '$Anrede','$Vorname','$Nachname','$Firma','$Abteilung',
              '$Strasse','$Postfach','$PLZ','$Ort','$Land','$Tel','$Fax','$Email','$Einkaufsvolumen',
              '$Login','$Passwort','$gesperrt','$temp','$Attribut1','$Attribut2','$Attribut3','$Attribut4',
              '$Attributwert1','$Attributwert2','$Attributwert3','$Attributwert4','$AnmeldeDatum'$sql_newKunde_1_2"))) {

              echo "<P><H1>U_B_Error: Der Kunde konnte nicht gespeichert werden (newKunde)</H1></P>\n";
              echo "Query: $sql_newKunde_1_1'$Kunden_ID','$Session_ID','$Kunden_Nr','$Anrede','$Vorname','$Nachname','$Firma','$Abteilung',
              '$Strasse','$Postfach','$PLZ','$Ort','$Land','$Tel','$Fax','$Email','$Einkaufsvolumen',
              '$Login','$Passwort','$gesperrt','$temp','$Attribut1','$Attribut2','$Attribut3','$Attribut4',
              '$Attributwert1','$Attributwert2','$Attributwert3','$Attributwert4'$sql_newKunde_1_2<BR>";
              die ("<B>Kunden-ID: </B>$Kunden_ID,<B> Name: </B>$Vorname, $Name<BR>");
          }
          return $Kunden_ID;
      }//End else
  }//End function newKunde

  // -----------------------------------------------------------------------
  // Loescht einen Kunden in der Datenbank
  // Argumente: Kunden_ID
  // Rueckgabewert: true bei Erfolg, sonst Abbruch via die-Funktion
  function delKunde($Kunden_ID) {

      //Kunden_ID explizit als String deklarieren (fuer DB, wegen Kompatibilitaet zu MySQL 3.22.x):
      $Kunden_ID = "'".$Kunden_ID."'";

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_delKunde_1_1; //Einlesen aller Kundendaten

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (delKunde)</H1></P>\n");
      }
      else {
          // DELETE ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Exec($sql_delKunde_1_1.$Kunden_ID);
          if(!$RS) {
              echo "<B>Kunden_ID:</B> $Kunden_ID, <B>Query:</B>".$sql_delKunde_1_1.$Kunden_ID."<BR>";
              die("<P><H1 class='content'>U_B_Error: Kunde konnte nicht geloescht werden (delKunde)</H1></P>\n");
          }
         return true;
      }//End else
  }//End function delKunde

  // -----------------------------------------------------------------------
  // Gibt aufgrund einer Bestellungs_ID ein Kunden-Objekt zurueck
  // Noch nicht abgeschlossene Bestellungen haben keine Kundenzuordnung. Hier wird der Kunde anhand eines
  // Session_ID-Vergleichs gemacht.
  // Argumente: Bestellungs_ID
  // Rueckgabewert: Kunden_Objekt (siehe kunde_def.php)
  function getKunde_einer_Bestellung($Bestellungs_ID) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getKunde_einer_Bestellung_1_1;
      global $sql_getKunde_einer_Bestellung_1_2;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (getKunde_einer_Bestellung)</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Query($sql_getKunde_einer_Bestellung_1_1.$Bestellungs_ID);
          if(!$RS) {
              die("<P><H1 class='content'>U_B_Error: RS-> nicht true (getKunde_einer_Bestellung): Teil 1</H1></P>\n");
          }
          $Bestellungs_Session_ID = "";
          $myKunde = new Kunde; //Ein neues Kunden-Objekt, vorerst noch leer
          if (is_object($RS) && $RS->NextRow()){
              // Der gefundene Kunde wird von der Funktion getKunde in ein Kunden-Objekte abgepackt
              $Bestellungs_Session_ID = getKunde($RS->GetField("Session_ID"));
              $myKunde = getKunde($RS->GetField("FK_Kunden_ID"));
          }
          // Wenn eine Bestellung noch nicht abgeschlossen wurde, so wurde diese Bestellung auch noch keinem
          // Kunden zugewiesen (kein Eintrag in Tabelle bestellung_kunde erfolgt), somit wurde kein Kunde zur
          // Bestellung gefunden. Wenn das der Fall ist, handelt es sich vermutlich um eine noch nicht abge-
          // schlossene Bestellung. Wir koennen dennoch ihren 'Kunden' ermitteln - falls vorhanden - indem wir
          // die Session_ID der nicht abgeschlossenen Bestellung mit der Session_ID der Kunden vergleichen.
          // (abgeschlossene Bestellungen haben im Session_ID Attribut einen Leerstring)
          if ($myKunde->Kunden_ID == "") {
              // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
              $RS = $Database->Query($sql_getKunde_einer_Bestellung_1_2.$Bestellungs_ID);
              if(!$RS) {
                  die("<P><H1 class='content'>U_B_Error: RS-> nicht true (getKunde_einer_Bestellung): Teil 2</H1></P>\n");
              }
              $myKunde = new Kunde; //Ein neues Kunden-Objekt, vorerst noch leer
              if (is_object($RS) && $RS->NextRow()){
                  // Der gefundene Kunde wird von der Funktion getKunde in ein Kunden-Objekte abgepackt
                  $myKunde = getKunde($RS->GetField("Kunden_ID"));
              }
          }
          return $myKunde; // Zurueckgegeben wird ein Kunden-Objekt
      }//End else
  }//End function getKunde_einer_Bestellung

  // -----------------------------------------------------------------------
  // Dies ist ein Update aller Kundendaten ausser seiner Bankdaten (diese werden mit set_kunden_bankdaten upgedated)
  // Argumente: Kunde in Einzel-Attributen
  // Rueckgabewert: true bei Erfolg, sonst Abbruch via die die-Funktion
  function updKunde($Kunden_Nr,$Session_ID,$Anrede,$Vorname,$Nachname,$Firma,$Abteilung,
              $Strasse,$Postfach,$PLZ,$Ort,$Land,$Tel,$Fax,$Email,$Einkaufsvolumen,
              $Login,$Passwort,$gesperrt,$temp,$Attribut1,$Attribut2,$Attribut3,$Attribut4,
              $Attributwert1,$Attributwert2,$Attributwert3,$Attributwert4,$Kunden_ID) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_updKunde_1_1;
      global $sql_updKunde_1_2;
      global $sql_updKunde_1_3;
      global $sql_updKunde_1_4;
      global $sql_updKunde_1_5;
      global $sql_updKunde_1_6;
      global $sql_updKunde_1_7;
      global $sql_updKunde_1_8;
      global $sql_updKunde_1_9;
      global $sql_updKunde_1_10;
      global $sql_updKunde_1_11;
      global $sql_updKunde_1_12;
      global $sql_updKunde_1_13;
      global $sql_updKunde_1_14;
      global $sql_updKunde_1_15;
      global $sql_updKunde_1_16;
      global $sql_updKunde_1_17;
      global $sql_updKunde_1_18;
      global $sql_updKunde_1_19;
      global $sql_updKunde_1_20;
      global $sql_updKunde_1_21;
      global $sql_updKunde_1_22;
      global $sql_updKunde_1_23;
      global $sql_updKunde_1_24;
      global $sql_updKunde_1_25;
      global $sql_updKunde_1_26;
      global $sql_updKunde_1_27;
      global $sql_updKunde_1_28;
      global $sql_updKunde_1_29;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (updKunde)</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // ...sowie Fehlerbehandlung und Ausgabe, sieht etwas unuebersichtlich aus
          if (!($Kunden_ID = $Database->Exec("$sql_updKunde_1_1'$Kunden_Nr'$sql_updKunde_1_2'$Session_ID'$sql_updKunde_1_3'$Anrede'
                $sql_updKunde_1_4'$Vorname'$sql_updKunde_1_5'$Nachname'$sql_updKunde_1_6'$Firma'$sql_updKunde_1_7'$Abteilung'
              $sql_updKunde_1_8'$Strasse'$sql_updKunde_1_9'$Postfach'$sql_updKunde_1_10'$PLZ'$sql_updKunde_1_11'$Ort'
              $sql_updKunde_1_12'$Land'$sql_updKunde_1_13'$Tel'$sql_updKunde_1_14'$Fax'$sql_updKunde_1_15'$Email'
              $sql_updKunde_1_16'$Einkaufsvolumen'$sql_updKunde_1_17'$Login'$sql_updKunde_1_18'$Passwort'$sql_updKunde_1_19'$gesperrt'
              $sql_updKunde_1_20'$temp'$sql_updKunde_1_21'$Attribut1'$sql_updKunde_1_22'$Attribut2'$sql_updKunde_1_23'$Attribut3'
              $sql_updKunde_1_24'$Attribut4'$sql_updKunde_1_25'$Attributwert1'$sql_updKunde_1_26'$Attributwert2'
              $sql_updKunde_1_27'$Attributwert3'$sql_updKunde_1_28'$Attributwert4'$sql_updKunde_1_29'$Kunden_ID'"))) {
              echo "<P><H1>U_B_Error: Der Kunde konnte nicht ver&auml;ndert werden (updKunde)</H1></P>\n";
              echo "Query: $sql_updKunde_1_1'$Kunden_Nr'$sql_updKunde_1_2'$Session_ID'$sql_updKunde_1_3'$Anrede'
                $sql_updKunde_1_4'$Vorname'$sql_updKunde_1_5'$Nachname'$sql_updKunde_1_6'$Firma'$sql_updKunde_1_7'$Abteilung'
              $sql_updKunde_1_8'$Strasse'$sql_updKunde_1_9'$Postfach'$sql_updKunde_1_10'$PLZ'$sql_updKunde_1_11'$Ort'
              $sql_updKunde_1_12'$Land'$sql_updKunde_1_13'$Tel'$sql_updKunde_1_14'$Fax'$sql_updKunde_1_15'$Email'
              $sql_updKunde_1_16'$Einkaufsvolumen'$sql_updKunde_1_17'$Login'$sql_updKunde_1_18'$Passwort'$sql_updKunde_1_19'$gesperrt'
              $sql_updKunde_1_20'$temp'$sql_updKunde_1_21'$Attribut1'$sql_updKunde_1_22'$Attribut2'$sql_updKunde_1_23'$Attribut3'
              $sql_updKunde_1_24'$Attribut4'$sql_updKunde_1_25'$Attributwert1'$sql_updKunde_1_26'$Attributwert2'
              $sql_updKunde_1_27'$Attributwert3'$sql_updKunde_1_28'$Attributwert4'$sql_updKunde_1_29'$Kunden_ID'<BR>";
              die ("<B>1.) Kunden-ID ist: </B>$Kunden_ID<BR><B>2.) Vorname, Name: </B>$Vorname, $Name<BR>");
          }//End if
          return true;
      }//End else
  }//End function updKunde

  // -----------------------------------------------------------------------
  // Dies ist ein Update aller vom Kunden beinflussbaren Felder seines Datensatzes
  // Argumente: In einzelnen Namen: Alle vom Kunden beeinflussbaren Felder
  // Rueckgabewert: true bei Erfolg, sonst Abbruch via die die-Funktion
  function updKundenFelder($Session_ID,$Anrede,$Vorname,$Nachname,$Firma,$Abteilung,
              $Strasse,$Postfach,$PLZ,$Ort,$Land,$Tel,$Fax,$Email,$Attribut1,$Attribut2,
              $Attribut3,$Attribut4,$Attributwert1,$Attributwert2,$Attributwert3,
              $Attributwert4,$Kunden_ID) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_updKundenFelder_1_1;
      global $sql_updKundenFelder_1_2;
      global $sql_updKundenFelder_1_3;
      global $sql_updKundenFelder_1_4;
      global $sql_updKundenFelder_1_5;
      global $sql_updKundenFelder_1_6;
      global $sql_updKundenFelder_1_7;
      global $sql_updKundenFelder_1_8;
      global $sql_updKundenFelder_1_9;
      global $sql_updKundenFelder_1_10;
      global $sql_updKundenFelder_1_11;
      global $sql_updKundenFelder_1_12;
      global $sql_updKundenFelder_1_13;
      global $sql_updKundenFelder_1_14;
      global $sql_updKundenFelder_1_15;
      global $sql_updKundenFelder_1_16;
      global $sql_updKundenFelder_1_17;
      global $sql_updKundenFelder_1_18;
      global $sql_updKundenFelder_1_19;
      global $sql_updKundenFelder_1_20;
      global $sql_updKundenFelder_1_21;
      global $sql_updKundenFelder_1_22;
      global $sql_updKundenFelder_1_23;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (updKundenFelder)</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // ...sowie Fehlerbehandlung und Ausgabe, sieht etwas unuebersichtlich aus
          if (!($Kunden_ID = $Database->Exec("$sql_updKundenFelder_1_1'$Session_ID'$sql_updKundenFelder_1_2'$Anrede'
                $sql_updKundenFelder_1_3'$Vorname'$sql_updKundenFelder_1_4'$Nachname'$sql_updKundenFelder_1_5'$Firma'$sql_updKundenFelder_1_6'$Abteilung'
              $sql_updKundenFelder_1_7'$Strasse'$sql_updKundenFelder_1_8'$Postfach'$sql_updKundenFelder_1_9'$PLZ'$sql_updKundenFelder_1_10'$Ort'
              $sql_updKundenFelder_1_11'$Land'$sql_updKundenFelder_1_12'$Tel'$sql_updKundenFelder_1_13'$Fax'$sql_updKundenFelder_1_14'$Email'
              $sql_updKundenFelder_1_15'".addslashes($Attribut1)."'$sql_updKundenFelder_1_16'".addslashes($Attribut2)."'$sql_updKundenFelder_1_17'".addslashes($Attribut3)."'
              $sql_updKundenFelder_1_18'".addslashes($Attribut4)."'$sql_updKundenFelder_1_19'$Attributwert1'$sql_updKundenFelder_1_20'$Attributwert2'
              $sql_updKundenFelder_1_21'$Attributwert3'$sql_updKundenFelder_1_22'$Attributwert4'$sql_updKundenFelder_1_23'$Kunden_ID'"))) {
              echo "<P><H1>U_B_Error: Der Kunde konnte nicht ver&auml;ndert werden (updKundenFelder)</H1></P>\n";
              echo "Query: $sql_updKundenFelder_1_1'$Session_ID'$sql_updKundenFelder_1_2'$Anrede'
                $sql_updKundenFelder_1_3'$Vorname'$sql_updKundenFelder_1_4'$Nachname'$sql_updKundenFelder_1_5'$Firma'$sql_updKundenFelder_1_6'$Abteilung'
              $sql_updKundenFelder_1_7'$Strasse'$sql_updKundenFelder_1_8'$Postfach'$sql_updKundenFelder_1_9'$PLZ'$sql_updKundenFelder_1_10'$Ort'
              $sql_updKundenFelder_1_11'$Land'$sql_updKundenFelder_1_12'$Tel'$sql_updKundenFelder_1_13'$Fax'$sql_updKundenFelder_1_14'$Email'
              $sql_updKundenFelder_1_15'$Attribut1'$sql_updKundenFelder_1_16'$Attribut2'$sql_updKundenFelder_1_17'$Attribut3'
              $sql_updKundenFelder_1_18'$Attribut4'$sql_updKundenFelder_1_19'$Attributwert1'$sql_updKundenFelder_1_20'$Attributwert2'
              $sql_updKundenFelder_1_21'$Attributwert3'$sql_updKundenFelder_1_22'$Attributwert4'$sql_updKundenFelder_1_23'$Kunden_ID'<BR>";
              die ("<B>1.) Kunden-ID ist: </B>$Kunden_ID<BR><B>2.) Vorname, Name: </B>$Vorname, $Name<BR>");
          }//End if
          return true;
      }//End else
  }//End function updKundenFelder

  // -----------------------------------------------------------------------
  // Ordnet eine Bestellung einem Kunden zu (in Tabelle bestellung_kunde)
  // Argumente: Bestellungs_ID, Kunden_ID
  // Rueckgabewert: true bei Erfolg sonst Abbruch via die-Funktion
  function gibBestellung_an_Kunde($Bestellungs_ID, $Kunden_ID) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_gibBestellung_an_Kunde_1_1;
      global $sql_gibBestellung_an_Kunde_1_2;
      global $sql_gibBestellung_an_Kunde_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (gibBestellung_an_Kunde)</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Exec($sql_gibBestellung_an_Kunde_1_1.$Bestellungs_ID.$sql_gibBestellung_an_Kunde_1_2.$Kunden_ID.$sql_gibBestellung_an_Kunde_1_3);
          if(!$RS) {
              echo "<B>Query:</B> ".$sql_gibBestellung_an_Kunde_1_1.$Bestellungs_ID.$sql_gibBestellung_an_Kunde_1_2.$Kunden_ID.$sql_gibBestellung_an_Kunde_1_3."<BR>";
              die("<P><H1>U_B_Error: Konnte die Bestellung nicht dem Kunden zuweisen (gibBestellung_an_Kunde)</H1></P>\n");
          }
          return true;
      }//End else
  }//End function gibBestellung_an_Kunde

  // -----------------------------------------------------------------------
  // Loescht alle Bestellungen eines Kunden. Wird benoetigt um persistente
  // Kunden und ihre Bestellungen und deren Referenzen zum zu loeschenden
  // Kunden zu entfernen.
  // Zuerst werden alle zu loeschenden Bestellungs_IDs ausgelesen, dann alle
  // Referenzen vom Kunden zu den Bestellungen geloescht und zum Schluss die
  // Bestellungen entfernt.
  // Argumente: Kunden_ID
  // Rueckgabewert: true bei Erfolg sonst Abbruch via die-Funktion
  function delBestellung_von_Kunde($Kunden_ID) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_delBestellung_von_Kunde_1_1;
      global $sql_delBestellung_von_Kunde_1_2;
      global $sql_delBestellung_von_Kunde_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (delBestellung_von_Kunde)</H1></P>\n");
      }
      else {
          // Kunden_ID fuer die Datenbank als String darstellen (wegen Kompatibilitaet zu MySQL 3.22.x):
          $Kunden_ID = "'".$Kunden_ID."'";

          // 1.) Zuerst werden die Bestellungs_IDs der zu loeschenden Bestellungen ausgelesen (Bestellungen welche dem
          //     zu loeschenden Kunden gehoeren) -> Alle Resultate in Array abfuellen (Tabelle bestellung_kunde)
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $Bestellungsarray = array();
          $RS = $Database->Query($sql_delBestellung_von_Kunde_1_1.$Kunden_ID);
          if(!$RS) {
              echo "<B>Query:</B> ".$sql_delBestellung_von_Kunde_1_1.$Kunden_ID."<BR>";
              die("<P><H1>U_B_Error: Konnte die Bestellung eines Kunden nicht loeschen (delBestellung_von_Kunde)_1</H1></P>\n");
          }
          while (is_object($RS) && $RS->NextRow()) {
              $Bestellungsarray[] = $RS->GetField("FK_Bestellungs_ID");
          }
          // 2.) Referenzen von Kunde zu Bestellung loeschen (Tabelle bestellung_kunde)
          $RS = $Database->Exec($sql_delBestellung_von_Kunde_1_2.$Kunden_ID);
          if(!$RS) {
              echo "<B>Query:</B> ".$sql_delBestellung_von_Kunde_1_2.$Kunden_ID."<BR>";
              die("<P><H1>U_B_Error: Konnte die Bestellung eines Kunden nicht loeschen (delBestellung_von_Kunde)_2</H1></P>\n");
          }
          // 3.) Bestellungen des zu loeschenden Kunden loeschen (Tabelle bestellung)
          foreach ($Bestellungsarray as $key=>$value) {
              $RS = $Database->Exec($sql_delBestellung_von_Kunde_1_3.$value);
              if(!$RS) {
                  echo "<B>Query:</B> ".$sql_delBestellung_von_Kunde_1_3.$value."<BR>";
                  die("<P><H1>U_B_Error: Konnte die Bestellung eines Kunden nicht loeschen (delBestellung_von_Kunde)_3</H1></P>\n");
              }
          }// End foreach
          return true;
      }//End else
  }//End function gibBestellung_an_Kunde

  // -----------------------------------------------------------------------
  // Gibt ein Attributobjekt zurueck (darin befinden sich in Arrays alle
  // Kunden-Attribute sortiert nach ihrer jeweiligen Positions-Nr)
  // Die Klassendefinition eines Attributs findet sich in attribut_def.php
  // Argumente: keine
  // Rueckgabewert: Attributobjekt
  function getAttributobjekt() {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getAttributobjekt_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (getAttributobjekt)</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Query($sql_getAttributobjekt_1_1);
          if(!$RS) {
              echo "<B>Query:</B> ".$sql_getAttributobjekt_1_1."<BR>";
              die("<P><H1>U_B_Error: \$RS != true: Konnte Attribute nicht auslesen (getAttributobjekt)</H1></P>\n");
          }
          $myAttributobjekt = new Attribut;
          while (is_object($RS) && $RS->NextRow()){
              $myAttributobjekt->putAttribut($RS->GetField("Attribut_ID"),$RS->GetField("Name"),
              $RS->GetField("Wert"),$RS->GetField("anzeigen"),$RS->GetField("in_DB"),
              $RS->GetField("Eingabe_testen"),$RS->GetField("Positions_Nr"));
          }
          return $myAttributobjekt;
      }//End else
  }//End function getAttributobjekt

  // -----------------------------------------------------------------------
  // Setzt alle Kunden-Attribute in der Tabelle attribut
  // sortiert nach ihrer jeweiligen Positions-Nr
  // Die Klassendefinition eines Attributs findet sich in attribut_def.php
  // Argumente: Attributobjekt
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion
  function setAttributobjekt($myAttributobjekt) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_setAttributobjekt_1_1;
      global $sql_setAttributobjekt_1_2;
      global $sql_setAttributobjekt_1_3;
      global $sql_setAttributobjekt_1_4;
      global $sql_setAttributobjekt_1_5;
      global $sql_setAttributobjekt_1_6;
      global $sql_setAttributobjekt_1_7;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (setAttributobjekt)</H1></P>\n");
      }
      else {
          // Zuerst werden die Arrays des Artikelobjekts ausgelesen und
          // in entsprechende temporaere Variablen abgelegt
          $myAttribut_ID = $myAttributobjekt->getallAttribut_ID();
          $myName = $myAttributobjekt->getallName();
          $myWert = $myAttributobjekt->getallWert();
          $myanzeigen = $myAttributobjekt->getallanzeigen();
          $myin_DB = $myAttributobjekt->getallin_DB();
          $myEingabe_testen = $myAttributobjekt->getallEingabe_testen();
          $myPositions_Nr = $myAttributobjekt->getallPositions_Nr();
          // In einer for-Schleife von 0 bis Anzahl Attribute - 1 werden
          // jetzt zeilenweise alle Attribute upgedated
          for ($i = 0;$i < $myAttributobjekt->attributanzahl();$i++) {
              // Update ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
              $RS = $Database->Exec($sql_setAttributobjekt_1_1.$myName[$i].$sql_setAttributobjekt_1_2.$myWert[$i].$sql_setAttributobjekt_1_3.$myanzeigen[$i].$sql_setAttributobjekt_1_4.$myin_DB[$i].$sql_setAttributobjekt_1_5.$myEingabe_testen[$i].$sql_setAttributobjekt_1_6.$myPositions_Nr[$i].$sql_setAttributobjekt_1_7.$myAttribut_ID[$i]);
              if(!$RS) {
                 echo "<BR><B>Query:</B> ".$sql_setAttributobjekt_1_1.$myName[$i].$sql_setAttributobjekt_1_2.$myWert[$i].$sql_setAttributobjekt_1_3.$myanzeigen[$i].$sql_setAttributobjekt_1_4.$myin_DB[$i].$sql_setAttributobjekt_1_5.$myEingabe_testen[$i].$sql_setAttributobjekt_1_6.$myPositions_Nr[$i].$sql_setAttributobjekt_1_7.$myAttribut_ID[$i]."<BR>";
                 echo "<B>Counter \$i=</B>$i <B>von </B> ".$myAttributobjekt->attributanzahl()."<BR>";
                 die("<P><H1>U_B_Error: \$RS != true: Konnte Attribute nicht updaten (setAttributobjekt)</H1></P>\n");
              }
          }//End for
          return true;
      }//End else
  }//End function setAttributobjekt

  // -----------------------------------------------------------------------
  // Diese Funktion ueberprueft die eingegebenen Login-Daten und veranlasst
  // dann, je nach Eingabe:
  // - einen neuen temporaeren Kunden anzulegen
  // - die Kunden_ID eines persistenten Kunden auslesen und seine Session_ID updaten
  // Argumente: $Login, $Passwort, $Session_ID
  // Rueckgabewert:
  // - User existiert, Passwort stimmt        -> existierende Kunden_ID
  // - User existiert, Passwort stimmt nicht  -> 'P'
  // - User existiert nicht                   -> neue Kunden_ID
  function checkLogin($Login,$Passwort,$Session_ID) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_checkLogin_1_1;
      global $sql_checkLogin_1_2;
      global $sql_checkLogin_1_3;
      global $sql_checkLogin_1_4;
      global $sql_checkLogin_1_5;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (checkLogin)</H1></P>\n");
      }
      else {
          // Wenn die uebergebene Variable $Login leer ist, so handelt es sich um einen temporaeren
          // Kunden, welcher nur solange existiert, wie man ihn benoetigt um die Bestellung abzu-
          // wickeln. Bei eingeschaltetem Bestellungsmanagement, bleibt er noch solange in der Daten-
          // bank, bis die Bestellung dann im Bestellungsmgmt. vom Administrator geloescht wird.
          if (trim($Login) == "") {
              // Eine neue einmalige Kunden_ID erstellen. Diese Kunden_ID ist sehr gross gewaehlt, sodass
              // die Aussicht auf Erfolg vom erraten von Kundennummern nahezu gleich Null ist (siehe auch Dokumentation).
              // Eine Kunden_ID besteht aus dreimal hintereinander gehaengten Zufallszahlen aus dem
              // Zahlenraum von 1 bis $RAND_MAX.
              $war_drin = false; // Flag welches true wird, wenn mindestens einmal eine Kunden_ID erstellt wurde (Fall: Erster Kunde $RS hat null Zeilen)
              do {
                  $nochmals = false; // Wenn $nochmals = false -> Kunden_ID = unique
                  $RS = $Database->Query($sql_checkLogin_1_2);
                  if(!$RS) {
                      echo "<B>Query:</B> ".$sql_checkLogin_1_2."<BR>";
                      die("<P><H1>U_B_Error: \$RS != true: Konnte das Login nicht ueberpruefen! (checkLogin)_2</H1></P>\n");
                  }
                  while (is_object($RS) && $RS->NextRow()) {
                      // Drei Zufallszahlen berechnen (mit srand initialisieren) und dann als String zusammensetzen
                      srand((double)microtime() * 1000000);
                      $rand1 = rand();
                      $rand2 = rand();
                      $rand3 = rand();
                      $Kunden_ID = $rand1.$rand2.$rand3;
                      // Testen, ob es eine gleiche Kunden_ID schon gibt, dann eine neue berechnen
                      if ($Kunden_ID == $RS->GetField("Kunden_ID")) {
                          $nochmals = true;
                      }// End if
                      $war_drin = true;
                  }// End while

                  // Falls $RS null Zeilen hatte, so muss hier explizit ein 'erster' Lauf erzeugt werden. Da es sich hier
                  // um den ersten Kunden handelt muss die erzeugte Kunden_ID auch nicht auf Dupletten geprueft werden:
                  if ($war_drin == false) {
                      // Drei Zufallszahlen berechnen (mit srand initialisieren) und dann als String zusammensetzen
                      srand((double)microtime() * 1000000);
                      $rand1 = rand();
                      $rand2 = rand();
                      $rand3 = rand();
                      $Kunden_ID = $rand1.$rand2.$rand3;
                      $war_drin = true;
                  }
              } while ($nochmals); // End do-while
              newKunde($Kunden_ID,$Session_ID,0,"","","","","","","","","","","","","",0.0,
                       $Kunden_ID,'@PhPepperShop@','N','Y',"","","","","","","","");
              return $Kunden_ID;
          }// End if Login == ""
          else {
              // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
              $RS = $Database->Query($sql_checkLogin_1_1);
              if(!$RS) {
                  echo "<B>Query:</B> ".$sql_checkLogin_1_1."<BR>";
                  die("<P><H1>U_B_Error: \$RS != true: Konnte das Login nicht ueberpruefen! (checkLogin)</H1></P>\n");
              }
              $match = false; // Wenn ein schon gespeicherter Kunde gefunden wird: = true
              while (is_object($RS) && $RS->NextRow()){
                  // Uebergebenes Login ueberpruefen (suchen, ob schon vorhanden)
                  if (strtoupper(trim($Login)) == strtoupper($RS->GetField("Login"))) {
                      // Schon vorhandener Kunde gefunden
                      $match = true;
                  }
                  if ($match) {
                      // Uebergebenes Passwort ueberpruefen -> wenn falsch -> P zurueck geben
                      // -> wenn true, so ist ein bestehender persistenter User gefunden worden
                      // Dann soll seine Session_ID in der kunde-Tabelle aktualisiert werden
                      if (trim($Passwort) == $RS->GetField("Passwort")) {
                          //Auslesen der Kunden_ID
                          $Kunden_ID = $RS->GetField("Kunden_ID");
                          //Update der Session_ID
                          $RS = $Database->Exec($sql_checkLogin_1_3.$Session_ID.$sql_checkLogin_1_4.$Login.$sql_checkLogin_1_5);
                          if(!$RS) {
                              echo "<B>Query:</B> ".$sql_checkLogin_1_3.$Session_ID.$sql_checkLogin_1_4.$Kunden_ID.$sql_checkLogin_1_5."<BR>";
                              die("<P><H1>U_B_Error: \$RS != true: Konnte das Session_ID eines bestehenden Users nicht updaten! (checkLogin)_3</H1></P>\n");
                          }
                          //Funktion beenden und Kunden_ID zurueckschreiben
                          return $Kunden_ID;
                      }
                      else {
                          return "P";
                      }
                  }
              }
              // Wenn wir hierhin kommen, so wurde kein schon existierender User gefunden
              // Es soll ein neuer persistenter User angelegt werden:

              // Eine neue einmalige Kunden_ID erstellen. Diese Kunden_ID ist sehr gross gewaehlt, sodass
              // die Aussicht auf Erfolg vom erraten von Kundennummern nahezu gleich Null ist
              // Eine Kunden_ID besteht aus dreimal hintereinander gehaengten Zufallszahlen aus dem
              // Zahlenraum 1 bis $RAND_MAX
              // Damit die Kunden_ID auch wirklich eindeutig (unique) ist, wird in einer Schleife kurz
              // die neu generierte Kunden_ID mit den schon bestehenden verglichen
              $war_drin = false; // Flag welches true wird, wenn mindestens einmal eine Kunden_ID erstellt wurde (Fall: Erster Kunde $RS hat null Zeilen)
              do {
                  $nochmals = false; // Wenn $nochmals = false -> Kunden_ID = unique
                  $RS = $Database->Query($sql_checkLogin_1_2);
                  if(!$RS) {
                      echo "<B>Query:</B> ".$sql_checkLogin_1_2."<BR>";
                      die("<P><H1>U_B_Error: \$RS != true: Konnte das Login nicht ueberpruefen! (checkLogin)_2</H1></P>\n");
                  }
                  while (is_object($RS) && $RS->NextRow()) {
                      // Drei Zufallszahlen berechnen (mit srand initialisieren) und dann als String zusammensetzen
                      srand((double)microtime() * 1000000);
                      $rand1 = rand();
                      $rand2 = rand();
                      $rand3 = rand();
                      $Kunden_ID = $rand1.$rand2.$rand3;
                      // Testen, ob es eine gleiche Kunden_ID schon gibt, dann eine neue berechnen
                      if ($Kunden_ID == $RS->GetField("Kunden_ID")) {
                          $nochmals = true;
                      }// End if
                      $war_drin = true;
                  }// End while

                  // Falls $RS null Zeilen hatte, so muss hier explizit ein 'erster' Lauf erzeugt werden. Da es sich hier
                  // um den ersten Kunden handelt muss die erzeugte Kunden_ID auch nicht auf Dupletten geprueft werden:
                  if ($war_drin == false) {
                      // Drei Zufallszahlen berechnen (mit srand initialisieren) und dann als String zusammensetzen
                      srand((double)microtime() * 1000000);
                      $rand1 = rand();
                      $rand2 = rand();
                      $rand3 = rand();
                      $Kunden_ID = $rand1.$rand2.$rand3;
                      $war_drin = true;
                  }
              } while ($nochmals); // End do-while
              newKunde($Kunden_ID,$Session_ID,0,"","","","","","","","","","","","","",0.0,
                       $Login,$Passwort,'N','N',"","","","","","","","");
              return $Kunden_ID;
          }// End else Login == ""
      }//End else
  }//End function checkLogin

  // -----------------------------------------------------------------------
  // Diese Funktion gibt die Kunden_ID zurueck, falls ein Kunde eingeloggt ist
  // und sich schon authentifiziert hat
  // Argument: $Session_ID
  // Rueckgabewert:
  // - User existiert und ist eingeloggt -> Kunden_ID (String)
  // - User nicht eingeloggt -> "" (Leerstring)
  function checkSession($Session_ID) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_checkSession_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (checkSession)</H1></P>\n");
      }
      else {
          $RS = $Database->Query($sql_checkSession_1_1);
          if(!$RS) {
              echo "<B>Query:</B> ".$sql_checkSession_1_1."<BR>";
              die("<P><H1>U_B_Error: \$RS != true: Konnte Sessions nicht abrufen! (checkSession)</H1></P>\n");
          }
          while (is_object($RS) && $RS->NextRow()) {
              if ($Session_ID == $RS->GetField("Session_ID")) {
                  return $RS->GetField("Kunden_ID");// Gefundene Kunden_ID zurueckgeben
              }//End if
          }//End while
          return ""; // Wenn keine Kunden_ID gefunden wurde, einen leeren String zurueckgeben
      }//End else
  }//End function checkSession

  // -----------------------------------------------------------------------
  // Diese Funktion sendet dem Benutzer, der sein Passwort vergessen hat sein
  // Passwort an seine im System gespeicherte E-Mail Adresse. Zur 'Verifikation'
  // muss er sein Login-Name eingeben. (Zuordnung zu E-Mail-Adresse)
  // Argument: $Login (String)
  // Rueckgabewert: true bei Erfolg, false wenn kein Login gefunden wurde, sonst Abbruch per die-Funktion
  function mailPasswort($Login) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_mailPasswort_1_1;
      global $sql_mailPasswort_1_2;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (mailPasswort)</H1></P>\n");
      }
      else {
          $RS = $Database->Query($sql_mailPasswort_1_1.$Login.$sql_mailPasswort_1_2);
          if(!$RS) {
              echo "<B>Query:</B> ".$sql_mailPasswort_1_1.$Login.$sql_mailPasswort_1_2."<BR>";
              die("<P><H1>U_B_Error: \$RS != true: Konnte nicht nach vergessenem Passwort suchen! (mailPasswort)</H1></P>\n");
          }
          $gefunden = false; // Flag, falls kein entsprechendes Login gefunden wurde -> return false
          while (is_object($RS) && $RS->NextRow()) {
              if (strtoupper(trim($Login)) == strtoupper($RS->GetField("Login"))) {
                  $Passwort = $RS->GetField("Passwort");
                  $Email = $RS->GetField("Email");
                  $gefunden = true;
                  break;
              }//End if
          }//End while
          if (!$gefunden || $Email == "") {
              return false;
          }
          else {
              // Aktuelles Datum berechnen (wird dem Mailheader angehaengt)
              $mydate = getdate();
              $Datum = $mydate[mday].".".$mydate[mon].".".$mydate[year];// Format dd-mm-yyyy
              $message=$Datum."\n";
              // Passwort anhaengen
              $message.="\n\n\nIhr vergessenes Passwort von ".getshopname().":\n------------------------------------------------------\n\n";
              $message.="\n";
              $message.="   Login: ".$Login."\n";
              $message.="   Passwort: ".$Passwort."\n";
              $message.="\n\n";
              $message.="Wir hoffen, Ihnen damit geholfen zu haben\nund freuen uns schon auf Ihren nächsten Besuch bei uns.\n";
              $message.="\nMit freundlichen Grüssen\n\nIhr ".getshopname()."-Team\n";
              //Mail an Shopkunden versenden
              $to=$Email;
              $subject="Ihr Passwort bei ".getshopname();
              $header="From: ".getShopEmail();
              // notwendig, damit deutsche Umlaute richtig angezeigt werden
              $header.="\nContent-Type: text/plain; charset=iso-8859-1";
              if (!mail ($to, $subject, $message, $header)){
                die("<h1 class='content'>Probleme beim Mailversand.. Bitte nehmen sie per E-Mail oder telefonisch Kontakt mit uns auf! (mailPasswort)</h1>");
              }
              return true;
          }
      }//End else
  }//End function mailPasswort

  // -----------------------------------------------------------------------
  // Diese Funktion addiert das Bestellungstotal zum Einkaufsvolumen eines
  // Kunden, ebenso wird das LetzteBestellung-Attribut erneuert
  // Diese Funktion wurde im seit PhPepperShop v.1.4 nicht mehr benutzten
  // Beta-Bestellungsmanagement verwendet. Sie wird ev. spaeter wieder im
  // neuen Kundenmanagement verwendet. Wenn Preise exkl. MwSt verwendet
  // werden, nicht vergessen, dass das Argument $Betrag nicht nur das
  // Bestellungsattribut $Rechnungsbetrag beinhaltet, sondern dazu addiert
  // auch noch das Attribut $MwSt (bei inkl. MwSt Preisen nicht noetig).
  // Argument: $Session_ID (String), $Betrag (Double)
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion
  function addEinkaufsvolumen($Session_ID, $Betrag) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_addEinkaufsvolumen_1_1;
      global $sql_addEinkaufsvolumen_1_2;
      global $sql_addEinkaufsvolumen_1_3;
      global $sql_addEinkaufsvolumen_1_4;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (addEinkaufsvolume)</H1></P>\n");
      }
      else {
          // Aktuelles Datum holen
          $mydate = getdate();
          // Datum entsprechend formatieren um es in die Datenbank einfuegen zu koennen
          $Datum = $mydate[year]."-".$mydate[mon]."-".$mydate[mday];// Format yyyy-mm-dd
          // Update ausfuehren
          $RS = $Database->Query($sql_addEinkaufsvolumen_1_1.$Betrag.$sql_addEinkaufsvolumen_1_2.$Datum.$sql_addEinkaufsvolumen_1_3.$Session_ID.$sql_addEinkaufsvolumen_1_4);
          if(!$RS) {
              echo "<B>Query:</B> ".$sql_addEinkaufsvolumen_1_1.$Betrag.$sql_addEinkaufsvolumen_1_2.$Datum.$sql_addEinkaufsvolumen_1_3.$Session_ID.$sql_addEinkaufsvolumen_1_4."<BR>";
              die("<P><H1>U_B_Error: \$RS != true: Konnte Einkaufsvolumen nicht addieren! (addEinkaufsvolumen)</H1></P>\n");
          }
          return true;
      }//End else
  }//End function addEinkaufsvolumen

  // -----------------------------------------------------------------------
  // Hier werden die zur Bestellung gehoerenden Daten eines Kunden zwischengespeichert
  // (Anmerkung, Datum, Bezahlungsart, Kreditkartendaten, Zusatzattribute1 bis 4 und ihre Namen)
  // Argumente: Session_ID, damit man weiss zu welcher Bestellung die Daten gehoeren werden
  //            Bestellungs-Objekt, enthaelt die User-Daten (Def. siehe bestellung_def.php)
  // Rueckgabewert: true bei Erfolg, Abbruch per die-Funktion bei allfaelligem Fehler
  function updBestellungsFelder($Session_ID, $Bestellung) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_updBestellungsFelder_1_1;
      global $sql_updBestellungsFelder_1_2;
      global $sql_updBestellungsFelder_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: updBestellungsFelder</H1></P><BR>");
      }
      else {
          // Bevor wir die Daten in der Datenbank speichern, fuellen wir alles
          // in den String $Bestellungsdaten ab. So dass der Update Befehl alles korrekt erkennt
          $Bestellungsdaten = "Datum='".$Bestellung->Datum."', Bezahlungsart='".$Bestellung->Bezahlungsart."', Kreditkarten_Hersteller='".addslashes($Bestellung->Kreditkarten_Hersteller).
                         "', Kreditkarten_Nummer='".addslashes($Bestellung->Kreditkarten_Nummer)."', Kreditkarten_Ablaufdatum='".addslashes($Bestellung->Kreditkarten_Ablaufdatum)."', Kreditkarten_Vorname='".addslashes($Bestellung->Kreditkarten_Vorname).
                         "', Kreditkarten_Nachname='".addslashes($Bestellung->Kreditkarten_Nachname)."', Attribut1='".addslashes($Bestellung->Attribut1)."', Attribut2='".addslashes($Bestellung->Attribut2).
                         "', Attribut3='".addslashes($Bestellung->Attribut3)."', Attribut4='".addslashes($Bestellung->Attribut4)."', Attributwert1='".addslashes($Bestellung->Attributwert1).
                         "', Anmerkung='".addslashes($Bestellung->Anmerkung)."', Attributwert2='".addslashes($Bestellung->Attributwert2).
                         "', Attributwert3='".addslashes($Bestellung->Attributwert3)."', Attributwert4='".addslashes($Bestellung->Attributwert4)."', clearing_id='".addslashes($Bestellung->clearing_id)."'";
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Exec("$sql_updBestellungsFelder_1_1".$Bestellungsdaten."$sql_updBestellungsFelder_1_2".$Session_ID."$sql_updBestellungsFelder_1_3");
          if (!$RS) {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1 class='content'>U_B_Error: Fehler beim Update der Bestellungs-Attribute (updBestellungsFelder)</H1></P><BR>";
              die("Query: $sql_updBestellungsFelder_1_1".$Bestellungsdaten."$sql_updBestellungsFelder_1_2".$Session_ID."$sql_updBestellungsFelder_1_3<BR>");
          }
          return true;
      }//End else
  }//End function updBestellungsFelder

  // -----------------------------------------------------------------------
  // Da bei der Kreditkartenzahlung mit externer Zahlungsabwicklung
  // der E-Mail-Message String verloren gehen wuerde, wird dieser temporaer
  // in der jeweiligen Bestellung zwischengespeichert. Diese Funktion liest
  // den temporaer zwischen gespeicherten E-Mail-Message-String aus und
  // LOESCHT den E-Mail-Message-String in der entsprechenden Bestellung!
  // Argument: Session_ID
  // Rueckgabewert: String oder Abbruch per die-Funktion
  function getEmailMessage($Session_ID) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getEmailMessage_1_1;
      global $sql_getEmailMessage_1_2;
      global $sql_getEmailMessage_1_3;
      global $sql_getEmailMessage_1_4;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (getEmailMessage)</H1></P>\n");
      }
      else {
          // Auslesen des E-Mail-Message-Strings
          $RS = $Database->Query($sql_getEmailMessage_1_1.$Session_ID.$sql_getEmailMessage_1_2);
          if (is_object($RS) && $RS->NextRow()) {
              $Email = $RS->GetField("temp_message_string");
          }
          else {
              echo "<B>Query:</B> ".$sql_getEmailMessage_1_1.$Session_ID.$sql_getEmailMessage_1_2."<BR>";
              die("<P><H1>U_B_Error: \$RS != true: Konnte E-Mail-Message-String nicht auslesen! (getEmailMessage)_1</H1></P>\n");
          }
          // Loeschen (Ueberschreiben) des temporaeren E-Mail-Message-Strings in der bestellen-Tabelle
          $RS = $Database->Exec($sql_getEmailMessage_1_3.$Session_ID.$sql_getEmailMessage_1_4);
          if (!$RS) {
              echo "<B>Query:</B> ".$sql_getEmailMessage_1_3.$Session_ID.$sql_getEmailMessage_1_4."<BR>";
              die("<P><H1>U_B_Error: \$RS != true: Konnte E-Mail-Message-String nicht l&ouml;schen! (getEmailMessage)_1</H1></P>\n");
          }
          return $Email;
      }//End else
  }//End function getEmailMessage

  // -----------------------------------------------------------------------
  // Da bei der Kreditkartenzahlung mit externer Zahlungsabwicklung
  // der E-Mail-Message String verloren gehen wuerde, wird dieser temporaer
  // in der jeweiligen Bestellung zwischengespeichert. Mit dieser Funktion
  // kann man den E-Mail-Messag-String temporaer der Bestellung des Kunden
  // zuweisen.
  // Argument: String
  // Rueckgabewert: true bei Erfolg oder Abbruch per die-Funktion
  function putEmailMessage($Emailstring,$Session_ID) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_putEmailMessage_1_1;
      global $sql_putEmailMessage_1_2;
      global $sql_putEmailMessage_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (putEmailMessage)</H1></P>\n");
      }
      else {
          // Auslesen des E-Mail-Message-Strings
          $RS = $Database->Exec($sql_putEmailMessage_1_1.$Emailstring.$sql_putEmailMessage_1_2.$Session_ID.$sql_putEmailMessage_1_3);
          if (!$RS) {
              echo "<B>Query:</B> ".$sql_putEmailMessage_1_1.$Emailstring.$sql_putEmailMessage_1_2.$Session_ID.$sql_putEmailMessage_1_3."<BR>";
              die("<P><H1>U_B_Error: \$RS != true: Konnte E-Mail-Message-String nicht speichern! (putEmailMessage)_1</H1></P>\n");
          }
          return true;
      }//End else
  }//End function putEmailMessage


  // -----------------------------------------------------------------------
  // Die folgende Funktion von Error Brett (Brett@InterWebDesign.com)
  // ueberprueft die Kreditkartennummer auf Vollstaendigkeit und Pruefsumme
  // (MOD-10 Verfahren)
  // Argument: Kreditkartennummer(String), Kreditkarten_Institut(String)
  // Rueckgabewert: - true bei korrekter Kreditkartennummer
  //                - false bei falscher Kreditkartennummer
  //                - (-1) bei nicht korrektem Kreditkarten Institut
  function validateCC($ccnum,  $type  =  'unknown'){
   // Eingabe von Leerzeichen befreien
   $type  =  strtolower($type);
   $ccnum = ereg_replace( '[-[:space:]]',  '', $ccnum);
   // Test des Kartentyps
   switch ($type) {
        case "unknown":
         break;
     case "visa":
        if (strlen($ccnum) != 13 and strlen($ccnum) != 16 or substr($ccnum,  0,  1) != "4") {
           return  0;
        }
        break;
     case "mastercard":
        if (strlen($ccnum) != 16 || !ereg("^5[1-5]",  $ccnum)) {
           return  0;
        }
        break;
     case "amex":
        if (strlen($ccnum) != 15 || !ereg("^3[47]", $ccnum)) {
              return  0;
         }
        break;
     case "discover":
        if (strlen($ccnum) != 16 || substr($ccnum,0,4) == "6011") {
            return  0;
        }
        break;
     default:
        return  -1;
    }
    //  Starte  MOD 10-Tests
    $dig  =  toCharArray($ccnum);
    $numdig  =  sizeof  ($dig);
    $j  =  0;
    for  ($i=($numdig-2);  $i>=0;  $i-=2) {
        $dbl[$j]  =  $dig[$i]  *  2;
        $j++;
     }
     $dblsz  =  sizeof($dbl);
     $validate = 0;
     for  ($i=0; $i<$dblsz; $i++){
         $add  =  toCharArray($dbl[$i]);
         for  ($j=0;$j<sizeof($add);$j++){
             $validate  +=  $add[$j];
         }
         $add  =  '';
     }
     for  ($i=($numdig-1);  $i>=0;  $i-=2){
         $validate  +=  $dig[$i];
     }
     if  (substr($validate,  -1,  1)  ==  '0')
          return  1;
     else
          return  0;
 }//End function validateCC


  // Diese Funktionen gibt eine Zeichenkette als Array zurück
  // Diese Funktion wird von validateCC benoetigt
  // Argumente: String
  // Rueckgabewert: Array der Zeichen vom Eingabestring
  function  toCharArray($input){
      $len  =  strlen($input);
      for  ($j=0; $j<$len; $j++){
         $char[$j]  =  substr($input,  $j,  1);
      }
      return  ($char);
  }//End function toCharArray

  // -----------------------------------------------------------------------
  // Wenn jemand als Zahlungsart 'Treuhandzahlung' ausgewaehlt hat, muss berechnet werden
  // wieviel der Kunde fuer diese Dienstleistung bezahlen muss. Die Kosten einer Zahlung
  // ueber einen Treuhandservice ist abhaengig von der Bestellsumme (ohne Versandkosten).
  // Diese Funktion berechnet den vom Kunden zu entrichtenden Tribut, abhaengig von der
  // angegebenen Bestellsumme ($Bestellsumme). Es wird dabei auch das Teilerverhaeltnis
  // von Versender und Kunden mitberuecksichtigt. Als Resultat gibt die Funktion einen
  // Array zurueck, welcher im ersten Feld die Treuhandkosten inkl. MwSt hat und im
  // zweiten Feld das Teilerverhaeltnis kundenseitig beschreibt (0 = Kunde muss nichts
  // bezahlen; 50 = Kunde bezahlte 50% der eigentlichen Kosten; 100 = Kunde muss die vollen
  // Treuhandkosten selbst tragen)
  // Argument: Bestellsumme (Float)
  // Rueckgabewert: Array (1. Wert = Treuhandkosten inkl. MwSt, 2. Wert = Teilerverhaeltnis)
  function getTreuhandbetrag($Bestellsumme) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getTreuhandbetrag_1_1;
      global $sql_getTreuhandbetrag_1_2;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar (getTreuhandbetrag)</H1></P>\n");
      }
      else {
          // Initialisierung von Variablen/Objekten
          $Resultatarray = array(); // Initialisierung fuer spaeteren Gebrauch des Rueckgabewertes
          $temparray = array(); // Initialisierung, Array fuer temporaeren Gebrauch (enthaelt codierte Parameter)
          $Parameterarray = array(); // Initialisierung fuer Speicherung der decodierten Treuhandzahlungsparameter
          $Treuhandzahlung = new Zahlung(); // Instanzieren eines neuen Zahlungsobjekts (Definition siehe zahlung_def.php)

          // Auslesen der Treuhandsettings aus der Tabelle zahlung_weitere und abpacken in vorher initialisiertes Zahlungsobjekt
          $RS = $Database->Query($sql_getTreuhandbetrag_1_1."Treuhandzahlung".$sql_getTreuhandbetrag_1_2);
          if (is_object($RS) && $RS->NextRow()) {
              $Treuhandzahlung->Gruppe = $RS->GetField("Gruppe");
              $Treuhandzahlung->Bezeichnung = $RS->GetField("Bezeichnung");
              $Treuhandzahlung->verwenden = $RS->GetField("verwenden");
              $Treuhandzahlung->payment_interface_name = $RS->GetField("payment_interface_name");
              $Treuhandzahlung->putparameter($RS->GetField("Par1"));
              $Treuhandzahlung->putparameter($RS->GetField("Par2"));
              $Treuhandzahlung->putparameter($RS->GetField("Par3"));
              $Treuhandzahlung->putparameter($RS->GetField("Par4"));
              $Treuhandzahlung->putparameter($RS->GetField("Par5"));
              $Treuhandzahlung->putparameter($RS->GetField("Par6"));
              $Treuhandzahlung->putparameter($RS->GetField("Par7"));
              $Treuhandzahlung->putparameter($RS->GetField("Par8"));
              $Treuhandzahlung->putparameter($RS->GetField("Par9"));
              $Treuhandzahlung->putparameter($RS->GetField("Par10"));
          }
          else {
              echo "<B>Query:</B> ".$sql_getTreuhandbetrag_1_1."Treuhandzahlung".$sql_getTreuhandbetrag_1_2."<BR>";
              die("<P><H1>U_B_Error: Konnte Treuhandzahlung nicht auslesen (zweite Zeile in der Tabelle zahlung_weitere (getTreuhandbetrag)_1</H1></P>\n");
          }

          // Decodieren der Wert1þWert2-codierten Parameterdaten und abspeichern in temporaeren Array
          $counter = 0; // Zaehler, welcher nach jeder Schleife + 2 gerechnet wird
          $temparray = $Treuhandzahlung->getallparameter(); // Auslesen der codierten Parameter
          for ($i = 0; $i < 10; $i++) {
              $Parameterarray[] = explode("þ",$temparray[$i]);
          }
          // Parameterarray enthaelt jetzt 0-9 Arrays mit je zwei Werten 0=bis Bestellsumme Wert, 1=Treuhandkostenwert.
          // Nummer 9 ist ein Spezialfall: 0=Anteil Versender, 1=Anteil Kunde (Treuhandkostenuebernahme)

          // Berechnung der Treuhandkosten
          $Anteilkunde = $Parameterarray[9][1];

          // Wenn Kundenanteil = 0 ist Resultatarray schreiben und Berechnung abschliessen
          if ($Anteilkunde == 0) {
              $Resultatarray[0] = 0; // Treuhandkosten = 0.00
              $Resultatarray[1] = 0; // Kundenanteil = 0
              return $Resultatarray;
          }
          else {
              // Der Kunde muss also fuer diesen Dienst mitbezahlen, wieviel, das wird hier berechnet:
              // Wir gehen durch jedes Bis-Preisintervall, bis wir ein passendes gefunden haben.
              $gefunden = false; // Dieses Flag wird true, wenn die Bestellsumme innerhalb der abgedeckten Summen liegt
              for ($i = 0; $i < 9; $i++) {
                  if ($Bestellsumme <= $Parameterarray[$i][0]) {
                      $Treuhandkosten = ($Anteilkunde /100) * $Parameterarray[$i][1];
                      $gefunden = true;
                      break;
                  }
              }
              // Wenn die Bestellsumme ausserhalb des abgedeckten Wertes liegt, so werden die Treuhandkosten ueber eine Proportionsrechnung
              // mit dem hoechsten Satz angenaehert und vorausgesagt. Ev. muss diese Ausnahmeregelung noch ueberarbeitet oder angepasst werden.
              if ($gefunden == false) {
                  // 1.) Ermitteln wo der hoechste Satz steht
                  $Parameternr = 0; // Initialisieren
                  $Parameterwert = 0; // Initialisieren
                  for ($i = 0; $i < 9; $i++) {
                      if ($Parameterarray[$i][1] > $Parameterwert) {
                          $Parameternr = $i;
                          $Parameterwert = $Parameterarray[$i][1];
                      }
                  }
                  // 2.) Berechnen der Treuhandkosten anhand eines Dreisatzes
                  $Bestellsumme1 = $Parameterarray[$Parameternr][0];
                  $Treuhandkosten1 = $Parameterarray[$Parameternr][1];
                  $Treuhandkosten = ($Anteilkunde/100) * (($Treuhandkosten1/$Bestellsumme1)*$Bestellsumme);
              }
              // Berechneten Treuhandkostenwert inkl. Kundenanteil in Resultatarray schreiben und zurueckgeben
              $Resultatarray[0] = $Treuhandkosten;
              $Resultatarray[1] = $Anteilkunde;
              return $Resultatarray;
          }
      }//End else
  }//End function getTreuhandbetrag

  // Diese Funktion liefert die Bankverbindungsdaten des angegebenen Kunden (Kontoinhaber, Bankname, BLZ, Kontonummer, Einstellungen_speichern)
  // Das Argument kann entweder eine Session_ID des Kunden oder aber seine Kunden_ID sein. Achtung, wenn man eine Kunden_ID ueber eine URL oder ein
  // POST-Formular sendet, wird diese autom. von PHP als einen String uebergeben, dann greift dieser Unterscheidungsmechanismus nicht.
  // Der Rueckgabewert ist ein assoziativer Array mit den oben erwaehnten Namen als Schluessel (alles in Kleinbuchstaben) und den entsprechenden Werten.
  // Lediglich das letzte Array-Element (temp) ist neu. Es wird dazu benutzt um zu erkennen, ob ein User eh nur temporaer vorhanden ist.
  // Argumente: $Identifikation (String oder Integer, String = Session_ID, Integer = Kunden_ID)
  // Rueckgabewert: Array
  function get_kunden_bankdaten($Identifikation) {
      // Globale Variablen sichtbar machen
      global $Database;
      global $sql_get_kunden_bankdaten_1_1;
      global $sql_get_kunden_bankdaten_1_2;
      global $sql_get_kunden_bankdaten_1_3;

      // Test des Arguments: Session_ID oder Kunden_ID?
      if (gettype($Identifikation) == "string") {
          // Es ist die Session_ID
          $sql = $sql_get_kunden_bankdaten_1_1.$Identifikation.$sql_get_kunden_bankdaten_1_3;
      }
      else {
          // Es ist die Kunden_ID
          $sql = $sql_get_kunden_bankdaten_1_2.$Identifikation;
      }

      // Auslesen der Bankdaten aus der Shopdatenbank
      $RS = $Database->Query($sql);
      if (is_object($RS) && $RS->NextRow()) {
          $resultat = array();
          $resultat["kontoinhaber"] = $RS->GetField("kontoinhaber");
          $resultat["bankname"] = $RS->GetField("bankname");
          $resultat["blz"] = $RS->GetField("blz");
          $resultat["kontonummer"] = $RS->GetField("kontonummer");
          $resultat["bankdaten_speichern"] = $RS->GetField("bankdaten_speichern");
          $resultat["temp"] = $RS->GetField("temp"); // Ob ein Kunde nur temporaer gespeichert wird
      }
      else {
          echo "<h2>U_B_Fehler beim auslesen der Kunden-Bankdaten. Funktion: get_kunden_bankdaten</h2>\n";
          die ("<b>Query:</b> ".$sql."<br>");
      }
      return $resultat;

  }//End function get_kunden_bankdaten

  // Folgende Funktion schreibt die (v.a. beim Lastschriftverfahren) benoetigten Kunden-Bankdaten in die Tabelle kunde
  // der Shopdatenbank. Der Kunde kann die Speicherung seiner Daten beeinflussen ($bankdaten_speichern Flag)
  // Anm. Das erste Argument kann die Kunden_ID enthalten, dann handelt es sich um einen Integer-Wert, oder aber die Session_ID = String.
  // Mit dem letzten Argument ($override), kann angegeben werden, ob die Entscheidung des Kunden ($bankdaten_speichern) beruecksichtigt
  // werden soll, oder nicht. Diese Funktionalitaet wird benoetigt, sodass wir nach Eingabe der Kundendaten diese immerhin noch temporaer
  // gespeichert werden (bis zur E-Mailgenerierung in der Datenbank zwischenspeichern).
  // Argumente: $Kunden_ID (String oder Integer) $kontoinhaber (String), $bankname (String), $blz (String, Bankleitzahl), $kontonummer (String), $bankdaten_speichern (Char -> enum Y oder N), $override (boolean, true/false)
  // Rueckgabewert: true oder Abbruch via die()-Funktion
  function set_kunden_bankdaten($Kunden_ID, $kontoinhaber, $bankname, $blz, $kontonummer, $bankdaten_speichern, $override) {
      // Globale Variablen sichtbar machen
      global $Database;
      global $sql_set_kunden_bankdaten_1_1;
      global $sql_set_kunden_bankdaten_1_2;
      global $sql_set_kunden_bankdaten_1_3;
      global $sql_set_kunden_bankdaten_1_4;
      global $sql_set_kunden_bankdaten_1_5;
      global $sql_set_kunden_bankdaten_1_6;
      global $sql_set_kunden_bankdaten_1_7;
      global $sql_set_kunden_bankdaten_1_8;

      // Test des ersten Arguments: Session_ID oder Kunden_ID?
      if (gettype($Kunden_ID) == "string") {
          // Es ist die Session_ID
          $sql_last = $sql_set_kunden_bankdaten_1_7.$Kunden_ID.$sql_set_kunden_bankdaten_1_8;
      }
      else {
          // Es ist die Kunden_ID
          $sql_last = $sql_set_kunden_bankdaten_1_6;
      }

      // Test ob der Kunde ueberhaupt will, dass wir seine Bankdaten persistent speichern, wenn nicht, alle Felder mit einem Leerstring ersetzen
      if ($bankdaten_speichern == "N" && $override == true) {
          $kontoinhaber = "";
          $bankname = "";
          $blz = "";
          $kontonummer = "";
      }
      // Update der Bankdaten in der Shopdatenbank
      if (!($RS = $Database->Exec($sql_set_kunden_bankdaten_1_1.$kontoinhaber.$sql_set_kunden_bankdaten_1_2.$bankname.$sql_set_kunden_bankdaten_1_3.$blz.$sql_set_kunden_bankdaten_1_4.$kontonummer.$sql_set_kunden_bankdaten_1_5.$bankdaten_speichern.$sql_last))) {
          echo "<h2>U_B_Fehler beim updaten der Kunden-Bankdaten. Funktion: set_kunden_bankdaten</h2>\n";
          die ("<b>Query:</b> ".$sql_set_kunden_bankdaten_1_1.$kontoinhaber.$sql_set_kunden_bankdaten_1_2.$bankname.$sql_set_kunden_bankdaten_1_3.$blz.$sql_set_kunden_bankdaten_1_4.$kontonummer.$sql_set_kunden_bankdaten_1_5.$bankdaten_speichern.$sql_last."<br>");
      }
      return true;

  }//End function set_kunden_bankdaten

  // -----------------------------------------------------------------------
  // Diese Funktion ueberprueft ob die uebergebene Session einem registrierten Kunden
  // (Eintrag in Tabelle kunde) gehoert und weiter, ob die allfaellig vorhandene Session
  // noch gueltig ist. Diese Funktion muss effizient sein, da sie in einem Performancesensitiven Teil
  // des PhPepperShops zum Einsatz kommt.
  // Argument: Session_ID (String)
  // Rueckgabewert: Kunden_ID bei gueltiger Session, sonst false (Abbruch bei Fehler)
  function checkSessionExpired($Session_ID) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_checkSessionExpired_1_1;
      global $sql_checkSessionExpired_1_2;

      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: checkSessionExpired</H1></P><BR>");
      }
      else {
          $Rueckgabewert = false; // Initialisierung des Rueckgabewertes
          // Mit der folgenden Query wird ueberprueft, ob zur uebergebenen Session ein
          // registrierter Kunde existiert und wenn ja, ob die Session noch gueltig ist.
          $RS = $Database->Query($sql_checkSessionExpired_1_1.$Session_ID.$sql_checkSessionExpired_1_2);
          if (is_object($RS) && $RS->NextRow()) {
              // Es wurde ein Eintrag gefunden - Jetzt wird die Gueltigkeit der Session ueberprueft.
              $expired = $RS->GetField("expired");
              $now = time();
              if($expired < $now) {
                  // Kunde hat zwar eine Session_ID, diese ist aber abgelaufen (Rueckgabewert ist schon false)
              }
              else {
                  $Rueckgabewert = $RS->GetField("Kunden_ID");  // Als Rueckgabewert die Kunden_ID des Kunden zurueckgeben
              }
          }// End if is_object($RS)...
      }// End else
      return $Rueckgabewert;
  }//End function checkSessionExpired

  // -----------------------------------------------------------------------
  // Hier wird ueberprueft, ob zum angegebenen Benutzernamen und Passwort auch ein registrierter
  // Kunde gefunden werden kann. Wenn ja, so wird eine gueltige Session erzeugt (expired == time()).
  // Wenn nicht, wird false (Boolean) zurueck gegeben.
  // Diese Funktion benutzt ausserdem die Funktion check_if_gesperrt um gesperrte Kunden zu identifizieren.
  // Ist ein Kunde vorhanden, aber gesperrt, so erhaelt man als Rueckgabewert den String 'gesperrt'.
  // Argument: Benutzername (String), Passwort (String), Session_ID (String)
  // Rueckgabewert: Datum des letzten Logins (String) bei Erfolg, ansonsten 'false' (String)
  //                oder 'gesperrt' (String) (sonst Abbruch durch die-Funktion)
  function test_create_Login($Benutzername, $Passwort, $Session_ID) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_test_create_Login_1_1;
      global $sql_test_create_Login_1_2;
      global $sql_test_create_Login_1_3;
      global $sql_test_create_Login_1_4;
      global $sql_test_create_Login_1_5;
      global $sql_test_create_Login_1_6;

      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: test_create_Login</H1></P><BR>");
      }
      else {
          $login = 'false'; // Initialisierung des Rucekgabewertes
          // Query ausfuehren, um zu sehen, ob wir einen Eintrag finden, welcher Passend zum angegebenen
          // Benutzernamen (Login) und Passwort ist. (Tabelle: kunde)
          // Test ob Benutzername oder Passworte * oder ? enthalten (moeglicher Crack-Versuch)
          if (preg_match('/[\*\?]/',$Benutzername.$Passwort)) {
              $last_login = 'false'; // Rueckgabewert als Falsch zurueckweisen. * und ? nicht erlaubt, weil Missbrauch damit betrieben werden koennte
              return $last_login;
          }
          $RS = $Database->Query($sql_test_create_Login_1_1.$Benutzername.$sql_test_create_Login_1_2.$Passwort.$sql_test_create_Login_1_3);
          if (is_object($RS) && $RS->NextRow()) {
              // Der Kunde existiert bereits und die Eingaben waren richtig
              // Check ob ein Kunde gesperrt ist (nur wenn Logindaten korrekt waren)
              $gesperrt = $RS->GetField("gesperrt");
              if (check_if_gesperrt($Session_ID, $gesperrt)) {
                  // Wenn ein Kunde gesperrt ist, so kommt man hier hin. Wir aendern den Rueckgabewert auf 'gesperrt'
                  // Dieser Ausdruck wird in der Datei USER_AUTH.php ausgewertet.
                  $last_login = 'gesperrt';
              }
              else {
                  // Der Kundenaccount ist also nicht gesperrt:
                  // Nun wird ueberprueft ob die Session
                  // noch gueltig ist, wenn ja, ist alles ok, andernfalls wird eine neue Session erzeugt
                  $tempsessionid = $RS->GetField("Session_ID");
                  $expired = $RS->GetField("expired");
                  $last_login = $expired;
                  $k_ID = $RS->GetField("k_ID"); // Die k_ID ist die interne Verwaltungsnummer des Kunden (Es gibt noch Kunden_ID und Kunden_Nr)
                  $now = time(); // Jetzige Zeit: (Ev. gibts spaeter mal Probleme bei dieser Funktion (UNIX-Zeit ~ im Jahre 2027))
                  if($expired < $now || $Session_ID != $tempsessionid) {
                      // Kunde hat zwar eine Session_ID, diese ist aber abgelaufen,
                      // deshalb erhaelt er jetzt eine neue, gueltige Session
                      $max_session_time = getmax_session_time();
                      $now = time(); //Aktuelle Zeit holen
                      $expired = time() + $max_session_time; // Neue Verfallszeit der neuen Session berechnen
                      $RS = $Database->Exec($sql_test_create_Login_1_4.$Session_ID.$sql_test_create_Login_1_5.$expired.$sql_test_create_Login_1_6.$k_ID);
                      if (!$RS) {
                          echo "<H1 class='content'>U_B_Error:test_create_Login: Session eines Kunden erneuern (aktivieren)</H1><BR>";
                          die("Now = $now, expired = $expired. Query =  $sql_test_create_Login_1_4".$Session_ID.$sql_test_create_Login_1_5.$expired.$sql_test_create_Login_1_6.$k_ID);
                      }
                  }
              }// End else check_if_gesperrt($Session_ID)
          }
          else {
              // Mit den Angaben des Kunden (Benutzername, Passwort) konnte kein Kunde gefunden werden
              $last_login = 'false';
          }
      }
      return $last_login;
  }//End function test_create_Login

  // -----------------------------------------------------------------------
  // Test ob ein bestehender Kunde gesperrt wurde oder nicht (Attribut gesperrt in Tabelle kunde)
  // Im optionalen Argument $gesperrt, kann man dieser Funktion uebergeben, ob der Kunde schon gesperrt ist oder nicht
  // Diese Option ist hilfreich, wenn check_if_gesperrt in einer weiteren Funktion eingesetzt wird, wo der Kunde noch
  // keine aktuelle - bekannte - Session_ID besitzt und die aufrufende Funktion das Attribut $gesperrt ausliest.
  // Wenn der Kunde noch keine Session_ID hat und das optionale Argument $gesperrt nicht gesetzt wurde UND bei der SQL-
  // Abfrage KEIN Resultat erhalten worden ist, was heisst, dass kein Kunde gefunden wurde, welchem die $Session_ID zuge-
  // wiesen worden ist, so wird der Kunde als NICHT gesperrt angesehen (man weiss ja nicht um welchen Kunden es geht).
  // Argument: Session_ID des Kunden (String), optional: $gesperrt-Status (falls Session_ID noch nicht zugewiesen wurde)
  // Rueckgabewert: true, wenn Kunde gesperrt ist, false, wenn Kunde NICHT gesperrt ist (Boolean)
  function check_if_gesperrt($Session_ID, $gesperrt="") {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_check_if_gesperrt_1_1;
      global $sql_check_if_gesperrt_1_2;

      // Initialisierung des Rueckgabewertes (Default: Kunde ist gesperrt -> Rueckgabewert = true)
      $Rueckgabewert = true;

      // Wenn das optionale Argument $gesperrt mitgeliefert wird, so muss keine Datenbankabfrage gemacht werden.
      if ($gesperrt != "") {
          if($gesperrt != "gesperrt") {
              // Kunde ist NICHT gesperrt
              $Rueckgabewert = false;
          }
      }
      else {
          // gesperrt wurde nicht mitgeliefert, wir muessen uns die Daten von der Datenbank beschaffen
          // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
          if (! is_object($Database)) {
              die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: check_if_gesperrt</H1></P><BR>");
          }
          else {
              // Mit der folgenden Query wird ueberprueft, ob zur uebergebenen Session ein
              // registrierter Kunde existiert und wenn ja, ob die Session noch gueltig ist.
              $RS = $Database->Query($sql_check_if_gesperrt_1_1.$Session_ID.$sql_check_if_gesperrt_1_2);
              if (is_object($RS) && $RS->NextRow()) {
                  // Es wurde ein Eintrag gefunden - Jetzt wird ueberprueft, ob der Account gesperrt ist
                  $gesperrt = $RS->GetField("gesperrt");
                  if($gesperrt != "gesperrt") {
                      // Kunde ist NICHT gesperrt
                      $Rueckgabewert = false;
                  }
              }// End if is_object($RS)...
              // Test ob ueberhaupt ein Kunde gefunden wurde, welchem die $Session_ID zugewiesen worden ist
              if ($RS->GetRecordCount() == 0) {
                  // Da kein Kunde gefunden worden ist, wird die Funktion false zurueck geben.
                  $Rueckgabewert = false;
              }
          }// End else
      } // End else $gesperrt != ""
      return $Rueckgabewert;
  }//End function check_if_gesperrt

  // -----------------------------------------------------------------------
  // Erzeugt eine eindeutige KundenID, welche in der Datenbank noch nicht existiert
  // Argument: ---
  // Rueckgabewert: KundenID
  function createKundenID() {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_createKundenID_1_1;
      global $sql_createKundenID_1_2;

      $unique = false;
      while (!$unique) {
          // Drei Zufallszahlen berechnen (mit srand initialisieren) und dann als String zusammensetzen
          srand((double)microtime() * 1000000);
          $rand1 = rand();
          $rand2 = rand();
          $rand3 = rand();
          $Kunden_ID = $rand1.$rand2.$rand3;
          // Testen, ob es eine gleiche Kunden_ID schon gibt, dann eine neue berechnen
          $sql = $sql_createKundenID_1_1.$Kunden_ID.$sql_createKundenID_1_2;
          $RS = $Database->Query($sql);
          if (is_object($RS) && $RS->GetRecordCount() == 0) {
              $unique = true;
          } // end of if
      }// End while
      return $Kunden_ID;
  } //End function createKundenID

  // -----------------------------------------------------------------------
  // Prueft, ob die uebergebene Kundennummer schon existiert
  // Argument: $Kunden_Nr
  // Rueckgabewert: Kunden_ID  -> vom Kunden, der Inhaber der Kundennummer ist
  //                false -> Kunden_Nr ist noch nicht vergeben
  function existKundenNr($Kunden_Nr) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_existKundenNr_1_1;
      global $sql_existKundenNr_1_2;

      // Abfragen, ob es die angegebene Kundennummer schon gibt
      $sql = $sql_existKundenNr_1_1.$Kunden_Nr.$sql_existKundenNr_1_2;
      $RS = $Database->Query($sql);
      if (is_object($RS) && $RS->NextRow()) {
          $Kunden_ID = $RS->GetField("Kunden_ID");
          return $Kunden_ID;
      } // end of if
      else{
          return false;
      } // end of else
  } //End function existKundenNr

  // -----------------------------------------------------------------------
  // Prueft, ob der uebergebene Login schon existiert
  // Argument: $Login
  // Rueckgabewert: Kunden_ID  -> vom Kunden, der Inhaber des Logins ist
  //                false -> Login ist noch nicht vergeben
  function existLogin($Login) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_existLogin_1_1;
      global $sql_existLogin_1_2;

      // Testen, ob es den angegebenen Login schon gibt
      $sql = $sql_existLogin_1_1.$Login.$sql_existLogin_1_2;
      $RS = $Database->Query($sql);
      if (is_object($RS) && $RS->NextRow()) {
          $Kunden_ID = $RS->GetField("Kunden_ID");
          return $Kunden_ID;
      } // end of if
      else{
          return false;
      } // end of else
  } // end of function existKundenNr

  // -----------------------------------------------------------------------
  // Wandelt ein Datum im amerikanischen Format in das europaeische um
  // Argument: $datum_us (yyyy-mm-dd)
  // Rueckgabewert: dd.mm.yyyy
  function date_us_to_eur($datum_us) {
      if(!empty($datum_us)) {
          list ($jahr, $monat, $tag) = explode ('-', $datum_us);
          return $tag.".".$monat.".".$jahr;
      } // end of if
      else {
          return false;
      } // end of else
  } //End function date_us_to_eur

  // -----------------------------------------------------------------------
  // Prueft, ob die uebergebene Bestellungsreferenz existiert und gibt
  // bei Erfolg die zugehoerige Kunden_ID zurueck
  // Argument: $referenz_nr
  // Rueckgabewert: Kunden_ID  -> vom Kunden, dem die Bestellung gehoert
  //                false -> Es gibt keine Bestellung mit dieser Referenz
  function existBestellung($referenz_nr) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_existBestellung_1_1;
      global $sql_existBestellung_1_2;

      // Testen, ob es den angegebenen Login schon gibt
      $sql = $sql_existBestellung_1_1.$referenz_nr.$sql_existBestellung_1_2;
      $RS = $Database->Query($sql);
      if (is_object($RS) && $RS->NextRow()) {
          $Kunden_ID = $RS->GetField("FK_Kunden_ID");
          return $Kunden_ID;
      } // end of if
      else{
          return false;
      } // end of else
  } //End function existKundenNr


  // -----------------------------------------------------------------------
  // Diese Funktion loescht die aktuelle Session_ID eines Kunden um ihm wieder einen Loginscreen anzeigen zu koennen.
  // Diese Funktionalitaet wird beim Loginprozess gebraucht, um gesperrten Kunden wieder einen Loginscreen zeigen zu koennen.
  // Argument: Session_ID des Kunden (String)
  // Rueckgabewert: true oder Abbruch via die-Funktion
  function del_kunden_session($Session_ID) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_del_kunden_session_1_1;
      global $sql_del_kunden_session_1_2;

      // gesperrt wurde nicht mitgeliefert, wir muessen uns die Daten von der Datenbank beschaffen
      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: Datenbank nicht erreichbar: check_if_gesperrt</H1></P><BR>");
      }
      else {
          // Session_ID des Kunden mit angegebener Session_ID loeschen
          $RS = $Database->Exec($sql_del_kunden_session_1_1.$Session_ID.$sql_del_kunden_session_1_2);
      }// End else
      return true;
  }//End function del_kunden_session

  // -----------------------------------------------------------------------
  // Diese Funktion speichert die Bestellung, wie sie als String abgebildet und als E-Mail versendet wurde (Admin-Email),
  // in die Datenbank zur entsprechenden Kundenbestellung. Sie kann im Kundenmanagement angesehen / geloescht werden.
  // Argument: Session_ID des Kunden (String), Bestellung als String (String)
  // Rueckgabewert: true oder Abbruch via die-Funktion
  function set_Bestellung_string($Session_ID,$Bestellung_string) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_set_Bestellung_string_1_1;
      global $sql_set_Bestellung_string_1_2;
      global $sql_set_Bestellung_string_1_3;

      // Plausibilitaetskontrolle des uebergebenen Bestellungsstrings
      if ($Bestellung_string == "") {
          die("<P><H1 class='content'>U_B_Error: set_Bestellung_string: Die &uuml;bergebene Bestellung war leer</H1></P><BR>");
      }
      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: set_Bestellung_string: Datenbank nicht erreichbar</H1></P><BR>");
      }
      else {
          // UPDATE-Query ausfuehren
          $RS = $Database->Exec($sql_set_Bestellung_string_1_1.$Bestellung_string.$sql_set_Bestellung_string_1_2.$Session_ID.$sql_set_Bestellung_string_1_3);
      }// End else
      return true;
  }//End function set_Bestellung_string

  // -----------------------------------------------------------------------
  // Diese Funktion gibt true (Boolean) zurueck, wenn der Kunde mit der angegebenen Session_ID ein
  // temporaerer Kunde ist, sonst false.
  // Argument: Session_ID des Kunden (String)
  // Rueckgabewert: true | false (Boolean)
  function is_tempKunde($Session_ID) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_is_tempKunde_1_1;
      global $sql_is_tempKunde_1_2;

      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_B_Error: is_tempKunde: Datenbank nicht erreichbar</H1></P><BR>");
      }
      else {
          // SELECT-Query ausfuehren
          $RS = $Database->Query($sql_is_tempKunde_1_1.$Session_ID.$sql_is_tempKunde_1_2);
          if (is_object($RS) && $RS->NextRow()) {
              if ($RS->GetField("temp") == "Y") {
                  return true;
              }
              else {
                  return false;
              }
          } // end of if
          else{
              return false;
          } // end of else
      }// End else
  }//End function is_tempKunde


  // -----------------------------------------------------------------------
  // Diese Funktion rechnet die angegebene IP-Adresse (Format xxx.xxx.xxx.xxx)
  // in das von der Laender-Lookup-Datenbank www.ip-to-country.com verwendete nummerische
  // Format um. Algorithmus: IP-Nummer = A x (256*256*256) + B x (256*256) + C x 256 + D
  // Argument: $dotted (String) = IP-Nummer in der ueblichen Dezimal-Schreibweise
  // Rueckgabewert: (Double) IP als nummerischer Wert fuer DB-Lookup
  function IPAddress2IPNumber($dotted) {
      $dotted = preg_split( "/[.]+/", $dotted);
      $ip = (double) ($dotted[0] * 16777216) + ($dotted[1] * 65536) + ($dotted[2] * 256) + ($dotted[3]);
      return $ip;
  }// End function IPAddress2IPNumber

  // -----------------------------------------------------------------------
  // Diese Funktion ist die Umkehrfunktion zu IPAddress2IPNumber($dotted). Sie
  // gibt eine angegebene nummerische IP (Definition siehe Funktion IPAddress2IPNumber)
  // als dezimal formatierte IP zurueck. Weitere Infos zu dieser Funktion findet man hier:
  // http://ip-to-country.com/tools/#IP_Number.
  // Argument: $number (Double) Nummerische IP
  // Rueckgabewert: (String) IP im Format xxx.xxx.xxx.xxx
  function IPNumber2IPAddress($number) {
      $a = ($number / 16777216) % 256;
      $b = ($number / 65536) % 256;
      $c = ($number / 256) % 256;
      $d = ($number) % 256;
      $dotted = $a.".".$b.".".$c.".".$d;
      return $dotted;
  }// End function IPNumber2IPAddress

  // -----------------------------------------------------------------------
  // Diese Funktion benutzt den Dienst von http://ip-to-country.com/ welcher
  // monatlich aktualisierte Landesdaten für IP-Lookups zur Verfuegung stellt.
  // Die IP-Adresse des Shopkunden wird ausgelesen und danach bestimmt zu
  // welchem Land die IP-Adresse gehoert. Bei lokalen Adressen wird ein Leer-
  // string zurueckgegeben.
  // Argumente: keine
  // Rueckgabewert: (String) Landesname (oder Leerstring)
  function check_user_country() {

      // Globale Variablen sichtbar machen:
      global $HTTP_SERVER_VARS;

      // Variableninitialisierung
      $user_ip = ""; // Enthaelt die IP des Shopkunden
      $landvermutung = " "; // Herausgefundenes Land (bei lokaler IP ein Leerstring (kein Space!))
      $addr_array = array(); // Speichert die IP_Adresse-Zahlen in einem Array

      // User-IP auslesen
      $user_ip = $_SERVER["REMOTE_ADDR"];
      if ($user_ip == "") {
          $user_ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
      };

      // Falls keine IP ausgelesen werden konnte, Leerstring zurueckgeben (sollte nicht vorkommen)
      if ($user_ip == "") {
          return "";
      }

      // Check, ob IP-Adresse lokal ist. Wenn ja, keine Landvermutung ausgeben.
      // Lokale Adressen: 192.168.xxx.xxx, 172.16.xxx.xxx, 10.xxx.xxx.xxx,
      //                  127.0.0.xxx,     0.xxx.xxx.xxx,  255.xxx.xxx.xxx
      $addr_array = explode(".",$user_ip);
      switch ($addr_array[0]) {
          case "192":
              if ($addr_array[1] == "168") {
                  $landvermutung = "";
              }
              break;
          case "172":
              if ($addr_array[1] == "16") {
                  $landvermutung = "";
              }
              break;
          case "127":
              if ($addr_array[1] == "0") {
                  if ($addr_array[1] == "0") {
                      $landvermutung = "";
                  }
              }
              break;
          case "10":
              $landvermutung = "";
              break;
          case "0":
              $landvermutung = "";
              break;
          case "255":
              $landvermutung = "";
              break;
      }// End switch

      //Check ob der User eine lokale IP benutzt hat, wenn ja, hier gleich abbrechen und Ruekgabe starten
      if ($landvermutung == "") {
          return "";
      }

      // Auslesen einer Vermutung - wo sich der Kunde befinden koennte
      $host = "ip-to-country.com";
      $url = "/get-country/?ip=".IPAddress2IPNumber($user_ip)."&user=guest&pass=guest";
      $fp = @fsockopen ($host, 80, $errno, $errstr, 30);
      // Check ob es einen Fehler beim Verbindungs-
      // versuch zum Laendercheck-Server gegeben hat.
      if (!$fp) {
          /* Ausgabe einer Fehlermeldung $ausgabe = "$errstr ($errno)<br>\n";  */
      }
      else {
          // Antwort in die $landvermutung-Variable schreiben
          fputs ($fp, "GET $url HTTP/1.0\r\nHost: ".$host."\r\n\r\n");
          while(!feof($fp)) {
              // Wir brauchen nur die letzte Zeile als Antwort
              $landvermutung = fgets ($fp,128);
          }
          fclose ($fp);
      }// End else !$fp
      // Laendernamen mit Grossbuchstaben beginnen und sonst Kleinbuchstaben verwenden
      $landvermutung = ucwords(strtolower($landvermutung));
      // Bei ein paar (z.T. deutschsprachigen) Laendern sollen die englischen Antworten noch
      // in deutsche Laendernamen konvertiert werden:
      switch ($landvermutung) {
          case "United States":
              $landvermutung = "USA";
              break;
          case "Switzerland":
              $landvermutung = "Schweiz";
              break;
          case "Germany":
              $landvermutung = "Deutschland";
              break;
          case "Austria":
              $landvermutung = "&Ouml;sterreich";
              break;
          case "Luxembourg":
              $landvermutung = "Luxenburg";
              break;
          case "Liechtenstein":
              $landvermutung = "Liechtenstein";
              break;
          case "Netherlands":
              $landvermutung = "Holland";
              break;
          case "Poland":
              $landvermutung = "Polen";
              break;
          case "Hungary":
              $landvermutung = "Ungarn";
              break;
      }
      return $landvermutung;
  }// End function check_user_country

  // -----------------------------------------------------------------------
  // Diese Funktion updated die beiden Tabellenattribute clearing_id und clearing_extra einer Bestellung.
  // Die clearing_id [VARCHAR(255)] bezeichnet die ID dieser Bestellung (Zahlung) -
  // das bieten viele externe Zahlungsinstitute an. Mit dem Attribut clearing_extra [text]
  // kann noch ein String uebergeben werden, welcher, allenfalls codiert, zusaetzliche
  // Informationen von Zahlungsinstituten aufnehmen kann, Bsp. Saferpay / B+S: TOKEN.
  // Die beiden Argumente muessen mit addslashes behandelt worden sein!
  // Argumente: $clearing_id (String), $clearing_extra (String), $Bestellungs_ID (Integer)
  // Rueckgabewert: true | false oder Abbruch via die()-Funktion
  function update_clearing_parameters($clearing_id, $clearing_extra, $Bestellungs_ID) {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_update_clearing_parameters_1_1;
      global $sql_update_clearing_parameters_1_2;
      global $sql_update_clearing_parameters_1_3;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<p><h1 class='content'>U_B_Error: Datenbank nicht erreichbar: update_clearing_parameters</h1></p><br></body></html>");
      }
      else {
          // Query zusammenstellen
          $sql = $sql_update_clearing_parameters_1_1.$clearing_id.$sql_update_clearing_parameters_1_2;
          $sql.= $clearing_extra.$sql_update_clearing_parameters_1_3.$Bestellungs_ID;
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Exec($sql);
          if (!$RS) {
              //Script mit einer (fuer User geeignete) Fehlermeldung beenden
              die("<p><h1 class='content'>U_B_Error: Konnte Informationen des externen Zahlungsinstitus nicht speichern (\$RS=$RS; update_clearing_parameters)</h1></p><br></body></html>");
          }
      }//End else

      return true;
  }//End function update_clearing_parameters

  // -----------------------------------------------------------------------
  // Diese Funktion schreibt den angegebenen MwSt. Betrag in die Datenbank (zur
  // angegebenen Bestellung, ins Attribut MwSt. hinzu).
  // Wenn man im Shop die Preise exkl. MwSt angibt, wie es seit PhPepperShop v.1.2
  // moeglich ist, so wird dieser MwSt-Betrag im Attribut MwSt der Tabelle bestellung
  // separat gespeichert. Wichtig ist zu wissen, dass das Attribut Rechnungsbetrag alle
  // Kosten kummuliert beinhaltet AUSSER zu entrichtende MwSt-Betraege wenn Preise EXKL.
  // MwSt. verwendet werden.
  // Diese Funktion wird in der Warenkorbdarstellung USER_BESTELLUNG_DARSTELLUNG.php verwendet.
  // Argumente: $mwst_betrag (Double), $Bestellungs_ID (Integer)
  // Rueckgabewert: true | false oder Abbruch via die()-Funktion
  function setExklMwSt($mwst_betrag, $Bestellungs_ID) {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_setExklMwSt_1_1;
      global $sql_setExklMwSt_1_2;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<p><h1 class='content'>U_B_Error: Datenbank nicht erreichbar: setExklMwSt</h1></p><br></body></html>");
      }
      else {
          // Abfrage zusammenstellen
          $sql = $sql_setExklMwSt_1_1.$mwst_betrag.$sql_setExklMwSt_1_2.$Bestellungs_ID;
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Exec($sql);
          if (!$RS) {
              //Script mit einer (fuer User geeignete) Fehlermeldung beenden
              die("<p><h1 class='content'>U_B_Error: Konnte MwSt-Betrag nicht zur Bestellung speichern (\$RS=$RS; setExklMwSt)</h1></p><br></body></html>");
          }
      }//End else

      return true;
  }// End function setExklMwSt

// End of file-----------------------------------------------------------------------------
?>
