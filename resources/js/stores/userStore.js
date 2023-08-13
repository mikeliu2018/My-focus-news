import { ref } from 'vue';
import moment from 'moment';
import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', () => {
  const isLoggedIn = ref(false);
  const email = ref('');
  const name = ref('');
  const token = ref('');
  const expireAt = ref(moment());

  function $reset() {
    isLoggedIn.value = false;
    email.value = '';
    name.value = '';
    token.value = '';
    expireAt.value = moment();
  }
  // const doubleCount = computed(() => count.value * 2)
  return { 
    isLoggedIn, 
    email, 
    name, 
    token, 
    expireAt,
    $reset, 
  };
})