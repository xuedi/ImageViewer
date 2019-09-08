<?php

namespace ImageViewer;

require '../vendor/autoload.php';

$hash = $_GET['show'];
$list = json_decode(file_get_contents('../data/files.json'), true);
foreach ($list as $item) {
    if($item['fileHash']==$hash) {
        echo json_encode($item, JSON_PRETTY_PRINT);
        exit;
    }
}
