export type ExecutionStatus = 'Idle' | 'Running' | 'Finished';
export type ExecutionType = 'Schedule' | 'Webhook';
export type Operator = '==' | '!=' | '>' | '>=' | '<' | '<=' | 'exists' | 'not_exists' | 'empty' | 'not_empty' | 'null' | 'not_null' | 'contains' | 'starts_with' | 'ends_with' | 'matches' | 'in' | 'not_in';
export type RunReason = 'Scheduled' | 'Manual' | 'Webhook';
export type ScheduleData = {
id: string | null;
triggerId: string | null;
typeCode: ScheduleType;
oneTimeAt: string | null;
time: string | null;
daysOfTheWeek: Array<any> | null;
timezone: string | null;
createdAt: string | null;
updatedAt: string | null;
};
export type ScheduleType = 'Once' | 'Daily' | 'Weekly';
export type SendEmailStepData = {
type: "notify.email.send";
params: SendEmailStepParams;
id: string | null;
triggerId: string | null;
order: number;
description: string;
createdAt: string | null;
updatedAt: string | null;
};
export type SendEmailStepParams = {
to: string;
cc: string | null;
bcc: string | null;
subject: string;
body: string;
};
export type SimpleConditionalStepData = {
type: "logic.conditional.simple";
params: SimpleConditionalStepParams;
id: string | null;
triggerId: string | null;
order: number;
description: string;
createdAt: string | null;
updatedAt: string | null;
};
export type SimpleConditionalStepParams = {
left: string;
operator: Operator;
right: any;
};
export type StepData = SendEmailStepData | SimpleConditionalStepData | WeatherStepData;
export type StepDataParams = {
};
export type StepType = 'http.weather.location' | 'notify.email.send' | 'logic.conditional.simple';
export type TriggerData = {
id: string | null;
name: string;
description: string;
executionType: ExecutionType;
schedules: Array<ScheduleData>;
steps: Array<StepData>;
createdAt: string | null;
updatedAt: string | null;
};
export type WeatherStepData = {
type: "http.weather.location";
params: WeatherStepParams;
id: string | null;
triggerId: string | null;
order: number;
description: string;
createdAt: string | null;
updatedAt: string | null;
};
export type WeatherStepParams = {
location: string;
};
