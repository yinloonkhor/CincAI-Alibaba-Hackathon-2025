<template>
  <div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <!-- Receipt Navigation Bar -->
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
      <div class="flex items-center">
        <h3 class="text-base font-medium text-gray-900">
          {{ isNew ? 'New Receipt' : 'Edit Receipt' }}
        </h3>

        <!-- Receipt counter and navigation -->
        <div v-if="totalReceipts > 1" class="ml-3 flex items-center" :class="{ 'text-red-600': hasNonDeductibleReceipt }">
          <button @click="prevReceipt" :disabled="receiptIndex === 0"
            class="p-1.5 rounded-md hover:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :class="{ 'hover:bg-red-200 focus:ring-red-500': hasNonDeductibleReceipt }">
            <ChevronLeftIcon class="h-4 w-4" :class="hasNonDeductibleReceipt ? 'text-red-600' : 'text-gray-600'" />
          </button>
          <span class="mx-2 text-sm" :class="hasNonDeductibleReceipt ? 'text-red-600' : 'text-gray-600'">
            {{ receiptIndex + 1 }} / {{ totalReceipts }}
          </span>
          <button @click="nextReceipt" :disabled="receiptIndex === totalReceipts - 1"
            class="p-1.5 rounded-md hover:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :class="{ 'hover:bg-red-200 focus:ring-red-500': hasNonDeductibleReceipt }">
            <ChevronRightIcon class="h-4 w-4" :class="hasNonDeductibleReceipt ? 'text-red-600' : 'text-gray-600'" />
          </button>
        </div>


      </div>

      <div class="flex items-center space-x-2">
        <span v-if="!isNew"
          :class="localReceipt.isDeductible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
          class="hidden sm:inline-flex px-2 py-1 text-xs font-medium rounded-full">
          {{ localReceipt.isDeductible ? 'Tax Deductible' : 'Non-Deductible' }}
        </span>
        <button v-if="totalReceipts >= 1" @click="addNewReceipt" type="button"
          class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          <PlusIcon class="h-3.5 w-3.5 mr-1" />
          <span class="hidden sm:inline">New Receipt</span>
          <span class="sm:hidden">New</span>
        </button>
      </div>
    </div>

    <div class="px-4 py-4 sm:p-5">
      
      <form @submit.prevent="saveReceipt">
        <div v-if="totalReceipts > 1" class="sm:hidden mb-4">
          <div class="flex overflow-x-auto scrollbar-hide space-x-2 pb-2">
            <button v-for="(receipt, index) in allLocalReceipts" :key="receipt.id" type="button"
              @click="switchToReceipt(index)" :class="[
                'flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                receiptIndex === index
                  ? 'bg-indigo-600 text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]">
              {{ receipt.vendor || `Receipt ${index + 1}` }}
            </button>
          </div>
        </div>

        <div v-if="localReceipt.conversionMessage" class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <ExclamationIcon class="h-5 w-5 text-yellow-400" />
            </div>
            <div class="ml-3">
              <p class="text-sm text-yellow-700">
                {{ localReceipt.conversionMessage }}
              </p>
            </div>
          </div>
        </div>

        <div class="space-y-5">
          <!-- Receipt Image and Basic Info Section -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Receipt Image Preview -->
            <div v-if="localReceipt.imageUrl" class="sm:col-span-1">
              <div class="relative rounded-lg overflow-hidden bg-gray-100 h-40 sm:h-full">
                <img :src="localReceipt.imageUrl" alt="Receipt" class="w-full h-full object-contain" />
                <button type="button" @click="removeImage"
                  class="absolute top-2 right-2 bg-white rounded-full p-1 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                  <XIcon class="h-4 w-4 text-gray-500" />
                </button>
              </div>
            </div>

            <!-- Basic Receipt Information -->
            <div :class="localReceipt.imageUrl ? 'sm:col-span-2' : 'sm:col-span-3'">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor/Merchant</label>
                  <input type="text" id="vendor" v-model="localReceipt.vendor"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="e.g. Office Depot" required />
                </div>

                <div>
                  <label for="date" class="block text-sm font-medium text-gray-700">Receipt Date</label>
                  <input type="date" id="date" v-model="localReceipt.date"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required />
                </div>

                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                  <input id="category" v-model="localReceipt.category"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required />

                </div>

                <div>
                  <label for="total" class="block text-sm font-medium text-gray-700">Total Amount</label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0  flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">RM</span>
                    </div>
                    <input type="number" id="total" v-model="localReceipt.total" step="0.01" min="0"
                      class="block w-full pl-7 pr-12 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      placeholder="0.00" required />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Receipt Items Section -->
          <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-center mb-3">
              <h4 class="text-sm font-medium text-gray-900">Items</h4>
              <button type="button" @click="addItem"
                class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <PlusIcon class="h-3.5 w-3.5 mr-1" />
                Add Item
              </button>
            </div>

            <!-- Empty state -->
            <div v-if="localReceipt.items.length === 0" class="text-center py-6">
              <DocumentTextIcon class="mx-auto h-10 w-10 text-gray-300" />
              <p class="mt-2 text-sm text-gray-500">No items added yet</p>
              <button type="button" @click="addItem" class="mt-2 text-sm text-indigo-600 hover:text-indigo-500">
                Add your first item
              </button>
            </div>

            <!-- Item cards for mobile -->
            <div v-else>
              <div class="sm:hidden space-y-3">
                <div v-for="(item, index) in localReceipt.items" :key="item.id"
                  class="bg-white rounded-md shadow-sm p-3 border border-gray-200">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <input type="text" v-model="item.name"
                        class="block w-full border-0 p-0 text-sm font-medium text-gray-900 focus:ring-0"
                        placeholder="Item description" required />
                    </div>
                    <button type="button" @click="removeItem(index)"
                      class="ml-2 text-gray-400 hover:text-red-500 focus:outline-none">
                      <TrashIcon class="h-4 w-4" />
                    </button>
                  </div>

                  <div class="mt-2 grid grid-cols-2 gap-2">
                    <div>
                      <label class="block text-xs text-gray-500">Price</label>
                      <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0  flex items-center pointer-events-none">
                          <span class="text-gray-500 text-xs">RM</span>
                        </div>
                        <input type="number" v-model="item.price" step="0.01" min="0"
                          class="block w-full pl-6 py-1 text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="0.00" required @input="updateTotal" />
                      </div>
                    </div>

                    <div>
                      <label class="block text-xs text-gray-500">Quantity</label>
                      <input type="number" v-model="item.quantity" min="1" step="1"
                        class="mt-1 block w-full py-1 text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        required @input="updateTotal" />
                    </div>
                  </div>

                  <div class="mt-2 flex justify-between items-center">
                    <div>
                      <input type="text" v-model="item.category"
                        class="mt-1 block w-full py-1 text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                        required />
                    </div>

                    <div class="text-right">
                      <div class="text-sm font-medium text-gray-900">
                        RM{{ (item.price * item.quantity).toFixed(2) }}
                      </div>
                    </div>
                  </div>

                  <div class="mt-2 pt-2 border-t border-gray-100 flex items-center">
                    <input type="checkbox" v-model="item.isDeductible" :id="`item-deductible-${index}`"
                      class="h-3.5 w-3.5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      @change="updateDeductibleStatus" />
                    <label :for="`item-deductible-${index}`" class="ml-2 text-xs text-gray-500">
                      Tax deductible
                    </label>
                  </div>
                </div>

                <!-- Mobile total -->
                <div class="bg-white rounded-md shadow-sm p-3 border border-gray-200 flex justify-between items-center">
                  <span class="text-sm font-medium text-gray-700">Total:</span>
                  <span class="text-base font-bold text-gray-900">RM{{ localReceipt.total.toFixed(2) }}</span>
                </div>
              </div>

              <!-- Item table for desktop -->
              <div class="hidden sm:block">
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                      <tr>
                        <th scope="col"
                          class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Category
                        </th>
                        <th scope="col"
                          class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Item
                        </th>
                        <th scope="col"
                          class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Price
                        </th>
                        <th scope="col"
                          class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Qty
                        </th>
                        <th scope="col"
                          class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Total
                        </th>
                        <th scope="col"
                          class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Actions
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="(item, index) in localReceipt.items" :key="item.id"
                        class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-3 py-2">
                          <div class="flex flex-col">

                            <input type="text" v-model="item.category"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="Item description" required />

                          </div>
                        </td>
                        <td class="px-3 py-2">
                          <div class="flex flex-col">
                            <textarea type="text" v-model="item.name"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="Item name" required></textarea>

                          </div>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                          <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                              <span class="text-gray-500 sm:text-xs">RM</span>
                            </div>
                            <input type="number" v-model="item.price" step="0.01" min="0"
                              class="block w-full pl-6 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="0.00" required @input="updateTotal" />
                          </div>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                          <input type="number" v-model="item.quantity" min="1" step="1"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required @input="updateTotal" />
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                          <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900">
                              RM{{ (item.price * item.quantity).toFixed(2) }}
                            </span>
                            <div class="mt-1 flex items-center">
                              <input type="checkbox" v-model="item.isDeductible" :id="`item-deductible-${index}`"
                                class="h-3 w-3 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                @change="updateDeductibleStatus" />
                              <label :for="`item-deductible-${index}`" class="ml-1 text-xs text-gray-500">
                                Tax deductible
                              </label>
                            </div>
                          </div>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-right">
                          <button type="button" @click="removeItem(index)"
                            class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <TrashIcon class="h-3 w-3" />
                          </button>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr class="bg-gray-50">
                        <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-700">
                          Total:
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900">
                          RM{{ localReceipt.total.toFixed(2) }}
                        </td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Tax Deductibility Status -->
          <div class="bg-gray-50 rounded-lg p-3 flex items-center" v-if="localReceipt.items.length > 0">
            <div :class="localReceipt.isDeductible ? 'bg-green-500' : 'bg-red-500'" class="text-white p-1.5 rounded-md">
              <component :is="localReceipt.isDeductible ? CheckCircleIcon : XCircleIcon" class="h-4 w-4" />
            </div>
            <div class="ml-3">
              <h4 class="text-sm font-medium text-gray-900">
                {{ localReceipt.isDeductible ? 'Tax Deductible' : 'Non-Deductible' }}
              </h4>
              <p class="text-xs text-gray-500">
                {{ localReceipt.isDeductible
                  ? 'All items in this receipt are tax deductible'
                  : 'Some items in this receipt are not tax deductible' }}
              </p>
            </div>
          </div>
        </div>

        <div v-if="showEmptyItemsError" class="mt-4 bg-red-50 border-l-4 border-red-400 p-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <ExclamationIcon class="h-5 w-5 text-red-400" />
      </div>
      <div class="ml-3">
        <p class="text-sm text-red-700">
          Receipt items cannot be empty. Please add at least one item before saving.
        </p>
      </div>
    </div>
  </div>

        <!-- Form Actions -->
        <div class="mt-5 flex justify-between items-center border-t border-gray-200 pt-4">
          <div>
            <button v-if="totalReceipts > 1" type="button" @click="deleteReceipt"
              class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
              <TrashIcon class="h-4 w-4 mr-1" />
              <span class="hidden sm:inline">Delete Receipt</span>
              <span class="sm:hidden">Delete</span>
            </button>
          </div>
          <div class="flex space-x-3">
            <button type="button" @click="cancel"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Cancel
            </button>
            <button type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Save Receipt
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, defineProps, defineEmits, onMounted, watch, defineComponent, h } from 'vue';
const showEmptyItemsError = ref(false);

