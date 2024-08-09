<template>
    <div class="d-flex vh-100">
        <div class="m-auto text-center">
            <h1 v-if="user" class="mb-4"><span class="text-desino">{{ user?.name }}</span> is logged in! </h1>
            <button class="btn btn-danger btn-block" @click="logout">{{ $t('logout_but_text') }}</button>
        </div>
    </div>
</template>

<script>
import AuthService from '../../services/AuthService.js';
import { mapState, mapGetters } from 'vuex';

export default {
    methods: {
        logout() {
            AuthService.logout();
        }
    },
    computed: {
        ...mapGetters(['user'])
    },
}
</script>
