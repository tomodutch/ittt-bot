import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

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

export interface Schedule {
  id: string;
  triggerId: string;
  typeCode: number;
  oneTimeAt?: string | null;
  runTime?: string | null;
  daysOfWeek?: number[] | null;
  timezone: string;
  createdAt: string;
  updatedAt: string;
  deletedAt?: string | null;
}

export interface Trigger {
  id: string;
  name: string;
  description?: string | null;
  executionType: number;
  schedules?: Schedule[] | null;
  timezone: string;
  createdAt: string;
  updatedAt: string;
  deletedAt?: string | null;
}

export type Condition = {
  left: string;
  operator: string;
  right: string;
};

export type Step = {
  id: string;
  type: string;
  description?: string;
  order?: number;
  params: {
    message?: string;
    conditions?: Condition[];
    [key: string]: any;
  };
};
