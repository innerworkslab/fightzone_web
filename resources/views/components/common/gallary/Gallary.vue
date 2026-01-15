<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { X, ChevronLeft, ChevronRight, Maximize2 } from "lucide-vue-next";

interface ImageItem {
    id: number;
    src: string;
    title?: string;
    subtitle?: string;
    alt?: string;
}

type Size = "sm" | "md" | "lg";

const props = defineProps<{
    images: ImageItem[];
    size?: Size;
}>();

const isOpen = ref(false);
const currentIndex = ref(0);
const lightboxRef = ref<HTMLElement | null>(null);

const currentImage = computed(() => props.images[currentIndex.value] || props.images[0]);

function openLightbox(idx = 0) {
    currentIndex.value = idx;
    isOpen.value = true;
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => lightboxRef.value?.focus());
}

function closeLightbox() {
    isOpen.value = false;
    document.body.style.overflow = '';
}

function next() { currentIndex.value = (currentIndex.value + 1) % props.images.length; }
function prev() { currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length; }

function handleKeydown(e: KeyboardEvent) {
    if (!isOpen.value) return;
    if (e.key === "ArrowRight") next();
    else if (e.key === "ArrowLeft") prev();
    else if (e.key === "Escape") closeLightbox();
}

onMounted(() => window.addEventListener("keydown", handleKeydown));
onBeforeUnmount(() => {
    window.removeEventListener("keydown", handleKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <div class="inline-block">
        <div class="relative rounded-lg overflow-hidden border border-border/50 bg-secondary/20 cursor-pointer group transition-all duration-300 hover:border-primary/50 shadow-sm"
            :class="{
                'w-10 h-10': props.size === 'sm',
                'w-40 h-24': props.size === 'md' || !props.size,
                'w-full aspect-video': props.size === 'lg'
            }" @click="openLightbox(0)">
            <img v-if="props.images.length" :src="props.images[0].src"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />

            <div
                class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                <Maximize2 class="w-4 h-4 text-white" />
            </div>

            <div v-if="props.images.length > 1"
                class="absolute bottom-0 right-0 bg-primary/90 text-white font-black text-[9px] px-1 py-0.5 rounded-tl tracking-tighter uppercase">
                +{{ props.images.length - 1 }}
            </div>
        </div>

        <Teleport to="body">
            <transition name="fighter-fade">
                <div v-if="isOpen"
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 backdrop-blur-xl bg-black/90"
                    @click.self="closeLightbox">

                    <div class="max-w-5xl w-full bg-card/60 border border-white/10 shadow-2xl relative flex flex-col rounded-2xl ring-1 ring-primary/20"
                        @keydown.stop.prevent="handleKeydown" tabindex="0" ref="lightboxRef">
                        <button
                            class="absolute top-6 right-6 z-50 p-2 rounded-lg bg-white/5 border border-white/10 text-white hover:bg-red-500/20 hover:text-red-500 hover:border-red-500/50 transition-all"
                            @click="closeLightbox">
                            <X class="w-5 h-5" />
                        </button>

                        <div class="relative flex-1 flex flex-col items-center justify-center p-4 min-h-[60vh]">
                            <button v-if="props.images.length > 1"
                                class="absolute left-6 z-10 p-3 rounded-xl bg-secondary/50 border border-white/10 text-white hover:border-primary hover:text-primary transition-all"
                                @click="prev">
                                <ChevronLeft class="w-6 h-6" />
                            </button>

                            <img :src="currentImage.src" :alt="currentImage.alt"
                                class="max-h-[75vh] w-auto object-contain rounded-lg shadow-[0_0_50px_rgba(0,0,0,0.5)] border border-white/5" />

                            <button v-if="props.images.length > 1"
                                class="absolute right-6 z-10 p-3 rounded-xl bg-secondary/50 border border-white/10 text-white hover:border-primary hover:text-primary transition-all"
                                @click="next">
                                <ChevronRight class="w-6 h-6" />
                            </button>
                        </div>

                        <div class="p-6 bg-secondary/30 border-t border-white/5 backdrop-blur-md rounded-b-2xl">
                            <h3 class="text-white font-black uppercase tracking-[0.2em] text-sm">
                                {{ currentImage.title || 'Asset Preview' }}
                            </h3>
                            <p class="text-primary font-bold text-[10px] uppercase tracking-widest mt-1">
                                {{ currentImage.subtitle || `INDEX: 00${currentIndex + 1} / SHOT_COUNT:
                                ${props.images.length}` }}
                            </p>
                        </div>
                    </div>
                </div>
            </transition>
        </Teleport>
    </div>
</template>

<style scoped>
/* HUD Entrance Animation */
.fighter-fade-enter-active,
.fighter-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.fighter-fade-enter-from,
.fighter-fade-leave-to {
    opacity: 0;
    transform: scale(0.98) translateY(10px);
    filter: blur(20px);
}

/* Cleanup outline */
div:focus {
    outline: none;
}
</style>
