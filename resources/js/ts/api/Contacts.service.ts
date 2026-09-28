import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type ContactType = "url" | "phone_number";

export type ContactsFilter = Filters & {
    search?: string;
    type?: string;
    is_active?: string;
};

export interface ContactData {
    id: number;
    type: ContactType;
    name: string;
    contact: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface ContactPayload {
    type: ContactType;
    name: string;
    contact: string;
    is_active?: boolean;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.CONTACT}`;

const useContacts = (filter: ContactsFilter = {}) => {
    return useFetch<APIResult<ContactData[]>>(baseURL, filter);
};

const useContactActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createContact = (payload: ContactPayload) => {
        return mutate(METHODS.POST, baseURL, payload);
    };

    const updateContact = (id: number, payload: Partial<ContactPayload>) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, payload);
    };

    const deleteContact = (id: number) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createContact,
        updateContact,
        deleteContact,
        toggleStatus,
    };
};

export const ContactsServices = {
    useContacts,
    useContactActions,
};
