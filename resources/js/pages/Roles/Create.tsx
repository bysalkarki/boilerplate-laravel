
import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import * as roles from '@/routes/roles';
import InputError from '@/components/input-error';
import HeadingSmall from '@/components/heading-small';

export default function Create() {
    const { data, setData, post, errors } = useForm({
        name: '',
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        post(roles.store().url);
    }

    return (
        <AppLayout
            breadcrumbs={[
                {
                    title: 'Roles',
                    href: roles.index().url,
                },
                {
                    title: 'Create',
                    href: roles.create().url,
                },
            ]}
        >
            <Head title="Create Role" />

            <div className="space-y-6">
                <HeadingSmall
                    title="Create Role"
                    description="Create a new role for your application."
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
                                <Button type="submit">Create</Button>
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
