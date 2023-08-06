<template>
  <div>
    <a-page-header
      style="border: 1px solid rgb(235, 237, 240)"
      title="國內外新聞"
      :breadcrumb="{ breadcrumb }"
      sub-title="中央社新聞"
    />    
    <a-table :dataSource="dataSource" :columns="columns" :loading="loading" :expand-column-width="100">
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
  import { onMounted, ref  } from 'vue'
  const breadcrumb = [];
  const API_BASE_URL = `http://localhost/api/news/list`;
  const params = {
    start_date: '2023-08-01',
    end_date: '2023-08-30',
    keyword: ''
  };
  const dataSource = ref([]);
  const loading = ref(false);

  const fetchData = () => {
    const API_URL = `${API_BASE_URL}?` + new URLSearchParams(params).toString();
    loading.value = true;    
    window.axios(API_URL).then((response) => {
      const { config, headers, data, request, status } = response;
      data.forEach(item => {
        dataSource.value.push({
          key: item.id,
          category: item.category,
          image: item.image,
          title: item.title,
          content: item.content,
          gmdate: item.gmdate,
          link: item.link,
        });
      });
    }).catch((error)=> { 
      console.error(error)
    }).finally(function () {
      loading.value = false;
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

  onMounted(async() => {    
    fetchData();
  });          
</script>

<style>
img.news-image {
  object-fit: contain;
  width: 50px;
  height: 35px;
}
</style>