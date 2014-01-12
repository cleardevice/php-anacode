php-anacode
===========

Code analyzer for php projects. Shows unused classes and suggests files can be removed from project.

* For correct results all classes in project must have different names.
* For dynamically called front controllers need generate list of possible class names.

Requirements

* PHP >= 5.4
* composer

### Usage

    $ php bin/gen_relations_map.php -p <path_to_project> -o tmp/relations_map.json
    $ php bin/gen_used_map.php -p <path_to_project_entry_point(s)> -o tmp/used_map.json
    $ php bin/gen_advise.php -r tmp/relations_map.json -u tmp/used_map.json -o tmp

### Todo
* add namespace support