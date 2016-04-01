@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="col-sm-12">

            <h1>Sites</h1>

            <table class="table table-striped">
                <thead>
                    <th>Name</th>
                    <th>Last Viewed</th>
                    <th>Last Edited</th>
                    <th>Created</th>
                </thead>
                <tbody>
                @foreach ($sites as $site)
                    <tr>
                        <td class="table-text">
                            <a href="{{ route('sites.show', $site->id) }}">{{ $site->name }}</a>
                        </td>
                        <td class="table-text">
                            @if ($site->viewed_at)
                                {{ $site->viewed_at->format('j/n/y H:i') }} by {{ $site->viewed_by()->first()->name }}
                            @endif
                        </td>
                        <td class="table-text">
                            @if ($site->edited_at)
                                {{ $site->edited_at->format('j/n/y H:i') }} by {{ $site->edited_by()->first()->name }}
                            @endif
                        </td>
                        <td class="table-text">
                            {{ $site->created_at->format('j/n/y H:i') }} by {{ $site->created_by()->first()->name }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="card">
                <div class="card-header">
                    <h4>Add a new record</h4>
                </div>
                <div class="card-block">

                    <div class="card-text">

                        <form action="{{ route('sites.store') }}" method="POST">
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <label for="cron-name" class="col-sm-3 form-control-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="name" id="site-name" class="form-control" value="{{ old('name') }}">
                                </div>
                            </div>

                            <fieldset class="form-group">
                                <div class="col-sm-offset-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
