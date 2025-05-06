<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>403 Forbidden</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .error-container {
      text-align: center;
    }
    .error-code {
      font-size: 10rem;
      font-weight: bold;
      color: #0d6efd;
    }
    .error-message {
      font-size: 1.5rem;
      color: #6c757d;
    }
  </style>
</head>
<body>

  <div class="container error-container">
    <div class="error-code">403</div>
    <h1 class="mb-3">Access Forbidden</h1>
    <p class="error-message mb-4">You don't have permission to access this page.</p>
    <a href="/" class="btn btn-primary">Back to Home</a>
  </div>

</body>
</html>
