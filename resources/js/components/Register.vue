<template>
  <div>
    <Header title="我的關注新聞" subTitle="會員註冊"></Header>
    <a-card title="會員註冊">
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
        
        <a-form-item has-feedback label="密碼確認" name="checkPassword">
          <a-input v-model:value="formState.checkPassword" type="password" autocomplete="on" />
        </a-form-item>

        <a-form-item :wrapper-col="{ span: 4, offset: 10 }">
          <a-button type="primary" html-type="submit" @click="onSubmit">註冊</a-button>
          <a-button style="margin-left: 10px" @click="resetForm">取消</a-button>
        </a-form-item>
      </a-form>
    </a-card>
  </div>
</template>

<script setup>
  import { reactive, ref, toRaw } from 'vue';
  import { message } from 'ant-design-vue';
  import Header from './Header.vue';

  const formRef = ref();
  
  const formState = reactive({
    email: '',
    password: '',
    checkPassword: '',
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
          if (formState.checkPassword !== '') {
            formRef.value.validateFields('checkPass');
          }
          return Promise.resolve();
        }
      },
      trigger: 'change',
    }],
    checkPassword: [{
      validator: async (_rule, value) => {
        if (value === '') {
          return Promise.reject('請再次輸入密碼');
        } else if (value !== formState.password) {
          return Promise.reject("密碼輸入不匹配!");
        } else {
          return Promise.resolve();
        }
      },
      trigger: 'change',
    }],
  };

  const resetForm = () => {
    formRef.value.resetFields();
  };

  const doRegister = (queryParams) => {
    const API_BASE_URL = `${import.meta.env.VITE_APP_URL}/api/user/register`;
    const defaultParams = {};
    console.log('queryParams', queryParams);
    const params = { ...defaultParams, ...queryParams };
    console.log('params', params);
    const API_URL = `${API_BASE_URL}`;
    // loading.value = true;    
    return window.axios.post(API_URL, params).then((response) => {
      const { config, headers, data, request, status } = response;
      return response;
    });
  };
  
  const onSubmit = () => {
    formRef.value
      .validate()
      .then(async() => {
        const params = toRaw(formState);
        console.log('values', formState, params);
        const response = await doRegister(params);
        if (response?.data?.result) {
          message.info(response.data.result);
        }
      })
      .catch(error => {
        console.log('error', error);
        if (error?.response?.data?.error) {
          message.error(error.response.data.error);
        }
      });
  };
</script>