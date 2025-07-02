import { Head, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card } from '@/components/ui/card';
import { type SharedData, type Trigger, type Step, type Schedule } from '@/types';
import ShowStepsGraph from './show-steps-graph';

const WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

function describeSchedule(schedule: Schedule) {
  const { typeCode, time: runTime, oneTimeAt, daysOfTheWeek: daysOfWeek, timezone } = schedule;

  // fallback timezone
  const tz = timezone || Intl.DateTimeFormat().resolvedOptions().timeZone;

  switch (typeCode) {
    case "Once": {
      if (!oneTimeAt) return 'Once at [no date/time set]';
      const date = new Date(oneTimeAt);
      const formatted = new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
        timeZone: tz,
      }).format(date);
      return `Once at ${formatted} (${tz})`;
    }
    case "Daily": {
      if (!runTime) return 'Every day at [no time set]';
      // Parse runTime like "14:30" as today’s date + time in tz, then format
      const [hours, minutes] = runTime.split(':').map(Number);
      const now = new Date();
      // Create a date in tz at today's date and runTime
      const dateInTz = new Date(Date.UTC(
        now.getUTCFullYear(),
        now.getUTCMonth(),
        now.getUTCDate(),
        hours,
        minutes
      ));
      // Format time only, with tz info
      const formatted = new Intl.DateTimeFormat(undefined, {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: tz,
        timeZoneName: 'short',
      }).format(dateInTz);
      return `Every day at ${formatted}`;
    }
    case "Weekly": {
      if (!runTime) return 'Every week at [no time set]';
      const days =
        (daysOfWeek?.length ?? 0) > 0
          ? daysOfWeek!.map((d) => WEEKDAYS[d]).join(', ')
          : '[no days selected]';

      const [hours, minutes] = runTime.split(':').map(Number);
      const now = new Date();
      const dateInTz = new Date(Date.UTC(
        now.getUTCFullYear(),
        now.getUTCMonth(),
        now.getUTCDate(),
        hours,
        minutes
      ));
      const formatted = new Intl.DateTimeFormat(undefined, {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: tz,
        timeZoneName: 'short',
      }).format(dateInTz);

      return `Every week on ${days} at ${formatted}`;
    }
    default:
      return 'Unknown schedule';
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
            <ShowStepsGraph steps={trigger.steps} />
          </Card>
        )}

        <Card className="p-4 space-y-2">
          <h2 className="text-xl font-semibold">Executions</h2>
          {trigger.executions?.length === 0 ? (
            <p className="text-muted-foreground">No executions yet.</p>
          ) : (
            <ul className="divide-y">
              {trigger.executions?.map((exec) => (
                <li key={exec.id} className="py-2">
                  <Link href={route('executions.show', {
                    triggerId: trigger.id,
                    executionId: exec.id,
                  })} className="block hover:bg-muted rounded-md p-2">
                    <div className="flex justify-between items-center">
                      <div>
                        <div className="font-medium">
                          {exec.runReasonCode} — {exec.statusCode ?? 'Unknown'}
                        </div>
                        <div className="text-sm text-muted-foreground">
                          Started at: {new Date(exec.createdAt!).toLocaleString()}
                        </div>
                        {exec.finishedAt && (
                          <div className="text-sm text-muted-foreground">
                            Finished at: {new Date(exec.finishedAt).toLocaleString()}
                          </div>
                        )}
                      </div>
                    </div>
                  </Link>
                </li>
              ))}
            </ul>
          )}
        </Card>
      </div>
    </AppLayout>
  );
}
