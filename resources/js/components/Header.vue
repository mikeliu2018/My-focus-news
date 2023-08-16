<template>
  <div>
    <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        :title="`${title}`"
        :breadcrumb="{ breadcrumb }"
        :sub-title="`${subTitle}`"
        >
        <template #extra>
          <a-button v-if="isLoggedIn === false" key="login" type="primary" @click="onClickLogin">會員登入</a-button>
          <a-button v-else-if="isLoggedIn === true" key="logout" type="primary" @click="onClickLogout">會員登出</a-button>
        </template>
        <Menu></Menu>
    </a-page-header>
  </div>
</template>

<script setup>
  import { useRouter } from 'vue-router';
  import { message } from 'ant-design-vue';
  import { storeToRefs } from 'pinia';
  import { useUserStore } from '@/stores';
  import Menu from './Menu.vue';    
  const props = defineProps(['title', 'breadcrumb', 'subTitle']);
  const title = props?.title || '';
  const subTitle = props?.subTitle || '';
  const breadcrumb = props?.breadcrumb || [];
  const userStore = useUserStore();
  const { isLoggedIn } = storeToRefs(userStore);
  const router = useRouter();

  const onClickLogin = () => {
    router.push({name: 'login'});
  };

  const doLogout = () => {
    const API_BASE_URL = `${import.meta.env.VITE_APP_URL}/api/user/logout`;
    const defaultParams = {};  
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
    return window.axios.put(API_URL, params, headers).then((response) => {
      const { config, headers, data, request, status } = response;
      return response;
    });
  };

  const onClickLogout = () => {
    doLogout()
    .then(response => {
      console.log('logout response', response);
      if (response?.data?.result) {
        message.info(response.data.result);
        userStore.$reset();
      }      
    })
    .catch(error => {
      console.error(error);
    })
  }
</script>