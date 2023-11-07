<?php
include_once __DIR__ . '/../utils/db.php';
include_once __DIR__ . '/../utils/data_classes.php';

function get_todos($list_id) {
  $db = db_connection();
  $stmt = $db->prepare('SELECT id, title, list_id, completed FROM todos WHERE list_id = :list_id');
  $stmt->execute([
    ':list_id' => $list_id,
  ]);
  return $stmt->fetchAll(PDO::FETCH_CLASS, 'HxxTodo');
}
global $state;
$list_id = $state->get('selected_list', 1);
$todos = get_todos($list_id);

$auto_reload = false;

$trigger = ($auto_reload) ? 'hx-trigger="every 5s" hx-get="/api/todos" hx-swap="outerHTML"' : '';

?>
<div id="todos-container" <?= $trigger ?> >
  <?php
    include __DIR__ . '/list_select.php';
  ?>
  <h1 class="mb-4">My Todos</h1>
  <ul id="todos" class="list-unstyled">
  <?php
    foreach ($todos as $todo) {
      include __DIR__ . '/todo_plus.php';
    }
  ?>
  </ul>
  <div class="d-flex">
<!--    <div class="ms-auto">-->
<!--      <button name="hide-completed" class="btn btn-outline-secondary">Hide Completed</button>-->
<!--    </div>-->
    <div class="ms-auto">
      <h4 class="me-auto">Add todo</h4>
      <form
          class="d-flex"
          hx-post="/api/todo"
          hx-target="#todos"
          hx-swap="beforeend">
        <input
            class="form-control"
            title="Todo title"
            name="title"
            placeholder="New Todo"
        >
        <button class="btn btn-primary">Add</button>
      </form>
    </div>
  </div>
</div>