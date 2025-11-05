// /src/components/landing/HeroSection.tsx
import Link from 'next/link';

export const HeroSection = () => {
  return (
    <section className="bg-white">
      <div className="container mx-auto flex max-w-5xl flex-col items-center px-4 py-20 text-center sm:py-32">
        
        {/* Título Principal (H1) */}
        <h1 className="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">
          Invitaciones digitales hermosas para tu boda
        </h1>

        {/* Subtítulo */}
        <p className="mt-6 max-w-2xl text-lg leading-8 text-gray-600">
          Crea, personaliza y gestiona tus invitaciones en minutos. Recibe
          confirmaciones automáticas y ahorra tiempo y dinero.
        </p>

        {/* Botón Call-to-Action (CTA) */}
        <div className="mt-10">
          <Link
            href="/register"
            className="rounded-md bg-blue-600 px-5 py-3 text-base font-semibold text-white shadow-sm hover:bg-blue-700"
          >
            Crear mi invitación gratis
          </Link>
        </div>

      </div>
    </section>
  );
};