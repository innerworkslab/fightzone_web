<script setup lang="ts">
import { Label } from "../../../../js/ts/components/ui/label";
import Button from "@/components/ui/button/Button.vue";
import { Plus, ChevronDown, Search } from "lucide-vue-next";
import {
    ref,
    computed,
    onMounted,
    onBeforeUnmount,
    watch,
    nextTick,
} from "vue";

const props = defineProps<{
    id: string;
    label?: string;
    modelValue: string | number | bigint | null;
    options?: Array<{ value: string | number | bigint; label: string }>;
    placeholder?: string;
    addFn?: () => void;
    disabled?: boolean;
    required?: boolean;
    fetchFn?: (
        page: number,
        query: string
    ) => Promise<{
        data: Array<{ value: string | number | bigint; label: string }>;
        hasMore: boolean;
    }>;
}>();

const emit = defineEmits<{
    (e: "update:modelValue", value: string | number | bigint | null): void;
}>();

const dropdownRef = ref<HTMLElement | null>(null);
const listRef = ref<HTMLElement | null>(null);
const isOpen = ref(false);
const searchQuery = ref("");

const page = ref(1);
const isFetching = ref(false);
const hasMore = ref(true);
const dynamicOptions = ref<
    Array<{ value: string | number | bigint; label: string }>
>([]);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

function toggleDropdown() {
    if (!props.disabled) {
        isOpen.value = !isOpen.value;
        if (isOpen.value && props.fetchFn) {
            loadOptions(true);
        } else {
            searchQuery.value = "";
        }
    }
}

function closeDropdown() {
    isOpen.value = false;
}

function selectOption(value: string | number | bigint) {
    if (!props.disabled) {
        emit("update:modelValue", value);
        searchQuery.value = "";
        closeDropdown();
    }
}

const allOptions = computed(() => {
    if (props.fetchFn) {
        return dynamicOptions.value;
    }
    return props.options || [];
});

const selectedItemLabel = computed(() => {
    const optionsToSearch = props.fetchFn ? dynamicOptions.value : props.options;
    const selectedOption = optionsToSearch?.find(option => option.value === props.modelValue);
    return selectedOption ? selectedOption.label : props.placeholder ?? "Select an option";
});

function handleClickOutside(event: MouseEvent) {
    if (
        dropdownRef.value &&
        !dropdownRef.value.contains(event.target as Node)
    ) {
        closeDropdown();
    }
}

async function loadOptions(reset = false, query = searchQuery.value) {
    if (!props.fetchFn || isFetching.value) return;

    if (reset) {
        page.value = 1;
        dynamicOptions.value = [];
        hasMore.value = true;
    }

    if (!hasMore.value) return;

    isFetching.value = true;
    try {
        const { data, hasMore: hasMorePages } = await props.fetchFn(
            page.value,
            query
        );

        dynamicOptions.value = [...dynamicOptions.value, ...data];

        hasMore.value = hasMorePages;

        if (hasMore.value) {
            page.value++;
        }
    } finally {
        isFetching.value = false;
    }
}

function handleScroll() {
    if (!listRef.value || isFetching.value || !hasMore.value) return;
    const el = listRef.value;

    if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10) {
        loadOptions(false);
    }
}

watch(searchQuery, (newQuery, oldQuery) => {
    if (props.fetchFn && newQuery !== oldQuery) {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadOptions(true, newQuery);
        }, 300);
    }
});

watch(isOpen, async (newVal) => {
    if (newVal) {
        await nextTick();
        if (listRef.value) {
            listRef.value.addEventListener("scroll", handleScroll);
        }
    } else {
        if (listRef.value) {
            listRef.value.removeEventListener("scroll", handleScroll);
        }
        searchQuery.value = "";
    }
});

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
    if (props.fetchFn) {
        loadOptions(true);
    }
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
    if (listRef.value)
        listRef.value.removeEventListener("scroll", handleScroll);
    if (debounceTimer) clearTimeout(debounceTimer);
});
</script>

<template>
    <div ref="dropdownRef" class="relative mb-4 group">
        <Label v-if="label" :for="id"
            class="block text-[10px] font-black uppercase tracking-widest text-muted-foreground mb-1.5 transition-colors group-focus-within:text-primary">
            {{ label }}
            <span v-if="!disabled && required" class="text-primary">*</span>
        </Label>

        <div :id="id"
            class="w-full flex justify-between items-center text-sm font-medium px-4 py-2.5 rounded-lg border transition-all duration-200 outline-none"
            :class="[
                disabled
                    ? 'cursor-not-allowed border-border/50 bg-muted/20 text-muted-foreground/50'
                    : 'cursor-pointer border-border bg-secondary/50 text-foreground hover:border-primary/50 hover:bg-secondary',
                isOpen ? 'ring-4 ring-primary/10 border-primary bg-background' : ''
            ]" @click="toggleDropdown">
            <span :class="{ 'text-muted-foreground/50': selectedItemLabel === placeholder }">
                {{ selectedItemLabel }}
            </span>
            <span class="ml-2 text-primary transition-transform duration-300" :class="{ 'rotate-180': isOpen }">
                <ChevronDown class="w-4 h-4" />
            </span>
        </div>

        <transition enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0">
            <div v-if="isOpen"
                class="absolute z-50 w-full mt-2 bg-card border border-border rounded-xl shadow-2xl overflow-hidden backdrop-blur-md">

                <div v-if="fetchFn || addFn" class="p-2 border-b border-border bg-muted/30 flex items-center gap-2">
                    <div class="relative flex-1">
                        <Search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-muted-foreground" />
                        <input type="text" v-model="searchQuery"
                            class="w-full pl-8 pr-3 py-1.5 bg-background border border-border rounded-md text-xs text-foreground placeholder:text-muted-foreground/50 focus:outline-none focus:border-primary"
                            placeholder="Search..." @click.stop />
                    </div>

                    <Button v-if="addFn" @click.prevent="addFn" class="flex-shrink-0 btn-primary !py-1.5 !px-3"
                        type="button">
                        <Plus class="w-4 h-4" />
                    </Button>
                </div>

                <ul ref="listRef" class="max-h-60 overflow-y-auto custom-scrollbar py-1">
                    <li v-for="option in allOptions" :key="option.value.toString()" @click="selectOption(option.value)"
                        class="px-4 py-2.5 text-xs font-bold uppercase tracking-tight cursor-pointer transition-colors"
                        :class="[
                            option.value === props.modelValue
                                ? 'bg-primary text-primary-foreground'
                                : 'text-foreground/80 hover:bg-primary/10 hover:text-primary'
                        ]">
                        {{ option.label }}
                    </li>

                    <li v-if="isFetching" class="px-4 py-8 flex flex-col items-center gap-2 text-muted-foreground">
                        <svg class="animate-spin h-5 w-5 text-primary" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="text-[10px] font-black uppercase">Syncing...</span>
                    </li>
                    <li v-else-if="allOptions.length === 0" class="px-4 py-8 text-center">
                        <span class="text-[10px] font-black uppercase text-muted-foreground italic tracking-widest">
                            No Results Found
                        </span>
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--primary);
}
</style>
