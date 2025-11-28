<x-filament-panels::page>
    <div
            x-data="{
            init() {
                // Listen for Livewire 'update-url' event to update browser URL
                $wire.on('update-url', (event) => {
                    const selectedPageId = event.selectedPageId;
                    const url = new URL(window.location);

                    if (selectedPageId) {
                        url.searchParams.set('selectedPageId', selectedPageId);
                    } else {
                        url.searchParams.delete('selectedPageId');
                    }

                    window.history.pushState({}, '', url);
                });

                // Handle browser back/forward buttons
                window.addEventListener('popstate', () => {
                    const params = new URLSearchParams(window.location.search);
                    const pageId = params.get('selectedPageId');

                    if (pageId) {
                        $wire.dispatch('page-selected', { pageId: parseInt(pageId) });
                    } else {
                        // Clear selection
                        $wire.set('selectedPageId', null);
                        $wire.set('selectedPage', null);
                    }
                });
            }
        }"
            class="grid grid-cols-1 md:grid-cols-12 gap-6"
    >
        {{-- Left Panel: Tree Navigation --}}
        <div class="md:col-span-4 lg:col-span-3">
            <section class="fi-section md:sticky md:top-6">
                <div class="fi-section-header flex items-center gap-x-3 overflow-hidden px-6 py-4">
                    <div class="grid flex-1 gap-y-1">
                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            {{ __('siteman::page.tree.title') }}
                        </h3>
                        <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                            {{ __('siteman::page.tree.description') }}
                        </p>
                    </div>
                </div>

                <div class="fi-section-content-ctn">
                    <div class="fi-section-content p-6 overflow-y-auto max-h-[calc(100vh-16rem)]">
                        <livewire:page-tree :selected-page-id="$this->selectedPageId"/>
                    </div>
                </div>
            </section>
        </div>

        {{-- Right Panel: Edit Form or Empty State --}}
        <div class="md:col-span-8 lg:col-span-9">
            @if($this->selectedPage)
                <div class="space-y-6">
                    {{-- Breadcrumb Navigation --}}
                    @if(count($this->getBreadcrumbs()) > 1)
                        <section class="fi-section">
                            <div class="fi-section-content px-6 py-3">
                                <x-filament::breadcrumbs :breadcrumbs="$this->getBreadcrumbs()" />
                            </div>
                        </section>
                    @endif

                    {{-- Quick Navigation --}}
                    @if($this->selectedPage->parent || $this->selectedPage->children->isNotEmpty())
                        <section class="fi-section">
                            <div class="fi-section-content px-6 py-3">
                                <div class="flex flex-wrap gap-3 text-sm">
                                    @if($this->selectedPage->parent)
                                        <button
                                            type="button"
                                            wire:click="$dispatch('page-selected', { pageId: {{ $this->selectedPage->parent->id }} })"
                                            class="inline-flex items-center gap-1.5 text-gray-600 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                            </svg>
                                            <span>{{ __('Go to Parent') }}: <strong>{{ $this->selectedPage->parent->title }}</strong></span>
                                        </button>
                                    @endif

                                    @if($this->selectedPage->children->isNotEmpty())
                                        <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            <span>{{ __('Children') }} ({{ $this->selectedPage->children->count() }}):</span>
                                            @foreach($this->selectedPage->children as $child)
                                                <button
                                                    type="button"
                                                    wire:click="$dispatch('page-selected', { pageId: {{ $child->id }} })"
                                                    class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium transition"
                                                >
                                                    {{ $child->title }}@if(!$loop->last),@endif
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </section>
                    @endif

                    {{-- Read-Only Banner --}}
                    @if($this->isFormReadOnly())
                        <section class="fi-section">
                            <div class="fi-section-content">
                                <div class="rounded-lg bg-warning-50 dark:bg-warning-400/10 p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-warning-600 dark:text-warning-400" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-warning-800 dark:text-warning-200">
                                                {{ __('Read-Only Mode') }}
                                            </h3>
                                            <p class="mt-1 text-sm text-warning-700 dark:text-warning-300">
                                                {{ __('You do not have permission to edit this page. All fields are disabled.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endif

                    {{-- Edit Form --}}
                    <form wire:submit="save" class="space-y-6">
                        {{ $this->form }}

                        <div class="fi-form-actions">
                            <div class="flex flex-wrap items-center gap-3 fi-fo-actions">
                                @foreach($this->getFormActions() as $action)
                                    {{ $action }}
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
            @else
                {{-- Empty State --}}
                <div class="fi-section">
                    <div class="flex items-center justify-center min-h-[400px]">
                        <div class="p-8 text-center max-w-md">
                            <div class="flex justify-center mb-4">
                                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-white/5">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-2">
                                {{ __('siteman::page.tree.empty_selection_title') }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('siteman::page.tree.empty_selection') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading.delay
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/50 dark:bg-gray-950/75">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10 px-6 py-4">
            <div class="flex items-center gap-3">
                <x-filament::loading-indicator class="h-5 w-5" />
                <span class="text-sm font-medium text-gray-950 dark:text-white">{{ __('filament-panels::pages/dashboard.loading') }}</span>
            </div>
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
