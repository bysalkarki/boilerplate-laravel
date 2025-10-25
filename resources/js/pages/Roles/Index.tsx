import AppLayout from '@/layouts/app-layout';
import { Paginated, Role } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { MoreHorizontal } from 'lucide-react';
import * as roles from '@/routes/roles';
import { useState, useEffect, useCallback } from 'react';
import { DataTable } from '@/components/ui/data-table';
import { usePermissions } from '@/hooks/usePermissions';

export default function Index({ roles: roleData, search: initialSearch }: { roles: Paginated<Role>, search?: string }) {
    const { delete: destroy } = useForm();
    const [search, setSearch] = useState(initialSearch || '');
    const { hasPermission } = usePermissions();

    const handleSearch = useCallback(
        (value: string) => {
            router.get(
                roles.index().url,
                { search: value },
                {
                    preserveState: true,
                    replace: true,
                }
            );
        },
        []
    );

    useEffect(() => {
        const handler = setTimeout(() => {
            handleSearch(search);
        }, 300);

        return () => {
            clearTimeout(handler);
        };
    }, [search, handleSearch]);

    const roleColumns = [
        {
            header: 'Name',
            accessor: (role: Role) => role.name,
        },
    ];

    const renderRoleActions = (role: Role) => (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon">
                        <MoreHorizontal className="h-4 w-4" />
                    </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                {hasPermission('update-role') && (
                    <DropdownMenuItem asChild>
                        <Link href={roles.edit(role).url}>Edit</Link>
                    </DropdownMenuItem>
                )}
                {hasPermission('update-role') && (
                    <DropdownMenuItem asChild>
                        <Link href={roles.assignPermissions(role).url}>Assign Permissions</Link>
                    </DropdownMenuItem>
                )}
                {hasPermission('delete-role') && (
                    <DropdownMenuItem
                        onClick={() => {
                            if (
                                confirm(
                                    'Are you sure you want to delete this role?'
                                )
                            ) {
                                destroy(roles.destroy(role).url);
                            }
                        }}
                    >
                        Delete
                    </DropdownMenuItem>
                )}
            </DropdownMenuContent>
        </DropdownMenu>
    );

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Roles',
                    href: roles.index().url,
                },
            ]}
        >
            <Head title="Roles" />

            <DataTable
                columns={roleColumns}
                data={roleData.data}
                paginationLinks={roleData.links}
                search={search}
                onSearchChange={setSearch}
                createUrl={roles.create().url}
                createLabel="Create Role"
                renderActions={renderRoleActions}
                resourceName="Role"
                title="Roles"
            />
        </AppLayout>
    );
}
