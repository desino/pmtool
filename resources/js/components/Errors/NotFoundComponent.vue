<template>
    <div class="bg-404">
        <div class="container container-404">
            <h1 class="display-1">404</h1>
            <p class="lead">Oops! The page you are looking for does not exist.</p>            
            <router-link class="btn btn-primary" :to="{ name: 'dashboard' }">
                {{ $t('error.not_found_page_link_text') }}
            </router-link>
        </div>
    </div>
</template>

<script>
    import globalMixin from '@/globalMixin';
    export default {
        name: 'Office365LoginComponent',
        mixins: [globalMixin],
    }
</script>