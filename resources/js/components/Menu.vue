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
  isLoggedIn.value === true && items.push(
    {
      key: 'news_focus',
      label: '我的關注',
      title: '我的關注',
    }
  );
  return items;
});

const onSelect = ({ item, key, selectedKeys }) => {
  router.push({name: key}); 
}
</script>