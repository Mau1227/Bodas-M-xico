// /src/components/layout/Footer.tsx
import Link from 'next/link';

// (Opcional: Si quieres usar iconos de redes sociales, importa iconos)
// import { FaFacebook, FaInstagram, FaTwitter } from 'react-icons/fa';

export const Footer = () => {
  return (
    <footer className="bg-gray-900 text-white">
      <div className="container mx-auto max-w-5xl px-4 py-12">
        <div className="flex flex-col justify-between sm:flex-row">
          
          {/* 1. Logo e Info */}
          <div>
            <h3 className="text-xl font-bold">BodasSaaS</h3>
            <p className="mt-2 text-sm text-gray-400">
              Invitaciones digitales fáciles y elegantes.
            </p>
            {/* Opcional: Iconos de Redes Sociales */}
            <div className="mt-4 flex space-x-4">
              {/* <Link href="#" aria-label="Facebook" className="text-gray-400 hover:text-white">
                <FaFacebook size={20} />
              </Link>
              <Link href="#" aria-label="Instagram" className="text-gray-400 hover:text-white">
                <FaInstagram size={20} />
              </Link>
              <Link href="#" aria-label="Twitter" className="text-gray-400 hover:text-white">
                <FaTwitter size={20} />
              </Link>
              */}
              <span className="text-sm text-gray-500">(Redes Sociales)</span>
            </div>
          </div>

          {/* 2. Links Legales */}
          <div className="mt-8 sm:mt-0">
            <h4 className="text-sm font-semibold uppercase text-gray-400">
              Legal
            </h4>
            <ul className="mt-4 space-y-2">
              <li>
                <Link
                  href="/terminos"
                  className="text-sm text-gray-400 hover:text-white"
                >
                  Términos y Condiciones
                </Link>
              </li>
              <li>
                <Link
                  href="/privacidad"
                  className="text-sm text-gray-400 hover:text-white"
                >
                  Aviso de Privacidad
                </Link>
              </li>
              <li>
                <Link
                  href="/contacto"
                  className="text-sm text-gray-400 hover:text-white"
                >
                  Contacto
                </Link>
              </li>
            </ul>
          </div>

        </div>

        {/* Barra inferior de Copyright */}
        <div className="mt-12 border-t border-gray-700 pt-8 text-center text-sm text-gray-400">
          <p>&copy; {new Date().getFullYear()} BodasSaaS. Todos los derechos reservados.</p>
          <p className="mt-1">Hecho con ❤️ para tu día especial.</p>
        </div>
      </div>
    </footer>
  );
};