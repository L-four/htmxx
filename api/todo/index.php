<?php
include_once __DIR__ . '/../../utils/db.php';

// check method
switch ($_SERVER['REQUEST_METHOD']) {
  case "POST":
    $id = create_todo($_POST['title'], $_POST['completed']);
    include __DIR__ . '/../todos.php';
    break;
}