@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Data Mekanik</h1>
        <a href="{{ route('admin.mechanics.create') }}" class="btn">Tambah Mekanik</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Bengkel</th>
                <th>Status</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mechanics as $mechanic)
                <tr>
                    <td>{{ $mechanic->name }}</td>
                    <td>{{ $mechanic->email }}</td>
                    <td>{{ $mechanic->phone ?? '-' }}</td>
                    <td>{{ $mechanic->workshop_name ?? '-' }}</td>
                    <td>{{ ucfirst($mechanic->status) }}</td>
                    <td>{{ $mechanic->latitude }}</td>
                    <td>{{ $mechanic->longitude }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.mechanics.edit', $mechanic->id) }}" class="btn btn-secondary">Edit</a>

                            <form action="{{ route('admin.mechanics.destroy', $mechanic->id) }}" method="POST" onsubmit="return confirm('Hapus mekanik ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection