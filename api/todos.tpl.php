<?php
/**
 * @var $todos \HxxTodo[]
 * @var $trigger string
 * @var $selected_list \HxxList|NULL
 **/
?>
<div id="todos-container" <?= $trigger ?> >
  <?php
    $listSelectComp = new ListSelectComp(selected_list: $selected_list ?? NULL);
    $listSelectComp->update();
    $listSelectComp->render();
  ?>
  <h1 class="mb-4">My Todos</h1>
  <ul id="todos" class="list-unstyled">
    <?php
    foreach ($todos as $todo) {
      $todoComp = new TodoComp($todo);
      $todoComp->update();
      $todoComp->render();
    }
    ?>
  </ul>
  <div class="d-flex">
    <!--    <div class="ms-auto">-->
    <!--      <button name="hide-completed" class="btn btn-outline-secondary">Hide Completed</button>-->
    <!--    </div>-->
    <div class="ms-auto">
      <h4 class="me-auto">Add todo</h4>
      <form
        class="d-flex"
        hx-post="/api/todo"
        hx-target="#todos"
        hx-swap="beforeend">
        <input
          class="form-control"
          title="Todo title"
          name="title"
          placeholder="New Todo"
        >
        <button class="btn btn-primary">Add</button>
      </form>
    </div>
  </div>
</div>
