@if ($paginator->hasPages())
    <div class="join">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button class="join-item btn btn-disabled">«</button>
        @else
            <a class="join-item btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">«</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <button class="join-item btn btn-disabled">{{ $element }}</button>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="join-item btn btn-primary">{{ $page }}</button>
                    @else
                        <a class="join-item btn" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="join-item btn" href="{{ $paginator->nextPageUrl() }}" rel="next">»</a>
        @else
            <button class="join-item btn btn-disabled">»</button>
        @endif
    </div>
@endif
