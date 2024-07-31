<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="updateOpportunity">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInitiativeModalLabel">{{ $t('edit_opportunity_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div class="mb-3">
                        <label for="client_name" class="form-label">{{ $t('edit_opportunity_modal_select_client_name') }} <strong class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.client_name" disabled :class="{'is-invalid': errors.client_name}" id="name" class="form-control">                        
                        <div v-if="errors.client_name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.client_name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ $t('edit_opportunity_modal_input_name') }} <strong class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.name" :class="{'is-invalid': errors.name}" id="name" class="form-control">
                        <div v-if="errors.name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ballpark_development_hours" class="form-label">{{ $t('edit_opportunity_modal_input_ballpark_development_hours') }} <strong class="text-danger">*</strong></label>
                        <input type="number" v-model="formData.ballpark_development_hours" :class="{'is-invalid': errors.ballpark_development_hours}" id="ballpark_development_hours" class="form-control">
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.is_sold" :class="{'is-invalid': errors.is_sold}" type="checkbox" id="is_sold">
                            <label class="form-check-label" for="is_sold">
                                {{ $t('edit_opportunity_modal_input_is_sold') }}
                            </label>
                        </div>
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{ error }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{ $t('edit_opportunity_modal_submit_but_text') }}</button>
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
                },
                errors: {},
                showMessage: true
            };
        },
        methods: {
            getEditOpportunityFormData(opportunity) {
                this.clearMessages();
                this.formData.id = opportunity.id;
                this.formData.client_id = opportunity.client_id;
                this.formData.name = opportunity.name;
                this.formData.ballpark_development_hours = opportunity.ballpark_development_hours;
                this.formData.is_sold = opportunity.status === 2 ?? false;
                this.formData.client_name = opportunity.client.name;                
            },
            async updateOpportunity() {
                this.clearMessages();
                try {                    
                    const response = await OpportunityService.updateOpportunity(this.formData);                    
                    showToast(response.data.message, 'success');
                    this.hideModal();
                    this.$emit('pageUpddated');
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