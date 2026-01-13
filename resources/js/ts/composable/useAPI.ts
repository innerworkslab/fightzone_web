// src/composables/useApi.ts
import { ref } from "vue";
import type { AxiosRequestConfig, AxiosResponse } from "axios";
import api from "@/lib/utils.axios";

export interface APIResult<T> {
    success: boolean;
    data: T;
}

export function useApi<T = any>() {
    const data = ref<T | null>(null);
    const error = ref<any>(null);
    const loading = ref<boolean>(false);

    const execute = async (config: AxiosRequestConfig): Promise<T | null> => {
        loading.value = true;
        error.value = null;

        try {
            const response: AxiosResponse<T> = await api(config);
            data.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err;
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return { data, error, loading, execute };
}

export function useFetch<T>(url: string, params?: Record<string, any>) {
    const { data, error, loading, execute } = useApi<T>();

    const fetchLocal = () => execute({ method: "GET", url, params });
    fetchLocal();

    return { data, error, loading, refresh: fetchLocal };
}

export function useMutation<T = any, R = any>() {
    const { data, error, loading, execute } = useApi<R>();

    const mutate = async (
        method: "POST" | "PUT" | "PATCH" | "DELETE",
        url: string,
        payload?: T,
        config?: AxiosRequestConfig
    ) => {
        return await execute({
            ...config,
            method,
            url,
            data: payload,
        });
    };

    return { data, error, loading, mutate };
}
