<!-- components/ConfirmDialogue.vue -->

<template>
    <popup-modal ref="popup">
        <h2 class="delete-title text-center">{{ title }}</h2>
        <p class="text-center">{{ message }}</p>
        <div class="btns">
            <button class="cancel-btn" @click="_cancel">{{ cancelButton }}</button>
            <span class="ok-btn" @click="_confirm">{{ okButton }}</span>
        </div>
    </popup-modal>
</template>

<script>
import PopupModal from './PopupModal.vue'

export default {
    name: 'ConfirmDialogue',

    components: { PopupModal },

    data: () => ({
        // Parameters that change depending on the type of dialogue
        title: undefined,
        message: undefined, // Main text content
        okButton: undefined, // Text for confirm button; leave it empty because we don't know what we're using it for
        cancelButton: 'Cancel', // text for cancel button
        
        // Private variables
        resolvePromise: undefined,
        rejectPromise: undefined,
    }),

    methods: {
        show(opts = {}) {
            this.title = opts.title
            this.message = opts.message
            this.okButton = opts.okButton
            if (opts.cancelButton) {
                this.cancelButton = opts.cancelButton
            }
            // Once we set our config, we tell the popup modal to open
            this.$refs.popup.open()
            // Return promise so the caller can get results
            return new Promise((resolve, reject) => {
                this.resolvePromise = resolve
                this.rejectPromise = reject
            })
        },

        _confirm() {
            this.$refs.popup.close()
            this.resolvePromise(true)
        },

        _cancel() {
            this.$refs.popup.close()
            this.resolvePromise(false)
            // Or you can throw an error
            // this.rejectPromise(new Error('User cancelled the dialogue'))
        },
    },
}
</script>

<style scoped>
.text-center{
    text-align: center;
}
.btns {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: stretch;
}

.ok-btn {
    padding: 0.5em 1em;
    background-color: #ed3833;
    color: #ffffff;
    border: 2px solid #ed3833;
    border-radius: 5px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
}

.cancel-btn {
    padding: 0.5em 1em;
    background-color: #fff;
    color: #ed3833;
    border: 2px solid #ed3833;
    border-radius: 5px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    margin-right: 10px;
}
</style>