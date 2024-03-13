<?php 
class DatabaseManager {
    private $conector_ifx;
    private $conexion;
    private $campana;

    public function __construct() {
        require_once ("Conexion_Pdo.php");
        $usua_acti=$_SESSION["codi_usua"];
        $fech_log=date("Y-m-d");
        $stdlog=fopen(RUTA_REGISTROS."registro_chat_{$fech_log}_{$usua_acti}_log.txt",'a');  
        $servidor = $_SERVER['SERVER_NAME']; 
        $nume_serv = (int) filter_var($servidor, FILTER_SANITIZE_NUMBER_INT);  
        switch($nume_serv){
            case '2':
                $this->conexion = new conexion_pdo("Informix","DesarrolloColombia",$stdlog,false);
            break;
            default:
            $this->conexion = new conexion_pdo("Informix","ProduccionColombia",$stdlog, false);
            break;
        };
    }

    public function executeQueryQuery($query) {
        return $this->conexion->ejecutar_consulta($query);
    }

    

    public function truncateTables() {
        $tables = ["pedidos_liqu", "fac_tem2", "conc_t2_c", "conc_t2", "conc_t3", "conc_t4"];
        foreach ($tables as $table) {
            $consulta = "truncate $table;";
            $this->executeQueryQuery($consulta);
        }
    }

    public function updatePedidos() {
        $consulta = "update pedidos set indi_gene='Y' where codi_camp='{$this->campana}';";
        $this->executeQueryQuery($consulta);
    }

    public function selectPedidos() {
        $consulta = "select pedidos.rowid as fila, tab_clie.codi_terc, tab_clie.nume_iden
            from pedidos, tab_clie
            where pedidos.cons_terc = tab_clie.cons_terc
            and pedidos.codi_terc <> tab_clie.codi_terc
            union
            select pedidos.rowid as fila, tab_clie.codi_terc, tab_clie.nume_iden
            from pedidos, tab_clie
            where pedidos.cons_terc = tab_clie.cons_terc
            and pedidos.nume_iden <> tab_clie.nume_iden
            into temp x1_datos;";
        $this->executeQueryQuery($consulta);
    }

    // ... Add more methods for other parts of the code

    public function selectCampCode() {
        $query = "SELECT codi_camp FROM tab_camp WHERE today BETWEEN fech_inic AND fech_fina";
        $statement = $this->executeQueryQuery($query);
        return $statement;
    }
    public function selectliqu_auto() {
        $query = "SELECT estado FROM liqu_auto;";
        $statement = $this->executeQueryQuery($query);
        return $statement;
    }
   
    public function selecttab_zona($cort) {
        $query = "select count(codi_cort) as nume_cort from tab_zona where codi_cort ='$cort';";
        $statement = $this->executeQueryQuery($query);
        return $statement;
    }
    
    public function selectcantpedidos($nume) {
        $query = "select count(nume_pedi) as cant_nume_pedi from pedidos where nume_pedi='$nume';";
        $statement = $this->executeQueryQuery($query);
        return $statement;
    }
    
    public function selectcodi_zona($codi_zona) {
        $query = "select count(codi_zona) as cant_codi_zona from tab_zona where codi_zona='$codi_zona';";
        $statement = $this->executeQueryQuery($query);
        return $statement;
    }


    public function truncateTable($table) {
        $query = "TRUNCATE TABLE $table";
        $this->executeQueryQuery($query);
    }

    public function createIndex($index, $table, $columns) {
        $query = "CREATE INDEX $index ON $table ($columns)";
        $this->executeQueryQuery($query);
    }

