@extends('layouts.app')

@section('content')

<div class="p-3">

    <!-- USER INFO -->
    <div class="text-center mb-3">
        <div style="font-size:40px;">👤</div>
        <strong>{{ $user->name }}</strong><br>
        <small class="text-muted">{{ $user->username }}</small>
    </div>

    <!-- STATS -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body text-center">
            <small class="text-muted">Jumlah Prospek</small>
            <h4>{{ $totalProspect }}</h4>
        </div>
    </div>

    <!-- ACTION -->
    <form method="POST" action="/logout">
        @csrf
        <button class="btn btn-danger w-100">
            Logout
        </button>
    </form>

</div>

@endsection