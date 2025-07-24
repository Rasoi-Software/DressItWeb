<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Code Verification</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 400px;
      margin-top: 50px;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .code-input {
      width: 45px;
      height: 45px;
      text-align: center;
      font-size: 20px;
      margin: 5px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    .btn-gradient {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      color: white;
      border: none;
    }
    .btn-gradient:hover {
      opacity: 0.9;
    }
    .resend-link {
      color: #6a11cb;
      text-decoration: none;
      font-weight: bold;
    }
    .resend-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container text-center">
  <h5 class="fw-bold">We sent link to your <br> Email</h5>
  <p>Check your email and enter the code, or just click the link we sent you.</p>

  <div class="d-flex justify-content-center">
    <input type="text" maxlength="1" class="code-input" />
    <input type="text" maxlength="1" class="code-input" />
    <input type="text" maxlength="1" class="code-input" />
    <input type="text" maxlength="1" class="code-input" />
    <input type="text" maxlength="1" class="code-input" />
    <input type="text" maxlength="1" class="code-input" />
  </div>

  <p class="mt-3">
    Didn't receive email yet? <a href="#" class="resend-link">Resend</a>
    <span>(1:00)</span>
  </p>

  <div class="d-grid gap-2">
    <button class="btn btn-gradient rounded-pill">Apply</button>
    <button class="btn btn-outline-secondary rounded-pill">Cancel</button>
  </div>
</div>

</body>
</html>
