<template>
    <div class="min-h-screen bg-gray-50">
      <app-header />
  
      <div class="py-4 sm:py-6">
        <header>
          <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
            <div class="md:flex md:items-center md:justify-between">
              <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold leading-tight text-gray-900">
                  {{ isInvoiceMode ? 'Invoices' : 'Quotations' }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                  {{ isInvoiceMode ? 'Manage your invoices and track payments' : 'Create and manage quotations for your clients' }}
                </p>
              </div>
              <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <button
                  @click="toggleMode"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <SwitchHorizontalIcon class="h-4 w-4 mr-2" />
                  Switch to {{ isInvoiceMode ? 'Quotations' : 'Invoices' }}
                </button>
                <button
                  @click="createNew"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  Create {{ isInvoiceMode ? 'Invoice' : 'Quotation' }}
                </button>
              </div>
            </div>
          </div>
        </header>
  
        <main>
          <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
            <!-- Tabs for different statuses -->
            <div class="mt-6">
              <div class="sm:hidden">
                <label for="tabs" class="sr-only">Select a tab</label>
                <select
                  id="tabs"
                  v-model="activeTab"
                  class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                  <option v-for="tab in tabs" :key="tab.id" :value="tab.id">{{ tab.name }}</option>
                </select>
              </div>
              <div class="hidden sm:block">
                <div class="border-b border-gray-200">
                  <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                      v-for="tab in tabs"
                      :key="tab.id"
                      @click="activeTab = tab.id"
                      :class="[
                        activeTab === tab.id
                          ? 'border-indigo-500 text-indigo-600'
                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                      ]"
                    >
                      {{ tab.name }}
                      <span
                        v-if="tab.count > 0"
                        :class="[
                          activeTab === tab.id ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900',
                          'ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium'
                        ]"
                      >
                        {{ tab.count }}
                      </span>
                    </button>
                  </nav>
                </div>
              </div>
            </div>
  
            <!-- Loading state -->
            <div v-if="loading" class="mt-6 flex justify-center">
              <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
  
            <!-- Empty state -->
            <div v-else-if="filteredItems.length === 0" class="mt-6 bg-white shadow rounded-lg">
              <div class="px-4 py-12 text-center">
                <DocumentTextIcon class="mx-auto h-12 w-12 text-gray-300" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No {{ isInvoiceMode ? 'invoices' : 'quotations' }} found</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Get started by creating a new {{ isInvoiceMode ? 'invoice' : 'quotation' }}.
                </p>
                <div class="mt-6">
                  <button
                    @click="createNew"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Create {{ isInvoiceMode ? 'Invoice' : 'Quotation' }}
                  </button>
                </div>
              </div>
            </div>
  
            <!-- List of items -->
            <div v-else class="mt-6 space-y-4">
              <div v-for="item in filteredItems" :key="item.id" class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-start">
                  <div>
                    <div class="flex items-center">
                      <h3 class="text-lg font-medium text-gray-900">{{ item.clientName }}</h3>
                      <span
                        :class="getStatusClass(item.status)"
                        class="ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        {{ item.status }}
                      </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ isInvoiceMode ? 'Invoice' : 'Quotation' }} #{{ item.number }} - {{ formatDate(item.date) }}
                    </p>
                  </div>
                  <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(item.total) }}</p>
                    <p v-if="item.dueDate" class="mt-1 text-sm text-gray-500">
                      Due: {{ formatDate(item.dueDate) }}
                    </p>
                  </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-4 sm:px-6 bg-gray-50">
                  <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                      <span class="text-sm text-gray-500">{{ item.items.length }} items</span>
                      <span v-if="item.notes" class="text-sm text-gray-500">•</span>
                      <span v-if="item.notes" class="text-sm text-gray-500 truncate max-w-xs">{{ item.notes }}</span>
                    </div>
                    <div class="flex space-x-2">
                      <button
                        @click="openViewItem(item)"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      >
                        <EyeIcon class="h-4 w-4 mr-1" />
                        View
                      </button>
                      <button
                        v-if="!isInvoiceMode && item.status === 'Draft'"
                        @click="convertToInvoice(item)"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                      >
                        <ArrowRightIcon class="h-4 w-4 mr-1" />
                        Convert to Invoice
                      </button>
                      <button
                        v-if="isInvoiceMode && item.status === 'Created'"
                        @click="markAsPaid(item)"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                      >
                        <CheckIcon class="h-4 w-4 mr-1" />
                        Mark as Paid
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
  
      <!-- Form Modal -->
      <div v-if="showModal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>
  
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    {{ isEditing ? (isInvoiceMode ? 'Edit Invoice' : 'Edit Quotation') : (isInvoiceMode ? 'Create Invoice' : 'Create Quotation') }}
                  </h3>
                  <div class="mt-4">
                    <form @submit.prevent="saveItem">
                      <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Client Information -->
                        <div class="sm:col-span-3">
                          <label for="clientName" class="block text-sm font-medium text-gray-700">Client Name</label>
                          <div class="mt-1">
                            <input
                              type="text"
                              id="clientName"
                              v-model="currentItem.clientName"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                              required
                            />
                          </div>
                        </div>
  
                        <div class="sm:col-span-3">
                          <label for="clientEmail" class="block text-sm font-medium text-gray-700">Client Email</label>
                          <div class="mt-1">
                            <input
                              type="email"
                              id="clientEmail"
                              v-model="currentItem.clientEmail"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                              required
                            />
                          </div>
                        </div>
  
                        <div class="sm:col-span-3">
                          <label for="number" class="block text-sm font-medium text-gray-700">
                            {{ isInvoiceMode ? 'Invoice' : 'Quotation' }} Number
                          </label>
                          <div class="mt-1">
                            <input
                              type="text"
                              id="number"
                              v-model="currentItem.number"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                              required
                            />
                          </div>
                        </div>
  
                        <div class="sm:col-span-3">
                          <label for="date" class="block text-sm font-medium text-gray-700">
                            {{ isInvoiceMode ? 'Invoice' : 'Quotation' }} Date
                          </label>
                          <div class="mt-1">
                            <input
                              type="date"
                              id="date"
                              v-model="currentItem.date"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                              required
                            />
                          </div>
                        </div>
                        <div class="sm:col-span-3">
                        <label for="dueDate" class="block text-sm font-medium text-gray-700">Due Date</label>
                        <div class="mt-1">
                          <input
                            type="date"
                            id="dueDate"
                            v-model="currentItem.dueDate"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            required
                          />
                        </div>
                      </div>

                      <div class="sm:col-span-3">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1">
                          <select
                            id="status"
                            v-model="currentItem.status"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            required
                          >
                            <option v-for="status in availableStatuses" :key="status" :value="status">
                              {{ status }}
                            </option>
                          </select>
                        </div>
                      </div>

                      <div class="sm:col-span-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <div class="mt-1">
                          <textarea
                            id="notes"
                            v-model="currentItem.notes"
                            rows="2"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                          ></textarea>
                        </div>
                      </div>

                      <!-- Line Items -->
                      <div class="sm:col-span-6">
                        <div class="flex justify-between items-center mb-3">
                          <h4 class="text-sm font-medium text-gray-900">Items</h4>
                          <button
                            type="button"
                            @click="addLineItem"
                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          >
                            <PlusIcon class="h-3.5 w-3.5 mr-1" />
                            Add Item
                          </button>
                        </div>

                        <!-- Empty state -->
                        <div v-if="currentItem.items.length === 0" class="text-center py-6 bg-gray-50 rounded-md">
                          <DocumentTextIcon class="mx-auto h-10 w-10 text-gray-300" />
                          <p class="mt-2 text-sm text-gray-500">No items added yet</p>
                          <button
                            type="button"
                            @click="addLineItem"
                            class="mt-2 text-sm text-indigo-600 hover:text-indigo-500"
                          >
                            Add your first item
                          </button>
                        </div>

                        <!-- Item table -->
                        <div v-else class="overflow-x-auto border border-gray-200 rounded-md">
                          <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                              <tr>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  Description
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  Quantity
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  Unit Price
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                  Total
                                </th>
                                <th scope="col" class="relative px-3 py-3">
                                  <span class="sr-only">Actions</span>
                                </th>
                              </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                              <tr v-for="(item, index) in currentItem.items" :key="index">
                                <td class="px-3 py-2 whitespace-nowrap">
                                  <input
                                    type="text"
                                    v-model="item.description"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Item description"
                                    required
                                  />
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                  <input
                                    type="number"
                                    v-model.number="item.quantity"
                                    min="1"
                                    step="1"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required
                                    @input="updateLineItemTotal(item)"
                                  />
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                  <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                      <span class="text-gray-500 sm:text-sm">RM</span>
                                    </div>
                                    <input
                                      type="number"
                                      v-model.number="item.unitPrice"
                                      step="0.01"
                                      min="0"
                                      class="block w-full pl-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                      placeholder="0.00"
                                      required
                                      @input="updateLineItemTotal(item)"
                                    />
                                  </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                  <div class="text-sm font-medium text-gray-900">
                                    RM {{ item.total.toFixed(2) }}
                                  </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                  <button
                                    type="button"
                                    @click="removeLineItem(index)"
                                    class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                  >
                                    <TrashIcon class="h-3 w-3" />
                                  </button>
                                </td>
                              </tr>
                            </tbody>
                            <tfoot>
                              <tr class="bg-gray-50">
                                <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-700">
                                  Subtotal:
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                  RM {{ calculateSubtotal().toFixed(2) }}
                                </td>
                                <td></td>
                              </tr>
                              <tr class="bg-gray-50">
                                <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-700">
                                  Tax ({{ currentItem.taxRate }}%):
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                  RM {{ calculateTax().toFixed(2) }}
                                </td>
                                <td></td>
                              </tr>
                              <tr class="bg-gray-50">
                                <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-700">
                                  Total:
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                                  RM {{ calculateTotal().toFixed(2) }}
                                </td>
                                <td></td>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>

                      <!-- Tax Rate -->
                      <div class="sm:col-span-2">
                        <label for="taxRate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                        <div class="mt-1">
                          <input
                            type="number"
                            id="taxRate"
                            v-model.number="currentItem.taxRate"
                            min="0"
                            max="100"
                            step="0.1"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            @input="updateTotal"
                          />
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="saveItem"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ isEditing ? 'Update' : 'Create' }}
            </button>
            <button
              type="button"
              @click="closeModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal" class="fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeViewModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <div class="flex justify-between items-center">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    {{ isInvoiceMode ? 'Invoice' : 'Quotation' }} #{{ viewItem.number }}
                  </h3>
                  <span
                    :class="getStatusClass(viewItem.status)"
                    class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ viewItem.status }}
                  </span>
                </div>
                
                <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-3">
                    <h4 class="text-sm font-medium text-gray-500">Client Information</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ viewItem.clientName }}</p>
                    <p class="text-sm text-gray-900">{{ viewItem.clientEmail }}</p>
                  </div>

                  <div class="sm:col-span-3 text-right">
                    <h4 class="text-sm font-medium text-gray-500">Dates</h4>
                    <p class="mt-1 text-sm text-gray-900">
                      {{ isInvoiceMode ? 'Invoice' : 'Quotation' }} Date: {{ formatDate(viewItem.date) }}
                    </p>
                    <p class="text-sm text-gray-900">Due Date: {{ formatDate(viewItem.dueDate) }}</p>
                  </div>

                  <div class="sm:col-span-6">
                    <div class="border border-gray-200 rounded-md overflow-hidden">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Description
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Quantity
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Unit Price
                            </th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Total
                            </th>
                          </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                          <tr v-for="(item, index) in viewItem.items" :key="index">
                            <td class="px-4 py-3 text-sm text-gray-900">
                              {{ item.description }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">
                              {{ item.quantity }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">
                              RM {{ item.unitPrice.toFixed(2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">
                              RM {{ item.total.toFixed(2) }}
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr class="bg-gray-50">
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                              Subtotal:
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                              RM {{ calculateViewSubtotal().toFixed(2) }}
                            </td>
                          </tr>
                          <tr class="bg-gray-50">
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                              Tax ({{ viewItem.taxRate }}%):
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                              RM {{ calculateViewTax().toFixed(2) }}
                            </td>
                          </tr>
                          <tr class="bg-gray-50">
                            <td colspan="3" class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                              Total:
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                              RM {{ viewItem.total.toFixed(2) }}
                            </td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                  <div v-if="viewItem.notes" class="sm:col-span-6">
                    <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ viewItem.notes }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              v-if="!isInvoiceMode && viewItem.status === 'Draft'"
              type="button"
              @click="convertToInvoice(viewItem)"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              <ArrowRightIcon class="h-4 w-4 mr-2" />
              Convert to Invoice
            </button>
            <button
              v-if="isInvoiceMode && viewItem.status === 'Created'"
              type="button"
              @click="markAsPaid(viewItem)"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              <CheckIcon class="h-4 w-4 mr-2" />
              Mark as Paid
            </button>
            <button
              type="button"
              @click="editCurrentItem(viewItem)"
              class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              <PencilIcon class="h-4 w-4 mr-2" />
              Edit
            </button>
            <button
              type="button"
              @click="printItem"
              class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              <PrinterIcon class="h-4 w-4 mr-2" />
              Print
            </button>
            <button
              type="button"
              @click="closeViewModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, defineComponent, h, watch } from 'vue';
import AppHeader from '@/components/layout/AppHeader.vue';
import api from '@/services/api';

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

const DocumentTextIcon = defineComponent({
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
      d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
    })
  ])
});

