<?php

require_once 'common.php';

$arguments = new \cli\Arguments();

$arguments->addFlag(array('help', 'h'), 'Show this help screen');
$arguments->addOption(array('relations', 'r'), 'Path to relations report json file');
$arguments->addOption(array('used', 'u'), 'Path to used report json file');
$arguments->addOption(array('out', 'o'), 'Output report path');

$arguments->parse();
$args = $arguments->getArguments();
if ($arguments['help'] || empty($args)) {
    echo $arguments->getHelpScreen();
    echo PHP_EOL . PHP_EOL;
    die;
}

if (!file_exists($arguments['relations'])) {
    die('Can\'t load relations report data on: ' . $arguments['relations']);
}
if (!file_exists($arguments['used'])) {
    die('Can\'t load used report data on: ' . $arguments['used']);
}
$out_path = realpath($arguments['out']) . DIRECTORY_SEPARATOR;
if (!is_dir($out_path)) {
    die('Can\'t find output path: ' . $out_path);
}

$relations_data = json_decode(file_get_contents($arguments['relations']), true);
$used_data = json_decode(file_get_contents($arguments['used']), true);

$project_files = array_map(function($class_info) { return $class_info['p']; }, $relations_data);
$project_files = array_unique(array_values($project_files));

$used_entities = $used_files = [];
foreach ($used_data as $entity => $freq_of_use) {
    $search_stack = [];
    $search = $entity;
    while (!empty($search) && isset($relations_data[$search])) {
        $used_entities[$search] = true;
        $used_files[$relations_data[$search]['p']] = true;
        if (isset($relations_data[$search]['ext']) || !empty($relations_data[$search]['ext'])) {
            if (!empty($relations_data[$search]['int'])) {
                $search_stack += $relations_data[$search]['int'];
            }
            if (isset($relations_data[$search]['ext'])) {
                $search = $relations_data[$search]['ext'];
            } else {
                $search = array_pop($search_stack);
            }
        } elseif (!empty($search_stack)) {
            $search = array_pop($search_stack);
        } else {
            $search = null;
        }
    }
}

$unused_entities = array_diff(array_keys($relations_data), array_keys($used_entities));
$unused_files = array_diff($project_files, array_unique(array_keys($used_files)));

file_put_contents($out_path . DIRECTORY_SEPARATOR . 'remove_entries', implode(PHP_EOL, $unused_entities));
file_put_contents($out_path . DIRECTORY_SEPARATOR . 'remove_files', implode(PHP_EOL, $unused_files));