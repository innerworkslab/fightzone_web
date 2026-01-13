import { Component } from "vue";

export interface SidebarChild {
    label: string;
    name: string;
    header?: string;
    route: string;
}

export interface SidebarItem {
    label: string;
    name: string;
    header?: string;
    icon?: Component;
    route: string;
    children?: SidebarChild[];
}
