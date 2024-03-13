
// Constants
const PROGRAM = 'proc_liqu2.php';
const GLOBAL_FIELDS = "esta_form,wg_estado,regi_inic,regi_fina,regi_real,prog_reto,vari_reto,vari_orig,campana,indi_prue,cier_camp,liqu_camp,rang_cort_pedi,rang_nume_pedi,rang_codi_zona";
const UPDATE_FIELDS = 'campana,indi_prue';

// Functions
function initializeNumericInput() {
    $('#campana').attr("type","number")
}

function setData() {
    $.ajax({
        type: "POST",
        url: PROGRAM ,
        dataType:'json',
        data:{"method":'get_usua'},
        success: function(data){
            let data_dec=JSON.parse(JSON.stringify(data));
            console.log('Usuarios disponibles: ', data_dec);
            $("#wg_codi_usua").val(data_dec);
            $("#esta_form").val(0);
        },
        error: function(e){
            console.log("error");
            // alert(JSON.parse(e));
        }
      });
   
      $.ajax({
        type: "POST",
        url: PROGRAM ,
        dataType:'json',
        data:{"method":'get_camp'},
        success: function(data){
            let data_dec=JSON.parse(JSON.stringify(data));
            $("#codi_camp").val(data_dec[0].codi_camp);
        },
        error: function(e){
            console.log("error");
            // alert(JSON.parse(e));
        }
      });
} 

function ProcesoSinR(program, action, global_fields, update_fields) {
    console.log("sasa",program)
    console.log(action)
    console.log(global_fields)
    let val_field="";
    let send_data="";
    if (global_fields!='') {
        val_field=$('#'+global_fields).val();   
    }
    if(action=='Liqu') {
        val_field={'codi_camp':$('#codi_camp').val()
                  ,'cier_camp':$('#cier_camp').val()
                  ,'liqu_camp':$('#liqu_camp').val()
                  ,'rang_cort_pedi':$('#rang_cort_pedi').val()
                  ,'rang_nume_pedi':$('#rang_nume_pedi').val()
                  ,'rang_codi_zona':$('#rang_codi_zona').val()
                };
    }
    $.ajax({
        type: "POST",
        url: program ,
        dataType:'json',
        data:{"method":"processCode",
            "case":action,
             value:JSON.stringify(val_field)
        },
        success: function(data){
            let data_dec=JSON.parse(JSON.stringify(data));

            show_msg(data_dec)
            console.log('Usuarios disponibles: ', data_dec);;
        },
        error: function(){
            alert("Error");
        }
      });
}


function validateProperty(prop, data) {
    if (!data.hasOwnProperty(prop) || data[prop] === null) {
        console.log(`Property ${prop} does not exist or is null`);
        return false;
    }
    return true;
}

function show_msg(data) {
    const requiredProps = ['type_msg', 'title'];

    for (let prop of requiredProps) {
        if (!validateProperty(prop, data)) {
            return false;
        }
    }

    $(".modal-title").html(data.type_msg);
    $("#body_msg").html(data.title);
    $("#capa_supe").modal("show");


}

function cerrar_modal() {
    $("#capa_supe").modal("hide");
}

// Main
$(document).ready(function() {
    let ctrl_WG=0;
    let camp_glob=0;
    initializeNumericInput();
    setData();

    $('#cier_camp').on('keypress', function(event) {
        if (event.keyCode == '13' && this.value.length > 0) {
            ProcesoSinR(PROGRAM, 'cier_camp', this.id, camp_glob);
            ctrl_WG = 1;
        }
        }).on('change', function() {
        if (!ctrl_WG) {
            ProcesoSinR(PROGRAM, 'cier_camp', this.id, camp_glob);
        }
    });
        
    $('#liqu_camp').on('keypress', function(event) {
        if (event.keyCode == '13' && this.value.length > 0) {
            ProcesoSinR(PROGRAM, 'liqu_camp', this.id, camp_glob);
            ctrl_WG = 1;
        }
    }).on('change', function() {
        if (!ctrl_WG) {
            ProcesoSinR(PROGRAM, 'liqu_camp', this.id, camp_glob);
        }
    });
        
    $('#rang_cort_pedi').on('keypress', function(event) {
        if (event.keyCode == '13' && this.value.length > 0) {
            ProcesoSinR(PROGRAM, 'rang_cort_pedi', this.id, camp_glob);
            ctrl_WG = 1;
        }
    }).on('change', function() {
        if (!ctrl_WG) {
            ProcesoSinR(PROGRAM, 'rang_cort_pedi', this.id, camp_glob);
        }
    });
        
    $('#rang_nume_pedi').on('keypress', function(event) {
        if (event.keyCode == '13' && this.value.length > 0) {
            ProcesoSinR(PROGRAM, 'rang_nume_pedi', this.id, camp_glob);
            ctrl_WG = 1;
        }
    }).on('change', function() {
        if (!ctrl_WG) {
            ProcesoSinR(PROGRAM, 'rang_nume_pedi', this.id, camp_glob);
        }
    });
        
    $('#rang_codi_zona').on('keypress', function(event) {
        if (event.keyCode == '13' && this.value.length > 0) {
            ProcesoSinR(PROGRAM, 'rang_codi_zona', this.id, camp_glob);
            ctrl_WG = 1;
        }
    }).on('change', function() {
        if (!ctrl_WG) {
            ProcesoSinR(PROGRAM, 'rang_codi_zona', this.id, camp_glob);
        }
    });

    $("#LIQUIDACION").on('click', function(e) {
        e.preventDefault()
        ProcesoSinR(PROGRAM, 'Liqu', '', camp_glob);
    });
        
});
