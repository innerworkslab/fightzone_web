import { useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";

export interface LoginPayload {
    username: string;
    password: string;
    fcm_token?: string | null;
}
const baseURL = `/${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.LOGIN}`;
const useAuthActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const login = (payload: LoginPayload) => {
        const formData = new FormData();
        formData.append("username", payload.username);
        formData.append("password", payload.password);
        if (payload.fcm_token) {
            formData.append("fcm_token", payload.fcm_token);
        }
        return mutate(METHODS.POST, baseURL, formData);
    };

    return {
        loading,
        error,
        data,
        login,
    };
};

export const AuthServices = {
    useAuthActions,
};
