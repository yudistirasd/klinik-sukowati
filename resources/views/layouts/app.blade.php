<!doctype html>
<html lang="en" data-bs-theme="light">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/css/tempus-dominus.min.css" crossorigin="anonymous">
    <link href="{{ asset('css/tabler.min.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <link href="{{ asset('css/tabler-icons.min.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <link href="{{ asset('css/inter.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- END GLOBAL MANDATORY STYLES -->
    @stack('css')
  </head>

  <body class="layout-fluid">
    <div class="page">
      <!-- BEGIN NAVBAR  -->
      <div class="sticky-top">
        <header class="navbar navbar-expand-md d-print-none" data-bs-theme="dark">
          @include('layouts._navbar')
        </header>
      </div>
      <!-- END NAVBAR  -->
      <div class="page-wrapper">
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
          <div class="container-xl">
            <div class="row g-2 align-items-center">
              <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">@yield('subtitle')</div>
                <h2 class="page-title">@yield('title')</h2>
              </div>
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  @yield('action-page')
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- END PAGE HEADER -->
        <!-- BEGIN PAGE BODY -->
        <div class="page-body">
          <div class="container-xl">
            @yield('content')
          </div>
        </div>
        <!-- END PAGE BODY -->
        <!--  BEGIN FOOTER  -->
        <footer class="footer footer-transparent d-print-none">
          <div class="container-xl">
            <div class="row text-center align-items-center flex-row-reverse">
              <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                  <li class="list-inline-item">Start Klinik v{{ config('app.version') }} by <a href="https://hardisoftware.com" target="_blank" class="link-secondary" rel="noopener">Hardi Software</a></li>
                </ul>
              </div>
              <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                  <li class="list-inline-item">
                    Copyright &copy; 2025 All rights reserved.
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </footer>
        <!--  END FOOTER  -->
      </div>
    </div>
    @routes()
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <!-- Popperjs -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <!-- Tempus Dominus JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/tabler-theme.min.js') }}?{{ config('app.version') }}" defer></script>
    <script src="{{ asset('js/tabler.min.js') }}?{{ config('app.version') }}" defer></script>
    <script src="{{ asset('libs/jquery/jquery-3.7.0.min.js') }}?{{ config('app.version') }}"></script>
    <script src="{{ asset('libs/sweetalert2/sweetalert2.all.min.js') }}?{{ config('app.version') }}"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });

      const confirmDelete = (endpoint, callback) => {
        Swal.fire({
          title: "Apakah anda yakin akan menghapus data ini?",
          html: "anda tidak dapat mengembalikan data yang sudah dihapus!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Ya, hapus!",
          cancelButtonText: "Tidak, batalkan",
          showLoaderOnConfirm: true,
          preConfirm: async (login) => {
            return $.ajax({
              url: endpoint,
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            }).done((response) => {
              Toast.fire({
                icon: 'success',
                title: response.message
              });

              if (typeof callback == 'function') {
                callback()
              } else {
                console.error(`Ajax ${endpoint}, callback is not a function !`);
              }
            }).fail((error) => {
              let response = error.responseJSON;

              Swal.fire({
                icon: 'error',
                title: 'Terjadi kesalahan !',
                message: response.message
              })
            })
          },
          allowOutsideClick: () => !Swal.isLoading()
        }).then(async (result) => {
          if (!result.value) {
            Swal.fire({
              icon: 'info',
              title: 'Aksi dibatalkan !',
            })
          }
        });
      }
    </script>
    @stack('js')
  </body>

</html>
