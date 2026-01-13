import { useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/constant.global";

export interface LoginPayload {
    username: string;
    password: string;
}
const baseURL = `/${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.LOGIN}`;
const useAuthActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const login = (payload: LoginPayload) => {
        const formData = new FormData();
        formData.append("username", payload.username);
        formData.append("password", payload.password);
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
