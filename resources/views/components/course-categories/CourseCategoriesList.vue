<script setup lang="ts">
import { computed, watch } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { CourseCategoriesServices } from "@/api/CourseCategories.service";
import { CourseCategoriesColumns, CourseCategoriesActions } from "./columns";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";
import { buildApiFilter } from "@/utils/buildAPIFilter";

const dataStore = useDataStore();
const modalStore = useModalStore();
const router = useRouter();

const { filters, refreshTrigger } = storeToRefs(dataStore);

const { data, loading, refresh } =
    CourseCategoriesServices.useCourseCategoriess(buildApiFilter(filters.value));

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
    <CourseCategoriesFilter />

    <DataTable :data="records" :columns="CourseCategoriesColumns" :actions="CourseCategoriesActions" :loading="loading"
        :extraArgs="{
            startIndex,
            router,
            dataStore,
            modalStore,
            refresh: () => refresh(buildApiFilter(filters)),
        }" />

    <div class="relative flex justify-center items-center">
        <p class="absolute top-[20px] left-[15px]">
            Total: {{ dataStore.totalItems }}
        </p>
        <Pagination />
    </div>
</template>
