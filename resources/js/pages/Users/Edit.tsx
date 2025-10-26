import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/input-error';
import * as users from '@/routes/users';
import { Role, User } from '@/types';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

export default function Edit({ user, roles }: { user: User, roles: Role[] }) {
    const { data, setData, put, processing, errors } = useForm({
        name: user.name,
        email: user.email,
        role_id: user.roles[0]?.id.toString() || '',
    });

    const { data: passwordData, setData: setPasswordData, put: putPassword, processing: passwordProcessing, errors: passwordErrors, reset } = useForm({
        password: '',
        password_confirmation: '',
    });

    const submitUserDetails = (e: React.FormEvent) => {
        e.preventDefault();
        put(users.update(user).url);
    };

    const submitPassword = (e: React.FormEvent) => {
        e.preventDefault();
        putPassword(users.updatePassword(user).url, {
            onSuccess: () => reset(),
        });
    };

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Users',
                    href: users.index().url,
                },
                {
                    title: 'Edit',
                    href: users.edit(user).url,
                },
            ]}
        >
            <Head title="Edit User" />

            <div className="flex items-center justify-between p-2">
                <h1 className="text-2xl font-semibold">Edit User</h1>
            </div>

            <div className="m-4 rounded-md border p-4">
                <Tabs defaultValue="details">
                    <TabsList>
                        <TabsTrigger value="details">User Details</TabsTrigger>
                        <TabsTrigger value="password">Update Password</TabsTrigger>
                    </TabsList>
                    <TabsContent value="details">
                        <form onSubmit={submitUserDetails} className="space-y-4 p-4">
                            <div>
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    required
                                    autoFocus
                                />
                                <InputError message={errors.name} className="mt-2" />
                            </div>

                            <div>
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                />
                                <InputError message={errors.email} className="mt-2" />
                            </div>

                            <div>
                                <Label htmlFor="role_id">Role</Label>
                                <Select
                                    onValueChange={(value) => setData('role_id', value)}
                                    value={data.role_id}
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Select a role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {roles.map((role) => (
                                            <SelectItem key={role.id} value={String(role.id)}>
                                                {role.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.role_id} className="mt-2" />
                            </div>

                            <div className="flex items-center justify-end mt-4">
                                <Button className="ms-4" disabled={processing}>
                                    Update Details
                                </Button>
                            </div>
                        </form>
                    </TabsContent>
                    <TabsContent value="password">
                        <form onSubmit={submitPassword} className="space-y-4 p-4">
                            <div>
                                <Label htmlFor="password">New Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    className="mt-1 block w-full"
                                    value={passwordData.password}
                                    onChange={(e) => setPasswordData('password', e.target.value)}
                                    required
                                />
                                <InputError message={passwordErrors.password} className="mt-2" />
                            </div>

                            <div>
                                <Label htmlFor="password_confirmation">Confirm New Password</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    className="mt-1 block w-full"
                                    value={passwordData.password_confirmation}
                                    onChange={(e) => setPasswordData('password_confirmation', e.target.value)}
                                    required
                                />
                                <InputError message={passwordErrors.password_confirmation} className="mt-2" />
                            </div>

                            <div className="flex items-center justify-end mt-4">
                                <Button className="ms-4" disabled={passwordProcessing}>
                                    Update Password
                                </Button>
                            </div>
                        </form>
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
