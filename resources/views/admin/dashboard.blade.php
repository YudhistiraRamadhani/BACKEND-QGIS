@extends('admin.layout')

@section('content')
    <h1>Dashboard Admin</h1>

    <div class="grid">
        <div class="card">
            <p>Total Bengkel</p>
            <div class="stat">{{ $totalWorkshops }}</div>
        </div>

        <div class="card">
            <p>Total Mekanik</p>
            <div class="stat">{{ $totalMechanics }}</div>
        </div>

        <div class="card">
            <p>Total User</p>
            <div class="stat">{{ $totalUsers }}</div>
        </div>

        <div class="card">
            <p>Total Order</p>
            <div class="stat">{{ $totalOrders }}</div>
        </div>
    </div>

    <div class="card">
        <h2>Backend Area</h2>
        <p>Halaman ini digunakan untuk mengelola data bengkel, mekanik, user, dan order.</p>
    </div>
@endsection