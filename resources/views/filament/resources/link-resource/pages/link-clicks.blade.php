<x-filament-panels::page>
    <div class="mb-4 rounded-xl bg-white p-4 shadow-sm dark:bg-gray-900">
        <div class="text-sm text-gray-500 dark:text-gray-400">Оригинальный URL</div>
        <a href="{{ $link->original_url }}" target="_blank"
           class="text-primary-600 hover:underline break-all">{{ $link->original_url }}</a>

        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Короткая ссылка</div>
        <div class="font-mono">{{ $link->short_url }}</div>

        <div class="mt-3 inline-flex items-center gap-2 rounded-lg bg-primary-50 px-3 py-1 text-primary-700 dark:bg-primary-950 dark:text-primary-300">
            Всего переходов: <span class="font-semibold">{{ $link->clicks_count }}</span>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
