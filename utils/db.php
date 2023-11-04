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

function get_todos() {
  $db = db_connection();
  $stmt = $db->prepare('SELECT * FROM todos');
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function create_todo($title, $completed = false) {
  $db = db_connection();
  $stmt = $db->prepare('INSERT INTO todos (title, completed) VALUES (:title, :completed)');
  $stmt->execute([
    ':title' => $title,
    ':completed' => $completed,
  ]);
  return $db->lastInsertId();
}

function get_todo($id) {
  $db = db_connection();
  $stmt = $db->prepare('SELECT * FROM todos WHERE id = :id');
  $stmt->execute([
    ':id' => $id,
  ]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
