<script setup lang="ts">
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
const { push }  = useRouter()

import { useNotification } from '@kyvg/vue3-notification'
const { notify }  = useNotification()

const errors = ref({})

const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const name = ref('')
const lastName = ref('')

import LaravelLogo from "@/components/LaravelLogo.vue";
import Input from "@/components/Input.vue";

function register() {
    errors.value = {}
    if(email.value === '' || password.value === '' || confirmPassword.value === '') {
        notify({
            title: 'Preencha todos os campos',
            text: 'Preencha todos os campos corretamente',
        });

        return;
    }

    axios.post('api/auth/register', {
        name: `${name.value} ${lastName.value}`,
        email: email.value,
        password: password.value,
        password_confirmation: confirmPassword.value
    }).then(res => {
        notify({
            title: 'Conta Criada',
            text: 'Sua conta foi criada com sucesso!',
        })
        console.log(res);
        // push({ name: 'home' })
    }).catch(error => {
        notify({
            title: 'Ocorreu um erro',
            text: 'Verifique se os campos foram preenchidos corretamente',
        })

        if (error.response.status === 422) {
            console.log(error.response)
            errors.value = error.response.data.errors
        }
    })
}
</script>

<template>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <LaravelLogo />
        <div class="w-full bg-white rounded-lg shadow border md:mt-0 sm:max-w-md xl:p-0 border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">
                    Criar nova Conta
                </h1>
                <form class="space-y-4 md:space-y-6">
                    <div class="flex justify-between space-x-2">
                        <Input name="name" id="name" label="Seu Nome" v-model="name" :errors="errors?.name" />
                        <Input name="lastname" id="lastname" label="Seu Sobrenome" v-model="lastName"/>
                    </div>
                    <Input type="email" name="email" id="email" label="Seu Email" v-model="email" :errors="errors?.email" />
                    <Input type="password" name="password" id="password" label="Senha" v-model="password" :errors="errors?.password"/>
                    <Input type="password" name="password_confirmation" id="password_confirmation" label="Confirmar Senha" v-model="confirmPassword"/>
                    <button type="submit" @click.prevent="register" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-blue-600 hover:bg-primary-700 focus:ring-primary-800">Criar a Conta</button>
                    <p class="text-sm font-light">
                        Já possui uma conta? <router-link to="/" class="font-medium text-primary-600 hover:underline text-primary-500">Ir para a pagina de Login</router-link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
