<?php declare(strict_types=1);

namespace ImageViewer;

require '../vendor/autoload.php';

$factory = new Factory('/home/xuedi/private/myPictures/');

$scanner = $factory->getFileScanner();
$scanner->buildImageTree();