const TrashIcon = defineComponent({
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
      d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
    })
  ])
});

const EyeIcon = defineComponent({
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
      d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z'
    }),
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'
    })
  ])
});

const ArrowRightIcon = defineComponent({
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
      d: 'M14 5l7 7m0 0l-7 7m7-7H3'
    })
  ])
});

const CheckIcon = defineComponent({
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
      d: 'M5 13l4 4L19 7'
    })
  ])
});

const PencilIcon = defineComponent({
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
      d: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'
    })
  ])
});

const PrinterIcon = defineComponent({
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
      d: 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z'
    })
  ])
});

const SwitchHorizontalIcon = defineComponent({
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
      d: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'
    })
  ])
});

// Define types
interface LineItem {
  description: string;
  quantity: number;
  unitPrice: number;
  total: number;
}

interface QuotationInvoice {
  id: number;
  type: 'quotation' | 'invoice';
  number: string;
  date: string;
  dueDate: string;
  clientName: string;
  clientEmail: string;
  status: string;
  items: LineItem[];
  taxRate: number;
  total: number;
  notes: string;
}

// Component state
const isInvoiceMode = ref(false);
const loading = ref(true);
const activeTab = ref('all');
const showModal = ref(false);
const showViewModal = ref(false);
const isEditing = ref(false);
const currentItem = ref<QuotationInvoice>({
  id: 0,
  type: 'quotation',
  number: '',
  date: new Date().toISOString().split('T')[0],
  dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  clientName: '',
  clientEmail: '',
  status: 'Draft',
  items: [],
  taxRate: 6, // Default Malaysian SST rate
  total: 0,
  notes: ''
});
const viewItem = ref<QuotationInvoice>({
  id: 0,
  type: 'quotation',
  number: '',
  date: '',
  dueDate: '',
  clientName: '',
  clientEmail: '',
  status: '',
  items: [],
  taxRate: 0,
  total: 0,
  notes: ''
});

