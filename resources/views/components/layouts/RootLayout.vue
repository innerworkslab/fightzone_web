<script lang="ts" setup>
import { RouterView } from "vue-router";
import Sidebar from "./Sidebar.vue";
import ConfirmModal from "../common/modal/ConfirmModal.vue";
</script>

<template>
    <div
        class="fixed inset-0 flex bg-background selection:bg-primary/30 text-foreground transition-colors duration-300">
        <aside class="w-64 flex-shrink-0 border-r border-sidebar-border z-20 bg-sidebar">
            <Sidebar />
        </aside>

        <div class="flex-1 flex flex-col relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-primary/10 blur-[120px] rounded-full">
                </div>
                <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-primary/10 blur-[120px] rounded-full">
                </div>
            </div>

            <main class="relative z-10 flex-1 overflow-y-auto pt-5 px-8 pb-8 custom-scrollbar">
                <Header />
                <div class="max-w-7xl h-full pt-3">
                    <RouterView />
                </div>
            </main>
        </div>

        <ConfirmModal />
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: var(--background);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--muted);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--primary);
}

main::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(var(--border) 1px, transparent 1px),
        linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(circle at center, black, transparent 80%);
    opacity: 0.2;
    pointer-events: none;
    z-index: -1;
}
</style>
