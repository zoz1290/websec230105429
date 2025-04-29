@extends('layouts.master')
@section('title', 'Prime Numbers')
@section('content')

<form action="{{route('products_save', $product->id)}}" method="post">
    {{ csrf_field() }}
    @foreach($errors->all() as $error)
    <div class="alert alert-danger">
    <strong>Error!</strong> {{$error}}
    </div>
    @endforeach
    <div class="row mb-2">
        <div class="col-6">
            <label for="code" class="form-label">Code:</label>
            <input type="text" class="form-control" placeholder="Code" id="code" name="code" required value="{{$product->code}}">
        </div>
        <div class="col-6">
            <label for="model" class="form-label">Model:</label>
            <input type="text" class="form-control" placeholder="Model" id="model" name="model" required value="{{$product->model}}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="name" class="form-label">Name:</label>
            <input type="text" class="form-control" placeholder="Name" id="name" name="name" required value="{{$product->name}}">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-6">
            <label for="price" class="form-label">Price:</label>
            <input type="numeric" class="form-control" placeholder="Price" id="price" name="price" required value="{{$product->price}}">
        </div>
        <div class="col-6">
            <label for="model" class="form-label">Photo:</label>
            <input type="text" class="form-control" placeholder="Photo" id="photo" name="photo" required value="{{$product->photo}}">

            <input type="file" class="form-control" id="photo" name="photo_file" accept="image/*">
        </div>
    </div>
    <div class="row mb-2">
        <div class="col">
            <label for="description" class="form-label">Description:</label>
            <textarea type="text" class="form-control" placeholder="Description" id="description" name="description" required>{{$product->description}}</textarea>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-3">
            <label for="stock" class="form-label">Stock:</label>
            <input type="numeric" class="form-control" placeholder="Stock" id="stock" name="stock" required value="{{$product->stock}}">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection
