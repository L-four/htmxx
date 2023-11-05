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
  global $state;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['list'];
    $selected_list = get_list($id);
    $state->set('selected_list', $id);
    include __DIR__ . '/todos.php';
    $render = false;
  }
  else {
    $selected_id = $state->get('selected_list', 1);
    $selected_list = get_list($selected_id);
    $render = true;
  }
}
else {
  $render = true;
}

if ($render) {
?>
<form>
  <select
      class="form-select"
      title="Todo lists"
      name="list"
      hx-post="/api/list_select"
      hx-target="#todos-container"
      hx-swap="outerHTML"
  >
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