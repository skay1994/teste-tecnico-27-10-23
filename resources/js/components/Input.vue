<script setup lang="ts">
defineProps({
    modelValue: String,
    label: String,
    name: String,
    id: String,
    errors: {
        type: Array,
        default: () => []
    },
    extraClass: {
        default: '',
        type: String
    },
    type: {
        default: 'text',
        type: String
    },
    placeholder: {
        default: '',
        type: String
    },
    required: {
        default: false,
        type: Boolean
    }
})
defineEmits(['update:modelValue'])
</script>

<template>
    <label :for="id" class="block mb-2 text-sm font-medium text-gray-900" v-text="label"/>
    <input
        :type="type" :name="name" :id="id" :placeholder="placeholder" :required="required"
        :class="extraClass" class="border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 border-gray-600 placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
        @input="$emit('update:modelValue', $event.target.value)"
    >
    <span class="text-red-600" v-if="errors.length">{{ errors[0] }}</span>
</template>
