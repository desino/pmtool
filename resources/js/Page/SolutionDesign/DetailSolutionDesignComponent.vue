<template>
    <div class="app-content-header pb-0">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('solution_design.page_title') }} - {{ initiativeData.name }}</h3>
                    <h5>
                        <span class="badge rounded bg-desino text-light my-3">
                            Development Ballpark: {{ initiativeData.ballpark_development_hours }} hours
                        </span>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <GlobalMessage v-if="showMessage" />

    <div class="app-content row position-relative">
        <div class="col-md-4 border-top border-bottom sticky top-0 d-none d-lg-block">
            <div class="input-group sticky-top pt-3 pb-1 bg-white">
                <input aria-label="Search" class="form-control" placeholder="Search" type="text">
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

        <div class="col-md-8 border-start border-bottom border-top">
            <section class="p-2 border-bottom section_detail" v-for="section in sectionsWithFunctionalities"
                :key="section.id" :id="'section_' + section.id">
                <h5 class="mb-2 text-primary"># {{ section.display_name }}</h5>
                <div v-if="section.functionalities.length > 0" class="px-4"
                    v-for="functionality in section.functionalities" :key="functionality.id"
                    :id="'functionality_' + functionality.id">
                    <h6># {{ functionality.display_name }}</h6>

                    <p class="ps-3 text-break mw-100" v-html="functionality.description">
                    </p>
                </div>
            </section>
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
            showMessage: true,
        };
    },
    methods: {
        ...mapActions(['setLoading']),
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
                this.setLoading(true);
                const { content } = await SolutionDesignService.getSectionsWithFunctionalities({ initiative_id: this.initiativeId });
                this.sectionsWithFunctionalities = content;
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
        this.resetForm();
        this.fetchData();
        next();
    },
}
</script>
