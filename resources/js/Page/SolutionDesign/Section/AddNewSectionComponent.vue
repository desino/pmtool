<template>
    <div class="row mt-3">
        <div class="col-md-4" v-if="!showInput">
            <button class="button btn btn-desino bg-desino text-light" type="button" @click="showHideInput">
                <i class="bi bi-plus-lg"></i> {{ $t('solution_design.add_section_but_text') }}
            </button>
        </div>
        <div class="col-md-4" v-if="showInput">
            <input type="text" @blur="handleBlur" @keyup.enter="handleEnter" v-model="formData.name" autofocus
                class="form-control" id="name" :placeholder="$t('solution_design.add_section_input_placeholder')">
        </div>
    </div>
</template>

<script>
import messageService from './../../../services/messageService';
import SolutionDesignService from './../../../services/SolutionDesignService';
import showToast from '../../../utils/toasts';
export default {
    name: 'AddNewSectionComponent',
    props: {
        initiativeData: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            showInput: false,
            formData: {
                initiative_id: '',
                name: ''
            },
            errors: {}
        };
    },
    watch: {
        initiativeData: {
            handler(newVal) {
                this.formData.initiative_id = newVal.id || null; // Update formData when initiativeData changes
            },
            deep: true
        }
    },
    methods: {
        showHideInput() {
            this.showInput = true;
            this.isSaving = false;
        },
        handleBlur() {
            if (this.isSaving) return; // Prevent duplicate calls
            this.isSaving = true;
            this.storeSection();
        },
        handleEnter(event) {
            event.preventDefault(); // Prevent form submission if inside a form
            if (this.isSaving) return; // Prevent duplicate calls
            this.isSaving = true;
            this.storeSection();
        },
        async storeSection() {
            try {
                const response = await SolutionDesignService.storeSection(this.formData);
                this.sectionData = response.content;
                this.$emit('sectionAdded', this.sectionData);
                this.resetForm();
                showToast(response.message, 'success');
            } catch (error) {
                this.handleError(error);
            }
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger');
            }
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
        resetForm() {
            this.showInput = false;
            this.formData.name = "";
            this.errors = {}
        }
    }
}
</script>
