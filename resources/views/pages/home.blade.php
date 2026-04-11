@extends('layouts.app')

@section('main')
    @if (session('status'))
        <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif
@endsection
