<?php

include_once 'utils/db.php';
include_once 'utils/form.php';
include_once 'utils/data_classes.php';
include_once 'utils/entity.php';

if (!class_exists('TodoComp')) {
  /**
   * @param $title string
   * @param $selected_id int
   * @param $completed bool
   *
   * @return false|string|HxxError
   */
  function create_todo($title, $selected_id, $completed = FALSE) {
    if (empty($selected_id)) {
      return new HxxError('No list selected', HxxError::BAD_INPUT);
    }
    $db = db_connection();
    $stmt = $db->prepare('INSERT INTO todos (title, list_id, completed) VALUES (:title, :list_id, :completed)');
    $stmt->execute([
      ':title'     => $title,
      ':list_id'   => $selected_id,
      ':completed' => $completed,
    ]);
    return $db->lastInsertId();
  }

  function get_todo(string $id) {
    return auto_get('HxxTodo', $id);
  }

  function delete_todo($id) {
    return auto_delete('HxxTodo', $id);
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
      ':id'        => $id,
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
      ':id'    => $id,
      ':title' => $title,
    ]);
  }

  class TodoComp implements Component {

    public $shouldRender = FALSE;

    public function __construct(public HxxTodo|null $todo) {
      if (!is_null($todo)) {
        $this->shouldRender = TRUE;
      }
    }

    public function update() {
      if (is_null($this->todo)) {
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
              $this->shouldRender = FALSE;
            }
            else {
              $this->todo = get_todo($id);
              $this->shouldRender = TRUE;
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
        $this->shouldRender = TRUE;
      }
    }

    public function render() {
      $todo = $this->todo;
      include __DIR__ . '/todo.tpl.php';
    }
  }
}

$comp = new TodoComp($todo ?? NULL);
$comp->update();
if ($comp->shouldRender) {
  $comp->render();
}