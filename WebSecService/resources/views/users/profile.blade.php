@extends('layouts.master')
@section('title', 'Profile')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Profile') }}</div>

                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>Name</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Credit</th>
                                <td>{{ $user->credit }}</td>
                            </tr>
                        </table>


                        <!-- Display Roles -->
                        <h3>Roles:</h3>
                        <ul>
                            @foreach ($user->roles as $role)
                                <li>{{ $role->name }}</li>
                            @endforeach
                        </ul>

                        <!-- Display Permissions -->
                        <h3>Permissions:</h3>
                        <ul>
                            @foreach ($user->permissions as $permission)
                                <li>{{ $permission->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
