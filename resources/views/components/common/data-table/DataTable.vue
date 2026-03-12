<script setup lang="ts">
import ScrollArea from "@/components/ui/scroll-area/ScrollArea.vue";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "../../../../js/ts/components/ui/table";
import { Props } from "./type";
import { useRouter } from "vue-router";
import { ref, reactive } from "vue";
import { ChevronDown, ChevronRight } from "lucide-vue-next";

const props = defineProps<Props<any>>();
const router = useRouter();

const expandedRow = ref<number | null>(null);
const loadingRow = ref<number | null>(null);
const subData = reactive<Record<number, any[]>>({});

function goToRoute(col: any, row: any) {
    router.push({
        name: col.routeName,
        params: {
            [col.routeParamKey]: row[col.routeParamKey],
        },
    });
}

async function toggleRow(row: any) {
    if (expandedRow.value === row.id) {
        expandedRow.value = null;
        return;
    }

    expandedRow.value = row.id;

    if (!subData[row.id] && props.fetchSubData) {
        loadingRow.value = row.id;
        try {
            const result = await props.fetchSubData(row);
            subData[row.id] = (result || []).map((item: any) => ({
                ...item,
                __parentRow: row,
            }));
        } finally {
            loadingRow.value = null;
        }
    }
}

async function reloadSubTable(parentRow: any) {
    if (!props.fetchSubData) return;

    loadingRow.value = parentRow.id;
    try {
        const result = await props.fetchSubData(parentRow);
        subData[parentRow.id] = (result || []).map((item: any) => ({
            ...item,
            __parentRow: parentRow,
        }));
    } finally {
        loadingRow.value = null;
    }
}

defineExpose({
    reloadSubTable,
});
</script>

