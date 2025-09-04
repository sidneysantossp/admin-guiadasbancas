<?php
// Set proper headers for web browser
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Admin Bancas do Bairro - Vercel Test</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }";
echo ".container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "h1 { color: #333; }";
echo ".success { color: #28a745; font-weight: bold; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>🎉 Deployment Bem-Sucedido!</h1>";
echo "<p class='success'>O Vercel está funcionando corretamente com PHP!</p>";
echo "<p>Próximo passo: Implementar a aplicação Laravel completa.</p>";
echo "<hr>";
echo "<h3>Informações do Servidor:</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Vercel Runtime:</strong> vercel-php@0.7.4</p>";
echo "<p><strong>Status:</strong> Deployment funcionando sem limite de 250MB!</p>";
echo "<a href='#' onclick='location.reload()' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Recarregar Página</a>";
echo "</div>";
echo "</body>";
echo "</html>";

// Force output
ob_end_flush();
flush();
