<?php


$folders = [
  'api',
  'utils',
];

foreach ($folders as $folder) {
  foreach (glob($folder . '/*.php') as $filename) {
    if (str_ends_with($filename, 'tpl.php')) {
      continue;
    }
    include_once $filename;
  }
}
