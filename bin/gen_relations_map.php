<?php

require_once 'common.php';

$arguments = new \cli\Arguments();

$arguments->addFlag(array('help', 'h'), 'Show this help screen');
$arguments->addOption(array('path', 'p'), 'Path to analyze');
$arguments->addOption(array('exclude', 'e'), 'Exclude pattern(s)');
$arguments->addOption(array('out', 'o'), array(
    'default' => 'relations_map.json',
    'description' => 'Output relations report filename'
));

$arguments->parse();
$args = $arguments->getArguments();
if ($arguments['help'] || empty($args)) {
    echo $arguments->getHelpScreen();
    echo PHP_EOL . PHP_EOL;
    die;
}

$path_list = [];
foreach (explode(' ', $arguments['path']) as $path) {
    try {
        $path_list[] = anacode\_getIterator(new \SplFileInfo($path), true);
    } catch (\RuntimeException $e) {
        // skip path errors
    }
}

$output_filename = empty($arguments['out']) ? 'relations_map.json' : $arguments['out'];

$relationsMapper = new anacode\RelationsMapper;
$relationsMapper->addExclude(explode(' ', $arguments['exclude']));
file_put_contents($output_filename, $relationsMapper->generateMap($path_list));
