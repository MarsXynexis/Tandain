  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-heading">MENU UTAMA</li>

      <li class="nav-item">
        <a class="nav-link <?= ($data['active_menu'] ?? '') === 'dashboard' ? '' : 'collapsed'; ?>" href="/Tandain/dashboard">
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($data['active_menu'] ?? '') === 'documents' ? '' : 'collapsed'; ?>" href="/Tandain/document">
          <span>Dokumen</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($data['active_menu'] ?? '') === 'signing' ? '' : 'collapsed'; ?>" href="/Tandain/signing">
          <span>Penandatanganan</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($data['active_menu'] ?? '') === 'triage' ? '' : 'collapsed'; ?>" href="/Tandain/triage">
          <span>Triage</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($data['active_menu'] ?? '') === 'templates' ? '' : 'collapsed'; ?>" href="/Tandain/template">
          <span>Template</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($data['active_menu'] ?? '') === 'certificates' ? '' : 'collapsed'; ?>" href="/Tandain/certificate">
          <span>Sertifikat</span>
        </a>
      </li>

      <li class="nav-heading">PUBLIK</li>

      <li class="nav-item">
        <a class="nav-link <?= ($data['active_menu'] ?? '') === 'verification' ? '' : 'collapsed'; ?>" href="/Tandain/verification">
          <span>Verifikasi</span>
        </a>
      </li>

    </ul>

  </aside>
