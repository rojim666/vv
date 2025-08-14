<template>
  <div class="mobile-table">
    <!-- 移动端卡片式表格 -->
    <div class="mobile-table-cards">
      <div 
        v-for="(record, index) in dataSource" 
        :key="index"
        class="mobile-table-card"
        @click="$emit('rowClick', record, index)"
      >
        <div 
          v-for="column in visibleColumns" 
          :key="column.dataIndex"
          class="mobile-table-row"
        >
          <div class="mobile-table-label">{{ column.title }}</div>
          <div class="mobile-table-value">
            <slot 
              v-if="column.customRender" 
              :name="column.dataIndex" 
              :record="record" 
              :index="index"
            >
              {{ record[column.dataIndex] }}
            </slot>
            <span v-else>{{ record[column.dataIndex] }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 分页 -->
    <div v-if="pagination" class="mobile-table-pagination">
      <a-pagination
        v-model:current="currentPage"
        v-model:page-size="pageSize"
        :total="pagination.total"
        :show-size-changer="false"
        :show-quick-jumper="false"
        :show-total="(total, range) => `${range[0]}-${range[1]} 共 ${total} 条`"
        size="small"
        @change="handlePageChange"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  dataSource: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: [Object, Boolean],
    default: false
  },
  hiddenColumns: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['rowClick', 'pageChange'])

const currentPage = ref(1)
const pageSize = ref(10)

// 过滤掉在移动端需要隐藏的列
const visibleColumns = computed(() => {
  return props.columns.filter(column => 
    !props.hiddenColumns.includes(column.dataIndex)
  )
})

const handlePageChange = (page, size) => {
  currentPage.value = page
  pageSize.value = size
  emit('pageChange', { page, pageSize: size })
}
</script>

<style scoped lang="less">
.mobile-table {
  .mobile-table-cards {
    .mobile-table-card {
      background: #fff;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: all 0.3s;
      
      &:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }
      
      .mobile-table-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        
        &:last-child {
          border-bottom: none;
        }
        
        .mobile-table-label {
          font-weight: 500;
          color: #666;
          font-size: 12px;
          min-width: 80px;
        }
        
        .mobile-table-value {
          flex: 1;
          text-align: right;
          font-size: 14px;
          color: #333;
        }
      }
    }
  }
  
  .mobile-table-pagination {
    margin-top: 16px;
    text-align: center;
  }
}
</style>