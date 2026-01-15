<script setup lang="ts">
import { computed, watch, provide } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { PackagesServices } from "@/api/Packages.service";
import { PackageActions, PackageColumns } from "./columns";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";

const dataStore = useDataStore();
const modalStore = useModalStore();
const router = useRouter();

const { filters, refreshTrigger } = storeToRefs(dataStore);

const { data, loading, refresh } = PackagesServices.usePackagess(filters.value);

const packageData = computed(() => {
    return (data.value as any)?.data?.data || [];
});

const paginationInfo = computed(() => {
    return (data.value as any)?.data || {};
});

const startIndex = computed(() => {
    const page = filters.value.page || 1;
    return (page - 1) * DEFAULT_PAGE_LIMIT;
});

watch(paginationInfo, (newInfo) => {
    if (newInfo && newInfo.total !== undefined) {
        dataStore.setTotalItems(newInfo.total);
    }
});

watch(
    filters,
    () => {
        const apiFilter = { ...filters.value };

        if (filters.value.search) {
            apiFilter.name = filters.value.search;
            delete (apiFilter as any).search;
        }

        if (apiFilter.status === "all") {
            delete apiFilter.status;
        }

        refresh(apiFilter);
    },
    { deep: true }
);

watch(refreshTrigger, () => {
    const apiFilter = { ...filters.value };

    if (filters.value.search) {
        apiFilter.name = filters.value.search;
        delete (apiFilter as any).search;
    }

    if (apiFilter.status === "all") {
        delete apiFilter.status;
    }

    refresh(apiFilter);
});
</script>

<template>
    <PackagesFilter />

    <DataTable :data="packageData" :columns="PackageColumns" :actions="PackageActions" :loading="loading" :extraArgs="{
        startIndex: startIndex,
        router,
        dataStore,
        modalStore,
        refresh: () => refresh(filters),
    }" />

    <div class="relative flex justify-center items-center">
        <p class="absolute top-[20px] left-[15px]">
            Total: {{ dataStore.totalItems }}
        </p>
        <Pagination />
    </div>
</template>
