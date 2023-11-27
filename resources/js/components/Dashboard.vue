<script setup lang="ts">
import { ref } from "vue";
import { useRouter, useRoute } from 'vue-router'
import { useNotification } from "@kyvg/vue3-notification";
import axios from "@/helpers/axios";
import Spinner from "./Spinner.vue";
import LaravelLogo from "./LaravelLogo.vue";

const { notify }  = useNotification()

const isLoading = ref(false)

const router = useRouter()
const route = useRoute()

async function logout() {
    isLoading.value = true
    await  axios.get('/api/auth/logout')
        .then(() => {
            router.replace({ name: 'login' })
            notify({
                title: 'Logout efetuado',
                text: 'Logout efetuado com sucesso',
            })
            isLoading.value = false
        })
}
</script>

<template>
    <Spinner v-if="isLoading" />
    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-4">
            <LaravelLogo size="h-12"/>
            <div class="flex items-center space-x-6 rtl:space-x-reverse">
                <span v-on:click="logout"
                      class="text-sm text-blue-600 dark:text-blue-500 hover:underline" style="cursor: pointer">
                    Fazer Logout
                </span>
            </div>
        </div>
    </nav>
    <nav class="bg-gray-50 dark:bg-gray-700">
        <div class="max-w-screen-xl px-4 py-3 mx-auto">
            <div class="flex items-center">
                <ul class="flex flex-row font-medium mt-0 space-x-8 rtl:space-x-reverse text-sm">
                    <li :class="{ 'bg-blue-300': route.name === 'home'}" class="p-2">
                        <router-link :to="{ name: 'home' }" class="dark:text-white hover:underline text-blue-900 "
                                     aria-current="page">Home
                        </router-link>
                    </li>
                    <li :class="{ 'bg-blue-300': route.name === 'patients'}" class="p-1">
                        <router-link :to="{ name: 'patients'}" class="text-gray-900 dark:text-white hover:underline">
                            Pacientes
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-1 h-screen bg-gray-100 p-6">
        <router-view/>
    </main>
</template>
