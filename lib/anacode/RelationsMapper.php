<?php

namespace anacode;

class RelationsMapper extends AbstractMapper {

    /*
     * Build project map in format
     *  [ entityName = [ 'ext' => <parent name>, 'int' => [ <implemented name>, ... ]]
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

            $ent_name = null;
            $readEntityName = $readParentName = $readImplementsName = false;
            foreach ($tokens as $token) {
                if (!is_array($token)) {
                    if ($readImplementsName && trim($token) == '{') {
                        $ent_name = null;
                        $readImplementsName = false;
                    }

                    continue;
                }

                switch ($token[0]) {
                    case T_STRING:
                        $token_name = strtolower($token[1]);
                        if ($readEntityName) {
                            $this->entities_processed++;
                            $ent_name = $token_name;
                            $map[$ent_name]['p'] = $file->getRealPath();
                            $readEntityName = false;
                        }

                        if ($readParentName) {
                            $map[$ent_name]['ext'] = $token_name;
                            $readParentName = false;
                        }

                        if ($readImplementsName) {
                            $map[$ent_name]['int'][] = $token_name;
                        }

                        break;

                    case T_ABSTRACT:
                    case T_CLASS:
                    case T_INTERFACE:
                        $readEntityName = true;
                        break;

                    case T_EXTENDS:
                        $readParentName = true;
                        break;

                    case T_IMPLEMENTS:
                        $readImplementsName = true;
                        break;
                }
            }
        }

        $this->map = $map;
        $this->time_elapsed = microtime(true) - $start_at;

        $this->printStat();

        return $this;
    }

}