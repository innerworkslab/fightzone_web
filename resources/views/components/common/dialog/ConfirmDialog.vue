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
import { X, AlertCircle } from 'lucide-vue-next';

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
            class="sm:max-w-md bg-card/90 backdrop-blur-xl border border-border/50 shadow-2xl rounded-xl ring-1 ring-white/10">
            <DialogHeader class="space-y-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 border border-primary/20">
                        <AlertCircle class="w-5 h-5 text-primary" />
                    </div>
                    <DialogTitle class="text-xl font-black uppercase tracking-[0.15em] text-foreground">
                        System <span class="text-primary">Confirm</span>
                    </DialogTitle>
                </div>

                <DialogDescription class="text-xs font-bold tracking-widest text-muted-foreground/80 leading-relaxed">
                    {{ modalStore.confirm.message }}
                </DialogDescription>

                <DialogClose
                    class="absolute right-4 top-4 rounded-lg p-1 text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all outline-none">
                    <X class="w-4 h-4" />
                    <span class="sr-only">Close</span>
                </DialogClose>
            </DialogHeader>

            <DialogFooter class="sm:justify-between gap-4 pt-6 border-t border-border/30 mt-4">
                <Button variant="ghost" type="button" @click="modalStore.closeConfirmModal"
                    class="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:bg-secondary/50">
                    Cancel
                </Button>

                <div class="flex gap-2">
                    <Button v-if="modalStore.confirm.rejectBtnText" variant="destructive" type="button"
                        @click="handleConfirmReject"
                        class="h-10 px-6 text-[10px] font-black uppercase tracking-widest rounded-lg">
                        {{ modalStore.confirm.rejectBtnText }}
                    </Button>

                    <Button type="button" @click="handleConfirmApprove" class="btn-primary h-10 px-8 min-w-[100px]">
                        {{ modalStore.confirm.approveBtnText }}
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
:deep([data-state='open']) {
    animation: modal-in 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modal-in {
    from {
        opacity: 0;
        transform: translate(-50%, -48%) scale(0.95);
    }

    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}
</style>
