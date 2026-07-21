<script setup lang="ts">
import { computed, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import {
    TechniqueCategoriesServices,
    type TechniqueCategoryPayload,
} from "@/api/TechniqueCategories.service";

const dataStore = useDataStore();
const modalStore = useModalStore();

const isUpdateMode = computed(() => !!modalStore.initialValues);
const isReadMode = computed(() => modalStore.isReadMode);

const {
    createTechniqueCategory,
    updateTechniqueCategory,
    loading: isSubmitting,
} = TechniqueCategoriesServices.useTechniqueCategoryActions();

const schema = yup.object({
    name: yup.string().required("Name is required"),
});

const { handleSubmit, setValues } = useForm<TechniqueCategoryPayload>({
    validationSchema: schema,
    initialValues: {
        name: "",
    },
});

const { value: name } = useField<string>("name");

watch(
    () => modalStore.initialValues,
    (newVal) => {
        if (!newVal) return;
        setValues({
            name: newVal.name ?? "",
        });
    },
    { immediate: true },
);

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    const response = isUpdateMode.value
        ? await updateTechniqueCategory(modalStore.initialValues.id, values)
        : await createTechniqueCategory(values);

    if (response?.success) {
        toast.success(
            isUpdateMode.value
                ? "Technique category updated successfully"
                : "Technique category created successfully",
        );
        dataStore.triggerRefresh();
        modalStore.closeModal();
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4">
        <div>
            <FormInput id="name" v-model="name" label="Name" type="text" placeholder="Enter category name"
                :disabled="isReadMode" />
            <ErrorMessage name="name" class="block text-start text-red-500 text-sm mt-1" />
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="min-w-[140px]">
                {{ isSubmitting ? "Processing..." : isUpdateMode ? "Update Category" : "Create Category" }}
            </Button>
        </div>
    </form>
</template>
