<template>
    <div>
        <div class="card">
            <div class="card-header border-0 fw-bold bg-desino text-white small">
                {{ $t('comment.comment_title') }}
            </div>
            <div class="card-body max-h-ticket-comment">
                <div class="w-100 mb-3">
                    <button v-if="hasMore" @click="getComments(previousPage)" class="btn btn-primary btn-sm">
                        Load Previous
                    </button>
                    <ul class="list-group list-group-flush mb-3 mt-2">
                        <li v-if="comments.length > 0" v-for="(comment, index) in comments" :key="index"
                            class="border list-group-item p-1 list-group-item-action ">
                            <div class="row g-1 w-100 align-items-center" style="min-height: 48px;">
                                <div v-html="comment.comment"></div>
                                {{ comment.id }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-footer">
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
                    <div class="col-12">
                        <multiselect :multiple="true" v-model="formData.tagged_users"
                            :class="{ 'is-invalid': errors.tagged_users }" :options="users"
                            :placeholder="$t('comment.comment_tagged_users_placeholder')" label="name" track-by="id">
                        </multiselect>
                        <div v-if="errors.tagged_users" class="text-danger mt-2">
                            <span v-for="(error, index) in errors.tagged_users" :key="index">{{ error }}</span>
                        </div>
                    </div>
                </div>
                <div class="row g-1 w-100 mt-2">
                    <div class="col-6">
                        <button type="button" @click="saveComment" class="btn btn-desino w-100 border-0">{{
                            $t('comment.comment_save_but_text') }}</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TinyMceEditor from '@/components/TinyMceEditor.vue';
import Multiselect from 'vue-multiselect';
import messageService from '../../../../services/messageService';
import CommentService from '../../../../services/CommentService';
import { mapActions } from 'vuex';
import showToast from '../../../../utils/toasts';

export default {
    name: "CommentComponent",
    components: {
        TinyMceEditor,
        Multiselect
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
            comments: [],
            latestComment: {},
            previousPage: 0,
            hasMore: true,
            errors: {},
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async getComments(page = 1) {
            try {
                this.setLoading(true);
                const passData = {
                    page: page,
                };
                const { content: { data, current_page, last_page } } = await CommentService.index(this.ticketData.initiative_id, this.ticketData.id, passData);
                this.comments = [...data, ...this.comments].sort((a, b) => a.id - b.id);
                this.previousPage = current_page + 1;
                this.hasMore = page < last_page;
                this.setLoading(false);
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
                // this.comments = [...this.comments, ...data];
                // this.comments.unshift(comment);
                this.comments.push(comment);
                showToast(message, 'success');
                await this.setLoading(false);
                this.resetForm();
                console.log('this.comments :: ', this.comments);
            } catch (error) {
                this.handleError(error);
            }
        },
        resetForm() {
            this.formData.comment = '';
            this.formData.tagged_users = [];
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
