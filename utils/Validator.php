<?php
// utils/Validator.php
// Utilitários de validação de dados
// Módulo: Validação
// Etapa: Desenvolvimento de camada de segurança

class Validator {
    /**
     * Valida email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida CPF/CNPJ (formato básico)
     */
    public static function validateDocument($document) {
        // Comentário: Verifica apenas o formato, sem cálculo de dígito
        $document = preg_replace('/[^0-9]/', '', $document);
        return (strlen($document) == 11 || strlen($document) == 14);
    }

    /**
     * Valida telefone
     */
    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 11;
    }

    /**
     * Valida data no formato YYYY-MM-DD
     */
    public static function validateDate($date) {
        $d = explode('-', $date);
        return checkdate($d[1], $d[2], $d[0]) ?? false;
    }

    /**
     * Valida número decimal
     */
    public static function validateDecimal($value) {
        return is_numeric($value) && $value >= 0;
    }

    /**
     * Sanitiza entrada de texto
     */
    public static function sanitizeText($text) {
        return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitiza entrada de email
     */
    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Valida força de senha
     */
    public static function validatePassword($password) {
        // Comentário: Mínimo 8 caracteres, com letra, número e caractere especial
        return strlen($password) >= 8 && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
}
