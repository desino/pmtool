<template>
    <div class="card">
        <div class="card-header bg-desino text-white text-center">
            {{ $t('home.my_actions.title') }}
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li v-if="initiatives.length > 0" v-for="initiative in initiatives" :key="initiative.id"
                    class="list-group-item" role="button" @click="openMyTickets(initiative)">
                    <!-- bg-warning rounded -->
                    <div class="row w-100" :class="{
                        'bg-warning rounded p-1': initiative?.is_priority_tickets_count > 0
                    }">
                        <div class="col-md-8 fw-bold">
                            {{ initiative?.client?.name }} -
                            {{ initiative.name }}
                        </div>
                        <div class="col-md-4 text-end mt-2">
                            <h6>
                                <div class="badge bg-desino">
                                    {{ initiative?.tickets_count }}
                                    <span class="small">{{
                                        $t('home.my_actions.tickets.text')
                                    }}</span>
                                </div>
                            </h6>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
import showToast from '../../utils/toasts';
import messageService from '../../services/messageService';
import HomeMyActionsService from '../../services/Home/HomeMyActionsService';

export default {
    name: 'MyActionsComponent',
    components: {
    },
    data() {
        return {
            initiatives: [],
            errors: {},
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getMyActionsData() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const { content: { initiatives } } = await HomeMyActionsService.getMyActionsData();
                this.initiatives = initiatives;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        openMyTickets(initiative) {
            this.$router.push({ name: 'my-tickets', params: { id: initiative.id } });
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
        this.clearMessages();
        this.getMyActionsData();
    }
}
</script>