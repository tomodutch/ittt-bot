import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { Schedule } from '@/types';
import TimezoneSelect from './timezone-select';

type Props = {
    schedule: Schedule;
    setSchedule: (schedule: Schedule) => void;
};

export const ScheduleBuilder = ({ schedule, setSchedule }: Props) => {
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

            <TimezoneSelect
                timezone={schedule.timezone || Intl.DateTimeFormat().resolvedOptions().timeZone}
                setTimezone={(tz) => setSchedule({ ...schedule, timezone: tz })}
            />
        </div>
    );
};

export default ScheduleBuilder;
