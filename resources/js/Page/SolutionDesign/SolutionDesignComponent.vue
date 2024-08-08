<template>
    <GlobalMessage v-if="showMessage" />
    <div>
        <!-- <draggable :list="items" item-key="id" handle=".handle" :move="checkMove" @end="onDragEnd">
            <template #item="{ element }">
                <div class="list-item">
                    <div class="handle">
                        test
                    </div>
                    {{ element.name }}
                    {{ element.id }}
                </div>
            </template>
</draggable> -->
        <h1 class="primary">{{ $t('solution_design.page_title') }} - {{ initiativeData.name }}</h1>
        <h5>
            <span class="badge rounded-pill bg-desino text-light">
                Development Ballpark: {{ initiativeData.ballpark_development_hours }} hours
            </span>
        </h5>
        <div class="row mt-4">
            <div class="col-md-4">
                <div v-if="sectionsWithFunctionalities.length" v-for="section in sectionsWithFunctionalities"
                    :key="section.id" class="">
                    <h5 class="d-flex mt-3">
                        <template v-if="editingSectionId === section.id">
                            <div>
                                <input type="text" class="form-control" :class="{ 'is-invalid': errors.section_name }"
                                    v-model="editingSectionName" @blur="updateSectionName(section)"
                                    @keyup.enter="updateSectionName(section)" />
                                <div v-if="errors.section_name" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.section_name" :key="index">{{ error
                                        }}</span>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            {{ section.name }}
                            <a href="javascript:" class="fw-bold fs-4 custom-hover mr-5 text-desino"
                                @click="toggleSection(section.id)">
                                <i :class="isSectionActive(section.id) ? 'bi bi-dash' : 'bi bi-plus'"></i>
                            </a>
                            <div class="dropdown custom-hover ml-5">
                                <a href="javascript:" class="text-desino" type="button" id="dropdown_section_menu"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdown_section_menu">
                                    <li>
                                        <a class="dropdown-item" href="javascript:" @click="editSection(section)">
                                            <i class="bi bi-pencil-square"></i>
                                            {{ $t('solution_design.section.option_edit_but_text') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:" @click="deleteSection(section)">
                                            <i class="bi bi-trash3"></i>
                                            {{ $t('solution_design.section.option_delete_but_text') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </template>
                    </h5>
                    <ul class="list-group">
                        <!-- group="people" -->
                        <draggable v-model="section.functionalities" :move="checkMoveFunctionality"
                            handle=".handle-functionality" @end="functionalityOnDragEnd" class="list-group"
                            item-key="id">
                            <template #item="{ element }">
                                <li :class="['list-group-item d-flex list-group-item-action', { 'bg-desino text-light': isSelected(element.id) }]"
                                    role="button" @click="selectFunctionality(element)">
                                    <span><i class="bi bi-grip-horizontal handle-functionality me-2"></i></span>
                                    <span>{{ element.name }}</span>
                                    <span class="ms-auto d-flex align-items-center">
                                        <a href="javascript:" class="text-danger me-2"
                                            @click.stop="deleteFunctionality(element)">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                        <span class="badge bg-secondary">0</span>
                                    </span>
                                </li>
                            </template>
                        </draggable>
                        <!-- <li role="button" v-for="func in section.functionalities" :key="func.id"
                            :class="['list-group-item d-flex list-group-item-action', { 'bg-desino text-light': isSelected(func.id) }]"
                            @click="selectFunctionality(func)">
                            <span>{{ func.name }}</span>
                            <span class="ms-auto d-flex align-items-center">
                                <a href="javascript:" class="text-danger me-2" @click.stop="deleteFunctionality(func)">
                                    <i class="bi bi-trash3"></i>
                                </a>
                                <span class="badge bg-secondary">0</span>
                            </span>
                        </li> -->
                    </ul>
                </div>
            </div>
            <div class="col-md-8">
                <form @submit.prevent="storeUpdateFunctionality">
                    <div v-if="errors.section_id" class="alert alert-danger">
                        <button type="button" class="btn-close" aria-label="Close" @click="clearMessages"></button>
                        <span v-for="(error, index) in errors.section_id" :key="index">{{ error }}</span>
                    </div>
                    <input type="hidden" v-model="functionalityFormData.section_id">
                    <input type="hidden" v-model="functionalityFormData.functionality_id">
                    <div class="mb-3">
                        <label for="functionalityName" class="form-label">{{
                            $t('solution_design.functionality_form.name') }}</label>
                        <input type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                            id="functionalityName" v-model="functionalityFormData.name" placeholder="Enter value">
                        <div v-if="errors.name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="functionalityDescription" class="form-label">{{
                            $t('solution_design.functionality_form.description') }}</label>
                        <TinyMceEditor v-model="functionalityFormData.description" />
                    </div>
                    <div class="mb-3">
                        <button type="submit" :disabled="!functionalityFormData.section_id"
                            class="btn bg-desino w-100 text-light">
                            {{ functionalityFormData.functionality_id ?
                                $t('solution_design.functionality_form.submit_update_but_text') :
                                $t('solution_design.functionality_form.submit_save_but_text') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <AddNewSectionComponent :initiativeData="initiativeData" @sectionAdded="handleSectionAdded" />
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
            // items: [
            //     { name: "John 1", id: 0 },
            //     { name: "Joao 2", id: 1 },
            //     { name: "Jean 3", id: 2 }
            // ],
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
                const { content: { functionality: updatedFunc, transactionType }, message } = await SolutionDesignService.storeUpdateFunctionality(this.functionalityFormData);
                const section = this.findItem(updatedFunc.section_id);
                if (transactionType === 'created') {
                    section.functionalities.push(updatedFunc);
                } else if (transactionType === 'updated') {
                    const index = section.functionalities.findIndex(func => func.id === updatedFunc.id);
                    if (index !== -1) section.functionalities.splice(index, 1, updatedFunc);
                }

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
                const { content } = await SolutionDesignService.getInitiativeData({ initiative_id: this.initiativeId });
                if (!content) {
                    messageService.setMessage(response.message, 'danger');
                    this.$router.push({ name: 'home' });
                } else {
                    this.initiativeData = content;
                }
            } catch (error) {
                this.handleError(error);
            }
        },
        async getSectionsWithFunctionalities() {
            try {
                const { content } = await SolutionDesignService.getSectionsWithFunctionalities({ initiative_id: this.initiativeId });
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
            this.functionalityFormData = { section_id: "", name: "", description: "", functionality_id: "" };
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
            try {
                const oldSelectedFunctionalityId = functionality;
                const { content, message } = await SolutionDesignService.deleteFunctionality({ functionality_id: functionality.id });
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
        },
        async deleteSection(section) {
            this.clearMessages();
            try {
                let result = section.functionalities.find(item => item['id'] === this.selectedFunctionalityId);
                const { content, message } = await SolutionDesignService.deleteSection({ section_id: section.id });
                const index = this.sectionsWithFunctionalities.findIndex(sec => sec.id === section.id);
                if (index !== -1) this.sectionsWithFunctionalities.splice(index, 1);
                if (result) {
                    this.resetForm();
                }
                showToast(message, 'success');
            } catch (error) {
                this.handleError(error);
            }
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
        checkMoveFunctionality(e) {
            this.moveFunctionality = e.draggedContext.element;
            this.moveFunctionality.order_no = e.draggedContext.futureIndex + 1;
        },

        async functionalityOnDragEnd(event) {
            this.drag = false;
            try {
                const response = await SolutionDesignService.updateFunctionalityOrderNo(this.moveFunctionality);
                showToast(response.message, 'success');
            } catch (error) {
                this.handleError(error);
            }
        },
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
