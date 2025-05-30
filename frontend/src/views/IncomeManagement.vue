<template>
  <div class="min-h-screen bg-gray-50">
    <app-header />

    <div class="py-4 sm:py-6">
      <header>
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
          <h1 class="text-xl sm:text-2xl font-bold leading-tight text-gray-900">Income Management</h1>
        </div>
      </header>
      <main>
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
          <!-- Loading state -->
          <div v-if="isLoading" class="py-8 flex justify-center">
            <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
          </div>

          <div v-else class="mt-4 space-y-4">
            <!-- Redesigned Income Summary Section -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                  <div>
                    <h2 class="text-lg leading-6 font-semibold text-gray-900">Income Summary</h2>
                    <p class="mt-1 text-sm text-gray-500">
                      Financial overview of your income sources
                    </p>
                  </div>
                  <div class="mt-3 sm:mt-0">
                    <button @click="showAddIncomeModal = true"
                      class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out flex items-center">
                      <PlusIcon class="h-4 w-4 mr-2" />
                      Add Income
                    </button>
                  </div>
                </div>

                <!-- Income Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                  <!-- Total Income Card -->
                  <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg overflow-hidden shadow-sm">
                    <div class="px-4 py-5 sm:p-6">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                          <CurrencyDollarIcon class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                          <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                              Total Income
                            </dt>
                            <dd>
                              <div class="text-lg font-semibold text-gray-900">
                                {{ formatCurrency(totalIncome) }}
                              </div>
                            </dd>
                          </dl>
                        </div>
                      </div>
                    </div>
                    <div class="bg-indigo-600 px-4 py-2">
                      <div class="text-xs text-indigo-100">
                        Lifetime earnings from all sources
                      </div>
                    </div>
                  </div>

                  <!-- This Month Card -->
                  <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg overflow-hidden shadow-sm">
                    <div class="px-4 py-5 sm:p-6">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                          <CalendarIcon class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                          <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                              This Month
                            </dt>
                            <dd>
                              <div class="text-lg font-semibold text-gray-900">
                                {{ formatCurrency(currentMonthIncome) }}
                              </div>
                            </dd>
                          </dl>
                        </div>
                      </div>
                    </div>
                    <div class="bg-green-600 px-4 py-2">
                      <div class="text-xs text-green-100">
                        Income for {{ getCurrentMonthName() }}
                      </div>
                    </div>
                  </div>

                  <!-- Average Income Card -->
                  <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg overflow-hidden shadow-sm">
                    <div class="px-4 py-5 sm:p-6">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                          <ChartBarIcon class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                          <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                              Average Income
                            </dt>
                            <dd>
                              <div class="text-lg font-semibold text-gray-900">
                                {{ formatCurrency(averageIncome) }}
                              </div>
                            </dd>
                          </dl>
                        </div>
                      </div>
                    </div>
                    <div class="bg-blue-600 px-4 py-2">
                      <div class="text-xs text-blue-100">
                        Average per income entry
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Income Distribution by Category -->
                <div class="mt-6">
                  <h3 class="text-sm font-medium text-gray-500 mb-3">Income Distribution by Category</h3>
                  <div class="space-y-2">
                    <div v-for="category in categoryDistribution" :key="category.name" class="flex items-center">
                      <span class="text-xs font-medium w-24 truncate" :class="getCategoryTextClass(category.name)">
                        {{ formatCategoryName(category.name) }}
                      </span>
                      <div class="flex-1 mx-2">
                        <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                          <div class="h-full rounded-full" :style="`width: ${category.percentage}%`"
                            :class="getCategoryBarClass(category.name)"></div>
                        </div>
                      </div>
                      <span class="text-xs font-medium text-gray-500 w-16 text-right">
                        {{ category.percentage.toFixed(0) }}%
                      </span>
                      <span class="text-xs font-medium text-gray-700 w-24 text-right">
                        {{ formatCurrency(category.amount) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-if="incomes.length === 0" class="text-center my-8 bg-white shadow-md rounded-lg p-8">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p class="text-gray-600 mt-4">No income records found. Add your first income entry!</p>
              <button
                @click="showAddIncomeModal = true"
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                Add Income
              </button>
            </div>

            <!-- Income Filters - Stack on mobile -->
            <div v-else class="bg-white overflow-hidden shadow-sm rounded-lg">
              <div class="px-4 py-3 border-b border-gray-100 flex flex-col xs:flex-row justify-between items-start xs:items-center space-y-2 xs:space-y-0">
                <h3 class="text-sm font-semibold text-gray-900">
                  Income Entries
                </h3>
                <!-- <div class="flex flex-col xs:flex-row space-y-2 xs:space-y-0 xs:space-x-2 w-full xs:w-auto">
                  <select v-model="filters.category"
                    class="text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                    <option value="">All Categories</option>
                    <option v-for="category in categories" :key="category.id" :value="category.name">
                      {{ formatCategoryName(category.name) }}
                    </option>
                    <option value="other">Other</option>
                  </select>
                  <select v-model="filters.month"
                    class="text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                    <option value="">All Months</option>
                    <option v-for="(month, index) in months" :key="index" :value="index">{{ month }}</option>
                  </select>
                </div> -->
              </div>

              <!-- Mobile card view for small screens -->
              <div class="block sm:hidden">
                <div v-for="income in filteredIncomes" :key="income.id" 
                  class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors duration-150">
                  <div class="flex justify-between items-start mb-2">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ income.title }}</div>
                      <div class="text-xs text-gray-500">{{ formatDate(income.date) }}</div>
                    </div>
                    <div class="text-sm font-medium text-green-600">
                      {{ formatCurrency(income.amount) }}
                    </div>
                  </div>
                  
                  <div class="flex justify-between items-center mb-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                      :class="getCategoryClass(getCategoryName(income))">
                      {{ formatCategoryName(getCategoryName(income)) }}
                    </span>
                    <div class="flex space-x-3">
                      <!-- <button @click="editIncome(income)"
                        class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button> -->
                      <button @click="deleteIncome(income.id)"
                        class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                    </div>
                  </div>
                  
                  <div v-if="income.description" class="text-xs text-gray-500 mt-1">
                    {{ income.description }}
                  </div>
                </div>
                
                <div v-if="filteredIncomes.length === 0" class="p-4 text-center text-sm text-gray-500">
                  No income entries found matching your filters.
                </div>
              </div>
              
              <!-- Table view for larger screens -->
              <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                      </th>
                      <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                      </th>
                      <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Category
                      </th>
                      <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                      </th>
                      <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                      </th>
                      <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="income in filteredIncomes" :key="income.id"
                      class="hover:bg-gray-50 transition-colors duration-150">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ formatDate(income.date) }}
                        </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ income.title }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                          :class="getCategoryClass(getCategoryName(income))">
                          {{ formatCategoryName(getCategoryName(income)) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                        {{ formatCurrency(income.amount) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ income.description || '-' }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button @click="editIncome(income)"
                          class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <button @click="deleteIncome(income.id)"
                          class="text-red-600 hover:text-red-900">Delete</button>
                      </td>
                    </tr>
                    <tr v-if="filteredIncomes.length === 0">
                      <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No income entries found matching your filters.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Add/Edit Income Modal - Mobile optimized -->
    <div v-if="showAddIncomeModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transition-all duration-300 transform">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
          <div class="flex justify-between items-center">
            <h2 class="text-white text-lg font-semibold">{{ editingIncome ? 'Edit Income' : 'Add New Income' }}</h2>
            <button @click="closeModal" class="text-white hover:text-indigo-100">
              <XIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh]">
          <form @submit.prevent="saveIncome" class="space-y-4">
            <div v-if="formError" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-red-700">{{ formError }}</p>
                </div>
              </div>
            </div>
            
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
              <input type="text" id="title" v-model="currentIncome.title"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                placeholder="Income title" required />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>
            
            <div>
              <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input type="number" id="amount" v-model="currentIncome.amount" step="0.01" min="0.01"
                  class="pl-7 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                  placeholder="0.00" required />
              </div>
              <p v-if="errors.amount" class="mt-1 text-sm text-red-600">{{ errors.amount }}</p>
            </div>
            
            <div>
              <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
              <input type="date" id="date" v-model="currentIncome.date"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                required />
              <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
            </div>
            
            <div>
              <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select id="category" v-model="currentIncome.category"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                required>
                <option value="" disabled>Select a category</option>
                <option value="salary">Salary</option>
                <option value="freelance">Freelance</option>
                <option value="investment">Investment</option>
                <option value="rental">Rental</option>
                <option value="business">Business</option>
                <option value="other">Other</option>
              </select>
              <p v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category }}</p>
            </div>
            
            <!-- Add a new field for custom category that appears when "other" is selected -->
            <div v-if="currentIncome.category === 'other'" class="mt-3">
              <label for="customCategory" class="block text-sm font-medium text-gray-700 mb-1">Specify Category</label>
              <input type="text" id="customCategory" v-model="currentIncome.customCategory"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                placeholder="Enter custom category" required />
              <p v-if="errors.customCategory" class="mt-1 text-sm text-red-600">{{ errors.customCategory }}</p>
            </div>
            
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
              <textarea id="description" v-model="currentIncome.description" rows="3"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                placeholder="Add any additional details about this income"></textarea>
            </div>
            
            <div class="pt-2">
              <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out"
                :disabled="isSubmitting">
                <span v-if="isSubmitting" class="flex items-center justify-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Processing...
                </span>
                <span v-else>{{ editingIncome ? 'Update Income' : 'Add Income' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal - Mobile optimized -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transition-all duration-300 transform">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
          <div class="flex justify-between items-center">
            <h2 class="text-white text-lg font-semibold">Confirm Deletion</h2>
            <button @click="showDeleteModal = false" class="text-white hover:text-red-100">
              <XIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
        <div class="p-6">
          <p class="text-gray-700 mb-6">Are you sure you want to delete this income entry? This action cannot be undone.</p>
          <div class="flex flex-col xs:flex-row justify-end space-y-2 xs:space-y-0 xs:space-x-3">
            <button @click="showDeleteModal = false"
              class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out">
              Cancel
            </button>
            <button @click="confirmDelete" :disabled="isDeleting"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out">
              <span v-if="isDeleting" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Deleting...
              </span>
              <span v-else>Delete</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, h, defineComponent } from 'vue';
import { useValidation } from '@/composables/useValidation';
import AppHeader from '@/components/layout/AppHeader.vue';
import api from '@/services/api';

// Define Income interface
interface Income {
  id?: number;
  title: string;
  amount: number;
  date: string;
  category: string;
  customCategory?: string;
  description?: string;
  created_at?: string;
  updated_at?: string;
}

// State management
const incomes = ref<Income[]>([]);  // Initialize as empty array
const isLoading = ref(true);
const isSubmitting = ref(false);
const isDeleting = ref(false);
const showAddIncomeModal = ref(false);
const showDeleteModal = ref(false);
const editingIncome = ref(false);
const itemToDeleteId = ref<number | null>(null);
const formError = ref('');
const { errors, validateRequired } = useValidation();

// Current income being edited or created
const currentIncome = ref<Income>({
  title: '',
  amount: 0,
  date: new Date().toISOString().split('T')[0], // Default to today
  category: '',
  customCategory: '',
  description: ''
});

// Filters
const filters = ref({
  category: '',
  month: ''
});

const statistics = ref({
  total_income: 0,
  current_month_income: 0,
  average_income: 0,
  category_distribution: []
});

const fetchStatistics = async () => {
  try {
    const response = await api.get('/incomes/statistics');
    statistics.value = response.data.data;
  } catch (err) {
    console.error('Error fetching statistics:', err);
  }
};


// Months array for filter dropdown
const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

// Computed properties
const filteredIncomes = computed(() => {
  // Ensure incomes.value is an array before filtering
  return Array.isArray(incomes.value) 
    ? incomes.value.filter(income => {
        const categoryMatch = !filters.value.category || income.category === filters.value.category;
        
        let monthMatch = true;
        if (filters.value.month !== '') {
          const incomeDate = new Date(income.date);
          const incomeMonth = incomeDate.getMonth();
          monthMatch = incomeMonth === parseInt(filters.value.month);
        }
        
        return categoryMatch && monthMatch;
      })
    : [];
});

const totalIncome = computed(() => statistics.value.total_income);
const currentMonthIncome = computed(() => statistics.value.current_month_income);
const averageIncome = computed(() => statistics.value.average_income);
const categoryDistribution = computed(() => statistics.value.category_distribution);



// Define icons
const PlusIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M12 4v16m8-8H4'
    })
  ])
});

const XIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M6 18L18 6M6 6l12 12'
    })
  ])
});

const CurrencyDollarIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    })
  ])
});

const CalendarIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
        'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
    })
  ])
});

const ChartBarIcon = defineComponent({
  render: () => h('svg', {
    xmlns: 'http://www.w3.org/2000/svg',
    fill: 'none',
    viewBox: '0 0 24 24',
    stroke: 'currentColor'
  }, [
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
    })
  ])
});

// Methods
const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-MY', {
    style: 'currency',
    currency: 'MYR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(amount);
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const formatCategoryName = (category: string): string => {
  console.log('Category:', category);
  if (!category || typeof category !== 'string') {
    return '';
  }
  return category.charAt(0).toUpperCase() + category.slice(1);
};


const getCategoryName = (income: Income): string => {
  // Handle both object and direct string category types
  if (income?.category) {
   
      return income.category.name || '';
    
  }
  return '';
};

const getCategoryClass = (category: string): string => {
  // Add null check
  if (!category) return 'bg-gray-100 text-gray-800'; // Default class
  
  const classes = {
    salary: 'bg-green-100 text-green-800',
    freelance: 'bg-blue-100 text-blue-800',
    investment: 'bg-purple-100 text-purple-800',
    rental: 'bg-yellow-100 text-yellow-800',
    business: 'bg-indigo-100 text-indigo-800',
    other: 'bg-gray-100 text-gray-800'
  };
  
  return classes[category as keyof typeof classes] || classes.other;
};

