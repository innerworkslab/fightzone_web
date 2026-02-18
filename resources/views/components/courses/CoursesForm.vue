<script setup lang="ts">
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { CoursesServices, CoursePayload } from "@/api/Courses.service";
import { useModalStore } from "@/store/modal";

const modalStore = useModalStore();

const schema = yup.object({
    name: yup.string().required().min(3),
    course_category_id: yup.number().required(),
    level: yup
        .mixed<"beginner" | "intermediate" | "expert">()
        .oneOf(["beginner", "intermediate", "expert"])
        .required(),
    price: yup.number().required().min(0),
});

const { handleSubmit, resetForm } = useForm<CoursePayload>({
    validationSchema: schema,
    initialValues: {
        name: "",
        course_category_id: 1,
        level: "beginner",
        price: 0,
    },
});

const { value: name } = useField<string>("name");
const { value: course_category_id } =
    useField<number>("course_category_id");
const { value: level } =
    useField<"beginner" | "intermediate" | "expert">("level");
const { value: price } = useField<number>("price");

const { createCourse, loading: isSubmitting } =
    CoursesServices.useCourseActions();

const submitForm = handleSubmit(async (payload) => {
    const response = await createCourse(payload);
    if (response?.success) {
        toast.success(response.message ?? "Course created successfully");
        modalStore.triggerRefresh();
        modalStore.closeModal();
        resetForm();
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="space-y-4 grid grid-cols-2 gap-3">
            <div>
                <FormInput id="name" v-model="name" label="Course Name" />
                <ErrorMessage name="name" class="text-red-500 text-sm" />
            </div>

            <div>
                <FormInput id="category" type="number" v-model="course_category_id" label="Category ID" />
                <ErrorMessage name="course_category_id" class="text-red-500 text-sm" />
            </div>

            <div>
                <FormSelect id="level" v-model="level" label="Level" :options="[
                    { label: 'Beginner', value: 'beginner' },
                    { label: 'Intermediate', value: 'intermediate' },
                    { label: 'Expert', value: 'expert' },
                ]" />
                <ErrorMessage name="level" class="text-red-500 text-sm" />
            </div>

            <div>
                <FormInput id="price" type="number" v-model="price" label="Price" />
                <ErrorMessage name="price" class="text-red-500 text-sm" />
            </div>
        </div>

        <div class="flex justify-end pt-6">
            <Button type="submit" :disabled="isSubmitting">
                <span v-if="isSubmitting">Saving...</span>
                <span v-else>Create Course</span>
            </Button>
        </div>
    </form>
</template>
