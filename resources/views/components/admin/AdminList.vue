<script setup lang="ts">
import { computed, watch, provide } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { AdminServices } from "@/api/Admin.service";
import { AdminActions, AdminColumns } from "./columns";

const dataStore = useDataStore();
const modalStore = useModalStore();
const router = useRouter();

const { filters } = storeToRefs(dataStore);

const { data, loading, refresh } = AdminServices.useAdmins(filters.value);

const adminData = computed(() => {
    return (data.value as any)?.data?.data || [];
});

const paginationInfo = computed(() => {
    return (data.value as any)?.data || {};
});

const startIndex = computed(() => {
    const page = filters.value.page || 1;
    const limit = filters.value.limit || 20;
    return (page - 1) * limit;
});

watch(paginationInfo, (newInfo) => {
    if (newInfo && newInfo.total !== undefined) {
        dataStore.setTotalItems(newInfo.total);
    }
});

watch(
    filters,
    () => {
        refresh();
    },
    { deep: true }
);
</script>

<template>
    <div class="mb-5">
        <AdminFilter />
    </div>

    <DataTable :data="adminData" :columns="AdminColumns" :actions="AdminActions" :loading="loading" :extraArgs="{
        startIndex: startIndex,
        router,
        dataStore,
        modalStore,
        refresh,
    }" />

    <div class="relative flex justify-center items-center">
        <p class="absolute top-[20px] left-[15px]">
            Total: {{ dataStore.totalItems }}
        </p>
        <Pagination />
    </div>
</template>
