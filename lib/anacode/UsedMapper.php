<?php

namespace anacode;

class UsedMapper extends AbstractMapper {

    private static
        $exceptEntities = ['self', 'parent'];

    /*
     * Collect used project entities
     *
     * @path_list array List of path analyzed code
     *
     * @return $this
     * @todo add NS support (T_NS_SEPARATOR)
     */
    public function generateMap(array $path_list) {
        $start_at = microtime(true);

        $map = [];
        foreach ($path_list as $path) foreach ($path as $file) {
            if ($file->getExtension() != 'php' || $this->isExcluded($file->getRealPath()))
                continue;

            $this->files_processed++;
            $tokens = token_get_all(file_get_contents($file->getRealPath()));

            $prev_token_name = null;
            $newEntity = $staticEntity = false;
            foreach ($tokens as $token) {
                switch ($token[0]) {
                    case T_STRING:
                        $curr_token_name = strtolower($token[1]);
                        if ($newEntity) {
                            $this->entities_processed++;
                            if (!in_array($curr_token_name, self::$exceptEntities)) {
                                $map[$curr_token_name] = isset($map[$curr_token_name]) ?
                                    $map[$curr_token_name]+1 : 1;
                            }
                            $newEntity = false;
                        }

                        if ($staticEntity) {
                            $this->entities_processed++;
                            if (!in_array($prev_token_name, self::$exceptEntities)) {
                                $map[$prev_token_name] = isset($map[$prev_token_name]) ?
                                    $map[$prev_token_name]+1 : 1;
                            }
                            $staticEntity = false;
                        }

                        $prev_token_name = $curr_token_name;
                        break;

                    case T_ABSTRACT:
                    case T_CLASS:
                    case T_INTERFACE:
                        $newEntity = true;
                        break;


                    case T_NEW:
                        $newEntity = true;
                        break;

                    case T_DOUBLE_COLON:
                        $staticEntity = true;
                        break;
                }
            }
        }

        $this->map = $map;
        $this->time_elapsed = microtime(true) - $start_at;

        return $this;
    }

    protected function sortMap() {
        arsort($this->map);
    }

    protected function prettifyJSON($json) {
        if (!$this->pretty)
            return $json;

        return str_replace(',"', sprintf(',%s"', PHP_EOL), $json);
    }

}