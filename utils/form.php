<?php

function request_body() {
  return file_get_contents('php://input');
}

function patch_data() {
  $body = request_body();
  $data = [];
  parse_str($body, $data);
  return $data;
}
