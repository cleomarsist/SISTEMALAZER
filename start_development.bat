@echo off
REM Script para iniciar o ambiente de desenvolvimento
REM Arquivo: start_development.bat
REM Uso: Duplo-clique para executar

echo ========================================
echo  Iniciando Ambiente de Desenvolvimento
echo  ERP Fenix Magazine Personalizados
echo ========================================
echo.

REM Iniciar WAMP se não estiver rodando
echo [1/3] Verificando WAMP...
timeout /t 2 /nobreak

REM Tentar iniciar Apache (se instalado como serviço)
echo [2/3] Iniciando serviços...
net start wampapache64 >nul 2>&1
net start wampmysqld64 >nul 2>&1

timeout /t 3 /nobreak

REM Abrir navegador
echo [3/3] Abrindo navegador...
timeout /t 1 /nobreak

start http://localhost/SISTEMAIA/ControleInvestimento/test_connection.php

echo.
echo ========================================
echo  Ambiente iniciado!
echo  - Acesse: http://localhost/SISTEMAIA/ControleInvestimento/
echo  - Teste: http://localhost/SISTEMAIA/ControleInvestimento/test_connection.php
echo ========================================
echo.
pause
