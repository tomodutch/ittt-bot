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
import { Schedule } from '@/types';

type Props = {
    schedule: Schedule;
    setSchedule: (schedule: Schedule) => void;
};

export const ScheduleBuilder = ({ schedule, setSchedule }: Props) => {
    const [expanded, setExpanded] = useState(false);

    const scheduleText = () => {
        if (schedule.typeCode === 'Once') {
            return schedule.oneTimeAt
                ? `once at ${new Date(schedule.oneTimeAt).toLocaleString()}`
                : 'once at [select date/time]';
        }

        if (schedule.typeCode === 'Weekly') {
            if (!schedule.daysOfTheWeek || schedule.daysOfTheWeek?.length === 0) return 'every week [select days]';
            const days = schedule.daysOfTheWeek
                .map((d) => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][d])
                .join(', ');
            return `every week on ${days} at ${schedule.time}`;
        }

        return `every day at ${schedule.time}`;
    };

    const toggleDay = (day: number) => {
        if (!schedule.daysOfTheWeek) {
            return;
        }

        const isActive = schedule.daysOfTheWeek.includes(day);
        const updated = isActive
            ? schedule.daysOfTheWeek.filter((d) => d !== day)
            : [...schedule.daysOfTheWeek, day];
        setSchedule({ ...schedule, daysOfTheWeek: updated });
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
                            value={schedule.typeCode}
                            onValueChange={(val) =>
                                setSchedule({ ...schedule, typeCode: val as Schedule['typeCode'] })
                            }
                        >
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="Daily">Daily</SelectItem>
                                <SelectItem value="Weekly">Weekly</SelectItem>
                                <SelectItem value="Once">Once</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    {/* If weekly: Days of week */}
                    {schedule.typeCode === 'Weekly' && (
                        <div className="grid gap-1">
                            <Label>Days of Week</Label>
                            <div className="flex flex-wrap gap-2">
                                {[0, 1, 2, 3, 4, 5, 6].map((day) => {
                                    const checked = schedule.daysOfTheWeek?.includes(day);
                                    return (
                                        <label
                                            key={day}
                                            className={`flex items-center gap-1 cursor-pointer select-none px-2 py-1 rounded-md border text-xs ${checked
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
                    {schedule.typeCode === 'Once' ? (
                        <div className="grid gap-1">
                            <Label>Date and Time</Label>
                            <Input
                                type="datetime-local"
                                value={schedule.oneTimeAt || ''}
                                onChange={(e) =>
                                    setSchedule({ ...schedule, oneTimeAt: e.target.value })
                                }
                            />
                        </div>
                    ) : (
                        <div className="grid gap-1">
                            <Label>Time</Label>
                            <Input
                                type="time"
                                value={schedule.time || ''}
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
