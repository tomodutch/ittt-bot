import { Head, Link, usePage } from '@inertiajs/react';
import { type BreadcrumbItem, type SharedData, type Trigger } from '@/types';
import AppLayout from '@/layouts/app-layout';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Triggers', href: '/triggers' },
];

interface PageProps {
    triggers: Trigger[]
}

export default function TriggerIndex() {
    const { triggers } = usePage<SharedData & PageProps>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Triggers" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <div className="relative flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div className="absolute inset-0">
                        <PlaceholderPattern className="stroke-neutral-900/10 dark:stroke-neutral-100/10" />
                    </div>

                    <div className="relative z-10 flex flex-col gap-4 p-6">
                        <div className="flex items-center justify-between">
                            <h2 className="text-xl font-semibold">Your Triggers</h2>
                            <Link
                                href={route('triggers.create')}
                                className="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm text-white shadow hover:bg-blue-700"
                            >
                                + Add Trigger
                            </Link>
                        </div>

                        {triggers.length === 0 ? (
                            <p className="text-sm text-gray-500">You haven’t created any triggers yet.</p>
                        ) : (
                            <ul className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                {triggers.map((trigger) => (
                                    <li
                                        key={trigger.id}
                                        className="rounded-lg border border-border p-4 shadow-sm dark:border-sidebar-border bg-white dark:bg-neutral-900"
                                    >
                                        <div className="flex flex-col gap-1">
                                            <span className="text-sm text-muted-foreground uppercase tracking-wide">
                                                {trigger.executionType}
                                            </span>
                                            {/* <h3 className="text-lg font-semibold">At {trigger.time}</h3> */}
                                        </div>
                                        <div className="mt-4 flex gap-3 text-sm">
                                            <Link
                                                href={route('triggers.edit', trigger.id)}
                                                className="text-blue-500 hover:underline"
                                            >
                                                Edit
                                            </Link>
                                            <form
                                                method="POST"
                                                action={route('triggers.destroy', trigger.id)}
                                                onSubmit={(e) => {
                                                    e.preventDefault();
                                                    if (confirm('Are you sure you want to delete this trigger?')) {
                                                        e.currentTarget.submit();
                                                    }
                                                }}
                                            >
                                                <input type="hidden" name="_method" value="DELETE" />
                                                <button type="submit" className="text-red-500 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
