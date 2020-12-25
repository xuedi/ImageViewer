<?php

use Backend\Backend;

require __DIR__ . '/../app/vendor/autoload.php';
require __DIR__ . '/../app/src/Backend/Backend.php';

$backend = new Backend();
$backend->process();
