<?php declare(strict_types=1);

namespace ImageViewer;

require '../vendor/autoload.php';

$factory = new Factory('/home/xuedi/private/myPictures/');

$thumbGenerator = $factory->getThumbGenerator();
$thumbGenerator->build(100);
$thumbGenerator->build(200);

