<script setup lang="ts">
import { computed } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { DepositsPayload, DepositsServices } from "@/api/Deposits.service";
import { Button } from "@/components/ui/button";
import { useModalStore } from "@/store/modal";

const modalStore = useModalStore();

const isReadMode = computed(() => modalStore.isReadMode);
const depositData = computed(() => modalStore.initialValues);

const schema = yup.object({
    note: yup.string().when([], {
        is: () => !isReadMode.value,
        then: (schema) => schema.required("Rejection note is required").min(5, "Note must be at least 5 characters"),
        otherwise: (schema) => schema.optional(),
    }),
});

const { handleSubmit, setValues } = useForm<DepositsPayload>({
    validationSchema: schema,
    initialValues: {
        note: "",
    }
});

const { value: note } = useField<string>("note");

const { rejectDeposit, loading: isSubmitting } = DepositsServices.useDepositActions();

const openImageInNewTab = (url: string) => {
    window.open(url, '_blank');
};

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value || !depositData.value) return;

    try {
        const response = await rejectDeposit(depositData.value.id, values);

        if (response?.success) {
            toast.success(response.message ?? "Deposit rejected successfully");
            modalStore.triggerRefresh();
            modalStore.closeModal();
        }
    } catch (error) {
        console.error("Error rejecting deposit:", error);
    }
});
</script>

<template>
    <div v-if="depositData" class="space-y-6">
        <!-- Deposit Details Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Deposit Information</h3>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Deposit ID</label>
                        <p class="text-sm">{{ depositData.id }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Transaction ID</label>
                        <p class="text-sm font-mono">{{ depositData.transaction_id }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Status</label>
                        <p class="text-sm">
                            <span :class="{
                                'text-yellow-600 bg-yellow-100': depositData.status === 'pending',
                                'text-green-600 bg-green-100': depositData.status === 'confirmed',
                                'text-red-600 bg-red-100': depositData.status === 'rejected'
                            }" class="px-2 py-1 rounded text-xs font-medium uppercase">
                                {{ depositData.status }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Amount</label>
                        <p class="text-lg font-semibold">${{ depositData.amount.toLocaleString() }}</p>
                    </div>

                    <div v-if="depositData.confirmed_at">
                        <label class="text-sm font-medium text-muted-foreground">Processed At</label>
                        <p class="text-sm">{{ new Date(depositData.confirmed_at).toLocaleString() }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold">User & Payment Information</h3>

                <div class="space-y-3">
                    <div v-if="depositData.user">
                        <label class="text-sm font-medium text-muted-foreground">User</label>
                        <p class="text-sm">{{ depositData.user.name }}</p>
                        <p class="text-sm text-muted-foreground">{{ depositData.user.phone_number }}</p>
                    </div>

                    <div v-if="depositData.paymentMethod">
                        <label class="text-sm font-medium text-muted-foreground">Payment Method</label>
                        <p class="text-sm">{{ depositData.paymentMethod.name }}</p>
                        <p class="text-sm text-muted-foreground">{{ depositData.paymentMethod.holder }}</p>
                    </div>

                    <div v-if="depositData.admin">
                        <label class="text-sm font-medium text-muted-foreground">Processed By</label>
                        <p class="text-sm">{{ depositData.admin.name }}</p>
                    </div>
                </div>

                <!-- Screenshot Section -->
                <div v-if="depositData.screenshot_url" class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Receipt Screenshot</label>
                    <div class="border rounded-lg p-2 bg-secondary/20">
                        <img :src="depositData.screenshot_url" alt="Receipt Screenshot"
                            class="max-w-full h-auto rounded cursor-pointer"
                            @click="openImageInNewTab(depositData.screenshot_url)" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Note Section -->
        <div v-if="depositData.admin_note" class="space-y-2">
            <label class="text-sm font-medium text-muted-foreground">Admin Note</label>
            <div class="p-3 bg-muted rounded-md">
                <p class="text-sm">{{ depositData.admin_note }}</p>
            </div>
        </div>

        <!-- Rejection Form (only show for pending deposits in non-read mode) -->
        <form v-if="!isReadMode && depositData.status === 'pending'" @submit.prevent="submitForm"
            class="space-y-4 border-t pt-6">
            <h3 class="text-lg font-semibold text-red-600">Reject Deposit</h3>

            <div>
                <label for="note"
                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                    Rejection Reason
                </label>
                <textarea id="note" v-model="note" rows="4"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-2"
                    placeholder="Please provide a reason for rejecting this deposit..."></textarea>
                <ErrorMessage name="note" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div class="flex justify-end pt-4">
                <Button variant="destructive" type="submit" :disabled="isSubmitting" class="min-w-[140px]">
                    <template v-if="isSubmitting">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Processing...
                    </template>
                    <template v-else>
                        Reject Deposit
                    </template>
                </Button>
            </div>
        </form>

        <!-- Close button for read mode -->
        <div v-if="isReadMode" class="flex justify-end pt-6 border-t">
            <Button variant="outline" @click="modalStore.closeModal()">
                Close
            </Button>
        </div>
    </div>
    <div v-else class="py-10 text-center text-primary font-medium">
        Deposit data not available
    </div>
</template>