    public function optimizeCode() {
        $this->truncateTable('conc_t1');
        $this->truncateTable('conc_t2');
        $this->truncateTable('conc_t2_c');
        $this->truncateTable('conc_t3');
        $this->truncateTable('conc_t4');
        $this->truncateTable('conc_t5');

        $this->truncateTable('fac_tem2');
        $this->truncateTable('deta_conc_temp');
        $this->truncateTable('deta_conc_2024');
        $this->truncateTable('resu_conc_2024');

        $this->truncateTable('vali_temp_inv_sald');
        $this->truncateTable('repo_rete');

        $this->truncateTable('sap_sald_fact');

        $this->truncateTable('temp_x1_saldc');
        $this->truncateTable('temp_x2_saldc');
        $this->truncateTable('temp_x3_saldc');

        $this->truncateTable('x1_r');

        $this->truncateTable('vpp_a0');
        $this->truncateTable('vpp_a1');
        $this->truncateTable('vpp_a');

        // Perform other optimized operations here
    }

    public function executeQueryQuery1() {
        $query = "select cons_terc, count(*) cant from fac_tem2 where indi_ofer not in ('C','P') group by 1 into temp x2_prem with no log;";
       
        $this->executeQueryQuery($query);
        $this->executeQueryQuery2();
    }
    
    public function executeQueryQuery2() {
        $query = "delete from fac_tem2 where cons_terc not in (select cons_terc from x2_prem);";
       
        $this->executeQueryQuery($query);
        $this->executeQueryQuery3();
    }
    
    public function executeQueryQuery3() {
        $query = "select cons_terc, sum(prec_vent*cant_pedi) total from fac_tem2 where codi_terc[1,2]='95' group by 1 into temp temp_conc_t2 ;";
       
        $this->executeQueryQuery($query);
        $this->executeQueryQuery4();
    }
    
    public function executeQueryQuery4() {
        $query = "update pedidos set indi_gene='M' where cons_terc in (select cons_terc from temp_conc_t2 where total<30000);";
       
        $this->executeQueryQuery($query);
        $this->executeQueryQuery5();
    }
    
    public function executeQueryQuery5() {
        $query = "delete from fac_tem2 where cons_terc in (select cons_terc from temp_conc_t2 where total<30000);";
       
        $this->executeQueryQuery($query);
    }

    public function selectCampFina() {
        $query = "SELECT camp_fina FROM camp_acti WHERE esta_acti = 'TOTA'";
       
        $this->executeQueryQuery($query);
    }

    public function inicio($codi_camp) {
        $this->campana = $codi_camp;

        $this->updateInvPrca();
        $this->updatePedidosLiqu();
        $this->updatePedidosLiquPrecVent();
        $this->createTempPediCanjMult();
        $this->createIndexesPediCanjMult();
        $this->createTempLiq1();
        $this->createIndexesLiq1();
        $this->createTempFacTem2();
        $this->updateParaCond();
        $this->createTempLiq3();
        $this->createIndexesLiq3();
        $this->insertIntoFacTem2();
    }

    private function updateInvPrca() {
        $consulta = "UPDATE inv_prca SET indi_desc='N'
                     WHERE codi_camp='{$this->campana}' AND porc_desc=0 AND indi_desc='S'
                     AND inv_prca.codi_pais='COL' AND inv_prca.esta_prod='S';";
    
        $this->executeQueryQuery($consulta);
    }

    private function updatePedidosLiqu() {
        $consulta = "UPDATE pedidos_liqu SET codi_prod=(SELECT codi_prod FROM inv_prca
                     WHERE pedidos_liqu.codi_vent=inv_prca.codi_vent AND inv_prca.codi_camp='{$this->campana}'
                     AND inv_prca.codi_pais='COL' AND inv_prca.esta_prod='S')
                     WHERE codi_camp='{$this->campana}'
                     AND codi_vent IN (SELECT codi_vent FROM inv_prca WHERE codi_camp='{$this->campana}'
                     AND inv_prca.codi_pais='COL' AND inv_prca.esta_prod='S');";
    
