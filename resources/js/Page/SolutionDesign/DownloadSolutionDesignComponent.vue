<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content position-relative">
        <div class="row w-100 mb-3 mt-3">
            <div class="col-md-10">
                <div class="form-check">
                    <input v-model="downloadFilters.include_in_solution_design" class="form-check-input" type="checkbox"
                        id="include_in_solution_design" @change="getSectionsWithFunctionalities">
                    <label class="form-check-label fw-bold" for="include_in_solution_design">
                        {{ $t('solution_design.functionality_form.include_in_solution_design_text') }}
                    </label>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-desino" @click="downloadPDF" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    :title="$t('solution_design_download.but_title_text')">
                    <i class="bi bi-download"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="app-content row position-relative">
        <div class="col-md-12 border-top border-bottom sticky top-0 d-none d-lg-block">
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
                                <a href="javascript:void(0)" class="text-decoration-none text-dark">{{
                                    section.display_name }}</a>
                            </div>
                        </div>
                    </div>
                    <div v-if="section.functionalities" :id="'collapse_' + section.id"
                        :class="{ 'show': !collapsedSections[section.id] }" class="list-group collapse">
                        <div v-for="functionality in section.functionalities" :key="functionality.id">
                            <div :class="['list-group-item d-flex list-group-item-action', { 'bg-desino text-light': isSelected(functionality.id) }]"
                                class="border-0 border-bottom" role="button"
                                @click="selectFunctionality(functionality)">
                                <span>{{ functionality.display_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import globalMixin from '@/globalMixin';
import GlobalMessage from './../../components/GlobalMessage.vue';
import SolutionDesignService from './../../services/SolutionDesignService';
import eventBus from '../../eventBus';
import { mapActions } from 'vuex';
import messageService from '../../services/messageService';
import store from '../../store';
import { Modal, Tooltip } from 'bootstrap';
export default {
    name: 'DownloadSolutionDesignComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
    },
    data() {
        return {
            collapsedSections: {},
            initiativeId: this.$route.params.id,
            initiativeData: {},
            sectionsWithFunctionalities: [],
            showMessage: true,
            selectedFunctionalityId: "",
            downloadFilters: {
                include_in_solution_design: false,
                name: ""
            }
        };
    },
    computed: {
        ...mapGetters(['user', 'currentInitiative']),
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
                    const setHeaderData = {
                        page_title: this.$t('solution_design_download.page_title') + ' - ' + this.initiativeData?.name,
                    }
                    store.commit("setHeaderData", setHeaderData);
                }
            } catch (error) {
                this.handleError(error);
            }
        },
        async getSectionsWithFunctionalities() {
            try {
                const hasValue = this.objectInValueExistOrNot(this.downloadFilters);
                hasValue ?? this.setLoading(true);
                this.downloadFilters.initiative_id = this.initiativeId;
                const { content } = await SolutionDesignService.getSectionsWithFunctionalitiesForDownloadList(this.downloadFilters);
                this.sectionsWithFunctionalities = content;
                hasValue ?? this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        isSelected(functionalityId) {
            return this.selectedFunctionalityId === functionalityId;
        },
        selectFunctionality(functionality) {
            if (this.selectedFunctionalityId === functionality.id) {
                this.selectedFunctionalityId = null;
            } else {
                this.selectedFunctionalityId = functionality.id;
            }
        },
        async downloadPDF() {
            this.clearMessages();
            try {
                this.setLoading(true);
                this.downloadFilters.initiative_id = this.initiativeId;
                const response = await SolutionDesignService.getSectionsWithFunctionalitiesForDownloadPDF(this.downloadFilters);

                const blob = new Blob([response.data], { type: 'application/pdf' });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = this.$t('solution_design_download.page_download_text') + '- ' + this.currentInitiative?.name + '.pdf';
                link.click();
                this.setLoading(false);
            } catch (error) {
                error.message = this.$t('solution_design_download.error_message');
                this.handleError(error);
            }
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
    },
    mounted() {
        this.fetchData();
        this.initializeTooltips();
    },
    beforeRouteUpdate(to, from, next) {
        this.initiativeId = to.params.id;
        this.resetForm();
        this.fetchData();
        next();
    },
}
</script>