<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue';
import { Label } from '@/components/ui/label';
import Button from '@/components/ui/button/Button.vue';

const props = defineProps<{
    id: string;
    label?: string;
    modelValue: (File | string)[] | null | undefined;
    required?: boolean;
    disabled?: boolean;
    accept?: string;
    helpText?: string;
    previewClass?: string;
    maxMB?: '5MB' | '10MB' | '20MB'
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: (File | string)[]): void;
}>();

const fileInputRef = ref<HTMLInputElement | null>(null);

const imagePreviews = ref<string[]>([]);
const blobUrls = ref<string[]>([]);

watch(() => props.modelValue, (newValues) => {
    blobUrls.value.forEach(url => URL.revokeObjectURL(url));
    blobUrls.value = [];

    const newPreviews: string[] = [];

    if (Array.isArray(newValues)) {
        newValues.forEach(item => {
            if (item instanceof File) {
                const blobUrl = URL.createObjectURL(item);
                newPreviews.push(blobUrl);
                blobUrls.value.push(blobUrl);
            } else if (typeof item === 'string' && item) {
                newPreviews.push(item);
            }
        });
    }
    imagePreviews.value = newPreviews;
}, { immediate: true });

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files ? Array.from(target.files) : [];

    const currentValues = Array.isArray(props.modelValue) ? [...props.modelValue] : [];

    let updatedValues = [...currentValues];

    for (const file of files) {
        const existingIndex = updatedValues.findIndex(value => {
            if (typeof value === 'string') {
                return false;
            }
            return value.name === file.name;
        });

        if (existingIndex !== -1) {
            updatedValues[existingIndex] = file;
        } else {
            updatedValues.push(file);
        }
    }

    emit('update:modelValue', updatedValues);

    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};


const removeImage = (index: number) => {
    const currentModelValue = Array.isArray(props.modelValue) ? [...props.modelValue] : [];

    const removedItem = currentModelValue.splice(index, 1)[0];

    if (typeof removedItem === 'string' && removedItem.startsWith('blob:')) {
        const urlIndex = blobUrls.value.indexOf(removedItem);
        if (urlIndex > -1) {
            URL.revokeObjectURL(blobUrls.value[urlIndex]);
            blobUrls.value.splice(urlIndex, 1);
        }
    }
    emit('update:modelValue', currentModelValue);
};

const clearAllFiles = () => {
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
    blobUrls.value.forEach(url => URL.revokeObjectURL(url));
    blobUrls.value = [];
    emit('update:modelValue', []);
};

onUnmounted(() => {
    blobUrls.value.forEach(url => URL.revokeObjectURL(url));
});
</script>

<template>
    <div :id="id" class="form-image-control space-y-4 text-left group">
        <Label v-if="label" :for="id"
            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground transition-colors group-focus-within:text-primary">
            {{ label }} <span v-if="!disabled && required" class="text-primary">*</span>
        </Label>

        <input :id="id" ref="fileInputRef" type="file" multiple :disabled="disabled" :accept="accept || 'image/*'"
            @change="handleFileChange" class="hidden" />

        <div @click="fileInputRef?.click()"
            class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl transition-all duration-300 cursor-pointer overflow-hidden"
            :class="[
                disabled
                    ? 'border-border/50 bg-muted/10 cursor-not-allowed'
                    : 'border-border bg-secondary/30 hover:bg-secondary/50 hover:border-primary/50 group-hover:shadow-[0_0_15px_rgba(var(--primary),0.1)]'
            ]">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-3 text-primary animate-pulse" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                    </path>
                </svg>
                <p class="text-[10px] font-black uppercase tracking-tighter text-foreground">Click to upload assets</p>
                <p v-if="helpText" class="text-[9px] text-muted-foreground uppercase mt-1">{{ helpText }}</p>
            </div>
        </div>

        <div v-if="imagePreviews.length > 0"
            class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <div v-for="(previewUrl, index) in imagePreviews" :key="previewUrl + index"
                class="relative group rounded-lg overflow-hidden border border-border bg-card shadow-lg aspect-square">

                <img :src="previewUrl" alt="Preview"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    :class="[previewClass || '', { 'opacity-50 grayscale': disabled }]" />

                <div v-if="!disabled"
                    class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <button type="button" @click.stop="removeImage(index)"
                        class="bg-destructive text-white p-2 rounded-full transform scale-75 group-hover:scale-100 transition-transform hover:bg-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-if="imagePreviews.length > 0 && !disabled" class="flex justify-end gap-3 pt-2">
            <Button type="button" variant="ghost" size="sm" @click="clearAllFiles"
                class="text-[10px] font-black uppercase tracking-widest text-destructive hover:bg-destructive/10">
                Wipe All
            </Button>
            <Button type="button" variant="outline" size="sm" @click="fileInputRef?.click()"
                class="text-[10px] font-black uppercase tracking-widest border-primary/50 text-primary hover:bg-primary/10">
                Add More
            </Button>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: translateY(0);
    }

    50% {
        opacity: 0.7;
        transform: translateY(-2px);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
