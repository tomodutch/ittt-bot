import { Button } from '@/components/ui/button';
import { ChevronDown, ChevronUp, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import {
    ConditionForm,
    FetchWeatherForm,
    SendEmailForm
} from './step-components';
import { Step } from '@/types';

type Props = {
    steps: Step[];
    setSteps: (steps: Step[]) => void;
};

export default function StepBuilder({ steps, setSteps }: Props) {
    const [expandedIndex, setExpandedIndex] = useState<number | null>(null);

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
            case 'fetchWeather':
                return <FetchWeatherForm step={step} onChange={onChange} />;
            case 'condition':
                return <ConditionForm step={step} onChange={onChange} />;
            case 'sendEmail':
                return <SendEmailForm step={step} onChange={onChange} />;
            default:
                return <div>Unsupported step type</div>;
        }
    };

    const addStep = () => {
        const newStep: Step = {
            id: crypto.randomUUID(),
            type: 'fetchWeather',
            params: { location: '' },
            exposedVariables: [{ name: 'temp', label: 'Temperature (C)' }]
        };
        setSteps([...steps, newStep]);
    };

    return (
        <div className="space-y-6">
            {steps.map((step, i) => {
                const isExpanded = expandedIndex === i;
                return (
                    <div key={step.id} className="border rounded-md bg-background p-4">
                        <div className="flex items-center justify-between">
                            <div
                                className="font-medium cursor-pointer flex-1"
                                onClick={() => toggleExpanded(i)}
                            >
                                {step.type} step
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
