<template>
    <div class="min-h-screen bg-gray-50">
      <app-header />
  
      <div class="py-4">
        <header>
          <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h1 class="text-xl sm:text-2xl font-bold leading-tight text-gray-900">Expenses</h1>
          </div>
        </header>
        <main>
          <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Loading state -->
            <div v-if="loading" class="py-8 flex justify-center">
              <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
              </svg>
            </div>
  
            <div v-else class="mt-4 space-y-4">
              <div class="bg-white overflow-hidden shadow-sm rounded-lg transition-all duration-300 hover:shadow-md">
                <div class="px-4 py-3 sm:p-4">
                  <h2 class="text-base leading-6 font-semibold text-gray-900">Expense Summary</h2>
                  <p class="mt-1 text-xs text-gray-600">
                    Track and categorize your business expenses for tax deductions.
                  </p>
                  
                  <div class="mt-4 grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-indigo-50 rounded-lg p-3">
                      <p class="text-xs text-indigo-700 font-medium">Total Expenses</p>
                      <p class="text-lg font-semibold text-indigo-900">{{ formatCurrency(expenseSummary.total) }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                      <p class="text-xs text-green-700 font-medium">Fully Deductible</p>
                      <p class="text-lg font-semibold text-green-900">{{ formatCurrency(expenseSummary.fullyDeductible) }}</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3">
                      <p class="text-xs text-yellow-700 font-medium">Partially Deductible</p>
                      <p class="text-lg font-semibold text-yellow-900">{{ formatCurrency(expenseSummary.partiallyDeductible) }}</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-3">
                      <p class="text-xs text-red-700 font-medium">Non-Deductible</p>
                      <p class="text-lg font-semibold text-red-900">{{ formatCurrency(expenseSummary.nonDeductible) }}</p>
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="flex flex-col xs:flex-row justify-end space-y-2 xs:space-y-0">
                <button @click="showAddExpenseModal = true"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out flex items-center justify-center">
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Add Expense
                </button>
              </div>
  
              <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-3 border-b border-gray-100">
                  <h3 class="text-sm font-semibold text-gray-900">
                    Expense Breakdown
                  </h3>
                </div>
                <div class="px-4 py-3">
                  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="bg-white rounded-lg p-3 h-64">
                      <canvas ref="expensesChart"></canvas>
                    </div>
  
                    <!-- Top Categories -->
                    <div class="space-y-3">
                      <h4 class="text-sm font-medium text-gray-700">Top Expense Categories</h4>
                      <div v-for="(category, index) in topCategories" :key="index"
                        class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                          <div class="flex items-center">
                 
                            <div class="h-8 w-8 rounded-full flex items-center justify-center"
                            
                              :class="getCategoryColor(category.name)">
                              <component :is="getCategoryIcon(category.name)" class="h-4 w-4 text-white" />
                            </div>
                            <div class="ml-3">
                              <p class="text-sm font-medium text-gray-900">{{ category.name }}</p>
                              <p class="text-xs text-gray-500">{{ category.count }} expenses</p>
                            </div>
                          </div>
                          <p class="text-sm font-semibold">{{ formatCurrency(category.amount) }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-white overflow-hidden shadow-sm rounded-lg">
              <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">
                  Tax Savings Suggestions
                </h3>
              </div>
              <div class="px-4 py-3">
                <div v-if="loadingSuggestions" class="flex justify-center py-4">
                  <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                  </svg>
                </div>
                <div v-else-if="taxSuggestions.length === 0" class="text-center py-4">
                  <p class="text-sm text-gray-500">No tax savings suggestions available at this time.</p>
                </div>
                <div v-else class="space-y-3">
                  <div v-for="(tip, index) in taxSuggestions" :key="index" class="flex p-2 rounded-lg bg-yellow-50">
                    <div class="flex-shrink-0">
                      <LightbulbIcon class="h-4 w-4 text-yellow-500" />
                    </div>
                    <div class="ml-3">
                      <h4 class="text-xs font-medium text-gray-900">{{ tip.title }}</h4>
                      <p class="mt-0.5 text-xs text-gray-600">{{ tip.description }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
  
              <!-- Expense Filters - Stack on mobile -->
              <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-3 border-b border-gray-100 flex flex-col xs:flex-row justify-between items-start xs:items-center space-y-2 xs:space-y-0">
                  <h3 class="text-sm font-semibold text-gray-900">
                    Recent Expenses
                  </h3>
                </div>
  
                <!-- Mobile-friendly expense list -->
                <div class="overflow-x-auto">
                  <!-- Mobile card view for small screens -->
                  <div class="block sm:hidden">
                    <div v-for="expense in filteredExpenses" :key="expense.id" 
                      class="border-b border-gray-200 p-4 hover:bg-gray-50 transition-colors duration-150">
                      <div class="flex justify-between items-start mb-2">
                        <div>
                          <div class="text-sm font-medium text-gray-900">{{ expense.description }}</div>
                          <div class="text-xs text-gray-500">Receipt #{{ expense.receipt_id }}</div>
                        </div>
                        <div class="text-sm font-medium text-gray-900">
                          {{ formatCurrency(expense.total_price) }}
                        </div>
                      </div>
                      
                      <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center">
                          <div class="h-5 w-5 rounded-full flex items-center justify-center mr-1.5"
                            :class="getCategoryColor(getCategoryName(expense.category_id))">
                            <component :is="getCategoryIcon(getCategoryName(expense.category_id))" class="h-2.5 w-2.5 text-white" />
                          </div>
                          <span class="text-xs text-gray-700">{{ getCategoryName(expense.category_id) }}</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ formatDate(expense.created_at) }}</span>
                      </div>
                      
                      <div class="flex justify-between items-center">
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                          :class="getDeductibilityClass(expense.deductibility_id, expense.deduction_percentage)">
                          {{ getDeductibilityLabel(expense.deductibility_id, expense.deduction_percentage) }}
                        </span>
                        
                        <div class="flex space-x-3">
                          <button @click="editExpense(expense)"
                            class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                          <button @click="deleteExpense(expense.id)"
                            class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </div>
                      </div>
                    </div>
                    
                    <div v-if="filteredExpenses.length === 0" class="p-4 text-center text-sm text-gray-500">
                      No expenses found matching your filters.
                    </div>
                  </div>
                  
                  <!-- Table view for larger screens -->
                  <table class="hidden sm:table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th scope="col"
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Date
                        </th>
                        <th scope="col"
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Description
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
                          Deductibility
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                          <span class="sr-only">Actions</span>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="expense in filteredExpenses" :key="expense.id"
                        class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ formatDate(expense.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="text-sm font-medium text-gray-900">{{ expense.description }}</div>
                          <div class="text-xs text-gray-500">Receipt #{{ expense.receipt_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="h-6 w-6 rounded-full flex items-center justify-center mr-2"
                              :class="getCategoryColor(getCategoryName(expense.category_id))">
                              <component :is="getCategoryIcon(getCategoryName(expense.category_id))" class="h-3 w-3 text-white" />
                            </div>
                            <span class="text-sm text-gray-900">{{ getCategoryName(expense.category_id) }}</span>
                          </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          {{ formatCurrency(expense.total_price) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span class="px-2 py-1 text-xs font-medium rounded-full"
                            :class="getDeductibilityClass(expense.deductibility_id, expense.deduction_percentage)">
                            {{ getDeductibilityLabel(expense.deductibility_id, expense.deduction_percentage) }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <!-- <button v-if="!category.is_system" @click="editCategory(category)"
                            class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button> -->
                        <button @click="deleteExpense(expense.id)"
                          class="text-red-600 hover:text-red-900">Delete</button>
                      </td>
                    </tr>
                    <tr v-if="filteredExpenses.length === 0">
                      <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No expenses found matching your filters.
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

    <!-- Add/Edit Expense Modal - Mobile optimized -->
    <div v-if="showAddExpenseModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transition-all duration-300 transform">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
          <div class="flex justify-between items-center">
            <h2 class="text-white text-lg font-semibold">{{ editingExpense ? 'Edit Expense' : 'Add New Expense' }}</h2>
            <button @click="closeModal" class="text-white hover:text-indigo-100">
              <XIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh]">
          <form @submit.prevent="saveExpense" class="space-y-4">
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Expenses Name</label>
              <input type="text" id="description" v-model="currentExpense.description"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                placeholder="What was this expense for?" required />
            </div>
            
            <div>
              <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Total Price</label>
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">RM</span>
                </div>
                <input type="number" id="total_price" v-model="currentExpense.total_price" step="0.01" min="0.01"
                  class="pl-7 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                  placeholder="0.00" required />
              </div>
            </div>
            
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <div class="relative">
                    <div v-if="!showNewCategoryInput">
                      <select id="category_id" v-model.number="currentExpense.category_id"
                          class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                          required>
                          <option :value="null" disabled>Select a category</option>
                          
                          <template v-if="Array.isArray(categories)">
                            <!-- Default categories group -->
                            <optgroup label="Default Categories" v-if="categories.some(c => c.is_default)">
                              <option v-for="category in categories.filter(c => c.is_default)" 
                                      :key="category.id" 
                                      :value="category.id">
                                {{ category.name }}
                              </option>
                            </optgroup>
                            
                            <!-- Custom categories group -->
                            <optgroup label="Custom Categories" v-if="categories.some(c => !c.is_default)">
                              <option v-for="category in categories.filter(c => !c.is_default)" 
                                      :key="category.id" 
                                      :value="category.id">
                                {{ category.name }}
                              </option>
                            </optgroup>
                          </template>
                          
                          <option v-else value="0">No categories available</option>
                      </select>
                    <div class="mt-2">
                        <button type="button" @click="showNewCategoryInput = true"
                        class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center">
                        <PlusIcon class="h-3 w-3 mr-1" /> Add new category
                        </button>
                    </div>
                    </div>
                    
                    <div v-else class="space-y-2">
                    <div class="flex space-x-2">
                        <input type="text" v-model="newCategoryName" 
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                        placeholder="Enter new category name" />
                        <button type="button" @click="addCategoryFromModal"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out">
                        Add
                        </button>
                    </div>
                    <button type="button" @click="showNewCategoryInput = false"
                        class="text-sm text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                    </div>
                </div>
                </div>
            
            <div v-if="isPartiallyDeductible">
              <label for="deduction_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                Deduction Percentage (%)
              </label>
              <input type="number" id="deduction_percentage" v-model="currentExpense.deduction_percentage" 
                min="1" max="99" step="0.01"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                placeholder="50.00" required />
            </div>
            
            <div>
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
              <textarea id="notes" v-model="currentExpense.notes" rows="3"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                placeholder="Add any additional details about this expense"></textarea>
            </div>
            
            <div class="pt-2">
              <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out">
                {{ editingExpense ? 'Update Expense' : 'Add Expense' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Category Management Modal - Mobile optimized -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transition-all duration-300 transform">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
          <div class="flex justify-between items-center">
            <h2 class="text-white text-lg font-semibold">Manage Expense Categories</h2>
            <button @click="showCategoryModal = false" class="text-white hover:text-indigo-100">
              <XIcon class="h-5 w-5" />
            </button>
          </div>
        </div>
        <div class="p-6">
          <div class="mb-4">
            <form @submit.prevent="addCategory" class="flex flex-col xs:flex-row space-y-2 xs:space-y-0 xs:space-x-2">
              <input type="text" v-model="newCategory.name" placeholder="Category name"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg" required />
              <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out whitespace-nowrap">
                Add Category
              </button>
            </form>
          </div>
          
          <div class="overflow-y-auto max-h-[50vh]">
            <!-- Mobile category list -->
            <div class="block sm:hidden space-y-2">
              <div v-for="category in categories" :key="category.id"
                class="border rounded-lg p-3 hover:bg-gray-50 transition-colors duration-150">
                <div class="flex justify-between items-center">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ category.name }}</p>
                    <span v-if="category.is_system" class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                      System
                    </span>
                    <span v-else-if="category.is_default" class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                      Default
                    </span>
                    <span v-else class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                      Custom
                    </span>
                  </div>
                  <div class="flex space-x-3">
                    <button v-if="!category.is_system" @click="editCategory(category)"
                      class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</button>
                    <button v-if="!category.is_system && !category.is_default" @click="deleteCategory(category.id)"
                      class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Desktop category table -->
            <table class="hidden sm:table min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="category in categories" :key="category.id"
                  class="hover:bg-gray-50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ category.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span v-if="category.is_system" class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                      System
                    </span>
                    <span v-else-if="category.is_default" class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                      Default
                    </span>
                    <span v-else class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                      Custom
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button v-if="!category.is_system" @click="editCategory(category)"
                      class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                    <button v-if="!category.is_system && !category.is_default" @click="deleteCategory(category.id)"
                      class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
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
          <p class="text-gray-700 mb-6">Are you sure you want to delete this {{ deletingCategory ? 'category' : 'expense' }}? This action cannot be undone.</p>
          <div class="flex flex-col xs:flex-row justify-end space-y-2 xs:space-y-0 xs:space-x-3">
            <button @click="showDeleteModal = false"
              class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out">
              Cancel
            </button>
            <button @click="confirmDelete"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, h, defineComponent } from 'vue';
import AppHeader from '@/components/layout/AppHeader.vue';
import Chart from 'chart.js/auto';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';

// State
const loading = ref(true);
const showAddExpenseModal = ref(false);
const showCategoryModal = ref(false);
const showDeleteModal = ref(false);
const editingExpense = ref(false);
const deletingCategory = ref(false);
const itemToDeleteId = ref('');
const showNewCategoryInput = ref(false);
const newCategoryName = ref('');
const expensesChart = ref<HTMLCanvasElement | null>(null);
const authStore = useAuthStore();
const summaryData = ref({
  total: 0,
  fullyDeductible: 0,
  partiallyDeductible: 0,
  nonDeductible: 0
});

let chart: Chart | null = null;

const fetchExpenseSummary = async () => {
  try {
    const userId = authStore.user?.id;
    const response = await api.get('/get-expenses-summary', {
      params: { user_id: userId }
    });
    
    console.log('Expense summary fetched successfully:', response.data);
    
    // Update the summary data with the fetched data
    if (response.data && typeof response.data === 'object') {
      // If the API returns the exact format we need
      if ('total' in response.data && 
          'fullyDeductible' in response.data && 
          'partiallyDeductible' in response.data && 
          'nonDeductible' in response.data) {
        summaryData.value = response.data;
      } 
      // If the API returns a different format, try to map it
      else if (response.data.summary) {
        summaryData.value = response.data.summary;
      }
      else {
        // Try to extract values from whatever format is returned
        summaryData.value = {
          total: parseFloat(response.data.total || 0),
          fullyDeductible: parseFloat(response.data.fully_deductible || 0),
          partiallyDeductible: parseFloat(response.data.partially_deductible || 0),
          nonDeductible: parseFloat(response.data.non_deductible || 0)
        };
      }
    } else {
      console.error('Unexpected API response format for expense summary:', response.data);
      // Fall back to calculation
      calculateExpenseSummary();
    }
  } catch (error) {
    console.error('Error fetching expense summary:', error);
    // If API fails, calculate from local expenses data
    calculateExpenseSummary();
  }
};
const taxSuggestions = ref([]);
const loadingSuggestions = ref(true);
const fetchTaxSuggestions = async () => {
  try {
    loadingSuggestions.value = true;
    const response = await api.get('/dashboard/tax-suggestions');
    taxSuggestions.value = response.data.suggestions || [];
  } catch (error) {
    console.error('Failed to fetch tax suggestions:', error);
    taxSuggestions.value = [];
  } finally {
    loadingSuggestions.value = false;
  }
};

const calculateExpenseSummary = () => {
  const total = expenses.value.reduce((sum, expense) => sum + parseFloat(expense.total_price), 0);
  
  const fullyDeductible = expenses.value
    .filter(expense => expense.deductibility_id === 1)
    .reduce((sum, expense) => sum + parseFloat(expense.total_price), 0);
  
  const partiallyDeductible = expenses.value
    .filter(expense => expense.deductibility_id === 2)
    .reduce((sum, expense) => {
      const deductibleAmount = parseFloat(expense.total_price) * (parseFloat(expense.deduction_percentage) / 100);
      return sum + deductibleAmount;
    }, 0);
  
  const nonDeductible = expenses.value
    .filter(expense => expense.deductibility_id === 3)
    .reduce((sum, expense) => sum + parseFloat(expense.total_price), 0);
  
  summaryData.value = {
    total,
    fullyDeductible,
    partiallyDeductible,
    nonDeductible
  };
};

const addCategoryFromModal = async () => {
  if (!newCategoryName.value.trim()) return;
  
  try {
    const userId = authStore.user?.id;
    
    const categoryData = {
      name: newCategoryName.value.trim(),
      description: null, 
      is_default: false,
      user_id: userId
    };
        
    const response = await api.post(`/expense-categories?user_id=${userId}`, categoryData);
    console.log('Category added successfully:', response.data);
    
    const newCategory = response.data;
    categories.value.push(newCategory);
    
    currentExpense.value.category_id = newCategory.id;
    
    newCategoryName.value = '';
    showNewCategoryInput.value = false;
  } catch (error) {
    console.error('Error adding category:', error);
    
    // Fallback: create a local category if API fails
    console.log('Using fallback to create local category');
    const newId = Math.max(...categories.value.map(c => c.id), 0) + 1;
    const newCategory = {
      id: newId,
      user_id: userId || 1,
      name: newCategoryName.value.trim(),
      description: null,
      is_default: false,
      is_system: false,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString()
    };
    
    categories.value.push(newCategory);
    currentExpense.value.category_id = newId;
    newCategoryName.value = '';
    showNewCategoryInput.value = false;
  }
};

const currentExpense = ref({
  id: '',
  receipt_id: 1,
  description: '',
  quantity: 1,
  unit_price: 0,
  total_price: 0,
  category_id: null, 
  deductibility_id: null,  
  deduction_percentage: 100,
  notes: '',
  created_at: ''
});

// New category form
const newCategory = ref({
  name: '',
  description: '',
  is_default: 0
});

// Deductibility types
const deductibilityTypes = ref([
  { id: 1, name: 'Fully Deductible' },
  { id: 2, name: 'Partially Deductible' },
  { id: 3, name: 'Non-Deductible' }
]);

// Sample data for categories
const categories = ref([
  {
    id: 1,
    user_id: 1,
    name: 'Office Supplies',
    description: 'Pens, paper, and other office supplies',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 2,
    user_id: 1,
    name: 'Meals & Entertainment',
    description: 'Business meals and entertainment expenses',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 3,
    user_id: 1,
    name: 'Software & Subscriptions',
    description: 'Software licenses and online subscriptions',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 4,
    user_id: 1,
    name: 'Travel',
    description: 'Business travel expenses',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 5,
    user_id: 1,
    name: 'Equipment',
    description: 'Computer hardware and other equipment',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 6,
    user_id: 1,
    name: 'Utilities',
    description: 'Internet, phone, and other utilities',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 7,
    user_id: 1,
    name: 'Professional Development',
    description: 'Courses, books, and other learning materials',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 8,
    user_id: 1,
    name: 'Marketing & Advertising',
    description: 'Advertising and promotional expenses',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 9,
    user_id: 1,
    name: 'Insurance',
    description: 'Business insurance premiums',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  },
  {
    id: 10,
    user_id: 1,
    name: 'Rent & Office Space',
    description: 'Office rent and coworking space fees',
    is_default: 1,
    is_system: 1,
    created_at: '2023-01-01T00:00:00',
    updated_at: '2023-01-01T00:00:00'
  }
]);

// Sample data for expenses
const expenses = ref([
  {
    id: 1,
    receipt_id: 101,
    description: 'Office supplies',
    quantity: 1,
    unit_price: 45.99,
    total_price: 45.99,
    category_id: 1,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'Purchased pens, notebooks, and printer paper',
    created_at: '2023-07-15T10:30:00',
    updated_at: '2023-07-15T10:30:00'
  },
  {
    id: 2,
    receipt_id: 102,
    description: 'Business lunch with client',
    quantity: 1,
    unit_price: 85.50,
    total_price: 85.50,
    category_id: 2,
    deductibility_id: 2,
    deduction_percentage: 50,
    notes: 'Lunch meeting with potential client to discuss project',
    created_at: '2023-07-12T13:45:00',
    updated_at: '2023-07-12T13:45:00'
  },
  {
    id: 3,
    receipt_id: 103,
    description: 'Adobe Creative Cloud subscription',
    quantity: 1,
    unit_price: 52.99,
    total_price: 52.99,
    category_id: 3,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'Monthly subscription for design software',
    created_at: '2023-07-05T09:15:00',
    updated_at: '2023-07-05T09:15:00'
  },
  {
    id: 4,
    receipt_id: 104,
    description: 'Flight to conference',
    quantity: 1,
    unit_price: 450.00,
    total_price: 450.00,
    category_id: 4,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'Round-trip flight to industry conference',
    created_at: '2023-06-28T16:20:00',
    updated_at: '2023-06-28T16:20:00'
  },
  {
    id: 5,
    receipt_id: 105,
    description: 'New laptop',
    quantity: 1,
    unit_price: 1299.99,
    total_price: 1299.99,
    category_id: 5,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'MacBook Pro for business use',
    created_at: '2023-06-15T11:10:00',
    updated_at: '2023-06-15T11:10:00'
  },
  {
    id: 6,
    receipt_id: 106,
    description: 'Internet bill',
    quantity: 1,
    unit_price: 89.99,
    total_price: 89.99,
    category_id: 6,
    deductibility_id: 2,
    deduction_percentage: 30,
    notes: 'Monthly internet service (partial business use)',
    created_at: '2023-07-01T08:00:00',
    updated_at: '2023-07-01T08:00:00'
  },
  {
    id: 7,
    receipt_id: 107,
    description: 'Online course',
    quantity: 1,
    unit_price: 199.00,
    total_price: 199.00,
    category_id: 7,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'Professional development course on advanced techniques',
    created_at: '2023-06-20T14:30:00',
    updated_at: '2023-06-20T14:30:00'
  },
  {
    id: 8,
    receipt_id: 108,
    description: 'Facebook ads',
    quantity: 1,
    unit_price: 250.00,
    total_price: 250.00,
    category_id: 8,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'Social media advertising campaign',
    created_at: '2023-07-10T10:00:00',
    updated_at: '2023-07-10T10:00:00'
  },
  {
    id: 9,
    receipt_id: 109,
    description: 'Liability insurance',
    quantity: 1,
    unit_price: 175.00,
    total_price: 175.00,
    category_id: 9,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'Quarterly premium for business liability insurance',
    created_at: '2023-07-05T15:45:00',
    updated_at: '2023-07-05T15:45:00'
  },
  {
    id: 10,
    receipt_id: 110,
    description: 'Coworking space membership',
    quantity: 1,
    unit_price: 350.00,
    total_price: 350.00,
    category_id: 10,
    deductibility_id: 1,
    deduction_percentage: 100,
    notes: 'Monthly membership for coworking space',
    created_at: '2023-07-01T09:30:00',
    updated_at: '2023-07-01T09:30:00'
  }
]);

const filters = ref({
  category: '',
  deductibility: ''
});

const fetchCategories = async () => {
  try {
    const response = await api.get('/expense-categories', {
      params: { user_id: authStore.user?.id }
    });
    
    if (Array.isArray(response.data)) {
      categories.value = response.data;
    } else if (response.data && typeof response.data === 'object') {
      categories.value = Array.isArray(response.data.categories) 
        ? response.data.categories 
        : Object.values(response.data);
    } else {
      console.error('Unexpected API response format:', response.data);
      return;
    }
    
    if (Array.isArray(categories.value)) {
      categories.value.sort((a, b) => {
        if (a.is_default && !b.is_default) return -1;
        if (!a.is_default && b.is_default) return 1;
        
        return a.name.localeCompare(b.name);
      });
    }
  } catch (error) {
    console.error('Error fetching expense categories:', error);
    console.log('Using fallback categories');
    
    if (!Array.isArray(categories.value)) {
      categories.value = [];
    }
  }
};

const filteredExpenses = computed(() => {
  return expenses.value.filter(expense => {
    const categoryMatch = !filters.value.category || expense.category_id.toString() === filters.value.category;
    const deductibilityMatch = !filters.value.deductibility || expense.deductibility_id.toString() === filters.value.deductibility;
    return categoryMatch && deductibilityMatch;
  });
});

const expenseSummary = computed(() => {
  return summaryData.value;
});

const topCategories = computed(() => {
  if (!Array.isArray(categories.value)) {
    return [];
  }
  
  const categoryMap = new Map();
  
  expenses.value.forEach(expense => {
    const categoryId = parseInt(expense.category_id);
    if (!categoryMap.has(categoryId)) {
      categoryMap.set(categoryId, {
        id: categoryId,
        name: getCategoryName(categoryId),
        amount: 0,
        count: 0
      });
    }
    
    const category = categoryMap.get(categoryId);
    category.amount += parseFloat(expense.total_price);
    category.count += 1;
  });
  
  return Array.from(categoryMap.values())
    .sort((a, b) => b.amount - a.amount)
    .slice(0, 5);
});

const isPartiallyDeductible = computed(() => {
  return currentExpense.value.deductibility_id === 2;
});

const TagIcon = defineComponent({
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
      d: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
    })
  ])
});

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

