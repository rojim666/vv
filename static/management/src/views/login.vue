<template>
    <div class="_main-container" :class="{ 'mobile-view': isMobile }">
        <MainHeader :is-mobile="isMobile"/>
        <div class="main-content">
            <!-- 移动端简化的左侧区域 -->
            <div class="left-box" v-if="!isMobile || !isLoginFormVisible">
                <div class="cover-gradient"></div>
                <div class="web-logo-title-box">
                    <div class="web-logo-title">{{ company.login_page_title || '百分之二CRM管理系统' }}</div>
                    <div class="web-logo-info">{{ company.login_page_description || '助力企业客户沟通合规管控和质量提升' }}</div>
                    <!-- 移动端显示登录按钮 -->
                    <div class="mobile-login-trigger" v-if="isMobile" @click="showLoginForm">
                        <button class="mobile-login-btn">开始登录</button>
                    </div>
                </div>
            </div>
            
            <!-- 移动端全屏登录表单 -->
            <div class="right-box" v-if="!isMobile || isLoginFormVisible" :class="{ 'mobile-fullscreen': isMobile }">
                <!-- 移动端返回按钮 -->
                <div class="mobile-back-btn" v-if="isMobile" @click="hideLoginForm">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    返回
                </div>
                
                <div class="right-icon-box" v-if="isWxLogin">
                    <div class="login-tip-top">
                        使用密码登录
                        <img class="login-tip-top-icon" src="@/assets/svg/login-tips-triangle.svg" />
                    </div>
                    <img class="right-check" src="@/assets/user-login.svg" @click="onChangeLogin('account')" />
                </div>
                <div id="ww_login" v-if="isWxLogin"></div>
                
                <div class="right-icon-box" v-else>
                    <div class="login-tip-top">
                        扫码登录更便捷
                        <img class="login-tip-top-icon" src="@/assets/svg/login-tips-triangle.svg" />
                    </div>
                    <img class="right-check" src="@/assets/wx-login.svg" @click="onChangeLogin('wx')" />
                </div>
                
                <div class="sign-in" v-if="!isWxLogin">
                    <h2 class="login-title">密码登录</h2>
                    <a-form class="login-form" :model="formState" name="basic" autocomplete="off" @finish="onFinish">
                        <a-form-item
                            name="username"
                            class="usernames"
                            :rules="[{ required: true, message: '请输入账号' }]"
                        >
                            <a-input class="login-item" v-model:value="formState.username" autocomplete="off" placeholder="请输入账号">
                                <template #prefix>
                                    <img class="login-input-icon" src="@/assets/svg/user.svg" />
                                </template>
                            </a-input>
                        </a-form-item>

                        <a-form-item
                            name="password"
                            :rules="[{ required: true, message: '请输入密码' }]"
                        >
                            <a-input-password class="login-item" v-model:value="formState.password" autocomplete="off" type="password" placeholder="请输入密码">
                                <template #prefix>
                                    <img class="login-input-icon" src="@/assets/svg/password.svg" />
                                </template>
                            </a-input-password>
                        </a-form-item>

                        <a-form-item>
                            <a-button class="login-btn" type="primary" block html-type="submit">登录</a-button>
                            <div class="login-tip">暂无账号？可扫码登录后设置</div>
                        </a-form-item>
                    </a-form>
                </div>
            </div>
        </div>
        <MainFooter :copyright="company.copyright" v-if="!isMobile || !isLoginFormVisible" />
    </div>
</template>

<script setup>
import {onMounted, getCurrentInstance, ref, reactive, computed} from 'vue';
import {useRouter} from 'vue-router';
import {useStore} from 'vuex';
import MainHeader from "@/components/mainHeader.vue";
import MainFooter from "@/components/mainFooter.vue";
import {checkInit, getSettings, getCurrentUser, loginByCode, loginByAccount} from "@/api/auth-login";
import {setAuthToken, setCorpInfo} from "@/utils/cache";
import { UserOutlined, LockOutlined } from '@ant-design/icons-vue';
import {setCookieAcrossSubdomain} from "@/utils/cookie";
import {loginHandle} from "@/utils/tools";