const hasNonDeductibleReceipt = computed(() => {
  return allLocalReceipts.value.some(receipt => !receipt.isDeductible);
});
// Define icons
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

const ChevronLeftIcon = defineComponent({
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
      d: 'M15 19l-7-7 7-7'
    })
  ])
});

const ChevronRightIcon = defineComponent({
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
      d: 'M9 5l7 7-7 7'
    })
  ])
});

const CheckCircleIcon = defineComponent({
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
      d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    })
  ])
});

const ExclamationIcon = defineComponent({
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
      d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
    })
  ])
});

const XCircleIcon = defineComponent({
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
      d: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
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

// Define types
interface ReceiptItem {
  id: number;
  name: string;
  price: number;
  quantity: number;
  category: string;
  isDeductible: boolean;
}

interface Receipt {
  id: number;
  vendor: string;
  date: string;
  total: number;
  category: string;
  items: ReceiptItem[];
  isDeductible: boolean;
  imageUrl?: string;
  imageFile?: File;
  conversionMessage?: string;
}

// Props and emits
const props = defineProps<{
  receipt: Receipt | null;
  isNew: boolean;
  allReceipts?: Receipt[];
}>();

const emit = defineEmits<{
  (e: 'save', receipt: Receipt): void;
  (e: 'saveAll', receipts: Receipt[]): void;
  (e: 'cancel'): void;
  (e: 'delete', receiptId: number): void;
  (e: 'addNew'): void;
}>();

// Local state
const localReceipt = ref<Receipt>({
  id: 0,
  vendor: '',
  date: new Date().toISOString().split('T')[0],
  total: 0,
  category: 'Office Supplies',
  items: [],
  isDeductible: true,
  conversionMessage: '',
});

const receiptIndex = ref(0);
const allLocalReceipts = ref<Receipt[]>([]);

// Computed properties
const totalReceipts = computed(() => {
  return allLocalReceipts.value.length;
});

// Initialize form with receipt data if provided
onMounted(() => {
  if (props.allReceipts && props.allReceipts.length > 0) {
    // Initialize with all receipts
    allLocalReceipts.value = JSON.parse(JSON.stringify(props.allReceipts));

    // Set the current receipt to the first one or the specified one
    if (props.receipt) {
      localReceipt.value = JSON.parse(JSON.stringify(props.receipt));
      // Find the index of the current receipt
      const index = props.allReceipts.findIndex(r => r.id === props.receipt?.id);
      if (index !== -1) {
        receiptIndex.value = index;
      }
    } else {
      localReceipt.value = JSON.parse(JSON.stringify(props.allReceipts[0]));
    }
  } else if (props.receipt) {
    // Initialize with a single receipt
    localReceipt.value = JSON.parse(JSON.stringify(props.receipt));
    allLocalReceipts.value = [localReceipt.value];
  } else {
    // Initialize with default values
    localReceipt.value = {
      id: Date.now(),
      vendor: '',
      date: new Date().toISOString().split('T')[0],
      total: 0,
      category: 'Office Supplies',
      items: [],
      isDeductible: true
    };
    allLocalReceipts.value = [localReceipt.value];
  }
});

// Watch for changes in props
watch(() => props.receipt, (newReceipt) => {
  if (newReceipt) {
    localReceipt.value = JSON.parse(JSON.stringify(newReceipt));
  }
});

watch(() => props.allReceipts, (newReceipts) => {
  if (newReceipts && newReceipts.length > 0) {
    allLocalReceipts.value = JSON.parse(JSON.stringify(newReceipts));
  }
});

// Methods
const addItem = () => {
  const newItem: ReceiptItem = {
    id: Date.now(),
    name: '',
    price: 0,
    quantity: 1,
    category: localReceipt.value.category,
    isDeductible: true
  };

  localReceipt.value.items.push(newItem);

  // Update the receipt in the allLocalReceipts array
  updateLocalReceiptInArray();
};

const removeItem = (index: number) => {
  localReceipt.value.items.splice(index, 1);
  updateTotal();
  updateDeductibleStatus();

  // Update the receipt in the allLocalReceipts array
  updateLocalReceiptInArray();
};

const updateTotal = () => {
  let total = 0;
  for (const item of localReceipt.value.items) {
    total += item.price * item.quantity;
  }
  localReceipt.value.total = parseFloat(total.toFixed(2));

  // Update the receipt in the allLocalReceipts array
  updateLocalReceiptInArray();
};

const updateDeductibleStatus = () => {
  // A receipt is deductible if all items are deductible
  localReceipt.value.isDeductible = localReceipt.value.items.length > 0 &&
    localReceipt.value.items.every(item => item.isDeductible);

  // Update the receipt in the allLocalReceipts array
  updateLocalReceiptInArray();
};

const removeImage = () => {
  localReceipt.value.imageUrl = undefined;
  localReceipt.value.imageFile = undefined;

  // Update the receipt in the allLocalReceipts array
  updateLocalReceiptInArray();
};

const updateLocalReceiptInArray = () => {
  if (allLocalReceipts.value.length > receiptIndex.value) {
    allLocalReceipts.value[receiptIndex.value] = JSON.parse(JSON.stringify(localReceipt.value));
  }
};

const prevReceipt = () => {
  if (receiptIndex.value > 0) {
    // Save current receipt changes to the array
    updateLocalReceiptInArray();

    // Move to previous receipt
    receiptIndex.value--;
    localReceipt.value = JSON.parse(JSON.stringify(allLocalReceipts.value[receiptIndex.value]));
  }
};

const nextReceipt = () => {
  if (receiptIndex.value < allLocalReceipts.value.length - 1) {
    // Save current receipt changes to the array
    updateLocalReceiptInArray();

    // Move to next receipt
    receiptIndex.value++;
    localReceipt.value = JSON.parse(JSON.stringify(allLocalReceipts.value[receiptIndex.value]));
  }
};

const switchToReceipt = (index: number) => {
  if (index >= 0 && index < allLocalReceipts.value.length) {
    // Save current receipt changes to the array
    updateLocalReceiptInArray();

    // Switch to the selected receipt
    receiptIndex.value = index;
    localReceipt.value = JSON.parse(JSON.stringify(allLocalReceipts.value[index]));
  }
};

const addNewReceipt = () => {
  // Save current receipt changes first
  updateLocalReceiptInArray();

  // Create a new empty receipt
  const newReceipt: Receipt = {
    id: Date.now(),
    vendor: '',
    date: new Date().toISOString().split('T')[0],
    total: 0,
    category: 'Office Supplies',
    items: [],
    isDeductible: true
  };

  // Add it to the array
  allLocalReceipts.value.push(newReceipt);

  // Switch to the new receipt
  receiptIndex.value = allLocalReceipts.value.length - 1;
  localReceipt.value = JSON.parse(JSON.stringify(newReceipt));

  // Notify parent component
  emit('addNew', newReceipt);
};

const deleteReceipt = () => {
  if (allLocalReceipts.value.length > 1) {
    const receiptToDelete = allLocalReceipts.value[receiptIndex.value];
    emit('delete', receiptToDelete.id);
  }
};

const saveReceipt = () => {
  // Check if items array is empty
  if (localReceipt.value.items.length === 0) {
    showEmptyItemsError.value = true;
    
    // Auto-hide the error message after 5 seconds
    setTimeout(() => {
      showEmptyItemsError.value = false;
    }, 5000);
    
    return; // Stop execution and don't save
  }
  
  // Reset error state if we have items
  showEmptyItemsError.value = false;
  
  // Update total one last time to ensure accuracy
  updateTotal();
  
  // Update deductible status
  updateDeductibleStatus();
  
  // Update the receipt in the allLocalReceipts array
  updateLocalReceiptInArray();
  
  // If we have multiple receipts, emit saveAll event with the updated receipts
  emit('saveAll', allLocalReceipts.value);
};


const cancel = () => {
  emit('cancel');
};
</script>

<style scoped>
/* Responsive table styles */
@media (max-width: 768px) {
  table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }

  th,
  td {
    min-width: 80px;
  }
}

/* Hide scrollbar for receipt tabs but keep functionality */
.scrollbar-hide {
  -ms-overflow-style: none;
  /* IE and Edge */
  scrollbar-width: none;
  /* Firefox */
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
  /* Chrome, Safari and Opera */
}

/* Smooth transitions */
.hover\:bg-gray-50 {
  transition: background-color 0.15s ease-in-out;
}

/* Focus styles for better accessibility */
input:focus,
select:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
}

/* Ensure the form doesn't get too tall */
.max-h-40 {
  max-height: 10rem;
}

.bg-red-50 {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Improved mobile form elements */
@media (max-width: 640px) {

  input,
  select {
    font-size: 16px;
    /* Prevents iOS zoom on focus */
  }
}
</style>