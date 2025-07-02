import {
    Handle,
    Position,
} from '@xyflow/react';

import '@xyflow/react/dist/style.css';
import { StepData } from '@/types/generated';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { MoreVertical, Trash2 } from 'lucide-react';
import stepConfig from "./step-config";

interface StepNodeParams {
    data: {
        step: StepData,
        onChange: (step: StepData, params: any) => void,
        onClick: (step: StepData) => void
    }
}

export default function StepNode({ data }: StepNodeParams) {
    let node;
    switch (data.step.type) {
        case "http.weather.location":
            node = <>Fetch weather for {data.step.params.location}</>
            break;
        case "notify.email.send":
            node = <>email</>
            break;
        case "logic.conditional.simple":
            node = <>logic</>
            break;
        case "logic.entry":
            node = <>entry</>
            break;
        default:
            node = <div>Unsupported step</div>;
    }

    return (
        <Card className="w-[240px] relative" onClick={() => {
            data.onClick(data.step);
        }}>
            <Handle type="target" position={Position.Top} className="!bg-blue-500" />
            <Handle type="source" position={Position.Bottom} className="!bg-green-500" id="default" />
            {
                data.step.type === "logic.conditional.simple" && (
                    <Handle type="source" position={Position.Right} className="!bg-green-500" id="false" />
                )
            }

            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium truncate">
                    {data.step.description || stepConfig[data.step.type]?.label || data.step.type}
                </CardTitle>
                <div className="flex gap-1">
                    <button className="text-destructive hover:opacity-80" onClick={() => console.log('remove')}>
                        <Trash2 className="w-4 h-4" />
                    </button>
                    <button className="text-muted-foreground hover:opacity-80">
                        <MoreVertical className="w-4 h-4" />
                    </button>
                </div>
            </CardHeader>

            <CardContent className="pt-2">
                {node}
            </CardContent>
        </Card>
    );
}