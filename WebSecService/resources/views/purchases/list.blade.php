@extends('layouts.master')
@section('title', 'Purchased Products')
@section('content')


    <div class="row mt-2">
        <div class="col col-10">
            <h1>Purchased Products</h1>
        </div>

    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price per Item</th>
                <th>Total Price</th>
                <th>Purchase Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $purchase->product->name }}</td>
                    <td>{{ $purchase->quantity }}</td>
                    <td>${{ number_format($purchase->price, 2) }}</td>
                    <td>${{ number_format($purchase->price * $purchase->quantity, 2) }}</td>
                    <td>{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>



@endsection
