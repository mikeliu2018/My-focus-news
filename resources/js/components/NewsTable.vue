<template>
  <div>
    <a-page-header
      style="border: 1px solid rgb(235, 237, 240)"
      title="國內外新聞"
      :breadcrumb="{ breadcrumb }"
      sub-title="中央社新聞"
    >
      <a-form            
        ref="formRef"
        :layout="formState.layout"
        v-bind="formItemLayout"
        :model="formState"
        >
        <a-form-item label="類別" name="category">
          <a-select v-model:value="formState.category" placeholder="請選擇一種類別">
            <a-select-option value="all" selected="selected">全部</a-select-option>
            <a-select-option value="政治">政治</a-select-option>
            <a-select-option value="國際">國際</a-select-option>
            <a-select-option value="兩岸">兩岸</a-select-option>
            <a-select-option value="產經證券">產經證券</a-select-option>
            <a-select-option value="科技">科技</a-select-option>
            <a-select-option value="生活">生活</a-select-option>
            <a-select-option value="社會">社會</a-select-option>
            <a-select-option value="地方">地方</a-select-option>
            <a-select-option value="文化">文化</a-select-option>
            <a-select-option value="運動">運動</a-select-option>
            <a-select-option value="娛樂">娛樂</a-select-option>
          </a-select>
        </a-form-item>
        <a-form-item ref="dateRange" name="dateRange" label="發佈時間">
          <a-range-picker v-model:value="formState.dateRange" value-format="YYYY-MM-DD" />
        </a-form-item>
        <a-form-item label="關鍵字" ref="keyword" name="keyword">
          <a-input v-model:value="formState.keyword" />
        </a-form-item>
        <a-form-item>
          <a-button type="primary" @click="onSubmit">查詢</a-button>
          <a-button style="margin-left: 10px" @click="resetForm">取消</a-button>
        </a-form-item>
      </a-form>
    </a-page-header>    

    <a-table 
      :columns="columns"
      :dataSource="dataSource"
      :pagination="pagination"      
      :loading="loading" 
      :expand-column-width="100"
      @change="handleTableChange"
      >
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'image' && record.image !== ''">        
          <img :src="`${record.image}`" class="news-image">
        </template>
        <template v-else-if="column.key === 'link'">
          <a :href="`${record.link}`" target="_blank">{{ record.link }}</a>
        </template>
      </template>
      <template #expandedRowRender="{ record }">
        <p style="margin: 0">
          {{ record.content }}
        </p>
      </template>
      <template #expandColumnTitle>
        <span style="color: red">More</span>
      </template>
    </a-table>
  </div>
</template>
<script setup>
  import { onMounted, computed, reactive, toRaw, ref } from 'vue';
  import { usePagination } from 'vue-request';
  const breadcrumb = [];
  const fetchData = (queryParams) => {
    const API_BASE_URL = `http://localhost/api/news/list`;
    const defaultParams = {
      keyword: ''
    };

    queryParams?.category && queryParams.category === 'all' && delete queryParams.category;
    if (queryParams?.dateRange && Array.isArray(queryParams.dateRange) && queryParams.dateRange.length > 1) {
      queryParams.start_date = queryParams.dateRange[0];
      queryParams.end_date = queryParams.dateRange[1];
    };
    delete queryParams.dateRange;
    console.log('queryParams', queryParams);
    const params = { ...defaultParams, ...queryParams };

    console.log('params', params);
    const API_URL = `${API_BASE_URL}?` + new URLSearchParams(params).toString();
    // loading.value = true;    
    return window.axios(API_URL).then((response) => {
      const { config, headers, data, request, status } = response;
      return response;
    }).catch((error)=> { 
      console.error(error);
      return error;
    });
  };

  const columns = [
    {
      title: '類別',
      dataIndex: 'category',
      key: 'category',
    },
    {
      title: '參考照片',
      dataIndex: 'image',
      key: 'image',
    },
    {
      title: '標題',
      dataIndex: 'title',
      key: 'title',
    },
    {
      title: '發佈時間',
      dataIndex: 'gmdate',
      key: 'gmdate',
    },
    {
      title: '來源網址',
      dataIndex: 'link',
      key: 'link',
    }
  ];

  const {
    data,
    run,
    loading,
    current,
    pageSize,
  } = usePagination(fetchData, {
    pagination: {
      currentKey: 'page',
      pageSizeKey: 'results',
      totalKey: 'data.total',
    },
  });
  
  const dataSource = computed(() => data.value?.data.results.map(item => {
    // for table do index
    item.key = item.id
    return item;
  }) || []);

  const pagination = computed(() => ({    
    total: data.value?.data.total || 0,
    current: current.value,
    pageSize: pageSize.value,
  }));

  const handleTableChange = (pag, filters, sorter) => {
    const params = toRaw(formState);
    run({
      results: pag.pageSize,
      page: pag?.current,
      sortField: sorter.field,
      sortOrder: sorter.order,
      ...filters,
      ...params,
    });
  };

  const formRef = ref();

  const formState = reactive({
    layout: "inline",
    keyword: '',   
    category: 'all',
    dateRange: []
  });  

  const formItemLayout = computed(() => {
    const {
      layout,
    } = formState;
    return layout === 'horizontal' ? {
      labelCol: {
        span: 2,
      },
      wrapperCol: {
        span: 4,
      },
    } : {};
  });

  const onSubmit = () => {
    const params = toRaw(formState);
    console.log('submit!', params);
    run({
      results: pageSize.value,
      page: 1,      
      ...params,
    });
  };

  const resetForm = () => {
    formRef.value.resetFields();
  };

  onMounted(() => {        
    console.log('onMounted');
  });          
</script>

<style>
img.news-image {
  object-fit: contain;
  width: 50px;
  height: 35px;
}
</style>