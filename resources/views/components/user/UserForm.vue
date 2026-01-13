<script setup lang="ts">
import { computed, ref, watch, inject } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";

import { RouteNames } from "@/config/route.config";
import { UserPayload, UserServices } from "@/api/User.service";
import { Button } from "@/components/ui/button";

const userListRefresh = inject<() => void>('userListRefresh');

const route = useRoute();
const router = useRouter();
const userId = Number(route.params.id);

const isUpdateMode = computed(() => !!route.params.id);
const isReadMode = computed(() => route.name === RouteNames.ViewUser);

const { data: fetchedUser, loading: isFetching } = isUpdateMode.value
    ? UserServices.useUserDetail(userId)
    : { data: ref(null), loading: ref(false) };

const { createUser, updateUser, loading: isSubmitting } = UserServices.useUserActions();

const schema = yup.object({
    phone_number: yup.string().required("Phone number is required"),
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
        }),
});

const { handleSubmit, setValues } = useForm<UserPayload>({
    validationSchema: schema,
});

const { value: phone_number } = useField<string>("phone_number");
const { value: name } = useField<string>("name");
const { value: password } = useField<string>("password");
const { value: confirm_password } = useField<string>("confirm_password");

watch(fetchedUser, (newVal) => {
    if (newVal) {
        setValues({
            phone_number: newVal.phone_number,
            name: newVal.name,
        });
    }
}, { immediate: true });

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    try {
        let response;
        if (isUpdateMode.value) {
            response = await updateUser(userId, values);
            if (response?.success) {
                toast.success("User updated successfully");
                // Trigger list refresh using injected function
                userListRefresh?.();
                router.push({ name: RouteNames.UserList });
            }
        } else {
            response = await createUser(values);
            if (response?.success) {
                toast.success("User created successfully");
                // Trigger list refresh using injected function
                userListRefresh?.();
                router.push({ name: RouteNames.UserList });
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
                <FormInput label="Phone Number" id="phone_number" v-model="phone_number" type="tel"
                    placeholder="Enter phone number" :disabled="isReadMode" />
                <ErrorMessage name="phone_number" class="block text-start text-red-500 text-sm" />
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
                        {{ isUpdateMode ? "Update User" : "Create User" }}
                    </template>
                </span>
            </Button>
        </div>
    </form>
</template>
