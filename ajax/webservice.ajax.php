<?php

require_once "../controllers/webservice.controlador.php";
require_once "../models/webservice.modelo.php";

class AjaxWebservice
{

	public $nombre_select;
	public $codigo_select;
	public $cbx;

	public $numero;
	public $producto;
	public $referencia;
	public $monto;

	public $requestid;
	public $requestid_pin;
	public $requestid_serv;
	public $requestid_ver;

	public function ajaxConsultarProductos()
	{
		$nombre_select = $this->nombre_select;
		$codigo_select = $this->codigo_select;
		$html = '';
		$datos = controladorWebservice::ctrObtenerProductos();

		switch ($this->cbx) {
			case 'cbxnombre':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;
					}
				}
				
				$html = "<option value='0'>Seleccionar Codigo</option>";
				
				foreach ($codigo as $row) {
					$html .= "<option value=" . $row . ">" . $row . "</option>";
				}
				
				echo $html;
				
				break;
			case 'cbxdescripcion':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;

						if ($fila->codigo == $codigo_select) {
							$descripcion = $fila->descripcion;
							$monto = $fila->monto;

							$html .= "<option value=" . $descripcion . ">" . $descripcion . "</option>";
						}
					}
				}
				echo $html;
				break;
			case 'cbxmonto':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;

						if ($fila->codigo == $codigo_select) {
							$descripcion = $fila->descripcion;
							$monto = $fila->monto;
							$html .= "<option value=" . $monto . ">" . $monto . "</option>";
						}
					}
				}
				echo $html;
				break;
		}
	}

	public function ajaxConsultarServicios()
	{

		$nombre_select = $this->nombre_select;
		$codigo_select = $this->codigo_select;
		$html = '';
		$datos = controladorWebservice::ctrObtenerServicios();

		switch ($this->cbx) {
			case 'cbxnombre':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;
					}
				}
				
				$html = "<option value='0'>Seleccionar Codigo</option>";
				
				foreach ($codigo as $row) {
					$html .= "<option value=" . $row . ">" . $row . "</option>";
				}
				
				echo $html;

				break;
			case 'cbxdescripcion':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;

						if ($fila->codigo == $codigo_select) {
							$descripcion = $fila->descripcion;
							$monto = $fila->monto;

							$html .= "<option value=" . $descripcion . ">" . $descripcion . "</option>";
						}
					}
				}
				echo $html;
				break;
			case 'cbxmonto':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;

						if ($fila->codigo == $codigo_select) {
							$descripcion = $fila->descripcion;
							$monto = $fila->monto;
							$html .= "<option value=" . $monto . ">" . $monto . "</option>";
						}
					}
				}
				echo $html;
				break;
		}
	}

	public function ajaxConsultarPines()
	{


		$nombre_select = $this->nombre_select;
		$codigo_select = $this->codigo_select;
		$html = '';
		$datos = controladorWebservice::ctrObtenerPines();



		switch ($this->cbx) {
			case 'cbxnombre':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;
					}
				}
				$html = "<option value='0'>Seleccionar Codigo</option>";
				foreach ($codigo as $row) {
					$html .= "<option value=" . $row . ">" . $row . "</option>";
				}
				echo $html;

				break;
			case 'cbxdescripcion':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;

						if ($fila->codigo == $codigo_select) {
							$descripcion = $fila->descripcion;
							$monto = $fila->monto;

							$html .= "<option value=" . $descripcion . ">" . $descripcion . "</option>";
						}
					}
				}
				echo $html;
				break;
			case 'cbxmonto':
				foreach ($datos as $fila) {
					$nombre = $fila->nombre;

					if ($nombre == $nombre_select) {

						$codigo[] = $fila->codigo;

						if ($fila->codigo == $codigo_select) {
							$descripcion = $fila->descripcion;
							$monto = $fila->monto;
							$html .= "<option value=" . $monto . ">" . $monto . "</option>";
						}
					}
				}
				echo $html;
				break;
		}
	}

	public function ajaxReservarRecarga()
	{
		$numero = $this->numero;
		$producto= $this->producto;
		/*$monto= $this->monto;
		$referencia= $this->referencia;*/

		$datos = controladorWebservice::ctrReservarTransaccion($numero,$producto);
		$res = $datos->{'requestid'};
		echo $res;
	}
	public function ajaxProcesarRecarga()
	{
		$requestid = $this->requestid;

		$datos = controladorWebservice::ctrProcesarTransaccion($requestid);
		$datosJson = array('requestid' => $datos->{'requestid'},'fecha' =>$datos->{'fecha'},
		'referencia' =>$datos->{'referencia'},
		'folio' =>$datos->{'folio'},
		'monto' =>$datos->{'monto'},
		'cargo' =>$datos->{'cargo'},
		'abono' =>$datos->{'abono'},
		'producto' =>$datos->{'producto'},
		'saldofinal' =>$datos->{'saldofinal'});
		echo json_encode($datosJson);
	}
	public function ajaxReservarRecargaPin()
	{
		$numero = $this->numero;
		$producto= $this->producto;

		$datos = controladorWebservice::ctrReservarTransaccion($numero,$producto);
		$res = $datos->{'requestid'};
		echo $res;
	}
	public function ajaxProcesarRecargaPin()
	{
		$requestid_pin = $this->requestid_pin;

		$datos = controladorWebservice::ctrProcesarTransaccion($requestid_pin);
		$datosJson = array('requestid' => $datos->{'requestid'},'fecha' =>$datos->{'fecha'},
		'referencia' =>$datos->{'referencia'},
		'folio' =>$datos->{'folio'},
		'monto' =>$datos->{'monto'},
		'cargo' =>$datos->{'cargo'},
		'abono' =>$datos->{'abono'},
		'producto' =>$datos->{'producto'},
		'saldofinal' =>$datos->{'saldofinal'});
		echo json_encode($datosJson);
	}

	public function ajaxReservarRecargaServ()
	{
		$producto= $this->producto;
		$monto= $this->monto;
		$referencia= $this->referencia;

		$datos = controladorWebservice::ctrReservarTransaccionServ($referencia,$producto,$monto);
		$res = $datos->{'requestid'};
		echo $res;
	}
	public function ajaxProcesarRecargaServ()
	{
		$requestid_serv = $this->requestid_serv;

		$datos = controladorWebservice::ctrProcesarTransaccion($requestid_serv);
		$datosJson = array('requestid' => $datos->{'requestid'},'fecha' =>$datos->{'fecha'},
		'referencia' =>$datos->{'referencia'},
		'folio' =>$datos->{'folio'},
		'monto' =>$datos->{'monto'},
		'cargo' =>$datos->{'cargo'},
		'abono' =>$datos->{'abono'},
		'producto' =>$datos->{'producto'},
		'saldofinal' =>$datos->{'saldofinal'});
		echo json_encode($datosJson);
	}
	public function ajaxVerificarRecarga()
	{
		$requestid_ver = $this->requestid_ver;

		$datos = controladorWebservice::ctrConsultarTransaccion($requestid_ver);
		$datosJson = array('requestid' => $datos->{'requestid'},'fecha' =>$datos->{'fecha'},
		'referencia' =>$datos->{'referencia'},
		'folio' =>$datos->{'folio'},
		'monto' =>$datos->{'monto'},
		'cargo' =>$datos->{'cargo'},
		'abono' =>$datos->{'abono'},
		'producto' =>$datos->{'producto'},
		'saldofinal' =>$datos->{'saldofinal'});
		echo json_encode($datosJson);
	}
	
}

