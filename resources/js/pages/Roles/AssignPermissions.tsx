import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/input-error';
import * as roles from '@/routes/roles';
import { Permission, Role } from '@/types';
import { useCallback, FormEvent, memo } from 'react';

interface AssignPermissionsProps {
    role: Role;
    allPermissions: Record<string, Permission[]>;
    assignedPermissions: number[];
}

export default function AssignPermissions({
                                              role,
                                              allPermissions,
                                              assignedPermissions,
                                          }: AssignPermissionsProps) {
    const { data, setData, put, processing, errors } = useForm({
        permission_ids: assignedPermissions,
    });

    const handleCheckboxChange = (permissionId: number, checked: boolean) => {
        const current = Array.isArray(data.permission_ids) ? data.permission_ids : [];
        setData(
            'permission_ids',
            checked
                ? [...current, permissionId]
                : current.filter((id) => id !== permissionId)
        );
    };


    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        put(roles.storePermissions(role).url);
    };

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Roles', href: roles.index().url },
                { title: role.name, href: roles.edit(role).url },
                { title: 'Assign Permissions', href: roles.assignPermissions(role).url },
            ]}
        >
            <Head title={`Assign Permissions to ${role.name}`} />

            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold tracking-tight">
                    Assign Permissions to <span className="font-bold">{role.name}</span>
                </h1>
                <Button asChild variant="outline">
                    <Link href={roles.index().url}>Back</Link>
                </Button>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6">
                {Object.entries(allPermissions).map(([moduleName, permissions]) => (
                    <ModuleSection
                        key={moduleName}
                        moduleName={moduleName}
                        permissions={permissions}
                        selected={data.permission_ids}
                        onToggle={handleCheckboxChange}
                    />
                ))}

                {errors.permission_ids && (
                    <InputError message={errors.permission_ids} className="text-sm" />
                )}

                <div className="flex justify-end pt-4 border-t">
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Saving...' : 'Save Permissions'}
                    </Button>
                </div>
            </form>
        </AppLayout>
    );
}

/* ----------------------------- Subcomponent ----------------------------- */

interface ModuleSectionProps {
    moduleName: string;
    permissions: Permission[];
    selected: number[];
    onToggle: (id: number, checked: boolean) => void;
}

const ModuleSection = memo(function ModuleSection({
                                                      moduleName,
                                                      permissions,
                                                      selected,
                                                      onToggle,
                                                  }: ModuleSectionProps) {
    return (
        <section className="rounded-lg border p-4 shadow-sm bg-card/50">
            <h3 className="text-lg font-semibold mb-3 capitalize text-primary">
                {moduleName}
            </h3>
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                {permissions.map((permission) => {
                    const checked = selected.includes(permission.id);
                    return (
                        <label
                            key={permission.id}
                            htmlFor={`perm-${permission.id}`}
                            className="flex items-center space-x-2 hover:bg-muted/50 p-2 rounded-md cursor-pointer transition"
                        >
                            <Checkbox
                                id={`perm-${permission.id}`}
                                checked={checked}
                                onCheckedChange={(checked) =>
                                    onToggle(permission.id, checked as boolean)
                                }
                            />
                            <span className="text-sm text-foreground/90">
                                {permission.name}
                            </span>
                        </label>
                    );
                })}
            </div>
        </section>
    );
});
