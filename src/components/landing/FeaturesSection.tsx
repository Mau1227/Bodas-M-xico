// /src/components/landing/FeaturesSection.tsx
import {
  SparklesIcon,
  ChartBarIcon,
  CurrencyDollarIcon,
  DevicePhoneMobileIcon,
  PencilIcon, // Para "Sin límite de cambios"
  BellAlertIcon, // Para "Notificaciones automáticas"
} from '@heroicons/react/24/outline'; 

const features = [
  {
    name: 'Diseños hermosos y profesionales',
    description: 'Plantillas elegantes que puedes personalizar a tu gusto.',
    icon: SparklesIcon,
  },
  {
    name: 'Confirmaciones en tiempo real',
    description: 'Tu dashboard se actualiza al instante cada vez que un invitado responde.',
    icon: ChartBarIcon,
  },
  {
    name: 'Sin límite de cambios',
    description: '¿Cambió el horario? Actualízalo y todos tus invitados lo verán.',
    icon: PencilIcon,
  },
  {
    name: 'Notificaciones automáticas',
    description: 'Recibe un aviso por email (o WhatsApp) cada vez que alguien confirma.',
    icon: BellAlertIcon,
  },
  {
    name: 'Ahorra tiempo y dinero',
    description: 'Evita los altos costos de impresión y el seguimiento manual.',
    icon: CurrencyDollarIcon,
  },
  {
    name: 'Perfecto en móviles',
    description: 'Tus invitados verán una invitación perfecta en cualquier dispositivo.',
    icon: DevicePhoneMobileIcon,
  },
];

export const FeaturesSection = () => {
  return (
    <section className="bg-white py-24 sm:py-32">
      <div className="container mx-auto max-w-5xl px-4">
        {/* Encabezado */}
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Todo lo que necesitas en un solo lugar
          </h2>
          <p className="mt-6 text-lg leading-8 text-gray-600">
            Gestiona tu evento de forma profesional y sin complicaciones.
          </p>
        </div>

        {/* Grid de Características */}
        <div className="mx-auto mt-16 grid max-w-none grid-cols-1 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-8">
          {features.map((feature) => (
            <div key={feature.name} className="relative pl-16">
              <dt className="text-base font-semibold leading-7 text-gray-900">
                <div className="absolute left-0 top-0 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                  <feature.icon
                    className="h-6 w-6 text-white"
                    aria-hidden="true"
                  />
                </div>
                {feature.name}
              </dt>
              <dd className="mt-2 text-base leading-7 text-gray-600">
                {feature.description}
              </dd>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};