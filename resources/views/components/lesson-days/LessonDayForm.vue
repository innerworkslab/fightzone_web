<script setup lang="ts">
import { computed, watch } from "vue";
import * as yup from "yup";
import { useForm } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import FormInput from "../common/formControl/FormInput.vue";
import FormSelect from "../common/formControl/FormSelect.vue";
import { useModalStore } from "@/store/modal";
import { LessonDaysServices } from "@/api/LessonDays.service";
import { useRoute, useRouter } from "vue-router";
import { RouteNames } from "@/config/route.config";

const route = useRoute();
const router = useRouter();

const modalStore = useModalStore();

const isUpdateMode = computed(() => route.name === RouteNames.EditLessonDay);
const isReadMode = computed(() => route.name === RouteNames.ViewLessonDay);
const lessonDayId = computed(() => route.params.id as string);
const courseLevelId = computed(() => route.params.courseLevelId as string);

const schema = yup.object({
    day_number: yup
        .number()
        .transform((value) => (isNaN(value) ? undefined : Number(value)))
        .typeError("Day number is required")
        .required("Day number is required")
        .min(1),
    duration: yup
        .string()
        .required("Duration is required")
        .matches(
            /^([0-1]\d|2[0-3]):([0-5]\d):([0-5]\d)$/,
            "Format must be HH:mm:ss"
        ),
    type: yup
        .mixed<"Lesson" | "Rest">()
        .oneOf(["Lesson", "Rest"])
        .required("Type is required"),
});

const { handleSubmit, setValues, defineField, errors } =
    useForm({
        validationSchema: schema,
        initialValues: {
            day_number: 1,
            duration: "00:00:00",
            type: "Lesson",
        },
    });

const [dayNumber] = defineField("day_number");
const [duration] = defineField("duration");
const [type] = defineField("type");

const {
    createLessonDay,
    updateLessonDay,
    loading: isSubmitting,
} = LessonDaysServices.useLessonDaysActions();

const { data: courseData } =
    LessonDaysServices.useLessonDays(courseLevelId.value);

watch(
    () => courseData?.value,
    (val: any) => {
        const nextDayNumber = val?.data?.length ? val.data.length + 1 : 1;

        if (val?.data) {
            setValues({
                day_number: nextDayNumber,
            });
        }
    },
    { immediate: true }
);

const { data: lessonDayDetail } = isUpdateMode.value
    ? LessonDaysServices.useLessonDayDetail(lessonDayId.value)
    : { data: null };

watch(
    () => lessonDayDetail?.value,
    (val: any) => {
        if (val?.data) {
            setValues({
                day_number: val.data.day_number,
                duration: val.data.duration,
                type: val.data.type,
            });
        }
    },
    { immediate: true }
);

watch(
    () => modalStore.initialValues,
    (val: any) => {
        console.log("initial", val);

        if (val) {
            setValues({
                day_number: val.day_number,
                duration: val.duration,
                type: val.type,
            });
        }
    },
    { immediate: true }
);

const submitForm = handleSubmit(async (values) => {
    const payload = {
        course_level_id: courseLevelId.value,
        day_number: Number(values.day_number),
        duration: values.duration,
        type: values.type,
    };

    const response = isUpdateMode.value
        ? await updateLessonDay(courseLevelId.value, Number(lessonDayId.value), payload)
        : await createLessonDay(courseLevelId.value, payload);

    if (response?.success) {
        toast.success(response.message ?? "Success");
        router.push({ name: RouteNames.LessonDaysList, params: { courseLevelId: courseLevelId.value } });
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="flex flex-col gap-1">
                <FormInput id="day_number" type="number" v-model="dayNumber" label="Day Number"
                    :disabled="isReadMode" />
                <span v-if="errors.day_number" class="text-red-500 text-xs">
                    {{ errors.day_number }}
                </span>
            </div>

            <div class="flex flex-col gap-1">
                <FormInput id="duration" v-model="duration" label="Duration (HH:mm:ss)" :disabled="isReadMode" />
                <span v-if="errors.duration" class="text-red-500 text-xs">
                    {{ errors.duration }}
                </span>
            </div>

            <div class="flex flex-col gap-1">
                <FormSelect id="type" v-model="type" label="Type" :options="[
                    { label: 'Lesson', value: 'Lesson' },
                    { label: 'Rest', value: 'Rest' }
                ]" :disabled="isReadMode" />
                <span v-if="errors.type" class="text-red-500 text-xs">
                    {{ errors.type }}
                </span>
            </div>

        </div>

        <div v-if="!isReadMode" class="flex justify-end pt-6">
            <Button type="submit" :disabled="isSubmitting">
                <span v-if="isSubmitting">Processing...</span>
                <span v-else>
                    {{ isUpdateMode ? "Update Lesson Day" : "Create Lesson Day" }}
                </span>
            </Button>
        </div>

    </form>
</template>
