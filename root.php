<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('APP_ROOT', __DIR__);
define('APP_VERSION', 1);

global $state;
/** @var PDO $db */
global $db;

class HxxState {
  const prefix = 's_';
  public $state = [];

  public $hasRendered = FALSE;

  function __construct() {
    foreach ($_GET as $key => $value) {
      if (strpos($key, self::prefix) === 0) {
        $this->state[substr($key, strlen(self::prefix))] = $value;
      }
    }
    $data = file_get_contents('php://input');
    $values = [];
    parse_str($data, $values);
    foreach ($values as $key => $value) {
      if (strpos($key, self::prefix) === 0) {
        $this->state[substr($key, strlen(self::prefix))] = $value;
      }
    }
  }
  function set($key, $value) {
    $this->state[$key] = $value;
  }
  function get($key, $default = null) {
    if (empty($this->state[$key])) {
      return $default;
    }
    return $this->state[$key];
  }

  function set_url_header(): void {
    $headers = getallheaders();
    if (!empty($headers['HX-Current-URL'])) {
      $old_q = parse_url($headers['HX-Current-URL'], PHP_URL_QUERY);
      $old_path = parse_url($headers['HX-Current-URL'], PHP_URL_PATH);

      $old_q_arr = [];
      if (!empty($old_q)) {
        parse_str($old_q, $old_q_arr);
      }

      $ret = [];
      foreach ($this->state as $key => $value) {
        $ret[self::prefix . $key] = $value;
      }
      $new_q = http_build_query($ret + array_filter($old_q_arr));
      header('hx-push-url: ' . $old_path . '?' . $new_q);
    }
  }
  function render() {
    $this->set_url_header();
    echo '<div id="state" class="d-none" hx-swap-oob="#state">';
    foreach ($this->state as $key => $value) {
      echo '<input class="state" name="' . self::prefix . $key . '" value="' . $value . '"/>';
    }
    echo '</div>';
    $this->hasRendered = TRUE;
  }
}
$state = new HxxState();

include_once "includes.php";

$db = db_connection();

//$handler = new HxxSessionHandler();
//
//session_set_save_handler($handler, true);

//if (session_status() == PHP_SESSION_NONE) {
//  session_start();
//}

ob_start();

$route = $_SERVER['PATH_INFO'] ?? $_SERVER['SCRIPT_NAME'];

switch ($route) {
  case '/api/list_select':
    $listSelectComp = new ListSelectComp(rootComponent: TRUE);
    $listSelectComp->update();
    if ($listSelectComp->shouldRender) {
      $listSelectComp->render();
    }
    break;
  case '/api/list':
    $comp = new ListComp();
    $comp->update();
    if ($comp->shouldRender) {
      $comp->render();
    }
    break;
  case '/api/todo':
    $comp = new TodoComp();
    $comp->update();
    if ($comp->shouldRender) {
      $comp->render();
    }
    break;
  case '/api/todos':
    $comp = new TodosComp();
    $comp->update();
    if ($comp->shouldRender) {
      $comp->render();
    }
    break;
  case '/_opcache':
    require_once 'opcache.php';
    break;
  case '/':
  default:
    require_once 'home.php';
    break;
}

if (!$state->hasRendered) {
  $state->render();
}
db_close();
ob_flush();


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