const CakeIcon = defineComponent({
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
      d: 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z'
    })
  ])
});

const DesktopComputerIcon = defineComponent({
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
      d: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
    })
  ])
});

const TruckIcon = defineComponent({
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
      d: 'M8 7l4-4m0 0l4 4m-4-4v18m-7-6h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
    })
  ])
});

const LightBulbIcon = defineComponent({
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
      d: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'
    })
  ])
});

const OfficeBuildingIcon = defineComponent({
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
      d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
    })
  ])
});

const HomeIcon = defineComponent({
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
      d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    })
  ])
});

// Methods
const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-MY', {
    style: 'currency',
    currency: 'MYR',
    minimumFractionDigits: 0
  }).format(amount);
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date);
};

const getCategoryName = (categoryId: number): string => {
  if (!Array.isArray(categories.value)) {
    console.warn('Categories is not an array:', categories.value);
    return 'Uncategorized';
  }
  
  const category = categories.value.find(c => c.id === categoryId);
  return category ? category.name : 'Uncategorized';
};

const getCategoryIcon = (categoryName: string) => {
  const name = categoryName.toLowerCase();
  if (name.includes('office')) return OfficeBuildingIcon;
  if (name.includes('meal') || name.includes('food') || name.includes('entertainment')) return CakeIcon;
  if (name.includes('software') || name.includes('computer')) return DesktopComputerIcon;
  if (name.includes('travel')) return TruckIcon;
  if (name.includes('development') || name.includes('education')) return LightBulbIcon;
  if (name.includes('rent') || name.includes('space')) return HomeIcon;
  return TagIcon;
};