        $this->executeQueryQuery($consulta);
    }

    private function updatePedidosLiquPrecVent() {
        $consulta = "UPDATE pedidos_liqu SET prec_vent = (SELECT prec_vent FROM inv_prca
                     WHERE pedidos_liqu.codi_vent = inv_prca.codi_vent
                     AND inv_prca.codi_camp=pedidos_liqu.codi_camp
                     AND pedidos_liqu.codi_camp = '{$this->campana}'
                     AND inv_prca.codi_pais='COL' AND inv_prca.esta_prod='S')
                     WHERE codi_vent IN (SELECT codi_vent FROM inv_prca
                     WHERE codi_camp='{$this->campana}'
                     AND inv_prca.codi_pais='COL' AND inv_prca.esta_prod='S')
                     AND codi_camp='{$this->campana}';";
    
        $this->executeQueryQuery($consulta);
    }

    private function createTempPediCanjMult() {
        $consulta = "SELECT MAX(fech_pedi) AS fech_pedi, MAX(nume_pedi) AS nume_pedi, MAX(codi_camp) AS codi_camp,
                     MAX(fech_inic) AS fech_inic, MAX(fech_fina) AS fech_fina, nume_iden, codi_terc, cons_terc,
                     MAX(acti_usua) AS acti_usua, MAX(acti_hora) AS acti_hora, MAX(indi_ctrl) AS indi_ctrl, MAX(indi_gene) AS indi_gene,
                     MAX(indi_desc) AS indi_desc, MAX(tota_neto) AS tota_neto, MAX(pedi_veri) AS pedi_veri, 6 AS tipo_cata,
                     MAX(tota_prep) AS tota_prep, MAX(valo_flet) AS valo_flet, MAX(valo_serv) AS valo_serv, MAX(indi_prep) AS indi_prep,
                     MAX(sald_prep) AS sald_prep, MAX(pedi_nume) AS pedi_nume
                     FROM pedidos_liqu WHERE cons_terc IN (SELECT cons_terc FROM canj_mult_sali
                     WHERE codi_camp='{$this->campana}' AND nume_fact IS NULL AND acti_esta='ACT')
                     GROUP BY 6, 7, 8
                     INTO TEMP pedi_canj_mult WITH NO LOG;";
    
        $this->executeQueryQuery($consulta);
    }

    private function createIndexesPediCanjMult() {
        $consulta = "CREATE INDEX pcm_cons_terc ON pedi_canj_mult (cons_terc);";
    
        $this->executeQueryQuery($consulta);

        $consulta = "CREATE INDEX pcm_codi_camp ON pedi_canj_mult (codi_camp);";
    
        $this->executeQueryQuery($consulta);
    }

    private function createTempLiq1() {
        $consulta = "SELECT UNIQUE para_cond.codi_camp, para_cond.codi_vent, para_cond.nume_cond, para_cond.nomb_cond
                     FROM para_cond WHERE codi_camp='{$this->campana}' AND para_cond.tipo_cond ='OFERTA'
                     INTO TEMP liq_1 WITH NO LOG;";
    
        $this->executeQueryQuery($consulta);
    }

    private function createIndexesLiq1() {
        $consulta = "CREATE INDEX liq_1_codi_vent ON liq_1 (codi_vent);";
    
        $this->executeQueryQuery($consulta);
    }

    private function createTempFacTem2() {
        $consulta = "INSERT INTO temp_fac_tem2 SELECT UNIQUE pedidos_liqu.nume_pedi, pedidos_liqu.cons_terc, tab_zona.tipo_zona
                     FROM pedidos_liqu, tab_clie, tab_zona
                     WHERE pedidos_liqu.cons_terc = tab_clie.cons_terc
                     AND tab_clie.codi_zona = tab_zona.codi_zona
                     AND pedidos_liqu.codi_camp = '{$this->campana}';";
    
        $this->executeQueryQuery($consulta);
    }

    private function updateParaCond() {
        $consulta = "UPDATE para_cond SET tipo_zona='' WHERE codi_camp = '{$this->campana}' AND para_cond.tipo_cond ='OFERTA' AND tipo_zona IS NULL;";
    
        $this->executeQueryQuery($consulta);
    }

    private function createTempLiq3() {
        $consulta = "SELECT UNIQUE liq_1.*, para_cond_deta.codi_prod, para_cond_deta.indi_ofer, para_cond_deta.valo_prod, para_cond_deta.cant_prod,
                     inv_prca.nume_pagi, tab_ivaa.porc_ivaa, inv_prca.indi_desc, inv_prca.porc_desc, tab_prod.codi_line, tab_prod.codi_grup,
                     inv_prca.codi_cata, tab_prod.codi_subg, para_cond_deta.punt_prod, para_cond.tipo_zona, para_cond_deta.codi_vehi, para_cond_deta.codi_estr
                     FROM liq_1, para_cond_deta, para_cond, inv_prca, tab_ivaa, tab_prod
                     WHERE liq_1.nume_cond=para_cond.nume_cond
                     AND inv_prca.codi_camp=para_cond.codi_camp
                     AND inv_prca.codi_camp='{$this->campana}'
                     AND para_cond.nume_cond=para_cond_deta.cond_nro
                     AND inv_prca.codi_vent=liq_1.codi_vent
                     AND para_cond.tipo_cond='OFERTA'
                     AND para_cond.tipo_zona NOT LIKE '%A%'
                     AND para_cond.tipo_zona <>''
                     AND inv_prca.codi_prod=tab_prod.codi_prod
                     AND inv_prca.codi_pais='COL' AND inv_prca.esta_prod='S'
                     AND tab_ivaa.codi_ivaa=tab_prod.codi_ivaa
                     AND tab_ivaa.acti_esta='ACT'
                     INTO TEMP liq_3 WITH NO LOG;";
    
        $this->executeQueryQuery($consulta);
    }

    private function createIndexesLiq3() {
        $consulta = "CREATE INDEX liq_3_codi_vent ON liq_3 (codi_vent);";
    
        $this->executeQueryQuery($consulta);

        $consulta = "CREATE INDEX liq_3_codi_camp ON liq_3 (codi_camp);";
    
        $this->executeQueryQuery($consulta);
    }

    private function insertIntoFacTem2() {
        $consulta = "INSERT INTO fac_tem2 (fech_pedi, nume_pedi, codi_camp, codi_terc, cons_terc, codi_vent, codi_prod, prec_vent, cant_pedi, cant_desp, cant_prem, acti_usua, acti_hora, indi_ctrl, indi_gene, tota_neto, codi_ven1, codi_pro1, prec_ven1, indi_ofer, nume_pagi, porc_ivaa, indi_desc, porc_desc, codi_line, codi_grup, codi_cata, codi_subg, codi_vehi, codi_estr)
                     SELECT pedidos_liqu.fech_pedi, pedidos_liqu.nume_iden, pedidos_liqu.codi_camp,
                     pedidos_liqu.codi_terc, pedidos_liqu.cons_terc, pedidos_liqu.codi_vent,
                     liq_3.codi_prod, liq_3.valo_prod, pedidos_liqu.cant_pedi, 0,
                     pedidos_liqu.cant_pedi*cant_prod,
                     pedidos_liqu.acti_usua, pedidos_liqu.acti_hora,
                     pedidos_liqu.indi_ctrl, pedidos_liqu.indi_gene, pedidos_liqu.tota_neto,
                     liq_3.codi_prod, liq_3.nomb_cond, liq_3.punt_prod*1000,
                     liq_3.indi_ofer, liq_3.nume_pagi, liq_3.porc_ivaa,
                     liq_3.indi_desc, liq_3.porc_desc, liq_3.codi_line,
                     liq_3.codi_grup, liq_3.codi_cata, liq_3.codi_subg, liq_3.codi_vehi, liq_3.codi_estr
                     FROM pedidos_liqu, liq_3, temp_liqu, tab_zona
                     WHERE pedidos_liqu.cons_terc=temp_liqu.cons_terc AND
                     pedidos_liqu.codi_camp='{$this->campana}'
                     AND pedidos_liqu.codi_vent=liq_3.codi_vent
                     AND pedidos_liqu.codi_terc[1,3]=tab_zona.codi_zona
                     AND tab_zona.tipo_zona=liq_3.tipo_zona ;";
    
        $this->executeQueryQuery($consulta);

        $consulta = "INSERT INTO fac_tem2 (fech_pedi, nume_pedi, codi_camp, codi_terc, cons_terc, codi_vent, codi_prod, prec_vent, cant_pedi, cant_desp, cant_prem, acti_usua, acti_hora, indi_ctrl, indi_gene, tota_neto, codi_ven1, codi_pro1, prec_ven1, indi_ofer, nume_pagi, porc_ivaa, indi_desc, porc_desc, codi_line, codi_grup, codi_cata, codi_subg, codi_vehi, codi_estr)
                     SELECT pedidos_liqu.fech_pedi, pedidos_liqu.nume_iden, pedidos_liqu.codi_camp,
                     pedidos_liqu.codi_terc, pedidos_liqu.cons_terc, pedidos_liqu.codi_vent,
                     liq_3.codi_prod, liq_3.valo_prod, pedidos_liqu.cant_pedi, 0,
                     pedidos_liqu.cant_pedi*cant_prod,
                     pedidos_liqu.acti_usua, pedidos_liqu.acti_hora,
                     pedidos_liqu.indi_ctrl, pedidos_liqu.indi_gene, pedidos_liqu.tota_neto,
                     liq_3.codi_prod, liq_3.nomb_cond, liq_3.punt_prod*1000,
                     liq_3.indi_ofer, liq_3.nume_pagi, liq_3.porc_ivaa,
                     liq_3.indi_desc, liq_3.porc_desc, liq_3.codi_line,
                     liq_3.codi_grup, liq_3.codi_cata, liq_3.codi_subg, liq_3.codi_vehi, liq_3.codi_estr
                     FROM pedidos_liqu, liq_3, temp_liqu
                     WHERE pedidos_liqu.cons_terc=temp_liqu.cons_terc AND
                     pedidos_liqu.codi_camp='{$this->campana}'
                     AND pedidos_liqu.codi_vent=liq_3.codi_vent
                     AND liq_3.tipo_zona='' ;";
    
        $this->executeQueryQuery($consulta);

        $consulta = "INSERT INTO fac_tem2 (fech_pedi, nume_pedi, codi_camp, codi_terc, cons_terc, codi_vent, codi_prod, prec_vent, cant_pedi, cant_desp, cant_prem, acti_usua, acti_hora, indi_ctrl, indi_gene, tota_neto, codi_ven1, codi_pro1, prec_ven1, indi_ofer, nume_pagi, porc_ivaa, indi_desc, porc_desc, codi_line, codi_grup, codi_cata, codi_subg, codi_vehi, codi_estr)
                     SELECT pedi_canj_mult2.fech_pedi, pedi_canj_mult2.nume_pedi, pedi_canj_mult2.codi_camp,
                     pedi_canj_mult2.codi_terc, pedi_canj_mult2.cons_terc, pedi_canj_mult2.codi_vent,
                     liq_3.codi_prod, liq_3.valo_prod, pedi_canj_mult2.cant_pedi, 0,
                     pedi_canj_mult2.cant_pedi*cant_prod,
                     pedi_canj_mult2.acti_usua, pedi_canj_mult2.acti_hora,
                     pedi_canj_mult2.indi_ctrl, pedi_canj_mult2.indi_gene, pedi_canj_mult2.tota_neto,
                     liq_3.codi_prod, liq_3.codi_prod, liq_3.punt_prod*1000,
                     'S' indi_ofer, liq_3.nume_pagi, liq_3.porc_ivaa,
                     liq_3.indi_desc, liq_3.porc_desc, liq_3.codi_line,
                     liq_3.codi_grup, 6 codi_cata, liq_3.codi_subg, liq_3.codi_vehi, liq_3.codi_estr
                     FROM pedi_canj_mult2, liq_3, temp_liqu
                     WHERE pedi_canj_mult2.cons_terc=temp_liqu.cons_terc AND
                     pedi_canj_mult2.codi_camp='{$this->campana}'
                     AND pedi_canj_mult2.codi_vent=liq_3.codi_vent;";
    
        $this->executeQueryQuery($consulta);
    }
    
  

}

?>