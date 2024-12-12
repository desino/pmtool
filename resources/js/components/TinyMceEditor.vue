<template>
    <editor ref="htmlEditorRef" v-model="content" :init="config" @change="update" @init="loaded" @keyup="update"
        @redo="update" @undo="update" />
</template>

<script lang="ts">
import Editor from '@tinymce/tinymce-vue';
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'VHtmlEditor',
    props: {
        value: {
            type: String,
            required: false,
            default: '',
        },
    },
    components: {
        Editor,
    },
    emits: ['input'],
    computed: {
        config(): any {
            return {
                selector: `textarea#editor`,
                hidden_input: false,
                base_url: '/tinymce/', // Base URL for TinyMCE resources
                suffix: '.min', // Suffix for TinyMCE resources
                license_key: 'gpl', // for use without api key
                entity_encoding: 'UTF-8',
                height: 650,
                width: '100%',
                menubar: false,
                // contextmenu: 'paste | link image inserttable | cell row column deletetable',
                browser_spellcheck: true,
                contextmenu: false,
                extended_valid_elements: 'img[!src|border:0|alt|title|width|height|style]a[name|href|target|title|onclick]',
                plugins: [
                    'advlist',
                    'autolink',
                    'lists',
                    'link',
                    'image',
                    'charmap',
                    'preview',
                    'anchor',
                    'searchreplace',
                    'visualblocks',
                    'code',
                    'fullscreen',
                    'insertdatetime',
                    'media',
                    'table',
                    'code',
                    'help',
                    'wordcount',
                ],
                mobile: {
                    toolbar_mode: 'wrap',
                },
                toolbar:
                    'undo redo | blocks | bold italic underline strikethrough forecolor backcolor | align bullist numlist | lineheight outdent indent | table',
                paste_data_images: true,
                setup: (editor) => {
                    editor.on('PastePreProcess', (event) => {
                        const clipboardData = event.content;
                        if (clipboardData.includes('<img')) {
                            event.preventDefault();
                            event.stopImmediatePropagation();
                        }
                    });

                    editor.on('Paste', async (event) => {
                        if (event.clipboardData) {
                            const items = event.clipboardData.items;
                            for (const item of items) {
                                if (item.type.startsWith('image/')) {
                                    const file = item.getAsFile();
                                    const compressedDataUrl = await this.compressImage(file);

                                    editor.insertContent(`<img src="${compressedDataUrl}" />`);

                                    event.preventDefault();
                                    event.stopImmediatePropagation();
                                    return;
                                }
                            }
                        }
                    });

                    editor.on('Drop', async (event) => {
                        const files = event.dataTransfer?.files;
                        if (files) {
                            for (const file of files) {
                                if (file.type.startsWith('image/')) {
                                    const compressedDataUrl = await this.compressImage(file);

                                    editor.insertContent(`<img src="${compressedDataUrl}" />`);

                                    event.preventDefault();
                                    event.stopImmediatePropagation();
                                    return;
                                }
                            }
                        }
                    });

                    editor.on('dragstart dragover drop', (event) => {
                        event.preventDefault();
                    });
                },
            };
        },
    },
    data() {
        return {
            content: this.value,
        };
    },
    watch: {
        value() {
            this.content = this.value;
        },
    },
    methods: {
        loaded(event: any, editor: any) {
            this.editor = editor;
        },
        update() {
            this.$emit('input', this.content);
        },
        async compressImage(file: File): Promise<string> {
            const img = new Image();
            const reader = new FileReader();

            const dataUrl = await new Promise<string>((resolve) => {
                reader.onload = (event) => resolve(event.target?.result as string);
                reader.readAsDataURL(file);
            });

            img.src = dataUrl;

            await new Promise<void>((resolve) => {
                img.onload = () => resolve();
            });

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d')!;
            const maxWidth = 800;
            const scaleFactor = maxWidth / img.width;

            canvas.width = maxWidth;
            canvas.height = img.height * scaleFactor;

            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            return canvas.toDataURL('image/jpeg', 0.75); // 75% quality
        },
    },
});
</script>
