@php
    $fieldWrapperView = $getFieldWrapperView();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $previewHeight = $getPreviewHeight();
    $frameworkHead = $getFrameworkHead();
    $defaultHtml = $getDefaultHtmlForType();
    $blockTypeDef = $getBlockTypeDefinition();
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('nuwa', 'jibaymcs/nuwa') }}"
        x-data="nuwaBlockEditor({
            state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false) }},
            frameworkHead: @js($frameworkHead),
            defaultHtml: @js($defaultHtml),
            previewHeight: @js($previewHeight),
            isDisabled: @js($isDisabled),
        })"
        wire:ignore
        class="fi-nuwa-block-editor rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden"
    >
        {{-- Toolbar --}}
        <div class="flex items-center border-b border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <template x-for="tab in ['html', 'css', 'js']" :key="tab">
                <button
                    type="button"
                    @click="switchTab(tab)"
                    :class="{
                        'bg-white dark:bg-gray-800 border-b-2 border-primary-500 text-primary-600 dark:text-primary-400': activeTab === tab,
                        'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': activeTab !== tab,
                    }"
                    class="px-4 py-2 text-sm font-medium uppercase tracking-wide transition"
                    x-text="tab"
                ></button>
            </template>

            <div class="ml-auto px-3 flex items-center gap-2">
                {{-- Loading indicator --}}
                <span
                    x-show="!monacoReady"
                    class="text-xs text-gray-400 animate-pulse"
                >Loading editor...</span>

                <button
                    type="button"
                    @click="refreshPreview()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                    title="Refresh preview"
                >
                    <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Split: editors + preview --}}
        <div class="grid grid-cols-1 lg:grid-cols-2">
            {{-- Monaco editors --}}
            <div class="border-r border-gray-300 dark:border-gray-700 relative">
                @foreach (['html', 'css', 'js'] as $tab)
                    <div
                        x-show="activeTab === '{{ $tab }}'"
                        x-cloak
                        class="w-full"
                        :style="'height: ' + previewHeight + 'px'"
                    >
                        <div
                            x-ref="{{ $tab }}Editor"
                            class="w-full h-full"
                        ></div>
                    </div>
                @endforeach
            </div>

            {{-- Live preview (iframe) --}}
            <div class="bg-white">
                <div class="flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Preview</span>
                    <div class="ml-auto flex gap-1">
                        <template x-for="mode in ['desktop', 'tablet', 'mobile']" :key="mode">
                            <button
                                type="button"
                                @click="viewportMode = mode"
                                :class="viewportMode === mode ? 'text-primary-500' : 'text-gray-400'"
                                class="p-1 hover:text-primary-400 transition"
                            >
                                <template x-if="mode === 'desktop'"><x-filament::icon icon="heroicon-o-computer-desktop" class="w-4 h-4" /></template>
                                <template x-if="mode === 'tablet'"><x-filament::icon icon="heroicon-o-device-tablet" class="w-4 h-4" /></template>
                                <template x-if="mode === 'mobile'"><x-filament::icon icon="heroicon-o-device-phone-mobile" class="w-4 h-4" /></template>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="flex justify-center bg-gray-50 dark:bg-gray-900 p-2" :style="'height: ' + previewHeight + 'px'">
                    <iframe
                        x-ref="previewFrame"
                        :style="'width: ' + viewportWidth + '; height: 100%; border: none; background: white; transition: width 0.3s ease;'"
                        sandbox="allow-scripts allow-same-origin"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
