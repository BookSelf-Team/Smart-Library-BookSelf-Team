@props([
    'items' => [],
])

@if (!empty($items))
    <nav aria-label="Breadcrumb" class="flex items-center text-xs sm:text-sm text-on-surface-variant mb-3">
        <ol class="inline-flex items-center flex-wrap gap-1 sm:gap-1.5">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[16px] sm:text-[18px]">home</span>
                    <span class="hidden sm:inline">Beranda</span>
                </a>
            </li>

            @foreach ($items as $item)
                <li class="inline-flex items-center gap-1 sm:gap-1.5">
                    <span class="material-symbols-outlined text-[14px] sm:text-[16px] text-outline/60">chevron_right</span>
                    @if (!empty($item['url']) && !$loop->last)
                        <a href="{{ $item['url'] }}" class="hover:text-primary transition-colors">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-medium text-on-surface" aria-current="page">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
