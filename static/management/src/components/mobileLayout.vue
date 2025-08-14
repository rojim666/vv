<template>
    <div class="mobile-layout">
        <!-- 移动端顶部导航 -->
        <div class="mobile-header">
            <div class="header-left">
                <img :src="company.logo || DEFAULT_ZH_LOGO" class="logo" />
                <span class="app-title">{{ company.navigation_bar_title || 'CRM系统' }}</span>
            </div>
            <div class="header-right">
                <a-dropdown>
                    <div class="user-avatar">
                        <img src="@/assets/default-avatar.png" class="avatar" />
                    </div>
                    <template #overlay>
                        <a-menu>
                            <a-menu-item @click="logout">
                                <LogoutOutlined /> 退出登录
                            </a-menu-item>
                        </a-menu>
                    </template>
                </a-dropdown>
            </div>
        </div>

        <!-- 主要内容区域 -->
        <div class="mobile-content">
            <slot></slot>
        </div>

        <!-- 底部导航菜单 -->
        <div class="mobile-bottom-nav">
            <div 
                v-for="menu in bottomMenus" 
                :key="menu.key"
                class="nav-item"
                :class="{ active: isActiveMenu(menu) }"
                @click="navigateToMenu(menu)"
            >
                <div class="nav-icon">
                    <component :is="menu.icon" />
                </div>
                <div class="nav-label">{{ menu.title }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { Modal, message } from 'ant-design-vue'
import {
    MessageOutlined,
    TeamOutlined,
    AppstoreOutlined,
    SettingOutlined,
    LogoutOutlined
} from '@ant-design/icons-vue'
import { logoutHandle } from '@/utils/tools'
import { DEFAULT_ZH_LOGO } from '@/constants'

const store = useStore()
const router = useRouter()
const route = useRoute()

const company = computed(() => store.getters.getCompany)
const userInfo = computed(() => store.getters.getUserInfo)

// 底部导航菜单配置
const bottomMenus = computed(() => {
    const baseMenus = [
        {
            key: 'SessionQualityInspection',
            title: '会话质检',
            icon: MessageOutlined,
            route: '/sessionArchive/index'
        },
        {
            key: 'CustomerManagement', 
            title: '客户管理',
            icon: TeamOutlined,
            route: '/customerManagement/index'
        },
        {
            key: 'FunctionCenter',
            title: '更多功能', 
            icon: AppstoreOutlined,
            route: '/plug/index'
        },
        {
            key: 'Systemctl',
            title: '系统设置',
            icon: SettingOutlined,
            route: '/systemctl/authConfig'
        }
    ]
    
    // 根据用户权限过滤菜单
    const roleId = userInfo.value?.role_id || 0
    switch (Number(roleId)) {
        case 1: // 普通员工
            return baseMenus.filter(menu => 
                ['SessionQualityInspection'].includes(menu.key)
            )
        case 2:
        case 3: // 2级和3级权限用户
            return baseMenus.filter(menu => 
                !['Systemctl'].includes(menu.key)
            )
        case 4: // 游客
            return baseMenus.filter(menu => 
                !['Systemctl'].includes(menu.key)
            )
        default:
            return baseMenus
    }
})

// 判断当前菜单是否激活
const isActiveMenu = (menu) => {
    const currentPath = route.path
    if (menu.key === 'SessionQualityInspection') {
        return currentPath.includes('/sessionArchive')
    } else if (menu.key === 'CustomerManagement') {
        return currentPath.includes('/customerManagement')
    } else if (menu.key === 'FunctionCenter') {
        return currentPath.includes('/plug') || currentPath.includes('/module')
    } else if (menu.key === 'Systemctl') {
        return currentPath.includes('/systemctl')
    }
    return false
}

// 导航到指定菜单
const navigateToMenu = (menu) => {
    if (route.path !== menu.route) {
        router.push(menu.route)
    }
}

// 退出登录
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
</script>

<style scoped lang="less">
.mobile-layout {
    display: flex;
    flex-direction: column;
    height: 100vh;
    background: #f5f5f5;
    
    .mobile-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 56px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        
        .header-left {
            display: flex;
            align-items: center;
            
            .logo {
                width: 32px;
                height: 32px;
                margin-right: 12px;
            }
            
            .app-title {
                font-size: 18px;
                font-weight: 600;
                color: #333;
            }
        }
        
        .header-right {
            .user-avatar {
                .avatar {
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                }
            }
        }
    }
    
    .mobile-content {
        flex: 1;
        margin-top: 56px;
        margin-bottom: 70px;
        overflow-y: auto;
        padding: 16px;
    }
    
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 70px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-around;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            height: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            
            .nav-icon {
                font-size: 20px;
                color: #999;
                margin-bottom: 4px;
                transition: all 0.3s ease;
            }
            
            .nav-label {
                font-size: 12px;
                color: #999;
                transition: all 0.3s ease;
            }
            
            &.active {
                .nav-icon {
                    color: #1890ff;
                }
                
                .nav-label {
                    color: #1890ff;
                    font-weight: 500;
                }
            }
            
            &:active {
                background: rgba(24, 144, 255, 0.1);
            }
        }
    }
}
</style>