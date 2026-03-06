<script setup lang="ts">
import { ref, watch } from "vue";
import { useDataStore } from "@/store/data";
import BaseFilter from "../common/filter/BaseFilter.vue";
import { StatusOption } from "@/constant/options.constant";
import { useModalStore } from "@/store/modal";

const modalStore = useModalStore();

const dataStore = useDataStore();

const search = ref("");
const isActive = ref("all");

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

function handleAdd() {
    modalStore.openModal({
        formIndex: "walkInForm",
    });
}
</script>

<template>
    <BaseFilter @reset="handleReset" add-label="SCAN QR" @add="handleAdd">
        <div class="flex items-center gap-x-3 w-full">
            <div>
                <FormInput id="search" v-model="search" placeholder="Search by name" />
            </div>

            <div class="w-44">
                <FormSelect id="status" v-model="isActive" :options="StatusOption" placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
        </div>
    </BaseFilter>
</template>
