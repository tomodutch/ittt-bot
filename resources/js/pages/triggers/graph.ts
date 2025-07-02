import { Step } from "@/types";
import { Edge, Node, Position } from "@xyflow/react";
import dagre from "dagre";
interface Params {
    nodeWidth: number,
    nodeHeight: number,
}

const defaults: Params = {
    nodeWidth: 250,
    nodeHeight: 150
};

export function getLayoutedElements(steps: Step[], params?: Params) {
    params = params ?? defaults;
    const dagreGraph = new dagre.graphlib.Graph();
    dagreGraph.setDefaultEdgeLabel(() => ({}));
    dagreGraph.setGraph({ rankdir: "TB" }); // Top-to-bottom layout

    // Register nodes
    steps.forEach((step) => {
        dagreGraph.setNode(step.key, { width: params.nodeWidth, height: params.nodeHeight });
    });

    const edges: Edge[] = [];

    const pushEdge = (source: string, target: string, label?: string) => {
        if (!target) return; // avoid pushing edges to undefined targets

        dagreGraph.setEdge(source, target);
        edges.push({
            id: crypto.randomUUID(),
            source,
            target,
            type: "smoothstep",
            animated: false,
            sourcePosition: Position.Bottom,
            targetPosition: Position.Top,
            markerEnd: {
                type: "arrowclosed",
                width: 12,
                height: 12,
            },
            label,
        } as Edge);
    };

    // Register edges
    steps.forEach((step) => {
        const { key, type } = step;
        if (step.nextStepKey) {
            pushEdge(key, step.nextStepKey);
        }

        if (type === "logic.conditional.simple" && step.nextStepKeyIfFalse) {
            pushEdge(key, step.nextStepKeyIfFalse, "false");
        }
    });

    // Compute layout
    dagre.layout(dagreGraph);

    // Convert to React Flow nodes
    const nodes: Node[] = steps.map((step) => {
        const dagreNode = dagreGraph.node(step.key);

        return {
            id: step.key,
            type: "stepNode",
            position: {
                x: dagreNode.x - params.nodeWidth / 2,
                y: dagreNode.y - params.nodeHeight / 2,
            },
            data: { step },
        };
    });

    return { nodes, edges };
}
