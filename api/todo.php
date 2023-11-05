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
   * @return false|string|HxxError
   */
  function create_todo($title, $selected_id, $completed = false) {
    if (empty($selected_id)) {
      return new HxxError('No list selected', HxxError::BAD_INPUT);
    }
    $db = db_connection();
    $stmt = $db->prepare('INSERT INTO todos (title, list_id, completed) VALUES (:title, :list_id, :completed)');
    $stmt->execute([
      ':title' => $title,
      ':list_id' => $selected_id,
      ':completed' => $completed,
    ]);
    return $db->lastInsertId();
  }

  function get_todo(string $id) {
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

  /**
   * @param $id
   * @param $title
   *
   * @return void
   */
  function patch_title($id, $title) {
    $db = db_connection();
    $stmt = $db->prepare('UPDATE todos SET title = :title WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
      ':title' => $title,
    ]);
  }

  // check method
  switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
      global $state;
      $title = (string) $_POST['title'];
      $completed = isset($_POST['completed']) ? (int) $_POST['completed'] : 0;
      $selected_id = $state->get('selected_list', 1);
      $id = create_todo($title, $selected_id, $completed);
      if ($id instanceof HxxError) {
        print $id->message;
        $render = false;
      }
      else {
        $todo = get_todo($id);
        $render = TRUE;
      }
      break;
    case "PATCH":
      if ($id = entity_get_id()) {
        $PATCH = form_data();
        if (isset($PATCH['completed'])) {
          $completed = $PATCH['completed'] === 'on';
          patch_completed($id, $completed);
        }
        if (isset($PATCH['title'])) {
          $title = $PATCH['title'];
          patch_title($id, $title);
        }
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
        class="form-check-input my-auto"
        title="Todo item completed"
        type="checkbox"
        name="completed"
        <?php if ($todo->completed) { ?>checked<?php } ?>
        hx-patch="/api/todo?id=<?= $todo->id ?>"
        hx-swap="none"
    >
    <input
        class="mx-5 ps-2 form-control flex-grow-1"
        title="Todo title"
        type="text"
        name="title"
        hx-patch="/api/todo?id=<?= $todo->id ?>"
        value="<?= $todo->title ?>"
        hx-swap="none"
    />
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