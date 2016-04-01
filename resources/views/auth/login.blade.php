@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-block">
                    @if (in_array(env('ACCESS_TYPE', 'all'), ['all', 'email']))
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                        {!! csrf_field() !!}

                        <div class="form-group row{{ $errors->has('email') ? ' has-danger' : '' }}">
                            <label class="col-md-4 form-control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="text-help">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row{{ $errors->has('password') ? ' has-danger' : '' }}">
                            <label class="col-md-4 form-control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="text-help">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">Login</button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                            </div>
                        </div>
                        <input type="hidden" name="remember" value="1">
                    </form>
                    @endif
                    @if (in_array(env('ACCESS_TYPE', 'all'), ['all', 'github']))
                        <a class="btn btn-link" href="{{ url('/auth/github') }}">Github Login</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
