<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { vMaska } from "maska"
import { useNotification } from "@kyvg/vue3-notification";

import Input from '@/components/Input.vue';
import Spinner from '@/components/Spinner.vue';
import axios from "../../helpers/axios";

const { notify }  = useNotification()
const router = useRouter();

const files = ref()

const isLoading = ref(false)

const errors = ref(null)

const name = ref('')
const motherName = ref('')
const document = ref('')
const cns = ref('')
const birthdate = ref('')
const phone = ref('')

const zipcode = ref('')
const street = ref('')
const number = ref('')
const complement = ref('')
const neighborhood = ref('')
const city = ref('')
const state = ref('')

const brazilianStates = [
    { name: 'Acre', sigla: 'AC' },
    { name: 'Alagoas', sigla: 'AL' },
    { name: 'Amapá', sigla: 'AP' },
    { name: 'Amazonas', sigla: 'AM' },
    { name: 'Bahia', sigla: 'BA' },
    { name: 'Ceará', sigla: 'CE' },
    { name: 'Distrito Federal', sigla: 'DF' },
    { name: 'Espírito Santo', sigla: 'ES' },
    { name: 'Goiás', sigla: 'GO' },
    { name: 'Maranhão', sigla: 'MA' },
    { name: 'Mato Grosso', sigla: 'MT' },
    { name: 'Mato Grosso do Sul', sigla: 'MS' },
    { name: 'Minas Gerais', sigla: 'MG' },
    { name: 'Pará', sigla: 'PA' },
    { name: 'Paraíba', sigla: 'PB' },
    { name: 'Paraná', sigla: 'PR' },
    { name: 'Pernambuco', sigla: 'PE' },
    { name: 'Piauí', sigla: 'PI' },
    { name: 'Rio de Janeiro', sigla: 'RJ' },
    { name: 'Rio Grande do Norte', sigla: 'RN' },
    { name: 'Rio Grande do Sul', sigla: 'RS' },
    { name: 'Rondônia', sigla: 'RO' },
    { name: 'Roraima', sigla: 'RR' },
    { name: 'Santa Catarina', sigla: 'SC' },
    { name: 'São Paulo', sigla: 'SP' },
    { name: 'Sergipe', sigla: 'SE' },
    { name: 'Tocantins', sigla: 'TO' },
];

function createPatient() {
    isLoading.value = true;
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('mother_name', motherName.value);
    formData.append('document', document.value);
    formData.append('cns', cns.value);
    formData.append('birthdate', birthdate.value);
    formData.append('phone', phone.value);

    if(files.value) {
        formData.append('photo', files.value);
    }

    formData.append('address[0][zipcode]', zipcode.value);
    formData.append('address[0][street]', street.value);
    formData.append('address[0][number]', number.value);
    formData.append('address[0][complement]', complement.value);
    formData.append('address[0][neighborhood]', neighborhood.value);
    formData.append('address[0][city]', city.value);
    formData.append('address[0][state]', state.value);

    axios.post('/api/patients', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    }).then(() => {
        notify({
            title: 'Cadastrado com sucesso',
            text: 'Paciente cadastrado com sucesso',
        })
        router.push({ name: 'patients' })
    }).catch(error => {
        notify({
            title: 'Erro',
            text: 'Erro ao cadastrar paciente',
        })
        if (error.response.status === 422) {
            console.log(error.response)
            errors.value = error.response.data.errors
        }
    }).finally(() => isLoading.value = false)
}

function handleFileChange(e) {
    files.value = e.target.files[0]
}

function getCep() {
    const value = zipcode.value.replace(/\D/,'')
    if(value.length !== 8) {
        return;
    }

    isLoading.value = true

    axios.get('/api/via-cep', {
        params: {
            cep: value
        }
    }).then((res) => {
        street.value = res.data.logradouro
        neighborhood.value = res.data.bairro
        city.value = res.data.localidade
        state.value = res.data.uf
        complement.value = res.data.complemento
    }).catch(() => {

    }).finally(() => isLoading.value = false)
}

</script>

<template>
    <Spinner v-if="isLoading" />
    <div class="container p-6">
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Pacientes</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Crie novos pacientes</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nome</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="name" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="name" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors?.name?.length">{{ errors?.name[0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="mothername" class="block text-sm font-medium leading-6 text-gray-900">Nome da Mãe</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="mothername" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="motherName" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors?.mother_name?.length">{{ errors?.mother_name[0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="document" class="block text-sm font-medium leading-6 text-gray-900">Documento</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="document" v-maska data-maska="###.###.###-##" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="document" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors?.document?.length">{{ errors?.document[0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="cns" class="block text-sm font-medium leading-6 text-gray-900">CNS</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="cns" v-maska data-maska="### ### ### ### ###" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="cns" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors?.cns?.length">{{ errors?.cns[0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="birthdate" class="block text-sm font-medium leading-6 text-gray-900">Data de Nascimento</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="birthdate" type="date" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="birthdate" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors?.birthdate?.length">{{ errors?.birthdate[0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Telefone</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input
                                    id="phone"
                                    v-maska data-maska="(##) #####-####"
                                    class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="phone"
                                />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors?.phone?.length">{{ errors?.phone[0] }}</span>
                    </div>

                    <div class="col-span-full">
                        <div class="col-span-full">
                            <label for="photo" class="block text-sm font-medium leading-6 text-gray-900">Photo</label>
                            <div class="mt-2 flex items-center gap-x-3">
                                <input
                                    @change="handleFileChange"
                                    type="file"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:border-gray-600 dark:placeholder-gray-400"
                                >
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors?.photo?.length">{{ errors?.photo[0] }}</span>
                    </div>



                    <div class="sm:col-span-4">
                        <label for="zipcode" class="block text-sm font-medium leading-6 text-gray-900">CEP</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="zipcode" @keyup="getCep" @focusout="getCep" v-maska data-maska="#####-###" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="zipcode" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors && errors['address.0.zipcode'].length">{{ errors['address.0.zipcode'][0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="street" class="block text-sm font-medium leading-6 text-gray-900">Rua</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="street" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="street" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors && errors['address.0.street'].length">{{ errors['address.0.street'][0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="number" class="block text-sm font-medium leading-6 text-gray-900">Número</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="number" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="number" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors && errors['address.0.number'].length">{{ errors['address.0.number'][0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="complement" class="block text-sm font-medium leading-6 text-gray-900">Complemento</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="complement" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="complement" />
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">Cidade</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <input id="city" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="city" />
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors && errors['address.0.city'].length">{{ errors['address.0.city'][0] }}</span>
                    </div>

                    <div class="sm:col-span-4">
                        <label for="state" class="block text-sm font-medium leading-6 text-gray-900">Estado</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <select id="state" class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6" v-model="state">
                                    <template v-for="state in brazilianStates">
                                        <option :value="state.sigla">{{ state.name }}</option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <span class="text-red-600" v-if="errors && errors['address.0.state'].length">{{ errors['address.0.state'][0] }}</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <router-link :to="{ name: 'patients' }" type="button" class="text-sm font-semibold leading-6 text-gray-900">Voltar</router-link>
            <button @click="createPatient" type="button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
    </div>
</template>
