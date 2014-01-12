<?php

require_once 'common.php';

$arguments = new \cli\Arguments(array('strict' => true));

$arguments->addFlag(array('help', 'h'), 'Show this help screen');
$arguments->addOption(array('order', 'o'), array(
    'default' => 'incr',
    'possible' => array('incr', 'alph'),
    'description' => 'Tokens output sorting order. Possible options are: incr (incremental) or alph (alphabetical)'
));

$parse_error = false;
try {
    $arguments->parse();
} catch (cli\arguments\InvalidArguments $e) {
    $parse_error = $e->getMessage();
}

if ($arguments['help'] || $parse_error) {
    if ($parse_error)
        echo $parse_error . PHP_EOL;

    echo $arguments->getHelpScreen();
    echo PHP_EOL . PHP_EOL;
} else {
    print_r(anacode\tokens_info($arguments['order'] == 'incr'));
}