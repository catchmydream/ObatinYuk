@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;justify-content:center;gap:6px;flex-wrap:wrap;margin-top:28px;">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="padding:7px 14px;border-radius:9px;font-size:13px;font-weight:600;border:1.5px solid oklch(var(--b3,0.9 0 0));color:oklch(var(--bc)/.3);cursor:default;">‹ Prev</span>
    @else
        <button type="button" wire:click="previousPage" wire:loading.attr="disabled"
            style="padding:7px 14px;border-radius:9px;font-size:13px;font-weight:600;border:1.5px solid oklch(var(--b3,0.9 0 0));background:oklch(var(--b1));color:oklch(var(--bc));cursor:pointer;"
            onmouseover="this.style.borderColor='oklch(var(--p))';this.style.color='oklch(var(--p))'"
            onmouseout="this.style.borderColor='oklch(var(--b3,0.9 0 0))';this.style.color='oklch(var(--bc))'">
            ‹ Prev
        </button>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="padding:7px 6px;font-size:13px;color:oklch(var(--bc)/.4);">…</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="padding:7px 13px;border-radius:9px;font-size:13px;font-weight:700;background:oklch(var(--p));color:oklch(var(--pc));border:1.5px solid oklch(var(--p));">{{ $page }}</span>
                @else
                    <button type="button" wire:click="gotoPage({{ $page }})"
                        style="padding:7px 13px;border-radius:9px;font-size:13px;font-weight:600;border:1.5px solid oklch(var(--b3,0.9 0 0));background:oklch(var(--b1));color:oklch(var(--bc));cursor:pointer;"
                        onmouseover="this.style.borderColor='oklch(var(--p))';this.style.color='oklch(var(--p))'"
                        onmouseout="this.style.borderColor='oklch(var(--b3,0.9 0 0))';this.style.color='oklch(var(--bc))'">
                        {{ $page }}
                    </button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <button type="button" wire:click="nextPage" wire:loading.attr="disabled"
            style="padding:7px 14px;border-radius:9px;font-size:13px;font-weight:600;border:1.5px solid oklch(var(--b3,0.9 0 0));background:oklch(var(--b1));color:oklch(var(--bc));cursor:pointer;"
            onmouseover="this.style.borderColor='oklch(var(--p))';this.style.color='oklch(var(--p))'"
            onmouseout="this.style.borderColor='oklch(var(--b3,0.9 0 0))';this.style.color='oklch(var(--bc))'">
            Next ›
        </button>
    @else
        <span style="padding:7px 14px;border-radius:9px;font-size:13px;font-weight:600;border:1.5px solid oklch(var(--b3,0.9 0 0));color:oklch(var(--bc)/.3);cursor:default;">Next ›</span>
    @endif

</nav>
@endif
