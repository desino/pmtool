<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content mt-3">
        <form>
            <ul class="list-group">
                <li class="list-group-item font-weight-bold bg-desino text-white rounded-top">
                    <div class="row w-100">
                        <div class="col-md-4">
                            {{ $t('bulk_create_tickets.functionalities_text') }}
                        </div>
                        <div class="col-md-4">
                            {{ $t('bulk_create_tickets.hours_text') }}
                        </div>
                        <div class="col-md-4">
                            {{ $t('bulk_create_tickets.clarify_and_estimation_text') }}
                        </div>
                    </div>
                </li>
                <li v-if="sectionsWithFunctionalities.length > 0"
                    v-for="(section, section_index) in sectionsWithFunctionalities" :key="section_index"
                    class="list-group-item">
                    <span class="fw-bold">{{ section.display_name }}</span>
                    <ul class="list-group list-group-flush">
                        <li v-if="section.functionalities.length > 0"
                            v-for="(functionality, index) in section.functionalities" :key="index"
                            class="list-group-item">
                            <div class="row w-100 align-items-center">
                                <div class="col-md-4">
                                    {{ functionality.display_name }}
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control"
                                        :class="{ 'is-invalid': errors[`sections.${section_index}.functionality.${index}.initial_estimation_development_time`] }"
                                        v-model="functionality.initial_estimation_development_time"
                                        :placeholder="$t('bulk_create_tickets.input_initial_estimation_development_time_placeholder')">
                                    <div v-if="errors[`sections.${section_index}.functionality.${index}.initial_estimation_development_time`]"
                                        class="invalid-feedback">
                                        <span
                                            v-for="(error, index) in errors[`sections.${section_index}.functionality.${index}.initial_estimation_development_time`]"
                                            :key="index">
                                            {{ error }} <br>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="functionality.clarify_estimate_checked">
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
                <li v-else class="border border-top-0 list-group-item px-0 py-1 list-group-item-action">
                    <div class="fw-bold fst-italic text-center w-100">{{ $t('bulk_create_tickets.list.not_record_found')
                        }}
                    </div>
                </li>
            </ul>
            <button type="button" @click="showConfirmation('createBulkTickets', createBulkTickets)"
                class="btn btn-desino mt-3 w-100">{{
                    $t('bulk_create_tickets.button_create_bulk_tickets')
                }}</button>
        </form>
        <ConfirmationModal ref="dynamicConfirmationModal" :title="modalTitle" :message="modalMessage"
            @confirm="modalConfirmCallback" />
    </div>
</template>

<script>
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions, mapGetters } from 'vuex';
import messageService from '../../../services/messageService';
import BulkCreateService from '../../../services/BulkCreateService';
import showToast from '../../../utils/toasts';
import eventBus from '../../../eventBus';
import store from '../../../store';

export default {
    name: 'BulkCreateTicketsComponent',
    components: {
        GlobalMessage
    },
    data() {
        return {
            initiative_id: this.$route.params.id,
            initiativeData: {},
            sectionsWithFunctionalities: [],
            modalTitle: '',
            modalMessage: '',
            modalConfirmCallback: null,
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getInitialDataForBulkCreate() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    initiative_id: this.initiative_id,
                }
                const { content: { initiative, sectionsWithFunctionalities } } = await BulkCreateService.getInitialDataForBulkCreate(params);
                this.sectionsWithFunctionalities = sectionsWithFunctionalities;
                this.initiativeData = initiative;
                this.setLoading(false);
                eventBus.$emit('selectHeaderInitiativeId', this.$route.params.id);
                const setHeaderData = {
                    page_title: this.$t('bulk_create_tickets.page_title', { 'INITIATIVE_NAME': this.initiativeData.name }),
                }
                store.commit("setHeaderData", setHeaderData);
            } catch (error) {
                this.handleError(error);
            }
        },
        async createBulkTickets() {
            this.clearMessages();

            const passData = {
                'initiative_id': '',
                'sections': [],
            };
            this.sectionsWithFunctionalities.forEach(section => {
                const sectionData = {
                    'section_id': '',
                    'functionality': [],
                }
                section.functionalities.forEach(functionality => {
                    // if (parseInt(functionality.initial_estimation_development_time) > 0) {
                    let functionalitiesFormData = {
                        functionality_id: functionality.id,
                        functionality_name: functionality.name,
                        // initial_estimation_development_time: parseInt(functionality.initial_estimation_development_time),
                        initial_estimation_development_time: functionality.initial_estimation_development_time,
                        clarify_estimate_checked: functionality.clarify_estimate_checked
                    }
                    sectionData.section_id = section.id
                    sectionData.functionality.push(functionalitiesFormData)
                    // }
                })
                if (sectionData.section_id) {
                    passData.sections.push(sectionData);
                }
            })
            if (passData.sections.length > 0) {
                this.setLoading(true);
                try {
                    passData.initiative_id = this.initiative_id;
                    const { message } = await BulkCreateService.storeNewBulkTickets(passData);
                    showToast(message, 'success');
                    this.setLoading(false);
                    this.getInitialDataForBulkCreate();
                } catch (error) {
                    this.handleError(error);
                }
            } else {
                this.getInitialDataForBulkCreate();
            }
        },
        showConfirmation(modalType, callback) {
            if (modalType === 'createBulkTickets') {
                this.modalTitle = this.$t('bulk_create_tickets.confirm_store_alert.title');
                this.modalMessage = this.$t('bulk_create_tickets.confirm_store_alert.text');
            }

            this.modalConfirmCallback = () => callback();

            this.$refs.dynamicConfirmationModal.showModal();
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
        }
    },
    mounted() {
        this.getInitialDataForBulkCreate();
    },
    beforeRouteUpdate(to, from, next) {
        this.initiative_id = to.params.id;
        this.getInitialDataForBulkCreate();
        next();
    },
}
</script>