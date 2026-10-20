<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= $data['title'] ?? 'Login - Tandain'; ?></title>

  <link rel="stylesheet" href="/Tandain/assets/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f2f2f2;
    }

    .form-container {
      background: #fff;
      padding: 24px;
      border: 1px solid #ccc;
      border-radius: 4px;
      margin-top: 60px;
      margin-bottom: 60px;
    }

    .form-container h4 {
      margin-bottom: 20px;
    }

    .form-label {
      font-weight: normal;
      margin-bottom: 4px;
    }

    .form-control {
      border-radius: 2px;
      margin-bottom: 12px;
    }

    .btn-login {
      display: block;
      margin: 8px auto 0;
      padding: 6px 20px;
    }

    .form-footer {
      margin-top: 12px;
      font-size: 14px;
    }

    .form-footer a {
      color: #0d6efd;
      text-decoration: none;
    }

    #captcha-img {
      cursor: pointer;
      border-radius: 2px;
      display: block;
      margin-bottom: 6px;
    }
  </style>
</head>

<body>

  <section class="container-fluid">
    <section class="row justify-content-center">
      <section class="col-12 col-sm-8 col-md-5 col-lg-4">

        <div class="form-container">

          <h4 class="text-center font-weight-bold">Halaman Login</h4>

          <?php if ($data['error'] !== '') : ?>
            <div class="alert alert-danger py-2" role="alert"><?= $data['error']; ?></div>
          <?php endif; ?>

          <form action="/Tandain/login" method="POST">

            <div class="mb-2">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email">
            </div>

            <div class="mb-2">
              <label for="InputPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="InputPassword" name="password" placeholder="Password">
            </div>

            <div class="mb-2">
              <label class="form-label">Captcha</label>
              <img src="/Tandain/captcha" id="captcha-img" alt="captcha" title="Klik untuk refresh">
              <input type="text" class="form-control" name="captcha" placeholder="Isikan captcha" autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary btn-login">Login</button>

          </form>

          <div class="form-footer text-center">
            <p class="mb-0">Belum punya account? <a href="/Tandain/register">Register</a></p>
          </div>

        </div>

      </section>
    </section>
  </section>

  <script src="/Tandain/assets/js/bootstrap.bundle.min.js"></script>

  <script>
    document.getElementById('captcha-img').addEventListener('click', function () {
      this.src = '/Tandain/captcha?' + Date.now();
    });
  </script>

</body>
</html>
