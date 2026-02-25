<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { LessonDaysServices } from "@/api/LessonDays.service";
import { LessonDaysColumns, LessonDaysActions, LessonDaySubColumns, LessonDaySubActions } from "./columns";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";
import { buildApiFilter } from "@/utils/buildAPIFilter";
import { LessonDayVideosServices } from "@/api/LessonDayVideos.service";

const router = useRouter();
const courseLevelId = computed(() => router.currentRoute.value.params.courseLevelId as string);

const dataStore = useDataStore();
const modalStore = useModalStore();

const dataTableRef = ref();

const { filters, refreshTrigger } = storeToRefs(dataStore);

const { data, loading, refresh } =
    LessonDaysServices.useLessonDays(courseLevelId.value, buildApiFilter(filters.value));

const records = computed(() => {
    return (data.value as any)?.data || [];
});

const paginationInfo = computed(() => {
    return (data.value as any)?.data || {};
});

const startIndex = computed(() => {
    const page = filters.value.page || 1;
    return (page - 1) * DEFAULT_PAGE_LIMIT;
});

const fetchSubData = async (row: any) => {
    const res = await LessonDayVideosServices.getLessonDayVideosByLessonDayId(row.id);
    return res.data?.videos || [];
};

const reloadSubTable = (parentRow: any) => {
    if (!dataTableRef.value) return;
    dataTableRef.value.reloadSubTable(parentRow);
};

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
    { deep: true }
);

watch(refreshTrigger, () => {
    refresh(buildApiFilter(filters.value));
});
</script>

<template>
    <LessonDaysFilter />

    <DataTable ref="dataTableRef" :data="records" :columns="LessonDaysColumns" :subColumns="LessonDaySubColumns"
        :actions="LessonDaysActions" :subActions="LessonDaySubActions" :loading="loading" :fetchSubData="fetchSubData"
        :extraArgs="{
            startIndex,
            router,
            dataStore,
            modalStore,
            refresh: () => refresh(buildApiFilter(filters)),
            reloadSubTable,
        }" />

    <div class="relative flex justify-center items-center">
        <p class="absolute top-[20px] left-[15px]">
            Total: {{ dataStore.totalItems }}
        </p>
        <Pagination />
    </div>
</template>
