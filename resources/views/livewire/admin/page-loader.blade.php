<div>
    @switch($page)
        @case('dashboard')
            @livewire('admin.dashboard')
            @break

        @case('news')
            @livewire('admin.news.index')
            @break

        @case('chat')
            @livewire('admin.chat.index')
            @break

        @default
            <div class="text-muted">Page not found</div>
    @endswitch
</div>
