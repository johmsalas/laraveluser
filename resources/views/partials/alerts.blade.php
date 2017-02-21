@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success') && count(session('success')) > 0)
    <div class="alert alert-info">
        <ul>
            @foreach (session('success') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
