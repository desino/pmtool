<template>
    <div class="modal-dialog modal-xl">
        <form @submit.prevent="updateOpportunity">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInitiativeModalLabel">{{ $t('edit_opportunity_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div class="row">
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="client_name" class="form-label">{{
                                    $t('edit_opportunity_modal_select_client_name')
                                }} <strong class="text-danger">*</strong></label>
                                <input type="text" v-model="formData.client_name" disabled
                                    :class="{ 'is-invalid': errors.client_name }" id="name" class="form-control">
                                <div v-if="errors.client_name" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.client_name" :key="index">{{ error }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="name" class="form-label">{{ $t('edit_opportunity_modal_input_name') }}
                                <strong class="text-danger">*</strong></label>
                            <input type="text" v-model="formData.name" :class="{ 'is-invalid': errors.name }" id="name"
                                class="form-control">
                            <div v-if="errors.name" class="invalid-feedback">
                                <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ballpark_development_hours" class="form-label">{{
                                $t('edit_opportunity_modal_input_ballpark_development_hours') }} <strong
                                    class="text-danger">*</strong></label>
                            <input type="number" v-model="formData.ballpark_development_hours"
                                :class="{ 'is-invalid': errors.ballpark_development_hours }"
                                id="ballpark_development_hours" class="form-control">
                            <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                                <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{
                                    error
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label for="functional_owner_id" class="form-label">
                                {{ $t('create_initiative_modal_select_functional_owner') }}
                            </label>
                            <select v-model="formData.functional_owner_id"
                                :class="{ 'is-invalid': errors.functional_owner_id }" id="functional_owner_id"
                                class="form-select">
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
                        <div class="col-md-4 mb-3">
                            <label for="technical_owner_id" class="form-label">
                                {{ $t('create_initiative_modal_select_technical_owner') }}
                            </label>
                            <select v-model="formData.technical_owner_id"
                                :class="{ 'is-invalid': errors.technical_owner_id }" id="technical_owner_id"
                                class="form-select">
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
                        <div class="col-md-4 mb-3">
                            <label for="quality_owner_id" class="form-label">
                                {{ $t('create_initiative_modal_select_quality_owner') }}
                            </label>
                            <select v-model="formData.quality_owner_id"
                                :class="{ 'is-invalid': errors.quality_owner_id }" id="quality_owner_id"
                                class="form-select">
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
                            <label class="form-check-label" for="is_sold">
                                {{ $t('edit_opportunity_modal_input_is_sold') }}
                            </label>
                        </div>
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{
                                error
                            }}</span>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $t('edit_opportunity_modal_card_header_sharepoint_link_text') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="share_point_url" class="form-label">{{
                                    $t('edit_opportunity_modal_input_share_point_url') }}</label>
                                <input type="text" v-model="formData.share_point_url"
                                    :class="{ 'is-invalid': errors.share_point_url }" id="share_point_url"
                                    class="form-control">
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
                        <div class="card-body">
                            <div v-for="(environment, index) in formData.environments" :key="index">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="name" class="form-label">{{
                                            $t('edit_opportunity_modal_input_environment_name') }} <strong
                                                class="text-danger">*</strong></label>
                                        <input type="text" v-model="environment.name"
                                            :class="{ 'is-invalid': errors[`environments.${index}.name`] }" id="name"
                                            class="form-control">
                                        <div v-if="errors[`environments.${index}.name`]" class="invalid-feedback">
                                            <span v-for="(error, index) in errors[`environments.${index}.name`]"
                                                :key="index">{{
                                                    error
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="url" class="form-label">{{
                                            $t('edit_opportunity_modal_input_environment_url') }}<strong
                                                class="text-danger">*</strong></label>
                                        <input type="text" v-model="environment.url"
                                            :class="{ 'is-invalid': errors[`environments.${index}.url`] }" id="url"
                                            class="form-control">
                                        <div v-if="errors[`environments.${index}.url`]" class="invalid-feedback">
                                            <span v-for="(error, index) in errors[`environments.${index}.url`]"
                                                :key="index">{{
                                                    error
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-4 my-auto">
                                        <div class="form-check">
                                            <input class="form-check-input" v-model="environment.desino_managed_fl"
                                                :class="{ 'is-invalid': errors.desino_managed_fl }" type="checkbox"
                                                :id="'desino_managed_fl' + index">
                                            <label class="form-check-label" :for="'desino_managed_fl' + index">
                                                {{ $t('edit_opportunity_modal_input_desino_managed_fl') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3 my-auto">
                                        <button v-show="formData.environments.length > 1" type="button"
                                            class="btn btn-danger mx-1" @click="removeEnvironment(index)">
                                            {{ $t('edit_opportunity_modal_input_remove') }}
                                        </button>
                                        <button v-show="index === formData.environments.length - 1" type="button"
                                            class="btn bg-desino text-white mx-1" @click="addEnvironment(index)">
                                            {{ $t('edit_opportunity_modal_input_add') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-desino bg-desino text-light">{{
                        $t('edit_opportunity_modal_submit_but_text') }}</button>
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
                is_sold: false,
                client_name: '',
                share_point_url: '',
                functional_owner_id: '',
                technical_owner_id: '',
                quality_owner_id: '',
                environments: [
                    {
                        id: '',
                        name: '',
                        url: '',
                        desino_managed_fl: false,
                    }
                ],
            },
            users: [],
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        getEditOpportunityFormData(opportunity) {
            this.setLoading(true);
            this.clearMessages();
            this.formData.id = opportunity.id;
            this.formData.client_id = opportunity.client_id;
            this.formData.name = opportunity.name;
            this.formData.ballpark_development_hours = opportunity.ballpark_development_hours;
            this.formData.is_sold = opportunity.status === 2 ?? false;
            this.formData.client_name = opportunity.client.name;
            this.formData.share_point_url = opportunity.share_point_url;
            this.formData.functional_owner_id = opportunity.functional_owner_id ?? '';
            this.formData.technical_owner_id = opportunity.technical_owner_id ?? '';
            this.formData.quality_owner_id = opportunity.quality_owner_id ?? '';
            let opportunityEnvironments = opportunity.initiative_environments;
            opportunityEnvironments.forEach((environment) => {
                environment.desino_managed_fl = environment.desino_managed_fl == 1 ?? false;
            })
            this.formData.environments = opportunityEnvironments.length == 0 ? [{
                id: '',
                name: '',
                url: '',
                desino_managed_fl: false,
            }] : opportunityEnvironments;
            this.getUserList();
            this.setLoading(false);
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
                this.handleError(error);
            }
        },
        addEnvironment() {
            this.formData.environments.push({
                id: '',
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
        async getUserList() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const response = await OpportunityService.getUserList();
                this.users = response.content;
                this.setLoading(false);
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
