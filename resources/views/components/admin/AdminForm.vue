<script setup lang="ts">
import { computed, watch, inject, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { RouteNames } from "@/config/route.config";
import { AdminPayload, AdminServices } from "@/api/Admin.service";
import { Button } from "@/components/ui/button";

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
        ? yup.string().nullable().transform(v => v === "" ? null : v)
        : yup.string().required("Password is required").min(6, "Min 6 characters"),
    confirm_password: yup.string()
        .oneOf([yup.ref('password')], 'Passwords must match')
        .when('password', {
            is: (val: string) => val && val.length > 0,
            then: (s) => s.required("Confirm password is required"),
            otherwise: (s) => s.strip()
        })
});

const { handleSubmit, setValues } = useForm<AdminPayload>({
    validationSchema: schema,
    initialValues: {
        username: "",
        name: "",
        password: "",
        confirm_password: ""
    }
});

const { value: username } = useField<string>("username");
const { value: name } = useField<string>("name");
const { value: password } = useField<string>("password");
const { value: confirm_password } = useField<string>("confirm_password");

watch(fetchedAdmin, (newVal) => {

    if (newVal) {
        setValues({
            username: newVal.data.username,
            name: newVal.data.name,
            password: "",
            confirm_password: ""
        });
    }
}, { immediate: true, deep: true });

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        const { confirm_password, ...payload } = values;
        let response;

        if (isUpdateMode.value) {
            if (!password.value) {
                delete payload.password
            }
            response = await updateAdmin(adminId, payload as AdminPayload);
        } else {
            response = await createAdmin(payload as AdminPayload);
        }

        if (response) {
            toast.success(isUpdateMode.value ? "Admin updated successfully" : "Admin created successfully");
            router.push({ name: RouteNames.AdminList });
        }
    } catch (error) { }
});
</script>

<template>
    <div v-if="(isUpdateMode || isReadMode) && isFetching" class="py-10 text-center text-primary font-medium">
        Loading admin details...
    </div>

    <form v-else @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <FormInput label="Full Name" id="name" v-model="name" type="text" placeholder="Enter full name"
                    :disabled="isReadMode" />
                <ErrorMessage name="name" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <div>
                <FormInput label="Username" id="username" v-model="username" type="text" placeholder="Enter username"
                    :disabled="isReadMode" />
                <ErrorMessage name="username" class="block text-start text-red-500 text-sm mt-1" />
            </div>

            <template v-if="!isReadMode">
                <div>
                    <FormInput label="Password" id="password" v-model="password" type="password"
                        :placeholder="isUpdateMode ? 'Leave blank to keep current' : 'Enter password'" />
                    <ErrorMessage name="password" class="block text-start text-red-500 text-sm mt-1" />
                </div>

                <div>
                    <FormInput label="Confirm Password" id="confirm_password" v-model="confirm_password" type="password"
                        :placeholder="isUpdateMode && !password ? 'No need to fill' : 'Confirm your password'"
                        :disabled="isUpdateMode && !password" />
                    <ErrorMessage name="confirm_password" class="block text-start text-red-500 text-sm mt-1" />
                </div>
            </template>
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="min-w-[140px]">
                <template v-if="isSubmitting">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Processing...
                </template>
                <template v-else>
                    {{ isUpdateMode ? "Update Admin" : "Create Admin" }}
                </template>
            </Button>
        </div>
    </form>
</template>