const getCategoryTextClass = (category: string): string => {
  // Add null check
  if (!category) return 'text-gray-700'; // Default class
  
  const classes = {
    salary: 'text-green-700',
    freelance: 'text-blue-700',
    investment: 'text-purple-700',
    rental: 'text-yellow-700',
    business: 'text-indigo-700',
    other: 'text-gray-700'
  };
  
  return classes[category as keyof typeof classes] || classes.other;
};

const getCategoryBarClass = (category: string): string => {
  // Add null check
  if (!category) return 'bg-gray-500'; // Default class
  
  const classes = {
    salary: 'bg-green-500',
    freelance: 'bg-blue-500',
    investment: 'bg-purple-500',
    rental: 'bg-yellow-500',
    business: 'bg-indigo-500',
    other: 'bg-gray-500'
  };
  
  return classes[category as keyof typeof classes] || classes.other;
};
const getCurrentMonthName = (): string => {
  const now = new Date();
  return now.toLocaleString('default', { month: 'long' });
};

const fetchIncomes = async () => {
  isLoading.value = true;
  
  try {
    const response = await api.get('/incomes');
    console.log('Response data:', response.data.data);
    // Ensure incomes.value is always an array
    incomes.value = response.data ? response.data.data : [];
    
   
  } catch (err: any) {
    console.error('Error fetching incomes:', err);
    
    incomes.value = [];
    
  } finally {
    isLoading.value = false;
  }
};


