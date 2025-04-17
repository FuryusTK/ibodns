<?php
// Variáveis para armazenar os resultados da consulta
$mac_address = ''; 
$payment_link = null; 

if (isset($_POST["mac_address"])) {
    // Pega o valor digitado e converte para maiúsculas
    $mac_address = strtoupper(trim($_POST["mac_address"]));

    // Conectar ao banco de dados de links de pagamento
    $db3 = new SQLite3('./api/.payment_links.db');

    if ($db3) {
        // Consulta ao banco de dados de links de pagamento, usando UPPER() para evitar problemas com maiúsculas/minúsculas
        $stmt_payment = $db3->prepare("SELECT payment_link FROM payment_links WHERE UPPER(mac_address) = :mac_address");

        // Verifica se a consulta foi preparada corretamente
        if ($stmt_payment) {
            $stmt_payment->bindValue(':mac_address', $mac_address, SQLITE3_TEXT);
            $payment_link = $stmt_payment->execute()->fetchArray(SQLITE3_ASSOC);
        } else {
            echo "<p>Erro ao preparar a consulta para links de pagamento: " . $db3->lastErrorMsg() . "</p>";
        }
    } else {
        echo "<p>Erro ao conectar ao banco de dados de links de pagamento.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovar / Ativar</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #8d7054; /* Cor de fundo mais clara */
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            animation: fadeIn 1.5s ease-in-out;
            padding: 12px; /* Adiciona um pouco de espaço nas bordas */
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .search-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5); /* Sombra mais intensa */
            text-align: center;
            width: 100%;
            max-width: 400px; /* Ajuste o tamanho máximo para dispositivos menores */
            margin: 0 auto; /* Centraliza a caixa */
            animation: slideUp 1s ease;
        }
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .search-container h1 {
            font-size: 20px; /* Ajuste o tamanho da fonte para dispositivos menores */
            margin-bottom: 20px;
        }
        .search-container input {
            margin-bottom: 20px;
            transition: border-color 0.3s;
            animation: blinking 1.5s infinite; /* Animação de piscar */
            width: 100%; /* Faz o input ocupar toda a largura da caixa */
        }
        .search-container input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }
        .search-container button {
            background-color: #5484ae; /* Azul médio */
            color: #fff;
            transition: background-color 0.3s, transform 0.3s;
            animation: blinking 1.5s infinite; /* Animação de piscar */
            width: 100%; /* Faz o botão ocupar toda a largura da caixa */
        }
        @keyframes blinking {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
            100% {
                opacity: 1;
            }
        }
        .search-container button:hover {
            background-color: #4169E1; /* Azul um pouco mais escuro no hover */
            transform: scale(1.05);
        }
        .search-container img {
            margin-bottom: 15px;
            width: 80px; /* Reduzido para 80x80 */
            height: 80px;
        }
        .search-container p {
            color: #007bff;
            font-size: 14px;
        }
    </style>
    <script>
        window.onload = function() {
            const macAddressInput = document.querySelector('.mac_address');
            const submitButton = document.querySelector('button[type="submit"]');
            macAddressInput.focus();

            // Adiciona o : a cada dois caracteres digitados
            macAddressInput.addEventListener('input', function(event) {
                let value = macAddressInput.value.replace(/[^a-fA-F0-9]/g, ''); // Remove caracteres inválidos
                let formattedMac = '';

                // Insere ":" a cada dois caracteres
                for (let i = 0; i < value.length; i += 2) {
                    if (i > 0) {
                        formattedMac += ':';
                    }
                    formattedMac += value.substr(i, 2);
                }

                // Atualiza o valor do campo com o MAC formatado
                macAddressInput.value = formattedMac.toUpperCase();

                // Limita a entrada a 17 caracteres (16 caracteres hexadecimais + 1 para o último ':')
                if (macAddressInput.value.length > 17) {
                    macAddressInput.value = macAddressInput.value.substring(0, 17);
                }

                // Move o foco para o botão se 17 caracteres forem atingidos
                if (macAddressInput.value.length === 17) {
                    submitButton.focus();
                }
            });
        };
    </script>
</head>
<body>

<div class="search-container">
    <img src="qr.png" alt="QR Code" width="80" height="80">

    <h1>Digite seu MAC para gerar LINK de Renovação! </h1>

    <form method="post">
        <input class="form-control mac_address text-primary" name="mac_address" placeholder="Digite seu MAC aqui!" type="text" maxlength="23" required/>
        <button type="submit" class="btn btn-primary btn-block">Gerar Link de Renovação</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se um valor de MAC foi buscado
        if ($mac_address && $payment_link) {
            echo "<div class='alert alert-success mt-3'>";
            echo "<strong>MAC do Dispositivo:</strong> " . htmlspecialchars($mac_address) . "<br>";
            
            // Verifica se o link de pagamento foi encontrado no banco
            if (!empty($payment_link['payment_link'])) {
                echo "<a href='" . htmlspecialchars($payment_link['payment_link']) . "' class='btn btn-success mt-3'>Renovar seu Acesso!</a>";
            } else {
                echo "<div class='alert alert-warning mt-3'>Link de pagamento não disponível.</div>";
            }

            echo "</div>";
        } elseif ($mac_address && !$payment_link) {
            echo "<div class='alert alert-danger mt-3'>MAC não encontrado!</div>";
        }
    }
    ?>
</div>

</body>
</html>
