<?php
/**
 * @var $lists array<\HxxList>
 * @var $selected_list \HxxList
 **/
$render = false;
if (!isset($lists)) {
  include_once __DIR__ . '/../utils/db.php';
  include_once __DIR__ . '/../utils/data_classes.php';
  include_once __DIR__ . '/list.php';

  function get_lists() {
    $db = db_connection();
    $stmt = $db->prepare('SELECT id, title FROM lists');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'HxxList');
  }
  $lists = get_lists();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['list'];
    $selected_list = get_list($id);
    set_key_value('selected_list', $id);
    include __DIR__ . '/todos.php';
    $render = false;
  }
  else {
    $selected_list = get_list(get_key_value('selected_list'));
    $render = true;
  }
}
else {
  $render = true;
}

if ($render) {
?>
<form>
  <select title="Todo lists" name="list" hx-post="/api/list_select" hx-target="#todos-container" hx-swap="outerHTML">
    <?php
    foreach ($lists as $list) {
      $selected = $list->id == $selected_list->id;
      include __DIR__ . '/list.php';
    }
    ?>
  </select>
</form>
<?php
}