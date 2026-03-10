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
});

const { handleSubmit, setValues, defineField, errors } = useForm({
    validationSchema: schema,
    initialValues: {
        day_number: 1,
    },
});

const [dayNumber] = defineField("day_number");

const {
    createLessonDay,
    updateLessonDay,
    loading: isSubmitting,
} = LessonDaysServices.useLessonDaysActions();

const { data: courseData } = LessonDaysServices.useLessonDays(
    courseLevelId.value,
);

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
    { immediate: true },
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
            });
        }
    },
    { immediate: true },
);

watch(
    () => modalStore.initialValues,
    (val: any) => {
        console.log("initial", val);

        if (val) {
            setValues({
                day_number: val.day_number,
            });
        }
    },
    { immediate: true },
);

const submitForm = handleSubmit(async (values) => {
    const payload = {
        course_level_id: courseLevelId.value,
        day_number: Number(values.day_number),
    };

    const response = isUpdateMode.value
        ? await updateLessonDay(
              courseLevelId.value,
              Number(lessonDayId.value),
              payload,
          )
        : await createLessonDay(courseLevelId.value, payload);

    if (response?.success) {
        toast.success(response.message ?? "Success");
        router.push({
            name: RouteNames.LessonDaysList,
            params: { courseLevelId: courseLevelId.value },
        });
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col gap-1">
                <FormInput
                    id="day_number"
                    type="number"
                    v-model="dayNumber"
                    label="Day Number"
                    :disabled="isReadMode"
                />
                <span v-if="errors.day_number" class="text-red-500 text-xs">
                    {{ errors.day_number }}
                </span>
            </div>
        </div>

        <div v-if="!isReadMode" class="flex justify-end pt-6">
            <Button type="submit" :disabled="isSubmitting">
                <span v-if="isSubmitting">Processing...</span>
                <span v-else>
                    {{
                        isUpdateMode ? "Update Lesson Day" : "Create Lesson Day"
                    }}
                </span>
            </Button>
        </div>
    </form>
</template>
