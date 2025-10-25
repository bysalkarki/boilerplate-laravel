import { useEffect, useRef } from 'react';
import { usePage } from '@inertiajs/react';
import { toast } from 'sonner';

export default function FlashMessageToaster() {
    const { flash } = usePage().props as unknown as {
        flash: { success?: string; error?: string };
    };

    const firedRef = useRef(false);

    useEffect(() => {
        if (firedRef.current) return;

        if (flash.success) toast.success(flash.success);
        if (flash.error) toast.error(flash.error);

        firedRef.current = true;
    }, [flash]);

    return null;
}
