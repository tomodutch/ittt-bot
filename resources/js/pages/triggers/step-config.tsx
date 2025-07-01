import { StepType } from "@/types";

export interface StepConfig {
  label: string;
}

const stepConfig: Record<StepType, StepConfig> = {
  'logic.entry': { label: 'Entry Point' },
  'http.weather.location': { label: 'Fetch Weather' },
  'notify.email.send': { label: 'Send Email' },
  'logic.conditional.simple': { label: 'Simple Condition' },
};


export default stepConfig;