<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { WalkInsColumns, WalkInsActions } from "./columns";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";
import { buildApiFilter } from "@/utils/buildAPIFilter";
import { useRouter } from "vue-router";
import { useModalStore } from "@/store/modal";
import { WalkInsServices } from "@/api/WalkIns.service";

const router = useRouter();

const dataStore = useDataStore();
const modalStore = useModalStore();

const { filters, refreshTrigger } = storeToRefs(dataStore);

const { data, refresh } = WalkInsServices.useWalkIns(filters.value);

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
    <WalkInsFilter />

    <DataTable
        ref="dataTableRef"
        :data="[]"
        :columns="WalkInsColumns"
        :actions="WalkInsActions"
        :loading="false"
        :extraArgs="{
            startIndex: startIndex,
            modalStore,
            router,
            refresh: () => refresh(buildApiFilter(filters)),
        }"
    />

    <div class="relative flex justify-center items-center">
        <p class="absolute top-[20px] left-[15px]">
            Total: {{ dataStore.totalItems }}
        </p>
        <Pagination />
    </div>
</template>
