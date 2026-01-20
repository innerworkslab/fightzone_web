export function getImageUrl(imageUrl: string) {
    const domain = import.meta.env.VITE_APP_URL;
    return `${domain}/storage/${imageUrl}`;
}

export const convertFileToImageUrl = (image: File | string) => {
    if (typeof image === "string") {
        return getImageUrl(image);
    }
    return URL.createObjectURL(image);
};

export function formatDateOnly(value?: string | null): string {
    if (!value) return "-";
    return value.split(" ")[0];
}

export function formatDateAndTime(value?: string | null): string {
    if (!value) return "-";

    const date = new Date(value);
    if (isNaN(date.getTime())) return value;

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    const seconds = String(date.getSeconds()).padStart(2, "0");

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

export function formatPriceOrNumber(
    value: number | string,
    currency = "",
    maxDecimals = 6
): string {
    if (value == null || value === "") return "";

    const number = typeof value === "string" ? Number(value) : value;
    if (Number.isNaN(number)) return "";

    return (
        number.toLocaleString("en-US", {
            maximumFractionDigits: maxDecimals,
        }) + (currency ? ` ${currency}` : "")
    );
}

export function formatStatus(value: string) {
    if (!value) return "-";
    return value
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
}

export const cleanPayload = <T extends object>(obj: T): Partial<T> => {
    return Object.fromEntries(
        Object.entries(obj).filter(([_, value]) => {
            if (typeof value === "number") return true;

            return value !== undefined && value !== null && value !== "";
        })
    ) as Partial<T>;
};
