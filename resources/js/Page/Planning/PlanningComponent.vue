<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('planning.page_title') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content" id="timeBookingPageSection">
        <div class="w-100">
            <!-- <div class="scrolling outer"> -->
            <!-- <div class="inner"> -->
            <table border="0" cellspacing="0" cellpadding="0">
                <tr class="header_row bg-transparent">
                    <th scope="col" class="border abs1 bg-transparent text-left text-white align-middle p-1"
                        style="height: 45px;">
                        <multiselect v-model="filter.initiative_id" :options="initiativesFilterList"
                            :placeholder="$t('create_ticket_modal_select_functionality_placeholder')" label="name"
                            track-by="id">
                        </multiselect>
                    </th>
                    <th scope="col" class="border abs2 bg-transparent text-left text-white align-middle p-1"
                        style="height: 45px;">
                        <multiselect v-model="filter.ticket_id" :options="usersFilterList"
                            :placeholder="$t('create_ticket_modal_select_functionality_placeholder')" label="name"
                            track-by="id">
                        </multiselect>
                    </th>
                    <th scope="col" class="border abs3 bg-dark text-center align-middle p-1" style="height: 45px;">
                        <a class="text-white" href="javascript:void(0);" @click="getPlanningData(-1)">
                            <i class="bi bi-caret-left"></i>
                        </a>
                    </th>
                    <td scope="col" class="border text-center align-middle p-1"
                        :class="week.is_current_week ? 'bg-black' : 'bg-desino'" v-for="(week, index) in loadWeeks"
                        :key="index" style="height: 45px;">
                        <small class="small text-white" style="font-size: 0.8rem;">
                            {{ week.display_week_name }}
                        </small>
                    </td>
                    <th scope="col" class="border abs4 bg-dark text-center align-middle p-1" style="height: 45px;">
                        <a class="text-white" href="javascript:void(0);" @click="getPlanningData(1)">
                            <i class="bi bi-caret-right"></i>
                        </a>
                    </th>
                </tr>
                <template v-for="(planning, planningIndex) in plannings" :key="planningIndex">
                    <tr v-for="(user, userIndex) in planning.users" :key="userIndex">
                        <!-- below th for total and plan new initiative -->
                        <th v-if="planning.default_row_name == 'heder_total' || planning.default_row_name == 'plan_new_initiative'"
                            class="border total_abs1 bg-opacity-25 text-center align-middle p-1" :class="{
                                'bg-primary': planning.default_row_name == 'heder_total',
                                'bg-warning': planning.default_row_name == 'plan_new_initiative'
                            }" colspan="2" :rowspan="planning.users.length">
                            {{ planning.initiative_name }}
                        </th>
                        <!-- below th except for total and plan new initiative -->
                        <th v-if="planning.default_row_name == '' && userIndex == 0"
                            class="border total_abs1 bg-opacity-25 text-center align-middle p-1"
                            :rowspan="planning.users.length">
                            {{ planning.initiative_name }}
                        </th>
                        <!-- below td for plan new user -->
                        <th v-if="planning.default_row_name == ''" class="border abs1 text-left p-1">
                            {{ user.name }}
                        </th>
                        <!-- below td previous week -->
                        <td></td>
                        <!-- below td for each week header total -->
                        <td v-if="planning.default_row_name == 'heder_total'"
                            class="text-center align-middle p-1 border" v-for="(week, index) in loadWeeks" :key="index">
                            <span class="badge text-white bg-secondary">
                                {{ user.hours_per_week[week.date].hours > 0 ? user.hours_per_week[week.date].hours :
                                    '' }}
                            </span>
                        </td>
                        <!-- below td except for each week header total -->
                        <td v-if="planning.default_row_name == ''" class="text-center align-middle p-1 border"
                            v-for="(week, index) in loadWeeks" :key="index">
                            <span class="">
                                {{ user.hours_per_week[week.date].hours > 0 ? user.hours_per_week[week.date].hours :
                                    '' }}
                            </span>
                        </td>
                    </tr>
                </template>
            </table>
            <!-- </div> -->
            <!-- </div> -->
        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect';
import { mapActions } from 'vuex';
import GlobalMessage from '../../components/GlobalMessage.vue';
import messageService from '../../services/messageService';
import PlanningService from '../../services/PlanningService';


export default {
    name: 'PlanningComponent',
    components: {
        GlobalMessage,
        Multiselect
    },
    data() {
        return {
            filter: {
                initiative_id: "",
            },
            initiativesFilterList: [],
            usersFilterList: [],
            loadWeeks: [],
            plannings: [],
            errors: {},
            showMessage: false
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async getPlanningInitialData() {
            try {
                const { content: { initiatives, users } } = await PlanningService.getPlanningInitialData();
                this.initiativesFilterList = initiatives;
                this.usersFilterList = users;
            } catch (error) {
                this.handleError(error);
            }
            this.setLoading(false);
        },
        async getPlanningData(number = 0) {
            this.clearMessages();
            try {
                this.setLoading(true);
                const passData = {
                    previous_or_next_of_week: number,
                    start_date: this.loadWeeks[0]?.date,
                    end_date: this.loadWeeks[this.loadWeeks.length - 1]?.date,
                }
                const { content: { plannings, loadWeeks } } = await PlanningService.getPlanningData(passData);
                this.plannings = plannings;
                this.loadWeeks = loadWeeks;
                this.setLoading(false);
                return content;
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
    },
    mounted() {
        this.clearMessages();
        this.getPlanningInitialData();
        this.getPlanningData();
    },

};
</script>