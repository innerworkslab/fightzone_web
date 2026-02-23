<script setup lang="ts">
import { computed, onMounted, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { CoursesServices } from "@/api/Courses.service";
import { CourseCategoriesServices } from "@/api/CourseCategories.service";
import { useRoute, useRouter } from "vue-router";
import { RouteNames } from "@/config/route.config";

const router = useRouter();
const route = useRoute();

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewCourse);

const schema = yup.object({
    name: yup.string().required().min(3),
    course_category_id: yup.number().required(),
    description: yup.string().nullable(),
});

const { handleSubmit, resetForm, setValues } = useForm({
    validationSchema: schema,
    initialValues: {
        name: "",
        course_category_id: undefined as unknown as number,
        description: "",
    },
});

const { value: name } = useField<string>("name");
const { value: course_category_id } = useField<number>("course_category_id");
const { value: description } = useField<string>("description");

const { createCourse, updateCourse, loading: isSubmitting } =
    CoursesServices.useCourseActions();

const { data: courseDetail, loading: isFetching } = isUpdateMode.value
    ? CoursesServices.useCourseDetail(route.params.id as string)
    : { data: null, loading: false };

watch(
    () => courseDetail?.value,
    (val: any) => {
        if (val?.data) {
            setValues({
                name: val.data.name,
                course_category_id: val.data.course_category_id,
                description: val.data.description,
            });
        }
    },
    { immediate: true }
);

const {
    data: categoryData,
    loading: categoryLoading,
    refresh: fetchCategories,
} = CourseCategoriesServices.useCourseCategoriess({
    page: 1,
    limit: 100,
});

onMounted(() => {
    fetchCategories({ page: 1, limit: 100 });
});

const categoryOptions = computed(() => {
    const list = (categoryData.value as any)?.data?.data || [];
    return list.map((item: any) => ({
        label: item.name,
        value: item.id,
    }));
});

const submitForm = handleSubmit(async (payload) => {
    const response = isUpdateMode.value
        ? await updateCourse(route.params.id as string, payload)
        : await createCourse(payload);

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
                <FormInput id="name" v-model="name" label="Course Name" :disabled="isReadMode" />
                <ErrorMessage name="name" class="text-red-500 text-sm" />
            </div>

            <div>
                <FormSelect id="course_category_id" v-model="course_category_id" label="Category"
                    :options="categoryOptions" :loading="categoryLoading" :disabled="isReadMode" />
                <ErrorMessage name="course_category_id" class="text-red-500 text-sm" />
            </div>

            <div class="col-span-2">
                <FormTextarea id="description" v-model="description" label="Description" :rows="5"
                    :disabled="isReadMode" />
                <ErrorMessage name="description" class="text-red-500 text-sm" />
            </div>
        </div>

        <div class="flex justify-end pt-6">
            <Button type="submit" :disabled="isSubmitting || isFetching">
                <span v-if="isSubmitting">Saving...</span>
                <span v-else>
                    {{ isUpdateMode ? "Update Course" : "Create Course" }}
                </span>
            </Button>
        </div>
    </form>
</template>
