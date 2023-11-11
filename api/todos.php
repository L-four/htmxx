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

class TodosComp implements Component {
  public $shouldRender = FALSE;
  public $trigger = NULL;

  public function __construct(public \HxxList|NULL $selected_list=NULL) {}

  public function update() {
    if (is_null($this->selected_list)) {
      global $state;
      $list_id = $state->get('selected_list', 1);
      $this->selected_list = get_list($list_id);
    }
    $this->todos = get_todos($this->selected_list->id);
    $auto_reload = false;
    $this->trigger = ($auto_reload) ? 'hx-trigger="every 5s" hx-get="/api/todos" hx-swap="outerHTML"' : '';
  }

  public function render() {
    $todos = $this->todos;
    $trigger = $this->trigger;
    $selected_list = $this->selected_list;
    require __DIR__ . '/todos.tpl.php';
  }
}
