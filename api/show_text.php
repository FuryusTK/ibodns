<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Texto Deslizante</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="marquee">
        <?php
        // Caminho para o arquivo temporário
        $tempFile = __DIR__ . '/temp/scrolling_text.txt';

        // Verificar se o arquivo temporário existe e ler o texto
        if (file_exists($tempFile)) {
            $scrollingText = file_get_contents($tempFile);
            // Exibir o texto dentro da tag <p>
            echo '<p>' . htmlspecialchars($scrollingText) . '</p>';
        } else {
            // Se o arquivo temporário não existir, exibir mensagem de erro
            echo '<p>Nenhum texto disponível.</p>';
        }
        ?>
    </div>
</body>
</html>
