<script setup>
import SettingLayout from '@/Layouts/SettingLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import Table from '@/Components/Table.vue';
import TableRow from '@/Components/TableRow.vue';
import TableHeaderCell from '@/Components/TableHeaderCell.vue';
import TableDataCell from '@/Components/TableDataCell.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps(['crons'])
const form = useForm({})

const showConfirmDeleteModal = ref(false);

const confirmDelete = () => showConfirmDeleteModal.value = true;

const closeModal = () => showConfirmDeleteModal.value = false;

const deleteCron = (id) => {
    form.delete(route('crons.destroy', id),{
        onSuccess: () => closeModal()
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <SettingLayout>
        <div class=" max-w-7xl mx-auto py-4">
            <div class="flex justify-between">
                <h1>Crons Index Page</h1>
                <Link :href="route('crons.create')" class="px-3 py-2 text-white font-semibold bg-indigo-500 hover:bg-indigo-700 rounded">
                    New CronJob
                </Link>
            </div>
            <div class="mt-6">
                <Table>
                    <template #header>
                        <TableRow>
                            <TableHeaderCell>ID</TableHeaderCell>
                            <TableHeaderCell>RS / Klinik</TableHeaderCell>
                            <TableHeaderCell>Task Name</TableHeaderCell>
                            <TableHeaderCell>Url EndPoint</TableHeaderCell>
                            <TableHeaderCell>Cron Kategori</TableHeaderCell>
                            <TableHeaderCell>Time</TableHeaderCell>
                            <TableHeaderCell>Action</TableHeaderCell>
                        </TableRow>
                    </template>
                    <template #default>
                        <TableRow v-for="cron in crons" :key="cron.id">
                            <TableDataCell>{{ cron.id }}</TableDataCell>
                            <TableDataCell>{{ cron.clientname }}</TableDataCell>
                            <TableDataCell>{{ cron.crontitle }}</TableDataCell>
                            <TableDataCell>{{ cron.endpoint }}</TableDataCell>
                            <TableDataCell>{{ cron.croncat }}</TableDataCell>
                            <TableDataCell>{{ cron.execution }}</TableDataCell>
                            <TableDataCell>
                                <Link :href="route('crons.edit', cron.id)" class="text-green-400 hover:text-green-600">
                                    Edit
                                </Link>
                                /

                                <button @click="confirmDelete" class="text-red-400 hover:text-red-600">Delete</button>
                                <Modal :show="showConfirmDeleteModal" @close="closeModal">
                                    <div class="p-6">
                                        <h2 class="text-lg font-semibold text-slate-800">Are you sure to delete this cron task?</h2>
                                        <div class="mt-6 flex space-x-4">
                                            <DangerButton @click="$event => deleteCron(cron.id)">Delete</DangerButton>
                                            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                                        </div>
                                    </div>
                                </Modal>
                            </TableDataCell>
                        </TableRow>
                    </template>
                </Table>
            </div>
        </div>
    </SettingLayout>
</template>
