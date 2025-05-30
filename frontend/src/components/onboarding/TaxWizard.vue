<template>
    <div class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 flex items-center justify-center sm:p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full h-full sm:h-auto sm:max-w-2xl sm:max-h-[90vh] overflow-hidden transition-all duration-300 transform">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5">
                <div class="flex justify-between items-center">
                    <h2 class="text-white text-lg font-semibold">Profile Setup</h2>
                    <div class="flex items-center">
                        <span class="text-indigo-200 text-sm mr-3">{{ currentStep }}/{{ totalSteps }}</span>
                        <!-- <button v-if="!isFirstStep" @click="skipWizard"
                            class="text-white hover:text-indigo-100 text-sm transition-colors duration-150 flex items-center">
                            <span>Skip for now</span>
                            <ArrowRightIcon class="ml-1 h-4 w-4" />
                        </button> -->
                    </div>
                </div>
            </div>

            <!-- Progress bar -->
            <div class="w-full bg-gray-200 h-1.5">
                <div class="bg-indigo-600 h-1.5 transition-all duration-500 ease-in-out"
                    :style="{ width: `${(currentStep / totalSteps) * 100}%` }"></div>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[calc(100vh-130px)] sm:max-h-[calc(90vh-130px)]">

                <div v-if="currentStep === 1" class="space-y-6 transition-opacity duration-300">
                    <div class="text-center">
                        <div class="bg-indigo-100 rounded-full p-4 inline-flex items-center justify-center mb-4">
                            <DocumentCheckIcon class="h-16 w-16 text-indigo-600" />
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">Welcome to Your Tax Dashboard</h3>
                        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">
                            Let's set up your tax profile to help you maximize your deductions and simplify your tax
                            filing.
                        </p>
                    </div>
                    <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100">
                        <p class="text-sm text-indigo-700">
                            This quick setup will only take about 5 minutes and will help us personalize your tax
                            experience.
                        </p>
                    </div>
                </div>

                <div v-else-if="currentStep === 2" class="space-y-6 transition-opacity duration-300">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                    <p class="text-sm text-gray-500">Please provide your identification details as they appear on official documents.</p>

                    <div class="space-y-4 mt-4">
                        <div class="relative">
                            <label for="nricName" class="block text-sm font-medium text-gray-700 mb-1">NRIC Name</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <UserIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="text" id="nricName" v-model="formData.nricName"
                                    class="pl-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                    placeholder="Your full name as per NRIC" />
                            </div>
                        </div>
                        
                        <div class="relative">
                            <label for="nric" class="block text-sm font-medium text-gray-700 mb-1">NRIC Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <IdentificationIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="text" id="nric" v-model="formData.nric"
                                    class="pl-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                    placeholder="e.g. xxxxxx-xx-xxxx" />
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Your NRIC is kept secure and used only for tax filing purposes.</p>
                        </div>
                        
                        <div class="relative">
                            <label for="tin" class="block text-sm font-medium text-gray-700 mb-1">TIN (Tax Identification Number)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <DocumentTextIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="text" id="tin" v-model="formData.tin"
                                    class="pl-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                    placeholder="Your Tax Identification Number" />
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Required for tax filing and verification purposes.</p>
                        </div>
                        
                        <div class="relative">
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <PhoneIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="tel" id="phoneNumber" v-model="formData.phoneNumber"
                                    class="pl-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                    placeholder="e.g. +65 9123 4567" />
                            </div>
                        </div>
                        
                        <div class="relative">
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <UserIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="number" id="age" v-model="formData.age"
                                    class="pl-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                    placeholder="Your age" min="18" max="120" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Personal and Work Information -->
                <div v-else-if="currentStep === 3" class="space-y-6 transition-opacity duration-300">
                    <h3 class="text-lg font-semibold text-gray-900">Personal and Work Information</h3>
                    <p class="text-sm text-gray-500">Tell us about your work environment to help identify potential
                        deductions.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Do you work from home or in an
                                office?</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div v-for="option in workLocations" :key="option.value"
                                    @click="formData.workLocation = option.value"
                                    class="relative border rounded-lg p-4 cursor-pointer transition-all duration-200"
                                    :class="formData.workLocation === option.value ?
                                        'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-500 ring-opacity-30' :
                                        'border-gray-200 hover:border-indigo-300'">
                                    <input type="radio" :id="option.value" name="workLocation" :value="option.value"
                                        v-model="formData.workLocation" class="sr-only" />
                                    <label :for="option.value"
                                        class="cursor-pointer flex flex-col items-center text-center">
                                        <span v-if="option.value === 'home'" class="text-2xl mb-2">🏠</span>
                                        <span v-else-if="option.value === 'office'" class="text-2xl mb-2">🏢</span>
                                        <span v-else class="text-2xl mb-2">🏠/🏢</span>
                                        <span class="text-sm font-medium text-gray-900">{{ option.label }}</span>
                                    </label>
                                    <div v-if="formData.workLocation === option.value"
                                        class="absolute -top-2 -right-2 bg-indigo-500 rounded-full p-1">
                                        <CheckIcon class="h-4 w-4 text-white" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Travel for Business -->
                <div v-else-if="currentStep === 4" class="space-y-6 transition-opacity duration-300">
                    <h3 class="text-lg font-semibold text-gray-900">Travel for Business</h3>
                    <p class="text-sm text-gray-500">Do you travel for business purposes? Select all that apply.</p>

                    <div class="space-y-3">
                        <div v-for="option in travelOptions" :key="option.value"
                            @click="formData.businessTravel = option.value"
                            class="relative border rounded-lg p-4 cursor-pointer transition-all duration-200 flex items-center"
                            :class="formData.businessTravel === option.value ?
                                'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-500 ring-opacity-30' :
                                'border-gray-200 hover:border-indigo-300'">
                            <input type="radio" :id="option.value" name="businessTravel" :value="option.value"
                                v-model="formData.businessTravel" class="sr-only" />

                            <div class="mr-3">
                                <span v-if="option.value === 'personal_vehicle'" class="text-xl">🚗</span>
                                <span v-else-if="option.value === 'rideshare'" class="text-xl">🚕</span>
                                <span v-else-if="option.value === 'flights'" class="text-xl">✈️</span>
                                <span v-else class="text-xl">🚫</span>
                            </div>

                            <label :for="option.value" class="cursor-pointer flex-1">
                                <span class="text-sm font-medium text-gray-900">{{ option.label }}</span>
                            </label>

                            <div v-if="formData.businessTravel === option.value"
                                class="bg-indigo-500 rounded-full h-5 w-5 flex items-center justify-center">
                                <CheckIcon class="h-3 w-3 text-white" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Business Meals -->
                <div v-else-if="currentStep === 5" class="space-y-6 transition-opacity duration-300">
                    <h3 class="text-lg font-semibold text-gray-900">Business Meals</h3>
                    <p class="text-sm text-gray-500">How often do you have business meals?</p>

                    <div class="space-y-3">
                        <div v-for="option in mealFrequencyOptions" :key="option.value"
                            @click="formData.businessMeals = option.value"
                            class="relative border rounded-lg p-4 cursor-pointer transition-all duration-200 flex items-center"
                            :class="formData.businessMeals === option.value ?
                                'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-500 ring-opacity-30' :
                                'border-gray-200 hover:border-indigo-300'">
                            <input type="radio" :id="option.value" name="businessMeals" :value="option.value"
                                v-model="formData.businessMeals" class="sr-only" />

                            <div class="mr-3">
                                <span v-if="option.value === 'mostly'" class="text-xl">🍽️</span>
                                <span v-else-if="option.value === 'sometimes'" class="text-xl">🍲</span>
                                <span v-else-if="option.value === 'rarely'" class="text-xl">🥪</span>
                                <span v-else class="text-xl">❌</span>
                            </div>

                            <label :for="option.value" class="cursor-pointer flex-1">
                                <span class="text-sm font-medium text-gray-900">{{ option.label }}</span>
                            </label>

                            <div v-if="formData.businessMeals === option.value"
                                class="bg-indigo-500 rounded-full h-5 w-5 flex items-center justify-center">
                                <CheckIcon class="h-3 w-3 text-white" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Personal Deductions -->
                <div v-else-if="currentStep === 6" class="space-y-6 transition-opacity duration-300">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Deductions</h3>
                    <p class="text-sm text-gray-500">Which of these personal deductions apply to you? Select all that
                        apply.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div v-for="option in personalDeductionOptions" :key="option.value"
                            @click="toggleDeduction(option.value)"
                            class="relative border rounded-lg p-3
                                                        cursor-pointer transition-all duration-200 flex items-center"
                            :class="formData.personalDeductions.includes(option.value) ?
                                'bg-indigo-50 border-indigo-500 ring-1 ring-indigo-500' :
                                'border-gray-200 hover:border-indigo-300'">
                            <input type="checkbox" :id="option.value" :value="option.value"
                                v-model="formData.personalDeductions" class="sr-only" />

                            <label :for="option.value" class="cursor-pointer flex-1">
                                <span class="text-sm font-medium text-gray-900">{{ option.label }}</span>
                            </label>

                            <div v-if="formData.personalDeductions.includes(option.value)"
                                class="bg-indigo-500 rounded-full h-5 w-5 flex items-center justify-center">
                                <CheckIcon class="h-3 w-3 text-white" />
                            </div>
                            <div v-else class="border border-gray-300 rounded-full h-5 w-5"></div>
                        </div>
                    </div>
                </div>

                <!-- Step 7: Profession -->
                <div v-else-if="currentStep === 7" class="space-y-6 transition-opacity duration-300">
                    <h3 class="text-lg font-semibold text-gray-900">Your Profession</h3>
                    <p class="text-sm text-gray-500">What is your profession? This helps us identify industry-specific
                        deductions.</p>

                    <div class="mt-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <BriefcaseIcon class="h-5 w-5 text-gray-400" />
                            </div>
                            <input type="text" v-model="formData.profession"
                                class="pl-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                placeholder="e.g. Software Developer, Graphic Designer, Consultant" />
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button v-for="suggestion in professionSuggestions" :key="suggestion"
                                @click="formData.profession = suggestion"
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-indigo-100 transition-colors duration-150">
                                {{ suggestion }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 8: Completion -->
                <div v-else-if="currentStep === 8" class="space-y-6 text-center transition-opacity duration-300">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 animate-pulse">
                        <CheckIcon class="h-10 w-10 text-green-600" />
                    </div>

                    <h3 class="text-xl font-semibold text-gray-900">All Set!</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto">
                        Thank you for completing your tax profile. We've personalized your dashboard based on your
                        information.
                    </p>

                    <div
                        class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 rounded-xl border border-indigo-200 text-left mt-6">
                        <h4 class="text-sm font-medium text-indigo-800 flex items-center">
                            <LightbulbIcon class="h-5 w-5 mr-2 text-indigo-500" />
                            What's Next?
                        </h4>
                        <ul class="mt-3 text-sm text-indigo-700 space-y-3">
                            <li class="flex items-center bg-white bg-opacity-60 p-2 rounded-lg">
                                <CheckIcon class="h-4 w-4 mr-2 text-green-500 flex-shrink-0" />
                                <span>We'll analyze your information to identify potential deductions</span>
                            </li>
                            <li class="flex items-center bg-white bg-opacity-60 p-2 rounded-lg">
                                <CheckIcon class="h-4 w-4 mr-2 text-green-500 flex-shrink-0" />
                                <span>Your dashboard will show personalized tax insights</span>
                            </li>
                            <li class="flex items-center bg-white bg-opacity-60 p-2 rounded-lg">
                                <CheckIcon class="h-4 w-4 mr-2 text-green-500 flex-shrink-0" />
                                <span>You can update your information anytime in your profile settings</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <button @click="completeWizard"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                            Go to Dashboard
                            <ArrowRightIcon class="ml-2 h-5 w-5" />
                        </button>
                    </div>
                </div>

                <!-- Loading state -->
                <div v-else-if="isLoading" class="py-12 flex flex-col items-center justify-center">
                    <div class="relative w-20 h-20">
                        <svg class="animate-spin h-20 w-20 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <SparklesIcon class="h-8 w-8 text-indigo-200" />
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-4 animate-pulse">{{ loadingMessage }}</p>
                </div>
            </div>

            <!-- Footer with navigation buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <button v-if="currentStep > 1 && !isLoading" @click="prevStep"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150 flex items-center">
                    <ArrowLeftIcon class="mr-2 h-4 w-4" />
                    Back
                </button>
                <div v-else></div>

                <button v-if="currentStep < totalSteps && !isLoading" @click="nextStep"
                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 transform hover:scale-105 flex items-center">
                    {{ currentStep === 1 ? 'Get Started' : 'Continue' }}
                    <ArrowRightIcon class="ml-2 h-4 w-4" />
                </button>
                <!-- <button v-else-if="currentStep === totalSteps && !isLoading" @click="completeWizard"
                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 transform hover:scale-105 flex items-center">
                    Complete Setup
                    <CheckIcon class="ml-2 h-4 w-4" />
                </button> -->
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, defineEmits, watch } from 'vue';
import { h, defineComponent } from 'vue';
import api from '@/services/api';

