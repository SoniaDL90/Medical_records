# Sistema de Gestión de Registros Médicos

Práctica de seguridad en Symfony 7 - Sistema hospitalario con control de acceso, roles y auditoría.

## Requisitos

- PHP 8.2+
- Composer 2.x
- Symfony CLI
- MySQL 8.0+

## Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/SoniaDL90/Medical_records.git
cd Medical_records
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar base de datos

Editar el archivo `.env` con tus credenciales:
```
DATABASE_URL="mysql://usuario:contraseña@127.0.0.1:3306/medical_records"
```

### 4. Crear la base de datos y ejecutar migraciones
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Cargar usuarios de prueba
```bash
php bin/console doctrine:fixtures:load
```

### 6. Generar claves JWT
```bash
php bin/console lexik:jwt:generate-keypair
```

### 7. Arrancar el servidor
```bash
symfony server:start
```

## Usuarios de prueba

| Email | Contraseña | Rol | Permisos |
|-------|-----------|-----|---------|
| admin@hospital.com | Password123! | ROLE_ADMIN | Leer, editar y eliminar todos los registros |
| doctor@hospital.com | Password123! | ROLE_DOCTOR | Leer y editar sus propios pacientes |
| nurse@hospital.com | Password123! | ROLE_NURSE | Leer todos los registros, editar limitado |
| reception@hospital.com | Password123! | ROLE_RECEPTIONIST | Solo datos básicos del paciente |

## URLs principales

- Login web: http://127.0.0.1:8000/login
- Panel admin: http://127.0.0.1:8000/admin
- Logs de auditoría: http://127.0.0.1:8000/admin/logs
- API login: POST http://127.0.0.1:8000/api/login
- API registros: GET http://127.0.0.1:8000/api/medical-records/

## API REST con JWT

### Obtener token
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin@hospital.com","password":"Password123!"}'
```

### Usar el token
```bash
curl http://127.0.0.1:8000/api/medical-records/ \
  -H "Authorization: Bearer <token>"
```

## Decisiones de seguridad

- **RBAC**: Sistema de roles jerárquico (ADMIN > DOCTOR > NURSE > RECEPTIONIST)
- **Voter**: Control granular de acceso a registros individuales por rol
- **JWT**: Autenticación stateless para la API REST (token válido 1 hora)
- **Rate Limiting**: Máximo 5 intentos de login cada 15 minutos
- **Auditoría**: Todos los accesos quedan registrados en la base de datos con IP, usuario y timestamp
- **CSRF**: Protección en formularios de edición y borrado
- **Bloqueo**: Cuenta bloqueada tras 5 intentos fallidos consecutivos
- **Accesos sospechosos**: Panel de monitorización de accesos fallidos últimas 24h
