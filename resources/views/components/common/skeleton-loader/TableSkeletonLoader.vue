<script setup lang="ts">
import TableCell from '@/components/ui/table/TableCell.vue';
import TableRow from '@/components/ui/table/TableRow.vue';

interface Props {
    isLoading: boolean;
    colNumber: number;
    rowNumber?: number;
}

const props = withDefaults(defineProps<Props>(), {
    rowNumber: 10,
});

const columnWidths = [
    'w-10', 'w-32', 'w-48', 'w-40', 'w-24', 'w-28', 'w-full'
];

const getColumnWidth = (index: number) => {
    return columnWidths[index] || 'w-24';
};
</script>

<template>
    <template v-if="isLoading">
        <TableRow v-for="i in rowNumber" :key="`skeleton-row-${i}`" class="hover:bg-transparent border-border/40">
            <TableCell v-for="(j, colIndex) in colNumber" :key="`skeleton-col-${i}-${j}`" :class="[
                'py-4 px-4',
                colIndex === colNumber - 1 ? 'flex justify-center gap-3' : ''
            ]">
                <template v-if="colIndex === colNumber - 1">
                    <div class="h-8 w-8 bg-secondary/60 rounded-lg animate-pulse border border-border/50"></div>
                    <div class="h-8 w-8 bg-secondary/60 rounded-lg animate-pulse border border-border/50"></div>
                </template>

                <template v-else>
                    <div class="h-3 bg-secondary/80 rounded animate-pulse shadow-[0_0_10px_rgba(var(--secondary),0.1)]"
                        :class="colIndex === 0 ? 'w-8' : getColumnWidth(colIndex)"></div>
                </template>
            </TableCell>
        </TableRow>
    </template>
</template>

<style scoped>
/* HUD "Scanning" Pulse Effect */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 0.5;
        filter: brightness(1);
    }

    50% {
        opacity: 0.2;
        filter: brightness(0.8);
    }
}
</style>
