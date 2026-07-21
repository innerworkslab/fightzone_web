<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { StatusOption } from "@/constant/options.constant";
import { TechniqueCategoriesServices } from "@/api/TechniqueCategories.service";

const dataStore = useDataStore();
const modalStore = useModalStore();

const search = ref("");
const isActive = ref("all");
const categoryId = ref("");

const { data: categoryData } = TechniqueCategoriesServices.useTechniqueCategories({
    is_active: "true",
    limit: 100,
});

const categoryOptions = computed(() => {
    const list = (categoryData.value as any)?.data?.data || (categoryData.value as any)?.data || [];
    return [
        { label: "All Categories", value: "" },
        ...list.map((item: any) => ({
            label: item.name,
            value: String(item.id),
        })),
    ];
});

watch([search, isActive, categoryId], ([newSearch, newStatus, newCategoryId]) => {
    dataStore.setFilter("search", newSearch);
    dataStore.setFilter("is_active", newStatus);
    dataStore.setFilter("technique_category_id", newCategoryId);
    dataStore.setFilter("page", 1);
});

function handleReset() {
    search.value = "";
    isActive.value = "all";
    categoryId.value = "";
    dataStore.resetFilters();
}

function handleAdd() {
    modalStore.openModal({
        formIndex: "technique",
    });
}
</script>

<template>
    <BaseFilter @reset="handleReset" @add="handleAdd">
        <div class="flex items-center gap-x-3 w-full">
            <FormInput id="search" v-model="search" placeholder="Search by name" />
            <div class="w-44">
                <FormSelect id="category" v-model="categoryId" :options="categoryOptions"
                    placeholder="Select category" class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
            <div class="w-44">
                <FormSelect id="status" v-model="isActive" :options="StatusOption" placeholder="Select status"
                    class="h-10 bg-secondary/30 border-border/50 w-full" />
            </div>
        </div>
    </BaseFilter>
</template>
