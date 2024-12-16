
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>

<header class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg bg-body-tertiary mx-5">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Work Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Панель администратора</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    @guest
                        <a href="/login" class="btn btn-outline-primary me-2">Login</a>
                        <a href="/register" class="btn btn-outline-success">Register</a>
                    @endguest

                    @auth
                        <h4 class="me-3">{{auth()->user()->name}}</h4>
                        <a href="/profile" class="btn btn-primary me-2">Profile</a>
                        <form action="{{ route('logout') }}" id="logout-form" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>


<main class="container">
    @yield('content')
</main>




</body>
</html>
