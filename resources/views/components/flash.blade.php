@props(['type' => session('status') ? 'success' : (session('error') ? 'error' : null),
        'message' => session('status') ?? session('error')])

@if ($type && $message)
    <div class="alert alert-{{ $type }} mb-4">
        <span>{{ $message }}</span>
    </div>
@endif
