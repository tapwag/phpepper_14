<?php
  // Filename: SHOP_ADMINISTRATION.php
  //
  // Modul: PHP-Funktionen - SHOP_ADMINISTRATION
  //
  // Autoren: José Fontanil & Reto Glanzmann
  //
  // Zweck: Beinhaltet alle Funktionen um den Shop zu administrieren
  //
  // Sicherheitsstatus:        *** ADMIN ***
  //
  // Version: 1.4
  //
  // CVS-Version / Datum: $Id: SHOP_ADMINISTRATION.php,v 1.78 2003/08/06 16:29:58 fontajos Exp $
  //
  // -----------------------------------------------------------------------
  // Damit jedes andere Modul ueberpruefen kann ob dieses hier schon "included" ist
  // wird folgende Vairable auf true gesetzt (Name = Name des Moduls ohne .php)
  $SHOP_ADMINISTRATION = true;

  // include Pfad anpassen. Dabei werden die unterschiedlichen Delimiter-Zeichen fuer
  // Windows und UNIX/Linux beruecksichtigt. Danke fuer die Idee an Eduard Mas Walgram.
  // Windows --> Delimiter = Strichpunkt | UNIX/Linux --> Delimiter = Doppelpunkt
  if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
  ini_set("include_path", "./$pd../$pd../../$pd../Frameset$pd./shop/Admin$pd./Admin$pd../Admin$pd/usr/local/lib/php");

  // PHP 5.3 Timezone setzen
  @date_default_timezone_set(@date_default_timezone_get());

  // Einbinden der benoetigten Module (PHP-Scripts)
  // Bei Unklarheiten, siehe include-Hierarchie in der Dokumentation
  if (!isset($ADMIN_Database)) {include("ADMIN_initialize.php");}
  if (!isset($ADMIN_SQL_BEFEHLE)) {include("ADMIN_SQL_BEFEHLE.php");}
  if (!isset($SHOP_ADMINISTRATION_ARTIKEL)){include("SHOP_ADMINISTRATION_ARTIKEL.php");}
  if (!isset($USER_ARTIKEL_HANDLING)){include("USER_ARTIKEL_HANDLING.php");}

  // -----------------------------------------------------------------------
  // Diese Funktion fuegt einen neuen Artikel ohne Bild in die Datenbank ein
  // Sie wird von dem Modul bild_up.php aufgerufen.
  // Argumente: "In Einzelteile zerlegter Artikel, vom Formular her"
  //   Damit mehrere Kategorien pro Artikel ermoeglicht werden koennen, werden
  //   die Kategorie_IDs in einem Array uebergeben. Eine weitere Spezialitaet ist
  //   das Flag $kat_save. Nur wenn dieses = true (1) ist, wird der neue Artikel auch
  //   in die angegebenen Kategorien eingetragen (siehe auch updArtikel_2 info)
  // Als Rueckgabewert liefert diese Funktion die Artikel_ID des neu eingefuegten
  //   Artikels. (Artikel_ID wurde per Auto-Increment Funktion erzeugt (MySQL)
  //   Hier sei noch erwaehnt, dass der DB-Wrapper (database.php) nur fuer MySQL diese
  //   Funktionalitaet bietet. Bei den anderen Datenbanken, auch Sybase, muss das noch
  //   nachprogrammiert werden)
  function newArtikel($Kategorie_IDarray, $Artikel_Nr, $Name, $Beschreibung, $letzteAenderung,
           $Preis, $Aktionspreis, $Gewicht, $MwSt, $Link, $Optionenarray, $Variationsarray, $Variationsgruppenarray, $Gruppentext,
           $Gruppe_darstellen, $Eingabefeld_text, $Eingabefeld_param, $Gewicht_Opt, $Gewicht_Var, $kat_save) {

      //Einbinden der globalen Variablen aus anderen PHP-Modulen (-Scripts)
      global $Admin_Database;
      global $sql_newArtikel_1_1;
      global $sql_newArtikel_1_2;
      global $sql_newArtikel_1_3;
      global $sql_newArtikel_1_4;
      global $sql_newArtikel_1_5;
      global $sql_newArtikel_1_6;
      global $sql_newArtikel_1_7;
      global $sql_newArtikel_1_8;
      global $sql_newArtikel_1_9;
      global $sql_newArtikel_2_0;
      global $sql_newArtikel_2_1;
      global $sql_newArtikel_3_1;
      global $sql_newArtikel_3_2;

      // Test ob die Datenbank erreichbar ist, sonst Abbruch
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar: newArtikel</H1></P>\n");
      }
      else {
          // Test ob das Kategorien-Update Flag $kat_save gesetzt ist:
          if (!isset($kat_save)) {
              die("<P><H1>S_A_Error: Die Pruefung isset(kat_save) hat ein negatives Resultat ergeben! Funktion: updArtikel_2</H1></P>\n");
          }

          // Nun wird der neue Artikel ($Artikelobjekt) in die Datenbank eingefuegt:

          // Teil 1 von 5: Artikel in artikel-Tabelle eintragen
          // Dies generiert einen INSERT der dank der erweiterten MySQL-Exec-Funktion
          // des database.php (DB-Wrapper) den aktuell zugewiesenen Auto-Wert
          // (hier Artikel-ID) zurueck liefert.
          $sql_exec = "$sql_newArtikel_1_1 '$Artikel_Nr', '$Name', '$Beschreibung',
                                '$letzteAenderung', '$Preis', '$Aktionspreis', '$Gewicht', '$MwSt',
                                '$Link', '$Eingabefeld_text', '$Eingabefeld_param' $sql_newArtikel_1_2";
          if (!($Artikel_ID = $Admin_Database->Exec($sql_exec))) {
              echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: nA_1</H1></P>\n");
              echo $sql_exec."<BR>";
              die ("Artikel-ID ist: $Artikel_ID<BR>");
          }


          // Teil 2 von 5: Artikel-Optionen in artikel_optionen-Tabelle eintragen
          //for ($i=1;$i <= count($Optionenarray);$i++){
          $opt_count = 1;
          foreach($Optionenarray as $Option => $Preis){
              $Optionsgewicht = $Gewicht_Opt[$Option];
              $sql_exec = "$sql_newArtikel_1_3 '$opt_count', '".$Option."',
                          '".$Preis."', '$Artikel_ID', '$Optionsgewicht' $sql_newArtikel_1_4";
              // Nur Optionen einfuegen, die auch einen Ihnalt haben..
              if ($Option != ""){
                  if (!$Admin_Database->Exec($sql_exec)) {
                      echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: nA_2</H1></P>\n");
                      echo $sql_exec."<BR>";
                      die ("INSERT fehlgeschlagen! artikel-Tabelle schon geschrieben");
                  } // end of if
              $opt_count++;
              } // end of if
              //next($Optionenarray);
          } // end of for


          // Teil 3 von 5: Artikel-Variationen in artikel_variationen-Tabelle eintragen
          $var_count = 1;
          foreach($Variationsarray as $Variation => $Preis){
              $Variationsgewicht = $Gewicht_Var[$Variation];
              $sql_exec = "$sql_newArtikel_1_5 '$var_count', '".$Variation."',
                            '".$Preis."', $Artikel_ID, ".$Variationsgruppenarray[$Variation].", '$Variationsgewicht' $sql_newArtikel_1_6";
              // Nur Variationen einfuegen, die auch einen Ihnalt haben..
              if ($Variation != ""){
                  if (!$Admin_Database->Exec($sql_exec)) {
                      echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: nA_3</H1></P>\n");
                      echo $sql_exec."<br>";
                      die ("Insert fehlgeschlagen! artikel, artikel_optionen-Tabellen schon geschrieben");
                  } // end of if
              $var_count++;
              } // end of if
          } // end of for


          // Teil 4 von 5: Variatinsgruppen in Tabelle artikel_variationsgruppen speichern
          $count = 1;
          foreach ($Gruppe_darstellen as $darstellen){
              // nur die Variationsgruppen abspeichern, die auch einen Bezeichnungstext haben
              if ($Gruppentext[$count] != ""){
                  if (!$Admin_Database->Exec("$sql_newArtikel_2_0 $Artikel_ID, $count, '".urldecode($Gruppentext[$count])."',
                                        '".urldecode($darstellen)."' $sql_newArtikel_2_1")) {
                      echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: nA_4</H1></P>\n");
                      echo "$sql_newArtikel_2_0 $Artikel_ID, $count, '".urldecode($Gruppentext[$count])."',
                                        '".urldecode($darstellen)."' $sql_newArtikel_2_1";
                      die ("Insert fehlgeschlagen! artikel, artikel_optionen, artikel_variationen-Tabellen schon geschrieben");
                  } // end of if
              } // end of if
              $count++;
          } // end of foreach

          // Nur wenn $kat_save = true ist, wird der neue Artikel auch in Kategorien eingetragen
          // sonst werden nur seine Artikeldaten ohne Bildinformationen gespeichert
          if ($kat_save) {
              // Teil 5 von 5: Den Artikel in der entsprechenden Kategorie ablegen

              if (count($Kategorie_IDarray) == 0) {
                  // Da der Artikel keiner Kategorie zugeordnet wird, soll er in die Kategorie Nichtzugeordnet
                  // abgelegt werden. Wir holen also die Kateogie_ID der Kategorie Nichtzugeordnet und fuellen
                  // diese ID in den Kategorie_IDarray:
                  $RS = $Admin_Database->Query("$sql_newArtikel_1_9");
                  if ($RS && $RS->NextRow()){
                      $Kategorie_IDarray[] = $RS->GetField("Kategorie_ID");
                  }
                  else {
                      echo "Query: $sql_newArtikel_1_9<BR>";
                      die("<B><H1>S_A_Error: Fehler beim Einf&uuml;gen eines neuen Artikels (newArtikel Teil 4 von 4)</B></H1><BR>");
                  }
              }


              foreach($Kategorie_IDarray as $key=>$Kategorie_ID) {
                  if (!$Admin_Database->Exec("$sql_newArtikel_1_7 $Artikel_ID, $Kategorie_ID $sql_newArtikel_1_8")) {
                      echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: nA_4</H1></P>\n");
                      echo "Query: $sql_newArtikel_1_7 $Artikel_ID, $Kategorie_ID $sql_newArtikel_1_8 <BR>";
                      echo "korrespondierende Kategorie-ID: $Kategorie_ID <BR>";
                      die ("Insert fehlgeschlagen! artikel-, -_optionen, -_variationen schon geschrieben, wahrscheinlich wurde vergessen eine Kategorie anzugeben!");
                  }
              }// End foreach
              // Neue Artikel-ID (>1) zurueckgeben (entspricht der Artikel_ID), wenn das Einfuegen erfolgreich war:
          }// End $kat_save
          return $Artikel_ID;
      }//End else
  }//End newArtikel

  // -----------------------------------------------------------------------------------------------
  // Einen Artikel aus der Datenbank entfernen (loeschen)
  // Argument: Artikel_ID
  // Rueckgabewert: 1 (entspricht true) bei Erfolg, sonst Abbruch und Fehlermeldung per die-Funktion
  function delArtikel($Artikel_ID) {

      //Einbinden der globalen Variablen aus anderen PHP-Modulen (-Scripts)
      global $Admin_Database;
      global $sql_delArtikel_1_1;
      global $sql_delArtikel_1_2;
      global $sql_delArtikel_2_1;
      global $sql_delArtikel_2_2;
      global $sql_delArtikel_3_1;
      global $sql_delArtikel_3_2;
      global $sql_delArtikel_4_1;
      global $sql_delArtikel_4_2;
      global $sql_delArtikel_5_1;
      global $sql_delArtikel_5_2;

      // Test ob Datenbank-Handler benutzt werden kann (DB erreichbar)
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar (delArtikel)</H1></P><BR>");
      }
      else {
          // Teil 1 von 5: Artikel aus artikel-Tabelle entfernen
          if (!$Admin_Database->Exec("$sql_delArtikel_1_1 $Artikel_ID $sql_delArtikel_1_2")) {
              echo ("<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: dA_1</H1></P><BR>");
              die ("Query: $sql_delArtikel_1_1 $Artikel_ID $sql_delArtikel_1_2<BR>");
          }

          // Teil 2 von 5: Alle Zeilen $ArtikeL_ID betreffend in Tabelle artikel_optionen loeschen
          if (!$Admin_Database->Exec("$sql_delArtikel_2_1 $Artikel_ID $sql_delArtikel_2_2")) {
              echo ("<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: dA_2</H1></P><BR>");
              die ("Query: $sql_delArtikel_2_1 $Artikel_ID $sql_delArtikel_2_2<BR>");
          }

          // Teil 3 von 5: Alle Zeilen $ArtikeL_ID betreffend in Tabelle artikel_variationen loeschen
          if (!$Admin_Database->Exec("$sql_delArtikel_3_1 $Artikel_ID $sql_delArtikel_3_2")) {
              echo ("<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: dA_3</H1></P><BR>");
              die ("Query: $sql_delArtikel_3_1 $Artikel_ID $sql_delArtikel_3_2<BR>");
          }

          // Teil 4 von 5: Alle Zeilen $ArtikeL_ID betreffend in Tabelle artikel_kategorie loeschen
          if (!$Admin_Database->Exec("$sql_delArtikel_4_1 $Artikel_ID $sql_delArtikel_4_2")) {
              echo ("<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: dA_4</H1></P><BR>");
              die ("Query: $sql_delArtikel_4_1 $Artikel_ID $sql_delArtikel_4_2<BR>");
          }

          // Teil 5 von 5: Alle Zeilen $ArtikeL_ID betreffend in Tabelle artikel_variationsgruppen loeschen
          if (!$Admin_Database->Exec("$sql_delArtikel_5_1 $Artikel_ID $sql_delArtikel_5_2")) {
              echo ("<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: dA_5</H1></P><BR>");
              die ("Query: $sql_delArtikel_5_1 $Artikel_ID $sql_delArtikel_5_2<BR>");
          }

          //True (=1) zurueckgeben wenn die Aktion erfolgreich war:
          return 1;

      }//End else
  }//End function delArtikel

  // -----------------------------------------------------------------------------------------------
  // Einen schon bestehenden Artikel in der Datenbank veraendern (updaten):
  // Diese Funktion ist etwas verzettelt und wurde seit der Version 1.05 komplett ueberarbeitet
  // Auf die Schnelle erklaert, wird ein Artikel wie folgt upgedated:
  // 1.) Daten des upzudatenden Artikels einlesen
  // 2.) Per darstellenArtikel-Funktion (SHOP_ADMINISTRATION_ARTIKEL.php) diesen im Eingabeformular darstellen
  // 3.) In der Datei SHOP_ADMINISTRATION_AUFRUF.php unter darstellen == 101 werden die Formulardaten aufbereitet
  //[4.) Optionaler Bild-Einfuege-Dialog] (bild_up.php, hier drin geht es auch weiter mit dem Update)
  // 5.) Ueber die updArtikel_2 Funktion den Artikel updaten (siehe gleich unter dieser Funktion)
  // Argument: Artikel_ID
  // Rueckgabewert: - Artikel_ID bei Erfolg
  //                - false bei erfolgreichem Artikel Update, der Artikel wurde in die Kategorie Nichtzugeordnet abgelegt
  //                - Abbruch der Funktion per die-Funktion und entsprechender Fehlermeldung
  function updArtikel($Artikel_ID) {
      // Artikel einlesen, als Artikel-Objekt weitergeben an darstellenArtikel-Funktion
      $myArtikel = getArtikel($Artikel_ID);

      //Hier kann der Artikel bearbeitet werden, danach geht der Update in bild_up.php weiter
      darstellenArtikel($myArtikel);
  }// End function updArtikel

  // -----------------------------------------------------------------------
  // Diese Funktion wird vom Modul bild_up.php aufgerufen, sobald ein Artikel
  // upgedated werden soll. Dies ist quasi der zweite Teil eines Artikel Updates.
  // Diese Funktion updated nur die Artikeldaten OHNE das Bild (weiteres dazu siehe bild_up.php).
  // Argumente: "In Einzelteile zerlegter Artikel, vom Formular her" und dazu das Flag $kat_save.
  //            $kat_save wird benutzt um den MEHR-OPTIONEN / VARIATIONEN Button bedienen zu koennen
  //            Er bewirkt, dass Teil 4 des Artikelupdates, (der Kategorien-Update) uebersprungen wird.
  // Rueckgabewert Artikel_ID bei Erfolg, sonst Abbruch und Fehlermeldung via die-Funktion.
  function updArtikel_2($Kategorie_IDarray, $Artikel_ID, $Artikel_Nr, $Name, $Beschreibung, $letzteAenderung,
           $Preis, $Aktionspreis, $Gewicht, $MwSt, $Link, $Optionenarray, $Variationsarray, $Variationsgruppenarray,
           $Gruppentext, $Gruppe_darstellen, $Eingabefeld_text, $Eingabefeld_param, $Gewicht_Opt, $Gewicht_Var, $kat_save) {

      //Einbinden der globalen Variablen aus anderen PHP-Modulen (-Scripts)
      global $Admin_Database;     // Datenbank handle
      global $sql_updArtikel_1_1; // Alle folgenden Variablen sind SQL-Kommando-Teile...
      global $sql_updArtikel_1_2; // ...jaja, da hat sich in Zwischenzeit ganz schoen was angesammelt...
      global $sql_updArtikel_1_3;
      global $sql_updArtikel_1_4;
      global $sql_updArtikel_1_5;
      global $sql_updArtikel_1_6;
      global $sql_updArtikel_1_7;
      global $sql_updArtikel_1_7_1;
      global $sql_updArtikel_1_8;
      global $sql_updArtikel_1_8_1;
      global $sql_updArtikel_1_8_2;
      global $sql_updArtikel_1_9;
      global $sql_updArtikel_2_1;
      global $sql_updArtikel_2_2;
      global $sql_updArtikel_2_2_1;
      global $sql_updArtikel_2_3;
      global $sql_updArtikel_2_4;
      global $sql_updArtikel_3_1;
      global $sql_updArtikel_3_2;
      global $sql_updArtikel_4_1;
      global $sql_updArtikel_4_2;
      global $sql_updArtikel_4_3;
      global $sql_updArtikel_4_3_1;
      global $sql_updArtikel_4_4;
      global $sql_updArtikel_4_5;
      global $sql_updArtikel_5_1;
      global $sql_updArtikel_5_2;
      global $sql_updArtikel_5_3;
      global $sql_updArtikel_5_4;
      global $sql_updArtikel_5_5;
      global $sql_updArtikel_5_6;
      global $sql_updArtikel_5_7;
      global $sql_updArtikel_5_8;
      global $sql_updArtikel_6_1;
      global $sql_updArtikel_6_2;
      global $sql_updArtikel_7_1;
      global $sql_updArtikel_7_2;
      global $sql_updArtikel_8_1;
      global $sql_updArtikel_8_2;
      global $sql_updArtikel_9_1;
      global $sql_updArtikel_9_2;
      global $sql_newArtikel_1_3;
      global $sql_newArtikel_1_4;
      global $sql_newArtikel_1_5;
      global $sql_newArtikel_1_6;
      global $sql_newArtikel_3_1;
      global $sql_newArtikel_3_2;
      global $sql_updArtikel_10_1;
      global $sql_updArtikel_10_2;
      global $sql_updArtikel_11_1;
      global $sql_updArtikel_11_2;
      global $sql_updArtikel_11_3;
      global $sql_updArtikel_11_4;
      global $sql_updArtikel_12_1;
      global $sql_updArtikel_12_2;
      global $sql_updArtikel_12_3;
      global $sql_updArtikel_12_4;
      global $sql_updArtikel_12_5;
      global $sql_updArtikel_13_1;
      global $sql_updArtikel_13_2;

      // Test ob die Datenbank erreichbar ist, sonst Abbruch:
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar: updArtikel_2</H1></P>\n");
      }
      else {
          // Test ob das Kategorien-Update Flag $kat_save gesetzt ist:
          if (!isset($kat_save)) {
              die("<P><H1>S_A_Error: Die Pruefung isset(kat_save) hat ein negatives Resultat ergeben! Funktion: updArtikel_2</H1></P>\n");
          }
          // Nun wird der Artikel ($Artikelobjekt) in der Datenbank upgedated:
          // (Diese Operation tangiert mehrere Tabellen, deshalb gibt es eine Aufteilung in 4 Teile)

          // Teil 1 von 4: Artikel in artikel-Tabelle eintragen
          // Dies generiert einen INSERT der dank der erweiterten MySQL-Exec-Funktion
          // des database.php (DB-Wrapper) den aktuell zugewiesenen Auto-Wert
          // (hier Artikel-ID) zurueck liefert.
          $RS = $Admin_Database->Exec("$sql_updArtikel_1_1 '$Artikel_Nr' $sql_updArtikel_1_2 '$Name'
                       $sql_updArtikel_1_3 '$Beschreibung' $sql_updArtikel_1_4 '$letzteAenderung'
                       $sql_updArtikel_1_5 '$Preis' $sql_updArtikel_1_6 '$Aktionspreis'
                       $sql_updArtikel_1_7 '$Gewicht' $sql_updArtikel_1_7_1 '$MwSt' $sql_updArtikel_1_8 '$Link'
                       $sql_updArtikel_1_8_1 '$Eingabefeld_text' $sql_updArtikel_1_8_2 '$Eingabefeld_param'
                       $sql_updArtikel_1_9 '$Artikel_ID'");
          if (!$RS) {
              // Fehler beim UPDATE der Tabelle artikel --> Mit Fehlermeldung abbrechen
              echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_1</H1></P>\n");
              echo " SQL: <BR>";
              die ("Artikel-ID ist: $Artikel_ID ($Name)<BR>");
          }

          // Teil 2 von 4: Artikel-Optionen in artikel_optionen-Tabelle updaten:

          // Bevor wir die Optionen ersetzen koennen, muessen wir wissen, wieviele Optionen
          // der Artikel vorher gehabt hat.(Ev. muessen ja welche geloescht/hinzugefuegt werden):
          $aold = 0; //Anzahl Optionen des noch nicht upgedateten Artikels
          $RS = $Admin_Database->Query("$sql_updArtikel_6_1 $Artikel_ID $sql_updArtikel_6_2");
          while (is_object($RS) && $RS->NextRow()) {
              $aold++;
          }

          // Nun muessen noch Leer-Eintraege aus dem neuen Array entfernt werden!
          $anew = (count($Optionenarray)-1); // Anzahl Optionen des upgedateten Artikels
          $temparray = array();
          for ($i=0;$i <= $anew;$i++) {
              if (key($Optionenarray) == "") {
              }
              else {
                  $temparray[key($Optionenarray)] = $Optionenarray[key($Optionenarray)];
              }
              next($Optionenarray);
          }
          $Optionenarray = $temparray;

          $anew = (count($Optionenarray)); // Anzahl Optionen des upgedateten Artikels

          if (($aold != 0) || ($anew != 0)) {
              // Wenn der Artikel ueberhaupt Optionen hat, so muessen auch diese upgedated werden:

              // Es folgen drei Fallunterscheidungen, weil man gleichviel, mehr oder weniger Opt. haben kann.
              // Gleichviel Optionen (wahrscheinlichster Fall, benoetigt nur SQL-Updates):
              if ($aold == $anew) {
                  for ($i=1;$i<=$anew;$i++) {
                      $Optionsgewicht = $Gewicht_Opt[key($Optionenarray)];
                      $sql_exec = "$sql_updArtikel_2_1 '".key($Optionenarray)."' $sql_updArtikel_2_2 '".$Optionenarray[key($Optionenarray)]
                                                  ."' $sql_updArtikel_2_2_1'".$Optionsgewicht."' $sql_updArtikel_2_3 '".$Artikel_ID."' $sql_updArtikel_2_4".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_2u1</H1></P>\n");
                          echo $sql_exec."<BR>";
                          die ("Update fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold == anew)");
                      }
                      next($Optionenarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }
              }

              // Es gibt mehr neue Optionen als alte, d.h. es muessen welche hinzugefuegt werden (insert)
              // 1.) Zuerst werden alle bestehenden Optionen upgedated
              // 2.) Alle weiteren Optionen werden hinzugefuegt (insert) --> SQLs von newArtikel() verwendet
              if ($aold < $anew) {
                  // Bestehende Optionen updaten
                  reset($Optionenarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=1;$i<=$aold;$i++) {
                      $Optionsgewicht = $Gewicht_Opt[key($Optionenarray)];
                      $sql_exec = "$sql_updArtikel_2_1 '".key($Optionenarray)."' $sql_updArtikel_2_2 '".$Optionenarray[key($Optionenarray)]
                                                  ."' $sql_updArtikel_2_2_1'".$Optionsgewicht."' $sql_updArtikel_2_3 '".$Artikel_ID."' $sql_updArtikel_2_4".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_2u2</H1></P>\n");
                          echo $sql_exec."<BR>";
                          die ("Update fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold < anew)");
                      }
                      next($Optionenarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }

                  // Neue Optionen hinzufuegen (insert)
                  for ($i=($neu-($aneu-$aold)+1);$i <= $anew;$i++){
                      $Optionsgewicht = $Gewicht_Opt[key($Optionenarray)];
                      $sql_exec = "$sql_newArtikel_1_3 '$i', '".key($Optionenarray)."',
                                            '".$Optionenarray[key($Optionenarray)]."', '$Artikel_ID', '$Optionsgewicht' $sql_newArtikel_1_4";

                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: updArtikel_2_2i2</H1></P>\n");
                          echo $sql_exec."<BR>";
                          die ("Insert fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold < anew)");
                      }
                      next($Optionenarray);
                  }
              }

              // Mehr alte Optionen als neue (es werden welche geloescht -> delete)
              // 1.) Zuerst werden alle neuen Optionen upgedated
              // 2.) Alle weiteren Optionen werden geloescht (delete)
              if ($aold > $anew) {
                  // Bestehende Optionen updaten
                  reset($Optionenarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=1;$i<=$anew;$i++) {
                      $Optionsgewicht = $Gewicht_Opt[key($Optionenarray)];
                      $sql_exec = "$sql_updArtikel_2_1 '".key($Optionenarray)."' $sql_updArtikel_2_2 '".$Optionenarray[key($Optionenarray)]
                                                  ."' $sql_updArtikel_2_2_1'".$Optionsgewicht."' $sql_updArtikel_2_3 '".$Artikel_ID."' $sql_updArtikel_2_4".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_2u3</H1></P>\n");
                          echo $sql_exec."<BR>";
                          die ("Update fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold > anew)");
                      }
                      next($Optionenarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }

                  // Alle weiteren (siehe $i Initialisierung) alten Optionen loeschen (delete)
                  for ($i=(($aold-($aold-$anew))+1);$i <= $aold;$i++) {
                      $sql_exec = "$sql_updArtikel_8_1".$Artikel_ID."$sql_updArtikel_8_2".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: updArtikel_2_2d3</H1></P>\n");
                          echo $sql_exec."<BR>";
                          die ("Delete fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold > anew)");
                      }
                  }
              }
          }
          // Teil 3 von 4: Artikel-Variationen in artikel_variationen-Tabelle eintragen
          // Hier geht alles genau gleich wie im Teil 2, einfach fuer Variationen:

          // Bevor wir die Variationen ersetzen koennen, muessen wir wissen, wieviele Variationen
          // der Artikel vorher gehabt hat.(Ev. muessen ja eventuell welche geloescht/hinzugefuegt werden):
          $aold = 0; //Anzahl Variationen des noch nicht upgedateten Artikels
          $RS = $Admin_Database->Query("$sql_updArtikel_7_1 $Artikel_ID $sql_updArtikel_7_2");
          while (is_object($RS) && $RS->NextRow()){
              $aold++;
          }

          // Nun muessen noch Leer-Eintraege aus dem neuen Array entfernt werden!
          $anew = (count($Variationsarray)-1); // Anzahl Optionen des upgedateten Artikels
          $temparray = array();
          for ($i=0;$i <= $anew;$i++) {
              if (key($Variationsarray) == "") {
              }
              else {
                  $temparray[key($Variationsarray)] = $Variationsarray[key($Variationsarray)];
              }
              next($Variationsarray);
          }
          $Variationsarray = $temparray;

          $anew = (count($Variationsarray)); // Anzahl Variationen des upgedateten Artikels

          if (($aold != 0)  || ($anew != 0)){
              // Wenn der Artikel Variationen hat, so muessen auch diese bearbeitet werden:

              // Es folgen drei Fallunterscheidungen, weil man gleichviel, mehr oder weniger Var. haben kann.
              // Gleichviel Variationen (wahrscheinlichster Fall, benoetigt nur SQL-Updates):
              if ($aold == $anew) {
                  reset($Variationsarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=1;$i<=$anew;$i++) {
                      $Variationsgewicht = $Gewicht_Var[key($Variationsarray)];
                      $sql_exec = "$sql_updArtikel_4_1 '".key($Variationsarray)."' $sql_updArtikel_4_2 '".$Variationsarray[key($Variationsarray)]
                                                  ."' $sql_updArtikel_4_3'".$Variationsgruppenarray[key($Variationsarray)]."' $sql_updArtikel_4_3_1'".$Variationsgewicht."'
                                                  $sql_updArtikel_4_4 '".$Artikel_ID."' $sql_updArtikel_4_5".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_2u1v</H1></P>\n");
                          echo $sql_exec."<br>";
                          die ("Update fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold == anew)");
                      }
                      next($Variationsarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }
              }

              // Es gibt mehr neue Variationen als alte, d.h. es muessen welche hinzugefuegt werden (insert)
              // 1.) Zuerst werden alle bestehenden Variationen upgedated
              // 2.) Alle weiteren Variationen werden hinzugefuegt (insert) --> SQLs von newArtikel() verwendet
              if ($aold < $anew) {
                  // Bestehende Variationen updaten
                  reset($Variationsarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=1;$i<=$aold;$i++) {
                      $Variationsgewicht = $Gewicht_Var[key($Variationsarray)];
                      $sql_exec = "$sql_updArtikel_4_1 '".key($Variationsarray)."' $sql_updArtikel_4_2 '".$Variationsarray[key($Variationsarray)]
                                                  ."' $sql_updArtikel_4_3'".$Variationsgruppenarray[key($Variationsarray)]."' $sql_updArtikel_4_3_1'".$Variationsgewicht."'
                                                  $sql_updArtikel_4_4 '".$Artikel_ID."' $sql_updArtikel_4_5".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_2u1v</H1></P>\n");
                          echo $sql_exec."<br>";
                          die ("Update fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold == anew)");
                      }
                      next($Variationsarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }

                  // Neue Variationen hinzufuegen (insert)
                  for ($i=($neu-($aneu-$aold)+1);$i <= $anew;$i++){
                      $Variationsgewicht = $Gewicht_Var[key($Variationsarray)];
                      $sql_exec = "$sql_newArtikel_1_5 '$i', '".key($Variationsarray)."',
                                    '".$Variationsarray[key($Variationsarray)]."', $Artikel_ID, ".$Variationsgruppenarray[key($Variationsarray)].", '$Variationsgewicht' $sql_newArtikel_1_6";
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: updArtikel_2_2i2v</H1></P>\n");
                          echo $sql_exec."<br>";
                          die ("Insert fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold < anew)");
                      }
                      next($Variationsarray);
                  }
              }

              // Mehr alte Variationen als neue (es werden welche geloescht -> delete)
              // 1.) Zuerst werden alle neuen Variationen upgedated
              // 2.) Alle weiteren Variationen werden geloescht (delete)
              if ($aold > $anew) {
                  // Bestehende Variationen updaten
                  reset($Variationsarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=1;$i<=$anew;$i++) {
                      $Variationsgewicht = $Gewicht_Var[key($Variationsarray)];
                      $sql_exec ="$sql_updArtikel_4_1 '".key($Variationsarray)."' $sql_updArtikel_4_2 '".$Variationsarray[key($Variationsarray)]
                                                  ."' $sql_updArtikel_4_3'".$Variationsgruppenarray[key($Variationsarray)]."' $sql_updArtikel_4_3_1'".$Variationsgewicht."'
                                                  $sql_updArtikel_4_4 '".$Artikel_ID."' $sql_updArtikel_4_5".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_2u3v</H1></P>\n");
                          echo $sql_exec."<BR>";
                          die ("Update fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold > anew)");
                      }
                      next($Variationsarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }

                  // Alle weiteren (siehe $i Initialisierung) alten Variationen loeschen (delete)
                  for ($i=(($aold-($aold-$anew))+1);$i <= $aold;$i++) {
                      $sql_exec = "$sql_updArtikel_9_1".$Artikel_ID."$sql_updArtikel_9_2".$i;
                      if (!$Admin_Database->Exec($sql_exec)) {
                          echo ("<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: updArtikel_2_2d3v</H1></P>\n");
                          echo $sql_exec."<BR>";
                          die ("Delete fehlgeschlagen! artikel-Tabelle schon geschrieben -> manuell loeschen! (aold > anew)");
                      }
                  }
              }
          }

          // Die Anzahl der bisher Verwendeten Variationsgruppen aus der Datenbank ermitteln
          // der Artikel vorher gehabt hat.(Ev. muessen ja welche geloescht/hinzugefuegt werden):
          $gold = 0; //Anzahl Optionen des noch nicht upgedateten Artikels
          $RS = $Admin_Database->Query("$sql_updArtikel_10_1 $Artikel_ID $sql_updArtikel_10_2");
          $gold_stat = array(); // Array, zum speichern, welche Datensätze in der DB existieren
          while (is_object($RS) && $RS->NextRow()) {
              $gold++;
              $gold_stat[$RS->GetField("Gruppen_Nr")] = true;
          } // end of while

          // Anzahl Variationsgruppen bestimmen, die abgespeichert werden sollen
          $gneu = count($Gruppe_darstellen);
          for ($count_grp = 1; $count_grp<=$gneu && $count_grp<=$gneu; $count_grp++){

              // Variationsgruppe updaten
              if ( $gold_stat[$count_grp] == true &&  $Gruppentext[$count_grp] != ""){
                  if (!$Admin_Database->Exec($sql_updArtikel_11_1."'".urldecode($Gruppentext[$count_grp])."'".$sql_updArtikel_11_2."'".
                          urldecode($Gruppe_darstellen[$count_grp])."'".$sql_updArtikel_11_3.$Artikel_ID.$sql_updArtikel_11_4.$count_grp)){
                      echo "<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_Variationsgruppe_updaten</H1></P>\n";
                      echo $sql_updArtikel_11_1."'".urldecode($Gruppentext[$count_grp])."'".$sql_updArtikel_11_2."'".
                          urldecode($Gruppe_darstellen[$count_grp])."'".$sql_updArtikel_11_3.$Artikel_ID.$sql_updArtikel_11_4[$count_grp];
                      die ("Update fehlgeschlagen!");
                  } // end of if
              } // end of if

              // Variationsgruppe inserten
              else if ( $gold_stat[$count_grp] != true &&  $Gruppentext[$count_grp] != ""){
                  if (!$Admin_Database->Exec($sql_updArtikel_12_1.$Artikel_ID.$sql_updArtikel_12_2.$count_grp.$sql_updArtikel_12_3.
                          "'".urldecode($Gruppentext[$count_grp])."'".$sql_updArtikel_12_4."'".$Gruppe_darstellen[$count_grp]."'".$sql_updArtikel_12_5)) {
                      echo ("<P><H1>S_A_Error: INSERT hat nicht geklappt. Funktion: updArtikel_Variationsgruppe_einfügen</H1></P>\n");
                      echo $sql_updArtikel_12_1.$Artikel_ID.$sql_updArtikel_12_2.$count_grp.$sql_updArtikel_12_3.
                          "'".urldecode($Gruppentext[$count_grp])."'".$sql_updArtikel_12_4."'".$Gruppe_darstellen[$count_grp]."'".$sql_updArtikel_12_5;
                      die ("Insert fehlgeschlagen!");
                  } // end of if
              } // end of if

              // Variationsgruppe löschen
              else if ($gold_stat[$count_grp] == true &&  $Gruppentext[$count_grp] == ""){
                  if (!$Admin_Database->Exec($sql_updArtikel_13_1.$Artikel_ID.$sql_updArtikel_13_2.$count_grp)) {
                      echo "<P><H1>S_A_Error: DELETE hat nicht geklappt. Funktion: updArtikel_Variationsgruppe_loeschen</H1></P>\n";
                      echo $sql_updArtikel_13_1.$Artikel_ID.$sql_updArtikel_13_2.$count_grp."<BR>";
                      die ("Delete fehlgeschlagen!");
                  } // end of if
              } // end of if

              else {
                  // nichts machen ;-)
              } // end of else
          } // end of for

          // Nur wenn $kat_save = true ist, wird der Artikel auch in die neuen Kategorien eingetragen
          // sonst werden nur seine Artikeldaten ohne Bildinformationen gespeichert
          if ($kat_save) {
              // Teil 4 von 4: Kategorie updaten
              // Zuerst wird der Kategorie_IDarray bearbeitet und in einen neuen Array uebertragen
              // --> Leereintraege filtern
              $newKatarray = array();
              if (is_array($Kategorie_IDarray)) {
                  foreach($Kategorie_IDarray as $key=>$Kategorie_ID) {
                      if ($Kategorie_ID != "") {
                          $newKatarray[] = $Kategorie_ID;
                      }
                  }
              }
              // Ein Artikel kann in mehreren Kategorien vorhanden sein. D.h. wir muessen folgende vier Fallunter-
              // scheidungen vornehmen:
              // 1.) Artikel wurde keiner Kategorie zugeordnet
              // Test ob der Array leer ist --> Dann soll der Artikel in die Kategorie Nichtzugeordnet abgelegt werden
              // dies erledigt die Funktion movengzArtikel($Artikel_ID)
              if (count($newKatarray) == 0) {
                  // Da der Artikel neu keiner Kategorie zugeordnet wird, soll er in die Kategorie Nichtzugeordnet
                  // abgelegt werden. Wir holen also die Kateogir_ID der Kategorie Nichtzugeordnet und fuellen
                  // diese ID in den neuen Array:
                  $RS = $Admin_Database->Query("$sql_updArtikel_5_8");
                  if ($RS && $RS->NextRow()){
                      $newKatarray[] = $RS->GetField("Kategorie_ID");
                  }
                  else {
                      echo "Query: $sql_updArtikel_5_8<BR>";
                      die("<B><H1>S_A_Error: Fehler beim Update der Kategorie (updArtikel_2_4_1)</B></H1><BR>");
                  }
              }
              // Nun wird verglichen, ob der Artikel gleichviel, mehr oder weniger Kategorieneintraege hat als vorher
              // Zuerst wird der alte Kategoriewert ausgelesen, dann der Vergleich mit Fallunterscheidungen
              $oldKatarray = getKategorieID_eines_Artikels($Artikel_ID);
              $aold = count($oldKatarray); // Anzahle alter Kategorien
              $anew = count($newKatarray); // Anzahl neuer Kategorien

              // Bei den folgenden drei Fallunterscheidungen werden aus der Tabelle artikel_kategorie, welches die
              // Verbindungstabelle zwischen artikel und kategorien ist, alle drei Attribute pro Zeile ausgelesen:
              // a_k_ID, FK_Artikel_ID, FK_Kategorie_ID  --> Alle Attribute werden in eigene Arrays abgelegt
              $akIDarray = array();
              $artIDarray = array();
              $katIDarray = array();

              // Auslesen der drei Attribute in entsprechende Arrays
              $RS = $Admin_Database->Query("$sql_updArtikel_5_1 $Artikel_ID");
              while (is_object($RS) && $RS->NextRow()){
                  $akIDarray[] = $RS->GetField("a_k_ID");
                  $artIDarray[] = $RS->GetField("FK_Artikel_ID");
                  $katIDarray[] = $RS->GetField("FK_Kategorie_ID");
              }

              // 2.) Gleichviel Kategorieeintraege wie vorher
              if ($aold == $anew) {
                  // Kopieren der neuen Kategorien in den Array mit den FK_Kategorie_IDs
                  reset($newKatarray);
                  reset($akIDarray);
                  // Einfuegen aller Updates in die artikel_kategorie Tabelle
                  for($i=0;$i < count($akIDarray);$i++) {
                     if(!$Admin_Database->Exec("$sql_updArtikel_5_2".current($newKatarray)."$sql_updArtikel_5_3".current($akIDarray))) {
                              echo "Query: $sql_updArtikel_5_2".current($newKatarray)."$sql_updArtikel_5_3".current($akIDarray)."<BR>";
                         die("<B><H1>S_A_Error: Fehler beim Update der Kategorie (updArtikel_2_4_2)</B></H1><BR>");
                     }
                     next($newKatarray);
                     next($akIDarray);
                  }
              // Abschliessendes Zuruecksetzen aller hier benutzten Arrays (interne Zeiger aufs erste Element setzen)
              reset($akIDarray);
              reset($newKatarray);
              }// End if gleichviel neue Kategorien

              // 3.) Weniger Kategorieeintraege als wie vorher
              if ($aold < $anew) {
                  // Bestehende Kategorieneintraege updaten
                  reset($newKatarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  reset($akIDarray); // Setzt auch hier den internen Zeiger des Arrays auf das erste Element
                  for ($i=1;$i<=$aold;$i++) {
                      if (!$Admin_Database->Exec("$sql_updArtikel_5_2".current($newKatarray)."$sql_updArtikel_5_3".current($akIDarray))) {
                          echo "<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_4_3_1</H1></P>\n";
                          echo "Query: $sql_updArtikel_5_2".current($newKatarray)."$sql_updArtikel_5_3".current($akIDarray)."<BR>";
                          die ("Update fehlgeschlagen! Kategorien nicht upgedated! (aold < anew)<BR>");
                      }
                      next($newKatarray); //Damit der interne Zeiger im Array eins weiter geht!
                      next($akIDarray); //Damit auch hier der interne Zeiger im Array eins weiter geht!
                  }

                  // Neue Kategorien hinzufuegen (insert)
                  for ($i=($neu-($aneu-$aold)+1);$i <= $anew;$i++){
                      if (!$Admin_Database->Exec("$sql_updArtikel_5_4".$Artikel_ID."$sql_updArtikel_5_5".current($newKatarray)."$sql_updArtikel_5_6")) {
                          echo "<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_4_3_2</H1></P>\n";
                          echo "Query: $sql_updArtikel_5_4".$Artikel_ID."$sql_updArtikel_5_5".current($newKatarray)."$sql_updArtikel_5_6<BR>";
                          die ("Update fehlgeschlagen! zus&auml;tzliche Kategorien nicht eingef&uuml;gt! (aold < anew)<BR>");
                      }
                      next($newKatarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }
                  reset($newKatarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  reset($akIDarray); // Setzt auch hier den internen Zeiger des Arrays auf das erste Element
              }// End if mehr neue Kategorien

              // 4.) Mehr Kategorieeintraege als wie vorher

              if ($aold > $anew) {
                  // Bestehende Kategorieneintraege updaten
                  reset($newKatarray); // Setzt den internen Zeiger des Arrays auf das erste Element
                  reset($akIDarray); // Setzt auch hier den internen Zeiger des Arrays auf das erste Element
                  for ($i=1;$i<=$anew;$i++) {
                      if (!$Admin_Database->Exec("$sql_updArtikel_5_2".current($newKatarray)."$sql_updArtikel_5_3".current($akIDarray))) {
                          echo "<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_4_4_1</H1></P>\n";
                          echo "Query: $sql_updArtikel_5_2".current($newKatarray)."$sql_updArtikel_5_3".current($akIDarray)."<BR>";
                          die ("Update fehlgeschlagen! Kategorien nicht upgedated! (aold > anew)<BR>");
                      }
                      next($newKatarray); //Damit der interne Zeiger im Array eins weiter geht!
                      next($akIDarray); //Damit auch hier der interne Zeiger im Array eins weiter geht!
                  }

                  // Neue Kategorien loeschen (delete)
                  for ($i=(($aold-($aold-$anew))+1);$i <= $aold;$i++) {
                      if (!$Admin_Database->Exec("$sql_updArtikel_5_7".current($akIDarray))) {
                          echo "<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: updArtikel_2_4_4_2</H1></P>\n";
                          echo "Query: $sql_updArtikel_5_7".current($akIDarray)."<BR>";
                          die ("Update fehlgeschlagen! zus&auml;tzliche Kategorien nicht gel&ouml;scht! (aold > anew)<BR>");
                      }
                      next($akIDarray); //Damit der interne Zeiger im Array eins weiter geht!
                  }
              }// End if weniger neue Kategorien
          }// End if $kat_save
          return $Artikel_ID;
      }//End else
  }//End function updArtikel_2

  // -----------------------------------------------------------------------
  // Kopiert den angegebenen Artikel (aus SQL-Sicht: alle Artikel mit dieser Artikel_ID)
  // in die Kategorie Nichtzugeordnet
  // Argumente: Artikel_ID
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion (siehe else)
  function movengzArtikel($Artikel_ID) {

      // Einbinden der in anderen Modulen deklarierten Variablen
      global $Admin_Database;
      global $sql_movengzArtikel_1_1;
      global $sql_movengzArtikel_1_2;
      global $sql_movengzArtikel_1_3;

      // Test ob Datenbank-Connection okay ist (existiert)
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar. Funktion movengzArtikel</H1></P><BR>");
      }
      else {
          // Artikel in die Kategorie Nichtzugeordnet speichern (diese ist Unterkategorie der
          // nicht existierenden Kategorie @PhPepperShop@)
          // 1.) Kategorie_ID der Kategorie Nichtzugeordnet auslesen
          $RS = $Admin_Database->Query("$sql_movengzArtikel_1_1");
          if (is_object($RS) && $RS->NextRow()) {
              $Kategorie_ID = $RS->GetField("Kategorie_ID");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1>S_A_Error: Fehler beim moven der Artikel in die Kategorie: Nichtzugeordnet_SELECT</H1></P><BR>";
              die ("Query was: $sql_movengzArtikel_1_1");
          }
          // 2.) Artikel der neuen Kategorie zuordnen (= Update)
          $RS = $Admin_Database->Exec("$sql_movengzArtikel_1_2".$Kategorie_ID."$sql_movengzArtikel_1_3".$Artikel_ID);
          if(!$RS) {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1>S_A_Error: Fehler beim moven der Artikel in die Kategorie: Nichtzugeordnet_UPDATE</H1></P><BR>";
              die ("Query was: $sql_movengzArtikel_1_2".$Kategorie_ID."$sql_movengzArtikel_1_3".$Artikel_ID);
          }
      }
      return true;
  } // End function movengzArtikel

  // -----------------------------------------------------------------------
  // *** Wird in v.1.05 nicht mehr verwendet und wird in den nächsten Versionen geloescht ***
  // Kopiert das Bild eines Artikels zu einem anderen Artikel:
  // Diese Funktion wird beim Update eines Artikels verwendet, da dort nach jedem
  // Update der neue Artikel eine neue ID hat, muss auch das Bild "mit-gezuegelt" werden.
  // Diese Funktion ist enorm zeitaufwaendig programmiert. MySQL unterstuetzt keine SELECT
  // Anweisung bei der Quellenwahl im Update-Befehl (auch nicht im INSERT- oder REPLACE Befehl)
  // Deshalb gehen ALLE Bilddaten eines Artikels den Weg zum Benutzer und wieder zurueck (!)
  // Argumente: Alte Artikel_ID (Artikel-Source), Neue Artikel_ID (Destination - Artikel)
  // Rueckgabewert: true bei Erfolg
  function bild_kopieren($Alt_Artikel_ID, $Neu_Artikel_ID) {

      // Einbinden der in anderen Modulen deklarierten Variablen
      global $Admin_Database;
      global $sql_bild_kopieren_1_1;
      global $sql_bild_kopieren_1_2;
      global $sql_bild_kopieren_1_3;
      global $sql_bild_kopieren_1_4;
      global $sql_bild_kopieren_1_5;
      global $sql_bild_kopieren_1_6;

      // Test ob Datenbank-Connection okay ist (existiert)
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar. Funktion bild_kopieren</H1></P><BR>");
      }
      else {
          // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          // addslashes ist noetig, damit gewisse Zeichen von Datensaetzen nicht von der DB interpretiert werden!
          $RS = $Admin_Database->Query("$sql_bild_kopieren_1_1".$Alt_Artikel_ID);
          if (is_object($RS) && $RS->NextRow()) {
              $data = addslashes($RS->GetField("Bild_gross"));
              $mini_pic = addslashes($RS->GetField("Bild_klein"));
              $form_data_type = $RS->GetField("Bildtyp");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1>S_A_Error: Fehler beim auslesen des Bildes vom Artikel $Alt_Artikel_ID</H1></P><BR>";
              die ("Query was: $sql_bild_kopieren_1_1".$Alt_Artikel_ID);

          }
          // Nun wird das Bild in die DB geschrieben (UPDATE des schon existierenden, neuen Artikels)
          $RS = $Admin_Database->Exec("$sql_bild_kopieren_1_2".$data."$sql_bild_kopieren_1_3".$mini_pic."$sql_bild_kopieren_1_4".$form_data_type."$sql_bild_kopieren_1_5".$Neu_Artikel_ID."$sql_bild_kopieren_1_6");
          if (!$RS){
              echo "<P><H1>S_A_Error: Bild einfuegen in Artikel $Neu_Artikel_ID hat nicht geklappt!</H1></P><BR>";
              die("Query was: $sql_bild_kopieren_1_2".$data."$sql_bild_kopieren_1_3".$mini_pic."$sql_bild_kopieren_1_4".$form_data_type."$sql_bild_kopieren_1_5".$Neu_Artikel_ID."$sql_bild_kopieren_1_6");
          }
      }//End else
      return true;
  }//End bild_kopieren

  // -----------------------------------------------------------------------
  // Liefert als einen Array of String die in der Datenbank in der Tabelle
  // shop_settings definierten Shop-Einstellungen.
  // Rueckgabewert: Array: Keys = Feldnamen, Values = Werte der Felder
  function getshopsettings() {

      // Einbinden von in anderen Modulen definierten Variablen
      global $Admin_Database;
      global $sql_getshopsettings_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar.getshopsettings</H1></P><BR>");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $Shopsettings = array();
          $RS = $Admin_Database->Query("$sql_getshopsettings_1");
          if (is_object($RS) && $RS->NextRow()) {
              $Shopsettings['Setting_Nr'] = $RS->GetField("Setting_Nr");
              $Shopsettings['MwStsatz'] = $RS->GetField("MwStsatz");
              $Shopsettings['MwStpflichtig'] = $RS->GetField("MwStpflichtig");
              $Shopsettings['MwStNummer'] = $RS->GetField("MwStNummer");
              $Shopsettings['Admin_pwd'] = $RS->GetField("Admin_pwd");
              $Shopsettings['Name'] = $RS->GetField("Name");
              $Shopsettings['Adresse1'] = $RS->GetField("Adresse1");
              $Shopsettings['Adresse2'] = $RS->GetField("Adresse2");
              $Shopsettings['PLZOrt'] = $RS->GetField("PLZOrt");
              $Shopsettings['Tel1'] = $RS->GetField("Tel1");
              $Shopsettings['Tel2'] = $RS->GetField("Tel2");
              $Shopsettings['Email'] = $RS->GetField("Email");
              $Shopsettings['Thumbnail_Breite'] = $RS->GetField("Thumbnail_Breite");
              $Shopsettings['Mindermengenzuschlag_Aufpreis'] = $RS->GetField("Mindermengenzuschlag_Aufpreis");
              $Shopsettings['Abrechnung_nach_Preis'] = $RS->GetField("Abrechnung_nach_Preis");
              $Shopsettings['Abrechnung_nach_Gewicht'] = $RS->GetField("Abrechnung_nach_Gewicht");
              $Shopsettings['Mindermengenzuschlag'] = $RS->GetField("Mindermengenzuschlag");
              $Shopsettings['Kreditkarten_Postcard'] = $RS->GetField("Kreditkarten_Postcard");
              $Shopsettings['ShopVersion'] = $RS->GetField("ShopVersion");
              $Shopsettings['Abrechnung_nach_Pauschale'] = $RS->GetField("Abrechnung_nach_Pauschale");
              $Shopsettings['Vorauskasse'] = $RS->GetField("Vorauskasse");
              $Shopsettings['Rechnung'] = $RS->GetField("Rechnung");
              $Shopsettings['Waehrung'] = $RS->GetField("Waehrung");
              $Shopsettings['Nachnahme'] = $RS->GetField("Nachnahme");
              $Shopsettings['Mindermengenzuschlag_bis_Preis'] = $RS->GetField("Mindermengenzuschlag_bis_Preis");
              $Shopsettings['keineVersandkostenmehr_ab'] = $RS->GetField("keineVersandkostenmehr_ab");
              $Shopsettings['keineVersandkostenmehr'] = $RS->GetField("keineVersandkostenmehr");
              $Shopsettings['SSL'] = $RS->GetField("TLS_value");
              $Shopsettings['Bestellungsmanagement'] = $RS->GetField("Bestellungsmanagement");
              $Shopsettings['Gewichts_Masseinheit'] = $RS->GetField("Gewichts_Masseinheit");
              $Shopsettings['max_session_time'] = $RS->GetField("max_session_time");
              $Shopsettings['AGB'] = $RS->GetField("AGB");
              $Shopsettings['Opt_inc'] = $RS->GetField("Opt_inc");
              $Shopsettings['Var_inc'] = $RS->GetField("Var_inc");
              $Shopsettings['Opt_anz'] = $RS->GetField("Opt_anz");
              $Shopsettings['Var_anz'] = $RS->GetField("Var_anz");
              $Shopsettings['SuchInkrement'] = $RS->GetField("SuchInkrement");
              $Shopsettings['Kontoinformation'] = $RS->GetField("Kontoinformation");
              $Shopsettings['Vargruppen_anz'] = $RS->GetField("Vargruppen_anz");
              $Shopsettings['Eingabefelder_anz'] = $RS->GetField("Eingabefelder_anz");
              $Shopsettings['Gesamtpreis_runden'] = $RS->GetField("Gesamtpreis_runden");
              $Shopsettings['ArtikelSuchInkrement'] = $RS->GetField("ArtikelSuchInkrement");
              $Shopsettings['Lastschrift'] = $RS->GetField("Lastschrift");
              $Shopsettings['Sortieren_nach'] = $RS->GetField("Sortieren_nach");
              $Shopsettings['Sortiermethode'] = $RS->GetField("Sortiermethode");
              $Shopsettings['Zahl_thousend_sep'] = $RS->GetField("Zahl_thousend_sep");
              $Shopsettings['Zahl_decimal_sep'] = $RS->GetField("Zahl_decimal_sep");
              $Shopsettings['Zahl_nachkomma'] = $RS->GetField("Zahl_nachkomma");
              $Shopsettings['Haendlermodus'] = $RS->GetField("Haendlermodus");
              $Shopsettings['Haendler_login_text'] = $RS->GetField("Haendler_login_text");
              $Shopsettings['tell_a_friend'] = $RS->GetField("tell_a_friend");
              $Shopsettings['tell_a_friend_bcc'] = $RS->GetField("tell_a_friend_bcc");
              $Shopsettings['my_account'] = $RS->GetField("my_account");
              $Shopsettings['check_user_country'] = $RS->GetField("check_user_country");
          }
          else {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1>S_A_Error: Fehler beim auslesen der Shop-Settings</H1></P><BR>";
              die("<P><H1>Query: </H1><B>$sql_getshopsettings_1</B></P>");
          }
          return $Shopsettings;
      }//End else
  }//End getshopsettings


  // -----------------------------------------------------------------------
  // Speichert alle Shop-Settings in der Datenbank ab
  // Als Argumente gibt man alle Shopsettings einzeln an (dies ist praktisch, da
  // diese Funktion nur nach einem Formular aufgerufen wird.
  // Rueckgabewert: Die Funktion liefert true bei Erfolg
  function setshopsettings($Admin_pwd, $Name, $Adresse1, $Adresse2, $PLZOrt, $Tel1, $Tel2,
                $Email, $Thumbnail_Breite, $Mindermengenzuschlag_Aufpreis, $Mindermengenzuschlag, $Kreditkarten_Postcard, $Rechnung,
                $Waehrung, $Nachnahme, $Mindermengenzuschlag_bis_Preis, $keineVersandkostenmehr_ab, $keineVersandkostenmehr, $SSL,
                $Bestellungsmanagement, $Gewichts_Masseinheit,$max_session_time, $AGB, $Opt_inc, $Var_inc, $Opt_anz, $Var_anz,
                $SuchInkrement,$Vorauskasse, $Kontoinformation, $Vargruppen_anz, $Eingabefelder_anz, $Gesamtpreis_runden, $ArtikelSuchInkrement,$Lastschrift,
                $Sortieren_nach, $Sortiermethode,$Zahl_thousend_sep,$Zahl_decimal_sep,$Zahl_nachkomma, $Haendlermodus, $Haendler_login_text,
                $tell_a_friend, $tell_a_friend_bcc, $my_account, $check_user_country) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_setshopsettings_1_1;
      global $sql_setshopsettings_1_2;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar. Funktion setshopsettings</H1></P><BR>");
      }
      else {
          // Tabelle shop_settings updaten. (Hier muesste man noch die Trim()-Funktion auf die
          // erhaltenen Werte anwenden (Leerschlaege wegschneiden, Zur Sicherheit)
          $RS = $Admin_Database->Exec("$sql_setshopsettings_1_1
              Admin_pwd='$Admin_pwd',
              Name='$Name', Adresse1='$Adresse1', Adresse2='$Adresse2',
              PLZOrt='$PLZOrt',Tel1='$Tel1', Tel2='$Tel2', Email='$Email',
              Thumbnail_Breite='$Thumbnail_Breite', Mindermengenzuschlag_Aufpreis='$Mindermengenzuschlag_Aufpreis',
              Mindermengenzuschlag='$Mindermengenzuschlag', Kreditkarten_Postcard='$Kreditkarten_Postcard',
              Rechnung='$Rechnung',Waehrung='$Waehrung', Nachnahme='$Nachnahme',
              Mindermengenzuschlag_bis_Preis='$Mindermengenzuschlag_bis_Preis',
              keineVersandkostenmehr_ab='$keineVersandkostenmehr_ab', keineVersandkostenmehr='$keineVersandkostenmehr',
              `TLS_value`='$SSL', Bestellungsmanagement='$Bestellungsmanagement', Gewichts_Masseinheit='$Gewichts_Masseinheit',
              max_session_time='$max_session_time', AGB='$AGB', Opt_inc='$Opt_inc', Var_inc='$Var_inc',
              Opt_anz='$Opt_anz', Var_anz='$Var_anz', SuchInkrement='$SuchInkrement' ,
              Vorauskasse='$Vorauskasse', Kontoinformation='$Kontoinformation', Vargruppen_anz='$Vargruppen_anz',
              Eingabefelder_anz='$Eingabefelder_anz', Gesamtpreis_runden='$Gesamtpreis_runden',
              ArtikelSuchInkrement='$ArtikelSuchInkrement', Lastschrift='$Lastschrift',
              Sortieren_nach='$Sortieren_nach', Sortiermethode='$Sortiermethode', Zahl_thousend_sep='$Zahl_thousend_sep',
              Zahl_decimal_sep='$Zahl_decimal_sep', Zahl_nachkomma='$Zahl_nachkomma', Haendlermodus='$Haendlermodus',
              Haendler_login_text='$Haendler_login_text', tell_a_friend='$tell_a_friend', tell_a_friend_bcc='$tell_a_friend_bcc',
              my_account='$my_account', check_user_country='$check_user_country' $sql_setshopsettings_1_2");
          if (!$RS) {
              // Fehler beim UPDATE der Tabelle shop_settings --> Mit Fehlermeldung abbrechen
              die("<P><H1>S_A_Error: RS ist nicht true (>= 1) ! Funktion: setshopsettings</H1></P><BR>");
          }
          return true;
      }// End else
  }// End setshopsettings



  // -----------------------------------------------------------------------
  // *** Wird in v.1.05 nicht mehr verwendet und wird in den nächsten Versionen geloescht ***
  // Aktualisiert nach einem Artikel-Update die artikel_bestellungen Tabelle
  // Argumente: Alte- und neue Artikel_ID
  // Rueckgabewert: Liefert true bei Erfolg, sonst Abbruch mit Fehlermeldung
  function updbestellungen($Artikel_ID, $new_ID) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_updbestellungen_1_1;
      global $sql_updbestellungen_1_2;

      // Test ob Datenbank erreichbar ist.
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar. Funktion updbestellungen</H1></P><BR>");
      }
      else {
          // Tabelle artikel_bestellungen updaten.
          $RS = $Admin_Database->Exec("$sql_updbestellungen_1_1".$new_ID."$sql_updbestellungen_1_2".$Artikel_ID);
          if (!$RS) {
              echo "<P><H1>S_A_Error: RS ist nicht true (>= 1) ! Funktion updbestellungen</H1></P><BR>";
              die("<P><H1>Query: </H1><B>$sql_updbestellungen_1_1".$new_ID."$sql_updbestellungen_1_2".$Artikel_ID."</B></P>");
          }
      }
      return true;
  }// End updbestellungen

  // -----------------------------------------------------------------------
  // Loescht das Bild eines Artikels in der DB (Wenn man dem Artikel kein Bild mehr
  // zuordnen will. (Diese Funktion wird beim Update eines Artikels verwendet)
  // Argument: Artikel_ID
  // Rueckgabewert: Liefert true zurueck (nur bie Erfolg)
  function delBild($Artikel_ID) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_delBild_1_1;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank konnte nicht erreicht werden (delBild)</H1></P><BR>");
      }
      else {
          $RS = $Admin_Database->Exec("$sql_delBild_1_1".$Artikel_ID);
          if (!$RS) {
              echo "<P><H1>S_A_Error: Bild konnte nicht gel&ouml;scht werden (delBild)</H1></P><BR>";
              die("Query: $sql_delBild_1_1".$Artikel_ID."<BR>");
          }
      }
      return true;
  }// End delBild

  // -----------------------------------------------------------------------
  // Fuegt eine neue Kategorie in die Tabelle kategorien ein
  // Vorerst NUR den Namen und Positionsnummer unterstuetz, noch keine
  // Beschreibung und Bild-Eingabe moeglich
  // Argumente: Name, Unterkategorie_von und Positions-Nr. der neu einzufuegenden Kategorie
  // Rueckgabewert: true bei Erfolg
  function newKategorie($Name, $Pos, $Beschreibung, $Details_anzeigen, $MwSt_Satz, $Unterkat) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_newKategorie_1_1;
      global $sql_newKategorie_1_2;
      global $sql_newKategorie_1_3;
      global $sql_newKategorie_1_3_1;
      global $sql_newKategorie_1_3_3;
      global $sql_newKategorie_1_3_4;
      global $sql_newKategorie_1_4;
      global $sql_newKategorie_1_2_2;
      global $sql_newKategorie_1_3_2;
      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank konnte nicht erreicht werden: newKategorie</H1></P><BR>");
      }
      else {

          // Bevor wir mit dem Einfuegen der neuen Kategorie beginnen koennen, muessen wir noch
          // die Positionsnummern der schon existierenden Kategorien dahingehend manipulieren,
          // dass eine Luecke entsteht, wo die neue Kategorie hinein kommen soll
          // Diese Aufgabe erledigt fuer uns die in diesem Modul definierte Funktion katposschieben(...)
          if (!katposschieben(0,$Pos,"","",$Unterkat,"")) {
              die("<P><H1>S_A_Error: Die Funktion katposschieben lieferte false zurueck -> Abbruch (newKategorie)</H1></P><BR>");
          }

          if ($Unterkat == "") {
              // OLD: $RS = $Admin_Database->Exec("$sql_newKategorie_1_1".$Name."$sql_newKategorie_1_2_2".$Pos."$sql_newKategorie_1_3_2");
              $RS = $Admin_Database->Exec($sql_newKategorie_1_1.$Name.$sql_newKategorie_1_2.$Beschreibung.$sql_newKategorie_1_3.$Details_anzeigen.$sql_newKategorie_1_3_1.$MwSt_Satz.$sql_newKategorie_1_2_2.$Pos.$sql_newKategorie_1_4);
          }
          else {
              // OLD: $RS = $Admin_Database->Exec("$sql_newKategorie_1_1".$Name."$sql_newKategorie_1_2".$Unterkat."$sql_newKategorie_1_3".$Pos."$sql_newKategorie_1_4");
              $RS = $Admin_Database->Exec($sql_newKategorie_1_1.$Name.$sql_newKategorie_1_2.$Beschreibung.$sql_newKategorie_1_3.$Details_anzeigen.$sql_newKategorie_1_3_1.$MwSt_Satz.$sql_newKategorie_1_3_3.$Unterkat.$sql_newKategorie_1_3_4.$Pos.$sql_newKategorie_1_4);
          }

          if (!$RS) {
              echo "<P><H1>S_A_Error: Kategorie konnte nicht erstellt werden (newKategorie)</H1></P><BR>";
              die("Query: ".$sql_newKategorie_1_1.$Name.$sql_newKategorie_1_2.$Beschreibung.$sql_newKategorie_1_3.$Details_anzeigen.$sql_newKategorie_1_3_1.$MwSt_Satz.$sql_newKategorie_1_3_3.$Unterkat.$sql_newKategorie_1_3_4.$Pos.$sql_newKategorie_1_4."<BR>");
          }
      }// End else
      return true;
  }// End newKategorie

  // -----------------------------------------------------------------------
  // Eine Kategorie loeschen: Dabei werden die noch darin enthaltenen Artikel,
  // sofern sie keiner weiteren, noch existiernden Kategorie angehoeren, in die
  // Diese Funktion benoetigt seit Unterkategorien und Mehrfachkategorien pro Artikel
  // einiges mehr an Logik gegenueber vorher!
  // Kategorie 'Nichtzugeordnet' abgelegt
  // Argumente: Kategorie_ID Artikelloeschen-Flag
  // Rueckgabewert: true bei Erfolg, sonst per die-Funktion Abbruch
  function delKategorie($Kategorie_ID, $Artikelloeschen) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_delKategorie_1_1;
      global $sql_delKategorie_1_2;
      global $sql_delKategorie_1_3;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank konnte nicht erreicht werden: (delKategorie)</H1></P><BR>");
      }
      else {
          // Bevor wir beginnen, wird noch der Name der betroffenen (Unter-)Kategorie ausgelesen
          // Dies ist noetig um den Test auf Unterkategorien zu machen
          $meineKategorie = getKategorie($Kategorie_ID);

          // Bevor wir mit dem Loeschen beginnen koennen, muessen wir noch
          // die Positionsnummern der Kategorien dahingehend manipulieren,
          // dass die entstehende Luecke geloescht wird
          // Diese Aufgabe erledigt fuer uns die in diesem Modul definierte Funktion katposschieben(...)
          if (!katposschieben($Kategorie_ID,"","",$meineKategorie->Positions_Nr,$meineKategorie->Unterkategorie_von,"")) {
              die("<P><H1>S_A_Error: Die Funktion katposschieben lieferte false zurueck -> Abbruch (delKategorie)</H1></P><BR>");
          }

          // Artikelloeschen Flag formatieren, da es als String vom Formular her uebergeben wird
        /*  if ($Artikelloeschen == "true") {
              $Artikelloeschen = 1;
          }
          else {
              $Artikelloeschen = 0;
          }      */

          // Wenn die Variable $Artikelloeschen = true ist, so werden alle Artikel
          // Welche in dieser Kategorie eingeteilt waren geloescht, SOFERN sie nicht
          // auch noch in anderen Kategorien eingeteilt sind (dann wird nur die Referenz
          // auf die zu loeschende Kategorie entfernt (Tabelle artikel_kategorie))
          if ($Artikelloeschen) {
              // Test ob die zu loeschende Kategorie Unterkategorien besitzt:
              if ($meineKategorie->kategorienanzahl() > 0) {  // true = ja, hat Unterkategorien
                  // Fuer jede Unterkategorie:
                  foreach($meineKategorie->getallkategorien() as $ukey=>$uval) {
                      // Alle Artikel der aktuellen Unterkategorie auslesen:
                      $myArtikelarray = getArtikeleinerKategorie(addslashes($uval->Name), addslashes($uval->Unterkategorie_von));
                      foreach ($myArtikelarray as $key => $val) {
                          // Test ob der Artikel noch in einer weiteren Kategorie, welche nicht geloescht wird
                          // eingeteilt ist(Alle Kategorien des entsprechenden Artikels aus der DB auslesen und
                          // dann die Anzahl Kategorien im uebergebenen Array zaehlen)
                          // Wenn der Artikel in mehreren Kategorien ist und eine davon nicht geloescht wird
                          // , so soll nur seine Referenz auf diese Kategorie geloescht werden
                          $KategoriendesArtikels = getKategorie_eines_Artikels($val->artikel_ID);
                          $loeschenflag = true; // Wenn dieses Flag true ist, wird der Artikel geloescht
                          // Fuer jede Kategorie in welcher der Artikel momentan eingeteilt ist:
                          foreach ($KategoriendesArtikels as $kkey=>$kval) {
                              // Nun wird jede Kategorie in welcher der aktuelle Artikel eingetragen ist
                              // mit den zu loeschenden Kategorien verglichen und gegebenenfalls als zu
                              // loeschen markiert ($loeschenflag = true)
                              // Vergleich mit allen Unterkategorien --> Wenn eine Kategorie gefunden wird
                              // welche NICHT Unterkategorie der zu loeschenden Kategorie ist, so soll
                              // der Artikel nicht geloescht werden (nur Referenz loeschen -> loeschenflag = false)
                              $Unterkategorienarray = array();
                              foreach ($meineKategorie->getallkategorien() as $kkkey=>$kkval) {
                                  // Alle Unterkategorien_IDs werden in einen Array
                                  // ($Unterkategorienarray) geschrieben
                                  $Unterkategorienarray[] = $kkval->Kategorie_ID;
                                  // Jedes Element des Unterkategorienarrays mit der Kategorie_ID der
                                  // aktuellen Kategorie des Artikels der Unterkategorie vergleichen
                                  // Wenn eine gleiche Kategorie gefunden wurde, so wird die Kategorie_ID durch
                                  // den String true ersetzt
                                  // --> Wenn der Array am Ende kein true beherbergt so, ist der Artikel in
                                  // einer Kategorie / Unterkategorie, welche nicht geloescht wird
                                  // --> Man soll nur die Referenz des Artikels auf die aktuelle Unterkategorie
                                  // entfernen
                                  foreach ($Unterkategorienarray as $ukkey=>$ukval) {
                                      if ($kval == $ukval) {
                                          $ukval = "true";
                                      }
                                  }
                                  foreach ($Unterkategorienarray as $ukkey=>$ukval) {
                                      if ($ukval == "true") {
                                          $loeschenflag = false;
                                          break;// foreach Unterkategorienarray abbrechen
                                      }
                                  }
                                  if (!$loeschenflag) {
                                      break;// foreach jede Unterkategorie des zu loeschenden Kategorie-Baumes
                                  }
                              }
                              if (!$loeschenflag) {
                                  break;// foreach alle Kategorien des einen Artikels
                              }
                          }// End foreach $KategoriendesArtikels

                          // Nun kann entschieden werden, ob der Artikel geloescht werden kann oder
                          // ob lediglich seine Referenz auf die zu loeschende Unterkategorie entfernt
                          // werden soll:
                          if ($loeschenflag) {
                              // Artikel kann geloescht werden
                              delArtikel($val->artikel_ID);
                          }
                          else {
                              // Artikel ist auch noch in einer nicht zu loeschenden, weiteren Kategorie
                              // vorhanden --> nur die Referenz zu dieser Kategorie loeschen:
                              $RS_krl = $Admin_Database->Exec("$sql_delKategorie_1_2".$uval->Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID);
                              if (!$RS_krl) {
                                  echo "<P><H1>S_A_Error: (RS_krl) Die Kategorie-Referenz des Artikels konnte nicht gel&ouml;scht werden (delKategorie)</H1></P><BR>";
                                  die("Query: $sql_delKategorie_1_2".$uval->Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID."<BR>");
                              }
                          }
                      }// End foreach myArtikelarray
                      // Nun wird die aktuelle Unterkategorie aus der kategorien-Tabelle entfernt
                      $RS_ukat = $Admin_Database->Exec("$sql_delKategorie_1_1".$uval->Kategorie_ID);
                      if (!$RS_ukat) {
                          echo "<P><H1>S_A_Error: (RS_ukat) Eine Unterkategorie konnte nicht entfernt werden (delKategorie)</H1></P><BR>";
                          die("Query: $sql_delKategorie_1_1".$uval->Kategorie_ID."<BR>");
                      }
                  }// End fuer alle Unterkategorien
                  // Nun wird die Dach-Kategorie des zu loeschenden Kategoriebaums aus der kategorien-Tabelle entfernt
                  $RS_kat = $Admin_Database->Exec("$sql_delKategorie_1_1".$Kategorie_ID);
                  if (!$RS_kat) {
                      echo "<P><H1>S_A_Error: (RS_kat) Die Wurzel-Kategorie konnte nicht entfernt werden (delKategorie)</H1></P><BR>";
                      die("Query: $sql_delKategorie_1_1".$Kategorie_ID."<BR>");
                  }
              }// End Unterkategorie Test

              // Kategorie hat keine Unterkategorien
              else {
                  // Wir lesen hier zuerst alle Artikel dieser Kategorie welche keine Unterkateogiren hat aus:
                  $myArtikelarray = getArtikeleinerKategorie(addslashes($meineKategorie->Name), addslashes($meineKategorie->Unterkategorie_von));
                  // Fuer jeden dieser Artikel ueberpruefen wir, ob er zusaetzlich zu dieser auch noch
                  // in einer weiteren Kategorie vorhanden ist --> nur Referenz auf diese Kategorie loeschen
                  foreach ($myArtikelarray as $key => $val) {
                      // Dazu lesen wir fuer den aktuellen Artikel alle seine Kategorien aus und schauen
                      // ob es mehr als eine sind:
                      $KategoriendesArtikels = getKategorieID_eines_Artikels($val->artikel_ID);
                      if (count($KategoriendesArtikels) > 1) {
                          // Referenz des Artikels auf die zu loeschende Kategorie entfernen
                          $RS_krl_2 = $Admin_Database->Exec("$sql_delKategorie_1_2".$Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID);
                          if (!$RS_krl_2) {
                              echo "<P><H1>S_A_Error: (RS_krl_2) Die Kategorie-Referenz des Artikels konnte nicht gel&ouml;scht werden (delKategorie)</H1></P><BR>";
                              die("Query: $sql_delKategorie_1_2".$Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID."<BR>");
                          }
                      }
                      else {
                          // Artikel kann geloescht werden
                          delArtikel($val->artikel_ID);
                      }
                  }// End foreach alle Artikel der Kategorie
                  // Nun wird die Kategorie aus der kategorien-Tabelle entfernt
                  $RS_kat_2 = $Admin_Database->Exec("$sql_delKategorie_1_1".$Kategorie_ID);
                  if (!$RS_kat_2) {
                      echo "<P><H1>S_A_Error: (RS_kat_2) Die Wurzel-Kategorie konnte nicht entfernt werden (delKategorie)</H1></P><BR>";
                      die("Query: $sql_delKategorie_1_1".$Kategorie_ID."<BR>");
                  }
              }// End Kategorie hat keine Unterkategorien, Artikel sollen geloescht werden
          }// End Artikel sollen geloescht werden
          // Artikel nicht loeschen sondern in die Kategorie Nichtzugeordnet verschieben:
          else {
              // Zuerst werden alle Artikel welche noch in dieser Kategorie sind, in die Kategorie
              // 'Nichtzugeordnet' umleiten.
              // Test ob die zu loeschende Kategorie Unterkategorien besitzt:
              if ($meineKategorie->kategorienanzahl() > 0) {  // true = ja, hat Unterkategorien
                  // Fuer jede Unterkategorie:
                  foreach($meineKategorie->getallkategorien() as $ukey=>$uval) {
                      // Alle Artikel der aktuellen Unterkategorie auslesen:
                      $myArtikelarray = getArtikeleinerKategorie(addslashes($uval->Name), addslashes($uval->Unterkategorie_von));
                      foreach ($myArtikelarray as $val) {
                          // Test ob der Artikel noch in einer weiteren Kategorie, welche nicht geloescht wird
                          // eingeteilt ist(Alle Kategorien des entsprechenden Artikels aus der DB auslesen und
                          // dann die Anzahl Kategorien im uebergebenen Array zaehlen)
                          // Wenn der Artikel in mehreren Kategorien ist und eine davon nicht geloescht wird
                          // , so soll nur seine Referenz auf diese Kategorie geloescht werden
                          $KategoriendesArtikels = getKategorie_eines_Artikels($val->artikel_ID);
                          $loeschenflag = true; // true = der Artikel wird in die Kategorie Nichtzugeordnet verschoben
                          // Fuer jede Kategorie in welcher der Artikel momentan eingeteilt ist:
                          foreach ($KategoriendesArtikels as $kkey=>$kval) {
                              // Nun wird jede Kategorie in welcher der aktuelle Artikel eingetragen ist
                              // mit den zu loeschenden Kategorien verglichen und gegebenenfalls als zu
                              // verschieben markiert ($loeschenflag = true)
                              // Vergleich mit allen Unterkategorien --> Wenn eine Kategorie gefunden wird
                              // welche NICHT Unterkategorie der zu loeschenden Kategorie ist, so soll
                              // der Artikel nicht verschoben werden (nur Referenz loeschen -> $loeschenflag = false)
                              $Unterkategorienarray = array();
                              foreach ($meineKategorie->getallkategorien() as $kkkey=>$kkval) {
                                  // Alle Unterkategorien_IDs werden in einen Array
                                  // ($Unterkategorienarray) geschrieben:
                                  $Unterkategorienarray[] = $kkval->Kategorie_ID;
                                  // Jedes Element des Unterkategorienarrays mit der Kategorie_ID der
                                  // aktuellen Kategorie des Artikels der Unterkategorie vergleichen
                                  // Wenn eine gleiche Kategorie gefunden wurde, so wird die Kategorie_ID durch
                                  // den String true ersetzt
                                  // --> Wenn der Array am Ende kein true beinhaltet, so ist der Artikel in
                                  // einer Kategorie / Unterkategorie, welche nicht geloescht wird
                                  // --> Dann soll nur die Referenz des Artikels auf die aktuelle Unterkategorie
                                  // entfernen
                                  foreach ($Unterkategorienarray as $ukkey=>$ukval) {
                                      if ($kval == $ukval) {
                                          $ukval = "true";
                                      }
                                  }
                                  foreach ($Unterkategorienarray as $ukkey=>$ukval) {
                                      if ($ukval == "true") {
                                          $loeschenflag = false;
                                          break;// foreach Unterkategorienarray abbrechen
                                      }
                                  }
                                  if (!$loeschenflag) {
                                      break;// foreach jede Unterkategorie des zu loeschenden Kategorie-Baumes
                                  }
                              }
                              if (!$loeschenflag) {
                                  break;// foreach alle Kategorien des einen Artikels
                              }
                          }// End foreach $KategoriendesArtikels

                          // Nun kann entschieden werden, ob der Artikel verschoben werden kann oder
                          // ob lediglich seine Referenz auf die zu loeschende Unterkategorie entfernt
                          // werden soll:
                          if ($loeschenflag) {
                              // Artikel wird in die Kategorie Nichtzugeordnet verschoben
                              movengzArtikel($val->artikel_ID);
                          }
                          else {
                              // Artikel ist auch noch in einer nicht zu loeschenden, weiteren Kategorie
                              // vorhanden --> nur die Referenz zu dieser Kategorie loeschen:
                              $RS_krl = $Admin_Database->Exec("$sql_delKategorie_1_2".$uval->Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID);
                              if (!$RS_krl) {
                                  echo "<P><H1>S_A_Error: (RS_krl) Die Kategorie-Referenz des Artikels konnte nicht gel&ouml;scht werden (delKategorie)</H1></P><BR>";
                                  die("Query: $sql_delKategorie_1_2".$uval->Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID."<BR>");
                              }
                          }
                      }// End foreach myArtikelarray
                      // Nun wird die aktuelle Unterkategorie aus der kategorien-Tabelle entfernt
                      $RS_ukat = $Admin_Database->Exec("$sql_delKategorie_1_1".$uval->Kategorie_ID);
                      if (!$RS_ukat) {
                          echo "<P><H1>S_A_Error: (RS_ukat) Eine Unterkategorie konnte nicht entfernt werden (delKategorie)</H1></P><BR>";
                          die("Query: $sql_delKategorie_1_1".$uval->Kategorie_ID."<BR>");
                      }
                  }// End fuer alle Unterkategorien
                  // Nun wird die Dach-Kategorie des zu loeschenden Kategoriebaums aus der kategorien-Tabelle entfernt
                  $RS_kat = $Admin_Database->Exec("$sql_delKategorie_1_1".$Kategorie_ID);
                  if (!$RS_kat) {
                      echo "<P><H1>S_A_Error: (RS_kat) Die Wurzel-Kategorie konnte nicht entfernt werden (delKategorie)</H1></P><BR>";
                      die("Query: $sql_delKategorie_1_1".$Kategorie_ID."<BR>");
                  }
              }// End Unterkategorie Test

              // Kategorie hat keine Unterkategorien
              else {
                  // Wir lesen hier zuerst alle Artikel dieser Kategorie welche keine Unterkateogiren hat aus:
                  $myArtikelarray = getArtikeleinerKategorie(addslashes($meineKategorie->Name), addslashes($meineKategorie->Unterkategorie_von));
                  // Fuer jeden dieser Artikel ueberpruefen wir, ob er zusaetzlich zu dieser auch noch
                  // in einer weiteren Kategorie vorhanden ist --> nur Referenz auf diese Kategorie loeschen
                  foreach ($myArtikelarray as $val) {
                      // Dazu lesen wir fuer den aktuellen Artikel alle seine Kategorien aus und schauen
                      // ob es mehr als eine sind:
                      $KategoriendesArtikels = getKategorieID_eines_Artikels($val->artikel_ID);
                      if (count($KategoriendesArtikels) > 1) {
                          // Referenz des Artikels auf die zu loeschende Kategorie entfernen
                          $RS_krl_2 = $Admin_Database->Exec("$sql_delKategorie_1_2".$Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID);
                          if (!$RS_krl_2) {
                              echo "<P><H1>S_A_Error: (RS_krl_2) Die Kategorie-Referenz des Artikels konnte nicht gel&ouml;scht werden (delKategorie)</H1></P><BR>";
                              die("Query: $sql_delKategorie_1_2".$Kategorie_ID."$sql_newKategorie_1_3".$val->artikel_ID."<BR>");
                          }
                      }
                      else {
                          // Artikel wird in die Kategorie Nichtzugeordnet verschoben
                          movengzArtikel($val->artikel_ID);
                      }
                  }// End foreach alle Artikel der Kategorie
                  // Nun wird die Kategorie aus der kategorien-Tabelle entfernt
                  $RS_kat_2 = $Admin_Database->Exec("$sql_delKategorie_1_1".$Kategorie_ID);
                  if (!$RS_kat_2) {
                      echo "<P><H1>S_A_Error: (RS_kat_2) Die Wurzel-Kategorie konnte nicht entfernt werden (delKategorie)</H1></P><BR>";
                      die("Query: $sql_delKategorie_1_1".$Kategorie_ID."<BR>");
                  }
              }// End Kategorie hat keine Unterkategorien, Artikel sollen geloescht werden
          }// End Artikel sollen NICHT geloescht werden
      }
      return true;
  }// End delKategorie

  // -----------------------------------------------------------------------
  // Eine (Unter-)Kategorie verschieben
  // Argumente: Kategorie_ID, neue Positions-Nummer, aktuelle und neue Unterkategorie
  // Rueckgabewert: true bei Erfolg, sonst per die-Funktion Abbruch
  function verschiebenKategorie($Kategorie_ID, $newPos, $currentUkat, $newUkat) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_verschiebenKategorie_1_1;
      global $sql_verschiebenKategorie_1_2;
      global $sql_verschiebenKategorie_1_3;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank konnte nicht erreicht werden: (verschiebenKategorie)</H1></P><BR>");
      }
      else {
          // Ganzes Objekt einlesen:
          $meineKategorie = getKategorie($Kategorie_ID);

          // Bevor wir mit dem Loeschen beginnen koennen, muessen wir noch
          // die Positionsnummern der Kategorien dahingehend manipulieren,
          // dass die entstehende Luecke geloescht wird
          // Diese Aufgabe erledigt fuer uns die in diesem Modul definierte Funktion katposschieben(...)
          if (!katposschieben($Kategorie_ID,$meineKategorie->Positions_Nr,$newPos,"",$currentUkat,$newUkat)) {
              die("<P><H1>S_A_Error: Die Funktion katposschieben lieferte false zurueck -> Abbruch (verschiebenKategorie)</H1></P><BR>");
          }
          // Jetzt kommt das eigentliche Verschieben, dazu muessen wir Fallunterscheidungen machen:
          // 1.) Kategorie verschieben -> nur die Positionsnummer aendert, nichts weiter zu tun
          // 2.) Unterkategorie verschieben, innerhalb der gleichen Kategorie -> nichts weiter zu tun
          // 3.) Unterkategorie verschieben, in neue Kategorie -> Unterkategorie_von Attribut aendern
          if (($currentUkat <> "") && ($currentUkat <> $newUkat)) {
              // Datenbank-Update ausfuehren:
              $RS = $Admin_Database->Exec("$sql_verschiebenKategorie_1_1".$newUkat."$sql_verschiebenKategorie_1_2".$newPos."$sql_verschiebenKategorie_1_3".$Kategorie_ID);
              if (!$RS) {
                  echo "<P><H1>S_A_Error: Der Unterkategorien Update hat nicht geklappt! (verschiebenKategorie)</H1></P><BR>";
                  die("Query: $sql_verschiebenKategorie_1_1".$newUkat."$sql_verschiebenKategorie_1_2".$newPos."$sql_verschiebenKategorie_1_3".$Kategorie_ID."<BR>");
              }
          }
          return true;
      }
  }// End function verschiebenKategorie


  // -----------------------------------------------------------------------
  // Eine (Unter-)Kategorie umbenennen (Es wird der Name der entsprechenden
  // Kategorie upgedated und falls die Kategorie Unterkategorien besitzt deren
  // Attribut Unterkategorie_von
  // Argumente: Kategorie_ID (INT), Neuer_Name (STRING)
  // Rueckgabewert: true oder Abbruch per die-Funktion
  function umbenennenKategorie($Kategorie_ID, $neuerName) {
      // Einbinden von in anderen Modulen definierten Variablen
      global $Admin_Database;
      global $sql_umbenennenKategorie_1_1;
      global $sql_umbenennenKategorie_1_2;
      global $sql_umbenennenKategorie_1_3;
      global $sql_umbenennenKategorie_1_4;
      global $sql_umbenennenKategorie_1_5;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar (umbenennenKategorie)</H1></P>\n");
      }
      else {
          // Zuerst lesen wir den alten Namen aus
          $meineKategorie = getKategorie($Kategorie_ID);
          // Da der Name Sonderzeichen enthalten KANN, werden diese noch mit der Funktion addslashes
          // markiert. Da das uebergebene Argument $neuerName von uns schon in der Darstellungs-Schicht
          // ein addslashes erhaelt, ist hier kein weiteres addslashes hinzuzufuegen
          $alterName = addslashes($meineKategorie->Name);
          //Update ausfuehren (Kategorie-Namen mit Neuem ueberschreiben)
          $RS = $Admin_Database->Exec("$sql_umbenennenKategorie_1_1".$neuerName."$sql_umbenennenKategorie_1_2".$Kategorie_ID);
          //Fehlerbehandlung
          if (!$RS) {
              echo "Query: $sql_umbenennenKategorie_1_1".$neuerName."$sql_umbenennenKategorie_1_2".$Kategorie_ID."<BR>";
              die("<B><U>S_A_Error:RS ist nicht true->Abbruch (umbenennenKategorie) -> Kategoriename updaten</U></B><BR><BR>");
          }
          //Falls die Kategorie Unterkategorien besitzt, so muss auch ihr Unterkategorie_von Attribut abgeaendert werden
          if ($meineKategorie->kategorienanzahl() > 0) {
              $RS = $Admin_Database->Exec("$sql_umbenennenKategorie_1_3".$neuerName."$sql_umbenennenKategorie_1_4".$alterName."$sql_umbenennenKategorie_1_5");
              //Fehlerbehandlung
              if (!$RS) {
                  echo "Query: $sql_umbenennenKategorie_1_3".$neuerName."$sql_umbenennenKategorie_1_4".$alterName."$sql_umbenennenKategorie_1_5<BR>";
                  die("<B><U>S_A_Error:RS ist nicht true->Abbruch (umbenennenKategorie) -> Unterkategorien nachfuehren</U></B><BR><BR>");
              }
          }
          //Bei Erfolg: Rueckgabewert = true
          return true;
      }
  }//End function umbenennenKategorie

  // -----------------------------------------------------------------------
  // Diese Funktion ist eine Hilfsfunktion im Kategorien-Management. Sie
  // verschiebt die Positions-Nummern der Kategorien entsprechend den Eingabe-
  // Parameter. Positions-Nummern ordnen die Kategorien-Anzeige aufsteigen ein.
  // Nachdem diese Funktion abgelaufen ist, kann man (z.B.) entweder eine
  // neue (Unter-)Kategorie mit gewuenschte Pos_Nr eintragen (eine Luecke wurde
  // geschaffen), oder es wurde eine Luecke geloescht -> z.B. delKategorie.
  // Argumente: Kategorie_ID (Int), $currentPos (Int), $newPos (Int), $delPos(Int),
  //            $currentUkat(String), $newUkat(String)
  // Rueckgabewert: true oder Abbruch per die-Funktion
  function katposschieben($Kat_ID, $currentPos, $newPos, $delPos, $currentUkat, $newUkat) {
      // Einbinden von in anderen Modulen definierten Variablen
      global $Admin_Database;
      global $sql_katposschieben_1_1;
      global $sql_katposschieben_1_2;
      global $sql_katposschieben_1_3;
      global $sql_katposschieben_1_4;
      global $sql_katposschieben_1_5;
      global $sql_katposschieben_1_6;
      global $sql_katposschieben_1_7;
      global $sql_katposschieben_1_8;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar (katposschieben)</H1></P>\n");
      }
      else {
          // Wir muessen nun mehrere Fallunterscheidungen vornehmen: (Neu, Loeschen, Verschieben, ...)

          // Neueintrag einer Kategorie
          if (($currentPos <> "") && ($newPos == "") && ($delPos == "") && ($currentUkat == "") && ($newUkat == "")) {
              // Auslesen aller Kategorien ($currentUkat == "")
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query("$sql_katposschieben_1_5");
              // Fehlerbehandlung im Fehlerfall
              if (!is_object($RS)) {
                  echo "Query: $sql_katposschieben_1_5<BR>";
                  die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Neu_Kat_1_1</U></B><BR><BR>");
              }
              // Nun werden alle Kategorien in einen assoziativen Array abgelegt
              // key = Kategorie_ID, value = aktuelle Positions_Nr
              $Kategorienarray = array();
              while (is_object($RS) && $RS->NextRow()){
                  $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
              }
              $counter = 1; // Initialisierung der Countervariable
              // Jetzt passen wir den Array den neuen Gegebenheiten an
              // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
              // geschrieben. Somit wird die betroffene Kategorie gleich mit upgedated
              foreach ($Kategorienarray as $key=>$value) {
                  // Ab der Position an der die neue Kategorie eingefuegt wird, muessen
                  // alle folgenden Positionen um eine inkrementiert werden. Es entsteht eine Luecke
                  if ($counter >= $currentPos) {
                      // Datenbank-Update der betroffenen Kategorie (Pos = Pos + 1)
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Neu_Kat_1_2</U></B><BR><BR>");
                      }
                  }
                  $counter++;
              }// End foreach Kategorienarray
              // Abschliessend noch das Update der Kategorie Nichtzugeordnet
              $ngz = new Kategorie;
              $ngz = getNichtzugeordnetKategorie();
              // Nun muss noch die spezielle Kategorie Nichtzugeordnet upgedated werden
              $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$ngz->Kategorie_ID);
              //Fehlerbehandlung im Fehlerfall
              if (!$RS) {
                  echo "Query: $sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$ngz->Kategorie_ID."<BR>";
                  die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Neu_Kat_1_2ngz</U></B><BR><BR>");
              }
          }// End if Neueintrag einer Kategorie

          // Neueintrag einer Unterkategorie
          elseif (($currentPos <> "") && ($newPos == "") && ($delPos == "") && ($currentUkat <> "") && ($newUkat == "")) {
              // Auslesen aller Unterkategorien der Kategorie $currentUkat
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query("$sql_katposschieben_1_1".$currentUkat."$sql_katposschieben_1_4");
              // Fehlerbehandlung im Fehlerfall
              if (!is_object($RS)) {
                  echo "Query: $sql_katposschieben_1_1".$currentUkat."$sql_katposschieben_1_4<BR>";
                  die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Neu_Ukat_1_1</U></B><BR><BR>");
              }
              // Nun werden alle Unterkategorien in einen assoziativen Array abgelegt
              // key = Kategorie_ID, value = aktuelle Positions_Nr
              $Kategorienarray = array();
              while (is_object($RS) && $RS->NextRow()){
                  $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
              }
              $counter = 1; // Initialisierung der Countervariable
              // Jetzt passen wir den Array den neuen Gegebenheiten an
              // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
              // geschrieben. Somit wird die betroffene Unterkategorie gleich mit upgedated
              foreach ($Kategorienarray as $key=>$value) {
                  // Ab der Position an der die neue Unterkategorie eingefuegt wird, muessen
                  // alle folgenden Positionen um eine inkrementiert werden. Es entsteht eine Luecke
                  if ($counter >= $currentPos) {
                      // Datenbank-Update der betroffenen Unterkategorie (Pos = Pos + 1)
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Neu_Ukat_1_2</U></B><BR><BR>");
                      }
                  }
                  $counter++;
              }// End foreach Kategorienarray
          }// End elseif Neueintrag einer Unterkategorie

          // Loeschen einer Kategorie
          elseif (($currentPos == "") && ($newPos == "") && ($delPos <> "") && ($currentUkat == "") && ($newUkat == "")) {
              // Auslesen aller Kategorien ($currentUkat == "")
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query("$sql_katposschieben_1_5");
              // Fehlerbehandlung im Fehlerfall
              if (!is_object($RS)) {
                  echo "Query: $sql_katposschieben_1_5<BR>";
                  die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Del_Kat_1_1</U></B><BR><BR>");
              }
              // Nun werden alle Kategorien in einen assoziativen Array abgelegt
              // key = Kategorie_ID, value = aktuelle Positions_Nr
              $Kategorienarray = array();
              while (is_object($RS) && $RS->NextRow()){
                  $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
              }
              $counter = 1; // Initialisierung der Countervariable
              // Jetzt passen wir den Array den neuen Gegebenheiten an
              // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
              // geschrieben. Somit wird die betroffene Kategorie gleich mit upgedated
              foreach ($Kategorienarray as $key=>$value) {
                  // Ab der Position an der die Kategorie geloescht wurde (werden wird), muss
                  // das entstandene Positions-Nummern-Loch wieder gestopft werden
                  if ($counter > $delPos) {
                      // Datenbank-Update der betroffenen Kategorie (Pos = Pos - 1)
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Del_Kat_1_2</U></B><BR><BR>");
                      }
                  }
                  $counter++;
              }// End foreach Kategorienarray
              // Abschliessend noch das Update der Kategorie Nichtzugeordnet
              $ngz = new Kategorie;
              $ngz = getNichtzugeordnetKategorie();
              // Nun muss noch die spezielle Kategorie Nichtzugeordnet upgedated werden
              $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$ngz->Kategorie_ID);
              //Fehlerbehandlung im Fehlerfall
              if (!$RS) {
                  echo "Query: $sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$ngz->Kategorie_ID."<BR>";
                  die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Del_Kat_1_2ngz</U></B><BR><BR>");
              }
          }// End elseif Loeschen einer Kategorie

          // Loeschen einer Unterkategorie
          elseif (($currentPos == "") && ($newPos == "") && ($delPos <> "") && ($currentUkat <> "") && ($newUkat == "")) {
              // Auslesen aller Unterkategorien der Kategorie $currentUkat
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query("$sql_katposschieben_1_1".addslashes($currentUkat)."$sql_katposschieben_1_4");
              // Fehlerbehandlung im Fehlerfall
              if (!is_object($RS)) {
                  echo "Query: $sql_katposschieben_1_1".addslashes($currentUkat)."$sql_katposschieben_1_4<BR>";
                  die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Del_Ukat_1_1</U></B><BR><BR>");
              }
              // Nun werden alle Unterkategorien in einen assoziativen Array abgelegt
              // key = Kategorie_ID, value = aktuelle Positions_Nr
              $Kategorienarray = array();
              while (is_object($RS) && $RS->NextRow()){
                  $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
              }
              $counter = 1; // Initialisierung der Countervariable
              // Jetzt passen wir den Array den neuen Gegebenheiten an
              // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
              // geschrieben. Somit wird die betroffene Unterkategorie gleich mit upgedated
              foreach ($Kategorienarray as $key=>$value) {
                  // Da eine Unterkategorie entfernt wurde (wird), muessen wir das entstandene
                  // Positions-Nummern-Loch wieder auffuellen, resp. alle dahinter eins dekrementieren
                  if ($counter > $delPos) {
                      // Datenbank-Update der betroffenen Unterkategorie (Pos = Pos + 1)
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Del_Ukat_1_2</U></B><BR><BR>");
                      }
                  }
                  $counter++;
              }// End foreach Kategorienarray
          }// End elseif Loeschen einer Unterkategorie

          // Verschieben einer Kategorie
          elseif (($currentPos <> "") && ($newPos <> "") && ($delPos == "") && ($currentUkat == "") && ($newUkat == "")) {
              // Auslesen aller Kategorien ($currentUkat == "", in der DB: WHERE ... IS NULL)
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query("$sql_katposschieben_1_5");
              // Fehlerbehandlung im Fehlerfall
              if (!is_object($RS)) {
                  echo "Query: $sql_katposschieben_1_5<BR>";
                  die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Move_Kat_1_1</U></B><BR><BR>");
              }
              // Nun werden alle Kategorien in einen assoziativen Array abgelegt
              // key = Kategorie_ID, value = aktuelle Positions_Nr
              $Kategorienarray = array();
              while (is_object($RS) && $RS->NextRow()){
                  $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
              }
              // Jetzt folgt noch eine weitere Fallunterscheidung: Wir muessen schauen
              // ob die neue Position hoeher ist als die alte oder nicht. Je nach Resultat
              // muessen wir danach den zu verschiebenden Kategorie-Block um eine Pos_Nr
              // inkrementieren oder dekrementieren:
              if ($newPos > $currentPos) {
                  // Die neue Kategorie-Position ist hoeher -> dekrementieren:
                  $counter = 1; // Initialisierung der Countervariable
                  // Jetzt passen wir den Array den neuen Gegebenheiten an
                  // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
                  // geschrieben. Somit wird die betroffene Kategorie gleich mit upgedated
                  foreach ($Kategorienarray as $key=>$value) {
                      // Alle Kategorien welche zwischen der alten und der neuen Position
                      // der zu verschiebenden Kategorie liegen, muessen um eine Position
                      // dekrementiert werden, da die neue Position weiter hinten (groessere Pos_Nr)
                      // liegt, als die aktuelle Pos_Nr. Es entsteht eine Luecke an der neuen Position
                      if (($counter > $currentPos) && ($counter < $newPos)) {
                          // Datenbank-Update der betroffenen Kategorie (Pos = Pos - 1)
                          $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key);
                          //Fehlerbehandlung im Fehlerfall
                          if (!$RS) {
                              echo "Query: $sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key."<BR>";
                              die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Kat_1_2_1</U></B><BR><BR>");
                          }
                      }
                      $counter++;
                  }// End foreach Kategorienarray
                  // Zum Schluss verschieben wir noch die Kategorie an ihr neues Ziel
                  $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($newPos-1)."$sql_katposschieben_1_3".$Kat_ID);
                  //Fehlerbehandlung im Fehlerfall
                  if (!$RS) {
                     echo "Query: $sql_katposschieben_1_2".($newPos-1)."$sql_katposschieben_1_3".$Kat_ID."<BR>";
                     die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Kat_1_3_1</U></B><BR><BR>");
                  }
              }// End if neuPos < currentPos
              else {
                  // Die neue Kategorie-Position liegt weiter vorne, als die alte Pos_Nr
                  // wir muessen den zu verschiebenden Kategorie-Block um eins inkrementieren:
                  // Die neue Kategorie-Position ist hoeher -> dekrementieren:
                  $counter = 1; // Initialisierung der Countervariable
                  // Jetzt passen wir den Array den neuen Gegebenheiten an
                  // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
                  // geschrieben. Somit wird die betroffene Kategorie gleich mit upgedated
                  foreach ($Kategorienarray as $key=>$value) {
                      // Alle Kategorien welche zwischen der alten und der neuen Position
                      // der zu verschiebenden Kategorie liegen, muessen um eine Position
                      // inkrementiert werden, da die neue Position weiter vorne (kleinere Pos_Nr)
                      // liegt, als die aktuelle Pos_Nr. Es entsteht eine Luecke an der neuen Position
                      if (($counter >= $newPos) && ($counter < $currentPos)) {
                          // Datenbank-Update der betroffenen Kategorie (Pos = Pos - 1)
                          $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key);
                          //Fehlerbehandlung im Fehlerfall
                          if (!$RS) {
                              echo "Query: $sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key."<BR>";
                              die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Kat_1_2_2</U></B><BR><BR>");
                          }
                      }
                      $counter++;
                  }// End foreach Kategorienarray
                  // Zum Schluss verschieben wir noch die Kategorie an ihr neues Ziel
                  $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".$newPos."$sql_katposschieben_1_3".$Kat_ID);
                  //Fehlerbehandlung im Fehlerfall
                  if (!$RS) {
                     echo "Query: $sql_katposschieben_1_2".$newPos."$sql_katposschieben_1_3".$Kat_ID."<BR>";
                     die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Kat_1_3_2</U></B><BR><BR>");
                  }
              }// End else neuPos > currentPos
          }// End elseif Verschieben einer Kategorie

          // Verschieben einer Unterkategorie
          elseif (($currentPos <> "") && ($newPos <> "") && ($delPos == "") && ($currentUkat <> "") && ($newUkat <> "")) {

              // Verschieben einer Unterkategorie innerhalb einer Kategorie
              if ($currentUkat == $newUkat) {
                  // Auslesen aller Unterkategorien (Kategorie = $currentUkat)
                  // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
                  $RS = $Admin_Database->Query("$sql_katposschieben_1_1".$currentUkat."$sql_katposschieben_1_4");
                  // Fehlerbehandlung im Fehlerfall
                  if (!is_object($RS)) {
                      echo "Query: $sql_katposschieben_1_1".$currentUkat."$sql_katposschieben_1_4<BR>";
                      die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Move_Ukat_in_gleicher_Ukat_1_1</U></B><BR><BR>");
                  }
                  // Nun werden alle Unterkategorien in einen assoziativen Array abgelegt
                  // key = Kategorie_ID, value = aktuelle Positions_Nr
                  $Kategorienarray = array();
                  while (is_object($RS) && $RS->NextRow()){
                      $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
                  }
                  // Jetzt folgt noch eine weitere Fallunterscheidung: Wir muessen schauen
                  // ob die neue Position hoeher ist als die alte oder nicht. Je nach Resultat
                  // muessen wir danach den zu verschiebenden Unterkategorie-Block um eine Pos_Nr
                  // inkrementieren oder dekrementieren:
                  if ($newPos > $currentPos) {
                      // Die neue Unterkategorie-Position ist hoeher -> dekrementieren:
                      $counter = 1; // Initialisierung der Countervariable
                      // Jetzt passen wir den Array den neuen Gegebenheiten an
                      // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
                      // geschrieben. Somit wird die betroffene Unterkategorie gleich mit upgedated
                      foreach ($Kategorienarray as $key=>$value) {
                          // Alle Unterkategorien welche zwischen der alten und der neuen Position
                          // der zu verschiebenden Unterkategorie liegen, muessen um eine Position
                          // dekrementiert werden, da die neue Position weiter hinten (groessere Pos_Nr)
                          // liegt, als die aktuelle Pos_Nr. Es entsteht eine Luecke an der neuen Position
                          if (($counter > $currentPos) && ($counter < $newPos)) {
                              // Datenbank-Update der betroffenen Unterkategorie (Pos = Pos - 1)
                              $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key);
                              //Fehlerbehandlung im Fehlerfall
                              if (!$RS) {
                                  echo "Query: $sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key."<BR>";
                                  die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Ukat_in_gleicher_Ukat_1_2_1</U></B><BR><BR>");
                              }
                          }
                          $counter++;
                      }// End foreach Kategorienarray
                      // Zum Schluss verschieben wir noch die  Unterkategorie an ihr neues Ziel
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($newPos-1)."$sql_katposschieben_1_3".$Kat_ID);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".($newPos-1)."$sql_katposschieben_1_3".$Kat_ID."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Ukat_in_gleiche_Ukat_1_3</U></B><BR><BR>");
                      }
                  }// End if neuPos > currentPos
                  else {
                      // Die neue Unterkategorie-Position liegt weiter vorne, als die alte Pos_Nr
                      // wir muessen den zu verschiebenden Unterkategorie-Block um eins inkrementieren:
                      // Die neue Unterkategorie-Position ist hoeher -> dekrementieren:
                      $counter = 1; // Initialisierung der Countervariable
                      // Jetzt passen wir den Array den neuen Gegebenheiten an
                      // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
                      // geschrieben. Somit wird die betroffene Unterkategorie gleich mit upgedated
                      foreach ($Kategorienarray as $key=>$value) {
                          // Alle Unterkategorien welche zwischen der alten und der neuen Position
                          // der zu verschiebenden Unterkategorie liegen, muessen um eine Position
                          // inkrementiert werden, da die neue Position weiter vorne (kleinere Pos_Nr)
                          // liegt, als die aktuelle Pos_Nr. Es entsteht eine Luecke an der neuen Position
                          if (($counter >= $newPos) && ($counter < $currentPos)) {
                              // Datenbank-Update der betroffenen Unterkategorie (Pos = Pos - 1)
                              $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key);
                              //Fehlerbehandlung im Fehlerfall
                              if (!$RS) {
                                  echo "Query: $sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key."<BR>";
                                  die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Ukat_in_gleiche_Ukat_1_2_2</U></B><BR><BR>");
                              }
                          }
                          $counter++;
                      }// End foreach Kategorienarray
                      // Zum Schluss verschieben wir noch die  Unterkategorie an ihr neues Ziel
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".$newPos."$sql_katposschieben_1_3".$Kat_ID);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".$newPos."$sql_katposschieben_1_3".$Kat_ID."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Ukat_in_gleiche_Ukat_1_3</U></B><BR><BR>");
                      }
                  }// End else neuPos < currentPos
              } // End if Verschieben einer Unterkategorie innerhalb einer Kategorie

              // Verschieben einer Unterkategorie in eine andere Kategorie
              else {
              // Diese Operation kann in zwei Teile gegliedert werden. Diese werden
              // chronologisch abgearbeitet:
              // 1.) Unterkategorie Positions-Nummer in alter Kategorie freigeben inkl. dekrementieren
              // 2.) Die neue, zusaetzliche Unterkategorie in die neue Kategorie einfuegen

              // Teil 1. Delete der Unterkategorie Pos_Nr in der 'alten' Kategorie
              // Auslesen aller Unterkategorien der Kategorie $currentUkat
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query("$sql_katposschieben_1_1".$currentUkat."$sql_katposschieben_1_4");
              // Fehlerbehandlung im Fehlerfall
              if (!is_object($RS)) {
                  echo "Query: $sql_katposschieben_1_1".$currentUkat."$sql_katposschieben_1_4<BR>";
                  die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Move_Ukat_in_andere_Ukat_1_1 (DEL)</U></B><BR><BR>");
              }
              // Nun werden alle Unterkategorien in einen assoziativen Array abgelegt
              // key = Kategorie_ID, value = aktuelle Positions_Nr
              $Kategorienarray = array();
              while (is_object($RS) && $RS->NextRow()){
                  $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
              }
              $counter = 1; // Initialisierung der Countervariable
              // Jetzt passen wir den Array den neuen Gegebenheiten an
              // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
              // geschrieben. Somit wird die betroffene Unterkategorie gleich mit upgedated
              // Ist sie schon die hinterste Kategorie, macht das nichts, kein Update ist dann noetig
              foreach ($Kategorienarray as $key=>$value) {
                  // Da die Unterkategorie in eine neue Kategorie geht, wird ihre Positions-Nummer hier
                  // geloescht. Sie wird ganz einfach mit dem darauffolgenden ueberschrieben
                  if ($counter > $currentPos) {
                      // Datenbank-Update der betroffenen Unterkategorie (Pos = Pos + 1)
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".($counter-1)."$sql_katposschieben_1_3".$key."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Move_Ukat_in_andere_Ukat_1_2 (DEL)</U></B><BR><BR>");
                      }
                  }
                  $counter++;
              }// End foreach Kategorienarray

              // Teil 2: Insert der Positionsnummer in die neue Kategorie
              // Auslesen aller Unterkategorien der Kategorie $newUkat (die andere Kategorie)
              // Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
              $RS = $Admin_Database->Query("$sql_katposschieben_1_1".$newUkat."$sql_katposschieben_1_4");
              // Fehlerbehandlung im Fehlerfall
              if (!is_object($RS)) {
                  echo "Query: $sql_katposschieben_1_1".$newUkat."$sql_katposschieben_1_4<BR>";
                  die("<B><U>S_A_Error:RS ist kein Objekt->Abbruch (katposschieben) Neu_Ukat_1_1</U></B><BR><BR>");
              }
              // Nun werden alle Unterkategorien in einen assoziativen Array abgelegt
              // key = Kategorie_ID, value = aktuelle Positions_Nr
              $Kategorienarray = array();
              while (is_object($RS) && $RS->NextRow()){
                  $Kategorienarray[$RS->GetField("Kategorie_ID")] = $RS->GetField("Position_Nr");
              }
              $counter = 1; // Initialisierung der Countervariable
              // Jetzt passen wir den Array den neuen Gegebenheiten an
              // Im gleichen 'Atemzug' wird auch ein SQL-Update in die Datenbank zurueck
              // geschrieben. Somit wird die betroffene Unterkategorie gleich mit upgedated
              foreach ($Kategorienarray as $key=>$value) {
                  // Ab der Position an der die neue Unterkategorie eingefuegt wird, muessen
                  // alle folgenden Positionen um eine inkrementiert werden. Es entsteht eine Luecke
                  if ($counter >= $newPos) {
                      // Datenbank-Update der betroffenen Unterkategorie (Pos = Pos + 1)
                      $RS = $Admin_Database->Exec("$sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key);
                      //Fehlerbehandlung im Fehlerfall
                      if (!$RS) {
                          echo "Query: $sql_katposschieben_1_2".($counter+1)."$sql_katposschieben_1_3".$key."<BR>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (katposschieben) Neu_Ukat_1_2</U></B><BR><BR>");
                      }
                  }
                  $counter++;
              }// End foreach Kategorienarray
              // Ende Teil 2
              }// End else Verschieben einer Unterkategorie in eine andere Kategorie
          }// End elseif Verschieben einer Unterkategorie

          // Abbruch da keine Aufgabe gefunden wurde (sollte nicht vorkommen)
          else {
              die("<B><U>S_A_Error:Keine Aufgabe gefunden (Einfuegen, Loeschen, Verschieben)->Abbruch (katposschieben) (sollte nicht vorkommen!)</U></B><BR><BR>");
          }// End else
          return true;
      }
  }// End function katposschieben

  // -----------------------------------------------------------------------
  // Liefert als Kategorie-Objekt, die spezielle Nichtzugeordnet Kategorie
  // Diese Kategorie wird gekennzeichnit, dass sie in ihrem Attribut
  // Unterkategorie_von den String: @PhPepperShop@ trägt.
  // Rueckgabewert: ein Kategorie-Objekt oder Abbruch per die-Funktion
  // ACHTUNG: Es wurde ABSICHTLICH das Attribut Unterkategorie_von von @PhPepperShop@ auf "" geaendert!!!
  function getNichtzugeordnetKategorie() {
      // Einbinden von in anderen Modulen definierten Variablen
      global $Admin_Database;
      global $getNichtzugeordnetKategorie_1_1;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar (getNichtzugeordnetKategorie)</H1></P>\n");
      }
      else {
          //Query ausfuehren und in ResultSet schreiben (Typ des ResultSets, siehe database.php)
          $RS = $Admin_Database->Query("$sql_$getNichtzugeordnetKategorie_1_1");
          //Auslesen der Kategorien mit ihren jeweiligen Unterkategorien (iterativ)
          if (!is_object($RS)) {
              die("<B><U>S_A_Error:RS ist kein Objekt (getNichtzugeordnetKategorie)</U></B><BR><BR>");
          }
          if (is_object($RS) && $RS->NextRow()){
              $meineKategorie = new Kategorie; //Ein neues Kategorie-Objekt instanzieren (siehe auch kategorie_def.php)
              $meineKategorie->Kategorie_ID = $RS->GetField("Kategorie_ID");
              $meineKategorie->Name = $RS->GetField("Name");
              $meineKategorie->Positions_Nr = $RS->GetField("Positions_Nr");
              $meineKategorie->Beschreibung = $RS->GetField("Beschreibung");
              $meineKategorie->Bild_gross = $RS->GetField("Bild_gross");
              $meineKategorie->Bild_klein = $RS->GetField("Bild_klein");
              $meineKategorie->Bildtyp = $RS->GetField("Bildtyp");
              $meineKategorie->Bild_last_modified = $RS->GetField("Bild_last_modified");
              // Damit diese spezielle Kategorie nicht als Unterkategorie deklariert wird, ueberschreiben
              // wir die Unterkategorie_von Variable mit einem leeren String
              $meineKategorie->Unterkategorie_von = "";
          }
          return $meineKategorie;
      }// End else
  }// End function getNichtzugeordnetKategorie

  // -----------------------------------------------------------------------
  // *** Wird in v.1.05 nicht mehr verwendet und wird in den nächsten Versionen geloescht ***
  // Alle Kategorien zurueckschreiben (UPDATE, aber nur fuer Pos. und Name) -BETA-
  // Zuerst wird der uns uebergebene Array mit Kategorienamen und Pos. Nr
  // in einen weiteren Array gepackt (Key Kategorie_ID, Value = Array (Name, Pos))
  // Danach beginnt der Update
  // Argumente: Array mit Kategorien drin (Key = Pos.Nr, Value = Name)
  // Rueckgabewert: true bei Erfolg, sonst per die-Funktion Abbruch
  function setallKategorien($Kategorienarray) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_setallKategorien_1_1;
      global $sql_setallKategorien_1_2;
      global $sql_setallKategorien_1_3;
      global $sql_setallKategorien_1_4;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank konnte nicht erreicht werden: setallKategorien</H1></P><BR>");
      }
      else {
          // Den jeweiligen Kategorien eine Kategorie_ID zuweisen und in updatearray abfuellen
          $oldkategorien = getallKategorien();
          $updatearray = array();
          // Auspacken der erhaltenen Werte in Variablen, so dass sie spaeter wieder weiter
          // verwendet werden koennen.
          $counter = 1;
          $posname = "Positions_Nr$counter";
          $katname = "Kategoriename$counter";
          foreach($Kategorienarray as $key => $value) {
              $$posname = $key;
              $$katname = $value;
              $counter++;
              $posname = "Positions_Nr$counter";
              $katname = "Kategoriename$counter";
          }

          // Holt alle Kategorie-IDs
          $counter = 1;
          $idname = "Kategorie_ID$counter";
          $RS = $Admin_Database->Query("$sql_setallKategorien_1_4");
          while (is_object($RS) && $RS->NextRow()){
              $$idname = $RS->GetField("Kategorie_ID");
              $counter++;
              $idname = "Kategorie_ID$counter";
          }

          // Update fuer jeweilige Kategorie durchfuehren
          $counter = 1;
          $posname = "Positions_Nr$counter";
          $katname = "Kategoriename$counter";
          $idname = "Kategorie_ID$counter";
          while (!empty($$idname)) {
              $Updatestring = "Positions_Nr=".$$posname.", Name='".$$katname."'";
              // SQL einer Kategorie ausfuehren
              $RS = $Admin_Database->Exec("$sql_setallKategorien_1_1".$Updatestring."$sql_setallKategorien_1_2".$$idname."$sql_setallKategorien_1_3");
              if (!$RS) {
                  echo "<P><H1>S_A_Error: Kategorien konnten nicht upgedated werden</H1></P><BR>";
                  die("Query: $sql_setallKategorien_1_1".$Updatestring."$sql_setallKategorien_1_2".$$idname."$sql_setallKategorien_1_3<BR>");
              }
              $counter++;
              $posname = "Positions_Nr$counter";
              $katname = "Kategoriename$counter";
              $idname = "Kategorie_ID$counter";
          }// End foreach $updatearray
      }
      return true;
  }// End setallKategorien


  // -----------------------------------------------------------------------
  // Einen CSS-String in der Tabelle css_file updaten:
  // Argumente: CSS-Identifier
  //            CSS-String
  // Rueckgabewert: true, sonst per die Funktionsabbruch
  function updatecssarg($css_id, $css_string) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_cssput_1_1;
      global $sql_cssput_1_2;
      global $sql_cssput_1_3;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank konnte nicht erreicht werden: updatecssarg</H1></P><BR>");
      }
      else {
           $RS = $Admin_Database->Exec($sql_cssput_1_1.$css_string.$sql_cssput_1_2.$css_id.$sql_cssput_1_3);
           if (!$RS) {
               echo "<P><H1>S_A_Error: CSS-Einstellung konnte nicht upgedated werden</H1></P><BR>";
               die("Query: ".$sql_cssput_1_1.$css_string.$sql_cssput_1_2.$css_id.$sql_cssput_1_3."<BR>");
           }
      }
      return true;
  }// End getcssarg

  // -----------------------------------------------------------------------
  // Die Inkremente fuer weitere leere Felder (darstellen_Artikel-Funktion)
  // in der Tabelle shop_settings setzen (Attribute: Opt_inc, Var_inc)
  // Argumente: Optionsinkrement (int), Variationsinkrement (int)
  // Rueckgabewert: entweder true (1) oder Abbruch per die-Funktion
  function setvaroptinc() {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Database;
      global $sql_setvaroptinc_1_1;
      global $sql_setvaroptinc_1_2;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank konnte nicht erreicht werden: setvaroptinc</H1></P><BR>");
      }
      else {
           $RS = $Admin_Database->Exec("$sql_setvaroptinc_1_1".$Opt_inc."$sql_setvaroptinc_1_2".$Var_inc);
           $rueckarray = array();
           if (!$RS){
              echo "Query: $sql_setvaroptinc_1_1".$Opt_inc."$sql_setvaroptinc_1_2".$Var_inc."<BR>";
              die("<P><H1 class='content'>S_A_Error: Konnte die Werte [$Opt_inc], [$Var_inc] nicht updaten!: setvaroptinc</H1></P><BR>");
           }
      }
      return true;
  }// End setvaroptinc

  // -----------------------------------------------------------------------
  // Speichert alle Versandkosten Einstellungen in der Datenbank ab
  // Als Argumente gibt man alle Versandkostensettings einzeln an (dies ist praktisch, da
  // diese Funktion nur nach einem Formular aufgerufen wird.
  // Rueckgabewert: Die Funktion liefert true bei Erfolg
  function setversandkostensettings($Abrechnung_nach_Preis,$Abrechnung_nach_Gewicht,$Abrechnung_nach_Pauschale,$Pauschale_text,
           $keineVersandkostenmehr,$keineVersandkostenmehr_ab,$neue_anzahl_Versandkostenintervalle,$Mindermengenzuschlag,
           $Mindermengenzuschlag_bis_Preis,$Mindermengenzuschlag_Aufpreis,$Nachnamebetrag,$Setting_Nr,$Versandkostenpreise) {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_setversandkostensettings_1_1;
      global $sql_setversandkostensettings_1_2;
      global $sql_setversandkostensettings_1_3;
      global $sql_setversandkostensettings_1_4;
      global $sql_setversandkostensettings_1_5;
      global $sql_setversandkostensettings_1_6;
      global $sql_setversandkostensettings_1_7;
      global $sql_setversandkostensettings_1_8;
      global $sql_setversandkostensettings_1_9;
      global $sql_setversandkostensettings_1_9_1;
      global $sql_setversandkostensettings_1_9_2;
      global $sql_setversandkostensettings_1_9_3;
      global $sql_setversandkostensettings_1_9_4;
      global $sql_setversandkostensettings_1_10;
      global $sql_setversandkostensettings_1_11;
      global $sql_setversandkostensettings_1_12;
      global $sql_setversandkostensettings_1_13;
      global $sql_setversandkostensettings_1_14;
      global $sql_setversandkostensettings_1_15;
      global $sql_setversandkostensettings_1_16;
      global $sql_setversandkostensettings_1_17;
      global $sql_setversandkostensettings_1_18;
      global $sql_setversandkostensettings_1_18_1;
      global $sql_setversandkostensettings_1_18_2;
      global $sql_setversandkostensettings_1_18_3;
      global $sql_setversandkostensettings_1_18_4;
      global $sql_setversandkostensettings_1_19;
      global $sql_setversandkostensettings_1_20;

      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar. Funktion setversandkostensettings</H1></P><BR>");
      }
      else {

          // Tabelle shop_settings updaten. (Hier sollte man noch die Trim()-Funktion auf die
          // erhaltenen Werte anwenden (Leerschlaege wegschneiden, Zur Sicherheit)
          $RS = $Admin_Database->Exec("$sql_setversandkostensettings_1_1
              Abrechnung_nach_Preis='$Abrechnung_nach_Preis', Abrechnung_nach_Gewicht='$Abrechnung_nach_Gewicht',
              Abrechnung_nach_Pauschale='$Abrechnung_nach_Pauschale',Pauschale_text='$Pauschale_text',
              keineVersandkostenmehr='$keineVersandkostenmehr', keineVersandkostenmehr_ab=$keineVersandkostenmehr_ab,
              anzahl_Versandkostenintervalle='$neue_anzahl_Versandkostenintervalle',
              Mindermengenzuschlag='$Mindermengenzuschlag',Mindermengenzuschlag_bis_Preis=$Mindermengenzuschlag_bis_Preis,
              Mindermengenzuschlag_Aufpreis=$Mindermengenzuschlag_Aufpreis,Nachnamebetrag=$Nachnamebetrag
              $sql_setversandkostensettings_1_2 $Setting_Nr");
          if (!$RS) {
              // Fehler beim UPDATE der Tabelle shop_settings --> Mit Fehlermeldung abbrechen
              die("<P><H1>S_A_Error: RS ist nicht true (>= 1) ! Funktion: setversandkostensettings_upd1</H1></P><BR>");
          }
          else {
              // Nun geht es um den Update der versandkostenpreise Tabelle:
              // Auslesen der alten Werte zwecks Vergleich
              $alteSettings = getversandkostensettings($Setting_Nr);
              $altePreise = $alteSettings->getallversandkostenpreise();
              $aold = count($altePreise);
              $anew = count($Versandkostenpreise);
              // Es folgen drei Fallunterscheidungen, weil man gleichviel, mehr oder weniger Zeilen haben kann.
              // Gleichviel Zeilen (wahrscheinlichster Fall, benoetigt nur SQL-Updates):
              if ($aold == $anew) {
                  reset($Versandkostenpreise); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=0;$i<$anew;$i++) {
                      if (!$Admin_Database->Exec("$sql_setversandkostensettings_1_3'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_4'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_5'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_6'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_7'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_8'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_9'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_9_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_9_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_9_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_9_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_10'".$Versandkostenpreise[$i]->Von_Bis_ID."'")) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: setversandkostensettings_upd2_1</H1></P>\n");
                          echo "Query: $sql_setversandkostensettings_1_3'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_4'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_5'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_6'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_7'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_8'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_9'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_9_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_9_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_9_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_9_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_10'".$Versandkostenpreise[$i]->Von_Bis_ID."'<BR>";
                          die ("Update fehlgeschlagen! (aold == anew)");
                      }
                      next($Versandkostenpreise); //Damit der interne Zeiger im Array eins weiter geht!
                  }
              }

              // Es gibt mehr neue Zeilen als alte, d.h. es muessen welche hinzugefuegt werden (insert)
              // 1.) Zuerst werden alle bestehenden Zeilen upgedated
              // 2.) Alle weiteren Zeilen werden hinzugefuegt (insert)
              if ($aold < $anew) {
                  // Bestehende Zeilen updaten
                  reset($Versandkostenpreise); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=0;$i<$aold;$i++) {
                      if (!$Admin_Database->Exec("$sql_setversandkostensettings_1_3'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_4'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_5'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_6'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_7'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_8'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_9'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_9_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_9_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_9_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_9_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_10'".$Versandkostenpreise[$i]->Von_Bis_ID."'")) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: setversandkostensettings_upd2_2_1</H1></P>\n");
                          echo "Query: $sql_setversandkostensettings_1_3'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_4'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_5'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_6'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_7'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_8'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_9'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_9_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_9_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_9_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_9_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_10'".$Versandkostenpreise[$i]->Von_Bis_ID."'<BR>";
                          die ("Update fehlgeschlagen! (aold < anew)");
                      }
                      next($Versandkostenpreise); //Damit der interne Zeiger im Array eins weiter geht!
                  }

                  // Neue Zeilen hinzufuegen (insert)
                  for ($i=($neu-($aneu-$aold));$i < $anew;$i++){
                      if (!$Admin_Database->Exec("$sql_setversandkostensettings_1_12'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_13'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_14'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_15'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_16'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_17'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_18'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_18_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_18_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_18_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_18_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_19'".$Setting_Nr."'$sql_setversandkostensettings_1_20")) {
                          echo ("<P><H1>S_A_Error: UPDATE (INSERT-Teil) hat nicht geklappt. Funktion: setversandkostensettings_upd2_2_2</H1></P>\n");
                          echo "<B>Query:</B> $sql_setversandkostensettings_1_12'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_13'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_14'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_15'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_16'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_17'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_18'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_18_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_18_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_18_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_18_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_19'".$Setting_Nr."'$sql_setversandkostensettings_1_20<BR>";
                          die ("Update fehlgeschlagen! (aold < anew)");
                      }
                      next($Versandkostenpreise); //Damit der interne Zeiger im Array eins weiter geht!
                  }
              }

              // Mehr alte Zeilen als neue (es werden welche geloescht -> delete)
              // 1.) Zuerst werden alle neuen Zeilen upgedated
              // 2.) Alle weiteren Zeilen werden geloescht (delete)
              if ($aold > $anew) {
                  // Bestehende Zeilen updaten
                  reset($Versandkostenpreise); // Setzt den internen Zeiger des Arrays auf das erste Element
                  for ($i=0;$i<$anew;$i++) {
                      if (!$Admin_Database->Exec("$sql_setversandkostensettings_1_3'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_4'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_5'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_6'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_7'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_8'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_9'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_9_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_9_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_9_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_9_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_10'".$Versandkostenpreise[$i]->Von_Bis_ID."'")) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: setversandkostensettings_upd2_3_1</H1></P>\n");
                          echo "Query: $sql_setversandkostensettings_1_3'".$Versandkostenpreise[$i]->Von.
                           "'$sql_setversandkostensettings_1_4'".$Versandkostenpreise[$i]->Bis."'$sql_setversandkostensettings_1_5'".
                           $Versandkostenpreise[$i]->Betrag."'$sql_setversandkostensettings_1_6'".$Versandkostenpreise[$i]->Vorauskasse.
                           "'$sql_setversandkostensettings_1_7'".$Versandkostenpreise[$i]->Rechnung."'$sql_setversandkostensettings_1_8'".
                           $Versandkostenpreise[$i]->Nachname."'$sql_setversandkostensettings_1_9'".$Versandkostenpreise[$i]->Kreditkarte.
                           "'$sql_setversandkostensettings_1_9_1'".$Versandkostenpreise[$i]->billBOX.
                           "'$sql_setversandkostensettings_1_9_2'".$Versandkostenpreise[$i]->Treuhandzahlung.
                           "'$sql_setversandkostensettings_1_9_3'".$Versandkostenpreise[$i]->Lastschrift.
                           "'$sql_setversandkostensettings_1_9_4'".$Versandkostenpreise[$i]->Postcard.
                           "'$sql_setversandkostensettings_1_10'".$Versandkostenpreise[$i]->Von_Bis_ID."'<BR>";
                          die ("Update fehlgeschlagen! (aold > anew)");
                      }
                      next($Versandkostenpreise); //Damit der interne Zeiger im Array eins weiter geht!
                  }

                  // Alle weiteren (siehe $i Initialisierung) alten Zeilen loeschen (delete)
                  for ($i=($aold-($aold-$anew));$i < $aold;$i++) {
                      if (!$Admin_Database->Exec("$sql_setversandkostensettings_1_11".$altePreise[$i]->Von_Bis_ID)) {
                          echo ("<P><H1>S_A_Error: UPDATE hat nicht geklappt. Funktion: setversandkostensettings_upd2_3_2</H1></P>\n");
                          echo "Query: $sql_setversandkostensettings_1_11".$altePreise[$i]->Von_Bis_ID."<BR>";
                          die ("Update fehlgeschlagen! (aold > anew)");
                      }
                      next($Versandkostenpreise); //Damit der interne Zeiger im Array eins weiter geht!
                  }
              }
          }
          return true;
      }// End else
  }// End setversandkostensettings

  // -----------------------------------------------------------------------
  // Gibt auf Grund einer Bestellungs Referenz_Nr die dazugehoerende Bestellung zurueck
  // Eine Referenz_Nr ist die Summe aus dem Offset $bestellungs_offset (definiert in der Datei
  // bestellung_def.php) und der aktuellen Bestellungs_ID.
  // Argument: Referenz_Nr (INT)
  // Rueckgabewert: Eine Bestellung als Bestellungs-Objekt (Definition siehe bestellung_def.php)
  function getBestellung_Ref($Referenz_Nr) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Admin_Database;
      global $sql_getBestellung_Ref_1_1;
      global $sql_getBestellung_Ref_1_2;
      global $sql_getBestellung_Ref_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar (getBestellung_Ref)</H1></P>\n");
      }
      else {
          // Aus der Referenz_Nr die Bestellungs_ID ausrechnen: (Referenz_Nr - Offset) = Bestellungs_ID
          $Bestellungs_ID = ref_to_bestellungs_id(trim($Referenz_Nr));

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Query($sql_getBestellung_Ref_1_1.$Bestellungs_ID.$sql_getBestellung_Ref_1_2);

          // Test ob keine Resultate zurueck gekommen sind, dann Versuch nur eine Bestellung OHNE Artikel einzulesen
          $noartikel = false;// Flag setzen, dass nicht versucht wird Artikel einzulesen
          if ($RS->GetRecordCount() == 0) {
              unset($RS);
              $RS = $Admin_Database->Query($sql_getBestellung_Ref_1_3.$Bestellungs_ID);
              $noartikel = true; // Flag setzen
          }

          // Auslesen:
          $myBestellung = new Bestellung; //Ein neues Bestellungs-Objekt instanzieren
          $art_counter = 1; //Counter um Artikel einer Bestellung zu zaehlen (=Array-Key)
          while (is_object($RS) && $RS->NextRow()){
              $myArtikel_info = new Artikel_info; //Ein neues Artikel_info-Objekt instanzieren
              // Bestellung einlesen:
              $myBestellung->Bestellungs_ID = $RS->GetField("Bestellungs_ID");
              $myBestellung->Session_ID = $RS->GetField("Session_ID");
              $myBestellung->Bestellung_abgeschlossen = $RS->GetField("Bestellung_abgeschlossen");
              $myBestellung->Anrede = $RS->GetField("Anrede");
              $myBestellung->Firma = $RS->GetField("Firma");
              $myBestellung->Abteilung = $RS->GetField("Abteilung");
              $myBestellung->Vorname = $RS->GetField("Vorname");
              $myBestellung->Name = $RS->GetField("Name");
              $myBestellung->Adresse1 = $RS->GetField("Adresse1");
              $myBestellung->Adresse2 = $RS->GetField("Adresse2");
              $myBestellung->PLZ = $RS->GetField("PLZ");
              $myBestellung->Ort = $RS->GetField("Ort");
              $myBestellung->Land = $RS->GetField("Land");
              $myBestellung->Telefon = $RS->GetField("Telefon");
              $myBestellung->Email = $RS->GetField("Email");
              $myBestellung->Datum = $RS->GetField("Datum");
              $myBestellung->Endpreis = $RS->GetField("Endpreis");
              $myBestellung->Bezahlungsart = $RS->GetField("Bezahlungsart");
              $myBestellung->Anmerkung = $RS->GetField("Anmerkung");
              $myBestellung->Versandkosten = $RS->GetField("Versandkosten");
              $myBestellung->Mindermengenzuschlag = $RS->GetField("Mindermengenzuschlag");
              $myBestellung->Rechnungsbetrag = $RS->GetField("Rechnungsbetrag");
              $myBestellung->Bestellung_ausgeloest = $RS->GetField("Bestellung_ausgeloest");
              $myBestellung->Bestellung_bezahlt = $RS->GetField("Bestellung_bezahlt");
              // Info des Artikels in ein Artikel_info-Objekt ablegen (Def. siehe bestellung_def.php)
              $myArtikel_info->Artikel_ID = $RS->GetField("FK_Artikel_ID");
              $myArtikel_info->Artikel_Nr = $RS->GetField("Artikel_Nr");
              $myArtikel_info->Name = $RS->GetField("Artikelname");
              $myArtikel_info->Anzahl = $RS->GetField("Anzahl");
              $myArtikel_info->Preis = $RS->GetField("Preis");
              $myArtikel_info->Gewicht = $RS->GetField("Gewicht");
              $myArtikel_info->Zusatzfelder = explode("þ",$RS->GetField("Zusatztexte"));

              // Je ein Variationstext und danach die Preisdifferenz werden per explode der Reihe
              // nach in den temporaeren Array $vararry abgelegt. Trennzeichen = Alt + 0254
              $vararray = array();
              $vararray = explode("þ",$RS->GetField("Variation"));
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
              if (!$noartikel) {
                  $myBestellung->putartikel($art_counter,$myArtikel_info);
              }
         }//End while
         return $myBestellung;
      }//End else
  }//End getBestellung_Ref

  // -----------------------------------------------------------------------
  // Gibt auf Grund eines Kundennamens die dazugehoerende Bestellung(-en) zurueck
  // Argument: Name und Vorname des Kunden, jeweils Strings
  // Rueckgabewert: Ein Array von Bestellung(-en) als Bestellungs-Objekt(-e) (Definition siehe bestellung_def.php)
  function getBestellung_Kunde($Nachname, $Vorname) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Admin_Database;
      global $sql_getBestellung_Kunde_1_1;
      global $sql_getBestellung_Kunde_1_2;
      global $sql_getBestellung_Kunde_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar (getBestellung_Kunde)</H1></P>\n");
      }
      else {
          // Aus der Referenz_Nr die Bestellungs_ID ausrechnen: (Referenz_Nr - Offset) = Bestellungs_ID
          $Bestellungs_ID = ref_to_bestellungs_id(trim($Referenz_Nr));

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Query($sql_getBestellung_Kunde_1_1.$Nachname.$sql_getBestellung_Kunde_1_2.$Vorname.$sql_getBestellung_Kunde_1_3);
          $Bestellungs_IDarray = array();
          while (is_object($RS) && $RS->NextRow()){
              $Bestellungs_IDarray[] = $RS->GetField("Bestellungs_ID");
          }
          // Keine Bestellungen gefunden, leerer Array zurueckgeben
          if (count($Bestellungs_IDarray) == 0) {
              return $Bestellungs_IDarray;
          }
          else {
              $rueckarray = array();
              foreach($Bestellungs_IDarray as $key=>$value) {
                  $rueckarray[] = getBestellung_Ref(bestellungs_id_to_ref($value));
              }
              return $rueckarray;
          }
      }
  }// End function getBestellung_Kunde

  // -----------------------------------------------------------------------
  // Gibt alle als abgeschlossen markierte Bestellungen in einem Array zurueck
  // Dem Argument $order kann optional ein Attributname uebergeben werden nachdem die Objekte
  // dann ausgelesen werden.
  // Argumente: $order
  // Rueckgabewert: Ein Array von Bestellung(-en) als Bestellungs-Objekt(-e) (Definition siehe bestellung_def.php)
  function getBestellung_Alle($order) {

      //Einbinden von in anderen Modulen deklarierten Variablen
      global $Admin_Database;
      global $sql_getBestellung_Alle_1_1;
      global $sql_getBestellung_Alle_1_2;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar (getBestellung_Alle)</H1></P>\n");
      }
      else {
          // Aus der Referenz_Nr die Bestellungs_ID ausrechnen: (Referenz_Nr - Offset) = Bestellungs_ID
          $Bestellungs_ID = bestellungs_id_to_ref(trim($Referenz_Nr));

          // $order auswerten (kann optional weggelassen werden, dann wird standardmaessig nach Buchungsdatum sortiert
          if ($order == "") {
              $order = "Datum";
          }

          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Query("$sql_getBestellung_Alle_1_1"."$order"."$sql_getBestellung_Alle_1_2");
          $Bestellungs_IDarray = array();
          while (is_object($RS) && $RS->NextRow()){
              $Bestellungs_IDarray[] = $RS->GetField("Bestellungs_ID");
          }
          // Keine Bestellungen gefunden, leerer Array zurueckgeben
          if (count($Bestellungs_IDarray) == 0) {
              return $Bestellungs_IDarray;
          }
          else {
              $rueckarray = array();
              foreach($Bestellungs_IDarray as $key=>$value) {
                  $rueckarray[] = getBestellung_Ref(bestellungs_id_to_ref($value));
              }
              return $rueckarray;
          }
      }
  }// End function getBestellung_Alle

  // -----------------------------------------------------------------------
  // *** Wird in v.1.05 nicht mehr verwendet und wird in den nächsten Versionen geloescht ***
  // Damit der Shop-Administrator jederzeit in einer geschlossenen Bestellung
  // die Kundendaten aendern kann
  // Daten = Sachen wie Name, Vorname, Adresse, Tel, Email, ...
  // Argumente: Bestellungs-Objekt, enthaelt die User-Daten (Def. siehe bestellung_def.php)
  // Rueckgabewert: true bei Erfolg, Abbruch per die-Funktion bei allfaelligem Fehler
  function setKundendatenAdmin($Bestellung) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_setKundendatenAdmin_1_1;
      global $sql_setKundendatenAdmin_1_2;
      global $sql_setKundendatenAdmin_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: setKundendatenAdmin</H1></P><BR>");
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
          $RS = $Admin_Database->Exec("$sql_setKundendatenAdmin_1_1".$Kundendaten."$sql_setKundendatenAdmin_1_2".$Bestellung->Bestellungs_ID."$sql_setKundendatenAdmin_1_3");
          if (!$RS) {
              //Script mit einer Fehlermeldung beenden
              echo "<P><H1 class='content'>U_B_Error: Fehler bei der Eingabe der Kundendaten</H1></P><BR>";
              die("Query: $sql_setKundendatenAdmin_1_1".$Kundendaten."$sql_setKundendatenAdmin_1_2".$Bestellung->Bestellungs_ID."$sql_setKundendatenAdmin_1_3<BR>");
          }
      }//End else
  }//End setKundendatenAdmin

  // -----------------------------------------------------------------------
  // Diese Funktion loescht eine abgeschlossene Bestellung unwiderruflich.
  // Natuerlich werden auch all ihre Referenzen in der Tabelle artikel_bestellung geloescht.
  // Als dritter Teil, werden noch alle Referenzen zum Kunden der Bestellung geloescht.
  // Argumente: Bestellungs_ID (Int)
  // Rueckgabewert: true bei Erfolg, Abbruch per die-Funktion bei allfaelligem Fehler
  function delBestellung($Bestellungs_ID) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_delBestellung_1_1;
      global $sql_delBestellung_1_2;
      global $sql_delBestellung_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: delBestellung</H1></P><BR>");
      }
      else {
           //Loeschen der Bestellung in der Tabelle bestellung
           $RS = $Admin_Database->Exec("$sql_delBestellung_1_1".$Bestellungs_ID);
           if (!$RS){
              echo "Query: $sql_delBestellung_1_1".$Bestellungs_ID."<BR>";
              die("<P><H1 class='content'>S_A_Error: Konnte eine Bestellung nicht l&ouml;schen!: delBestellung_1</H1></P><BR>");
           }
           //Loeschen ihrer Referenzen in der Tabelle artikel_bestellung
           $RS = $Admin_Database->Exec("$sql_delBestellung_1_2".$Bestellungs_ID);
           if (!$RS){
              echo "Query: $sql_delBestellung_1_2".$Bestellungs_ID."<BR>";
              die("<P><H1 class='content'>S_A_Error: Konnte eine Bestellung nicht l&ouml;schen!: delBestellung_2</H1></P><BR>");
           }
           //Loeschen ihrer Referenzen in der Tabelle bestellung_kunde
           $RS = $Admin_Database->Exec("$sql_delBestellung_1_3".$Bestellungs_ID);
           if (!$RS){
              echo "Query: $sql_delBestellung_1_3".$Bestellungs_ID."<BR>";
              die("<P><H1 class='content'>S_A_Error: Konnte eine Bestellung nicht l&ouml;schen!: delBestellung_3</H1></P><BR>");
           }
      }
      return true;
  }//End delBestellung

  // -----------------------------------------------------------------------
  // Diese Funktion schreibt alle Kreditkartendaten in die kreditkarten-Tabelle
  // Vorgehen: Vorandene Daten loeschen, neue Daten speichern (Kategorie_ID inkrementiert)
  // Argumente: Herstellerarray, benutzenarray, Handlingarray
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion
  function setKreditkarten($Herstellerarray, $benutzenarray, $Handlingarray) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_setKreditkarten_1_1;
      global $sql_setKreditkarten_1_2;
      global $sql_setKreditkarten_1_3;
      global $sql_setKreditkarten_1_4;

      $Anzahl_Kreditkarten = 10; //Vorlaeufig noch statisch (auch in SHOP_SETTINGS)

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: setKreditkarten_1</H1></P><BR>");
      }
      else {
          //Loeschen der vorhandenen Kreditkartendaten
          $RS = $Admin_Database->Exec("$sql_setKreditkarten_1_2");
          if (!$RS){
             echo "<B>Query:</B> $sql_setKreditkarten_1_2<BR>";
             die("<P><H1 class='content'>S_A_Error: Konnte Kreditkarten nicht l&ouml;schen: setKreditkarten_2_1</H1></P><BR>");
          }
          for ($i=0;$i < $Anzahl_Kreditkarten;$i++) {
              $RS = $Admin_Database->Exec("$sql_setKreditkarten_1_3'".$Herstellerarray[$i]."','".$benutzenarray[$i]."','".$Handlingarray[$i]."'$sql_setKreditkarten_1_4");
              if (!$RS){
                  echo "<B>Query:</B> $sql_setKreditkarten_1_3'".$Herstellerarray[$i]."','".$benutzenarray[$i]."','".$Handlingarray[$i]."'$sql_setKreditkarten_1_4<BR>";
                  die("<P><H1 class='content'>S_A_Error: Konnte Kreditkarten nicht speichern: setKreditkarten_2_2</H1></P><BR>");
              }
          }//End for
          return true;
      }//End else
  }//End setKreditkarten

  // -----------------------------------------------------------------------
  // Speichert alle Backup Einstellungen zurueck in die Tabelle 'backup'.
  // Es werden dazu zuerst alle alten Einstellungen geloescht und mit den neuen
  // ueberschrieben.
  // Argumente: Anzahl_Backups(INT), Backup_Intervall(INT), Komprimierung('Y'|'N'), Automatisierung('auto'|'cron'|'kein')
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion
  function setBackupSettings($Anzahl_Backups, $Backup_Intervall, $Komprimierung, $Automatisierung) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_setBackupSettings_1_1;
      global $sql_setBackupSettings_1_2;
      global $sql_setBackupSettings_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: setBackupSettings_1</H1></P><BR>");
      }
      else {
          //Loeschen der vorhandenen (alten) Backup Einstellungen
          $RS = $Admin_Database->Exec("$sql_setBackupSettings_1_1");
          if (!$RS){
             echo "<B>Query:</B> $sql_setBackupSettings_1_1<BR>";
             die("<P><H1 class='content'>S_A_Error: Konnte alte Backup Einstellungen nicht l&ouml;schen: setBackupSettings_2_1</H1></P><BR>");
          }

          //Abfuellen der Eingabewerte in Arrays zwecks besserer handhabung der Insert-SQLs
          $Backup_ID_array[0] = "Anzahl_Backups";
          $Backup_ID_array[1] = "Automatisierung";
          $Backup_ID_array[2] = "Backup_Intervall";
          $Backup_ID_array[3] = "Komprimierung";
          $Wert_array[0] = "$Anzahl_Backups";
          $Wert_array[1] = "$Automatisierung";
          $Wert_array[2] = "$Backup_Intervall";
          $Wert_array[3] = "$Komprimierung";

          //Einfuegen der neuen Backup Einstellungen
          for ($i=0;$i < count($Backup_ID_array);$i++) {
              $RS = $Admin_Database->Exec("$sql_setBackupSettings_1_2'".$Backup_ID_array[$i]."','".$Wert_array[$i]."'$sql_setBackupSettings_1_3");
              if (!$RS){
                  echo "<B>Query:</B> $sql_setBackupSettings_1_2'".$Backup_ID_array[$i]."','".$Wert_array[$i]."'$sql_setBackupSettings_1_3<BR>";
                  die("<P><H1 class='content'>S_A_Error: Konnte Backup Einstellungen nicht speichern: setBackupSettings_2_2</H1></P><BR>");
              }
          }//End for
          return true;
      }//End else
  }//End setBackupSettings

  // -----------------------------------------------------------------------
  // Erstellt das File index.php aus dem Template-file indextemplate.txt (Admin Verzeichnis)
  // Tags, welche im Templatefile in Doppelklammern << >> stehen, werden durch
  // die entsprechenden Eintraege in der Datenbank (Tabelle: cssfile) ersetzt.
  // Auch das Backup macht von dieser Funktion gebrauch.
  // Argumente: keine
  // Rueckgabewert: False, wenn ein Fehler aufgetreten ist
  function mkindexphp(){
    // am Anfang ist alles in Ordnung
    $ok = true;
    // index-templatefile "indextemplate.txt" zum lesen oeffnen
    $fp_src = fopen("indextemplate.txt","r");
    // index.php im Shophauptverzeichnis zum ueberschreiben oeffnen
    $fp_dest = fopen("../../index.php","w");

    // wenn index.php erfolgreich zum Schreiben geoeffnet werden konnte..
    if ($fp_dest <>0){
        // wenn indextemplate.txt erfolgreich zum Lesen geoeffnet werden konnte..
        if ($fp_src <>0){
            // solange EOF nicht erreicht ist..
            while ($zeile = fgets($fp_src,4096)){
                // Tags suchen, welche in Doppelklammern eingeschlossen sind (<<tag>>)
                preg_match_all("|<{2,2}(.*)>{2,2}|U", $zeile, $output);
                foreach($output[1] as $csstag){
                    $replace = "<<".$csstag.">>";
                    // CSS-Wert aus Datenbank auslesen!
                    $replacement = getcssarg($csstag); ;
                    // Tags in Doppelklammern durch den aus der Datenbank ausgelesenen
                    // Ausdruck ersetzen
                    $zeile = ereg_replace($replace, $replacement, $zeile);
                } // end foreach
                fputs($fp_dest, $zeile);
            } // end of while $zeile
            fclose ($fp_src);   // template-file schliessen
        } // end of if $fp_src
        else{
          echo "S_A_Error: Index-Template-Datei shop/Admin/indextemplate.txt konnte nicht gelesen werden! (mkindexphp)";
          $ok = false;
        }
        fclose ($fp_dest);  // css-file schliessen
    } // end of if $fp_dest

    else{
      echo "index.php konnte nicht zum schreiben geöffnet werden!";
      $ok = false;
    }
  // Varaible $ok zurueckgeben. Ist False, falls ein Fehler aufgetreten ist.
  return $ok;
  } // end of function mkindexphp

  // -----------------------------------------------------------------------
  // Diese Funktion speichert die Einstellungen der weiteren Zahlungsmethoden in der Tabelle zahlung_weiter ab.
  // ACHTUNG: Diese Funktion erledigt diese Aufgabe der Einfachheit halber mit folgenden Befehlen:
  // 1.) Alle Inhalte der Tabelle zahlung_weitere loeschen, 2.) Alle neuen Eintraege per SQL-INSERT einfuegen
  // Es muessen also immer alle Zahlungen im uebergebenen Objekt vorhanden sein! (z.B. wie in SHOP_SETTINGS.php)
  // Argumente: Allezahlungen-Objekt (siehe zahlung_def.php)
  // Rueckgabewert: true bei Erfolg, sonst Abbruch per die-Funktion
  function setAllezahlungen($myAllezahlungen) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_setAllezahlungen_1_1;
      global $sql_setAllezahlungen_1_2;
      global $sql_setAllezahlungen_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: setAllezahlungen</H1></P><BR>");
      }
      else {
          //Loeschen der vorhandenen (alten) Zahlungs-Einstellungen:
          $RS = $Admin_Database->Exec("$sql_setAllezahlungen_1_1");
          if (!$RS){
             echo "<B>Query:</B> $sql_setAllezahlungen_1_1<BR>";
             die("<P><H1 class='content'>S_A_Error: Konnte alte Einstellungen der weiteren Zahlungsmethoden nicht l&ouml;schen: sql_setAllezahlungen_1_1</H1></P><BR>");
          }

          //Einfuegen der neuen Backup Einstellungen (SQL-Insert):
          // Zuerst noch alle Parameter auslesen
          for ($i=0;$i < $myAllezahlungen->zahlungsanzahl();$i++) {
              $myParameterarray = $myAllezahlungen->Zahlungsarray[$i]->getallparameter();
              $RS = $Admin_Database->Exec("$sql_setAllezahlungen_1_2'".$myAllezahlungen->Zahlungsarray[$i]->Gruppe."','".$myAllezahlungen->Zahlungsarray[$i]->Bezeichnung.
                                          "','".$myAllezahlungen->Zahlungsarray[$i]->verwenden."','".$myAllezahlungen->Zahlungsarray[$i]->payment_interface_name.
                                          "','".$myParameterarray[0]."','".$myParameterarray[1].
                                          "','".$myParameterarray[2]."','".$myParameterarray[3].
                                          "','".$myParameterarray[4]."','".$myParameterarray[5].
                                          "','".$myParameterarray[6]."','".$myParameterarray[7].
                                          "','".$myParameterarray[8]."','".$myParameterarray[9]."'$sql_setAllezahlungen_1_3");
              if (!$RS) {
                  echo "<B>Query:</B> $sql_setAllezahlungen_1_2'".$myAllezahlungen->Zahlungsarray[$i]->Gruppe."','".$myAllezahlungen->Zahlungsarray[$i]->Bezeichnung.
                                          "','".$myAllezahlungen->Zahlungsarray[$i]->verwenden."','".$myAllezahlungen->Zahlungsarray[$i]->payment_interface_name.
                                          "','".$myParameterarray[0]."','".$myParameterarray[1].
                                          "','".$myParameterarray[2]."','".$myParameterarray[3].
                                          "','".$myParameterarray[4]."','".$myParameterarray[5].
                                          "','".$myParameterarray[6]."','".$myParameterarray[7].
                                          "','".$myParameterarray[8]."','".$myParameterarray[9]."'$sql_setAllezahlungen_1_3<BR>";
                  die("<P><H1 class='content'>S_A_Error: Konnte neue Einstellungen der weiteren Zahlungsmethoden nicht speichern: setAllezahlungen(_1_2 und _1_3-SQLs) Alle Einstellungen verloren -> Bitte oben notieren!</H1></P><BR>");
              }
          }//End for
          return true;
      }//End else
  }//End setAllezahlungen

  // -----------------------------------------------------------------------
  // Diese Funktion überbrüft, ob ein ihr übergebenes Artikelbild in mehreren Artikeln gebraucht wird, oder nur in einem
  // Argumente: Dateiname eines grossen Artikelbildes
  // Rueckgabewert: true falls das Bild mehrmals verwendet wird, false, falls es nur von einem Artikel gebraucht wird
  function bildmehrmals($bildname) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_getAlleArtikelvomBild_1_1;
      global $sql_getAlleArtikelvomBild_1_2;
      global $sql_getAlleArtikelvomBild_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)){
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: bild_mehrmals</H1></P><BR>");
      } // end of if
      else{
          $RS = $Admin_Database->Query($sql_getAlleArtikelvomBild_1_1.$sql_getAlleArtikelvomBild_1_2.addslashes($bildname).$sql_getAlleArtikelvomBild_1_3);
          if (!$RS) {
              echo "<B>Query:</B> ".$sql_getAlleArtikelvomBild_1_1.$sql_getAlleArtikelvomBild_1_2.addslashes($bildname).$sql_getAlleArtikelvomBild_1_3;
              die("<P><H1 class='content'>S_A_Error: Die Anzahl der Artikel, welche das Bild '$bildname' verwenden, konnte nicht ermittelt werden! </H1></P><BR>");
          } // end of if
          $count = $RS->GetRecordCount();
          return $count;
      }  // end of else
  } // end of function bild_mehrmals

  // -----------------------------------------------------------------------
  // Diese Funktion updated die Mehrwertsteuereinstellungen in der Tabelle mehrwertsteuer
  // welche es seit der Shopversion v.1.2 pro Artikel einstellbar gibt. Der Rueck-
  // gabewert ist true bei Erfolg, sonst Abbruch via die()-Funktion.
  // Argumente: array von MwSt-Objekten
  // Rueckgabewert: true bei Erfolg, sonst Abbruch via die()-Funktion
  function setmwstsettings($array_of_mwstsettings) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_setmwstsettings_1_1;
      global $sql_setmwstsettings_1_2;
      global $sql_setmwstsettings_1_3;
      global $sql_setmwstsettings_1_4;
      global $sql_setmwstsettings_1_4_1;
      global $sql_setmwstsettings_1_5;
      global $sql_setmwstsettings_1_6;
      global $sql_setmwstsettings_1_7;
      global $sql_setmwstsettings_1_8;
      global $sql_setmwstsettings_1_9;
      global $sql_setmwstsettings_1_10;
      global $sql_setmwstsettings_1_11;
      global $sql_setmwstsettings_1_12;
      global $sql_setmwstsettings_1_13;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: setmwstsettings</H1></P><BR>");
      }

      // Vergleich, welche MwSt-Settings wie veraendert wurden
      $array_mwstsettings_vorher = getmwstsettings(); //Auslesen der vorigen Einstellungen

      // Jeder bestehende Eintrag wird mit seinem neuen Pendant verglichen und dann
      // wird ein SQL-Operator festgelegt (UPDATE, INSERT oder DELETE)
      foreach($array_of_mwstsettings as $neu) {
          $setting_gefunden = false; // Flag, ob fuer den Eintrag im neuen Array schon ein alter Pendant gefunden wurde
          foreach($array_mwstsettings_vorher as $vorher) {
              $myMwSt = new MwSt();
              if ($neu->Mehrwertsteuer_ID == $vorher->Mehrwertsteuer_ID) {
                  // Wenn kein MwSt-Satz definiert ist, dem zu-loeschen-Array hinzufuegen
                  if ($neu->MwSt_Satz == "") {
                      $setting_gefunden = true;
                      // Objekt abpacken
                      $myMwSt->MwSt_Satz = $neu->MwSt_Satz;
                      $myMwSt->MwSt_default_Satz = $neu->MwSt_default_Satz;
                      $myMwSt->Beschreibung = $neu->Beschreibung;
                      $myMwSt->Preise_inkl_MwSt = $neu->Preise_inkl_MwSt;
                      $myMwSt->Mehrwertsteuer_ID = $neu->Mehrwertsteuer_ID;
                      $myMwSt->Positions_Nr = $neu->Positions_Nr;
                      // ...und in Array ablegen
                      $deletearray[] = $myMwSt;
                  }// End if
                  // ...sonst den Eintrag updaten
                  else {
                      $setting_gefunden = true;
                      // Objekt abpacken
                      $myMwSt->MwSt_Satz = $neu->MwSt_Satz;
                      $myMwSt->MwSt_default_Satz = $neu->MwSt_default_Satz;
                      $myMwSt->Beschreibung = $neu->Beschreibung;
                      $myMwSt->Preise_inkl_MwSt = $neu->Preise_inkl_MwSt;
                      $myMwSt->Mehrwertsteuer_ID = $neu->Mehrwertsteuer_ID;
                      $myMwSt->Positions_Nr = $neu->Positions_Nr;
                      // ...und in Array ablegen
                      $updatearray[] = $myMwSt;
                  }// End else
              }// End if
          }// End foreach vorher
          // Es gibt keinen vorherigen Eintrag (Mehrwertsteuer_ID = 0)
          // Nun muss ueberprueft werden, ob es sich um ein leeres Feld handelt
          // -> nichts machen, oder um einen Neueintrag, dann ist ein MwSt-Satz definiert
          // dieser muss dann mit einem Insert in die DB eingefuegt werden.
          if ($neu->MwSt_Satz != "" && $setting_gefunden == false) {
              // Objekt abpacken
              $myMwSt->MwSt_Satz = $neu->MwSt_Satz;
              $myMwSt->MwSt_default_Satz = $neu->MwSt_default_Satz;
              $myMwSt->Beschreibung = $neu->Beschreibung;
              $myMwSt->Preise_inkl_MwSt = $neu->Preise_inkl_MwSt;
              $myMwSt->Mehrwertsteuer_ID = $neu->Mehrwertsteuer_ID;
              $myMwSt->Positions_Nr = $neu->Positions_Nr;
              // ...und in Array ablegen
              $insertarray[] = $myMwSt;
          }// End if
      }// End foreach neu

      // Jetzt gehts ans ausfuehren der entsprechenden SQLs der drei Arrays:
      // 1.) Updates durchfuehren
      if (count($updatearray) > 0) {
          foreach($updatearray as $value) {
              $RS = $Admin_Database->Exec($sql_setmwstsettings_1_1.$value->MwSt_Satz.$sql_setmwstsettings_1_2.$value->Beschreibung.$sql_setmwstsettings_1_3.$value->MwSt_default_Satz.$sql_setmwstsettings_1_4.$value->Preise_inkl_MwSt.$sql_setmwstsettings_1_4_1.$value->Positions_Nr.$sql_setmwstsettings_1_5.$value->Mehrwertsteuer_ID.$sql_setmwstsettings_1_6);
              if (!$RS){
                 echo "<B>Query:</B> $sql_setmwstsettings_1_1".$value->MwSt_Satz."$sql_setmwstsettings_1_2".$value->Beschreibung.
                      "$sql_setmwstsettings_1_3".$value->MwSt_default_Satz."$sql_setmwstsettings_1_4".$value->Preise_inkl_MwSt.
                      "$sql_setmwstsettings_1_4_1".$value->Positions_Nr."$sql_setmwstsettings_1_5".$value->Mehrwertsteuer_ID."<BR>";
                 die("<P><H1 class='content'>S_A_Error: Fehler beim UPDATE der Tabelle mehrwertsteuer: setmwstsettings: Eintraege updaten</H1></P><BR>");
              }// End if
          }// End foreach
      }// End if

      // 2.) Inserts durchfuehren
      if (count($insertarray) > 0) {
          foreach($insertarray as $value) {
              $RS = $Admin_Database->Exec($sql_setmwstsettings_1_7.$value->MwSt_Satz.$sql_setmwstsettings_1_8.$value->Beschreibung.$sql_setmwstsettings_1_9.$value->MwSt_default_Satz.$sql_setmwstsettings_1_10.$value->Preise_inkl_MwSt.$sql_setmwstsettings_1_11.$value->Positions_Nr.$sql_setmwstsettings_1_12);
              if (!$RS){
                 echo "<B>Query:</B> ".$sql_setmwstsettings_1_7.$value->MwSt_Satz.$sql_setmwstsettings_1_8.$value->Beschreibung.$sql_setmwstsettings_1_9.$value->MwSt_default_Satz.$sql_setmwstsettings_1_10.$value->Preise_inkl_MwSt.$sql_setmwstsettings_1_11.$value->Positions_Nr.$sql_setmwstsettings_1_12." <BR>";
                 die("<P><H1 class='content'>S_A_Error: Fehler beim UPDATE der Tabelle mehrwertsteuer: setmwstsettings: Eintraege hinzufuegen</H1></P><BR>");
              }// End if
          }// End foreach
      }// End if

      // 3.) Deletes durchfuehren
      if (count($deletearray) > 0) {
          foreach($deletearray as $value) {
              $RS = $Admin_Database->Exec($sql_setmwstsettings_1_13.$value->Mehrwertsteuer_ID);
              if (!$RS){
                 echo "<B>Query:</B>".$sql_setmwstsettings_1_13.$value->Mehrwertsteuer_ID."<BR>";
                 die("<P><H1 class='content'>S_A_Error: Fehler beim UPDATE der Tabelle mehrwertsteuer: setmwstsettings: Eintraege loeschen</H1></P><BR>");
             }// End if
          }// End foreach
      }// End if
      return true;
  }//End setmwstsettings

  // -----------------------------------------------------------------------
  // Diese Funktion updated die Attribute MwStpflichtig und MwStNummer in der
  // Tabelle shop_settings. Wenn der Shop als NICHT MwSt-pflichtig gesetzt wird
  // so wird automatisch die MwST-Nummer auf 0 gesetzt.
  // Der Shop wird als NICHT MwSt-Pflichtig konfiguriert, wenn die MwSt-Nummer = 0 ist.
  // Dies alles, weil es ein Tabellenattribut MwStpflichtig gibt (Y/N) UND eine MwSt-Nummer.
  // Argumente: Mwst-Nummer (String)
  // Rueckgabewert: true bei Erfolg, sonst Abbruch via die()-Funktion
  function setmwstnr($MwStNr) {

      // Sichtbarmachen von Variablen aus anderen Modulen
      global $Admin_Database;
      global $sql_setmwstnr_1_1;
      global $sql_setmwstnr_1_2;
      global $sql_setmwstnr_1_3;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1 class='content'>S_A_Error: Datenbank nicht erreichbar: setmwstnr</H1></P><BR>");
      }

      // Entscheidung ob MwStpflichtig oder nicht (MwSt-Nummer = 0?)
      if ($MwStNr == "0") {
          // Shop ist nicht (mehr) MwSt-Pflichtig. Attribute updaten
          $RS = $Admin_Database->Exec($sql_setmwstnr_1_1.$MwStNr.$sql_setmwstnr_1_2."N".$sql_setmwstnr_1_3);
          //Fehlerbehandlung
          if (!$RS) {
              echo "Query: ".$sql_setmwstnr_1_1.$MwStNr.$sql_setmwstnr_1_2."N".$sql_setmwstnr_1_3."<br>";
              die("<B><U>S_A_Error:RS ist nicht true->Abbruch (setmwstnr) -> Attribute updaten (nicht mehr MwSt-pflichtig)</U></B><BR><BR>");
          }
      }
      else {
          // Shop wird/ist MwSt-Pflichtig, Attribute entsprechend updaten
          $RS = $Admin_Database->Exec($sql_setmwstnr_1_1.$MwStNr.$sql_setmwstnr_1_2."Y".$sql_setmwstnr_1_3);
          //Fehlerbehandlung
          if (!$RS) {
              echo "Query: ".$sql_setmwstnr_1_1.$MwStNr.$sql_setmwstnr_1_2."N".$sql_setmwstnr_1_3."<br>";
              die("<B><U>S_A_Error:RS ist nicht true->Abbruch (setmwstnr) -> Attribute updaten (wird MwSt-pflichtig)</U></B><BR><BR>");
          }
      }
      //Bei Erfolg: Rueckgabewert = true
      return true;
  } // End setmwstnr

  // -----------------------------------------------------------------------
  // Zweck: Die Eigenschaften (Attribute) einer Haupt- oder Unterkategorie abspeichern.
  // Falls die Kategorie Unterkategorien besitzt werden auch deren
  // Attribut Unterkategorie_von upgedated.
  // Argumente: Kategorie Objekt (der Unterkategorien Array wird nicht benutzt)
  // Rueckgabewert: true oder Abbruch per die-Funktion
  function setKategorie($myKategorie) {
      // Einbinden von in anderen Modulen definierten Variablen
      global $Admin_Database;
      global $sql_setKategorie_1_1;
      global $sql_setKategorie_1_2;
      global $sql_setKategorie_1_3;
      global $sql_setKategorie_1_4;
      global $sql_setKategorie_1_5;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar (setKategorie)</H1></P>\n");
      }
      else {
          // Kategorienamen und die Unterkategorie_von der entsprechenden Unterkategorien updaten
          if(!umbenennenKategorie($myKategorie->Kategorie_ID, $myKategorie->Name)) {
              die("<P><H1>S_A_Error: Konnte die Kategorienamen nicht updaten (setKategorie)</H1></P>\n");
          };
          // Alle Attribute der Tabelle Kategorie updaten (vorlaeufig nur Name, Beschreibung, MwSt_Satz)
          $RS = $Admin_Database->Exec("$sql_setKategorie_1_1".$myKategorie->Name."$sql_setKategorie_1_2".$myKategorie->Beschreibung."$sql_setKategorie_1_3".$myKategorie->MwSt_Satz."$sql_setKategorie_1_4".$myKategorie->Details_anzeigen."$sql_setKategorie_1_5".$myKategorie->Kategorie_ID);
          //Fehlerbehandlung
          if (!$RS) {
              echo "Query: $sql_setKategorie_1_1".$myKategorie->Name."$sql_setKategorie_1_2".$myKategorie->Beschreibung."$sql_setKategorie_1_3".$myKategorie->MwSt_Satz."$sql_setKategorie_1_4".$myKategorie->Details_anzeigen."$sql_setKategorie_1_5".$myKategorie->Kategorie_ID."<br>";
              die("<B><U>S_A_Error:RS ist nicht true->Abbruch (setKategorie) -> Kategorieattribute updaten</U></B><BR><BR>");
          }
          //Bei Erfolg: Rueckgabewert = true
          return true;
      }
  }//End function setKategorie

  // -----------------------------------------------------------------------
  // Die MwSt-default-Saetze von Kategorien (und ihren Unterkategorien) updaten
  // Weiter werden auch alle MwSt-Saetze der in den Kat/Ukat enthaltenen Artikel
  // upgedated. Wird eine Hauptkategorie angegeben, so werden die enthaltenen Unterkategorien
  // auch gleich mitupgedated!
  // Argumente: Array mit Kategorie_IDs als Index und jeweils zu uebernehmender MwSt-Satz als Wert
  // Rueckgabewert: Array mit den vorformatierten Namen der upgedateten Kategorienamen oder Abbruch per die-Funktion
  function setKatmwst($Kategorie_IDarray) {
      // Einbinden von in anderen Modulen definierten Variablen
      global $Admin_Database;
      global $sql_setKatmwst_1_1;
      global $sql_setKatmwst_1_2;
      global $sql_setKatmwst_1_3;
      global $sql_setKatmwst_1_4;
      global $sql_setKatmwst_1_5;

      // Initialisierung des Rueckgabewerts
      $changedkatarray = array();

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar (setKatmwst)</H1></P>\n");
      }
      else {
          // Kategorienamen und die Unterkategorie_von der entsprechenden Unterkategorien updaten
          foreach ($Kategorie_IDarray as $key=>$wert) {
              // Alle allenfalls existierenden Unterkategorien der aktuell bearbeiteten Kategorie holen
              $Kategorie = getKategorie($key);
              $value = $key; // Kategorie_ID
              $MwSt_Satz = $wert;

              // 1.) MwSt-default-Satz der Haupt- /Unterategorie updaten
              $RS = $Admin_Database->Exec($sql_setKatmwst_1_1.$MwSt_Satz.$sql_setKatmwst_1_2.$value);
              //Fehlerbehandlung
              if (!$RS) {
                  echo "<i>Query:</i> ".$sql_setKatmwst_1_1.$MwSt_Satz.$sql_setKatmwst_1_2.$value."<br>";
                  die("<B><U>S_A_Error:RS ist nicht true->Abbruch (setKatmwst) -> beim Haupt-/Unterkategorie-MwSt updaten</U></B><BR><BR>");
              }// End if
              // Damit Unterkategorien besser ersichtlich formatiert werden koennen, muss hier ueberprueft werden, ob es sich um
              // eine direkt angegebene Unterkategorie handelt. Dann wird noch zusaetzlich die Parent-Kategorie ausgegeben.
              if ($Kategorie->Unterkategorie_von != "") {
                  $changedkatarray[] = "&nbsp;&nbsp;- <small>".$Kategorie->Name." ($MwSt_Satz%)</small>"; //Protokollieren, welche Hauptkategorie veraendert wurde
              }
              else {
                  $changedkatarray[] = "<i>".$Kategorie->Name."</i> ($MwSt_Satz%)"; //Protokollieren, welche Hauptkategorie veraendert wurde
              }

              // 2.) Artikel-IDs der in der Kategorie enthaltenen Artikel auslesen und dann die Artikel der (Haupt)kategorie updaten
              // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
              $ArtikelIDarray = array();
              $RS = $Admin_Database->Query($sql_setKatmwst_1_3.$value);
              while (is_object($RS) && $RS->NextRow()){
                  $ArtikelIDarray[] = $RS->GetField("FK_Artikel_ID");
              }// End while

              if (count($ArtikelIDarray) > 0) { //Wenn die Kategorie keine Artikel hat - diesen Teil ueberspringen
                  foreach ($ArtikelIDarray as $Artikel_ID) {
                      $RS = $Admin_Database->Exec($sql_setKatmwst_1_4.$MwSt_Satz.$sql_setKatmwst_1_5.$Artikel_ID);
                      //Fehlerbehandlung
                      if (!$RS) {
                          echo "<i>Query:</i> ".$sql_setKatmwst_1_4.$MwSt_Satz.$sql_setKatmwst_1_5.$Artikel_ID."<br>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (setKatmwst) -> beim Artikel der Hauptkategorie - MwSt updaten</U></B><BR><BR>");
                      }// End if
                  }// End foreach $Artikelarray
              }// End if count
/*
              // 3.) Allfaellige Unterkategorien MwSt-default-Saetze und auch gleich ihre Artikel updaten
              if ($Ukatanzahl > 0) {
                  foreach ($Ukatarray as $Ukat) {
                      // Jetzt das selbe Spiel wie fuer die Hauptkategorie fuer jede Unterkategorie auch (Kat-upd, dann Art-upd):
                      $RS = $Admin_Database->Exec($sql_setKatmwst_1_1.$MwSt_Satz.$sql_setKatmwst_1_2.$Ukat->Kategorie_ID);
                      //Fehlerbehandlung
                      if (!$RS) {
                          echo "<i>Query:</i> ".$sql_setKatmwst_1_1.$MwSt_Satz.$sql_setKatmwst_1_2.$Ukat->Kategorie_ID."<br>";
                          die("<B><U>S_A_Error:RS ist nicht true->Abbruch (setKatmwst) -> beim Unterkategorie (".$Ukat->Name.")-MwSt updaten</U></B><BR><BR>");
                      }// End if

                      // Artikel-IDs der in der Unterkategorie enthaltenen Artikel auslesen und dann die Artikel dieser Kategorie updaten
                      // Query ausfuehren und in ResultSet schreiben (Typ ResultSet (RS), siehe database.php)
                      $ArtikelIDarray = array();
                      $RS = $Admin_Database->Query($sql_setKatmwst_1_3.$Ukat->Kategorie_ID);
                      while (is_object($RS) && $RS->NextRow()){
                          $ArtikelIDarray[] = $RS->GetField("FK_Artikel_ID");
                      }// End while

                      if (count($ArtikelIDarray) > 0) { //Wenn die Kategorie keine Artikel hat - diesen Teil ueberspringen
                          foreach ($ArtikelIDarray as $Artikel_ID) {
                              $RS = $Admin_Database->Exec($sql_setKatmwst_1_4.$MwSt_Satz.$sql_setKatmwst_1_5.$Artikel_ID);
                              //Fehlerbehandlung
                              if (!$RS) {
                                  echo "<i>Query:</i> ".$sql_setKatmwst_1_4.$MwSt_Satz.$sql_setKatmwst_1_5.$Artikel_ID."<br>";
                                  die("<B><U>S_A_Error:RS ist nicht true->Abbruch (setKatmwst) -> beim Artikel der Unterkategorie (".$Ukat->Name.") - MwSt updaten</U></B><BR><BR>");
                              }// End if !RS
                          }// End foreach $Artikelarray
                      }// End if count $ArtikelIDarray
                      $changedkatarray[] = " - ".$Ukat->Name; //Protokollieren, welche Unterkategorie veraendert wurde
                  }// End foreach $Ukatarray
              }// End if $Ukatanzahl
*/
          }// End foreach $KategorieIDarray

          //Bei Erfolg: Rueckgabewert = true
          return $changedkatarray;
      }// End else
  }//End function setKatmwst

  // -----------------------------------------------------------------------
  // Zweck: Ersetzt shopweit bei allen Kategorien/Unterkategorien/Artikel mit
  // dem MwSt-Satz $alt_mwst den Mehrwertsteuersatz mit dem Satz in $neu_mwst.
  // Argumente: Double Zahl (zu ersetzender MwSt-Satz), Double Zahl (neuer MwSt-Satz)
  // Rueckgabewert: true oder Abbruch per die-Funktion
  function updatewithmwst($alt_mwst, $neu_mwst) {
      // Einbinden von in anderen Modulen definierten Variablen
      global $Admin_Database;
      global $sql_updatewithmwst_1_1;
      global $sql_updatewithmwst_1_2;
      global $sql_updatewithmwst_1_3;
      global $sql_updatewithmwst_1_4;

      // Test ob die Datenbank erreichbar ist
      if (! is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar (updatewithmwst)</H1></P>\n");
      }
      else {
          // Betroffene Kategorien/Unterkategorien einen neuen MwSt-Satz zuweisen
          $RS = $Admin_Database->Exec($sql_updatewithmwst_1_1.$neu_mwst.$sql_updatewithmwst_1_2.$alt_mwst);
          //Fehlerbehandlung
          if (!$RS) {
              echo "<b>Query:</b> ".$sql_updatewithmwst_1_1.$neu_mwst.$sql_updatewithmwst_1_2.$alt_mwst."<br>";
              die("<B><U>S_A_Error:Abbruch weil MwSt-Update nicht durchgef&uuml;hrt werden konnte (updatewithmwst) -> Haupt- und Unterkategorien</U></B><BR><BR>");
          }
          // Betroffene Artikel einen neuen MwSt-Satz zuweisen
          $RS = $Admin_Database->Exec($sql_updatewithmwst_1_3.$neu_mwst.$sql_updatewithmwst_1_4.$alt_mwst);
          //Fehlerbehandlung
          if (!$RS) {
              echo "<b>Query:</b> ".$sql_updatewithmwst_1_3.$neu_mwst.$sql_updatewithmwst_1_4.$alt_mwst."<br>";
              die("<B><U>S_A_Error:Abbruch weil MwSt-Update nicht durchgef&uuml;hrt werden konnte (updatewithmwst) -> Artikel</U></B><BR><BR>");
          }

          //Bei Erfolg: Rueckgabewert = true
          return true;
      }
  }//End function updatewithmwst

  // -----------------------------------------------------------------------
  // Gibt auf Grund einer Bestellungs_ID die dazugehoerende Bestellung zurueck
  // Argument: Bestellungs_ID (Integer)
  // Rueckgabewert: Eine Bestellung als Bestellungs-Objekt (Definition siehe <shopdir>/shop/bestellung_def.php)
  function IDgetBestellung($Bestellungs_ID) {

    //Einbinden von in anderen Modulen deklarierten Variablen
    global $Admin_Database;
    global $sql_IDgetBestellung_1_1;
    global $sql_IDgetBestellung_1_2;
    global $sql_IDgetBestellung_1_3;

    // Test ob die Datenbank erreichbar ist
    if (! is_object($Admin_Database)) {
        die("<h3 class='content'>shop_kunden_mgmt_func_Error: Datenbank nicht erreichbar (IDgetBestellung)</h3>\n");
    }
    else {
          // Query ausfuehren und in ResultSet schreiben (Typ ResultSet, siehe database.php)
          $RS = $Admin_Database->Query($sql_IDgetBestellung_1_1.$Bestellungs_ID.$sql_IDgetBestellung_1_2);

          // Test ob keine Resultate zurueck gekommen sind, dann Versuch nur eine Bestellung OHNE Artikel einzulesen
          $noartikel = false;// Flag setzen, dass nicht versucht wird Artikel einzulesen
          if ($RS->GetRecordCount() == 0) {
              unset($RS);
              $RS = $Admin_Database->Query($sql_IDgetBestellung_1_3.$Bestellungs_ID);
              $noartikel = true; // Flag setzen
          }

          // Auslesen:
          $myBestellung = new Bestellung; //Ein neues Bestellungs-Objekt instanzieren
          $art_counter = 1; //Counter um Artikel einer Bestellung zu zaehlen (=Array-Key)
          while (is_object($RS) && $RS->NextRow()){
              $myArtikel_info = new Artikel_info; //Ein neues Artikel_info-Objekt instanzieren (Def. siehe bestellung_def.php)
              // Bestellung einlesen:
              $myBestellung->Bestellungs_ID = $RS->GetField("Bestellungs_ID");
              $myBestellung->Session_ID = $RS->GetField("Session_ID");
              $myBestellung->Referenz_Nr = bestellungs_id_to_ref($myBestellung->Bestellungs_ID); // Funktion siehe bestellung_def.php
              $myBestellung->Bestellung_abgeschlossen = $RS->GetField("Bestellung_abgeschlossen");
              $myBestellung->Anrede = $RS->GetField("Anrede");
              $myBestellung->Firma = $RS->GetField("Firma");
              $myBestellung->Abteilung = $RS->GetField("Abteilung");
              $myBestellung->Vorname = $RS->GetField("Vorname");
              $myBestellung->Name = $RS->GetField("Name");
              $myBestellung->Adresse1 = $RS->GetField("Adresse1");
              $myBestellung->Adresse2 = $RS->GetField("Adresse2");
              $myBestellung->PLZ = $RS->GetField("PLZ");
              $myBestellung->Ort = $RS->GetField("Ort");
              $myBestellung->Land = $RS->GetField("Land");
              $myBestellung->Telefon = $RS->GetField("Telefon");
              $myBestellung->Email = $RS->GetField("Email");
              $myBestellung->Datum = $RS->GetField("Datum");
              $myBestellung->Endpreis = $RS->GetField("Endpreis");
              $myBestellung->Bezahlungsart = $RS->GetField("Bezahlungsart");
              $myBestellung->Anmerkung = $RS->GetField("Anmerkung");
              $myBestellung->Versandkosten = $RS->GetField("Versandkosten");
              $myBestellung->Mindermengenzuschlag = $RS->GetField("Mindermengenzuschlag");
              $myBestellung->Rechnungsbetrag = $RS->GetField("Rechnungsbetrag");
              $myBestellung->Bestellung_ausgeloest = $RS->GetField("Bestellung_ausgeloest");
              $myBestellung->Bestellung_bezahlt = $RS->GetField("Bestellung_bezahlt");
              // Info des Artikels in ein Artikel_info-Objekt ablegen (Def. siehe bestellung_def.php)
              $myArtikel_info->Artikel_ID = $RS->GetField("FK_Artikel_ID");
              $myArtikel_info->Artikel_Nr = $RS->GetField("Artikel_Nr");
              $myArtikel_info->Name = $RS->GetField("Artikelname");
              $myArtikel_info->Anzahl = $RS->GetField("Anzahl");
              $myArtikel_info->Preis = $RS->GetField("Preis");
              $myArtikel_info->Gewicht = $RS->GetField("Gewicht");
              $myArtikel_info->Zusatzfelder = explode("þ",$RS->GetField("Zusatztexte"));

              // Je ein Variationstext und danach die Preisdifferenz werden per explode der Reihe
              // nach in den temporaeren Array $vararry abgelegt. Trennzeichen = Alt + 0254
              $vararray = array();
              $vararray = explode("þ",$RS->GetField("Variation"));
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
              if (!$noartikel) {
                  $myBestellung->putartikel($art_counter,$myArtikel_info);
              }
         }//End while
         return $myBestellung;
      }//End else
  }//End of function IDgetBestellung


  // -----------------------------------------------------------------------
  // Veraendert die Groesse eines Bildes. Die Funktion wird unter anderem bei der Erzeugung von Thumbnails
  // eingesetzt und zum Test der GD-Library. Das angegebene Bild wird je nach GD-Library Version
  // mit verschiedener Qualitaet in seiner Groesse veraendert. Wird das optionale $override Flag
  // auf true gesetzt, so werden die 'alten Funktionen' verwendet. Bei GIF-Bildern soll man dieses
  // Flag auf true setzen, weil hier bei der GD-Library nicht die TrueColor Funktion benutzt werden
  // koennen. Das letzte optionale Argument $test steuert die Funktionalitaet dieser Funktion. Wenn es
  // = true ist, so kann mit Hilfe dieser Funktion eine GD-Library Erkennung (<2 oder >=2) durchge-
  // fuehrt werden. Es wird dann als Rueckgabewert der Integer 1.x oder 2.x zurueckgegeben. Das Bild wird
  // NICHT verarbeitet. Per default ist $test=false ($test=true, wird in SHOP_KONFIGURATION.php benutzt).
  // Argumente: $altesBild = Image Resource des Bildes, wessen Groesse veraendert werden muss,
  //            $neueBreite, $neueHoehe, $alteBreite, $alteHoehe (Integers), $override (Boolean), $test (Boolean)
  // Rueckgabewert: Das Bild in der neuen Groesse (Image Resource) oder false bei Fehler oder 1|2 (Integer) bei $test=true
  function resize_image($altesBild, $neueBreite, $neueHoehe, $alteBreite, $alteHoehe, $override=false, $test=false) {
      // Initialisierung des GD-Library Erkennungsflags. Annahme GD >= v.2.0.x
      $GD_Version = 2;

      // Test ob wir ein ImageCreateTrueColor benutzen koennen (erst ab GD v.2.0.x)
      if (function_exists("gd_info") || substr(trim(get_gdlibrary_info("short")),0,1) == "2") {
          // GD-Library v.2.0.x vorhanden
          $neuesBild = imageCreateTrueColor($neueBreite, $neueHoehe);
      }
      else {
          // GD-Library v.1.x vorhanden
          $neuesBild == "false";
      }
      if ($override || !$neuesBild) {
          // Wir haben es mit einer GD v.1.x zu tun, alte Methoden verwenden und Flag setzen
          $neuesBild=ImageCreate($neueBreite,$neueHoehe);
          $GD_Version = 1;
      }

      // Je nach GD-Version wird nun die Groesse des Bildes veraendert. Resample ist besser.
      if ($GD_Version >= 2) {
          ImageCopyResampled($neuesBild,$altesBild,0,0,0,0,$neueBreite,$neueHoehe,$alteBreite,$alteHoehe);
      }
      else {
          ImageCopyResized($neuesBild,$altesBild,0,0,0,0,$neueBreite,$neueHoehe,$alteBreite,$alteHoehe);
      }

      // Test ob ueberhaupt ein neues Bild erstellt wurde
      if (!$neuesBild) {
          $neuesBild = false;
      }

      if ($test == true) {
          return $GD_Version;
      }
      else {
          return $neuesBild;
      }
  }// End function resize_image

  // -----------------------------------------------------------------------
  // Gibt Auskunft ueber die installierte GD-Library Version. Wenn keine GD-Library installiert ist, wird als
  // Antwort ein leerer Array (mit nur dem optional kuenstlich hinzugefuegten Element my_gd_info = 1) zurueckgegeben.
  // Andernfalls ist der assoziative Array gefuellt mit Informationen ueber die installierte GD-Library.
  // Das optionale Argument $formatting kann entweder mit "short" dazu gezwungen werden nur die Version der GD
  // auszugeben, oder mit "verbose" (default) alle Ausgaben zurueckzugeben.
  // Argumente: $formatting (String (= Enum("short", "verbose"))
  // Rueckgabewert: GD-Version (String) oder assoz. Array mit GD-Informationen (Array)
  function get_gdlibrary_info($formatting="verbose") {
      // Test ob es GD-Library v.1.xx oder v.2.xx ist:
      // Ab PHP 4.3.0 kann man die integriert Funktion gd_info() benutzen, vorher muss man sich eines
      // Tricks behelfen. Quelle: http://www.php.net/manual/en/function.gd-info.php Kommentar von johnschaefer@gmx.de
      // Via eval fuehren wir eine zuvor definierte Funktion aus, welche via Output-Buffering die GD-Library
      // Informationen aus einem phpinfo()-Aufruf extrahiert und in einen Array abpackt.
      $code = 'function my_gd_info() {
               $array = Array(
                   "GD Version" => "",
                   "FreeType Support" => 0,
                   "FreeType Support" => 0,
                   "FreeType Linkage" => "",
                   "T1Lib Support" => 0,
                   "GIF Read Support" => 0,
                   "GIF Create Support" => 0,
                   "JPG Support" => 0,
                   "PNG Support" => 0,
                   "WBMP Support" => 0,
                   "XBM Support" => 0
               );
               $gif_support = 0;

               ob_start();
                   eval("phpinfo();");
                   $info = ob_get_contents();
               ob_end_clean();

               foreach(explode("\n", $info) as $line) {
                   if(strpos($line, "GD Version")!==false)
                       $array["GD Version"] = trim(str_replace("GD Version", "", strip_tags($line)));
                   if(strpos($line, "FreeType Support")!==false)
                       $array["FreeType Support"] = trim(str_replace("FreeType Support", "", strip_tags($line)));
                   if(strpos($line, "FreeType Linkage")!==false)
                       $array["FreeType Linkage"] = trim(str_replace("FreeType Linkage", "", strip_tags($line)));
                   if(strpos($line, "T1Lib Support")!==false)
                       $array["T1Lib Support"] = trim(str_replace("T1Lib Support", "", strip_tags($line)));
                   if(strpos($line, "GIF Read Support")!==false)
                       $array["GIF Read Support"] = trim(str_replace("GIF Read Support", "", strip_tags($line)));
                   if(strpos($line, "GIF Create Support")!==false)
                       $array["GIF Create Support"] = trim(str_replace("GIF Create Support", "", strip_tags($line)));
                   if(strpos($line, "GIF Support")!==false)
                       $gif_support = trim(str_replace("GIF Support", "", strip_tags($line)));
                  if(strpos($line, "JPG Support")!==false)
                       $array["JPG Support"] = trim(str_replace("JPG Support", "", strip_tags($line)));
                  if(strpos($line, "PNG Support")!==false)
                       $array["PNG Support"] = trim(str_replace("PNG Support", "", strip_tags($line)));
                  if(strpos($line, "WBMP Support")!==false)
                       $array["WBMP Support"] = trim(str_replace("WBMP Support", "", strip_tags($line)));
                  if(strpos($line, "XBM Support")!==false)
                       $array["XBM Support"] = trim(str_replace("XBM Support", "", strip_tags($line)));
               }

               if($gif_support==="enabled") {
                   $array["GIF Read Support"]   = 1;
                   $array["GIF Create Support"] = 1;
               }

               if($array["FreeType Support"]==="enabled"){
                   $array["FreeType Support"] = 1;
               }

               if($array["T1Lib Support"]==="enabled")
                   $array["T1Lib Support"] = 1;

               if($array["GIF Read Support"]==="enabled"){
                   $array["GIF Read Support"] = 1;    }

               if($array["GIF Create Support"]==="enabled")
                   $array["GIF Create Support"] = 1;

               if($array["JPG Support"]==="enabled")
                  $array["JPG Support"] = 1;

               if($array["PNG Support"]==="enabled")
                   $array["PNG Support"] = 1;

               if($array["WBMP Support"]==="enabled")
                   $array["WBMP Support"] = 1;

               if($array["XBM Support"]==="enabled")
                   $array["XBM Support"] = 1;

               $array["my_gd_info"] = "1";

               return $array;
               }';
      // Je nach vorhandener Funktion wird gd_info() oder der Trick mit eval und my_gd_info() verwendet
      // Im Array $gd_info sind nun alle Informationen ueber die GD-Library gespeichert.
      $gd_info = array();
      if(!function_exists("gd_info")) {
          if(!function_exists("my_gd_info")) {
              eval($code);
          }
          $gd_info = my_gd_info();
      }
      else {
          $gd_info = gd_info();
      };
      // Auswertung ob nur die Version der GD-Library gefragt ist oder gleich der ganze Informationsarray
      if ($formatting == "short") {
          return $gd_info["GD Version"];
      }
      else {
          return $gd_info;
      }
  }// End function get_gdlibrary_info

  // -----------------------------------------------------------------------
  // Gibt Auskunft ueber die PHP-Konfiguration - Parsing der phpinfo-Ausgabe.
  // Mit dem Argument $parameternamen, kann man den Namen eines Parameters angeben
  // welchen man gerne ausgelesen haette. Da es pro 'Suchangabe' -> Parameternamen
  // z.T. mehrere Ergebnisse geben kann, werden im zurueckgegebenen assoziativen Array
  // dem Indexwort $parametername noch ein Zaehler mit der Syntax 'x) ' vorangestellt.
  // x ist dabei ein Zaehler. Bsp: '1.) error_reporting' waere ein Key fuer 'error_reporting'.
  // Argumente: $parametername (String)
  // Rueckgabewert: Assoziativer Array mit dem Parameternamen als Key und dem entsprechenden Wert
  function get_phpinfo_value($parametername) {
      // Initialisierung
      $zielarray = array();
      $counter = 1;

      // Wenn kein Parameter uebergeben wurde, Abbruch und Rueckgabe eines leeren Arrays
      if ($parametername == "") {
          return $zielarray;
      }

      // Via Output Buffering lesen wir die Ausgabe der phpinfo()-Funktion in einen Buffer ein.
      ob_start();
          eval("phpinfo();");
          $info = ob_get_contents();
      ob_end_clean();

      foreach(explode("\n", $info) as $line) {
          if(strpos($line, $parametername)!==false) {
              $zielarray[$counter.") ".$parametername] = trim(str_replace($parametername,"",strip_tags(str_replace("</td><td class=\"v\">","þ",str_replace("</td><td align=\"center\">","þ",$line)))));
              // Ersetzungen von offensichtlich verwirrenden Antworten
              if ($zielarray[$counter.") ".$parametername] == "þOnþOn" || $zielarray[$counter.") ".$parametername] == "OnOn" ) {
                  $zielarray[$counter.") ".$parametername] = "Local = On, Master = On";
              }
              else if ($zielarray[$counter.") ".$parametername] == "þOnþOff" || $zielarray[$counter.") ".$parametername] == "OnOff") {
                  $zielarray[$counter.") ".$parametername] = "Local = On, Master = Off";
              }
              else if ($zielarray[$counter.") ".$parametername] == "þOffþOn" || $zielarray[$counter.") ".$parametername] == "OffOn") {
                  $zielarray[$counter.") ".$parametername] = "Local = Off, Master = On";
              }
              else if ($zielarray[$counter.") ".$parametername] == "þOffþOff" || $zielarray[$counter.") ".$parametername] == "OffOff") {
                  $zielarray[$counter.") ".$parametername] = "Local = Off, Master = Off";
              }
              else {
                  // Testen, ob es zwei Werte gibt, die gleich sind (Durch þ getrennte gleiche Strings)
                  // (þ-Zeichen = ALT + 0254). Wenn ja, so wird das Wort Local und Master davor gehaehngt
                  $temparray = array(); // Initialisierung
                  $temparray = explode("þ",$zielarray[$counter.") ".$parametername]);
                  if (count($temparray) == 3) {
                      $zielarray[$counter.") ".$parametername] = "Local = ".$temparray[1].", Master = ".$temparray[2];
                  }
                  else {
                      $zielarray[$counter.") ".$parametername] = str_replace("þ","",$zielarray[$counter.") ".$parametername]);
                  }

              }
              $counter++;
          }// End if

      }// End foreach
      // Rueckgabe des Arrays
      return $zielarray;
  }// End function get_phpinfo_value

  // -----------------------------------------------------------------------
  // Diese Funktion updated die angegebene Shopeinstellung (seit PhPepperShop v.1.4 werden
  // neue Shopsettings als key-value Paar in der Tabelle shop_settings_new gespeichert).
  // Wenn man das vierte Argument ($security) weglaesst oder es als Leerstring mituebergibt,
  // wird die aktuell in der Datenbank vorhandene Security nicht upgedated.
  // Argumente: $name (String), $gruppe (String), $wert (String), $security (String, optional)
  // Rueckgabewert: true oder Abbruch via die()-Funktion
  function set_new_shop_setting($name, $gruppe, $wert, $security="") {

      // Verwendete Variablen aus anderen Modulen lesbar machen
      global $Admin_Database;
      global $sql_set_new_shop_setting_1_1;
      global $sql_set_new_shop_setting_1_2;
      global $sql_set_new_shop_setting_1_3;
      global $sql_set_new_shop_setting_1_4; // Optional
      global $sql_set_new_shop_setting_1_5;
      global $sql_set_new_shop_setting_1_6;
      global $sql_set_new_shop_setting_1_7;

      // SQL-Statement zusammenbauen
      if ($security == "") {
          $sql = $sql_set_new_shop_setting_1_1.$name.$sql_set_new_shop_setting_1_2.$gruppe.$sql_set_new_shop_setting_1_3.$wert.$sql_set_new_shop_setting_1_4.$security.$sql_set_new_shop_setting_1_5.$name.$sql_set_new_shop_setting_1_6.$gruppe.$sql_set_new_shop_setting_1_7;
      }
      else {
          $sql = $sql_set_new_shop_setting_1_1.$name.$sql_set_new_shop_setting_1_2.$gruppe.$sql_set_new_shop_setting_1_3.$wert.$sql_set_new_shop_setting_1_5.$name.$sql_set_new_shop_setting_1_6.$gruppe.$sql_set_new_shop_setting_1_7;
      }


      // Test ob Datenbank erreichbar ist
      if (!is_object($Admin_Database)) {
          die("<P><H1>S_A_Error: Datenbank nicht erreichbar. Funktion set_new_shop_setting</H1></P><BR>");
      }
      else {
          // Tabelle shop_settings_new updaten.
          $RS = $Admin_Database->Exec($sql);
          if (!$RS) {
              // Fehler beim UPDATE der Tabelle shop_settings --> Mit Fehlermeldung abbrechen
              die("<P><H2>S_A_Error: Speichern der Shopeinstellung (Tabelle: shop_settings_new) ".$name."_".$gruppe." mit dem Wert ".$wert." war nicht m&ouml;glich. Funktion: set_new_shop_setting</H2></P><BR>");
          }
          return true;
      }// End else
  }// End set_new_shop_setting

  // End of file---------------------------------------------------------------------------------------
?>
