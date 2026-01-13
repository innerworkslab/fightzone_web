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
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Confirmation</DialogTitle>
                <DialogDescription>
                    {{ modalStore.confirm.message }}
                </DialogDescription>
                <DialogClose
                    class="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none data-[state=open]:bg-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-4 h-4 text-muted-foreground">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                    <span class="sr-only">Close</span>
                </DialogClose>
            </DialogHeader>

            <DialogFooter class="justify-between">
                <Button variant="outline" type="button" @click="modalStore.closeConfirmModal">
                    Close
                </Button>
                <div class="ml-auto space-x-2">
                    <Button v-if="modalStore.confirm.rejectBtnText" variant="destructive" type="button"
                        @click="handleConfirmReject">
                        {{ modalStore.confirm.rejectBtnText }}
                    </Button>
                    <Button type="button" @click="handleConfirmApprove">
                        {{ modalStore.confirm.approveBtnText }}
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