const getCategoryColor = (categoryName: string): string => {
  // Convert input to lowercase for comparison
  const name = categoryName?.toLowerCase() || '';
  
  const colorMap = {
    'PARENTS MEDICAL CARER': 'bg-fuchsia-600',
    'DISABLED EQUIPMENT': 'bg-fuchsia-600',
    'BASIC EDUCATION FEES': 'bg-purple-500',
    'HIGHER EDUCATION FEES': 'bg-violet-600',
    'SELF ENHANCEMENT COURSES': 'bg-blue-500',
    'SERIOUS MEDICAL EXPENSES': 'bg-cyan-500',
    'FERTILITY TREATMENT': 'bg-teal-600',
    'VACCINATION': 'bg-emerald-500',
    'MEDICAL EXAMINATION': 'bg-green-600',
    'COVID-19 DETECTION': 'bg-lime-500',
    'MENTAL HEALTH CARE': 'bg-yellow-600',
    'LEARNING DISABILITY ASSESSMENT': 'bg-amber-500',
    'LEARNING DISABILITY TREATMENT': 'bg-orange-600',
    'READING MATERIALS': 'bg-red-500',
    'ELECTRONIC GADGET': 'bg-sky-600',
    'GYM AND SPORTS EQUIPMENT': 'bg-pink-500',
    'INTERNET SUBSCRIPTION': 'bg-slate-600',
    'ADDITIONAL SPORTS EQUIPMENT': 'bg-stone-500',
    'SPORTS RENTAL AND ENTRANCE FEES': 'bg-zinc-600',
    'SPORTS COMPETITION FEES': 'bg-neutral-500',
    'BREASTFEEDING EQUIPMENT': 'bg-rose-700',
    'CHILDCARE FEES': 'bg-fuchsia-700',
    'SSPN': 'bg-purple-700',
    'LIFE INSURANCE': 'bg-violet-700',
    'FOOD': 'bg-blue-700',
    'PRS AND DEFERRED ANNUITY': 'bg-cyan-700',
    'EDUCATION AND MEDICAL INSURANCE': 'bg-teal-700',
    'SOCSO': 'bg-emerald-700',
    'ELECTRIC VEHICLE FEE': 'bg-green-700'
  };

  // Find matching color regardless of case
  const color = Object.entries(colorMap).find(([key]) => 
    key.toLowerCase() === name
  )?.[1];

  return color || 'bg-rose-500';
};



