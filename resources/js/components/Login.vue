<template>
  <div>
    <Header title="我的關注新聞" subTitle="會員登入"></Header>
    <a-card title="會員登入">
      <a-form
        ref="formRef"
        name="register"        
        :model="formState"        
        :label-col="{ span: 4, offset: 6 }"
        :wrapper-col="{ span: 4 }"
        :rules="rules"
      >
        <a-form-item has-feedback label="電子信箱" name="email">
          <a-input v-model:value="formState.email" autocomplete="on" />
        </a-form-item>

        <a-form-item has-feedback label="密碼" name="password">
          <a-input-password v-model:value="formState.password" autocomplete="on" />
        </a-form-item>        

        <a-form-item :wrapper-col="{ span: 4, offset: 10 }">
          <a-button type="primary" html-type="submit" @click="onSubmit">登入</a-button>
          <a-button style="margin-left: 10px" @click="resetForm">取消</a-button>          
        </a-form-item>

        <a-form-item :wrapper-col="{ span: 4, offset: 10 }">
          <a @click="goRegister">註冊新帳號</a>
        </a-form-item>        
      </a-form>      
    </a-card>
  </div>
</template>

<script setup>
  import { reactive, ref, toRaw } from 'vue';
  import moment from 'moment';
  import { useRouter } from 'vue-router';
  import { message } from 'ant-design-vue';  
  import { storeToRefs } from 'pinia';
  import { useUserStore } from '@/stores';
  import Header from './Header.vue';

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

  const goRegister = () => {
    router.push({name: 'register'});
  }
</script>