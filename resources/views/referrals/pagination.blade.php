@if($paginator->hasPages())
<nav class="ref-inline" aria-label="Halaman referral">
    @if($paginator->previousPageUrl())<a class="rd-button secondary" href="{{ $paginator->previousPageUrl() }}">← Sebelumnya</a>@endif
    <span class="rd-muted rd-small">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
    @if($paginator->nextPageUrl())<a class="rd-button secondary" href="{{ $paginator->nextPageUrl() }}">Berikutnya →</a>@endif
</nav>
@endif
