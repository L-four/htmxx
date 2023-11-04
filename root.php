<?php

define('APP_ROOT', __DIR__);
define('APP_VERSION', 1);

include_once "utils/db.php";
include_once "utils/core.php";

//$handler = new HxxSessionHandler();
//
//session_set_save_handler($handler, true);

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

switch ($_SERVER['SCRIPT_NAME']) {
  case '/api/list_select':
    include_once 'api/list_select.php';
    break;
  case '/api/list':
    include_once 'api/list.php';
    break;
  case '/api/todo':
    include_once 'api/todo.php';
    break;
  case '/api/todos':
    include_once 'api/todos.php';
    break;
  case '/':
  default:
    include_once 'index.php';
    break;
}


if (php_sapi_name() == 'cli') {
  if ($argc == 2) {
    if ($argv[1] == 'create-database') {
      $res = create_database();
      if ($res instanceof HxxError) {
        echo $res->message . "\n";
        exit($res->code);
      }
      echo "Database created\n";
    }
    else if ($argv[1] == 'php-info') {
      phpinfo();
    }
  }
  else {
    echo "Usage: php root.php create-database\n";
  }
}