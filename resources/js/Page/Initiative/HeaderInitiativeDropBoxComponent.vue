<template>
    <select class="form-select form-select-sm" aria-label="Default select example">
        <option value="">{{ $t('header.initiative_list.placeholder') }}</option>
        <option v-if="initiatives.length > 0" v-for="initiative in initiatives" :key="initiative.id"
            value="{{ initiative.id }}">{{ initiative.client.name }} - {{ initiative.name }}</option>
    </select>
</template>

<script>
import eventBus from '../../eventBus';
import HeaderService from '../../services/HeaderService';

export default {
    name: 'HeaderInitiativeDropBoxComponent',
    data() {
        return {
            initiatives: []
        }
    },
    methods: {
        async getInitiativeWithClienData() {
            const response = await HeaderService.getInitiatives();
            this.initiatives = response.content;
        },
        handleAppendHeaderInitiativeSelectBox(data) {
            this.initiatives.push(data.initiative);
        }
    },
    mounted() {
        this.getInitiativeWithClienData();
        eventBus.$on('appendHeaderInitiativeSelectBox', this.handleAppendHeaderInitiativeSelectBox);
    },
    beforeUnmount() {
        // eventBus.$off('appendHeaderInitiativeSelectBox', this.handleAppendHeaderInitiativeSelectBox);
    }
}
</script>
