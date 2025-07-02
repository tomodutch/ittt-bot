import { useEffect, useState } from "react";
import {
    ReactFlow,
    MiniMap,
    Controls,
    Background,
    Edge,
    Node,
    NodeTypes,
    Handle,
    Position,
} from '@xyflow/react';

import '@xyflow/react/dist/style.css';

import { StepData } from "@/types/generated";
import { getLayoutedElements } from "./graph";
import stepConfig from "./step-config";

interface Props {
    steps: StepData[];
}

export default function ShowStepsGraph({ steps }: Props) {
    const [nodes, setNodes] = useState<Node[]>([]);
    const [edges, setEdges] = useState<Edge[]>([]);

    const nodeTypes: NodeTypes = {
        stepNode: StepNode,
    };

    useEffect(() => {
        const { nodes: layoutedNodes, edges: layoutedEdges } = getLayoutedElements(steps, {
            nodeWidth: 200,
            nodeHeight: 80,
        });
        setNodes(layoutedNodes);
        setEdges(layoutedEdges);
    }, [steps]);

    return (
        <div style={{ width: "100%", height: 500 }}>
            <ReactFlow
                nodes={nodes}
                edges={edges}
                fitView
                nodeTypes={nodeTypes}
                nodesDraggable={false}
                nodesConnectable={false}
                elementsSelectable={false}
                zoomOnScroll
                panOnScroll
            >
                <Controls showZoom={true} showFitView={true} />
                <Background />
            </ReactFlow>
        </div>
    );
}

interface StepNodeParams {
    data: {
        step: StepData,
    }
}
function StepNode({ data }: StepNodeParams) {
    const {step} = data;
    return (
        <div className="bg-white border border-gray-300 rounded p-2 shadow text-sm">
            {describeStep(step)}

            <Handle type="target" position={Position.Top} isConnectable={false} />
            <Handle type="source" position={Position.Bottom} isConnectable={false} />
        </div>
    );
}


function describeStep(step: StepData) {
  switch (step.type) {
    case "http.weather.location":
      return <p>Fetch weather for {step.params.location}</p>;
    case 'logic.conditional.simple':
      const c = step.params;
      if (!c) return 'Condition (missing)';
      return <p>If {c.left} {c.operator} {c.right}</p>;
    case 'notify.email.send':
      return (<>
        <p>Send email</p>
        <p>to: {step.params.to}</p>
      </>);
    default:
      return stepConfig[step.type].label;
  }
}
