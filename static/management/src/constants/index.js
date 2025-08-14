/**
 * 请求contentType
 */
export const CONTENT_TYPE = 'application/x-www-form-urlencoded;charset=UTF-8'

/**
 * 请求超时时间
 */
export const REQUEST_TIMEOUT = 10 * 60 * 1000

/**
 * 刷新Token时间
 */
export const REFRESHTOKEN_TIMEOUT = 1 * 60 * 60 * 1000

// 默认的用户头像
export const DEFAULT_USER_AVATAR = new URL('@/assets/default-avatar.png', import.meta.url).href

// 新的现代化logo
export const DEFAULT_ZH_LOGO = new URL('@/assets/new-logo.svg', import.meta.url).href

// 新的配色方案
export const THEME_COLORS = {
  primary: '#10B981',
  primaryDark: '#059669',
  background: '#F8FAFC',
  surface: '#FFFFFF',
  textPrimary: '#1F2937',
  textSecondary: '#6B7280',
  border: '#E5E7EB',
  success: '#10B981',
  warning: '#F59E0B',
  error: '#EF4444',
  info: '#3B82F6'
}
