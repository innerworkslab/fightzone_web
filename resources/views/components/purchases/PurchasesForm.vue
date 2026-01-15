<script setup lang="ts">
import { computed } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { PurchasesPayload, PurchasesServices } from "@/api/Purchases.service";
import { Button } from "@/components/ui/button";
import { useModalStore } from "@/store/modal";

const modalStore = useModalStore();

const isReadMode = computed(() => modalStore.isReadMode);
const purchaseData = computed(() => modalStore.initialValues);

const schema = yup.object({
    note: yup.string().when([], {
        is: () => !isReadMode.value,
        then: (schema) => schema.required("Rejection note is required").min(5, "Note must be at least 5 characters"),
        otherwise: (schema) => schema.optional(),
    }),
});

const { handleSubmit, setValues } = useForm<PurchasesPayload>({
    validationSchema: schema,
    initialValues: {
        note: "",
    }
});

const { value: note } = useField<string>("note");

const { rejectPurchase, loading: isSubmitting } = PurchasesServices.usePurchaseActions();

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value || !purchaseData.value) return;

    try {
        const response = await rejectPurchase(purchaseData.value.id, values);

        if (response?.success) {
            toast.success(response.message ?? "Purchase rejected successfully");
            modalStore.triggerRefresh();
            modalStore.closeModal();
        }
    } catch (error) {
        console.error("Error rejecting purchase:", error);
    }
});
</script>

<template>
    <div v-if="purchaseData" class="space-y-6">
        <!-- Purchase Details Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold">Purchase Information</h3>

                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Purchase ID</label>
                        <p class="text-sm">{{ purchaseData.id }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Status</label>
                        <p class="text-sm">
                            <span :class="{
                                'text-yellow-600 bg-yellow-100': purchaseData.status === 'pending',
                                'text-green-600 bg-green-100': purchaseData.status === 'confirmed',
                                'text-red-600 bg-red-100': purchaseData.status === 'rejected'
                            }" class="px-2 py-1 rounded text-xs font-medium uppercase">
                                {{ purchaseData.status }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Quantity</label>
                        <p class="text-sm">{{ purchaseData.quantity }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Unit Price</label>
                        <p class="text-sm">${{ purchaseData.unit_price }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Total Points</label>
                        <p class="text-sm font-semibold">{{ purchaseData.total_points.toLocaleString() }} points</p>
                    </div>

                    <div v-if="purchaseData.confirmed_at">
                        <label class="text-sm font-medium text-muted-foreground">Processed At</label>
                        <p class="text-sm">{{ new Date(purchaseData.confirmed_at).toLocaleString() }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold">User & Item Information</h3>

                <div class="space-y-3">
                    <div v-if="purchaseData.user">
                        <label class="text-sm font-medium text-muted-foreground">User</label>
                        <p class="text-sm">{{ purchaseData.user.name }}</p>
                        <p class="text-sm text-muted-foreground">{{ purchaseData.user.phone_number }}</p>
                    </div>

                    <div v-if="purchaseData.purchasable">
                        <label class="text-sm font-medium text-muted-foreground">Item</label>
                        <p class="text-sm">{{ purchaseData.purchasable.name }}</p>
                        <p class="text-sm text-muted-foreground capitalize">
                            {{ purchaseData.purchasable_type.split('\\').pop()?.toLowerCase() }}
                        </p>
                    </div>

                    <div v-if="purchaseData.admin">
                        <label class="text-sm font-medium text-muted-foreground">Processed By</label>
                        <p class="text-sm">{{ purchaseData.admin.name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Note Section -->
        <div v-if="purchaseData.admin_note" class="space-y-2">
            <label class="text-sm font-medium text-muted-foreground">Admin Note</label>
            <div class="p-3 bg-muted rounded-md">
                <p class="text-sm">{{ purchaseData.admin_note }}</p>
            </div>
        </div>

        <!-- Rejection Form (only show for pending purchases in non-read mode) -->
        <form v-if="!isReadMode && purchaseData.status === 'pending'" @submit.prevent="submitForm"
            class="space-y-4 border-t pt-6">
            <h3 class="text-lg font-semibold text-red-600">Reject Purchase</h3>

            <div>
                <label for="note"
                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                    Rejection Reason
                </label>
                <textarea id="note" v-model="note" rows="4"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-2"
                    placeholder="Please provide a reason for rejecting this purchase..."></textarea>
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
                        Reject Purchase
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
        Purchase data not available
    </div>
</template>
