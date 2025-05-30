<template>
    <div class="min-h-screen bg-gray-50">
      <app-header />
  
      <div class="py-4 sm:py-6">
        <header>
          <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
            <h1 class="text-xl sm:text-2xl font-bold leading-tight text-gray-900">Receipt Filing</h1>
            <p class="mt-1 text-sm text-gray-600">Upload or manually enter your receipts for tax deductions</p>
          </div>
        </header>
        
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 mt-2">
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <!-- Information icon -->
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-700">
                  <strong>Note:</strong> Tax categories suggested are for reference only and may not be accurate for your specific situation. Please review and consult a tax professional for definitive advice.
                </p>
              </div>
            </div>
          </div>
        </div>
  
        <main>
          <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
            <!-- Method Selection -->
            <div v-if="!selectedMethod && !isEditing" class="mt-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Upload Method Card -->
                <div @click="selectMethod('upload')"
                  class="bg-white overflow-hidden shadow-sm rounded-lg transition-all duration-300 hover:shadow-md cursor-pointer border-2 border-transparent hover:border-indigo-200">
                  <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-center mb-4">
                      <div class="bg-indigo-100 p-3 rounded-full">
                        <UploadIcon class="h-8 w-8 text-indigo-600" />
                      </div>
                    </div>
                    <h3 class="text-center text-lg font-medium text-gray-900">Upload Receipts</h3>
                    <p class="mt-2 text-sm text-center text-gray-600">
                      Upload images or PDFs of your receipts and let our AI extract the information
                    </p>
                  </div>
                </div>

  
                <!-- Manual Entry Card -->
                <div @click="selectMethod('manual')"
                  class="bg-white overflow-hidden shadow-sm rounded-lg transition-all duration-300 hover:shadow-md cursor-pointer border-2 border-transparent hover:border-indigo-200">
                  <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-center mb-4">
                      <div class="bg-green-100 p-3 rounded-full">
                        <PencilIcon class="h-8 w-8 text-green-600" />
                      </div>
                    </div>
                    <h3 class="text-center text-lg font-medium text-gray-900">Manual Entry</h3>
                    <p class="mt-2 text-sm text-center text-gray-600">
                      Manually enter receipt details for precise control over your tax records
                    </p>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Upload Interface -->
            <div v-else-if="selectedMethod === 'upload' && !isProcessing && !isEditing" class="mt-6">
              <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Receipts</h3>
                  <button type="button" @click="selectedMethod = null"
          class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <ArrowLeftIcon class="-ml-0.5 mr-2 h-4 w-4" />
          Back
        </button>
                  <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center"
                    :class="{ 'bg-indigo-50 border-indigo-300': isDragging }" @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false" @drop.prevent="handleFileDrop">
                    <div class="text-center">
                      <UploadIcon class="mx-auto h-12 w-12 text-gray-400" />
                      <p class="mt-1 text-sm text-gray-600">
                        Drag and drop your receipt files here, or
                        <button type="button" @click="$refs.fileInput.click()"
                          class="text-indigo-600 hover:text-indigo-500 font-medium">
                          browse
                        </button>
                        to select files
                      </p>
                      <p class="mt-1 text-xs text-gray-500">
                        Supports JPG, PNG, and PDF files (max 10MB each)
                      </p>
                    </div>
  
                    <input ref="fileInput" type="file" class="hidden" multiple accept=".jpg,.jpeg,.png,.pdf"
                      @change="handleFileSelect" />
  
                    <div class="mt-4 flex space-x-4">
                      <button type="button" @click="captureImage"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <CameraIcon class="-ml-0.5 mr-2 h-4 w-4" />
                        Take Photo
                      </button>
                      <button type="button" @click="$refs.fileInput.click()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <DocumentIcon class="-ml-0.5 mr-2 h-4 w-4" />
                        Select Files
                      </button>
                    </div>
                  </div>
  
                  <!-- Selected Files Preview -->
                  <div v-if="selectedFiles.length > 0" class="mt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Files ({{ selectedFiles.length }})</h4>
                    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-md overflow-hidden">
                      <li v-for="(file, index) in selectedFiles" :key="index"
                        class="px-4 py-3 flex items-center justify-between bg-white">
                        <div class="flex items-center">
                          <DocumentIcon v-if="file.type.includes('pdf')" class="h-5 w-5 text-red-500 mr-3" />
                          <PhotographIcon v-else class="h-5 w-5 text-blue-500 mr-3" />
                          <span class="text-sm text-gray-900 truncate max-w-xs">{{ file.name }}</span>
                        </div>
                        <button type="button" @click="removeFile(index)"
                          class="text-gray-400 hover:text-gray-500 focus:outline-none">
                          <XIcon class="h-5 w-5" />
                        </button>
                      </li>
                    </ul>
  
                    <div class="mt-4 flex justify-end">
                      <button type="button" @click="cancelUpload"
                        class="mr-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                      </button>
                      <button type="button" @click="processReceipts"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Process Receipts
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Processing Animation -->
            <div v-else-if="isProcessing" class="mt-6">
              <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6 flex flex-col items-center justify-center">
                  <div class="w-full max-w-md">
                    <div class="relative pt-1">
                      <div class="flex mb-2 items-center justify-between">
                        <div>
                          <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-200">
                            Processing Receipts
                          </span>
                        </div>
                        <div class="text-right">
                          <span class="text-xs font-semibold inline-block text-indigo-600">
                            {{ Math.round(processingProgress) }}%
                          </span>
                        </div>
                      </div>
                      <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-200">
                        <div :style="{ width: processingProgress + '%' }"
                          class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500 transition-all duration-500 ease-in-out">
                        </div>
                      </div>
                    </div>
                  </div>
  
                  <div class="mt-4 text-center">
                    <div class="receipt-scanner-animation">
                      <div class="scanner-container">
                        <div class="receipt-image"></div>
                        <div class="scan-line"></div>
                        <div class="data-extraction">
                          <div class="data-particle"></div>
                          <div class="data-particle"></div>
                          <div class="data-particle"></div>
                        </div>
                      </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">{{ processingMessage }}</p>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Receipt Folders View -->
            <div v-else-if="extractedReceipts.length > 0 && !isEditing" class="mt-6">
              <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                  <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Extracted Receipts</h3>
                    <button type="button" @click="addNewReceipt"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      <PlusIcon class="-ml-0.5 mr-2 h-4 w-4" />
                      Add Receipt
                    </button>
                  </div>
  
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(receipt, index) in extractedReceipts" :key="index" @click="editReceipt(index)"
  class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200 cursor-pointer">
  <div class="p-4">
    <div class="flex items-start justify-between">
      <div class="flex items-center">
        <div :class="receipt.isDeductible ? 'bg-green-100' : 'bg-red-100'" class="p-2 rounded-full">
          <CheckCircleIcon v-if="receipt.isDeductible" class="h-5 w-5 text-green-600" />
          <XCircleIcon v-else class="h-5 w-5 text-red-600" />
        </div>
        <div class="ml-3">
          <h4 class="text-sm font-medium text-gray-900">{{ receipt.vendor }}</h4>
          <p class="text-xs text-gray-500">{{ formatDate(receipt.date) }}</p>
        </div>
      </div>
      <span class="text-sm font-semibold">${{ receipt.total.toFixed(2) }}</span>
    </div>

    <div class="mt-3">
      <div class="flex items-center text-xs text-gray-500">
        <TagIcon class="h-4 w-4 mr-1" />
        <span>{{ receipt.category }}</span>
      </div>
      <div class="flex items-center text-xs text-gray-500 mt-1">
        <ReceiptTaxIcon class="h-4 w-4 mr-1" />
        <span>{{ receipt.items.length }} items</span>
      </div>
    </div>

    <!-- Add warning for mixed deductibility -->
    <div v-if="receipt.isDeductible && receipt.items.some(item => !item.isDeductible)" 
      class="mt-2 bg-yellow-50 border border-yellow-200 rounded-md p-2">
      <div class="flex items-center">
        <ExclamationIcon class="h-4 w-4 text-yellow-600 mr-1" />
        <span class="text-xs text-yellow-700">Contains non-deductible items</span>
      </div>
    </div>

    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between">
      <span :class="receipt.isDeductible ? 'text-green-600' : 'text-red-600'"
        class="text-xs font-medium">
        {{ receipt.isDeductible ? 'Tax Deductible' : 'Non-Deductible' }}
      </span>
      <button @click.stop="removeReceipt(index)"
        class="text-gray-400 hover:text-gray-500 focus:outline-none">
        <TrashIcon class="h-4 w-4" />
      </button>
    </div>
  </div>
