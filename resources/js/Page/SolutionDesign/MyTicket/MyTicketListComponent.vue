<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <div class="row g-0 align-items-center">
                <div class="col-12 col-md-3">
                    <div class="w-100 p-1">
                        <input v-model="filter.task_name" :placeholder="$t('my_ticket.filter.task_name')"
                            class="form-control" type="text" @keyup="getMyTickets">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="w-100 p-1">
                        <select v-model="filter.task_type" class="form-select" @change="getMyTickets">
                            <option value="">{{ $t('my_ticket.filter.task_type_placeholder') }}</option>
                            <option v-for="type in filterTaskTypes" :key="type.id" :value="type.id">{{ type.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="w-100 p-1">
                        <div class="form-check ms-auto">
                            <input v-model="filter.is_include_done" @change="getMyTickets" class="form-check-input"
                                type="checkbox" id="is_include_done">
                            <label class="form-check-label fw-bold" for="is_include_done">
                                {{ $t('my_ticket.filter.is_include_done') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                    <div class="row g-1 align-items-center">
                        <div class="col-xl-8 col-lg-7 col-md-9 col-12">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-auto me-1" style="width:10px"></div>
                                <div class="col-auto" style="width: calc(100% - 40px)">
                                    {{ $t('my_ticket.list.column_ticket_name') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 d-none d-lg-block">
                            {{ $t('my_ticket.list.column_macro_status') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block">
                            {{ $t('my_ticket.list.column_current_action') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block fw-bold text-lg-end"></div>
                    </div>
                </li>
                <li v-for="(ticket, index) in tickets" v-if="tickets.length > 0" :key="ticket.id"
                    class="list-group-item p-1 list-group-item-action" role="button"
                    @click="redirectTaskDetailPage(ticket)">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-xl-8 col-lg-7 col-md-12 col-12">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-auto me-1" style="width:10px">
                                    <div class="position-absolute" :class="{
                                        'bg-secondary': !ticket.is_visible,
                                        'bg-warning': ticket.is_priority && ticket.is_visible,
                                        '': ticket.is_visible && !ticket.is_priority
                                    }" style="width: 10px; height: 100%; left: 0; top: 0;">
                                    </div>
                                </div>
                                <div class="col-auto" style="width: calc(100% - 40px)">
                                    <div class="w-100" data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" :title="tooltipContentForTicketName(ticket)">
                                        {{ ticket.composed_name }}
                                    </div>
                                    <div class="w-100 fst-italic text-secondary small" v-if="ticket?.functionality?.display_name">
                                        ({{ ticket?.functionality?.display_name }})
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-5 col-6 text-center py-2 py-lg-0">
                            <span class="badge p-2 w-100 text-wrap" :class="ticket.macro_status_label?.color">
                                {{ ticket.macro_status_label?.label }}
                            </span>
                        </div>
                        <div class="col-lg-1 col-md-5 col-5 py-2 py-lg-0">
                            {{ ticket?.current_action?.action_name }}
                        </div>
                        <div class="col-lg-1 col-md-2 col-1 py-2 py-lg-0 text-end">
                            <div class="dropdown" @click.stop="">
                                <button class="btn btn-secondary border-0 btn-sm dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu shadow border-0 p-2">
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="copyToClipboard(ticket)">
                                            {{ $t('ticket.list.column.action.copy_text') }}
                                        </a>
                                    </li>
                                    <li class="small pb-1" v-if="ticket.asana_task_link">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" :href="ticket.asana_task_link" target="_blank" >
                                            {{ $t('ticket.list.column.action.asana_text') }}
                                        </a>
                                    </li>
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-warning w-100 small" href="javascript:" @click="handleTimeBooking(ticket)" >
                                            {{ $t('ticket_details.time_booking') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('my_ticket.list.not_ticket') }}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="getMyTickets" />

        <div id="timeBookingForTicketDetailModal" aria-hidden="true" aria-labelledby="timeBookingForTicketDetailLabel"
            class="modal fade" tabindex="-1">
            <TimeBookingForTicketDetailComponent ref="timeBookingForTicketDetailComponent" />
        </div>
        <span id="copyableLink" style="cursor: pointer; text-decoration: underline; color: blue; display: none">
            <a v-bind:href="copyLink">{{ copyLabel }}</a>
        </span>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import { mapActions, mapGetters } from 'vuex';
import showToast from '../../../utils/toasts';
import { Modal, Tooltip } from 'bootstrap';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import messageService from '../../../services/messageService';
import MyTicketService from '../../../services/MyTicketService';
import PaginationComponent from '../../../components/PaginationComponent.vue';
import TimeBookingForTicketDetailComponent from '../Ticket/TimeBookingForTicketDetailComponent.vue';
import eventBus from '../../../eventBus';
import store from '../../../store';
export default {
    name: 'MyTicketListComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        PaginationComponent,
        TimeBookingForTicketDetailComponent
    },
    data() {
        return {
            initiative_id: this.$route.params.id,
            filterTaskTypes: [],
            filter: {
                task_name: "",
                task_type: "",
                is_include_done: false
            },
            tickets: [],
            currentPage: "",
            totalPages: "",
            copyLabel: "",
            copyLink: "",
            initiativeData: {},
            errors: {},
            showMessage: true
        }
    },
    computed: {
        ...mapGetters(['user', 'passedData']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchData() {
            try {
                this.setLoading(true);
                await Promise.all([
                    this.getMyTickets(),
                    eventBus.$emit('selectHeaderInitiativeId', this.initiative_id),
                ]);
                this.setLoading(false);
                this.clearMessages();
            } catch (error) {
                this.handleError(error);
            }
        },
        async getMyTickets(page = 1) {
            this.clearMessages();
            try {
                await this.setLoading(true);
                const params = {
                    page: page,
                    initiative_id: this.initiative_id,
                    filters: this.filter
                }
                const { content, meta_data } = await MyTicketService.getMyTickets(params);
                this.tickets = content;
                this.currentPage = content.current_page;
                this.totalPages = content.last_page;
                this.filterTaskTypes = meta_data.task_type;
                this.initiativeData = meta_data.initiative;
                await this.setLoading(false);
                this.initializeTooltips();
                this.setPageHeader();
            } catch (error) {
                this.handleError(error);
            }
        },
        handleTimeBooking(ticket) {
            const modalElement = document.getElementById('timeBookingForTicketDetailModal');
            if (modalElement) {
                this.$refs.timeBookingForTicketDetailComponent.getTimeBookingForTicketDetailData(ticket);
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        redirectTaskDetailPage(ticket) {
            const ticketDetailRoute = this.$router.resolve({ name: 'task.detail', params: { initiative_id: this.initiative_id, ticket_id: ticket.id } });
            window.open(ticketDetailRoute.href, '_blank');
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        tooltipContentForTicketName(ticket) {
            const createdAtLabel = this.$t('ticket.list.row_hover_tooltip_created_at_text');
            const createdByLabel = this.$t('ticket.list.row_hover_tooltip_created_by_text');
            const commentLabel = this.$t('ticket.list.row_hover_tooltip_comment_text');
            const commentedAtLabel = this.$t('ticket.list.row_hover_tooltip_commented_at_text');

            let commentHtml = "";
            if (ticket?.latest_comment?.comment != null) {
                const comment = ticket?.latest_comment?.comment;
                const userName = ticket?.latest_comment?.created_updated_user_name;
                const createdAt = ticket?.latest_comment?.display_updated_at ?? ticket?.latest_comment?.display_created_at;
                commentHtml = `<strong class='small'>${commentLabel}</strong> <span class="badge bg-secondary d-block-inline text-wrap fst-italic"> ${userName}</span> : <span class="fst-italic">${comment}</span> <strong class='small fst-italic'>${commentedAtLabel}</strong> <span class="badge bg-secondary d-block-inline text-wrap fst-italic">${createdAt}</span>`;
            }
            return `<div class='row g-1 align-items-center small'>
                        <div class='col-12 text-start'>
                            <strong class='small'>${createdByLabel}</strong> <span>${ticket.display_created_by}</span> <strong class='small'>${createdAtLabel}</strong> <span>${ticket.display_created_at}</span>
                        </div>
                        <div class='col-12 text-start'>
                            ${commentHtml}
                        </div>
                    </div>`;
        },
        copyToClipboard(ticket) {
            this.copyLink = `${window.location.origin}/solution-design/${this.initiative_id}/ticket-detail/${ticket.id}`;
            this.copyLabel = ticket.composed_name;

            this.$nextTick(() => {
                const linkElement = document.getElementById('copyableLink');
                if (linkElement) {
                    linkElement.style.display = 'inline';

                    const range = document.createRange();
                    range.selectNodeContents(linkElement);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);

                    try {
                        const successful = document.execCommand('copy');
                        if (successful) {
                            showToast(this.$t('ticket.link_copied_to_clipboard'), 'success');
                        } else {
                            showToast(this.$t('ticket.failed_to_copy_link'), 'danger');
                        }
                    } catch (error) {
                        showToast(this.$t('ticket.failed_to_copy_link'), 'danger');
                    }
                    linkElement.style.display = 'none';
                }
            });
        },
        setPageHeader() {
            const setHeaderData = {
                page_title: this.$t('my_ticket.page_title') + ' - ' + this.initiativeData?.name,
            }
            store.commit("setHeaderData", setHeaderData);
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
        this.fetchData();
    },
    beforeRouteUpdate(to, from, next) {
        this.initiative_id = to.params.id;
        this.fetchData();
        next();
    },
}
</script>
