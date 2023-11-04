<?php
include_once __DIR__ . '/../utils/db.php';

$todos = get_todos();

?>
<div id="todos">
  <ul>
  <?php foreach ($todos as $todo) { ?>
    <li class="todo">
      <input type="checkbox" <?php if ($todo['completed']) { ?>checked<?php } ?> onclick="hx.patch(this.closest('.todo'), '/api/todo.php?id=<?php echo $todo['id'] ?>', {completed: this.checked})">
      <span><?php echo $todo['title'] ?></span>
      <button onclick="hx.delete(this.closest('.todo'), '/api/todo.php?id=<?php echo $todo['id'] ?>')">Delete</button>
    </li>
  <?php } ?>
  </ul>
  <form hx-post="/api/todo/" hx-target="#todos">
    <input name="title" placeholder="New Todo">
    <button>Add</button>
  </form>
</div>