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
    icon: Component;
    tooltip?: string;
    onClick?: (row: T, ...args: any[]) => void;
    show?: (row: T) => boolean;
    disabled?: (row: T) => boolean;
}

export interface Props<T> {
    data: T[];
    columns: ColumnDef<T>[];
    actions?: ActionDef<T>[];
    loading?: boolean;
    perPage?: number;
    startIndex?: number;
    extraArgs?: any;
}
