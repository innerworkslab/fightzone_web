<script setup lang="ts">
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useDataStore } from "@/store/data";
import { RouteNames } from "@/config/route.config";
import BaseFilter from "../common/filter/BaseFilter.vue";

const router = useRouter();
const dataStore = useDataStore();

const search = ref("");

watch(search, (newSearch) => {
    if (dataStore.filters.search !== newSearch) {
        dataStore.filters.search = newSearch;
        dataStore.filters.page = 1;
    }
});

function handleReset() {
    search.value = "";
    dataStore.filters.search = "";
}

function handleAdd() {
    router.push({ name: RouteNames.AddPayment });
}
</script>

<template>
    <BaseFilter add-label="Add Payment Method" @reset="handleReset" @add="handleAdd">
        <div class="flex items-center gap-3 w-full">
            <div>
                <FormInput id="search" v-model="search" placeholder="Search payment methods..." />
            </div>
        </div>
    </BaseFilter>
</template>
