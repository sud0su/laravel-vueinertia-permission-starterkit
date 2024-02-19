<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';
import { usePermission } from '@/Composables/permissions';
const { hasRole } = usePermission();

defineProps(['token', 'clients'])
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :token="token">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        You're logged in!
                        <hr class="mt-2 pb-2" />
                        token :
                        <b>{{ token }}</b>
                        <b v-if="token === null">Authentication Token tidak ditemukan, silahkan logout dan login kembali</b>
                    </div>
                </div>
                <div class="mt-6">
                    <div v-if="hasRole('admin') && token !== null && token !== ''"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8">
                        <div v-for="client in clients" :id="client.id">
                            <Card :address="client.address" :title="client.clientname" :minHeght="64" :services="client.crontask.length"/>
                        </div>
                    </div>
                    <div v-else class="w-full align-center text-center font-semibold">
                        Authentication Token tidak ditemukan, silahkan logout dan login kembali
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