const {proxy} = getCurrentInstance();
const router = useRouter()
const store = useStore()
const company = computed(() => store.getters.getCompany)
const loading = ref(true)
const isWxLogin = ref(false)
const formState = reactive({
  username: '',
  password: '',
  remember: true
})

// const getCUrrentCorpData = () => {
//   getSettings().then((res) => {
//     if (res.status === 'success') {
//         store.commit('setCompany', res.data)
//     }
//   }).catch((e) => {
//     // 用默认的头像和企业信息
//     store.commit('setCompany', {
//       title: '',
//       logo: '',
//       navigation_bar_title: '',
//       login_page_title: '',
//       login_page_description: '',
//       copyright: ''
//     })
//   })
// }

onMounted(async () => {
  // getCUrrentCorpData()
    // console.log('ww', ww)
    // console.log('SDK_VERSION', ww.SDK_VERSION)
    // console.log('process.env.NODE_ENV', process.env.NODE_ENV)
})

const localLoginTest = () => {
    loginAfterHandle('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjUsInVzZXJpZCI6IllvdUVyWXVhblhpYW9CYVdhbmciLCJjb3JwX2lkIjoid3c1ZjQzMmIzYTI0YTliOWYxIiwiZXhwIjoxNzM2NTA3MzcxfQ.77WBQ5tRSrBMSSiaK7p90HvTWhiPzF-5z6RJPJo7uzM')
}

const onChangeLogin = (type) => {
    // console.log(type)
    if (type === 'wx') {
        isWxLogin.value = true
         // 验证是否完成企业初始化
        checkCorpInit()
    } else {
        isWxLogin.value = false
    }
}

const onFinish = () => {
  handleLogin()
}

const handleLogin = () => {
    loginByAccount({
      username: formState.username,
      password: formState.password
    })
    .then((res) => {
        loginAfterHandle(res.data.token)
    })
    .catch((err) => {
    //   console.log(err.message)
    })
}

const checkCorpInit = () => {
    checkInit().then(res => {
        if (res?.data?.init) {
            let {corp_id, agent_id} = res.data
            loginInit(corp_id, agent_id)
            //TODO 本地环境直接登录（本地域名无法生成登录二维码）
            process.env.NODE_ENV === 'development' && localLoginTest()
        } else {
            // 企业信息不存在
            proxy.$message.warning('企业信息不存在，请先注册企业信息！')
            setTimeout(() => {
                router.push({path: '/authorizedAccess/index'})
            }, 1000)
        }
    }).finally(() => {
        loading.value = false
    })
}

const loginInit = async (corp_id, agent_id) => {
    try {
        const wwLogin = ww.createWWLoginPanel({
            el: '#ww_login',
            params: {
                login_type: 'CorpApp',
                appid: corp_id,
                agentid: agent_id,
                redirect_uri: window.location.origin,
                state: 'loginState',
                redirect_type: 'callback',
            },
            onCheckWeComLogin({isWeComLogin}) {
                // console.log(isWeComLogin)
            },
            onLoginSuccess({code}) {
                // console.log({code})
                loginByCode({
                    corp_id: corp_id,
                    code,
                }).then(res => {
                    // console.log('res', res)
                    loginAfterHandle(res.data.token)
                })
            },
            onLoginFail(err) {
                // console.log(err)
            },
        })
    } catch (e) {
        // console.log("loginInit Err:", e)
        proxy.$message.error('初始化登录失败！')
    }
}

const loginAfterHandle = async token => {
    try {
        const {corpInfo} = await loginHandle(token)
        proxy.$message.success('登录成功，正在跳转主页')
        setTimeout(() => {
            // chat_public_key_version > 0表示已经配置会话存档
            // 否则去配置
            if (corpInfo.data?.chat_public_key_version > 0) {
                router.push({path: '/'})
            } else {
                router.push({path: '/authorizedAccess/guide'})
            }
        }, 1000)
    } catch (e) {
        // console.log('Err:', e)
        proxy.$message.error('登录失败')
    }
}
</script>

<style scoped lang="less">
._main-header {
    background: none;
    box-shadow: none;
}

