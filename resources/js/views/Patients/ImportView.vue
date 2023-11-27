<script setup lang="ts">
import { ref } from "vue";
import { useNotification } from "@kyvg/vue3-notification";
import axios from "../../helpers/axios";
import Spinner from "../../components/Spinner.vue";

const isLoading = ref(false)

const { notify }  = useNotification()

function handleFileChange(e) {
    const file = e .target.files[0]
    const formData = new FormData();
    formData.append('file', file)

    axios.post('/api/patients/import-csv', formData)
        .then(() => {
            notify({
                title: 'Importação iniciada com sucesso',
                text: 'Arquivo está sendo importado em segundo plano.',
            })
        })
}
</script>

<template>
    <Spinner v-if="isLoading" />
    <div class="container p-6">
        <div class="col-span-full">
            <div class="col-span-full">
                <label for="photo" class="block text-sm font-medium leading-6 text-gray-900">Arquivo</label>
                <div class="mt-2 flex items-center gap-x-3">
                    <input
                        @change="handleFileChange"
                        type="file"
                        accept="text/csv"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400"
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
