@extends('layouts.master')
@section('title', 'Alphabits')
@section('content')
    <div class="card m-4">
        <div class="card-header">Alphabits</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Letter</th>
                        <th>Uppercase</th>
                        <th>Lowercase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (range('A', 'Z') as $letter)
                        <tr>
                            <td>{{ $letter }}</td>
                            <td>{{ strtoupper($letter) }}</td>
                            <td>{{ strtolower($letter) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection