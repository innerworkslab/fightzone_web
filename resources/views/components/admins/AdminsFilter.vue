<script setup lang="ts">
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useDataStore } from "@/store/data";
import { RouteNames } from "@/config/route.config";
import { StatusOption } from "@/constant/options.constant";

const router = useRouter();
const dataStore = useDataStore();

const search = ref("");
const is_active = ref("all");

watch([search, is_active], ([newSearch, newStatus]) => {
    if (dataStore.filters.search !== newSearch || dataStore.filters.is_active !== newStatus) {
        dataStore.filters.search = newSearch;
        dataStore.filters.is_active = newStatus;
        dataStore.filters.page = 1;
    }
});

function handleReset() {
    search.value = "";
    is_active.value = "all";
    dataStore.filters.is_active = "all";
}

function handleAdd() {
    router.push({ name: RouteNames.AddAdmin });
}
</script>

<template>
    <BaseFilter add-label="Add" @reset="handleReset" @add="handleAdd">
        <div class="flex items-center gap-3 w-full">
            <div>
                <FormInput id="search" v-model="search" placeholder="Search operators..." />
            </div>

            <div class="w-44">
                <FormSelect id="is_active" v-model="is_active" :options="StatusOption" placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
        </div>
    </BaseFilter>
</template>
