@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Data Order</h1>
        <p>Data order ini dipakai untuk tracking lokasi user, mekanik, dan bengkel.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Order</th>
                <th>Customer</th>
                <th>Bengkel</th>
                <th>Mekanik</th>
                <th>Status</th>
                <th>Problem</th>
                <th>Total</th>
                <th>ETA</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->workshop_name ?? '-' }}</td>
                    <td>{{ $order->mechanic_name ?? '-' }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->problem ?? '-' }}</td>
                    <td>Rp {{ number_format($order->total_cost, 0, ',', '.') }}</td>
                    <td>{{ $order->eta ? $order->eta . ' menit' : '-' }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
                            Tracking
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Belum ada order.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