const getDeductibilityLabel = (deductibilityId: number, percentage: number): string => {
  if (deductibilityId === 1) return 'Fully Deductible';
  if (deductibilityId === 2) return `${percentage}% Deductible`;
  if (deductibilityId === 3) return 'Non-Deductible';
  return 'Unknown';
};

const getDeductibilityClass = (deductibilityId: number, percentage: number): string => {
  if (deductibilityId === 1) return 'bg-green-100 text-green-800';
  if (deductibilityId === 2) return 'bg-yellow-100 text-yellow-800';
  if (deductibilityId === 3) return 'bg-red-100 text-red-800';
  return 'bg-gray-100 text-gray-800';
};

const calculateTotalPrice = () => {
  const quantity = parseFloat(currentExpense.value.quantity.toString());
  const unitPrice = parseFloat(currentExpense.value.unit_price.toString());
  if (!isNaN(quantity) && !isNaN(unitPrice)) {
    currentExpense.value.total_price = quantity * unitPrice;
  }
};

const openAddExpenseModal = () => {
  editingExpense.value = false;
  currentExpense.value = {
    id: '',
    receipt_id: 1,
    description: '',
    quantity: 1,
    unit_price: 0,
    total_price: 0,
    category_id: null, 
    deductibility_id: null, 
    deduction_percentage: 100,
    notes: '',
    created_at: new Date().toISOString()
  };
  showAddExpenseModal.value = true;
};

