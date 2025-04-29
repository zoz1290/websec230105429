@extends('layouts.master')
@section('title', 'Edit Customer Credit')
@section('content')
 
 
 <div class="d-flex justify-content-center">
    <div class="row m-4 col-sm-8">
        <form action="{{route('customers_update_credit', $user->id)}}" method="POST">
            {{ csrf_field() }}
            @method('PUT') <!-- This tells Laravel to treat it as an update request -->
            @foreach($errors->all() as $error)
            <div class="alert alert-danger">
            <strong>Error!</strong> {{$error}}
            </div>
            @endforeach
            <div class="row mb-2">
                <div class="col-12">
                    <label for="code" class="form-label">Name:</label>
                    {{$user->name}} 
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <label for="code" class="form-label">Email:</label>
                  {{$user->email}} 
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <label for="code" class="form-label">Credit:</label>
                  {{$user->credit}} 
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <label for="code" class="form-label">Add Credit:</label>
                    <input type="text" class="form-control" placeholder="Credit" name="credit" required value="0">
                </div>
            </div>
            
            
            
            @can('edit_user_credit')
            <button type="submit" class="btn btn-primary">Add Credit</button>
            @endcan
            @can('reset_credit')
            <button type="submit" class="btn btn-primary">reset Credit</button>
            @endcan
        </form>
    </div>
</div>
@endsection
