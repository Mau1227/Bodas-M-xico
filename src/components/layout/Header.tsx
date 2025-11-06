// /src/components/layout/Header.tsx
'use client'; // ⬅️ 1. Convertir a Componente de Cliente

import Link from 'next/link';
import { useSession, signOut } from 'next-auth/react'; // ⬅️ 2. Importar hooks

export const Header = () => {
  // 3. Obtener el estado de la sesión
  const { data: session, status } = useSession();
  // status puede ser: 'loading', 'authenticated', 'unauthenticated'

  // 4. (Opcional) No mostrar nada mientras carga para evitar saltos
  if (status === 'loading') {
    return (
      <header className="sticky top-0 z-50 w-full bg-white shadow-sm">
        <nav className="container mx-auto flex max-w-5xl items-center justify-between p-4 h-[68px]">
          {/* Espacio reservado para evitar saltos de layout */}
        </nav>
      </header>
    );
  }

  return (
    <header className="sticky top-0 z-50 w-full bg-white shadow-sm">
      <nav className="container mx-auto flex max-w-5xl items-center justify-between p-4">
        {/* 1. Logo (Izquierda) */}
        <Link href="/" className="text-2xl font-bold text-gray-900">
          BodasSaaS
        </Link>

        {/* 2. Links de Navegación (Centro) */}
        <div className="hidden space-x-6 md:flex">
          <Link href="/#precios" className="text-sm font-medium text-gray-600 hover:text-gray-900">
            Precios
          </Link>
          <Link href="/#plantillas" className="text-sm font-medium text-gray-600 hover:text-gray-900">
            Plantillas
          </Link>
          <Link href="/#faq" className="text-sm font-medium text-gray-600 hover:text-gray-900">
            FAQ
          </Link>
        </div>

        {/* 3. Botones de Acción (Derecha) - LÓGICA CONDICIONAL */}
        <div className="flex items-center space-x-3">
          {session ? (
            // 5. Si el usuario ESTÁ autenticado
            <>
              <span className="hidden text-sm text-gray-600 sm:block">
                ¡Hola, {session.user?.name?.split(' ')[0]}!
              </span>
              <Link
                href="/dashboard" // Enviar al panel de control
                className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
              >
                Mi Dashboard
              </Link>
              <button
                onClick={() => signOut({ callbackUrl: '/' })} // ⬅️ 6. Botón de Cerrar Sesión
                className="hidden text-sm font-medium text-gray-600 hover:text-gray-900 sm:block"
              >
                Cerrar Sesión
              </button>
            </>
          ) : (
            // 7. Si el usuario NO ESTÁ autenticado
            <>
              <Link
                href="/login"
                className="hidden text-sm font-medium text-gray-600 hover:text-gray-900 sm:block"
              >
                Iniciar Sesión
              </Link>
              <Link
                href="/register"
                className="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
              >
                Crear mi invitación
              </Link>
            </>
          )}
        </div>
      </nav>
    </header>
  );
};