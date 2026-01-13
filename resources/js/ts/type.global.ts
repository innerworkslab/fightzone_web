export interface Filters {
    page?: number;
    limit?: number;
    currentPage?: string;
}

export interface Methods {
    GET: "GET";
    POST: "POST";
    PUT: "PUT";
    PATCH: "PATCH";
    DELETE: "DELETE";
}
