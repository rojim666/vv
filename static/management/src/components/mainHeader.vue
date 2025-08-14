<template>
    <div class="_main-header" :style="style" :class="{ 'mobile-header': isMobile }">
        <div class="logo-box">
            <!-- 添加菜单收起按钮 -->
            <div class="menu-toggle-btn" @click="toggleSidebar" v-if="!isMobile">
                <MenuFoldOutlined v-if="!sidebarCollapsed" />
                <MenuUnfoldOutlined v-if="sidebarCollapsed" />
            </div>
            <img :src="company.logo || DEFAULT_ZH_LOGO" class="logo"/>
            <div class="system-name-box" v-if="!isMobile || !isCompactMode">
                <div v-if="company.navigation_bar_title" class="default-system-name">{{ company.navigation_bar_title || '百分之二CRM管理系统' }}</div>
                <div v-else class="default-system-name">百分之二CRM管理系统</div>
            </div>
        </div>
        <div class="right-header-nav">
            <div v-if="showMenus && !isMobile" class="menus-box">
                <!-- 菜单项 -->
            </div>
            <a-dropdown v-if="loginInfo.id > 0">
                <div class="user-info-box">
                    <img src="@/assets/default-avatar.png" class="avatar"/>
                    <span class="ml4" v-if="!isMobile || !isCompactMode">{{ loginInfo.account || loginInfo.userid }}</span>
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
import {computed, onMounted, ref} from 'vue';
import {useStore} from 'vuex';
import {Modal, message} from 'ant-design-vue';
import {DownOutlined, MenuFoldOutlined, MenuUnfoldOutlined} from '@ant-design/icons-vue';
import {logoutHandle} from "@/utils/tools";
import {getSettings} from "@/api/auth-login";
import {DEFAULT_ZH_LOGO} from "@/constants";

const props = defineProps({
    background: String,
    showMenus: {
        type: Boolean,
        default: false
    },
    isMobile: {
        type: Boolean,
        default: false
    }
})

const isCompactMode = ref(false);

// 检测是否需要紧凑模式
const checkCompactMode = () => {
    isCompactMode.value = window.innerWidth <= 480;
};

const store = useStore()
const company = computed(() => store.getters.getCompany)
const loginInfo = computed(() => {
    return store.getters.getUserInfo
})
// 添加菜单收起状态
const sidebarCollapsed = computed(() => store.getters.getSidebarCollapsed)

// 添加切换菜单的方法
const toggleSidebar = () => {
    store.commit('toggleSidebar')
}

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
    checkCompactMode();
    window.addEventListener('resize', checkCompactMode);
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
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 52px;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);

    .logo-box {
        display: flex;
        align-items: center;
        
        // 添加菜单切换按钮样式
        .menu-toggle-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s;
            
            &:hover {
                background-color: rgba(0, 0, 0, 0.06);
            }
            
            .anticon {
                font-size: 16px;
                color: #666;
            }
        }
        
        .logo {
            width: 32px;
            height: 32px;
            margin-right: 12px;
        }
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

// 移动端响应式优化
@media (max-width: 768px) {
    ._main-header {
        .logo-box {
            max-width: 200px;
            padding: 16px;
        }
        
        .system-name-box {
            .default-system-name {
                font-size: 14px;
            }
        }
        
        .right-header-nav {
            .user-info-box {
                font-size: 13px;
            }
        }
    }
}

@media (max-width: 480px) {
    ._main-header {
        .logo-box {
            max-width: 150px;
            padding: 12px;
            
            .logo {
                height: 20px;
            }
        }
        
        .system-name-box {
            .default-system-name {
                font-size: 12px;
            }
        }
    }
}
</style>
