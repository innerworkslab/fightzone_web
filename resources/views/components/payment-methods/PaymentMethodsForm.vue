<script setup lang="ts">
import { computed, watch, inject, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { RouteNames } from "@/config/route.config";
import { PaymentMethodsPayload, PaymentMethodsServices } from "@/api/Payments.service";
import { Button } from "@/components/ui/button";

const route = useRoute();
const router = useRouter();
const paymentId = Number(route.params.id);

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewPayment);

const { data: fetchedPaymentMethod, loading: isFetching } = isUpdateMode.value
    ? PaymentMethodsServices.usePaymentMethodDetail(paymentId)
    : { data: ref(null), loading: ref(false) };

const { createPaymentMethod, updatePaymentMethod, loading: isSubmitting } = PaymentMethodsServices.usePaymentMethodActions();


const schema = yup.object({
    name: yup.string().required("Name is required").min(3, "Name must be at least 3 characters"),
    holder: yup.string().required("Holder name is required").min(2, "Holder name must be at least 2 characters"),
    account_number: yup.string().required("Account number is required").min(4, "Account number must be at least 4 characters"),
    logo: yup.array().nullable().max(1, "Please choose one image only."),
    qr: yup.array().nullable().max(1, "Please choose one image only."),
});

const { handleSubmit, setValues } = useForm<PaymentMethodsPayload>({
    validationSchema: schema,
    initialValues: {
        name: "",
        holder: "",
        account_number: "",
        logo: [],
        qr: []
    }
});

const { value: name } = useField<string>("name");
const { value: holder } = useField<string>("holder");
const { value: account_number } = useField<string>("account_number");
const { value: logo } = useField<(File | string)[]>("logo");
const { value: qr } = useField<(File | string)[]>("qr");

watch(fetchedPaymentMethod, (newVal) => {

    if (newVal) {
        setValues({
            name: newVal.data.name,
            holder: newVal.data.holder,
            account_number: newVal.data.account_number,
            logo: newVal.data.logo_url ? [newVal.data.logo_url] : [],
            qr: newVal.data.qr_url ? [newVal.data.qr_url] : [],
        });
    }
}, { immediate: true, deep: true });

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        let response;

        if (isUpdateMode.value) {
            response = await updatePaymentMethod(paymentId, values);
        } else {
            response = await createPaymentMethod(values);
        }

        if (response?.success) {
            toast.success(isUpdateMode.value ? "Payment method updated successfully" : "Payment method created successfully");
            router.push({ name: RouteNames.PaymentMethodsList });
        }
    } catch (error) {
        console.error('Form submission error:', error);
    }
});
</script>

<template>
    <div v-if="(isUpdateMode || isReadMode) && isFetching" class="py-10 text-center text-primary font-medium">
        Loading payment method details...
    </div>

    <form v-else @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <FormInput label="Name" id="name" v-model="name" type="text" placeholder="Enter payment method name"
                    :disabled="isReadMode" />
                <ErrorMessage name="name" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput label="Holder" id="holder" v-model="holder" type="text"
                    placeholder="Enter account holder name" :disabled="isReadMode" />
                <ErrorMessage name="holder" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput label="Account Number" id="account_number" v-model="account_number" type="password"
                    placeholder="Enter account number" :disabled="isReadMode" />
                <ErrorMessage name="account_number" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div class="col-span-2">
                <FormImage id="logo" label="Logo" v-model="logo" accept="image/*"
                    help-text="Upload a logo image for the payment method" :disabled="isReadMode"
                    preview-class="w-full h-32 object-cover" />
                <ErrorMessage name="logo" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div class="col-span-2">
                <FormImage id="qr" label="QR Code" v-model="qr" accept="image/*"
                    help-text="Upload a QR code image for the payment method" :disabled="isReadMode"
                    preview-class="w-32 h-32 object-cover" />
                <ErrorMessage name="qr" class="block text-start text-red-500 text-sm mt-1" />
            </div>
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="min-w-[200px]">
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
                    {{ isUpdateMode ? "Update Payment Method" : "Create Payment Method" }}
                </template>
            </Button>
        </div>
    </form>
</template>