// Sample data (replace with API calls in production)
const quotations = ref<QuotationInvoice[]>([
  {
    id: 1,
    type: 'quotation',
    number: 'Q-2023-001',
    date: '2023-05-15',
    dueDate: '2023-06-15',
    clientName: 'ABC Company',
    clientEmail: 'contact@abccompany.com',
    status: 'Draft',
    items: [
      {
        description: 'Web Design Services',
        quantity: 1,
        unitPrice: 2500,
        total: 2500
      },
      {
        description: 'SEO Optimization',
        quantity: 1,
        unitPrice: 1200,
        total: 1200
      }
    ],
    taxRate: 6,
    total: 3922,
    notes: 'Payment due within 30 days of receipt.'
  },
  {
    id: 2,
    type: 'quotation',
    number: 'Q-2023-002',
    date: '2023-05-20',
    dueDate: '2023-06-20',
    clientName: 'XYZ Corporation',
    clientEmail: 'finance@xyzcorp.com',
    status: 'Sent',
    items: [
      {
        description: 'Mobile App Development',
        quantity: 1,
        unitPrice: 8000,
        total: 8000
      }
    ],
    taxRate: 6,
    total: 8480,
    notes: 'This quotation is valid for 30 days.'
  }
]);

const invoices = ref<QuotationInvoice[]>([
  {
    id: 101,
    type: 'invoice',
    number: 'INV-2023-001',
    date: '2023-05-01',
    dueDate: '2023-05-31',
    clientName: 'ABC Company',
    clientEmail: 'contact@abccompany.com',
    status: 'Paid',
    items: [
      {
        description: 'Website Maintenance',
        quantity: 1,
        unitPrice: 1500,
        total: 1500
      }
    ],
    taxRate: 6,
    total: 1590,
    notes: 'Thank you for your business!'
  },
  {
    id: 102,
    type: 'invoice',
    number: 'INV-2023-002',
    date: '2023-05-10',
    dueDate: '2023-06-10',
    clientName: 'XYZ Corporation',
    clientEmail: 'finance@xyzcorp.com',
    status: 'Created',
    items: [
      {
        description: 'Consulting Services',
        quantity: 10,
        unitPrice: 200,
        total: 2000
      },
      {
        description: 'Training Session',
        quantity: 2,
        unitPrice: 500,
        total: 1000
      }
    ],
    taxRate: 6,
    total: 3180,
    notes: 'Please make payment by the due date.'
  }
]);

