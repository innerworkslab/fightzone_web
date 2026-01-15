<script setup lang="ts">
import { ref, watch } from "vue";
import { useModalStore } from "@/store/modal";
import { useDataStore } from "@/store/data";
import { StatusOption } from "@/constant/options.constant";
import BaseFilter from "../common/filter/BaseFilter.vue";

const dataStore = useDataStore();

const search = ref("");
const status = ref("all");

const statusOptions = [
    { label: "All", value: "all" },
    { label: "Pending", value: "pending" },
    { label: "Confirmed", value: "confirmed" },
    { label: "Rejected", value: "rejected" },
];

watch([search, status], ([newSearch, newStatus]) => {
    if (dataStore.filters.search !== newSearch || dataStore.filters.status !== newStatus) {
        dataStore.filters.search = newSearch;
        dataStore.filters.status = newStatus;
        dataStore.filters.page = 1;
    }
});

function handleReset() {
    search.value = "";
    status.value = "all";
    dataStore.filters.search = "";
    dataStore.filters.status = "all";
}
</script>

<template>
    <BaseFilter @reset="handleReset" :show-add="false">
        <div class="flex items-center gap-x-3 w-full">
            <div>
                <FormInput id="search" v-model="search" placeholder="Search by user name or transaction ID..." />
            </div>

            <div class="w-44">
                <FormSelect id="status" v-model="status" :options="statusOptions" placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
        </div>
    </BaseFilter>
</template>