<template>
    <div class="relative rounded-xl border border-border bg-card overflow-hidden transition-colors duration-300">
        <ScrollArea class="max-h-[calc(100vh-220px)] overflow-auto custom-scrollbar">
            <Table class="w-full border-collapse">
                <TableHeader class="bg-secondary/80 sticky top-0 z-10 backdrop-blur-md">
                    <TableRow class="border-b border-border hover:bg-transparent">
                        <TableHead v-for="(col, i) in props.columns" :key="i"
                            class="h-12 px-4 text-xs font-black uppercase tracking-widest text-primary">
                            {{ col.label }}
                        </TableHead>
                        <TableHead v-if="props.actions || props.fetchSubData"
                            class="h-12 px-4 text-xs font-black uppercase tracking-widest text-primary text-center">
                            Action
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="props.loading">
                        <TableRow>
                            <TableCell :colspan="props.columns.length + (props.actions || props.fetchSubData ? 1 : 0)"
                                class="h-24 text-center">
                                Loading...
                            </TableCell>
                        </TableRow>
                    </template>

                    <template v-else-if="props.data?.length">
                        <template v-for="(row, rowIndex) in props.data" :key="row.id || rowIndex">
                            <TableRow class="group border-b border-border transition-colors hover:bg-primary/5">
                                <TableCell v-for="(col, colIndex) in props.columns" :key="col.key"
                                    class="px-4 py-4 text-sm font-medium text-foreground/80 align-middle"
                                    :class="col.onClick ? 'cursor-pointer hover:text-foreground' : ''"
                                    @click="col.onClick ? col.onClick(row, props.extraArgs) : null">
                                    <template v-if="col.render">
                                        <component v-if="typeof col.render(row, rowIndex, props.extraArgs) === 'object'"
                                            :is="col.render(row, rowIndex, props.extraArgs)" />
                                        <div v-else v-html="col.render(row, rowIndex, props.extraArgs)"></div>
                                    </template>

                                    <template v-else>
                                        <span
                                            class="block truncate max-w-[180px] group-hover:text-foreground transition-colors">
                                            {{ row[col.key] ?? "-" }}
                                        </span>
                                    </template>
                                </TableCell>

                                <TableCell v-if="props.actions || props.fetchSubData" class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <template v-for="(action, actionIdx) in props.actions" :key="actionIdx">
                                            <button v-if="!action.show || action.show(row)"
                                                :disabled="action.disabled ? action.disabled(row) : false"
                                                class="p-2 rounded-lg bg-secondary text-muted-foreground transition-all hover:bg-primary hover:text-primary-foreground disabled:opacity-20 disabled:cursor-not-allowed shadow-sm border border-border"
                                                @click.stop="action.onClick ? action.onClick(row, props.extraArgs) : null"
                                                :title="action.tooltip">
                                                <component :is="action.icon" class="w-4 h-4" />
                                            </button>
                                        </template>

                                        <button v-if="props.fetchSubData"
                                            class="p-2 rounded-lg bg-secondary text-muted-foreground transition-all hover:bg-primary hover:text-primary-foreground shadow-sm border border-border"
                                            @click.stop="toggleRow(row)">
                                            <component :is="expandedRow === row.id ? ChevronDown : ChevronRight"
                                                class="w-4 h-4" />
                                        </button>
                                    </div>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="expandedRow === row.id">
                                <TableCell
                                    :colspan="props.columns.length + (props.actions || props.fetchSubData ? 1 : 0)"
                                    class="bg-muted/40 px-6 py-4">
                                    <div v-if="loadingRow === row.id" class="text-sm text-muted-foreground">
                                        Loading...
                                    </div>

                                    <div v-else>
                                        <Table v-if="props.subColumns?.length" class="w-full border-collapse">
                                            <TableHeader>
                                                <TableRow class="border-b border-border">
                                                    <TableHead v-for="(col, i) in props.subColumns" :key="i"
                                                        class="px-4 py-2 text-xs font-black uppercase tracking-widest text-primary border-b border-border">
                                                        {{ col.label }}
                                                    </TableHead>
                                                    <TableHead v-if="props.subActions"
                                                        class="px-4 py-2 text-xs font-black uppercase tracking-widest text-primary border-b border-border text-center">
                                                        Action
                                                    </TableHead>
                                                </TableRow>
                                            </TableHeader>

                                            <TableBody>
                                                <TableRow v-for="(subRow, subIndex) in subData[row.id] || []"
                                                    :key="subRow.id || subIndex" class="border-b border-border">
                                                    <TableCell v-for="(col, i) in props.subColumns" :key="col.key"
                                                        class="px-4 py-3 text-sm"
                                                        :class="col.onClick ? 'cursor-pointer hover:text-foreground' : ''"
                                                        @click="col.onClick ? col.onClick(subRow, props.extraArgs) : null">
                                                        <template v-if="col.render">
                                                            <component
                                                                v-if="typeof col.render(subRow, subIndex, props.extraArgs) === 'object'"
                                                                :is="col.render(subRow, subIndex, props.extraArgs)" />
                                                            <div v-else
                                                                v-html="col.render(subRow, subIndex, props.extraArgs)">
                                                            </div>
                                                        </template>

                                                        <template v-else>
                                                            <span>
                                                                {{
                                                                    typeof subRow[col.key] === "string"
                                                                        ? subRow[col.key].substring(0, 50)
                                                                        : subRow[col.key] ?? "-"
                                                                }}
                                                            </span>
                                                        </template>
                                                    </TableCell>

                                                    <TableCell v-if="props.subActions" class="px-4 py-3 text-center">
                                                        <div class="flex items-center justify-center gap-3">
                                                            <template v-for="(action, i) in props.subActions" :key="i">
                                                                <button v-if="!action.show || action.show(subRow)"
                                                                    class="p-2 rounded-lg bg-secondary text-muted-foreground transition-all hover:bg-primary hover:text-primary-foreground shadow-sm border border-border"
                                                                    @click.stop="action.onClick?.(subRow, props.extraArgs)"
                                                                    :title="action.tooltip">
                                                                    <component :is="action.icon" class="w-4 h-4" />
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </TableCell>
                                                </TableRow>
                                            </TableBody>
                                        </Table>

                                        <div v-if="!subData[row.id] || !subData[row.id].length"
                                            class="text-xs text-muted-foreground italic mt-3">
                                            Nothing to show right now.
                                        </div>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </template>
                    </template>

                    <TableRow v-else>
                        <TableCell :colspan="props.columns.length + (props.actions || props.fetchSubData ? 1 : 0)"
                            class="h-32 text-center">
                            Nothing to show yet
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </ScrollArea>
    </div>
</template>
