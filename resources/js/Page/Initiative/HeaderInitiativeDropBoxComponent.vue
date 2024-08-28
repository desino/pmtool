<template>
    <select class="form-select form-select-sm" aria-label="Default select example" @change="navigate"
        v-model="selected_initiative_id">
        <option value="">{{ $t('header.initiative_list.placeholder') }}</option>
        <option v-if="initiatives.length > 0" v-for="initiative in initiatives" :key="initiative.id"
            :value="initiative.id">{{ initiative.client.name }} - {{ initiative.name }}</option>
    </select>
</template>

<script>
import eventBus from '../../eventBus';
import HeaderService from '../../services/HeaderService';
import { mapActions } from "vuex";
import store from "../../store/index.js";

export default {
    name: 'HeaderInitiativeDropBoxComponent',
    data() {
        return {
            initiatives: [],
            selected_initiative_id: this.$route.params.initiative_id ?? this.$route.params.id,
        }
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
            // eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        handleAppendHeaderInitiativeSelectBox(data) {
            this.initiatives.push(data.initiative);
            this.selected_initiative_id = data.initiative.id;
            // eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        handleUnselectHeaderInitiativeId() {
            this.selected_initiative_id = "";
            // eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        selectHeaderInitiativeId(initiativeId) {
            this.selected_initiative_id = initiativeId;
            // eventBus.$emit('sidebarSelectHeaderInitiativeId', this.selected_initiative_id);
        },
        navigate(event) {
            const initiativeId = event.target.value;
            store.commit("setCurrentInitiative", { id: initiativeId });
            eventBus.$emit('sidebarSelectHeaderInitiativeId', initiativeId);
            if (initiativeId) {
                this.$router.push({ name: 'solution-design', params: { id: initiativeId } });
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
    },
}
</script>
