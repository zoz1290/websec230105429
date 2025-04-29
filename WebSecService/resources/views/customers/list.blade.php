@extends('layouts.master')
@section('title', 'Customers')
@section('content')

    <div class="row mt-2">
        <div class="col col-10">
            <h1>Customers</h1>
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
    <form>
        <div class="row">
            <div class="col col-sm-2">
                <input name="keywords" type="text" class="form-control" placeholder="Search Keywords"
                    value="{{ request()->keywords }}" />
            </div>
            <div class="col col-sm-1">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <div class="col col-sm-1">
                <button type="reset" class="btn btn-danger">Reset</button>
            </div>

        </div>
    </form>

    <div class="card mt-2">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Credit</th>
                        <th scope="col">Employee</th>
                    </tr>
                </thead>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->credit }}</td>
                        <td>{{ $customer->employee ? $customer->employee->name : 'N/A' }}</td>
                        <td>
                            @can('edit_user_credit')
                                <a class="btn btn-primary" href='{{ route('customers_edit_credit', [$customer->id]) }}'>Edit</a>
                            @endcan
                           
                            



                        </td>
                        <td>
                            @can('reset_credit')
                                <a class="btn btn-primary" href='{{ route('reset_credit', [$customer->id]) }}'>Reset Credit</a>
                            @endcan
                           
                            



                        </td>
                        
                    </tr>
                @endforeach
            </table>
        </div>
    </div>


@endsection
