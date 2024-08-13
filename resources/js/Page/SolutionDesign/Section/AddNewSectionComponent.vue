<template>

    <div class="d-flex align-items-center section-container pe-4">
        <div class="fw-bold fs-5 mt-3">
            <div class="col-md-12" v-if="!showInput">
                <a class="text-decoration-none text-dark ms-4" role="button" @click="showHideInput">
                    <span><i class="bi bi-plus-lg fw-bolder"></i></span> Add Section
                </a>
            </div>
            <div class="col-md-12" v-if="showInput">
                <!-- <input type="text" @blur="handleBlur" @keyup.enter="handleEnter" v-model="formData.name" autofocus
                    class="form-control ms-4" id="name"
                    :placeholder="$t('solution_design.add_section_input_placeholder')"> -->

                <div class="input-group mb-3">
                    <input type="text" class="form-control ms-4"
                        :placeholder="$t('solution_design.add_section_input_placeholder')" v-model="formData.name"
                        aria-label="Recipient's username" aria-describedby="button-addon2" @blur="handleBlur"
                        @keyup.enter="handleEnter">
                    <button class="btn btn-outline-desino bg-desino text-white" type="button" id="button-addon2"
                        @click="handleStoreButton">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </div>
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
            // if (this.isSaving) return;
            // this.isSaving = true;
            // this.storeSection();

            this.resetForm();
        },
        handleStoreButton() {
            if (this.isSaving) return;
            this.isSaving = true;
            this.storeSection();
        },
        handleEnter(event) {
            event.preventDefault();
            if (this.isSaving) return;
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
