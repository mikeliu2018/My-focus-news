
import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import News from './components/News.vue';

const routes = [
  { 
    path: '/',
    name: 'home',
    component: Home 
  }
  ,
  { 
    path: '/news',
    name: 'news',
    component: News
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;