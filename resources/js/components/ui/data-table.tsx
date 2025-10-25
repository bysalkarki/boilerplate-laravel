import { Paginated } from '@/types';
import { Link, useForm } from '@inertiajs/react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useCallback } from 'react';
import clsx from 'clsx';

interface Column<T> {
    header: string;
    accessor: (item: T) => React.ReactNode;
}

interface DataTableProps<T> {
    columns: Column<T>[];
    data: T[];
    paginationLinks: Paginated<T>['links'];
    search: string;
    onSearchChange: (value: string) => void;
    createUrl: string;
    createLabel: string;
    renderActions: (item: T) => React.ReactNode;
    resourceName: string;
    title: string;
}

export function DataTable<T extends { id: number }>({
                                                        columns,
                                                        data,
                                                        paginationLinks,
                                                        search,
                                                        onSearchChange,
                                                        createUrl,
                                                        createLabel,
                                                        renderActions,
                                                        resourceName,
                                                        title,
                                                    }: DataTableProps<T>) {
    const { delete: destroy } = useForm();

    const appendSearchParam = useCallback(
        (url: string | null) => {
            if (!url) return '#';
            const urlObj = new URL(url);
            if (search) {
                urlObj.searchParams.set('search', search);
            } else {
                urlObj.searchParams.delete('search');
            }
            return urlObj.toString();
        },
        [search]
    );

    return (
        <div className="space-y-4">
            {/* Header */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 p-2">
                <h1 className="text-2xl font-semibold">{title}</h1>
                <div className="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <Input
                        placeholder={`Search ${resourceName.toLowerCase()}s...`}
                        value={search}
                        onChange={(e) => onSearchChange(e.target.value)}
                        className="max-w-sm"
                    />
                    <Button asChild>
                        <Link href={createUrl}>{createLabel}</Link>
                    </Button>
                </div>
            </div>

            {/* Table */}
            <div className="overflow-x-auto rounded-md border">
                <Table className="min-w-full">
                    <TableHeader>
                        <TableRow>
                            {columns.map((column, index) => (
                                <TableHead key={index} className="px-4 py-2 text-left">
                                    {column.header}
                                </TableHead>
                            ))}
                            <TableHead className="w-0 px-4 py-2">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {data.length > 0 ? (
                            data.map((item) => (
                                <TableRow key={item.id} className="hover:bg-gray-50">
                                    {columns.map((column, index) => (
                                        <TableCell key={index} className="px-4 py-2">
                                            {column.accessor(item)}
                                        </TableCell>
                                    ))}
                                    <TableCell className="px-4 py-2">{renderActions(item)}</TableCell>
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell colSpan={columns.length + 1} className="text-center py-6">
                                    No {resourceName.toLowerCase()}s found.
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>

            {/* Pagination */}
            {paginationLinks.length > 1 && (
                <div className="flex justify-end p-2">
                    <div className="flex flex-wrap gap-2">
                        {paginationLinks.map((link, index) => (
                            <Link
                                key={index}
                                href={appendSearchParam(link.url)}
                                className={clsx(
                                    'px-3 py-1 rounded-md border text-sm hover:bg-primary hover:text-primary-foreground transition-colors',
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : 'bg-white text-gray-700'
                                )}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