// Computed properties
const items = computed(() => {
  return isInvoiceMode.value ? invoices.value : quotations.value;
});

const filteredItems = computed(() => {
  if (activeTab.value === 'all') {
    return items.value;
  }
  return items.value.filter(item => item.status.toLowerCase() === activeTab.value);
});

const tabs = computed(() => {
  const allItems = items.value;
  const statusCounts: Record<string, number> = { all: allItems.length };
  
  allItems.forEach(item => {
    const status = item.status.toLowerCase();
    statusCounts[status] = (statusCounts[status] || 0) + 1;
  });
  
  const result = [{ id: 'all', name: 'All', count: statusCounts.all }];
  
  if (isInvoiceMode.value) {
    if (statusCounts.created) result.push({ id: 'created', name: 'Created', count: statusCounts.created });
    if (statusCounts.paid) result.push({ id: 'paid', name: 'Paid', count: statusCounts.paid });
    if (statusCounts.overdue) result.push({ id: 'overdue', name: 'Overdue', count: statusCounts.overdue });
  } else {
    if (statusCounts.draft) result.push({ id: 'draft', name: 'Draft', count: statusCounts.draft });
    if (statusCounts.sent) result.push({ id: 'sent', name: 'Sent', count: statusCounts.sent });
    if (statusCounts.accepted) result.push({ id: 'accepted', name: 'Accepted', count: statusCounts.accepted });
    if (statusCounts.declined) result.push({ id: 'declined', name: 'Declined', count: statusCounts.declined });
  }
  
  return result;
});

