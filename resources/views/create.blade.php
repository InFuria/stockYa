@extends('layouts.app')

@section('content')
{!! Form::model($product, ['route' => ['products.update', $product->id], 'method' => 'POST']) !!}
@method('patch')
<div class="form-group">
    {!! Form::label('slug', 'Identificador') !!}
    {!! Form::text('slug', isset($product) ? $product->slug : null, ['class' => 'form-control', 'placeholder' => 'Ingrese el codigo identificador del producto']) !!}
</div>

<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', isset($product) ? $product->name : null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre del producto']) !!}
</div>

<div class="form-group">
    {!! Form::label('description', 'Descripcion') !!}
    {!! Form::text('description', isset($product) ? $product->description : null, ['class' => 'form-control', 'placeholder' => 'Ingrese una descripcion del producto', 'type' => 'text']) !!}
</div>

<div class="form-group">
    {!! Form::label('image', 'Imagen') !!}
    <br>
    {!! Form::text('image', isset($product) ? $product->image : null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('price', 'Precio en Guaraníes') !!}
    {!! Form::number('price', isset($product) ? $product->price : null, ['class' => 'form-control', 'placeholder' => 'Ingrese el precio del producto en guaranies']) !!}
</div>

<div class="form-group">
    {!! Form::label('category_id', 'Categoria') !!}
    {!! Form::number('category_id', isset($product) ? $product->category_id : null, ['class' => 'form-control', 'placeholder' => 'Ingrese su caegoria']) !!}
</div>

<button type="submit" class="btn btn-warning btn-block"><a>Enviar</a></button>

{!! Form::close() !!}
@endsection
