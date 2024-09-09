<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="updateTestCase">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="updateTestCaseModalLabel" class="modal-title">{{ $t('task_detail.update_testcase_heading') }}
                    </h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <label>Test Case </label>
                    <p class="text-muted">{{testCase}}</p>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label" for="name">{{ $t('task_details.update_comments_input_name') }} <strong
                            class="text-danger">*</strong></label>
                        <textarea id="name" v-model="formData.comment" :class="{ 'is-invalid': errors.comment }"
                                  class="form-control" type="text">
                        </textarea>
                        <div v-if="errors.comment" class="invalid-feedback">
                            <span v-for="(error, index) in errors.comment" :key="index">{{ error }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-desino bg-desino text-light" type="submit">
                        {{ $t('task_detail.update_testcase_submit_btn_text') }}
                    </button>
                    <button class="btn btn-secondary" @click="hideModal" data-bs-dismiss="modal"
                            type="button">{{$t('task_detail.update_testcase_close_btn_text')}}</button>
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

export default {
    name: 'UpdateTestCaseModalComponent',
    components: {
        GlobalMessage,
    },
    props: {
        ticket_id: Number,
    },
    data() {
        return {
            testCase: '',
            ticket_id: this.$props.ticket_id,
            test_case_id: null,
            formData: {
                comment: "",
            },
            errors: {},
            showMessage: true,
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async getTestCaseData(testCaseId)
        {
            this.test_case_id = testCaseId;
            this.clearMessages();
            try {
                await this.setLoading(true);
                const response = await testCaseService.getTestCase(this.ticket_id, testCaseId);
                this.testCase = response.content.test_case;
                this.formData.comment = response.content.comment;
                await this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async updateTestCase() {
            this.clearMessages();
            try {
                await this.setLoading(true);
                this.formData.ticket_id = this.ticket_id;
                this.formData.test_case_id = this.test_case_id;
                const response = await testCaseService.updateTestCase(this.formData);
                await this.setLoading(false);
                this.$emit('stored-testcase',response);
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
                comment: "",
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
