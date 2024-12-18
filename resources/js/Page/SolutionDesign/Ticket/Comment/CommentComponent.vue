<template>
    <div>
        <div class="card">
            <div class="card-header border-0 fw-bold bg-desino text-white small">
                {{ $t('comment.comment_title') }}
            </div>
            <div class="card-body max-h-ticket-comment px-0">
                <div class="w-100 mb-3">
                    <div class="w-100 text-center">
                        <button v-if="hasMore" @click="getComments()" class="btn btn-info text-white btn-sm border-0">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </div>
                    <div class="w-100">
                        <ul class="list-group list-group-flush mb-3 mt-2">
                            <li v-if="comments.length > 0" v-for="(comment, index) in comments" :key="index"
                                class="border list-group-item p-1 list-group-item-action border-top-0 border-start-0 border-end-0">
                                <div class="row g-1 w-100 align-items-center"
                                    :class="{ 'bg-warning-subtle': checkTagUser(comment) }">
                                    <div class="col-12">
                                        <div class="row g-1 w-100 align-items-center">
                                            <div class="col-10">
                                                <div class="badge bg-secondary text-white" style="font-size: 0.5rem;">
                                                    {{ comment.display_updated_at ?? comment.display_created_at }}
                                                </div>
                                                <div class="fw-bold text-primary">
                                                    {{ comment.updated_user_name ?? comment.created_user_name }}
                                                </div>
                                            </div>
                                            <div class="col-2 text-end" v-if="comment.user_id == user.id">
                                                <a href="javascript:" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom"
                                                    :title="$t('comment.edit_comment_but_text')" role="button"
                                                    class="btn-link me-2" @click.stop="editComment(comment)">
                                                    <i class="bi bi-pencil text-primary"></i>
                                                </a>
                                                <a href="javascript:" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom"
                                                    :title="$t('comment.delete_comment_but_text')"
                                                    @click.stop="showConfirmation('deleteComment', deleteComment, comment)"
                                                    role="button" class="btn-link">
                                                    <i class="bi bi-x-lg text-danger"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 optimize-image img">
                                        <p v-if="!comment.is_edit_comment" v-html="comment.comment">
                                        </p>
                                    </div>
                                    <div class="col-12" v-if="comment.is_edit_comment">
                                        <div class="row g-1 w-100">
                                            <div class="col-12">
                                                <TinyMceEditor v-model="editForm.comment" :init="{
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
                                            <div class="col-12 mb-3">
                                                <multiselect :multiple="true" v-model="editTaggedUsers"
                                                    :class="{ 'is-invalid': errors.tagged_users }" :options="users"
                                                    :placeholder="$t('comment.comment_tagged_users_placeholder')"
                                                    label="name" track-by="id">
                                                </multiselect>
                                                <div v-if="errors.tagged_users" class="text-danger mt-2">
                                                    <span v-for="(error, index) in errors.tagged_users" :key="index">{{
                                                        error }}</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" @click="updateComment"
                                                    class="btn btn-desino w-100 border-0">{{
                                                        $t('comment.comment_update_but_text') }}</button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-danger w-100 border-0"
                                                    @click="closeEditForm(comment)">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="row g-1 w-100">
                    <div class="col-12">
                        <TinyMceEditor v-model="formData.comment" :init="{
                            height: 175,
                            menubar: '',
                            plugins: [],
                            toolbar: [],
                        }" />
                        <div v-if="errors.comment" class="text-danger mt-2">
                            <span v-for="(error, index) in errors.comment" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <multiselect :multiple="true" v-model="formTaggedUsers"
                            :class="{ 'is-invalid': errors.tagged_users }" :options="users"
                            :placeholder="$t('comment.comment_tagged_users_placeholder')" label="name" track-by="id">
                        </multiselect>
                        <div v-if="errors.tagged_users" class="text-danger mt-2">
                            <span v-for="(error, index) in errors.tagged_users" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <button type="button" @click="saveComment" class="btn btn-desino w-100 border-0">{{
                            $t('comment.comment_save_but_text') }}</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-danger w-100 border-0" @click="resetForm">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmationModal ref="dynamicConfirmationModal" :modalId="'dynamicConfirmationForCommentModal'"
            :title="modalTitle" :message="modalMessage" @confirm="modalConfirmCallback" />
    </div>
</template>

<script>
import TinyMceEditor from '@/components/TinyMceEditor.vue';
import Multiselect from 'vue-multiselect';
import messageService from '../../../../services/messageService';
import CommentService from '../../../../services/CommentService';
import { mapActions, mapGetters } from 'vuex';
import showToast from '../../../../utils/toasts';
import { Modal, Tooltip } from 'bootstrap';

