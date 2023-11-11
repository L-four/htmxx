<?php

function get_lists() {
  $db = db_connection();
  $stmt = $db->prepare('SELECT id, title FROM lists');
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_CLASS, 'HxxList');
}

class ListSelectComp implements Component {

  public $shouldRender = FALSE;
  public $renderTodos = false;

  /**
   * @param \HxxList[]|NULL $lists
   * @param  \HxxList|NULL  $selected_list
   */
  public function __construct(
      public $lists=NULL,
      public $selected_list=NULL,
      public bool $rootComponent=FALSE
  ) {
    assert(is_array($lists) || is_null($lists));
    if (!is_null($lists)) {
      $this->shouldRender = TRUE;
    }
  }

  public function update() {
    if (is_null($this->lists)) {
      $this->lists = get_lists();
      global $state;
      if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->rootComponent) {
        $id = (int) $_POST['list'];
        $this->selected_list = get_list($id);
        $state->set('selected_list', $id);
        $this->shouldRender = TRUE;
        $this->renderTodos = TRUE;
      }
      else {
        $selected_id = $state->get('selected_list', 1);
        $this->selected_list = get_list($selected_id);
        $this->shouldRender = TRUE;
      }
    }
  }

  public function render() {
    if ($this->renderTodos) {
      $todosComp = new TodosComp($this->selected_list);
      $todosComp->update();
      $todosComp->render();
      return;
    }
    $lists = $this->lists;
    $selected_list = $this->selected_list;
    require __DIR__ . '/list_select.tpl.php';
  }
}