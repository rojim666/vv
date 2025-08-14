<template>
    <div>
        <!-- 桌面端布局 -->
        <MainLayout v-if="!isMobile" :title="'首页'">
            <div class="dashboard-content">
                <!-- 桌面端首页内容 -->
                <h1>欢迎使用CRM管理系统</h1>
                <p>这是桌面端界面</p>
            </div>
        </MainLayout>
        
        <!-- 移动端布局 -->
        <MobileLayout v-else>
            <div class="mobile-dashboard">
                <!-- 移动端首页内容 -->
                <div class="welcome-card">
                    <h2>欢迎使用</h2>
                    <p>{{ company.navigation_bar_title || 'CRM管理系统' }}</p>
                </div>
                
                <div class="quick-actions">
                    <div class="action-card" @click="$router.push('/sessionArchive/index')">
                        <MessageOutlined class="action-icon" />
                        <span>会话质检</span>
                    </div>
                    <div class="action-card" @click="$router.push('/customerManagement/index')">
                        <TeamOutlined class="action-icon" />
                        <span>客户管理</span>
                    </div>
                    <div class="action-card" @click="$router.push('/plug/index')">
                        <AppstoreOutlined class="action-icon" />
                        <span>更多功能</span>
                    </div>
                </div>
            </div>
        </MobileLayout>
    </div>
</template>

<script setup>
import {computed} from 'vue';
import {useStore} from 'vuex';
import MainLayout from '@/components/mainLayout.vue';
import MobileLayout from '@/components/mobileLayout.vue';
import {
    MessageOutlined,
    TeamOutlined,
    AppstoreOutlined
} from '@ant-design/icons-vue';

const store = useStore();
const isMobile = computed(() => store.getters.getIsMobile);
const company = computed(() => store.getters.getCompany);
</script>

<style scoped lang="less">
.dashboard-content {
    padding: 24px;
    background: #fff;
    border-radius: 8px;
    margin: 16px;
}

.mobile-dashboard {
    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        text-align: center;
        
        h2 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        p {
            margin: 0;
            opacity: 0.9;
        }
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        
        .action-card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            
            .action-icon {
                font-size: 32px;
                color: #1890ff;
                margin-bottom: 12px;
                display: block;
            }
            
            span {
                font-size: 16px;
                font-weight: 500;
                color: #333;
            }
            
            &:active {
                transform: scale(0.95);
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            }
        }
    }
}
</style>
