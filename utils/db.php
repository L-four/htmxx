<?php

include_once __DIR__ . '/../root.php';
include_once __DIR__ . '/../utils/core.php';

function db_connection() {
  $db = new PDO('sqlite:' . APP_ROOT . '/db.sqlite3');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $db;
}

/**
 * @param $key string
 *
 * @return string
 */
function get_key_value($key): string {
  $db = db_connection();
  $stmt = $db->prepare('SELECT value FROM kv WHERE key = :key');
  $stmt->execute([
    ':key' => $key,
  ]);
  return $stmt->fetch(PDO::FETCH_COLUMN);
}

function set_key_value($key, $value) {
  $db = db_connection();
  $stmt = $db->prepare('INSERT INTO kv (key, value) VALUES (:key, :value) ON CONFLICT (key) DO UPDATE SET value = :value');
  $stmt->execute([
    ':key' => $key,
    ':value' => $value,
  ]);
  return $db->lastInsertId();
}

/**
 * @param $table_name string
 *
 * @return bool
 */
function table_exists($table_name): bool {
  $db = db_connection();
  $stmt = $db->prepare('SELECT count(*) FROM sqlite_master WHERE type = "table" AND name = :name');
  $stmt->execute([
    ':name' => $table_name,
  ]);
  return $stmt->fetch(PDO::FETCH_COLUMN) > 0;
}

/**
 * @return \HxxError|bool
 */
function create_database() {
  $db = db_connection();
  if (table_exists('kv')) {
    $version = get_key_value('version');
    return new HxxError("Database already exists at version $version", HxxError::DB_EXISTS);
  }
  $schema = file_get_contents(__DIR__ . '/schema.sql');
  $db->exec($schema);
  set_key_value('version', APP_VERSION);
  return true;
}

function populate_seed_data() {
  $db = db_connection();
  $seed_data = file_get_contents(__DIR__ . '/seed_data.sql');
  $db->exec($seed_data);
}
