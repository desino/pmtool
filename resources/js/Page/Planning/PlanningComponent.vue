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
                            }" colspan="2" :rowspan="planning.users.length"
                            :role="planning.default_row_name == 'plan_new_initiative' ? 'button' : ''"
                            @click="handlePlanNewInitiative(planning, user)">
                            {{ planning.initiative_name }}
                        </th>
                        <!-- below th except for total and plan new initiative -->
                        <th v-if="planning.default_row_name == '' && userIndex == 0"
                            class="border total_abs1 bg-opacity-25 text-center align-middle p-1"
                            :rowspan="planning.users.length">
                            {{ planning.initiative_name }}
                        </th>
                        <!-- below td for plan new user -->
                        <th v-if="planning.default_row_name == '' && user.id != ''" class="border abs1 text-left p-1">
                            {{ user.name }}
                        </th>
                        <th v-if="planning.default_row_name == '' && user.id == ''"
                            class="border abs1 text-left p-1 bg-info text-white text-center" role="button"
                            v-html="user.name" @click="handlePlanNewUser(planning)">
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
                        <td v-if="planning.default_row_name == ''" class="border text-center align-middle p-1 border"
                            v-for="(week, index) in loadWeeks" :key="index">
                            <!-- <input v-if="user.id != ''" type="text" v-model="user.hours_per_week[week.date].hours"
                                class="form-control form-control-sm text-center"
                                :placeholder="$t('time_booking_on_new_initiative_or_ticket.modal_input_hours_label_text')"> -->
                            <input v-if="user.id != ''" type="text" v-model="user.hours_per_week[week.date].hours"
                                class="form-control form-control-sm text-center"
                                :placeholder="$t('time_booking_on_new_initiative_or_ticket.modal_input_hours_label_text')">
                        </td>
                    </tr>
                </template>
            </table>
            <!-- </div> -->
            <!-- </div> -->
        </div>
    </div>
    <div class="container-fluid mt-3">
        <div class="row w-100 mx-0">
            <button class="btn btn-desino text-uppercase" @click="storePlanning">{{
                $t('planning.store_button_text')
                }}</button>
        </div>
    </div>

    <div id="planNewInitiativeModal" aria-hidden="true" aria-labelledby="planNewInitiativeModalLabel" class="modal fade"
        tabindex="-1">
        <PlanNewInitiativeModalComponent ref="planNewInitiativeModalComponent" @pageUpdated="getPlanningData"
            @add-plan-new-initiative="addPlanNewInitiativeFromModal" />
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect';
import { mapActions } from 'vuex';
import GlobalMessage from '../../components/GlobalMessage.vue';
import messageService from '../../services/messageService';
import PlanningService from '../../services/PlanningService';
import PlanNewInitiativeModalComponent from './PlanNewInitiativeModalComponent.vue';
import { Modal } from 'bootstrap';
import showToast from '../../utils/toasts';

export default {
    name: 'PlanningComponent',
    components: {
        GlobalMessage,
        Multiselect,
        PlanNewInitiativeModalComponent
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
            passData: [],
            errors: {},
            showMessage: true
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
            } catch (error) {
                this.handleError(error);
            }
        },
        handlePlanNewInitiative(planning, user) {
            if (planning.default_row_name != 'plan_new_initiative') {
                return;
            }
            const existingPlannings = this.plannings.filter(item => item.default_row_name == "");
            const planNewInitiativeModalElement = document.getElementById('planNewInitiativeModal');
            if (planNewInitiativeModalElement) {
                this.$refs.planNewInitiativeModalComponent.getPlanNewInitiativeForOpenModalData(existingPlannings);
                const planNewInitiativeModal = new Modal(planNewInitiativeModalElement);
                planNewInitiativeModal.show();
            }
        },
        handlePlanNewUser(planning) {
            console.log('planning :: ', planning.users.filter(item => item.id != ''));

        },
        addPlanNewInitiativeFromModal(formData) {
            let addNewPlaning = {
                'default_row_name': '',
                'initiative_id': formData.initiative_id,
                'initiative_name': formData.initiative_name,
                'users': [],
            }
            const hoursPerWeek = [];
            this.loadWeeks.forEach(week => {
                hoursPerWeek[week.date] = {
                    'hours': 0
                }
            });
            addNewPlaning.users.push({
                'id': formData.user_id,
                'name': formData.user_name,
                'hours_per_week': hoursPerWeek,
            })
            this.plannings.splice(this.plannings.length - 1, 0, addNewPlaning);
        },
        async storePlanning() {
            const passData = [];
            this.plannings.forEach(planning => {
                if (planning.default_row_name == '') {
                    const passInitiativeData = {
                        'initiative_id': '',
                        'initiative_name': '',
                        'user_id': '',
                        'user_name': '',
                        'hours_per_week': [],
                    }
                    planning.users.forEach(user => {
                        this.loadWeeks.forEach(week => {
                            if (user.hours_per_week[week.date].hours > 0) {
                                passInitiativeData.initiative_id = planning.initiative_id;
                                passInitiativeData.initiative_name = planning.initiative_name;
                                passInitiativeData.user_id = user.id;
                                passInitiativeData.user_name = user.name;
                                passInitiativeData.hours_per_week.push({
                                    'date': week.date,
                                    'hours': user.hours_per_week[week.date].hours
                                });
                            }
                        });
                    });
                    passData.push(passInitiativeData);
                }
            })
            if (passData.length > 0) {
                this.setLoading(true);
                try {
                    const { message } = await PlanningService.storePlanning(passData);
                    showToast(message, 'success');
                    this.setLoading(false);
                    this.getPlanningData();
                } catch (error) {
                    this.handleError(error);
                }
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