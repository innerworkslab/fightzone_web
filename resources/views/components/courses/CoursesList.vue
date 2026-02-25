<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { CoursesColumns, CoursesActions, CoursesSubColumns, CoursesSubActions } from "./columns";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";
import { CoursesServices } from "@/api/Courses.service";
import { buildApiFilter } from "@/utils/buildAPIFilter";
import { useRouter } from "vue-router";
import { CourseLevelsServices } from "@/api/CourseLevels.service";
import { useModalStore } from "@/store/modal";

const router = useRouter();

const dataStore = useDataStore();
const modalStore = useModalStore();

const { filters, refreshTrigger } = storeToRefs(dataStore);

const { data, loading, refresh } = CoursesServices.useCourses(filters.value);

const dataTableRef = ref();

const records = computed(() => {
    return (data.value as any)?.data?.data || [];
});

const paginationInfo = computed(() => {
    return (data.value as any)?.data || {};
});

const startIndex = computed(() => {
    const page = filters.value.page || 1;
    return (page - 1) * DEFAULT_PAGE_LIMIT;
});

const fetchSubData = async (row: any) => {
    const res = await CourseLevelsServices.getCourseLevelsByCourseId(row.id);
    return res.data || [];
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
    <CoursesFilter />

    <DataTable ref="dataTableRef" :data="records" :columns="CoursesColumns" :subColumns="CoursesSubColumns"
        :actions="CoursesActions" :subActions="CoursesSubActions" :loading="loading" :fetchSubData="fetchSubData"
        :extraArgs="{
            startIndex: startIndex,
            modalStore,
            router,
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
