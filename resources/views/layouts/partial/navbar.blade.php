<header>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('index') }}">
                Hímző
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link @if($router->current()->getName() === 'index') {{ 'active' }} @endif" href="{{ route('index') }}">
                            Főoldal
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav d-flex">
                    @isset($user)
                        <li class="nav-item">
                            <a class="nav-link @if($router->current()->getName() === 'profile') {{ 'active' }} @endif" href="{{ route('profile') }}">
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('auth.logout') }}">
                                Log out
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('auth.redirect') }}">
                                Log in
                            </a>
                         </li>
                    @endisset
                </ul>
            </div>
        </div>
    </nav>

</header>
