<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";
import { Label } from "reka-ui";
import { routes } from "../../../js/ts/config/route.config";
import { SidebarChild, SidebarItem } from "@/router/type";

const route = useRoute();

const matchRoute = (pattern: string, path: string) => {
    if (!pattern) return false;
    const regex = new RegExp(`^${pattern.replace(/:[^/]+/g, "[^/]+")}$`);
    return regex.test(path);
};

const currentHeader = computed(() => {
    for (const item of routes as SidebarItem[]) {
        if (item.name === route.name && item.header) return item.header;
        if (item.children && item.children.length > 0) {
            for (const child of item.children as SidebarChild[]) {
                if (
                    child.name === route.name ||
                    matchRoute(child.route, route.path)
                ) {
                    return child.header || item.header || "";
                }
            }
            const hasMatchingChild = item.children.some((child) =>
                matchRoute(child.route, route.path)
            );
            if (hasMatchingChild && item.header) return item.header;
        }
    }
    return "";
});
</script>

<template>
    <header v-if="currentHeader">
        <Label class="text-xl font-bold text-primary">{{ currentHeader }}</Label>
    </header>
</template>
