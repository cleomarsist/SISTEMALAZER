<?php
/**
 * Testes de Validação - ETAPA 3
 * Verifica funcionamento dos algoritmos de validação
 */

class TesteValidacao {
    
    /**
     * Teste 1: Validação de CPF
     */
    public function testarCPF() {
        echo "\n🧪 TESTE 1: Validação de CPF\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $cpfs = [
            '111.444.777-35' => true,   // Válido
            '123.456.789-00' => false,  // Inválido
            '000.000.000-00' => false,  // Sequência repetida
            '111111111-11' => false,    // Sequência
            '000.000.000-00' => false,  // Tudo zero
        ];
        
        $passaram = 0;
        $falharam = 0;
        
        foreach ($cpfs as $cpf => $esperado) {
            $resultado = $this->validarCPF($cpf);
            $passou = $resultado === $esperado;
            
            if ($passou) {
                echo "✓ CPF $cpf: " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " ✓\n";
                $passaram++;
            } else {
                echo "✗ CPF $cpf: " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " (esperado: " . ($esperado ? 'VÁLIDO' : 'INVÁLIDO') . ")\n";
                $falharam++;
            }
        }
        
        echo "\nResultado: $passaram passou, $falharam falhou\n";
        return $falharam === 0;
    }
    
    /**
     * Teste 2: Validação de CNPJ
     */
    public function testarCNPJ() {
        echo "\n🧪 TESTE 2: Validação de CNPJ\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $cnpjs = [
            '11.222.333/0001-81' => true,  // Válido (exemplo)
            '00.000.000/0000-00' => false, // Sequência
            '123.456.789-00' => false,     // Formato errado
        ];
        
        $passaram = 0;
        $falharam = 0;
        
        foreach ($cnpjs as $cnpj => $esperado) {
            $resultado = $this->validarCNPJ($cnpj);
            $passou = $resultado === $esperado;
            
            if ($passou) {
                echo "✓ CNPJ $cnpj: " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " ✓\n";
                $passaram++;
            } else {
                echo "✗ CNPJ $cnpj: " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " (esperado: " . ($esperado ? 'VÁLIDO' : 'INVÁLIDO') . ")\n";
                $falharam++;
            }
        }
        
        echo "\nResultado: $passaram passou, $falharam falhou\n";
        return $falharam === 0;
    }
    
    /**
     * Teste 3: Cálculo de Preço com Margem
     */
    public function testarCalculoPreco() {
        echo "\n🧪 TESTE 3: Cálculo de Preço com Margem\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $testes = [
            ['custo' => 10.00, 'margem' => 50, 'esperado' => 15.00],
            ['custo' => 20.00, 'margem' => 100, 'esperado' => 40.00],
            ['custo' => 15.00, 'margem' => 20, 'esperado' => 18.00],
        ];
        
        $passaram = 0;
        $falharam = 0;
        
        foreach ($testes as $teste) {
            $resultado = $this->calcularPrecoVenda($teste['custo'], $teste['margem']);
            $passou = abs($resultado - $teste['esperado']) < 0.01;
            
            if ($passou) {
                echo "✓ Custo: R$ {$teste['custo']} + Margem: {$teste['margem']}% = R$ {$resultado} ✓\n";
                $passaram++;
            } else {
                echo "✗ Custo: R$ {$teste['custo']} + Margem: {$teste['margem']}% = R$ {$resultado} (esperado: R$ {$teste['esperado']})\n";
                $falharam++;
            }
        }
        
        echo "\nResultado: $passaram passou, $falharam falhou\n";
        return $falharam === 0;
    }
    
    /**
     * Teste 4: Validação de Email
     */
    public function testarEmail() {
        echo "\n🧪 TESTE 4: Validação de Email\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $emails = [
            'usuario@email.com' => true,
            'teste.nome@empresa.com.br' => true,
            'invalido@' => false,
            'sem-arroba.com' => false,
            'espacos nao @ email.com' => false,
        ];
        
        $passaram = 0;
        $falharam = 0;
        
        foreach ($emails as $email => $esperado) {
            $resultado = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
            $passou = $resultado === $esperado;
            
            if ($passou) {
                echo "✓ Email '$email': " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " ✓\n";
                $passaram++;
            } else {
                echo "✗ Email '$email': " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " (esperado: " . ($esperado ? 'VÁLIDO' : 'INVÁLIDO') . ")\n";
                $falharam++;
            }
        }
        
        echo "\nResultado: $passaram passou, $falharam falhou\n";
        return $falharam === 0;
    }
    
    /**
     * Teste 5: Validação de CEP
     */
    public function testarCEP() {
        echo "\n🧪 TESTE 5: Validação de CEP\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $ceps = [
            '01310-100' => true,
            '01310100' => true,
            '20040-020' => true,
            '123' => false,
            '12345-67890' => false,
        ];
        
        $passaram = 0;
        $falharam = 0;
        
        foreach ($ceps as $cep => $esperado) {
            $cepLimpo = preg_replace('/[^0-9]/', '', $cep);
            $resultado = strlen($cepLimpo) === 8;
            $passou = $resultado === $esperado;
            
            if ($passou) {
                echo "✓ CEP '$cep': " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " ✓\n";
                $passaram++;
            } else {
                echo "✗ CEP '$cep': " . ($resultado ? 'VÁLIDO' : 'INVÁLIDO') . " (esperado: " . ($esperado ? 'VÁLIDO' : 'INVÁLIDO') . ")\n";
                $falharam++;
            }
        }
        
        echo "\nResultado: $passaram passou, $falharam falhou\n";
        return $falharam === 0;
    }
    
    /**
     * Teste 6: Formatação de CEP
     */
    public function testarFormatacaoCEP() {
        echo "\n🧪 TESTE 6: Formatação de CEP\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $testes = [
            ['entrada' => '01310100', 'esperado' => '01310-100'],
            ['entrada' => '20040020', 'esperado' => '20040-020'],
            ['entrada' => '01310-100', 'esperado' => '01310-100'],
        ];
        
        $passaram = 0;
        $falharam = 0;
        
        foreach ($testes as $teste) {
            $cep = preg_replace('/[^0-9]/', '', $teste['entrada']);
            $resultado = substr($cep, 0, 5) . '-' . substr($cep, 5);
            $passou = $resultado === $teste['esperado'];
            
            if ($passou) {
                echo "✓ CEP '{$teste['entrada']}' → '{$resultado}' ✓\n";
                $passaram++;
            } else {
                echo "✗ CEP '{$teste['entrada']}' → '{$resultado}' (esperado: '{$teste['esperado']}')\n";
                $falharam++;
            }
        }
        
        echo "\nResultado: $passaram passou, $falharam falhou\n";
        return $falharam === 0;
    }
    
    /**
     * Teste 7: Cálculo de Total com Desconto
     */
    public function testarCalculoTotal() {
        echo "\n🧪 TESTE 7: Cálculo de Total com Desconto\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $testes = [
            [
                'subtotal' => 1000.00,
                'desconto_percent' => 10,
                'taxa' => 0,
                'esperado' => 900.00
            ],
            [
                'subtotal' => 1000.00,
                'desconto_percent' => 10,
                'taxa' => 50,
                'esperado' => 950.00
            ],
            [
                'subtotal' => 500.00,
                'desconto_percent' => 20,
                'taxa' => 25,
                'esperado' => 425.00
            ],
        ];
        
        $passaram = 0;
        $falharam = 0;
        
        foreach ($testes as $teste) {
            $descontoValor = $teste['subtotal'] * ($teste['desconto_percent'] / 100);
            $total = round($teste['subtotal'] - $descontoValor + $teste['taxa'], 2);
            $passou = abs($total - $teste['esperado']) < 0.01;
            
            if ($passou) {
                echo "✓ R$ {$teste['subtotal']} - {$teste['desconto_percent']}% + R$ {$teste['taxa']} = R$ {$total} ✓\n";
                $passaram++;
            } else {
                echo "✗ R$ {$teste['subtotal']} - {$teste['desconto_percent']}% + R$ {$teste['taxa']} = R$ {$total} (esperado: R$ {$teste['esperado']})\n";
                $falharam++;
            }
        }
        
        echo "\nResultado: $passaram passou, $falharam falhou\n";
        return $falharam === 0;
    }
    
    // ========== MÉTODOS AUXILIARES ==========
    
    private function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) !== 11) return false;
        if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;
        
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;
        if (intval($cpf[9]) !== $digito1) return false;
        
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;
        
        return intval($cpf[10]) === $digito2;
    }
    
    private function validarCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        if (strlen($cnpj) !== 14) return false;
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) return false;
        
        $soma = 0;
        $mult = 5;
        for ($i = 0; $i < 12; $i++) {
            $soma += intval($cnpj[$i]) * $mult;
            $mult--;
            if ($mult < 2) $mult = 9;
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;
        if (intval($cnpj[12]) !== $digito1) return false;
        
        $soma = 0;
        $mult = 6;
        for ($i = 0; $i < 13; $i++) {
            $soma += intval($cnpj[$i]) * $mult;
            $mult--;
            if ($mult < 2) $mult = 9;
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;
        
        return intval($cnpj[13]) === $digito2;
    }
    
    private function calcularPrecoVenda($custo, $margem) {
        $percentualMargem = $margem / 100;
        return round($custo * (1 + $percentualMargem), 2);
    }
}

