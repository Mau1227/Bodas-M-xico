// /src/components/landing/PricingSection.tsx
'use client'; 

import { CheckIcon } from '@heroicons/react/20/solid';
import Link from 'next/link';
import { useSession } from 'next-auth/react'; // Importamos el hook

// (Las listas de características se quedan igual)
const freeFeatures = [
  '2 plantillas básicas',
  'Hasta 5 invitados',
  'Galería de 5 fotos',
  'Confirmaciones por Email',
];
const premiumFeatures = [
  '5 plantillas (Básicas + Premium)',
  'Invitados ilimitados',
  'Galería de 50 fotos',
  'Confirmaciones por Email y WhatsApp',
  'Importación masiva (CSV)',
  'Programar envío',
  'Descargar reportes Excel/PDF',
];

export const PricingSection = () => {
  // Obtenemos el estado de la sesión y el plan del usuario
  const { data: session, status } = useSession();
  const userPlan = session?.user?.plan; // Puede ser 'FREE' o 'PREMIUM'
  const isLoading = status === 'loading';

  // Lógica para el botón/enlace de FREE
  const getFreeButton = () => {
    if (isLoading) {
      return (
        <div className="mt-6 block w-full rounded-md bg-gray-100 px-3 py-2 text-center text-sm font-semibold leading-6 text-gray-400">
          Cargando...
        </div>
      );
    }
    
    if (userPlan === 'FREE') {
      return (
        <div className="mt-6 block w-full rounded-md bg-blue-100 px-3 py-2 text-center text-sm font-semibold leading-6 text-blue-700 ring-1 ring-inset ring-blue-200">
          Actualmente eres free
        </div>
      );
    }

    if (userPlan === 'PREMIUM') {
      return (
        <div className="mt-6 block w-full rounded-md bg-gray-100 px-3 py-2 text-center text-sm font-semibold leading-6 text-gray-500">
          Ya eres Premium
        </div>
      );
    }
    
    // Si no está logueado, mostrar el botón de registro
    return (
      <Link
        href="/register"
        className="mt-6 block w-full rounded-md bg-gray-100 px-3 py-2 text-center text-sm font-semibold leading-6 text-blue-600 shadow-sm hover:bg-gray-200"
      >
        Empezar Gratis
      </Link>
    );
  };

  // Lógica para el botón/enlace de PREMIUM
  const getPremiumButton = () => {
    if (isLoading) {
      return (
        <div className="mt-6 block w-full rounded-md bg-gray-400 px-3 py-2 text-center text-sm font-semibold leading-6 text-white">
          Cargando...
        </div>
      );
    }
    
    if (userPlan === 'PREMIUM') {
      return (
        <div className="mt-6 block w-full rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold leading-6 text-white ring-1 ring-inset ring-blue-200">
          Ya eres Premium
        </div>
      );
    }

    // Si es FREE o no está logueado, mostrar el enlace para pagar
    // (Si está logueado (FREE), va a pagos. Si no, va a login)
    const premiumLink = session ? '/dashboard/pagos' : '/login'; 
    return (
      <Link
        href={premiumLink}
        className="mt-6 block w-full rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500"
      >
        Actualizar a Premium
      </Link>
    );
  };

  return (
    <section id="precios" className="bg-gray-50 py-24 sm:py-32">
      <div className="container mx-auto max-w-5xl px-4">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Un plan simple para tu boda
          </h2>
          <p className="mt-6 text-lg leading-8 text-gray-600">
            Empieza gratis y actualiza solo cuando estés listo.
          </p>
        </div>

        <div className="mx-auto mt-16 grid max-w-none grid-cols-1 gap-8 lg:grid-cols-2">
          {/* Plan FREE */}
          <div className="rounded-3xl bg-white p-8 shadow-lg ring-1 ring-gray-200">
            <h3 className="text-lg font-semibold leading-8 text-gray-900">
              Plan FREE
            </h3>
            <p className="mt-4 text-sm leading-6 text-gray-600">
              Ideal para probar la plataforma o eventos muy pequeños.
            </p>
            <p className="mt-6 flex items-baseline gap-x-1">
              <span className="text-4xl font-bold tracking-tight text-gray-900">
                $0
              </span>
              <span className="text-sm font-semibold leading-6 text-gray-600">
                MXN
              </span>
            </p>
            
            {/* Botón/Mensaje Dinámico de FREE */}
            {getFreeButton()}

            <ul
              role="list"
              className="mt-8 space-y-3 text-sm leading-6 text-gray-600"
            >
              {freeFeatures.map((feature) => (
                <li key={feature} className="flex gap-x-3">
                  <CheckIcon
                    className="h-6 w-5 flex-none text-blue-600"
                    aria-hidden="true"
                  />
                  {feature}
                </li>
              ))}
            </ul>
          </div>

          {/* Plan PREMIUM (Destacado) */}
          <div className="rounded-3xl bg-white p-8 shadow-lg ring-2 ring-blue-600">
            <h3 className="text-lg font-semibold leading-8 text-blue-600">
              Plan PREMIUM
            </h3>
            <p className="mt-4 text-sm leading-6 text-gray-600">
              Todas las funciones para un evento inolvidable y sin límites.
            </p>
            <p className="mt-6 flex items-baseline gap-x-1">
              <span className="text-4xl font-bold tracking-tight text-gray-900">
                $999
              </span>
              <span className="text-sm font-semibold leading-6 text-gray-600">
                MXN (pago único)
              </span>
            </p>

            {/* Botón/Mensaje Dinámico de PREMIUM */}
            {getPremiumButton()}

            {/* Texto persuasivo añadido */}
            <p className="mt-8 text-sm font-semibold text-gray-700">
              Desbloquea todo el potencial de tu evento:
            </p>
            
            <ul
              role="list"
              className="mt-6 space-y-3 text-sm leading-6 text-gray-600"
            >
              {premiumFeatures.map((feature) => (
                <li key={feature} className="flex gap-x-3">
                  <CheckIcon
                    className="h-6 w-5 flex-none text-blue-600"
                    aria-hidden="true"
                  />
                  {feature}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
    </section>
  );
};