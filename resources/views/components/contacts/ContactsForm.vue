<script setup lang="ts">
import { computed, watch } from "vue";
import * as yup from "yup";
import { useForm, useField, ErrorMessage } from "vee-validate";
import { toast } from "vue3-toastify";
import { Button } from "@/components/ui/button";
import { useDataStore } from "@/store/data";
import { useModalStore } from "@/store/modal";
import {
    ContactsServices,
    type ContactPayload,
    type ContactType,
} from "@/api/Contacts.service";

const dataStore = useDataStore();
const modalStore = useModalStore();

const contactTypeOptions = [
    { label: "URL", value: "url" },
    { label: "Phone Number", value: "phone_number" },
];

const isUpdateMode = computed(() => !!modalStore.initialValues);
const isReadMode = computed(() => modalStore.isReadMode);

const { createContact, updateContact, loading: isSubmitting } =
    ContactsServices.useContactActions();

const schema = yup.object({
    type: yup.string().oneOf(["url", "phone_number"]).required("Type is required"),
    name: yup.string().required("Name is required"),
    contact: yup.string().required("Contact is required"),
});

const { handleSubmit, setValues } = useForm<ContactPayload>({
    validationSchema: schema,
    initialValues: {
        type: "url",
        name: "",
        contact: "",
        is_active: true,
    },
});

const { value: type } = useField<ContactType>("type");
const { value: name } = useField<string>("name");
const { value: contact } = useField<string>("contact");

watch(
    () => modalStore.initialValues,
    (newVal) => {
        if (!newVal) return;
        setValues({
            type: newVal.type ?? "url",
            name: newVal.name ?? "",
            contact: newVal.contact ?? "",
            is_active: newVal.is_active ?? true,
        });
    },
    { immediate: true },
);

const submitForm = handleSubmit(async (values) => {
    if (isReadMode.value) return;

    const response = isUpdateMode.value
        ? await updateContact(modalStore.initialValues.id, values)
        : await createContact(values);

    if (response?.success) {
        toast.success(
            isUpdateMode.value
                ? "Contact updated successfully"
                : "Contact created successfully",
        );
        dataStore.triggerRefresh();
        modalStore.closeModal();
    }
});
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4">
        <div>
            <FormSelect id="type" v-model="type" label="Type" :options="contactTypeOptions" :disabled="isReadMode" />
            <ErrorMessage name="type" class="block text-start text-red-500 text-sm mt-1" />
        </div>

        <div>
            <FormInput id="name" v-model="name" label="Name" type="text" placeholder="Enter contact name"
                :disabled="isReadMode" />
            <ErrorMessage name="name" class="block text-start text-red-500 text-sm mt-1" />
        </div>

        <div>
            <FormInput id="contact" v-model="contact" label="Contact" type="text"
                placeholder="Enter link or phone number" :disabled="isReadMode" />
            <ErrorMessage name="contact" class="block text-start text-red-500 text-sm mt-1" />
        </div>

        <div v-if="!isReadMode" class="w-full flex justify-end pt-6">
            <Button variant="default" type="submit" :disabled="isSubmitting" class="min-w-[140px]">
                {{ isSubmitting ? "Processing..." : isUpdateMode ? "Update Contact" : "Create Contact" }}
            </Button>
        </div>
    </form>
</template>
