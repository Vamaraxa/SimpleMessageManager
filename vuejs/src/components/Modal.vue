<template>
    <div>
        <b-modal v-model="show" :id="id" :title=title ok-only ok-variant="secondary" ok-title="Cancel">
            <p v-if="msg" class="my-4">
                {{ msg }}
            </p>
            <slot></slot>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: 'BootstrapModal',
    props: {
        msg: String,
        id: String,
        title: {
            type: String,
            default() {
                return 'BootstrapVue'
            }
        }
    },
    data() {
        return {
            show: false
        }
    }
}
</script>