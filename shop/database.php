<?php
// Projekt: PhPepperShop
//
// Filename: database.php
//
// Modul: Datenbankabstraktion
//
// Autoren: Michael Baumer <baumi@vis.ethz.ch> Citrin, Feisthammel & Partner
//          MySQL-Port: (2001) Jose Fontanil & Reto Glanzmann
//
// Zweck: Datenbank-Anbindung für PHP-Skripte (Datenbank Wrapper)
//
// Security Status:           *** USER ***
//
// Version: 1.4
//
// CVS-Version / Datum: $Id: database.php,v 1.18 2003/06/15 21:21:10 fontajos Exp $
//
// WARNING: This Wrapper needs to be rewritten for use with Sybase or
// other DBs. For Sybase only the function TSybaseDatabase->Exec() has
// to be enhanced. The rest is already programmed and should work, but we
// didn't test it!
//
// -----------------------------------------------------------------------------------------

// To ensure that no script is going to be included twice, we have this
// variable (name of the script without .php). If it's true, the script
// has already been included and the content of it is available to the
// calling script.
$database = true;

// configure include Paths. Different delimiters for Windows or the rest
// Windows --> delimiter = ; | UNIX/Linux/... --> delimiter = :
if (substr(PHP_OS,0,3) == 'WIN') {$pd = ';';} else {$pd = ':';}
ini_set("include_path", "./$pd./shop$pd../$pd../../$pd./Frameset$pd/usr/local/lib/php");

// Inclusion of the needed modules
if (empty($util)) {include("util.php");}

// -----------------------------------------------------------------------

// TRecordSet
// Abstract base class for handling the result from a Database
class TRecordSet {
    var $myRecordCount;
    // WARNING: Do not use this function until all
    // rows have been fetched.

    function GetRecordCount() {
        die("db: Abstract Method called");
    }

    function NextRow() {
        die("db: Abstract Method called");
    }
}// End abstract class TRecordSet

// TSybaseRecordSet
// Concrete Implementation for the Sybase Record Set
class TSybaseRecordSet extends TRecordSet {
    // member vars
    var $myDBresult;
    var $myDatabase;
    var $myRow;

    // Constructor
    function TSybaseRecordSet($Database, $DBresult) {
        $this->myDBresult = $DBresult;
        $this->myDatabase = $Database;
    }

    // Reads to the next tuple
    // Returns FALSE if no more tuple exist
    function NextRow() {
        $this->myRow = sybase_fetch_array($this->myDBresult);
        if (!$this->myRow)
            return false;
        else
            return true;
        }

    // Return the value for a field index
    function GetField($key) {
        if ($this->myRow)
            return $this->myRow[$key];
        else
            return false;
        }

    // Returns number of Records
    function GetRecordCount() {
        if (!$this->my_recordCount) {
            $this->myRecordCount = sybase_num_rows($this->myDBresult);
        }
        return($this->myRecordCount);
    }
}// End abstract class TRecordSet

// TMySQLRecordSet
// Concrete Implementation for the MySQL Record Set
class TMySQLRecordSet extends TRecordSet {
    // member vars
    var $myDBresult;
    var $myDatabase;
    var $myRow;

    // Constructor
    function TMySQLRecordSet($Database, $DBresult) {
        $this->myDBresult = $DBresult;
        $this->myDatabase = $Database;
    }

    // Reads to the next tuple
    // Returns FALSE if no more tuple exist
    function NextRow() {
        $this->myRow = mysql_fetch_array($this->myDBresult);
        if (!$this->myRow)
            return false;
        else
            return true;
    }

    // Return the value for a field index
    function GetField($key) {
        if ($this->myRow)
            return $this->myRow[$key];
        else
            return false;
    }

    // Returns number of Records
    function GetRecordCount() {
        if (!$this->my_recordCount) {
            $this->myRecordCount = mysql_num_rows($this->myDBresult);
        }
        return($this->myRecordCount);
    }
}// End class TMySQLRecordSet

// TDatabase
// The (abstract) base class for different databases.
class TDatabase {
    // Execute somethin without result (e.g INSERT, UPDATE)
    // If an INSERT is done it will return the last actualized
    // AUTO-INCREMENT value or false if the function couldn't execute
    // This feature is not yet implemented in the Sybase version
    function Exec($Query) {
        die("db: Abstract Method called");
    }

    // Execute a query giving a result (e.g SELECT)
    function Query($Query) {
        die("db: Abstract Method called");
    }
}// End abstract class TDatabase

