<script setup lang="ts">
import { computed, onMounted, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import FormInput from "../common/formControl/FormInput.vue";
import { useModalStore } from "@/store/modal";
import { LessonDayVideosServices } from "@/api/LessonDayVideos.service";
import { RestDayVideosServices } from "@/api/RestDayVideos.service";

const modalStore = useModalStore();

const isUpdateMode = computed(() => !!modalStore.initialValues?.name);
const isReadMode = computed(() => modalStore.isReadMode);

const lessonDayId = computed(() =>
    Number(modalStore.initialValues?.lesson_day_id),
);

const videoId = computed(() => Number(modalStore.initialValues?.id));

const createSchema = yup.object({
    lesson_day_id: yup.number().required(),
    url: yup.string().required("Youtube URL is required").url(),
    type: yup.string().required("Type is required"),
});

const updateSchema = yup.object({
    name: yup.string().required("Name is required"),
    description: yup.string().required("Description is required"),
    url: yup.string().required("Youtube URL is required").url(),
    type: yup.string().required("Type is required"),
});

const { handleSubmit, setValues } = useForm({
    validationSchema: computed(() =>
        isUpdateMode.value ? updateSchema : createSchema,
    ),
    initialValues: {
        lesson_day_id: 0,
        name: "",
        description: "",
        url: "",
        type: "",
    },
});

const { value: name } = useField<string>("name");
const { value: type } = useField<string>("type");
const { value: video } = useField<string>("video");
const { value: description } = useField<string>("description");
const { value: url } = useField<string>("url");

const {
    createLessonDayVideo,
    updateLessonDayVideo,
    loading: isSubmitting,
} = LessonDayVideosServices.useLessonDayVideosActions();

const {
    data: restVideoData,
    loading: restVideoLoading,
    refresh: fetchRestDayVideos,
} = RestDayVideosServices.useRestDayVideos({
    page: 1,
    limit: 100,
});

const restVideoOptions = computed(() => {
    const list = (restVideoData.value as any)?.data?.data || [];
    return list.map((item: any) => ({
        label: item.name,
        value: item.url,
    }));
});

onMounted(() => {
    fetchRestDayVideos({ page: 1, limit: 100 });
});

watch(
    () => modalStore.initialValues,
    (val: any) => {
        if (!val) return;

        setValues({
            lesson_day_id: val.lesson_day_id ?? 0,
            name: val.name ?? "",
            description: val.description ?? "",
            url: val.url ?? "",
            type: val.type ?? "",
        });
    },
    { immediate: true },
);

watch(video, () => {
    url.value = video.value;
});

const submitForm = handleSubmit(async (values: any) => {
    const response = isUpdateMode.value
        ? await updateLessonDayVideo(videoId.value, values)
        : await createLessonDayVideo({
              lesson_day_id: lessonDayId.value,
              url: values.url,
              type: values.type,
          });

    if (response?.success) {
        toast.success(response.message ?? "Success");
        modalStore.refreshCallback?.();
        modalStore.closeModal();
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid grid-cols-1 gap-4">
            <div v-if="!isUpdateMode">
                <div class="flex flex-col gap-1">
                    <FormSelect
                        id="type"
                        v-model="type"
                        label="Type"
                        :options="[
                            { label: 'Lesson', value: 'Lesson' },
                            { label: 'Rest', value: 'Rest' },
                        ]"
                        :disabled="isReadMode"
                    />
                    <ErrorMessage name="type" class="text-red-500 text-sm" />
                </div>
                <div v-if="type === 'Rest'">
                    <FormSelect
                        id="video"
                        v-model="video"
                        label="Video"
                        :options="restVideoOptions"
                        :loading="restVideoLoading"
                        :disabled="isReadMode"
                    />
                    <ErrorMessage name="video" class="text-red-500 text-sm" />
                </div>
                <FormInput
                    id="url"
                    v-model="url"
                    label="Youtube URL"
                    :disabled="isReadMode || type === 'Rest'"
                />
                <ErrorMessage name="url" class="text-red-500 text-sm" />
            </div>

            <template v-else>
                <div>
                    <FormInput
                        id="name"
                        v-model="name"
                        label="Video Name"
                        :disabled="isReadMode"
                    />
                    <ErrorMessage name="name" class="text-red-500 text-sm" />
                </div>

                <div class="flex flex-col gap-1">
                    <FormSelect
                        id="type"
                        v-model="type"
                        label="Type"
                        :options="[
                            { label: 'Lesson', value: 'Lesson' },
                            { label: 'Rest', value: 'Rest' },
                        ]"
                        :disabled="isReadMode"
                    />
                    <ErrorMessage name="type" class="text-red-500 text-sm" />
                </div>

                <div v-if="type === 'Rest'">
                    <FormSelect
                        id="video"
                        v-model="video"
                        label="Video"
                        :options="restVideoOptions"
                        :loading="restVideoLoading"
                        :disabled="isReadMode"
                    />
                    <ErrorMessage name="video" class="text-red-500 text-sm" />
                </div>

                <div>
                    <FormInput
                        id="url"
                        v-model="url"
                        label="Youtube URL"
                        :disabled="isReadMode || type === 'Rest'"
                    />
                    <ErrorMessage name="url" class="text-red-500 text-sm" />
                </div>

                <div>
                    <FormTextarea
                        id="description"
                        v-model="description"
                        label="Description"
                        :disabled="isReadMode"
                    />
                    <ErrorMessage
                        name="description"
                        class="text-red-500 text-sm"
                    />
                </div>
            </template>
        </div>

        <div class="flex justify-end pt-6" v-if="!isReadMode">
            <Button type="submit" :disabled="isSubmitting">
                <span v-if="isSubmitting">Saving...</span>
                <span v-else>
                    {{ isUpdateMode ? "Update Video" : "Upload Video" }}
                </span>
            </Button>
        </div>
    </form>
</template>
