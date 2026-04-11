<div
    x-load
    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('nuwa-editor', 'jibaymcs/nuwa') }}"
    x-data="nuwaEditorApp({
        blockTypes: @js($this->blockTypes),
        initialBlocks: @js($this->initialBlocks),
        rootOrder: @js($this->rootOrder),
        framework: @js($this->framework),
        frameworkHead: @js($this->frameworkHead),
        pageId: @js($page->id),
    })"
    class="fi-nuwa-editor flex flex-col h-[calc(100vh-4rem)]"
>
    {{-- Toolbar --}}
    <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shrink-0">
        {{-- Add block --}}
        <div class="relative">
            <button
                type="button"
                @click="pickerOpen = !pickerOpen"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-500 transition"
            >
                <x-filament::icon icon="heroicon-o-plus" class="w-4 h-4" />
                {{ __('nuwa::nuwa.editor.add_block') }}
            </button>

            {{-- Block picker dropdown --}}
            <div
                x-show="pickerOpen"
                x-cloak
                @click.outside="pickerOpen = false"
                class="absolute top-full left-0 mt-1 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
            >
                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                    <input
                        type="text"
                        x-model="pickerFilter"
                        placeholder="{{ __('nuwa::nuwa.editor.search_blocks') }}"
                        class="w-full px-3 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 focus:ring-primary-500 focus:border-primary-500"
                    />
                </div>
                <div class="max-h-64 overflow-y-auto p-1">
                    <template x-for="bt in filteredBlockTypes()" :key="bt.name">
                        <button
                            type="button"
                            @click="addBlock(bt.name)"
                            class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition text-left"
                        >
                            <span class="text-gray-500 dark:text-gray-400 text-xs uppercase w-16 shrink-0" x-text="bt.category"></span>
                            <span class="font-medium text-gray-900 dark:text-gray-100" x-text="bt.label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Separator --}}
        <div class="w-px h-6 bg-gray-200 dark:bg-gray-700"></div>

        {{-- Delete selected --}}
        <button
            type="button"
            @click="removeSelectedBlock()"
            :disabled="!selectedBlock"
            :class="selectedBlock ? 'text-red-500 hover:text-red-400' : 'text-gray-300 dark:text-gray-600 cursor-not-allowed'"
            class="p-1.5 rounded transition"
            title="{{ __('nuwa::nuwa.editor.delete_block') }}"
        >
            <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4" />
        </button>

        <div class="w-px h-6 bg-gray-200 dark:bg-gray-700"></div>

        {{-- Viewport toggle --}}
        @foreach (['desktop' => 'heroicon-o-computer-desktop', 'tablet' => 'heroicon-o-device-tablet', 'mobile' => 'heroicon-o-device-phone-mobile'] as $mode => $icon)
            <button
                type="button"
                @click="setViewport('{{ $mode }}')"
                :class="viewportMode === '{{ $mode }}' ? 'text-primary-500' : 'text-gray-400'"
                class="p-1.5 rounded hover:text-primary-400 transition"
            >
                <x-filament::icon :icon="$icon" class="w-4 h-4" />
            </button>
        @endforeach

        <div class="w-px h-6 bg-gray-200 dark:bg-gray-700"></div>

        {{-- Preview toggle --}}
        <button
            type="button"
            @click="togglePreview()"
            :class="mode === 'preview' ? 'text-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-400'"
            class="p-1.5 rounded hover:text-primary-400 transition"
            title="{{ __('nuwa::nuwa.editor.preview') }}"
        >
            <x-filament::icon icon="heroicon-o-eye" class="w-4 h-4" />
        </button>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Dirty indicator --}}
        <span
            x-show="isDirty"
            x-cloak
            class="text-xs text-amber-500 font-medium"
        >{{ __('nuwa::nuwa.editor.unsaved') }}</span>

        {{-- Save --}}
        <button
            type="button"
            @click="save()"
            :disabled="saving || !isDirty"
            class="inline-flex items-center gap-1.5 px-4 py-1.5 text-sm font-medium rounded-lg transition"
            :class="isDirty ? 'bg-primary-600 text-white hover:bg-primary-500' : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'"
        >
            <template x-if="saving">
                <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4 animate-spin" />
            </template>
            <template x-if="!saving">
                <x-filament::icon icon="heroicon-o-check" class="w-4 h-4" />
            </template>
            {{ __('nuwa::nuwa.editor.save') }}
        </button>
    </div>

    {{-- Main area: Canvas + Inspector --}}
    <div class="flex flex-1 overflow-hidden">
        {{-- Canvas --}}
        <div class="flex-1 bg-gray-100 dark:bg-gray-950 flex justify-center p-4 overflow-auto">
            <iframe
                x-ref="editorCanvas"
                :style="'width: ' + viewportWidth + '; height: 100%; border: none; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: width 0.3s ease;'"
                sandbox="allow-scripts allow-same-origin"
            ></iframe>
        </div>

        {{-- Inspector sidebar --}}
        <div
            x-show="selectedBlock"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-80 bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden shrink-0"
        >
            {{-- Inspector header --}}
            <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase" x-text="selectedBlock?.type"></span>
                <button
                    type="button"
                    @click="deselectBlock()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                >
                    <x-filament::icon icon="heroicon-o-x-mark" class="w-4 h-4" />
                </button>
            </div>

            {{-- Inspector tabs --}}
            <div class="flex border-b border-gray-200 dark:border-gray-700">
                @foreach (['properties' => 'nuwa::nuwa.editor.properties', 'styles' => 'nuwa::nuwa.editor.styles', 'code' => 'nuwa::nuwa.editor.code'] as $tab => $label)
                    <button
                        type="button"
                        @click="setInspectorTab('{{ $tab }}')"
                        :class="inspectorTab === '{{ $tab }}'
                            ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400'
                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                        class="flex-1 px-3 py-2 text-xs font-medium uppercase tracking-wide transition"
                    >
                        {{ __($label) }}
                    </button>
                @endforeach
            </div>

            {{-- Inspector content --}}
            <div class="flex-1 overflow-y-auto">
                {{-- Properties tab --}}
                <div x-show="inspectorTab === 'properties'" x-cloak class="p-4 space-y-3">
                    <template x-for="(value, key) in selectedBlockData" :key="key">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase" x-text="key"></label>
                            <input
                                type="text"
                                :value="value"
                                @change="updateProperty(key, $event.target.value)"
                                class="w-full px-3 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-primary-500 focus:border-primary-500"
                            />
                        </div>
                    </template>
                    <p x-show="Object.keys(selectedBlockData).length === 0" class="text-sm text-gray-400 italic">
                        {{ __('nuwa::nuwa.editor.no_properties') }}
                    </p>
                </div>

                {{-- Styles tab --}}
                <div x-show="inspectorTab === 'styles'" x-cloak class="p-4">
                    <p class="text-sm text-gray-400 italic">
                        {{ __('nuwa::nuwa.editor.styles_coming') }}
                    </p>
                </div>

                {{-- Code tab --}}
                <div x-show="inspectorTab === 'code'" x-cloak class="flex flex-col h-full">
                    @foreach (['html' => 'HTML', 'css' => 'CSS', 'js' => 'JavaScript'] as $field => $label)
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <div class="px-3 py-1.5 bg-gray-50 dark:bg-gray-800">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $label }}</span>
                            </div>
                            <div x-ref="inspector_{{ $field }}" class="h-40"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
