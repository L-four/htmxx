<?php
class HxxList {
  const _TABLE = 'lists';

  public $id;
  public $title;
}

class HxxTodo {
  const _TABLE = 'todos';

  public $id;
  public $title;
  public $list_id;
  public $completed;
}