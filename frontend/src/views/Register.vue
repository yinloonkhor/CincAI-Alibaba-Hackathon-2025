<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create a new account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Or
          <router-link to="/login" class="font-medium text-blue-600 hover:text-blue-500">
            sign in to your existing account
          </router-link>
        </p>
      </div>
  
      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <app-alert
            v-if="error"
            type="error"
            :message="error"
            @close="error = ''"
          />
  
          <form class="space-y-6" @submit.prevent="handleRegister">
            <div>
              <app-input
                v-model="name"
                label="Full name"
                type="text"
                required
                :error="nameError"
              />
            </div>
  
            <div>
              <app-input
                v-model="email"
                label="Email address"
                type="email"
                required
                :error="emailError"
              />
            </div>
  
            <div>
              <app-input
                v-model="password"
                label="Password"
                type="password"
                required
                :error="passwordError"
                hint="Password must be at least 8 characters"
              />
            </div>
  
            <div>
              <app-input
                v-model="passwordConfirmation"
                label="Confirm password"
                type="password"
                required
                :error="passwordConfirmationError"
              />
            </div>
  
            <div>
              <app-button
                type="submit"
                :loading="loading"
                fullWidth
              >
                Register
              </app-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import { useAuthStore } from '@/stores/auth';
  import AppInput from '@/components/common/AppInput.vue';
  import AppButton from '@/components/common/AppButton.vue';
  import AppAlert from '@/components/common/AppAlert.vue';
  
  const router = useRouter();
  const authStore = useAuthStore();
  
  const name = ref('');
  const email = ref('');
  const password = ref('');
  const passwordConfirmation = ref('');
  const error = ref('');
  const nameError = ref('');
  const emailError = ref('');
  const passwordError = ref('');
  const passwordConfirmationError = ref('');
  const loading = ref(false);
  
  const validateForm = () => {
    let isValid = true;
    nameError.value = '';
    emailError.value = '';
    passwordError.value = '';
    passwordConfirmationError.value = '';
  
    if (!name.value) {
      nameError.value = 'Name is required';
      isValid = false;
    }
  
    if (!email.value) {
      emailError.value = 'Email is required';
      isValid = false;
    } else if (!/^\S+@\S+\.\S+$/.test(email.value)) {
      emailError.value = 'Please enter a valid email address';
      isValid = false;
    }
  
    if (!password.value) {
      passwordError.value = 'Password is required';
      isValid = false;
    } else if (password.value.length < 8) {
      passwordError.value = 'Password must be at least 8 characters';
      isValid = false;
    }
  
    if (!passwordConfirmation.value) {
      passwordConfirmationError.value = 'Please confirm your password';
      isValid = false;
    } else if (password.value !== passwordConfirmation.value) {
      passwordConfirmationError.value = 'Passwords do not match';
      isValid = false;
    }
  
    return isValid;
  };
  
  const handleRegister = async () => {
    if (!validateForm()) return;
  
    loading.value = true;
    error.value = '';
    
    try {
      await authStore.register({
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value
      });
      
      // Show success message and redirect to login
      router.push({
        path: '/login',
        query: { registered: 'success' }
      });
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Registration failed. Please try again.';
      
      // Handle validation errors from Laravel
      if (err.response?.data?.errors) {
        const errors = err.response.data.errors;
        if (errors.name) nameError.value = errors.name[0];
        if (errors.email) emailError.value = errors.email[0];
        if (errors.password) passwordError.value = errors.password[0];
      }
    } finally {
      loading.value = false;
    }
  };
  </script>
  