<script setup lang="ts">
import { computed, watch, ref } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { PackagesPayload, PackagesServices } from "@/api/Packages.service";
import { Button } from "@/components/ui/button";
import { useModalStore } from "@/store/modal";

const modalStore = useModalStore();
const isUpdateMode = computed(() => !!modalStore.initialValues);
const isReadMode = computed(() => modalStore.isReadMode);

const { data: fetchedPackage, loading: isFetching } = isUpdateMode.value
    ? PackagesServices.usePackagesDetail(modalStore.initialValues.id)
    : { data: ref(null), loading: ref(false) };

const { createPackages, updatePackages, loading: isSubmitting } = PackagesServices.usePackagesActions();

const schema = yup.object({
    name: yup.string().required("Package name is required"),
    price: yup.number().required("Price is required").min(0, "Price must be positive"),
    days: yup.number().required("Days is required").min(1, "Days must be at least 1"),
});

const { handleSubmit, setValues } = useForm<PackagesPayload>({
    validationSchema: schema,
    initialValues: {
        name: "",
        price: 0,
        days: 1,
    }
});

const { value: name } = useField<string>("name");
const { value: price } = useField<number>("price");
const { value: days } = useField<number>("days");

watch(fetchedPackage, (newVal) => {
    if (newVal) {
        setValues({
            name: newVal.data.name,
            price: newVal.data.price,
            days: newVal.data.days,
        });
    }
}, { immediate: true, deep: true });

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        let response;

        if (isUpdateMode.value) {
            response = await updatePackages(modalStore.initialValues.id, values);
        } else {
            response = await createPackages(values);
        }

        if (response?.success) {
            toast.success(isUpdateMode.value ? "Package updated successfully" : "Package created successfully");
            modalStore.triggerRefresh();
            modalStore.closeModal();
        }
    } catch (error) { }
});
</script>

<template>
    <div v-if="(isUpdateMode || isReadMode) && isFetching" class="py-10 text-center text-primary font-medium">
        Loading package details...
    </div>

    <form v-else @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <FormInput label="Package Name" id="name" v-model="name" type="text" placeholder="Enter package name"
                    :disabled="isReadMode" />
                <ErrorMessage name="name" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput label="Price" id="price" v-model.number="price" type="number" step="0.01" min="0"
                    placeholder="Enter price" :disabled="isReadMode" />
                <ErrorMessage name="price" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput label="Days" id="days" v-model.number="days" type="number" min="1"
                    placeholder="Enter number of days" :disabled="isReadMode" />
                <ErrorMessage name="days" class="block text-start text-red-500 text-sm mt-1" />
            </div>
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="min-w-[140px]">
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
                    {{ isUpdateMode ? "Update Package" : "Create Package" }}
                </template>
            </Button>
        </div>
    </form>
</template>
