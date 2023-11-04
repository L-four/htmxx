<?php

define('APP_ROOT', __DIR__);

include_once "utils/db.php";


if (php_sapi_name() == 'cli') {
  if ($argc == 2) {
    if ($argv[1] == 'create-database') {
      create_database();
    }
    else if ($argv[1] == 'php-info') {
      phpinfo();
    }
  }
  else {
    echo "Usage: php root.php create-database\n";
  }
}