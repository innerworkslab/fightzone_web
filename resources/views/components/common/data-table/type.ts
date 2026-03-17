import { Component } from "vue";

export interface ColumnDef<T> {
    label: string;
    key: keyof T | string;
    className?: string;
    type?: string;
    textKey?: string;
    routeName?: string;
    routeParamKey?: string;
    onClick?: (row: T, extraArgs?: any) => void;
    render?: (row: T, rowIndex?: number, extraArgs?: any) => any;
}

export interface ActionDef<T> {
    icon: any | ((row: T) => Component);
    tooltip?: string | ((row: T) => string);
    onClick?: (row: T, ...args: any[]) => void;
    show?: (row: T) => boolean;
    disabled?: (row: T) => boolean;
}

export interface Props<T> {
    data: T[];
    columns: ColumnDef<T>[];
    subColumns?: ColumnDef<T>[];
    actions?: ActionDef<T>[];
    subActions?: ActionDef<any>[];
    loading?: boolean;
    perPage?: number;
    startIndex?: number;
    extraArgs?: any;
    fetchSubData?: (row: any) => Promise<any[]>;
}
