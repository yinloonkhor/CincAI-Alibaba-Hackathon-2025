<template>
    <div class="min-h-screen bg-gray-50">
      <app-header />
  
      <div class="py-6 sm:py-8">
        <header>
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
              <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                  Income Summary Report
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                  Tax Year {{ reportData.taxYear }}
                </p>
              </div>
              <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <button 
                  @click="printReport" 
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <PrinterIcon class="h-4 w-4 mr-2" />
                  Print
                </button>
                <button 
                  @click="downloadPDF" 
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <DownloadIcon class="h-4 w-4 mr-2" />
                  Download PDF
                </button>
              </div>
            </div>
          </div>
        </header>
  
        <main>
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Report Content -->
            <div ref="reportContent" class="mt-8 pdf-content">
              <!-- Report Header -->
              <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="flex-shrink-0">
                        <DocumentReportIcon class="h-8 w-8 text-indigo-600" />
                      </div>
                      <div class="ml-4">
                        <h2 class="text-xl font-medium text-gray-900">Income Summary Report</h2>
                        <p class="mt-1 text-sm text-gray-500">Generated on: {{ formattedCurrentDate }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- User Information Card -->
              <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Taxpayer Information</h3>
                  <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Full name</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ reportData.user.name }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Tax ID</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ reportData.user.taxId }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Email address</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ reportData.user.email }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Phone number</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ reportData.user.phone }}</dd>
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- Summary Card -->
              <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="bg-indigo-600 px-4 py-4 sm:px-6">
                  <h3 class="text-lg font-medium text-white">Financial Summary</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                  <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Total Income</dt>
                      <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ formatCurrency(totalIncome) }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Total Deductions</dt>
                      <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ formatCurrency(totalDeductions) }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Taxable Income</dt>
                      <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ formatCurrency(taxableIncome) }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Estimated Tax</dt>
                      <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ formatCurrency(reportData.estimatedTax) }}</dd>
                      <dd class="mt-1 text-xs text-gray-500">Effective rate: {{ effectiveTaxRate }}%</dd>
                    </div>
                  </dl>
                </div>
              </div>
  
              <!-- Income Details Card -->
              <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="bg-gray-50 px-4 py-4 sm:px-6 border-b border-gray-200">
                  <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Income Details</h3>
                    <span class="text-sm font-medium text-gray-500">Total: {{ formatCurrency(totalIncome) }}</span>
                  </div>
                </div>
                <div class="px-4 py-5 sm:p-6">
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead>
                        <tr>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Income Source
                          </th>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                          </th>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                          </th>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            % of Total
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(income, index) in reportData.incomes" :key="index" class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ income.source }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ income.category }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            {{ formatCurrency(income.amount) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                            {{ calculatePercentage(income.amount, totalIncome) }}%
                          </td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr class="bg-gray-50">
                          <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Total Income
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900">
                            {{ formatCurrency(totalIncome) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900">
                            100%
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
  
              <!-- Deductions Details Card -->
              <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="bg-gray-50 px-4 py-4 sm:px-6 border-b border-gray-200">
                  <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Deductions Details</h3>
                    <span class="text-sm font-medium text-gray-500">Total: {{ formatCurrency(totalDeductions) }}</span>
                  </div>
                </div>
                <div class="px-4 py-5 sm:p-6">
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead>
                        <tr>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deduction Type
                          </th>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                          </th>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                          </th>
                          <th scope="col" class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            % of Total
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(deduction, index) in reportData.deductions" :key="index" class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ deduction.type }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ deduction.category }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            {{ formatCurrency(deduction.amount) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                            {{ calculatePercentage(deduction.amount, totalDeductions) }}%
                          </td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr class="bg-gray-50">
                          <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Total Deductions
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900">
                            {{ formatCurrency(totalDeductions) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900">
                            100%
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
  
              <!-- Tax Calculation Card -->
              <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="bg-gray-50 px-4 py-4 sm:px-6 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Tax Calculation</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                  <dl class="space-y-6">
                    <div class="flex justify-between pb-4 border-b border-gray-200">
                      <dt class="text-sm font-medium text-gray-500">Total Income</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(totalIncome) }}</dd>
                    </div>
                    <div class="flex justify-between pb-4 border-b border-gray-200">
                      <dt class="text-sm font-medium text-gray-500">Less: Total Deductions</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(totalDeductions) }}</dd>
                    </div>
                    <div class="flex justify-between pb-4 border-b border-gray-200">
                    <dt class="text-sm font-medium text-gray-500">Taxable Income</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(taxableIncome) }}</dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Estimated Tax</dt>
                    <dd class="text-sm font-bold text-gray-900">{{ formatCurrency(reportData.estimatedTax) }}</dd>
                  </div>
                  <div class="flex justify-end">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      Effective Tax Rate: {{ effectiveTaxRate }}%
                    </span>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Disclaimer -->
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg mb-6">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <InformationCircleIcon class="h-5 w-5 text-gray-400" />
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-900">Disclaimer</h3>
                    <div class="mt-2 text-sm text-gray-500">
                      <p>This report is for informational purposes only and does not constitute tax advice. Please consult with a tax professional for advice specific to your situation.</p>
                      <p class="mt-1">Generated by SmarTax on {{ formattedCurrentDate }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      <!-- Loading indicator for PDF generation -->
      <div v-if="loading" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full">
          <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm font-medium text-gray-900">Processing Income Summary Report...</p>
          </div>
          <p class="mt-2 text-xs text-gray-500">This may take a few moments. Please don't close this window.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, defineComponent, h } from 'vue';
import AppHeader from '@/components/layout/AppHeader.vue';
import api from '@/services/api';
import html2pdf from 'html2pdf.js';

// Define icons
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

const DownloadIcon = defineComponent({
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
      d: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'
    })
  ])
});

