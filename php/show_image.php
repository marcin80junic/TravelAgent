<?php

  $name = false;

  if (isset($_GET['image'])) {

    $ext = strtolower(substr($_GET['image'], -4));
    if ($ext === ".jpg" || $ext === "jpeg" || $ext === ".png") {

      $image = "../../../../xxuploads/{$_GET['image']}";

      if (file_exists($image) && is_file($image)) {
        $name = $_GET['image'];
      }
    }
  }

  if (!$name) {
    $image = "../img/unavailable.png";
    $name = "unavailable.png";
  }

  $image_info = getimagesize($image);
  $file_size = filesize($image);

  header("Content-Type: {$image_info['mime']}\n");
  header("Content-Disposition: inline; filename=$name\n");
  header("X-Sendfile: c:/xampp/xxuploads/$name");
  header("Content-Length: $file_size\n");

  readfile($image);
