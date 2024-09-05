<?php
include_once 'includes/init.php';

$path = 'documents/access_denied.pdf';
if(isset($_GET['doc'])){
    $tempPath = 'documents/'.$_GET['doc'];
    if(file_exists($tempPath)){
        $path = $tempPath;
    }
}


header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename='.$path);
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

readfile($path);