<script setup lang="ts">
import ScrollArea from "@/components/ui/scroll-area/ScrollArea.vue";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "../../../../js/ts/components/ui/table";
import { Props } from "./type";
import { useRouter } from "vue-router";
const props = defineProps<Props<any>>();
const router = useRouter();

function getNestedValue(obj: any, path: string) {
    return path.split('.').reduce((o, i) => o?.[i], obj);
}

function goToRoute(col: any, row: any) {
    router.push({
        name: col.routeName,
        params: {
            [col.routeParamKey]: row[col.routeParamKey],
        },
    });
}
</script>

<template>
    <div
        class="relative rounded-xl border border-border bg-card overflow-hidden shadow-2xl transition-colors duration-300">
        <ScrollArea class="max-h-[calc(100vh-220px)] overflow-auto custom-scrollbar">
            <Table class="w-full border-collapse">
                <TableHeader class="bg-secondary/80 sticky top-0 z-10 backdrop-blur-md">
                    <TableRow class="border-b border-border hover:bg-transparent">
                        <TableHead v-for="(col, i) in props.columns" :key="i"
                            class="h-12 px-4 text-xs font-black uppercase tracking-widest text-primary">
                            {{ col.label }}
                        </TableHead>
                        <TableHead v-if="props.actions"
                            class="h-12 px-4 text-xs font-black uppercase tracking-widest text-primary text-center">
                            Action
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableSkeletonLoader v-if="props.loading" :isLoading="props.loading"
                        :colNumber="props.columns.length + (props.actions ? 1 : 0)" :rowNumber="props.perPage || 5" />

                    <template v-else-if="props.data.length">
                        <TableRow v-for="(row, rowIndex) in props.data" :key="row.id || rowIndex"
                            class="group border-b border-border transition-colors hover:bg-primary/5">

                            <TableCell v-for="(col, colIndex) in props.columns" :key="col.key"
                                class="px-4 py-4 text-sm font-medium text-foreground/80 align-middle"
                                :class="col.onClick ? 'cursor-pointer hover:text-foreground' : ''"
                                @click="col.onClick ? col.onClick(row, props.extraArgs) : null">

                                <template v-if="col.render">
                                    <div class="text-xs font-bold tracking-tight">
                                        <component v-if="typeof col.render(row, rowIndex, props.extraArgs) === 'object'"
                                            :is="col.render(row, rowIndex, props.extraArgs)" />
                                        <div v-else v-html="col.render(row, rowIndex, props.extraArgs)"></div>
                                    </div>
                                </template>

                                <template v-else-if="col.type === 'link'">
                                    <span
                                        class="cursor-pointer text-primary font-bold uppercase tracking-tighter hover:underline decoration-2"
                                        @click.stop="goToRoute(col, row)">
                                        {{ col.textKey ? getNestedValue(row, col.textKey) || "-" : row[col.key] || "-"
                                        }}
                                    </span>
                                </template>

                                <template v-else>
                                    <span
                                        class="block truncate max-w-[180px] group-hover:text-foreground transition-colors">
                                        {{ row[col.key] ?? "-" }}
                                    </span>
                                </template>
                            </TableCell>

                            <TableCell v-if="props.actions" class="px-4 py-4">
                                <div class="flex items-center justify-center gap-3">
                                    <template v-for="(action, actionIdx) in props.actions" :key="actionIdx">
                                        <button v-if="!action.show || action.show(row)"
                                            :disabled="action.disabled ? action.disabled(row) : false" class="p-2 rounded-lg bg-secondary text-muted-foreground transition-all
                                                   hover:bg-primary hover:text-primary-foreground disabled:opacity-20
                                                   disabled:cursor-not-allowed shadow-sm border border-border"
                                            @click.stop="action.onClick ? action.onClick(row, props.extraArgs) : null"
                                            :title="action.tooltip">
                                            <component :is="action.icon" class="w-4 h-4" />
                                        </button>
                                    </template>
                                </div>
                            </TableCell>
                        </TableRow>
                    </template>

                    <TableRow v-else>
                        <TableCell :colspan="props.columns.length + (props.actions ? 1 : 0)" class="h-32 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span
                                    class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground italic">Nothing
                                    to show
                                    yet</span>
                                <div class="h-[1px] w-12 bg-primary/30"></div>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </ScrollArea>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--muted);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--primary);
}

tr:last-child {
    border-bottom: none;
}

.group:hover td:first-child {
    box-shadow: inset 4px 0 0 0 var(--primary);
}
</style>
