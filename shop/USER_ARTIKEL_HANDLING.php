<?php
  // Filename: USER_ARTIKEL_HANDLING.php
  //
  // Modul: PHP-Funktionen - USER_ARTIKEL_HANDLING
  //
  // Autoren: José Fontanil & Reto Glanzmann, Zuercher Hochschule Winterthur
  //
  // Zweck: Beinhaltet alle Funktionen fuer Shop-User zum Handling der Artikel
  //
  // Sicherheitsstatus:        *** USER ***
  //
  // Version: 1.4
  //
  // CVS-Revision / Datum: $Id: USER_ARTIKEL_HANDLING.php,v 1.97 2003/08/11 13:57:03 glanzret Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $USER_ARTIKEL_HANDLING = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($initialize)) {include("initialize.php");}
  if (empty($artikel_def)) {include("artikel_def.php");}
  if (empty($kategorie_def)) {include("kategorie_def.php");}
  if (empty($versandkosten_def)) {include("versandkosten_def.php");}
  if (!isset($kunde_def)) {include("kunde_def.php");}
  if (!isset($pay_def)) {include("pay_def.php");}
  if (!isset($attribut_def)) {include("attribut_def.php");}
  if (!isset($kreditkarte_def)) {include("kreditkarte_def.php");}
  if (!isset($mwst_def)){include("mwst_def.php");}
  if (!isset($zahlung_def)) {include("zahlung_def.php");}
  if (!isset($mime_mail_def)) {include("mime_mail_def.php");}
  if (!isset($USER_SQL_BEFEHLE)) {include("USER_SQL_BEFEHLE.php");}

  // -----------------------------------------------------------------------
  // Gibt auf Grund einer Artikel_ID ein Artikelobjekt zurueck (ev. schneller, wenn DB auf anderem Server ist als der Webserver, 1-SQL Algorithmus)
  // Argument: Artikel_ID (INT)
  // Rueckgabewert: Einen Artikel (als Artikel-Objekt, siehe artikel_def.php)
  function getArtikel_deaktiviert($Artikel_ID) {
      global $Database;
      global $sql_getArtikel_1_1;
      global $sql_getArtikel_1_2;
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar</H1></P>\n");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Database->Query($sql_getArtikel_1_1.trim($Artikel_ID).$sql_getArtikel_1_2);
          $myArtikel = new Artikel; //Ein neues Artikel-Objekt instanziieren
          $grunddaten_eingelesen = 0; // Wird true bei > 0, sobald Artikelgrunddaten EINMAL eingelesen worden sind
          while (is_object($RS) && $RS->NextRow()){
              //Artikel einlesen:
              // Einlesen Teil 1/2: Artikelgrunddaten einlesen
              if (!$grunddaten_eingelesen) {
                  $myArtikel->artikel_ID = $RS->GetField("Artikel_ID");
                  $myArtikel->artikel_Nr = $RS->GetField("Artikel_Nr");
                  $myArtikel->name = $RS->GetField("Name");
                  $myArtikel->beschreibung = $RS->GetField("Beschreibung");
                  $myArtikel->letzteAenderung = $RS->GetField("letzteAenderung");
                  $myArtikel->gewicht = $RS->GetField("Gewicht");
                  $myArtikel->preis = $RS->GetField("Preis");
                  $myArtikel->aktionspreis = $RS->GetField("Aktionspreis");
                  $myArtikel->link = $RS->GetField("Link");
                  $myArtikel->bild_gross = $RS->GetField("Bild_gross");
                  $myArtikel->bild_klein = $RS->GetField("Bild_klein");
                  $myArtikel->bildtyp = $RS->GetField("Bildtyp");
                  $myArtikel->bild_last_modified = $RS->GetField("Bild_last_modified");
                  $myArtikel->aktionspreis_verwenden = $RS->GetField("Aktionspreis_verwenden");
                  $myArtikel->mwst_satz = $RS->GetField("MwSt_Satz");
                  $myArtikel->aktion_von = $RS->GetField("Aktion_von");
                  $myArtikel->aktion_bis = $RS->GetField("Aktion_bis");
                  $myArtikel->zusatzfelder_text = explode("þ",$RS->GetField("Zusatzfeld_text"));
                  $myArtikel->zusatzfelder_param = explode("þ",$RS->GetField("Zusatzfeld_param"));
              }
              $grunddaten_eingelesen++; // Flag wird beim naechsten Durchgang > 0 = false
              // Einlesen Teil 2/2: Optionen, Variationen, Variationsgruppen eines Artikels einlesen
              $myArtikel->putoption($RS->GetField("Optionstext"),$RS->GetField("Preisdifferenz"));
              $myArtikel->putopt_gewicht($RS->GetField("Optionstext"),$RS->GetField("Gewicht_Opt"));
              $array_preis[$RS->GetField("Variations_Nr")] = $RS->GetField("Aufpreis");
              $array_text[$RS->GetField("Variations_Nr")] = $RS->GetField("Variationstext");
              $array_gruppe[$RS->GetField("Variations_Nr")] = $RS->GetField("Variations_Grp");
              $array_gewicht_var[$RS->GetField("Variations_Nr")] = $RS->GetField("Gewicht_Var");
              $myArtikel->var_gruppen_text[$RS->GetField("Gruppen_Nr")] = $RS->GetField("Gruppentext");
              $myArtikel->var_gruppen_darst[$RS->GetField("Gruppen_Nr")] = $RS->GetField("Gruppe_darstellen");
          }//End while
          // Damit die Optionen ihre richtige Position behalten wird dem SQL ein ORDER BY Optionen_Nr Statement hinzugefuegt
          // Damit jetzt auch die Variationen ihre Reihenfolge behalten koennen, werden diese sortiert in zwei Arrays ausgelesen
          // und dann in einer weiteren Schleife korrekt sortiert in ihr eigentliches Zielarray im Artikel-Objekt abgelegt.
          for($i=1;$i <= count($array_preis);$i++) {
              $myArtikel->putvariation($array_text[$i],$array_preis[$i]);
              $myArtikel->putvar_gruppe($array_text[$i],$array_gruppe[$i]);
              $myArtikel->putvar_gewicht($array_text[$i],$array_gewicht_var[$i]);
          } // end of for
          return $myArtikel;
      }//End else
  }//End getArtikel (momentan deaktiviert, wird noch geloescht)

  // -----------------------------------------------------------------------
  // Gibt auf Grund einer Artikel_ID ein Artikelobjekt zurueck (OPTIMIERT seit Nov. 2002 - 4-SQL Algorithmus)
  // Argument: Artikel_ID (INT)
  // Rueckgabewert: Einen Artikel (als Artikel-Objekt, siehe artikel_def.php)
  function getArtikel($Artikel_ID) {
      global $Database;
      global $sql_getArtikel_1_3;
      global $sql_getArtikel_1_4;
      global $sql_getArtikel_1_5;
      global $sql_getArtikel_1_6;
      global $sql_getArtikel_1_7;

      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar</H1></P>\n");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // Artikelgrunddaten auslesen
          $RS = $Database->Query($sql_getArtikel_1_3.trim($Artikel_ID));
          $myArtikel = new Artikel; //Ein neues Artikel-Objekt instanziieren
          while (is_object($RS) && $RS->NextRow()){
              $myArtikel->artikel_ID = $RS->GetField("Artikel_ID");
              $myArtikel->artikel_Nr = $RS->GetField("Artikel_Nr");
              $myArtikel->name = $RS->GetField("Name");
              $myArtikel->beschreibung = $RS->GetField("Beschreibung");
              $myArtikel->letzteAenderung = $RS->GetField("letzteAenderung");
              $myArtikel->gewicht = $RS->GetField("Gewicht");
              $myArtikel->preis = $RS->GetField("Preis");
              $myArtikel->aktionspreis = $RS->GetField("Aktionspreis");
              $myArtikel->link = $RS->GetField("Link");
              $myArtikel->bild_gross = $RS->GetField("Bild_gross");
              $myArtikel->bild_klein = $RS->GetField("Bild_klein");
              $myArtikel->bildtyp = $RS->GetField("Bildtyp");
              $myArtikel->bild_last_modified = $RS->GetField("Bild_last_modified");
              $myArtikel->aktionspreis_verwenden = $RS->GetField("Aktionspreis_verwenden");
              $myArtikel->mwst_satz = $RS->GetField("MwSt_Satz");
              $myArtikel->aktion_von = $RS->GetField("Aktion_von");
              $myArtikel->aktion_bis = $RS->GetField("Aktion_bis");
              $myArtikel->zusatzfelder_text = explode("þ",$RS->GetField("Zusatzfeld_text"));
              $myArtikel->zusatzfelder_param = explode("þ",$RS->GetField("Zusatzfeld_param"));
          }//End while

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // Artikeloptionen auslesen
          $RS = $Database->Query($sql_getArtikel_1_4.trim($Artikel_ID).$sql_getArtikel_1_5);
          while (is_object($RS) && $RS->NextRow()){
              $myArtikel->putoption($RS->GetField("Optionstext"),$RS->GetField("Preisdifferenz"));
              $myArtikel->putopt_gewicht($RS->GetField("Optionstext"),$RS->GetField("Gewicht_Opt"));
          }//End while

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // Artikelvariationen auslesen
          $RS = $Database->Query($sql_getArtikel_1_6.trim($Artikel_ID));
          while (is_object($RS) && $RS->NextRow()){
              $array_preis[$RS->GetField("Variations_Nr")] = $RS->GetField("Aufpreis");
              $array_text[$RS->GetField("Variations_Nr")] = $RS->GetField("Variationstext");
              $array_gruppe[$RS->GetField("Variations_Nr")] = $RS->GetField("Variations_Grp");
              $array_gewicht_var[$RS->GetField("Variations_Nr")] = $RS->GetField("Gewicht_Var");
          }//End while

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          // Variationsgruppen auslesen
          $RS = $Database->Query($sql_getArtikel_1_7.trim($Artikel_ID));
          while (is_object($RS) && $RS->NextRow()){
              $myArtikel->var_gruppen_text[$RS->GetField("Gruppen_Nr")] = $RS->GetField("Gruppentext");
              $myArtikel->var_gruppen_darst[$RS->GetField("Gruppen_Nr")] = $RS->GetField("Gruppe_darstellen");
          }//End while

          // Damit die Optionen ihre richtige Position behalten wird dem SQL ein ORDER BY Optionen_Nr Statement hinzugefuegt
          // Damit jetzt auch die Variationen ihre Reihenfolge behalten koennen, werden diese sortiert in zwei Arrays ausgelesen
          // und dann in einer weiteren Schleife korrekt sortiert in ihr eigentliches Zielarray im Artikel-Objekt abgelegt.
          for($i=1;$i <= count($array_preis);$i++) {
              $myArtikel->putvariation($array_text[$i],$array_preis[$i]);
              $myArtikel->putvar_gruppe($array_text[$i],$array_gruppe[$i]);
              $myArtikel->putvar_gewicht($array_text[$i],$array_gewicht_var[$i]);
          } // end of for
          return $myArtikel;
      }//End else
  }//End getArtikel

  // -----------------------------------------------------------------------
  // Alle Artikel einer Kategorie holen
  // getArtikeleinerKategorie liefert in einem Array alle Artikel die einer
  // Kategorie untergeordnet sind
  // Argumente: Kategoriename (String), Name der Parent-Kategorie, falls es eine Unterkategorie ist (String)
  // Rueckgabewert: Array (Key = Artikel_ID, Wert = Artikel-Objekt)
  function getArtikeleinerKategorie ($Kategoriename, $Unterkategorie_von) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar(getArtikeleinerKategorie)</H1></P>\n");
      }
      else {
          // Optimierter Zugriff mit Hilfe von getArtikel():
          // global $sql_getArtikeleinerKategorie_1_3;
          global $sql_getArtikeleinerKategorie_1_4;
          global $sql_getArtikeleinerKategorie_1_5;
          global $sql_getArtikeleinerKategorie_1_6;
          global $sql_getArtikeleinerKategorie_1_5_2;
          global $sql_getArtikeleinerKategorie_1_6_2;

          $myArtikelarray = array(); // Initialisierung
          // Test ob es eine Haupt- oder Unterkategorie ist, falls es eine Hauptkategorie ist, muss ein anderer SQL-String verwendet werden
          if ($Unterkategorie_von != "") {
              $RS = $Database->Query($sql_getArtikeleinerKategorie_1_4.trim($Kategoriename).$sql_getArtikeleinerKategorie_1_5.trim($Unterkategorie_von).$sql_getArtikeleinerKategorie_1_6);
          }
          else {
              $RS = $Database->Query($sql_getArtikeleinerKategorie_1_4.trim($Kategoriename)."$sql_getArtikeleinerKategorie_1_5_2 $sql_getArtikeleinerKategorie_1_6_2");
          }
          if (!$RS) {
              die("U_A_H_Error: Funktion getArtikeleinerKategorie -> RS ist nicht true");
          }
          while (is_object($RS) && $RS->NextRow()) {
              $myArtikelarray[] = getArtikel($RS->GetField("FK_Artikel_ID"));
          }

          return $myArtikelarray;
      }//end else
  }//End getArtikeleinerKategorie

  // -----------------------------------------------------------------------
  // Alle Artikel einer Kategorie holen, als Argument wird eine Kategorie_ID erwartet
  // IDgetArtikeleinerKategorie liefert in einem Array alle Artikel die einer
  // Kategorie eingegeordnet sind. Diese Funktion arbeitet analog zur Funktion getArtikeleinerKategorie.
  // Argumente: Kategorie_ID
  // Rueckgabewert: Array (Key = Artikel_ID, Wert = Artikel-Objekt)
  function IDgetArtikeleinerKategorie ($Kategorie_ID) {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar(IDgetArtikeleinerKategorie)</H1></P>\n");
      }
      else {
          // Optimierter Zugriff mit Hilfe von getArtikel():
          global $sql_IDgetArtikeleinerKategorie_1_3;
          global $sql_IDgetArtikeleinerKategorie_1_4;

          $myArtikelarray = array();
          $RS = $Database->Query($sql_IDgetArtikeleinerKategorie_1_3.$Kategorie_ID.$sql_IDgetArtikeleinerKategorie_1_4.getSortierung());
          if (!$RS) {
              die("U_A_H_Error: Funktion IDgetArtikeleinerKategorie -> RS ist nicht true (2)");
          }
          while (is_object($RS) && $RS->NextRow()) {
              $myArtikelarray[] = getArtikel($RS->GetField("FK_Artikel_ID"));
          }

          return $myArtikelarray;
      }//end else
  }//End IDgetArtikeleinerKategorie

  // -----------------------------------------------------------------------
  // Liefert auf Grund der Kategorie_ID alle in dieser Kategorie enthaltenen Artikel.
  // Die beiden Argumente $low- und $anzahl_gleichzeitig definieren das Anzeigefenster
  // $lowlimit sagt von wo, $anzahl_gleichzeitig sagt wieviel ab $lowlimit ausgelesen werden soll
  // Optional kann zusaetzlich noch eine Artikel-ID mit uebergeben werden. Ist diese in der anzuzeigenden
  // Kategorie vorhanden, so wird automatisch das lowlimit so gesetzt, dass der Artikel angezeigt wird.
  // (Diese Funktionalitaet wird von der Artikelsuche und fuers direkte anspringen eines Artikels von extern verwendet)
  // Argument: Kategorie_ID (INT), $anzahl_gleichzeitig (INT), $lowlimit (Int, standardmaessig auf 0 gesetzt), $Artikel_ID (INT)
  // Rueckgabewert: Einen Array (Key: Artikel_ID, Wert: Artikel_Objekt)
  function IDgetArtikeleinerKategorievonbis($Kategorie_ID,$anzahl_gleichzeitig,$lowlimit=0,$Artikel_ID=-1) {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_IDgetArtikeleinerKategorievonbis_1_1;
      global $sql_IDgetArtikeleinerKategorievonbis_1_4;
      // global $sql_IDgetArtikeleinerKategorievonbis_1_2; (Alter Algorithmus - Teil 1/2)
      // global $sql_IDgetArtikeleinerKategorievonbis_1_3; (Alter Algorithmus - Teil 2/2)

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (IDgetArtikeleinerKategorievonbis)</H1></P>\n");
      }
      else {
          // Auslesen aller Artikel-IDs welche sich in der Kategorie mit der ID $Kategorie_ID befinden:
          $RS = $Database->Query($sql_IDgetArtikeleinerKategorievonbis_1_1.$Kategorie_ID.$sql_IDgetArtikeleinerKategorievonbis_1_4.getSortierung());
          if (!$RS) {
              echo "<B>Query:</B> ".$sql_IDgetArtikeleinerKategorievonbis_1_1.$Kategorie_ID.$sql_IDgetArtikeleinerKategorievonbis_1_4.getSortierung()."<BR>";
              die("<h1>Fehler beim Ausfuehren einer SQL-Abfrage! (IDgetArtikeleinerKategorievonbis)</h1>");
          }
          //Abpacken in einen Array
          $artikel_id_array = array(); //In diesem Array werden die Artikel_IDs abgelegt
          while (is_object($RS) && $RS->NextRow()) {
              $tempartikel_id = $RS->GetField("FK_Artikel_ID"); // Bei Verwendung des alten Algorithmus: $RS->GetField("Artikel_ID")
              if ($tempartikel_id != $vorigeartikel_id) {
                  $artikel_id_array[] = $tempartikel_id;
              }
              $vorigeartikel_id = $tempartikel_id;
          }//End while RS

          // Test ob ueberhaupt Artikel in dieser Kategorie vorhanden sind, wenn nicht, leeren Array zurueckgeben
          if (count($artikel_id_array) <= 0) {
              $artikel_array = array();
              return $artikel_array; //leeren Array zurueckgeben und Funktion beenden.
          }

          // Wenn eine Artikel_ID uebergeben wurde ($Artikel_ID > 0), so wird ueberprueft, ob diese Artikel_ID auch
          // tatsaechlich in dieser Kategorie vorhanden ist. Wenn ja, so wird das $lowlimit so veraendert, dass der
          // 'gewuenschte' Artikel im Intervall enthalten ist, und somit schliesslich auch angezeigt wird!
          if ($Artikel_ID > 0) {
              // Variablen, welche zur Berechnung benoetigt werden auslesen / berechnen.
              $total_anzahl = count($artikel_id_array);
              $anzahl_inkremente = $total_anzahl / $anzahl_gleichzeitig;
              // Jetzt muss herausgefunden werden, in welchem Inkrement sich der gefundene Artikel befindet. Hier auch noch
              // der Test, ob der Artikel ueberhaupt in dieser Kategorie vorhaden ist ($artikel_gefunden Variable):
              $artikel_gefunden = -1; // Wenn der Artikel in dieser Kategorie vorhanden ist, steht hier seine Artikel_ID drin
              for ($i = 0; $i < count($artikel_id_array); $i++) {
                  if ($Artikel_ID == $artikel_id_array[$i]) {
                      $artikel_gefunden = $i;
                  }
              }
              // Herausfinden in welchem Anzeigeintervall sich der Artikel befindet
              $abbruch = false; // Dieses Flag wird true, wenn der Artikel einem Intervall zugeordnet werden konnte
              $counter = 0; // Wird nach jedem while-Durchlauf um 1 inkrementiert (Multiplikator fuer $anzahl_gleichzeitig)
              $artikel_in_inkrement = -1; // Hier wird abgespeichert in welchem Intervall sich der Artikel befindet
              while ($abbruch == false) {
                  for ($i=0;$i < $anzahl_gleichzeitig; $i++) {
                      if ($artikel_gefunden >= ($counter * $anzahl_gleichzeitig) && $artikel_gefunden <= ((($counter+1) * $anzahl_gleichzeitig))-1) {
                          $artikel_in_inkrement = $counter * $anzahl_gleichzeitig;
                          $abbruch = true;
                      }
                  }
                  $counter++;
              }
              // Der Artikel mit ID $Artikel_ID ist in der anzuzeigenden Kategorie vorhanden, $lowlimit anpassen, sodass
              // der 'gewuenschte' Artikel auch angezeigt wird (im aktuellen Inkrement ist).
              if ($artikel_gefunden > 0 && $artikel_in_inkrement > 0) {
                  $lowlimit = $artikel_in_inkrement;
              }
          }

          // Nun ist sichergestellt, dass Artikel in der gewuenschten Kategorie vorhanden sind.
          // In den folgenden Array wird zuerst ein speziell praeparierter Artikel eingelesen und dann werden
          // die benoetigten Artikel ausgelesen und darin abgepackt:
          $artikel_array = array();
          $infoartikel = new Artikel();
          $infoartikel->artikel_ID = $Kategorie_ID; // In der Artikel_ID wir die ID der anzuzeigenden Kategorie gespeichert
          $infoartikel->preis = $lowlimit;
          $infoartikel->aktionspreis = $anzahl_gleichzeitig;
          $infoartikel->gewicht = count($artikel_id_array);
          $infoartikel->artikel_Nr = $Artikel_ID; // Ja, das ist jetzt etwas verwirrend: In der Artikel_Nr wird noch zusaetzlich eine optionale
                                                  // Artikel_ID angegeben - falls man innerhalb der Kategorie zu einem Artikel springen soll.

          // Berechnen des oberen Limite, bis wohin Artikel ausgelesen werden koennen:
          $highlimit = $lowlimit + $anzahl_gleichzeitig;
          if ($highlimit >= count($artikel_id_array)) {
              $highlimit = count($artikel_id_array);
          }

          // Auslesen der benoetigten Artikel und abpacken in $artikel_array Array:
          for($i = $lowlimit; $i < $highlimit; $i++) {
              $artikel_array[] = getArtikel($artikel_id_array[$i]);
          }

          // Rueckgabewert zurueckgeben
          $artikel_array[] = $infoartikel;
          return $artikel_array;
      }//End else
  }//End IDgetArtikeleinerKategorievonbis

  // -----------------------------------------------------------------------
  // Liefert in einem Array alle Artikel IDs.
  // Mit dem optionalen Argument $inkl_nichtzugeordnet kann man steuern, ob man auch die eigentlich
  // 'unsichtbaren'/'versteckten' Artikel, welche in der Kategorie Nichtzugeordnet liegen ausgelesen haben
  // moechte. Mit den beiden optionalen Argumenten $sortkrit (Sortierkriterium) und $sortorder (Richtung)
  // kann man die Artikel-IDs sortiert auslesen. Bsp. $sortkrit="Preis,Name", $sortorder="ASC" kann man alle
  // Artikel-IDs sortiert nach Preis und dann nach Artikelnamen aufsteigend auslesen.
  // Wenn keine Artikel gefunden wurden, so wird ein leerer Array zurueckgegeben
  // Argumente: $inkl_nichtzugeordnet (Boolean, optional, default=false),
  //            $sortkrit (String, optional, default = 'Artikel_ID'),
  //            $sortorder (String, optional, default = 'ASC', Moeglichkeiten = 'ASC' oder 'DESC'),
  // Rueckgabewert: Array mit Artikel-IDs (Array) oder Abbruch mit der die()-Funktion
  function get_alle_artikel_id($inkl_nichtzugeordnet = false, $sortkrit = "Artikel_ID", $sortorder="ASC") {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_get_alle_artikel_id_1_1;
      global $sql_get_alle_artikel_id_1_2;
      global $sql_get_alle_artikel_id_1_3;
      global $sql_get_alle_artikel_id_1_4;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<p><h1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (get_alle_artikel_id)</h1></p></body></html>\n");
      }
      else {
          // Initialisierungen
          $myArtikelarray = array();
          $Nichtzugeordnet_ID = 0;

          // Test ob die Artikel-IDs welche in der Kategorie Nichtzugeordnet liegen ausgeblendet werden sollen
          if ($inkl_nichtzugeordnet == false) {
              // ausblenden
              // 1.) Auslesen der Kategorie-ID der Kategorie mit der Bezeichnung (Name) Nichtzugeordnet
              $sql = $sql_get_alle_artikel_id_1_1;
              $RS = $Database->Query($sql);
              if (is_object($RS) && $RS->NextRow()) {
                  $Nichtzugeordnet_ID = $RS->GetField("Kategorie_ID");
              }
              else {
                  die("<p><h1 class='content'>U_A_H_Error: Konnte Kategorie_ID der Kategorie Nichtzugeordnet nicht auslesen (get_alle_artikel_id)</h1></p></body></html>\n");
              }
              // 2.) Auslesen aller Artikel-IDs ausser denen der Kategorie Nichtzugeordnet
              $sql = $sql_get_alle_artikel_id_1_2.$sql_get_alle_artikel_id_1_3.$Nichtzugeordnet_ID.$sql_get_alle_artikel_id_1_4.$sortkrit." ".$sortorder;
              $RS = $Database->Query($sql);
              if (!$RS) {
                  die("<p><h1 class='content'>U_A_H_Error: Konnte Artikel-IDs nicht auslesen (1) (get_alle_artikel_id)</h1></p></body></html>\n");
              }
              while (is_object($RS) && $RS->NextRow()) {
                  $myArtikelarray[] = $RS->GetField("Artikel_ID");
              }
          }
          else {
              // alle anzeigen
              $sql = $sql_get_alle_artikel_id_1_2.$sql_get_alle_artikel_id_1_4.$sortkrit." ".$sortorder;
              $RS = $Database->Query($sql);
              if (!$RS) {
                  die("<p><h1 class='content'>U_A_H_Error: Konnte Artikel-IDs nicht auslesen (2) (get_alle_artikel_id)</h1></p></body></html>\n");
              }
              while (is_object($RS) && $RS->NextRow()) {
                  $myArtikelarray[] = $RS->GetField("Artikel_ID");
              }
          }

          // Rueckgabe der ausgelesenen Artikel-ID Arrays
          return $myArtikelarray;
      }//end else
  }//End get_alle_artikel_id

  // -----------------------------------------------------------------------
  // Liefert auf Grund eines Such-Strings, der entweder im Namen oder in der
  // Beschreibung des Artikels vorkommen kann alle zutreffenden Artikel zurueck.
  // Es koennen auch mehrere Begriffe eingegeben werden. Diese werden dann
  // kunjunktiv verknuepft (AND), auch Bilder werden optional angezeigt.
  // Die beiden Argumente $low- und $highlimit definieren das Anzeigefenster
  // $low sagt von wo, $high sagt wieviel ab $low ausgelesen werden soll
  // Argument: Suchstring (String), $lowlimit (Int), $highlimit (Int)
  // Rueckgabewert: Einen Array (Key: Artikel_Objekt, Wert: Kategorienarray)
  function getgesuchterArtikel($Suchstring,$lowlimit,$highlimit) {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getgesuchterArtikel_1_1;
      global $sql_getgesuchterArtikel_1_2;
      global $sql_getgesuchterArtikel_1_3;
      global $sql_getgesuchterArtikel_1_4;
      global $sql_getgesuchterArtikel_1_5;
      global $sql_getgesuchterArtikel_1_6;
      global $sql_getgesuchterArtikel_1_7;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (getgesuchterArtikel)</H1></P>\n");
      }
      else {
          if ($Suchstring == "") {
              //Es wurde ein leerer String eingegeben, kein Resultat
              $leerarray = array();
              return $leerarray;
          }
          //Der Suchstring ist nicht leer
          //Alle eingegebenen Woerter in den Array Suchstringarray abfuellen
          $Suchstringarray = explode(" ",$Suchstring);
          //SQL-Query zusammenbauen:
          $sqlquery = $sql_getgesuchterArtikel_1_1.$Suchstringarray[0].$sql_getgesuchterArtikel_1_2.$Suchstringarray[0];
          for($i = 1;$i < count($Suchstringarray);$i++) {
              $sqlquery.=$sql_getgesuchterArtikel_1_5.$Suchstringarray[$i].$sql_getgesuchterArtikel_1_2.$Suchstringarray[$i];
          }
          $sqlquerylimitless = $sqlquery.$sql_getgesuchterArtikel_1_3;  //Query ohne Limite um die Gesamt-Anzahl an Treffer zu erfahren
          $sqlquery.=$sql_getgesuchterArtikel_1_3.$sql_getgesuchterArtikel_1_6.$lowlimit.$sql_getgesuchterArtikel_1_7.$highlimit;
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS_3 = $Database->Query($sqlquerylimitless);
          $Anzahl_Treffer = $RS_3->GetRecordCount(); //Gesamt-Anzahl Treffer auslesen
          // Nun laeuft die Query mit der angegebenen Limite
          $RS = $Database->Query($sqlquery);
          if (!$RS) {
              echo "<B>Query:</B> $sqlquery<BR>";
              die("<h1>Fehler beim Ausfuehren der Query! (getgesuchterArtikel)_RS</h1>");
          }
          //Abpacken in einen Array
          $myArtikelarray = array(); //In diesem Array werden die Artikelobjekte abgespeichert
          while (is_object($RS) && $RS->NextRow()) {
              $myArtikelmitkategorien = new Artikelmitkategorien;
              //Artikel einlesen:
              $myArtikel = new Artikel;
              $myArtikel->artikel_ID = $RS->GetField("Artikel_ID");
              $myArtikel->artikel_Nr = $RS->GetField("Artikel_Nr");
              $myArtikel->name = $RS->GetField("Name");
              $myArtikel->beschreibung = $RS->GetField("Beschreibung");
              $myArtikel->letzteAenderung = $RS->GetField("letzteAenderung");
              $myArtikel->gewicht = $RS->GetField("Gewicht");
              $myArtikel->preis = $RS->GetField("Preis");
              $myArtikel->aktionspreis = $RS->GetField("Aktionspreis");
              $myArtikel->link = $RS->GetField("Link");
              $myArtikel->bild_gross = $RS->GetField("Bild_gross");
              $myArtikel->bild_klein = $RS->GetField("Bild_klein");
              $myArtikel->bildtyp = $RS->GetField("Bildtyp");
              $myArtikel->bild_last_modified = $RS->GetField("Bild_last_modified");
              $myArtikel->mwst_satz = $RS->GetField("MwSt_Satz");
              $myArtikel->zusatzfelder_text = explode("þ",$RS->GetField("Zusatzfeld_text"));
              $myArtikel->zusatzfelder_param = explode("þ",$RS->GetField("Zusatzfeld_param"));
              $myArtikel->mwst_satz = $RS->GetField("MwSt_Satz");
              $myArtikel->aktion_von = $RS->GetField("Aktion_von");
              $myArtikel->aktion_bis = $RS->GetField("Aktion_bis");
              $myArtikel->aktion_bis = $RS->GetField("Aktion_bis");
              //Kategorien einlesen (Nur Name und Unterkategorie_von, da hier nicht mehr benoetigt wird)
              //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS_2 = $Database->Query("$sql_getgesuchterArtikel_1_4".$myArtikel->artikel_ID);
              //Abpacken in ein Artikelmitkategorien-Objekt (siehe artikel_def.php) und dann in einen Array
              $myKategorienarray = array(); // Initialisierung
              if (!$RS_2) {
                  echo "<B>Query:</B> $sql_getgesuchterArtikel_1_4".$myArtikel->artikel_ID."<BR>";
                  die("<h1>Fehler beim Ausfuehren der Query! (getgesuchterArtikel)_RS_2</h1>");
              }
              $nichtzugeordnet = false;
              while (is_object($RS_2) && $RS_2->NextRow()) {
                  $myKategorie = new Kategorie;
                  $myKategorie->Name = $RS_2->GetField("Name");
                  $myKategorie->Unterkategorie_von = $RS_2->GetField("Unterkategorie_von");
                  $myKategorie->Kategorie_ID = $RS_2->GetField("Kategorie_ID");
                  // Prüfen, ob sich der Artikel in der Kategorie nichtzugeordnet befindet. Falls das
                  // der Fall ist, kann er keiner weiteren Kategorie zugeordnet sein und wird deshalb
                  // nicht als Suchergebnis angezeigt
                  if ($myKategorie->Unterkategorie_von == "@PhPepperShop@"){
                      $nichtzugeordnet = true;
                  }
                  //Kategorie
                  $myKategorienarray[] = $myKategorie;
              }//End while RS_2
              $myArtikelmitkategorien->myArtikel = $myArtikel;
              $myArtikelmitkategorien->myKategorienarray = $myKategorienarray;

              if ($nichtzugeordnet == false){
                  //Abpacken des Artikelmitkategorienobjekts in einen Array,
                  //welcher am Schluss zurueckgegeben wird
                  $myArtikelarray[] = $myArtikelmitkategorien;
              } // end of if
              else {
                  $Anzahl_Treffer--;
              } // end of else
          }//End while RS
          //Rueckgabe des Arrays mit Artikelmitkategorien-Objekten. Das letzte Objekt wurde von dieser Funktion instanziert
          //und enthaelt in seiner Artikel_ID die Anzahl Suchergebnisse (gesamthaft, ohne LIMIT)
          //Wir speichern nun unsere Anzahl Treffer in einen Artikel im Artikelmitkategorien-Objekt
          //und fuegen dies ans Ende des $myArtikelarray an, auf diese Weise koennen wir in diesem
          //Array die Anzahl Treffer transportieren
          $anzahlArtikel = new Artikel;
          $anzahlArtikelmitkategorien = new Artikelmitkategorien;
          $anzahlArtikel->artikel_ID = $Anzahl_Treffer;
          $anzahlArtikel->preis = $lowlimit;
          $anzahlArtikel->aktionspreis = $highlimit;
          $anzahlArtikelmitkategorien->myArtikel = $anzahlArtikel;
          array_push($myArtikelarray, $anzahlArtikelmitkategorien);
          return $myArtikelarray;
      }//End else
  }//End getgesuchterArtikel

  // -----------------------------------------------------------------------
  // Liefert ein Array mit allen Kategorien als Kategorie-Objekten zurueck
  // Rueckgabewert: Ein Array mit Kategorien, welche ihre Unterkategorien enthalten
  function getallKategorien() {
      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getallKategorien_1;
      global $sql_getallKategorien_1_2;
      global $sql_getallKategorien_1_3;
      $myKategorien = array();


      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar</H1></P>\n");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getallKategorien_1");
          //Auslesen der Kategorien mit ihren jeweiligen Unterkategorien (iterativ)
          if (!is_object($RS)) {
              die("<B class='content'><U>U_A_H_Error: RS ist kein Objekt (getallKategorien_RS)</U></B><BR><BR>");
          }
          while (is_object($RS) && $RS->NextRow()){
              $unterkategoriencounter = 0; //Zaehler initialisieren (zaehlt Unterkategorien einer Kategorie)
              $meineKategorie = new Kategorie; //Ein neues Kategorie-Objekt instanzieren (siehe auch kategorie_def.php)
              $meineKategorie->Kategorie_ID = $RS->GetField("Kategorie_ID");
              $meineKategorie->Name = $RS->GetField("Name");
              $meineKategorie->Positions_Nr = $RS->GetField("Positions_Nr");
              $meineKategorie->Beschreibung = $RS->GetField("Beschreibung");
              $meineKategorie->Bild_gross = $RS->GetField("Bild_gross");
              $meineKategorie->Bild_klein = $RS->GetField("Bild_klein");
              $meineKategorie->Bildtyp = $RS->GetField("Bildtyp");
              $meineKategorie->Bild_last_modified = $RS->GetField("Bild_last_modified");
              $meineKategorie->MwSt_default_Satz = $RS->GetField("MwSt_Satz");
              $meineKategorie->Details_anzeigen = $RS->GetField("Details_anzeigen");
              //Unterkategorien auslesen
              $RS_2 = $Database->Query($sql_getallKategorien_1_2.addslashes($meineKategorie->Name).$sql_getallKategorien_1_3);
              if (!is_object($RS_2)) {
                  die("<B class='content'><U>U_A_H_Error:RS_2 ist kein Objekt (getallKategorien_RS_2)</U></B><BR><BR>");
              }
              while (is_object($RS_2) && $RS_2->NextRow()){
                  //Jetzt kommen die Unterkategorien
                  $meineUnterkategorie = new Unterkategorie; //Ein neues Unterkategorien-Objekt erzeugen
                  $meineUnterkategorie->Kategorie_ID = $RS_2->GetField("Kategorie_ID");
                  $meineUnterkategorie->Name = $RS_2->GetField("Name");
                  $meineUnterkategorie->Positions_Nr = $RS_2->GetField("Positions_Nr");
                  $meineUnterkategorie->Beschreibung = $RS_2->GetField("Beschreibung");
                  $meineUnterkategorie->Bild_gross = $RS_2->GetField("Bild_gross");
                  $meineUnterkategorie->Bild_klein = $RS_2->GetField("Bild_klein");
                  $meineUnterkategorie->Bildtyp = $RS_2->GetField("Bildtyp");
                  $meineUnterkategorie->Bild_last_modified = $RS_2->GetField("Bild_last_modified");
                  $meineUnterkategorie->MwSt_default_Satz = $RS_2->GetField("MwSt_Satz");
                  $meineUnterkategorie->setUnterkategorie_von($RS_2->GetField("Unterkategorie_von"));
                  $meineUnterkategorie->Details_anzeigen = $RS_2->GetField("Details_anzeigen");
                  $unterkategoriencounter++;
                  // Die ausgelesene Unterkategorie wird der Kategorie zugeordnet (in ihren Array ablegen)
                  $meineKategorie->Unterkategorien[] = $meineUnterkategorie;
                  unset($meineUnterkategorie);
              }
              // Die ausgelesene Kategorie inkl. ihren Unterkategorien dem Array $myKategorien uebergeben
              $myKategorien[] = $meineKategorie;
          }
          unset($meineKategorie);

          // Zurueckgegeben wird ein Array von Kategorie-Objekten, welche in ihren Unterkategorien-Arrays
          // ihre jeweiligen Unterkategorien inkl. allen Attributen beinhalten
          return $myKategorien;
      }// End else
  }// End function getallKategorien

  // -----------------------------------------------------------------------
  // Liefert den Namen der Kategorie in welcher der als Argument uebergebene Artikel(_ID) drin ist.
  // Argument: Artikel_ID (INT)
  // Rueckgabewert: Array: key = Kategoriename (String), value = Parent-Kategorie (String)
  // (Wenn die Kategorie KEINE Unterkategorie ist, so ist der Parent-Kategorie-Name = dem Kategorienamen)
  function getKategorie_eines_Artikels($Artikel_ID) {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getKategorie_eines_Artikels_1_1;
      global $sql_getKategorie_eines_Artikels_1_2;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar(getKategorie_eines_Artikels)</H1></P><BR>");
      }
      else {
          $rueckarray = array();
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getKategorie_eines_Artikels_1_1".trim($Artikel_ID)."$sql_getKategorie_eines_Artikels_1_2");
          //Verpacken der einzelnen Kategorienamen in einen Array
          if (!is_object($RS)) {
              echo "<B class='content'><U>getKategorie_eines_Artikels_Error: RS ist kein Objekt</U></B><BR><BR>";
              die("Query:$sql_getKategorie_eines_Artikels_1_1 $Artikel_ID $sql_getKategorie_eines_Artikels_1_2");
          }
          $RS->NextRow(); //Resultat "einlesen"
          $Kategorienamen = $RS->GetField("Name");
          $Unterkategorie_von = $RS->GetField("Unterkategorie_von");
          //Array abfuellen:
          if ($Unterkategorie_von == "") {
              $rueckarray[$Kategorienamen] = $Kategorienamen;
          }
          else {
              $rueckarray[$Kategorienamen] = $Unterkategorie_von;
          }

          // Zurueck gegeben wird ein Array mit dem Kategorienamen und dem Unterkategorienamen
          return $rueckarray;
      }// End else
  }// End function getKategorie_eines_Artikels

  //-------------------------------------------------------------------------------------------
  // Liefert die Kategorie_IDs der Kategorien in welchen der angegebene Artikel eingeteilt ist
  // Argument: Artikel_ID (INT)
  // Rueckgabewert: Array: Kategorie_IDs
  function getKategorieID_eines_Artikels($Artikel_ID) {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getKategorieID_eines_Artikels_1_1;
      global $sql_getKategorieID_eines_Artikels_1_2;
      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar(getKategorieID_eines_Artikels)</H1></P><BR>");
      }
      else {
          $rueckarray = array();
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getKategorieID_eines_Artikels_1_1".trim($Artikel_ID)."$sql_getKategorieID_eines_Artikels_1_2");
          //Verpacken der einzelnen Kategorie_IDs in einen Array
          while (is_object($RS) && $RS->NextRow()) {
              $rueckarray[] = $RS->GetField("FK_Kategorie_ID");
          }
          // Zurueck gegeben wird ein Array mit den Kategorie_IDs
          return $rueckarray;
      }// End else
  }// End function getKategorieID_eines_Artikels

  // -----------------------------------------------------------------------
  // Liefert als Objekt eine (Unter-)Kategorie
  // Argument: Kategorie_ID (INT)
  // Rueckgabewert: ein Unterkategorie-Objekt (Abbruch per die-Funktion und Fehlermeldung)
  function getKategorie($Kategorie_ID) {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getKategorie_1_1;
      global $sql_getKategorie_1_2;
      global $sql_getKategorie_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (getKategorie))</H1></P><BR>");
      }
      else {
          // Auslesen der Kategorie
          $RS = $Database->Query("$sql_getKategorie_1_1".$Kategorie_ID);
          if ($RS && $RS->NextRow()) {
              $meineKategorie = new Unterkategorie; //Ein neues Unterkategorie-Objekt instanzieren (siehe auch kategorie_def.php)
              $meineKategorie->Kategorie_ID = $RS->GetField("Kategorie_ID");
              $meineKategorie->Name = $RS->GetField("Name");
              $meineKategorie->Positions_Nr = $RS->GetField("Positions_Nr");
              $meineKategorie->Beschreibung = $RS->GetField("Beschreibung");
              $meineKategorie->Bild_gross = $RS->GetField("Bild_gross");
              $meineKategorie->Bild_klein = $RS->GetField("Bild_klein");
              $meineKategorie->Bildtyp = $RS->GetField("Bildtyp");
              $meineKategorie->Bild_last_modified = $RS->GetField("Bild_last_modified");
              $meineKategorie->MwSt_default_Satz = $RS->GetField("MwSt_Satz");
              $meineKategorie->Unterkategorie_von = $RS->GetField("Unterkategorie_von");
              $meineKategorie->Details_anzeigen = $RS->GetField("Details_anzeigen");
              //allfaellige Unterkategorien auslesen

              $RS_2 = $Database->Query("$sql_getKategorie_1_2'".addslashes($meineKategorie->Name)."' $sql_getKategorie_1_3");
              if (!is_object($RS_2)) {
                  echo "<B class='content'><U>U_A_H_Error: getKategorie: RS_2 ist kein Objekt</U></B><BR><BR>";
                  die("Query: $sql_getKategorie_1_2'".addslashes($meineKategorie->Name)."'$sql_getKategorie_1_3<BR>");
              }
              while (is_object($RS_2) && $RS_2->NextRow()){
                  //Jetzt kommen die Unterkategorien
                  $meineUnterkategorie = new Unterkategorie; //Ein neues Unterkategorien-Objekt erzeugen
                  $meineUnterkategorie->Kategorie_ID = $RS_2->GetField("Kategorie_ID");
                  $meineUnterkategorie->Name = $RS_2->GetField("Name");
                  $meineUnterkategorie->Positions_Nr = $RS_2->GetField("Positions_Nr");
                  $meineUnterkategorie->Beschreibung = $RS_2->GetField("Beschreibung");
                  $meineUnterkategorie->Bild_gross = $RS_2->GetField("Bild_gross");
                  $meineUnterkategorie->Bild_klein = $RS_2->GetField("Bild_klein");
                  $meineUnterkategorie->Bildtyp = $RS_2->GetField("Bildtyp");
                  $meineUnterkategorie->Bild_last_modified = $RS_2->GetField("Bild_last_modified");
                  $meineUnterkategorie->MwSt_default_Satz = $RS_2->GetField("MwSt_Satz");
                  $meineUnterkategorie->setUnterkategorie_von($RS_2->GetField("Unterkategorie_von"));
                  $meineUnterkategorie->Details_anzeigen = $RS_2->GetField("Details_anzeigen");
                  //Die ausgelesene Unterkategorie wird der Kategorie zugeordnet (in ihren Array ablegen)
                   $meineKategorie->Unterkategorien[] = $meineUnterkategorie;
                  unset($meineUnterkategorie);
              }// End while
          }// End if
          else {
              echo "<B class='content'><b>Diese Kategorie existiert nicht!</b><br><br><U>U_A_H_Error: getKategorie: RS ist kein Objekt</U></B><BR><BR>";
              die("Query: $sql_getKategorie_1_1".$Kategorie_ID."<BR>");
          }
      //Zurueckgegeben wird ein Unterkategorien-Objekt (auch wenn es sich um eine Kategorie handelt
      return $meineKategorie;
      }// End else
  }// End function getKategorie

  // -----------------------------------------------------------------------
  // Diese Funktion testet, ob eine Kategorie Unterkategorien hat oder nicht.
  // Die Funktion verlangt einen Kategorie-/Unterkategorienamen und gibt als Antwort
  // einen Array mit Kategorie Objekten wieder. Wenn die Kategorie/Unterkategorie
  // keine Unterkategorien besitzt, so wird als Antwort im ersten Arrayfeld
  // ein Kategorieobjekt mit dem Namen @PhPepperShop@ zurueckgegeben.
  // Anmerkung: Von den gefundenen Unterkategorieobjekten werden nur die  Kategorieattribute ausgelesen,
  // wenn eine dieser Kategorie/Unterkategorie aber Unterkategorien besitzt, so werden diese NICHT
  // in den Unterkategorien-Array des Objekts ausgelesen (Dazu bitte noch getKategorie()
  // benutzen). Dies, weil man auf diese Weise die Datenmenge klein halten kann.
  // Argumente: Kategorie-ID
  // Rueckgabewert: Unterkategorien (Array mit Unterkategorie-Objekten)
  function checkaufUnterkategorien($Kategoriename) {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_checkaufUnterkategorien_1_1;
      global $sql_checkaufUnterkategorien_1_2;

      // Generierung eines leeren Rueckgabearrays und Unterkategorieobjekts
      $returnarray = array();
      $tempkategorie = new Unterkategorie();

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (checkaufUnterkategorien))</H1></P><BR>");
      }
      // Auslesen der Kategorie
      $RS = $Database->Query("$sql_checkaufUnterkategorien_1_1".$Kategoriename."$sql_checkaufUnterkategorien_1_2");
      // Wenn die ueberpruefte Kategorie/Unterkategorie weitere Unterkategorien besitzt:
      if (is_object($RS)) {
          while ($RS->NextRow()){
              // Abpacken der Unterkategorieattribute in ein temp. Kategorieobjekt
              $tempkategorie->Kategorie_ID = $RS->GetField("Kategorie_ID");
              $tempkategorie->Positions_Nr = $RS->GetField("Positions_Nr");
              $tempkategorie->Name = $RS->GetField("Name");
              $tempkategorie->Beschreibung = $RS->GetField("Beschreibung");
              $tempkategorie->Bild_gross = $RS->GetField("Bild_gross");
              $tempkategorie->Bild_klein = $RS->GetField("Bild_klein");
              $tempkategorie->Bildtyp = $RS->GetField("Bildtyp");
              $tempkategorie->Bild_last_modified = $RS->GetField("Bild_last_modified");
              $tempkategorie->MwSt_default_Satz = $RS->GetField("MwSt_Satz");
              $tempkategorie->Unterkategorie_von = $RS->GetField("Unterkategorie_von");
              $tempkategorie->Details_anzeigen = $RS->GetField("Details_anzeigen");
              // Abfuellen der ausgelesenen Unterkategorie in den Rueckgabearray
              $returnarray[] = $tempkategorie;
          }// End while-Schleife
      }// Enf if
      else {
          // Die Kategorie/Unterkategorie besitzt keine weiteren Unterkategorien
          // Dummy Rueckgabewert mit Kategorienamen @PhPepperShop@ und Kategorie_ID = 0
          // erstellen und in den Rueckgabearray abfuellen.
          $tempkategorie->Kategorie_ID = 0;
          $tempkategorie->Name = "@PhPepperShop@";
          $returnarray[] = $tempkategorie;
      }// End else
      return $returnarray;
  }// End function checkaufUnterkategorien

  // -----------------------------------------------------------------------
  // Diese Funktion ueberprueft, ob eine Kategorie/Unterkategorie Artikel besitzt.
  // Wenn Artikel vorhanden sind, wird als Rueckgabewert deren Anzahl zurueckgegeben,
  // sonst wird eine 0 zurueckgegeben.
  // Argumente: Kategorie-ID (Integer)
  // Rueckgabewert: Anzahl Artikel (Integer)
  function hatKategorieArtikel($Kategorie_ID) {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_hatKategorieArtikel_1_1;

      // Rueckgabewert auf 0 initialisieren:
      $AnzahlArtikel = 0;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (hatKategorieArtikel))</H1></P><BR>");
      }// End if
      else {
          // Auslesen der Kategorie
          $RS = $Database->Query("$sql_hatKategorieArtikel_1_1".$Kategorie_ID);
          while (is_object($RS) && $RS->NextRow()){
              $AnzahlArtikel = $AnzahlArtikel + 1;
          }
      }// End else
      return $AnzahlArtikel;
  }// End function hatKategorieArtikel


  // -----------------------------------------------------------------------
  // Liefert als einen String die Waehrung zurueck. (z.B. SFr., CHF, DM, Ös)
  // Argumente: keine
  // Rueckgabewert: Waehrung (String)
  function getWaehrung() {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getWaehrung_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getWaehrung_1");
          if (is_object($RS) && $RS->NextRow()) {
              return $RS->GetField("Waehrung");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der W&auml;hrung</H1></P><BR>";
              die("<P><H1 class='content'>Query: $sql_getWaehrung_1</H1></P>");
          }
      }//End else
  }//End getWaehrung

  // -----------------------------------------------------------------------
  // Liefert als einen String die Gewichts-Masseinheit zurueck. (z.B. kg, mg, t, ...)
  // Argumente: keine
  // Rueckgabewert: Gewichts-Masseinheit (String)
  function getGewichts_Masseinheit() {
      global $Database;
      global $sql_getGewichts_Masseinheit_1;
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getGewichts_Masseinheit_1");
          if (is_object($RS) && $RS->NextRow()) {
              return $RS->GetField("Gewichts_Masseinheit");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Gewichts_Masseinheit</H1></P><BR>");
          }
      }//End else
  }//End getGewichts_Masseinheit

  // -----------------------------------------------------------------------
  // Liefert als einen Integer die Breite des kleinen Bildes zurueck (Thumbnail)
  // Argumente: keine
  // Rueckgabewert: Thumbnail-Breite
  function getThumbnail_Breite() {

      // Einlesen von in anderen Modulen deklarierten Variablen
      global $Database;
      global $sql_getThumbnail_Breite_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getThumbnail_Breite</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getThumbnail_Breite_1");
          if (is_object($RS) && $RS->NextRow()) {
              return $RS->GetField("Thumbnail_Breite");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Thumbnail-Breite</H1></P><BR>");
          }
      }//End else
  }//End getThumbnail_Breite

  // -----------------------------------------------------------------------
  // Liefert als einen String den in der Datenbank definierten Shopnamen
  // Argumente: keine
  // Rueckgabewert: Shopname als String
  function getshopname() {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getShopname_1;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getShopname</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getShopname_1");
          if (is_object($RS) && $RS->NextRow()) {
              return $RS->GetField("Name");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen des Shopnamens</H1></P><BR>");
          }
      }//End else
  }//End getshopname

  // -----------------------------------------------------------------------
  // Liefert die maximale Zeit fuer wie lange eine Session gueltig ist (in Sek.)
  // Argumente: keine
  // Rueckbagewert: Maximale Zeit die ein User eine Session haben kann
  function getmax_session_time() {
      global $Database;
      global $sql_getmax_session_time_1_1;

      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getShopname</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getmax_session_time_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              return $RS->GetField("max_session_time");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der maximalen_Session_Zeit</H1></P><BR>");
          }
      }//End else
  }//End getmax_session_time

  // -----------------------------------------------------------------------
  // Liefert einen Array wo drin steht welche Zahlungsarten gewaehlt werden koennen
  // Der jeweilige Name ist 'Y' wenn er gewaehlt werden kann (sonst 'N')
  // Rueckgabe-Array:   Key = Zahlungsart, Wert = 'Y' oder 'N'
  // Bsp.  rueckgabearray[Rechnung] wuerde z.B. true ergeben, dann kann man also per
  // Rechnung bezahlen
  function getBezahlungsart() {

      // Einlesen von in anderen Modulen definierten Vairablen
      global $Database;
      global $sql_getBezahlungsart_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getBezahlungsart</H1></P><BR>");
      }
      else {
          // Test ob per Pauschale abgerechnet wird. Dann muss nicht weiter ueberprueft werden, ob in dem Betrags-Intervall
          // in welchem sich die aktuelle Bestelllung befindet, irgendeine Zahlungsmethode vom Shopadministrator gesperrt wurde.
          // Die shopweiten Versandkosten-Einstellungen auslesen (Das Argument 1 bezieht sich auf die Setting_Nr 1)
          $Versandkosteneinstellungen = getversandkostensettings(1);
          $pauschale = false; // Dieses Flag wird true, wenn die Versandkosten per Pauschale abgerechnet werden
          if ($Versandkosteneinstellungen->Abrechnung_nach_Pauschale == "Y") {
              $pauschale = true;
          }
          else {
              $pauschale = false;
              // Da nicht pauschal abgerechnet wird, muessen wir noch herausfinden, welche Zahlungsmethoden in diesem Betrags-
              // Intervall freigeschaltet sind:
              // Bestellungsversandkosten auslesen:
              $myBestellung = getBestellung(session_id());
              $myRechnungsbetrag = $myBestellung->Rechnungsbetrag;
              $myVersandkostenpreise = $Versandkosteneinstellungen->getallversandkostenpreise();
              $Versandkostenpreisintervall = 1; //Initialisierung dieser Variable
              for($i=0;$i < count($myVersandkostenpreise);$i++) {
                  $dieses = $myVersandkostenpreise[$i]->Von;
                  $naechstes = $myVersandkostenpreise[$i]->Bis;
                  // Schauen ob Rechnungsbetrag in ein Von-Bis Intervall passt:
                  if (($dieses <= $myRechnungsbetrag) && ($naechstes > $myRechnungsbetrag)) {
                      $Versandkostenpreisintervall = $i;
                      break; //For-Schleife abbrechen und weiterfahren
                  }// End if
                  // Schauen ob Rechnungsbetrag in ein Von-Bis Zwischen-Intervall passt:
                  // Hier ist man sicher, dass es noch eine weitere Tabellen-Zeile gibt (Von+1)
                  $dieses = $myVersandkostenpreise[$i]->Bis;
                  $naechstes = $myVersandkostenpreise[($i+1)]->Von;
                  if (($dieses <= $myRechnungsbetrag) && ($naechstes > $myRechnungsbetrag)) {
                      $Versandkostenpreisintervall = $i;
                      break; //For-Schleife abbrechen und weiterfahren
                  }// End if
              }// End for
              // Auslesen aller erlaubten Zahlungsmethoden
              $erlaubteZahlungsmethodenarray['Vorauskasse'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->Vorauskasse;
              $erlaubteZahlungsmethodenarray['Rechnung'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->Rechnung;
              $erlaubteZahlungsmethodenarray['Nachname'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->Nachname;
              $erlaubteZahlungsmethodenarray['Lastschrift'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->Lastschrift;
              $erlaubteZahlungsmethodenarray['Kreditkarte'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->Kreditkarte;
              // Wenn eine weitere Zahlungsmethode hinzugefuegt wird, muss die Klasse Versandkostenpreis um ein entsprechendes
              // Attribut erweitert werden und auch hier bedarf es einer speziell dafuer vorgesehenen Zeile. Bsp. billBOX:
              $erlaubteZahlungsmethodenarray['billBOX'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->billBOX;
              $erlaubteZahlungsmethodenarray['Treuhandzahlung'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->Treuhandzahlung;
              $erlaubteZahlungsmethodenarray['Postcard'] = $myVersandkostenpreise[$Versandkostenpreisintervall]->Postcard;
          }// End else

          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getBezahlungsart_1_1");
          // Abarbeitung der Resultate. Dabei wird in einem if-Statement ueberprueft, dass wenn man die Versandkosten
          // nicht pauschal abrechnet, dass danach noch ueberprueft wird, ob die entsprechende Zahlungsmethode in diesem
          // Betragsintervall ueberhaupt erlaubt ist.
          if (is_object($RS) && $RS->NextRow()) {
              if ($pauschale == false) {
                  if ($erlaubteZahlungsmethodenarray['Vorauskasse'] == "Y") {
                      $Vorauskasse = $RS->GetField("Vorauskasse");
                  }
                  else {
                      $Vorauskasse = "N";
                  }
              }
              else {
                      $Vorauskasse = $RS->GetField("Vorauskasse");
              }
              if ($pauschale == false) {
                  if ($erlaubteZahlungsmethodenarray['Rechnung'] == "Y") {
                      $Rechnung = $RS->GetField("Rechnung");
                  }
                  else {
                      $Rechnung = "N";
                  }
              }
              else {
                      $Rechnung = $RS->GetField("Rechnung");
              }

              if ($pauschale == false) {
                  if ($erlaubteZahlungsmethodenarray['Nachname'] == "Y") {
                      $Nachnahme = $RS->GetField("Nachnahme");
                  }
                  else {
                      $Nachnahme = "N";
                  }
              }
              else {
                      $Nachnahme = $RS->GetField("Nachnahme");
              }
              if ($pauschale == false) {
                  if ($erlaubteZahlungsmethodenarray['Lastschrift'] == "Y") {
                      $Lastschrift = $RS->GetField("Lastschrift");
                  }
                  else {
                      $Lastschrift = "N";
                  }
              }
              else {
                      $Lastschrift = $RS->GetField("Lastschrift");
              }
              // Achtung: Der Name Kreditkarten_Postcard ist ein historisch
              // entstandener Begriff, tatsaechlich meinen wir damit aber
              // nur die Kreditkarten. Postcard hat einen eigenen Abschnitt.
              if ($pauschale == false) {
                  if ($erlaubteZahlungsmethodenarray['Kreditkarte'] == "Y") {
                      $Kred_Post = $RS->GetField("Kreditkarten_Postcard");
                  }
                  else {
                      $Kred_Post = "N";
                  }
              }
              else {
                      $Kred_Post = $RS->GetField("Kreditkarten_Postcard");
              }
              // Evaluierte Resultate in den Rueckgabearray schreiben
              $rueckgabearray = array();
              $rueckgabearray['Vorauskasse'] = $Vorauskasse;
              $rueckgabearray['Rechnung'] = $Rechnung;
              $rueckgabearray['Nachnahme'] = $Nachnahme;
              $rueckgabearray['Lastschrift'] = $Lastschrift;
              $rueckgabearray['Kred_Post'] = $Kred_Post;

              // Jetzt werden noch die weiteren Zahlungsmethoden aus der Tabelle zahlung_weitere ausgelesen
              // Achtung: die Zahlungsart Postcard kommt in diesem Array als Postfinance benennt daher. Im
              // Array $erlaubteZahlungsmethodenarray wird sie aber Postcard genannt. Dies kommt daher, dass
              // Postfinance auch fuer Kreditkartenzahlungen benutzt wird.
              $myAllezahlungen = getAllezahlungen();

              foreach($myAllezahlungen->getallzahlungen() as $value) {
                  // Wenn die weitere Zahlungsart verwendet werden soll:
                  if ($pauschale == false) {
                      if ($value->Bezeichnung == 'Postfinance') {
                          $check_this = 'Postcard';
                      }
                      else {
                          $check_this = $value->Bezeichnung;
                      }
                      if ($erlaubteZahlungsmethodenarray[$check_this] == "Y") {
                          // Spezialbehandlung fuer die Postcard von Postfinance yellowpay
                          if ($value->Bezeichnung == 'Postfinance') {
                              $rueckgabearray['Postcard'] = $value->verwenden;
                          }
                          else {
                              $rueckgabearray[$value->Bezeichnung] = $value->verwenden;
                          }
                      }
                  }
                  else {
                     if ($value->Bezeichnung == "Postfinance") {
                         $rueckgabearray['Postcard'] = $value->verwenden;
                     }
                     else {
                         $rueckgabearray[$value->Bezeichnung] = $value->verwenden;
                     }
                  }
              }// End foreach
              //Nun wird das Array zurueckgegeben
              return $rueckgabearray;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Bezahlungsarten</H1></P><BR>");
          }
      }//End else
  }//End getBezahlungsart

  // -----------------------------------------------------------------------
  // Liefert als String die allgemeinen Geschaeftsbedingungen (AGB) aus der DB
  // Argumente: keine
  // Rueckgabewert: Allgemeine Geschaeftsbedingungen (String)
  function getAGB() {

      // Einlesen von in anderen Modulen deklariertn Variablen
      global $Database;
      global $sql_getAGB_1_1;

      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getAGB</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getAGB_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $AGB = $RS->GetField("AGB");
              return $AGB;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der AGB</H1></P><BR>");
          }
      }//End else
  }//End getAGB

  // -----------------------------------------------------------------------
  // Liefert die Texte und Preise aller Optionen und Variationen eines Artikels
  // Dieser Artikel wird benoetigt, da nie Preisinformationen von Seite zu Seite
  // uebertragen wird (zu unsicher). Preisinformationen jeglicher Art, kommen immer
  // direkt von der Datenbank in die Ziel-Page
  // Argument: Artikel_ID
  // Rueckgabewert: Ein Artikel-Objekt das Optionen und Variationen enthaelt
  function get_var_opt_preise($Artikel_ID) {
      global $Database;
      global $sql_get_var_opt_preise_1_1;
      global $sql_get_var_opt_preise_2_1;

      if (!is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar</H1></P><BR>");
      }
      else {
          // Zuerst alle Optionen auslesen und in Ziel-Variable speichern
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $myArtikel = new Artikel; // Hier drin werden die Arrays abgelegt
          $RS = $Database->Query("$sql_get_var_opt_preise_1_1".$Artikel_ID);
          while (is_object($RS) && $RS->NextRow()) {
              $myArtikel->putoption($RS->GetField("Optionstext"), $RS->GetField("Preisdifferenz"));
          }
          $RS = $Database->Query("$sql_get_var_opt_preise_2_1".$Artikel_ID);
          while (is_object($RS) && $RS->NextRow()) {
              $myArtikel->putvariation($RS->GetField("Variationstext"), $RS->GetField("Aufpreis"));
          }
          return $myArtikel;
      }
  }//End get_var_opt_preise

  // -----------------------------------------------------------------------
  // Liefert als String die E-Mail Adresse passend zur Bestellung welche von der
  // im Argument angegebenen Session_ID referenziert wird (Kunden E-Mail Adresse)
  // Argument: Session_ID
  // Rueckgabewert: E-Mail-Adresse als String (Eingaben ungepruegft!)
  function getEmail($Session_ID) {

      // Einbinden von in anderern Modulen deklarierten Variablen
      global $Database;
      global $sql_getEmail_1_1;
      global $sql_getEmail_1_2;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getEmail</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getEmail_1_1".$Session_ID."$sql_getEmail_1_2");
          if (is_object($RS) && $RS->NextRow()) {
              $Email = $RS->GetField("Email");
              return $Email;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der E-Mail Adresse</H1></P><BR>");
          }
      }//End else
  }//End getEmail

  // -----------------------------------------------------------------------
  // Liefert als String die E-Mail Adresse des Shops (Administrator des Shops)
  // Argumente: keine
  // Rueckgabewert: Shop-Email-Adresse
  function getShopEmail() {

      // Einlesen von in anderen Moudulen deklarierten Variablem
      global $Database;
      global $sql_getShopEmail_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getShopEmail</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getShopEmail_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $Email = $RS->GetField("Email");
              return $Email;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der E-Mail Adresse des Shops</H1></P><BR>");
          }
      }//End else
  }//End getShopEmail

  // -----------------------------------------------------------------------
  // Liefert als Int das Such-Inkrement -> Wieviele gefundene Resultate auf einmal anzeigen
  // Argumente: keine
  // Rueckgabewert: Such-Inkrement (Int)
  function getSuchInkrement() {

      // Einlesen von in anderen Moudulen deklarierten Variablem
      global $Database;
      global $sql_getSuchInkrement_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar! (getSuchInkrement)</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getSuchInkrement_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $SuchInkrement = $RS->GetField("SuchInkrement");
              return $SuchInkrement;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              echo "<B>Query:</B> $sql_getSuchInkrement_1_1<BR>";
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen des Such-Inkrements des Shops (getSuchInkrement)</H1></P><BR>");
          }
      }//End else
  }//End getSuchInkrement

  // -----------------------------------------------------------------------
  // Liefert 'Y' oder 'N', jenachdem ob das Bestellungsmanagement erwuenscht ist oder nicht
  // Eingestellt wird dieses Attribut in den allgemeinen Shop Einstellungen (setshopsettings)
  // Argumente: keine
  // Rueckgabewert: 'Y' oder 'N' (String)
  function getBestellungsmanagement() {

      // Einlesen von in anderen Moudulen deklarierten Variablem
      global $Database;
      global $sql_getBestellungsmanagement_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getBestellungsmanagement</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getBestellungsmanagement_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $bm = $RS->GetField("Bestellungsmanagement");
              return $bm;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Bestellungsmanagement-Variablen (getBestellungsmanagement)</H1></P><BR>");
          }
      }//End else
  }//End getShopEmail

  // -----------------------------------------------------------------------
  // Wenn SSL/TLS in den allgemeinen Shopeinstellungen eingeschaltet ist, so wird true (Boolean)
  // zurueckgegeben, ansonsten false (Boolean).
  // Argumente: keine
  // Rueckgabewert: true | false (Boolean)
  function getSSLsetting() {

      // Einlesen von in anderen Moudulen deklarierten Variablem
      global $Database;
      global $sql_getSSLsetting_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<p><h2 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getSSLsetting</h2></p></body></html>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_getSSLsetting_1_1);
          if (is_object($RS) && $RS->NextRow()) {
              $ssl = $RS->GetField("TLS_value"); // TLS_value hiess frueher SSL
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<p><h2 class='content'>U_A_H_Error: Fehler beim auslesen der SSL-Variablen TLS_value: getSSLsetting</h2></p></body></html>");
          }
          if ($ssl == 'Y') {
              $rueckgabewert = true;
          }
          else {
              $rueckgabewert = false;
          }
      }//End else db ok
      return $rueckgabewert;
  }//End getSSLsetting

  // -----------------------------------------------------------------------
  // *** Anmerkung: Wenn man nur wissen will, ob SSL/TLS eingeschaltet ist oder nicht,
  // soll man die Funktion getSSLsetting benutzen. ***
  // Diese Funktion uberprueft ob SSL/TLS eingeschaltet wurde. Wenn ja, so wird eine
  // absolute URL zusammengebaut und https voran gestellt. Um wieder aus dem SSL-Bereich
  // austreten zu koennen, kann man der Funktion im Flag $noSSL (true) mitteilen, dass
  // man den SSL-Bereich verlassen will. Es wird dann als Rueckgabewert eine URL mit
  // http zurueckgegeben. Mit der Variable $server wird meistens $HTTP_HOST uebergeben.
  // Ein Aufruf dieser Funktion sieht typischerweise so aus: getSSL($PHP_SELF,$HTTP_HOST,false)
  // Eingestellt wird SSL in den allgemeinen Shop Einstellungen (setshopsettings)
  // Anmerkung: Seit v.1.06 wird $HTTP_HOST anstatt $SERVER_NAME verwendet. Dies erlaubt nun auch
  // den Einsatz des Shops mit einer nummerischen IP, anstatt dem DNS-Namen.
  // Anmerkung: Bis und mit PhPepperShop v.1.3 hiess das Attribut in der Tabelle shop_settings
  // noch SSL. Da dies viele Probleme verursachte weil es ein reserviertes MySQL-Wort ist, wurde
  // das Attribut jetzt in TLS_value umbenannt.
  // Argumente: $oldphpself (String, $PHP_SELF), $server(String, $HTTP_HOST), $noSSL (boolean)
  // Rueckgabewert: neuer URL (mit oder ohne https)
  function getSSL($oldphpself, $server, $noSSL) {

      // Einlesen von in anderen Moudulen deklarierten Variablem
      global $Database;
      global $sql_getSSL_1_1;

      if ($noSSL) {
          $newphpself = "http://".$server.$oldphpself;
      }
      else {
          // Test ob die Datenbank erreichbar ist
          if (! is_object($Database)) {
              die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getSSL</H1></P><BR>");
          }
          else {
              //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Database->Query("$sql_getSSL_1_1");
              if (is_object($RS) && $RS->NextRow()) {
                  $ssl = $RS->GetField("TLS_value"); // TLS_value hiess frueher SSL
              }
              else {
                  //Script mit einer Fehlermeldung beenden
                  die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der SSL-Variablen (getSSL)</H1></P><BR>");
              }
              if ($ssl == 'Y') {
                  $newphpself = "https://".$server.$oldphpself;
              }
              else {
                  $newphpself = "http://".$server.$oldphpself;
              }
          }//End else db ok
      }//End else noSSL
      return $newphpself;
  }//End getSSL

  // -----------------------------------------------------------------------
  // Einen CSS-String aus der Tabelle css_file auslesen:
  // Argumente: CSS-Identifier
  // Rueckgabewert: zugehoeriger CSS-String
  function getcssarg($css_id) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Database;
      global $sql_cssget_1_1;
      global $sql_cssget_1_2;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank konnte nicht erreicht werden: getcssarg</H1></P><BR>");
      }
      else {
           $RS = $Database->Query($sql_cssget_1_1.$css_id.$sql_cssget_1_2);
           if (is_object($RS) && $RS->NextRow()){
               $css_string = $RS->GetField("CSS_String");
           }
      }
      return $css_string;
  }// End getcssarg

  // -----------------------------------------------------------------------
  // Aus den Shopsettings auslesen, wieviele Felder fuer die Erfassung von
  // Optionen und Variationen bei einem Artikel mindestens dargestellte werden
  // Zusätlich wird noch der Wert ausgelesen, wie viele leere Felder angezeigt
  // werden, wenn die Anzahl der eingegeben Optionen/Variationen die Anzahl
  // der Eingestellten Minimalfelderanzahl Uebersteigt
  // Argumente: keine
  // Rueckgabewert: entweder Array:
  //                             Element 1 = Optionen Inkrement (Opt_inc),
  //                             Element 2 = Variations Inkrement (Var_inc)
  //                             Element 3 = Optionen Basisanzahl, (Opt_anz)
  //                             Element 4 = Variations Basisanzahl (Var_anz)
  //                             Element 5 = Variationsgruppen Anzahl (Vargruppen_anz)
  //                             Element 6 = Eingabefelder Anzahl (Eingabefelder_anz)


  //                oder Abbruch per die-Funktion
  function getvaroptinc() {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Database;
      global $sql_getvaroptinc_1_1;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank konnte nicht erreicht werden: getvaroptinc</H1></P><BR>");
      }
      else {
           $RS = $Database->Query($sql_getvaroptinc_1_1);
           $rueckarray = array();
           if (is_object($RS) && $RS->NextRow()){
               $rueckarray[] = $RS->GetField("Opt_inc");
               $rueckarray[] = $RS->GetField("Var_inc");
               $rueckarray[] = $RS->GetField("Opt_anz");
               $rueckarray[] = $RS->GetField("Var_anz");
               $rueckarray[] = $RS->GetField("Vargruppen_anz");
               $rueckarray[] = $RS->GetField("Eingabefelder_anz");
           } // end of if
      }
      return $rueckarray;
  }// End getvaroptinc

  // -----------------------------------------------------------------------
  // Liefert den Geldwert welcher bei einer Nachnahmelieferung zu verrechnen ist
  // Argumente: keine
  // Rueckgabewert: Nachnahmebetrag (DOUBLE), 0 = keiner vorhanden
  function getNachnahmebetrag() {

      // Einlesen von in anderen Moudulen deklarierten Variablem
      global $Database;
      global $sql_getNachnahmebetrag_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getNachnahmebetrag</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getNachnahmebetrag_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $nm = $RS->GetField("Nachnamebetrag");
              return $nm;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Nachnahme-Geb&uuml;hr (getNachnahmebetrag)</H1></P><BR>");
          }
      }//End else
  }//End getNachnahmebetrag

  // -----------------------------------------------------------------------
  // Liefert den String, welcher man in den allgemeinen Shop-Einstellungen
  // neben Vorauskasse im Feld Kontoinformation eingeben kann
  // Argumente: keine
  // Rueckgabewert: Kontoinformation (String) oder Abbruch per die-Funktion
  function getKontoinformation() {

      // Einlesen von in anderen Moudulen deklarierten Variablem
      global $Database;
      global $sql_getKontoinformation_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getKontoinformation</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getKontoinformation_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $nm = $RS->GetField("Kontoinformation");
              return $nm;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Kontoinformation (getKontoinformation)</H1></P><BR>");
          }
      }//End else
  }//End getKontoinformation

  // -----------------------------------------------------------------------
  // Diese Funktion liest alle Kreditkartendaten aus der kreditkarten-Tabelle aus
  // und gibt alle Kreditkarten-Objekte in einem Array zurueuck
  // Argumente: keine
  // Rueckgabewert: Array von Kreditkarten-Objekten
  function getKreditkarten() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getKreditkarten_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getKreditkarten</H1></P><BR>");
      }
      else {
           //Auslesen aller Kreditkartendaten
           $RS = $Database->Query("$sql_getKreditkarten_1_1");
           if (!$RS){
              echo "<B>Query:</B> $sql_getKreditkarten_1_1<BR>";
              die("<P><H1 class='content'>U_A_H_Error: Konnte Kreditkarten nicht auslesen: getKreditkarten</H1></P><BR>");
           }
           $Kreditkartenarray = array();
           while (is_object($RS) && $RS->NextRow()){
               $Kreditkarte = new Kreditkarte;
               $Kreditkarte->Hersteller = $RS->GetField("Hersteller");
               $Kreditkarte->benutzen = $RS->GetField("benutzen");
               $Kreditkarte->Handling = $RS->GetField("Handling");
               $Kreditkarte->Kreditkarten_ID = $RS->GetField("Kreditkarten_ID");
               $Kreditkartenarray[] = $Kreditkarte;
           }
           return $Kreditkartenarray;
      }//End else
  }//End getKreditkarten

  // -----------------------------------------------------------------------
  // Liefert als einen String den in der Datenbank definierten Shopnamen
  // Argumente: keine
  // Rueckgabewert: Array mit Shopangaben in folgender Reihenfolge (beginnend bei 0):
  //                1. Element = Shopname
  //                2. Element = Adresse1
  //                3. Element = Adresse2
  //                4. Element = PLZ und Ort
  //                5. Element = E-Mail Adresse
  //                6. Element = Telefon
  //                7. Element = Fax
  function getShopadresse() {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getShopadresse_1_1;

      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar getShopadresse</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getShopadresse_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $rueckarray[0] = $RS->GetField("Name");
              $rueckarray[1] = $RS->GetField("Adresse1");
              $rueckarray[2] = $RS->GetField("Adresse2");
              $rueckarray[3] = $RS->GetField("PLZOrt");
              $rueckarray[4] = $RS->GetField("Email");
              $rueckarray[5] = $RS->GetField("Tel1");
              $rueckarray[6] = $RS->GetField("Tel2");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Shopangaben</H1></P><BR>");
          }
          return $rueckarray;
      }//End else
  }//End getShopadresse

  // -----------------------------------------------------------------------
  // Liefert in einem Array alle Daten der Tabelle Backup. (alphabetisch geordnet)
  // Argumente: keine
  // Rueckgabewert: Array mit den Backup Settings:
  //                1.Element Name: 'Anzahl_Backups'      2. Element Wert (INT)
  //                3.Element Name: 'Automatisierung'     4. Element Wert (enum('auto','cron','kein'))
  //                5.Element Name: 'Backup_Intervall'    6. Element Wert (INT)
  //                7.Element Name: 'Komprimierung'       8. Element Wert (enum('Y','N'))

  function getBackupSettings() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getBackupSettings_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getBackupSettings</H1></P><BR>");
      }
      else {
           //Auslesen aller Backup Einstellungen
           $RS = $Database->Query("$sql_getBackupSettings_1_1");
           if (!$RS){
              echo "<B>Query:</B> $sql_getBackupSettings_1_1<BR>";
              die("<P><H1 class='content'>U_A_H_Error: Konnte Backup Einstellungen nicht auslesen: getBackupSettings</H1></P><BR>");
           }
           $Rueckarray = array();
           while (is_object($RS) && $RS->NextRow()){
               $Rueckarray[] = $RS->GetField("Backup_ID");
               $Rueckarray[] = $RS->GetField("Wert");
           }
           return $Rueckarray;
      }//End else
  }//End getBackupSettings

  // ---------------------------------------------------------------------------------------------
  // Diese Funktion liest alle weiteren Zahlungsmethoden aus der zahlung_weitere-Tabelle aus
  // und gibt alle Zahlungs-Objekte in einem AlleZahlungen-Objekt zurueck (siehe zahlung_def.php).
  // Argumente: keine
  // Rueckgabewert: Allezahlungen-Objekt
  function getAllezahlungen() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getAllezahlungen_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getAllezahlungen</H1></P><BR>");
      }
      else {
           //Auslesen aller weiteren Zahlungsmethoden und ihren Eigenschaften (Parameter 1 - 10)
           $RS = $Database->Query("$sql_getAllezahlungen_1_1");
           if (!$RS){
              echo "<B>Query:</B> $sql_getAllezahlungen_1_1<BR>";
              die("<P><H1 class='content'>U_A_H_Error: Konnte weitere Zahlungsmethoden nicht auslesen: getAllezahlungen</H1></P><BR>");
           }
           // Instanzieren der benoetigten Objekte (je einmal ein Objekt der Klassen Zahlung und Allezahlungen)
           // Allezahlungen ist ein Objekt, welches ein Array von Zahlungen aufnimmt.
           $myAllezahlungen = new Allezahlungen;
           while (is_object($RS) && $RS->NextRow()){
               // Auslesen einer Zeile der Tabelle zahlung_weiter (entspricht einer weiteren Zahlungsmethode)
               $myZahlung = new Zahlung; // Objekt neu instanzieren
               $myZahlung->Gruppe = $RS->GetField("Gruppe");
               $myZahlung->Bezeichnung = $RS->GetField("Bezeichnung");
               $myZahlung->verwenden = $RS->GetField("verwenden");
               $myZahlung->payment_interface_name = $RS->GetField("payment_interface_name");
               $myZahlung->putparameter($RS->GetField("Par1"));
               $myZahlung->putparameter($RS->GetField("Par2"));
               $myZahlung->putparameter($RS->GetField("Par3"));
               $myZahlung->putparameter($RS->GetField("Par4"));
               $myZahlung->putparameter($RS->GetField("Par5"));
               $myZahlung->putparameter($RS->GetField("Par6"));
               $myZahlung->putparameter($RS->GetField("Par7"));
               $myZahlung->putparameter($RS->GetField("Par8"));
               $myZahlung->putparameter($RS->GetField("Par9"));
               $myZahlung->putparameter($RS->GetField("Par10"));
               // Ablegen der ausgelesenen Zahlungsmethode in das Allezahlungen-Objekt, welches am Schluss zurueckgegeben wird
               $myAllezahlungen->putzahlung($myZahlung);
           }
           return $myAllezahlungen;
      }//End else
  }//End getAllezahlungen

  // -----------------------------------------------------------------------
  // Liefert einen Array der die Links auf die Bilder des Artikels beinhaltet
  // Argumente: Artikel_ID
  // Rueckgabewert: Array
  // [0] -> Bild_gross
  // [1] -> Bild_klein
  // [2] -> Bildtyp
  // [3] -> Bild_last_modified
  function getArtikelBilder($Artikel_ID) {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getBildervonArtikel_1_1;
      global $sql_getBildervonArtikel_1_2;
      global $sql_getBildervonArtikel_1_3;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getArtikelBilder</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_getBildervonArtikel_1_1.$sql_getBildervonArtikel_1_2.$Artikel_ID.$sql_getBildervonArtikel_1_3);
          if (is_object($RS) && $RS->NextRow()) {
              $bildarray[0] = $RS->GetField("Bild_gross");
              $bildarray[1] = $RS->GetField("Bild_klein");
              $bildarray[2] = $RS->GetField("Bildtyp");
              $bildarray[3] = $RS->GetField("Bild_last_modified");
              return $bildarray;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim Auslesen des der Bilddaten f&uuml;r den Artikel: $Artikel_ID</H1></P><BR>");
          }
      }//End else
  }//End getshopname

  // ---------------------------------------------------------------------------------------------
  // Diese Funktion analisert einen String, der die Parameter für ein Zusatzeingabefeld eines
  // Artikels enthaelt. Die Daten werden in einem assoziativen Array zurueckgegeben
  // Argumente: Zusatzfeld-Parameter-String
  // Rueckgabewert: Parameter-Array
  function zusatzfeld_parameter($Parameter_String) {
      $par_src = array();
      $par_dst = array();
      $par_src = explode(":",$Parameter_String);
      $par_dst["laenge_feld"] = $par_src[0];
      $par_dst["laenge_max"]= $par_src[1];
      $par_dst["hoehe_feld"] = $par_src[2];
      $par_dst["reserve1"] = $par_src[3];
      $par_dst["reserve2"] = $par_src[4];
      return $par_dst;
  } // End zusatzfeld_parameter

  // ---------------------------------------------------------------------------------------------
  // Diese Funktion analisert einen String, der den Beschreibungstext fuer ein Zusatzeingabefeld
  // eines Artikels beinhaltet. Hat es im String ein '<t>', so wird der String aufgeteilt. Dies
  // ermoeglicht es, nach dem Eingabefeld noch einen weiteren Text (z.B Einheit) auszugeben.
  // Argumente: Zusatzfeld-Text-String
  // Rueckgabewert: Text-Array
  function zusatzfeld_beschreibung($Text_String) {
      $par_src = array();
      $par_dst = array();
      $par_src = explode("<t>",$Text_String);
      $par_dst["vor"] = $par_src[0];
      $par_dst["nach"]= $par_src[1];
      return $par_dst;
  } // End zusatzfeld_beschreibung

  // ---------------------------------------------------------------------------------------------
  // Diese Funktion fuellt einen Array in einen Sonderzeichen-getrennten (þ) Spezial-String ab.
  // Dies wird bei der Zuordnung von Variationen und Optionen zu einem bestellten Artikel ge-
  // braucht (muss alles auf eine Zeile in der Tabelle artikel_bestellung). Das Zeichen þ kann
  // man mit Hilfe der Tastenkombination CTRL + 0254 erzeugen.
  // Argumente: Array von Elementen
  // Rueckgabewert: Sonderzeichen-getrennter String
  function spezial_string($Elemente) {
      $ZusatzString = "";
      $counter = 0;
      foreach($Elemente as $Feldinhalt){
          // zwichen jedes Element kommt ein "þ", nicht aber am Anfang und Ende des Strings
          if ($counter != 0){
              $ZusatzString.="þ";
          } // end of if
          $ZusatzString.= $Feldinhalt;
        $counter++;
      } // end of foreach
      return $ZusatzString;
  } // End zusatzfeld_beschreibung

  // -----------------------------------------------------------------------
  // Liefert als einen String die MwSt-Nummer (auch UIN genannt). Wenn der Shop
  // aber nicht MwSt-pflichtig ist, so wird als Rückgabewert eine Null (0) zurückgegeben.
  // Argumente: keine
  // Rueckgabewert: MwSt-Nummer als String, 0 (=Null) wenn Shop nicht MwSt-pflichtig
  function getmwstnr() {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getmwstnr_1;

      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (getmwstnr)</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query("$sql_getmwstnr_1");
          if (is_object($RS) && $RS->NextRow()) {
              $mwstnummer = $RS->GetField("MwStNummer");
              $mwstpflichtig = $RS->GetField("MwStpflichtig");
              if ($mwstpflichtig == "" || $mwstpflichtig == "N") { //Bug in v.1.2: Wenn MwSt-Pflicht ausgeschaltet ist, kommt "" anstatt "N" !
                  $mwstnummer = 0;
              }
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der MwSt-Nummer: getmwstnr()</H1></P><BR>");
          }//End else
          return $mwstnummer;
      }//End else (! is_object($Database))
  }//End getmwstnr

  // -----------------------------------------------------------------------
  // Diese Funktion liefert in einem array die Mehrwertsteuereinstellungen zurueck
  // welche es seit der PhPepperShop Version 1.2 pro Artikel einstellbar gibt. Der Rueck-
  // gabewert ist ein array von MwSt Objekten (siehe dazu: mwst_def.php)
  // Argumente: keine
  // Rueckgabewert: array von MwSt Objekten
  function getmwstsettings() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getmwstsettings_1_1;

      $array_of_mwstsettings = array(); //Variable als Array initialisieren

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getmwstsettings</H1></P><BR>");
      }
      else {
          //Auslesen der MwSt-Settings aus der mehrwertsteuer Tabelle
          $RS = $Database->Query("$sql_getmwstsettings_1_1");
          while (is_object($RS) && $RS->NextRow()){
              $my_mwst = new MwSt();
              $my_mwst->Mehrwertsteuer_ID = $RS->GetField("Mehrwertsteuer_ID");
              $my_mwst->MwSt_Satz = $RS->GetField("MwSt_Satz");
              $my_mwst->Beschreibung = $RS->GetField("Beschreibung");
              $my_mwst->MwSt_default_Satz = $RS->GetField("MwSt_default_Satz");
              $my_mwst->Preise_inkl_MwSt = $RS->GetField("Preise_inkl_MwSt");
              $my_mwst->Positions_Nr = $RS->GetField("Positions_Nr");
              $array_of_mwstsettings[] = $my_mwst; //In Array ablegen des aktuell ausgelesenen MwSt-Objekts
          }//End while
      }//End else
      return $array_of_mwstsettings;
  }//End getmwstsettings

  // -----------------------------------------------------------------------
  // Liefert den Default MwSt-Satz einer Kategorie, z.B. die Zahl 7.6 (für 7.6% MwSt.)
  // Die Funktion liefert -1 zurueck, wenn die Kategorie nicht gefunden wurde!
  // Als Argument kann man entweder einen Kategorienamen (+ Ukat) oder eine Kategorie_ID angeben.
  // Die Funktion wertet danach das Argument aus und verwendet das entsprechende SQL.
  // Argumente: Kategorieidentifikation, Optionales Argument: Unterkategorie_von
  // Rueckgabewert: MwSt-Satz (Integer) (-1 bei nichtfinden der Kategorie)
  function getDefaultMwStSatz($Kategorieidentifikation, $Unterkategorie_von = "") {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getDefaultMwStSatz_1;
      global $sql_getDefaultMwStSatz_2;
      global $sql_getDefaultMwStSatz_3;
      global $sql_getDefaultMwStSatz_4;
      global $sql_getDefaultMwStSatz_5;

      // Test, ob das Argument uebergeben wurde:
      if ($Kategorieidentifikation == "") {
          die("<H3>U_A_H_Error: Es wurde keine Kategorieidentifikation uebergeben -> Abbruch! Funktion: getDefaultMwStSatz</H3><BR>\n");
      }

      // Wenn die Kategorieidentifikation eine Integerzahl ist, so wurde eine Kategorie_ID uebergeben, SQL 1 verwenden, sonst SQL2.
      if (is_int($Kategorieidentifikation) && $Unterkategorie_von == "") {
          $sql_to_run = $sql_getDefaultMwStSatz_1.$Kategorieidentifikation;
      }
      elseif (is_string($Kategorieidentifikation) && $Unterkategorie_von != "") {
          $sql_to_run = $sql_getDefaultMwStSatz_2.$Kategorieidentifikation.$sql_getDefaultMwStSatz_3.$Unterkategorie_von.$sql_getDefaultMwStSatz_4;
      }
      else {
          $sql_to_run = $sql_getDefaultMwStSatz_2.$Kategorieidentifikation.$sql_getDefaultMwStSatz_5;
      }
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H3 class='content'>U_A_H_Error: Datenbank nicht erreichbar (getDefaultMwStSatz)</H3></P><BR>\n");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_to_run);
          if (is_object($RS) && $RS->NextRow()) {
              $MwSt_default_Satz = $RS->GetField("MwSt_Satz");
          }
          else {
              $MwSt_default_Satz = -1;
          }//End else
          return $MwSt_default_Satz;
      }//End else (! is_object($Database))
  }//End getDefaultMwStSatz

  // -----------------------------------------------------------------------
  // Diese Funktion liefert den MwSt-Prozentsatz des entsprechenden Artikels
  // Argumente: Artikel_ID (Integer)
  // Rueckgabewert: MwSt-Prozentsatz (Float) oder Abbruch via die-Funktion
  function getmwstofArtikel($Artikel_ID) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getmwstofArtikel_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getmwstofArtikel</H1></P><BR>");
      }
      else {
          //Auslesen der MwSt-Settings aus der mehrwertsteuer Tabelle
          $RS = $Database->Query($sql_getmwstofArtikel_1_1.$Artikel_ID);
          $MwSt_Satz = -3.0; //Initialisierung mit einem ungueltigen MwSt-Satz
          if (is_object($RS) && $RS->NextRow()) {
              $MwSt_Satz = $RS->GetField("MwSt_Satz");
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Artikel (ID='$Artikel_ID') hat keinen definierten MwSt-Satz -> Abbruch: getmwstofArtikel</H2></P><BR>");
          }//End else
      }//End else
      return $MwSt_Satz;
  }//End getmwstofArtikel

  // -----------------------------------------------------------------------
  // Diese Funktion liefert den MwSt-Prozentsatz, welcher in der Tabelle mehrwertsteuer
  // das Attribut MwSt_default_Satz = Y hat. Er wird meistens dazu verwendet den Standard
  // MwSt-Satz zu definieren.
  // Argumente: keine
  // Rueckgabewert: MwSt-Prozentsatz (Float) oder Abbruch via die-Funktion
  function getstandardmwstsatz() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getstandardmwstsatz_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getstandardmwstsatz</H1></P><BR>");
      }
      else {
          //Auslesen der MwSt-Settings aus der mehrwertsteuer Tabelle
          $RS = $Database->Query($sql_getstandardmwstsatz_1_1);
          $MwSt_default_Satz = -3.0; //Initialisierung mit einem ungueltigen MwSt-Satz
          if (is_object($RS) && $RS->NextRow()) {
              $MwSt_default_Satz = $RS->GetField("MwSt_Satz");
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Es wurde kein default MwSt-Satz gefunden -> Abbruch: getstandardmwstsatz</H2></P><BR>");
          }//End else
      }//End else
      return $MwSt_default_Satz;
  }//End getstandardmwstsatz

  // -----------------------------------------------------------------------
  // Diese Funktion liefert den MwSt-Prozentsatz, welcher in der Tabelle mehrwertsteuer
  // die Beschreibung 'Porto und Verpackung' hat. Das Resultat wird folgend interpretiert:
  // 1.) Es handelt sich um eine positive Fliesskommazahl -> Festsatz: Porto und Verpackung sollen zu diesem Satz versteuert werden
  // 2.) Es handelt sich um eine 0 -> Poro und Verpackung sollen MwSt-frei sein (0% MwSt)
  // 3.) Es handelt sich um eine -1 -> Porto und Verpackung sollen anteilsmaessig versteuert werden (z.B. zu 78% 7.6% und Rest zu 2.5%)
  // 4.) Es handelt sich um eine -2 -> Porto und Verpackung sollen zu dem MwSt-Satz versteuert werden, welcher den groessten Anteil der Rechnungssumme hat
  // Argumente: keine
  // Rueckgabewert: MwSt-Prozentsatz (Float) oder Abbruch via die-Funktion
  function getportoverpackungmwstsatz() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getportoverpackungmwstsatz_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getportoverpackungmwstsatz</H1></P><BR>");
      }
      else {
          //Auslesen der MwSt-Settings aus der mehrwertsteuer Tabelle
          $RS = $Database->Query($sql_getportoverpackungmwstsatz_1_1);
          $MwSt_porto_verpackung_Satz = -3.0; //Initialisierung mit einem ungueltigen MwSt-Satz
          if (is_object($RS) && $RS->NextRow()) {
              $MwSt_porto_verpackung_Satz = $RS->GetField("MwSt_Satz");
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Es wurde kein default MwSt-Satz gefunden -> Abbruch: getportoverpackungmwstsatz</H2></P><BR>");
          }//End else
      }//End else
      return $MwSt_porto_verpackung_Satz;
  }//End getportoverpackungmwstsatz

  // -----------------------------------------------------------------------
  // Rundet die in $betrag uebergebene Zahl auf 0.05 genau.
  // Argumente: Float (Zahl zum runden)
  // Rueckgabewert: Float (auf 0.05 gerundete Zahl)
  function runden_05($betrag) {

      // Damit die Funktion PHP-intern funktioniert, muss ein minimal kleiner Betrag dazuaddiert werden
      // (damit bie 0.05 aufgerundet wird. Sonst wird auf die naechste gerade Zahl gerundet)
      // Anmerkung: Da wir den Betrag (wenn auch sehr wenig) verandern, sollte man das Propagieren des Fehlers nicht ausser Acht lassen!
      $betrag = $betrag + 0.0000001;
      $betrag = $betrag * 20;
      $betrag = round($betrag);
      $betrag = $betrag / 20;
      return $betrag;
  }//End runden_05

  // -----------------------------------------------------------------------
  // Diese Funktion liefert true, wenn der Gesamtpreis einer Bestellung auf 0.05 gerundet werden soll, andernfalls false
  // Argumente: keine
  // Rueckgabewert: (Boolean) true, wenn gerundet werden soll
  function getgesamtpreisrunden() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getgesamtpreisrunden_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getgesamtpreisrunden</H1></P><BR>");
      }
      else {
          $runden = false; // Initialisierung
          $RS = $Database->Query($sql_getgesamtpreisrunden_1_1);
          if (is_object($RS) && $RS->NextRow()) {
              if ($RS->GetField("Gesamtpreis_runden") == "Y") {
                  $runden = true;
              }
              else {
                  $runden = false;
              }
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Abbruch: getgesamtpreisrunden</H2></P><BR>");
          }//End else
      }//End else
      return $runden;
  }//End getgesamtpreisrunden

  // -----------------------------------------------------------------------
  // Diese Funktion liefert -1 wenn ALLE Artikel einer Kategorie gleichzeitig angezeigt werden sollen, oder
  // den Integer Wert der gleichzeitig anzuzeigenden Artikel einer Kategorie, falls diese Anzahl eingeschraenkt wurde.
  // Argumente: keine
  // Rueckgabewert: (INT) Anzahl gleichzeitig anzuzeigender Artikel (-1 = alle)
  function getArtikelInkrement() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getArtikelInkrement_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getArtikelInkrement</H1></P><BR>");
      }
      else {
          $runden = false; // Initialisierung
          $RS = $Database->Query($sql_getArtikelInkrement_1_1);
          if (is_object($RS) && $RS->NextRow()) {
              $anzahl = $RS->GetField("ArtikelSuchInkrement");
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Abbruch: getArtikelInkrement</H2></P><BR>");
          }//End else
      }//End else
      return $anzahl;
  }//End getArtikelInkrement

  // -----------------------------------------------------------------------
  // Diese Funktion aktualisiert die Ablaufzeit der uebergebenen Session. Auf diese Weise
  // koennen Kunden 'Stunden' im Shop herumsurfen ohne ihren Warenkorb zu verlieren.
  // Argument: Session_ID (String)
  // Rueckgabewert: true bei Erfolg (sonst Abbruch durch die-Funktion)
  function extend_Session($Session_ID) {

      // Benoetigte Variablen aus anderen Modulen einbinden
      global $Database;
      global $sql_extend_Session_1_1;
      global $sql_extend_Session_1_2;
      global $sql_extend_Session_1_3;
      global $sql_extend_Session_1_4;
      global $sql_extend_Session_1_5;

      // Test ob man die Datenbank ansprechen kann (ob es ein Database-Objekt gibt - siehe database.php / initialize.php)
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: extend_Session</H1></P><BR>");
      }
      else {
          // Mit der folgenden Query wird die Ablaufzeit der Session (Bestellung) ausgelesen
          $RS = $Database->Query("$sql_extend_Session_1_1".$Session_ID."$sql_extend_Session_1_2");
          if (is_object($RS) && $RS->NextRow()) {
              $expired = $RS->GetField("expired");
              // Jetzige Zeit: (Ev. gibts spaeter mal Probleme bei dieser Funktion (UNIX-Zeit))
              $max_session_time = getmax_session_time();
              $now = time(); //Aktuelle Zeit
              $expired = time() + $max_session_time; // Neue Ablaufzeit berechnen (= Jetzt + Maximale Sessiondauer)
              // Sessionupdate in der Datenbank vornehmen
              $RS = $Database->Exec("$sql_extend_Session_1_3".$expired."$sql_extend_Session_1_4".$Session_ID."$sql_extend_Session_1_5");
              if (!$RS) {
                  echo "Now = $now, expired = $expired. Query =  $sql_extend_Session_1_3".$expired."$sql_extend_Session_1_4".$Session_ID."$sql_extend_Session_1_5";
                  die("<H1 class='content'>U_A_H_Error:extend_Session: Session Update</H1><BR>");
              }
          }
      }
      return true;
  }//End extend_Session

  // -----------------------------------------------------------------------
  // Liefert als einen String die in der Datenbank definierte Artikelsortieranzeige (z.B. a.Artikel_Nr DESC, a.Name ASC)
  // Diese Funktionalitaet wird im Moment NUR bei den beiten IDgetArtikeleinerKategorie[vonbis] Funktionen verwendet.
  // Argumente: keine
  // Rueckgabewert: Sortierbeschreibung als String
  function getSortierung() {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getSortierung_1;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getSortierung</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_getSortierung_1);
          if (is_object($RS) && $RS->NextRow()) {
              $Sortieren_nach = $RS->GetField("Sortieren_nach");  // Z.B. a.Preis oder a.Name, ...
              $Sortiermethode = $RS->GetField("Sortiermethode");  // Entweder ASC oder DESC
              // Auswertung der Artikelsortierreihenfolge:
              if ($Sortieren_nach != "a.Name") {
                 // Wenn nicht nach dem Artikelnamen sortiert wird, so soll immerhin als zweites Sortierkriterium
                 // der Name (immer ASC) mitbenutzt werden
                 $Sortieren_nach = $Sortieren_nach." ".$Sortiermethode.", a.Name ASC";
              }
              else {
                 $Sortieren_nach = $Sortieren_nach." ".$Sortiermethode;
              }
              return $Sortieren_nach;
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Artikelsortierung (getSortierung)</H1></P><BR>");
          }
      }
  }//End getSortierung

  // -----------------------------------------------------------------------
  // Liefert in einem Array Artikelobjekte zurueck, welche ABER NUR FOLGENDE ATTRIBUTE GESETZT HABEN:
  // Artikel_ID, Name, Artikel_Nr
  // Diese Funktionalitaet wird im Moment NUR im Adminbereich bei bestehender Artikel bearbeiten/loeschen benutzt.
  // Argumente: Suchstring (STRING)
  // Rueckgabewert: Array mit Artikelobjekten (Definition siehe artikel_def.php)
  function getArtikelauswahl($Suchstring) {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getArtikelauswahl_1_1;
      global $sql_getArtikelauswahl_1_2;
      global $sql_getArtikelauswahl_1_3;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getArtikelauswahl</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_getArtikelauswahl_1_1.$Suchstring.$sql_getArtikelauswahl_1_2.$Suchstring.$sql_getArtikelauswahl_1_3);
          $resultatarray = array(); // Initialisierung
          while (is_object($RS) && $RS->NextRow()){
              $tempArtikel = new Artikel();
              $tempArtikel->artikel_ID = $RS->GetField("Artikel_ID");
              $tempArtikel->name = $RS->GetField("Name");
              $tempArtikel->artikel_Nr = $RS->GetField("Artikel_Nr");
              $resultatarray[] = $tempArtikel; //ausgelesener Artikel in Array ablegen
          }//End while
      }
      return $resultatarray;
  }//End getArtikelauswahl

  // -----------------------------------------------------------------------
  // Liefert die formatierte Zahl (Preis) zurueck.
  // Argumente: Preis (FLOAT)
  // Rueckgabewert: Array mit obigen drei Elementen (ARRAY OF STRINGS)
  function getZahlenformat($Preis_unformatiert) {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_getZahlenformat_1;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.getZahlenformat</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_getZahlenformat_1);
          $Zahlenformat = array(); // Initialisierung
          if (is_object($RS) && $RS->NextRow()) {
              $Zahlenformat[0] = $RS->GetField("Zahl_thousend_sep");
              $Zahlenformat[1] = $RS->GetField("Zahl_decimal_sep");
              $Zahlenformat[2] = $RS->GetField("Zahl_nachkomma");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<P><H1 class='content'>U_A_H_Error: Fehler beim auslesen der Zahlenformatierung (getZahlenformat)</H1></P><BR>");
          }
          // Formatieren der Zahl vornehmen und Ergebnis zurueckgeben
          return number_format($Preis_unformatiert, $Zahlenformat[2], $Zahlenformat[1], $Zahlenformat[0]);
      }
  }//End getZahlenformat

  // -----------------------------------------------------------------------
  // Diese Funktion liefert die Version des verwendeten PhPepperShops
  // Argumente: keine
  // Rueckgabewert: Shopversion (STRING)
  function getshopversion() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getshopversion_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getshopversion</H1></P><BR>");
      }
      else {
          $version = "ungültig!"; // Initialisierung
          $RS = $Database->Query($sql_getshopversion_1_1);
          if (is_object($RS) && $RS->NextRow()) {
              $version = $RS->GetField("ShopVersion");
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Abbruch: getshopversion</H2></P><BR>");
          }//End else
      }//End else
      return $version;
  }//End getshopversion

  // -----------------------------------------------------------------------
  // Diese Funktion liefert bei Erfolg einen Array als Rueckgabewert.
  // Element 0 ist der Haendlermodus: 'Y', wenn der Shop sich im 'Haendlermodus' befindet, sonst 'N'.
  // Element 1 ist der Haendler_login_text -> Begruessungstext des Kunden beim Loginscreen
  // Wenn der Haendlermodus auf 'Y' steht, heisst das, dass sich jeder Shopkunde einloggen muss.
  // Argumente: keine
  // Rueckgabewert: 'Y', wenn Shop im Haendlermodus ist, 'N' wenn nicht (Char/String) (Abbruch bei Fehler)
  function getHaendlermodus() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_getHaendlermodus_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: getHaendlermodus</H1></P><BR>");
      }
      else {
          $Haendlermodus =array(); // Initialisierung (Aus Sicherheit = true)
          $RS = $Database->Query($sql_getHaendlermodus_1_1);
          if (is_object($RS) && $RS->NextRow()) {
              $Haendlermodus[0] = $RS->GetField("Haendlermodus");
              $Haendlermodus[1] = $RS->GetField("Haendler_login_text");
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Abbruch: getHaendlermodus (<b>Query:</b> $sql_getHaendlermodus_1_1)</H2></P><BR>");
          }//End else
      }//End else
      return $Haendlermodus;
  }//End getHaendlermodus

  // -----------------------------------------------------------------------
  // Diese Funktion liefert den absoluten Systempfad zum Shoproot (dort wo index.php liegt)
  // Der Pfad wurde bei uns jeweils OHNE trailing Slash geliefert (ohne / am Ende).
  // Wenn das optionale URI-Flag = true ist, so wird anstatt der UNIX-Dateipfad bis zum Shoproot
  // der URI bis zum Shoproot zurueck gegeben.
  // *** ACHTUNG: Diese Funktion kann ev. noch fehlerhaft arbeiten ***
  // Argumente: URI-Flag (Boolean)
  // Rueckgabewert: Pfad (String)
  function getShopRootPath($URI=false) {

      // Globaler Array einbinden
      global $HTTP_SERVER_VARS;

      if ($URI == false) {
          // UNIX-Dateipfad zurueckgeben
          $result = $HTTP_SERVER_VARS['PATH_TRANSLATED'];
          if ($result == "") {
              $result = $_SERVER['PATH_TRANSLATED'];
          }
          if ($result == "") {
              // Es liegt wahrscheinlich eine CGI-PHP Umgebung vor. Hier ist die Variable PATH_TRANSLATED
              // schlicht nicht vorhanden, wir muessen stattdessen den Pfad via $_SERVER['SCRIPT_FILENAME']
              // erarbeiten.
              $result = $HTTP_SERVER_VARS["SCRIPT_FILENAME"];
              if ($result == "") {
                  $result = $_SERVER["SCRIPT_FILENAME"];
              }
              if ($result == "") {
                  die('U_A_H_Error: getShopRootPath: Der absolute Pfad zum Shop kann nicht ermittelt werden');
              }
          }
          // Dateiname abschneiden
          if (substr($result, -1) != "/") {
              $result = preg_replace('/\/[^\/]*$/','',$result);
          }
      }
      else {
          // Herausfinden, ob SSL verwendet wird, wenn ja, https:// als Schema benutzen, sonst http://
          $https_test = $_SERVER['HTTPS'];
          if (!empty($https_test)) {
              $https_test = $HTTP_SERVER_VARS['HTTPS'];
          }
          $url_schema = (!empty($https_test) ? 'https' : 'http').'://';

          // Kompletter URI bis zum Shoproot zurueckgeben
          $result = $url_schema.$HTTP_SERVER_VARS['SERVER_NAME'].$HTTP_SERVER_VARS['REQUEST_URI'];
          if ($result == $url_schema || $result == "http://") {
              $result = $url_schema.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
          }
          if ($result == $url_schema || $result == "http://") {
              die('U_A_H_Error: getShopRootPath: Der URI zum Shop kann nicht ermittelt werden'."($result)");
          }
          // Dateiname abschneiden
          if (substr($result, -1) != "/") {
              $result = preg_replace('/\/[^\/]*$/','',$result);
          }
      }
      return $result;
  }//End getShopRootPath

  // -----------------------------------------------------------------------
  // Diese Funktion liefert true, wenn die Tell-a-Friend Funktionalitaet verwendet werden soll
  // oder false, wenn nicht.
  // Argumente: keine
  // Rueckgabewert: true | false (Boolean)
  function get_tell_a_friend() {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Database;
      global $sql_get_tell_a_friend_1_1;

      $result = false; // Initialisierung des Rueckgabewerts

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<P><H1 class='content'>U_A_H_Error: Datenbank nicht erreichbar: get_tell_a_friend</H1></P><BR>");
      }
      else {
          $RS = $Database->Query($sql_get_tell_a_friend_1_1);
          if (is_object($RS) && $RS->NextRow()) {
              if ($RS->GetField("tell_a_friend") == 'Y') {
                  $result = true;
              }
          }
          else {
              die("<P><H2 class='content'>U_A_H_Error: Abbruch: get_tell_a_friend (<b>Query:</b> $sql_get_tell_a_friend_1_1)</H2></P><BR>");
          }
      }//End else

      return $result;
  }//End get_tell_a_friend

  // -----------------------------------------------------------------------
  // Liefert true (Boolean), wenn die automatische Landeserkennung fuer Shopkunden eingeschaltet ist
  // oder false (Boolean), wenn diese abgeschaltet ist. Die Einstellung wird in den Allgemeinen Shopsettings
  // konfiguriert.
  // Argumente: keine
  // Rueckgabewert: true | false (Boolean)
  function get_check_user_country() {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_get_check_user_country_1;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<p><h1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.get_check_user_country</h1></p><br></body></html>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_get_check_user_country_1);
          if (is_object($RS) && $RS->NextRow()) {
              $check = $RS->GetField("check_user_country");
              if (strtolower($check) == 'n') {
                  return false;
              }
              else {
                  return true;
              }
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<p><h1 class='content'>U_A_H_Error: Fehler beim auslesen der automatischen Landeserkennung</h1></p><br></body></html>");
          }
      }//End else
  }//End get_check_user_country


  // -----------------------------------------------------------------------
  // Liefert die E-Mail Adresse, so eine angegeben wurde um via Bcc die via Tell-A-Friend versendeten E-Mails
  // als Kopie zugeschickt zu bekommen. Wenn keine E-Mailadresse definiert ist wird false (Boolean) zurueck-
  // gegeben
  // Argumente: keine
  // Rueckgabewert: E-Mail Adresse des Bcc-Kontos (String) oder false (Boolean)
  function get_tell_a_friend_bcc() {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_get_tell_a_friend_bcc_1;
      // Test ob Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<p><h1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.get_tell_a_friend_bcc</h1></p><br></body></html>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql_get_tell_a_friend_bcc_1);
          if (is_object($RS) && $RS->NextRow()) {
              $bcc = ""; // Initialisierung
              $bcc = $RS->GetField("tell_a_friend_bcc");
              if ($bcc == "") {
                  return false;
              }
              else {
                  return $bcc;
              }
          }
          else {
              //Script mit einer (fuer User geeignete) Fehlermeldung beenden (Vermerk auf Bcc wuerde keinen Guten Eindruck machen...)
              die("<p><h1 class='content'>U_A_H_Error: Fehler beim auslesen der Tell-A-Friend Konfiguration</h1></p><br></body></html>");
          }
      }//End else
  }//End get_tell_a_friend_bcc

  // -----------------------------------------------------------------------
  // Ausgabe der Artikel-Blaettern angaben in eine Variable, falls die Anzahl gleichzeitig anzuzeigender Artikel einer Kategorie eingeschraenkt wurde und
  // es noch mehr als die hier schon angezeigten Artikel gibt. (Infos wurden ganz zu Beginn von U_A_H_A, darstellen == 1 in die Variable $blaetterninfo gespeichert)
  // $blaetterninfo enthaelt einen Artikel, in welchem aber andere Daten gespeichert wurden (def. siehe USER_ARTIKEL_HANDLING.php, IDgetArtikeleinerKategorievonbis(...))
  // Wenn in dieser Kategorie nicht geblaettert werden muss, so wird als Antwort der boolsche Wert false zurueckgegeben.
  // Semantik der $blaetterninfo-Membervariablen:
  // $blaetterninfo->artikel_ID   = Kategorie-ID der Kategorie der momentan angezeigten Artikel
  // $blaetterninfo->preis        = ab Artikel-ID anzeigen
  // $blaetterninfo->aktionspreis = Anzahl gleichzeitig anzuzeigender Artikel
  // $blaetterninfo->gewicht      = Totale Anzahl Artikel dieser Kategorie
  // Argumente: $blaetterninfo (Artikel-Objekt), $close_html (Boolean)
  // Rueckgabewert: false (Boolean), wenn es nichts zu blaettern gibt oder Variable, welche Blaetternanzeige als String beinhaltet (String)
  function artikel_blaettern_anzeige($blaetterninfo) {
      $ausgabe = ""; // Initialisierung des Rueckgabewerts
      $anzahl_total = $blaetterninfo->gewicht;
      $inkrement = $blaetterninfo->aktionspreis;
      // Wenn kein oder ein Artikel in der Kategorie enthalten ist, kann auch nicht geblaettert werden
      if ($anzahl_total <= 1) {
          return false;
      }
      $anzahl_inkremente = ($anzahl_total / $inkrement); // Ganzzahldivision damit man weiss, wieviele Seiten es zum blaettern geben wird
      // Wenn es nichts zu blaettern gibt (weil alle Artikel im ersten Inkrement Platz haben, hier abbrechen)
      if ($anzahl_inkremente <= 1) {
          return false;
      }
      // Wenn es aber etwas zu blaettern gibt...
      $anzeigen_ab = $blaetterninfo->preis;
      $Kategorie_ID = $blaetterninfo->artikel_ID;
      $link1 = "";
      $link2 = "</a>\n";
      $aktuelles_inkrement = 0; // Zaehler der in jeder schleife um das Inkrement erhoeht wird
      $blaetternausgabe = ""; // Initialisierung - Hier kommt die Blaettern-Navigation rein
      for ($i = 0; $i < $anzahl_inkremente; $i++) {
          if ($aktuelles_inkrement == $anzeigen_ab) {
              $blaetternausgabe.="<a class=\"content\" $cssargaktiv href=\"USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&Kategorie_ID=$Kategorie_ID&anzeigen_ab=$aktuelles_inkrement&anzahlartikelgleichzeitig=$anzahlartikelgleichzeitig&anzahl_total=$anzahl_total\"><b>".($i+1)."</b></a>&nbsp;&nbsp;";
              $bin_jetzt_hier_seite = $i+1; // Momentan aktuelle Blaettern-Seite
              $bin_jetzt_hier_inkrement = $aktuelles_inkrement;
          }
          else {
              $blaetternausgabe.="<a class=\"content\" $cssargumente href=\"USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&Kategorie_ID=$Kategorie_ID&anzeigen_ab=$aktuelles_inkrement&anzahlartikelgleichzeitig=$anzahlartikelgleichzeitig&anzahl_total=$anzahl_total\">".($i+1)."</a>&nbsp;&nbsp;";
          }
          $aktuelles_inkrement = $aktuelles_inkrement + $inkrement;
      }// End for

      // Wir haben jetzt alle benoetigten Navigationsdaten. Wir koennen jetzt noch die inkrementuebergreifenden Angaben
      // zusammenstellen und noch zur Blaetternausgabe hinzufuegen. Dann ist die Ausgabe fertig und kann angezeigt werden.
      $blaetterntext.="Es hat $i Seiten mit je $inkrement Artikeln in dieser Kategorie.<br>";
      $blaetternzurueck = "";
      $blaetternweiter = "";

      $bin_jetzt_hier_inkrement_zurueck = 0; // Initialisierung
      if (($bin_jetzt_hier_inkrement - $inkrement) >= 0) { // Zurueck nur dann anzeigen, wenn es NICHT das erste Element ist
          $bin_jetzt_hier_inkrement_zurueck = $bin_jetzt_hier_inkrement - $inkrement;
          $blaetternzurueck = "<a class=\"content\" $cssargumente href=\"USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&Kategorie_ID=$Kategorie_ID&anzeigen_ab=".$bin_jetzt_hier_inkrement_zurueck."&anzahlartikelgleichzeitig=$anzahlartikelgleichzeitig&anzahl_total=$anzahl_total\">zur&uuml;ck</a>&nbsp;&nbsp;";
      }
      $bin_jetzt_hier_inkrement_weiter = $aktuelles_inkrement - $inkrement; // Initialisierung
      if (($bin_jetzt_hier_inkrement + $inkrement) < $aktuelles_inkrement) { // Weiter nur dann anzeigen, wenn es NICHT das letzte Element ist
          $bin_jetzt_hier_inkrement_weiter = $bin_jetzt_hier_inkrement + $inkrement;
          $blaetternweiter = "<a class=\"content\" $cssargumente href=\"USER_ARTIKEL_HANDLING_AUFRUF.php?darstellen=1&Kategorie_ID=$Kategorie_ID&anzeigen_ab=".$bin_jetzt_hier_inkrement_weiter."&anzahlartikelgleichzeitig=$anzahlartikelgleichzeitig&anzahl_total=$anzahl_total\">weiter</a>";
      }
      $ausgabe .= "<center>".$blaetterntext.$blaetternzurueck.$blaetternausgabe.$blaetternweiter."</center>"; // Ausgabe der Navigationszeile
      return $ausgabe;
  }//End artikel_blaettern_anzeige

  // ----------------------------------------------------------
  // Funktion:  send_error_mail
  // Zweck:     Folgende Funktion kann aufgerufen werden, um dem Shop-Administrator ueber ein
  //            Fehler-Ereignis zu benachrichtigen. In der Variable $error_msg kann eine Nachricht
  //            uebergeben werden. Wenn das optionale Argument $quiet=false gesetzt wird kann im Fall,
  //            dass der Administrator nicht benachrichtigt werden konnte eine Fehlermeldung ausgegeben werden
  // Argumente: $error_msg (String), $quiet(false, optional, default = true)
  // Rueckgabe: true|false (Boolean), oder Abbruch via die()-Funktion
  function send_error_mail($error_msg, $quiet=true) {
        // Benoetigte Variablen vorbereiten
        $shopnamen = getshopname();
        $email = getShopEmail();

        if ($error_msg == "") {
            $message = "An Administrator von ".$shopnamen." von der automatischen Fehlerbenachrichtigung des $shopnamen Shopsystems:\n\n";
            $message.= "Ein nicht näher spezifizierter Fehler ist im Shopsystem von $shopnamen aufgetreten.\n";
            $message.= "Es wurde die Funktion send_error_mail() aufgerufen aber keine nähere\n";
            $message.= "Beschreibung des Fehlers mitgeliefert.\n\n";
            $message.= "Automatische Fehlerbenachrichtigung $shopnamen Shopsystem";
        }
        else {
            $message = "An Administrator von $shopnamen von der automatische Fehlerbenachrichtigung des $shopnamen Shopsystems:\n\n";
            $message.= "Ein Fehler im Shopsystem von $shopnamen ist aufgetreten!\n\nFehlerbeschreibung:\n";
            $message = $error_msg."\n\n";
            $message.= "Automatische Fehlerbenachrichtigung $shopnamen Shopsystem";
        }
        $to=$email;
        $subject="Fehler auf - $shopnamen - DRINGEND!";
        $header="From: $shopnamen Shopsystem <$email>";
        // notwendig, damit deutsche Umlaute richtig angezeigt werden
        $header.="\nContent-Type: text/plain; charset=iso-8859-1";
        if (!mail ($to, $subject, $message, $header) && !$quiet){
            echo "<h2>Administrator konnte ueber einen Fehler nicht benachrichtigt werden ($email)!</h2><br><br>\n";
        }
  }// End function send_error_mail

  // -----------------------------------------------------------------------
  // Diese Funktion liefert eine Konfigurationseinstellung, welche in der Tabelle
  // shop_settings_new gespeichert wurde. Wenn man Shopsettings abrufen will, wird
  // ausserdem die Zugriffssecurity ueberprueft. Nur wenn die Datei $ADMIN_SQL_BEFEHLE
  // included ist, wird ein SQL verwendet, welches alle Settings unabhaengig vom
  // Attribut security (admin, user) anzeigt, ansonsten werden nur user-Settings angezeigt.
  // Als Abfrage muss man die Gruppe der Einstellung und den Namen der abzufragenden Ein-
  // stellung angeben. Wenn man aber alle Einstellungen einer Gruppe haben moechte, so kann
  // man als Namen einen Stern mituebergeben. Als Rueckgabewert wird dann der ausgelesene
  // Wert (oder die Werte) in einem Array zurueckgegeben. Wenn keine Einstellung mit
  // Name-Gruppe gefunden wurde, gibt die Funktion false (Boolean) zurueck.
  // Der Aufbau des Rueckgabearrays sieht wie folgt aus: Key:Name, Value:Wert.
  // Argumente: $name (String), $gruppe (String)
  // Rueckgabewert: Wert der Einstellung(en) (assoz. Array) oder false (Boolean)
  function get_new_shop_setting($name, $gruppe) {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $ADMIN_SQL_BEFEHLE;
      // Wenn wir Administratoren sind, diese SQLs einbinden, sonst USER-SQLs einbinden
      // Anm. Wenn hier jemand was vorfaked koennen die richtigen SQLs nicht eingebunden werden,
      // weil diese geschuetzt sind. (Schreibzugruff sollte eh nur der Admin-User haben)
      if (isset($ADMIN_SQL_BEFEHLE)) {
          global $sql_get_new_shop_setting_1_1_admin;
          global $sql_get_new_shop_setting_1_2_admin;
          global $sql_get_new_shop_setting_1_3_admin;
          // Test, ob ALLE Einstellungen einer Gruppe uebergeben werden sollen (Wenn name = *)
          if ($name == "*") {
              $sql = $sql_get_new_shop_setting_1_1_admin.$gruppe.$sql_get_new_shop_setting_1_3_admin;
          }
          else {
              $sql = $sql_get_new_shop_setting_1_1_admin.$gruppe.$sql_get_new_shop_setting_1_2_admin.$name.$sql_get_new_shop_setting_1_3_admin;
          }
      }
      else {
          global $sql_get_new_shop_setting_1_1_user;
          global $sql_get_new_shop_setting_1_2_user;
          global $sql_get_new_shop_setting_1_3_user;
          // Test, ob ALLE Einstellungen einer Gruppe uebergeben werden sollen (Wenn name = *)
          if ($name == "*") {
              $sql = $sql_get_new_shop_setting_1_1_user.$gruppe.$sql_get_new_shop_setting_1_3_user;
          }
          else {
              $sql = $sql_get_new_shop_setting_1_1_user.$gruppe.$sql_get_new_shop_setting_1_2_user.$name.$sql_get_new_shop_setting_1_3_user;
          }
      }

      // Initialisierung
      $wert = false; // Rueckgabewert
      $anzahl = 0;   // Anzahl gefundene Eintraege -> sollte = 1 sein.

      // Test ob Datenbank erreichbar ist
      if (!is_object($Database)) {
          // Fehlermeldung ausgeben, da die Datenbank nicht erreichbar ist
          die("<p><h1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.get_new_shop_setting</h1></p><br></body></html>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql);
          $anzahl = $RS->GetRecordCount();
          if ($anzahl == 0) {
              $wert = false;
          }
          elseif (is_object($RS)) {
              // Auslesen der Werte und abfuellen in den assoziativen Rueckgabearray $wert
              $wert = array();
              while ($RS->NextRow()) {
                  $key_1 = $RS->GetField('name');
                  $key_2 = $RS->GetField('gruppe');
                  // Da ja max. der Inhalt von nur EINER Gruppe angzezeigt wird, wird diese Einstellung nicht benoetigt
                  // $wert[$key_1."_".$key_2] = $RS->GetField('wert');
                  $wert[$key_1] = $RS->GetField('wert');

              }// End while
          }
          else {
              //Script mit einer (fuer User geeignete) Fehlermeldung beenden
              die("<p><h1 class='content'>U_A_H_Error: Fehler beim auslesen einer Shop-Einstellung</h1></p><br></body></html>");
          }
      }//End else

      return $wert;
  }//End get_new_shop_setting

  // -----------------------------------------------------------------------
  // Diese Funktion versucht(!!!) eine ISO-4217 konforme Darstellung der Waerhung
  // zurueckzugeben. Dies funktioniert erst einigermassen zuverlaessig fuer CHF, EUR, USD, GBP
  // Eine Liste mit den entsprechenden Namen findet man hier: http://www.xe.com/iso4217.htm
  // Um eine Fremdwaehrung zu benutzen empfehlen wir die Fremdwaehrung shopweit gleich als
  // ISO-4217 konform anzugeben und dort, wo diese Funktion verwendet wird den $override auf
  // true zu setzen.
  // Argumente: $waehrung (String), $override (Boolean, optional, default=false)
  // Rueckgabewert: ISO-4217 konforme Waehrungsdarstellung (String)
  function get_iso4217_waehrung($waehrung, $override=false) {
    if ($override == true) {
        return $waehrung;
    }
    switch (trim(substr(strtolower(trim($waehrung)),0,3))) {
        case "sfr":
        case "chf";
        case "fr";
        case "fr.";
        case "fra";
        case "rp";
        case "rp.";
            $waehrung = "CHF";
            break;
        case "eur";
        case "&eu";
        case "€"; // Alt+0128
        case "e";
        case "ecu";
            $waehrung = "EUR";
            break;
        case "\$";
        case "us\$";
        case "usd";
            $waehrung = "USD";
            break;
        case "£";
        case "GBP";
            $waehrung = "GBP";
            break;
        default:  // Defaultwaehrung ist EUR
            $error_msg.= "Die Funktion get_iso4217_waehrung(...) konnte keine ISO-4217 Währungskonvertierung\n";
            $error_msg.= "der von ihnen angegebenen Währung [$waehrung] vornehmen. Bitte passen Sie die Währung an!\n\n";
            $error_msg.= "Als zu verwendende Währung wurde EUR definiert!\n\n";
            $error_msg.= "Der Fehler kam in der Datei ".__FILE__." vor (Zeile ".__LINE__.").\n\n";
            $error_msg.= "Bei Fragen, wenden Sie sich bitte an das Forum auf http://www.phpeppershop.com/\n\n";
            send_error_mail($error_msg);
            $waehrung = "EUR";
    }// End switch
    return $waehrung;
  }//End get_new_shop_setting

  // -----------------------------------------------------------------------
  // Liefert bei einem angegebenen Variantennamen den Namen der Variationsgruppe
  // zurueck. Dies kann hilfreich sein, wenn man z.B. von einer Variation eines
  // Artikels nur den Namen der Variation weiss, aber man trotzdem auf den Namen
  // der uebergeordneten Variationsgruppe schliessen will.
  // Da die Variationsnamen nicht eindeutig sind, kann man optional auch noch die
  // Artikel-ID mit angeben. Auf diese Weise kann man den String einschraenken.
  // Wenn keine Artikel-ID mitgegeben wird, so kann es sein, dass die 'falsche'
  // Variationsgruppe zurueckgegeben wird, weil der Variationsname nicht eindeutig
  // ist - und er ev. in versch. Variationsgruppen verwendet worden ist.
  // Argumente:     $variationsname (String)
  // optional:      $artikel_id (Integer, default = 0)
  // Rueckgabewert: $variationsgruppenname (String) [kann auch ein Leerstring sein]
  function get_var_grp_name($variationsname, $artikel_id=0) {

      // Einlesen von in anderen Modulen definierten Variablen
      global $Database;
      global $sql_get_var_grp_name_1_1;
      global $sql_get_var_grp_name_1_2;
      global $sql_get_var_grp_name_1_3;

      // Initialisierung
      $var_grp_name = "";

      // Test ob Datenbank erreichbar ist, sonst Abbruch mit Fehlermeldung
      if (! is_object($Database)) {
          die("<p><h1 class='content'>U_A_H_Error: Datenbank nicht erreichbar.get_var_grp_name</h1></p><br></body></html>");
      }
      else {
          // SQL-Statement zusammenstellen
          $sql = $sql_get_var_grp_name_1_1.$variationsname.$sql_get_var_grp_name_1_2;
          if ($artikel_id > 0) {
              $sql.= $sql_get_var_grp_name_1_3.$artikel_id;
          }

          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Database->Query($sql);
          if (is_object($RS) && $RS->NextRow()) {
              $var_grp_name = $RS->GetField("Gruppentext");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              die("<p><h1 class='content'>U_A_H_Error: Fehler beim auslesen des Variationsgruppennamens (get_var_grp_name)</h1></p><br></body></html>");
          }
      }//End else

      // Rueckgabe des gefundenen Variationsgruppennamens
      return $var_grp_name;
  }// End function get_var_grp_name


  // -----------------------------------------------------------------------
  // Liefert die Kategorie-ID der Kategorie Nichtzugeordnet
  // Argumente:     keine
  // optional:      keine
  // Rueckgabewert: Kategorie-ID oder false

  function get_kat_id_nichtzugeordnet() {

      // Einbinden von in anderen Modulen deklarierten Variablen
      global $Database;
      global $get_kat_id_nichtzugeordnet_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Database)) {
          die("<p><h1 class='content'>U_A_H_Error: Datenbank nicht erreichbar (get_kat_id_nichtzugeordnet)</h1></p></body></html>\n");
      }
      else {
          // Initialisierungen
          $Nichtzugeordnet_ID = 0;

          // 1.) Auslesen der Kategorie-ID der Kategorie mit der Bezeichnung (Name) Nichtzugeordnet
          $sql = $get_kat_id_nichtzugeordnet_1_1;
          $RS = $Database->Query($sql);
          if (is_object($RS) && $RS->NextRow()) {
              return $RS->GetField("Kategorie_ID");
          }
          else {
              die("<p><h1 class='content'>U_A_H_Error: Konnte Kategorie_ID der Kategorie Nichtzugeordnet nicht auslesen (get_kat_id_nichtzugeordnet)</h1></p></body></html>\n");
          }
      } // end of else
  } // end of function get_kat_id_nichtzugeordnet



  // End of file-----------------------------------------------------------------------
?>
