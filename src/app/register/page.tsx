// /src/app/register/page.tsx
'use client'; // ⬅️ ¡ESTA LÍNEA ES LA SOLUCIÓN!

import { useState } from 'react';
import { useRouter } from 'next/navigation';

// La página debe tener 'export default'
export default function RegisterPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [full_name, setFullName] = useState('');
  const [error, setError] = useState('');
  const router = useRouter();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    try {
      // Esta es la ruta de API que creamos (que funciona sin Prisma)
      const res = await fetch('/api/auth/register', { 
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email,
          password,
          full_name,
        }),
      });

      if (res.ok) {
        // Si el registro es exitoso, envía al usuario a la página de login
        router.push('/login');
      } else {
        const data = await res.json();
        setError(data.message || 'Error al registrar. Intenta de nuevo.');
      }
    } catch (err) {
      setError('Error de conexión.');
    }
  };

  // El resto de tu JSX...
  return (
    <div className="container mx-auto max-w-sm py-20">
      <h1 className="text-3xl font-bold text-center">Crear Cuenta</h1>
      <form onSubmit={handleSubmit} className="mt-8 space-y-4">
        {error && <p className="text-red-500 text-center">{error}</p>}
        <div>
          <label className="block text-sm font-medium text-gray-700">Nombre Completo</label>
          <input
            type="text"
            value={full_name}
            onChange={(e) => setFullName(e.target.value)}
            required
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700">Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700">Contraseña</label>
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2"
          />
        </div>
        <button
          type="submit"
          className="w-full rounded-md bg-blue-600 px-4 py-2 font-medium text-white shadow-sm hover:bg-blue-700"
        >
          Registrarme
        </button>
      </form>
    </div>
  );
}