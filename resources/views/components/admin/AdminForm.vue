<script setup lang="ts">
import { computed, ref, watch, inject } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { RouteNames } from "@/config/route.config";
import { AdminPayload, AdminServices } from "@/api/Admin.service";
import { Button } from "@/components/ui/button";

// Inject refresh function from parent (AdminList)
const adminListRefresh = inject<() => void>('adminListRefresh');

const route = useRoute();
const router = useRouter();
const adminId = Number(route.params.id);

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewAdmin);

const { data: fetchedAdmin, loading: isFetching } = isUpdateMode.value
    ? AdminServices.useAdminDetail(adminId)
    : { data: ref(null), loading: ref(false) };

const { createAdmin, updateAdmin, loading: isSubmitting } = AdminServices.useAdminActions();

const schema = yup.object({
    username: yup.string().required("Username is required"),
    name: yup.string().required("Name is required"),
    password: isUpdateMode.value
        ? yup.string().nullable()
        : yup.string().required("Password is required").min(6, "Min 6 characters"),
    confirm_password: yup.string()
        .oneOf([yup.ref('password')], 'Passwords must match')
        .when('password', {
            is: (val: string) => val && val.length > 0,
            then: (schema) => schema.required("Confirm password is required"),
            otherwise: (schema) => schema.nullable()
        })
});

const { handleSubmit, setValues } = useForm<AdminPayload>({
    validationSchema: schema,
});

const { value: username } = useField<string>("username");
const { value: name } = useField<string>("name");
const { value: password } = useField<string>("password");
const { value: confirm_password } = useField<string>("confirm_password");

watch(fetchedAdmin, (newVal) => {
    if (newVal) {
        setValues({
            username: newVal.username,
            name: newVal.name,
        });
    }
}, { immediate: true });

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        let response;
        if (isUpdateMode.value) {
            response = await updateAdmin(adminId, values);
            if (response?.success) {
                toast.success("Admin updated successfully");
                // Trigger list refresh using injected function
                adminListRefresh?.();
                router.push({ name: RouteNames.AdminList });
            }
        } else {
            response = await createAdmin(values);
            if (response?.success) {
                toast.success("Admin created successfully");
                // Trigger list refresh using injected function
                adminListRefresh?.();
                router.push({ name: RouteNames.AdminList });
            }
        }
    } catch (error) { }
});
</script>

<template>
    <div v-if="isFetching" class="py-10 text-center">Loading data...</div>

    <form v-else @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <FormInput label="Full Name" id="name" v-model="name" type="text" placeholder="Enter full name"
                    :disabled="isReadMode" />
                <ErrorMessage name="name" class="block text-start text-red-500 text-sm" />
            </div>

            <div>
                <FormInput label="Username" id="username" v-model="username" type="text" placeholder="Enter username"
                    :disabled="isReadMode" />
                <ErrorMessage name="username" class="block text-start text-red-500 text-sm" />
            </div>

            <div v-if="!isReadMode">
                <FormInput label="Password" id="password" v-model="password" type="password"
                    :placeholder="isUpdateMode ? 'Leave blank to keep current' : 'Enter password'" />
                <ErrorMessage name="password" class="block text-start text-red-500 text-sm" />
            </div>

            <div v-if="!isReadMode">
                <FormInput label="Confirm Password" id="confirm_password" v-model="confirm_password" type="password"
                    placeholder="Confirm your password" />
                <ErrorMessage name="confirm_password" class="block text-start text-red-500 text-sm" />
            </div>
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="btn-primary"
                :class="{ 'btn-processing': isSubmitting }">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <template v-if="isSubmitting">
                        <svg class="animate-spin h-4 w-4 text-current" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                fill="none"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        PROCESSING...
                    </template>
                    <template v-else>
                        {{ isUpdateMode ? "Update Admin" : "Create Admin" }}
                    </template>
                </span>
            </Button>
        </div>
    </form>
</template>
