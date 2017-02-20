@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <ol class="breadcrumb">
                        @can ('see users')
                            <li><a href="{{ route('users.index') }}">Users</a></li>
                        @elsecan
                            <li>Users</li>
                        @endcan
                        <li class="active">{{ title_case($user->name) }}</li>
                    </ol>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input name="name" value="{{ $user->name }}" type="text" class="form-control" id="name" placeholder="User's name" readonly="readonly">
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input name="email" value="{{ $user->email }}" type="email" class="form-control" id="email" placeholder="Email address" readonly="readonly">
                        </div>
                        <div class="form-group">
                            <label for="email">Roles</label>
                            <p>{{ $roles }}</p>
                        </div>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-default">Edit</a>
                        <a href="#" class="btn btn-default">Remove</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
