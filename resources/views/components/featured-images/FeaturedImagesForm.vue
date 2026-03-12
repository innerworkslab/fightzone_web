<script setup lang="ts">
import { computed } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import {
    FeaturedImagesPayload,
    FeaturedImagesServices,
} from "@/api/FeaturedImage.service";
import { useModalStore } from "@/store/modal";
import Button from "@/components/ui/button/Button.vue";

const modalStore = useModalStore();

const initialValues = modalStore.initialValues;

const imageId = initialValues?.id;

const isUpdateMode = computed(() => !!imageId);

const {
    createFeaturedImage,
    updateFeaturedImage,
    loading: isSubmitting,
} = FeaturedImagesServices.useFeaturedImageActions();

const schema = yup.object({
    image: yup
        .array()
        .of(yup.mixed<File | string>())
        .max(1, "Only one image is allowed")
        .min(1, "At least one image is required")
        .required("Image is required"),
});

const { handleSubmit } = useForm<FeaturedImagesPayload>({
    validationSchema: schema,
    initialValues: {
        image: initialValues?.image_url ? [initialValues.image_url] : [],
    },
});

const { value: image } = useField<(File | string)[]>("image");

const submitForm = handleSubmit(async (values) => {
    try {
        let response;

        if (isUpdateMode.value) {
            response = await updateFeaturedImage(imageId, values);
        } else {
            response = await createFeaturedImage(values);
        }

        if (response?.success) {
            toast.success(response.message ?? "Success");
            modalStore.refreshCallback?.();
            modalStore.closeModal();
        }
    } catch (error) { }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4">
        <FormImage id="featured-image" label="Image" v-model="image" :required="true" />
        <ErrorMessage name="image" class="text-red-500 text-sm" />

        <div class="flex justify-end pt-6">
            <Button type="submit" :disabled="isSubmitting">
                <span v-if="isSubmitting">Saving...</span>
                <span v-else>
                    {{ isUpdateMode ? "Update Image" : "Upload Image" }}
                </span>
            </Button>
        </div>
    </form>
</template>
