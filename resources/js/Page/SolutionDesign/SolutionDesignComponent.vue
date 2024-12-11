<template>
    <GlobalMessage v-if="showMessage" />

    <div class="app-content mt-3 row">
        <div class="col-md-4 border-end">
            <div class="input-group my-3">
                <input v-model="solutionDesignFilters.name" type="text" class="form-control" placeholder="Search"
                    aria-label="Recipient's username" aria-describedby="basic-addon2"
                    @keyup="getSectionsWithFunctionalities">
                <span class="input-group-text" id="basic-addon2"><i class="bi bi-search"></i></span>
            </div>
            <draggable v-model="sectionsWithFunctionalities" :move="checkMoveSection"
                class="list-group list-group-flush border border-start-0 border-end-0" handle=".handle-section"
                item-key="id" @end="sectionOnDragEnd">
                <template #item="{ element: section, index }">
                    <div class="list-group-item section-functionality-container px-0 border-0">
                        <div class="section-container">
                            <div v-if="editingSectionId === section.id">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input v-model="editingSectionName"
                                            :class="{ 'is-invalid': errors.section_name }" class="form-control ms-4"
                                            type="text" @keyup.enter="updateSectionName(section)"
                                            @keydown.esc="showHideSectionInput(section)" />
                                        <button class="btn btn-outline-danger bg-danger text-white" type="button"
                                            id="button-addon2" @click="showHideSectionInput(section, 'close')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        <div v-if="errors.section_name" class="invalid-feedback ms-4">
                                            <span v-for="(error, index) in errors.section_name" :key="index">{{
                                                error
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="w-100 section-container section_name_area_cover">
                                <div class="row w-100 h-100 w-100 g-0">
                                    <div class="col-auto text-start" style="width:24px">
                                        <div class="section-sort" role="button">
                                            <i class="bi bi-grip-vertical handle-section me-2 hover-sort"></i>
                                        </div>
                                    </div>
                                    <div class="col-auto text-start" style="width:24px">
                                        <i :class="['bi', collapsedSections[section.id] ? 'bi-caret-right-fill' : 'bi-caret-down-fill']"
                                            data-bs-toggle="collapse" :data-bs-target="'#collapse_' + section.id"
                                            :aria-expanded="!collapsedSections[section.id]"
                                            :aria-controls="'#collapse_' + section.id"
                                            @click="collapsedSections[section.id] = !collapsedSections[section.id]"></i>
                                    </div>
                                    <div class="col-auto text-start" style="width: calc(100% - 50px)">
                                        <span class="section_name fs-5 fw-bold">{{ section.display_name }}</span>
                                        <span class="section-menu ms-3 d-inline-block">
                                            <span class="hover-menu">
                                                <a v-if="user?.is_admin" class="btn btn-desino btn-sm border-0 me-1"
                                                    href="javascript:" @click="toggleSection(section.id)"
                                                    :class="isSectionActive(section.id) ? 'd-none' : ''">
                                                    <i class="bi bi-plus-lg"></i>
                                                </a>
                                                <a v-if="user?.is_admin" class="btn btn-desino btn-sm border-0 me-1"
                                                    href="javascript:" @click="editSection(section)">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a v-if="user?.is_admin" class="btn btn-danger btn-sm border-0 me-1"
                                                    href="javascript:"
                                                    @click="showConfirmation('deleteSection', deleteSection, section)">
                                                    <i class="bi bi-trash3"></i>
                                                </a>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="section.functionalities" class="ps-4 py-2 pe-0 collapse w-100"
                            :class="{ 'show': !collapsedSections[section.id] }" :id="'collapse_' + section.id">
                            <draggable v-model="section.functionalities" :move="checkMoveFunctionality"
                                class="list-group list-group-flush" group="people" handle=".handle-functionality"
                                item-key="id" @add="functionalityOnDragAdd($event, index)"
                                @end="functionalityOnDragEnd">
                                <template #item="{ element: functionality, index: functionalityIndex }">
                                    <div class="border-0 border-bottom"
                                        :class="['list-group-item px-1 d-flex list-group-item-action', { 'bg-desino text-light': isSelected(functionality.id) }]"
                                        role="button" @click="selectFunctionality(functionality)">
                                        <span><i class="bi bi-grip-vertical handle-functionality me-2"></i></span>
                                        <span>{{ functionality.display_name }}</span>
                                        <span class="ms-auto d-flex align-items-center">
                                            <a v-if="user?.is_admin" class="nav-link text-dark" href="javascript:"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                :title="$t('solution_design.functionality_form.actions_create_ticket_tooltip')"
                                                @click.stop="showFunctionalityCreateTicketModal(functionality)">
                                                <i class="bi bi-plus-circle mx-2"></i>
                                            </a>
                                            <a v-if="user?.is_admin" class="text-danger me-2" href="javascript:"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                :title="$t('solution_design.functionality_form.actions_delete_tooltip')"
                                                @click.stop="showConfirmation('deleteFunctionality', deleteFunctionality, functionality)">
                                                <i class="bi bi-trash3"></i>
                                            </a>
                                            <span class="badge bg-secondary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom"
                                                :title="$t('solution_design.functionality_form.actions_open_tickets_count_tooltip')"
                                                @click.stop="user?.is_admin && showFunctionalityDetailModal(functionality)">{{
                                                    functionality?.open_tickets_count
                                                }}</span>
                                        </span>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>
                </template>
            </draggable>
            <AddNewSectionComponent v-if="user?.is_admin" class="mb-3" :initiativeData="initiativeData"
                @sectionAdded="handleSectionAdded" />
        </div>
        <div class="col-md-8 border-bottom p-3">
            <form @submit.prevent="storeUpdateFunctionality" v-if="functionalityFormData.section_id && user?.is_admin">
                <input type="hidden" v-model="functionalityFormData.functionality_id">
                <div class="row w-100">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{
                                $t('solution_design.functionality_form.name')
                            }} <strong class="text-danger">*</strong>
                            </label>
                            <input v-model="functionalityFormData.name" :class="{ 'is-invalid': errors.name }"
                                class="form-control" placeholder="Enter value" type="text">
                            <div v-if="errors.name" class="invalid-feedback">
                                <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{
                                $t('solution_design.functionality_form.section_name_select_box')
                            }} <strong class="text-danger">*</strong>
                            </label>
                            <select v-model="functionalityFormData.section_id" aria-label="Default select example"
                                class="form-select" :class="{ 'is-invalid': errors.section_id }">
                                <option value="">{{
                                    $t('solution_design.functionality_form.section_name_select_box_placeholder')
                                }}
                                </option>
                                <option v-for="section in sectionsWithFunctionalities" :key="section.id"
                                    :value="section.id">
                                    {{ section.name }}
                                </option>
                            </select>
                            <div v-if="errors.section_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.section_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{{
                        $t('solution_design.functionality_form.description')
                    }}</label>
                    <TinyMceEditor v-model="functionalityFormData.description" />
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" :class="{ 'is-invalid': errors.include_in_solution_design }"
                            v-model="functionalityFormData.include_in_solution_design" type="checkbox"
                            id="include_in_solution_design">
                        <label class="form-check-label fw-bold" for="include_in_solution_design">
                            {{ $t('solution_design.functionality_form.include_in_solution_design_text') }}
                        </label>
                        <div v-if="errors.include_in_solution_design" class="invalid-feedback">
                            <span v-for="(error, index) in errors.include_in_solution_design" :key="index">{{ error
                                }}</span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 d-flex gap-3" v-if="!functionalityFormData.functionality_id">
                    <button class="btn btn-desino w-50" type="submit"
                        @click="handleFunctionalitySubmitButtonClick('create')">{{
                            $t('solution_design.functionality_form.submit_create_but_text') }}
                    </button>
                    <button class="btn btn-desino w-50" type="submit"
                        @click="handleFunctionalitySubmitButtonClick('create_and_add_new')">{{
                            $t('solution_design.functionality_form.submit_create_and_add_new_but_text') }}
                    </button>
                </div>
                <div class="mb-3 d-flex gap-3" v-if="functionalityFormData.functionality_id">
                    <button class="btn btn-desino w-50" type="submit"
                        @click="handleFunctionalitySubmitButtonClick('update')">{{
                            $t('solution_design.functionality_form.submit_update_but_text') }}
                    </button>
                    <button class="btn btn-desino w-50" type="submit"
                        @click="handleFunctionalitySubmitButtonClick('update_and_add_new')">{{
                            $t('solution_design.functionality_form.submit_update_and_add_new_but_text') }}
                    </button>
                </div>
            </form>
        </div>

        <div id="createFunctionalityTicketModal" aria-hidden="true"
            aria-labelledby="createFunctionalityTicketModalLabel" class="modal fade" tabindex="-1">
            <CreateTicketModalComponent ref="createFunctionalityTicketModalComponent" @pageUpdated="fetchData" />
        </div>
        <ConfirmationModal ref="dynamicConfirmationModal" :title="modalTitle" :message="modalMessage"
            @confirm="modalConfirmCallback" />
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import GlobalMessage from './../../components/GlobalMessage.vue';
import messageService from './../../services/messageService';
import SolutionDesignService from './../../services/SolutionDesignService';
import AddNewSectionComponent from './Section/AddNewSectionComponent.vue';
import TinyMceEditor from './../../components/TinyMceEditor.vue';
import showToast from '../../utils/toasts';
import eventBus from '../../eventBus';
import draggable from 'vuedraggable';
import { mapActions, mapGetters } from 'vuex';
import { Modal, Tooltip } from "bootstrap";
import CreateTicketModalComponent from './../../Page/SolutionDesign/Ticket/CreateTicketModalComponent.vue';
import store from '../../store';