const editExpense = (expense) => {
  editingExpense.value = true;
  currentExpense.value = { ...expense };
  showAddExpenseModal.value = true;
};

const closeModal = () => {
  showAddExpenseModal.value = false;
  editingExpense.value = false;
  showNewCategoryInput.value = false;
  newCategoryName.value = '';
};

const saveExpense = async () => {
  try {
    currentExpense.value.updated_at = new Date().toISOString();
    
    const userId = authStore.user?.id;
    
    currentExpense.value.receipt_id = Math.floor(Math.random() * 9000) + 1000; // Random 4-digit number
    currentExpense.value.created_at = new Date().toISOString();

    // Prepare data for API
    const expenseData = {
      ...currentExpense.value,
      user_id: userId
    };

    // Add directly to local state first for immediate UI update
    const newId = Math.max(0, ...expenses.value.map(e => e.id)) + 1;
    const newExpense = { 
      ...currentExpense.value,
      id: newId,
      user_id: userId
    };

    expenses.value.push(newExpense);
    console.log('New expense added locally:', newExpense);

    // Then make API call
    try {
      const response = await api.post('/expenses', expenseData);
      console.log('Expense also saved to API:', response.data);
    } catch (apiError) {
      console.error('API error when creating expense, but local state was updated:', apiError);
    }

    // Reset the form fields
    currentExpense.value = {
      id: '',
      receipt_id: 1,
      description: '',
      quantity: 1,
      unit_price: 0,
      total_price: 0,
      category_id: null, 
      deductibility_id: null,  
      deduction_percentage: 100,
      notes: '',
      created_at: ''
    };

    // Close the modal
    showAddExpenseModal.value = false;
    fetchExpenseSummary();

    // Refresh the chart
    setTimeout(() => {
      initExpensesChart();
    }, 100);
  } catch (error) {
    console.error('Error saving expense:', error);
    // You could add error handling UI here
  }
};

