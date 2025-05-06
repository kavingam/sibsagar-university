<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Responsive Login</title>
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
    .login-card {
      background-color: #fff;
      border-radius: 1rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      padding: 2rem;
      width: 100%;
      max-width: 400px;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #2575fc;
    }
    .btn-primary {
      background-color: #2575fc;
      border: none;
    }
    .btn-primary:hover {
      background-color: #1e60d4;
    }
    @media (max-width: 576px) {
      .login-card {
        padding: 1.5rem;
      }
      h3 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h3 class="text-center mb-4">Login to Your Account</h3>
    <form id="loginForm">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" placeholder="Enter email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Enter password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
      <div class="text-center mt-3">
        <a href="forgot.php" class="text-decoration-none">Forgot password?</a>
      </div>
    </form>
    <div id="loginMessage" class="mt-2 text-center"></div>
  </div>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#loginForm').on('submit', function(e) {
    e.preventDefault(); // Prevent normal form submission

    var email = $('#email').val();
    var password = $('#password').val();

    $.ajax({
      url: 'xyz/var_log/var-login.php',
      type: 'POST',
      data: { email: email, password: password },
      success: function(response) {
        if (response === "Login successful!") {
          // If login is successful, the server will handle the redirection via session
          window.location.href = 'index.php'; // Redirect to the dashboard or desired page
        } else {
          // Show error message if login failed
          $('#loginMessage').html('<div class="alert alert-danger">' + response + '</div>');
        }
      },
      error: function() {
        $('#loginMessage').html('<div class="alert alert-danger">AJAX request failed.</div>');
      }
    });
  });
</script>

</html>
