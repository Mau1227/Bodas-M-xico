// /src/components/landing/FAQSection.tsx
// (Esta versión es estática. Para un acordeón real necesitarías estado de React o usar 'details/summary' de HTML)
export const FAQSection = () => {
  return (
    <section id="faq" className="bg-gray-50 py-24 sm:py-32">
      <div className="container mx-auto max-w-5xl px-4">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Preguntas Frecuentes
          </h2>
        </div>
        <div className="mt-16 divide-y divide-gray-200">
          {/* Pregunta 1 */}
          <div className="py-8">
            <dt className="text-lg font-semibold text-gray-900">
              ¿Qué pasa si necesito hacer un cambio de último minuto?
            </dt>
            <dd className="mt-2 text-base text-gray-600">
              No hay problema. Puedes editar la información de tu evento
              (horarios, lugares, etc.) en cualquier momento desde tu dashboard
              y todos tus invitados verán la información actualizada al instante.
            </dd>
          </div>
          {/* Pregunta 2 */}
          <div className="py-8">
            <dt className="text-lg font-semibold text-gray-900">
              ¿Mis invitados necesitan descargar una app?
            </dt>
            <dd className="mt-2 text-base text-gray-600">
              No. Tu invitación es una página web optimizada. Tus invitados
              solo tienen que hacer clic en el link que reciben por email o
              WhatsApp y verán todo en su navegador.
            </dd>
          </div>
          {/* Pregunta 3 */}
          <div className="py-8">
            <dt className="text-lg font-semibold text-gray-900">
              ¿El pago es mensual o único?
            </dt>
            <dd className="mt-2 text-base text-gray-600">
              Es un pago único por evento. Pagas una vez y tienes acceso a
              todas las funciones premium para tu boda sin cargos ocultos ni
              mensualidades.
            </dd>
          </div>
        </div>
      </div>
    </section>
  );
};