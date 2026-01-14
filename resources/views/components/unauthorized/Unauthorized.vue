<script setup lang="ts">
import { useRouter } from "vue-router";
import { Button } from "@/components/ui/button";
import { ShieldX, ArrowLeft } from "lucide-vue-next";
import { RouteNames } from "@/config/route.config";

const router = useRouter();

const goBack = () => {
    router.go(-1);
};

const goToLogin = () => {
    router.push({ name: RouteNames.Login });
};
</script>

<template>
    <div
        class="fixed inset-0 flex items-center justify-center bg-background selection:bg-primary/30 transition-colors duration-500 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-destructive/10 blur-[120px] rounded-full">
            </div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-destructive/5 blur-[100px] rounded-full">
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
                        class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-destructive/10 border border-destructive/20 mb-4">
                        <ShieldX class="w-8 h-8 text-destructive" />
                    </div>
                    <h2 class="text-2xl font-black uppercase tracking-[0.2em] text-foreground">
                        Access <span class="text-destructive">Denied</span>
                    </h2>
                    <p class="text-sm text-muted-foreground mt-2">
                        You don't have permission to access this page
                    </p>
                </div>

                <div class="space-y-4">
                    <Button @click="goBack" class="w-full btn-secondary h-12" variant="outline">
                        <span class="flex items-center justify-center gap-2">
                            <ArrowLeft class="w-4 h-4" />
                            GO BACK
                        </span>
                    </Button>

                    <Button @click="goToLogin" class="w-full btn-primary h-12" variant="default">
                        <span class="flex items-center justify-center gap-2">
                            RETURN TO LOGIN
                        </span>
                    </Button>
                </div>

                <div class="mt-8 pt-4 text-center">
                    <p class="text-[9px] font-bold text-muted-foreground/60 uppercase tracking-widest">
                        Contact Administrator for Access
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
