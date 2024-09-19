<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="updateTestCase">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="updateTestCaseModalLabel" class="modal-title">{{ $t('task_detail.update_testcase_heading')
                        }}
                    </h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <label>Test Case </label>
                    <p class="text-muted">{{ testCase }}</p>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{
                            $t('task_details.update_observations_input_name') }} <strong
                                class="text-danger">*</strong></label>
                        <TinyMceEditor v-model="formData.observations" :class="{ 'is-invalid': errors.observations }" />
                        <div v-if="errors.observations" class="invalid-feedback">
                            <span v-for="(error, index) in errors.observations" :key="index">{{ error }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-desino" type="submit">
                        {{ $t('task_detail.update_testcase_submit_btn_text') }}
                    </button>
                    <button class="btn btn-secondary" @click="hideModal" data-bs-dismiss="modal" type="button">{{
                        $t('task_detail.update_testcase_close_btn_text') }}</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import GlobalMessage from './../../../../components/GlobalMessage.vue';
import messageService from '../../../../services/messageService';
import { Modal } from 'bootstrap';
import showToast from '../../../../utils/toasts';
import { mapActions } from "vuex";
import testCaseService from "../../../../services/TestCaseService.js";
import TinyMceEditor from "./../../../../components/TinyMceEditor.vue";

export default {
    name: 'UpdateTestCaseModalComponent',
    components: {
        TinyMceEditor,
        GlobalMessage,
    },
    props: {
        ticket_id: String,
        initiative_id: String,
    },
    data() {
        return {
            testCase: '',
            localTicketId: this.$props.ticket_id,
            localInitiativeId: this.$props.initiative_id,
            test_case_id: null,
            formData: {
                observations: "",
                status: ""
            },
            errors: {},
            showMessage: true,
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async getTestCaseData(testCaseId, status = false) {
            this.test_case_id = testCaseId;
            this.clearMessages();
            try {
                await this.setLoading(true);
                const response = await testCaseService.getTestCase(this.localInitiativeId, this.localTicketId, testCaseId);
                this.testCase = response.content.expected_behaviour;
                this.formData.observations = response.content.observations;
                this.formData.status = status;
                await this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async updateTestCase() {
            this.clearMessages();
            try {
                await this.setLoading(true);
                this.formData.ticket_id = this.localTicketId;
                this.formData.test_case_id = this.test_case_id;
                this.formData.initiative_id = this.localInitiativeId;
                const response = await testCaseService.updateTestCase(this.formData);
                await this.setLoading(false);
                this.$emit('stored-testcase', response);
                showToast(response.message, 'success');
                this.hideModal();
                this.resetForm();
            } catch (error) {
                this.handleError(error);
            }
        },
        hideModal() {
            const modalElement = document.getElementById('updateTestCaseModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    this.setLoading(false);
                    modal.hide();
                }
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
        resetForm() {
            this.formData = {
                observations: "",
            };
            this.errors = {};
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
    },
    mounted() {
        this.clearMessages();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