const fetchRecentExpenses = async () => {
  try {
    const userId = authStore.user?.id;
    const response = await api.get('/get-recent-expenses', {
      params: { user_id: userId }
    });
    
    // Update the expenses array with the fetched data
    if (Array.isArray(response.data)) {
      expenses.value = response.data;
    } else if (response.data && typeof response.data === 'object' && Array.isArray(response.data.expenses)) {
      expenses.value = response.data.expenses;
    } else {
      console.error('Unexpected API response format for recent expenses:', response.data);
    }
  } catch (error) {
    console.error('Error fetching recent expenses:', error);
    // Keep the sample data as fallback if API fails
    console.log('Using fallback expense data');
  }
};

const deleteExpense = (id) => {
  deletingCategory.value = false;
  itemToDeleteId.value = id.toString();
  showDeleteModal.value = true;
};

const addCategory = () => {
  if (!newCategory.value.name) return;
  
  const newId = Math.max(...categories.value.map(c => c.id)) + 1;
  categories.value.push({
    id: newId,
    user_id: 1, // This would typically be the current user's ID
    name: newCategory.value.name,
    description: newCategory.value.description,
    is_default: 0,
    is_system: 0,
    created_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
  });
  
  newCategory.value = {
    name: '',
    description: '',
    is_default: 0
  };
};

