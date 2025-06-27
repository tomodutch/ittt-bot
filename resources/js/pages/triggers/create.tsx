import React from 'react';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import TriggerBuilder from './step-builder';
import { ScheduleBuilder } from './schedule-builder';
import { type BreadcrumbItem, type Step, Schedule, Trigger } from '@/types';
import { Button } from '@/components/ui/button';
import { router, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Triggers', href: '/triggers' },
];


export default function CreateTriggerPage() {
    const { errors } = usePage().props
    const [steps, setSteps] = React.useState<Step[]>([]);
    const [schedule, setSchedule] = React.useState<Schedule>({
        id: null,
        triggerId: "",
        typeCode: 'Daily',
        time: '10:00',
        daysOfTheWeek: [],
        oneTimeAt: '',
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        createdAt: null,
        updatedAt: null,
    });

    function handleSave() {
        const newTrigger: Trigger = {
            id: null,
            name: 'New Trigger',
            description: 'This is a new trigger',
            executionType: "Webhook",
            schedules: [schedule],
            steps: steps,
            createdAt: null,
            updatedAt: null,
        }

        router.post('/triggers', newTrigger);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Trigger" />
            {JSON.stringify(errors)}
            <div className="p-4 space-y-8 pb-24">
                <div>
                    <h1 className="text-2xl font-semibold mb-4">Create Trigger</h1>
                    <ScheduleBuilder schedule={schedule} setSchedule={setSchedule} />
                </div>

                <div>
                    <h2 className="text-xl font-semibold mb-4">Steps</h2>
                    <TriggerBuilder steps={steps} setSteps={setSteps} />
                </div>
            </div>

            {/* Fixed Save Button at Bottom */}
            <div className="fixed bottom-0 left-0 right-0 z-10 border-t bg-background shadow-md">
                <div className="max-w-4xl mx-auto px-4 py-3 flex justify-end">
                    <Button onClick={handleSave}>Save Trigger</Button>
                </div>
            </div>
        </AppLayout>
    );
}
