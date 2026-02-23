<script setup lang="ts">
import { computed, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import FormInput from "../common/formControl/FormInput.vue";
import { useRouter } from "vue-router";
import { RouteNames } from "@/config/route.config";
import { useModalStore } from "@/store/modal";
import { LessonDayVideosServices } from "@/api/LessonDayVideos.service";

const router = useRouter();
const modalStore = useModalStore();

const isUpdateMode = computed(() => !!modalStore.initialValues?.videoId);
const isReadMode = computed(() => modalStore.isReadMode);

const lessonDayId = computed(() =>
    Number(modalStore.initialValues?.id)
);

const videoId = computed(() =>
    Number(modalStore.initialValues?.videoId)
);

const createSchema = yup.object({
    lesson_day_id: yup.number().required(),
    url: yup.string().required("Youtube URL is required").url(),
});

const updateSchema = yup.object({
    name: yup.string().required("Name is required"),
    description: yup.string().required("Description is required"),
    duration: yup
        .string()
        .required("Duration is required")
        .matches(
            /^([0-1]\d|2[0-3]):([0-5]\d):([0-5]\d)$/,
            "Format must be HH:mm:ss"
        ),
    url: yup.string().required("Youtube URL is required").url(),
});

const { handleSubmit, setValues } = useForm({
    validationSchema: computed(() =>
        isUpdateMode.value ? updateSchema : createSchema
    ),
    initialValues: {
        lesson_day_id: lessonDayId.value,
        name: "",
        description: "",
        duration: "00:00:00",
        url: "",
    },
});

const { value: name } = useField<string>("name");
const { value: description } = useField<string>("description");
const { value: duration } = useField<string>("duration");
const { value: url } = useField<string>("url");

const {
    createLessonDayVideo,
    updateLessonDayVideo,
    loading: isSubmitting,
} = LessonDayVideosServices.useLessonDayVideosActions();

const { data: videoDetail } = isUpdateMode.value
    ? LessonDayVideosServices.useLessonDayVideos(videoId.value.toString())
    : { data: null };

watch(
    () => videoDetail?.value,
    (val: any) => {
        if (val?.data) {
            setValues({
                name: val.data.name,
                description: val.data.description,
                duration: val.data.duration,
                url: val.data.url,
            });
        }
    },
    { immediate: true }
);

const submitForm = handleSubmit(async (values: any) => {
    const response = isUpdateMode.value
        ? await updateLessonDayVideo(videoId.value, values)
        : await createLessonDayVideo({
            lesson_day_id: lessonDayId.value,
            url: values.url,
        });

    if (response?.success) {
        toast.success(response.message ?? "Success");
        modalStore.closeModal();
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid grid-cols-1 gap-4" :class="isUpdateMode ? 'md:grid-cols-2' : ''">
            <div v-if="!isUpdateMode">
                <FormInput id="url" v-model="url" label="Youtube URL" :disabled="isReadMode" />
                <ErrorMessage name="url" class="text-red-500 text-sm" />
            </div>

            <template v-else>
                <div>
                    <FormInput id="name" v-model="name" label="Video Name" :disabled="isReadMode" />
                    <ErrorMessage name="name" class="text-red-500 text-sm" />
                </div>

                <div>
                    <FormInput id="duration" v-model="duration" label="Duration (HH:mm:ss)" :disabled="isReadMode" />
                    <ErrorMessage name="duration" class="text-red-500 text-sm" />
                </div>

                <div class="md:col-span-2">
                    <FormInput id="description" v-model="description" label="Description" :disabled="isReadMode" />
                    <ErrorMessage name="description" class="text-red-500 text-sm" />
                </div>

                <div class="md:col-span-2">
                    <FormInput id="url" v-model="url" label="Youtube URL" :disabled="isReadMode" />
                    <ErrorMessage name="url" class="text-red-500 text-sm" />
                </div>
            </template>
        </div>

        <div class="flex justify-end pt-6" v-if="!isReadMode">
            <Button type="submit" :disabled="isSubmitting">
                <span v-if="isSubmitting">Saving...</span>
                <span v-else>
                    {{ isUpdateMode ? "Update Video" : "Add Video" }}
                </span>
            </Button>
        </div>
    </form>
</template>
