@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination">
    <ul class="pagination">

        {{-- Previous --}}
        <li>
            @if ($paginator->onFirstPage())
            <span style="opacity:.4;cursor:not-allowed">« Prev</span>
            @else
            <a href="{{ $paginator->previousPageUrl() }}">« Prev</a>
            @endif
        </li>

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
        @if (is_string($element))
        <li><span>{{ $element }}</span></li>
        @endif

        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <li class="active"><span>{{ $page }}</span></li>
        @else
        <li><a href="{{ $url }}">{{ $page }}</a></li>
        @endif
        @endforeach
        @endif
        @endforeach

        {{-- Next --}}
        <li>
            @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Next »</a>
            @else
            <span style="opacity:.4;cursor:not-allowed">Next »</span>
            @endif
        </li>

    </ul>
</nav>
@endif