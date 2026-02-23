import { defineStore } from "pinia";

export interface Filters {
    search: string;
    page: number;
    month?: string;
    is_active?: string;
}

export interface State {
    filters: Filters;
    temp: any[];
    totalItems: number;
    cashbookDate: string;
    initialDate: string;
    refreshTrigger: number;
}

export const useDataStore = defineStore("data", {
    state: (): State => ({
        filters: {
            search: "",
            page: 1,
            month: "",
            is_active: "all",
        },
        temp: [],
        totalItems: 0,
        cashbookDate: new Date().toISOString().split("T")[0],
        initialDate: new Date().toISOString().split("T")[0],
        refreshTrigger: 0,
    }),

    actions: {
        setFilter<K extends keyof Filters>(key: K, value: Filters[K]) {
            this.filters[key] = value;
        },

        setTempData(data: any[]) {
            this.temp = data;
        },

        resetFilters() {
            this.filters = {
                search: "",
                page: 1,
                month: "",
                is_active: "all",
            };
            this.totalItems = 0;
        },

        setTotalItems(total: number) {
            this.totalItems = total;
        },

        goToPage(pageNumber: number) {
            this.filters.page = pageNumber;
        },

        setCashbookDate(date: string) {
            this.cashbookDate = date;
        },

        triggerRefresh() {
            this.refreshTrigger++;
        },
    },
});
