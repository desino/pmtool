<template>
    <div class="vh-100 d-flex align-items-center justify-content-center">
        <div class="login-container col-12 col-md-8 col-lg-4">
            <GlobalMessage v-if="showMessage" />
            <div class="card w-100">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $t('login_page_title') }}</h5>
                    <Office365LoginComponent v-if="appVariables.ENABLE_OFFICE_365_LOGIN" />

                    <form @submit.prevent="loginUser" class="mt-10" v-if="appVariables.ENABLE_MANUAL_LOGIN">
                        <div class="input-group mb-3">
                            <input v-model="email" :class="{ 'is-invalid': errors.email }" class="form-control"
                                :placeholder="$t('login_input_email')" type="email">
                            <div class="input-group-text">
                                <span class="bi bi-envelope-at-fill"></span>
                            </div>
                            <div v-if="errors.email" class="invalid-feedback">
                                <span v-for="(error, index) in errors.email" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input v-model="password" :class="{ 'is-invalid': errors.password }" class="form-control"
                                :placeholder="$t('login_input_password')" type="password">
                            <div class="input-group-text">
                                <span class="bi bi-person-fill-lock"></span>
                            </div>
                            <div v-if="errors.password" class="invalid-feedback">
                                <span v-for="(error, index) in errors.password" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-desino w-100 bg-desino text-light">{{
                            $t('login_submit_but_text') }}</button>
                        <p class="mb-1">
                            <router-link class="text-decoration-none" :to="{ name: 'forgot-password' }">
                                {{ $t('login_forgot_password_link') }}
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
import Office365LoginComponent from "./Office365LoginComponent.vue";

export default {
    name: 'LoginComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        Office365LoginComponent
    },
    data() {
        return {
            email: '',
            password: '',
            errors: {},
            showMessage: true
        };
    },
    methods: {
        async loginUser() {
            this.clearMessages();
            try {
                const credentials = {
                    email: this.email,
                    password: this.password
                };
                await AuthService.loginUser(credentials);
                this.$router.push({ name: 'home' });
            } catch (error) {
                this.handleLoginError(error);
            }
        },
        async getProviderCallbackSessionData() {
            this.clearMessages();
            try {
                const response = await AuthService.getProviderCallbackSessionData();
                const data = response.data.content;
                if (data.token != null) {
                    this.$router.push({ name: 'home' });
                } else {
                    messageService.setMessage(data.message, data.message_class);
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
        this.clearMessages();
        this.getProviderCallbackSessionData();
    },
    beforeUnmount() {
        // Hide the message when the component is unmounted
        this.showMessage = false;
    }
}
</script>
