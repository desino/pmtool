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
            <ul class="list-group list-group-flush mb-3 mt-2">
                <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                    <div class="row g-1 align-items-center">
                        <div class="col-lg-5 col-md-9 col-12 fw-bold small">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-auto me-1" style="width:10px"></div>
                                <div class="col-auto" style="width: calc(100% - 40px)">
                                    {{ $t('my_ticket.list.column_ticket_name') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 d-none d-md-block fw-bold small">
                            {{ $t('my_ticket.list.column_macro_status') }}
                        </div>
                        <div class="col-lg-3 d-none d-lg-block fw-bold small">
                            {{ $t('my_ticket.list.column_related_functionality') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block fw-bold small">
                            {{ $t('my_ticket.list.column_current_action') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block fw-bold text-lg-end small">
                            {{ $t('my_ticket.list.column_actions') }}
                        </div>
                    </div>
                </li>
                <li v-for="(ticket, index) in tickets" v-if="tickets.length > 0" :key="ticket.id"
                    class="list-group-item p-1 list-group-item-action" role="button"
                    @click="redirectTaskDetailPage(ticket)">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-lg-5 col-md-9 col-12">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-auto me-1" style="width:10px">
                                    <div class="position-absolute" :class="{
                                        'bg-secondary': !ticket.is_visible,
                                        'bg-warning': ticket.is_priority && ticket.is_visible,
                                        '': ticket.is_visible && !ticket.is_priority
                                    }" style="width: 10px; height: 100%; left: 0; top: 0;">
                                    </div>
                                </div>
                                <div class="col-auto" style="width: calc(100% - 40px)" data-bs-toggle="tooltip"
                                    data-bs-html="true" data-bs-placement="bottom"
                                    :title="tooltipContentForTicketName(ticket)">
                                    {{ ticket.composed_name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 offset-md-0 col-md-3 offset-1 col-5 text-center py-2 py-lg-0">
                            <span class="badge p-2 w-100 text-wrap" :class="ticket.macro_status_label?.color">
                                {{ ticket.macro_status_label?.label }}
                            </span>
                        </div>
                        <div class="offset-0 offset-md-1 offset-lg-0 col-lg-3 col-md-4 col-6 py-2 py-lg-0">
                            {{ ticket?.functionality?.display_name }}
                        </div>
                        <div class="offset-1 offset-md-0 col-lg-1 col-md-4 col-6 py-2 py-lg-0">
                            {{ ticket?.current_action?.action_name }}
                        </div>
                        <div class="col-lg-1 col-md-2 col-5 py-2 py-lg-0 text-end">
                            <a data-bs-toggle="tooltip" data-bs-placement="bottom"
                                :title="$t('ticket.list.column.action.copy_text')" class="text-primary me-1"
                                href="javascript:" @click.stop="copyToClipboard(ticket)">
                                <i class="bi bi-share"></i>
                            </a>
                            <a v-if="ticket.asana_task_link" class="me-1" @click.stop :href="ticket.asana_task_link"
                                target="_blank" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                :title="$t('ticket.list.column.action.asana_text')">
                                <svg fill="none" height="21px" viewBox="0 0 24 24" width="21px"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path clip-rule="evenodd"
                                        d="M10.4693 3.55448C10.9546 3.35346 11.4747 3.25 12 3.25C12.5253 3.25 13.0454 3.35346 13.5307 3.55448C14.016 3.7555 14.457 4.05014 14.8284 4.42157C15.1999 4.79301 15.4945 5.23397 15.6955 5.71927C15.8965 6.20457 16 6.72471 16 7.25C16 7.77529 15.8965 8.29543 15.6955 8.78073C15.4945 9.26603 15.1999 9.70699 14.8284 10.0784C14.457 10.4499 14.016 10.7445 13.5307 10.9455C13.0454 11.1465 12.5253 11.25 12 11.25C11.4747 11.25 10.9546 11.1465 10.4693 10.9455C9.98396 10.7445 9.54301 10.4499 9.17157 10.0784C8.80014 9.70699 8.5055 9.26604 8.30448 8.78073C8.10346 8.29543 8 7.77529 8 7.25C8 6.72471 8.10346 6.20457 8.30448 5.71927C8.5055 5.23396 8.80014 4.79301 9.17157 4.42157C9.54301 4.05014 9.98396 3.7555 10.4693 3.55448ZM12 4.75C11.6717 4.75 11.3466 4.81466 11.0433 4.9403C10.74 5.06594 10.4644 5.25009 10.2322 5.48223C10.0001 5.71438 9.81594 5.98998 9.6903 6.29329C9.56466 6.59661 9.5 6.92169 9.5 7.25C9.5 7.5783 9.56466 7.90339 9.6903 8.20671C9.81594 8.51002 10.0001 8.78562 10.2322 9.01777C10.4644 9.24991 10.74 9.43406 11.0433 9.5597C11.3466 9.68534 11.6717 9.75 12 9.75C12.3283 9.75 12.6534 9.68534 12.9567 9.5597C13.26 9.43406 13.5356 9.24991 13.7678 9.01777C13.9999 8.78562 14.1841 8.51002 14.3097 8.20671C14.4353 7.90339 14.5 7.5783 14.5 7.25C14.5 6.9217 14.4353 6.59661 14.3097 6.29329C14.1841 5.98998 13.9999 5.71438 13.7678 5.48223C13.5356 5.25009 13.26 5.06594 12.9567 4.9403C12.6534 4.81466 12.3283 4.75 12 4.75Z"
                                        fill="#ffc107" fill-rule="evenodd" />
                                    <path clip-rule="evenodd"
                                        d="M5.46927 12.5545C5.95457 12.3535 6.47471 12.25 7 12.25C7.52529 12.25 8.04543 12.3535 8.53073 12.5545C9.01604 12.7555 9.45699 13.0501 9.82843 13.4216C10.1999 13.793 10.4945 14.234 10.6955 14.7193C10.8965 15.2046 11 15.7247 11 16.25C11 16.7753 10.8965 17.2954 10.6955 17.7807C10.4945 18.266 10.1999 18.707 9.82843 19.0784C9.45699 19.4499 9.01604 19.7445 8.53073 19.9455C8.04543 20.1465 7.52529 20.25 7 20.25C6.47471 20.25 5.95457 20.1465 5.46927 19.9455C4.98396 19.7445 4.54301 19.4499 4.17157 19.0784C3.80014 18.707 3.5055 18.266 3.30448 17.7807C3.10346 17.2954 3 16.7753 3 16.25C3 15.7247 3.10346 15.2046 3.30448 14.7193C3.5055 14.234 3.80014 13.793 4.17157 13.4216C4.54301 13.0501 4.98396 12.7555 5.46927 12.5545ZM7 13.75C6.67169 13.75 6.34661 13.8147 6.04329 13.9403C5.73998 14.0659 5.46438 14.2501 5.23223 14.4822C5.00009 14.7144 4.81594 14.99 4.6903 15.2933C4.56466 15.5966 4.5 15.9217 4.5 16.25C4.5 16.5783 4.56466 16.9034 4.6903 17.2067C4.81594 17.51 5.00009 17.7856 5.23223 18.0178C5.46438 18.2499 5.73998 18.4341 6.04329 18.5597C6.34661 18.6853 6.67169 18.75 7 18.75C7.3283 18.75 7.65339 18.6853 7.95671 18.5597C8.26002 18.4341 8.53562 18.2499 8.76777 18.0178C8.99991 17.7856 9.18406 17.51 9.3097 17.2067C9.43534 16.9034 9.5 16.5783 9.5 16.25C9.5 15.9217 9.43534 15.5966 9.3097 15.2933C9.18406 14.99 8.99991 14.7144 8.76777 14.4822C8.53562 14.2501 8.26002 14.0659 7.95671 13.9403C7.65339 13.8147 7.3283 13.75 7 13.75Z"
                                        fill="#ffc107" fill-rule="evenodd" />
                                    <path clip-rule="evenodd"
                                        d="M17 12.25C16.4747 12.25 15.9546 12.3535 15.4693 12.5545C14.984 12.7555 14.543 13.0501 14.1716 13.4216C13.8001 13.793 13.5055 14.234 13.3045 14.7193C13.1035 15.2046 13 15.7247 13 16.25C13 16.7753 13.1035 17.2954 13.3045 17.7807C13.5055 18.266 13.8001 18.707 14.1716 19.0784C14.543 19.4499 14.984 19.7445 15.4693 19.9455C15.9546 20.1465 16.4747 20.25 17 20.25C17.5253 20.25 18.0454 20.1465 18.5307 19.9455C19.016 19.7445 19.457 19.4499 19.8284 19.0784C20.1999 18.707 20.4945 18.266 20.6955 17.7807C20.8965 17.2954 21 16.7753 21 16.25C21 15.7247 20.8965 15.2046 20.6955 14.7193C20.4945 14.234 20.1999 13.793 19.8284 13.4216C19.457 13.0501 19.016 12.7555 18.5307 12.5545C18.0454 12.3535 17.5253 12.25 17 12.25ZM16.0433 13.9403C16.3466 13.8147 16.6717 13.75 17 13.75C17.3283 13.75 17.6534 13.8147 17.9567 13.9403C18.26 14.0659 18.5356 14.2501 18.7678 14.4822C18.9999 14.7144 19.1841 14.99 19.3097 15.2933C19.4353 15.5966 19.5 15.9217 19.5 16.25C19.5 16.5783 19.4353 16.9034 19.3097 17.2067C19.1841 17.51 18.9999 17.7856 18.7678 18.0178C18.5356 18.2499 18.26 18.4341 17.9567 18.5597C17.6534 18.6853 17.3283 18.75 17 18.75C16.6717 18.75 16.3466 18.6853 16.0433 18.5597C15.74 18.4341 15.4644 18.2499 15.2322 18.0178C15.0001 17.7856 14.8159 17.51 14.6903 17.2067C14.5647 16.9034 14.5 16.5783 14.5 16.25C14.5 15.9217 14.5647 15.5966 14.6903 15.2933C14.8159 14.99 15.0001 14.7144 15.2322 14.4822C15.4644 14.2501 15.74 14.0659 16.0433 13.9403Z"
                                        fill="#ffc107" fill-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="javascript:" @click.stop="handleTimeBooking(ticket)" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" :title="$t('ticket_details.time_booking')">
                                <i class="bi bi-clock-history"></i>
                            </a>
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
