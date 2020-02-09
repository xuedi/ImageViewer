<?php

$cloverXml = __DIR__ . '/../reports/clover.xml';
$template = __DIR__ . '/badge/coverage_template.svg';
$badge = __DIR__ . '/badge/coverage.svg';

if (!file_exists($cloverXml)) {
    echo "No coverage file found: $cloverXml" . PHP_EOL;
    exit;
}

$xml = new SimpleXMLElement(file_get_contents($cloverXml));
$metrics = $xml->xpath('//metrics');
$totalElements = 0;
$checkedElements = 0;

foreach ($metrics as $metric) {
    $totalElements += (int)$metric['elements'];
    $checkedElements += (int)$metric['coveredelements'];
}
$coverage = round(($checkedElements / $totalElements) * 100);

$color = 'cd3616';
if($coverage > 50) $color = 'd19017';
if($coverage > 60) $color = 'd1d117';
if($coverage > 70) $color = '7ad117';
if($coverage > 80) $color = '39d117';

$values = [
    'coverage' => $coverage,
    'color' => $color,
];



$svg = file_get_contents($template);
foreach ($values as $search => $replace) {
    $search = '{{' . $search . '}}';
    $svg = str_replace($search, $replace, $svg);
}
file_put_contents($badge, $svg);
