<script setup lang="ts">
import { computed, onMounted, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { CourseLevelsPayload, CourseLevelsServices } from "@/api/CourseLevels.service";
import { CoursesServices } from "@/api/Courses.service";
import { useRoute, useRouter } from "vue-router";
import { RouteNames } from "@/config/route.config";

const router = useRouter();
const route = useRoute();

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewCourseLevel);
const courseId = computed(() => Number(route.params.courseId));

const schema = yup.object({
    course_id: yup.number().required(),
    level: yup
        .mixed<"Beginner" | "Intermediate" | "Expert">()
        .oneOf(["Beginner", "Intermediate", "Expert"])
        .required(),
    price: yup.number().required().min(1, "Price must be greater than 1"),
});

const { handleSubmit, setValues } = useForm<CourseLevelsPayload>({
    validationSchema: schema,
    initialValues: {
        course_id: courseId.value,
        level: "Beginner",
        price: 0,
    },
});

const { value: level } =
    useField<"Beginner" | "Intermediate" | "Expert">("level");
const { value: price } = useField<number>("price");

const { createCourseLevel, updateCourseLevel, loading: isSubmitting } =
    CourseLevelsServices.useCourseLevelActions();

const { data: levelDetail, loading: isFetching } = isUpdateMode.value
    ? CourseLevelsServices.useCourseLevelDetail(route.params.id as string)
    : { data: null, loading: false };

watch(
    () => levelDetail?.value,
    (val: any) => {
        if (val?.data) {
            setValues({
                course_id: val.data.course_id,
                level: val.data.level,
                price: val.data.price,
            });
        }
    },
    { immediate: true }
);

const submitForm = handleSubmit(async (payload) => {
    const response = isUpdateMode.value
        ? await updateCourseLevel(Number(route.params.id), payload)
        : await createCourseLevel(payload);

    if (response?.success) {
        toast.success(response.message ?? "Success");
        router.push({ name: RouteNames.CoursesList });
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="space-y-4 grid grid-cols-2 gap-3">
            <div>
                <FormSelect id="level" v-model="level" label="Level" :options="[
                    { label: 'Beginner', value: 'Beginner' },
                    { label: 'Intermediate', value: 'Intermediate' },
                    { label: 'Expert', value: 'Expert' },
                ]" :disabled="isReadMode" />
                <ErrorMessage name="level" class="text-red-500 text-sm" />
            </div>

            <div>
                <FormInput id="price" type="number" v-model="price" label="Price" :disabled="isReadMode" />
                <ErrorMessage name="price" class="text-red-500 text-sm" />
            </div>
        </div>

        <div class="flex justify-end pt-6" v-if="!isReadMode">
            <Button type="submit" :disabled="isSubmitting || isFetching">
                <span v-if="isSubmitting">Saving...</span>
                <span v-else>
                    {{ isUpdateMode ? "Update Level" : "Create Level" }}
                </span>
            </Button>
        </div>
    </form>
</template>
