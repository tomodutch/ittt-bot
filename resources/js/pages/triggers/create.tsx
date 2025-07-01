import AppHeaderLayout from '@/layouts/app/app-header-layout';
import { Head } from '@inertiajs/react';
import TriggerBuilder from './step-builder';
import { StepType, type BreadcrumbItem } from '@/types';
import { useTriggerBuilder } from "./builder-hook";
import { Accordion, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { AccordionContent } from '@radix-ui/react-accordion';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import stepConfig from "./step-config";
import { StepData } from '@/types/generated';
import { ConditionForm, FetchWeatherForm, SendEmailForm } from './step-components';
import { useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Triggers', href: '/triggers' },
];

export default function CreateTriggerPage() {
    const {
        steps,
        addStep,
        connectSteps,
        selectedStep,
        setSelectedStep,
        layoutVersion
    } = useTriggerBuilder();
    const [addStepType, setAddStepType] = useState<StepType>("http.weather.location");

    return (
        <AppHeaderLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Trigger" />
            <div className="flex h-[calc(100vh-100px)]">
                <div className="flex-grow h-full min-w-0">
                    <div className="w-full h-full">
                        <TriggerBuilder
                            steps={steps}
                            onConnect={connectSteps}
                            setSelectedStep={setSelectedStep}
                            layoutVersion={layoutVersion}
                        />
                    </div>
                </div>

                <div className="w-[20vw] border-l border-gray-200 p-4 overflow-auto h-full">
                    <Accordion type="single">
                        <AccordionItem value="edit">
                            <AccordionTrigger>Edit Step</AccordionTrigger>
                            <AccordionContent>
                                {renderStep(selectedStep)}
                            </AccordionContent>
                        </AccordionItem>

                        <AccordionItem value="add">
                            <AccordionTrigger>Add New Step</AccordionTrigger>
                            <AccordionContent>
                                <Select value={addStepType} onValueChange={(selected: StepType) => {
                                    setAddStepType(selected);
                                }}>
                                    <SelectTrigger className="w-[180px]">
                                        <SelectValue placeholder="Theme" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(stepConfig).map(([key, config]) => (
                                            <SelectItem key={key} value={key}>
                                                {config.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                <Button onClick={() => {
                                    addStep(addStepType);
                                }}>Add</Button>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </div>
            </div>
        </AppHeaderLayout>
    );
}

function renderStep(step: StepData | null) {
    if (!step) return <p>Select a step to edit</p>
    switch (step.type) {
        case "http.weather.location":
            return <FetchWeatherForm step={step} onChange={() => { }} />
        case "logic.conditional.simple":
            return <ConditionForm step={step} onChange={() => { }} />
        case "notify.email.send":
            return <SendEmailForm step={step} onChange={() => { }} />
        case "logic.entry":
            return <div>entry</div>
    }
}