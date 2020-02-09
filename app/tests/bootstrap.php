<?php

use DG\BypassFinals;

require __DIR__ . '/../vendor/autoload.php';


// ProgressBar & other symfony stuff using unmockable final classes, so do magic
BypassFinals::enable();

