<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Busca</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: url('https://i.postimg.cc/SKZkjjZv/Inserir-um-t-tulo-16.png') no-repeat center center fixed;
            background-size: cover;
            background-color: #e0f7fa;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
            box-sizing: border-box;
            position: relative;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px; /* Espaço para o botão */
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            position: relative;
            padding-left: 30px; /* Espaço para o ícone */
        }

        th i {
            position: absolute;
            left: 10px; /* Espaço à esquerda do ícone */
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        p {
            text-align: center;
            color: #333;
        }

        .payment-button {
            display: inline-block;
            padding: 8px 12px; /* Ajuste o padding para diminuir o tamanho do botão */
            text-align: center;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px; /* Ajuste o tamanho da fonte */
            text-decoration: none;
            cursor: pointer;
        }

        .payment-button:hover {
            background-color: #0056b3;
        }

        .button-container {
            text-align: center;
            margin-top: 20px; /* Espaço acima do botão */
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Conecte-se ao banco de dados SQLite
        $db = new SQLite3("./api/.ansdb.db");

        // Verifique se a consulta foi enviada
        if (isset($_GET['query']) && !empty($_GET['query'])) {
            // Obtenha o valor da consulta
            $query = $_GET['query'];

            // Prepare a consulta SQL para buscar por Username ou MAC Address
            $stmt = $adb->prepare("SELECT * FROM playlist WHERE username LIKE :query OR mac_address LIKE :query");
            if ($stmt) {
                $stmt->bindValue(':query', '%' . $query . '%', SQLITE3_TEXT);

                // Execute a consulta
                $result = $stmt->execute();

                // Verifique se há resultados
                if ($result) {
                    $found = false; // Flag para verificar se há resultados
                    $paymentLink = ''; // Variável para armazenar o link de pagamento

                    echo "<h2>Resultados da busca:</h2>";
                    echo "<table>";
                    echo "<tr>
                            <th><i class='fas fa-hdd'></i> DNS ID</th>
                            <th><i class='fas fa-network-wired'></i> MAC Address</th>
                            <th><i class='fas fa-user'></i> Username</th>
                            <th><i class='fas fa-key'></i> Password</th>
                            <th><i class='fas fa-lock'></i> Parental Pin</th>
                          </tr>";

                    // Exiba os resultados
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                        $found = true;
                        $paymentLink = htmlspecialchars($row['link']); // Armazena o último link de pagamento
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['dns_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['mac_address']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['pin']) . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";

                    if (!$found) {
                        echo "<p>Nenhum resultado encontrado para a sua busca.</p>";
                    }

                    // Exibe o botão de pagamento com o último link encontrado
                    if ($paymentLink) {
                        echo "<div class='button-container'>
                                <a href='" . $paymentLink . "' target='_blank' class='payment-button'>Clique Aqui para efetuar o Pagamento</a>
                              </div>";
                    }
                } else {
                    echo "<p>Erro ao executar a consulta.</p>";
                }
            } else {
                echo "<p>Erro ao preparar a consulta.</p>";
            }
        } else {
            echo "<p>Por favor, digite um termo de busca.</p>";
        }

        // Feche a conexão com o banco de dados
        $adb->close();
        ?>
    </div>
</body>
</html>
