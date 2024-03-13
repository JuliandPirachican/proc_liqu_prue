
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proceso Liquidacion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type='text/javascript' src='/js_generator/jquery/jsGeneral.js'></script>
    <script type='text/javascript' src='/js_generator/jquery/jquery.numeric.js'></script>
    <script src="proc_liqu.js"></script>
    <script src="fun_publica.js"></script>
    <script src="custom.js"></script> <!-- New JS file -->
    <link rel="stylesheet" href="/css/custom.css">
</head>
<body>
<div class="container">
    <form id="proc_fact" name="proc_fact">
        <input type="hidden" id="form_deta" name="form_deta">
        <input type="hidden" id="prog_reto" name="prog_reto">
        <input type="hidden" id="vari_reto" name="vari_reto">
        <input type="hidden" id="vari_orig" name="vari_orig">
        <input type="hidden" id="regi_inic" name="regi_inic">
        <input type="hidden" id="esta_form" name="esta_form">
        <input type="hidden" id="wg_codi_usua" name="wg_codi_usua">
        <div class="alert alert-info" id="alerta" name="alerta" style="display: none; z-index: 2000;"></div>
        <div class="modal" id="capa_supe" name="capa_supe" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cerrar</h5>
                        <button type="button" class="btn-close" onclick= "cerrar_modal()"></button>
                    </div>
                    <div class="modal-body" id="capa_interna" name="capa_interna">
                        <p id="body_msg"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <div id="forma_general" style="width: 100%;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">PROCESO LIQUIDACION</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <div class="btn-group">
                                <button class="btn btn-primary" id="LIQUIDACION" name="LIQUIDACION" >
                                    <span class="material-icons">save</span>
                                </button>
                                <button class="btn btn-primary" id="boto_nuev" name="boto_nuev" onclick="ProcesoSinR('proc_liqu', 'LIMPIAR', 'esta_form', 'form_deta');">
                                    <span class="material-icons">refresh</span>
                                </button>
                                <a href="#" onclick="window.open('/manuales/formularios/proc_liqu.pdf', 'manuales', 'top=5, left=5, height=800, width=1000, location=no, resizable=yes, scrollbars=yes, menubar=no');" class="btn btn-primary">
                                    <span class="material-icons">help</span>
                                </a>
                                <button class="btn btn-primary" id="boto_sali" name="boto_sali" onclick="ProcesoSinR('proc_liqu','SALIR','esta_form',0);">
                                    <span class="material-icons">exit_to_app</span>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="codi_camp" class="form-label">Codigo Campana:</label>
                                    <input type="text" class="form-control" id="codi_camp" name="codi_camp" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="cier_camp" class="form-label">Cierre Campana:</label>
                                    <input type="text" class="form-control" id="cier_camp" name="cier_camp">
                                </div>
                                <div class="mb-3">
                                    <label for="liqu_camp" class="form-label">Liquidacion para facturar:</label>
                                    <input type="text" class="form-control" id="liqu_camp" name="liqu_camp">
                                </div>
                                <div class="mb-3">
                                    <label for="rang_cort_pedi" class="form-label">Rango de cortes de pedido:</label>
                                    <input type="text" class="form-control" id="rang_cort_pedi" name="rang_cort_pedi">
                                </div>
                                <div class="mb-3">
                                    <label for="rang_nume_pedi" class="form-label">Rango Numero De Pedido:</label>
                                    <input type="text" class="form-control" id="rang_nume_pedi" name="rang_nume_pedi">
                                </div>
                                <div class="mb-3">
                                    <label for="rang_codi_zona" class="form-label">Rango Codigo De Zona:</label>
                                    <input type="text" class="form-control" id="rang_codi_zona" name="rang_codi_zona">
                                </div>
                            </div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>
    </form>
</div>
</body>
</html>
