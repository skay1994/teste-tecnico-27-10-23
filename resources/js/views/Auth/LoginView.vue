<script setup lang="ts">
import axios from 'axios'
import { ref } from 'vue'
import  { setItem } from "@/helpers/localStorage"
import LaravelLogo from "@/components/LaravelLogo.vue";
import Input from "@/components/Input.vue";
import Spinner from "@/components/Spinner.vue";

import { RouterLink, useRouter } from "vue-router";
const router  = useRouter()

import { useNotification } from "@kyvg/vue3-notification";
const { notify }  = useNotification()

const errors = ref({})
const email = ref('')
const password = ref('')
const isLoading = ref(false)

function login() {
    errors.value = {}
    if(email.value === '' || password.value === '') {
        notify({
            title: 'Preencha todos os campos',
            text: 'Preencha todos os campos corretamente',
        });

        return;
    }

    isLoading.value = true;

    axios.post('/api/auth/login', {
        email: email.value,
        password: password.value
    }).then(res => {
        setItem('token', res.data.token)
        router.replace({ name: 'home' })
        notify({
            title: 'Login Efetuado',
            text: 'Login efetuado com sucesso',
        })
    }).catch(() => {
        notify({
            title: 'Credenciais Incorretas',
            text: 'As credenciais fornecidas são inválidas',
        })
    }).finally(() => isLoading.value = false)
}
</script>

<template>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <LaravelLogo/>
        <Spinner v-if="isLoading" />
        <div class="w-full bg-white rounded-lg shadow border md:mt-0 sm:max-w-md xl:p-0 border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">
                    Faça Login
                </h1>
                <form class="space-y-4 md:space-y-6" action="#">
                    <Input type="email" v-model="email" :errors="errors?.email" label="Email" name="email" id="email" placeholder="name@company.com"/>
                    <Input type="password" v-model="password" label="Senha" name="password" id="password" placeholder="••••••••"/>
                    <div class="flex grid-cols-1 gap-4">
                        <button type="submit" @click.prevent="login"
                                class="p-8 text-white bg-blue-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-primary-600 hover:bg-primary-700 focus:ring-primary-800">
                            Entrar
                        </button>
                        <router-link to="/register">
                            <button type="button"
                                    class="w-full text-white bg-blue-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-primary-600 hover:bg-primary-700 focus:ring-primary-800">
                                Fazer Registro
                            </button>
                        </router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