// TSybaseDatabase
// The concrete implemenation for the Sybase Database
// Usage:
//   new ($Host, $Database)
//        create a link to the DB
//   $handle = Execute($Query)
//        returns a handle to the result of the Query
class TSybaseDatabase extends TDatabase {
    // member vars
    var $myConnectionHandle;
    var $errMsg;

    function TSybaseDatabase($Host, $Database, $Username, $Password) {
        // Note:
        //   use sybase_connect() for non-persistent and
        //   sybase_pconnect() for persistent DB-connections

        //  $this->myConnectionHandle = sybase_connect($Host, "sa", "");
        //  $this->myConnectionHandle = sybase_connect($Host, $Database, $Username, $Password);
        $this->myConnectionHandle = sybase_connect("$Host", "$Username", "$Password");
        if (!$this->myConnectionHandle) {
            $this->errMsg = "db: Unable to connect to database on $Host as user $Username";
            $this->errNo  = 1;
        }
        if (!sybase_select_db($Database, $this->myConnectionHandle)) {
            $this->errMsg = "db: Unable to select database " . $Database;
            $this->errNo  = 1;
        }
        $this->errNo  = 0;
    }

    // Execute a query without result
    // WARNING: This Function needs to be enhanced, so that the functionality is similar to
    // the MySQL-Version. Problem is, that PHP4 did not support a Sybase_insert_id()-function
    // What is to do:
    // This function needs to return either false if the query did not work, true if the
    // query did work and a value > 0 [int] if the query was an INSERT into a table which
    // has an AUTO-INCREMENT column.
    // As a help, see the MySQL-Version of TMySQLDatabase->Exec().
    function Exec($Query) {
        $DBresult = sybase_query($Query);
        if (!$DBresult) {
            return false;
        }
            return true;
    }

    // Perform a Query that results a RecordSet
    function Query($Query) {
        $DBresult = sybase_query($Query);
        if (!$DBresult) {
            echo "<b>db: Query failed.</b> Query was: $Query\n";
            return false;
        }
        return new TSybaseRecordSet($this, $DBresult);
    }
} // End class TSybaseDatabase

// TMySQLDatabase
// The concrete implemenation for the MySQL Database
class TMySQLDatabase extends TDatabase {
    // member vars
    var $myConnectionHandle;
    var $errMsg;

    function TMySQLDatabase($Host, $Database, $Username, $Password) {
        // Note:
        //   use mysql_connect() for non-persistent and
        //   mysql_pconnect() for persistent DB-connections

        $this->myConnectionHandle = mysql_connect($Host, $Username, $Password);
        if (!$this->myConnectionHandle) {
            $this->errMsg = "db: Unable to connect to database on $Host as user $Username";
            $this->errNo  = 1;
        }
        if (!mysql_select_db($Database, $this->myConnectionHandle)) {
            $this->errMsg = "db: Unable to select database " . $Database;
            $this->errNo  = 1;
        }

        // Ab MySQL 5.0.2 kann man im SQL-Mode angeben, ob die SQL-Syntax strikter geprueft werden soll. Wenn der
        // Provider / DB-Administrator im SQL-Mode Restriktionen angegeben hat, so muss man diese deaktivieren:
        // STRICT_TRANS_TABLES, STRICT_ALL_TABLES, NO_ZERO_IN_DATE, NO_ZERO_DATE, TRADITIONAL_SQL
        if (MYSQL_5_PLUS_NO_STRICT == true) {
            $this->Exec('SET sql_mode = \'\'');
        }

        $this->errNo  = 0;
    }

    // Execute a query without result
    function Exec($Query) {
        $DBresult = mysql_query($Query);
        if (!$DBresult) {
            echo "<b>db: Execution failed.</b> Query was: $Query<BR>";
            echo "<b>MySQL Fehlermeldung: </b>".mysql_error()."<BR>";
            return false;
        }
        $Lastautoincrement = mysql_insert_id();
        // mysql_insert_id will return 0 if there wasn't a AUTO-INCREMENT tag that was altered during the
        // last insert. Since 0 means false, this function would return false, which is not true. Thats why
        // we have to check for this situation and change the return-value to true in that case.
        if ($Lastautoincrement > 0) {
            return $Lastautoincrement; //$Lastautoincrement will be >= 1
        }
        else {
            return true;
        }
    }

    // Perform a Query that results a RecordSet
    function Query($Query) {
        $DBresult = mysql_query($Query);
        if (!$DBresult) {
            echo "<b>db: Query failed.</b> Query was: $Query<BR>";
            echo "<b>MySQL Fehlermeldung: </b>".mysql_error()."<BR>";
            return false;
        }
        return new TMySQLRecordSet($this, $DBresult);
    }
}// End class TMySQLDatabase

// End of file-----------------------------------------------------------------
?>
