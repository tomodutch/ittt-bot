import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card } from '@/components/ui/card';
import { type SharedData, type TriggerExecution } from '@/types';

const logLevelColors: Record<string, string> = {
    Emergency: 'text-red-900 bg-red-200',
    Alert: 'text-red-800 bg-red-300',
    Critical: 'text-red-700 bg-red-400',
    Error: 'text-red-600 bg-red-500',
    Warning: 'text-yellow-700 bg-yellow-200',
    Notice: 'text-yellow-800 bg-yellow-300',
    Info: 'text-blue-700 bg-blue-200',
    Debug: 'text-gray-600 bg-gray-200',
};

interface PageProps {
    execution: TriggerExecution;
}

export default function ExecutionShow() {
    const { execution } = usePage<SharedData & PageProps>().props;

    return (
        <AppLayout>
            <Head title={`Execution ${execution.id}`} />

            <div className="p-6 space-y-6">
                <h1 className="text-3xl font-semibold">Execution Details</h1>

                <Card className="p-4 space-y-2">
                    <div><strong>Status:</strong> {execution.statusCode}</div>
                    <div><strong>Run Reason:</strong> {execution.runReasonCode}</div>
                    <div><strong>Started at:</strong> {execution.createdAt}</div>
                    {execution.finishedAt && <div><strong>Finished at:</strong> {execution.finishedAt}</div>}
                </Card>

                <Card className="p-4 space-y-2 bg-black text-white font-mono overflow-auto max-h-96">
                    <h2 className="text-xl font-semibold border-b border-gray-700 pb-2 mb-4">Logs</h2>
                    {execution.logs.length === 0 ? (
                        <p>No logs available.</p>
                    ) : (
                        <ul>
                            {execution.logs.map(log => (
                                <li key={log.id} className="mb-2">
                                    <details className="text-sm text-gray-300 cursor-pointer bg-black rounded px-2 py-1 group">
                                        <summary className="flex items-center gap-3 list-none select-none">
                                            <span
                                                className="inline-block transition-transform duration-200 group-open:rotate-90"
                                                style={{ width: '1em' }}
                                            >
                                                ▶
                                            </span>

                                            <span
                                                className={`inline-block px-2 py-0.5 rounded font-bold ${logLevelColors[log.level] ?? 'bg-gray-700 text-white'}`}
                                            >
                                                {log.level.toUpperCase()}
                                            </span>
                                            <span className="flex-1">{log.message}</span>
                                        </summary>
                                        {log.details && (
                                            <pre className="whitespace-pre-wrap mt-2 ml-12 text-gray-400 font-mono">{JSON.stringify(log.details, null, 2)}</pre>
                                        )}
                                    </details>
                                </li>
                            ))}
                        </ul>

                    )}
                </Card>
            </div>
        </AppLayout>
    );
}