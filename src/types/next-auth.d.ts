// /src/types/next-auth.d.ts
// (O /src/next-auth.d.ts)

import { DefaultSession, DefaultUser } from "next-auth";
import { JWT as DefaultJWT } from "next-auth/jwt";

// 1. Extender el objeto de sesión (para el cliente)
// Esto le dice a TypeScript qué esperar cuando usas useSession()
declare module "next-auth" {
  
  /**
   * El objeto 'session.user' ahora tendrá 'id' y 'plan'
   */
  interface Session {
    user: {
      id: string; // Tu ID personalizado
      plan: string; // Tu plan personalizado (FREE o PREMIUM)
    } & DefaultSession["user"]; // Mantiene las propiedades por defecto (name, email, image)
  }
  
  /**
   * El objeto 'user' (pasado al callback JWT) también tendrá 'plan'
   */
  interface User extends DefaultUser {
    plan: string;
  }
}

// 2. Extender el objeto JWT (para el servidor/callbacks)
// Esto le dice a TypeScript qué esperar en el callback 'jwt'
declare module "next-auth/jwt" {
  /**
   * El token JWT ahora tendrá 'id' y 'plan'
   */
  interface JWT extends DefaultJWT {
    id: string; // Tu ID personalizado
    plan: string; // Tu plan personalizado
  }
}