<!doctype html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Sign in - {{ config('app.name') }}</title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="{{ asset('css/tabler.min.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <link href="{{ asset('css/tabler-icons.min.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <link href="{{ asset('css/inter.css') }}?{{ config('app.version') }}" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </head>

  <body>
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <!-- BEGIN NAVBAR LOGO -->
          <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
            <img src="{{ asset('img/logo-tabler.png') }}" height="36" alt="">
          </a><!-- END NAVBAR LOGO -->
        </div>
        <div class="card card-md">
          <div class="card-body">
            <h2 class="h2 text-center mb-4">Login in Start Klinik</h2>
            <form action="{{ route('login') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="username" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Your username" />
                @error('username')
                  <div class="invalid-feedback">
                    {{ $message }}
                  </div>
                @enderror
              </div>
              <div class="mb-2">
                <label class="form-label">
                  Password
                </label>
                <div class="input-group input-group-flat" x-data="{ showPassword: false }">
                  <input :type="showPassword ? 'text' : 'password'" class="form-control" name="password" placeholder="Your password" />
                  <span class="input-group-text">
                    <a href="#" @click.prevent="showPassword = !showPassword" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                      <i class="ti ti-eye" x-show="!showPassword"></i>
                      <i class="ti ti-eye-off" x-show="showPassword"></i>
                    </a>
                  </span>
                </div>
              </div>
              <div class="mb-2">
                <label class="form-check">
                  <input type="checkbox" class="form-check-input" />
                  <span class="form-check-label">Remember me on this device</span>
                </label>
              </div>
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Sign in</button>
              </div>
            </form>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col">
                <img src="{{ asset('img/logo-pse-small.png') }}" alt="" style="height: 55%">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('js/tabler.min.js') }}?{{ config('app.version') }}" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
  </body>

</html>
