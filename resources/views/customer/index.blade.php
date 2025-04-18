@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h4 class="mt-4">Customer Section</h4>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                <tr>
                    <th>Customer Name</th>
                    <th>Order#</th>
                    <th>Connection Status</th>
                    <th>Active</th>
                </tr>
                </thead>
                <tbody>
                @foreach($slaves as $slave)
                    <tr>
                        <td>{{ $slave->name }}</td>
                        <td>{{ $slave->order_id }}</td>
                        <td>{{ $slave->connection_status ? 'Connected' : 'Disconnected' }}</td>
                        <td>

                            <input
                                type="checkbox"
                                class="form-check-input toggle-status"
                                data-id="{{ $slave->id }}"
                                data-url="{{ route('slave.toggle', $slave->id) }}"
                                {{ $slave->status ? 'checked' : '' }}>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.toggle-status');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const url = this.getAttribute('data-url');

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({}),
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Status updated:', data.status);
                        })
                        .catch(error => {
                            alert('Error updating status.');
                            this.checked = !this.checked; // rollback toggle
                        });
                });
            });
        });
    </script>
@endpush
