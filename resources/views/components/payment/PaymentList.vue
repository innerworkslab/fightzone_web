<script setup lang="ts">
import { computed, watch, provide } from "vue";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import { PaymentServices } from "@/api/Payment.service";
import { PaymentActions, PaymentColumns } from "./columns";

const dataStore = useDataStore();
const modalStore = useModalStore();
const router = useRouter();

const { filters } = storeToRefs(dataStore);

const { data, loading, refresh } = PaymentServices.usePaymentMethods(filters.value);

const paymentData = computed(() => {
    return (data.value as any)?.data?.data || [];
});

const paginationInfo = computed(() => {
    return (data.value as any)?.data || {};
});

provide('paymentListRefresh', refresh);

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
        <PaymentFilter />
    </div>

    <DataTable :data="paymentData" :columns="PaymentColumns" :actions="PaymentActions" :loading="loading" :extraArgs="{
        startIndex: startIndex,
        router,
        dataStore,
        modalStore,
    }" />

    <div class="relative flex justify-center items-center">
        <p class="absolute top-[20px] left-[15px]">
            Total: {{ dataStore.totalItems }}
        </p>
    </div>
</template>
