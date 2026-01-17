
fetch('https://ve.dolarapi.com/v1/dolares/oficial')
  .then(response => {
    if (!response.ok) {
      throw new Error('La solicitud no fue exitosa');
    }
    return response.json();
  })
  .then(data => {
    console.log('Datos recibidos:', data);
    // Aquí puedes manejar los datos como necesites
    data.forEach(dolar => {
      console.log(`Fuente: ${dolar.fuente}`);
      console.log(`Nombre: ${dolar.nombre}`);
      console.log(`Compra: ${dolar.compra}`);
      console.log(`Venta: ${dolar.venta}`);
      console.log(`Promedio: ${dolar.promedio}`);
      console.log(`Fecha de Actualización: ${dolar.fechaActualizacion}`);
      console.log('-----------------------------');
    dolarToday = dolar.promedio;
    console.log("var dolar" + dolarToday)
    });
  })
  .catch(error => {
    console.error('Hubo un problema con la solicitud:', error);
  });
