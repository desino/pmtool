<template>
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title" id="planNewInitiativeModalLabel">
                    {{ $t('planning.plan_new_initiative_modal_title') }}
                </h5>
            </div>
            <form @submit.prevent="addPlanNewInitiative">
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <div class="row w-100">
                        <div class="col-12 mb-3">
                            <select v-model="formData.initiative_id" :class="{ 'is-invalid': errors.initiative_id }"
                                class="form-select">
                                <option value="">{{
                                    $t('planning.plan_new_initiative.modal_select_initiative_label_text')
                                }}</option>
                                <option v-for="initiative in initiativesList" :key="initiative.id"
                                    :value="initiative.id">{{
                                        initiative.name }}
                                </option>
                            </select>
                            <div v-if="errors.initiative_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.initiative_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <select v-model="formData.user_id" :class="{ 'is-invalid': errors.user_id }"
                                class="form-select">
                                <option value="">{{
                                    $t('planning.plan_new_initiative.modal_select_user_label_text')
                                }}</option>
                                <option v-for="user in usersList" :key="user.id" :value="user.id">{{
                                    user.name }}
                                </option>
                            </select>
                            <div v-if="errors.user_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.user_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-12 col-md-12 col-lg-6">
                            <button type="submit" class="btn btn-desino w-100" :disabled="!isDisableSubmitBut">{{
                                $t('planning.plan_new_initiative.modal_submit_but_text') }}</button>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                            <button class="btn btn-danger w-100 border-0" data-bs-dismiss="modal" type="button">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import messageService from '../../services/messageService';
import { mapActions } from 'vuex';
import PlanningService from '../../services/PlanningService';
import { Modal } from 'bootstrap';

export default {
    name: 'PlanNewInitiativeModalComponent',
    components: {
        GlobalMessage
    },
    data() {
        return {
            formData: {
                initiative_id: '',
                user_id: '',
            },
            initiativesList: [],
            usersList: [],
            existingPlanningInitiativeIds: [],
            errors: {},
            showMessage: true
        }
    },
    computed: {
        isDisableSubmitBut() {
            return this.formData.initiative_id && this.formData.user_id;
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getPlanNewInitiativeForOpenModalData(existingPlannings) {
            this.clearForm();
            this.existingPlanningInitiativeIds = existingPlannings.map(item => item.initiative_id);
            try {
                const { content: { initiatives, users } } = await PlanningService.getPlanningInitialData();
                this.usersList = users;
                this.initiativesList = initiatives.filter(item => !this.existingPlanningInitiativeIds.includes(item.id));
            } catch (error) {
                this.handleError(error);
            }
        },
        addPlanNewInitiative() {
            if (!this.formData.initiative_id && !this.formData.user_id) {
                return;
            }
            this.formData.initiative_name = this.initiativesList.find(item => item.id == this.formData.initiative_id).name;
            this.formData.user_name = this.usersList.find(item => item.id == this.formData.user_id).name;
            this.$emit('add-plan-new-initiative', this.formData);
            this.hideModal();
        },
        hideModal() {
            const modalElement = document.getElementById('planNewInitiativeModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
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
            messageService.clearMessage();
        },
        clearForm() {
            this.formData.initiative_id = '';
            this.formData.user_id = '';
        }
    }
}
</script>