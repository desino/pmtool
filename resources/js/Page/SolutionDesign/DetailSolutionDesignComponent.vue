<template>
    <GlobalMessage v-if="showMessage" />
    {{ currentInitiative?.name }}
    <div class="app-content row position-relative">
        <div class="col-md-4 border-end border-bottom sticky top-0 d-none d-lg-block">
            <div class="input-group sticky-top pt-3 pb-1 bg-white">
                <input v-model="solutionDesignFilters.name" aria-label="Search" class="form-control"
                    placeholder="Search" type="text" @keyup="getSectionsWithFunctionalities">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
            </div>
            <hr>
            <div v-for="section in sectionsWithFunctionalities" :key="section.id">
                <div class="section-functionality-container ps-3">
                    <div class="mt-3 section-container">
                        <div class="d-flex align-items-center section-container">
                            <div class="fw-bold fs-5">
                                <i :aria-controls="'collapse_' + section.id"
                                    :aria-expanded="!collapsedSections[section.id]"
                                    :class="['bi', collapsedSections[section.id] ? 'bi-caret-right-fill' : 'bi-caret-down-fill']"
                                    :data-bs-target="'#collapse_' + section.id" data-bs-toggle="collapse"
                                    @click="collapsedSections[section.id] = !collapsedSections[section.id]"></i>
                                <a href="javascript:void(0)" @click="scrollToSection(section.id)"
                                    class="text-decoration-none text-dark">{{ section.display_name }}</a>
                            </div>
                        </div>
                    </div>
                    <div v-if="section.functionalities" :id="'collapse_' + section.id"
                        :class="{ 'show': !collapsedSections[section.id] }" class="list-group collapse">
                        <div v-for="functionality in section.functionalities" :key="functionality.id">
                            <div :class="['list-group-item d-flex list-group-item-action', { 'bg-desino text-light': isSelected(functionality.id) }]"
                                class="border-0 border-bottom" role="button"
                                @click="scrollToFunctionality(section.id, functionality.id)">
                                <span>{{ functionality.display_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 border-start border-bottom optimize-image">
            <div class="p-3">
                <div v-for="section in sectionsWithFunctionalities" :key="section.id" :id="'section_' + section.id"
                    class="mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-desino text-white">
                            <h5 class="mb-0">{{ section.display_name }}</h5>
                        </div>
                        <div class="card-body">
                            <ul v-if="section.functionalities.length > 0" class="list-group list-group-flush">
                                <li v-for="functionality in section.functionalities" :key="functionality.id"
                                    :id="'functionality_' + functionality.id" class="list-group-item">
                                    <h6 class="fw-semibold text-dark">{{ functionality.display_name }}</h6>
                                    <p class="text-muted text-break mw-100" v-html="functionality.description"></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import GlobalMessage from './../../components/GlobalMessage.vue';
import messageService from './../../services/messageService';
import SolutionDesignService from './../../services/SolutionDesignService';
import eventBus from '../../eventBus';
import draggable from 'vuedraggable';
import { mapActions } from 'vuex';
import store from '../../store';

export default {
    name: 'SolutionDesignComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        draggable
    },
    data() {
        return {
            collapsedSections: {}, // for collapse and expand
            initiativeId: this.$route.params.id,
            initiativeData: {},
            selectedFunctionalityId: "",
            sectionsWithFunctionalities: [],
            solutionDesignFilters: {
                name: '',
            },
            showMessage: true,
        };
    },
    methods: {
        ...mapActions(['setLoading', 'currentInitiative']),
        async fetchData() {
            try {
                this.setLoading(true);
                const sectionsWithFunctionalitiesPromise = this.getSectionsWithFunctionalities();
                await sectionsWithFunctionalitiesPromise;
                this.getInitiativeData();
                eventBus.$emit('selectHeaderInitiativeId', this.initiativeId)
                this.setLoading(false);
                this.clearMessages();
            } catch (error) {
                this.handleError(error);
            }
        },
        async getInitiativeData() {
            try {
                const setHeaderData = {
                    page_title: this.$t('solution_design.page_title') + ' - ' + this.initiativeData?.name,
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
                const { content, meta_data: { initiative } } = await SolutionDesignService.getSectionsWithFunctionalitiesForRead(passData);
                this.sectionsWithFunctionalities = content;
                this.initiativeData = initiative;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        objectInValueExistOrNot(obj) {
            const hasValue = Object.values(obj).some(value => value);
            return hasValue;
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
        isSelected(functionalityId) {
            return this.selectedFunctionalityId === functionalityId;
        },
        scrollToSection(sectionId) {
            const element = document.getElementById(`section_${sectionId}`);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        },
        scrollToFunctionality(sectionId, functionalityId) {
            const element = document.getElementById(`functionality_${functionalityId}`);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
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
