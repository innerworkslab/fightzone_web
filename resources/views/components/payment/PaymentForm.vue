<script setup lang="ts">
import { computed, ref, watch, inject } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { RouteNames } from "@/config/route.config";
import { PaymentMethodPayload, PaymentServices } from "@/api/Payment.service";
import { Button } from "@/components/ui/button";

const paymentListRefresh = inject<() => void>('paymentListRefresh');

const route = useRoute();
const router = useRouter();
const paymentId = Number(route.params.id);

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewPayment);

const { data: fetchedPaymentMethod, loading: isFetching } = isUpdateMode.value
    ? PaymentServices.usePaymentMethodDetail(paymentId)
    : { data: ref(null), loading: ref(false) };

const { createPaymentMethod, updatePaymentMethod, loading: isSubmitting } = PaymentServices.usePaymentMethodActions();

const statusOptions = [
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' }
];

const schema = yup.object({
    name: yup.string().required("Name is required").min(3, "Name must be at least 3 characters"),
    holder: yup.string().required("Holder name is required").min(2, "Holder name must be at least 2 characters"),
    account_number: yup.string().required("Account number is required").min(4, "Account number must be at least 4 characters"),
    logo: yup.array().nullable().max(1, "Please choose one image only."),
    status: yup.string().required("Status is required").oneOf(['active', 'inactive'], "Invalid status"),
});

const { handleSubmit, setValues } = useForm<PaymentMethodPayload>({
    validationSchema: schema,
});

const { value: name } = useField<string>("name");
const { value: holder } = useField<string>("holder");
const { value: account_number } = useField<string>("account_number");
const { value: logo } = useField<(File | string)[]>("logo");
const { value: status } = useField<string>("status");

watch(fetchedPaymentMethod, (newVal) => {
    if (newVal) {
        setValues({
            name: newVal.name,
            holder: newVal.holder,
            account_number: newVal.account_number,
            logo: newVal.logo_url ? [newVal.logo_url] : [],
            status: newVal.status || 'active',
        });
    }
}, { immediate: true });

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        let response;
        if (isUpdateMode.value) {
            response = await updatePaymentMethod(paymentId, values);
            if (response?.success) {
                toast.success("Payment method updated successfully");
                paymentListRefresh?.();
                router.push({ name: RouteNames.PaymentList });
            }
        } else {
            response = await createPaymentMethod(values);
            if (response?.success) {
                toast.success("Payment method created successfully");
                paymentListRefresh?.();
                router.push({ name: RouteNames.PaymentList });
            }
        }
    } catch (error) {
        console.error('Form submission error:', error);
    }
});
</script>

<template>
    <div v-if="isFetching" class="py-10 text-center">Loading payment method data...</div>

    <form v-else @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <FormInput label="Name" id="name" v-model="name" type="text" placeholder="Enter payment method name"
                    :disabled="isReadMode" />
                <ErrorMessage name="name" class="block text-start text-red-500 text-sm" />
            </div>

            <div>
                <FormInput label="Holder" id="holder" v-model="holder" type="text"
                    placeholder="Enter account holder name" :disabled="isReadMode" />
                <ErrorMessage name="holder" class="block text-start text-red-500 text-sm" />
            </div>

            <div>
                <FormInput label="Account Number" id="account_number" v-model="account_number" type="text"
                    placeholder="Enter account number" :disabled="isReadMode" />
                <ErrorMessage name="account_number" class="block text-start text-red-500 text-sm" />
            </div>

            <div>
                <FormSelect label="Status" id="status" v-model="status" :options="statusOptions"
                    placeholder="Select status" :disabled="isReadMode" />
                <ErrorMessage name="status" class="block text-start text-red-500 text-sm" />
            </div>

            <div class="col-span-2">
                <FormImage id="logo" label="Logo" v-model="logo" accept="image/*"
                    help-text="Upload a logo image for the payment method" :disabled="isReadMode"
                    preview-class="w-full h-32 object-cover" />
                <ErrorMessage name="logo" class="block text-start text-red-500 text-sm" />
            </div>
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="btn-primary"
                :class="{ 'btn-processing': isSubmitting }">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <template v-if="isSubmitting">
                        <svg class="animate-spin h-4 w-4 text-current" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        PROCESSING...
                    </template>
                    <template v-else>
                        {{ isUpdateMode ? "Update Payment Method" : "Create Payment Method" }}
                    </template>
                </span>
            </Button>
        </div>
    </form>
</template>
