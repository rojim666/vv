/**
 * 设备检测工具函数
 */

// 检测是否为移动设备
export function isMobileDevice() {
    const userAgent = navigator.userAgent.toLowerCase()
    return /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(userAgent)
}

// 检测是否为平板设备
export function isTabletDevice() {
    const userAgent = navigator.userAgent.toLowerCase()
    return /ipad|android(?!.*mobile)/i.test(userAgent)
}

// 检测设备类型
export function getDeviceType() {
    if (isTabletDevice()) {
        return 'tablet'
    } else if (isMobileDevice()) {
        return 'mobile'
    } else {
        return 'desktop'
    }
}

// 检测屏幕尺寸
export function getScreenSize() {
    const width = window.innerWidth
    const height = window.innerHeight
    
    if (width <= 480) {
        return 'xs' // 超小屏
    } else if (width <= 768) {
        return 'sm' // 小屏
    } else if (width <= 992) {
        return 'md' // 中屏
    } else if (width <= 1200) {
        return 'lg' // 大屏
    } else {
        return 'xl' // 超大屏
    }
}

// 检测是否为微信环境
export function isWeChatEnvironment() {
    const userAgent = navigator.userAgent.toLowerCase()
    return /micromessenger/i.test(userAgent)
}

// 检测是否为企业微信环境
export function isWorkWeChatEnvironment() {
    const userAgent = navigator.userAgent.toLowerCase()
    return /wxwork/i.test(userAgent)
}

// 综合设备信息检测
export function getDeviceInfo() {
    return {
        deviceType: getDeviceType(),
        screenSize: getScreenSize(),
        isMobile: isMobileDevice(),
        isTablet: isTabletDevice(),
        isWeChat: isWeChatEnvironment(),
        isWorkWeChat: isWorkWeChatEnvironment(),
        userAgent: navigator.userAgent,
        screenWidth: window.innerWidth,
        screenHeight: window.innerHeight
    }
}