<template>
  <a-menu 
    v-model:selectedKeys="current"
    mode="horizontal"
    :items="items"
    @select="onSelect" />
</template>
<script setup>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useUserStore } from '@/stores';

const router = useRouter();
const route = useRoute();
const userStore = useUserStore();

const current = ref([`${route.name}`]);
const items = computed(() => {
  const items = [
    {
      key: 'home',
      label: '首頁',
      title: '首頁',
    }, {
      key: 'news',
      label: '國內外新聞',
      title: '國內外新聞',
    },
  ];

  const { isLoggedIn } = storeToRefs(userStore);

  isLoggedIn.value === false ? items.push({
      key: 'login',
      label: '會員登入',
      title: '會員登入',
    }) : items.push({
      key: 'logout',
      label: '會員登出',
      title: '會員登出',
    });
  return items;
});

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

const onSelect = ({ item, key, selectedKeys }) => {
  if (key === 'logout') {
    doLogout()
    .then(response => {
      console.log('logout response', response);      
      userStore.$reset();
    })
    .catch(error => {
      console.error(error);
    })
  } else {
    router.push({name: key});
  }  
}
</script>