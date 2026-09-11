#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Translate the Spanish UI text on the login, dashboard and audit-log pages to English.
Only touches literal text strings; CSS, Twig logic and routes are untouched."""

replacements_by_file = {
    "templates/login/index.html.twig": [
        ('<html lang="es">', '<html lang="en">'),
        ("<title>MedRecord — Acceso Seguro</title>", "<title>MedRecord — Secure Access</title>"),
        ("<p class=\"brand-tagline\">Sistema Hospitalario de Gestión Segura</p>",
         "<p class=\"brand-tagline\">Secure Hospital Management System</p>"),
        ("<h4>Control de Acceso por Roles</h4><p>Médicos, enfermeros, recepción y admin</p>",
         "<h4>Role-Based Access Control</h4><p>Doctors, nurses, reception and admin</p>"),
        ("<h4>Auditoría Completa</h4><p>Registro de todos los accesos</p>",
         "<h4>Full Audit Trail</h4><p>Every access is logged</p>"),
        ("<h4>JWT y Cifrado</h4><p>API REST segura con tokens</p>",
         "<h4>JWT &amp; Encryption</h4><p>Secure REST API with tokens</p>"),
        ("<h2>Bienvenido</h2>", "<h2>Welcome</h2>"),
        ("<p>Accede con tus credenciales hospitalarias</p>", "<p>Sign in with your hospital credentials</p>"),
        ("<label>Correo electrónico</label>", "<label>Email</label>"),
        ("<label>Contraseña</label>", "<label>Password</label>"),
        ('placeholder="usuario@hospital.com"', 'placeholder="user@hospital.com"'),
        ('<button type="submit" class="btn-login">Iniciar Sesión</button>',
         '<button type="submit" class="btn-login">Sign In</button>'),
        ('<div class="security-badge">🔒 Conexión cifrada · RGPD compliant</div>',
         '<div class="security-badge">🔒 Encrypted connection · GDPR compliant</div>'),
        ("<strong>Usuarios de prueba:</strong>", "<strong>Test users:</strong>"),
    ],
    "templates/login/dashboard.html.twig": [
        ('<html lang="es">', '<html lang="en">'),
        ('<div class="nav-label">Principal</div>', '<div class="nav-label">Main</div>'),
        ('<div class="nav-label">Administración</div>', '<div class="nav-label">Administration</div>'),
        ("<p>Panel de control del Sistema de Registros Médicos</p>",
         "<p>Medical Records System control panel</p>"),
        ("Registros Médicos", "Medical Records"),
        ("Panel Admin", "Admin Panel"),
        ("Logs de Auditoría", "Audit Logs"),
        ("Cerrar Sesión", "Log Out"),
        ("<h1>Bienvenido, {{ app.user.name }}</h1>", "<h1>Welcome, {{ app.user.name }}</h1>"),
        ('<div class="stat-label">Registros médicos</div>', '<div class="stat-label">Medical records</div>'),
        ('<div class="stat-label">Usuarios activos</div>', '<div class="stat-label">Active users</div>'),
        ('<div class="stat-label">Pacientes</div>', '<div class="stat-label">Patients</div>'),
        ('<div class="stat-label">Sistema seguro</div>', '<div class="stat-label">Secure system</div>'),
        ("<p>Ver y gestionar historiales clínicos</p>", "<p>View and manage clinical records</p>"),
        ("<p>Supervisar accesos y actividad</p>", "<p>Monitor access and activity</p>"),
    ],
    "templates/admin/logs.html.twig": [
        ('<html lang="es">', '<html lang="en">'),
        ("<title>MedRecord — Logs de Auditoría</title>", "<title>MedRecord — Audit Logs</title>"),
        ("<h1>Logs de Auditoría</h1>", "<h1>Audit Logs</h1>"),
        ('<div class="nav-label">Principal</div>', '<div class="nav-label">Main</div>'),
        ('<div class="nav-label">Administración</div>', '<div class="nav-label">Administration</div>'),
        ("Registros Médicos", "Medical Records"),
        ("Panel Admin", "Admin Panel"),
        ("Logs de Auditoría", "Audit Logs"),
        ("Cerrar Sesión", "Log Out"),
        ("<p>Registro completo de accesos y actividad del sistema</p>",
         "<p>Full log of system access and activity</p>"),
        ("<div class=\"stat-label\">Total accesos</div>", "<div class=\"stat-label\">Total access events</div>"),
        ("<div class=\"stat-label\">Fallos últimas 24h</div>", "<div class=\"stat-label\">Failures last 24h</div>"),
        ("<div class=\"stat-label\">Accesos sospechosos 24h</div>",
         "<div class=\"stat-label\">Suspicious access 24h</div>"),
        ("<label>Acción:</label>", "<label>Action:</label>"),
        (">Todas</option>", ">All</option>"),
        (">Login exitoso</option>", ">Successful login</option>"),
        (">Login fallido</option>", ">Failed login</option>"),
        (">Acceso denegado</option>", ">Access denied</option>"),
        ("<label>Resultado:</label>", "<label>Result:</label>"),
        (">Todos</option>", ">All</option>"),
        (">Exitoso</option>", ">Successful</option>"),
        (">Fallido</option>", ">Failed</option>"),
        ("<label>Usuario:</label>", "<label>User:</label>"),
        ("<label>Desde:</label>", "<label>From:</label>"),
        ("<label>Hasta:</label>", "<label>To:</label>"),
        ('<button type="submit" class="btn-filter">Filtrar</button>',
         '<button type="submit" class="btn-filter">Filter</button>'),
        ('class="btn-clear">Limpiar</a>', 'class="btn-clear">Clear</a>'),
        ("<h3>Últimos 100 accesos</h3>", "<h3>Last 100 access events</h3>"),
        ("{{ logs|length }} registros", "{{ logs|length }} records"),
        ("<th>Fecha</th>", "<th>Date</th>"),
        ("<th>Usuario</th>", "<th>User</th>"),
        ("<th>Acción</th>", "<th>Action</th>"),
        ("<th>Recurso</th>", "<th>Resource</th>"),
        ("<th>Resultado</th>", "<th>Result</th>"),
        ("<th>Detalles</th>", "<th>Details</th>"),
        (">Exitoso</span>", ">Successful</span>"),
        (">Fallido</span>", ">Failed</span>"),
        ("Accesos sospechosos últimas 24h — {{ suspicious|length }} detectados",
         "Suspicious access last 24h — {{ suspicious|length }} detected"),
    ],
}

for path, pairs in replacements_by_file.items():
    with open(path, "r", encoding="utf-8") as f:
        content = f.read()
    for old, new in pairs:
        count = content.count(old)
        if count == 0:
            print(f"WARNING: not found in {path}: {old!r}")
        content = content.replace(old, new)
    with open(path, "w", encoding="utf-8") as f:
        f.write(content)
    print(f"Updated {path}")
