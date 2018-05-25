#/usr/bin/php
<?php
include __DIR__."/findProgram.php";

// First, try to find in program tv
if ($result = findProgram($argv[1])) {
    die($result);
}
die('Rien à la télé.');
