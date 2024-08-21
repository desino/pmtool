<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('ticket.page_title') }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a class="text-decoration-none" href="javascript:void(0)">{{
                            $t('Dashboard') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="row mb-3">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <input v-model="filter.task_name" :placeholder="$t('ticket.filter.task_name')" class="form-control"
                    type="text" @keyup="fetchAllTasks">
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <select id="client_id" v-model="filter.task_type" class="form-select" @change="fetchAllTasks">
                    <option value="">{{ $t('ticket.filter.task_type_placeholder') }}</option>
                    <option v-for="(name, id) in filterTaskTypes" :key="id" :value="id">{{ name }}
                    </option>
                </select>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <multiselect v-model="filter.functionalities" :multiple="true" :options="functionalities"
                    :searchable="true" deselect-label="" label="display_name" placeholder="Functionality" track-by="id"
                    @select="fetchAllTasks">
                </multiselect>
            </div>
        </div>
        <div class="list-group-item mx-2 mb-3 mt-2">
            <div class="row justify-content-between font-weight-bold bg-desino text-white rounded-top">
                <div class="col-lg-4 col-md-6 col-6 fw-bold py-2">{{ $t('ticket.list.column_task_name') }}
                </div>
                <div class="col-lg-3 col-md-6 col-6 fw-bold py-2">{{ $t('ticket.list.column_task_type') }}
                </div>
                <div class="col-lg-3 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                    {{ $t('ticket.list.column_task_created_at') }}
                </div>
                <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                    {{ $t('ticket.list.column_action') }}
                </div>
            </div>
            <div v-for="task in tasks" v-if="tasks.length > 0" :key="task.id">
                <div class="row border-desino border p-2">
                    <div class="col-lg-4 col-md-6 col-6">{{ task.name }}</div>
                    <div class="col-lg-3 col-md-6 col-6">{{ task.type_label }}</div>
                    <div class="col-lg-3 col-md-6 col-8 text-center text-lg-start">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 p-0 text-white text-center rounded-top">
                            {{ $t('ticket.list.column_task_created_at') }} </span>
                        {{ task.created_at }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-4">
                        <span class="d-block d-lg-none fw-bold bg-light-subtle mt-2 text-white text-center">
                            {{ $t('ticket.list.column_action') }}</span>
                        <router-link
                            :to="{ name: 'task.detail', params: { initiative_id: this.initiative_id, ticket_id: task.id } }"
                            class="text-success me-2">
                            <i class="bi bi-box-arrow-up-right fw-bold"></i>
                        </router-link>

                    </div>
                </div>
            </div>
            <div v-else class="list-group-item row border p-4">
                <div class="col h4 fw-bold text-center">{{ $t('ticket.list.not_ticket') }}
                </div>
            </div>
        </div>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="fetchAllTasks" />
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import PaginationComponent from '../../../components/PaginationComponent.vue';
import messageService from '../../../services/messageService';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions } from 'vuex';
import ticketService from "../../../services/TicketService";
import Multiselect from "vue-multiselect";

export default {
    name: 'TicketListComponent',
    mixins: [globalMixin],
    components: {
        Multiselect,
        GlobalMessage,
        PaginationComponent
    },
    props: ['id'],
    data() {
        return {
            initiative_id: this.$route.params.id,
            tasks: [],
            currentPage: "",
            totalPages: "",
            errors: {},
            filterTaskTypes: [],
            functionalities: [],
            filter: {
                task_name: "",
                task_type: "",
                functionalities: "",
            },
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchAllTasks(page = 1) {
            this.clearMessages();
            try {
                const params = {
                    page: page,
                    filters: this.filter
                }
                await this.setLoading(true);
                const response = await ticketService.fetchAllTickets(this.initiative_id, params);
                this.tasks = response.content;
                this.filterTaskTypes = response.meta_data.task_type;
                this.functionalities = response.meta_data.functionalities;
                await this.setLoading(false);
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
    },
    mounted() {
        this.fetchAllTasks();
    },
    beforeUnmount() {
        // Hide the message when the component is unmounted
        this.showMessage = false;
    }
}
</script>
