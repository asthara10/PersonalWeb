<html> 
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!--for compatibility with explorer-->
        <meta name="viewport" content="width=device-width, initial-scale=1"> <!--responsive page-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> <!--Bootstrap stylesheet-->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css"> <!-- Bootstrap table stylesheet -->
        <link rel="stylesheet" type="text/css" href="styles.css"> <!--My style sheet-->
        <!--<link rel="stylesheet" type="text/css" href="BLASTstyle.css"> BLAST styles-->
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script> <!-- dynamic table links -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> <!-- dynamic menu links -->
        <link href='https://fonts.googleapis.com/css?family=Cinzel' rel='stylesheet'> <!--font-->
        <link href='https://fonts.googleapis.com/css?family=Nixie One' rel='stylesheet'> <!--font-->
    </head>
    <body>
        <div class="container">
            <ul class="menu" id="myMenu">
                <li><a href="index.html">Home</a></li>
                <li><a href="game.html">Play with nucleotides</a></li>
                <li><a href="classwork.html">Class work</a></li>
                <li><a href="miod.html">MIOD</a></li>
                <li><a href="https://www.linkedin.com/in/juliamirpedrol/" target="_blank">LinkedIn</a></li>
                <li><a class="last" href="contact.html">Contact</a></li>
                <a href="javascript:void(0);" class="icon" onclick="RespMenu()">&#9776;</a>
            </ul>   
        </div>
        <script>
            function RespMenu() {
                var x = document.getElementById("myMenu");
                if (x.className === "menu") {
                    x.className += " responsive";
                } else {
                    x.className = "menu";
                }
            }
        </script>
<?php

// load global vars and includes
include "globals.inc.php";
session_start();
// Store input data in $_SESSION to reload initial form if necessary
$_SESSION['queryData'] = $_REQUEST;
// Selection of action to do
// 1. IdCode -> Results page
// 2. Sequence input -> runBlast
// 3. Other -> search on DB
//
// 1. Redirection to the requested entry if code selected
if ($_REQUEST['idCode']) {
    header('Location: getStruc.php?idCode=' . $_REQUEST['idCode']);
    // 2. Sequence input. If uploaded file, this takes preference
} elseif ($_FILES['seqFile']['name'] or $_REQUEST['seqQuery']) {
    if (($_FILES['seqFile']['tmp_name'])) {
        $_SESSION['queryData']['seqQuery'] = file_get_contents($_FILES['seqFile']['tmp_name']);
    }   
} 

// Select database
if ($_REQUEST['database']) {
    $DB = $_REQUEST['database'];
} else {
    $DB = 'PDB';
}

$blastCmdLine = "$blastExe -d $blastDbsDir/" . $blastDbs[$DB] . " -p blastp  -e 0.001 -v 100 -b 0 ";

// Take data from $_SESSION, loaded in search.php, if empty back to front page
if (!isset($_SESSION['queryData']) or ! $_SESSION['queryData']['seqQuery'])
    header('Location: index.php');
// prepare FASTA file
// Identify file format, ">" as first char indicates FASTA
$p = strpos($_SESSION['queryData']['seqQuery'], '>');
if (!$p and ( $p !== 0)) { // strpos returns False if not found and "0" if first character in the string
    // When is not already FASTA, add header + new line
    $_SESSION['queryData']['seqQuery'] = ">User provided sequence\n" . $_SESSION['queryData']['seqQuery'];
}
// Set temporary file name to a unique value to protect from concurrent runs
$tempFile = $tmpDir . "/" . uniqId('bl');
// Open temporary file and store query FASTA
$ff = fopen($tempFile . ".query.fasta", 'wt');
fwrite($ff, $_SESSION['queryData']['seqQuery']);
fclose($ff);
// execute Blast, Command line set in globals.inc.php
//print $blastCmdLine." -i ".$tempFile.".query.fasta -o ".$tempFile.".blast.out";
exec($blastCmdLine . " -i " . $tempFile . ".query.fasta -o " . $tempFile . ".blast.out");