const DocumentCheckIcon = defineComponent({
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
            d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
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

const LinkIcon = defineComponent({
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
            d: 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'
        })
    ])
});

const MailIcon = defineComponent({
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
            d: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
        })
    ])
});

const UserIcon = defineComponent({
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
            d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
        })
    ])
});

const BriefcaseIcon = defineComponent({
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
            d: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
        })
    ])
});

const SparklesIcon = defineComponent({
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
            d: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L             13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'
        })
    ])
});

const LightbulbIcon = defineComponent({
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

const ArrowLeftIcon = defineComponent({
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
            d: 'M10 19l-7-7m0 0l7-7m-7 7h18'
        })
    ])
});

const emit = defineEmits(['complete', 'skip','close']);

// State
const currentStep = ref(1);
const totalSteps = 8; // Reduced from 10 to 8 after removing 2 steps
const isLoading = ref(false);
const loadingMessage = ref('Processing your information...');
const animateStep = ref(true);

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

// Form data
const formData = ref({
    workLocation: '',
    businessTravel: '',
    businessMeals: '',
    personalDeductions: [] as string[],
    profession: '',
    nricName: '',
    nric: '',
    tin: '',           // TIN field
    age: '',    
    phoneNumber: '',
    statementMethod: 'connect'
});

// Options for form selections
const workLocations = [
    { value: 'home', label: 'I work from home' },
    { value: 'office', label: 'I work in an office' },
    { value: 'hybrid', label: 'I have a hybrid arrangement' }
];