const editCategory = (category) => {
  // This would typically open a modal to edit the category
  // For simplicity, we'll just update the name with a prompt
  const newName = prompt('Enter new category name:', category.name);
  if (newName && newName !== category.name) {
    const index = categories.value.findIndex(c => c.id === category.id);
    if (index !== -1) {
      categories.value[index] = {
        ...categories.value[index],
        name: newName,
        updated_at: new Date().toISOString()
      };
    }
  }
};

watch(() => currentExpense.value.category_id, (newCategoryId, oldCategoryId) => {
  if (newCategoryId !== oldCategoryId) {
    const selectedCategory = categories.value.find(c => c.id === newCategoryId);
    console.log('Category selected:', {
      categoryId: newCategoryId,
      categoryName: selectedCategory ? selectedCategory.name : 'Unknown',
      fullCategory: selectedCategory
    });
  }
});

const deleteCategory = (id) => {
  deletingCategory.value = true;
  itemToDeleteId.value = id.toString();
  showDeleteModal.value = true;
};

const confirmDelete = () => {
  if (deletingCategory.value) {
    // Delete category
    const id = parseInt(itemToDeleteId.value);
    categories.value = categories.value.filter(c => c.id !== id);
  } else {
    // Delete expense
    const id = parseInt(itemToDeleteId.value);
    expenses.value = expenses.value.filter(e => e.id !== id);
    fetchExpenseSummary();
    initExpensesChart();
  }
  
  showDeleteModal.value = false;
  itemToDeleteId.value = '';
};

