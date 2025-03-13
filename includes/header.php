<?php include 'config.php'; include 'db/pdo_connect.php'; ?>

<!doctype html>
<html lang="en">
  <head>
    <title></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/layout/css/styles.css" type="text/css">
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css">
    <script src="assets/plugins/jquery-3.7.1.min.js"></script>
    <style>
      .btn-xs {
          padding: 0.25rem 0.5rem;
          font-size: 0.75rem;
      }
      .scrollable-container {
        max-height: 70vh;
        overflow-y: auto; /* Enable vertical scrolling */
      }
      .fs-7 {
        font-size: 0.7rem;
      }
    </style>
  </head>
  <body>

<?php include 'navbar.php'; include 'modal.php'; ?>