const travelOptions = [
    { value: 'personal_vehicle', label: 'Personal Vehicle' },
    { value: 'rideshare', label: 'Rideshare or public transport' },
    { value: 'flights', label: 'Flights' },
    { value: 'no_travel', label: 'No, I don\'t travel for business' }
];

const mealFrequencyOptions = [
    { value: 'mostly', label: 'Mostly (5+ per month)' },
    { value: 'sometimes', label: 'Sometimes (2-4 per month)' },
    { value: 'rarely', label: 'Rarely (Once a month)' },
    { value: 'never', label: 'Never' }
];

const personalDeductionOptions = [
    { value: 'rent', label: 'Rent' },
    { value: 'utilities', label: 'Utilities' },
    { value: 'medical', label: 'Medical' },
    { value: 'charitable', label: 'Charitable Contributions' },
    { value: 'education', label: 'Education Expenses' },
    { value: 'student_loans', label: 'Student Loans' },
    { value: 'child_care', label: 'Child Care' },
    { value: 'mortgage', label: 'Mortgage' },
    { value: 'none', label: 'None of the above' }
];

// Add these new icons to your imports
const IdentificationIcon = defineComponent({
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
            d: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2'
        })
    ])
});

const PhoneIcon = defineComponent({
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
            d: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'
        })
    ])
});

