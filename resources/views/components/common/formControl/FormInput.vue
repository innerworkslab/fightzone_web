<script setup lang="ts">
import { ref, computed, useAttrs, watch } from 'vue';
import Label from '@/components/ui/label/Label.vue';
import { Eye, EyeClosed } from 'lucide-vue-next';

const props = defineProps<{
    label?: string;
    id: string;
    modelValue?: string | number | null;
    type?: string;
    required?: boolean;
    placeholder?: string;
    disabled?: boolean;
    step?: string | number;
    min?: string | number;
    max?: string | number;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number | null): void;
    (e: 'blur', event: FocusEvent): void;
}>();

const attrs = useAttrs();

const showPassword = ref(false);

const inputType = computed(() => {
    if (props.type === 'password') {
        return showPassword.value ? 'text' : 'password';
    }
    return props.type || 'text';
});

const internalValue = ref(
    props.type === "number"
        ? (props.modelValue === '' || props.modelValue === null || props.modelValue === undefined
            ? '0'
            : String(props.modelValue))
        : (props.modelValue !== null && props.modelValue !== undefined ? String(props.modelValue) : '')
);

watch(() => props.modelValue, (newValue) => {
    const stringifiedNewValue = (newValue === null || newValue === undefined) ? '' : String(newValue);
    if (internalValue.value !== stringifiedNewValue) {
        internalValue.value = stringifiedNewValue;
    }
}, { immediate: true });

const clampValue = (value: number | null): number | null => {
    if (value === null) return null;

    let clampedValue = value;
    const minNum = props.min !== undefined ? parseFloat(String(props.min)) : -Infinity;
    const maxNum = props.max !== undefined ? parseFloat(String(props.max)) : Infinity;

    if (!isNaN(minNum) && clampedValue < minNum) {
        clampedValue = minNum;
    }
    if (!isNaN(maxNum) && clampedValue > maxNum) {
        clampedValue = maxNum;
    }
    return clampedValue;
};

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    let typedValue = target.value;

    if (props.type === 'tel') {
        const digitsOnly = typedValue.replace(/\D/g, '');
        if (typedValue !== digitsOnly) {
            target.value = digitsOnly;
            typedValue = digitsOnly;
        }
        internalValue.value = typedValue;
        emit('update:modelValue', typedValue);
        return;
    }

    if (props.type === 'number') {
        if (typedValue === '') {
            internalValue.value = '';
            emit('update:modelValue', null);
            return;
        }

        if (typedValue === '-' && (props.min === undefined || parseFloat(String(props.min)) < 0)) {
            internalValue.value = typedValue;
            emit('update:modelValue', null);
            return;
        }

        if (!/^-?\d*\.?\d*$/.test(typedValue)) {
            target.value = internalValue.value;
            return;
        }

        const parsedValue = parseFloat(typedValue);

        if (!isNaN(parsedValue)) {
            const minNum = props.min !== undefined ? parseFloat(String(props.min)) : -Infinity;
            const maxNum = props.max !== undefined ? parseFloat(String(props.max)) : Infinity;

            if (parsedValue < minNum) {
                internalValue.value = String(minNum);
                emit('update:modelValue', minNum);
                target.value = String(minNum);
                return;
            }
            if (parsedValue > maxNum) {
                internalValue.value = String(maxNum);
                emit('update:modelValue', maxNum);
                target.value = String(maxNum);
                return;
            }
            internalValue.value = typedValue;
            emit('update:modelValue', parsedValue);
        } else {
            internalValue.value = typedValue;
            emit('update:modelValue', null);
        }

    } else {
        internalValue.value = typedValue;
        emit('update:modelValue', typedValue);
    }
};


const handleBlur = (event: FocusEvent) => {
    if (props.type === 'number') {
        if (internalValue.value === '') {
            internalValue.value = '0';
            emit('update:modelValue', 0);
        } else {
            const parsedValue = parseFloat(internalValue.value);
            if (isNaN(parsedValue)) {
                emit('update:modelValue', null);
                internalValue.value = '';
            } else {
                const clampedValue = clampValue(parsedValue);
                internalValue.value = String(clampedValue);
                emit('update:modelValue', clampedValue);
            }
        }
    }
    emit('blur', event);
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

watch(
    () => props.modelValue,
    (newValue) => {
        let stringifiedNewValue: string;

        if (props.type === 'number') {
            if (newValue === '' || newValue === null || newValue === undefined) {
                stringifiedNewValue = '0';
                emit('update:modelValue', 0);
            } else {
                stringifiedNewValue = String(newValue);
            }
        } else {
            stringifiedNewValue =
                newValue === null || newValue === undefined ? '' : String(newValue);
        }

        if (internalValue.value !== stringifiedNewValue) {
            internalValue.value = stringifiedNewValue;
        }
    },
    { immediate: true }
);
</script>

<template>
    <div class="form-input-container mb-4 group">
        <Label v-if="label" :for="id"
            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-1.5 transition-colors group-focus-within:text-primary">
            {{ label }}
            <span v-if="!disabled && required" class="text-primary">*</span>
        </Label>

        <div class="relative">
            <input :id="id" :type="inputType" :value="internalValue" @input="handleInput" @blur="handleBlur"
                :step="step ?? 1" :min="0"
                :max="props.type === 'number' && props.max !== undefined ? props.max : undefined"
                :placeholder="placeholder" :disabled="disabled" v-bind="attrs" :class="[
                    'block w-full px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 outline-none',
                    'bg-secondary/50 border border-border placeholder:text-muted-foreground/50 text-foreground',
                    'focus:border-primary focus:ring-4 focus:ring-primary/10 focus:bg-background',
                    'disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-muted/20',
                    type === 'password' ? 'pr-11' : ''
                ]" />

            <button v-if="props.type === 'password'" type="button" @click="togglePasswordVisibility"
                :disabled="disabled"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-muted-foreground hover:text-primary transition-colors focus:outline-none"
                :class="{ 'cursor-not-allowed opacity-50': disabled }"
                :aria-label="showPassword ? 'Hide password' : 'Show password'">
                <Eye v-if="showPassword" class="w-4 h-4" />
                <EyeClosed v-else class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance: textfield;
}

::placeholder {
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.05em;
    font-weight: 700;
}
</style>
