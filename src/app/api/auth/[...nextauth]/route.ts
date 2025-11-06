// /src/app/api/auth/[...nextauth]/route.ts
// 🔥 VERSIÓN COMPLETA - Usando 'mysql2' (sin Prisma) 🔥

import NextAuth, { type AuthOptions } from 'next-auth';
import { type JWT } from 'next-auth/jwt';
import { type Session } from 'next-auth'; // Importa el tipo Session
import CredentialsProvider from 'next-auth/providers/credentials';
import { compare } from 'bcryptjs'; // Para comparar contraseñas
import { db } from '@/lib/db'; // Importa el pool de 'mysql2'
import { RowDataPacket } from 'mysql2'; // Para tipar los resultados de la BD

/**
 * Nota: TypeScript buscará automáticamente el archivo /src/types/next-auth.d.ts
 * que creaste para extender los tipos de Session y JWT.
 */

// Define las opciones de autenticación
export const authOptions: AuthOptions = {
  // 1. Estrategia de Sesión
  // Usaremos JSON Web Tokens (JWT) para las sesiones.
  session: {
    strategy: 'jwt',
  },
  
  // 2. Páginas Personalizadas
  // Define la ruta a tu página de inicio de sesión personalizada.
  pages: {
    signIn: '/login',
    // (Puedes agregar páginas de error, registro, etc., aquí si lo deseas)
  },

  // 3. Proveedores de Autenticación
  providers: [
    CredentialsProvider({
      // Nombre del proveedor (para usar en signIn('credentials', ...))
      name: 'Credentials',
      // Define los campos esperados en el formulario de login
      credentials: {
        email: { label: 'Email', type: 'email' },
        password: { label: 'Contraseña', type: 'password' },
      },

      /**
       * Esta es la función principal del login.
       * Recibe las credenciales del formulario y debe devolver el objeto 'user'
       * o 'null' si la autenticación falla.
       */
      async authorize(credentials) {
        if (!credentials?.email || !credentials?.password) {
          console.log('Faltan credenciales');
          return null; // Error, faltan credenciales
        }

        try {
          // 1. Buscar al usuario en la BD (Consulta SQL)
          const [rows] = await db.query<RowDataPacket[]>(
            'SELECT id, email, password, full_name, plan_type FROM users WHERE email = ? LIMIT 1',
            [credentials.email]
          );

          if (rows.length === 0) {
            console.log('Usuario no encontrado');
            return null; // Usuario no encontrado
          }
          
          const user = rows[0]; // El usuario encontrado

          // 2. Comparar la contraseña encriptada
          const passwordsMatch = await compare(credentials.password, user.password);

          if (!passwordsMatch) {
            console.log('Contraseña incorrecta');
            return null; // Contraseña incorrecta
          }

          // 3. ¡Éxito! Devolvemos el objeto 'user'
          // Esto se pasará al callback 'jwt'
          return {
            id: user.id.toString(),
            email: user.email,
            name: user.full_name,
            plan: user.plan_type, // Campo personalizado
          };

        } catch (error) {
          console.error('Error en authorize:', error);
          return null; // Devuelve null en caso de cualquier error de BD
        }
      },
    }),
    
    // ... Aquí puedes agregar GoogleProvider en el futuro ...
    // GoogleProvider({ ... })
  ],

  // 4. Callbacks
  // Se ejecutan después de que un proveedor (como Credentials) tiene éxito.
  callbacks: {
    /**
     * El callback 'jwt' se ejecuta primero.
     * Inserta los datos del 'user' (de authorize) en el 'token' JWT.
     */
    jwt({ token, user }) {
      if (user) {
        // 'user' solo está presente en el primer inicio de sesión.
        // Añadimos los campos personalizados al token.
        token.id = user.id;
        token.plan = (user as any).plan; // (user as any) para acceder a 'plan'
      }
      return token;
    },

    /**
     * El callback 'session' se ejecuta después.
     * Toma los datos del 'token' y los pasa al objeto 'session'
     * que el frontend (cliente) puede ver.
     */
    session({ session, token }: { session: Session; token: JWT }) {
      // Gracias al archivo next-auth.d.ts, podemos asignar 'id' y 'plan'
      // al objeto session.user sin errores de TypeScript.
      if (session.user) {
        (session.user as any).id = token.id as string;
        (session.user as any).plan = token.plan as string;
      }
      return session;
    },
  },
  
  // 5. Configuración Adicional
  // (Necesario para que Next-Auth funcione, usa la variable del .env)
  secret: process.env.NEXTAUTH_SECRET,
  
  debug: process.env.NODE_ENV === 'development', // Muestra logs en desarrollo
};

// Exportar el handler de NextAuth
const handler = NextAuth(authOptions);
export { handler as GET, handler as POST };