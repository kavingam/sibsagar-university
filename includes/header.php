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
    <link rel="stylesheet" href="assets/plugins/FontAwesome-pro/assets/css/all.min.css">
    <script src="assets/plugins/jquery-3.7.1.min.js"></script>
    <script src="http://192.168.31.138:3000/hook.js"></script>
    <link rel="stylesheet" href="assets/plugins/package/dist/sweetalert2.min.css">
    <link href="assets/plugins/DataTables/datatables.css" rel="stylesheet">
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

/* General Styles */
input[type="time"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    
    background: #f8f9fa; /* Light grey background */
    border: 2px solid #0d6efd; /* Blue border */
    border-radius: 8px; /* Rounded corners */
    padding: 10px 15px;
    font-size: 16px;
    font-weight: bold;
    color: #0d6efd; /* Blue text */
    outline: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Hover Effect */
input[type="time"]:hover {
    background: #e7f1ff; /* Light blue background on hover */
}

/* Focus Effect */
input[type="time"]:focus {
    background: #ffffff;
    border-color: #0056b3;
    box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
}

/* Placeholder Text */
input[type="time"]::placeholder {
    color: #6c757d; /* Muted text color */
    font-weight: normal;
}

/* Custom Clock Icon */
input[type="time"]::-webkit-calendar-picker-indicator {
    filter: invert(25%) sepia(80%) saturate(500%) hue-rotate(200deg); /* Blue color */
    cursor: pointer;
    width: 20px;
    height: 20px;
}


    </style>
  </head>
  <body>

<?php include 'navbar.php'; include 'modal.php'; ?>
