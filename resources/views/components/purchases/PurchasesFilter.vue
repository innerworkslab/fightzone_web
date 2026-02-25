<script setup lang="ts">
import { ref, watch } from "vue";
import { useDataStore } from "@/store/data";
import BaseFilter from "../common/filter/BaseFilter.vue";
import { useRouter } from "vue-router";

const router = useRouter();

const dataStore = useDataStore();

const search = ref("");
const isActive = ref("all");

const statusOptions = [
    { label: "All", value: "all" },
    { label: "Pending", value: "pending" },
    { label: "Confirmed", value: "confirmed" },
    { label: "Rejected", value: "rejected" },
];

watch([search, isActive], ([newSearch, newStatus]) => {
    if (
        dataStore.filters.search !== newSearch ||
        dataStore.filters.is_active !== newStatus
    ) {
        dataStore.setFilter("search", newSearch);
        dataStore.setFilter("is_active", newStatus);
        dataStore.setFilter("page", 1);
    }
});

function handleReset() {
    search.value = "";
    isActive.value = "all";
    dataStore.resetFilters();
}
</script>

<template>
    <BaseFilter @reset="handleReset" :show-add="false">
        <div class="flex items-center gap-x-3 w-full">
            <div>
                <FormInput id="search" v-model="search" placeholder="Search by name" />
            </div>

            <div class="w-44">
                <FormSelect id="status" v-model="isActive" :options="statusOptions" placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
        </div>
    </BaseFilter>
</template>
