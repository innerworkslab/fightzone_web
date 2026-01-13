<script setup lang="ts">
import TableCell from '@/components/ui/table/TableCell.vue';
import TableRow from '@/components/ui/table/TableRow.vue';


interface Props {
    isLoading: boolean;
    colNumber: number;
    rowNumber?: number;
}

const props = withDefaults(defineProps<Props>(), {
    rowNumber: 5,
});

const columnWidths = [
    'w-8', 'w-3/4', 'w-2/3', 'w-5/6', 'w-1/2', 'w-1/2', 'w-full'
];

const getColumnWidth = (index: number) => {
    return columnWidths[index] || 'w-full';
};

const renderActionColumn = (colIndex: number) => {
    if (colIndex === props.colNumber - 1) {
        return `
            <div class="h-5 w-5 bg-gray-200 rounded-full animate-pulse"></div>
            <div class="h-5 w-5 bg-gray-200 rounded-full animate-pulse"></div>
            <div class="h-5 w-5 bg-gray-200 rounded-full animate-pulse"></div>
        `;
    }
    return '';
};
</script>

<template>
    <template v-if="isLoading">
        <TableRow v-for="i in rowNumber" :key="`skeleton-row-${i}`">
            <TableCell v-for="(j, colIndex) in colNumber" :key="`skeleton-col-${i}-${j}`"
                :class="j === colNumber ? 'py-3 flex justify-center gap-2' : 'py-3'">
                <template v-if="j === colNumber">
                    <div class="h-5 w-5 bg-gray-200 rounded-full animate-pulse"></div>
                    <div class="h-5 w-5 bg-gray-200 rounded-full animate-pulse"></div>
                    <div class="h-5 w-5 bg-gray-200 rounded-full animate-pulse"></div>
                </template>
                <template v-else>
                    <div class="h-4 bg-gray-200 rounded animate-pulse"
                        :class="colIndex === 0 ? 'w-[50px]' : getColumnWidth(colIndex)">
                    </div>
                </template>
            </TableCell>
        </TableRow>
    </template>
</template>

<style scoped></style>