if (isset($_POST["operacion"])) {

	$operacion = $_POST["operacion"];
	$recarga = new AjaxWebservice();
	$recarga->nombre_select = $_POST["nombre"];
	$recarga->codigo_select = $_POST["codigo"];
	$recarga->cbx = $_POST["cbx"];

	if ($operacion == 'Productos') {
		$recarga->ajaxConsultarProductos();
	} else if ($operacion == 'Servicios') {
		$recarga->ajaxConsultarServicios();
	} else if ($operacion == 'Pines') {
		$recarga->ajaxConsultarPines();
	}
}

if (isset($_POST["numero"]) || isset($_POST["producto"])) {

	$recarga = new AjaxWebservice();
	$recarga->numero = $_POST["numero"];
	$recarga->producto = $_POST["producto"];
	$recarga->ajaxReservarRecarga();
}
if (isset($_POST["requestid"])) {

	$recarga = new AjaxWebservice();
	$recarga->requestid = $_POST["requestid"];
	$recarga->ajaxProcesarRecarga();
}
if (isset($_POST["numero_pin"]) || isset($_POST["producto_pin"])) {

	$recarga = new AjaxWebservice();
	$recarga->numero = $_POST["numero_pin"];
	$recarga->producto = $_POST["producto_pin"];
	$recarga->ajaxReservarRecargaPin();
}
if (isset($_POST["requestid_pin"])) {

	$recarga = new AjaxWebservice();
	$recarga->requestid_pin = $_POST["requestid_pin"];
	$recarga->ajaxProcesarRecargaPin();
}
if (isset($_POST["referencia_serv"]) || isset($_POST["producto_serv"]) || isset($_POST["monto_serv"])) {

	$recarga = new AjaxWebservice();
	$recarga->referencia = $_POST["referencia_serv"];
	$recarga->producto = $_POST["producto_serv"];
	$recarga->monto = $_POST["monto_serv"];
	$recarga->ajaxReservarRecargaServ();
}
if (isset($_POST["requestid_serv"])) {

	$recarga = new AjaxWebservice();
	$recarga->requestid_serv = $_POST["requestid_serv"];
	$recarga->ajaxProcesarRecargaServ();
}
if (isset($_POST["requestid_ver"])) {

	$recarga = new AjaxWebservice();
	$recarga->requestid_ver = $_POST["requestid_ver"];
	$recarga->ajaxVerificarRecarga();
}