export default {
    name: "CommentComponent",
    components: {
        TinyMceEditor,
        Multiselect,
    },
    props: {
        ticketData: Object,
        users: Object
    },
    data() {
        return {
            formData: {
                initiative_id: '',
                ticket_id: '',
                type: 1,
                comment: '',
                tagged_users: [],
            },
            editForm: {
                id: '',
                initiative_id: '',
                ticket_id: '',
                type: 1,
                comment: '',
                tagged_users: [],
            },
            comments: [],
            firstComment: {},
            latestComment: {},
            modalTitle: "",
            modalMessage: "",
            modalConfirmCallback: null,
            hasMore: true,
            errors: {},
        };
    },
    computed: {
        ...mapGetters(['user']),
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
        editTaggedUsers: {
            get() {
                return this.editForm.tagged_users.map(id =>
                    this.users.find(user => user.id === id)
                ).filter(user => user);
            },
            set(value) {
                this.editForm.tagged_users = value.map(user => user.id);
            }
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getComments() {
            try {
                this.setLoading(true);
                const passData = {
                    first_comment_id: this.firstComment?.id,
                };
                const { content } = await CommentService.index(this.ticketData.initiative_id, this.ticketData.id, passData);
                this.comments = [...content, ...this.comments].sort((a, b) => a.id - b.id);
                this.comments = this.comments.map(comment => ({
                    ...comment,
                    is_edit_comment: false
                }));
                this.firstComment = this.comments.length > 0 ? this.comments[0] : 0;
                this.hasMore = content.length > 0;
                await this.setLoading(false);
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        async saveComment() {
            this.clearMessages();
            try {
                this.setLoading(true);
                this.formData.initiative_id = this.ticketData.initiative_id;
                this.formData.ticket_id = this.ticketData.id;
                const { content: { comment }, message } = await CommentService.store(this.formData);
                this.latestComment = comment;
                this.comments = [comment, ...this.comments.filter(c => c.id !== comment.id)].sort((a, b) => a.id - b.id);
                showToast(message, 'success');
                await this.setLoading(false);
                this.resetForm();
            } catch (error) {
                this.handleError(error);
            }
        },
        checkTagUser(comment) {
            if (this.user?.id == comment.user_id) {
                const valuesArray = comment.tagged_users.split(',').map(str => parseInt(str.trim()));
                const valueExists = valuesArray.includes(this.user?.id);
                return valueExists;
            }
            return false;
        },
        async deleteComment(comment) {
            try {
                this.clearMessages();
                this.setLoading(true);
                const passData = {
                    id: comment?.id,
                };
                const { content } = await CommentService.delete(this.ticketData.initiative_id, this.ticketData.id, passData);
                await this.setLoading(false);
                this.comments = this.comments.filter(item => item.id !== comment?.id);
            } catch (error) {
                this.handleError(error);
            }
        },
        editComment(comment) {
            this.comments.forEach(c => c.is_edit_comment = false);
            this.editFormReset();
            comment.is_edit_comment = true;
            let taggedUserIds = [];
            if (comment?.tagged_users != '') {
                taggedUserIds = comment.tagged_users.split(',').map(str => parseInt(str.trim()));
            }
            this.editForm = {
                id: comment.id,
                initiative_id: this.ticketData.initiative_id,
                ticket_id: this.ticketData.id,
                type: 1,
                comment: comment.comment,
                tagged_users: taggedUserIds,
            };
        },
        showConfirmation(modalType, callback, callbackParam, index = null, event = null) {
            if (modalType === 'deleteComment') {
                this.modalTitle = this.$t('comment.delete.conformation_popup_title');
                this.modalMessage = this.$t('comment.delete.conformation_popup_text');
            }

            this.modalConfirmCallback = () => callback(callbackParam, modalType, index, event);

            this.$refs.dynamicConfirmationModal.showModal();
        },
        closeEditForm(comment) {
            this.editFormReset();
            comment.is_edit_comment = false;
        },
        resetForm() {
            this.formData.comment = '';
            this.formTaggedUsers = [];
        },
        async updateComment() {
            try {
                this.setLoading(true);
                const { content: { comment }, message } = await CommentService.update(this.editForm.initiative_id, this.editForm.ticket_id, this.editForm);
                this.comments = this.comments.map(c => c.id === comment.id ? comment : c);
                showToast(message, 'success');
                await this.setLoading(false);
                this.resetForm();
            } catch (error) {
                this.handleError(error);
            }
        },
        appendPreviousActionComment(comment) {
            this.comments = [comment, ...this.comments.filter(c => c.id !== comment.id)].sort((a, b) => a.id - b.id);
        },
        editFormReset() {
            this.editForm.comment = '';
            this.editTaggedUsers = [];
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger');
            }
            this.setLoading(false);
        },
    },
    mounted() {
        this.clearMessages();
        this.getComments();
    }
};
</script>
