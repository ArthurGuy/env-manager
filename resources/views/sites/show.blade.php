@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <h1>{{ $site->name }}</h1>

        <div class="col-sm-12">


            <form action="{{ route('sites.update', $site->id) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <fieldset class="form-group">
                    <label for="cron-name">Name</label>
                    <input type="text" name="name" id="site-name" class="form-control" value="{{ old('name', $site->name) }}">
                </fieldset>

                <fieldset class="form-group">
                    <label for="cron-command">.env</label>
                    <textarea name="env" id="site-env" class="form-control" rows="20">{{ old('env', $site->decrypted_env) }}</textarea>
                </fieldset>

                <button type="submit" class="btn btn-primary">Save</button>
            </form>

            <pre>
                curl {{ url('env/' . $site->name) }} -o .env-encrypted
                aws kms decrypt --ciphertext-blob fileb://.env-encrypted --query Plaintext --output text | base64 -D > .env
            </pre>

            <pre>
                curl {{ url('env/' . $site->name) }} -o .env-encrypted
                aws kms decrypt --ciphertext-blob fileb://.env-encrypted --query Plaintext --output text | base64 -d > .env
            </pre>

            <h4>Access History</h4>
            <table class="table">
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>User</th>
                </tr>
            @foreach($accessLog as $log)
                <tr>
                    <td>{{ $log->created_at->format('j/n/y H:i') }}</td>
                    <td>{{ $log->action }}</td>
                    <td>
                        @if($log->user_id)
                            {{ $log->user()->first()->name }}
                        @else
                            {{ $log->ip_address }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </table>

        </div>
    </div>
@endsection
