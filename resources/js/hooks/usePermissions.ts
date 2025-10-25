import { usePage } from '@inertiajs/react';
import { PageProps } from '@/types';

export function usePermissions() {
    const { props } = usePage<PageProps>();
    const userPermissions = props.auth.user?.permissions || [];

    const hasPermission = (permission: string): boolean => {
        return userPermissions.includes(permission);
    };

    return { hasPermission };
}
