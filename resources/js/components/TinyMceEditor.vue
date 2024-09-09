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
                height: 375,
                width: '100%',
                menubar: 'format table',
                contextmenu: 'paste | link image inserttable | cell row column deletetable',
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
                    'undo redo | blocks | bold italic underline strikethrough forecolor backcolor '
                    + 'fontselect | alignleft aligncenter alignright alignjustify | '
                    + 'bullist numlist outdent indent | table | removeformat | image ',
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
    },
});
</script>
