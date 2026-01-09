<?php 
    require_once '/xampp/htdocs/avance/HTML/conexion.php';
    $doc_alumno = $_GET['doc_alumno'] ?? null;
    $result = mysqli_query($cn, "SELECT * FROM alumno WHERE N_DOCUMENTO_ALUMNO = '$doc_alumno'");
    $xxx = mysqli_fetch_array($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Ficha de Carrera</title>
<style>
  table {
    border-collapse: collapse;
    
  }
  th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: left;
  }
  th {
    background-color: #f2f2f2;
  }
</style>
</head>
<body>
  <table>
    <tr>
      <td style="border: 0;"><img src="/avance/HTML/images/escudo.png" alt="minedu"></td>
      <td colspan="6" style="text-align: center; border: 0;"><strong><h1>MINISTERIO DE EDUCACIÓN</h1></strong>EDUCACION TÉCNICO PRODUCTIVA
        <h1><strong>FICHA DE MATRICULA</strong></h1>
      </td>
    </tr>
    <tr>
        <td style="border: 0;"></td>
    </tr>
    
    <tr>
      <th>CODIGO DE INSCRIPCION</th>
      <td>1048958002525</td>
    </tr>
    <tr>
        <td style="border: 0;"></td>
    </tr>
    <tr>
      <th colspan="3">1. NOMBRE DEL CETPRO</th>
      <th colspan="2">ESPECIALIDAD</th>
      <th colspan="2">TURNO</th>
      
    </tr>
    <tr>
      <td colspan="3">LA CASA DEL NIÑO TRABAJADOR - SAN MARTIN DE PORRES</td>
      <td colspan="2"><?php echo "" ?></td>
      <td colspan="2"><?php echo "" ?></td>
    </tr>
    <tr>
        <td colspan="7"></td>
    <tr>
      <th colspan="7" style="text-align: center;">2. UBICACIÓN DEL CENTRO DE EDUCACION TECNICO PRODUCTIVO</th>
    </tr>
    <tr>
      <td colspan="7" style="text-align: center;font-size:20px;"><strong>Direccion Regional de Educacion de Lima Metropolitana</strong></td>
    </tr>
    <tr>
      <td colspan="7" style="text-align: center;font-size:20px;"><strong>UGEL 03</strong></td>
    </tr>
    <tr>
        <th>PROVINCIA</th>
        <td>LIMA</td>
        <th>DISTRITO</th>
        <td colspan="4">LIMA</td>
    </tr>
    <tr>
        <th>LUGAR</th>
        <td>LIMA</td>
        <th>DIRECCION</th>
        <td colspan="4">JR PUNO 412</td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
      <th colspan="7">3. DATOS PERSONALES DEL ESTUDIANTE</th>
    </tr>
    <tr>
        <th>APELLIDOS PATERNO</th>
        <th>APELLIDO MATERNO</th>
        <th>NOMBRES</th>
        <th colspan="4">SEXO</th>
    </tr>
    <tr>
        <?php error_reporting(0); ?>
        <td><?php echo "" ?></td>
        <td><?php echo "" ?></td>
        <td><?php echo "" ?></td>
        <th>H</th>
        <td><?php if ($xxx['SEXO']== 'M'): echo "X"; 
        if ($xxx['SEXO']== null): echo ""; endif;
        endif;?></td>
        <th>M</th>
        <td><?php if ($xxx['SEXO']== 'F'): echo "X"; 
        if ($xxx['SEXO']== null): echo ""; endif;
        endif; ?></td>
        
    </tr>
    <tr>
      <th colspan="3">NACIMIENTO</th>
      <th rowspan="2">LUGAR</th>
      <td rowspan="2"><?php echo "" ?></td>
      <th rowspan="2">DISTRITO</th>
      <td rowspan="2"><?php echo "" ?></td>
    </tr>
    <tr>
      <th>DIA</th>
      <th>MES</th>
      <th>AÑO</th>
    </tr>
    <tr>
        <td><?php echo ""; ?></td>
        <td></td>
        <td></td>
        <th>PAIS</th>
        <td></td>
        <th>DPTO.</th>
        <td></td>
    </tr>
    <tr>
        <th colspan="2">EDAD</th>
        <td></td>
        <th>PROV.</th>
        <td colspan="3"></td>
    </tr>
    <tr>
        <th colspan="3">ESTADO CIVIL</th>
        <th colspan="2">DOCUMENTO IDENTIDAD</th>
        <th>GRADO DE INSTRUCCIÓN</th>
        <th>OCUPACION</th>
    </tr>
    <tr>
        <td colspan="3"><?php echo "" ?></td>
        <td colspan="2"><?php echo "" ?></td>
        <td><?php echo "" ?></td>
        <td><?php echo "" ?></td>
    </tr>
    <tr>
        <th colspan="7">DOMICILIO</th>
    </tr>
    <tr>
      <th colspan="2">PROVINCIA</th>
      <td colspan="3"><?php echo "" ?></td>
      <th colspan="">DISTRITO</th>
      <td colspan="2"><?php echo "" ?></td>
    </tr>
    <tr>
      <th colspan="2">LUGAR</th>
      <td colspan="3"><?php echo "" ?></td>
      <th colspan="">CALLE</th>
      <td colspan="2"><?php echo "" ?></td>
    </tr>
    <tr>
      <th colspan="2">TELEFONO</th>
      <td colspan="3"><?php echo "" ?></td>
      <th colspan="">EMAIL</th>
      <td colspan="2"><?php echo "" ?></td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <th colspan="7">4. DATOS DEL MODULO</th>
    </tr>
    <tr>
        <th colspan="2">CICLO</th>
        <td colspan="5"><?php echo "" ?></td>
    </tr>
    <tr>
        <th colspan="2">MODULO</th>
        <td colspan="5"><?php echo "" ?></td>
    </tr>
    <tr>
        <th colspan="2">DURACION</th>
        <td colspan=""><?php echo "" ?></td>
        <th>INICIO</th>
        <td colspan=""><?php echo "" ?></td>
        <th>TERMINO</th>
        <td colspan=""><?php echo "" ?></td>
    </tr>
    <tr>
        <th colspan="3">REQUISITOS DE ACCESO</th>
        <td colspan="4"><?php echo "" ?></td>
    </tr>
    <tr>
      <th colspan="7">ENUMERAR LOS MÓDULOS APROBADOS Y CERTIFICADOS A LA FECHA</th>
    </tr>
    <tr>
        <th colspan="3">MÓDULOS</th>
        <td colspan="2"><?php echo "" ?></td>
        <td colspan="2"><?php echo "" ?></td>
    </tr>
    <tr>
        <th colspan="3">N° DE HORAS</th>
        <td colspan="2"><?php echo "" ?></td>
        <td colspan="2"><?php echo "" ?></td>
    </tr>
    <tr>
        <td colspan="7" style="border: 0;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border: 0;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border: 0;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border: 0;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border: 0;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border: 0;"></td>
    </tr>
    <tr>
        <td colspan="7" style="border: 0;"></td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: center;border: 0;"></td>
        <td colspan="3" style="text-align: center;border: 0;"><img src="../images/firma.png" alt="firma" width="150" height="150"></td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: center;border: 0;">__________________________________________</td>
        <td colspan="3" style="text-align: center;border: 0;">_________________________</td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: center;border: 0;">FRIMA DEL ESTUDIANTE Y/O APODERADO</td>
        <td colspan="3" style="text-align: center;border: 0;">SECRETARÍA / DIRECCION</td>
    </tr>
  </table>
</body>
</html>