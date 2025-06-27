import { useEffect, useState } from 'react';
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
} from '@/components/ui/select';
import { Label } from '@/components/ui/label';

type Props = {
  timezone: string;
  setTimezone: (tz: string) => void;
};

const TimezoneSelect = ({ timezone, setTimezone }: Props) => {
  const [timezones, setTimezones] = useState<string[]>([]);

  useEffect(() => {
    try {
      const supported = Intl.supportedValuesOf('timeZone');
      setTimezones(supported);
    } catch (e) {
      console.warn('Intl.supportedValuesOf not supported in this environment.');
      setTimezones([]);
    }
  }, []);

  return (
    <div className="grid gap-1">
      <Label htmlFor="timezone">Timezone</Label>
      <Select value={timezone} onValueChange={(val) => setTimezone(val)}>
        <SelectTrigger id="timezone">
          <SelectValue placeholder="Select a timezone" />
        </SelectTrigger>
        <SelectContent className="max-h-60 overflow-y-auto">
          {timezones.map((tz) => (
            <SelectItem key={tz} value={tz}>
              {tz}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
    </div>
  );
};

export default TimezoneSelect;
