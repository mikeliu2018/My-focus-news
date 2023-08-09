<template>
  <div>
    <a-page-header
        style="border: 1px solid rgb(235, 237, 240)"
        :title="`${title}`"
        :breadcrumb="{ breadcrumb }"
        :sub-title="`${subTitle}`"
        >
        <Menu></Menu>
    </a-page-header>
  </div>
</template>

<script setup>
    import Menu from './Menu.vue';
    const props = defineProps(['title', 'breadcrumb', 'subTitle']);
    const title = props?.title || '';
    const subTitle = props?.subTitle || '';
    const breadcrumb = props?.breadcrumb || [];
</script>