const availableStatuses = computed(() => {
  if (isInvoiceMode.value) {
    return ['Created', 'Paid', 'Overdue'];
  } else {
    return ['Draft', 'Sent', 'Accepted', 'Declined'];
  }
});

// Methods
const toggleMode = () => {
  isInvoiceMode.value = !isInvoiceMode.value;
  activeTab.value = 'all';
};

const createNew = () => {
  isEditing.value = false;
  currentItem.value = {
    id: Date.now(),
    type: isInvoiceMode.value ? 'invoice' : 'quotation',
    number: isInvoiceMode.value 
      ? `INV-${new Date().getFullYear()}-${String(invoices.value.length + 1).padStart(3, '0')}` 
      : `Q-${new Date().getFullYear()}-${String(quotations.value.length + 1).padStart(3, '0')}`,
    date: new Date().toISOString().split('T')[0],
    dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    clientName: '',
    clientEmail: '',
    status: isInvoiceMode.value ? 'Created' : 'Draft',
    items: [],
    taxRate: 6,
    total: 0,
    notes: ''
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const closeViewModal = () => {
  showViewModal.value = false;
};

const addLineItem = () => {
  currentItem.value.items.push({
    description: '',
    quantity: 1,
    unitPrice: 0,
    total: 0
  });
};

const removeLineItem = (index: number) => {
  currentItem.value.items.splice(index, 1);
  updateTotal();
};

const updateLineItemTotal = (item: LineItem) => {
  item.total = item.quantity * item.unitPrice;
  updateTotal();
};

const calculateSubtotal = () => {
  return currentItem.value.items.reduce((sum, item) => sum + item.total, 0);
};

const calculateTax = () => {
  return calculateSubtotal() * (currentItem.value.taxRate / 100);
};

const calculateTotal = () => {
  return calculateSubtotal() + calculateTax();
};

const calculateViewSubtotal = () => {
  return viewItem.value.items.reduce((sum, item) => sum + item.total, 0);
};

const calculateViewTax = () => {
  return calculateViewSubtotal() * (viewItem.value.taxRate / 100);
};

const updateTotal = () => {
  currentItem.value.total = calculateTotal();
};

const saveItem = () => {
  // Update the total before saving
  updateTotal();
  
  if (isEditing.value) {
    // Update existing item
    if (isInvoiceMode.value) {
      const index = invoices.value.findIndex(item => item.id === currentItem.value.id);
      if (index !== -1) {
        invoices.value[index] = { ...currentItem.value };
      }
    } else {
      const index = quotations.value.findIndex(item => item.id === currentItem.value.id);
      if (index !== -1) {
        quotations.value[index] = { ...currentItem.value };
      }
    }
  } else {
    // Add new item
    if (isInvoiceMode.value) {
      invoices.value.push({ ...currentItem.value });
    } else {
      quotations.value.push({ ...currentItem.value });
    }
  }
  
  // Close the modal
  closeModal();
  
  // In a real application, you would save to the backend here
  // api.post('/quotations-invoices', currentItem.value);
};

const openViewItem = (item: QuotationInvoice) => {
  viewItem.value = { ...item };
  showViewModal.value = true;
};

const editCurrentItem = (item: QuotationInvoice) => {
  currentItem.value = { ...item };
  isEditing.value = true;
  showViewModal.value = false;
  showModal.value = true;
};

const convertToInvoice = (item: QuotationInvoice) => {
  // Create a new invoice based on the quotation
  const newInvoice: QuotationInvoice = {
    ...item,
    id: Date.now(),
    type: 'invoice',
    number: `INV-${new Date().getFullYear()}-${String(invoices.value.length + 1).padStart(3, '0')}`,
    date: new Date().toISOString().split('T')[0],
    status: 'Created'
  };
  
  // Add the new invoice
  invoices.value.push(newInvoice);
  
  // Update the quotation status
  const index = quotations.value.findIndex(q => q.id === item.id);
  if (index !== -1) {
    quotations.value[index].status = 'Accepted';
  }
  
  // Switch to invoice mode and show the new invoice
  isInvoiceMode.value = true;
  activeTab.value = 'all';
  
  // Close modals
  closeViewModal();
  closeModal();
  
  // Show success message (in a real app, you might use a toast notification)
  alert('Quotation successfully converted to Invoice!');
};

const markAsPaid = (item: QuotationInvoice) => {
  // Update the invoice status
  const index = invoices.value.findIndex(inv => inv.id === item.id);
  if (index !== -1) {
    invoices.value[index].status = 'Paid';
    
    // In a real application, you would also create an income record
    // api.post('/incomes', {
    //   source: `Invoice #${item.number}`,
    //   category: 'Sales',
    //   amount: item.total,
    //   date: new Date().toISOString().split('T')[0],
    //   notes: `Payment received for invoice #${item.number}`
    // });
    
    // Close modals
    closeViewModal();
    
    // Show success message
    alert('Invoice marked as paid and added to income!');
  }
};

const printItem = () => {
  // In a real application, you would generate a PDF or use a print-specific layout
  window.print();
};

const formatDate = (dateString: string): string => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-MY', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date);
};

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-MY', {
    style: 'currency',
    currency: 'MYR',
    minimumFractionDigits: 2
  }).format(amount);
};

