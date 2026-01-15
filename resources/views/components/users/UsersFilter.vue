<script setup lang="ts">
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useDataStore } from "@/store/data";
import { RouteNames } from "@/config/route.config";
import { StatusOption } from "@/constant/options.constant";
import BaseFilter from "../common/filter/BaseFilter.vue";

const router = useRouter();
const dataStore = useDataStore();

const search = ref("");
const status = ref("all");

watch([search, status], ([newSearch, newStatus]) => {
    if (dataStore.filters.search !== newSearch || dataStore.filters.is_active !== newStatus) {
        dataStore.filters.search = newSearch;
        dataStore.filters.is_active = newStatus;
        dataStore.filters.page = 1;
    }
});

function handleReset() {
    search.value = "";
    status.value = "all";
    dataStore.filters.search = "";
    dataStore.filters.is_active = "all";
}

function handleAdd() {
    router.push({ name: RouteNames.AddUser });
}
</script>

<template>
    <BaseFilter add-label="Add User" @reset="handleReset" @add="handleAdd">
        <div class="flex items-center gap-x-3 w-full">
            <div>
                <FormInput id="search" v-model="search" placeholder="Search users..." />
            </div>

            <div class="w-44">
                <FormSelect id="status" v-model="status" :options="StatusOption" placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
        </div>
    </BaseFilter>
</template>
