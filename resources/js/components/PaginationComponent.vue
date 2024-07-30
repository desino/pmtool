<template>
    <nav v-if="totalPages > 1" aria-label="Page navigation">
        <ul class="pagination justify-content-end my-1 py-2">
            <!-- Previous Page Link -->
            <li :class="{ 'page-item': true, 'disabled': currentPage === 1 }" @click.prevent="changePage(currentPage - 1)">
                <a href="#" class="page-link" aria-label="Previous" v-if="currentPage > 1">&lsaquo;</a>
                <span class="page-link" aria-hidden="true" v-else>&lsaquo;</span>
            </li>

            <!-- Pagination Elements -->
            <template v-for="pageNumber in visiblePages" :key="pageNumber">
                <li :class="{ 'page-item': true, 'active': pageNumber === currentPage }">
                    <a href="#" class="page-link" @click.prevent="changePage(pageNumber)">{{ pageNumber }}</a>
                </li>
            </template>

            <!-- Next Page Link -->
            <li :class="{ 'page-item': true, 'disabled': currentPage === totalPages }" @click.prevent="changePage(currentPage + 1)">
                <a href="#" class="page-link" aria-label="Next" v-if="currentPage < totalPages">&rsaquo;</a>
                <span class="page-link" aria-hidden="true" v-else>&rsaquo;</span>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    props: {
        totalPages: {
            type: Number,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
    },
    computed: {
        visiblePages() {
            const totalPages = this.totalPages;
            const currentPage = this.currentPage;
            const fixedOptions = 7; // Fixed number of options to show

            // Calculate start and end pages to display
            let startPage = Math.max(1, currentPage - Math.floor(fixedOptions / 2));
            let endPage = Math.min(totalPages, startPage + fixedOptions - 1);

            // Adjust startPage when near the end of totalPages
            if (endPage - startPage + 1 < fixedOptions) {
                startPage = Math.max(1, endPage - fixedOptions + 1);
            }

            // Prepare array of pages to display
            let pages = [];
            for (let i = startPage; i <= endPage; i++) {
                pages.push(i);
            }

            return pages;
        },
    },
    methods: {
        changePage(page) {
            if (page !== this.currentPage && page > 0 && page <= this.totalPages) {
                this.$emit('page-changed', page);
            }
        },
    },
};
</script>

<style>
/* Add Bootstrap or custom CSS for pagination styling */
</style>
 