const getStatusClass = (status: string): string => {
  switch (status.toLowerCase()) {
    case 'draft':
      return 'bg-gray-100 text-gray-800';
    case 'sent':
      return 'bg-blue-100 text-blue-800';
    case 'accepted':
      return 'bg-green-100 text-green-800';
    case 'declined':
      return 'bg-red-100 text-red-800';
    case 'created':
      return 'bg-yellow-100 text-yellow-800';
    case 'paid':
      return 'bg-green-100 text-green-800';
    case 'overdue':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

// Lifecycle hooks
onMounted(async () => {
  try {
    loading.value = true;
    // In a real application, you would fetch data from your API
    // const quotationsResponse = await api.get('/quotations');
    // const invoicesResponse = await api.get('/invoices');
    // quotations.value = quotationsResponse.data;
    // invoices.value = invoicesResponse.data;
    
    // For demo purposes, we're using the sample data defined above
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 500));
  } catch (error) {
    console.error('Failed to fetch data:', error);
  } finally {
    loading.value = false;
  }
});

// Watch for changes in mode to update the active tab
watch(isInvoiceMode, () => {
  activeTab.value = 'all';
});
</script>

<style scoped>
/* Print styles */
@media print {
  body * {
    visibility: hidden;
  }
  
  .viewModal, .viewModal * {
    visibility: visible;
  }
  
  .viewModal {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  
  button {
    display: none;
  }
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Ensure tables look good */
table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 8px 12px;
}

/* Add some spacing between sections */
h3 {
  margin-top: 16px;
}
</style>