._main-container {
    background: #ffffff; // 修改为白色背景
    min-height: 100vh;
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;

    .main-content {
        width: 960px;
        height: 565px;
        border-radius: 20px;
        background: #FFF;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        display: flex;
        overflow: hidden;
        transition: all 0.3s ease;

        &:hover {
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .left-box {
            position: relative;
            width: 480px;
            flex-shrink: 0;
            overflow: hidden;

            .cover-gradient {
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                border-radius: 20px 0 0 20px;
                position: relative;
                
                &::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
                    opacity: 0.3;
                }
            }

            .web-logo-title-box {
                font-family: "PingFang SC", -apple-system, BlinkMacSystemFont, sans-serif;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 12px;
                z-index: 2;
                text-align: center;
                padding: 0 40px;

                .web-logo-title {
                    color: #ffffff;
                    font-size: 28px;
                    font-weight: 700;
                    line-height: 1.3;
                    margin-bottom: 8px;
                    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }

                .web-logo-info {
                    color: rgba(255, 255, 255, 0.9);
                    font-size: 16px;
                    font-weight: 400;
                    line-height: 1.5;
                    text-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
                }
            }
        }

        .right-box {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #fafbfc;

            .right-icon-box {
                display: flex;
                align-items: center;
                position: absolute;
                right: 24px;
                top: 24px;
                z-index: 10;
            }

            .login-tip-top {
                position: relative;
                padding: 8px 16px;
                border-radius: 8px;
                background: #1890ff;
                color: #ffffff;
                font-size: 13px;
                font-weight: 500;
                white-space: nowrap;
                box-shadow: 0 2px 8px rgba(24, 144, 255, 0.3);
                transition: all 0.3s ease;

                &:hover {
                    background: #40a9ff;
                    transform: translateY(-1px);
                }

                .login-tip-top-icon {
                    position: absolute;
                    width: 6px;
                    right: -6px;
                    top: 50%;
                    margin-top: -6px;
                }
            }

            .right-check {
                width: 48px;
                height: 48px;
                cursor: pointer;
                margin-left: 12px;
                border-radius: 8px;
                transition: all 0.3s ease;
                padding: 8px;
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);

                &:hover {
                    background: rgba(255, 255, 255, 1);
                    transform: scale(1.05);
                }
            }

            .sign-in {
                width: 100%;
                max-width: 360px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                font-family: "PingFang SC", -apple-system, BlinkMacSystemFont, sans-serif;

                .login-title {
                    text-align: center;
                    color: #262626;
                    font-size: 28px;
                    font-weight: 600;
                    line-height: 1.3;
                    margin-bottom: 48px;
                    position: relative;

                    &::after {
                        content: '';
                        position: absolute;
                        bottom: -16px;
                        left: 50%;
                        transform: translateX(-50%);
                        width: 40px;
                        height: 3px;
                        background: linear-gradient(90deg, #1890ff, #40a9ff);
                        border-radius: 2px;
                    }
                }

                .login-form {
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                }

                .login-item {
                    width: 100%;
                    height: 48px;
                    padding: 12px 16px;
                    border-radius: 8px;
                    border: 2px solid #e8e8e8;
                    background: #ffffff;
                    font-size: 15px;
                    transition: all 0.3s ease;
                    margin-bottom: 20px;

                    &:focus {
                        border-color: #1890ff;
                        box-shadow: 0 0 0 3px rgba(24, 144, 255, 0.1);
                        outline: none;
                    }

                    &:hover {
                        border-color: #40a9ff;
                    }

                    .login-input-icon {
                        width: 18px;
                        height: 18px;
                        margin-right: 12px;
                        opacity: 0.6;
                    }
                }

                .login-btn {
                    width: 100%;
                    height: 48px;
                    border-radius: 8px;
                    background: linear-gradient(135deg, #1890ff 0%, #40a9ff 100%);
                    border: none;
                    color: #ffffff;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    margin-top: 8px;
                    box-shadow: 0 4px 15px rgba(24, 144, 255, 0.3);

                    &:hover {
                        background: linear-gradient(135deg, #40a9ff 0%, #1890ff 100%);
                        transform: translateY(-2px);
                        box-shadow: 0 6px 20px rgba(24, 144, 255, 0.4);
                    }

                    &:active {
                        transform: translateY(0);
                    }
                }

                .login-tip {
                    text-align: center;
                    color: #8c8c8c;
                    font-size: 14px;
                    margin-top: 24px;
                    line-height: 1.5;
                }
            }

            #ww_login {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 300px;
                height: 300px;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }
        }
    }
}

// 响应式设计
@media (max-width: 1024px) {
    ._main-container .main-content {
        width: 90%;
        max-width: 800px;
        height: auto;
        flex-direction: column;

        .left-box {
            width: 100%;
            height: 250px;

            .cover-gradient {
                border-radius: 20px 20px 0 0;
            }
            
            .web-logo-title-box {
                .web-logo-title {
                    font-size: 24px;
                }
                
                .web-logo-info {
                    font-size: 14px;
                }
            }
        }

        .right-box {
            padding: 40px 20px;
            
            .sign-in {
                .login-title {
                    font-size: 24px;
                }
            }
        }
    }
}

@media (max-width: 768px) {
    ._main-container:not(.mobile-view) {
        padding: 12px;

        .main-content {
            .left-box {
                height: 200px;
                
                .web-logo-title-box {
                    .web-logo-title {
                        font-size: 20px;
                    }
                    
                    .web-logo-info {
                        font-size: 13px;
                    }
                }
            }
            
            .right-box {
                padding: 30px 16px;

                .right-icon-box {
                    right: 16px;
                    top: 16px;
                    
                    .login-tip-top {
                        font-size: 12px;
                        padding: 6px 12px;
                    }
                    
                    .right-check {
                        width: 40px;
                        height: 40px;
                    }
                }

                .sign-in {
                    max-width: 100%;

                    .login-title {
                        font-size: 22px;
                        margin-bottom: 32px;
                    }
                    
                    .login-item {
                        height: 50px;
                        font-size: 15px;
                    }
                    
                    .login-btn {
                        height: 50px;
                        font-size: 16px;
                    }
                }
                
                #ww_login {
                    width: 250px;
                    height: 250px;
                }
            }
        }
    }
}

