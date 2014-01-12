<?php

namespace anacode;

class AbstractMapper {

    protected $map;
    protected $pretty = true;
    protected $exclude_pattern = array();

    // statistic variables
    protected $entities_processed = 0;
    protected $files_processed = 0;
    protected $time_elapsed;

    public function setPretty($value = true)
    {
        $this->pretty = (bool)$value;
    }

    public function addExclude(array $patterns) {
        $this->exclude_pattern += array_filter($patterns);
    }

    protected function isExcluded($curr_path) {
        if (!empty($this->exclude_pattern)) {
            foreach ($this->exclude_pattern as $pattern) {
                if (strpos($curr_path, $pattern) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getMap()
    {
        return $this->map;
    }

    protected function sortMap() {
        ksort($this->map);
    }

    protected function prettifyJSON($json) {
        if (!$this->pretty)
            return $json;

        return str_replace(["},", "],"], ["}," . PHP_EOL, "]," . PHP_EOL], $json);
    }

    public function __toString()
    {
        $this->sortMap();
        return $this->prettifyJSON(json_encode($this->map));
    }

    protected function printStat() {
        printf('%d entities in %d files found in %.3f sec' . PHP_EOL,
            $this->entities_processed, $this->files_processed, $this->time_elapsed);
    }

}