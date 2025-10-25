import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/input-error';
import * as roles from '@/routes/roles';
import { Permission, Role } from '@/types';
import { useState } from 'react';

interface AssignPermissionsProps {
    role: Role;
    allPermissions: Record<string, Permission[]>;
    assignedPermissions: number[];
}

export default function AssignPermissions({ role, allPermissions, assignedPermissions }: AssignPermissionsProps) {
    const { data, setData, put, processing, errors } = useForm({
        permission_ids: assignedPermissions,
    });

    const handleCheckboxChange = (permissionId: number, checked: boolean) => {
        setData('permission_ids', (prev) =>
            checked ? [...prev, permissionId] : prev.filter((id) => id !== permissionId)
        );
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(roles.storePermissions(role).url);
    };

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Roles',
                    href: roles.index().url,
                },
                {
                    title: role.name,
                    href: roles.edit(role).url,
                },
                {
                    title: 'Assign Permissions',
                    href: roles.assignPermissions(role).url,
                },
            ]}
        >
            <Head title={`Assign Permissions to ${role.name}`} />

            <div className="flex items-center justify-between p-2">
                <h1 className="text-2xl font-semibold">Assign Permissions to {role.name}</h1>
            </div>

            <div className="m-4 rounded-md border p-4">
                <form onSubmit={submit} className="space-y-4">
                    {Object.entries(allPermissions).map(([moduleName, permissions]) => (
                        <div key={moduleName} className="border p-3 rounded-md">
                            <h3 className="text-lg font-semibold capitalize mb-2">{moduleName}</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                {permissions.map((permission) => (
                                    <div key={permission.id} className="flex items-center space-x-2">
                                        <Checkbox
                                            id={`permission-${permission.id}`}
                                            checked={data.permission_ids.includes(permission.id)}
                                            onCheckedChange={(checked) =>
                                                handleCheckboxChange(permission.id, checked as boolean)
                                            }
                                        />
                                        <Label htmlFor={`permission-${permission.id}`}>
                                            {permission.name}
                                        </Label>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}

                    <InputError message={errors.permission_ids} className="mt-2" />

                    <div className="flex items-center justify-end mt-4">
                        <Button className="ms-4" disabled={processing}>
                            Save Permissions
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
