<?php
// Parcial reutilizada por reservas.php (carga inicial) y ajax/reservas.ajax.php (cambio de mes por AJAX).
// Espera en scope: $mesActual, $anioActual, $totalDias, $hoy, $diasSemana, $Habitaciones.
?>
<thead>
  <tr>
    <th class="reserva-col-habitacion">Habitaciones</th>
    <?php for ($d = 1; $d <= $totalDias; $d++):
      $fechaCol = sprintf("%04d-%02d-%02d", $anioActual, $mesActual, $d);
      $nombreDia = $diasSemana[(int) date("w", mktime(0, 0, 0, $mesActual, $d, $anioActual))];
      $esHoy = $fechaCol === $hoy;
    ?>
      <th class="<?php echo $esHoy ? "reserva-col-hoy" : ""; ?>">
        <span class="reserva-dia-nombre"><?php echo $nombreDia; ?></span>
        <span class="reserva-dia-numero"><?php echo $d; ?></span>
      </th>
    <?php endfor; ?>
  </tr>
</thead>
<tbody>
  <?php if (count($Habitaciones) === 0): ?>
    <tr>
      <td colspan="<?php echo $totalDias + 1; ?>" class="text-center text-muted" style="padding:20px;">
        No hay habitaciones registradas. Da de alta habitaciones desde el módulo de Habitaciones para verlas aquí.
      </td>
    </tr>
  <?php else: foreach ($Habitaciones as $hab):
    $carriles = $hab["Carriles"];
    $totalCarriles = count($carriles);
    foreach ($carriles as $indiceCarril => $segmentos):
      // Cuando una habitación tiene reservas encimadas usa varios "carriles" (filas) para
      // mostrarlas todas. La línea entre esas filas es de la MISMA habitación, no una
      // separación real como la que hay entre habitaciones distintas: se quita para que no
      // parezca que la fila de abajo pertenece a la siguiente habitación de la tabla.
      $clasesFila = "reserva-fila";
      if ($indiceCarril > 0) { $clasesFila .= " reserva-fila-continua"; }
      if ($indiceCarril < $totalCarriles - 1) { $clasesFila .= " reserva-fila-sin-borde-inferior"; }
    ?>
    <tr class="<?php echo $clasesFila; ?>" data-habitacion="<?php echo (int) $hab["Id_Habitacion"]; ?>">
      <?php if ($indiceCarril === 0): ?>
        <td class="reserva-col-habitacion" rowspan="<?php echo $totalCarriles; ?>"><?php echo htmlspecialchars($hab["TipoHabitacion"]); ?></td>
      <?php endif; ?>
      <?php
      $d = 1;
      while ($d <= $totalDias):
        $fechaCol = sprintf("%04d-%02d-%02d", $anioActual, $mesActual, $d);
        $esHoy = $fechaCol === $hoy;

        if (isset($segmentos[$d])):
          $seg = $segmentos[$d];
          $span = $seg["fin"] - $d + 1;
      ?>
        <td colspan="<?php echo $span; ?>" class="reserva-celda-barra">
          <div class="reserva-barra estado-<?php echo $seg["estado"]; ?>" title="<?php echo htmlspecialchars($seg["titulo"]); ?>"></div>
        </td>
      <?php
          $d += $span;
        else:
      ?>
        <td class="<?php echo $esHoy ? "reserva-col-hoy" : ""; ?>"></td>
      <?php
          $d++;
        endif;
      endwhile;
      ?>
    </tr>
  <?php endforeach; endforeach; endif; ?>
</tbody>
