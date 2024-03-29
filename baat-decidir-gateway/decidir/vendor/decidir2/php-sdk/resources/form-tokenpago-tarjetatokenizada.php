<!DOCTYPE HTML>
<html>
<head>
<script src="http://baat.com.ar/decidir/decidirv1.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<meta charset="utf-8" />
</head>
<body>

<script type="text/javascript">

const publicApiKey = "b192e4cb99564b84bf5db5550112adea";
const urlSandbox = "https://developers.decidir.com/api/v1";

const decidirSandbox = new Decidir(urlSandbox);
decidirSandbox.setPublishableKey(publicApiKey);
decidirSandbox.setTimeout(0);//se configura sin timeout

//funcion para manejar la respuesta
function sdkResponseHandler(status, response) {
  console.log(status);
  if (status == '200' ||status == '201') {
    console.log('OK - Respuesta: '+response );
    console.log('TOKEN: '+response.id );

    alert("Token de pago: "+response.id);
    
    $.ajax('form_action.php',
    {
        method  : 'POST',
        data    :
        {
              token      : response.id
        },                                    
        success : function (response)
        {
              var res = JSON.parse(response);
              if(res["status"]=="approved"){
                console.log('ok pagado'+response+' - Estado: '+res["status"]);
              }else{
                console.log('Error al pagar'+response+' - Estado: '+res["status"]);
              }
        },
        error   : function (e, status)
        {
              console.log('Error pagado'+response);
        }
    });
    
  }else {
      console.log('ERROR - STATUS: ' + status + ' - Respuesta: ' +response );
  }
}


//funcion de invocacion con sdk
function sendFormFunc(event) {
  //event.preventDefault();
  var form=document.querySelector('#formulario');
  decidirSandbox.createToken(form, sdkResponseHandler);//formulario y callback
  return false;
}

</script>

<!--form action="#" id="formulario" onsubmit="sendFormFunc()" >
  <fieldset>
    <ul>
      <li>
        <label for="card_number">Numero de tarjeta:</label>
        <input type="text" data-decidir="card_number" placeholder="XXXXXXXXXXXXXXXX" value="4507990000004905"/>
      </li>
      <li>
        <label for="security_code">Codigo de seguridad:</label>
      <input type="text"  data-decidir="security_code" placeholder="XXX" value="775" />
      </li>
      <li>
        <label for="card_expiration_month">Mes de vencimiento:</label>
        <input type="text"  data-decidir="card_expiration_month" placeholder="MM" value="08"/>
      </li>
      <li>
        <label for="card_expiration_year">Año de vencimiento:</label>
        <input type="text"  data-decidir="card_expiration_year" placeholder="AA" value="18"/>
      </li>
      <li>
        <label for="card_holder_name">Nombre del titular:</label>
        <input type="text" data-decidir="card_holder_name" placeholder="TITULAR" value="Tarjeta Visa"/>
      </li>
      <li>
        <label for="card_holder_doc_type">Tipo de documento:</label>
        <select data-decidir="card_holder_doc_type">
          <option value="dni">DNI</option>
        </select>
      </li>
      <li>
        <label for="card_holder_doc_type">Numero de documento:</label>
        <input type="text"data-decidir="card_holder_doc_number" placeholder="XXXXXXXXXX" value="27859328"/>
      </li>
    </ul>
    <input type="submit" value="Generar Token" />
  </fieldset>
</form-->

<form action="#" id="formulario" onsubmit="sendFormFunc()">
  <fieldset>
    <ul>
      <li>
        <label for="token">Tarjeta tokenizada:</label>
        <input type="text"  data-decidir="token" placeholder="xxxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxx" value="d666c2da-909d-4912-8c56-44abee399b3d"/>
      </li>
      <li>
        <label for="security_code">Codigo de seguridad:</label>
        <input type="text"  data-decidir="security_code" placeholder="XXX" value="775" />
      </li>
    </ul>
    <input type="submit" value="Generar Token" />
  </fieldset>
</form>

</body>
</html>
