<script setup lang="ts">
import { ref, watch } from "vue";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { StatusOption } from "@/constant/options.constant";

const dataStore = useDataStore();
const modalStore = useModalStore();

const contactTypeOptions = [
    { label: "All", value: "all" },
    { label: "URL", value: "url" },
    { label: "Phone Number", value: "phone_number" },
];

const search = ref("");
const type = ref("all");
const isActive = ref("all");

watch([search, type, isActive], ([newSearch, newType, newStatus]) => {
    dataStore.setFilter("search", newSearch);
    dataStore.setFilter("type", newType);
    dataStore.setFilter("is_active", newStatus);
    dataStore.setFilter("page", 1);
});

function handleReset() {
    search.value = "";
    type.value = "all";
    isActive.value = "all";
    dataStore.resetFilters();
}

function handleAdd() {
    modalStore.openModal({
        formIndex: "contact",
    });
}
</script>

<template>
    <BaseFilter @reset="handleReset" @add="handleAdd">
        <div class="flex items-center gap-x-3 w-full">
            <FormInput id="search" v-model="search" placeholder="Search by name or contact" />
            <div class="w-44">
                <FormSelect id="type" v-model="type" :options="contactTypeOptions" placeholder="Select type"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
            <div class="w-44">
                <FormSelect id="status" v-model="isActive" :options="StatusOption" placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
        </div>
    </BaseFilter>
</template>
