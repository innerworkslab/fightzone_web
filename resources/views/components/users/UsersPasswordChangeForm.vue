<script setup lang="ts">
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Lock, ShieldCheck } from "lucide-vue-next";

import { AdminsPayload } from "@/api/Admins.service";
import { Button } from "@/components/ui/button";
import { UsersServices } from "@/api/Users.service";
import { useModalStore } from "@/store/modal";

const modalStore = useModalStore();
const userId = modalStore.initialValues.id as number;

const { updateUser, loading: isSubmitting } = UsersServices.useUserActions();

const schema = yup.object({
    password: yup.string()
        .required("New password is required")
        .min(6, "Security requirement: Min 6 characters"),
    confirm_password: yup.string()
        .required("Please confirm your password")
        .oneOf([yup.ref('password')], 'Passwords do not match')
});

const { handleSubmit } = useForm({
    validationSchema: schema,
    initialValues: {
        password: "",
        confirm_password: ""
    }
});

const { value: password } = useField<string>("password");
const { value: confirm_password } = useField<string>("confirm_password");

const submitForm = handleSubmit(async (values) => {
    try {
        const payload: Partial<AdminsPayload> = {
            password: values.password
        };

        const response = await updateUser(userId, payload as AdminsPayload);

        if (response?.success) {
            toast.success("Security credentials updated successfully");
            modalStore.closeModal();
        }
    } catch (error) {
        console.error("Password update failed", error);
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <div class="flex items-center gap-3 mb-2 p-3 bg-primary/5 border border-primary/10 rounded-lg">
            <Lock class="w-5 h-5 text-primary" />
            <div>
                <h4 class="text-xs font-black uppercase tracking-widest text-foreground">Credential Reset</h4>
                <p class="text-[10px] text-muted-foreground uppercase font-bold">Update system access for UID:{{ userId
                    }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5">
            <div class="space-y-1">
                <FormInput label="New Password" id="password" v-model="password" type="password" placeholder="••••••••"
                    class="bg-secondary/30 border-border/50 focus:border-primary" />
                <ErrorMessage name="password"
                    class="block text-start text-red-500 text-[10px] font-bold uppercase tracking-wide mt-1" />
            </div>

            <div class="space-y-1">
                <FormInput label="Confirm New Password" id="confirm_password" v-model="confirm_password" type="password"
                    placeholder="••••••••" class="bg-secondary/30 border-border/50 focus:border-primary" />
                <ErrorMessage name="confirm_password"
                    class="block text-start text-red-500 text-[10px] font-bold uppercase tracking-wide mt-1" />
            </div>
        </div>

        <div class="w-full flex justify-end pt-4 border-t border-border/50">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="btn-primary min-w-[160px] h-11">
                <template v-if="isSubmitting">
                    <div class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Encrypting...</span>
                    </div>
                </template>
                <template v-else>
                    <ShieldCheck class="w-4 h-4 mr-2" />
                    <span class="text-[10px] font-black uppercase tracking-widest">Update Password</span>
                </template>
            </Button>
        </div>
    </form>
</template>
