<?php
/**
 * @var $lists \HxxList[]
 * @var $selected_list \HxxList
 **/
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
      $option = new ListComp($list, $selected);
      $option->update();
      $option->render();
    }
    ?>
  </select>
</form>