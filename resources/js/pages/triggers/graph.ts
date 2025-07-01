import { Step } from "@/types";
import { Edge, Node, Position } from "@xyflow/react";
import dagre from "dagre";

const nodeWidth = 250;
const nodeHeight = 150;

export function getLayoutedElements(steps: Step[]) {
    const dagreGraph = new dagre.graphlib.Graph();
    dagreGraph.setDefaultEdgeLabel(() => ({}));
    dagreGraph.setGraph({ rankdir: "TB" }); // Top-to-bottom layout

    // Register nodes
    steps.forEach((step) => {
        dagreGraph.setNode(step.key, { width: nodeWidth, height: nodeHeight });
    });

    const edges: Edge[] = [];

    const pushEdge = (source: string, target: string, label?: string) => {
        if (!target) return; // avoid pushing edges to undefined targets

        dagreGraph.setEdge(source, target);
        edges.push({
            id: `e-${source}-${target}`,
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
        const { params, key, type } = step;

        if (params && typeof params === "object") {
            if ("nextStep" in params && typeof params.nextStep === "string") {
                pushEdge(key, params.nextStep);
            }

            if (type === "logic.conditional.simple") {
                if ("nextStepIfTrue" in params && typeof params.nextStepIfTrue === "string") {
                    pushEdge(key, params.nextStepIfTrue, "true");
                }
                if ("nextStepIfFalse" in params && typeof params.nextStepIfFalse === "string") {
                    pushEdge(key, params.nextStepIfFalse, "false");
                }
            }
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
                x: dagreNode.x - nodeWidth / 2,
                y: dagreNode.y - nodeHeight / 2,
            },
            data: { step },
        };
    });

    return { nodes, edges };
}
