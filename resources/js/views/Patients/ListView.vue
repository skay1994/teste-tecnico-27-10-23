<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import axios from "../../helpers/axios";
import Spinner from "../../components/Spinner.vue";
import {notify} from "@kyvg/vue3-notification";

const isLoading = ref(false)
const modalActive = ref(false)
const deletePatientId = ref('')

const search = ref('')
const patients = ref([])
const pagination = ref({
    links: [],
    last_page: 1
});

onMounted(() => getPatients())

watch(search, (value, oldValue) => {
    if(value !== oldValue) {
        getPatients(value)
    }
})

function toggleModal(id?: string) {
    modalActive.value = !modalActive.value
    deletePatientId.value = id
}

function getPatients(s?: string, page?: string|number) {
    let params = new URLSearchParams()
    params.set('page', page ? page.toString() : '1')
    params.set('from', pagination.value.from ?? '1')

    if(s) {
        params.set('s', s)
    }

    axios.get('/api/patients', { params })
        .then(res => {
            patients.value = res.data.data
            pagination.value = {
                links: parsePagination(res.data.meta.current_page, res.data.meta.last_page),
                current_page: res.data.meta.current_page,
                from: res.data.meta.from,
                to: res.data.meta.to,
                total: res.data.meta.total,
                last_page: res.data.meta.last_page
            }
        }).finally(() => isLoading.value = false)
}

function deletePatient() {
    isLoading.value = true

    axios.delete(`/api/patients/${deletePatientId.value}`)
        .then(() => {
            getPatients()
            notify({
                title: 'Paciente excluido',
                text: 'Paciente excluido com sucesso',
            })
        })
        .finally(() => {
            isLoading.value = false
            toggleModal()
        })
}

function parsePagination(current_page: number, total: number) {
    const newLinks = [];
    let startPagination = current_page - 2;
    let endPagination = current_page + 2;

    if(endPagination > total) {
        endPagination = total
    }

    while (startPagination <= endPagination) {
        if (startPagination > 0 && startPagination <= total) {
            newLinks.push({ label: startPagination.toString(), active: startPagination.toString() === current_page.toString() });
        }
        startPagination++;
    }


    return newLinks
}
</script>

<template>
    <Spinner v-if="isLoading" />
    <div class="mx-auto max-w-screen-xl px-4">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form class="flex items-center">
                        <label for="simple-search" class="sr-only">Pesquisar</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                     fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input
                                v-model="search"
                                type="text" id="simple-search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Search"
                            >
                        </div>
                    </form>
                </div>
                <div
                    class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <router-link :to="{ name: 'patient-import'}"
                            class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        Importar CSV
                    </router-link>
                    <router-link :to="{name: 'patient-create'}" type="button"
                            class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                        </svg>
                        Adicionar Paciente
                    </router-link>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">Nome</th>
                        <th scope="col" class="px-4 py-3">Nome da Mãe</th>
                        <th scope="col" class="px-4 py-3">Documento</th>
                        <th scope="col" class="px-4 py-3">CNS</th>
                        <th scope="col" class="px-4 py-3">Data de Nascimento</th>
                        <th scope="col" class="px-4 py-3"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="border-b dark:border-gray-700" v-for="patient in patients">
                        <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ patient.name }}
                        </th>
                        <td class="px-4 py-3">{{ patient.mother_name }}</td>
                        <td class="px-4 py-3">{{ patient.document }}</td>
                        <td class="px-4 py-3">{{ patient.cns }}</td>
                        <td class="px-4 py-3">{{ patient.birthdate }}</td>
                        <td class="px-4 py-3">
                            <button @click="() => toggleModal(patient.id)" type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                Deletar
                            </button>
                            <router-link
                                :to="{name: 'patient-update', params: { id: patient.id, pageTitle: `Editar Paciente: ${patient.name}`}}"
                                class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"
                            >Editar</router-link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <nav
                class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
                aria-label="Table navigation">
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">{{ pagination.from }}-{{ pagination.to  }}</span>
                    of
                    <span class="font-semibold text-gray-900 dark:text-white">{{ pagination.total }}</span>
                </span>
                <ul class="inline-flex items-stretch -space-x-px">
                    <li>
                        <a href="#"
                           class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="sr-only">Previous</span>
                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </li>

                    <template v-for="page in pagination.links">
                        <li>
                            <template v-if="page.active">
                                <button
                                    aria-current="page" @click="getPatients(search, page.label)"
                                    class="flex items-center justify-center text-sm z-10 py-2 px-3 leading-tight text-primary-600 bg-primary-50 border border-primary-300 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white"
                                >{{ page.label }}</button>
                            </template>
                            <template v-if="!page.active">
                                <button
                                    @click="getPatients(search, page.label)"
                                    class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                >{{ page.label }}</button>
                            </template>
                        </li>
                    </template>
                    <li>
                        <a href="#"
                           class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="sr-only">Next</span>
                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main modal -->
        <div id="deleteModal" tabindex="-1" aria-hidden="true" :class="{'hidden': !modalActive}" class="flex justify-content-center overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
            <div class="relative p-4 w-full max-w-md h-full md:h-auto">
                <!-- Modal content -->
                <div class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                    <button type="button" class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="deleteModal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <svg class="text-gray-400 dark:text-gray-500 w-11 h-11 mb-3.5 mx-auto" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <p class="mb-4 text-gray-500 dark:text-gray-300">Are you sure you want to delete this item?</p>
                    <div class="flex justify-center items-center space-x-4">
                        <button
                            @click="toggleModal()"
                            type="button"
                            class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                        >
                            Não, cancelar
                        </button>
                        <button
                            @click="deletePatient"
                            type="button" class="py-2 px-3 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                            Sim, tenho certeza
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
