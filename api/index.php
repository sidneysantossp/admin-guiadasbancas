<?php
// Force proper HTML content type
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline');

// Simple HTML page to test if the issue is resolved
echo '<!DOCTYPE html>';
echo '<html lang="pt-BR">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Admin Bancas do Bairro</title>';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }';
echo '.container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }';
echo 'h1 { color: #333; text-align: center; }';
echo '.success { color: #28a745; font-weight: bold; text-align: center; }';
echo '.info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; }';
echo '.btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 5px; }';
echo '</style>';
echo '</head>';
echo '<body>';
echo '<div class="container">';
echo '<h1>🎉 Admin Bancas do Bairro</h1>';
echo '<p class="success">Site funcionando corretamente!</p>';
echo '<div class="info">';
echo '<h3>Status do Sistema:</h3>';
echo '<p><strong>Servidor:</strong> Vercel PHP Runtime</p>';
echo '<p><strong>Versão PHP:</strong> ' . phpversion() . '</p>';
echo '<p><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</p>';
echo '<p><strong>Status:</strong> ✅ Online e funcionando</p>';
echo '</div>';
echo '<div style="text-align: center;">';
echo '<a href="#" onclick="location.reload()" class="btn">Recarregar Página</a>';
echo '<a href="/admin" class="btn">Acessar Painel Admin</a>';
echo '</div>';
echo '</div>';
echo '</body>';
echo '</html>';

// Force output
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}
?>
