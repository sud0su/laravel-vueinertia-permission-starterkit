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

defineProps(['hospitals'])
const form = useForm({})

const showConfirmDeleteModal = ref(false);

const confirmDelete = () => showConfirmDeleteModal.value = true;

const closeModal = () => showConfirmDeleteModal.value = false;

const deleteHospital = (id) => {
    form.delete(route('hospitals.destroy', id),{
        onSuccess: () => closeModal()
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <SettingLayout>
        <div class=" max-w-7xl mx-auto py-4">
            <div class="flex justify-between">
                <h1>Hospital Index Page</h1>
                <Link :href="route('hospitals.create')" class="px-3 py-2 text-white font-semibold bg-indigo-500 hover:bg-indigo-700 rounded">
                    New Hospital
                </Link>
            </div>
            <div class="mt-6">
                <Table>
                    <template #header>
                        <TableRow>
                            <TableHeaderCell>ID</TableHeaderCell>
                            <TableHeaderCell>RS / Klinik</TableHeaderCell>
                            <TableHeaderCell>Address</TableHeaderCell>
                            <TableHeaderCell>Organization ID</TableHeaderCell>
                            <TableHeaderCell>Client ID</TableHeaderCell>
                            <TableHeaderCell>Client Secret</TableHeaderCell>
                            <TableHeaderCell>Action</TableHeaderCell>
                        </TableRow>
                    </template>
                    <template #default>
                        <TableRow v-for="hospital in hospitals" :key="hospital.id">
                            <TableDataCell>{{ hospital.id }}</TableDataCell>
                            <TableDataCell>{{ hospital.clientname }}</TableDataCell>
                            <TableDataCell>{{ hospital.address }}</TableDataCell>
                            <TableDataCell>{{ hospital.orgid_prod }}</TableDataCell>
                            <TableDataCell>{{ hospital.clientid_prod }}</TableDataCell>
                            <TableDataCell>{{ hospital.clientsecret_prod }}</TableDataCell>
                            <TableDataCell>
                                <Link :href="route('hospitals.edit', hospital.id)" class="text-green-400 hover:text-green-600">
                                    Edit
                                </Link>
                                /

                                <button @click="confirmDelete" class="text-red-400 hover:text-red-600">Delete</button>
                                <Modal :show="showConfirmDeleteModal" @close="closeModal">
                                    <div class="p-6">
                                        <h2 class="text-lg font-semibold text-slate-800">Are you sure to delete this hospital?</h2>
                                        <div class="mt-6 flex space-x-4">
                                            <DangerButton @click="$event => deleteHospital(hospital.id)">Delete</DangerButton>
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
