<template>
  <div>
    <Header title="我的關注新聞" subTitle="中央社新聞"></Header>
    <a-card title="過濾條件">
      <a-form
        ref="formRef"
        :layout="formState.layout"
        v-bind="formItemLayout"
        :model="formState"
        >
        <a-form-item label="類別" name="category">
          <a-select v-model:value="formState.category" placeholder="請選擇一種類別">                     
            <a-select-option v-for="(category) in categorys" :value="category.key">{{category.value}}</a-select-option>
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
    </a-card>
    <a-card title="查詢結果">
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
    </a-card>
  </div>
</template>
<script setup>
  import { computed, reactive, toRaw, ref } from 'vue';
  import { usePagination } from 'vue-request';
  // import moment from 'moment';
  import moment from 'moment-timezone';
  import Header from './Header.vue';

  const categorys = [
    { key: 'all', value: '全部' },
    { key: '政治', value: '政治' },
    { key: '國際', value: '國際' },
    { key: '兩岸', value: '兩岸' },
    { key: '產經證券', value: '產經證券' },
    { key: '科技', value: '科技' },
    { key: '生活', value: '生活' },
    { key: '社會', value: '社會' },
    { key: '地方', value: '地方' },
    { key: '文化', value: '文化' },
    { key: '運動', value: '運動' },
    { key: '娛樂', value: '娛樂' },
  ];

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

  const timezone = moment.tz.guess();  
  console.log('timezone', timezone);
  
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

  const fetchData = (queryParams) => {
    const API_BASE_URL = `${import.meta.env.VITE_APP_URL}/api/news/list`;
    const defaultParams = {
      keyword: '',
    };
    console.log('queryParams', queryParams);
    queryParams?.category && queryParams.category === 'all' && delete queryParams.category;
    if (queryParams?.dateRange && Array.isArray(queryParams.dateRange) && queryParams.dateRange.length > 1) {
      queryParams.start_date = queryParams.dateRange[0];
      queryParams.end_date = queryParams.dateRange[1];
      queryParams.timezone = timezone;
    };
    delete queryParams.dateRange;
    const params = { ...defaultParams, ...queryParams };

    console.log('params', params);
    const API_URL = `${API_BASE_URL}?` + new URLSearchParams(params).toString();
    return window.axios(API_URL).then((response) => {
      // const { config, headers, data, request, status } = response;
      return response;
    });
  };

  const {
    data,
    run,
    loading,
    current,
    pageSize,
  } = usePagination(fetchData, {
    // first parameter will pass to fetchData
    defaultParams: [
      {        
        ...toRaw(formState)
      },
    ],
    pagination: {
      currentKey: 'page',
      pageSizeKey: 'results',
      totalKey: 'data.total',
    },
  });
  
  const dataSource = computed(() => data.value?.data.results.map(item => {
    // for table do index
    item.key = item.id;
    item.gmdate = moment.utc(item.gmdate, 'YYYY-MM-DD hh:mm:ss').tz(timezone).format('YYYY-MM-DD HH:mm:ss');
    return item;
  }) || []);

  const pagination = computed(() => ({    
    total: data.value?.data.total || 0,
    current: current.value,
    pageSize: pageSize.value,
  }));

  const handleTableChange = (pag, filters, sorter) => {
    const queryParams = {
      results: pag.pageSize,
      page: pag?.current,
      sortField: sorter.field,
      sortOrder: sorter.order,
      ...filters,
      ...toRaw(formState),
    };
    run(queryParams);
  };

  const onSubmit = () => {
    const initPage = 1;
    const queryParams = {
      results: pageSize.value,
      page: initPage,
      ...toRaw(formState)
    };
    console.log('submit!', queryParams);    
    run(queryParams);
  };

  const resetForm = () => {
    formRef.value.resetFields();
  };
</script>

<style>
img.news-image {
  object-fit: contain;
  width: 50px;
  height: 35px;
}
</style>