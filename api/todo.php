<?php
$render = false;
if (!isset($todo)) {
  include_once __DIR__ . '/../utils/db.php';
  include_once __DIR__ . '/../utils/form.php';
  include_once __DIR__ . '/../utils/data_classes.php';
  include_once __DIR__ . '/../utils/entity.php';

  /**
   * @param $title string
   * @param $selected_id int
   * @param $completed bool
   *
   * @return false|string
   */
  function create_todo($title, $selected_id, $completed = false) {
    $db = db_connection();
    $stmt = $db->prepare('INSERT INTO todos (title, list_id, completed) VALUES (:title, :list_id, :completed)');
    $stmt->execute([
      ':title' => $title,
      ':list_id' => $selected_id,
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
    return $stmt->fetchObject( 'HxxTodo');
  }

  function delete_todo($id) {
    $db = db_connection();
    $stmt = $db->prepare('DELETE FROM todos WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
    ]);
  }

  /**
   * @param $id int
   * @param $completed bool
   *
   * @return void
   */
  function patch_completed($id, $completed) {
    $db = db_connection();
    $stmt = $db->prepare('UPDATE todos SET completed = :completed WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
      ':completed' => (int) $completed,
    ]);
  }

  // check method
  switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
      $title = (string) $_POST['title'];
      $completed = isset($_POST['completed']) ? (int) $_POST['completed'] : 0;
      $selected_id = get_key_value('selected_list');
      $id = create_todo($title, $selected_id, $completed);
      $todo = get_todo($id);
      $render = true;
      break;
    case "PATCH":
      if ($id = entity_get_id()) {
        $PATCH = form_data();
        $completed = isset($PATCH['completed']) ? $PATCH['completed'] === 'on' : false;
        patch_completed($id, $completed);
      }
      break;
    case "DELETE":
      if ($id = entity_get_id()) {
        delete_todo($id);
      }
      print "";
      break;
    default:
      print "Error: no todo found\n";
      break;
  }
}
else {
  $render = true;
}



if ($render) {
?>
<li class="todo">
  <form class="d-flex mb-3">
    <input
        class="form-check-input"
        title="Todo item completed"
        type="checkbox" name="completed"
        <?php if ($todo->completed) { ?>checked<?php } ?>
        hx-patch="/api/todo?id=<?= $todo->id ?>"
        hx-swap="none"
    >
    <span class="ps-2">
      <?php echo $todo->title ?>
    </span>
    <button
        class="btn btn-danger ms-auto p-2"
        hx-target="closest .todo"
        hx-swap="outerHTML"
        hx-delete="/api/todo?id=<?= $todo->id ?>"
    >Delete</button>
  </form>
</li>
<?php
}
?>