</div>
                  </div>
  
                  <div class="mt-6 flex justify-end">
                    <button type="button" @click="saveAllReceipts"
                      class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      Save All Receipts
                    </button>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Manual Entry / Edit Receipt Form -->
            <div v-else-if="selectedMethod === 'manual' || isEditing" class="mt-6">
              <div v-if="isNewReceipt && !extractedReceipts.length" class="mb-4">
    <button type="button" @click="selectedMethod = null"
      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
      <ArrowLeftIcon class="-ml-0.5 mr-2 h-4 w-4" />
      Back to Selection
    </button>
  </div>
                <receipt-form
  :receipt="currentReceipt"
  :is-new="isNewReceipt"
  :all-receipts="extractedReceipts"
  @save="saveReceipt"
  @saveAll="saveAllReceipts"
  @cancel="cancelEdit"
  @delete="removeReceipt"
  @addNew="addNewReceipt"
/>

          </div>
        </div>
      </main>
    </div>

    <!-- Camera Modal -->
    <div v-if="showCamera" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
      aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" @click="closeCamera"
            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <span class="sr-only">Close</span>
            <XIcon class="h-6 w-6" />
          </button>
        </div>
        <div class="sm:flex sm:items-start">
          <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              Take a Photo of Your Receipt
            </h3>
            <div class="mt-4">
              <video ref="videoElement" class="w-full h-auto rounded-lg" autoplay></video>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button type="button" @click="takePhoto"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                Take Photo
              </button>
              <button type="button" @click="closeCamera"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, defineComponent, h } from 'vue';
