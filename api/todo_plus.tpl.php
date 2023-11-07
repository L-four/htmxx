<?php
  /**
   * This should be defined in the parent template
   * @var \HxxTodo $todo
   */
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
