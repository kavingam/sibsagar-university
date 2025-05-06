<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .forgot-card {
      background-color: #fff;
      border-radius: 1rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      padding: 2rem;
      width: 100%;
      max-width: 400px;
    }
    .btn-primary {
      background-color: #2575fc;
      border: none;
    }
    .btn-primary:hover {
      background-color: #1e60d4;
    }
    @media (max-width: 576px) {
      .forgot-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="forgot-card">
    <h3 class="text-center mb-4">Forgot Password</h3>
    <p class="text-center text-muted mb-4">Enter your email and we’ll send you a link to reset your password.</p>
    <form>
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" id="email" class="form-control" placeholder="Enter your email" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
      <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none">Back to Login</a>
      </div>
    </form>
  </div>

</body>
</html>
