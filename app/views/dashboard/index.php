<!--
    VIEW: DASHBOARD - PÁGINA INICIAL
    
    ETAPA 1: ARQUITETURA GERAL
    Arquivo: app/views/dashboard/index.php
    
    Esta é a página inicial da aplicação.
    Mostra:
    - Resumo do sistema
    - Links para os módulos principais
    - Status da aplicação
-->

<div class="container">
    <h1>🎯 Bem-vindo ao ERP Fênix Magazine Personalizados</h1>
    
    <!-- SEÇÃO DE INFORMAÇÕES DO SISTEMA -->
    <div style="background: #f1f3f5; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <h2>📊 Informações do Sistema</h2>
        
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td><strong>Nome da Empresa:</strong></td>
                <td><?php echo COMPANY_NAME; ?></td>
            </tr>
            <tr>
                <td><strong>Lema:</strong></td>
                <td><?php echo COMPANY_MOTTO; ?></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span style="background: #28a745; color: white; padding: 5px 10px; border-radius: 3px; display: inline-block;">
                        ✓ <?php echo isset($sistema_status) ? $sistema_status : 'OK'; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Versão:</strong></td>
                <td><?php echo isset($versao) ? $versao : '1.0'; ?></td>
            </tr>
            <tr>
                <td><strong>Ambiente:</strong></td>
                <td><?php echo IS_development ? '🛠️ Desenvolvimento' : '🚀 Produção'; ?></td>
            </tr>
            <tr>
                <td><strong>Horário do Servidor:</strong></td>
                <td><?php echo date('d/m/Y H:i:s'); ?></td>
            </tr>
            <tr>
                <td><strong>Timezone:</strong></td>
                <td><?php echo date_default_timezone_get(); ?></td>
            </tr>
        </table>
    </div>
    
    <!-- SEÇÃO DE STATUS DE AUTENTICAÇÃO -->
    <div style="background: #e7f3ff; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196F3;">
        <h2>🔐 Autenticação</h2>
        
        <?php if (Session::isAuthenticated()): ?>
            <p>
                <strong>✓ Usuário Autenticado</strong><br>
                ID: <?php echo Session::getUserId(); ?><br>
                Nome: <?php echo htmlspecialchars(Session::getUserName()); ?><br>
                Email: <?php echo htmlspecialchars(Session::get('user_email', 'N/A')); ?><br>
                Última atividade: <?php echo Session::get('last_activity', 'N/A'); ?>
            </p>
        <?php else: ?>
            <p>
                <strong>⚠️ Não Autenticado</strong><br>
                <a href="<?php echo WEB_ROOT; ?>/login">Clique aqui para fazer login</a>
            </p>
        <?php endif; ?>
    </div>
    
    <!-- SEÇÃO DE MÓDULOS -->
    <div style="margin: 30px 0;">
        <h2>📦 Módulos Disponíveis</h2>
        
        <p style="color: #666; margin-bottom: 20px;">
            Abaixo estão os módulos principais do sistema. Clique para acessar:
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            
            <!-- MÓDULO: CLIENTES -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">👥</div>
                <h3 style="margin: 10px 0;">Clientes & Fornecedores</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Gerenciar clientes, fornecedores e credito</p>
                <a href="<?php echo isset($modulos_disponiveis['Clientes']) ? WEB_ROOT . $modulos_disponiveis['Clientes'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
            
            <!-- MÓDULO: MATERIAIS -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">📋</div>
                <h3 style="margin: 10px 0;">Materiais</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Chapas, insumos e controle de estoque</p>
                <a href="<?php echo isset($modulos_disponiveis['Materiais']) ? WEB_ROOT . $modulos_disponiveis['Materiais'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
            
            <!-- MÓDULO: CUSTOS -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">💰</div>
                <h3 style="margin: 10px 0;">Custos</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Custos fixos, variáveis e operacionais</p>
                <a href="<?php echo isset($modulos_disponiveis['Custos']) ? WEB_ROOT . $modulos_disponiveis['Custos'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
            
            <!-- MÓDULO: SIMULADOR -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">⚙️</div>
                <h3 style="margin: 10px 0;">Simulador</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Simule cortes a laser com cálculos automáticos</p>
                <a href="<?php echo isset($modulos_disponiveis['Simulador']) ? WEB_ROOT . $modulos_disponiveis['Simulador'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
            
            <!-- MÓDULO: PRODUTOS -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
                <h3 style="margin: 10px 0;">Produtos</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Catálogo de produtos e kits</p>
                <a href="<?php echo isset($modulos_disponiveis['Produtos']) ? WEB_ROOT . $modulos_disponiveis['Produtos'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
            
            <!-- MÓDULO: ORÇAMENTOS -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">📄</div>
                <h3 style="margin: 10px 0;">Orçamentos</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Crie e gerencie orçamentos para clientes</p>
                <a href="<?php echo isset($modulos_disponiveis['Orçamentos']) ? WEB_ROOT . $modulos_disponiveis['Orçamentos'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
            
            <!-- MÓDULO: PEDIDOS -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">🛒</div>
                <h3 style="margin: 10px 0;">Pedidos</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Gerenciar pedidos e status de produção</p>
                <a href="<?php echo isset($modulos_disponiveis['Pedidos']) ? WEB_ROOT . $modulos_disponiveis['Pedidos'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
            
            <!-- MÓDULO: FINANCEIRO -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.boxShadow='0 5px 20px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='none'">
                <div style="font-size: 40px; margin-bottom: 10px;">💳</div>
                <h3 style="margin: 10px 0;">Financeiro</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 15px;">Contas a receber, pagar e fluxo de caixa</p>
                <a href="<?php echo isset($modulos_disponiveis['Financeiro']) ? WEB_ROOT . $modulos_disponiveis['Financeiro'] : '#'; ?>" style="background: #667eea; color: white; padding: 10px 20px; border-radius: 3px; display: inline-block; text-decoration: none;">
                    Acessar →
                </a>
            </div>
        </div>
    </div>
    
    <!-- SEÇÃO DE PRÓXIMOS PASSOS -->
    <div style="background: #fff3cd; padding: 20px; border-radius: 5px; margin: 30px 0; border-left: 4px solid #ffc107;">
        <h2>🚀 Próximos Passos</h2>
        
        <ol style="margin-left: 20px; line-height: 1.8;">
            <li><strong>ETAPA 2:</strong> Banco de Dados - Criar todas as tabelas do sistema</li>
            <li><strong>ETAPA 3:</strong> Módulo de Clientes e Fornecedores (CRUD completo)</li>
            <li><strong>ETAPA 4:</strong> Módulo de Materiais (Chapas e Insumos)</li>
            <li><strong>ETAPA 5:</strong> Módulo de Custos</li>
            <li><strong>ETAPA 6:</strong> Simulador de Peças (módulo central)</li>
            <li><strong>ETAPA 7:</strong> Gerenciamento de Produtos</li>
            <li><strong>ETAPA 8:</strong> Módulo de Orçamentos</li>
            <li><strong>ETAPA 9:</strong> Módulo de Pedidos</li>
            <li><strong>ETAPA 10:</strong> Módulo Financeiro</li>
            <li><strong>ETAPA 11:</strong> Dashboard e Auditoria</li>
            <li><strong>ETAPA 12:</strong> Segurança Avançada</li>
        </ol>
    </div>
    
    <!-- DOCUMENTAÇÃO -->
    <div style="background: #e8f5e9; padding: 20px; border-radius: 5px; margin: 30px 0; border-left: 4px solid #4caf50;">
        <h2>📚 Documentação</h2>
        
        <p>
            Acesse a documentação completa da ETAPA 1 em: <code>ETAPA1_ARQUITETURA.md</code>
        </p>
        
        <p style="margin-top: 15px; color: #666; font-size: 14px;">
            Nesta documentação você encontrará:
        </p>
        
        <ul style="margin-left: 20px; color: #666;">
            <li>Explicação completa da estrutura de pastas</li>
            <li>Como o padrão MVC funciona</li>
            <li>Fluxo completo de uma requisição</li>
            <li>Como configurar o sistema</li>
            <li>Medidas de segurança implementadas</li>
            <li>Exemplos de código para cada camada (Model, Controller, View)</li>
        </ul>
    </div>
    
</div>
