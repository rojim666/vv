<template>
    <div class="_main-header" :style="style">
        <div class="logo-box">
            <img :src="company.logo || DEFAULT_ZH_LOGO" class="logo"/>
            <div class="system-name-box">
                <!-- <div class="default-system-name">百分之一CRM管理系统</div> -->
                <!-- <div v-if="company.navigation_bar_title" class="system-line"></div> -->
                <div v-if="company.navigation_bar_title" class="default-system-name">{{ company.navigation_bar_title || '百分之一CRM管理系统' }}</div>
                <div v-else class="default-system-name">百分之一CRM管理系统</div>
            </div>
        </div>
        <div class="right-header-nav">
            <!-- <div v-if="showMenus" class="my-shadow"></div> -->
            <div>
                <div v-if="showMenus" class="menus-box">
                    <!-- <div class="menu-item active">会话质检</div> -->
                </div>
            </div>
            <a-dropdown v-if="loginInfo.id > 0">
                <div class="user-info-box">
                    <img src="@/assets/default-avatar.png" class="avatar"/>
                    <span class="ml4">{{ loginInfo.account || loginInfo.userid }}</span>
                    <DownOutlined class="ml4"/>
                </div>
                <template #overlay>
                    <a-menu>
                        <a-menu-item></a-menu-item>
                        <a-menu-item>
                            <div class="text-center" @click="logout">
                               <a>退出登录</a>
                            </div>
                        </a-menu-item>
                        <a-menu-item></a-menu-item>
                    </a-menu>
                </template>
            </a-dropdown>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted} from 'vue';
import {useStore} from 'vuex';
import {Modal, message} from 'ant-design-vue';
import {DownOutlined} from '@ant-design/icons-vue';
import {logoutHandle} from "@/utils/tools";
import {getSettings} from "@/api/auth-login";
import {DEFAULT_ZH_LOGO} from "@/constants";

const props = defineProps({
    background: String,
    showMenus: {
        type: Boolean,
        default: false
    },
})

const store = useStore()
const company = computed(() => store.getters.getCompany)
const loginInfo = computed(() => {
    return store.getters.getUserInfo
})

const style = computed(() => {
    return {
        background: props.background,
    }
})

const logout = () => {
    Modal.confirm({
        title: '提示',
        content: '确认退出当前登录账户？',
        onOk: () => {
            message.loading('正在退出...')
            setTimeout(() => {
                message.destroy()
                logoutHandle()
            }, 1000)
        }
    })
}

onMounted(() => {
  try {
    getSettings().then((res) => {
      if (res.status === 'success') {
        if (res.data) {
          store.commit('setCompany', res.data)
        } else {
          store.commit('setCompany', {
            title: '',
            logo: '',
            navigation_bar_title: '',
            login_page_title: '',
            login_page_description: '',
            copyright: ''
          })
        }
      }
    }).catch((e) => {
      // 用默认的头像和企业信息
      store.commit('setCompany', {
        title: '',
        logo: '',
        navigation_bar_title: '',
        login_page_title: '',
        login_page_description: '',
        copyright: ''
      })
    })
  } catch (e) {

  }
})
</script>

<style scoped lang="less">
._main-header {
    display: flex;
    align-items: center;
    position: fixed;
    z-index: 99;
    top: 0;
    left: 0;
    width: 100%;
    background: #FFFFFF;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border-bottom: 1px solid #E5E7EB;

    .logo-box {
        display: flex;
        align-items: center;
        max-width: 365px;
        min-width: 256px;
        flex-shrink: 0;
        height: 52px;
        padding: 20px;

        .logo {
            height: 32px;
            margin-right: 12px;
        }
    }

    .system-name-box {
        display: flex;
        align-items: center;
        font-family: "PingFang SC";
        font-style: normal;
    }

    .default-system-name {
        white-space: nowrap;
        color: #1F2937;
        font-size: 16px;
        font-weight: 600;
    }

    .right-header-nav {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-left: 24px;
        position: relative;

        .menus-box {
            display: flex;
            align-items: center;

            .menu-item {
                width: 98px;
                height: 36px;
                margin-right: 25px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 16px;
                font-weight: 400;
                color: #6B7280;
                border-radius: 8px;
                transition: all 0.2s ease;

                &.active {
                    background: #ECFDF5;
                    color: #10B981;
                    font-weight: 600;
                }

                &:not(.active):hover {
                    background: #F3F4F6;
                    color: #10B981;
                }
            }
        }

        .user-info-box {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #6B7280;
            font-size: 14px;
            font-weight: 400;
            margin: 0 16px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s ease;

            &:hover {
                background: #F3F4F6;
                color: #10B981;
            }

            .avatar {
                width: 24px;
                height: 24px;
                border-radius: 6px;
            }
        }
    }
}
</style>
