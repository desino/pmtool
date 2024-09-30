<template>
    <div class="modal-dialog">
        <form @submit.prevent="submitCreateRelease">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="CreateReleaseModalLabel" class="modal-title">{{ $t('ticket.release.create.modal_title') }}
                    </h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div v-if="showErrorMessage" class="alert alert-danger">
                        <button type="button" class="btn-close" aria-label="Close" @click="hideErrorMessage"></button>
                        {{ showErrorMessage }}
                    </div>
                    <div class="mb-3 p-3 shadow">
                        <select v-model="formData.release_id" :class="{ 'is-invalid': errors.release_id }"
                            id="release_id" class="form-select">
                            <option value="">{{
                                $t('ticket.assign.create.release.modal.select_unprocessed_release_placeholder') }}
                            </option>
                            <option v-for="unprocessedRelease in unprocessedReleaseList" :key="unprocessedRelease.id"
                                :value="unprocessedRelease.id">
                                {{ unprocessedRelease.name }}
                            </option>
                        </select>
                        <div v-if="errors.release_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.release_id" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="row w-100 my-3">
                        <div class="col-5">
                            <hr>
                        </div>
                        <div class="col-2 text-center">
                            <span>OR</span>
                        </div>
                        <div class="col-5">
                            <hr>
                        </div>
                    </div>
                    <div class="shadow p-3">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" v-model="formData.is_major"
                                    :class="{ 'is-invalid': errors.is_major }" type="checkbox" id="is_major">
                                <label class="form-check-label" for="is_major">
                                    {{ $t('ticket.release.create.modal.checkbox.is_major') }}
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input id="tags" v-model="formData.tags" :class="{ 'is-invalid': errors.tags }"
                                class="form-control" type="text"
                                :placeholder="$t('ticket.release.create.modal.input.tags')">
                            <div v-if="errors.tags" class="invalid-feedback">
                                <span v-for="(error, index) in errors.tags" :key="index">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-desino" type="submit">
                        {{ $t('ticket.release.create.modal_submit_but_text') }}
                    </button>
                    <button class="btn btn-secondary" @click="hideModal" data-bs-dismiss="modal"
                        type="button">Close</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import GlobalMessage from "../../../components/GlobalMessage.vue";
import { mapActions, mapGetters } from "vuex";
import { Modal } from 'bootstrap';
import showToast from '../../../utils/toasts';
import messageService from '../../../services/messageService';
import TicketService from '../../../services/TicketService';
export default {
    name: 'AssignProjectModalComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
    },
    data() {
        return {
            formData: {
                release_id: '',
                initiative_id: '',
                is_major: false,
                tags: '',
                selectedTasks: [],
            },
            unprocessedReleaseList: {},
            errors: {},
            showMessage: true,
            showErrorMessage: ""
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setLoading']),
        getSelectedTasksData(data) {
            this.clearMessages();
            this.resetForm();
            this.formData.selectedTasks = data.tasks;
            this.formData.initiative_id = data.initiative_id;
            this.getCreateReleaseModelData();
        },
        async getCreateReleaseModelData() {
            try {
                const { content: { unprocessedRelease } } = await TicketService.getCreateReleaseModelData(this.formData.initiative_id);
                this.unprocessedReleaseList = unprocessedRelease;
                this.formData.release_id = unprocessedRelease[0]?.id ?? '';
            } catch (error) {
                this.handleError(error);
            }
        },
        async submitCreateRelease() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const response = await TicketService.submitCreateRelease(this.formData);
                showToast(response.message, 'success');
                this.hideModal();
                this.setLoading(false);
                this.resetForm();
                this.$emit('refreshTickets');
            } catch (error) {
                this.handleError(error);
            }
        },
        hideModal() {
            const modalElement = document.getElementById('createReleaseModal');
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
                this.showErrorMessage = error.message
                // messageService.setMessage(error.message, 'danger');
            }
            this.setLoading(false);
        },
        resetForm() {
            this.formData = {
                release_id: '',
                initiative_id: '',
                is_major: false,
                tags: '',
                selectedTasks: [],
            };
            this.errors = {};
        },
        clearMessages() {
            this.showErrorMessage = "";
            this.errors = {};
            messageService.clearMessage();
        },
        hideErrorMessage() {
            this.showErrorMessage = "";
        }
    },
    mounted() {
        this.clearMessages();
        this.setLoading(false);
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
<style scoped>
.alert {
    margin-top: 1rem;
}

.btn-close {
    position: absolute;
    right: 1rem;
}
</style>