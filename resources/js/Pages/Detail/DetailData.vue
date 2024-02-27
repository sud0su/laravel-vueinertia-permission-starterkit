<script setup>
import DetailLayout from '@/Layouts/DetailLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Progressbar from '@/Components/Progressbar.vue';

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-responsive';
import 'datatables.net-select';


defineProps(['hospital', 'crons'])

DataTable.use(DataTablesCore);

const columns = [
    { data: 'index' },
    { data: 'id' },
    { data: 'identifier' },
    { data: 'resourceType' },
    { data: 'flag_text' },
    { data: 'created_at' },
    { data: 'updated_at' },
    { data: 'action' },
];

const options = {
    select: false,
    serverSide: true,
    deferLoading: 50,
    responsive: true,
    processing: true,
};

const ajaxRequest = (rsid, cronId, resourceType, endpoint) => {
    return {
        url: '/satusehat/datatablejson',
        type: 'GET',
        data: {
            'id': rsid,
            'resourceType': resourceType,
            'cronid': cronId,
            'endpoint': endpoint,
        }
    };
};

window.viewJson = (id, service) => {
    const url = `/satusehat/json/${id}/${service}`;
    getJsonPatient(url);
}

const getJsonPatient = (url) => {
    fetch(url, {
        method: 'GET',
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to retrieve data');
            }
            return response.json();
        })
        .then(data => {
            const jsonString = JSON.stringify(data, null, 2);
            showPreviewModal.value = true;
            jsonPreview.value = jsonString;
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Failed to retrieve data. Please check the console for details.');
        });
};

const showPreviewModal = ref(false);
const jsonPreview = ref('');
const closeModal = () => {
    showPreviewModal.value = false;
    jsonPreview.value = '';
};


const showLoading = ref(false);
const countProgress = ref(0);
const sync = (cronid, service, rsid) => {
    showLoading.value = true;
    const url = `/satusehat/sync/${cronid}/${service}/${rsid}`;
    syncData(url);
}

const syncData = (url) => {
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            data.forEach(item => {
                const { status, message } = item;
                if (status === 'success') {
                    toastr.success(message);
                } else if (status === 'error') {
                    toastr.error(message);
                }

                refreshDatatable(tableId, tabId, id, endpoint, clientid_prod);
            });
            showLoading.value = false;
        })
        .catch(error => {
            showLoading.value = false;
            console.error('Error:', error);
        });
}

</script>

<template>
    <Head title="Detail" />

    <DetailLayout :title="hospital.clientname" :menus="hospital.crontasks" :id="hospital.id">
        <div class="py-4">
            <h1>{{ crons.endpoint }}?key={{ hospital.clientid_prod }}</h1>
            <hr class="mb-5 mt-2" />

            <div class="w-full flex justify-between mb-2">
                <PrimaryButton :active="showLoading" @click="sync(crons.id, crons.crontitle, hospital.id)">Synchronize
                </PrimaryButton>
                <SecondaryButton @click="sync">Log Data</SecondaryButton>
            </div>

            <div class="relative">
                <div v-if="showLoading" :class="showLoading && 'absolute inset-0 z-20'">
                    <div class="absolute rounded-md inset-0 bg-black opacity-50"></div>
                    <div :class="showLoading && 'z-50 relative flex justify-center items-center h-full'">
                        <Progressbar :progress="countProgress.value" />
                    </div>
                </div>

                <DataTable :columns="columns" :options="options"
                    :ajax="ajaxRequest(hospital.id, crons.id, crons.crontitle, crons.endpoint + `?key=` + hospital.clientid_prod)"
                    class="display nowarp w-full" width="100%">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Identifier</th>
                            <th class="px-4 py-2">Resource Type</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Created At</th>
                            <th class="px-4 py-2">Updated At</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                </DataTable>
            </div>
            <Modal :show="showPreviewModal" @close="closeModal">
                <div class="p-6">
                    <div class="text-sm text-slate-800 max-h-[80vh] overflow-auto">
                        <pre>{{ jsonPreview }}</pre>
                    </div>
                    <div class="mt-6 flex space-x-4">
                        <SecondaryButton @click="closeModal">Close</SecondaryButton>
                    </div>
                </div>
            </Modal>
        </div>
    </DetailLayout>
</template>


<style>
@import 'datatables.net-dt';
@import 'datatables.net-responsive-dt';
@import 'datatables.net-select-dt';

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 12px;
    color: #ffffff;
    font-weight: 600;
}

.bg-warning {
    background-color: #d8db16;
}

.bg-success {
    background-color: #28a745;
}

.bg-danger {
    background-color: #890d1c;
}

.bg-info {
    background-color: #17a2b8;
}

.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

.btn-xs {
    padding: .25rem .5rem;
    font-size: .75rem;
}

.btn-default {
    color: #495057;
    background-color: #ffffff;
    border-color: #ced4da;
}

.text-teal {
    color: #008080;
    /* atau warna teal yang Anda inginkan */
}

.mx-1 {
    margin-right: .25rem;
    margin-left: .25rem;
}

.shadow {
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
}

pre {
    background-color: ghostwhite;
    border: 1px solid silver;
    padding: 10px 20px;
    margin: 20px;
    border-radius: 4px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}
</style>
