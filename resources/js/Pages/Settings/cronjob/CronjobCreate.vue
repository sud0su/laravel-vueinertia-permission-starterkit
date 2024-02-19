<script setup>
import SettingLayout from '@/Layouts/SettingLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { cronopts } from '@/Composables/options';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import VueMultiselect from 'vue-multiselect';

defineProps({
    hospitals: Array,
})

const form = useForm({
    crontitle: '',
    endpoint: '',
    rsklien_id: [],
    // croncat: '',
    // day: '',
    // hour: '',
    // minute: '',
});

</script>

<template>
    <Head title="Create New Role" />

    <SettingLayout>
        <div class=" max-w-7xl mx-auto py-4">
            <div class="flex justify-between">
                <Link :href="route('crons.index')"
                    class="px-3 py-2 text-white font-semibold bg-indigo-500 hover:bg-indigo-700 rounded">
                Back
                </Link>
            </div>
            <div class="mt-6 max-w-6xl mx-auto bg-slate-100 shadow-lg rounded-lg p-6">

                <form @submit.prevent="form.post(route('crons.store'))">
                    <div>
                        <InputLabel for="crontitle" value="Task Name" />

                        <VueMultiselect v-model="form.crontitle" :options="cronopts" :multiple="false"
                            :close-on-select="true" placeholder="Pick some" />

                    </div>

                    <div class="mt-4">
                        <InputLabel for="hospitals" value="RS / Klinik" />

                        <VueMultiselect v-model="form.rsklien_id" :options="hospitals"
                            :close-on-select="true" placeholder="Pick some" label="clientname" track-by="id" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="endpoint" value="URL Endpoint" />

                        <TextInput id="endpoint" type="text" class="mt-1 block w-full" v-model="form.endpoint" autofocus
                            autocomplete="endpoint" />

                        <InputError class="mt-2" :message="form.errors.endpoint" />
                    </div>

                    <div class="flex items-center justify-end mt-4">

                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Create
                        </PrimaryButton>
                    </div>
                </form>

            </div>
        </div>
    </SettingLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
