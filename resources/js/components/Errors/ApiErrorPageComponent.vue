<template>
    <section class="app-content mt-5 text-center">
        <div class="container-fluid">
            <div class="error-page row">
                <div class="col-md-12 error-page">
                    <h2 class="headline text-desino">{{ errorCode }}</h2></div>
                <div class="col-md-12 error-page"><h3>
                    <i class="fas fa-exclamation-triangle text-warning"></i>{{
                        errorMessage ?? 'Oops! Page not found.'
                    }} </h3>
                    <p>
                        We could not find the page you were looking for. Meanwhile,
                        you may
                        <router-link :to="{ name: 'home' }">
                            return to dashboard
                        </router-link>
                    </p>
                </div>
            </div>
        </div>
    </section>


<!--    <div class="bg-error-page">-->
<!--        <div class="container container-error">-->
<!--            <h1 class="display-1">{{ errorCode }}</h1>-->
<!--            <p class="lead">{{ errorMessage }}</p>-->
<!--            <router-link :to="{ name: 'home' }" class="btn btn-desino bg-desino text-light">-->
<!--                {{ $t('page_not_found_button_link') }}-->
<!--            </router-link>-->
<!--        </div>-->
<!--    </div>-->
</template>

<script>
import globalMixin from '@/globalMixin';

export default {
    name: 'ApiErrorPageComponent',
    mixins: [globalMixin],
    props: {
        errorCode: {
            default: 500,
            type: Number,
            required: true
        },
        errorMessage: {
            default: 'Internal Server Error',
            type: String,
            required: true
        }
    }
}
</script>

