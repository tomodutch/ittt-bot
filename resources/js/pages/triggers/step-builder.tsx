import {
    ReactFlow,
    MiniMap,
    Controls,
    Background,
    useNodesState,
    useEdgesState,
    addEdge,
    Edge,
    Node,
    Connection,
} from '@xyflow/react';

import '@xyflow/react/dist/style.css';

import { getLayoutedElements } from './graph';
import StepNode from './step-node';
import { useEffect } from 'react';
import { StepData } from '@/types/generated';

const nodeTypes = { stepNode: StepNode };

interface Props {
    steps: StepData[];
    onConnect: (source: string, target: string) => void;
    setSelectedStep: (step: StepData) => void,
    layoutVersion: any
}

export default function StepBuilder({
    steps,
    onConnect,
    setSelectedStep,
    layoutVersion
}: Props) {
    const [nodes, setNodes, onNodesChange] = useNodesState<Node>([]);
    const [edges, setEdges, onEdgesChange] = useEdgesState<Edge>([]);
    useEffect(() => {
        const { nodes: layoutedNodes, edges: layoutedEdges } = getLayoutedElements(steps);
        const nodesWithData = layoutedNodes.map((node) => ({
            ...node,
            data: {
                step: node.data.step,
                onClick: (step: StepData) => {
                    setSelectedStep(step);
                },
            },
        }));

        setNodes(nodesWithData);
        setEdges(layoutedEdges);
    }, [steps, layoutVersion, setSelectedStep]);

    const handleConnect = (params: Connection) => {
        if (params.source && params.target) {
            onConnect(params.source, params.target);
            setEdges((eds) => addEdge(params, eds));
        }
    };

    return (
        <ReactFlow
            nodes={nodes}
            edges={edges}
            onNodesChange={onNodesChange}
            onEdgesChange={onEdgesChange}
            onConnect={handleConnect}
            nodeTypes={nodeTypes}
            snapToGrid
            fitView
        >
            <Controls />
            <MiniMap />
            <Background gap={12} size={1} />
        </ReactFlow>
    );
}
