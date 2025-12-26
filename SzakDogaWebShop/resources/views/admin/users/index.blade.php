@extends('admin.layout')

@section('content')
<div class="admin-container">
    <h1 class="admin-title">Felhasználók</h1>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Név</th>
                <th>Email</th>
                <th>Szerepkör</th>
                <th>Regisztrált</th>
                <th>Műveletek</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->is_admin ? 'Admin' : 'Felhasználó' }}</td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit">Szerkesztés</a>

                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-form">
                            @csrf
                            @method('DELETE')
                            <button class="btn-delete" onclick="return confirm('Biztos törlöd?')">Törlés</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
</div>
@endsection
