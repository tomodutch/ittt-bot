import { useState } from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from '@/components/ui/select';
import { ChevronDown, ChevronUp } from 'lucide-react';

export type Schedule = {
  interval: 'daily' | 'weekly' | 'once';
  time: string;
  daysOfWeek: number[];
  onceAt: string;
};

type Props = {
  schedule: Schedule;
  setSchedule: (schedule: Schedule) => void;
};

export const ScheduleBuilder = ({ schedule, setSchedule }: Props) => {
  const [expanded, setExpanded] = useState(false);

  const scheduleText = () => {
    if (schedule.interval === 'once') {
      return schedule.onceAt
        ? `once at ${new Date(schedule.onceAt).toLocaleString()}`
        : 'once at [select date/time]';
    }

    if (schedule.interval === 'weekly') {
      if (schedule.daysOfWeek.length === 0) return 'every week [select days]';
      const days = schedule.daysOfWeek
        .map((d) => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][d])
        .join(', ');
      return `every week on ${days} at ${schedule.time}`;
    }

    return `every day at ${schedule.time}`;
  };

  const toggleDay = (day: number) => {
    const isActive = schedule.daysOfWeek.includes(day);
    const updated = isActive
      ? schedule.daysOfWeek.filter((d) => d !== day)
      : [...schedule.daysOfWeek, day];
    setSchedule({ ...schedule, daysOfWeek: updated });
  };

  return (
    <div className="w-full space-y-4 border rounded-md p-4 bg-background">
      <div
        className="flex justify-between items-center cursor-pointer"
        onClick={() => setExpanded(!expanded)}
      >
        <div className="font-medium text-sm">{scheduleText()}</div>
        {expanded ? (
          <ChevronUp className="w-5 h-5 text-muted-foreground" />
        ) : (
          <ChevronDown className="w-5 h-5 text-muted-foreground" />
        )}
      </div>

      {expanded && (
        <div className="space-y-4">
          {/* Interval */}
          <div className="grid gap-1">
            <Label>Interval</Label>
            <Select
              value={schedule.interval}
              onValueChange={(val) =>
                setSchedule({ ...schedule, interval: val as Schedule['interval'] })
              }
            >
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="daily">Daily</SelectItem>
                <SelectItem value="weekly">Weekly</SelectItem>
                <SelectItem value="once">Once</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {/* If weekly: Days of week */}
          {schedule.interval === 'weekly' && (
            <div className="grid gap-1">
              <Label>Days of Week</Label>
              <div className="flex flex-wrap gap-2">
                {[0, 1, 2, 3, 4, 5, 6].map((day) => {
                  const checked = schedule.daysOfWeek.includes(day);
                  return (
                    <label
                      key={day}
                      className={`flex items-center gap-1 cursor-pointer select-none px-2 py-1 rounded-md border text-xs ${
                        checked
                          ? 'bg-primary text-white border-primary'
                          : 'bg-muted text-muted-foreground border-border'
                      }`}
                      onClick={() => toggleDay(day)}
                    >
                      {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][day]}
                    </label>
                  );
                })}
              </div>
            </div>
          )}

          {/* Time / Date-Time */}
          {schedule.interval === 'once' ? (
            <div className="grid gap-1">
              <Label>Date and Time</Label>
              <Input
                type="datetime-local"
                value={schedule.onceAt}
                onChange={(e) =>
                  setSchedule({ ...schedule, onceAt: e.target.value })
                }
              />
            </div>
          ) : (
            <div className="grid gap-1">
              <Label>Time</Label>
              <Input
                type="time"
                value={schedule.time}
                onChange={(e) =>
                  setSchedule({ ...schedule, time: e.target.value })
                }
              />
            </div>
          )}
        </div>
      )}
    </div>
  );
};

export default ScheduleBuilder;
