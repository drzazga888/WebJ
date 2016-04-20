<?php
// router.php

$path = pathinfo($_SERVER["SCRIPT_FILENAME"]);
if ($path["extension"] == "appcache") {
    header("Content-Type: text/cache-manifest");
    readfile($_SERVER["SCRIPT_FILENAME"]);
}
else {
    return FALSE;
}

