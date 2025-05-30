import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';

// Get base URL from environment variables
const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000';
// Ensure we have a clean path join
const apiUrl = baseUrl.endsWith('/') ? `${baseUrl}api` : `${baseUrl}/api`;

// Helper function to get cookies
const getCookie = (name: string): string => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) {
    const cookieValue = parts.pop();
    if (cookieValue) {
      return cookieValue.split(';').shift() || '';
    }
  }
  return '';
};

// Function to get CSRF token
export const getCsrfCookie = async (): Promise<void> => {
  try {
    await axios.get(`${baseUrl}/sanctum/csrf-cookie`, {
      withCredentials: true
    });
  } catch (error) {
    console.error('Failed to fetch CSRF cookie:', error);
  }
};

// Create axios instance with default config
const axiosInstance = axios.create({
  baseURL: apiUrl,
  headers: {
    'Accept': 'application/json'
  },
  withCredentials: true // This ensures cookies are sent with cross-origin requests
});

// Add request interceptor
axiosInstance.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    // Add session ID if needed
    const sessionId = localStorage.getItem('sessionId') || Date.now().toString();
    if (sessionId) {
      config.headers['X-Session-Id'] = sessionId;
    }
    
    // Add CSRF token if available
    const csrfToken = getCookie('XSRF-TOKEN');
    if (csrfToken) {
      config.headers['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken);
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add response interceptor
axiosInstance.interceptors.response.use(
  (response) => {
    return response;
  },
  async (error) => {
    // Handle 419 CSRF token mismatch errors
    if (error.response && error.response.status === 419) {
      try {
        await getCsrfCookie();
        const originalRequest = error.config;
        const csrfToken = getCookie('XSRF-TOKEN');
        if (csrfToken) {
          originalRequest.headers['X-XSRF-TOKEN'] = decodeURIComponent(csrfToken);
        }
        return axios(originalRequest);
      } catch (retryError) {
        console.error('Failed to refresh CSRF token:', retryError);
      }
    }
    
    // Handle 401 Unauthorized errors
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    
    return Promise.reject(error);
  }
);

// Define interface for API options
interface ApiOptions {
  params?: Record<string, any>;
  headers?: Record<string, string>;
  isMultipart?: boolean;
  [key: string]: any;
}

// Main API object with methods similar to your React implementation
export const api = {
  async request(endpoint: string, options: ApiOptions = {}) {
    const config: AxiosRequestConfig = {
      ...options,
      headers: { ...options.headers }
    };
    
    // Handle multipart form data
    if (options.isMultipart) {
      config.headers = {
        ...config.headers,
        'Content-Type': 'multipart/form-data'
      };
    } else if (!options.headers?.['Content-Type']) {
      config.headers = {
        ...config.headers,
        'Content-Type': 'application/json'
      };
    }
    
    // Add query parameters if provided
    if (options.params) {
      config.params = options.params;
    }
    
    try {
      const response: AxiosResponse = await axiosInstance(endpoint, config);
      return response.data;
    } catch (error: any) {
      // Format error response similar to your React implementation
      return {
        success: false,
        message: error.response?.data?.message || error.message || 'An error occurred',
        error: error
      };
    }
  },
  
  // Convenience methods
  get(endpoint: string, options?: ApiOptions) {
    return this.request(endpoint, { ...options, method: 'GET' });
  },
  
  post(endpoint: string, body: any, options?: ApiOptions) {
    return this.request(endpoint, {
      ...options,
      method: 'POST',
      data: body // Axios uses 'data' instead of 'body'
    });
  },
  
  put(endpoint: string, body: any, options?: ApiOptions) {
    return this.request(endpoint, {
      ...options,
      method: 'PUT',
      data: body
    });
  },
  
  delete(endpoint: string, options?: ApiOptions) {
    return this.request(endpoint, { ...options, method: 'DELETE' });
  }
};

// For backward compatibility, also export the axios instance
export default axiosInstance;
