<?php
// include 'config.php';
// include 'db/pdo_connect.php';
?>

<?php
// Include configuration and DB connection files
include 'config.php';
include 'db/pdo_connect.php';

?>

<!doctype html>
<html lang="en">
  <head>
    <title>Advance Automated Seat Plan Generator</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="assets/layout/css/styles.css" type="text/css"> -->
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/plugins/FontAwesome-pro/assets/css/all.min.css">
    <script src="assets/plugins/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="assets/plugins/package/dist/sweetalert2.min.css">
    <link href="assets/plugins/DataTables/datatables.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/printCss/a4.css">
    <style>
      .navbar-nav .nav-link {
          color: white !important;
      }
      .navbar-nav .nav-link.active {
          color: red !important;
          /* font-weight: bold; */
      }
      /* Style dropdown items in navbar */
      .navbar .dropdown-menu a.dropdown-item {
        color: #6a1b9a;
        background-color: #f9f9f9;
        padding: 10px 16px;
        border-bottom: 1px solid #e0e0e0;
        text-align: left;
        font-weight: 500;
        transition: background-color 0.3s ease;
      }

      .navbar .dropdown-menu a.dropdown-item:hover {
        background-color: #e1bee7; /* Light purple hover */
        color: #4a148c;
      }

      /* Center and size dropdown on small devices */
      @media (max-width: 768px) {
        .navbar .dropdown-menu {
          /* left: 50% !important; */
          /* transform: translateX(-50%) !important; */
          width: 90vw;
          max-width: 320px;
          text-align: center;
          border-radius: 12px;
          box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar .dropdown-menu a.dropdown-item {
          text-align: center;
        }
      }
    /* General Section Styling */
    .import-section, .export-section {
        background: #f9f9ff;
        /* padding: 2rem; */
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
        margin: 1rem;
    }

    /* Title Styling */
    .import-section .section-title, .export-section .section-title {
        color: #000; /* Black */
        font-weight: 700;
        font-size: 1.6rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    /* Subtext Styling */
    .import-section .section-subtext, .export-section .section-subtext {
        color: #000; /* Black */
        font-size: 1rem;
        text-align: center;
    }

    /* Responsive design for mobile devices */
    @media (max-width: 576px) {
        .import-section, .export-section {
            padding: 1.5rem;
        }

        .import-section .section-title, .export-section .section-title {
            font-size: 1.4rem;
        }

        .import-section .section-subtext, .export-section .section-subtext {
            font-size: 0.95rem;
        }
    }


      /* Responsive tweaks for screens smaller than 576px (like Android phones) */
      @media (max-width: 576px) {
          .import-section {
              padding: 1.5rem;
              margin: 1rem 0.5rem;
          }

          .import-section .section-title {
              font-size: 1.4rem;
          }

          .import-section .section-subtext {
              font-size: 0.95rem;
          }
      }
/* This will center the button group horizontally and vertically inside the table cell */
td.text-center {
    display: flex;
    justify-content: center;  /* Centers buttons horizontally */
    align-items: center;      /* Vertically centers the buttons */
    height: 100%;             /* Makes sure the cell takes full height */
}

/* Optional: Space out the buttons slightly */
.btn-group a {
    margin: 0 5px; /* Adds horizontal spacing between buttons */
}



    .fixed-header-table {
            table-layout: fixed;
            width: 100%;
        }
        .scrollable-body {
            max-height: 400px;
            overflow-y: auto;
        }
        .scrollable-body table {
            table-layout: fixed;
            width: 100%;
        }


      .btn-xs {
          padding: 0.25rem 0.5rem;
          font-size: 0.75rem;
      }
      .scrollable-container {
        max-height: 70vh;
        overflow-y: auto; /* Enable vertical scrolling */
      }
      .fs-6 {
        font-size: 0.5rem;
      }
      .fs-7 {
        font-size: 0.7rem;
      }
      .fs-8 {
        font-size: 0.8rem;
      }
      .fs-9 {
        font-size: 0.9rem;
      }
      .fs-10 {
        font-size: 1rem;
      }

      .student-card {
        border: 1px solid #000 !important;
        /* padding: 10mm !important; */
        /* margin-bottom: 10mm !important; */
      }

      @media print {
        body {
            margin: 0;
            padding: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .container-fluid {
            padding: 0 !important;
            margin: 0 auto;
        }

        /* .row {
            page-break-inside: avoid;
            margin: 0;
        }

        .col {
            border: 1px solid #ccc;
            padding: 10px;
            font-size: 12px;
            width: 166px !important;
            height: 65px !important;
            flex: 0 0 auto !important;
            page-break-inside: avoid;
        } */
    }

    </style>
  </head>
  <body class="mt-5">

<?php include 'navbar.php';
include 'modal.php'; ?>
