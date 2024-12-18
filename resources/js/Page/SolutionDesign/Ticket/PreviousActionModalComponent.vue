<template>
    <div class="modal fade" id="previousActionModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title" id="confirmationModalLabel">{{ title }}</h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <div class="row w-100 g-1 mb-3">
                        <span class="fw-bold align-middle " v-html="message">
                        </span>
                    </div>
                    <div class="row w-100 g-1">
                        <div class="col-12 mb-3">
                            <TinyMceEditor v-model="formData.comment" :init="{
                                height: 175,
                                menubar: '',
                                plugins: [],
                                toolbar: [],
                            }" />
                            <div v-if="errors.comment" class="text-danger mt-2">
                                <span v-for="(error, index) in errors.comment" :key="index">{{
                                    error }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <multiselect v-model="formTaggedUsers" :multiple="true"
                                :class="{ 'is-invalid': errors.tagged_users }" :options="users"
                                :placeholder="$t('comment.comment_tagged_users_placeholder')" label="name"
                                track-by="id">
                            </multiselect>
                            <div v-if="errors.tagged_users" class="text-danger mt-2">
                                <span v-for="(error, index) in errors.tagged_users" :key="index">{{
                                    error }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1 align-items-center">
                        <div class="col-6">
                            <button type="button" class="btn btn-desino w-100 border-0" @click="confirm">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal"
                                @click="hideModal(false)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from 'bootstrap';
import TinyMceEditor from '@/components/TinyMceEditor.vue';
import Multiselect from 'vue-multiselect';
import CommentService from '../../../services/CommentService';
import messageService from '../../../services/messageService';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import showToast from '../../../utils/toasts';
import { mapActions } from 'vuex';
export default {
    name: "PreviousActionModalComponent",
    components: {
        TinyMceEditor,
        Multiselect,
        GlobalMessage
    },
    props: {
        title: {
            type: String,
            default: 'Confirm Action',
        },
        message: {
            type: String,
            default: 'Are you sure you want to proceed?',
        },
        users: {
            type: Object,
            default: null,
        },
        ticketData: Object,
    },
    data() {
        return {
            formData: {
                initiative_id: '',
                ticket_id: '',
                type: 2,
                comment: '',
                tagged_users: [],
                comment_type: 'previous_action',
            },
            previousActionFormData: {},
            errors: {},
            showMessage: true,
        };
    },
    computed: {
        formTaggedUsers: {
            get() {
                return this.formData.tagged_users.map(id =>
                    this.users.find(user => user.id === id)
                ).filter(user => user);
            },
            set(value) {
                this.formData.tagged_users = value.map(user => user.id);
            }
        },
    },
    methods: {
        ...mapActions(['setLoading']),
        async confirm() {
            let storedComment = "";
            this.clearMessages();
            try {
                if (this.formData.comment != '') {
                    this.setLoading(true);
                    this.formData.initiative_id = this.ticketData.initiative_id;
                    this.formData.ticket_id = this.ticketData.id;
                    const { content: { comment }, message } = await CommentService.store(this.formData);
                    await this.setLoading(false);
                    // showToast(message, 'success');
                    this.resetForm();
                    storedComment = comment;
                }
                this.$emit('confirm', this.previousActionFormData, storedComment);
                this.hideModal();
            } catch (error) {
                this.handleError(error);
            }
        },
        showModal(previousActionFormData) {
            this.previousActionFormData = previousActionFormData;
            const modal = new Modal(document.getElementById('previousActionModal'), {
                backdrop: 'static',
                keyboard: true,
            });
            modal.show();
        },
        hideModal() {
            const modal = Modal.getInstance(document.getElementById('previousActionModal'));
            modal.hide();
        },
        resetForm() {
            this.formData.comment = '';
            this.formTaggedUsers = [];
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                // messageService.setMessage(error.message, 'danger');
                messageService.setMessage(error.message, 'danger', 'modal');
            }
            this.setLoading(false);
        },
    },
};
</script>