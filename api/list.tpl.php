<?php
/**
 * @var $list \HxxList
 * @var $is_selected bool
 **/
?>
<option value="<?= $list->id ?>" <?= ($is_selected)? 'selected="selected"' : '' ?>><?= $list->title ?></option>