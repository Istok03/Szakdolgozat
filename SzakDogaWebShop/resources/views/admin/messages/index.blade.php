@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h1>Beérkezett üzenetek</h1>
    <p>A választ a feladó e-mail-címére küldheted el a levelezőprogramodból.</p>
    @forelse($messages as $message)
        <article class="card mb-4">
            <div class="card-body" style="overflow-wrap: anywhere">
                <h2>{{ $message->name }}</h2>
                <p><a href="mailto:{{ $message->email }}">{{ $message->email }}</a> · {{ $message->created_at->format('Y-m-d H:i') }}</p>
                <p style="white-space: pre-wrap">{{ $message->message }}</p>
            </div>
        </article>
    @empty
        <p>Még nem érkezett üzenet.</p>
    @endforelse
    {{ $messages->links('partials.pagination') }}
</div>
@endsection
