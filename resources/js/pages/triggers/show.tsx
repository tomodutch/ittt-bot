import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card } from '@/components/ui/card';
import { type SharedData, type Trigger, type Step, type Schedule } from '@/types';

const WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

function describeSchedule(schedule: Schedule) {
    const { typeCode, time: runTime, oneTimeAt, daysOfTheWeek: daysOfWeek } = schedule;
    switch (typeCode) {
        case "Once":
            return `Once at ${new Date(oneTimeAt ?? '').toLocaleString()}`;
        case "Daily":
            return `Every day at ${runTime}`;
        case "Weekly":
            const days =
                (daysOfWeek?.length ?? 0) > 0
                    ? daysOfWeek!.map((d) => WEEKDAYS[d]).join(', ')
                    : '[no days selected]';
            return `Every week on ${days} at ${runTime}`;
        default:
            return 'Unknown schedule';
    }
}

function describeStep(step: Step) {
    switch (step.type) {
        case 'fetchWeather':
            return `Fetch weather for "${step.params.location}"`;
        case 'condition':
            const c = step.params;
            if (!c) return 'Condition (missing)';
            return `If ${c.left} ${c.operator} ${c.right}`;
        case 'sendEmail':
            return `Send email to "${step.params.to}" with subject "${step.params.subject}"`;
        default:
            return 'Unknown step';
    }
}

interface PageProps {
    trigger: Trigger;
}

export default function ShowTrigger() {
    const { trigger } = usePage<SharedData & PageProps>().props;

    return (
        <AppLayout>
            <Head title={trigger.name} />

            <div className="p-6 space-y-6">
                <h1 className="text-3xl font-semibold">{trigger.name}</h1>
                {trigger.description && <p className="text-muted-foreground">{trigger.description}</p>}

                <Card className="p-4 space-y-2">
                    <h2 className="text-xl font-semibold">Schedule</h2>
                    <ul className="list-disc ml-6">
                        {trigger?.schedules?.map((s, i) => (
                            <li key={i}>{describeSchedule(s)}</li>
                        ))}
                    </ul>
                </Card>

                {trigger.steps && trigger.steps.length > 0 && (
                    <Card className="p-4 space-y-2">
                        <h2 className="text-xl font-semibold">Steps</h2>
                        <ol className="list-decimal ml-6">
                            {trigger.steps.map((step, i) => (
                                <li key={i}>{describeStep(step)}</li>
                            ))}
                        </ol>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}
