<?php
$render = false;
if (!isset($todo)) {
  include_once __DIR__ . '/../utils/db.php';
  include_once __DIR__ . '/../utils/form.php';

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
      $id = create_todo($title, $completed);
      $todo = get_todo($id);
      $render = true;
      break;
    case "PATCH":
      if ($_GET['id']) {
        $id = (int)$_GET['id'];
        $PATCH = patch_data();
        $completed = isset($PATCH['completed']) ? $PATCH['completed'] === 'on' : false;
        patch_completed($id, $completed);
      }
      break;
    case "DELETE":
      if ($_GET['id']) {
        $id = (int)$_GET['id'];
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
        <?php if ($todo['completed']) { ?>checked<?php } ?>
        hx-patch="/api/todo.php?id=<?= $todo['id'] ?>"
    >
    <span class="ps-2">
      <?php echo $todo['title'] ?>
    </span>
    <button
        class="btn btn-danger ms-auto p-2"
        hx-target="closest .todo"
        hx-swap="outerHTML"
        hx-delete="/api/todo.php?id=<?= $todo['id'] ?>"
    >Delete</button>
  </form>
</li>
<?php
}
?>