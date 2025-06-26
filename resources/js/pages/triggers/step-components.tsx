import React from 'react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { SimpleConditionalStep, FetchWeatherStep, SendEmailStep } from '@/types';

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
    onChange: (key: keyof SimpleConditionalStep['params'], value: any) => void
}) {
    const OPERATORS = ['==', '!=', '<', '>'];
    const cond = step.params;

    return (
        <div className="flex flex-wrap gap-2 items-center">
            <Select
                value={cond.left}
                onValueChange={(val) => onChange('left', val)}
            >
                <SelectTrigger><SelectValue placeholder="Variable" /></SelectTrigger>
                <SelectContent>
                    {/* {step.exposedVariables?.map((v) => (
                        <SelectItem key={v.name} value={v.name}>{v.label}</SelectItem>
                    ))} */}
                </SelectContent>
            </Select>

            <Select
                value={cond.operator}
                onValueChange={(val) => onChange('operator', val)}
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
                onChange={(e) => onChange('right', e.target.value)}
                placeholder="Value"
                className="min-w-[100px]"
            />
        </div>
    );
}

export function SendEmailForm({ step, onChange }: {
    step: SendEmailStep,
    onChange: (key: keyof SendEmailStep['params'], value: any) => void
}) {
    const p = step.params;
    return (
        <div className="space-y-4">
            <div className="grid gap-2">
                <Label>To</Label>
                <Input value={p.to} onChange={(e) => onChange('to', e.target.value)} />
            </div>
            <div className="grid gap-2">
                <Label>Subject</Label>
                <Input value={p.subject} onChange={(e) => onChange('subject', e.target.value)} />
            </div>
            <div className="grid gap-2">
                <Label>Body</Label>
                <Textarea value={p.body} onChange={(e) => onChange('body', e.target.value)} />
            </div>
        </div>
    );
}
