<template>

    <div class="fs-5 mt-3">
        <div class="w-100" v-if="!showInput">
            <a class="btn btn-desino border-0 w-100" role="button" @click="showHideInput">
                <span class="fw-bold"><i class="bi bi-plus-lg"></i></span> Add Section
            </a>
        </div>
        <div class="w-100" v-if="showInput">
            <div class="input-group mb-3">
                <input type="text" class="form-control ms-4"
                    :placeholder="$t('solution_design.add_section_input_placeholder')" v-model="formData.section_name"
                    aria-label="Recipient's username" aria-describedby="button-addon2" @keyup.enter="handleEnter"
                    @keydown.esc="handleHideInput" :class="{ 'is-invalid': errors.section_name }" autofocus>
                <button class="btn btn-outline-danger bg-danger text-white" type="button" id="button-addon2"
                    @click="handleHideInput">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div v-if="errors.section_name" class="invalid-feedback ms-4">
                    <span v-for="(error, index) in errors.section_name" :key="index">
                        {{ error }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import messageService from './../../../services/messageService';
import SolutionDesignService from './../../../services/SolutionDesignService';
import showToast from '../../../utils/toasts';
import { mapActions } from 'vuex';
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
                section_name: ''
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
        ...mapActions(['setLoading']),
        showHideInput() {
            this.showInput = true;
            this.isSaving = false;
        },
        handleHideInput() {
            this.resetForm();
        },
        // handleBlur() {
        //     // if (this.isSaving) return;
        //     // this.isSaving = true;
        //     // this.storeSection();

        //     this.resetForm();
        // },
        handleStoreButton() {
            if (this.isSaving) return;
            this.isSaving = true;
            this.storeSection();
        },
        handleEnter(event) {
            event.preventDefault();
            // if (this.isSaving) return;
            this.isSaving = true;
            this.storeSection();
        },
        async storeSection() {
            try {
                this.setLoading(true);
                const response = await SolutionDesignService.storeSection(this.formData);
                this.sectionData = response.content;
                this.$emit('sectionAdded', this.sectionData);
                this.resetForm();
                showToast(response.message, 'success');
                this.setLoading(false);
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
            this.setLoading(false);
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
        resetForm() {
            this.showInput = false;
            this.formData.section_name = "";
            this.errors = {}
        }
    }
}
</script>