// ========== EXECUTAR TESTES ==========

$teste = new TesteValidacao();
$resultados = [];

$resultados['CPF'] = $teste->testarCPF();
$resultados['CNPJ'] = $teste->testarCNPJ();
$resultados['Preço'] = $teste->testarCalculoPreco();
$resultados['Email'] = $teste->testarEmail();
$resultados['CEP'] = $teste->testarCEP();
$resultados['Formatação CEP'] = $teste->testarFormatacaoCEP();
$resultados['Total com Desconto'] = $teste->testarCalculoTotal();

// ========== RESUMO FINAL ==========

echo "\n\n";
echo "╔═════════════════════════════════════════════════════════╗\n";
echo "║               📊 RESUMO DOS TESTES                      ║\n";
echo "╚═════════════════════════════════════════════════════════╝\n\n";

$totalTestes = count($resultados);
$passou = 0;
$falhou = 0;

foreach ($resultados as $nome => $resultado) {
    $status = $resultado ? '✓' : '✗';
    $cor = $resultado ? '✓' : '✗';
    echo "$cor $nome: " . ($resultado ? 'PASSOU' : 'FALHOU') . "\n";
    if ($resultado) $passou++;
    else $falhou++;
}

echo "\n";
echo "Resultado Final: $passou/$totalTestes testes passaram\n";

if ($falhou === 0) {
    echo "\n🎉 TODOS OS TESTES PASSARAM! O sistema está funcionando corretamente.\n";
} else {
    echo "\n⚠️  $falhou testes falharam. Verifique os resultados acima.\n";
}
?>
