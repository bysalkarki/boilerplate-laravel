import AppLayout from '@/layouts/app-layout';
import { Paginated, User } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { MoreHorizontal } from 'lucide-react';
import * as users from '@/routes/users';
import { useState, useEffect, useCallback } from 'react';
import { DataTable } from '@/components/ui/data-table';
import { usePermissions } from '@/hooks/usePermissions';

export default function Index({ users: userData, search: initialSearch }: { users: Paginated<User>, search?: string }) {
    const { delete: destroy } = useForm();
    const [search, setSearch] = useState(initialSearch || '');
    const { hasPermission } = usePermissions();

    const handleSearch = useCallback(
        (value: string) => {
            router.get(
                users.index().url,
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

    const userColumns = [
        {
            header: 'Name',
            accessor: (user: User) => user.name,
        },
        {
            header: 'Email',
            accessor: (user: User) => user.email,
        },
    ];

    const renderUserActions = (user: User) => (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon">
                    <MoreHorizontal className="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                {hasPermission('update-user') && (
                    <DropdownMenuItem asChild>
                        <Link href={users.edit(user).url}>Edit</Link>
                    </DropdownMenuItem>
                )}
                {hasPermission('delete-user') && (
                    <DropdownMenuItem
                        onClick={() => {
                            if (
                                confirm(
                                    'Are you sure you want to delete this user?'
                                )
                            ) {
                                destroy(users.destroy(user).url);
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
                    title: 'Users',
                    href: users.index().url,
                },
            ]}
        >
            <Head title="Users" />

            <DataTable
                columns={userColumns}
                data={userData.data}
                paginationLinks={userData.links}
                search={search}
                onSearchChange={setSearch}
                createUrl={users.create().url}
                createLabel="Create User"
                renderActions={renderUserActions}
                resourceName="User"
                title="Users"
            />
        </AppLayout>
    );
}
