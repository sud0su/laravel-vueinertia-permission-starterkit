<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Nama RS/Klinik',
    },
    address: {
        type: String,
        default: 'Alamat',
    },
    services: {
        type: Number,
        default: 0,
    },
    minHeght: {
        type: Number,
        default: 3,
    },
});


const minHeightClass = computed(() => {
    return {
        3: 'min-h-3',
        28: 'min-h-28',
        60: 'min-h-60',
        64: 'min-h-64',
        72: 'min-h-72',
    }[props.minHeght];
});

</script>

<template>
    <div class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700 relative"
        :class="minHeightClass">
        <h5 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">
            {{ title }}
        </h5>
        <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">
            {{ address }}
        </p>
        <div class="items-bottom justify-center space-y-4 sm:flex sm:space-y-0 sm:space-x-4 rtl:space-x-reverse absolute bottom-0 left-1/2 transform -translate-x-1/2 pb-3">
            <a href="#"
                class="w-full sm:w-auto focus:ring-4 focus:outline-none focus:ring-gray-300 text-white rounded-lg inline-flex items-center justify-center px-4 py-2.5"
                :class="services > 0 ? 'bg-green-800 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 dark:focus:ring-green-700' : 'bg-red-800 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 dark:focus:ring-red-700'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h7.5a2.25 2.25 0 0 0 2.25-2.25v-7.5a2.25 2.25 0 0 0-2.25-2.25h-.75m0-3-3-3m0 0-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 0 1 2.25 2.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25v-.75" />
                </svg>

                <div class="text-left rtl:text-right">
                    <div class="mb-1 text-xs">Services</div>
                    <div class="text-center font-sans text-sm font-semibold">{{ services }}</div>
                </div>
            </a>
        </div>
    </div>
</template>
