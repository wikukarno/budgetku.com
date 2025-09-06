// Vue 3 Composables untuk utility functions
import { ref } from 'vue';

export function useNumberFormat() {
  // Format number dengan Rupiah
  const formatCurrency = (value) => {
    if (value == null || isNaN(value)) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(value);
  };

  // Format number tanpa currency symbol
  const formatNumber = (value) => {
    if (value == null || isNaN(value)) return '0';
    return new Intl.NumberFormat('id-ID').format(value);
  };

  // Format persentase
  const formatPercentage = (value, decimals = 1) => {
    if (value == null || isNaN(value)) return '0%';
    return new Intl.NumberFormat('id-ID', {
      style: 'percent',
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    }).format(value / 100);
  };

  return {
    formatCurrency,
    formatNumber,
    formatPercentage,
  };
}

export function useAlert() {
  const showAlert = (type, message, title = null) => {
    if (typeof window !== 'undefined' && window.Swal) {
      const config = {
        text: message,
        icon: type, // 'success', 'error', 'warning', 'info'
        showConfirmButton: true,
        timer: type === 'success' ? 3000 : undefined,
        timerProgressBar: type === 'success',
      };

      if (title) config.title = title;

      return window.Swal.fire(config);
    } else {
      // Fallback untuk browser alert
      alert(`${type.toUpperCase()}: ${message}`);
      return Promise.resolve();
    }
  };

  const showSuccess = (message, title = 'Success!') => showAlert('success', message, title);
  const showError = (message, title = 'Error!') => showAlert('error', message, title);
  const showWarning = (message, title = 'Warning!') => showAlert('warning', message, title);
  const showInfo = (message, title = 'Info') => showAlert('info', message, title);

  const showConfirm = (message, title = 'Confirm') => {
    if (typeof window !== 'undefined' && window.Swal) {
      return window.Swal.fire({
        title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel'
      });
    }
    return Promise.resolve({ isConfirmed: confirm(message) });
  };

  return {
    showAlert,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showConfirm,
  };
}

export function useLoading() {
  const isLoading = ref(false);
  const loadingMessage = ref('');

  const setLoading = (loading, message = '') => {
    isLoading.value = loading;
    loadingMessage.value = message;
  };

  const withLoading = async (asyncFn, message = 'Processing...') => {
    try {
      setLoading(true, message);
      return await asyncFn();
    } finally {
      setLoading(false);
    }
  };

  return {
    isLoading,
    loadingMessage,
    setLoading,
    withLoading,
  };
}

export function useValidation() {
  const validateEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const validatePassword = (password, minLength = 8) => {
    return password && password.length >= minLength;
  };

  const validateRequired = (value) => {
    return value != null && String(value).trim().length > 0;
  };

  const validateNumeric = (value) => {
    return !isNaN(value) && !isNaN(parseFloat(value));
  };

  const validatePositive = (value) => {
    return validateNumeric(value) && parseFloat(value) > 0;
  };

  return {
    validateEmail,
    validatePassword,
    validateRequired,
    validateNumeric,
    validatePositive,
  };
}

export function useLocalStorage() {
  const setItem = (key, value) => {
    try {
      const serialized = typeof value === 'string' ? value : JSON.stringify(value);
      localStorage.setItem(key, serialized);
      return true;
    } catch (error) {
      console.warn('Failed to save to localStorage:', error);
      return false;
    }
  };

  const getItem = (key, defaultValue = null) => {
    try {
      const item = localStorage.getItem(key);
      if (item === null) return defaultValue;
      
      // Try to parse as JSON, fallback to string
      try {
        return JSON.parse(item);
      } catch {
        return item;
      }
    } catch (error) {
      console.warn('Failed to read from localStorage:', error);
      return defaultValue;
    }
  };

  const removeItem = (key) => {
    try {
      localStorage.removeItem(key);
      return true;
    } catch (error) {
      console.warn('Failed to remove from localStorage:', error);
      return false;
    }
  };

  const clear = () => {
    try {
      localStorage.clear();
      return true;
    } catch (error) {
      console.warn('Failed to clear localStorage:', error);
      return false;
    }
  };

  return {
    setItem,
    getItem,
    removeItem,
    clear,
  };
}

export function useCrypto() {
  const randomBytes = (length) => {
    const array = new Uint8Array(length);
    crypto.getRandomValues(array);
    return array;
  };

  const base64Encode = (bytes) => {
    return btoa(String.fromCharCode(...new Uint8Array(bytes)));
  };

  const base64Decode = (base64String) => {
    const binaryString = atob(base64String);
    const bytes = new Uint8Array(binaryString.length);
    for (let i = 0; i < binaryString.length; i++) {
      bytes[i] = binaryString.charCodeAt(i);
    }
    return bytes;
  };

  const generateRecoveryCode = (length = 26) => {
    const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let result = '';
    const bytes = randomBytes(length);
    
    for (let i = 0; i < length; i++) {
      result += alphabet[bytes[i] % alphabet.length];
    }
    
    // Format sebagai groups of 4 characters
    return result.match(/.{1,4}/g).join('-');
  };

  return {
    randomBytes,
    base64Encode,
    base64Decode,
    generateRecoveryCode,
  };
}

export function useClipboard() {
  const copy = async (text) => {
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(text);
        return true;
      } else {
        // Fallback untuk browser lama
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
          const successful = document.execCommand('copy');
          document.body.removeChild(textArea);
          return successful;
        } catch (error) {
          document.body.removeChild(textArea);
          throw error;
        }
      }
    } catch (error) {
      console.error('Failed to copy text:', error);
      return false;
    }
  };

  return { copy };
}
