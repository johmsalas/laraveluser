@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Editing {{ $user->name }}</div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                        {{ method_field('PUT') }}{{csrf_field()}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input name="name" value="{{ old('name', $user->name) }}" type="text" class="form-control" id="name" placeholder="User's name">
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input name="email" value="{{ $user->email }}" type="email" class="form-control" id="email" placeholder="Email address">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ old('email', $errors->first('email')) }}</strong>
                                </span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-default">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
