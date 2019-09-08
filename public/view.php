<?php

namespace ImageViewer;

require '../vendor/autoload.php';

$hash = $_GET['show'];
$list = json_decode(file_get_contents('../data/HashMap.json'), true);
$file = $list[$hash];

$fp = fopen($file, 'rb');

// send the right headers
header("Content-Type: image/png");
header("Content-Length: " . filesize($file));

// dump the picture and stop the script
fpassthru($fp);
exit;
