<template>
    <span class="zm-payment-block">
        <slot></slot>
        <span :class="{'zm-payment-tag': showPaymentTag}" @click="onClick"></span>
    </span>
</template>

<script setup>
import {computed, h} from 'vue';
import {useRouter} from 'vue-router';
import {useStore} from 'vuex';
import {Modal} from 'ant-design-vue';
import {getPluginRouteParams} from "@/utils/tools";

const props = defineProps({
    type: {
        type: Number,
        default: 1
    }
})
const router = useRouter()
const store = useStore()
const archiveStfModule = computed(() => {
    return store.getters.getArchiveStfInfo || []
})

const showPaymentTag = computed(() => {
    const val = archiveStfModule.value
    return !val.is_install || val.is_expired || val.has_bought != 1
})

function onClick() {
    if (showPaymentTag.value) {
        switch (props.type) {
            case 1:
                Modal.confirm({
                    title: '存档员工管理',
                    onOk: () => {
                        linkPlugHome()
                    }
                })
                break
        }
    }
}

const linkPlugHome = () => {
    const link = router.resolve({
        path: '/plug/index'
    })
    window.open(link.href)
}
</script>

<style scoped>
.zm-payment-block {
    display: flex;
    align-items: center;
}
</style>
