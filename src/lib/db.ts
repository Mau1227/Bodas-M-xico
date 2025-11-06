// /src/lib/db.ts
// 🔥 NUEVA VERSIÓN - Usando 'mysql2' en lugar de Prisma 🔥

import mysql from 'mysql2/promise';

// Verifica que la DATABASE_URL exista (la reutilizamos del .env)
if (!process.env.DATABASE_URL) {
  throw new Error('La variable DATABASE_URL no está definida en .env');
}

// createPool es la forma correcta de manejar conexiones en un entorno
// de servidor como Next.js, ya que reutiliza las conexiones.
const pool = mysql.createPool(process.env.DATABASE_URL);

// Exportamos el pool. Lo usaremos para hacer consultas.
// En lugar de 'db.users.findUnique', usaremos 'db.query(...)'
export const db = pool;