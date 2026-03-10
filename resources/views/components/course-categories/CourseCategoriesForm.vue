<script setup lang="ts">
import { computed, ref, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { useModalStore } from "@/store/modal";
import { useDataStore } from "@/store/data";
import {
    CourseCategoriesPayload,
    CourseCategoriesServices,
} from "@/api/CourseCategories.service";
import { SquareXIcon } from "lucide-vue-next";

const imgBaseUrl = import.meta.env.VITE_IMG_BASE_URL;

const modalStore = useModalStore();
const dataStore = useDataStore();

const isUpdateMode = computed(() => !!modalStore.initialValues);
const isReadMode = computed(() => modalStore.isReadMode);

const image = ref(null);
const imagePreview = ref("");

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
            imagePreview.value = imgBaseUrl + newVal.image_url;
        }
    },
    { immediate: true, deep: true },
);

const onImageChange = (e: any) => {
    const file = e.target.files[0];
    if (!file) return;

    image.value = file;
    imagePreview.value = URL.createObjectURL(file);
};

const removeImage = () => {
    image.value = null;
    imagePreview.value = "";
};

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        const formData = new FormData();

        formData.append("name", values.name);
        formData.append("description", values.description);

        if (image.value) {
            formData.append("image", image.value);
        }

        let response;

        if (isUpdateMode.value) {
            response = await updateCourseCategories(
                modalStore.initialValues.id,
                formData,
            );
        } else {
            response = await createCourseCategories(formData);
        }

        if (response?.success) {
            toast.success(
                isUpdateMode.value
                    ? "Course category updated successfully"
                    : "Course category created successfully",
            );

            dataStore.triggerRefresh();
            modalStore.closeModal();
        }
    } catch (error) {}
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 gap-4">
            <div>
                <FormInput
                    label="Name"
                    id="name"
                    v-model="name"
                    type="text"
                    placeholder="Enter name"
                    :disabled="isReadMode"
                />
                <ErrorMessage
                    name="name"
                    class="block text-start text-red-500 text-sm mt-1"
                />
            </div>

            <div>
                <FormInput
                    label="Description"
                    id="description"
                    v-model="description"
                    type="text"
                    placeholder="Enter description"
                    :disabled="isReadMode"
                />
                <ErrorMessage
                    name="description"
                    class="block text-start text-red-500 text-sm mt-1"
                />
            </div>

            <div class="space-y-2 max-w-lg">
                <label class="text-sm font-medium"> Image</label>
                <input type="file" class="hidden" @change="onImageChange" />

                <div class="flex items-center gap-3 mt-2">
                    <div v-if="imagePreview" class="relative inline-block">
                        <img
                            :src="imagePreview"
                            class="h-30 w-30 object-cover rounded border"
                        />
                        <button
                            @click="removeImage"
                            type="button"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-sm h-6 w-6 flex items-center justify-center"
                        >
                            <SquareXIcon />
                        </button>
                    </div>

                    <label
                        v-else
                        class="h-30 w-30 border rounded flex items-center justify-center cursor-pointer bg-gray-50 hover:bg-gray-100 mt-2"
                    >
                        <span class="text-2xl">＋</span>
                        <input
                            type="file"
                            class="hidden"
                            @change="onImageChange"
                        />
                    </label>
                </div>
            </div>
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button
                variant="default"
                type="submit"
                :disabled="isSubmitting"
                class="min-w-[140px]"
            >
                <template v-if="isSubmitting">
                    <svg
                        class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        />
                    </svg>
                    Processing...
                </template>

                <template v-else>
                    {{
                        isUpdateMode
                            ? "Update Course Category"
                            : "Create Course Category"
                    }}
                </template>
            </Button>
        </div>
    </form>
</template>
