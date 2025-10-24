<a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
  <span class="avatar avatar-sm" style="background-image: url({{ asset('storage/avatars/' . Auth::user()->avatar) }})"> </span>
  <div class="d-none d-xl-block ps-2">
    <div>{{ Auth::user()->name }}</div>
    <div class="mt-1 small text-secondary">{{ Auth::user()->role }}</div>
  </div>
</a>
<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" data-bs-theme="light">
  <a href="#" class="dropdown-item">Status</a>
  <a href="./profile.html" class="dropdown-item">Profile</a>
  <a href="#" class="dropdown-item">Feedback</a>
  <div class="dropdown-divider"></div>
  <a href="./settings.html" class="dropdown-item">Settings</a>
  <form action="{{ route('logout') }}" id="form-logout" method="POST" class="inline">
    @csrf
    <button type="submit" class="dropdown-item">Logout</button>
  </form>
</div>
