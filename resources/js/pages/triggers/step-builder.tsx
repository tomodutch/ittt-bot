import { Button } from '@/components/ui/button';
import { ChevronDown, ChevronUp, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {
    ConditionForm,
    FetchWeatherForm,
    SendEmailForm
} from './step-components';
import { FetchWeatherStep, SendEmailStep, SimpleConditionalStep, Step } from '@/types';

const STEP_TYPES = [
    { value: 'http.weather.location', label: 'Fetch Weather' },
    { value: 'logic.conditional.simple', label: 'Simple Condition' },
    { value: 'notify.email.send', label: 'Send Email' },
];

export default function StepBuilder({ steps, setSteps }: { steps: Step[]; setSteps: (steps: Step[]) => void }) {
    const [expandedIndex, setExpandedIndex] = useState<number | null>(null);
    const [newStepType, setNewStepType] = useState<string>(STEP_TYPES[0].value);

    const toggleExpanded = (index: number) => {
        setExpandedIndex(expandedIndex === index ? null : index);
    };

    const renderStepForm = (step: Step, index: number) => {
        const onChange = (newParams: any) => {
            const updated = [...steps];
            updated[index] = { ...step, params: { ...step.params, ...newParams } };
            setSteps(updated);
        };

        switch (step.type) {
            case 'http.weather.location':
                return <FetchWeatherForm step={step} onChange={onChange} />;
            case 'logic.conditional.simple':
                return <ConditionForm step={step} onChange={onChange} />;
            case 'notify.email.send':
                return <SendEmailForm step={step} onChange={onChange} />;
            default:
                return <div>Unsupported step type</div>;
        }
    };

    function addWeatherStep(): FetchWeatherStep {
        return {
            id: null,
            triggerId: null,
            order: steps.length + 1,
            description: 'Fetch weather',
            type: "http.weather.location",
            params: { location: '' },
            createdAt: null,
            updatedAt: null,
        };
    }

    function addSimpleConditionStep(): SimpleConditionalStep {
        return {
            id: null,
            triggerId: null,
            order: steps.length + 1,
            description: 'Simple condition',
            type: "logic.conditional.simple",
            params: { left: '', operator: '==', right: '' },
            createdAt: null,
            updatedAt: null,
        };
    }

    function addEmailStep(): SendEmailStep {
        return {
            id: null,
            triggerId: null,
            order: steps.length + 1,
            description: 'Send email',
            type: "notify.email.send",
            params: { to: '', subject: '', body: '', cc: null, bcc: null },
            createdAt: null,
            updatedAt: null,
        };
    }

    function getNewStep() {
        switch (newStepType) {
            case 'http.weather.location':
                return addWeatherStep();
            case 'logic.conditional.simple':
                return addSimpleConditionStep();
            case 'notify.email.send':
                return addEmailStep();
            default:
                console.warn(`Unsupported step type: ${newStepType}`);
                break;
        }
    }

    const addStep = () => {
        const newStep = getNewStep();
        if (newStep) {
            setSteps([...steps, newStep]);
        }
    };

    return (
        <div className="space-y-6">
            {steps.map((step, i) => {
                const isExpanded = expandedIndex === i;
                return (
                    <div key={i} className="border rounded-md bg-background p-4">
                        <div className="flex items-center justify-between">
                            <div
                                className="font-medium cursor-pointer flex-1"
                                onClick={() => toggleExpanded(i)}
                            >
                                {step.type}
                            </div>

                            <Button
                                size="icon"
                                variant="ghost"
                                className="text-destructive mr-2"
                                onClick={() => {
                                    const updated = [...steps];
                                    updated.splice(i, 1);
                                    setSteps(updated);
                                }}
                            >
                                <Trash2 className="w-5 h-5" />
                            </Button>

                            {isExpanded ? (
                                <ChevronUp className="w-5 h-5 cursor-pointer" onClick={() => toggleExpanded(i)} />
                            ) : (
                                <ChevronDown className="w-5 h-5 cursor-pointer" onClick={() => toggleExpanded(i)} />
                            )}
                        </div>

                        {isExpanded && (
                            <div className="mt-4 space-y-4">
                                {renderStepForm(step, i)}
                            </div>
                        )}
                    </div>
                );
            })}

            <div className="flex gap-4">
                <select
                    className="border px-3 py-2 rounded-md"
                    value={newStepType}
                    onChange={(e) => setNewStepType(e.target.value)}
                >
                    {STEP_TYPES.map((type) => (
                        <option key={type.value} value={type.value}>
                            {type.label}
                        </option>
                    ))}
                </select>

                <Button type="button" variant="outline" onClick={addStep} className="flex items-center gap-2">
                    <Plus className="h-5 w-5" />
                    Add Step
                </Button>
            </div>
        </div>
    );
}
