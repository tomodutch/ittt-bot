import { useCallback, useState } from "react";
import { StepType, Trigger, Schedule } from "@/types";
import { router } from "@inertiajs/react";
import { StepData } from "@/types/generated";

export function useTriggerBuilder() {
    const [steps, setSteps] = useState<StepData[]>([
        {
            id: null,
            type: "logic.entry",
            key: "entry-1",
            order: 1,
            triggerId: "",
            description: "",
            params: { nextStep: "" },
            createdAt: null,
            updatedAt: null,
        },
    ]);

    const [schedule, setSchedule] = useState<Schedule>({
        id: null,
        triggerId: "",
        typeCode: "Daily",
        time: "10:00",
        daysOfTheWeek: [],
        oneTimeAt: "",
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        createdAt: null,
        updatedAt: null,
    });

    const [selectedStep, setSelectedStep] = useState<StepData | null>(steps[0]);
    const [layoutVersion, setLayoutVersion] = useState(0);

    const addStep = useCallback((type: StepType) => {
        setSteps((prev) => {
            const key = `step-${prev.length + 1}`;

            const base: Partial<StepData> = {
                id: null,
                key,
                order: prev.length,
                triggerId: "",
                description: "",
                createdAt: null,
                updatedAt: null,
            };

            const step: StepData = (() => {
                switch (type) {
                    case "http.weather.location":
                        return { ...base, type, params: { location: "" } } as StepData;
                    case "notify.email.send":
                        return { ...base, type, params: { to: "", subject: "", body: "" } } as StepData;
                    case "logic.conditional.simple":
                        return {
                            ...base,
                            type,
                            params: {
                                left: "",
                                operator: "==",
                                right: "",
                                nextStepIfTrue: "",
                                nextStepIfFalse: "",
                            },
                        } as StepData;
                    default:
                        throw new Error(`Unsupported type: ${type}`);
                }
            })();

            return [...prev, step];
        });

        setLayoutVersion((v) => v + 1);
    }, []);

    const updateStepParams = useCallback((stepKey: string, updatedParams: any) => {
        setSteps((prev) =>
            prev.map((s) => (s.key === stepKey ? { ...s, params: updatedParams } : s))
        );
    }, []);

    const connectSteps = useCallback((sourceKey: string, targetKey: string) => {
        setSteps((prev) =>
            prev.map((s) => {
                if (s.key !== sourceKey) return s;

                switch (s.type) {
                    case "logic.conditional.simple":
                        return {
                            ...s,
                            params: {
                                ...s.params,
                                nextStepIfTrue: targetKey,
                            },
                        };
                    case "http.weather.location":
                        return {
                            ...s,
                            params: {
                                ...s.params,
                                nextStep: targetKey,
                            },
                        };
                    case "notify.email.send":
                        return {
                            ...s,
                            params: {
                                ...s.params,
                                nextStep: targetKey,
                            },
                        };
                    case "logic.entry":
                        return {
                            ...s,
                            params: {
                                ...s.params,
                                nextStep: targetKey,
                            },
                        };
                    default:
                        throw new Error("undefined type");
                }
            })
        );
        setLayoutVersion((v) => v + 1);
    }, []);

    const handleSave = useCallback(() => {
        const newTrigger: Trigger = {
            id: null,
            name: "New Trigger",
            description: "This is a new trigger",
            executionType: "Webhook",
            schedules: [schedule],
            executions: [],
            steps: steps,
            createdAt: null,
            updatedAt: null,
        };

        router.post("/triggers", newTrigger);
    }, [schedule, steps]);

    return {
        steps,
        setSteps,
        schedule,
        setSchedule,
        addStep,
        updateStepParams,
        connectSteps,
        handleSave,
        selectedStep,
        setSelectedStep,
        layoutVersion,
        setLayoutVersion
    };
}