const DocumentReportIcon = defineComponent({
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
      d: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
    })
  ])
});

const InformationCircleIcon = defineComponent({
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
      d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    })
  ])
});

// Component state
const loading = ref(false);
const reportContent = ref<HTMLElement | null>(null);

// Sample report data (replace with API call in production)
const reportData = ref({
  taxYear: '2023',
  user: {
    name: 'John Doe',
    taxId: '123456789',
    email: 'john.doe@example.com',
    phone: '+60 12-345-6789'
  },
  incomes: [
    { source: 'Salary', category: 'Employment', amount: 85000 },
    { source: 'Freelance Work', category: 'Self-Employment', amount: 12000 },
    { source: 'Rental Income', category: 'Property', amount: 24000 },
    { source: 'Dividends', category: 'Investments', amount: 3500 },
    { source: 'Interest', category: 'Investments', amount: 1200 }
  ],
  deductions: [
    { type: 'EPF Contributions', category: 'Retirement', amount: 11000 },
    { type: 'Medical Expenses', category: 'Healthcare', amount: 8000 },
    { type: 'Education Fees', category: 'Education', amount: 7000 },
    { type: 'Zakat', category: 'Religious', amount: 5000 },
    { type: 'Lifestyle', category: 'Personal', amount: 2500 }
  ],
  estimatedTax: 15600
});

const totalIncome = computed(() => reportData.value.totalIncome || 0);
const totalDeductions = computed(() => reportData.value.totalDeductions || 0);
const taxableIncome = computed(() => reportData.value.taxableIncome || 0);

const effectiveTaxRate = computed(() => {
  if (taxableIncome.value === 0) return 0;
  return ((reportData.value.estimatedTax / taxableIncome.value) * 100).toFixed(1);
});

const formattedCurrentDate = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('en-MY', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
});

// Format currency helper function
const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-MY', {
    style: 'currency',
    currency: 'MYR',
    minimumFractionDigits: 0
  }).format(amount);
};

// Calculate percentage helper function
const calculatePercentage = (amount: number, total: number): string => {
  if (total === 0) return '0.0';
  return ((amount / total) * 100).toFixed(1);
};

const fetchReportData = async () => {
  try {
    loading.value = true;
    const response = await api.get('/incomes/summary-report', {
      params: {
        taxYear: new Date().getFullYear().toString()
      }
    });

    console.log('Report Data:', response.data);
    
    if (response.data.success) {
      reportData.value = response.data.data;
    }
  } catch (error) {
    console.error('Failed to fetch report data:', error);
  } finally {
    loading.value = false;
  }
};

const printReport = () => {
  window.print();
};

const downloadPDF = () => {
  if (!reportContent.value) return;
  
  // Set loading state
  loading.value = true;
  
  // Wait a moment to ensure DOM is fully rendered
  setTimeout(() => {
    const element = reportContent.value;
    const opt = {
      margin: [15, 15, 15, 15],
      filename: `Income_Summary_Report_${reportData.value.taxYear}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { 
        scale: 2, 
        useCORS: true,
        logging: false,
        letterRendering: true,
        allowTaint: false,
        scrollY: -window.scrollY,
        windowHeight: document.documentElement.offsetHeight
      },
      jsPDF: { 
        unit: 'mm', 
        format: 'a4', 
        orientation: 'portrait',
        compress: true,
        pagesplit: true
      },
      pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };
    
    // Generate PDF with improved settings
    html2pdf()
      .from(element)
      .set(opt)
      .toPdf()
      .get('pdf')
      .then((pdf) => {
        // Add page numbers
        const totalPages = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
          pdf.setPage(i);
          pdf.setFontSize(8);
          pdf.setTextColor(100);
          pdf.text(
            `Page ${i} of ${totalPages}`, 
            pdf.internal.pageSize.getWidth() - 30, 
            pdf.internal.pageSize.getHeight() - 10
          );
        }
      })
      .save()
      .then(() => {
        loading.value = false;
      });
  }, 500);
};

// Lifecycle hooks
onMounted(async () => {
  await fetchReportData();
});
</script>

<style scoped>
/* PDF-specific styles */
.pdf-content {
  page-break-inside: auto;
}

/* Force page breaks before major sections */
.pdf-content > div {
  page-break-inside: avoid;
  margin-bottom: 20px;
}

/* Ensure tables don't get cut off */
table {
  page-break-inside: avoid;
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9em;
}

/* Adjust card spacing for PDF */
.bg-white.overflow-hidden.shadow.rounded-lg.mb-6 {
  page-break-inside: avoid;
  margin-bottom: 20px;
}

/* Print styles */
@media print {
  body * {
    visibility: hidden;
  }
  
  #app {
    visibility: visible;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  
  .pdf-content, .pdf-content * {
    visibility: visible;
  }
  
  button {
    display: none;
  }
  
  header {
    margin-bottom: 20px;
  }
  
  /* Ensure proper page breaks in print mode */
  .bg-white.overflow-hidden.shadow.rounded-lg.mb-6 {
    break-inside: avoid;
    margin-bottom: 20px;
  }
  
  table {
    break-inside: avoid;
  }
}

/* Ensure tables look good on PDF */
th, td {
  padding: 8px 12px;
}

/* Hover effect for table rows */
.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .grid-cols-4 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