// 超小屏幕优化
@media (max-width: 480px) {
    ._main-container:not(.mobile-view) {
        padding: 8px;
        
        .main-content {
            .left-box {
                height: 160px;
                
                .web-logo-title-box {
                    padding: 0 20px;
                    
                    .web-logo-title {
                        font-size: 18px;
                    }
                    
                    .web-logo-info {
                        font-size: 12px;
                    }
                }
            }
            
            .right-box {
                padding: 20px 12px;
                
                .sign-in {
                    .login-title {
                        font-size: 20px;
                        margin-bottom: 24px;
                    }
                    
                    .login-item {
                        height: 48px;
                        padding: 12px 14px;
                    }
                    
                    .login-btn {
                        height: 48px;
                    }
                }
                
                #ww_login {
                    width: 200px;
                    height: 200px;
                }
            }
        }
    }
}

// 横屏模式优化
@media (max-height: 600px) and (orientation: landscape) {
    ._main-container:not(.mobile-view) {
        .main-content {
            .left-box {
                height: 120px;
                
                .web-logo-title-box {
                    .web-logo-title {
                        font-size: 16px;
                    }
                    
                    .web-logo-info {
                        font-size: 11px;
                    }
                }
            }
            
            .right-box {
                padding: 20px;
                
                .sign-in {
                    .login-title {
                        font-size: 18px;
                        margin-bottom: 16px;
                    }
                    
                    .login-item {
                        height: 40px;
                        margin-bottom: 12px;
                    }
                    
                    .login-btn {
                        height: 40px;
                        margin-top: 4px;
                    }
                }
            }
        }
    }
}

