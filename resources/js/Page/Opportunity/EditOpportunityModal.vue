<template>
    <div class="modal-dialog modal-xl">
        <form @submit.prevent="updateOpportunity">
            <div class="modal-content border-0">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title" id="editInitiativeModalLabel">{{ modalTitle }}</h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <div class="row w-100">
                    </div>

                    <!-- <div class="row g-1 w-100 align-items-center"> -->
                    <div class="row g-1 w-100">
                        <div class="col-12 col-md-6 col-lg-6 mb-3">
                            <label class="form-label fw-bold">{{
                                $t('edit_opportunity_modal_select_client_name')
                                }} <strong class="text-danger">*</strong></label>
                            <input type="text" v-model="formData.client_name" disabled
                                :class="{ 'is-invalid': errors.client_name }" class="form-control">
                            <div v-if="errors.client_name" class="invalid-feedback">
                                <span v-for="(error, index) in errors.client_name" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6 mb-3">
                            <label class="form-label fw-bold">{{ $t('edit_opportunity_modal_input_name') }}
                                <strong class="text-danger">*</strong></label>
                            <input type="text" v-model="formData.name" :class="{ 'is-invalid': errors.name }"
                                class="form-control">
                            <div v-if="errors.name" class="invalid-feedback">
                                <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6 mb-3">
                            <label class="form-label fw-bold">{{
                                $t('edit_opportunity_modal_input_ballpark_development_hours') }} <strong
                                    class="text-danger">*</strong></label>
                            <input type="text" v-model="formData.ballpark_development_hours"
                                :class="{ 'is-invalid': errors.ballpark_development_hours }" class="form-control">
                            <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                                <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">
                                    {{ error }} <br>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-6 mb-3">
                            <label class="form-label fw-bold">{{
                                $t('edit_opportunity_modal_select_pdf_template_text') }}</label>
                            <select v-model="formData.template_id" :class="{ 'is-invalid': errors.template_id }"
                                class="form-select">
                                <option value="">{{
                                    $t('edit_opportunity_modal_select_template_placeholder') }}</option>
                                <option v-for="template in pdfTemplates" :key="template.id" :value="template.id">
                                    {{ template.name }}
                                </option>
                            </select>
                            <div v-if="errors.template_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.template_id" :key="index">
                                    {{ error }} <br>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label fw-bold">
                                {{ $t('create_initiative_modal_select_functional_owner') }}
                            </label>
                            <select v-model="formData.functional_owner_id"
                                :class="{ 'is-invalid': errors.functional_owner_id }" class="form-select">
                                <option value="">{{
                                    $t('create_initiative_modal_select_functional_owner_placeholder') }}</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{
                                    user.name }}
                                </option>
                            </select>
                            <div v-if="errors.functional_owner_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.functional_owner_id" :key="index">{{ error
                                    }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label fw-bold">
                                {{ $t('create_initiative_modal_select_technical_owner') }}
                            </label>
                            <select v-model="formData.technical_owner_id"
                                :class="{ 'is-invalid': errors.technical_owner_id }" class="form-select">
                                <option value="">{{
                                    $t('create_initiative_modal_select_technical_owner_placeholder') }}</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{
                                    user.name }}
                                </option>
                            </select>
                            <div v-if="errors.technical_owner_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.technical_owner_id" :key="index">{{ error
                                    }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 mb-3">
                            <label class="form-label fw-bold">
                                {{ $t('create_initiative_modal_select_quality_owner') }}
                            </label>
                            <select v-model="formData.quality_owner_id"
                                :class="{ 'is-invalid': errors.quality_owner_id }" class="form-select">
                                <option value="">{{
                                    $t('create_initiative_modal_select_quality_owner_placeholder') }}</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{
                                    user.name }}
                                </option>
                            </select>
                            <div v-if="errors.quality_owner_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.quality_owner_id" :key="index">{{ error
                                    }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.is_sold"
                                :class="{ 'is-invalid': errors.is_sold }" type="checkbox" id="is_sold">
                            <label class="form-check-label fw-bold" for="is_sold">
                                {{ $t('edit_opportunity_modal_input_is_sold') }}
                            </label>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $t('edit_opportunity_modal_card_header_sharepoint_link_text') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="w-100">
                                <label class="form-label fw-bold">{{
                                    $t('edit_opportunity_modal_input_share_point_url') }}</label>
                                <input type="text" v-model="formData.share_point_url"
                                    :class="{ 'is-invalid': errors.share_point_url }" class="form-control">
                                <div v-if="errors.share_point_url" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.share_point_url" :key="index">{{
                                        error
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $t('edit_opportunity_modal_card_header_servers_text') }}</h6>
                        </div>
                        <div class="card-body p-1">
                            <div class="w-100">
                                <ul class="list-group list-group-flush">
                                    <li class="border-0 list-group-item py-3 px-1"
                                        v-for="(environment, index) in formData.environments" :key="index">
                                        <div class="row g-1 w-100 align-items-center">
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        v-model="environment.desino_managed_fl"
                                                        :class="{ 'is-invalid': errors.desino_managed_fl }"
                                                        type="checkbox" :id="'desino_managed_fl' + index">
                                                    <label class="form-check-label fw-bold"
                                                        :for="'desino_managed_fl' + index">
                                                        {{ $t('edit_opportunity_modal_input_desino_managed_fl') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6 col-lg-4 col-xl-2">
                                                <select v-model="environment.type"
                                                    :class="{ 'is-invalid': errors.type }" class="form-select">
                                                    <option value="">{{
                                                        $t('edit_opportunity_modal_input_environment_server_type_placeholder')
                                                        }}
                                                    </option>
                                                    <option v-for="serverType in serverTypes" :key="serverType.id"
                                                        :value="serverType.id">{{
                                                            serverType.name }}
                                                    </option>
                                                </select>
                                                <div v-if="errors.type" class="invalid-feedback">
                                                    <span v-for="(error, index) in errors.type" :key="index">{{
                                                        error
                                                        }}</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6 col-lg-8 col-xl-3">
                                                <input type="text" v-model="environment.name"
                                                    :class="{ 'is-invalid': errors[`environments.${index}.name`] }"
                                                    class="form-control"
                                                    :placeholder="$t('edit_opportunity_modal_input_environment_name')">
                                                <div v-if="errors[`environments.${index}.name`]"
                                                    class="invalid-feedback">
                                                    <span v-for="(error, index) in errors[`environments.${index}.name`]"
                                                        :key="index">{{
                                                            error
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-10 col-md-10 col-lg-11 col-xl-6">
                                                <input type="text" v-model="environment.url"
                                                    :class="{ 'is-invalid': errors[`environments.${index}.url`] }"
                                                    class="form-control"
                                                    :placeholder="$t('edit_opportunity_modal_input_environment_url')">
                                                <div v-if="errors[`environments.${index}.url`]"
                                                    class="invalid-feedback">
                                                    <span v-for="(error, index) in errors[`environments.${index}.url`]"
                                                        :key="index">{{
                                                            error
                                                        }}</span>
                                                </div>
                                            </div>
                                            <div class="col-2 col-md-2 col-lg-1 col-xl-1">
                                                <button v-show="formData.environments.length > 1" type="button"
                                                    class="btn btn-danger border-0 w-100"
                                                    @click="removeEnvironment(index)">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-desino border-0 w-100" @click="addEnvironment()">
                                {{ $t('edit_opportunity_modal_input_add') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-6">
                            <button type="submit" class="btn btn-desino w-100 border-0">{{
                                $t('edit_opportunity_modal_submit_but_text') }}</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import GlobalMessage from './../../components/GlobalMessage.vue';
import OpportunityService from '../../services/OpportunityService';
import messageService from '../../services/messageService';
import { Modal } from 'bootstrap';
import showToast from '../../utils/toasts';
import { mapActions } from 'vuex';
import eventBus from "@/eventBus.js";
export default {
    name: 'EditOpportunityModal',
    components: {
        GlobalMessage,
    },
    data() {
        return {
            formData: {
                id: '',
                client_id: '',
                name: '',
                ballpark_development_hours: '',
                template_id: '',
                is_sold: false,
                client_name: '',
                share_point_url: '',
                functional_owner_id: '',
                technical_owner_id: '',
                quality_owner_id: '',
                environments: [
                    {
                        id: '',
                        type: '',
                        name: '',
                        url: '',
                        desino_managed_fl: false,
                    }
                ],
            },
            modalTitle: '',
            users: [],
            pdfTemplates: [],
            serverTypes: [],
            oldOpportunity: {},
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        getEditOpportunityFormData(opportunity) {
            this.setLoading(true);
            this.clearMessages();
            this.oldOpportunity = opportunity;
            this.setFormData(opportunity);

            if (document.getElementById('editInitiativeModal') != null) {
                this.modalTitle = this.$t('edit_initiative_modal_title')
            }
            if (document.getElementById('editOpportunityModal') != null) {
                this.modalTitle = this.$t('edit_opportunity_modal_title')
            }
            this.getEditOpportunityData();
            this.setLoading(false);
        },
        setFormData(opportunity) {
            this.formData.id = opportunity.id;
            this.formData.client_id = opportunity.client_id;
            this.formData.name = opportunity.name;
            this.formData.ballpark_development_hours = opportunity.ballpark_development_hours;
            this.formData.template_id = opportunity.template_id ?? '';
            this.formData.is_sold = opportunity.status === 2 ?? false;
            this.formData.client_name = opportunity.client.name;
            this.formData.share_point_url = opportunity.share_point_url;
            this.formData.functional_owner_id = opportunity.functional_owner_id ?? '';
            this.formData.technical_owner_id = opportunity.technical_owner_id ?? '';
            this.formData.quality_owner_id = opportunity.quality_owner_id ?? '';
            let opportunityEnvironments = JSON.parse(JSON.stringify(opportunity.initiative_environments));
            opportunityEnvironments.forEach((environment) => {
                environment.desino_managed_fl = environment.desino_managed_fl == 1 ?? false;
                environment.type = environment.type != null ? environment.type : '';
            })
            this.formData.environments = opportunityEnvironments.length == 0 ? [{
                id: '',
                type: '',
                name: '',
                url: '',
                desino_managed_fl: false,
            }] : opportunityEnvironments;
        },
        async updateOpportunity() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const response = await OpportunityService.updateOpportunity(this.formData);
                showToast(response.data.message, 'success');
                this.hideModal();
                this.$emit('pageUpdated');
                eventBus.$emit('sidebarSelectInitiativeUpdate');
                if (this.$route.name === 'task.detail') {
                    eventBus.$emit('refreshTicketDetail');
                }
                if (this.$route.name === 'tasks') {
                    eventBus.$emit('refreshTickets');
                }
                this.setLoading(false);
            } catch (error) {
                this.setFormData(this.oldOpportunity);
                this.handleError(error);
            }
        },
        addEnvironment() {
            this.formData.environments.push({
                id: '',
                type: '',
                name: '',
                url: '',
                desino_managed_fl: false,
            });
        },
        removeEnvironment(index) {
            this.formData.environments.splice(index, 1);
        },
        removeEnvironment(index) {
            this.formData.environments.splice(index, 1);
        },
        async getEditOpportunityData() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const response = await OpportunityService.getEditOpportunityData();
                this.users = response.content.clients;
                this.serverTypes = response.content.initiative_server_type;
                this.pdfTemplates = response.content.pdf_templates;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },

        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger', 'modal');
            }
            this.setLoading(false);
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage('modal');
        },
        hideModal() {
            const modalElement = document.getElementById('editOpportunityModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            const editInitiativeModalElement = document.getElementById('editInitiativeModal');
            if (editInitiativeModalElement) {
                const initiativeModal = Modal.getInstance(editInitiativeModalElement);
                if (initiativeModal) {
                    initiativeModal.hide();
                }
            }
            const editInitiativeOverViewModalElement = document.getElementById('editInitiativeOverviewModal');
            if (editInitiativeOverViewModalElement) {
                const initiativeOverViewModal = Modal.getInstance(editInitiativeOverViewModalElement);
                if (initiativeOverViewModal) {
                    initiativeOverViewModal.hide();
                }
            }
        }
    },
    mounted() {
        this.clearMessages();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
