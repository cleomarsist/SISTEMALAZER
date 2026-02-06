<?php
/**
 * CONTROLLER DE DASHBOARD
 * 
 * ETAPA 1: ARQUITETURA GERAL
 * Módulo: Dashboard
 * 
 * Responsabilidade:
 * - Página inicial/dashboard da aplicação
 * - Exibe informações gerais do sistema
 * - Ponto de entrada após login
 */

class DashboardController extends BaseController {
    
    /**
     * Página inicial do dashboard
     * 
     * Exibe:
     * - Bem-vindo ao usuário
     * - Informações do sistema
     * - Links para módulos principais
     */
    public function index() {
        // Dados para a view
        $data = [
            'page_title' => 'Dashboard - ' . COMPANY_NAME,
            'sistema_status' => 'Funcionando',
            'versao' => '1.0 - ETAPA 1',
            'modulos_disponiveis' => [
                'Clientes' => '/clientes/listar',
                'Materiais' => '/materiais/listar',
                'Custos' => '/custos/listar',
                'Simulador' => '/simulador/index',
                'Produtos' => '/produtos/listar',
                'Orçamentos' => '/orcamentos/listar',
                'Pedidos' => '/pedidos/listar',
                'Financeiro' => '/financeiro/index',
            ]
        ];
        
        // Renderiza view
        $this->render('index', $data);
    }
}
?>
