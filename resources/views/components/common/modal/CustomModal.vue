<script setup lang="ts">
import { useModalStore } from "../../../../js/ts/store/modal";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { getFormConfig } from "@/config/form.config";
import { computed } from "vue";

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
        <DialogContent class="sm:max-w-xl md:max-w-3xl grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[90dvh]">
            <DialogHeader class="p-6 pb-0">
                <DialogTitle>{{ form.header }}</DialogTitle>
                <DialogDescription>
                    {{ form.message }}
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4 overflow-y-auto px-6">
                <component :is="form.form" />
            </div>
        </DialogContent>
    </Dialog>
</template>
