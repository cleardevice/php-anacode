<?php

namespace anacode;

function _getIterator(\SplFileInfo $fileInfo, $recursive) {
    if ($fileInfo->isFile())
        return [$fileInfo];

    if ($recursive === true) {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fileInfo->getRealPath()),
            \RecursiveIteratorIterator::SELF_FIRST
        );
    } else {
        return new \DirectoryIterator($fileInfo->getRealPath());
    }
}

function tokens_info($incremental = true) {
    for($i=258; $i < 390; $i++) {
        $res[$i] = token_name($i);
    }

    if ($incremental)
        ksort($res);
    else
        asort($res);

    return $res;
}