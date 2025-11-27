<div class="container-xl">
  <!-- BEGIN NAVBAR TOGGLER -->
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <!-- END NAVBAR TOGGLER -->
  <!-- BEGIN NAVBAR LOGO -->
  <div class="navbar-brand d-none-navbar-horizontal pe-0 pe-md-3">
    <a href="." aria-label="Tabler">
      <img src="{{ asset('logo.png') }}" height="36" alt="" class="navbar-brand-image">
    </a>
  </div>
  <!-- END NAVBAR LOGO -->
  <div class="navbar-nav flex-row order-md-last">
    <div class="d-none d-md-flex">
      <div class="nav-item">
        <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
          <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
            <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
          </svg>
        </a>
        <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode" data-bs-toggle="tooltip" data-bs-placement="bottom">
          <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
            <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
            <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
          </svg>
        </a>
      </div>
    </div>
    <div class="nav-item dropdown">
      @include('layouts._navbar_user')
    </div>
  </div>
  <div class="collapse navbar-collapse" id="navbar-menu">
    <!-- BEGIN NAVBAR MENU -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
          <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/home -->
            <i class="ti ti-layout-dashboard"></i>
          </span>
          <span class="nav-link-title"> Dashboard </span>
        </a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
          <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/package -->
            <i class="ti ti-calendar-user"></i>
          </span>
          <span class="nav-link-title"> Registrasi </span>
        </a>
        <div class="dropdown-menu">
          <div class="dropdown-menu-columns">
            <div class="dropdown-menu-column">
              @if (Auth::user()->hasRole(['admin', 'apoteker', 'loket']))
                <a class="dropdown-item" href="{{ route('registrasi.pasien.index') }}">
                  Daftar Pasien
                </a>
              @endif
              @if (Auth::user()->hasRole(['admin', 'dokter', 'loket']))
                <a class="dropdown-item" href="{{ route('registrasi.kunjungan.index') }}">
                  Daftar Kunjungan Pasien
                </a>
              @endif
            </div>
          </div>
        </div>
      </li>
      @if (Auth::user()->hasRole(['admin', 'loket']))
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/package -->
              <i class="ti ti-cash-register"></i>
            </span>
            <span class="nav-link-title"> Kasir </span>
          </a>
          <div class="dropdown-menu">
            <div class="dropdown-menu-columns">
              <div class="dropdown-menu-column">
                @if (Auth::user()->hasRole(['admin', 'loket']))
                  <a class="dropdown-item" href="{{ route('kasir.tagihan-pasien') }}">
                    Tagihan Tindakan Pasien
                  </a>
                @endif
              </div>
            </div>
          </div>
        </li>
      @endif
      @if (Auth::user()->hasRole(['admin', 'apoteker']))
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/package -->
              <i class="ti ti-medicine-syrup"></i>
            </span>
            <span class="nav-link-title"> Farmasi </span>
          </a>
          <div class="dropdown-menu">
            <div class="dropdown-menu-columns">
              <div class="dropdown-menu-column">
                <a class="dropdown-item" href="{{ route('farmasi.resep-pasien.index') }}">
                  Resep Pasien
                </a>
                <a class="dropdown-item" href="{{ route('farmasi.stok-obat.index') }}">
                  Stok Obat
                </a>
                <a class="dropdown-item" href="{{ route('farmasi.pembelian.index') }}">
                  Pembelian Obat
                </a>
                <div class="dropend">
                  <a class="dropdown-item dropdown-toggle" href="#sidebar-authentication" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                    Penjualan
                  </a>
                  <div class="dropdown-menu">
                    <a href="{{ route('farmasi.penjualan.index') }}" class="dropdown-item"> Daftar Penjualan </a>
                    <a href="{{ route('farmasi.penjualan.create', ['jenis' => 'bebas']) }}" class="dropdown-item"> Buat Penjualan Obat Bebas</a>
                    <a href="{{ route('farmasi.penjualan.create', ['jenis' => 'apotek']) }}" class="dropdown-item"> Buat Penjualan Sesama Apotek</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </li>
      @endif

      @if (Auth::user()->hasRole(['admin', 'apoteker', 'loket']))
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler.io/icons/icon/package -->
              <i class="ti ti-server"></i>
            </span>
            <span class="nav-link-title"> Master Data </span>
          </a>
          <div class="dropdown-menu">
            <div class="dropdown-menu-columns">
              <div class="dropdown-menu-column">
                <a class="dropdown-item" href="{{ route('master.pengguna.index') }}">
                  Pengguna
                </a>
                <a class="dropdown-item" href="{{ route('master.departemen.index') }}">
                  Departemen
                </a>
                <a class="dropdown-item" href="{{ route('master.ruangan.index') }}">
                  Ruangan
                </a>
                <a class="dropdown-item" href="{{ route('master.produk.index', ['jenis' => 'tindakan']) }}">
                  Tindakan
                </a>
                <a class="dropdown-item" href="{{ route('master.produk.index', ['jenis' => 'obat']) }}">
                  Obat
                </a>
                <a class="dropdown-item" href="{{ route('master.suplier.index') }}">
                  Suplier
                </a>
              </div>
            </div>
          </div>
        </li>
      @endif
    </ul>
    <!-- END NAVBAR MENU -->
  </div>
</div>
