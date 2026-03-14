@props([
    'label' => null,
    'name',
    'value' => '',
    'hint' => null,
    'error' => null,
])

@php
    $wireAttributes = $attributes->whereStartsWith('wire:model');
@endphp

<div
    x-data="wysiwygEditor({
        initialValue: @js($value),
    })"
    class="space-y-2"
>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50 px-3 py-3">
            <select
                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700"
                @change="setBlock($event.target.value)"
            >
                <option value="p">Paragraf</option>
                <option value="h2">Naslov H2</option>
                <option value="h3">Naslov H3</option>
                <option value="blockquote">Citat</option>
            </select>

            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" @click="format('bold')"><strong>B</strong></button>
            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium italic text-slate-700 hover:bg-slate-100" @click="format('italic')">I</button>
            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium underline text-slate-700 hover:bg-slate-100" @click="format('underline')">U</button>
            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" @click="format('insertUnorderedList')">Lista</button>
            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" @click="insertLink()">Link</button>
            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100" @click="format('removeFormat')">Očisti</button>
        </div>

        <div
            x-ref="editor"
            class="wysiwyg-content min-h-[320px] px-5 py-4 text-base leading-7 text-slate-800 focus:outline-none"
            contenteditable="true"
            @input="syncFromEditor"
            @blur="syncFromEditor"
        ></div>
    </div>

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        x-ref="textarea"
        class="hidden"
        x-model="content"
        {{ $wireAttributes }}
    ></textarea>

    @if ($hint)
        <p class="text-sm text-slate-500">{{ $hint }}</p>
    @endif

    @if ($error)
        <p class="text-sm font-medium text-red-600">{{ $error }}</p>
    @endif
</div>

@once
    @push('scripts')
        <script>
            window.wysiwygEditor = function ({ initialValue = '' } = {}) {
                return {
                    content: initialValue,
                    init() {
                        this.$refs.editor.innerHTML = this.content || '<p></p>';
                        this.pushValue();
                    },
                    focusEditor() {
                        this.$refs.editor.focus();
                    },
                    syncFromEditor() {
                        this.content = this.$refs.editor.innerHTML.trim();
                        this.pushValue();
                    },
                    pushValue() {
                        if (! this.$refs.textarea) {
                            return;
                        }

                        this.$refs.textarea.value = this.content;
                        this.$refs.textarea.dispatchEvent(new Event('input', { bubbles: true }));
                        this.$refs.textarea.dispatchEvent(new Event('change', { bubbles: true }));
                    },
                    format(command, value = null) {
                        this.focusEditor();
                        document.execCommand(command, false, value);
                        this.syncFromEditor();
                    },
                    setBlock(tag) {
                        this.focusEditor();
                        document.execCommand('formatBlock', false, tag);
                        this.syncFromEditor();
                    },
                    insertLink() {
                        const url = window.prompt('Unesi URL');

                        if (! url) {
                            return;
                        }

                        this.format('createLink', url);
                    },
                };
            };
        </script>
    @endpush
@endonce
