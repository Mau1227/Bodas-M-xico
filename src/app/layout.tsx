// /src/app/layout.tsx
import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css'; // Asegúrate que tu archivo de estilos de Tailwind esté importado
import { Header } from '@/components/layout/Header'; // ¡Importa tu Header!

const inter = Inter({ subsets: ['latin'] });

export const metadata: Metadata = {
  title: 'Invitaciones Digitales para Bodas',
  description: 'Crea, personaliza y gestiona tu invitación de boda en minutos.',
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="es">
      <body className={inter.className}>
        
        {/* El Header se renderiza aquí, fuera del 'children' */}
        <Header /> 

        {/* 'children' renderizará el contenido de tu page.tsx */}
        <main>{children}</main>

        {/* (Opcional) Aquí podrías añadir un <Footer /> global también */}

      </body>
    </html>
  );
}