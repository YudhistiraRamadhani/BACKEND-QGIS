@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Data Bengkel</h1>
        <a href="{{ route('admin.workshops.create') }}" class="btn">Tambah Bengkel</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workshops as $workshop)
                <tr>
                    <td>{{ $workshop->name }}</td>
                    <td>{{ $workshop->address }}</td>
                    <td>{{ $workshop->rating }}</td>
                    <td>{{ $workshop->is_open ? 'Buka' : 'Tutup' }}</td>
                    <td>{{ $workshop->latitude }}</td>
                    <td>{{ $workshop->longitude }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.workshops.edit', $workshop->id) }}" class="btn btn-secondary">Edit</a>

                            <form action="{{ route('admin.workshops.destroy', $workshop->id) }}" method="POST" onsubmit="return confirm('Hapus bengkel ini?')">
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