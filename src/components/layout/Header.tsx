// /src/components/layout/Header.tsx
import Link from 'next/link';

export const Header = () => {
  return (
    <header className="sticky top-0 z-50 w-full bg-white shadow-sm">
      <nav className="container mx-auto flex max-w-5xl items-center justify-between p-4">
        {/* 1. Logo (Izquierda) */}
        <Link href="/" className="text-2xl font-bold text-gray-900">
          BodasSaaS
        </Link>

        {/* 2. Links de Navegación (Centro) - (Añadiremos más después) */}
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

        {/* 3. Botones de Acción (Derecha) */}
        <div className="flex items-center space-x-3">
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
        </div>
      </nav>
    </header>
  );
};