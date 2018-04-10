<?php
/*
 * index.php
 * main form
 */
# Loading global variables and DB connection
include "globals.inc.php";
//
// $_SESSION['queryData'] array holds data from previous forms, 
// if empty it should be initialized to avoid warnings, and set defaults
// also a ...?new=1 allows to clean it from the URL.
//
if (isset($_REQUEST['new']) or !isset($_SESSION['queryData'])) {
    $_SESSION['queryData'] = [
        'minRes' => '0.0',
        'maxRes' => 'Inf',
        'query' => ''
    ];
}

#Main Form follows
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!--for compatibility with explorer-->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!--responsive page-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> <!--Bootstrap stylesheet-->
    <link rel="stylesheet" type="text/css" href="styles.css"> <!--My style sheet-->
    <!--<link rel="stylesheet" type="text/css" href="BLASTstyle.css"> BLAST styles-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
    <div class="container" style="margin:20px;">
        <form name="MainForm" action="runBlast.php" method="POST" enctype="multipart/form-data">
            <div class="row" class="container">
                <div class="col-md-6">
                    <label><h1>BLAST Application</h1></label>
                    <p style="margin:20px;">This BLAST application performs a BLAST alignment with the provided protein sequences allowing to choos between two databases (SwissProt or PDB)</p>
                    <p>Enter protein sequences in FASTA format by hand or select a FASTA file:</p>
                    <div class="form-group">
                        <textarea class="form-control" name="seqQuery" rows="4" cols="60" style="width:100%"></textarea><br>
                        Upload sequence file: <input type="file" name="seqFile" value="" width="50" style="width:100%"/>
                    </div>
                </div>
            </div>
            <div class="container">
                <p> Select the database to run BLAST with: </p>
                <p>
                    <input type="radio" name = "database" value="SwissProt"> SwissProt </input> 
                    <input type="radio" name = "database" value="PDB"> PDB </input>
                </p>
            </div>
            <div class="row" style="margin:20px;">
                <p>
                    <button class="button" type='submit' class="btn btn-primary">Submit</button>
                    <button class="button" type='reset' class="btn btn-primary">Reset</button>
                </p>
            </div>
        </form>
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
</body>
<?php
print footerDBW();

