<template>
    <div class="vh-100 d-flex align-items-center justify-content-center">
        <div class="login-container col-12 col-md-8 col-lg-4">
            <GlobalMessage v-if="showMessage" />
            <div class="card w-100">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $t('auth.login.title') }}</h5>
                    <form @submit.prevent="loginUser">
                        <div class="input-group mb-3">
                            <input v-model="email" :class="{'is-invalid': errors.email}" class="form-control" :placeholder="$t('auth.login.email')" type="email">
                            <div class="input-group-text">
                                <span class="bi bi-envelope-at-fill"></span>
                            </div>
                            <div v-if="errors.email" class="invalid-feedback">
                                <span v-for="(error, index) in errors.email" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input v-model="password" :class="{'is-invalid': errors.password}" class="form-control" :placeholder="$t('auth.login.password')" type="password">
                            <div class="input-group-text">
                                <span class="bi bi-person-fill-lock"></span>
                            </div>
                            <div v-if="errors.password" class="invalid-feedback">
                                <span v-for="(error, index) in errors.password" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">{{ $t('auth.login.submit') }}</button>
                        <p class="mb-1">
                            <router-link class="text-decoration-none" :to="{ name: 'forgot-password' }">
                                {{ $t('auth.login.forgot_password_link') }}
                            </router-link>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AuthService from "../../Services/AuthService.js";
import globalMixin from '@/globalMixin';
import messageService from './../../Services/messageService.js';
import GlobalMessage from './../../Components/GlobalMessage.vue';

export default {
    name: 'LoginComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage
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
                this.$router.push({name: 'dashboard'});
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
        // console.log('asdasd123:: ', this.appVariables);
        // console.log('import.meta.env.VITE_APP_NAME:: ', import.meta.env.VITE_APP_NAME);
    },
    beforeUnmount() {
        // Hide the message when the component is unmounted
        this.showMessage = false;
    }
}
</script>
