<?php
session_start();
/*
 * globals.inc.php
 * Global variables and settings
 */
// Base directories
// Automatic, taken from CGI variables.


// Temporal dir, create if not exists, however Web server 
// may not have the appropriate permission to do so
$tmpDir = "./tmp";

// Blast details, change to adapt to local settings
// Blast databases should be created using the appropriate programs.
$blastHome = "../../dbw00/blast";
$blastDbsDir = "../../dbw00/blast/dbs";
$blastExe = "../../dbw00/blast/bin/blastall";
$blastDbs = array("SwissProt" => "sprot", "PDB" => "pdb_seqres.txt");

//$DB = $_SESSION['database'];
//print("<script>alert(".$DB.");</script>");

//$blastCmdLine = "$blastExe -d $blastDbsDir/" . $blastDbs[$DB] . " -p blastp  -e 0.001 -v 100 -b 0 ";

// Start session to store queries

