<?php

include_once __DIR__ . '/../root.php';

function db_connection() {
  $db = new PDO('sqlite:' . APP_ROOT . '/db.sqlite3');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $db;
}

function create_database() {
  $db = db_connection();
  $db->exec('CREATE TABLE IF NOT EXISTS todos (id INTEGER PRIMARY KEY, title TEXT, completed INTEGER)');
}
