<script setup lang="ts">
import { computed, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { useModalStore } from "@/store/modal";
import { useDataStore } from "@/store/data";
import { CourseCategoriesPayload, CourseCategoriesServices } from "@/api/CourseCategories.service";

const modalStore = useModalStore();
const dataStore = useDataStore();

const isUpdateMode = computed(() => !!modalStore.initialValues);
const isReadMode = computed(() => modalStore.isReadMode);

const {
    createCourseCategories,
    updateCourseCategories,
    loading: isSubmitting,
} = CourseCategoriesServices.useCourseCategoriesActions();

const schema = yup.object({
    name: yup.string().required("Name is required"),
    description: yup.string().required("Description is required"),
});

const { handleSubmit, setValues } = useForm<CourseCategoriesPayload>({
    validationSchema: schema,
    initialValues: {
        name: "",
        description: "",
    },
});

const { value: name } = useField<string>("name");
const { value: description } = useField<string>("description");

watch(
    () => modalStore.initialValues,
    (newVal) => {
        if (newVal) {
            setValues({
                name: newVal.name,
                description: newVal.description,
            });
        }
    },
    { immediate: true, deep: true }
);

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        let response;

        if (isUpdateMode.value) {
            response = await updateCourseCategories(modalStore.initialValues.id, values);
        } else {
            response = await createCourseCategories(values);
        }

        if (response?.success) {
            toast.success(
                isUpdateMode.value
                    ? "Course category updated successfully"
                    : "Course category created successfully"
            );

            dataStore.triggerRefresh();
            modalStore.closeModal();
        }
    } catch (error) { }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <FormInput label="Name" id="name" v-model="name" type="text" placeholder="Enter name"
                    :disabled="isReadMode" />
                <ErrorMessage name="name" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput label="Description" id="description" v-model="description" type="text"
                    placeholder="Enter description" :disabled="isReadMode" />
                <ErrorMessage name="description" class="block text-start text-red-500 text-sm mt-1" />
            </div>
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
                    {{ isUpdateMode ? "Update Course Category" : "Create Course Category" }}
                </template>
            </Button>
        </div>
    </form>
</template>
