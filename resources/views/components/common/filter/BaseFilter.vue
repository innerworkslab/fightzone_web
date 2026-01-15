<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Plus, RotateCcw } from "lucide-vue-next";

interface Props {
    addLabel?: string;
    showAdd?: boolean;
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    addLabel: "Add New",
    showAdd: true,
    loading: false
});

const emit = defineEmits(['reset', 'add']);
</script>

<template>
    <div class="relative z-20 flex flex-col md:flex-row items-center justify-between gap-4 py-6">

        <div class="flex flex-1 items-center gap-3 w-full md:w-auto">
            <slot></slot>

            <Button variant="ghost" size="icon" @click="emit('reset')"
                class="h-10 w-10 text-muted-foreground hover:text-primary hover:bg-primary/10 transition-all shrink-0">
                <RotateCcw class="w-4 h-4" />
            </Button>
        </div>

        <div v-if="showAdd" class="flex items-center gap-2 w-full md:w-auto justify-end shrink-0">
            <Button @click="emit('add')" class="btn-primary h-10 px-6">
                <Plus class="w-4 h-4 mr-2" />
                <span class="text-[10px] font-black uppercase tracking-widest">
                    {{ addLabel }}
                </span>
            </Button>
        </div>
    </div>
</template>
