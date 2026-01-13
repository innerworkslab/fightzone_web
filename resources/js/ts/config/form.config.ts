export const getFormConfig = (initialValues: any, isReadMode: boolean) => ({
    admin: {
        form: null,
        header: `${
            initialValues ? (isReadMode ? "(Read Only) " : "Update") : "Create"
        } Admin`,
        message: "",
    },
});