// Profession suggestions
const professionSuggestions = [
    'Software Developer',
    'Graphic Designer',
    'Freelance Writer',
    'Marketing Consultant',
    'Web Developer',
    'Photographer',
    'Accountant',
    'Teacher'
];

// Computed properties
const isFirstStep = computed(() => currentStep.value === 1);
const isLastStep = computed(() => currentStep.value === totalSteps);

// Methods
const nextStep = () => {
    if (currentStep.value < totalSteps) {
        // Animate step transition
        animateStep.value = false;
        setTimeout(() => {
            currentStep.value++;
            animateStep.value = true;
        }, 150);
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        // Animate step transition
        animateStep.value = false;
        setTimeout(() => {
            currentStep.value--;
            animateStep.value = true;
        }, 150);
    }
};

const simulateLoading = (message: string, duration: number) => {
    isLoading.value = true;
    loadingMessage.value = message;

    setTimeout(() => {
        isLoading.value = false;
        currentStep.value++;
        animateStep.value = true;
    }, duration);
};

const completeWizard = async () => {
  console.log('Complete Form Data Object:', formData.value);
  
  isLoading.value = true;
  loadingMessage.value = 'Submitting your information...';
  
  try {
    const apiFormData = new FormData();
    Object.entries(formData.value).forEach(([key, value]) => {
      if (Array.isArray(value)) {
        apiFormData.append(key, JSON.stringify(value));
      } else {
        apiFormData.append(key, value as string);
      }
    });

    const response = await api.post('/user-preference', apiFormData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    console.log('API Response:', response.data);
    console.log('Emitting complete and close events');
    
    loadingMessage.value = 'Finalizing your tax profile...';
    isLoading.value = false;
    
    // Emit both events
    emit('complete', formData.value);
    emit('close');
    
  } catch (error) {
    console.error('Error submitting form data:', error);
    loadingMessage.value = 'There was an error processing your information. Please try again.';
    isLoading.value = false;
  }
};


const skipWizard = () => {
    emit('skip');
};

// Toggle personal deduction selection
const toggleDeduction = (value: string) => {
    const index = formData.value.personalDeductions.indexOf(value);

    // If "None of the above" is selected, clear other selections
    if (value === 'none') {
        if (index === -1) {
            formData.value.personalDeductions = ['none'];
        } else {
            formData.value.personalDeductions = [];
        }
        return;
    }

    // If selecting another option while "None" is selected, remove "None"
    if (formData.value.personalDeductions.includes('none')) {
        formData.value.personalDeductions = [];
    }

    // Toggle the selected value
    if (index === -1) {
        formData.value.personalDeductions.push(value);
    } else {
        formData.value.personalDeductions.splice(index, 1);
    }
};

// Watch for changes to animate transitions
watch(currentStep, () => {
    animateStep.value = true;
});
</script>

<style scoped>
.transition-opacity {
    transition: opacity 0.3s ease-in-out;
    opacity: v-bind(animateStep ? 1 : 0);
}
</style>


