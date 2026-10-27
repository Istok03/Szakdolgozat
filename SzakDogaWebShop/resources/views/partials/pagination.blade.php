@if($paginator->hasPages())
    <nav class="shop-pagination" aria-label="Lapozás">
        @if($paginator->onFirstPage())
            <span aria-disabled="true">Előző</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">Előző</a>
        @endif
        <span>{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}. oldal</span>
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Következő</a>
        @else
            <span aria-disabled="true">Következő</span>
        @endif
    </nav>
@endif
