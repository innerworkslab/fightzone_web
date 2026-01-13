<script setup lang="ts">
import { useModalStore } from '@/store/modal';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
    DialogClose,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { AlertTriangle, X } from "lucide-vue-next";

const modalStore = useModalStore();

const handleConfirmApprove = () => {
    modalStore.confirm.onApprove();
    modalStore.closeConfirmModal();
};

const handleConfirmReject = () => {
    if (modalStore.confirm.onReject) {
        modalStore.confirm.onReject();
    }
    modalStore.closeConfirmModal();
};
</script>

<template>
    <Dialog v-model:open="modalStore.confirm.isOpen">
        <DialogContent
            class="sm:max-w-md bg-card/95 backdrop-blur-xl border-border shadow-2xl rounded-2xl ring-1 ring-white/10">
            <DialogHeader class="relative">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 rounded-lg bg-primary/10 border border-primary/20">
                        <AlertTriangle class="w-5 h-5 text-primary" />
                    </div>
                    <DialogTitle class="text-xl font-black uppercase tracking-[0.15em] text-foreground">
                        System <span class="text-primary">Confirm</span>
                    </DialogTitle>
                </div>

                <DialogDescription class="text-sm font-medium text-muted-foreground leading-relaxed pt-2">
                    {{ modalStore.confirm.message }}
                </DialogDescription>

                <DialogClose
                    class="absolute -right-2 -top-2 p-2 rounded-full bg-secondary text-muted-foreground hover:text-primary transition-colors outline-none border border-border">
                    <X class="w-4 h-4" />
                    <span class="sr-only">Close</span>
                </DialogClose>
            </DialogHeader>

            <DialogFooter class="mt-6 flex flex-row items-center !justify-between gap-3 pt-4 border-t border-border/50">
                <Button variant="ghost" type="button" @click="modalStore.closeConfirmModal"
                    class="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:bg-secondary">
                    Cancel
                </Button>

                <div class="flex items-center gap-2">
                    <Button v-if="modalStore.confirm.rejectBtnText" variant="destructive" type="button"
                        @click="handleConfirmReject"
                        class="text-[10px] font-black uppercase tracking-widest px-6 h-10 shadow-lg shadow-destructive/20">
                        {{ modalStore.confirm.rejectBtnText }}
                    </Button>

                    <Button type="button" @click="handleConfirmApprove" class="btn-primary min-w-[120px] h-10 !py-0">
                        {{ modalStore.confirm.approveBtnText }}
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
:deep(.fixed) {
    backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.4);
}
</style>
