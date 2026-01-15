<script setup lang="ts">
import { useModalStore } from "@/store/modal";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { getFormConfig } from "@/config/form.config";
import { computed } from "vue";
import { LayoutPanelLeft } from "lucide-vue-next";

const modalStore = useModalStore();

const reactiveFormConfig = computed(() => {
    return getFormConfig(
        modalStore.initialValues,
        modalStore.isReadMode ?? false
    );
});

const form = computed(() => {
    return reactiveFormConfig.value[
        modalStore.formIndex as keyof typeof reactiveFormConfig.value
    ];
});
</script>

<template>
    <Dialog v-if="form?.form" v-model:open="modalStore.isOpen">
        <DialogContent
            class="sm:max-w-xl md:max-w-3xl grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[90dvh] bg-card/95 backdrop-blur-2xl border-border/50 shadow-2xl overflow-hidden rounded-2xl ring-1 ring-white/5">
            <DialogHeader class="p-6 border-b border-border/50 bg-secondary/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-primary/10 border border-primary/20">
                        <LayoutPanelLeft class="w-5 h-5 text-primary" />
                    </div>
                    <div>
                        <DialogTitle class="text-xl font-black uppercase tracking-[0.2em] text-foreground">
                            {{ form.header }}
                        </DialogTitle>
                        <DialogDescription class="text-[10px] font-bold tracking-widest text-muted-foreground mt-1">
                            {{ form.message }}
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <div class="grid gap-6 py-6 overflow-y-auto px-8 custom-scrollbar">
                <component :is="form.form" />
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
:deep(.fixed) {
    backdrop-filter: blur(4px);
}
</style>
