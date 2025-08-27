@if (session('ok') || $errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-3">
        @if (session('ok'))
            <div class="rounded border border-green-200 bg-green-50 text-green-800 px-4 py-2">
                {{ session('ok') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded border border-red-200 bg-red-50 text-red-800 px-4 py-2 mt-2">
                <ul class="list-disc ml-4">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
