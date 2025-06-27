import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';
import "./generated";
import * as Generated from "./generated";
export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export type Schedule = Generated.ScheduleData;;
export type ScheduleType = Generated.ScheduleType;
export type Trigger = Generated.TriggerData;

export type StepType = Generated.StepType;
export type SendEmailStep = Generated.SendEmailStepData;
export type SimpleConditionalStep = Generated.SimpleConditionalStepData;
export type FetchWeatherStep = Generated.WeatherStepData;
export type Step = Generated.StepData;
