<template>
    <div class="app-content-header pb-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('solution_design.page_title') }} - {{ initiativeData.name }}</h3>
                    <h5>
            <span class="badge rounded bg-desino text-light my-3">
                Development Ballpark: {{ initiativeData.ballpark_development_hours }} hours
            </span>
                    </h5>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a class="text-decoration-none"
                                                       href="javascript:void(0)">{{ $t('Dashboard') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <GlobalMessage v-if="showMessage"/>

    <div class="app-content row">
            <div class="col-md-4 border">
                <div class="input-group my-3">
                    <input type="text" class="form-control" placeholder="Search" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <span class="input-group-text" id="basic-addon2"><i class="bi bi-search"></i></span>
                </div>
                <hr>
                <draggable v-model="sectionsWithFunctionalities" :move="checkMoveSection" class="list-group"
                           handle=".handle-section" item-key="id" @end="sectionOnDragEnd">
                    <template #item="{ element: section, index }">
                        <div class="section-functionality-container">
                            <div class="d-flex mt-3 section-container">
                                <div v-if="editingSectionId === section.id">
                                    <div>
                                        <input v-model="editingSectionName"
                                               :class="{ 'is-invalid': errors.section_name }"
                                               class="form-control"
                                               type="text"
                                               @blur="updateSectionName(section)"
                                               @keyup.enter="updateSectionName(section)"/>
                                        <div v-if="errors.section_name" class="invalid-feedback">
                                            <span v-for="(error, index) in errors.section_name" :key="index">{{
                                                    error
                                                }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="d-flex align-items-center section-container">
                                    <div class="section-sort" role="button">
                                        <i class="bi bi-grip-horizontal handle-section me-2 hover-sort"></i>
                                    </div>
                                    <div class="fw-bold fs-5">
                                        <a class="text-decoration-none text-dark" data-bs-toggle="collapse" :data-bs-target="'#collapse_' + section.id" aria-expanded="false" :aria-controls="'#collapse_' + section.id">
                                            <span><i class="bi bi-caret-right-fill"></i></span> {{ section.name }}
                                        </a>
                                    </div>
                                    <div class="section-menu">
                                        <div class="dropdown align-contents-start ml-5 hover-menu">
                                            <a class="fw-bold fs-4 text-desino" href="javascript:"
                                               @click="toggleSection(section.id)">
                                                <i :class="isSectionActive(section.id) ? 'bi bi-dash' : 'bi bi-plus'"></i>
                                            </a>
                                            <a id="dropdown_section_menu" aria-expanded="false"
                                               class="text-desino fw-bold fs-4 text-desino"
                                               data-bs-toggle="dropdown" href="javascript:" type="button">
                                                <i class="bi bi-three-dots"></i>
                                            </a>
                                            <ul aria-labelledby="dropdown_section_menu" class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="javascript:"
                                                       @click="editSection(section)">
                                                        <i class="bi bi-pencil-square"></i>
                                                        {{ $t('solution_design.section.option_edit_but_text') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="javascript:"
                                                       @click="deleteSection(section)">
                                                        <i class="bi bi-trash3"></i>
                                                        {{ $t('solution_design.section.option_delete_but_text') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="section.functionalities" class="list-group ps-5 collapse" :id="'collapse_' + section.id">
                                <draggable v-model="section.functionalities" :move="checkMoveFunctionality"
                                           class="list-group" group="people"
                                           handle=".handle-functionality" item-key="id"
                                           @add="functionalityOnDragAdd($event, index)" @end="functionalityOnDragEnd">
                                    <template #item="{ element: functionality, index: functionalityIndex }">
                                        <div
                                            class="border-0 border-bottom"
                                            :class="['list-group-item d-flex list-group-item-action', { 'bg-desino text-light': isSelected(functionality.id) }]"
                                            role="button" @click="selectFunctionality(functionality)">
                                            <span><i class="bi bi-grip-horizontal handle-functionality me-2"></i></span>
                                            <span>{{ functionality.name }}</span>
                                            <span class="ms-auto d-flex align-items-center">
                                                <a class="text-danger me-2" href="javascript:"
                                                   @click.stop="deleteFunctionality(functionality)">
                                                    <i class="bi bi-trash3"></i>
                                                </a>
                                                <span class="badge bg-secondary">0</span>
                                            </span>
                                        </div>
                                    </template>
                                </draggable>
                            </div>

                        </div>
                    </template>
                </draggable>
                <AddNewSectionComponent :initiativeData="initiativeData" @sectionAdded="handleSectionAdded"/>
            </div>
            <div class="col-md-8 border-top border-bottom p-3">
                <form @submit.prevent="storeUpdateFunctionality">
                    <input v-model="functionalityFormData.functionality_id" type="hidden">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="functionality_name">{{
                                        $t('solution_design.functionality_form.name')
                                    }} <strong
                                        class="text-danger">*</strong>
                                </label>
                                <input id="functionality_name" v-model="functionalityFormData.name"
                                       :class="{ 'is-invalid': errors.name }"
                                       class="form-control" placeholder="Enter value"
                                       type="text">
                                <div v-if="errors.name" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="section_id">{{
                                        $t('solution_design.functionality_form.section_name_select_box')
                                    }} <strong
                                        class="text-danger">*</strong>
                                </label>
                                <select v-model="functionalityFormData.section_id" aria-label="Default select example"
                                        class="form-select">
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
                        <label class="form-label" for="functionalityDescription">{{
                                $t('solution_design.functionality_form.description')
                            }}</label>
                        <TinyMceEditor v-model="functionalityFormData.description"/>
                    </div>
                    <div class="mb-3">
                        <button :disabled="!functionalityFormData.section_id" class="btn bg-desino w-100 text-light"
                                type="submit">
                            {{
                                functionalityFormData.functionality_id ?
                                    $t('solution_design.functionality_form.submit_update_but_text') :
                                    $t('solution_design.functionality_form.submit_save_but_text')
                            }}
                        </button>
                    </div>
                </form>
            </div>
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


export default {
    name: 'SolutionDesignComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        AddNewSectionComponent,
        TinyMceEditor,
        draggable
    },
    data() {
        return {
            drag: false,

            initiativeId: this.$route.params.id,
            initiativeData: {},
            sectionsWithFunctionalities: [],
            errors: {},
            showMessage: true,
            functionalityFormData: {
                section_id: "",
                name: "",
                description: "",
                functionality_id: "",
            },
            activeSectionId: null,
            selectedFunctionalityId: null,
            editingSectionId: null,
            editingSectionName: "",
            moveFunctionality: {},
            oldMoveFunctionality: {},
            moveSection: {},
        };
    },
    methods: {
        async fetchData() {
            try {
                await Promise.all([
                    this.getInitiativeData(),
                    this.getSectionsWithFunctionalities(),
                    eventBus.$emit('selectHeaderInitiativeId', this.initiativeId)
                ]);
                this.clearMessages();
            } catch (error) {
                this.handleError(error);
            }
        },
        async storeUpdateFunctionality() {
            this.clearMessages();
            try {
                const {
                    content: {functionality: updatedFunc, transactionType},
                    message
                } = await SolutionDesignService.storeUpdateFunctionality(this.functionalityFormData);
                const section = this.findItem(updatedFunc.section_id);
                this.getSectionsWithFunctionalities();


                this.functionalityFormData.functionality_id = updatedFunc.id;
                this.selectedFunctionalityId = updatedFunc.id;
                this.activeSectionId = null;
                showToast(message, 'success');
            } catch (error) {
                this.handleError(error);
            }
        },
        async getInitiativeData() {
            try {
                const {content} = await SolutionDesignService.getInitiativeData({initiative_id: this.initiativeId});
                if (!content) {
                    messageService.setMessage(response.message, 'danger');
                    this.$router.push({name: 'home'});
                } else {
                    this.initiativeData = content;
                }
            } catch (error) {
                this.handleError(error);
            }
        },
        async getSectionsWithFunctionalities() {
            try {
                const {content} = await SolutionDesignService.getSectionsWithFunctionalities({initiative_id: this.initiativeId});
                this.sectionsWithFunctionalities = content;
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
            this.functionalityFormData = {section_id: "", name: "", description: "", functionality_id: ""};
            this.errors = {};
            this.activeSectionId = null;
        },
        findItem(sectionId) {
            return this.sectionsWithFunctionalities.find(section => section.id === sectionId);
        },
        selectFunctionality(functionality) {
            if (this.selectedFunctionalityId === functionality.id) {
                this.selectedFunctionalityId = null;
                this.resetForm();
            } else {
                this.functionalityFormData = {
                    section_id: functionality.section_id,
                    name: functionality.name,
                    description: functionality.description ?? '',
                    functionality_id: functionality.id,
                };
                this.selectedFunctionalityId = functionality.id;
                this.activeSectionId = null;
            }
        },
        isSelected(functionalityId) {
            return this.selectedFunctionalityId === functionalityId;
        },
        async deleteFunctionality(functionality) {
            this.clearMessages();
            this.$swal({
                title: this.$t('solution_design.functionality_delete_actions_modal_title'),
                text: this.$t('solution_design.functionality_delete_actions_modal_text'),
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: this.$t('opportunity_list_table.actions_lost_status_modal_confirm_button_text')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const oldSelectedFunctionalityId = functionality;
                        const {
                            content,
                            message
                        } = await SolutionDesignService.deleteFunctionality({functionality_id: functionality.id});
                        const section = this.findItem(functionality.section_id);
                        section.functionalities = section.functionalities.filter(item => item.id !== functionality.id);
                        const index = section.functionalities.findIndex(func => func.id === functionality.id);
                        if (index !== -1) section.functionalities.splice(index, 1);
                        if (this.selectedFunctionalityId == functionality.id) {
                            this.resetForm();
                        }
                        showToast(message, 'success');
                    } catch (error) {
                        this.handleError(error);
                    }
                }
            })
        },
        async deleteSection(section) {
            this.clearMessages();
            this.$swal({
                title: this.$t('solution_design.section_delete_actions_modal_title'),
                text: this.$t('solution_design.section_delete_actions_modal_text'),
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: this.$t('opportunity_list_table.actions_lost_status_modal_confirm_button_text')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        let result = section.functionalities.find(item => item['id'] === this.selectedFunctionalityId);
                        const {content, message} = await SolutionDesignService.deleteSection({section_id: section.id});
                        const index = this.sectionsWithFunctionalities.findIndex(sec => sec.id === section.id);
                        if (index !== -1) this.sectionsWithFunctionalities.splice(index, 1);
                        if (result) {
                            this.resetForm();
                        }
                        showToast(message, 'success');
                    } catch (error) {
                        this.handleError(error);
                    }
                }
            })
        },
        editSection(section) {
            this.editingSectionId = section.id;
            this.editingSectionName = section.name;

            this.activeSectionId = null;
            this.selectedFunctionalityId = null;
        },
        async updateSectionName(section) {
            if (this.editingSectionName.trim() === section.name.trim()) {
                this.editingSectionId = null;
                return;
            }

            try {
                const updateData = {
                    section_id: section.id,
                    section_name: this.editingSectionName.trim(),
                };
                const response = await SolutionDesignService.updateSectionName(updateData);
                section.name = this.editingSectionName.trim();
                this.editingSectionId = null;
                showToast(response.message, 'success');
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
            this.drag = false;
            try {
                this.moveFunctionality['move_to_section_id'] = this.oldMoveFunctionality.id;
                const response = await SolutionDesignService.updateFunctionalityOrderNo(this.moveFunctionality);
                showToast(response.message, 'success');
                this.oldMoveFunctionality = {};
            } catch (error) {
                this.handleError(error);
            }
        },
        checkMoveSection(e) {
            this.moveSection = e.draggedContext.element;
            this.moveSection.order_no = e.draggedContext.futureIndex + 1;
        },
        async sectionOnDragEnd(e) {
            this.drag = false;
            try {
                const response = await SolutionDesignService.updateSectionOrderNo(this.moveSection);
                showToast(response.message, 'success');
            } catch (error) {
                this.handleError(error);
            }
        }
    },
    mounted() {
        this.fetchData();
    },
    beforeRouteUpdate(to, from, next) {
        this.initiativeId = to.params.id;
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
