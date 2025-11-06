// /src/app/api/auth/register/route.ts
// 🔥 NUEVA VERSIÓN - Usando 'mysql2' 🔥

import { NextResponse } from 'next/server';
import { hash } from 'bcryptjs';
import { db } from '@/lib/db'; // Importa el pool de 'mysql2'
import { RowDataPacket } from 'mysql2'; // Para tipar los resultados

export async function POST(req: Request) {
  try {
    const body = await req.json();
    const { email, password, full_name, phone } = body;

    // 1. Validar
    if (!email || !password || !full_name) {
      return new NextResponse('Faltan datos (Email, Contraseña, Nombre)', { status: 400 });
    }

    // 2. Verificar si el email ya existe (Consulta SQL)
    // Usamos '?' para prevenir Inyección SQL
    const [existingUsers] = await db.query<RowDataPacket[]>(
      'SELECT email FROM users WHERE email = ?',
      [email]
    );

    if (existingUsers.length > 0) {
      return new NextResponse('El email ya está en uso', { status: 409 });
    }

    // 3. Encriptar la contraseña
    const hashedPassword = await hash(password, 12);

    // 4. Crear el usuario (Consulta SQL)
    // ¡Aquí está el cambio! Usamos 'db.query' en lugar de 'db.users.create'
    await db.query(
      'INSERT INTO users (email, password, full_name, phone, plan_type) VALUES (?, ?, ?, ?, ?)',
      [email, hashedPassword, full_name, phone || null, 'FREE']
    );

    // Nota: El 'id' se autoincrementa solo en la BD.
    return NextResponse.json({
      email: email,
      full_name: full_name,
    }, { status: 201 });

  } catch (error) {
    console.error('[AUTH_REGISTER_ERROR]', error);
    return new NextResponse('Error Interno del Servidor', { status: 500 });
  }
}