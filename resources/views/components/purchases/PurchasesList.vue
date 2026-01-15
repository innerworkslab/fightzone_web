<script setup lang="ts">
import { computed, watch } from "vue";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { PurchasesServices } from "@/api/Purchases.service";
import { PurchaseActions, PurchaseColumns } from "./columns";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";

const dataStore = useDataStore();
const modalStore = useModalStore();

const { filters, refreshTrigger } = storeToRefs(dataStore);

const { data, loading, refresh } = PurchasesServices.usePurchases(filters.value);

const purchaseData = computed(() => {
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
            // For purchases, search can be by user name or phone
            apiFilter.user_name = filters.value.search;
            apiFilter.user_phone = filters.value.search;
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
        apiFilter.user_name = filters.value.search;
        apiFilter.user_phone = filters.value.search;
        delete (apiFilter as any).search;
    }

    if (apiFilter.status === "all") {
        delete apiFilter.status;
    }

    refresh(apiFilter);
});
</script>

<template>
    <PurchasesFilter />

    <DataTable :data="purchaseData" :columns="PurchaseColumns" :actions="PurchaseActions" :loading="loading" :extraArgs="{
        startIndex: startIndex,
        modalStore,
        refresh: () => {
            const apiFilter = { ...filters };
            if (filters.search) {
                apiFilter.user_name = filters.search;
                apiFilter.user_phone = filters.search;
                delete (apiFilter as any).search;
            }
            if (apiFilter.status === 'all') {
                delete apiFilter.status;
            }
            refresh(apiFilter);
        },
    }" />

    <div class="relative flex justify-center items-center">
        <p class="absolute top-[20px] left-[15px]">
            Total: {{ dataStore.totalItems }}
        </p>
        <Pagination />
    </div>
</template>
