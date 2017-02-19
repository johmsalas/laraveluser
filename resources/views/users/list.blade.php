@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Users</div>
                <div class="panel-body">
                    <table class="table">
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ title_case($user->name) }}</td>
                                    <td>{{ strtolower($user->email) }}</td>
                                    @if ($user->id > 1)
                                        <td><a href="{{ route('users.edit', $user->id) }}"><i class="glyphicon glyphicon-edit"></i></a></td>
                                        <td><a href="{{ route('users.delete', $user->id) }}"><i class="glyphicon glyphicon-remove-circle"></i></a></td>
                                    @else
                                        <td colspan="2"></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
