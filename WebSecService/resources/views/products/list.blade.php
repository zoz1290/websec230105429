@extends('layouts.master')
@section('title', 'Test Page')
@section('content')
@if(session('success'))
<div class="alert alert-success">
  <strong>Success!</strong> {{ session('success') }}
</div>
@endif
<script>
    function confirmDelete(productName) {
        return confirm('Are you sure you want to delete the product: ' + productName + '?');
    }
  </script>
<div class="row mt-2">
    <div class="col col-10">
        <h1>Products</h1>
    </div>
    <div class="col col-2">
        @can('add_products')
        <a href="{{route('products_edit')}}" class="btn btn-success form-control">Add Product</a>
        @endcan
    </div>
</div>
<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text"  class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="numeric"  class="form-control" placeholder="Min Price" value="{{ request()->min_price }}"/>
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="numeric"  class="form-control" placeholder="Max Price" value="{{ request()->max_price }}"/>
        </div>
        <div class="col col-sm-2">
            <select name="order_by" class="form-select">
                <option value="" {{ request()->order_by==""?"selected":"" }} disabled>Order By</option>
                <option value="name" {{ request()->order_by=="name"?"selected":"" }}>Name</option>
                <option value="price" {{ request()->order_by=="price"?"selected":"" }}>Price</option>
            </select>
        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" {{ request()->order_direction==""?"selected":"" }} disabled>Order Direction</option>
                <option value="ASC" {{ request()->order_direction=="ASC"?"selected":"" }}>ASC</option>
                <option value="DESC" {{ request()->order_direction=="DESC"?"selected":"" }}>DESC</option>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>


@foreach($products as $product)
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col col-sm-12 col-lg-4">
                    <img src="{{asset("images/$product->photo")}}" class="img-thumbnail" alt="{{$product->name}}" width="100%">
                </div>
                <div class="col col-sm-12 col-lg-8 mt-3">
                    <div class="row mb-2">
					    <div class="col-8">
					        <h3>{{$product->name}}</h3>
                        {{-- <div class="col col-2">
                                @can('manage_discounts')
                                <a href="{{route('manage_discounts', $product->id)}}" class="btn btn-success form-control">add discount</a>
                                @endcan
                                </div> --}}
					    </div>  
					    <div class="col col-2">
                            @can('edit_products')
					        <a href="{{route('products_edit', $product->id)}}" class="btn btn-success form-control">Edit</a>
                            @endcan
					    </div>
					    <div class="col col-2">
                            @can('delete_products')
                            <form id="delete-form-{{ $product->id }}" action="{{ route('products_delete', $product->id) }}"
                                method="POST" style="display: inline;">
                        
                             @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger form-control"
                                    onclick="return confirmDelete('{{ $product->name }}')">Delete</button>
                        
                             </form>
                            @endcan
					    </div>
					</div>

                    <table class="table table-striped">
                        <tr><th width="20%">Name</th><td>{{$product->name}}</td></tr>
                        <tr><th>Model</th><td>{{$product->model}}</td></tr>
                        <tr><th>Code</th><td>{{$product->code}}</td></tr>
                        <tr><th>Price</th><td>{{$product->price}}</td>
                        <tr><th>Discount</th><td>{{$product->discount_percentage}}</td></tr>
                        <tr><th>product count</th><td>{{$product->discount_max_products}}</td></tr>
                        <tr><th>Stock</th><td>{{$product->stock}}</td></tr>
                        <tr><th>Description</th><td>{{$product->description}}</td></tr>
                        @can('purchase')
                        <tr><th>Buy</th><td>
                           
                            <form id="purchase-form-{{ $product->id }}" action="{{ route('purchase') }}"
                                method="POST" style="display: inline;">
                                @csrf

                                @foreach($errors->all() as $error)
                                <div class="alert alert-danger">
                                <strong>Error!</strong> {{$error}}
                                </div>
                                @endforeach

                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="number" name="quantity" min="1" max="{{ $product->stock }}"
                                    required>
                                <button type="submit" class="btn btn-success">Purchase</button>
                             </form>

                          </td></tr> 
                           @endcan
                           
                        @can('manage_discounts')
                           <tr><th  colspan="6"> 
                           
                            <form id="discount-form-{{ $product->id }}" action="{{ route('products_discount', $product->id) }}"
                                method="POST" style="display: inline;">
                                @csrf

                                @foreach($errors->all() as $error)
                                <div class="alert alert-danger">
                                <strong>Error!</strong> {{$error}}
                                </div>
                                @endforeach

                              
                                Count <input type="number" name="max_count" min="1" max="100"
                                    required>
                                    <!--add discount percentage-->
                                 percentage<input type="number" name="discount" min="1" max="100" required > 

                                <button type="submit" class="btn btn-success">add discount</button>
                              
                             </form>
                            </th> </tr>
                           @endcan

                            

                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection