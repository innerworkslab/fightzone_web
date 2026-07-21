<script setup lang="ts">
import { computed, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { useModalStore } from "@/store/modal";
import { useDataStore } from "@/store/data";
import { TechniqueCategoriesServices } from "@/api/TechniqueCategories.service";
import {
    TechniquesServices,
    type TechniquePayload,
} from "@/api/Techniques.service";

const modalStore = useModalStore();
const dataStore = useDataStore();

const isUpdateMode = computed(() => !!modalStore.initialValues?.id);
const isReadMode = computed(() => modalStore.isReadMode);

const { createTechnique, updateTechnique, loading: isSubmitting } =
    TechniquesServices.useTechniqueActions();

const { data: categoryData } = TechniqueCategoriesServices.useTechniqueCategories({
    is_active: "true",
    limit: 100,
});

const categoryOptions = computed(() => {
    const list = (categoryData.value as any)?.data?.data || (categoryData.value as any)?.data || [];
    return list.map((item: any) => ({
        label: item.name,
        value: item.id,
    }));
});

const schema = yup.object({
    technique_category_id: yup.number().required("Category is required"),
    name: yup.string().nullable(),
    description: yup.string().nullable(),
    url: yup.string().required("Video URL is required").url(),
});

const { handleSubmit, setValues } = useForm<TechniquePayload>({
    validationSchema: schema,
    initialValues: {
        technique_category_id: 0,
        name: "",
        description: "",
        url: "",
    },
});

const { value: techniqueCategoryId } = useField<number>("technique_category_id");
const { value: name } = useField<string>("name");
const { value: description } = useField<string>("description");
const { value: url } = useField<string>("url");

watch(
    () => modalStore.initialValues,
    (val: any) => {
        if (!val) return;

        setValues({
            technique_category_id: val.technique_category_id ?? 0,
            name: val.name ?? "",
            description: val.description ?? "",
            url: val.url ?? "",
        });
    },
    { immediate: true },
);

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    const payload = {
        ...values,
        technique_category_id: Number(values.technique_category_id),
    };

    const response = isUpdateMode.value
        ? await updateTechnique(modalStore.initialValues.id, payload)
        : await createTechnique(payload);

    if (response?.success) {
        toast.success(isUpdateMode.value ? "Technique updated successfully" : "Technique created successfully");
        dataStore.triggerRefresh();
        modalStore.closeModal();
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-5">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <FormSelect id="technique_category_id" v-model="techniqueCategoryId" label="Category"
                    :options="categoryOptions" :disabled="isReadMode" />
                <ErrorMessage name="technique_category_id" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput id="url" v-model="url" label="Video URL" type="text"
                    placeholder="Paste YouTube or Vimeo URL" :disabled="isReadMode" />
                <ErrorMessage name="url" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput id="name" v-model="name" label="Name" type="text"
                    placeholder="Leave blank to use video title" :disabled="isReadMode" />
                <ErrorMessage name="name" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormTextarea id="description" v-model="description" label="Description"
                    :disabled="isReadMode" />
                <ErrorMessage name="description" class="block text-start text-red-500 text-sm mt-1" />
            </div>
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="min-w-[140px]">
                {{ isSubmitting ? "Saving..." : isUpdateMode ? "Update Technique" : "Create Technique" }}
            </Button>
        </div>
    </form>
</template>
