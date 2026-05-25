<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bengkel Track Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #111827;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 24px 16px;
            box-sizing: border-box;
        }

        .sidebar h2 {
            margin-top: 0;
            color: #ff6b35;
        }

        .sidebar a {
            display: block;
            color: #d1d5db;
            text-decoration: none;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .sidebar a:hover {
            background: #1f2937;
            color: white;
        }

        .content {
            margin-left: 240px;
            padding: 28px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
            margin-bottom: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .stat {
            font-size: 32px;
            font-weight: bold;
            color: #ff6b35;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 16px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f9fafb;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            margin-bottom: 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
        }

        label {
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 10px;
            border: none;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn-danger {
            background: #dc2626;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Bengkel Track</h2>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.workshops.index') }}">Data Bengkel</a>
        <a href="{{ route('admin.mechanics.index') }}">Data Mekanik</a>
        <a href="{{ route('admin.users.index') }}">Data User</a>
        <a href="{{ route('admin.orders.index') }}">Data Order</a>
        <a href="/api/workshops" target="_blank">Cek API</a>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </div>
</body>
</html>