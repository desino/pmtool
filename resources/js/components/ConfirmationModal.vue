<template>
    <div class="modal fade" :id="modalId" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <!-- modal-dialog-centered -->
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title" id="confirmationModalLabel">{{ title }}</h5>
                </div>
                <div class="modal-body">
                    <div class="row w-100 g-1">
                        <span class="fw-bold align-middle " v-html="message">
                        </span>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1 align-items-center">
                        <div class="col-6">
                            <button type="button" class="btn btn-desino w-100 border-0" @click="confirm">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal"
                                @click="hideModal(false)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from 'bootstrap';
export default {
    props: {
        modalId: {
            type: String,
            default: 'confirmationModal',
        },
        title: {
            type: String,
            default: 'Confirm Action',
        },
        message: {
            type: String,
            default: 'Are you sure you want to proceed?',
        },
    },
    // emits: ['confirm'],
    data() {
        return {
            isConfirmed: false
        };
    },
    methods: {
        confirm() {
            this.isConfirmed = true;
            this.$emit('confirm');
            this.hideModal(this.isConfirmed);
        },
        showModal() {
            const modal = new Modal(document.getElementById(this.modalId), {
                backdrop: 'static',
                keyboard: true,
            });
            modal.show();
        },
        hideModal(isConfirmed = false) {
            if (!isConfirmed) {
                this.isConfirmed = false;
            }
            const modal = Modal.getInstance(document.getElementById(this.modalId));
            modal.hide();
        },
    },
};
</script>