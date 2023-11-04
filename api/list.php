<?php
/**
 * @var $list \HxxList
 * @var $selected bool
 **/
$render = false;
if (!isset($list)) {
  include_once __DIR__ . '/../utils/db.php';
  include_once __DIR__ . '/../utils/data_classes.php';
  include_once __DIR__ . '/../utils/entity.php';

  function get_list($id) {
    $db = db_connection();
    $stmt = $db->prepare('SELECT id, title FROM lists WHERE id = :id');
    $stmt->execute([
      ':id' => $id,
    ]);
    return $stmt->fetchObject(PDO::FETCH_CLASS, "HxxList");
  }
  if ($id = entity_get_id()) {
    $list = get_list($id);
    $render = true;
  }
}
else {
  $render = true;
}

if ($render) {
?>
<option value="<?= $list->id ?>" <?= ($selected)? 'selected="selected"' : '' ?>><?= $list->title ?></option>
<?php
}
?>
