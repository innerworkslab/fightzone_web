<script setup lang="ts">
defineProps<{
    id: string
    label?: string
    modelValue: string | null
    placeholder?: string
    rows?: number
    disabled?: boolean
}>()

const emit = defineEmits<{
    (e: "update:modelValue", value: string): void
}>()

const updateValue = (event: Event) => {
    const target = event.target as HTMLTextAreaElement
    emit("update:modelValue", target.value)
}
</script>

<template>
    <div class="space-y-2">
        <label v-if="label" :for="id" class="text-sm font-medium">
            {{ label }}
        </label>

        <textarea :id="id" :value="modelValue ?? ''" :placeholder="placeholder" :rows="rows ?? 4" :disabled="disabled"
            @input="updateValue"
            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary disabled:opacity-50" />
    </div>
</template>
