<template>
    <!-- <select class="form-select form-select-sm" aria-label="Default select example" @change="navigateOld($event, user)"
        v-model="selected_initiative_id">
        <option value="">{{ $t('header.initiative_list.placeholder') }}</option>
        <option v-if="initiatives.length > 0" v-for="initiative in initiatives" :key="initiative.id"
            :value="initiative.id">{{ initiative.client.name }} - {{ initiative.name }}</option>
    </select> -->

    <multiselect v-model="initiative" :options="initiatives" :multiple="false" label="client_initiative_name"
        :placeholder="$t('header.initiative_list.placeholder')" track-by="id" @select="navigate($event, user)"
        :allow-empty="false" select-label="" deselect-label="">
        <template #option="{ option }">
            <div class="custom-option">
                {{ option.client_initiative_name }}
            </div>
        </template>
    </multiselect>
</template>

<script>
import eventBus from '../../eventBus';
import HeaderService from '../../services/HeaderService';
import { mapActions, mapGetters } from "vuex";
import store from "../../store/index.js";
import OpportunityService from '../../services/OpportunityService.js';
import globalMixin from '@/globalMixin';
import Multiselect from "vue-multiselect";

export default {
    name: 'HeaderInitiativeDropBoxComponent',
    mixins: [globalMixin],
    components: {
        Multiselect
    },
    data() {
        return {
            initiative: "",
            initiatives: [],
            selected_initiative_id: this.$route.params.initiative_id ?? this.$route.params.id,
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setCurrentInitiative']),
        async getInitiativeWithClientData() {
            const response = await HeaderService.getInitiatives();
            this.initiatives = response.content;
            this.selected_initiative_id = this.$route.params.id ?? this.$route.params.initiative_id;
            if (this.selected_initiative_id === undefined) {
                this.selected_initiative_id = "";
                this.initiative = "";
            } else {
                this.initiative = this.initiatives.find(initiative => initiative.id == this.selected_initiative_id);
            }
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        handleAppendHeaderInitiativeSelectBox(data) {
            this.initiatives.push(data.initiative);
            this.selected_initiative_id = data.initiative.id;
            this.initiative = this.initiatives.find(initiative => initiative.id == this.selected_initiative_id);
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        handleUnselectHeaderInitiativeId() {
            this.selected_initiative_id = "";
            this.initiative = "";
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        selectHeaderInitiativeId(initiativeId) {
            this.selected_initiative_id = initiativeId;
            this.initiative = this.initiatives.find(initiative => initiative.id == this.selected_initiative_id);
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        async navigateOld(event, userData) {
            const initiativeId = event.target.value;
            const currentInitiative = this.initiatives.find(initiative => initiative.id == initiativeId);
            store.commit("setCurrentInitiative", currentInitiative);
            eventBus.$emit('sidebarSelectHeaderInitiativeId', initiativeId);

            const routesWithInitiativeId = this.appVariables.ROUTES_NAME_WITH_INITIATIVE_ID;
            if (routesWithInitiativeId.includes(this.$route.name)) {
                if (this.$route.name == 'task.detail' && userData.is_admin) {
                    this.$router.push({ name: 'tasks', params: { id: initiativeId } });
                } else if (this.$route.name == 'my-tickets' && !userData.is_admin) {
                    this.$router.push({ name: 'my-tickets', params: { id: initiativeId } });
                } else {
                    this.$router.push({ name: this.$route.name, params: { id: initiativeId } });
                }
            } else if (initiativeId && userData.is_admin) {
                this.$router.push({ name: 'solution-design', params: { id: initiativeId } });
            } else if (initiativeId && !userData.is_admin) {
                this.$router.push({ name: 'solution-design.detail', params: { id: initiativeId } });
            } else {
                this.$router.push({ name: 'opportunities' });
            }
        },
        async navigate(event, userData) {
            const initiativeId = event.id;
            const currentInitiative = this.initiatives.find(initiative => initiative.id == initiativeId);
            store.commit("setCurrentInitiative", currentInitiative);
            eventBus.$emit('sidebarSelectHeaderInitiativeId', initiativeId);

            const routesWithInitiativeId = this.appVariables.ROUTES_NAME_WITH_INITIATIVE_ID;
            if (routesWithInitiativeId.includes(this.$route.name)) {
                if (this.$route.name == 'task.detail' && userData.is_admin) {
                    this.$router.push({ name: 'tasks', params: { id: initiativeId } });
                } else if (this.$route.name == 'my-tickets' && !userData.is_admin) {
                    this.$router.push({ name: 'my-tickets', params: { id: initiativeId } });
                } else {
                    this.$router.push({ name: this.$route.name, params: { id: initiativeId } });
                }
            } else if (initiativeId && userData.is_admin) {
                this.$router.push({ name: 'solution-design', params: { id: initiativeId } });
            } else if (initiativeId && !userData.is_admin) {
                this.$router.push({ name: 'solution-design.detail', params: { id: initiativeId } });
            } else {
                this.$router.push({ name: 'opportunities' });
            }
        },
        // nameWithClient({ name, client }) {
        //     return `${client?.name} - ${name}`
        // }        
    },
    mounted() {
        this.getInitiativeWithClientData();
        eventBus.$on('appendHeaderInitiativeSelectBox', this.handleAppendHeaderInitiativeSelectBox);
        eventBus.$on('unselectHeaderInitiativeId', this.handleUnselectHeaderInitiativeId);
        eventBus.$on('selectHeaderInitiativeId', this.selectHeaderInitiativeId);
        eventBus.$on('sidebarSelectInitiativeUpdate', this.getInitiativeWithClientData);
    },
}
</script>
<style>
.custom-option {
    white-space: normal;
    word-break: break-word;
    padding: 2px;
    min-height: 15px;
}
</style>
