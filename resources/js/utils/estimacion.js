export function calcularTotales(bloques, factor = 1) {
    let minutosBase = 0;

    bloques.forEach((b) => {
        b.tareas?.forEach((t) => {
            minutosBase += Number(t.duracion_minuto || 0);
        });
    });

    const minutos = minutosBase * factor;
    const horas = Number((minutos / 60).toFixed(2));

    return {
        minutos,
        horas,
    };
}
