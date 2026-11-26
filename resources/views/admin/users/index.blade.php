@extends('layouts.admin')

@section('title', 'Readers')

@section('content')
    <header class="page-head">
        <p class="kicker">Floor</p>
        <h1>Readers</h1>
        <p class="fine">Roles and account status for people who buy after dark.</p>
    </header>
    <form class="filters" method="GET">
        <label>Search
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Name or email">
        </label>
        <button class="btn btn-ghost" type="submit">Search</button>
    </form>
    <div class="stack">
        @foreach($users as $user)
            <form class="panel reader-row" method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div>
                    <strong>{{ $user->name }}</strong>
                    <p class="fine">{{ $user->email }} · {{ $user->provider }}</p>
                </div>
                <label>Role
                    <select name="role">
                        <option value="customer" @selected($user->role === 'customer')>Reader</option>
                        <option value="admin" @selected($user->role === 'admin')>Editor</option>
                    </select>
                </label>
                <label class="check">
                    <input type="checkbox" name="is_active" value="1" @checked($user->is_active)>
                    Active
                </label>
                <button class="btn btn-ghost" type="submit">Save</button>
            </form>
        @endforeach
    </div>
    {{ $users->links() }}
@endsection
