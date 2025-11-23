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
                    {{-- Breadcrumb --}}
                    @if($this->getBreadcrumbs())
                        <section class="fi-section">
                            <div class="fi-section-content px-6 py-3">
                                <nav class="flex" aria-label="Breadcrumb">
                                    <ol class="inline-flex items-center space-x-1">
                                        @foreach($this->getBreadcrumbs() as $index => $breadcrumb)
                                            <li class="inline-flex items-center">
                                                @if($index > 0)
                                                    <svg class="w-3 h-3 mx-1 text-gray-400 dark:text-gray-500"
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                              clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                                <span class="text-sm {{ $loop->last ? 'font-medium text-gray-950 dark:text-white' : 'text-gray-500 dark:text-gray-400' }}">
                                                    {{ $breadcrumb }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ol>
                                </nav>
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
                <svg class="animate-spin h-5 w-5 text-primary-600 dark:text-primary-400"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-950 dark:text-white">{{ __('filament-panels::pages/dashboard.loading') }}</span>
            </div>
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
