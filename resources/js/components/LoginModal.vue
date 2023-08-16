<template>
  <div>
    <a-modal v-model:open="open" title="會員登入" @ok="handleOk">
      <a-card title="會員登入">
        <a-form
          ref="formRef"
          name="register"        
          :model="formState"        
          :label-col="{ span: 6, offset: 3 }"
          :wrapper-col="{ span: 10 }"
          :rules="rules"
        >
          <a-form-item has-feedback label="電子信箱" name="email">
            <a-input v-model:value="formState.email" autocomplete="on" />
          </a-form-item>

          <a-form-item has-feedback label="密碼" name="password">
            <a-input-password v-model:value="formState.password" autocomplete="on" />
          </a-form-item>

          <a-form-item :wrapper-col="{ span: 4, offset: 10 }">
            <a @click="goRegister">註冊新帳號</a>
          </a-form-item>
        </a-form>      
      </a-card>
      <template #footer>
        <a-button key="back" @click="handleCancel">取消</a-button>
        <a-button key="submit" type="primary" :loading="loading" @click="handleOk">登入</a-button>
      </template>
    </a-modal>
  </div>
</template>

<script setup>
  import { computed, reactive, ref, toRaw } from 'vue';
  import moment from 'moment';
  import { useRouter } from 'vue-router';
  import { message } from 'ant-design-vue';  
  import { storeToRefs } from 'pinia';
  import { useUserStore } from '@/stores';

  const props = defineProps(['open']);
  const emit = defineEmits(['update:open']);

  const openLoginModal = ref(false);

  const open = computed({
    get() {
      return props.open;
    },
    set(value) {
      emit('update:open', value);
    }
  });

  const userStore = useUserStore();
  const router = useRouter();
  const formRef = ref();  
  const formState = reactive({
    email: '',
    password: '',
  });  

  const rules = {
    email: [{
      require: true,
      validator: async (_rule, value) => {
        if (value === '') {
          return Promise.reject('請輸入帳號');
        } else {
          const regex = /[A-Za-z0-9_.]{1,}\@[A-Za-z]{2,}\.[A-Za-z]{2,}/g;
          if (formState.email.match(regex) === null) {
            return Promise.reject('僅接受英文大小寫,數字,特殊符號:_.');
          }
          return Promise.resolve();
        }
      },
      trigger: 'change',
    }],
    password: [{
      required: true,
      validator: async (_rule, value) => {
        if (value === '') {
          return Promise.reject('請輸入密碼');
        } else {
          const regex = /[A-Za-z0-9!@#$%^&*_.]{8,}/g;
          if (formState.password.match(regex) === null) {
            return Promise.reject('僅接受8碼以上英文大小寫,數字,特殊符號:!@#$%^&*_.');
          }
          return Promise.resolve();
        }
      },
      trigger: 'change',
    }],    
  };

  const resetForm = () => {
    formRef.value.resetFields();
  };  

  const doLogin = (queryParams) => {
    const API_BASE_URL = `${import.meta.env.VITE_APP_URL}/api/user/login`;
    const defaultParams = {};
    console.log('queryParams', queryParams);
    const params = { ...defaultParams, ...queryParams };
    console.log('params', params);
    const API_URL = `${API_BASE_URL}`;
    // loading.value = true;    
    return window.axios.put(API_URL, params).then((response) => {
      const { config, headers, data, request, status } = response;
      return response;
    });
  };

  const onSubmit = () => {
    formRef.value
      .validate()
      .then(async() => {
        const params = toRaw(formState)
        console.log('values', formState, params);
        const response = await doLogin(params);        
        if (response?.data?.result) {
          message.info(response.data.result);
          const { isLoggedIn, email, name, token, expireAt } = storeToRefs(userStore);
          console.log('isLoggedIn', isLoggedIn.value);
          console.log('email', email.value);
          console.log('name', name.value);
          console.log('token', token.value);
          console.log('expireAt', moment(expireAt.value));
          userStore.$patch({
            isLoggedIn: true,
            email: response.data.user.email,
            name: response.data.user.name,
            token: response.data.token.access_token,
            expireAt: moment().add(response.data.token.expires_in, 'seconds'),
          });
          console.log('isLoggedIn', isLoggedIn.value);
          console.log('email', email.value);
          console.log('name', name.value);
          console.log('token', token.value);
          console.log('expireAt', expireAt.value);

          router.push({name: 'home'});
        }
      })
      .catch(error => {
        console.log('error', error);
        if (error?.response?.data?.error) {
          message.error(error.response.data.error);
        }
      });
  };

  const loading = ref(false);

  const handleOk = () => {
    loading.value = true;
    formRef.value
      .validate()
      .then(async() => {
        const params = toRaw(formState)
        console.log('values', formState, params);
        const response = await doLogin(params);        
        if (response?.data?.result) {
          message.info(response.data.result);
          const { isLoggedIn, email, name, token, expireAt } = storeToRefs(userStore);
          console.log('isLoggedIn', isLoggedIn.value);
          console.log('email', email.value);
          console.log('name', name.value);
          console.log('token', token.value);
          console.log('expireAt', moment(expireAt.value));
          userStore.$patch({
            isLoggedIn: true,
            email: response.data.user.email,
            name: response.data.user.name,
            token: response.data.token.access_token,
            expireAt: moment().add(response.data.token.expires_in, 'seconds'),
          });
          console.log('isLoggedIn', isLoggedIn.value);
          console.log('email', email.value);
          console.log('name', name.value);
          console.log('token', token.value);
          console.log('expireAt', expireAt.value);
          setTimeout(() => {
            loading.value = false;
            open.value = false;
          }, 500);
        }
      })
      .catch(error => {
        console.log('error', error);
        loading.value = false;
        if (error?.response?.data?.error) {
          message.error(error.response.data.error);
        }
      })
      .finally(() => {
        
      })
  };

  const handleCancel = () => {
    formRef.value.resetFields();
    open.value = false;
  };

  const goRegister = () => {
    router.push({name: 'register'});
  }
</script>