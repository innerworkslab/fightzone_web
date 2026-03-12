<script setup lang="ts">
import { ref, watch } from "vue";
import { useDataStore } from "@/store/data";
import BaseFilter from "../common/filter/BaseFilter.vue";
import { StatusOption } from "@/constant/options.constant";
import { RouteNames } from "@/config/route.config";
import { useRouter } from "vue-router";

const router = useRouter();

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
    router.push({ name: RouteNames.AddFeaturedImage });
}
</script>

<template>
    <BaseFilter @reset="handleReset" @add="handleAdd">
        <div class="flex items-center gap-x-3 w-full">
            <div>
                <FormInput
                    id="search"
                    v-model="search"
                    placeholder="Search by name"
                />
            </div>

            <div class="w-44">
                <FormSelect
                    id="status"
                    v-model="isActive"
                    :options="StatusOption"
                    placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full"
                />
            </div>
        </div>
    </BaseFilter>
</template>
