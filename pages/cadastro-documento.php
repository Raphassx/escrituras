<?php
include './config/includes.php';

?>

<div class="container-fluid">
    <h1 class="text-center">Cadastro de documento</h1>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8 my-4">

            <nav>
                <div class="nav nav-pills nav-fill" id="nav-tab" role="tablist">
                    <a
                            class="nav-link active"
                            id="documento-tab"
                            href="#documento">Documento</a>

                    <a
                            class="nav-link"
                            id="vendedor-tab"
                            href="#vendedor">Vendedor</a>

                    <a
                            class="nav-link"
                            id="comprador-tab"
                            href="#comprador">Comprador</a>

                    <a
                            class="nav-link"
                            id="endereco-tab"
                            href="#endereco">Endereço</a>

                    <a
                            class="nav-link"
                            id="mapa-tab"
                            href="#mapa">Mapa</a>
                </div>
            </nav>

            <div class="tab-content py-4">

                <div class="tab-pane fade show active content-pane" id="">
                </div>

            </div>


        </div>
    </div>
</div>

<script>

    function select_localidade(select, select_to, url) {
        let valor = $(`#${select}`).val();

        $.ajax({
            url: `./pages/lista/${url}.php`,
            method: 'POST',
            data: {valor},
            dataType: 'html',
            success: function (data) {
                $(`#${select_to}`).html(data);
            }
        });
    }

    function exibiContainer(input, id_container) {
        if ($(input).is(":checked")) {
            $(`#${id_container}`).show();
        } else {
            let container = $(`#${id_container}`);
            container.hide();
            container.find('input, select').val('');
        }
    }

    $(document).ready(function () {

        var doc_id = window.localStorage.getItem('doc_id');

        $.ajax({
            url: "./pages/cadastro_documento/documento.php",
            data: {doc_id},
            success: function (data) {
                $(".content-pane").html(data);
            }
        })
    });

    $(function () {

        //Adicionar mascara de telefones
        $("#form-cadastro-documento .nav-link").click(function (e) {
            e.preventDefault();
        });

        /*$("#form-cadastro-documento").submit(function (e) {
            e.preventDefault();

            vertices = poligono.getPath();

            var formData = $(this).serializeArray();
            var coordenadas = [];

            for (let i = 0; i < vertices.getLength(); i++) {
                const xy = vertices.getAt(i);

                coordenadas.push({
                    "lat": xy.lat(),
                    "lng": xy.lng()
                });
            }

            formData.push({
                name: "coordenadas",
                value: JSON.stringify(coordenadas)
            });

            $.ajax({
                url: "./pages/actions/actionCadastro_documento.php",
                method: "POST",
                data: formData, //$(this).serializeArray(),
                success: function (data) {
                    if (data === "ok") {
                        alert('Dados salvos com sucesso!');
                        window.location.reload();
                    } else {
                        alert(data);
                    }
                }
            });
        });*/
    });

</script>