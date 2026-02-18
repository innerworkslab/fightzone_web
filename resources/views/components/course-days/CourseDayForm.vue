<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "vee-validate";
import * as yup from "yup";
import { Button } from "@/components/ui/button";
import FormInput from "../common/formControl/FormInput.vue";
import FormSelect from "../common/formControl/FormSelect.vue";

interface CourseDay {
    day_number: number | null;
    duration: string;
    type: "Lesson" | "Rest";
}

const isMultiple = ref(false);
const courseDays = ref<CourseDay[]>([]);

const schema = yup.object({
    day_number: yup
        .number()
        .transform((value) => (isNaN(value) ? undefined : value))
        .typeError("Day is required")
        .required("Day is required")
        .min(1),
    duration: yup
        .string()
        .required("Duration is required")
        .matches(
            /^([0-1]\d|2[0-3]):([0-5]\d):([0-5]\d)$/,
            "Format: HH:mm:ss"
        ),
    type: yup
        .string()
        .required("Type is required")
        .oneOf(["Lesson", "Rest"]),
});

const { handleSubmit, resetForm, defineField, errors } = useForm<CourseDay>({
    validationSchema: schema,
    initialValues: {
        day_number: null,
        duration: "00:00:00",
        type: "Lesson",
    },
});

const [dayNumber] = defineField('day_number');
const [duration] = defineField('duration');
const [type] = defineField('type');

const addOrSubmit = handleSubmit((formValues) => {
    if (isMultiple.value) {
        courseDays.value.push({ ...formValues });
        resetForm();
    } else {
        console.log("Single Day Submit:", formValues);
    }
});

const removeDay = (index: number) => {
    courseDays.value.splice(index, 1);
};
</script>

<template>
    <form @submit="addOrSubmit" class="space-y-6">
        <div class="flex items-center gap-2">
            <input type="checkbox" v-model="isMultiple" id="multiple" class="w-4 h-4" />
            <label for="multiple" class="text-sm font-medium">Add multiple days</label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col gap-1">
                <FormInput id="day_number" type="number" v-model="dayNumber" label="Day" />
                <span v-if="errors.day_number" class="text-red-500 text-xs">{{ errors.day_number }}</span>
            </div>

            <div class="flex flex-col gap-1">
                <FormInput id="duration" v-model="duration" label="Duration (HH:mm:ss)" />
                <span v-if="errors.duration" class="text-red-500 text-xs">{{ errors.duration }}</span>
            </div>

            <div class="flex flex-col gap-1">
                <FormSelect id="type" v-model="type" label="Type" :options="[
                    { label: 'Lesson', value: 'Lesson' },
                    { label: 'Rest', value: 'Rest' },
                ]" />
                <span v-if="errors.type" class="text-red-500 text-xs">{{ errors.type }}</span>
            </div>
        </div>

        <Button type="submit" class="w-full md:w-auto">
            {{ isMultiple ? "Add Day" : "Submit" }}
        </Button>
    </form>

    <div v-if="isMultiple && courseDays.length" class="mt-8 border rounded-md overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-background border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Day</th>
                    <th class="px-4 py-3 text-left font-medium">Duration</th>
                    <th class="px-4 py-3 text-left font-medium">Type</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <tr v-for="(day, index) in courseDays" :key="index">
                    <td class="px-4 py-3">{{ day.day_number }}</td>
                    <td class="px-4 py-3">{{ day.duration }}</td>
                    <td class="px-4 py-3">{{ day.type }}</td>
                    <td class="px-4 py-3 text-right">
                        <Button type="button" size="sm" variant="destructive" @click="removeDay(index)">
                            Remove
                        </Button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
