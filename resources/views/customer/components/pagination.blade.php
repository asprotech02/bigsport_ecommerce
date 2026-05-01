@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-5 pt-3 w-100">
        
        @if ($paginator->onFirstPage())
            <button class="btn btn-black rounded-0 px-4 py-2 fw-bold text-capitalize disabled" style="letter-spacing: 0.5px; font-size: 14px; opacity: 0.5; cursor: not-allowed;">
                Sebelumnya
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-black rounded-0 px-4 py-2 fw-bold text-capitalize" style="letter-spacing: 0.5px; font-size: 14px;">
                Sebelumnya
            </a>
        @endif

        <span class="fw-bold text-dark" style="font-size: 15px;">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-black rounded-0 px-4 py-2 fw-bold text-capitalize" style="letter-spacing: 0.5px; font-size: 14px;">
                Berikutnya
            </a>
        @else
            <button class="btn btn-black rounded-0 px-4 py-2 fw-bold text-capitalize disabled" style="letter-spacing: 0.5px; font-size: 14px; opacity: 0.5; cursor: not-allowed;">
                Berikutnya
            </button>
        @endif
        
    </div>
@endif