<script setup lang="ts">
import { computed, watch, provide } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { RestDayVideosServices } from "@/api/RestDayVideos.service";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";
import { RestDayVideosActions, RestDayVideosColumns } from "./columns";
import { buildApiFilter } from "@/utils/buildAPIFilter";

const dataStore = useDataStore();
const modalStore = useModalStore();
const router = useRouter();

const { filters, refreshTrigger } = storeToRefs(dataStore);

const { data, loading, refresh } = RestDayVideosServices.useRestDayVideos(
    filters.value,
);

const userData = computed(() => {
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
        refresh(buildApiFilter(filters.value));
    },
    { deep: true },
);

watch(refreshTrigger, () => {
    refresh(buildApiFilter(filters.value));
});
</script>

<template>
    <RestDayVideoFilter />

    <DataTable :data="userData" :columns="RestDayVideosColumns" :actions="RestDayVideosActions" :loading="loading"
        :extraArgs="{
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