const closeModal = () => {
  showAddIncomeModal.value = false;
  editingIncome.value = false;
  resetForm();
};

const resetForm = () => {
  currentIncome.value = {
    title: '',
    amount: 0,
    date: new Date().toISOString().split('T')[0],
    category: '',
    customCategory: '',
    description: ''
  };
  Object.keys(errors.value).forEach(key => delete errors.value[key]);
  formError.value = '';
};

const validateForm = (): boolean => {
  let isValid = true;
  
  if (!validateRequired('title', currentIncome.value.title, 'Title')) {
    isValid = false;
  }
  
  if (!currentIncome.value.amount) {
    errors.value.amount = 'Amount is required';
    isValid = false;
  } else if (currentIncome.value.amount <= 0) {
    errors.value.amount = 'Amount must be greater than zero';
    isValid = false;
  } else {
    delete errors.value.amount;
  }
  
  if (!validateRequired('date', currentIncome.value.date, 'Date')) {
    isValid = false;
  }
  
  if (!validateRequired('category', currentIncome.value.category, 'Category')) {
    isValid = false;
  }
  
  // Validate custom category if category is "other"
  if (currentIncome.value.category === 'other') {
    if (!validateRequired('customCategory', currentIncome.value.customCategory, 'Custom Category')) {
      isValid = false;
    }
  }
  
  return isValid;
};

