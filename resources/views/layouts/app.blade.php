<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>.envManager</title>

    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

</head>
<body>
    <div>
        <nav class="navbar navbar-full navbar-dark bg-primary m-b-1">

            <button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#navbar-header" aria-controls="navbar-header">
                &#9776;
            </button>
            <div class="collapse navbar-toggleable-xs" id="navbar-header">
                <span class="navbar-brand">.envManager</span>

                <ul class="nav navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('sites') }}">Sites</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav pull-xs-right">
                    @if (Auth::guest())
                        <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>
                        @if (in_array(env('ACCESS_TYPE', 'all'), ['all', 'email']))
                            <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
                        @endif
                    @else
                        <li class="nav-item">
                            <span class="nav-link">{{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a>
                        </li>
                        <li class="nav-item">
                            <img src="{{ \Gravatar::get(Auth::user()->email) }}" width="40" height="40">
                        </li>
                    @endif
                </ul>
            </div>
        </nav>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session()->pull('status') }}
            </div>
        @endif

        @include('common.errors')


        @yield('content')
    </div>

</body>
</html>
