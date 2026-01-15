<script setup lang="ts">
import { computed } from "vue";
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from "lucide-vue-next";
import { useDataStore } from "@/store/data";
import { DEFAULT_PAGE_LIMIT } from "@/constant/global.constant";

const dataStore = useDataStore();
const totalItems = computed(() => dataStore.totalItems);

const currentPageNumber = computed(() => {
    return dataStore.filters.page || 1;
});

const totalPages = computed(() => {
    if (totalItems.value === 0) return 1;
    return Math.ceil(totalItems.value / DEFAULT_PAGE_LIMIT);
});

const pages = computed(() => {
    const pagesArray: (number | string)[] = [];
    const maxVisibleButtons = 5;
    const ellipsis = "...";

    if (totalPages.value > 0) {
        pagesArray.push(1);
    }

    if (totalPages.value <= maxVisibleButtons + 2) {
        for (let i = 2; i <= totalPages.value; i++) {
            pagesArray.push(i);
        }
    } else {
        const leftBound = currentPageNumber.value - Math.floor(maxVisibleButtons / 2);
        const rightBound = currentPageNumber.value + Math.floor(maxVisibleButtons / 2);

        let startRange = Math.max(2, leftBound);
        let endRange = Math.min(totalPages.value - 1, rightBound);

        if (currentPageNumber.value <= Math.ceil(maxVisibleButtons / 2) + 1) {
            endRange = maxVisibleButtons;
            if (endRange > totalPages.value - 1) endRange = totalPages.value - 1;
        } else if (currentPageNumber.value >= totalPages.value - Math.floor(maxVisibleButtons / 2)) {
            startRange = totalPages.value - maxVisibleButtons + 1;
            if (startRange < 2) startRange = 2;
        }

        if (startRange > 2) {
            pagesArray.push(ellipsis);
        }

        for (let i = startRange; i <= endRange; i++) {
            if (i > 1 && i < totalPages.value) {
                pagesArray.push(i);
            }
        }

        if (endRange < totalPages.value - 1) {
            pagesArray.push(ellipsis);
        }

        if (totalPages.value > 1 && !pagesArray.includes(totalPages.value)) {
            pagesArray.push(totalPages.value);
        }
    }

    return pagesArray.filter(
        (value, index, self) =>
            value !== ellipsis || index === 0 || self[index - 1] !== ellipsis
    );
});

const goToPage = (page: number | string) => {
    if (
        typeof page === "number" &&
        page >= 1 &&
        page <= totalPages.value &&
        page !== currentPageNumber.value
    ) {
        dataStore.goToPage(page);
    }
};

const goToFirstPage = () => goToPage(1);
const goToLastPage = () => goToPage(totalPages.value);
const goToPreviousPage = () => goToPage(currentPageNumber.value - 1);
const goToNextPage = () => goToPage(currentPageNumber.value + 1);
</script>

<template>
    <div v-if="totalItems > 0" class="flex items-center justify-center gap-1.5 py-8">

        <button @click="goToFirstPage" :disabled="currentPageNumber === 1"
            class="flex items-center justify-center w-8 h-8 rounded-lg border border-border/50 bg-secondary/30 text-muted-foreground transition-all duration-300 hover:border-primary/50 hover:text-primary hover:bg-secondary disabled:opacity-20 disabled:cursor-not-allowed">
            <ChevronsLeft class="w-4 h-4" />
        </button>

        <button @click="goToPreviousPage" :disabled="currentPageNumber === 1"
            class="flex items-center justify-center w-8 h-8 rounded-lg border border-border/50 bg-secondary/30 text-muted-foreground transition-all duration-300 hover:border-primary/50 hover:text-primary hover:bg-secondary disabled:opacity-20 disabled:cursor-not-allowed">
            <ChevronLeft class="w-4 h-4" />
        </button>

        <div class="flex items-center gap-1.5 mx-2">
            <button v-for="(page, index) in pages" :key="`page-${index}`"
                @click="typeof page === 'number' ? goToPage(page) : null"
                class="min-w-[38px] h-8 flex items-center justify-center rounded-lg border text-[11px] font-black uppercase tracking-tighter transition-all duration-300"
                :class="[
                    page === currentPageNumber
                        ? 'bg-primary text-primary-foreground border-primary shadow-[0_0_20px_rgba(var(--primary),0.3)] scale-105 z-10'
                        : 'bg-secondary/30 border-border/50 text-muted-foreground hover:border-primary/50 hover:text-primary hover:bg-secondary',
                    page === '...' ? 'border-transparent bg-transparent cursor-default pointer-events-none' : 'cursor-pointer'
                ]">
                {{ page }}
            </button>
        </div>

        <button @click="goToNextPage" :disabled="currentPageNumber === totalPages"
            class="flex items-center justify-center w-8 h-8 rounded-lg border border-border/50 bg-secondary/30 text-muted-foreground transition-all duration-300 hover:border-primary/50 hover:text-primary hover:bg-secondary disabled:opacity-20 disabled:cursor-not-allowed">
            <ChevronRight class="w-4 h-4" />
        </button>

        <button @click="goToLastPage" :disabled="currentPageNumber === totalPages"
            class="flex items-center justify-center w-8 h-8 rounded-lg border border-border/50 bg-secondary/30 text-muted-foreground transition-all duration-300 hover:border-primary/50 hover:text-primary hover:bg-secondary disabled:opacity-20 disabled:cursor-not-allowed">
            <ChevronsRight class="w-4 h-4" />
        </button>
    </div>
    <div v-else class="h-24"></div>
</template>
