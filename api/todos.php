<?php
include_once __DIR__ . '/../utils/db.php';
function get_todos() {
  $db = db_connection();
  $stmt = $db->prepare('SELECT * FROM todos');
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$todos = get_todos();
?>
<div id="todos-container">
  <h1 class="mb-4">My Todos</h1>
  <ul id="todos" class="list-unstyled">
  <?php
    foreach ($todos as $todo) {
      include __DIR__ . '/todo.php';
    }
  ?>
  </ul>
  <div class="d-flex">
    <div class="ms-auto">
      <h4 class="me-auto">Add todo</h4>
      <form
          hx-post="/api/todo.php"
          hx-target="#todos"
          hx-swap="beforeend">
        <input name="title" placeholder="New Todo">
        <button class="btn ">Add</button>
      </form>
    </div>
  </div>
</div>