<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= $data['title'] ?? 'Register - Tandain'; ?></title>

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

    .btn-register {
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

          <h4 class="text-center font-weight-bold">Sign-Up</h4>

          <?php if ($data['error'] !== '') : ?>
            <div class="alert alert-danger py-2" role="alert"><?= $data['error']; ?></div>
          <?php endif; ?>

          <form action="/Tandain/register" method="POST">

            <div class="mb-2">
              <label for="full_name" class="form-label">Nama</label>
              <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Masukkan nama">
            </div>

            <div class="mb-2">
              <label for="identity_number" class="form-label">NIP / NIM</label>
              <input type="text" class="form-control" id="identity_number" name="identity_number" placeholder="Masukkan NIP atau NIM">
            </div>

            <div class="mb-2">
              <label for="InputEmail" class="form-label">Alamat Email</label>
              <input type="email" class="form-control" id="InputEmail" name="email" placeholder="Masukkan email">
            </div>

            <div class="mb-2">
              <label for="InputPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="InputPassword" name="password" placeholder="Password">

              <?php if ($data['validate'] !== '') : ?>
                <p class="text-danger small"><?= $data['validate']; ?></p>
              <?php endif; ?>
            </div>

            <div class="mb-2">
              <label for="InputRePassword" class="form-label">Re-Password</label>
              <input type="password" class="form-control" id="InputRePassword" name="repassword" placeholder="Re-Password">

              <?php if ($data['validate'] !== '') : ?>
                <p class="text-danger small"><?= $data['validate']; ?></p>
              <?php endif; ?>
            </div>

            <div class="mb-2">
              <label class="form-label">Captcha</label>
              <img src="/Tandain/captcha" id="captcha-img" alt="captcha" title="Klik untuk refresh">
              <input type="text" class="form-control" name="captcha" placeholder="Isikan captcha" autocomplete="off">
            </div>

            <button type="submit" class="btn btn-primary btn-register">Register</button>

          </form>

          <div class="form-footer text-center">
            <p class="mb-0">Sudah punya account? <a href="/Tandain/login">Login</a></p>
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
