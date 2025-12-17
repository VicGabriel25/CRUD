<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Mundo - Sistema de Gerenciamento Geográfico</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0a0e27;
            --secondary-color: #1a1f3a;
            --accent-color: #00ff88;
            --accent-gradient: linear-gradient(90deg, #00ff88, #00cc6a);
            --text-primary: #ffffff;
            --text-secondary: #b0b0c0;
            --text-tertiary: #808090;
            --border-radius: 12px;
            --box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--primary-color);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 255, 136, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 20%);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header melhorado */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 0;
            margin-bottom: 40px;
            border-bottom: 2px solid rgba(0, 255, 136, 0.2);
            position: relative;
        }

        header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100px;
            height: 2px;
            background: var(--accent-gradient);
            border-radius: 2px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }

        .logo-icon {
            font-size: 2.8rem;
            color: var(--accent-color);
            filter: drop-shadow(0 0 10px rgba(0, 255, 136, 0.5));
        }

        .logo-text h1 {
            font-size: 2.2rem;
            margin: 0;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .logo-text p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        /* Badge do sistema */
        .system-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 255, 136, 0.1);
            padding: 10px 20px;
            border-radius: 20px;
            border: 1px solid rgba(0, 255, 136, 0.3);
            font-size: 0.9rem;
            color: var(--accent-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Navegação */
        .nav-links {
            display: flex;
            gap: 15px;
            background: rgba(26, 31, 58, 0.8);
            backdrop-filter: blur(10px);
            padding: 10px;
            border-radius: var(--border-radius);
            border: 1px solid rgba(0, 255, 136, 0.1);
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-links a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--accent-gradient);
            transition: var(--transition);
            opacity: 0.1;
        }

        .nav-links a:hover::before {
            left: 0;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--accent-color);
            background: rgba(0, 255, 136, 0.1);
            transform: translateY(-2px);
        }

        /* Cards de estatísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(145deg, var(--secondary-color), rgba(26, 31, 58, 0.8));
            border-radius: var(--border-radius);
            padding: 25px;
            border: 1px solid rgba(0, 255, 136, 0.1);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent-gradient);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-color);
            box-shadow: var(--box-shadow);
        }

        .stat-icon {
            font-size: 2.2rem;
            margin-bottom: 15px;
            display: inline-block;
            padding: 15px;
            border-radius: 12px;
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.2);
        }

        .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            margin: 15px 0;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
        }

        /* Seções principais */
        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .section {
            background: linear-gradient(145deg, rgba(26, 31, 58, 0.95), rgba(26, 31, 58, 0.8));
            border-radius: var(--border-radius);
            padding: 30px;
            border: 1px solid rgba(0, 255, 136, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
        }

        .section h2 {
            color: var(--accent-color);
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        /* Botões melhorados */
        .btn {
            background: var(--accent-gradient);
            color: #000;
            border: none;
            padding: 14px 28px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            text-decoration: none;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 255, 136, 0.2);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 255, 136, 0.3);
            color: #000;
        }

        .btn-outline {
            background: transparent;
            color: var(--accent-color);
            border: 2px solid var(--accent-color);
            box-shadow: none;
        }

        .btn-outline:hover {
            background: var(--accent-gradient);
            color: #000;
            border-color: transparent;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .btn-sm i {
            font-size: 0.9rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .action-btn {
            background: rgba(0, 255, 136, 0.08);
            border: 2px solid rgba(0, 255, 136, 0.3);
            color: var(--accent-color);
            padding: 25px 15px;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            transition: var(--transition);
            text-decoration: none;
            display: block;
        }

        .action-btn:hover {
            background: var(--accent-gradient);
            color: #000;
            transform: translateY(-5px);
            border-color: transparent;
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }

        /* NOVO: Módulo de Consulta */
        .consulta-module {
            background: linear-gradient(145deg, rgba(26, 31, 58, 0.9), rgba(26, 31, 58, 0.7));
            border-radius: var(--border-radius);
            padding: 25px;
            border: 1px solid rgba(0, 255, 136, 0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .consulta-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(0, 255, 136, 0.1);
        }

        .consulta-header h3 {
            color: var(--accent-color);
            font-size: 1.3rem;
            margin: 0;
        }

        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filtro-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filtro-group label {
            color: var(--accent-color);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filtro-group select,
        .filtro-group input {
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(0, 255, 136, 0.2);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: var(--transition);
        }

        .filtro-group select:focus,
        .filtro-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
        }

        .consulta-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .resultados-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 255, 136, 0.05);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(0, 255, 136, 0.1);
            margin-top: 20px;
        }

        .resultados-count {
            color: var(--accent-color);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .export-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .export-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 6px;
            color: var(--accent-color);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .export-btn:hover {
            background: rgba(0, 255, 136, 0.2);
            transform: translateY(-2px);
        }

        /* Tabelas */
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 136, 0.1);
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(26, 31, 58, 0.5);
        }

        th {
            background: linear-gradient(90deg, rgba(0, 255, 136, 0.1), rgba(0, 255, 136, 0.05));
            color: var(--accent-color);
            padding: 18px 16px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid rgba(0, 255, 136, 0.2);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
        }

        tr:hover td {
            background: rgba(0, 255, 136, 0.03);
        }

        /* Formulários - CORREÇÃO ESPECÍFICA DO SELECT DE CONTINENTES */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--accent-color);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(0, 255, 136, 0.2);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: var(--transition);
        }

        /* CORREÇÃO CRÍTICA: Select de continentes visível */
        #paisContinente,
        #filtroContinente {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%2300ff88" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 20px;
            padding-right: 45px;
        }

        /* Opções visíveis */
        #paisContinente option,
        #filtroContinente option {
            background: var(--secondary-color);
            color: var(--text-primary);
            padding: 12px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
            background: rgba(255, 255, 255, 0.08);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(10px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: linear-gradient(145deg, var(--secondary-color), rgba(26, 31, 58, 0.95));
            padding: 40px;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 600px;
            border: 2px solid var(--accent-color);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: var(--accent-color);
            font-size: 1.5rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .modal-close:hover {
            background: rgba(0, 255, 136, 0.1);
            transform: rotate(90deg);
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        /* Loading */
        .loading {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 3px solid rgba(0, 255, 136, 0.1);
            border-radius: 50%;
            border-top-color: var(--accent-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Footer */
        footer {
            margin-top: 50px;
            padding: 30px 0;
            border-top: 1px solid rgba(0, 255, 136, 0.1);
            text-align: center;
            color: var(--text-secondary);
        }

        .footer-features {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-features div {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        /* Responsividade */
        @media (max-width: 1200px) {
            .container {
                max-width: 100%;
                padding: 15px;
            }
        }

        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .system-badge {
                position: relative;
                top: 0;
                right: 0;
                margin-top: 10px;
                justify-content: center;
                width: fit-content;
                margin-left: auto;
                margin-right: auto;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .filtros-grid {
                grid-template-columns: 1fr;
            }
            
            .consulta-actions {
                flex-direction: column;
            }
            
            .resultados-info {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .footer-features {
                flex-direction: column;
                gap: 15px;
            }
            
            .modal-content {
                padding: 25px;
            }
        }

        @media (max-width: 576px) {
            .logo-text h1 {
                font-size: 1.8rem;
            }
            
            .logo-icon {
                font-size: 2.2rem;
            }
            
            .btn {
                padding: 12px 20px;
                font-size: 0.9rem;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links a {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .export-options {
                justify-content: center;
            }
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(0, 255, 136, 0.15);
            color: var(--accent-color);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .badge-info {
            background: rgba(99, 102, 241, 0.15);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        /* Notificações */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            color: var(--accent-color);
            padding: 15px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 10000;
            backdrop-filter: blur(10px);
            animation: fadeIn 0.3s ease-out;
        }

        .notification.error {
            background: rgba(255, 107, 107, 0.1);
            border-color: rgba(255, 107, 107, 0.3);
            color: #ff6b6b;
        }

        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(26, 31, 58, 0.5);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-gradient);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(#00cc6a, var(--accent-color));
        }

        /* Filtros avançados */
        .filtro-avancado {
            background: rgba(0, 255, 136, 0.05);
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid rgba(0, 255, 136, 0.1);
            display: none;
        }

        .filtro-avancado.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .toggle-filtro-avancado {
            background: none;
            border: none;
            color: var(--accent-color);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: var(--transition);
        }

        .toggle-filtro-avancado:hover {
            background: rgba(0, 255, 136, 0.1);
        }

        .filtro-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filtro-range input {
            flex: 1;
        }

        .range-value {
            color: var(--accent-color);
            font-weight: bold;
            min-width: 60px;
            text-align: center;
        }

        /* Ordenação */
        .ordenacao {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .ordenacao label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .ordenacao select {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(0, 255, 136, 0.2);
            border-radius: 5px;
            color: var(--text-primary);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo">
                <i class="fas fa-globe-americas logo-icon"></i>
                <div class="logo-text">
                    <h1>CRUD Mundo</h1>
                    <p>Sistema de Gerenciamento Geográfico Avançado</p>
                </div>
            </div>
            
            <div class="system-badge">
                <i class="fas fa-database"></i>
                <span>Sistema Geográfico v2.0</span>
            </div>
            
            <nav class="nav-links">
                <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="#paises"><i class="fas fa-flag"></i> Países</a>
                <a href="#cidades"><i class="fas fa-city"></i> Cidades</a>
                <a href="mapa.html" target="_blank"><i class="fas fa-map"></i> Mapa 3D</a>
                <a href="#estatisticas"><i class="fas fa-chart-bar"></i> Estatísticas</a>
            </nav>
        </header>
        
        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <h3>Total de Países</h3>
                <div class="stat-number" id="totalPaises">0</div>
                <p style="color: var(--text-secondary); margin: 0;">Cadastrados no sistema</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-city"></i>
                </div>
                <h3>Total de Cidades</h3>
                <div class="stat-number" id="totalCidades">0</div>
                <p style="color: var(--text-secondary); margin: 0;">Cadastradas no sistema</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <h3>Continentes</h3>
                <div class="stat-number" id="totalContinentes">0</div>
                <p style="color: var(--text-secondary); margin: 0;">Com países cadastrados</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>População Total</h3>
                <div class="stat-number" id="populacaoTotal">0</div>
                <p style="color: var(--text-secondary); margin: 0;">Habitantes registrados</p>
            </div>
        </div>
        
        <!-- Conteúdo Principal -->
        <div class="main-content">
            <div>
                <!-- NOVO: Módulo de Consulta de Países -->
                <div class="consulta-module">
                    <div class="consulta-header">
                        <i class="fas fa-search" style="color: var(--accent-color); font-size: 1.5rem;"></i>
                        <h3>Consulta Avançada de Países</h3>
                    </div>
                    
                    <div class="filtros-grid">
                        <div class="filtro-group">
                            <label for="filtroTipo"><i class="fas fa-filter"></i> Tipo de Consulta</label>
                            <select id="filtroTipo">
                                <option value="todos">Todos os Países Cadastrados</option>
                                <option value="continente">Por Continente</option>
                                <option value="nome">Por Nome</option>
                                <option value="populacao">Por Faixa de População</option>
                                <option value="idioma">Por Idioma</option>
                            </select>
                        </div>
                        
                        <div class="filtro-group" id="filtroContinenteGroup" style="display: none;">
                            <label for="filtroContinente"><i class="fas fa-globe-americas"></i> Continente</label>
                            <select id="filtroContinente">
                                <option value="">Selecione um continente...</option>
                                <option value="África">África</option>
                                <option value="América">América</option>
                                <option value="Ásia">Ásia</option>
                                <option value="Europa">Europa</option>
                                <option value="Oceania">Oceania</option>
                            </select>
                        </div>
                        
                        <div class="filtro-group" id="filtroNomeGroup" style="display: none;">
                            <label for="filtroNome"><i class="fas fa-font"></i> Nome do País</label>
                            <input type="text" id="filtroNome" placeholder="Digite o nome do país...">
                        </div>
                        
                        <div class="filtro-group" id="filtroPopulacaoGroup" style="display: none;">
                            <label for="filtroPopulacaoMin"><i class="fas fa-users"></i> Faixa de População</label>
                            <div class="filtro-range">
                                <input type="number" id="filtroPopulacaoMin" placeholder="Mínimo" min="0">
                                <span style="color: var(--text-secondary);">a</span>
                                <input type="number" id="filtroPopulacaoMax" placeholder="Máximo" min="0">
                            </div>
                        </div>
                        
                        <div class="filtro-group" id="filtroIdiomaGroup" style="display: none;">
                            <label for="filtroIdioma"><i class="fas fa-language"></i> Idioma</label>
                            <input type="text" id="filtroIdioma" placeholder="Digite o idioma...">
                        </div>
                    </div>
                    
                    <!-- Ordenação -->
                    <div class="ordenacao">
                        <label for="ordenarPor"><i class="fas fa-sort-amount-down"></i> Ordenar por:</label>
                        <select id="ordenarPor">
                            <option value="nome">Nome (A-Z)</option>
                            <option value="nome_desc">Nome (Z-A)</option>
                            <option value="populacao">População (Menor-Maior)</option>
                            <option value="populacao_desc">População (Maior-Menor)</option>
                            <option value="continente">Continente</option>
                        </select>
                    </div>
                    
                    <div class="consulta-actions">
                        <button class="btn" onclick="consultarPaises()">
                            <i class="fas fa-search"></i> Executar Consulta
                        </button>
                        <button class="btn-outline" onclick="limparFiltros()">
                            <i class="fas fa-eraser"></i> Limpar Filtros
                        </button>
                        <button class="btn-outline" onclick="exportarConsulta('csv')">
                            <i class="fas fa-file-csv"></i> Exportar CSV
                        </button>
                        <button class="btn-outline" onclick="exportarConsulta('pdf')">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                    </div>
                    
                    <div class="resultados-info" id="resultadosInfo" style="display: none;">
                        <div class="resultados-count" id="resultadosCount">
                            <i class="fas fa-info-circle"></i> <span id="resultadosTexto">0 resultados encontrados</span>
                        </div>
                        <div class="export-options">
                            <button class="export-btn" onclick="copiarResultados()">
                                <i class="fas fa-copy"></i> Copiar Resultados
                            </button>
                            <button class="export-btn" onclick="imprimirResultados()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Seção Países -->
                <div class="section" id="paises">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2><i class="fas fa-flag"></i> Gerenciar Países</h2>
                        <button class="btn" onclick="abrirModal('pais')">
                            <i class="fas fa-plus"></i> Novo País
                        </button>
                    </div>
                    
                    <div class="table-container">
                        <table id="tabelaPaises">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Bandeira</th>
                                    <th>País</th>
                                    <th>Continente</th>
                                    <th>População</th>
                                    <th>Idioma</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Países serão carregados aqui via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 20px; text-align: center; color: var(--text-secondary);" id="mensagemSemResultados">
                        <i class="fas fa-info-circle"></i> Utilize a consulta acima para filtrar os países
                    </div>
                </div>
                
                <!-- Seção Cidades -->
                <div class="section" id="cidades" style="margin-top: 30px;">
                    <h2><i class="fas fa-city"></i> Gerenciar Cidades</h2>
                    <button class="btn" onclick="abrirModal('cidade')">
                        <i class="fas fa-plus"></i> Nova Cidade
                    </button>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <input type="text" id="buscaCidade" placeholder="Buscar cidade..." 
                               style="flex: 1; padding: 15px; background: rgba(255,255,255,0.05); 
                                      border: 2px solid rgba(0,255,136,0.2); border-radius: 10px; 
                                      color: white; font-size: 1rem;">
                        <button class="btn-outline" onclick="buscarCidades()">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="table-container">
                        <table id="tabelaCidades">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cidade</th>
                                    <th>População</th>
                                    <th>País</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Cidades serão carregadas aqui via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div>
                <!-- Seção Estatísticas -->
                <div class="section" id="estatisticas">
                    <h2><i class="fas fa-chart-bar"></i> Estatísticas</h2>
                    <div style="margin-top: 20px;">
                        <h3 style="color: var(--accent-color); font-size: 1rem; margin-bottom: 15px;">
                            🌍 Distribuição por Continente
                        </h3>
                        <div id="graficoContinentes" style="margin-top: 15px;">
                            <!-- Gráfico será gerado aqui via JavaScript -->
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <h3 style="color: var(--accent-color); font-size: 1rem; margin-bottom: 15px;">
                            ⚡ Ações Rápidas
                        </h3>
                        <div class="actions-grid">
                            <a href="mapa.html" target="_blank" class="action-btn">
                                <i class="fas fa-map-marked-alt"></i>
                                Mapa Interativo
                            </a>
                            <button class="action-btn" onclick="exportarDados()">
                                <i class="fas fa-file-export"></i>
                                Exportar Dados
                            </button>
                            <button class="action-btn" onclick="importarPaisAPI()">
                                <i class="fas fa-download"></i>
                                Importar da API
                            </button>
                            <button class="action-btn" onclick="atualizarEstatisticas()">
                                <i class="fas fa-sync-alt"></i>
                                Atualizar Stats
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer>
            <div class="footer-features">
                <div>
                    <i class="fas fa-code" style="color: var(--accent-color);"></i>
                    Desenvolvido com PHP 8 & MySQL
                </div>
                <div>
                    <i class="fas fa-palette" style="color: var(--accent-color);"></i>
                    Design Moderno & Responsivo
                </div>
                <div>
                    <i class="fas fa-bolt" style="color: var(--accent-color);"></i>
                    Performance Otimizada
                </div>
            </div>
            <p style="margin: 0; font-size: 0.9rem;">
                &copy; 2024 CRUD Mundo - Sistema de Gerenciamento Geográfico. 
                <span style="color: var(--accent-color);">Versão 2.0</span>
            </p>
        </footer>
    </div>
    
    <!-- Modal para País -->
    <div id="modalPais" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('pais')">&times;</button>
            <h2 style="color: var(--accent-color); margin-top: 0; margin-bottom: 20px;">
                <i class="fas fa-flag"></i> <span id="modalPaisTitulo">Novo País</span>
            </h2>
            <form id="formPais">
                <input type="hidden" id="paisId">
                
                <div class="form-group">
                    <label for="paisNome">Nome do País *</label>
                    <input type="text" id="paisNome" required>
                </div>
                
                <div class="form-group">
                    <label for="paisContinente">Continente *</label>
                    <select id="paisContinente" required>
                        <option value="">Selecione...</option>
                        <option value="África">África</option>
                        <option value="América">América</option>
                        <option value="Ásia">Ásia</option>
                        <option value="Europa">Europa</option>
                        <option value="Oceania">Oceania</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="paisPopulacao">População *</label>
                    <input type="number" id="paisPopulacao" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="paisIdioma">Idioma Principal *</label>
                    <input type="text" id="paisIdioma" required>
                </div>
                
                <div class="form-group">
                    <label for="paisCodigoISO">Código ISO (ex: BRA)</label>
                    <input type="text" id="paisCodigoISO" maxlength="3" 
                           placeholder="Código de 3 letras">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-outline" onclick="fecharModal('pais')">
                        Cancelar
                    </button>
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Salvar País
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal para Cidade -->
    <div id="modalCidade" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="fecharModal('cidade')">&times;</button>
            <h2 style="color: var(--accent-color); margin-top: 0; margin-bottom: 20px;">
                <i class="fas fa-city"></i> <span id="modalCidadeTitulo">Nova Cidade</span>
            </h2>
            <form id="formCidade">
                <input type="hidden" id="cidadeId">
                
                <div class="form-group">
                    <label for="cidadeNome">Nome da Cidade *</label>
                    <input type="text" id="cidadeNome" required>
                </div>
                
                <div class="form-group">
                    <label for="cidadePopulacao">População *</label>
                    <input type="number" id="cidadePopulacao" required min="1">
                </div>
                
                <div class="form-group">
                    <label for="cidadePais">País *</label>
                    <select id="cidadePais" required>
                        <option value="">Selecione um país...</option>
                        <!-- Países serão carregados via JavaScript -->
                    </select>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-outline" onclick="fecharModal('cidade')">
                        Cancelar
                    </button>
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Salvar Cidade
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Principal -->
    <script>
        // Configurações
        // Construir a URL da API dinamicamente a partir da URL atual para funcionar
        // quando o projeto está em um subdiretório (ex.: /prjDeep/public)
        const API_BASE_URL = (function() {
            const origin = window.location.origin;
            const basePath = window.location.pathname.replace(/\/[^\/]*$/, '');
            return `${origin}${basePath}/api/index.php`;
        })();
        
        // Sistema principal
        let crudMundo = {
            paises: [],
            cidades: [],
            paisesFiltrados: [],
            filtrosAtivos: {},
            
            // Inicializar
            async init() {
                try {
                    await this.carregarDados();
                    this.configurarEventos();
                    this.configurarFiltros();
                    this.mostrarNotificacao('Sistema carregado com sucesso!', 'success');
                } catch (error) {
                    console.error('Erro na inicialização:', error);
                    this.mostrarNotificacao('Erro ao carregar dados do sistema', 'error');
                }
            },
            
            // Configurar sistema de filtros
            configurarFiltros() {
                const filtroTipo = document.getElementById('filtroTipo');
                if (filtroTipo) {
                    filtroTipo.addEventListener('change', (e) => {
                        this.controlarVisibilidadeFiltros(e.target.value);
                    });
                }
            },
            
            // Controlar visibilidade dos campos de filtro
            controlarVisibilidadeFiltros(tipo) {
                // Esconder todos os grupos
                document.getElementById('filtroContinenteGroup').style.display = 'none';
                document.getElementById('filtroNomeGroup').style.display = 'none';
                document.getElementById('filtroPopulacaoGroup').style.display = 'none';
                document.getElementById('filtroIdiomaGroup').style.display = 'none';
                
                // Mostrar apenas o grupo correspondente
                if (tipo === 'continente') {
                    document.getElementById('filtroContinenteGroup').style.display = 'flex';
                } else if (tipo === 'nome') {
                    document.getElementById('filtroNomeGroup').style.display = 'flex';
                } else if (tipo === 'populacao') {
                    document.getElementById('filtroPopulacaoGroup').style.display = 'flex';
                } else if (tipo === 'idioma') {
                    document.getElementById('filtroIdiomaGroup').style.display = 'flex';
                }
            },
            
            // Carregar dados da API
            async carregarDados() {
                try {
                    // Carregar países
                    const respostaPaises = await fetch(`${API_BASE_URL}/paises/listar`);
                    const dadosPaises = await respostaPaises.json();
                    
                    if (dadosPaises.sucesso) {
                        this.paises = dadosPaises.dados || [];
                        this.paisesFiltrados = [...this.paises];
                        this.renderizarPaises(this.paisesFiltrados);
                        this.atualizarContagemResultados(this.paisesFiltrados.length);
                    } else {
                        throw new Error(dadosPaises.mensagem || 'Erro ao carregar países');
                    }
                    
                    // Carregar cidades
                    const respostaCidades = await fetch(`${API_BASE_URL}/cidades/listar`);
                    const dadosCidades = await respostaCidades.json();
                    
                    if (dadosCidades.sucesso) {
                        this.cidades = dadosCidades.dados || [];
                        this.renderizarCidades(this.cidades);
                    } else {
                        throw new Error(dadosCidades.mensagem || 'Erro ao carregar cidades');
                    }
                    
                    // Carregar estatísticas
                    await this.carregarEstatisticas();
                    
                    // Popular select de países
                    this.popularSelectPaises();
                    
                } catch (error) {
                    console.error('Erro ao carregar dados:', error);
                    this.mostrarNotificacao('Erro ao carregar dados do servidor: ' + error.message, 'error');
                    throw error;
                }
            },
            
            // Carregar estatísticas
            async carregarEstatisticas() {
                try {
                    const resposta = await fetch(`${API_BASE_URL}/paises/estatisticas`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.atualizarEstatisticas(dados.dados);
                    }
                } catch (error) {
                    console.error('Erro ao carregar estatísticas:', error);
                }
            },
            
            // Atualizar estatísticas na interface
            atualizarEstatisticas(dados) {
                // Total de países
                document.getElementById('totalPaises').textContent = this.formatarNumero(dados.total_paises || 0);
                
                // Total de continentes
                document.getElementById('totalContinentes').textContent = dados.total_continentes || 0;
                
                // Calcular população total
                let populacaoTotal = 0;
                this.paises.forEach(pais => {
                    populacaoTotal += parseInt(pais.populacao) || 0;
                });
                document.getElementById('populacaoTotal').textContent = this.formatarNumero(populacaoTotal);
                
                // Gerar gráfico de continentes
                this.gerarGraficoContinentes(dados.por_continente || []);
            },
            
            // Consultar países com filtros
            async consultarPaises() {
                const tipo = document.getElementById('filtroTipo').value;
                const ordenacao = document.getElementById('ordenarPor').value;
                
                let paisesFiltrados = [...this.paises];
                
                // Aplicar filtros
                if (tipo === 'continente') {
                    const continente = document.getElementById('filtroContinente').value;
                    if (continente) {
                        paisesFiltrados = paisesFiltrados.filter(pais => 
                            pais.continente === continente
                        );
                        this.filtrosAtivos = { tipo: 'continente', valor: continente };
                    }
                } else if (tipo === 'nome') {
                    const nome = document.getElementById('filtroNome').value.toLowerCase();
                    if (nome) {
                        paisesFiltrados = paisesFiltrados.filter(pais => 
                            pais.nome.toLowerCase().includes(nome)
                        );
                        this.filtrosAtivos = { tipo: 'nome', valor: nome };
                    }
                } else if (tipo === 'populacao') {
                    const min = parseInt(document.getElementById('filtroPopulacaoMin').value) || 0;
                    const max = parseInt(document.getElementById('filtroPopulacaoMax').value) || Number.MAX_SAFE_INTEGER;
                    
                    if (min > 0 || max < Number.MAX_SAFE_INTEGER) {
                        paisesFiltrados = paisesFiltrados.filter(pais => {
                            const populacao = parseInt(pais.populacao);
                            return populacao >= min && populacao <= max;
                        });
                        this.filtrosAtivos = { tipo: 'populacao', min, max };
                    }
                } else if (tipo === 'idioma') {
                    const idioma = document.getElementById('filtroIdioma').value.toLowerCase();
                    if (idioma) {
                        paisesFiltrados = paisesFiltrados.filter(pais => 
                            pais.idioma.toLowerCase().includes(idioma)
                        );
                        this.filtrosAtivos = { tipo: 'idioma', valor: idioma };
                    }
                } else {
                    // "todos" - não aplica filtro
                    this.filtrosAtivos = {};
                }
                
                // Aplicar ordenação
                paisesFiltrados = this.ordenarPaises(paisesFiltrados, ordenacao);
                
                // Atualizar lista
                this.paisesFiltrados = paisesFiltrados;
                this.renderizarPaises(paisesFiltrados);
                this.atualizarContagemResultados(paisesFiltrados.length);
                
                // Mostrar notificação
                this.mostrarNotificacao(`Consulta realizada: ${paisesFiltrados.length} países encontrados`, 'success');
            },
            
            // Ordenar países
            ordenarPaises(paises, ordenacao) {
                return [...paises].sort((a, b) => {
                    switch(ordenacao) {
                        case 'nome':
                            return a.nome.localeCompare(b.nome);
                        case 'nome_desc':
                            return b.nome.localeCompare(a.nome);
                        case 'populacao':
                            return parseInt(a.populacao) - parseInt(b.populacao);
                        case 'populacao_desc':
                            return parseInt(b.populacao) - parseInt(a.populacao);
                        case 'continente':
                            return a.continente.localeCompare(b.continente);
                        default:
                            return 0;
                    }
                });
            },
            
            // Limpar filtros
            limparFiltros() {
                document.getElementById('filtroTipo').value = 'todos';
                document.getElementById('filtroContinente').value = '';
                document.getElementById('filtroNome').value = '';
                document.getElementById('filtroPopulacaoMin').value = '';
                document.getElementById('filtroPopulacaoMax').value = '';
                document.getElementById('filtroIdioma').value = '';
                document.getElementById('ordenarPor').value = 'nome';
                
                this.controlarVisibilidadeFiltros('todos');
                this.filtrosAtivos = {};
                
                // Mostrar todos os países
                this.paisesFiltrados = [...this.paises];
                this.renderizarPaises(this.paisesFiltrados);
                this.atualizarContagemResultados(this.paisesFiltrados.length);
                
                this.mostrarNotificacao('Filtros limpos com sucesso', 'success');
            },
            
            // Atualizar contagem de resultados
            atualizarContagemResultados(total) {
                const resultadosInfo = document.getElementById('resultadosInfo');
                const resultadosTexto = document.getElementById('resultadosTexto');
                const mensagemSemResultados = document.getElementById('mensagemSemResultados');
                
                if (total > 0) {
                    resultadosInfo.style.display = 'flex';
                    resultadosTexto.textContent = `${this.formatarNumero(total)} resultados encontrados`;
                    mensagemSemResultados.style.display = 'none';
                } else {
                    resultadosInfo.style.display = 'none';
                    mensagemSemResultados.innerHTML = `
                        <i class="fas fa-exclamation-circle"></i> 
                        Nenhum país encontrado com os filtros aplicados
                    `;
                    mensagemSemResultados.style.display = 'block';
                }
            },
            
            // Exportar consulta
            exportarConsulta(formato) {
                if (this.paisesFiltrados.length === 0) {
                    this.mostrarNotificacao('Não há dados para exportar', 'error');
                    return;
                }
                
                if (formato === 'csv') {
                    this.exportarCSV();
                } else if (formato === 'pdf') {
                    this.exportarPDF();
                }
            },
            
            // Exportar para CSV
            exportarCSV() {
                const cabecalhos = ['ID', 'País', 'Continente', 'População', 'Idioma', 'Código ISO', 'Capital'];
                const linhas = this.paisesFiltrados.map(pais => [
                    pais.id_pais,
                    pais.nome,
                    pais.continente,
                    this.formatarNumero(pais.populacao),
                    pais.idioma,
                    pais.codigo_iso || '',
                    pais.capital || ''
                ]);
                
                let csv = cabecalhos.join(';') + '\n';
                linhas.forEach(linha => {
                    csv += linha.join(';') + '\n';
                });
                
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `consulta-paises-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                this.mostrarNotificacao('Consulta exportada para CSV com sucesso', 'success');
            },
            
            // Exportar para PDF (simulado)
            exportarPDF() {
                // Em um sistema real, usaria uma biblioteca como jsPDF
                // Aqui vamos simular e oferecer um JSON
                const dados = {
                    titulo: 'Relatório de Países',
                    data: new Date().toLocaleString('pt-BR'),
                    total: this.paisesFiltrados.length,
                    filtros: this.filtrosAtivos,
                    paises: this.paisesFiltrados.map(pais => ({
                        nome: pais.nome,
                        continente: pais.continente,
                        populacao: pais.populacao,
                        idioma: pais.idioma,
                        codigo_iso: pais.codigo_iso
                    }))
                };
                
                const blob = new Blob([JSON.stringify(dados, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `relatorio-paises-${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                this.mostrarNotificacao('Dados exportados para JSON (simulação de PDF)', 'success');
            },
            
            // Copiar resultados para área de transferência
            async copiarResultados() {
                if (this.paisesFiltrados.length === 0) {
                    this.mostrarNotificacao('Não há dados para copiar', 'error');
                    return;
                }
                
                const texto = this.paisesFiltrados.map(pais => 
                    `${pais.nome} - ${pais.continente} - ${this.formatarNumero(pais.populacao)} hab. - ${pais.idioma}`
                ).join('\n');
                
                try {
                    await navigator.clipboard.writeText(texto);
                    this.mostrarNotificacao('Resultados copiados para área de transferência', 'success');
                } catch (error) {
                    this.mostrarNotificacao('Erro ao copiar resultados', 'error');
                }
            },
            
            // Imprimir resultados
            imprimirResultados() {
                if (this.paisesFiltrados.length === 0) {
                    this.mostrarNotificacao('Não há dados para imprimir', 'error');
                    return;
                }
                
                const janela = window.open('', '_blank');
                janela.document.write(`
                    <html>
                    <head>
                        <title>Relatório de Países - CRUD Mundo</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            h1 { color: #333; }
                            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                            th { background: #f0f0f0; padding: 10px; text-align: left; }
                            td { padding: 10px; border-bottom: 1px solid #ddd; }
                            .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
                            .total { font-weight: bold; color: #00cc6a; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h1>Relatório de Países</h1>
                            <div>Gerado em: ${new Date().toLocaleString('pt-BR')}</div>
                        </div>
                        <div class="total">Total: ${this.paisesFiltrados.length} países</div>
                        <table>
                            <tr>
                                <th>País</th>
                                <th>Continente</th>
                                <th>População</th>
                                <th>Idioma</th>
                                <th>Código ISO</th>
                            </tr>
                            ${this.paisesFiltrados.map(pais => `
                                <tr>
                                    <td>${pais.nome}</td>
                                    <td>${pais.continente}</td>
                                    <td>${this.formatarNumero(pais.populacao)}</td>
                                    <td>${pais.idioma}</td>
                                    <td>${pais.codigo_iso || '-'}</td>
                                </tr>
                            `).join('')}
                        </table>
                    </body>
                    </html>
                `);
                janela.document.close();
                janela.focus();
                setTimeout(() => {
                    janela.print();
                    janela.close();
                }, 500);
            },
            
            // Renderizar países na tabela
            renderizarPaises(paises) {
                const tbody = document.querySelector('#tabelaPaises tbody');
                if (!tbody) return;
                
                tbody.innerHTML = paises.map(pais => `
                    <tr class="fade-in">
                        <td>${pais.id_pais}</td>
                        <td>
                            ${pais.bandeira_url ? 
                                `<img src="${pais.bandeira_url}" alt="${pais.nome}" 
                                      style="width: 30px; height: auto; border-radius: 3px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">` : 
                                `<i class="fas fa-flag" style="color: var(--accent-color);"></i>`
                            }
                        </td>
                        <td>
                            <strong>${pais.nome}</strong>
                            ${pais.codigo_iso ? 
                                `<span class="badge badge-info" style="margin-left: 8px;">${pais.codigo_iso}</span>` : 
                                ''}
                        </td>
                        <td>
                            <span class="badge" style="background: ${this.getCorContinente(pais.continente)}; color: #000;">
                                ${pais.continente}
                            </span>
                        </td>
                        <td>${this.formatarNumero(pais.populacao)}</td>
                        <td>${pais.idioma}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn-outline btn-sm" onclick="crudMundo.editarPais(${pais.id_pais})" 
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-outline btn-sm" onclick="crudMundo.excluirPais(${pais.id_pais})" 
                                        title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                                ${pais.codigo_iso ? `
                                <button class="btn-outline btn-sm" onclick="crudMundo.verInfoAPI(${pais.id_pais})" 
                                        title="Informações da API">
                                    <i class="fas fa-external-link-alt"></i>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `).join('');
            },
            
            // Cor do badge do continente
            getCorContinente(continente) {
                const cores = {
                    'África': '#FFD700',
                    'América': '#FF6B6B',
                    'Ásia': '#4ECDC4',
                    'Europa': '#95E1D3',
                    'Oceania': '#FF9F1C'
                };
                return cores[continente] || 'rgba(0, 255, 136, 0.5)';
            },
            
            // Renderizar cidades na tabela
            renderizarCidades(cidades) {
                const tbody = document.querySelector('#tabelaCidades tbody');
                if (!tbody) return;
                
                tbody.innerHTML = cidades.map(cidade => `
                    <tr class="fade-in">
                        <td>${cidade.id_cidade}</td>
                        <td><strong>${cidade.nome}</strong></td>
                        <td>${this.formatarNumero(cidade.populacao)}</td>
                        <td>${cidade.pais_nome || 'N/A'}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn-outline btn-sm" onclick="crudMundo.editarCidade(${cidade.id_cidade})" 
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-outline btn-sm" onclick="crudMundo.excluirCidade(${cidade.id_cidade})" 
                                        title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn-outline btn-sm" onclick="crudMundo.verClimaAPI(${cidade.id_cidade})" 
                                        title="Clima">
                                    <i class="fas fa-cloud-sun"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            },
            
            // Popular select de países
            popularSelectPaises() {
                const select = document.getElementById('cidadePais');
                if (!select) return;
                
                select.innerHTML = '<option value="">Selecione um país...</option>' +
                    this.paises.map(pais => 
                        `<option value="${pais.id_pais}">${pais.nome}</option>`
                    ).join('');
            },
            
            // Gerar gráfico de continentes
            gerarGraficoContinentes(dados) {
                const container = document.getElementById('graficoContinentes');
                if (!container || !dados.length) {
                    container.innerHTML = '<p style="color: var(--text-tertiary); text-align: center;">Nenhum dado disponível</p>';
                    return;
                }
                
                container.innerHTML = dados.map(item => {
                    const porcentagem = (item.total / this.paises.length * 100).toFixed(1);
                    return `
                        <div style="margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="color: var(--text-secondary); font-size: 0.9rem;">${item.continente}</span>
                                <span style="color: var(--accent-color); font-size: 0.9rem;">${item.total} (${porcentagem}%)</span>
                            </div>
                            <div style="background: rgba(0,255,136,0.1); height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="background: var(--accent-color); height: 100%; width: ${porcentagem}%; border-radius: 4px;"></div>
                            </div>
                        </div>
                    `;
                }).join('');
            },
            
            // Buscar países
            async buscarPaises() {
                const termo = document.getElementById('buscaPais').value;
                
                if (!termo.trim()) {
                    this.renderizarPaises(this.paises);
                    return;
                }
                
                try {
                    const resposta = await fetch(`${API_BASE_URL}/paises/buscar?q=${encodeURIComponent(termo)}`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.renderizarPaises(dados.dados);
                    }
                } catch (error) {
                    console.error('Erro na busca:', error);
                }
            },
            
            // Buscar cidades
            async buscarCidades() {
                const termo = document.getElementById('buscaCidade').value;
                
                if (!termo.trim()) {
                    this.renderizarCidades(this.cidades);
                    return;
                }
                
                try {
                    const resposta = await fetch(`${API_BASE_URL}/cidades/buscar?q=${encodeURIComponent(termo)}`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.renderizarCidades(dados.dados);
                    }
                } catch (error) {
                    console.error('Erro na busca:', error);
                }
            },
            
            // Editar país
            async editarPais(id) {
                try {
                    const resposta = await fetch(`${API_BASE_URL}/paises/obter/${id}`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.abrirModalPais(dados.dados);
                    } else {
                        this.mostrarNotificacao(dados.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao carregar país', 'error');
                }
            },
            
            // Excluir país
            async excluirPais(id) {
                if (!confirm('Tem certeza que deseja excluir este país? Esta ação não pode ser desfeita.')) {
                    return;
                }
                
                try {
                    const resposta = await fetch(`${API_BASE_URL}/paises/deletar/${id}`, {
                        method: 'DELETE'
                    });
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.mostrarNotificacao('País excluído com sucesso!', 'success');
                        await this.carregarDados();
                    } else {
                        this.mostrarNotificacao(dados.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao excluir país', 'error');
                }
            },
            
            // Editar cidade
            async editarCidade(id) {
                try {
                    const resposta = await fetch(`${API_BASE_URL}/cidades/obter/${id}`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.abrirModalCidade(dados.dados);
                    } else {
                        this.mostrarNotificacao(dados.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao carregar cidade', 'error');
                }
            },
            
            // Excluir cidade
            async excluirCidade(id) {
                if (!confirm('Tem certeza que deseja excluir esta cidade? Esta ação não pode ser desfeita.')) {
                    return;
                }
                
                try {
                    const resposta = await fetch(`${API_BASE_URL}/cidades/deletar/${id}`, {
                        method: 'DELETE'
                    });
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.mostrarNotificacao('Cidade excluída com sucesso!', 'success');
                        await this.carregarDados();
                    } else {
                        this.mostrarNotificacao(dados.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao excluir cidade', 'error');
                }
            },
            
            // Abrir modal de país
            abrirModalPais(pais = null) {
                const modal = document.getElementById('modalPais');
                const titulo = document.getElementById('modalPaisTitulo');
                
                if (pais) {
                    titulo.textContent = 'Editar País';
                    document.getElementById('paisId').value = pais.id_pais;
                    document.getElementById('paisNome').value = pais.nome;
                    document.getElementById('paisContinente').value = pais.continente;
                    document.getElementById('paisPopulacao').value = pais.populacao;
                    document.getElementById('paisIdioma').value = pais.idioma;
                    document.getElementById('paisCodigoISO').value = pais.codigo_iso || '';
                } else {
                    titulo.textContent = 'Novo País';
                    document.getElementById('formPais').reset();
                    document.getElementById('paisId').value = '';
                }
                
                modal.style.display = 'flex';
            },
            
            // Abrir modal de cidade
            abrirModalCidade(cidade = null) {
                const modal = document.getElementById('modalCidade');
                const titulo = document.getElementById('modalCidadeTitulo');
                
                this.popularSelectPaises();
                
                if (cidade) {
                    titulo.textContent = 'Editar Cidade';
                    document.getElementById('cidadeId').value = cidade.id_cidade;
                    document.getElementById('cidadeNome').value = cidade.nome;
                    document.getElementById('cidadePopulacao').value = cidade.populacao;
                    document.getElementById('cidadePais').value = cidade.id_pais;
                } else {
                    titulo.textContent = 'Nova Cidade';
                    document.getElementById('formCidade').reset();
                    document.getElementById('cidadeId').value = '';
                }
                
                modal.style.display = 'flex';
            },
            
            // Fechar modal
            fecharModal(tipo) {
                const modal = document.getElementById(`modal${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
                if (modal) {
                    modal.style.display = 'none';
                }
            },
            
            // Configurar eventos
            configurarEventos() {
                // Formulário de país
                document.getElementById('formPais').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.salvarPais();
                });
                
                // Formulário de cidade
                document.getElementById('formCidade').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.salvarCidade();
                });
                
                // Busca em tempo real
                document.getElementById('buscaPais').addEventListener('input', 
                    this.debounce(() => this.buscarPaises(), 300));
                
                document.getElementById('buscaCidade').addEventListener('input', 
                    this.debounce(() => this.buscarCidades(), 300));
            },
            
            // Salvar país
            async salvarPais() {
                const id = document.getElementById('paisId').value;
                const dados = {
                    nome: document.getElementById('paisNome').value,
                    continente: document.getElementById('paisContinente').value,
                    populacao: document.getElementById('paisPopulacao').value,
                    idioma: document.getElementById('paisIdioma').value,
                    codigo_iso: document.getElementById('paisCodigoISO').value || null
                };
                
                try {
                    let url = `${API_BASE_URL}/paises/criar`;
                    let metodo = 'POST';
                    
                    if (id) {
                        url = `${API_BASE_URL}/paises/atualizar/${id}`;
                        metodo = 'PUT';
                    }
                    
                    const resposta = await fetch(url, {
                        method: metodo,
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(dados)
                    });
                    
                    const resultado = await resposta.json();
                    
                    if (resultado.sucesso) {
                        this.mostrarNotificacao(resultado.mensagem, 'success');
                        this.fecharModal('pais');
                        await this.carregarDados();
                    } else {
                        this.mostrarNotificacao(resultado.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao salvar país', 'error');
                }
            },
            
            // Salvar cidade
            async salvarCidade() {
                const id = document.getElementById('cidadeId').value;
                const dados = {
                    nome: document.getElementById('cidadeNome').value,
                    populacao: document.getElementById('cidadePopulacao').value,
                    id_pais: document.getElementById('cidadePais').value
                };
                
                try {
                    let url = `${API_BASE_URL}/cidades/criar`;
                    let metodo = 'POST';
                    
                    if (id) {
                        url = `${API_BASE_URL}/cidades/atualizar/${id}`;
                        metodo = 'PUT';
                    }
                    
                    const resposta = await fetch(url, {
                        method: metodo,
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(dados)
                    });
                    
                    const resultado = await resposta.json();
                    
                    if (resultado.sucesso) {
                        this.mostrarNotificacao(resultado.mensagem, 'success');
                        this.fecharModal('cidade');
                        await this.carregarDados();
                    } else {
                        this.mostrarNotificacao(resultado.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao salvar cidade', 'error');
                }
            },
            
            // Importar país da API REST Countries
            async importarPaisAPI() {
                const codigo = prompt('Digite o código do país (ex: BRA, USA, FRA, DEU, JPN):');
                
                if (!codigo) return;
                
                try {
                    const resposta = await fetch(`${API_BASE_URL}/paises/importar-api?codigo=${codigo.toUpperCase()}`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.mostrarNotificacao(`✅ ${dados.mensagem}`, 'success');
                        await this.carregarDados();
                    } else {
                        this.mostrarNotificacao(`❌ ${dados.mensagem}`, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao importar país da API', 'error');
                }
            },
            
            // Ver informações da API REST Countries
            async verInfoAPI(paisId) {
                try {
                    const pais = this.paises.find(p => p.id_pais == paisId);
                    if (!pais || !pais.codigo_iso) {
                        this.mostrarNotificacao('Este país não tem código ISO cadastrado', 'error');
                        return;
                    }
                    
                    const resposta = await fetch(`${API_BASE_URL}/paises/informacoes-api?codigo=${pais.codigo_iso}`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.mostrarModalInfoAPI(dados.dados);
                    } else {
                        this.mostrarNotificacao(dados.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao obter informações da API', 'error');
                }
            },
            
            // Ver clima da cidade
            async verClimaAPI(cidadeId) {
                try {
                    const resposta = await fetch(`${API_BASE_URL}/cidades/clima/${cidadeId}`);
                    const dados = await resposta.json();
                    
                    if (dados.sucesso) {
                        this.mostrarModalClima(dados.dados);
                    } else {
                        this.mostrarNotificacao(dados.mensagem, 'error');
                    }
                } catch (error) {
                    this.mostrarNotificacao('Erro ao obter dados do clima', 'error');
                }
            },
            
            // Mostrar modal de informações da API
            mostrarModalInfoAPI(info) {
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content" style="max-width: 500px;">
                        <button class="modal-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                        <h2 style="color: var(--accent-color); margin-bottom: 20px;">
                            <i class="fas fa-globe-americas"></i> ${info.nome_oficial}
                        </h2>
                        <div style="text-align: center; margin: 20px 0;">
                            <img src="${info.bandeira}" alt="Bandeira" 
                                 style="width: 200px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.3);">
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                                <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">🌎 Capital</div>
                                <div style="font-weight: bold; font-size: 1.1rem;">${info.capital}</div>
                            </div>
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                                <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">👥 População</div>
                                <div style="font-weight: bold; font-size: 1.1rem;">${this.formatarNumero(info.populacao)}</div>
                            </div>
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                                <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">📏 Área</div>
                                <div style="font-weight: bold; font-size: 1.1rem;">${this.formatarNumero(info.area)} km²</div>
                            </div>
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                                <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">🗣️ Idiomas</div>
                                <div style="font-weight: bold; font-size: 1.1rem;">${info.idiomas.join(', ')}</div>
                            </div>
                        </div>
                        ${info.mapa ? `
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="${info.mapa}" target="_blank" class="btn">
                                <i class="fas fa-map-marked-alt"></i> Ver no Google Maps
                            </a>
                        </div>
                        ` : ''}
                    </div>
                `;
                document.body.appendChild(modal);
                modal.style.display = 'flex';
            },
            
            // Mostrar modal de clima
            mostrarModalClima(clima) {
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content" style="max-width: 400px;">
                        <button class="modal-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                        <h2 style="color: var(--accent-color); margin-bottom: 20px;">
                            <i class="fas fa-cloud-sun"></i> Clima em ${clima.cidade}
                        </h2>
                        <div style="text-align: center; margin: 30px 0;">
                            <div style="font-size: 4rem; font-weight: bold; color: var(--accent-color); margin-bottom: 10px;">
                                ${clima.temperatura}°C
                            </div>
                            <div style="color: var(--text-secondary); font-size: 1.2rem;">${clima.condicao}</div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 30px 0;">
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                                <div style="color: var(--text-secondary); font-size: 0.9rem;">Sensação</div>
                                <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${clima.sensacao_termica}°C</div>
                            </div>
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                                <div style="color: var(--text-secondary); font-size: 0.9rem;">Mínima</div>
                                <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${clima.minima}°C</div>
                            </div>
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                                <div style="color: var(--text-secondary); font-size: 0.9rem;">Máxima</div>
                                <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${clima.maxima}°C</div>
                            </div>
                            <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                                <div style="color: var(--text-secondary); font-size: 0.9rem;">Umidade</div>
                                <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${clima.umidade}%</div>
                            </div>
                        </div>
                        <div style="color: var(--text-tertiary); text-align: center; font-size: 0.9rem; margin-top: 20px;">
                            Fonte: ${clima.fonte} | Atualizado: ${clima.atualizado}
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
                modal.style.display = 'flex';
            },
            
            // Exportar dados
            exportarDados() {
                const dados = {
                    paises: this.paises,
                    cidades: this.cidades,
                    exportado_em: new Date().toISOString(),
                    total_paises: this.paises.length,
                    total_cidades: this.cidades.length
                };
                
                const blob = new Blob([JSON.stringify(dados, null, 2)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `crud-mundo-backup-${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                this.mostrarNotificacao('Dados exportados com sucesso!', 'success');
            },
            
            // Utilitários
            formatarNumero(num) {
                return new Intl.NumberFormat('pt-BR').format(num);
            },
            
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },
            
            mostrarNotificacao(mensagem, tipo = 'success') {
                // Remover notificações existentes
                const notificacoesExistentes = document.querySelectorAll('.notification');
                notificacoesExistentes.forEach(n => n.remove());
                
                const notificacao = document.createElement('div');
                notificacao.className = `notification ${tipo === 'error' ? 'error' : ''}`;
                notificacao.innerHTML = `
                    <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${mensagem}</span>
                    <button onclick="this.parentElement.remove()" style="
                        background: none; border: none; color: inherit; 
                        cursor: pointer; font-size: 1.2rem; margin-left: 10px;">
                        &times;
                    </button>
                `;
                
                document.body.appendChild(notificacao);
                
                // Remover automaticamente após 5 segundos
                setTimeout(() => {
                    if (notificacao.parentElement) {
                        notificacao.remove();
                    }
                }, 5000);
            }
        };

        // Funções globais para uso nos botões HTML
        function abrirModal(tipo) {
            if (tipo === 'pais') {
                crudMundo.abrirModalPais();
            } else if (tipo === 'cidade') {
                crudMundo.abrirModalCidade();
            }
        }

        function fecharModal(tipo) {
            crudMundo.fecharModal(tipo);
        }

        function buscarPaises() {
            crudMundo.buscarPaises();
        }

        function buscarCidades() {
            crudMundo.buscarCidades();
        }

        function consultarPaises() {
            crudMundo.consultarPaises();
        }

        function limparFiltros() {
            crudMundo.limparFiltros();
        }

        function exportarConsulta(formato) {
            crudMundo.exportarConsulta(formato);
        }

        function copiarResultados() {
            crudMundo.copiarResultados();
        }

        function imprimirResultados() {
            crudMundo.imprimirResultados();
        }

        function importarPaisAPI() {
            crudMundo.importarPaisAPI();
        }

        function exportarDados() {
            crudMundo.exportarDados();
        }

        function atualizarEstatisticas() {
            crudMundo.carregarEstatisticas();
            crudMundo.mostrarNotificacao('Estatísticas atualizadas!', 'success');
        }

        // Inicializar o sistema quando o DOM estiver carregado
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🌍 CRUD Mundo - Sistema de Gerenciamento Geográfico v2.0');
            console.log('📊 Módulo de Consulta de Países ativado');
            crudMundo.init();
        });
    </script>
</body>
</html>