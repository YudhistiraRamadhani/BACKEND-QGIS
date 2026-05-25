@extends('admin.layout')

@section('content')
    <div class="card">
        <h1>Data User</h1>
        <a href="{{ route('admin.users.create') }}" class="btn">Tambah User</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>{{ $user->role ?? '-' }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-secondary">
                                Edit
                            </a>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada data user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection