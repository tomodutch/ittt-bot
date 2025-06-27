import React from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { SimpleConditionalStep, FetchWeatherStep, SendEmailStep, Operator } from '@/types';

export function FetchWeatherForm({ step, onChange }: {
    step: FetchWeatherStep,
    onChange: (newParams: Partial<FetchWeatherStep['params']>) => void;
}) {
    return (
        <div className="grid gap-2">
            <Label>Location</Label>
            <Input
                value={step.params.location}
                onChange={(e) => onChange({ location: e.target.value })}
                placeholder="City or coordinates"
            />
        </div>
    );
}

export function ConditionForm({ step, onChange }: {
    step: SimpleConditionalStep,
    onChange: (newParams: Partial<SimpleConditionalStep['params']>) => void;
}) {
    const OPERATORS: Operator[] = ['==', '!=', '<', '>'];
    const cond = step.params;

    return (
        <div className="flex flex-wrap gap-2 items-center">
            <Input
                value={cond.left}
                onChange={(e) => onChange({ ...cond, left: e.target.value })}
                placeholder="Value"
                className="min-w-[100px]"
            />

            <Select
                value={cond.operator}
                onValueChange={(val) => {
                    if (!val) {
                        return;
                    }

                    onChange({ ...cond, operator: val as Operator });
                }}
            >
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                    {OPERATORS.map(op => (
                        <SelectItem key={op} value={op}>{op}</SelectItem>
                    ))}
                </SelectContent>
            </Select>

            <Input
                value={cond.right}
                onChange={(e) => onChange({ ...cond, right: e.target.value })}
                placeholder="Value"
                className="min-w-[100px]"
            />
        </div>
    );
}

export function SendEmailForm({ step, onChange }: {
    step: SendEmailStep,
    onChange: (newParams: Partial<SendEmailStep['params']>) => void;
}) {
    const p = step.params;
    return (
        <div className="space-y-4">
            <div className="grid gap-2">
                <Label>To</Label>
                <Input value={p.to} onChange={(e) => onChange({ ...p, to: e.target.value })} />
            </div>
            <div className="grid gap-2">
                <Label>Subject</Label>
                <Input value={p.subject} onChange={(e) => onChange({ ...p, subject: e.target.value })} />
            </div>
            <div className="grid gap-2">
                <Label>Body</Label>
                <Textarea value={p.body} onChange={(e) => onChange({ ...p, body: e.target.value })} />
            </div>
        </div>
    );
}
