<template>
    <select class="form-select form-select-sm" aria-label="Default select example" @change="navigate($event, user)"
        v-model="selected_initiative_id">
        <option value="">{{ $t('header.initiative_list.placeholder') }}</option>
        <option v-if="initiatives.length > 0" v-for="initiative in initiatives" :key="initiative.id"
            :value="initiative.id">{{ initiative.client.name }} - {{ initiative.name }}</option>
    </select>
</template>

<script>
import eventBus from '../../eventBus';
import HeaderService from '../../services/HeaderService';
import { mapActions, mapGetters } from "vuex";
import store from "../../store/index.js";
import OpportunityService from '../../services/OpportunityService.js';

export default {
    name: 'HeaderInitiativeDropBoxComponent',
    data() {
        return {
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
            }
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        handleAppendHeaderInitiativeSelectBox(data) {
            this.initiatives.push(data.initiative);
            this.selected_initiative_id = data.initiative.id;
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        handleUnselectHeaderInitiativeId() {
            this.selected_initiative_id = "";
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        selectHeaderInitiativeId(initiativeId) {
            this.selected_initiative_id = initiativeId;
            eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        async navigate(event, userData) {
            const initiativeId = event.target.value;
            const currentInitiative = this.initiatives.find(initiative => initiative.id == initiativeId)
            store.commit("setCurrentInitiative", currentInitiative);
            eventBus.$emit('sidebarSelectHeaderInitiativeId', initiativeId);
            if (initiativeId && userData.is_admin) {
                this.$router.push({ name: 'solution-design', params: { id: initiativeId } });
            } else if (initiativeId && !userData.is_admin) {
                this.$router.push({ name: 'solution-design.detail', params: { id: initiativeId } });
            } else {
                this.$router.push({ name: 'opportunities' });
            }
        }
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
