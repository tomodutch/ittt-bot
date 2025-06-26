import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Plus, Trash2, ChevronDown, ChevronUp } from 'lucide-react';

type StepType = 'fetchWeather' | 'condition' | 'sendEmail';

export type Step = {
    id: string;
    type: StepType;
    params: Record<string, any>;
    exposedVariables?: { name: string; label: string }[];
};

const OPERATORS = ['==', '!=', '<', '>'];

interface Props {
    steps: Step[];
    setSteps: (steps: Step[]) => void;
}

export default function StepBuilder({ steps, setSteps }: Props) {
    const [expandedIndex, setExpandedIndex] = useState<number | null>(null);

    const toggleExpanded = (index: number) => {
        setExpandedIndex(expandedIndex === index ? null : index);
    };

    const updateStep = (index: number, key: keyof Step, value: any) => {
        const updated = [...steps];
        updated[index] = { ...updated[index], [key]: value };
        setSteps(updated);
    };

    const updateStepParam = (index: number, key: string, value: any) => {
        const updated = [...steps];
        updated[index] = {
            ...updated[index],
            params: {
                ...updated[index].params,
                [key]: value,
            },
        };
        setSteps(updated);
    };

    const removeStep = (index: number) => {
        const updated = [...steps];
        updated.splice(index, 1);
        setSteps(updated);
        if (expandedIndex === index) setExpandedIndex(null);
    };

    // Create a short summary text for each step
    const stepSummary = (step: Step) => {
        switch (step.type) {
            case 'fetchWeather':
                return `Fetch weather for "${step.params.location ?? ''}"`;
            case 'condition': {
                const cond = step.params.conditions?.[0];
                if (!cond) return 'Condition (incomplete)';
                return `If ${cond.left} ${cond.operator} ${cond.right}`;
            }
            case 'sendEmail':
                return `Send email to "${step.params.to ?? ''}"`;
            default:
                return 'Unknown step';
        }
    };

    // Add new step defaults
    const addStep = () => {
        setSteps([
            ...steps,
            {
                id: crypto.randomUUID(),
                type: 'fetchWeather',
                exposedVariables: [{ name: 'Temperature in celsius', label: 'tempInC' }],
                params: { location: '' },
            },
        ]);
    };

    return (
        <div className="space-y-6 w-full max-w-full">
            {steps.map((step, i) => {
                const isExpanded = expandedIndex === i;

                return (
                    <div
                        key={step.id}
                        className="border rounded-md bg-background p-4"
                    >
                        {/* Summary + toggle button for mobile */}
                        <div
                            className="flex items-center justify-between cursor-pointer md:cursor-default"
                            onClick={() => toggleExpanded(i)}
                            role="button"
                            tabIndex={0}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter' || e.key === ' ') {
                                    e.preventDefault();
                                    toggleExpanded(i);
                                }
                            }}
                        >
                            <div className="font-medium">{stepSummary(step)}</div>

                            <div className="flex items-center">
                                {isExpanded ? (
                                    <ChevronUp className="w-5 h-5" />
                                ) : (
                                    <ChevronDown className="w-5 h-5" />
                                )}
                            </div>
                        </div>

                        <div
                            className={`mt-4 space-y-4 ${isExpanded ? 'block' : 'hidden'
                                }`}
                        >
                            {/* Step Type Selector */}
                            <div className="grid gap-2">
                                <Label>Step Type</Label>
                                <Select
                                    value={step.type}
                                    onValueChange={(val) => updateStep(i, 'type', val as StepType)}
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="fetchWeather">Fetch Weather</SelectItem>
                                        <SelectItem value="condition">Condition</SelectItem>
                                        <SelectItem value="sendEmail">Send Email</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* Step-specific inputs */}
                            {step.type === 'fetchWeather' && (
                                <div className="grid gap-2">
                                    <Label>Location</Label>
                                    <Input
                                        value={step.params.location ?? ''}
                                        onChange={(e) => updateStepParam(i, 'location', e.target.value)}
                                        placeholder="City or coordinates"
                                    />
                                </div>
                            )}

                            {step.type === 'condition' && (
                                <div className="flex flex-wrap gap-2 items-center">
                                    <Select
                                        value={step.params.conditions?.[0]?.left ?? ''}
                                        onValueChange={(val) =>
                                            updateStepParam(i, 'conditions', [{ ...step.params.conditions?.[0], left: val }])
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Variable" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {(step.exposedVariables ?? []).map((v) => (
                                                <SelectItem key={v.name} value={v.name}>
                                                    {v.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>

                                    <Select
                                        value={step.params.conditions?.[0]?.operator ?? '=='}
                                        onValueChange={(val) =>
                                            updateStepParam(i, 'conditions', [{ ...step.params.conditions?.[0], operator: val }])
                                        }
                                    >
                                        <SelectTrigger>
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {OPERATORS.map((op) => (
                                                <SelectItem key={op} value={op}>
                                                    {op}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>

                                    <Input
                                        value={step.params.conditions?.[0]?.right ?? ''}
                                        onChange={(e) =>
                                            updateStepParam(i, 'conditions', [{ ...step.params.conditions?.[0], right: e.target.value }])
                                        }
                                        placeholder="Value"
                                        className="min-w-[100px]"
                                    />
                                </div>
                            )}

                            {step.type === 'sendEmail' && (
                                <div className="space-y-4">
                                    <div className="grid gap-2">
                                        <Label>To</Label>
                                        <Input
                                            value={step.params.to ?? ''}
                                            onChange={(e) => updateStepParam(i, 'to', e.target.value)}
                                            placeholder="Email address"
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label>Subject</Label>
                                        <Input
                                            value={step.params.subject ?? ''}
                                            onChange={(e) => updateStepParam(i, 'subject', e.target.value)}
                                            placeholder="Email subject"
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label>Message</Label>
                                        <Textarea
                                            value={step.params.message ?? ''}
                                            onChange={(e) => updateStepParam(i, 'message', e.target.value)}
                                            placeholder="Email body"
                                            rows={4}
                                        />
                                    </div>
                                </div>
                            )}

                            {/* Remove step button */}
                            <div className="flex justify-end">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    className="text-destructive"
                                    onClick={() => removeStep(i)}
                                    aria-label="Remove step"
                                >
                                    <Trash2 className="w-5 h-5" />
                                </Button>
                            </div>
                        </div>
                    </div>
                );
            })}

            <Button
                type="button"
                variant="outline"
                onClick={addStep}
                className="flex items-center w-full justify-center"
            >
                <Plus className="mr-2 h-5 w-5" />
                Add Step
            </Button>
        </div>
    );
}