// Initialize the expenses chart
const initExpensesChart = () => {
  if (!expensesChart.value) return;
  const ctx = expensesChart.value.getContext('2d');
  if (!ctx) return;

  if (chart) {
    chart.destroy();
  }

  // Convert Tailwind colors to RGBA
  const tailwindToRGBA = (colorClass: string, opacity: number) => {
    const colorMap = {
      'bg-fuchsia-600': 'rgba(192, 38, 211, ',
      'bg-purple-500': 'rgba(168, 85, 247, ',
      'bg-violet-600': 'rgba(124, 58, 237, ',
      'bg-blue-500': 'rgba(59, 130, 246, ',
      'bg-cyan-500': 'rgba(6, 182, 212, ',
      'bg-teal-600': 'rgba(13, 148, 136, ',
      'bg-emerald-500': 'rgba(16, 185, 129, ',
      'bg-green-600': 'rgba(22, 163, 74, ',
      'bg-lime-500': 'rgba(132, 204, 22, ',
      'bg-yellow-600': 'rgba(202, 138, 4, ',
      'bg-amber-500': 'rgba(245, 158, 11, ',
      'bg-orange-600': 'rgba(234, 88, 12, ',
      'bg-red-500': 'rgba(239, 68, 68, ',
      'bg-sky-600': 'rgba(2, 132, 199, ',
      'bg-pink-500': 'rgba(236, 72, 153, ',
      'bg-slate-600': 'rgba(71, 85, 105, ',
      'bg-stone-500': 'rgba(120, 113, 108, ',
      'bg-zinc-600': 'rgba(82, 82, 91, ',
      'bg-neutral-500': 'rgba(115, 115, 115, ',
      'bg-rose-700': 'rgba(190, 18, 60, ',
      'bg-fuchsia-700': 'rgba(162, 28, 175, ',
      'bg-purple-700': 'rgba(126, 34, 206, ',
      'bg-violet-700': 'rgba(109, 40, 217, ',
      'bg-blue-700': 'rgba(29, 78, 216, ',
      'bg-cyan-700': 'rgba(14, 116, 144, ',
      'bg-teal-700': 'rgba(15, 118, 110, ',
      'bg-emerald-700': 'rgba(4, 120, 87, ',
      'bg-green-700': 'rgba(21, 128, 61, ',
      'bg-rose-500': 'rgba(244, 63, 94, '
    };
    return (colorMap[colorClass] || 'rgba(99, 102, 241, ') + opacity + ')';
  };

  const categoryTotals = new Map();
  expenses.value.forEach(expense => {
    const categoryName = getCategoryName(expense.category_id);
    const currentTotal = categoryTotals.get(categoryName) || 0;
    categoryTotals.set(categoryName, currentTotal + parseFloat(expense.total_price));
  });

  const sortedCategories = Array.from(categoryTotals.entries())
    .sort((a, b) => b[1] - a[1])
    .slice(0, 6);

  const labels = sortedCategories.map(([name]) => name);
  const data = sortedCategories.map(([, total]) => total);
  const backgroundColors = labels.map(name => {
    const tailwindColor = getCategoryColor(name);
    return tailwindToRGBA(tailwindColor, 0.7);
  });
  const borderColors = labels.map(name => {
    const tailwindColor = getCategoryColor(name);
    return tailwindToRGBA(tailwindColor, 1);
  });

  chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: backgroundColors,
        borderColor: borderColors,
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            font: { size: 11 },
            padding: 10,
            usePointStyle: true,
            pointStyle: 'circle'
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw as number;
              const total = data.reduce((a, b) => a + b, 0);
              const percentage = ((value / total) * 100).toFixed(1);
              return `${label}: ${formatCurrency(value)} (${percentage}%)`;
            }
          }
        }
      },
      cutout: '60%'
    }
  });
};



onMounted(async () => {
  await authStore.fetchUser();
  fetchCategories();
  fetchRecentExpenses();
  fetchExpenseSummary(); 
  setTimeout(() => {
    loading.value = false;
    setTimeout(() => {
      initExpensesChart();
    }, 100);
  }, 1000);
  fetchTaxSuggestions();

});

watch([() => filters.value.category, () => filters.value.deductibility], () => {
  setTimeout(() => {
    initExpensesChart();
  }, 100);
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


