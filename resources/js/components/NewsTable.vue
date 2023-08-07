<template>
  <div>
    <a-page-header
      style="border: 1px solid rgb(235, 237, 240)"
      title="國內外新聞"
      :breadcrumb="{ breadcrumb }"
      sub-title="中央社新聞"
    />    
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
  import { onMounted, computed  } from 'vue';
  import { usePagination } from 'vue-request';
  const breadcrumb = [];
  const fetchData = (queryParams) => {
    const API_BASE_URL = `http://localhost/api/news/list`;
    const defaultParams = {
      start_date: '2023-08-01',
      end_date: '2023-08-30',
      keyword: ''
    };
    const params = { ...queryParams, ...defaultParams };

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
    run({
      results: pag.pageSize,
      page: pag?.current,
      sortField: sorter.field,
      sortOrder: sorter.order,
      ...filters,
    });
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