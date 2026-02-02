<script setup lang="ts">
import { useRouter } from "vue-router";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { onMounted } from "vue";

import { AuthServices, type LoginPayload } from "@/api/Auth.service";
import { setEncryptedCookie } from "@/lib/utils.cookies";
import { setEncryptedLocalStorage } from "@/lib/utils.localStorage";
import { COOKIES, LOCALSTORAGE } from "@/constant/global.constant";
import { Button } from "@/components/ui/button";
import { Swords, ArrowRight } from "lucide-vue-next";
import { RouteNames } from "@/config/route.config";

const router = useRouter();

const schema = yup.object({
    username: yup.string().required("Username is required"),
    password: yup.string().required("Password is required"),
});

const { handleSubmit } = useForm<LoginPayload>({
    validationSchema: schema,
    initialValues: {
        username: "",
        password: ""
    }
});

const { value: username } = useField<string>("username");
const { value: password } = useField<string>("password");

const { login, loading } = AuthServices.useAuthActions();

const submitLogin = handleSubmit(async (values) => {
    const response = await login(values);
    const data = response?.data;

    if (response?.success) {
        setEncryptedCookie(COOKIES.ACCESS_TOKEN, data.token);
        setEncryptedLocalStorage(LOCALSTORAGE.AUTH_USER, data.user);
        setEncryptedLocalStorage(LOCALSTORAGE.PERMISSIONS, data.permissions || [{ permission_type_name: "all" }]);
        router.push({ name: RouteNames.AdminsList });
    }
});

onMounted(() => {
    const isDark = localStorage.getItem('theme') === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div
        class="fixed inset-0 flex items-center justify-center bg-background selection:bg-primary/30 transition-colors duration-500 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-primary/10 blur-[120px] rounded-full"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary/5 blur-[100px] rounded-full">
            </div>
            <div class="absolute inset-0 opacity-[0.15] dark:opacity-20"
                style="background-image: radial-gradient(var(--border) 1px, transparent 1px); background-size: 30px 30px;">
            </div>
        </div>

        <div class="relative z-10 w-full max-w-md px-6">
            <div
                class="rounded-2xl border border-border bg-card/80 dark:bg-card/50 backdrop-blur-xl p-8 shadow-2xl ring-1 ring-border/50">
                <div class="text-center mb-8">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary/10 border border-primary/20 mb-4">
                        <Swords class="w-8 h-8 text-primary" />
                    </div>
                    <h2 data-testid="login-title"
                        class="text-2xl font-black uppercase tracking-[0.2em] text-foreground">
                        Fighter <span class="text-primary">Zone</span>
                    </h2>
                </div>

                <form @submit.prevent="submitLogin" class="space-y-5">
                    <div class="space-y-1">
                        <FormInput label="Username" id="username" v-model="username" type="text"
                            placeholder="PLEASE ENTER YOUR ID" required />
                        <ErrorMessage name="username"
                            class="text-primary text-[10px] font-bold uppercase tracking-tight block ml-1" />
                    </div>

                    <div class="space-y-1">
                        <FormInput label="Password" id="password" v-model="password" type="password"
                            placeholder="••••••••" required />
                        <ErrorMessage name="password"
                            class="text-primary text-[10px] font-bold uppercase tracking-tight block ml-1" />
                    </div>

                    <div class="pt-2">
                        <Button type="submit" class="w-full btn-primary h-12" variant="default" :disabled="loading"
                            :class="{ 'btn-processing': loading }">
                            <span class="flex items-center justify-center gap-2">
                                {{ loading ? "AUTHENTICATING..." : "LOGIN" }}
                                <ArrowRight v-if="!loading" class="w-4 h-4" />
                            </span>
                        </Button>
                    </div>
                </form>

                <div class="mt-8 pt-4 text-center">
                    <p class="text-[9px] font-bold text-muted-foreground/60 uppercase tracking-widest">
                        Authorized Personnel Only
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
