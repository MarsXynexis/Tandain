<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= $data['title'] ?? 'Dashboard'; ?></title>

  <link rel="stylesheet" href="/Tandain/assets/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f2f2f2;
    }

    .result-container {
      background: #fff;
      padding: 24px;
      border: 1px solid #ccc;
      border-radius: 4px;
      margin-top: 60px;
      margin-bottom: 60px;
    }

    .result-container h4 {
      margin-bottom: 20px;
    }
  </style>
</head>

<body>

  <section class="container-fluid">
    <section class="row justify-content-center">
      <section class="col-12 col-sm-8 col-md-5 col-lg-4">

        <div class="result-container">

          <h4 class="text-center font-weight-bold">Informasi Login</h4>

          <p>Nama anda adalah <strong><?= htmlspecialchars($data['user']['full_name']); ?></strong></p>
          <p>Email anda adalah <strong><?= htmlspecialchars($data['user']['email']); ?></strong></p>
          <p>Status login: <strong class="text-success">Berhasil</strong></p>
          <p>Kode CAPTCHA anda <strong class="text-success">benar</strong></p>

          <div class="text-center mt-3">
            <a href="/Tandain/logout" class="btn btn-danger">Logout</a>
          </div>

        </div>

      </section>
    </section>
  </section>

  <script src="/Tandain/assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>
