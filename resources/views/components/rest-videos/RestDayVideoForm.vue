<script setup lang="ts">
import { computed, onMounted, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { RestDayVideosServices } from "@/api/RestDayVideos.service";
import { useRoute, useRouter } from "vue-router";
import { RouteNames } from "@/config/route.config";

const router = useRouter();
const route = useRoute();

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewRestDayVideo);

const schema = yup.object({
    url: yup.string().required("Video link is required"),
});

const { handleSubmit, resetForm, setValues } = useForm({
    validationSchema: schema,
    initialValues: {
        url: "",
    },
});

const { value: url } = useField<string>("url");

const {
    createRestDayVideo,
    updateRestDayVideo,
    loading: isSubmitting,
} = RestDayVideosServices.useRestDayVideoActions();

const { data: RestDayVideoDetail, loading: isFetching } = isUpdateMode.value
    ? RestDayVideosServices.useRestDayVideoDetail(route.params.id as string)
    : { data: null, loading: false };

watch(
    () => RestDayVideoDetail?.value,
    (val: any) => {
        if (val?.data) {
            setValues({
                url: val.data.url,
            });
        }
    },
    { immediate: true },
);

const submitForm = handleSubmit(async (payload) => {
    const response = isUpdateMode.value
        ? await updateRestDayVideo(route.params.id as string, payload)
        : await createRestDayVideo(payload);

    if (response?.success) {
        toast.success(response.message ?? "Success");
        router.push({ name: RouteNames.RestDayVideosList });
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="space-y-4 grid grid-cols-2 gap-3">
            <div>
                <FormInput
                    id="url"
                    v-model="url"
                    label="Video Link"
                    :disabled="isReadMode"
                />
                <ErrorMessage name="url" class="text-red-500 text-sm" />
            </div>
        </div>

        <div class="flex justify-end pt-6">
            <Button type="submit" :disabled="isSubmitting || isFetching">
                <span v-if="isSubmitting">Saving...</span>
                <span v-else>
                    {{ isUpdateMode ? "Update Video" : "Create Video" }}
                </span>
            </Button>
        </div>
    </form>
</template>
