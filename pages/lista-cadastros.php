<?php
#include '../config/includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' and $_POST['acao'] === "excluir") {
    include_once "../config/ConnectionMySQL.php";

    $codigo = $_POST["codigo"];

    if (mysqli_query($con, "DELETE FROM documentos WHERE codigo = '{$codigo}'")) {
        echo json_encode([
            "status" => true,
            "msg" => "Registro excluído com sucesso"
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "msg" => "Error ao excluir"
        ]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' and $_GET['acao'] === 'pesquisar') {
    $search_array = [];
    $data = $_GET;

    if (!empty($data['vendedor'])) {
        $search_array[] = "(d.vendedor_nome LIKE '%{$data['vendedor']}%' OR d.vendedor_cpf LIKE '%{$data['vendedor']}%' OR d.vendedor_cnpj LIKE '%{$data['vendedor']}%')";
    }

    if (!empty($data['comprador'])) {
        $search_array[] = "(d.comprador_nome LIKE '%{$data['comprador']}%' OR d.comprador_cpf LIKE '%{$data['comprador']}%')";
    }

    if (!empty($data['localidade'])) {
        $search_array[] = "(e.nome LIKE '%{$data['localidade']}%' OR c.nome LIKE '%{$data['localidade']}%' OR b.nome LIKE '%{$data['localidade']}%')";
    }

    $search = "";

    if ($search_array) $search = "AND " . implode(' AND ', $search_array);
}

$colunas_array = [
    "d.codigo",
    "d.situacao",
    "vendedor_nome",
    "comprador_nome",
    "c.nome AS end_municipio",
    "b.nome AS end_bairro",
];

$colunas = implode(", ", $colunas_array);

$query = "SELECT {$colunas} FROM documentos d "
    . "LEFT JOIN aux_cidades c ON c.codigo = d.cidade "
    . "LEFT JOIN aux_bairros b ON b.codigo = d.bairro "
    . "LEFT JOIN aux_estados e ON e.codigo = d.estado "
    . "WHERE true {$search} LIMIT 10";
#echo $query;
$result = mysqli_query($con, $query);

?>

<div class="container py-5">
    <h2 class="text-center">Lista de cadastros</h2>

    <div class="my-2 mt-4">
        <h4>Filtros</h4>
        <form>
            <input type="hidden" name="acao" value="pesquisar">

            <div class="row g-3">

                <div class="col-sm-4">
                    <label for="vendedor">Vendedor</label>
                    <input
                            id="vendedor"
                            name="vendedor"
                            type="text"
                            class="form-control"
                            placeholder="Nome ou CPF"
                            value="<?= $_GET['vendedor'] ?>"
                    >
                </div>

                <div class="col-sm">
                    <label for="comprador">Comprador</label>
                    <input
                            id="comprador"
                            name="comprador"
                            type="text"
                            class="form-control"
                            placeholder="Nome ou CPF"
                            value="<?= $_GET['comprador'] ?>"
                    >
                </div>

                <div class="col-sm">
                    <label for="localidade">Localidade</label>
                    <input
                            id="localidade"
                            name="localidade"
                            type="text"
                            class="form-control"
                            placeholder="Cidade, Bairro ou CEP"
                            value="<?= $_GET['localidade'] ?>"
                    >
                </div>

                <div class="col-sm">
                    <label for="endereco">Endereço</label>
                    <input
                            id="endereco"
                            name="endereco"
                            type="text"
                            class="form-control"
                            placeholder="endereço"
                            value="<?= $_GET['endereco'] ?>"
                    >
                </div>


            </div>
            <div class="row">
                <div class="col-md-12 mt-2">
                    <button class="btn btn-success float-end ">Buscar</button>
                </div>
            </div>
        </form>
    </div>

    <div style="margin-bottom: 6rem">
        <table class="table my-5">
            <thead>
            <tr>
                <th class="color-gray" scope="col">Vendedor</th>
                <th class="color-gray" scope="col">Comprador</th>
                <th class="color-gray" scope="col">Município</th>
                <th class="color-gray" scope="col">Bairro</th>
                <th class="color-gray" scope="col">Situação</th>
                <th class="color-gray" scope="col" style="width: 10%">Ação</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($d = mysqli_fetch_object($result)): ?>
                <tr id="documento-<?= $d->codigo; ?>">
                    <td><?= $d->vendedor_nome ?: 'Não definido' ?></td>
                    <td><?= $d->comprador_nome ?: 'Não definido' ?></td>
                    <td><?= $d->end_municipio ?: 'Não definido' ?></td>
                    <td><?= $d->end_bairro ?: 'Não definido' ?></td>
                    <td><?= $d->situacao; ?></td>
                    <td class="text-center">
                        <a href="./editar-documento?id=<?= $d->codigo ?>" type="button" class="btn btn-light btn-sm">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <button
                                type="button"
                                class="btn btn-light btn-sm excluir-documento"
                                data-codigo="<?= $d->codigo; ?>"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function () {
        $(".excluir-documento").click(function () {
            var codigo = $(this).data("codigo");

            $.alert({
                title: "Aviso",
                content: "Tem certeza que deseja excluir?",
                buttons: {
                    sim: {
                        text: 'sim',
                        action: function () {
                            $.ajax({
                                url: "./pages/lista-cadastros.php",
                                method: "post",
                                data: {codigo, acao: "excluir"},
                                dataType: "json",
                                success: function (response) {
                                    if (response.status) {
                                        $.alert({
                                            title: 'Sucesso',
                                            content: response.msg
                                        });

                                        $(`#documento-${codigo}`).remove();
                                    } else {
                                        $.alert({
                                            title: 'Error',
                                            content: response.msg
                                        });
                                    }
                                }
                            })
                        }
                    },
                    nao: {
                        text: 'Não',
                        action: () => {
                        },
                    }
                }
            })
        })
    });
</script>
