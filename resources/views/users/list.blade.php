@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @include('partials.alerts')
            <div class="panel panel-default">
                <div class="panel-heading">
                    <ol class="breadcrumb">
                        <li class="active">Users</li>
                    </ol>
                </div> 
                <div class="panel-heading">
                    <div class="btn-group" role="group" aria-label="...">
                        <label class="btn btn-default btn-file">
                            <form class="" action="{{ route('users-import') }}" method="post" enctype="multipart/form-data">
                                {{csrf_field()}}
                                Import <input name="imported" class="submit-on-change" type="file" style="display: none;">
                            </form>
                        </label>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Export
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('users-export', ['xls']) }}">XLS</a></li>
                                <li><a href="{{ route('users-export', ['xlsx']) }}">XLSX</a></li>
                                <li><a href="{{ route('users-export', ['tsv']) }}">TSV</a></li>
                                <li><a href="{{ route('users-export', ['csv']) }}">CSV</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td><a href="{{ route('users.show', $user->id) }}">{{ title_case($user->name) }}</a></td>
                                    <td>{{ strtolower($user->email) }}</td>
                                    <td>
                                        @can ('edit users')
                                            <a href="{{ route('users.edit', $user->id) }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                        @endcan
                                    </td>
                                    <td>
                                        @can ('delete users')
                                            <a href="{{ route('users.delete', $user->id) }}">
                                                <i class="glyphicon glyphicon-remove-circle"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
