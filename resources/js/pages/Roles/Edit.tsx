
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import * as roles from '@/routes/roles';
import InputError from '@/components/input-error';
import { Role } from '@/types';
import HeadingSmall from '@/components/heading-small';

export default function Edit({ role }: { role: Role }) {
    const { data, setData, put, errors } = useForm({
        name: role.name,
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        put(roles.update(role).url);
    }

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Roles',
                    href: roles.index().url,
                },
                {
                    title: 'Edit',
                    href: roles.edit(role).url,
                },
            ]}
        >
            <Head title="Edit Role" />

            <div className="space-y-6">
                <HeadingSmall
                    title="Edit Role"
                    description="Edit the role name."
                />

                <Card>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    className="mt-1 block w-full"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="flex items-center gap-4">
                                <Button type="submit">Update</Button>
                                <Button variant="outline" asChild>
                                    <Link href={roles.index().url}>Cancel</Link>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
