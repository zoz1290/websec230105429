@extends('layouts.master')
@section('title', 'Users')
@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Users</h1>
    </div>
</div>

<script>
  function confirmDelete(userName) {
      return confirm('Are you sure you want to delete the user: ' + userName + '?');
  }
</script>
 
@if(session('success'))
<div class="alert alert-success">
  <strong>Success!</strong> {{ session('success') }}
</div>
@endif
 
<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text"  class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <button type="reset" class="btn btn-danger">Reset</button>
        </div> <div class="col col-sm-1">

          @can(abilities: 'add_users')
              <a href="{{ route('users_create') }}" class="btn btn-success form-control">Add</a>
          @endcan

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
          <th scope="col">Roles</th>

          <th scope="col"></th>

        </tr>
      </thead>
      @foreach($users as $user)
      <tr>
        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->credit }}</td>
                        <td>{{ $user->employee->name ?? '' }}</td>
                     

        <td>
          @foreach($user->roles as $role)
            <span class="badge bg-primary">{{$role->name}}</span>
          @endforeach
        </td>
        <td>
          @can('edit_users')
          <a class="btn btn-primary" href='{{route('users_edit', [$user->id])}}'>Edit</a>
          @endcan
          @can('admin_users')
          <a class="btn btn-primary" href='{{route('edit_password', [$user->id])}}'>Change Password</a>
          @endcan
          @can('delete_users')
          <form id="delete-form-{{ $user->id }}" action="{{ route('users_delete', $user->id) }}"
            method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                onclick="return confirmDelete('{{ $user->name }}')">Delete</button>
         </form>
          @endcan
        </td>
      </tr>
      @endforeach
    </table>
  </div>
</div>


@endsection
