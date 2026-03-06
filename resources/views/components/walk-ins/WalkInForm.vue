<script setup lang="ts">
import { computed, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { useModalStore } from "@/store/modal";
import { useDataStore } from "@/store/data";
import { WalkInsServices } from "@/api/WalkIns.service";

const modalStore = useModalStore();
const dataStore = useDataStore();

const isReadMode = computed(() => modalStore.isReadMode);

const {
    confirmWalkIns,
    loading: isSubmitting,
} = WalkInsServices.useWalkInsActions();

const schema = yup.object({
    qr_payload: yup.string().required("QR token is required"),
});

const { handleSubmit, setValues } = useForm({
    validationSchema: schema,
    initialValues: {
        qr_payload: "",
    },
});

const { value: qr_payload } = useField<string>("qr_payload");

watch(
    () => modalStore.initialValues,
    (newVal) => {
        if (newVal) {
            setValues({
                qr_payload: newVal.qr_payload,
            });
        }
    },
    { immediate: true, deep: true }
);

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    const response = await confirmWalkIns(values);

    if (response?.success) {
        toast.success(response.message || "Walk-in confirmed successfully");
        dataStore.triggerRefresh();
        modalStore.closeModal();
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4">
        <div>
            <FormTextarea label="QR Token" id="qr_payload" v-model="qr_payload"
                placeholder="Paste scanned QR code token" :disabled="isReadMode" />
            <ErrorMessage name="qr_payload" class="block text-start text-red-500 text-sm mt-1" />
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="min-w-[140px]">
                <template v-if="isSubmitting">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Processing...
                </template>

                <template v-else>
                    Confirm Work-In
                </template>
            </Button>
        </div>
    </form>
</template>
