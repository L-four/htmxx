<?php

include_once __DIR__ . '/../utils/db.php';
include_once __DIR__ . '/../utils/data_classes.php';
include_once __DIR__ . '/../utils/entity.php';


function get_list($id) {
  $db = db_connection();
  $stmt = $db->prepare('SELECT id, title FROM lists WHERE id = :id');
  $stmt->execute([
    ':id' => $id,
  ]);
  return $stmt->fetchObject( "HxxList");
}

class ListComp implements Component {
  public $shouldRender = FALSE;

  /**
   * @param \HxxList|NULL $list
   * @param  bool|NULL $is_selected
   */
  public function __construct(
      public $list=NULL,
      public $is_selected=NULL
  ) {
    if (!is_null($list)) {
      $this->shouldRender = TRUE;
    }
  }

  public function update() {
    if ($id = entity_get_id()) {
      $this->list = get_list($id);
      $this->shouldRender = true;
    }
  }

  public function render() {
    $list = $this->list;
    $is_selected = $this->is_selected;
    require __DIR__ . '/list.tpl.php';
  }
}
