declare namespace App.Data {
export type ScheduleData = {
id: string | null;
triggerId: string | null;
typeCode: App.Enums.ScheduleType;
oneTimeAt: string | null;
time: string | null;
daysOfTheWeek: Array<any> | null;
timezone: string | null;
createdAt: string | null;
updatedAt: string | null;
};
export type StepData = {
id: string | null;
triggerId: string | null;
order: number;
description: string;
type: App.Domain.Workflow.Steps.StepType;
action: string | null;
params: App.Domain.Workflow.Steps.SendEmail.SendEmailStepParams | App.Domain.Workflow.Steps.Weather.WeatherStepParams | App.Domain.Workflow.Steps.SimpleConditional.SimpleConditionalStepParams;
createdAt: string | null;
updatedAt: string | null;
};
export type TriggerData = {
id: string | null;
name: string;
description: string;
executionType: App.Enums.ExecutionType;
schedules: Array<App.Data.ScheduleData>;
steps: Array<App.Data.StepData>;
createdAt: string | null;
updatedAt: string | null;
};
}
declare namespace App.Domain.Workflow.Steps {
export type StepType = 'http.weather.location' | 'notify.email.send' | 'logic.conditional.simple';
}
declare namespace App.Domain.Workflow.Steps.SendEmail {
export type SendEmailStepData = {
type: "notify.email.send";
params: App.Domain.Workflow.Steps.SendEmail.SendEmailStepParams;
};
export type SendEmailStepParams = {
to: string;
cc: string | null;
bcc: string | null;
subject: string;
body: string;
};
}
declare namespace App.Domain.Workflow.Steps.SimpleConditional {
export type SimpleConditionalStepData = {
type: "logic.conditional.simple";
params: App.Domain.Workflow.Steps.SimpleConditional.SimpleConditionalStepParams;
};
export type SimpleConditionalStepParams = {
left: string;
operator: App.Enums.Operator;
right: any;
};
}
declare namespace App.Domain.Workflow.Steps.Weather {
export type WeatherStepData = {
type: "http.weather.location";
params: App.Domain.Workflow.Steps.Weather.WeatherStepParams;
};
export type WeatherStepParams = {
location: string;
};
}
declare namespace App.Enums {
export type ExecutionStatus = 'Idle' | 'Running' | 'Finished';
export type ExecutionType = 'Schedule' | 'Webhook';
export type Operator = '==' | '!=' | '>' | '>=' | '<' | '<=' | 'exists' | 'not_exists' | 'empty' | 'not_empty' | 'null' | 'not_null' | 'contains' | 'starts_with' | 'ends_with' | 'matches' | 'in' | 'not_in';
export type RunReason = 'Scheduled' | 'Manual' | 'Webhook';
export type ScheduleType = 'Once' | 'Daily' | 'Weekly';
}
