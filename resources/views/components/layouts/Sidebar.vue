<script setup lang="ts">
import { reactive, computed, ref, watch, onMounted, onUnmounted } from "vue";
import { useRoute, RouterLink, useRouter } from "vue-router";
import { routes } from "@/config/route.config";
import { SidebarItem } from "@/router/type";
import { deleteCookie } from '@/lib/utils.cookies';
import { COOKIES, LOCALSTORAGE } from '@/constant/global.constant';
import { deleteLocalStorage, getDecryptedLocalStorage } from "@/lib/utils.localStorage";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import {
    ChevronRight,
    LogOut, User, Sun, Moon
} from "lucide-vue-next";

const route = useRoute();
const router = useRouter();
const dataStore = useDataStore();
const modalStore = useModalStore();

const authUser = ref(getDecryptedLocalStorage(LOCALSTORAGE.AUTH_USER));
const isDark = ref(true);
const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

const state = reactive({
    showDropdown: "",
});

const gymBranches = ref<any[]>([]);
const boxingRoster = ref<any[]>([]);
const gearInventory = ref<any[]>([]);
const fightSchedules = ref<any[]>([]);

const applyTheme = (dark: boolean) => {
    isDark.value = dark;
    if (dark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

const toggleTheme = () => {
    const newStatus = !isDark.value;
    applyTheme(newStatus);
    localStorage.setItem('theme', newStatus ? 'dark' : 'light');
};

const handleSystemChange = (event: MediaQueryListEvent) => {
    if (!localStorage.getItem('theme')) {
        applyTheme(event.matches);
    }
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        applyTheme(savedTheme === 'dark');
    } else {
        applyTheme(mediaQuery.matches);
    }
    mediaQuery.addEventListener('change', handleSystemChange);
});

onUnmounted(() => {
    mediaQuery.removeEventListener('change', handleSystemChange);
});

const handleLogout = () => {
    modalStore.openConfirmModal({
        message: "Are you sure you want to logout?",
        onApprove: () => {
            deleteCookie(COOKIES.ACCESS_TOKEN);
            deleteLocalStorage(LOCALSTORAGE.PERMISSIONS);
            router.push('/');
        },
        approveBtnText: "Logout",
    });
}

const sidebarItems = computed<SidebarItem[]>(() => {
    return routes
        .map((item) => {
            if (item.children) {
                const filteredChildren = item.children.filter(
                    (child) => child.name && child.label?.trim() !== ""
                );
                return filteredChildren.length > 0
                    ? { ...item, children: filteredChildren }
                    : null;
            }
            return item.name && item.label !== "" ? item : null;
        })
        .filter(Boolean) as SidebarItem[];
});

function getResourceSegment(itemPath: string) {
    return route.path === itemPath;
}

function hasActiveChild(item: SidebarItem) {
    return item.children?.some((child) => route.path === child.route);
}

function toggleDropdown(name: string) {
    state.showDropdown = state.showDropdown === name ? "" : name;
}

watch(
    () => route.path,
    () => {
        const activeParent = sidebarItems.value.find((item) => hasActiveChild(item));
        if (activeParent) state.showDropdown = activeParent.name;
    },
    { immediate: true }
);
</script>

<template>
    <nav class="side-nav flex flex-col h-full max-h-screen transition-colors duration-300">
        <div class="p-6 border-b border-sidebar-border bg-gradient-to-b from-primary/10 to-transparent">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div
                            class="h-10 w-10 rounded-full bg-secondary border-2 border-primary flex items-center justify-center overflow-hidden">
                            <User class="w-5 h-5 text-muted-foreground" />
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-foreground truncate w-24 uppercase">
                            {{ authUser?.username ?? "CHAMPION" }}
                        </span>
                    </div>
                </div>

                <button @click="toggleTheme"
                    class="p-2 rounded-lg bg-secondary/50 border border-sidebar-border text-muted-foreground hover:text-primary transition-all">
                    <Sun v-if="isDark" class="w-4 h-4" />
                    <Moon v-else class="w-4 h-4" />
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6 custom-scrollbar">
            <ul class="space-y-4">
                <li v-for="item in sidebarItems" :key="item.name">
                    <div v-if="item.children" class="rounded-xl bg-secondary/30 ring-1 ring-sidebar-border">
                        <button @click="toggleDropdown(item.name)" class="group flex w-full text-start justify-between rounded-xl px-4 py-3 text-xs font-black
                                       text-muted-foreground transition-all duration-200 uppercase tracking-widest
                                       hover:text-foreground"
                            :class="{ 'bg-primary text-primary-foreground': hasActiveChild(item) || state.showDropdown === item.name }">
                            <div class="flex items-center gap-3">
                                <component v-if="item.icon" :is="item.icon" class="h-4 w-4"
                                    :class="hasActiveChild(item) || state.showDropdown === item.name ? 'text-primary-foreground' : 'text-primary'" />
                                <span>{{ item.label }}</span>
                            </div>
                            <ChevronRight class="h-4 w-4 transition-transform duration-300"
                                :class="{ 'rotate-90': hasActiveChild(item) || state.showDropdown === item.name }" />
                        </button>

                        <ul v-show="state.showDropdown === item.name || hasActiveChild(item)"
                            class="mt-2 space-y-1 pb-3 pl-4 pr-2">
                            <li v-for="child in item.children" :key="child.name">
                                <RouterLink :to="child.route" class="group flex items-center gap-3 rounded-lg px-3 py-2 text-xs font-bold
                                                  text-muted-foreground transition-all duration-200 uppercase
                                                  hover:text-primary"
                                    :class="{ 'text-foreground': getResourceSegment(child.route) }">
                                    <div class="h-1 w-2 rounded-full transition-all"
                                        :class="getResourceSegment(child.route) ? 'bg-primary w-4' : 'bg-muted'" />
                                    <span class="truncate">{{ child.label }}</span>
                                </RouterLink>
                            </li>
                        </ul>
                    </div>

                    <template v-else>
                        <RouterLink v-if="item.label" :to="item.route" class="group flex items-center gap-3 rounded-xl px-4 py-3 text-xs font-black
                                          text-muted-foreground transition-all duration-200 uppercase tracking-widest
                                          hover:bg-secondary hover:text-foreground"
                            :class="{ 'bg-primary text-primary-foreground shadow-lg': getResourceSegment(item.route) }">
                            <component v-if="item.icon" :is="item.icon" class="h-4 w-4"
                                :class="getResourceSegment(item.route) ? 'text-primary-foreground' : 'text-primary'" />
                            <span>{{ item.label }}</span>
                        </RouterLink>
                    </template>
                </li>
            </ul>
        </div>

        <div class="p-4 border-t border-sidebar-border">
            <button @click="handleLogout"
                class="flex w-full items-center gap-3 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-all">
                <LogOut class="w-4 h-4" />
                <span>log out</span>
            </button>
        </div>
    </nav>
</template>

<style scoped>
.side-nav {
    position: relative;
    background-color: var(--sidebar);
    border-right: 1px solid var(--sidebar-border);
}

.side-nav::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle at 2px 2px, var(--primary) 1px, transparent 0);
    background-size: 24px 24px;
    opacity: 0.03;
    pointer-events: none;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--muted);
}
</style>
