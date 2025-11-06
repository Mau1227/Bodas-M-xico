// /src/components/landing/TestimonialsSection.tsx
export const TestimonialsSection = () => {
  return (
    <section className="bg-white py-24 sm:py-32">
      <div className="container mx-auto max-w-5xl px-4">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Lo que dicen nuestros novios
          </h2>
        </div>
        <div className="mx-auto mt-16 grid max-w-none grid-cols-1 gap-y-10 lg:grid-cols-2 lg:gap-x-8">
          {/* Testimonio 1 */}
          <figure className="rounded-2xl bg-gray-50 p-8">
            <blockquote className="text-lg leading-7 text-gray-700">
              <p>
                “La plataforma nos salvó la vida. Pudimos gestionar los 200
                invitados sin estrés y el dashboard de confirmaciones es una
                maravilla. ¡Totalmente recomendado!”
              </p>
            </blockquote>
            <figcaption className="mt-6 flex items-center gap-x-4">
              {/* <img className="h-10 w-10 rounded-full bg-gray-200" src="..." alt="" /> */}
              <div>
                <div className="font-semibold text-gray-900">
                  Sofía y Carlos
                </div>
                <div className="text-gray-600">Boda Noviembre 2024</div>
              </div>
            </figcaption>
          </figure>

          {/* Testimonio 2 */}
          <figure className="rounded-2xl bg-gray-50 p-8">
            <blockquote className="text-lg leading-7 text-gray-700">
              <p>
                “Nos encantó poder enviar recordatorios por WhatsApp una semana
                antes. Todos nuestros invitados amaron la invitación digital y
                lo fácil que fue confirmar.”
              </p>
            </blockquote>
            <figcaption className="mt-6 flex items-center gap-x-4">
              {/* <img className="h-10 w-10 rounded-full bg-gray-200" src="..." alt="" /> */}
              <div>
                <div className="font-semibold text-gray-900">
                  Mariana y Juan
                </div>
                <div className="text-gray-600">Boda Enero 2025</div>
              </div>
            </figcaption>
          </figure>
        </div>
      </div>
    </section>
  );
};