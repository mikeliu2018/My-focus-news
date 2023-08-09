<template>
  <a-menu 
    v-model:selectedKeys="current"
    mode="horizontal"
    :items="items"
    @select="onSelect" />
</template>
<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

const current = ref([`${route.name}`]);
const items = ref([
  {
    key: 'home',
    label: '首頁',
    title: '首頁',
  }, {
    key: 'news',
    label: '國內外新聞',
    title: '國內外新聞',
  }
]);

const onSelect = ({ item, key, selectedKeys }) => {
  router.push({name: key});
}
</script>