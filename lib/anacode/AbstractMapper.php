<?php

namespace anacode;

class AbstractMapper {

    protected $map;
    protected $pretty = true;
    protected $exclude_pattern = array();

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

        return str_replace(["},", "],"], ["},\n", "],\n"], $json);
    }

    public function __toString()
    {
        $this->sortMap();
        return $this->prettifyJSON(json_encode($this->map));
    }

}