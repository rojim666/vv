<template>
    <a-config-provider :locale="zhCN">
        <router-view/>
    </a-config-provider>
</template>

<script setup>
import {onMounted, onUnmounted} from 'vue';
import {useStore} from 'vuex';
import zhCN from 'ant-design-vue/es/locale/zh_CN';
import dayjs from 'dayjs';
import 'dayjs/locale/zh-cn';
import {updateUserInfo} from "@/utils/tools";
import {getDeviceInfo} from "@/utils/deviceDetection";

// 引入移动端样式
import '@/common/mobile.less';

dayjs.locale('zh-cn');

const store = useStore();

// 设备检测和窗口大小监听
const handleResize = () => {
    const deviceInfo = getDeviceInfo();
    store.commit('setDeviceType', deviceInfo.deviceType);
    store.commit('setIsMobile', deviceInfo.isMobile);
};

onMounted(() => {
    updateUserInfo();
    
    // 初始设备检测
    handleResize();
    
    // 监听窗口大小变化
    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
    window.removeEventListener('orientationchange', handleResize);
});
</script>
<script >
// 解决ERROR ResizeObserver loop completed with undelivered notifications.
//问题的
const debounce = (fn, delay) => {
  let timer = null;
  return function () {
    let context = this;
    let args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      fn.apply(context, args);
    }, delay);
  };
};
// 解决ERROR ResizeObserver loop completed with undelivered notifications.
const _ResizeObserver = window.ResizeObserver;
window.ResizeObserver = class ResizeObserver extends _ResizeObserver {
  constructor(callback) {
    callback = debounce(callback, 16);
    super(callback);
  }
};
</script>

<style lang="less">
@import "./common/common.less";
@import "./common/mobile.less"; // 新增移动端样式

body {
    background-color: #f0f2f5;
}

#app {
    font-size: 14px;
    font-family: Avenir, Helvetica, Arial, sans-serif;
    color: #595959;
}

// 移动端全局样式优化
@media (max-width: 768px) {
    body {
        background-color: #f5f5f5;
        // 防止移动端缩放
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }
    
    #app {
        font-size: 16px; // 移动端字体稍大
        line-height: 1.5;
    }
    
    // 移动端滚动优化
    * {
        -webkit-overflow-scrolling: touch;
    }
}

nav {
    padding: 30px;

    a {
        font-weight: bold;
        color: #2c3e50;

        &.router-link-exact-active {
            color: #42b983;
        }
    }
}
</style>
