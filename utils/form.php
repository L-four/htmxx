<?php

function request_body() {
  $data = file_get_contents('php://input');
  return $data;
}

function form_data() {
  $body = request_body();
  $data = [];
  parse_str($body, $data);
  return $data;
}