// 移动端专用样式
.mobile-view {
    .main-content {
        height: 100vh;
        border-radius: 0;
        box-shadow: none;
        
        .left-box {
            .mobile-login-trigger {
                margin-top: 40px;
                
                .mobile-login-btn {
                    padding: 16px 32px;
                    background: rgba(255, 255, 255, 0.2);
                    border: 2px solid rgba(255, 255, 255, 0.8);
                    border-radius: 25px;
                    color: white;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    backdrop-filter: blur(10px);
                    
                    &:hover {
                        background: rgba(255, 255, 255, 0.3);
                        transform: translateY(-2px);
                    }
                }
            }
        }
        
        .right-box.mobile-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1000;
            background: #fafbfc;
            border-radius: 0;
            
            .mobile-back-btn {
                position: absolute;
                top: 20px;
                left: 20px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #666;
                font-size: 16px;
                cursor: pointer;
                z-index: 10;
                
                &:hover {
                    color: #1890ff;
                }
            }
            
            .sign-in {
                padding-top: 80px;
                
                .login-title {
                    font-size: 32px;
                    margin-bottom: 60px;
                }
                
                .login-item {
                    height: 56px;
                    font-size: 16px;
                    margin-bottom: 24px;
                }
                
                .login-btn {
                    height: 56px;
                    font-size: 18px;
                }
            }
        }
    }
}

// 增强的响应式设计
@media (max-width: 1024px) {
    ._main-container:not(.mobile-view) .main-content {
        width: 90%;
        max-width: 800px;
        height: auto;
        flex-direction: column;

        .left-box {
            width: 100%;
            height: 250px;

            .cover-gradient {
                border-radius: 20px 20px 0 0;
            }
            
            .web-logo-title-box {
                .web-logo-title {
                    font-size: 24px;
                }
                
                .web-logo-info {
                    font-size: 14px;
                }
            }
        }

        .right-box {
            padding: 40px 20px;
            
            .sign-in {
                .login-title {
                    font-size: 24px;
                }
            }
        }
    }
}

@media (max-width: 768px) {
    ._main-container:not(.mobile-view) {
        padding: 12px;

        .main-content {
            .left-box {
                height: 200px;
                
                .web-logo-title-box {
                    .web-logo-title {
                        font-size: 20px;
                    }
                    
                    .web-logo-info {
                        font-size: 13px;
                    }
                }
            }
            
            .right-box {
                padding: 30px 16px;

                .right-icon-box {
                    right: 16px;
                    top: 16px;
                    
                    .login-tip-top {
                        font-size: 12px;
                        padding: 6px 12px;
                    }
                    
                    .right-check {
                        width: 40px;
                        height: 40px;
                    }
                }

                .sign-in {
                    max-width: 100%;

                    .login-title {
                        font-size: 22px;
                        margin-bottom: 32px;
                    }
                    
                    .login-item {
                        height: 50px;
                        font-size: 15px;
                    }
                    
                    .login-btn {
                        height: 50px;
                        font-size: 16px;
                    }
                }
                
                #ww_login {
                    width: 250px;
                    height: 250px;
                }
            }
        }
    }
}

// 超小屏幕优化
@media (max-width: 480px) {
    ._main-container:not(.mobile-view) {
        padding: 8px;
        
        .main-content {
            .left-box {
                height: 160px;
                
                .web-logo-title-box {
                    padding: 0 20px;
                    
                    .web-logo-title {
                        font-size: 18px;
                    }
                    
                    .web-logo-info {
                        font-size: 12px;
                    }
                }
            }
            
            .right-box {
                padding: 20px 12px;
                
                .sign-in {
                    .login-title {
                        font-size: 20px;
                        margin-bottom: 24px;
                    }
                    
                    .login-item {
                        height: 48px;
                        padding: 12px 14px;
                    }
                    
                    .login-btn {
                        height: 48px;
                    }
                }
                
                #ww_login {
                    width: 200px;
                    height: 200px;
                }
            }
        }
    }
}

// 横屏模式优化
@media (max-height: 600px) and (orientation: landscape) {
    ._main-container:not(.mobile-view) {
        .main-content {
            .left-box {
                height: 120px;
                
                .web-logo-title-box {
                    .web-logo-title {
                        font-size: 16px;
                    }
                    
                    .web-logo-info {
                        font-size: 11px;
                    }
                }
            }
            
            .right-box {
                padding: 20px;
                
                .sign-in {
                    .login-title {
                        font-size: 18px;
                        margin-bottom: 16px;
                    }
                    
                    .login-item {
                        height: 40px;
                        margin-bottom: 12px;
                    }
                    
                    .login-btn {
                        height: 40px;
                        margin-top: 4px;
                    }
                }
            }
        }
    }
}
</style>
