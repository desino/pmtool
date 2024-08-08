<template>
    <div class="bg-404">
        <div class="container container-404">
            <h1 class="display-1">404</h1>
            <p class="lead">Oops! The page you are looking for does not exist.</p>
            <router-link class="btn btn-desino" :to="{ name: 'home' }">
                {{ $t('page_not_found_button_link') }}
            </router-link>
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
export default {
    name: 'NotFoundComponent',
    mixins: [globalMixin],
}
</script>
