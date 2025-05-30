export interface User {
    id: number;
    name: string;
    email: string;
    created_at?: string;
    updated_at?: string;
    data_filled?: boolean;
  }
  
  export interface LoginCredentials {
    email: string;
    password: string;
  }
  
  export interface RegisterData {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
  }
  
  export interface AuthResponse {
    user: User;
    token: string;
    message: string;
  }
  