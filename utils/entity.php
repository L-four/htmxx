<?php

/**
 * @return int | null
 */
function entity_get_id() {
  if (!empty($_GET['id'])) {
    return (int) $_GET['id'];
  }
  return null;
}