import PackagesForm from "../../../views/components/packages/PackagesForm.vue";
import PurchasesForm from "../../../views/components/purchases/PurchasesForm.vue";
import DepositsForm from "../../../views/components/deposits/DepositsForm.vue";
import UsersPasswordChangeForm from "../../../views/components/users/UsersPasswordChangeForm.vue";
import AdminsChangePasswordForm from "../../../views/components/admins/AdminsChangePasswordForm.vue";
import CourseCategoriesForm from "../../../views/components/course-categories/CourseCategoriesForm.vue";
import LessonDayVideoForm from "../../../views/components/lesson-day-videos/lessonDayVideoForm.vue";
import WalkInForm from "../../../views/components/walk-ins/WalkInForm.vue";
import RestDayVideoForm from "../../../views/components/rest-videos/RestDayVideoForm.vue";
import FeaturedImagesForm from "../../../views/components/featured-images/FeaturedImagesForm.vue";
import TechniqueCategoriesForm from "../../../views/components/technique-categories/TechniqueCategoriesForm.vue";
import TechniquesForm from "../../../views/components/techniques/TechniquesForm.vue";

export const getFormConfig = (initialValues: any, isReadMode: boolean) => ({
    // #A
    adminsChangePassword: {
        form: AdminsChangePasswordForm,
        header: "Change Admin Password",
        message: "",
    },
    // #C
    courseCategory: {
        form: CourseCategoriesForm,
        header: `${
            initialValues ? (isReadMode ? "(Read Only) " : "Update") : "Create"
        } Course Category`,
        message: "",
    },
    // #D
    deposit: {
        form: DepositsForm,
        header: `${isReadMode ? "Deposit Details" : "Reject Deposit"}`,
        message: isReadMode
            ? "View deposit details and receipt"
            : "Provide a reason for rejecting this deposit",
    },
    // #F
    featuredImage: {
        form: FeaturedImagesForm,
        header: `${
            initialValues?.id
                ? isReadMode
                    ? "(Read Only) "
                    : "Update"
                : "Upload"
        } Featured Image`,
        message: initialValues?.id
            ? isReadMode
                ? "View image for this featured image"
                : "Update image for this featured image"
            : "Upload an image for this featured image",
    },
    // #P
    package: {
        form: PackagesForm,
        header: `${
            initialValues ? (isReadMode ? "(Read Only) " : "Update") : "Create"
        } Package`,
        message: "",
    },
    purchase: {
        form: PurchasesForm,
        header: `${isReadMode ? "Purchase Details" : "Reject Purchase"}`,
        message: isReadMode
            ? "View purchase details"
            : "Provide a reason for rejecting this purchase",
    },
    // #L
    lessonDayVideo: {
        form: LessonDayVideoForm,
        header: `${
            initialValues?.id
                ? isReadMode
                    ? "(Read Only) "
                    : "Update"
                : "Upload"
        } Lesson Day Video`,
        message: initialValues?.id
            ? isReadMode
                ? "View video for this lesson day"
                : "Update video for this lesson day"
            : "Upload a video for this lesson day",
    },
    // #R
    restDayVideo: {
        form: RestDayVideoForm,
        header: `${
            initialValues?.id
                ? isReadMode
                    ? "(Read Only) "
                    : "Update"
                : "Upload"
        } Rest Day Video`,
        message: initialValues?.id
            ? isReadMode
                ? "View video for this rest day"
                : "Update video for this rest day"
            : "Upload a video for this rest day",
    },
    techniqueCategory: {
        form: TechniqueCategoriesForm,
        header: `${
            initialValues ? (isReadMode ? "(Read Only) " : "Update") : "Create"
        } Technique Category`,
        message: "",
    },
    technique: {
        form: TechniquesForm,
        header: `${
            initialValues?.id
                ? isReadMode
                    ? "(Read Only) "
                    : "Update"
                : "Create"
        } Technique`,
        message: initialValues?.id
            ? isReadMode
                ? "View technique video"
                : "Update technique video"
            : "Add a technique video link",
    },
    // #U
    usersChangePassword: {
        form: UsersPasswordChangeForm,
        header: "Change User Password",
        message: "",
    },
    // #W
    walkInForm: {
        form: WalkInForm,
        header: `${
            initialValues
                ? isReadMode
                    ? "(Read Only) "
                    : "Update"
                : "Scan QR for"
        } Walk In`,
        message: "",
    },
});
