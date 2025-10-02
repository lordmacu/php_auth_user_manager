<?php

namespace Infrastructure\Security;

/**
 * JWT - Manejo de JSON Web Tokens
 * 
 * Genera y valida tokens JWT desde cero (sin librerías)
 */
class JWT
{
    private string $secret;
    
    public function __construct()
    {
        $this->secret = JWT_SECRET;
    }
    
    /**
     * Generar un nuevo token JWT
     */
    public function generate(array $payload): string
    {
        // Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        
        // Agregar fecha de expiración al payload
        $payload['exp'] = time() + JWT_EXPIRATION;
        
        // Codificar header y payload
        $header_encoded = $this->base64UrlEncode(json_encode($header));
        $payload_encoded = $this->base64UrlEncode(json_encode($payload));
        
        // Crear firma
        $signature = hash_hmac(
            'sha256',
            "$header_encoded.$payload_encoded",
            $this->secret,
            true
        );
        $signature_encoded = $this->base64UrlEncode($signature);
        
        // Retornar token completo
        return "$header_encoded.$payload_encoded.$signature_encoded";
    }
    
    /**
     * Validar y decodificar un token JWT
     */
    public function validate(string $token): array|false
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        [$header_encoded, $payload_encoded, $signature_encoded] = $parts;
        
        $signature = hash_hmac(
            'sha256',
            "$header_encoded.$payload_encoded",
            $this->secret,
            true
        );
        $signature_valid = $this->base64UrlEncode($signature);
        
        if ($signature_encoded !== $signature_valid) {
            return false;
        }
        
        $payload = json_decode($this->base64UrlDecode($payload_encoded), true);
        
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Codificar en Base64 URL-safe
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decodificar desde Base64 URL-safe
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}