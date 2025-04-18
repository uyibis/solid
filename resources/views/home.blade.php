@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <!-- Master Section -->
        <h4 class="mt-4">Master Section</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>No Of Master ID's</th>
                    <th>No Of Slaves</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($userTraders as $userTrader)
                    <tr>
                        <td>{{ $userTrader->name }}</td>
                        <td>{{ $userTrader->trader_count }}</td> <!-- Displaying the count of traders (Master IDs) -->
                        <td>{{ $userTrader->slaves_count ?? 0 }}</td> <!-- Adjust field names for slave count -->
                        <td>
                            <form action="{{ route('home.toggleStatus', $userTrader->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="checkbox" class="form-check-input"
                                       {{ $userTrader->status ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
