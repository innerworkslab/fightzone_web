import type { Filters } from "@/store/data";

export const buildApiFilter = (filters: Filters) => {
    const { search, is_active, ...rest } = filters;

    return {
        ...rest,
        ...(search ? { name: search } : {}),
        ...(is_active && is_active !== "all" ? { is_active } : {}),
    };
};
