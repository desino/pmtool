<template>
    <Editor
      v-model="content"
      :init="editorConfig"
      @editorChange="onEditorChange"
    />
  </template>
  
  <script>
  import { defineComponent, ref, watch } from 'vue';
  import { Editor } from '@tinymce/tinymce-vue';
  
  export default defineComponent({
    name: 'TinyMceEditor',
    components: {
      Editor
    },
    props: {
      modelValue: {
        type: String,
        default: ''
      }
    },
    setup(props, { emit }) {
      const content = ref(props.modelValue);
  
      watch(() => props.modelValue, (newValue) => {
        content.value = newValue;
      });
  
      const editorConfig = {
        plugins: 'link image code',
        toolbar: 'undo redo | link image | code',
        height: 500,
        menubar: false
      };
  
      const onEditorChange = (content) => {
        emit('update:modelValue', content);
      };
  
      return {
        content,
        editorConfig,
        onEditorChange
      };
    }
  });
  </script>
  
  <style scoped>
  /* Add any custom styles here */
  </style>
  