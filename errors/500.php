<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>500 Internal Server Error</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
      color: #ffc107;
    }
    .error-message {
      font-size: 1.5rem;
      color: #6c757d;
    }
  </style>
</head>
<body>

  <div class="container error-container">
    <div class="error-code">500</div>
    <h1 class="mb-3">Internal Server Error</h1>
    <p class="error-message mb-4">Oops! Something went wrong on our end. Please try again later.</p>
    <a href="/" class="btn btn-primary">Return Home</a>
  </div>

</body>
</html>