import AppHeader from '@/components/layout/AppHeader.vue';
import ReceiptForm from '@/components/receipt/ReceiptForm.vue';
import api from '@/services/api';
import router from '@/router';

// Define icons
const UploadIcon = defineComponent({
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
      d: 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12'
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

const CameraIcon = defineComponent({
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
      d: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'
    }),
    h('path', {
      'stroke-linecap': 'round',
      'stroke-linejoin': 'round',
      'stroke-width': '2',
      d: 'M15 13a3 3 0 11-6 0 3 3 0 016 0z'
    })
  ])
});

const DocumentIcon = defineComponent({
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

const PhotographIcon = defineComponent({
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
      d: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'
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

const ReceiptTaxIcon = defineComponent({
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
      d: 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z'
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
  imageFile?: File; // Store the original file for later upload
  conversionMessage?: string;
}

// Component state
const selectedMethod = ref<'upload' | 'manual' | null>(null);
const isDragging = ref(false);
const selectedFiles = ref<File[]>([]);
const isProcessing = ref(false);
const processingProgress = ref(0);
const processingMessage = ref('Analyzing receipt...');
const extractedReceipts = ref<Receipt[]>([]);
const isEditing = ref(false);
const currentReceipt = ref<Receipt | null>(null);
const isNewReceipt = ref(false);
const showCamera = ref(false);
const videoElement = ref<HTMLVideoElement | null>(null);
const stream = ref<MediaStream | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

// Methods
const selectMethod = (method: 'upload' | 'manual') => {
  selectedMethod.value = method;
  
  if (method === 'manual') {
    isNewReceipt.value = true;
    currentReceipt.value = createEmptyReceipt();
    isEditing.value = true;
  }
};

const createEmptyReceipt = (): Receipt => {
  return {
    id: Date.now(),
    vendor: '',
    date: new Date().toISOString().split('T')[0],
    total: 0,
    category: 'Office Supplies',
    items: [],
    isDeductible: true
  };
};

const handleFileSelect = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files) {
    addFiles(Array.from(input.files));
  }
};

const handleFileDrop = (event: DragEvent) => {
  isDragging.value = false;
  if (event.dataTransfer?.files) {
    addFiles(Array.from(event.dataTransfer.files));
  }
};

const addFiles = (files: File[]) => {
  // Filter for supported file types and size
  const validFiles = files.filter(file => {
    const isValidType = ['image/jpeg', 'image/png', 'application/pdf'].includes(file.type);
    const isValidSize = file.size <= 10 * 1024 * 1024; // 10MB
    return isValidType && isValidSize;
  });

  // Add valid files to the selected files array
  selectedFiles.value = [...selectedFiles.value, ...validFiles];
};

const removeFile = (index: number) => {
  selectedFiles.value.splice(index, 1);
};

const cancelUpload = () => {
  selectedFiles.value = [];
  selectedMethod.value = null;
};

const formatDateForInput = (dateString: string): string => {
  if (!dateString) return '';
  
  // Parse date in format DD/MM/YY
  const parts = dateString.split('/');
  if (parts.length !== 3) return '';
  
  const day = parts[0];
  const month = parts[1];
  let year = parts[2];
  
  // Add century to year if it's just 2 digits
  if (year.length === 2) {
    // Assume 20xx for years less than 50, 19xx for years 50 or greater
    const century = parseInt(year) < 50 ? '20' : '19';
    year = century + year;
  }
  
  // Format as YYYY-MM-DD
  return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
};

const processReceipts = async () => {
  if (selectedFiles.value.length === 0) return;

  isProcessing.value = true;
  processingProgress.value = 0;
  
  try {
    // Process each file one by one
    const totalFiles = selectedFiles.value.length;
    
    for (let i = 0; i < totalFiles; i++) {
      const file = selectedFiles.value[i];
      
      // Update progress and message
      processingProgress.value = (i / totalFiles) * 100;
      processingMessage.value = `Processing ${file.name} (${i + 1}/${totalFiles})...`;
      
      // Create form data for this file
      const formData = new FormData();
      formData.append('image', file);
      
      // Send the file to the backend for processing
      try {
        const response = await api.post('/receipts/images/process-ai', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        console.log('Extracted data:', response.data.data);
        
        // Check if response.data.data is an array
        const extractedDataArray = Array.isArray(response.data.data) 
          ? response.data.data 
          : [response.data.data];
        
        // Process each receipt in the array
        for (const extractedData of extractedDataArray) {
          console.log('Processing receipt:', extractedData); // Add this for debugging
          
          // Map items with proper property names
          const mappedItems = extractedData.items?.map((item: any) => {
            console.log('Processing item:', item); // Add this for debugging
            return {
              id: Date.now() + Math.random(),
              name: item.description || 'Unknown Item',
              price: item.unit_price || 0,
              quantity: item.quantity || 1,
              category: item.expense_category || 'Miscellaneous',
              isDeductible: item.expense_category === 'Miscellaneous' ? false : true
            };
          }) || [];
          
          console.log('Mapped items:', mappedItems); // Add this for debugging
          
          const receipt: Receipt = {
            id: Date.now() + Math.random(),
            vendor: extractedData.vendor_name || 'Unknown Vendor',
            date: formatDateForInput(extractedData.date) || new Date().toISOString().split('T')[0],
            total: extractedData.total_amount || 0,
            category: extractedData.items?.[0]?.expense_category || 'Miscellaneous',
            items: mappedItems,
            isDeductible: extractedData.is_deductible,
            imageUrl: URL.createObjectURL(file),
            imageFile: file,
            conversionMessage:extractedData.conversion_message || null
          };
          
          console.log('Created receipt object:', receipt); // Add this for debugging
          
          extractedReceipts.value.push(receipt);
        }
      } catch (error) {
        console.error(`Error processing file ${file.name}:`, error);
        
        // If extraction fails, create a basic receipt with the image
        const basicReceipt: Receipt = {
          id: Date.now() + i,
          vendor: 'Could not extract',
          date: new Date().toISOString().split('T')[0],
          total: 0,
          category: 'Miscellaneous',
          items: [],
          isDeductible: true,
          imageUrl: URL.createObjectURL(file),
          imageFile: file
        };
        
        extractedReceipts.value.push(basicReceipt);
      }
    }
    
    // Complete processing
    processingProgress.value = 100;
    processingMessage.value = 'Processing complete!';
    
    // Short delay before showing results
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Important: Set up editing for the first extracted receipt
    if (extractedReceipts.value.length > 0) {
      currentReceipt.value = JSON.parse(JSON.stringify(extractedReceipts.value[0]));
      isNewReceipt.value = false;
    }
    
  } catch (error) {
    console.error('Error during receipt processing:', error);
  } finally {
    // Reset processing state
    isProcessing.value = false;
    isEditing.value = true;  // Set editing mode
  }
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date);
};

const editReceipt = (index: number) => {
  currentReceipt.value = JSON.parse(JSON.stringify(extractedReceipts.value[index]));
  isEditing.value = true;
  isNewReceipt.value = false;
};

const addNewReceipt = () => {
  currentReceipt.value = createEmptyReceipt();
  isEditing.value = true;
  isNewReceipt.value = true;
};

const saveReceipt = (receipt: Receipt) => {
  if (isNewReceipt.value) {
    // Add new receipt
    extractedReceipts.value.push(receipt);
  } else {
    // Update existing receipt
    const index = extractedReceipts.value.findIndex(r => r.id === receipt.id);
    if (index !== -1) {
      // Preserve the original image file if it exists
      if (extractedReceipts.value[index].imageFile) {
        receipt.imageFile = extractedReceipts.value[index].imageFile;
      }
      extractedReceipts.value[index] = receipt;
    }
  }
  
  // Reset state
  isEditing.value = false;
  currentReceipt.value = null;
};

const cancelEdit = () => {
  isEditing.value = false;
  currentReceipt.value = null;
  
  if (isNewReceipt.value && extractedReceipts.value.length === 0) {
    selectedMethod.value = null;
  }
};

const removeReceipt = (index: number) => {
  // Release object URL to prevent memory leaks
  if (extractedReceipts.value[index].imageUrl && extractedReceipts.value[index].imageUrl.startsWith('blob:')) {
    URL.revokeObjectURL(extractedReceipts.value[index].imageUrl);
  }
  
  extractedReceipts.value.splice(index, 1);
};

const saveAllReceipts = async (updatedReceipts = null) => {
  console.log('Saving all receipts:', updatedReceipts);
  console.log('Default all receipts:', extractedReceipts.value);

  // If updated receipts are provided from the form, use them
  // but preserve the original imageFile references
  if (updatedReceipts) {
    // For each updated receipt, find the matching original receipt and preserve its imageFile
    updatedReceipts.forEach(updatedReceipt => {
      const originalReceipt = extractedReceipts.value.find(r => r.id === updatedReceipt.id);
      if (originalReceipt && originalReceipt.imageFile) {
        updatedReceipt.imageFile = originalReceipt.imageFile;
      }
    });
    
    extractedReceipts.value = updatedReceipts;
  }
  
  if (extractedReceipts.value.length === 0) return;
  
  isProcessing.value = true;
  processingProgress.value = 0;
  processingMessage.value = 'Saving receipts...';
  
  try {
    const totalReceipts = extractedReceipts.value.length;
    
    for (let i = 0; i < totalReceipts; i++) {
      const receipt = extractedReceipts.value[i];
      
      // Update progress
      processingProgress.value = (i / totalReceipts) * 100;
      processingMessage.value = `Saving receipt ${i + 1} of ${totalReceipts}...`;
      
      // Create form data for this receipt
      const formData = new FormData();
      
      // Add receipt data as JSON, but exclude imageFile and imageUrl properties
      const receiptData = {
        vendor: receipt.vendor,
        date: receipt.date,
        total: receipt.total,
        category: receipt.category,
        is_deductible: receipt.isDeductible,
        items: receipt.items.map(item => ({
          name: item.name,
          price: item.price,
          quantity: item.quantity,
          category: item.category,
          is_deductible: item.isDeductible
        }))
      };
      
      formData.append('data', JSON.stringify(receiptData));
      
      // Add image file if available
      if (receipt.imageFile) {
        formData.append('image', receipt.imageFile);
      }
      
      await api.post('/receipts', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      
      // Short delay to avoid overwhelming the server
      await new Promise(resolve => setTimeout(resolve, 300));
    }
    
    // Complete saving
    processingProgress.value = 100;
    processingMessage.value = 'All receipts saved successfully!';
        // Clear receipts and reset state
    extractedReceipts.value = [];
    selectedMethod.value = null;
    selectedFiles.value = [];
    router.push('/expenses');
    // Short delay before resetting
    await new Promise(resolve => setTimeout(resolve, 1000));
    

    
  } catch (error) {
    console.error('Error saving receipts:', error);
    processingMessage.value = 'Error saving receipts. Please try again.';
    
    // Short delay before resetting processing state
    await new Promise(resolve => setTimeout(resolve, 2000));
  } finally {
    isProcessing.value = false;
  }
};



const captureImage = async () => {
  try {
    showCamera.value = true;
    
    // Request camera access
    stream.value = await navigator.mediaDevices.getUserMedia({ 
      video: { facingMode: 'environment' } 
    });
    
    // Once we have the stream, attach it to the video element
    if (videoElement.value && stream.value) {
      videoElement.value.srcObject = stream.value;
    }
  } catch (error) {
    console.error('Error accessing camera:', error);
    showCamera.value = false;
  }
};

const takePhoto = () => {
  if (!videoElement.value || !stream.value) return;
  
  // Create a canvas element to capture the image
  const canvas = document.createElement('canvas');
  canvas.width = videoElement.value.videoWidth;
  canvas.height = videoElement.value.videoHeight;
  
  // Draw the video frame to the canvas
  const context = canvas.getContext('2d');
  if (context) {
    context.drawImage(videoElement.value, 0, 0, canvas.width, canvas.height);
    
    // Convert the canvas to a blob
    canvas.toBlob((blob) => {
      if (blob) {
        // Create a File object from the blob
        const file = new File([blob], `receipt-${Date.now()}.jpg`, { type: 'image/jpeg' });
        
        // Add the file to selected files
        addFiles([file]);
        
        // Close the camera
        closeCamera();
      }
    }, 'image/jpeg', 0.95);
  }
};

const closeCamera = () => {
  // Stop all video streams
  if (stream.value) {
    stream.value.getTracks().forEach(track => track.stop());
    stream.value = null;
  }
  
  // Hide the camera modal
  showCamera.value = false;
};

// Clean up on component unmount
onMounted(() => {
  // Any initialization if needed
});
</script>

<style scoped>
/* Receipt Scanner Animation */
.receipt-scanner-animation {
  width: 200px;
  height: 250px;
  margin: 0 auto;
  position: relative;
}

.scanner-container {
  width: 100%;
  height: 100%;
  background-color: #f3f4f6;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.receipt-image {
  width: 80%;
  height: 90%;
  background-color: white;
  margin: 5% auto;
  position: relative;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.scan-line {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #4f46e5;
  box-shadow: 0 0 8px #4f46e5;
  animation: scan 2s ease-in-out infinite;
}

.data-extraction {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.data-particle {
  position: absolute;
  width: 4px;
  height: 4px;
  background-color: #4f46e5;
  border-radius: 50%;
  opacity: 0;
}

.data-particle:nth-child(1) {
  animation: particle 3s ease-in-out infinite;
  animation-delay: 0.2s;
}

.data-particle:nth-child(2) {
  animation: particle 3s ease-in-out infinite;
  animation-delay: 0.8s;
}

.data-particle:nth-child(3) {
  animation: particle 3s ease-in-out infinite;
  animation-delay: 1.5s;
}

@keyframes scan {
  0% {
    top: 0;
  }
  50% {
    top: 100%;
  }
  100% {
    top: 0;
  }
}

@keyframes particle {
  0% {
    top: 50%;
    left: 50%;
    opacity: 0;
    transform: scale(0);
  }
  20% {
    opacity: 1;
    transform: scale(1);
  }
  80% {
    opacity: 1;
  }
  100% {
    top: 80%;
    left: 80%;
    opacity: 0;
    transform: scale(0);
  }
}
</style>

  