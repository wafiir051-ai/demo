@if ($paginator->hasPages())
<nav class="pager" role="navigation" aria-label="Paginasi">
    @if ($paginator->onFirstPage())<span class="off">&laquo; Sebelumnya</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>@endif
    <span class="cur">Hal. {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
    @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>@else<span class="off">Berikutnya &raquo;</span>@endif
</nav>
@endif
