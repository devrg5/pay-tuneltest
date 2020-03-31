@extends('master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <form action="/transaction/tigo" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="inputName">Nombre</label>
                        <input type="text" class="form-control" id="inputName" name="inputName" value="Gonzalo">
                    </div>
                    <div class="form-group">
                        <label for="inputLastName">Apellido</label>
                        <input type="text" class="form-control" id="inputLastName" name="inputLastName" value="Ramirez">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" value="aaa@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="inputDescription">Descripcion</label>
                        <input type="text" class="form-control" id="inputDescription" name="inputDescription" value="2 productos (Televisor y Heladera)">
                    </div>
                    <div class="form-group">
                        <label for="inputAmount">Monto</label>
                        <input type="text" class="form-control" id="inputAmount" name="inputAmount" value="500">
                    </div>
                    <input type="text" name="inputClientId" value="HJ123" hidden>
                    <input type="text" name="orderId" value="50" hidden>
                    <input type="text" name="inputCurrency" value="068" hidden>
                    <input type="text" name="inputAddress" value="B/57 viviendas N163" hidden>
                    <input type="text" name="inputZip" value="000" hidden>
                    <input type="text" name="inputCity" value="Tarija" hidden>
                    <input type="text" name="inputState" value="Tarija" hidden>
                    <input type="text" name="inputCountry" value="Bolivia" hidden>
                    <input type="text" name="inputLang" value="SP" hidden>
                    <input type="text" name="verificationNumber" value="0b04131bc9e6a98258fa9d2701ba3a6bbfed81c6c98e1aade22d21ef6578f949814d68849f575c4070a0561a0762e00133c153e642db1dd96d4381296871688b" hidden>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
@endsection