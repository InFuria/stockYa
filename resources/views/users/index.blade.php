@include('layouts.app')

@section('content')

@endsection
    {!! Form::open(['route' => 'users.index', 'method' => 'GET']) !!}
        <input type="text" id="name" name="name"/>
        <button type="submit">Enviar</button>
    {!! Form::close() !!}
@section('js')

@append
