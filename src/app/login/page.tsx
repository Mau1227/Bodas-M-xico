// /src/app/login/page.tsx
'use client';

import { useState } from 'react';
import { signIn } from 'next-auth/react';
import { useRouter } from 'next/navigation';

export default function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const router = useRouter();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');

    try {
      // Usamos la función signIn de Next-Auth
      const result = await signIn('credentials', {
        redirect: false, // No redirige automáticamente, manejamos la respuesta
        email,
        password,
      });

      if (result?.ok) {
        // ¡Éxito! Redirige al Dashboard
        router.push('/dashboard');
      } else {
        // result.error ya viene con un mensaje (ej. "CredentialsSignin")
        setError('Email o contraseña incorrectos.');
      }
    } catch (err) {
      setError('Error de conexión.');
    }
  };

  return (
    <div className="container mx-auto max-w-sm py-20">
      <h1 className="text-3xl font-bold text-center">Iniciar Sesión</h1>
      <form onSubmit={handleSubmit} className="mt-8 space-y-4">
        {error && <p className="text-red-500 text-center">{error}</p>}
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
          Entrar
        </button>
      </form>
    </div>
  );
}