<template>
    <div class="_main-container">
        <MainHeader background="#FFF" showMenus/>
        <div class="_main-content-block">
            <MainLeftMenus/>
            <div class="_main-right-block" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
                <slot name="navbar">
                    <MainNavbar :title="title"/>
                </slot>
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed} from 'vue';
import {useStore} from 'vuex';
import MainLeftMenus from "@/components/mainLeftMenus.vue";
import MainHeader from "@/components/mainHeader.vue";
import MainNavbar from "@/components/mainNavbar.vue";

const props = defineProps({
    title: {
        type: [Array, String]
    }
})

const store = useStore()
const sidebarCollapsed = computed(() => store.getters.getSidebarCollapsed)
</script>

<style scoped lang="less">
._main-content-block {
    display: flex;
    padding-top: 52px;

    ._main-right-block {
        width: calc(100vw - 256px);
        transition: all 0.3s ease;
        
        &.sidebar-collapsed {
            width: calc(100vw - 80px);
        }
    }
}
</style>
