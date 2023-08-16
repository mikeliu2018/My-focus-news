<template>
  <a-drawer 
    width="400"    
    placement="right" 
    :closable="true" 
    v-model:open="open"
    v-model:refresh="refresh"
    :record="record"
    @close="onClose"
  >
    <a-card :title="`${record.category}`" hoverable style="width: 100%;">
      <template #extra>發佈時間：{{record.gmdate}}</template>
      <template #cover v-if="record.image !== ''">
        <img :src="`${record.image}`" />
      </template>
      <template #actions>
        <comment-outlined key="comment-outlined" @click="onClickComment(record)"></comment-outlined>
        <heart-outlined v-if="record?.user_id === undefined || record.user_id === null" key="heart-outlined" @click="onClickHeart(record)"></heart-outlined>
        <heart-filled v-if="record?.user_id !== undefined && record.user_id !== null" key="heart-filled" @click="onClickHeart(record)"></heart-filled>
      </template>
      <a-card-meta :title="`${record.title}`">
        <template #description>{{record.content}}</template>
      </a-card-meta>
    </a-card>
  </a-drawer>
  <LoginModal v-if="openLoginModal === true" v-model:open="openLoginModal"></LoginModal>
</template>

<script setup>
  import { ref, computed } from 'vue';
  import { HeartOutlined, HeartFilled, CommentOutlined } from '@ant-design/icons-vue';
  import { message } from 'ant-design-vue';
  import { storeToRefs } from 'pinia';
  import { useUserStore } from '@/stores';  
  import LoginModal from './LoginModal.vue';
  const props = defineProps(['open', 'record', 'refresh']);
  const emit = defineEmits(['update:open', 'update:refresh']);
  const record = ref(props.record);
  const openLoginModal = ref(false);
  const open = computed({
    get() {      
      console.log('record', record.value);
      return props.open;
    },
    set(value) {
      emit('update:open', value);
    }
  });
  
  const refresh = computed({
    get() {
      console.log('refresh', props.refresh);
      return props.refresh;
    },
    set(value) {
      emit('update:refresh', value);
    }
  })

  const userStore = useUserStore();  

  const doFocusNewsAdd = (record) => {
    const API_BASE_URL = `${import.meta.env.VITE_APP_URL}/api/news_focus/add`;
    const defaultParams = {
      id: record.id,
    };  
    const params = { ...defaultParams };
    console.log('params', params);
    const API_URL = `${API_BASE_URL}`;  
    const { token } = storeToRefs(userStore);
    const headers = {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization' : `Bearer ${token.value}`
      }
    };
    return window.axios.post(API_URL, params, headers).then((response) => {
      const { config, headers, data, request, status } = response;
      return response;
    });    
  }

  const doFocusNewsRemove = (record) => {
    const API_BASE_URL = `${import.meta.env.VITE_APP_URL}/api/news_focus/remove`;
    const defaultParams = {
      id: record.id,
    };  
    const params = { ...defaultParams };
    console.log('params', params);
    const API_URL = `${API_BASE_URL}`;  
    const { token } = storeToRefs(userStore);
    const headers = {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization' : `Bearer ${token.value}`
      }
    };
    return window.axios.delete(API_URL, { ...headers, params}).then((response) => {
      const { config, headers, data, request, status } = response;
      return response;
    });    
  }
  
  const onClose = () => {
    console.log('onClose');
  }

  const onClickComment = (record) => {
    console.log('onClickComment', record);
    const { isLoggedIn } = storeToRefs(userStore);
    if (isLoggedIn.value === false) {
      openLoginModal.value = true;
    } else {
      message.warning('施工中，敬請期待');
    }
  }

  const onClickHeart = (record) => {
    console.log('onClickHeart', record);
    const { isLoggedIn } = storeToRefs(userStore);
    if (isLoggedIn.value === false) {
      openLoginModal.value = true;
    } else {
      if (record?.user_id === undefined || record.user_id === null) {
        doFocusNewsAdd(record)
        .then(response => {
          console.log('response', response);
          if (response?.data?.result) {
            open.value = false;
            refresh.value = true;
            message.info(response.data.result);            
          }      
        })
        .catch(error => {
          console.error(error);
        });
      } else if (record?.user_id !== undefined && record.user_id !== null) {
        doFocusNewsRemove(record)
        .then(response => {
          console.log('response', response);
          if (response?.data?.result) {
            open.value = false;
            refresh.value = true;
            message.info(response.data.result);            
          }      
        })
        .catch(error => {
          console.error(error);
        });
      }
      
    }
  }  

</script>