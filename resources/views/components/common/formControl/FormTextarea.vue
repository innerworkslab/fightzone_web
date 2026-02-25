<script setup lang="ts">
import Label from '@/components/ui/label/Label.vue'

defineProps<{
    id: string
    label?: string
    modelValue: string | null
    placeholder?: string
    rows?: number
    disabled?: boolean
    required?: boolean
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
    <div class="form-input-container mb-4 group">
        <Label v-if="label" :for="id"
            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-1.5 transition-colors group-focus-within:text-primary">
            {{ label }}
            <span v-if="required" class="text-primary">*</span>
        </Label>

        <textarea :id="id" :value="modelValue ?? ''" :placeholder="placeholder" :rows="rows ?? 4" :disabled="disabled"
            @input="updateValue" class="block w-full px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 outline-none
                   bg-secondary/50 border border-border placeholder:text-muted-foreground/50 text-foreground
                   focus:border-primary focus:ring-4 focus:ring-primary/10 focus:bg-background
                   disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-muted/20 resize-none" />
    </div>
</template>

<style scoped>
::placeholder {
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.05em;
    font-weight: 700;
}
</style>
