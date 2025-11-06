// /src/app/components/providers/AuthProvider.tsx
'use client'; // Este debe ser un componente de cliente

import { SessionProvider } from 'next-auth/react';

export default function AuthProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  return <SessionProvider>{children}</SessionProvider>;
}