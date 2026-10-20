  <footer id="footer" class="footer">
    <div class="copyright text-center py-3 text-muted">
      &copy; Tandain <strong><span>PNJ</span></strong>
    </div>
  </footer>

  <script src="/Tandain/assets/js/bootstrap.bundle.min.js"></script>

  <script src="/Tandain/assets/js/app.js"></script>

  <?php
    $flashSuccess = $data['success'] ?? $_SESSION['success'] ?? null;
    $flashError = $data['error'] ?? $_SESSION['error'] ?? null;
    unset($_SESSION['success'], $_SESSION['error']);
  ?>

</body>
</html>
