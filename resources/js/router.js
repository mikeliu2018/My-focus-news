
import { createRouter, createWebHistory } from 'vue-router';
import Home from './components/Home.vue';
import News from './components/News.vue';
import Register from './components/Register.vue';
import Login from './components/Login.vue';
import NewsFocus from './components/NewsFocus.vue';

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
  { 
    path: '/register',
    name: 'register',
    component: Register
  },
  { 
    path: '/login',
    name: 'login',
    component: Login
  },
  { 
    path: '/news_focus',
    name: 'news_focus',
    component: NewsFocus
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;