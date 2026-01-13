import { ref, onMounted, onUnmounted } from "vue";

export function useTheme() {
    const isDark = ref(false);
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

    const applyTheme = (dark: boolean) => {
        isDark.value = dark;
        if (dark) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    };

    const toggleTheme = () => {
        const newStatus = !isDark.value;
        applyTheme(newStatus);
        localStorage.setItem("theme", newStatus ? "dark" : "light");
    };

    const handleSystemChange = (event: MediaQueryListEvent) => {
        if (!localStorage.getItem("theme")) {
            applyTheme(event.matches);
        }
    };

    onMounted(() => {
        const savedTheme = localStorage.getItem("theme");

        if (savedTheme) {
            applyTheme(savedTheme === "dark");
        } else {
            applyTheme(mediaQuery.matches);
        }

        mediaQuery.addEventListener("change", handleSystemChange);
    });

    onUnmounted(() => {
        mediaQuery.removeEventListener("change", handleSystemChange);
    });

    return { isDark, toggleTheme };
}
