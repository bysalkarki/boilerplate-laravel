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
import { useCallback, useEffect } from 'react';

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

export function DataTable<T extends { id: number }>({ // Added extends { id: number } to ensure T has an id
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

    const appendSearchParam = useCallback((url: string | null) => {
        if (!url) return '#';
        const urlObj = new URL(url);
        if (search) {
            urlObj.searchParams.set('search', search);
        } else {
            urlObj.searchParams.delete('search');
        }
        return urlObj.toString();
    }, [search]);

    return (
        <>
            <div className="flex items-center justify-between p-2">
                <h1 className="text-2xl font-semibold">{title}</h1>
                <Button asChild>
                    <Link href={createUrl}>{createLabel}</Link>
                </Button>
            </div>

            <div className="flex items-center justify-between p-2">
                <Input
                    placeholder={`Search ${resourceName.toLowerCase()}s...`}
                    className="max-w-sm"
                    value={search}
                    onChange={(e) => onSearchChange(e.target.value)}
                />
            </div>

            <div className="m-4 rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            {columns.map((column, index) => (
                                <TableHead key={index} className="px-4 py-2">
                                    {column.header}
                                </TableHead>
                            ))}
                            <TableHead className="w-0 px-4 py-2">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {data.map((item) => (
                            <TableRow key={item.id}>
                                {columns.map((column, index) => (
                                    <TableCell key={index} className="px-4 py-2">
                                        {column.accessor(item)}
                                    </TableCell>
                                ))}
                                <TableCell className="px-4 py-2">
                                    {renderActions(item)}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>

            <div className="m-2 flex items-center justify-end">
                <div className="flex items-center space-x-2">
                    {paginationLinks.map((link, index) => (
                        <Link
                            key={index}
                            href={appendSearchParam(link.url)}
                            className={`px-3 py-1 text-sm rounded-md ${
                                link.active
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-secondary text-secondary-foreground'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </div>
            </div>
        </>
    );
}
