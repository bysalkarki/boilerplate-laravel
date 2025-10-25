import { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 40 40"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
        >
            {/* Outer circle */}
            <circle cx="20" cy="20" r="18" />

            {/* Stylized “A” shape */}
            <path d="M12 28L20 10L28 28M16 22H24" />
        </svg>
    );
}
