import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';
import "./generated";
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

export type Schedule = App.Data.ScheduleData;;
export type ScheduleType = App.Enums.ScheduleType;
export type Trigger = App.Data.TriggerData & {
    steps: Step[]
};

export type SendEmailStep = App.Domain.Workflow.Steps.SendEmail.SendEmailStepData;
export type SimpleConditionalStep = App.Domain.Workflow.Steps.SimpleConditional.SimpleConditionalStepData;
export type FetchWeatherStep = App.Domain.Workflow.Steps.Weather.WeatherStepData;
export type Step = SendEmailStep | SimpleConditionalStep | WeatherStep;
