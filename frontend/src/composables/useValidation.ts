import { ref } from 'vue';

export function useValidation() {
  const errors = ref<Record<string, string>>({});

  const validateEmail = (email: string): boolean => {
    if (!email) {
      errors.value.email = 'Email is required';
      return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      errors.value.email = 'Please enter a valid email address';
      return false;
    }
    
    delete errors.value.email;
    return true;
  };

  const validatePassword = (password: string, minLength = 8): boolean => {
    if (!password) {
      errors.value.password = 'Password is required';
      return false;
    }
    
    if (password.length < minLength) {
      errors.value.password = `Password must be at least ${minLength} characters`;
      return false;
    }
    
    delete errors.value.password;
    return true;
  };

  const validatePasswordConfirmation = (password: string, confirmation: string): boolean => {
    if (!confirmation) {
      errors.value.passwordConfirmation = 'Please confirm your password';
      return false;
    }
    
    if (password !== confirmation) {
      errors.value.passwordConfirmation = 'Passwords do not match';
      return false;
    }
    
    delete errors.value.passwordConfirmation;
    return true;
  };

  const validateRequired = (field: string, value: string, fieldName: string): boolean => {
    if (!value) {
      errors.value[field] = `${fieldName} is required`;
      return false;
    }
    
    delete errors.value[field];
    return true;
  };

  const clearErrors = () => {
    errors.value = {};
  };

  const hasErrors = (): boolean => {
    return Object.keys(errors.value).length > 0;
  };

  return {
    errors,
    validateEmail,
    validatePassword,
    validatePasswordConfirmation,
    validateRequired,
    clearErrors,
    hasErrors
  };
}
