// /src/components/landing/ComoFuncionaSection.tsx
import {
  CheckBadgeIcon,
  PencilSquareIcon,
  UserGroupIcon,
  PaperAirplaneIcon,
} from '@heroicons/react/24/outline'; 

const steps = [
  {
    name: 'Paso 1: Crea tu cuenta',
    description: 'Regístrate gratis y llena los detalles básicos de tu evento.',
    icon: PencilSquareIcon,
  },
  {
    name: 'Paso 2: Personaliza tu invitación',
    description: 'Elige una plantilla, sube tus fotos y ajusta los colores.',
    icon: CheckBadgeIcon,
  },
  {
    name: 'Paso 3: Invita a tus seres queridos',
    description: 'Carga tu lista de invitados manual o masivamente (CSV).',
    icon: UserGroupIcon,
  },
  {
    name: 'Paso 4: Recibe confirmaciones',
    description: 'Envía las invitaciones y mira quién confirma en tiempo real.',
    icon: PaperAirplaneIcon,
  },
];

export const ComoFuncionaSection = () => {
  return (
    <section className="bg-gray-50 py-24 sm:py-32">
      <div className="container mx-auto max-w-5xl px-4">
        {/* Encabezado de la sección */}
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Así de fácil funciona
          </h2>
          <p className="mt-6 text-lg leading-8 text-gray-600">
            En menos de 10 minutos tendrás tus invitaciones listas para enviar.
          </p>
        </div>

        {/* Grid de Pasos */}
        <div className="mx-auto mt-16 grid max-w-none grid-cols-1 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-x-8">
          {steps.map((step) => (
            <div key={step.name} className="flex flex-col items-center text-center">
              <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600 text-white">
                <step.icon className="h-6 w-6" aria-hidden="true" />
              </div>
              <h3 className="mt-5 text-lg font-semibold leading-6 text-gray-900">
                {step.name}
              </h3>
              <p className="mt-2 text-base leading-7 text-gray-600">
                {step.description}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};