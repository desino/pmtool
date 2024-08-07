<template>
    <div class="vh-100 d-flex align-items-center justify-content-center">
        <div class="login-container col-12 col-md-8 col-lg-4">
            <GlobalMessage v-if="showMessage" />
            <div class="card w-100">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $t('reset_password_page_title') }}</h5>
                    <form @submit.prevent="resetPassword">
                        <input type="hidden" v-model="token" />
                        <input type="hidden" v-model="email" />
                        <div class="mb-3">
                            <input v-model="password" :class="{'is-invalid': errors.password}" class="form-control" :placeholder="$t('reset_password_input_password')" type="password">
                            <div v-if="errors.password" class="invalid-feedback">
                                <span v-for="(error, index) in errors.password" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input v-model="password_confirmation" :class="{'is-invalid': errors.password_confirmation}" class="form-control" :placeholder="$t('reset_password_input_password_confirmation')" type="password">
                            <div v-if="errors.password_confirmation" class="invalid-feedback">
                                <span v-for="(error, index) in errors.password_confirmation" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-desino w-100">{{ $t('reset_password_submit_but_text') }}</button>
                        <p class="mb-1">
                            <router-link class="text-decoration-none" :to="{ name: 'login' }">
                                {{ $t('reset_password_back_to_login_but_text') }}
                            </router-link>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AuthService from "../../services/AuthService.js";
import globalMixin from '@/globalMixin';
import messageService from './../../services/messageService.js';
import GlobalMessage from './../../components/GlobalMessage.vue';
import { useRoute } from "vue-router";

export default {
    name: 'LoginComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage
    },
    data() {
        return {
            token: '',
            email: '',
            password: '',
            password_confirmation: '',
            errors: {},
            showMessage: true
        };
    },
    setup() {
        const route = useRoute();
        return { route };
    },
    methods: {
        async resetPassword() {
            this.clearMessages();
            try {
                const credentials = {
                    token: this.token,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                };
                let response = await AuthService.resetPassword(credentials);
                if(response.data.status){
                    messageService.setMessage(response.data.message, 'success');
                    this.$router.push({ name: 'login' });
                } else {
                    messageService.setMessage(response.data.message, 'danger');
                }
            } catch (error) {
                this.handleLoginError(error);
            }
        },
        handleLoginError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger');
            }
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
    },
    mounted() {
        this.token = this.route.params.token;
        this.email = this.route.params.email;
    },
    beforeUnmount() {
        // Hide the message when the component is unmounted
        this.showMessage = false;
    }
}
</script>
