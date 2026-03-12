<script setup lang="ts">
import { computed, watch, inject, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { RouteNames } from "@/config/route.config";
import { Button } from "@/components/ui/button";
import {
    FeaturedImagesPayload,
    FeaturedImagesServices,
} from "@/api/FeaturedImage.service";
import { SquareXIcon } from "lucide-vue-next";

const route = useRoute();
const router = useRouter();
const imageId = Number(route.params.id);

const image = ref(null);
const imagePreview = ref("");

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewFeaturedImage);

const { data: fetchedFeaturedImage, loading: isFetching } = isUpdateMode.value
    ? FeaturedImagesServices.useFeaturedImageDetail(imageId)
    : { data: ref(null), loading: ref(false) };

const {
    createFeaturedImage,
    updateFeaturedImage,
    loading: isSubmitting,
} = FeaturedImagesServices.useFeaturedImageActions();

const schema = yup.object({
    image: yup.mixed().nullable().optional(),
});

const { handleSubmit, setValues } = useForm<FeaturedImagesPayload>({
    validationSchema: schema,
    initialValues: {
        image: null,
    },
});

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

watch(
    fetchedFeaturedImage,
    (newVal) => {
        if (newVal) {
            setValues({
                image: null,
            });
        }
    },
    { immediate: true, deep: true },
);

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        if (!image.value) {
            toast.error("Please select an image.");
            return;
        }
        let response;

        const formData = new FormData();

        if (image.value) {
            formData.append("image", image.value);
        }

        if (isUpdateMode.value) {
            response = await updateFeaturedImage(imageId, formData);
        } else {
            response = await createFeaturedImage(formData);
        }

        if (response?.success) {
            toast.success(
                isUpdateMode.value
                    ? "FeaturedImage updated successfully"
                    : "FeaturedImage created successfully",
            );
            router.push({ name: RouteNames.FeaturedImagesList });
        }
    } catch (error) {}
});
</script>

<template>
    <div
        v-if="(isUpdateMode || isReadMode) && isFetching"
        class="py-10 text-center text-primary font-medium"
    >
        Loading image details...
    </div>

    <form v-else @submit.prevent="submitForm" class="space-y-4">
        <div
            v-if="!isReadMode"
            class="w-full flex flex-col justify-center pt-6 gap-5"
        >
            <div v-if="!isReadMode" class="w-full flex justify-center pt-6">
                <div class="space-y-2 max-w-lg flex flex-col items-center">
                    <label class="text-sm font-medium">Image</label>

                    <div class="flex items-center gap-3 mt-2 justify-center">
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
                            class="h-30 w-30 border rounded flex items-center justify-center cursor-pointer bg-gray-50 hover:bg-gray-100"
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

            <div class="w-full flex justify-end">
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
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        Processing...
                    </template>
                    <template v-else>
                        {{
                            isUpdateMode
                                ? "Update Featured Image"
                                : "Create Featured Image"
                        }}
                    </template>
                </Button>
            </div>
        </div>
    </form>
</template>