const editIncome = (income: Income) => {
  editingIncome.value = true;
  currentIncome.value = { ...income };
  showAddIncomeModal.value = true;
};

const deleteIncome = (id?: number) => {
  if (id) {
    itemToDeleteId.value = id;
    showDeleteModal.value = true;
  }
};

const confirmDelete = async () => {
  if (!itemToDeleteId.value) return;
  
  isDeleting.value = true;
  
  try {
    await api.delete(`/incomes/${itemToDeleteId.value}`);
    
    // Remove the income from the list
    incomes.value = incomes.value.filter(income => income.id !== itemToDeleteId.value);
    
    // Close the modal
    showDeleteModal.value = false;
    itemToDeleteId.value = null;
  } catch (err) {
    console.error('Failed to delete income:', err);
    formError.value = 'Failed to delete income. Please try again.';
    
    // For demo purposes
    if (process.env.NODE_ENV === 'development') {
      incomes.value = incomes.value.filter(income => income.id !== itemToDeleteId.value);
      showDeleteModal.value = false;
      itemToDeleteId.value = null;
    }
  } finally {
    isDeleting.value = false;
  }
};

const saveIncome = async () => {
  if (!validateForm()) {
    return;
  }
  
  isSubmitting.value = true;
  formError.value = '';
  
  try {
    if (editingIncome.value && currentIncome.value.id) {
      const response = await api.put(`/incomes/${currentIncome.value.id}`, currentIncome.value);
      const updatedIncome = response.data.data;
      const index = incomes.value.findIndex(income => income.id === currentIncome.value.id);
      if (index !== -1) {
        incomes.value[index] = updatedIncome;
      }
    } else {
      const response = await api.post('/incomes', currentIncome.value);
      const newIncome = response.data.data;
      incomes.value.push(newIncome);
    }
    
    // Refresh statistics after adding/updating income
    await fetchStatistics();
    closeModal();
  } catch (err: any) {
    console.error('Failed to save income:', err);
    formError.value = err.message || 'Failed to save income. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};

// // Sample data for development/demo purposes
// const getSampleIncomes = (): Income[] => {
//   return [
//     {
//       id: 1,
//       title: 'Monthly Salary',
//       amount: 5000,
//       date: '2023-05-01',
//       category: 'salary',
//       description: 'May 2023 salary payment',
//       created_at: '2023-05-01T12:00:00Z',
//       updated_at: '2023-05-01T12:00:00Z'
//     },
//     {
//       id: 2,
//       title: 'Freelance Project',
//       amount: 1200,
//       date: '2023-05-15',
//       category: 'freelance',
//       description: 'Website development for client XYZ',
//       created_at: '2023-05-15T14:30:00Z',
//       updated_at: '2023-05-15T14:30:00Z'
//     },
//     {
//       id: 3,
//       title: 'Dividend Payment',
//       amount: 350,
//       date: '2023-05-20',
//       category: 'investment',
//       description: 'Quarterly dividend from stock portfolio',
//       created_at: '2023-05-20T09:15:00Z',
//       updated_at: '2023-05-20T09:15:00Z'
//     },
//     {
//       id: 4,
//       title: 'Apartment Rent',
//       amount: 800,
//       date: '2023-05-05',
//       category: 'rental',
//       description: 'Rent from tenant for May',
//       created_at: '2023-05-05T18:00:00Z',
//       updated_at: '2023-05-05T18:00:00Z'
//     },
//     {
//       id: 5,
//       title: 'Online Course Sales',
//       amount: 450,
//       date: '2023-05-28',
//       category: 'business',
//       description: 'Revenue from online course platform',
//       created_at: '2023-05-28T10:45:00Z',
//       updated_at: '2023-05-28T10:45:00Z'
//     },
//     {
//       id: 6,
//       title: 'Consulting Fee',
//       amount: 1500,
//       date: '2023-06-10',
//       category: 'freelance',
//       description: 'Strategic consulting for startup',
//       created_at: '2023-06-10T11:20:00Z',
//       updated_at: '2023-06-10T11:20:00Z'
//     },
//     {
//       id: 7,
//       title: 'Monthly Salary',
//       amount: 5000,
//       date: '2023-06-01',
//       category: 'salary',
//       description: 'June 2023 salary payment',
//       created_at: '2023-06-01T12:00:00Z',
//       updated_at: '2023-06-01T12:00:00Z'
//     },
//     {
//       id: 8,
//       title: 'Stock Sale',
//       amount: 2300,
//       date: '2023-06-15',
//       category: 'investment',
//       description: 'Sold shares of Tech Company Inc.',
//       created_at: '2023-06-15T15:30:00Z',
//       updated_at: '2023-06-15T15:30:00Z'
//     },
//     {
//       id: 9,
//       title: 'Gift Money',
//       amount: 500,
//       date: '2023-06-20',
//       category: 'other',
//       customCategory: 'Gift',
//       description: 'Birthday gift from parents',
//       created_at: '2023-06-20T09:00:00Z',
//       updated_at: '2023-06-20T09:00:00Z'
//     }
//   ];
// };

// Lifecycle hooks
onMounted(async () => {
  await Promise.all([
    fetchIncomes(),
    fetchStatistics()
  ]);
});
</script>

<style scoped>
/* Add any component-specific styles here */
.xs\:grid-cols-2 {
  @media (min-width: 480px) {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

.xs\:flex-row {
  @media (min-width: 480px) {
    flex-direction: row;
  }
}

.xs\:space-y-0 {
  @media (min-width: 480px) {
    --tw-space-y-reverse: 0;
    margin-top: calc(0px * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(0px * var(--tw-space-y-reverse));
  }
}

.xs\:space-x-2 {
  @media (min-width: 480px) {
    --tw-space-x-reverse: 0;
    margin-right: calc(0.5rem * var(--tw-space-x-reverse));
    margin-left: calc(0.5rem * calc(1 - var(--tw-space-x-reverse)));
  }
}

.xs\:space-x-3 {
  @media (min-width: 480px) {
    --tw-space-x-reverse: 0;
    margin-right: calc(0.75rem * var(--tw-space-x-reverse));
    margin-left: calc(0.75rem * calc(1 - var(--tw-space-x-reverse)));
  }
}

.xs\:w-auto {
  @media (min-width: 480px) {
    width: auto;
  }
}

.xs\:items-center {
  @media (min-width: 480px) {
    align-items: center;
  }
}
</style>