export default {
    name: 'SolutionDesignComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        AddNewSectionComponent,
        TinyMceEditor,
        draggable,
        CreateTicketModalComponent,
    },
    data() {
        return {
            drag: false,
            collapsedSections: {}, // for collapse and expand
            initiativeId: this.$route.params.id,
            initiativeData: {},
            sectionsWithFunctionalities: [],
            errors: {},
            showMessage: true,
            functionalityFormData: {
                section_id: "",
                name: "",
                initiative_id: "",
                description: "",
                functionality_id: "",
                include_in_solution_design: false
            },
            activeSectionId: null,
            selectedFunctionalityId: null,
            editingSectionId: null,
            editingSectionName: "",
            moveFunctionality: {},
            oldMoveFunctionality: {},
            moveSection: {},
            functionalitySubmitButtonClickedValue: "",
            solutionDesignFilters: {
                name: '',
            },
            modalTitle: '',
            modalMessage: '',
            modalConfirmCallback: null
        };
    },
    watch: {
        initiativeId: {
            handler(newValue, oldValue) {
                this.functionalityFormData.section_id = "";
                this.activeSectionId = "";
                // console.log(`Value changed from ${oldValue} to ${newValue}`);
            },
            deep: true
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchData() {
            try {
                this.setLoading(true);
                const sectionsWithFunctionalitiesPromise = this.getSectionsWithFunctionalities();
                await sectionsWithFunctionalitiesPromise;
                this.getInitiativeData();
                eventBus.$emit('selectHeaderInitiativeId', this.initiativeId);
                this.setLoading(false);
                this.clearMessages();
            } catch (error) {
                this.handleError(error);
            }
        },
        handleFunctionalitySubmitButtonClick(buttonValue) {
            this.functionalitySubmitButtonClickedValue = buttonValue;
        },
        async storeUpdateFunctionality() {
            this.clearMessages();
            try {
                await this.setLoading(true);
                this.functionalityFormData.initiative_id = this.initiativeId;
                const {
                    content: { functionality: updatedFunc, transactionType },
                    message
                } = await SolutionDesignService.storeUpdateFunctionality(this.functionalityFormData);
                const section = this.findItem(updatedFunc.section_id);
                this.getSectionsWithFunctionalities();

                if (this.functionalitySubmitButtonClickedValue === 'create' || this.functionalitySubmitButtonClickedValue === 'update') {
                    this.functionalityFormData.functionality_id = updatedFunc.id;
                    this.selectedFunctionalityId = updatedFunc.id;
                    this.activeSectionId = null;
                } else if (this.functionalitySubmitButtonClickedValue === 'create_and_add_new') {
                    this.functionalityFormData.name = "";
                    this.functionalityFormData.description = "";
                    this.functionalityFormData.include_in_solution_design = false;
                } else if (this.functionalitySubmitButtonClickedValue === 'update_and_add_new') {
                    this.functionalityFormData.functionality_id = "";
                    this.functionalityFormData.name = "";
                    this.functionalityFormData.description = "";
                    this.functionalityFormData.include_in_solution_design = false;
                    this.selectedFunctionalityId = null;
                }
                showToast(message, 'success');
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async getInitiativeData() {
            try {
                const setHeaderData = {
                    page_title: this.$t('solution_design.page_title') + ' - ' + this.initiativeData?.name,
                    is_solution_design_detail_path: true,
                    is_solution_design_download: true,
                    initiative_id: this.initiativeData?.id,
                }
                store.commit("setHeaderData", setHeaderData);
            } catch (error) {
                this.handleError(error);
            }
        },
        async getSectionsWithFunctionalities() {
            try {
                const hasValue = this.objectInValueExistOrNot(this.solutionDesignFilters);
                await hasValue ?? this.setLoading(true);
                const passData = {
                    initiative_id: this.initiativeId,
                    name: this.solutionDesignFilters.name
                }
                const { content, meta_data: { initiative } } = await SolutionDesignService.getSectionsWithFunctionalities(passData);
                this.sectionsWithFunctionalities = content;
                this.initiativeData = initiative;
                await hasValue ?? this.setLoading(false);
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        handleSectionAdded(newSection) {
            this.sectionsWithFunctionalities.push(newSection);
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
        toggleSection(sectionId) {
            this.resetForm();
            this.activeSectionId = this.activeSectionId === sectionId ? null : sectionId;
            this.functionalityFormData.section_id = this.activeSectionId || "";

            this.selectedFunctionalityId = null;
            this.functionalityFormData.section_id = this.activeSectionId || "";

            this.editingSectionId = null;
            this.editingSectionName = "";
        },
        isSectionActive(sectionId) {
            return this.activeSectionId === sectionId;
        },
        resetForm() {
            this.functionalityFormData = { section_id: "", name: "", description: "", functionality_id: "" };
            this.errors = {};
            this.activeSectionId = null;
        },
        findItem(sectionId) {
            return this.sectionsWithFunctionalities.find(section => section.id === sectionId);
        },
        async selectFunctionality(functionality) {
            if (this.selectedFunctionalityId === functionality.id) {
                this.selectedFunctionalityId = null;
                this.resetForm();
            } else {
                // this.functionalityFormData = {
                //     section_id: functionality.section_id,
                //     name: functionality.name,
                //     description: functionality.description ?? '',
                //     functionality_id: functionality.id,
                //     initiative_id: this.initiativeId,
                //     include_in_solution_design: functionality.include_in_solution_design == 1 ?? false,
                // };
                // this.selectedFunctionalityId = functionality.id;

                try {
                    await this.setLoading(true);
                    const passData = {
                        id: functionality.id,
                        initiative_id: this.initiativeId,
                    }
                    const { content: { functionalityData } } = await SolutionDesignService.getSectionFunctionality(passData);
                    this.functionalityFormData = functionalityData;
                    this.functionalityFormData.include_in_solution_design = functionalityData.include_in_solution_design == 1 ?? false;
                    this.selectedFunctionalityId = functionalityData.id;
                    this.activeSectionId = null;
                    await this.setLoading(false);
                } catch (error) {
                    this.handleError(error);
                }
            }
        },
        isSelected(functionalityId) {
            return this.selectedFunctionalityId === functionalityId;
        },
        showConfirmation(modalType, callback, callbackParam) {
            if (modalType === 'deleteFunctionality') {
                this.modalTitle = this.$t('solution_design.functionality_delete_actions_modal_title');
                this.modalMessage = this.$t('solution_design.functionality_delete_actions_modal_text');
            }
            if (modalType === 'deleteSection') {
                this.modalTitle = this.$t('solution_design.section_delete_actions_modal_title');
                this.modalMessage = this.$t('solution_design.section_delete_actions_modal_text');
            }

            this.modalConfirmCallback = () => callback(callbackParam);

            this.$refs.dynamicConfirmationModal.showModal();
        },
        async deleteFunctionality(functionality) {
            this.clearMessages();

            this.setLoading(true);
            functionality.initiative_id = this.initiativeId;
            const oldSelectedFunctionalityId = functionality;
            const { content, message } = await SolutionDesignService.deleteFunctionality(functionality);
            const section = this.findItem(functionality.section_id);
            section.functionalities = section.functionalities.filter(item => item.id !== functionality.id);
            const index = section.functionalities.findIndex(func => func.id === functionality.id);
            if (index !== -1) section.functionalities.splice(index, 1);
            if (this.selectedFunctionalityId == functionality.id) {
                this.resetForm();
            }
            this.getSectionsWithFunctionalities();
            showToast(message, 'success');
            this.setLoading(false);
        },
        async deleteSection(section) {
            this.clearMessages();
            try {
                this.setLoading(true);
                let result = section.functionalities.find(item => item['id'] === this.activeSectionId);
                delete section.functionalities;
                const { content, message } = await SolutionDesignService.deleteSection(section);
                const index = this.sectionsWithFunctionalities.findIndex(sec => sec.id === section.id);
                if (index !== -1) this.sectionsWithFunctionalities.splice(index, 1);
                if (result) {
                    this.resetForm();
                }
                showToast(message, 'success');
                this.getSectionsWithFunctionalities();
                this.setLoading(false);
            } catch (error) {
                this.getSectionsWithFunctionalities();
                this.handleError(error);
            }
        },
        editSection(section) {
            this.editingSectionId = section.id;
            this.editingSectionName = section.name;

            this.activeSectionId = null;
            this.selectedFunctionalityId = null;
            this.errors = {};
            messageService.clearMessage();
        },
        showHideSectionInput(section, eventName = "") {
            if (eventName == 'close') {
                this.editingSectionId = null;
                section.name = section.name.trim();
                section.display_name = section.display_name.trim();
                return;
            }
            if (this.editingSectionName.trim() === section.name.trim()) {
                this.editingSectionId = null;
                return;
            }
        },
        async updateSectionName(section) {
            this.showHideSectionInput(section);

            try {
                this.setLoading(true);
                const updateData = {
                    section_id: section.id,
                    initiative_id: section.initiative_id,
                    section_name: this.editingSectionName.trim(),
                };
                const response = await SolutionDesignService.updateSectionName(updateData);
                section.name = response.content.name.trim();
                section.display_name = response.content.display_name.trim();
                this.editingSectionId = null;
                showToast(response.message, 'success');
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        functionalityOnDragAdd($event, index) {
            this.oldMoveFunctionality = this.sectionsWithFunctionalities[index];
        },
        checkMoveFunctionality(e) {
            this.moveFunctionality = e.draggedContext.element;
            this.moveFunctionality.order_no = e.draggedContext.futureIndex + 1;
        },
        async functionalityOnDragEnd(event) {
            this.clearMessages();
            if (Object.keys(this.moveFunctionality).length === 0) {
                return;
            }
            this.drag = false;
            try {
                this.setLoading(true);
                this.moveFunctionality.move_to_section_id = this.oldMoveFunctionality.id;
                this.moveFunctionality.initiative_id = this.initiativeId;
                const response = await SolutionDesignService.updateFunctionalityOrderNo(this.moveFunctionality);
                if (this.functionalityFormData.functionality_id != '') {
                    this.functionalityFormData = {
                        'functionality_id': response.content.id,
                        'section_id': response.content.section_id,
                        'name': response.content.name,
                        'order_no': response.content.order_no,
                        'description': response.content.description
                    };
                }
                this.getSectionsWithFunctionalities();
                showToast(response.message, 'success');
                this.oldMoveFunctionality = {};
                this.setLoading(false);
            } catch (error) {
                this.getSectionsWithFunctionalities();
                this.handleError(error);
            }
        },
        checkMoveSection(e) {
            this.moveSection = e.draggedContext.element;
            this.moveSection.order_no = e.draggedContext.futureIndex + 1;
        },
        async sectionOnDragEnd(e) {
            this.clearMessages();
            if (Object.keys(this.moveSection).length === 0) {
                return;
            }
            this.drag = false;
            try {
                this.setLoading(true);
                delete this.moveSection.functionalities;
                const response = await SolutionDesignService.updateSectionOrderNo(this.moveSection);
                this.getSectionsWithFunctionalities();
                showToast(response.message, 'success');
                this.setLoading(false);
            } catch (error) {
                this.getSectionsWithFunctionalities();
                this.handleError(error);
            }
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
        showFunctionalityCreateTicketModal(functionality) {
            this.$refs.createFunctionalityTicketModalComponent.resetForm();
            this.$refs.createFunctionalityTicketModalComponent.fetchData();
            this.$refs.createFunctionalityTicketModalComponent.selectedFunctionalityFromFunctionalityList(functionality);
            const modalElement = document.getElementById('createFunctionalityTicketModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        showFunctionalityDetailModal(functionality) {
            const data = { functionality: functionality };
            store.commit("setPassedData", data);
            this.$router.push({
                name: 'tasks'
            });
        },
        objectInValueExistOrNot(obj) {
            const hasValue = Object.values(obj).some(value => value);
            return hasValue;
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
    },
    mounted() {
        this.fetchData();
    },
    beforeRouteUpdate(to, from, next) {
        this.initiativeId = to.params.id;
        // this.resetForm();
        this.fetchData();
        next();
    },
}
</script>

<style scoped>
.alert {
    margin-top: 1rem;
}

.btn-close {
    position: absolute;
    right: 1rem;
}
</style>