// Regular Expression to select results in function of the database used
if ($DB == 'PDB') {
	$resregex = '/(....)_(.) mol:([^ ]*) length:([0-9]*) *(.*)/';
} elseif ($DB == 'SwissProt') {
	$resregex = '/(sp)(\|.+\|)(.+\.\.\.) *([0-9]*) *(.*)/';
}

// Read results file and parse hits onto $result[]
$blast = file($tempFile . ".blast.out");
$i = 0;
$queries = 0;
while ($i < count($blast)) {
    while (!preg_match('/Sequences producing/', $blast[$i]) and ( $i < count($blast))) {
        if (!preg_match('/(^Query=\s)(.+)/', $blast[$i])) {
        } else {
            $queries++;
            $result[] = $blast[$i];
        }
        $i++;
    }
    $i++;
    while (!preg_match('/Database:/', $blast[$i]) and ( $i < count($blast))) {
        if (($blast[$i]) and (preg_match($resregex, $blast[$i]))) {
            $result[] = $blast[$i];
        } elseif (($blast[$i]) and (preg_match('/(^Query=\s)(.+)/', $blast[$i]))) {
            $queries++;
            $result[] = $blast[$i];
        }
        $i++;
    }
}
if (!count($result)) {
    print("Results where not found :(");
} else {
//        Results table
    ?>
<p>Num Hits: <?php print count($result)-$queries ?> </p>
    <table class="table" class="table-bordered" cellspacing="2" width="100%" cellpadding="4" id="blastTable">
        <thead>
            <tr>
                <th>Query ID</th>
                <th>Hit ID</th>
                <th>Description</th>
                <th>Score (bits)</th>
                <th>E-value</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // parsing hit following specific format, note that this format is not standard. It comes from the 
            // headers used to generate BLAST databases, this is from PDB
            foreach (array_values($result) as $rr) {
                if (strlen($rr) > 1) {
                    if (preg_match('/(^Query=\s)(.+)/', $rr, $quer)) {
                    	list ($whole, $word, $queryid) = $quer;
                    }
                    elseif (!preg_match('/(^Query=\s)(.+)/', $rr, $quer)) {
                        preg_match($resregex, $rr, $hits);

                        if ($DB == 'PDB') {
	                        list ($r, $idCode, $sub, $tip, $l, $tmpTxt)= $hits;
	                        $tmpData = preg_split('/\s+/',$tmpTxt);
	                        if (!preg_match('/[0-9]/',$tmpData[count($tmpData)-1])) {
	                            array_pop($tmpData);
	                        }
	                        $ev = array_pop($tmpData);
	                        $sco = array_pop($tmpData);
	                        $desc = join(' ',$tmpData);
	                        $ID = $idCode . "_$sub";
	                    } elseif ($DB == 'SwissProt') {
	                    	list ($r, $sq, $idCode, $desc, $sco, $ev) = $hits;
	                    	$ID = preg_replace('/\|/', '', $idCode);
	                    }
                    ?>
                    <tr>
                        <td><?php print $queryid ?></td>
                        <td><?php print $ID ?></td>
                        <td><?php print $desc ?></td>
                        <td><?php print $sco ?></td>
                        <td><?php print $ev ?></td>
                    </tr>
                    <?php
                    }
                }
            }
            ?>
        </tbody>
    </table>
    <button class="button" style="margin:20px;" onclick="window.location.href='blast.php'">New Search</button>
    <?php
    // Cleaning temporary files
    if (file_exists($tempFile . ".query.fasta"))
        unlink($tempFile . ".query.fasta");
    if (file_exists($tempFile . ".blast.out"))
        unlink($tempFile . ".blast.out");
    //print footerDBW();
}
// DataTable activation
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#blastTable').DataTable();
    });
</script>
