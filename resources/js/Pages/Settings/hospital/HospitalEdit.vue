<script setup>
import SettingLayout from '@/Layouts/SettingLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    hospital: {
        type: Object,
        required: true,
    }
})

const form = useForm({
    clientname: props.hospital.clientname,
    address: props.hospital.address,
    orgid_prod: props.hospital.orgid_prod,
    clientid_prod: props.hospital.clientid_prod,
    clientsecret_prod: props.hospital.clientsecret_prod,
});
</script>

<template>
    <Head title="Update Hospital" />

    <SettingLayout>
        <div class=" max-w-7xl mx-auto py-4">
            <div class="flex justify-between">
                <Link :href="route('hospitals.index')"
                    class="px-3 py-2 text-white font-semibold bg-indigo-500 hover:bg-indigo-700 rounded">
                Back
                </Link>
            </div>
            <div class="mt-6 max-w-6xl mx-auto bg-slate-100 shadow-lg rounded-lg p-6">

                <form @submit.prevent="form.put(route('hospitals.update', hospital.id))">
                    <div class="mt-4">
                        <InputLabel for="clientname" value="Clientname" />

                        <TextInput id="clientname" type="text" class="mt-1 block w-full" v-model="form.clientname" autofocus
                            autocomplete="clientname" />

                        <InputError class="mt-2" :message="form.errors.clientname" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="address" value="Address" />

                        <TextInput id="address" type="text" class="mt-1 block w-full" v-model="form.address" autofocus
                            autocomplete="address" />

                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="orgid_prod" value="Organization ID" />

                        <TextInput id="orgid_prod" type="text" class="mt-1 block w-full" v-model="form.orgid_prod" autofocus
                            autocomplete="orgid_prod" />

                        <InputError class="mt-2" :message="form.errors.orgid_prod" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="clientid_prod" value="Client ID Production" />

                        <TextInput id="clientid_prod" type="text" class="mt-1 block w-full" v-model="form.clientid_prod" autofocus
                            autocomplete="clientid_prod" />

                        <InputError class="mt-2" :message="form.errors.clientid_prod" />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="clientsecret_prod" value="Client Secret Production" />

                        <TextInput id="clientsecret_prod" type="text" class="mt-1 block w-full" v-model="form.clientsecret_prod" autofocus
                            autocomplete="clientsecret_prod" />

                        <InputError class="mt-2" :message="form.errors.clientsecret_prod" />
                    </div>

                    <div class="flex items-center justify-end mt-4">

                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Update
                        </PrimaryButton>
                    </div>
                </form>

            </div>
        </div>
    </SettingLayout>
</template>
