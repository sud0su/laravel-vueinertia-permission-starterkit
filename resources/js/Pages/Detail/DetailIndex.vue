<script setup>
import DetailLayout from '@/Layouts/DetailLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps(['hospital'])
</script>

<template>
    <Head title="Detail" />

    <DetailLayout :title="hospital.clientname" :menus="hospital.crontasks" :id="hospital.id">
        <div class="py-4">
            <h1>Selamat Datang</h1>
        </div>
    </DetailLayout>
</template>
